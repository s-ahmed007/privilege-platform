<?php

namespace App\Http\Controllers;

use App\Http\Controllers\functionController;
use App\Http\Controllers\sksort;
use Auth;
use Datetime;
use DB;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ViewErrorBag;
use Mail;
use Pusher\Pusher;
use Session;
use View;

class pusherController extends Controller
{
    //function to set pusher credentials
    public function initializePusher()
    {
        //Remember to change this with your cluster name.
        $options = [
            'cluster' => 'ap2',
            'encrypted' => true,
        ];

        //Remember to set your credentials below.
        $pusher = new Pusher(
            'cd1798cb19ea196ecaf2', // public key
            '3ad18e1e700ef53156bf', // Secret
            '505554', // App_id
            $options
        );

        return $pusher;
    }

    //function to append customer notification
    public function appendCustomerNotification($param)
    {
        //got pusher set up from this function
        $pusher = $this->initializePusher();
        $allNotifications = (new functionController)->allNotifications($param['customer_id']);
        $output = $this->customerNotificationView($allNotifications);

        $result['customerNotification'] = $output;
        $result['info'] = $param;
        //Send a message to notify channel with an event name of notify-event
        $pusher->trigger('append', 'appendNotification-event', $result);
    }

    //function to append partner notification
    public function appendPartnerNotification($param)
    {
        //got pusher set up from this function
        $pusher = $this->initializePusher();
        $allNotifications = (new functionController)->partnerAllNotifications($param);
        $output = $this->partnerNotificationView($allNotifications);

        $result['partnerNotification'] = $output;
        $result['partner_id'] = $param;
        //Send a message to notify channel with an event name of notify-event
        $pusher->trigger('appendPartnerNotification', 'appendPartnerNotification-event', $result);
    }

    //function to set pusher for live like notification
    public function liveLikeNotification($parameter)
    {
        //got pusher set up from this function
        $pusher = $this->initializePusher();
        $allNotifications = (new functionController)->allNotifications($parameter['customer_id']);
        $output = $this->customerNotificationView($allNotifications);

        $result['unseenNotification'] = $output;
        $result['likeInfo'] = $parameter;
        //Send a message to notify channel with an event name of notify-event
        $pusher->trigger('like', 'like-event', $result);

        return $parameter;
    }

    //function to get customer notification view
    public function customerNotificationView(Request $request)
    {
        $customer_id = $request->input('customer_id');
        $allNotifications = (new functionController)->allNotifications($customer_id);

        $output = '';
        $output .= '<a href="#">';
        $output .= '<i class="bell-icon"></i>';
        if ($allNotifications['unseen'] != 0) {
            $output .= '<span class="notify_num" id="customer_notification_number">'.$allNotifications['unseen'].'</span>';
        }
        $output .= '</a>';
        $output .= '<ul id="notification_ul">';
        $output .= '<div class="drop-content">';
        $output .= '<li>
                       <p class="notification-header" style="float: left;">Your Notifications</p>
                       <a href="'.url('mark_user_all_notifications_as_read').'" style="display: inline-block; float: right; font-size: 1.2rem">Read all</a>
                    </li>';

        if (isset($allNotifications['today']) && count($allNotifications['today']) > 0) {
            $output .= '<li><div class="col-md-6">Today</div></li>';
            $notifications = (new functionController)->getNotificationView($allNotifications['today']);
            $output .= $notifications;
        }
        if (isset($allNotifications['yesterday']) && count($allNotifications['yesterday']) > 0) {
            $output .= '<li><div class="col-md-6">Yesterday</div></li>';
            $notifications = (new functionController)->getNotificationView($allNotifications['yesterday']);
            $output .= $notifications;
        }
        if (isset($allNotifications['this_week']) && count($allNotifications['this_week']) > 0) {
            $output .= '<li><div class="col-md-6">This week</div></li>';
            $notifications = (new functionController)->getNotificationView($allNotifications['this_week']);
            $output .= $notifications;
        }
        if (isset($allNotifications['earlier']) && count($allNotifications['earlier']) > 0) {
            $output .= '<li><div class="col-md-6">Earlier</div></li>';
            $notifications = (new functionController)->getNotificationView($allNotifications['earlier']);
            $output .= $notifications;
        }

        $output .= '</div>';
        $output .= '<li>
                       <div class="notify-drop-footer text-center">
                          <a href="'.url('user/all-notifications').'">View all notifications</a>
                       </div>
                    </li>';
        $output .= '</ul>';

        return Response::json($output);
    }

