<?php

namespace App\Http\Controllers\Backend;

use config;
use App\Post;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;


class BlogController extends BackendController
{
    protected $limit = 5;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $posts = Post::with('category', 'author')->latest()->paginate($this->limit);
        $postCount = Post::count();
        return view('backend.blog.index', compact('posts', 'postCount'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
       return view('backend.blog.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
            $this->validate($request,[
                'title'         =>'required',
               // 'slug'          =>'required|unique:posts',
                'body'          =>'required',
                'category_id'   => 'required',
                'image'         => 'required'
            ]);

            $author = Auth::user()->id;
            $data =  $request->only(['title', 'slug', 'body','excerpt' ,'image', 'published_at', 'category_id', 'author_id']);
                if($request->hasFile('image')){
                $file = $request->file('image');
                $fileName = $file->getClientOriginalName();
                $path = public_path('/img');
                $success =  $file->move($path, $fileName);

                if($success){
                    $width = config('cms.image.thumbnail.width');
                    $height = config('cms.image.thumbnail.height');
                    $extension = $file->getClientOriginalExtension();
                    $thumbnail =   str_replace(".{$extension}", "_thumb.{$extension}", $fileName);
                    $path = public_path('/img');
                    Image::make($path . '/' . $fileName)
                        ->resize($width,$height)
                        ->save($path . '/' . $thumbnail);
                    }
                }

            $title = $request->title;
            $slug = str_slug($title, '-');
            $data['slug'] = $slug;
            $data['author_id'] = $author;
           // $data['image'] =
            // dd($data);
            $post = new Post($data);
            $confirm = $post->save();
             if($confirm){
                    toastr()->success('Blog Post Submitted Successfully', 'Success');
                    return redirect('/backend/blog');
              }else{
                    toastr()->error('An error occurred while submitting', 'Error');
                    return back();
              }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        return view('backend.blog.show');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        return view('backend.blog.edit');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
