<?php

namespace App\Http\Controllers\Newsfeed;

use App\BranchUser;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Enum\PostType;
use App\Http\Controllers\functionController;
use App\Http\Controllers\Newsfeed\functionController as newsFeedFunctionController;
use App\Http\Controllers\TransactionRequest\v2\webController;
use App\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class merchantWebController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        $user = BranchUser::where('id', session('branch_user_id'))->with('branchScanner.scannerReward')->first();
        $point = $user->branchScanner->scannerReward->point;
        $posts = Post::where('poster_id', session('branch_id'))->orderBy('id', 'DESC')->withCount('sharePost')
            ->withCount('like')->get();
        $allNotifications = (new webController())->getAllNotifications(session('branch_user_id'));

        return view('partner-dashboard.post.index', compact('allNotifications', 'posts', 'point'));
    }

    public function pendingPosts()
    {
        $user = BranchUser::where('id', session('branch_user_id'))->with('branchScanner.scannerReward')->first();
        $point = $user->branchScanner->scannerReward->point;
        $posts = Post::where('poster_id', session('branch_id'))->where('moderate_status', 0)->get();
        $allNotifications = (new webController())->getAllNotifications(session('branch_user_id'));

        return view('partner-dashboard.post.index', compact('allNotifications', 'posts', 'point'));
    }

    public function approvedPosts()
    {
        $user = BranchUser::where('id', session('branch_user_id'))->with('branchScanner.scannerReward')->first();
        $point = $user->branchScanner->scannerReward->point;
        $posts = Post::where('poster_id', session('branch_id'))->where('moderate_status', 1)->get();
        $allNotifications = (new webController())->getAllNotifications(session('branch_user_id'));

        return view('partner-dashboard.post.index', compact('allNotifications', 'posts', 'point'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create()
    {
        $user = BranchUser::where('id', session('branch_user_id'))->with('branchScanner.scannerReward')->first();
        $point = $user->branchScanner->scannerReward->point;
        $allNotifications = (new webController())->getAllNotifications(session('branch_user_id'));

        return view('partner-dashboard.post.create', compact('allNotifications', 'point'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
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

        //upload image to aws & save url to DB
        if ($request->hasFile('postImage')) {
            $file = $request->file('postImage');
        } else {
            return redirect()->back()->with('status', 'Please select an Image');
        }
        //image is being resized & uploaded here
        $image_url = (new functionController)->uploadImageToAWS($file, 'dynamic-images/admin_post_image');
        $post = (new newsFeedFunctionController())->addPost(
            session('branch_id'),
            $postHeader,
            $postCaption,
            $image_url
        );

        return redirect('partner/branch/post')->with('status', 'Your post is under moderation. Our team will review 
            it if everything is alright then it will be posted to Royalty News Feed. Thank you.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return View
     */
    public function show($id)
    {
        $user = BranchUser::where('id', session('branch_user_id'))->with('branchScanner.scannerReward')->first();
        $point = $user->branchScanner->scannerReward->point;
        $posts = Post::where('poster_id', session('branch_id'))->where('id', $id)->withCount('like')->get();
        $allNotifications = (new webController())->getAllNotifications(session('branch_user_id'));

        return view('partner-dashboard.post.index', compact('allNotifications', 'posts', 'point'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return View
     */
    public function edit($id)
    {
        $post = Post::find($id);
        $user = BranchUser::where('id', session('branch_user_id'))->with('branchScanner.scannerReward')->first();
        $point = $user->branchScanner->scannerReward->point;
        $allNotifications = (new webController())->getAllNotifications(session('branch_user_id'));

        return view('partner-dashboard.post.edit', compact('allNotifications', 'post', 'point'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'postHeader' => 'required',
        ]);
        $request->flashOnly(['postHeader']);

        $postHeader = $request->get('postHeader');
        $postCaption = $request->get('postCaption') != null ? $request->get('postCaption') : null;
        $post = Post::findOrFail($id);
        if ($request->hasFile('postImage')) {
            $file = $request->file('postImage');
            //at first delete the previous image
            $image_path = $post->image_url;
            $exploded_path = explode('/', $image_path);
            //remove previous profile image from bucket
            Storage::disk('s3')->delete('dynamic-images/admin_post_image/'.end($exploded_path));
            //image is being resized & uploaded here
            $image_url = (new functionController)->uploadImageToAWS($file, 'dynamic-images/admin_post_image');
        } else {
            $image_url = $post->image_url;
        }
        (new newsFeedFunctionController())->editPost($id, $postHeader, $postCaption, $image_url);

        return redirect('partner/branch/post')->with('status', 'Post Updated Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return RedirectResponse
     */
    public function destroy($id)
    {
        (new newsFeedFunctionController())->deletePost($id);

        return redirect('partner/branch/post')->with('status', 'Post Deleted Successfully!');
    }
}