    //function to set pusher for live discount notification
    public function liveDiscountNotification($parameter)
    {
        $pusher = $this->initializePusher();
        $allNotifications = (new functionController)->allNotifications($parameter);
        $output = $this->customerNotificationView($allNotifications);
        $result['unseenNotification'] = $output;
        $result['customer_id'] = $parameter;
        //Send a message to notify channel with an event name of notify-event
        $pusher->trigger('discount', 'discount-event', $result);
    }

    //function to set pusher for live reply notification
    public function liveReplyNotification($parameter)
    {
        $pusher = $this->initializePusher();
        $allNotifications = (new functionController)->allNotifications($parameter);
        $output = $this->customerNotificationView($allNotifications);

        $result['unseenNotification'] = $output;
        $result['customer_id'] = $parameter;
        //Send a message to notify channel with an event name of notify-event
        $pusher->trigger('reply', 'reply-event', $result);
    }

    //function to set pusher for live follow notification
    public function liveCustomerFollowNotification($parameter)
    {
        $pusher = $this->initializePusher();
        $allNotifications = (new functionController)->allNotifications($parameter);
        $output = $this->customerNotificationView($allNotifications);
        $result['unseenNotification'] = $output;
        $result['customer_id'] = $parameter;
        //Send a message to notify channel with an event name of notify-event
        $pusher->trigger('follow', 'follow-event', $result);
    }

    //function to set pusher for live follow notification
//    public function liveCancelCustomerFollowNotification($parameter)
//    {
//        $pusher = $this->initializePusher();
//        $unseenNotification = (new functionController)->customerUnseenNotifications($parameter);
//        //get customer notification view with unseen notification variable
//        $output = $this->customerNotificationView($parameter, $unseenNotification);
//        $result['unseenNotification'] = $output;
//        $result['customer_id'] = $parameter;
//        //Send a message to notify channel with an event name of notify-event
//        $pusher->trigger('cancel-follow', 'cancel-follow-event', $result);
//    }
//
//    //function to set pusher for live unfollow notification
//    public function liveCustomerUnfollowNotification($parameter)
//    {
//        $pusher = $this->initializePusher();
//        $unseenNotification = (new functionController)->customerUnseenNotifications($parameter);
//        //get customer notification view with unseen notification variable
//        $output = $this->customerNotificationView($parameter, $unseenNotification);
//        $result['unseenNotification'] = $output;
//        $result['customer_id'] = $parameter;
//        //Send a message to notify channel with an event name of notify-event
//        $pusher->trigger('unfollow', 'unfollow-event', $result);
//    }

    //function to set pusher for live follow request accept notification
    public function liveFollowRequestAcceptNotification($parameter)
    {
        $pusher = $this->initializePusher();
        $allNotifications = (new functionController)->allNotifications($parameter);
        $output = $this->customerNotificationView($allNotifications);
        $result['unseenNotification'] = $output;
        $result['customer_id'] = $parameter;
        //Send a message to notify channel with an event name of notify-event
        $pusher->trigger('acceptFollowRequest', 'acceptFollowRequest-event', $result);
    }

    //function to set pusher for live birthday notification
    public function liveBirthdayNotification($parameter)
    {
        $pusher = $this->initializePusher();
        $allNotifications = (new functionController)->allNotifications($parameter);
        $output = $this->customerNotificationView($allNotifications);
        $result['unseenNotification'] = $output;
        $result['customer_id'] = $parameter;
        //Send a message to notify channel with an event name of notify-event
        $pusher->trigger('birthday', 'birthday-event', $result);
    }

