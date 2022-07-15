<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Enum\PostType;
use App\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class partnerPostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $allPosts = Post::where('poster_id', Session::get('partner_id'))->get();

        return view('partner-admin.production.posts.index', compact('allPosts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('partner-admin.production.posts.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'postCaption' => 'required',
        ]);
        $request->flashOnly(['postHeader', 'postCaption']);
        $postHeader = $request->get('postHeader') != null ? $request->get('postHeader') : null;
        $postCaption = $request->get('postCaption');
        $post_url = $request->get('postLink') != null ? $request->get('postLink') : null;

        //upload image to aws & save url to DB
        if ($request->hasFile('postImage')) {
            $file = $request->file('postImage');
        } else {
            dd('Please select an Image');
        }

        //image is being resized & uploaded here

        $image_url = (new functionController)->uploadImageToAWS($file, 'dynamic-images/partner_post_image');

        $post = new Post;
        $post->poster_id = Session::get('partner_id');
        $post->poster_type = PostType::partner;
        $post->header = $postHeader;
        $post->caption = $postCaption;
        $post->image_url = $image_url;
        $post->post_link = $post_url;
        $post->save();

        return redirect('partner/post')->with('status', 'Post Created Successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //dd('show');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $allPosts = Post::findOrFail($id);

        return view('partner-admin.production.posts.edit', compact('allPosts'));
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
        $this->validate($request, [
            'postCaption' => 'required',
        ]);
        $request->flashOnly(['postHeader', 'postCaption']);
        $postHeader = $request->get('postHeader') != null ? $request->get('postHeader') : null;
        $postCaption = $request->get('postCaption');
        $post_url = $request->get('postLink') != null ? $request->get('postLink') : null;

        //get the updating instance
        $post = Post::findOrFail($id);

        //upload image to aws & save url to DB
        if ($request->hasFile('postImage')) {
            $file = $request->file('postImage');
            //at first delete the previous image
            $image_path = $post->image_url;
            $exploded_path = explode('/', $image_path);
            //remove previous profile image from bucket
            Storage::disk('s3')->delete('dynamic-images/partner_post_image/'.end($exploded_path));

            //image is being resized & uploaded here
            $image_url = (new functionController)->uploadImageToAWS($file, 'dynamic-images/partner_post_image');
            $post->header = $postHeader;
            $post->caption = $postCaption;
            $post->post_link = $post_url;
            $post->image_url = $image_url;
            $post->save();
        } else {
            $post->header = $postHeader;
            $post->caption = $postCaption;
            $post->post_link = $post_url;
            $post->save();
        }

        return redirect('partner/post')->with('status', 'Post Updated Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $image_path = $post->image_url;
        //at first delete the previous image
        $exploded_path = explode('/', $image_path);
        //remove previous profile image from bucket
        Storage::disk('s3')->delete('dynamic-images/partner_post_image/'.end($exploded_path));
        $post->delete();

        return redirect('partner/post')->with('status', 'Post Deleted Successfully!');
    }
}
