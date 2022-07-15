<?php

namespace App\Http\Controllers\b2b2c;

use App\CustomerInfo;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Enum\PostType;
use App\Http\Controllers\functionController;
use App\Http\Controllers\jsonController;
use App\Post;
use App\RoyaltyLogEvents;
use App\SharePost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class b2b2cPostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $allPosts = Post::withCount('sharePost')
                ->where('poster_type', PostType::b2b2c)
                ->orderBy('id', 'DESC')->get();

        foreach ($allPosts as $post) {
            $event = ['feed_notification_received', 'feed_notification_opened'];
            $log_events = RoyaltyLogEvents::whereIn('event', $event)->where('event_value', $post->id)->get();
            $post['log_events'] = $log_events;
        }

        return view('b2b2c.posts.index', compact('allPosts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('b2b2c.posts.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'postCaption' => 'required',
            'postImage' => 'required',
        ]);
        $request->flashOnly(['postCaption', 'postImage']);

        $postHeader = $request->get('postHeader') != null ? $request->get('postHeader') : null;
        $postCaption = $request->get('postCaption');
        $postLink = $request->get('postLink') != null ? $request->get('postLink') : null;

        //upload image to aws & save url to DB
        if ($request->hasFile('postImage')) {
            $file = $request->file('postImage');
        } else {
            dd('Please select an Image');
        }

        //image is being resized & uploaded here
        $image_url = (new functionController)->uploadImageToAWS($file, 'dynamic-images/client_post_image');

        $post = new Post;
        $post->poster_id = Session::get('client-admin-id');
        $post->poster_type = PostType::b2b2c;
        $post->header = $postHeader;
        $post->caption = $postCaption;
        $post->moderate_status = 0;
        $post->image_url = $image_url;
        $post->post_link = $postLink;
        $post->save();

        return redirect('client/all-post')->with('status', 'Post Created Successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //dd('show');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $allPosts = Post::findOrFail($id);

        return view('b2b2c.posts.edit', compact('allPosts'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'postHeader' => 'required',
            'postCaption' => 'required',
        ]);
        $request->flashOnly(['postHeader', 'postCaption']);
        $postHeader = $request->get('postHeader');
        $postCaption = $request->get('postCaption');
        $postLink = $request->get('postLink') != null ? $request->get('postLink') : null;

        //get the updating instance
        $post = Post::findOrFail($id);

        //upload image to aws & save url to DB
        if ($request->hasFile('postImage')) {
            $file = $request->file('postImage');
            //at first delete the previous image
            $image_path = $post->image_url;
            $exploded_path = explode('/', $image_path);
            //remove previous profile image from bucket
            Storage::disk('s3')->delete('dynamic-images/client_post_image/'.end($exploded_path));

            //image is being resized & uploaded here
            $image_url = (new functionController)->uploadImageToAWS($file, 'dynamic-images/client_post_image');
            $post->header = $postHeader;
            $post->caption = $postCaption;
            $post->image_url = $image_url;
            $post->post_link = $postLink;
            $post->save();
        } else {
            $post->header = $postHeader;
            $post->caption = $postCaption;
            $post->post_link = $postLink;
            $post->save();
        }

        return redirect('client/all-post')->with('status', 'Post Updated Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $image_path = $post->image_url;
        //at first delete the previous image
        $exploded_path = explode('/', $image_path);
        //remove previous profile image from bucket
        Storage::disk('s3')->delete('dynamic-images/client_post_image/'.end($exploded_path));
        //delete post from count table
        SharePost::where('post_id', $id)->delete();
        $post->delete();

        return redirect('client/all-post')->with('delete post', 'Post Deleted Successfully!');
    }
}