    //function to set pusher for live refer notification
    public function liveCustomerReferNotification($parameter)
    {
        $pusher = $this->initializePusher();
        $allNotifications = (new functionController)->allNotifications($parameter);
        $output = $this->customerNotificationView($allNotifications);
        $result['unseenNotification'] = $output;
        $result['customer_id'] = $parameter;
        //Send a message to notify channel with an event name of notify-event
        $pusher->trigger('refer', 'refer-event', $result);
    }

    //function to set pusher for live refer notification
    public function liveCustomerReferCouponNotification($parameter)
    {
        $pusher = $this->initializePusher();
        $allNotifications = (new functionController)->allNotifications($parameter);
        $output = $this->customerNotificationView($allNotifications);
        $result['unseenNotification'] = $output;
        $result['customer_id'] = $parameter;
        //Send a message to notify channel with an event name of notify-event
        $pusher->trigger('250tkCoupon', '250tkCoupon-event', $result);
    }

    //function to get partner notification view
    public function partnerNotificationView($allNotifications)
    {
        $output = '';
        $output .= '<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"';
        $output .= 'aria-expanded="false">';
        $output .= '<i class="bell-icon" aria-hidden="true"></i>';
        if ($allNotifications['unseen'] != 0) {
            $output .= '<span class="notify_num" id="partner_notification_number">'.$allNotifications['unseen'].'</span></a>';
        }
        $output .= '<ul>';
        $output .= '<div class="notify-drop-title"><div class="row"><div class="col-md-6 col-sm-6 col-xs-6">
                Your Notifications</div></div></div>';
        $output .= '<div class="drop-content">';

        if (isset($allNotifications['today']) && count($allNotifications['today']) > 0) {
            $output .= '<li><div class="col-md-6">Today</div></li>';
            $notifications = (new functionController)->getPartnerNotificationView($allNotifications['today']);
            $output .= $notifications;
        }
        if (isset($allNotifications['yesterday']) && count($allNotifications['yesterday']) > 0) {
            $output .= '<li><div class="col-md-6">Today</div></li>';
            $notifications = (new functionController)->getPartnerNotificationView($allNotifications['yesterday']);
            $output .= $notifications;
        }
        if (isset($allNotifications['this_week']) && count($allNotifications['this_week']) > 0) {
            $output .= '<li><div class="col-md-6">This week</div></li>';
            $notifications = (new functionController)->getPartnerNotificationView($allNotifications['this_week']);
            $output .= $notifications;
        }
        if (isset($allNotifications['earlier']) && count($allNotifications['earlier']) > 0) {
            $output .= '<li><div class="col-md-6">Earlier</div></li>';
            $notifications = (new functionController)->getPartnerNotificationView($allNotifications['earlier']);
            $output .= $notifications;
        }

        $output .= '</div>';
        $output .= '<div class="notify-drop-footer text-center">';
        $output .= '<a href="'.url('partner/all-notifications').'">
                <i class="eye-open"></i> View all notifications</a>';
        $output .= '</div></ul>';

        return $output;
    }

