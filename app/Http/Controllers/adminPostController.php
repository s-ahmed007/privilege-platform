<?php

namespace App\Http\Controllers;

use App\CustomerInfo;
use App\Http\Controllers\Enum\Constants;
use App\Http\Controllers\Enum\LoginStatus;
use App\Http\Controllers\Enum\MediaType;
use App\Http\Controllers\Enum\PlatformType;
use App\Http\Controllers\Enum\PostType;
use App\Http\Controllers\Enum\PushNotificationType;
use App\Post;
use App\SharePost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class adminPostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        if (isset($_GET['post_of'])) {
            $post_of = $_GET['post_of'];
            if ($post_of == 'royalty') {
                $allPosts = Post::withCount('sharePost')->withCount('like')
                    ->where('poster_type', PostType::admin)
                    ->orderBy('pinned_post', 'DESC')->orderBy('id', 'DESC')->get();
            } else {
                $allPosts = Post::withCount('sharePost')->withCount('like')
                    ->where('poster_type', PostType::partner)
                    ->orderBy('pinned_post', 'DESC')->orderBy('id', 'DESC')->get();
            }
        } else {
            $allPosts = Post::withCount('sharePost')->withCount('like')->orderBy('pinned_post', 'DESC')
                ->orderBy('id', 'DESC')->get();
        }

        return view('admin.production.posts.index', compact('allPosts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create()
    {
        return view('admin.production.posts.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'postHeader' => 'required',
            'postImage' => 'required',
        ]);

        $postHeader = $request->get('postHeader');
        $postCaption = $request->get('postCaption') != null ? $request->get('postCaption') : null;
        $postLink = $request->get('postLink') != null ? $request->get('postLink') : null;
        $postSchedule = $request->postSchedule != null ? $request->postSchedule : null;
        $moderate_status = $request->postSchedule != null ? 0 : 1;

        //upload image/video to aws & save url to DB
        if ($request->hasFile('postImage')) {
            $file = $request->file('postImage');
            $filesize = filesize($file) / 1000000;
            if ($filesize > 10) {//if file size is too large
                return redirect()->back()->with('error', 'File too large.');
            }
            $mime = $_FILES['postImage']['type'];
            if (strstr($mime, 'video/')) {
                $media_url = (new functionController)->uploadVideoToAWS($file, 'videos/newsfeed');
                $media_type = MediaType::VIDEO;
            } elseif (strstr($mime, 'image/')) {
                $media_url = (new functionController)->uploadImageToAWS($file, 'dynamic-images/admin_post_image');
                $media_type = MediaType::IMAGE;
            }
        } else {
            return redirect()->back()->with('status', 'Please select an Image');
        }

        try {
            $post = new Post;
            $post->poster_id = 0;
            $post->poster_type = PostType::admin;
            $post->header = $postHeader;
            $post->caption = $postCaption;
            $post->moderate_status = $moderate_status;
            $post->push_status = 1;
            $post->image_url = $media_url;
            $post->post_link = $postLink;
            $post->scheduled_at = $postSchedule;
            $post->media_type = $media_type;
            $post->save();
        } catch (\Exception $e) {
            return redirect('admin/post')->with('status', 'Something went wrong!');
        }

        $scroll_id = 0;
        $posts = Post::orderBy('id', 'DESC')->get();
        foreach ($posts as $key => $value) {
            if ($value->id == $post->id) {
                $scroll_id = $key;
            }
        }

        if ($postSchedule == null) {
            $android = PlatformType::android;
            $ios = PlatformType::ios;

            $android_data = collect(DB::select("SELECT *
                                                FROM customer_login_sessions
                                                WHERE id IN (
                                                    SELECT MAX(id)
                                                    FROM customer_login_sessions
                                                    where platform = '$android'
                                                    GROUP BY customer_id)"))
                ->where('status', LoginStatus::logged_in)->pluck('physical_address');

            $ios_data = collect(DB::select("SELECT *
                                                FROM customer_login_sessions
                                                WHERE id IN (
                                                    SELECT MAX(id)
                                                    FROM customer_login_sessions
                                                    where platform = '$ios'
                                                    GROUP BY customer_id)"))
                ->where('status', LoginStatus::logged_in)->pluck('physical_address');

            $f_android_result = array_chunk($android_data->toArray(), Constants::notification_chunk);
            $f_ios_result = array_chunk($ios_data->toArray(), Constants::notification_chunk);
            foreach ($f_android_result as $customers) {
                (new jsonController)->sendFirebaseFeedNotification('Royalty', $postHeader, $customers, $scroll_id, $media_url,
                    PushNotificationType::FROM_NEWSFEED);
            }
            foreach ($f_ios_result as $customers) {
                (new jsonController)->sendFirebaseIOSFeedNotification('Royalty', $postHeader, $customers, $scroll_id, $media_url,
                    PushNotificationType::FROM_NEWSFEED);
            }
        } else {
            Post::where('id', $post->id)->update(['push_status' => 0]);
        }

        return redirect('admin/post')->with('status', 'Post Created Successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //dd('show');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return View
     */
    public function edit($id)
    {
        $allPosts = Post::findOrFail($id);

        return view('admin.production.posts.edit', compact('allPosts'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'postHeader' => 'required',
        ]);
        $request->flash(['postHeader']);
        //get the updating instance
        $post = Post::findOrFail($id);

        $postHeader = $request->get('postHeader');
        $postCaption = $request->get('postCaption');
        $postLink = $request->get('postLink');
        $postSchedule = $request->postSchedule != null ? $request->postSchedule : $post->scheduled_at;

        //upload image to aws & save url to DB
        if ($request->hasFile('postImage')) {
            $file = $request->file('postImage');
            $filesize = filesize($file) / 1000000;
            if ($filesize > 10) {//if file size is too large
                return redirect()->back()->with('error', 'File too large.');
            }
            $mime = $_FILES['postImage']['type'];
            $media_path = $post->image_url;
            $exploded_path = explode('/', $media_path);

            if (strstr($mime, 'video/')) {
                $media_url = (new functionController)->uploadVideoToAWS($file, 'videos/newsfeed');
                $media_type = MediaType::VIDEO;
            } elseif (strstr($mime, 'image/')) {
                $media_url = (new functionController)->uploadImageToAWS($file, 'dynamic-images/admin_post_image');
                $media_type = MediaType::IMAGE;
            }

            if ($post->media_type == MediaType::IMAGE) {
                Storage::disk('s3')->delete('dynamic-images/admin_post_image/'.end($exploded_path));
            } else {
                Storage::disk('s3')->delete('videos/newsfeed/'.end($exploded_path));
            }

            $post->header = $postHeader;
            $post->caption = $postCaption;
            $post->image_url = $media_url;
            $post->media_type = $media_type;
            $post->post_link = $postLink;
            $post->scheduled_at = $postSchedule;
            $post->save();
        } else {
            $post->header = $postHeader;
            $post->caption = $postCaption;
            $post->post_link = $postLink;
            $post->scheduled_at = $postSchedule;
            $post->save();
        }

        return redirect('admin/post')->with('status', 'Post Updated Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $image_path = $post->image_url;
        //at first delete the previous image
        $exploded_path = explode('/', $image_path);
        //remove previous profile image from bucket
        Storage::disk('s3')->delete('dynamic-images/admin_post_image/'.end($exploded_path));
        //delete post from count table
        SharePost::where('post_id', $id)->delete();
        $post->delete();

        return redirect('admin/post')->with('delete post', 'Post Deleted Successfully!');
    }

    //Post Active/Deactive
    public function activate_post($id)
    {
        Post::where('id', $id)->update(['moderate_status' => 1]);
        $postDetails = Post::findOrFail($id);
        if ($postDetails->push_status == 0) {
            Post::where('id', $id)->update(['push_status' => 1]);

            $scroll_id = 0;
            $posts = Post::orderBy('id', 'DESC')->get();
            foreach ($posts as $key => $value) {
                if ($value->id == $postDetails->id) {
                    $scroll_id = $key;
                }
            }

            $android = PlatformType::android;
            $ios = PlatformType::ios;
            $android_data = collect(DB::select("SELECT *
                                                FROM customer_login_sessions
                                                WHERE id IN (
                                                    SELECT MAX(id)
                                                    FROM customer_login_sessions
                                                    where platform = '$android'
                                                    GROUP BY customer_id)"))
                ->where('status', LoginStatus::logged_in)->pluck('physical_address');

            $ios_data = collect(DB::select("SELECT *
                                                FROM customer_login_sessions
                                                WHERE id IN (
                                                    SELECT MAX(id)
                                                    FROM customer_login_sessions
                                                    where platform = '$ios'
                                                    GROUP BY customer_id)"))
                ->where('status', LoginStatus::logged_in)->pluck('physical_address');

            $f_android_result = array_chunk($android_data->toArray(), Constants::notification_chunk);
            $f_ios_result = array_chunk($ios_data->toArray(), Constants::notification_chunk);

            foreach ($f_android_result as $customers) {
                (new jsonController)->sendFirebaseFeedNotification('Royalty', $postDetails->header,  $customers, $scroll_id,
                    $postDetails->image_url, PushNotificationType::FROM_NEWSFEED);
            }
            foreach ($f_ios_result as $customers) {
                (new jsonController)->sendFirebaseIOSFeedNotification('Royalty', $postDetails->header,  $customers, $scroll_id,
                    $postDetails->image_url, PushNotificationType::FROM_NEWSFEED);
            }
        }

        return redirect()->back()->with('status', 'Post activated successfully');
    }

    public function deactivate_post($id)
    {
        Post::where('id', $id)->update(['moderate_status' => 0]);

        return redirect()->back()->with('status', 'Post deactivated successfully');
    }

    //post pin/unpin
    public function unpinPost($id)
    {
        Post::where('id', $id)->update(['pinned_post' => 0]);

        return redirect()->back()->with('post_unpinned', 'Post unpinned successfully');
    }

    public function pinPost($id)
    {
        Post::where('id', $id)->update(['pinned_post' => 1]);
        Post::where('id', '!=', $id)->update(['pinned_post' => 0]);

        return redirect()->back()->with('post_pinned', 'Post pinned successfully');
    }
}
