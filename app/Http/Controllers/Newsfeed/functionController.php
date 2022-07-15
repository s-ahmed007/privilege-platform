<?php

namespace App\Http\Controllers\Newsfeed;

use App\BranchUserNotification;
use App\CustomerInfo;
use App\Events\like_post;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Enum\BranchUserRole;
use App\Http\Controllers\Enum\Constants;
use App\Http\Controllers\Enum\LoginStatus;
use App\Http\Controllers\Enum\PartnerBranchNotificationType;
use App\Http\Controllers\Enum\PlatformType;
use App\Http\Controllers\Enum\PostType;
use App\Http\Controllers\Enum\PushNotificationType;
use App\Http\Controllers\Firebase\Merchant\SetupController;
use App\Http\Controllers\jsonController;
use App\PartnerBranch;
use App\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class functionController extends Controller
{
    public function addPost($branch_id, $title, $caption, $image)
    {
        $post = new Post;
        $post->poster_id = $branch_id;
        $post->poster_type = PostType::partner;
        $post->header = $title;
        $post->caption = $caption;
        $post->moderate_status = 0;
        $post->push_status = 0;
        $post->image_url = $image;
        $post->save();
        (new \App\Http\Controllers\AdminNotification\functionController())->partnerAddedNewPost($post);

        return $post;
    }

    public function setPostLikeNotification($post, $customer, $like_id)
    {
        $branch = PartnerBranch::where('id', $post->poster_id)->with('branchScanner.branchUser')->first();
        $notification_text = $customer->customer_full_name.' has liked your post.';
        foreach ($branch->branchScanner as $branch_scanner) {
            if ($branch_scanner->branchUser->role == BranchUserRole::branchOwner) {
                $notification = new BranchUserNotification();
                $notification->branch_user_id = $branch_scanner->branch_user_id;
                $notification->customer_id = $customer->customer_id;
                $notification->notification_text = $notification_text;
                $notification->notification_type = PartnerBranchNotificationType::LIKE_POST;
                $notification->source_id = $like_id;
                $notification->seen = 0; //not seen
                $notification->posted_on = date('Y-m-d H:i:s'); //not seen
                $notification->save();

                event(new like_post($post->poster_id));
                (new SetupController())->sendMerchantGlobalMessage(
                    $notification_text,
                    $branch_scanner->branchUser->f_token
                );
            }
        }
    }

    public function editPost($post_id, $title, $caption, $image)
    {
        $post = Post::find($post_id);
        if ($post) {
            $post->header = $title;
            $post->caption = $caption;
            $post->moderate_status = 0;
            $post->image_url = $image;
            $post->save();
            (new \App\Http\Controllers\AdminNotification\functionController())->partnerEditedPost($post);
            return $post;
        } else {
            return null;
        }
    }

    public function deletePost($post_id)
    {
        $post = Post::find($post_id);
        $post->delete();
        (new \App\Http\Controllers\AdminNotification\functionController())->partnerDeletedPost($post);

        return 'Post deleted successfully';
    }

    public function getAllPosts($branch_id, $post_id = null)
    {
        if (! $post_id) {
            return Post::where('poster_id', $branch_id)
                ->where('poster_type', PostType::partner)->withCount('like')->orderBy('id', 'desc')->get();
        } else {
            return Post::where('poster_id', $branch_id)->where('id', $post_id)
                ->where('poster_type', PostType::partner)->withCount('like')->orderBy('id', 'desc')->get();
        }
    }

    public function getPost($post_id)
    {
        return Post::where('id', $post_id)->withCount('like')->first();
    }

    public function sendPostNotification($post)
    {
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
            (new jsonController)->sendFirebaseFeedNotification('Royalty', $post->header, $customers, $this->getScrollId($post),
                $post->image_url, PushNotificationType::FROM_NEWSFEED);
        }
        foreach ($f_ios_result as $customers) {
            (new jsonController)->sendFirebaseIOSFeedNotification('Royalty', $post->header, $customers, $this->getScrollId($post),
                $post->image_url, PushNotificationType::FROM_NEWSFEED);
        }
    }

    public function getScrollId($post)
    {
        $scroll_id = 0;
        $posts = Post::orderBy('id', 'DESC')->get();
        foreach ($posts as $key => $value) {
            if ($value->id == $post->id) {
                $scroll_id = $key;
            }
        }

        return $scroll_id;
    }
}