    //function to set pusher for live partner follow notification
    public function livePartnerFollowNotification($parameter)
    {
        $pusher = $this->initializePusher();
        //get partner's unseen notifications
        $unseenNotification = (new functionController)->partnerUnseenNotifications($parameter);
        $seenNotification = (new functionController)->partnerUnseenNotifications($parameter);
        //get partner name from partner id
        $partner_name = DB::table('partner_info')
                        ->select('partner_name')
                        ->where('partner_account_id', $parameter)
                        ->get();
        $partner_name = json_decode(json_encode($partner_name), true);
        //get partner notification view with unseen notifications
        $output = $this->partnerNotificationView($unseenNotification, $seenNotification);
        //followers info of this partner
        $followers = DB::table('follow_partner')
            ->where('following', $parameter)
            ->get();
        $followers = json_decode(json_encode($followers), true);
        $followers_info = [];
        foreach ($followers as $value) {
            $follower_info = DB::table('customer_account as ca')
                ->join('customer_info as ci', 'ca.customer_id', '=', 'ci.customer_id')
                ->select('ca.customer_id', 'ca.customer_username', 'ci.customer_first_name', 'ci.customer_last_name', 'ci.customer_profile_image', 'ci.customer_type')
                ->where('ca.customer_id', $value['follower'])
                ->first();
            $follower_info = json_decode(json_encode($follower_info), true);
            array_push($followers_info, $follower_info);
        }
        //output followers list of partner
        $output1 = '';
        $output1 = '<div class="modal-dialog">';
        $output1 .= '<div class="modal-content">';
        $output1 .= '<div class="modal-header">';
        $output1 .= '<button type="button" class="close" data-dismiss="modal">';
        $output1 .= '<i class="fa fa-close"></i>';
        $output1 .= '</button>';
        $output1 .= '<h4 class="modal-title">Followers of '.$partner_name[0]['partner_name'].'</h4>';
        $output1 .= '</div>';
        $output1 .= '<div class="modal-body" id="profile_modal" class="profile_modal" style="text-align: unset">';
        if (isset($followers_info) && count($followers_info) > 0) {
            foreach ($followers_info as $info) {
                $output1 .= '<div class="row" style="margin: 0 0 10px 0;">';
                $output1 .= '<div class="col-md-8">';
                $output1 .= '<a target="_blank" href="'.url('user-profile/'.$info['customer_username']).'">';
                $output1 .= '<img class="lazyload image-left" src="'.asset($info['customer_profile_image']).'">';
                $output1 .= '<p class="heading-right">'.$info['customer_first_name'].' '.$info['customer_last_name'];
                $output1 .= '</p><br>';
                $output1 .= '<p class="sub-heading-right">';
                $output1 .= '<i class="fa fa-star"></i> ';
                if ($info['customer_type'] == 1) {
                    $output1 .= 'Gold Member';
                } elseif ($info['customer_type'] == 2) {
                    $output1 .= 'Platinum Member';
                } else {
                    $output1 .= 'Member';
                }
                $output1 .= '</p></a></div>';
                $output1 .= '<div class="col-md-4" style="margin-top: 11px;text-align: center">';
                $output1 .= '<i class="review-icon"></i>&nbsp;'.functionController::reviewNumber($info['customer_id']);
                $output1 .= '<i class="fa fa-thumbs-up"></i>&nbsp;'.functionController::likeNumber($info['customer_id']);
                $output1 .= '</div></div>';
            }
        } else {
            $output1 .= '<p>No followers</p>';
        }
        $output1 .= '</div></div></div>';

        $result['unseenNotification'] = $output;
        $result['partner_id'] = $parameter;
        $result['total_followers'] = count($followers_info);
        $result['followers_list'] = $output1;
        //Send a message to notify channel with an event name of notify-event
        $pusher->trigger('partnerFollow', 'partnerFollow-event', $result);
    }

    //function to set pusher for live partner follow notification
    public function liveReviewNotification($parameter)
    {
        $pusher = $this->initializePusher();
        $allNotifications = (new functionController)->partnerAllNotifications($parameter);
        $output = $this->partnerNotificationView($allNotifications);
        $result['unseenNotification'] = $output;
        $result['partner_id'] = $parameter;
        //Send a message to notify channel with an event name of notify-event
        $pusher->trigger('createReview', 'createReview-event', $result);
    }

    //function to set pusher for live post like notification
    public function livePostLikeNotification($parameter)
    {
        $pusher = $this->initializePusher();
        $allNotifications = (new functionController)->partnerAllNotifications($parameter);
        $output = $this->partnerNotificationView($allNotifications);

        $result['unseenNotification'] = $output;
        $result['partner_id'] = $parameter;
        //Send a message to notify channel with an event name of notify-event
        $pusher->trigger('postLike', 'postLike-event', $result);
    }
}
