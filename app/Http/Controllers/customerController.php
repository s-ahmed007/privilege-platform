<?php

namespace App\Http\Controllers;

use App\Categories;
use App\CustomerAccount;
use App\CustomerInfo;
use App\CustomerPoint;
use App\Events\append_notification;
use App\Events\check_email_verify_status;
use App\Events\like_post;
use App\Events\like_review;
use App\Events\offer_availed;
use App\Http\Controllers\Enum\Constants;
use App\Http\Controllers\Enum\DeliveryType;
use App\Http\Controllers\Enum\LikerType;
use App\Http\Controllers\Enum\MiscellaneousType;
use App\Http\Controllers\Enum\notificationType;
use App\Http\Controllers\Enum\PointType;
use App\Http\Controllers\Enum\PostType;
use App\Http\Controllers\Enum\VerificationType;
use App\Http\Controllers\Enum\ReviewType;
use App\Http\Controllers\functionController;
use App\Http\Controllers\homeController;
use App\Http\Controllers\OTP\functionController as otpFunctionController;
use App\Http\Controllers\Reward\functionController as rewardFunctionController;
use App\Http\Controllers\LoginRegister\functionController as loginFunctionController;
use App\InfoAtBuyCard;
use App\LikePost;
use App\LikesReview;
use App\PartnerBranch;
use App\Post;
use App\ResetUser;
use App\SocialId;
use App\TransactionTable;
use Carbon\Carbon;
use Datetime;
use DB;
use File;
use function foo\func;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Session;
use SMTPValidateEmail\Validator as SmtpEmailValidator;
use Storage;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;
use View;

class customerController extends Controller
{
    //function to sort transaction history month wise
    public function sortTransactionHistory(Request $request)
    {
        $month = $request->input('month');
        $year = $request->input('year');
        $check_all_year = 0;
        $check_all_month = 0;
        $allTransactions = TransactionTable::where('customer_id', Session::get('customer_id'))
            ->with('branch.info.profileImage', 'offer')
            ->orderBy('id', "DESC")
            ->get();
        $allTransactions = $allTransactions->where('offer.selling_point', null);

        if ($year != 'all' && $month != 'all') {
            foreach ($allTransactions as $key => $value) {
                $ex = explode('-', $value['posted_on']);
                //checking if DB=>"month,year" & selected=>"month,year" are same or not
                if ($ex[0] != $year || $ex[1] != $month) {
                    //unset specific array index if not match
                    unset($allTransactions[$key]);
                }
            }
        } elseif ($year != 'all' && $month == 'all') {
            foreach ($allTransactions as $key => $value) {
                $ex = explode('-', $value['posted_on']);
                //checking if DB=>year & selected=>year are same or not
                if ($ex[0] != $year) {
                    //unset specific array index if not match
                    unset($allTransactions[$key]);
                }
            }
            $check_all_year = 1;
        } else {
            $check_all_month = 1;
        }

        $output = '';
        $total_point_got = 0;
        if (count($allTransactions) > 0) {
            $output .= '<div class="table-responsive whitebox">';
            $output .= '<table class="table">';
            $output .= '<thead>';
            $output .= '<tr>';
            $output .= '<td>Date & Time</td>';
            $output .= '<td>Partners Visited</td>';
            $output .= '<td>Credits</td>';
            $output .= '<td>Offers Availed</td>';
            $output .= '<td>Review</td>';
            $output .= '</tr>';
            $output .= '</thead>';
            $output .= '<tbody>';

            foreach ($allTransactions as $tr) {
                $total_point_got += $tr->transaction_point;
                $output .= '<tr>';
                $output .= '<th>';
                $posted_on = date("Y-M-d H:i:s", strtotime($tr->posted_on));
                $created = \Carbon\Carbon::createFromTimeStamp(strtotime($posted_on));
                $output .= date_format($created, "h:i A d-m-y");
                $output .= '</th>';
                $output .= '<th>';
                $output .= $tr->branch->info->partner_name . " - " . $tr->branch->partner_area;
                $output .= '</th>';
                $output .= '<th>';
                $output .= $tr->redeem_id == null ? $tr->transaction_point : 'Reward';
                $output .= '</th>';
                $output .= '<th>';
                $output .= $tr->offer != null ? $tr->offer->offer_description : 'Discount Availed';
                $output .= '</th>';

                $output .= '<th>';
                if ($tr->review_id == null) {
                    $review_submit_url = url('createReview/' . $tr->branch->partner_account_id . '/' . $tr->id);
                    $output .= '<button class="btn btn-green" onclick="createReview(\''. $review_submit_url. '\', \''
                        .ReviewType::OFFER.'\')">Review</button>';
                } else {
                    $output .= '<span class="btn" style="color:#fff; background-color: #ffc107;cursor: default;
                                  padding: 0px 5px;">Reviewed</span>';
                }
                $output .= '</th>';
                $output .= "</tr>";
            }
            $output .= '<tr>';
            $output .= '<td><i class="minus-icon"></i></td>';
            $output .= '<td><i class="minus-icon"></i></td>';
            $output .= '<td>'.$total_point_got.'</td>';
            $output .= '<td><i class="minus-icon"></i></td>';
            $output .= '<td><i class="minus-icon"></i></td>';
            $output .= '</tr>';
        } elseif (count($allTransactions) == 0 && $check_all_month == 1) {
            $output .= '<div class="no-info"><h4>No offers availed yet.</h4></div>';
        } elseif (count($allTransactions) == 0 && $check_all_year == 1) {
            $output .= '<div class="no-info"><h4>No offers availed during this time.</h4></div>';
        } else {
            $output .= '<div class="no-info"><h4>No offers availed during this time.</h4></div>';
        }

        return Response::json($output);
    }

    //Customer edit profile form
    public function editByCustomerForm()
    {
        $profileInfo = DB::table('customer_info as ci')
            ->join('customer_account as ca', 'ca.customer_id', '=', 'ci.customer_id')
            ->select('ci.*', 'ca.customer_username', 'ca.pin')
            ->where('ci.customer_id', Session::get('customer_id'))
            ->first();
        //send all data to the respective edit profile page
        return view('customerEditProfile', compact('profileInfo'));
    }

    //function to crop user image in admin dashboard
    public function editUserImageSelf()
    {
        $data = $_POST['image'];
        list($type, $data) = explode(';', $data);
        list(, $data) = explode(',', $data);
        $data = base64_decode($data);
        $imageName = time().'.jpg';
        Session::put('user_profile_image_name', $imageName);
        Session::put('user_profile_image', $data);

        echo 'Image Uploaded';
    }

    //function to save customer new pro pic from customer account
    public function updateUserProPic(Request $request, $id)
    {
        if (Session::has('user_profile_image_name')) {
            //just update the new image info
            Storage::disk('s3')->put('dynamic-images/users/'.Session::get('user_profile_image_name'), Session::get('user_profile_image'), 'public');
            $image_url = Storage::disk('s3')->url('dynamic-images/users/'.Session::get('user_profile_image_name'));
            $point_exist = CustomerPoint::where('customer_id', $id)
                ->where('point_type', PointType::profile_completion_point)->first();

            (new functionController)->update_profile_image_link($image_url, $id);
            (new rewardFunctionController())->addProfileCompletionPoint($id);

            //remove session of cropped image
            $request->session()->forget('user_profile_image_name');
            $request->session()->forget('user_profile_image');
            if ($point_exist) {
                return redirect('users/'.Session::get('customer_username').'/info')->with('img_updated_without_percentage',
                    'Profile image updated!');
            } else {
                return redirect('users/'.Session::get('customer_username').'/info')->with('img_updated',
                    'Profile image updated!');
            }
        } else {
            return redirect()->back();
        }
    }

    //function to save customer new email from customer account
    public function updateUserEmail(Request $request)
    {
        $this->validate($request, [
            'customer_email' => 'required|email|unique:customer_info,customer_email|unique:partner_branch,partner_email',
        ]);
        $email = $request->get('customer_email');
        //update email with new one
        DB::table('customer_info')
            ->where('customer_id', Session::get('customer_id'))
            ->update(['customer_email' => $email, 'email_verified' => 0]);

        return Redirect('edit-profile')->with(
            'email_updated',
            'Email successfully updated'
        );
    }

    //function to save customer new username from customer account
    public function updateUserUsername(Request $request)
    {
        $this->validate($request, [
            'customer_username' => 'required|unique:customer_account,customer_username|regex:/^\S*$/u',
        ]);
        $username = $request->get('customer_username');
        //update username in info at buy card table if user exists
        $old_info = CustomerAccount::where('customer_id', Session::get('customer_id'))->first();
        InfoAtBuyCard::where('customer_username', $old_info->customer_username)
            ->update([
                'customer_username' => $username,
            ]);

        //update username with new one
        DB::table('customer_account')
            ->where('customer_id', Session::get('customer_id'))
            ->update(['customer_username' => $username]);
        //update session
        session(['customer_username' => $username]);

        return Redirect('edit-profile')->with(
            'username_updated',
            'Username successfully updated'
        );
    }

    //function to save customer new pin from customer account
    public function updateUserPin(Request $request)
    {
        $this->validate($request, [
            'new_pin' => 'required|digits:4',
        ]);

        if ($request->get('current_pin')) {
            if ($request->new_pin != $request->current_pin) {
                //match current pin
                $encrypted_pin = (new functionController)->encrypt_decrypt('encrypt', $request->current_pin);
                $pin_match = CustomerAccount::where([['customer_id', session('customer_id')], ['pin', $encrypted_pin]])->count();
                if (! $pin_match) {
                    return \redirect()->back()->with('wrong_cur_credential', 'Current PIN did not match');
                }
            } else {
                return \redirect()->back()->with('error_new_pin', 'Please choose a different PIN');
            }
        }
        //else{
        //            //match current password
        //            $encrypted_pass = (new functionController)->encrypt_decrypt('encrypt', $request->current_password);
        //            $pass_match = CustomerAccount::where([['customer_id', session('customer_id')],['password', $encrypted_pass]])->count();
        //            if(!$pass_match){
        //                return \redirect()->back()->with('wrong_cur_credential', 'Current password did not match.');
        //            }
        //        }

        $encrypted_pin = (new functionController)->encrypt_decrypt('encrypt', $request->new_pin);

        //update pin with new one
        DB::table('customer_account')
            ->where('customer_id', Session::get('customer_id'))
            ->update(['pin' => $encrypted_pin]);
        //update session
        session(['isPinSet' => true]);

        return Redirect()->back()->with(
            'pin_updated',
            'PIN successfully updated'
        );
    }

    //Function to save customer new phone from customer account
    public function updateUserPhone(Request $request)
    {
        $phone = $request->get('updatePhone');

        //update phone with new one
        $is_updated = DB::table('customer_info')
            ->where('customer_id', Session::get('customer_id'))
            ->update(['customer_contact_number' => $phone]);

        if ($is_updated == 1) {
            return Response::json('1');
        } else {
            return Response::json('0');
        }
    }

    //function to edit DOB from customer edit
    public function editDOB(Request $request)
    {
        $year = $request->input('birth_year');
        $month = $request->input('birth_month');
        $day = $request->input('birth_day');
        $date_of_birth = $year.'-'.$month.'-'.$day;

        $valid_date = checkdate($month, $day, $year);
        if ($valid_date == 'true') {
            try {
                $result = CustomerInfo::where('customer_id', Session::get('customer_id'))
                    ->update(['customer_dob' => $date_of_birth, 'birthday_status' => 1]);
            } catch (\Exception $e) {
                dd($e);
            }

            return redirect('users/'.Session::get('customer_username'))->with('dob_updated', 'Your date of birth has been updated.');
        } else {
            return redirect()->back()->with('invalid_dob', 'Invalid Date of Birth');
        }
    }

    //function for unsubscribe
    public function unsubscribe(Request $request)
    {
        $email = $request->input('email');
        DB::table('subscribers')
            ->where('email', $email)
            ->delete();

        return Response::json('unsubscribed');
    }

    //function to re subscribe
    public function subscribeAgain(Request $request)
    {
        $email = $request->input('email');
        DB::table('subscribers')->insert([
            'email' => $email,
        ]);

        return Response::json('subscribed');
    }

    //function for follow partner option
    public function followPartner(Request $request)
    {
        $id = $request->input('id');
        //check if this user already following this partner or not
        $alreadyFollowing = DB::table('follow_partner')
            ->where('follower', Session::get('customer_id'))
            ->where('following', $id)
            ->count();
        if ($alreadyFollowing > 0) {
            //do nothing
            return Response::json($id);
        } else {
            try {
                DB::beginTransaction(); //to do query rollback

                DB::table('follow_partner')->insert(
                    [
                        'follower' => Session::get('customer_id'),
                        'following' => $id,
                    ]
                );
                //get last inserted id of follow_partner table
                $last_inserted_id = DB::table('follow_partner')
                    ->select('id')
                    ->orderBy('id', 'DESC')
                    ->take(1)
                    ->get();
                $last_inserted_id = json_decode(json_encode($last_inserted_id), true);
                $follow_id = $last_inserted_id[0]['id'];
                if (Session::get('customer_profile_image') == '') {
                    $profile_pic_link = 'images/user.png';
                } else {
                    $profile_pic_link = Session::get('customer_profile_image');
                }
                //insert info into notification table
                DB::table('partner_notification')->insert(
                    [
                        'partner_account_id' => $id,
                        'image_link' => $profile_pic_link,
                        'notification_text' => 'started following you',
                        'notification_type' => 4,
                        'source_id' => $follow_id,
                        'seen' => 0,
                    ]
                );
                DB::commit(); //to do query rollback
            } catch (\Exception $e) {
                DB::rollback(); //rollback all successfully executed queries
                exit();
            }
            //trigger 'livePushNotification' function to do live push notification
            (new pusherController)->livePartnerFollowNotification($id);

            return Response::json($id);
        }
    }

    //function for follow customer option
    public function followCustomer(Request $request)
    {
        $id = $request->input('id');
        //check if this user already following this user or not
        $alreadyFollowing = DB::table('follow_customer')
            ->where('follower', Session::get('customer_id'))
            ->where('following', $id)
            ->count();
        if ($alreadyFollowing > 0) {
            return Response::json($id);
        } else {
            try {
                DB::beginTransaction(); //to do query rollback

                DB::table('follow_customer')->insert(
                    [
                        'follower' => Session::get('customer_id'),
                        'following' => $id,
                    ]
                );
                //get last inserted id of follow_customer table
                $last_inserted_id = DB::table('follow_customer')
                    ->select('id')
                    ->orderBy('id', 'DESC')
                    ->take(1)
                    ->get();
                $last_inserted_id = json_decode(json_encode($last_inserted_id), true);
                $follow_id = $last_inserted_id[0]['id'];
                if (Session::get('customer_profile_image') == '') {
                    $profile_pic_link = 'images/user.png';
                } else {
                    $profile_pic_link = Session::get('customer_profile_image');
                }
                //insert info into notification table
                if (Session::get('customer_profile_image') != '') {
                    DB::table('customer_notification')->insert(
                        [
                            'user_id' => $id,
                            'image_link' => $profile_pic_link,
                            'notification_text' => 'wants to follow you.',
                            'notification_type' => 8,
                            'source_id' => $follow_id,
                            'seen' => 0,
                        ]
                    );
                }
                DB::commit(); //to do query rollback
            } catch (\Exception $e) {
                DB::rollback(); //rollback all successfully executed queries
                exit();
            }

            //trigger 'livePushNotification' function to do live push notification
            (new pusherController)->liveCustomerFollowNotification($id);
            //message to send as parameter
            $message = Session::get('customer_full_name').' wants to follow you';
            $customer = DB::table('customer_info')->where('customer_id', $id)->first();
            //send notification to app
            (new jsonController)->functionSendGlobalPushNotification($message, $customer);
        }
    }

    //function to cancel follow customer request option
    public function cancelFollowRequest(Request $request)
    {
        $following = $request->input('id');
        try {
            DB::beginTransaction(); //to do query rollback

            //get specific id of follow partner table
            $follow_id = DB::table('follow_customer')
                ->select('id')
                ->where('follower', Session::get('customer_id'))
                ->where('following', $following)
                ->first();
            //delete from customer notification table
            DB::table('customer_notification')
                ->where('user_id', $following)
                ->where('notification_type', 8)
                ->where('source_id', $follow_id->id)
                ->delete();
            //delete from follow customer table
            DB::table('follow_customer')
                ->where('follower', Session::get('customer_id'))
                ->where('following', $following)
                ->delete();

            DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            DB::rollback(); //rollback all successfully executed queries
            exit();
        }

        return Response::json($following);
    }

    //function for unfollow option of partner
    public function unfollowPartner(Request $request)
    {
        $id = $request->input('id');
        try {
            DB::beginTransaction(); //to do query rollback

            //get specific id of follow partner table
            $follow_id = DB::table('follow_partner')
                ->select('id')
                ->where('follower', Session::get('customer_id'))
                ->where('following', $id)
                ->first();
            //delete from partner notification table
            (new functionController)->deleteNotification($id, $follow_id->id, notificationType::partner_follow);
            //delete from follow partner table
            DB::table('follow_partner')
                ->where('follower', Session::get('customer_id'))
                ->where('following', $id)
                ->delete();
            //get partner name from partner id
            $partner_name = DB::table('partner_info')
                ->select('partner_name')
                ->where('partner_account_id', $id)
                ->get();
            $partner_name = json_decode(json_encode($partner_name), true);
            //total followers info of this partner
            $followers = DB::table('follow_partner')
                ->where('following', $id)
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

            DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            DB::rollback(); //rollback all successfully executed queries
            exit();
        }

        //output followers list of partner
        $output = '';
        $output .= '<div class="modal-dialog">';
        $output .= '<div class="modal-content">';
        $output .= '<div class="modal-header">';
        $output .= '<button type="button" class="close" data-dismiss="modal">';
        $output .= '<i class="cross-icon"></i>';
        $output .= '</button>';
        $output .= '<h4 class="modal-title">Followers of '.$partner_name[0]['partner_name'].'</h4>';
        $output .= '</div>';
        $output .= '<div class="modal-body" id="profile_modal" class="profile_modal" style="text-align: unset">';
        if (isset($followers_info) && count($followers_info) > 0) {
            foreach ($followers_info as $info) {
                $output .= '<div class="row" style="margin: 0 0 10px 0;">';
                $output .= '<div class="col-md-8">';
                $output .= '<a target="_blank" href="'.url('user-profile/'.$info['customer_username']).'">';
                $output .= '<img class="lazyload image-left" src="'.asset($info['customer_profile_image']).'">';
                $output .= '<p class="heading-right">'.$info['customer_full_name'];
                $output .= '</p><br>';
                $output .= '<p class="sub-heading-right">';
                $output .= '<i class="bx bxs-star yellow"></i> ';
                if ($info['customer_type'] == 1) {
                    $output .= 'Gold Member';
                } elseif ($info['customer_type'] == 2) {
                    $output .= 'Platinum Member';
                } else {
                    $output .= 'Member';
                }
                $output .= '</p></a></div>';
                $output .= '<div class="col-md-4" style="margin-top: 11px;text-align: center">';
                $output .= '<i class="review-icon"></i>&nbsp;'.functionController::reviewNumber($info['customer_id']);
                $output .= '<i class="like-icon"></i>&nbsp;'.functionController::likeNumber($info['customer_id']);
                $output .= '</div></div>';
            }
        } else {
            $output .= '<p>No followers</p>';
        }
        $output .= '</div></div></div>';

        $data['partner_id'] = $id;
        $data['total_followers'] = count($followers);
        $data['followers_list'] = $output;

        return Response::json($data);
    }

    //function for unfollow option of user
    public function unfollowCustomer(Request $request)
    {
        $id = $request->input('id');

        try {
            DB::beginTransaction(); //to do query rollback

            //get specific id of follow customer table
            $follow_id = DB::table('follow_customer')
                ->select('id')
                ->where('follower', Session::get('customer_id'))
                ->where('following', $id)
                ->first();
            //delete from follow customer table
            DB::table('follow_customer')
                ->where('follower', Session::get('customer_id'))
                ->where('following', $id)
                ->delete();
            //delete follow request notification (8)
            (new functionController)->deleteNotification($id, $follow_id->id, notificationType::customer_follow);

            //delete follow acceptance notification (9)
            (new functionController)->deleteNotification(Session::get('customer_id'), $follow_id->id, notificationType::follow_accept);

            DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            DB::rollback(); //rollback all successfully executed queries
            exit();
        }

        return Response::json($id);
    }

    //function to get branches list from partner id
    public function BranchesFromPartnerId(Request $request)
    {
        $partner_id = $request->input('partner_id');
        $branch_list = PartnerBranch::where('partner_account_id', $partner_id)->get();

        return Response::json($branch_list);
    }

    //function to make discount notification seen
    public function discountNotification($noti_id, $cust_id)
    {
        DB::table('customer_notification')
            ->where('id', $noti_id)
            ->update([
                'seen' => 1,
            ]);

        //get all unseen notification of this customer
        $allNotifications = (new functionController)->allNotifications(Session::get('customer_id'));
        session(['customerAllNotifications' => $allNotifications]);

        $username = CustomerAccount::where('customer_id', $cust_id)->select('customer_username')->first();

        return redirect('users/'.$username->customer_username.'/offers');
        //        return redirect('partner-profile/'.$part_name.'/'.$branch_id)->with('make_review', 'show make review modal');
    }

    //function to make like notification seen
    public function likedNotification($ids)
    {
        $ids = explode('_', $ids);
        DB::table('customer_notification')
            ->where('id', $ids[1])
            ->update([
                'seen' => 1,
            ]);
        $partner_name_from_review_id = DB::table('partner_info as pi')
            ->join('review as rev', 'rev.partner_account_id', '=', 'pi.partner_account_id')
            ->select('pi.partner_name')
            ->where('rev.id', $ids[0])
            ->get();
        $partner_name_from_review_id = json_decode(json_encode($partner_name_from_review_id), true);
        $partner_name_from_review_id = $partner_name_from_review_id[0]['partner_name'];
        //get all unseen notification of this customer
        $allNotifications = (new functionController)->allNotifications(Session::get('customer_id'));
        session(['customerAllNotifications' => $allNotifications]);
        $encrypted_id = (new functionController)->socialShareEncryption('encrypt', $ids[0]);

        return redirect('review/'.$encrypted_id);
    }

    //function to make reply notification seen
    public function replyNotification($ids)
    {
        $ids = explode('_', $ids);
        DB::table('customer_notification')
            ->where('id', $ids[1])
            ->update([
                'seen' => 1,
            ]);

        $partner_name_from_review_id = DB::table('partner_info as pi')
            ->join('review as rev', 'rev.partner_account_id', '=', 'pi.partner_account_id')
            ->select('pi.partner_name')
            ->where('rev.id', $ids[0])
            ->get();
        $partner_name_from_review_id = json_decode(json_encode($partner_name_from_review_id), true);
        $partner_name_from_review_id = $partner_name_from_review_id[0]['partner_name'];
        //get all unseen notification of this customer
        $allNotifications = (new functionController)->allNotifications(Session::get('customer_id'));
        session(['customerAllNotifications' => $allNotifications]);
        $encrypted_id = (new functionController)->socialShareEncryption('encrypt', $ids[0]);

        return redirect('review/'.$encrypted_id);
    }

    //function to make reply notification seen
    public function followNotification($ids)
    {
        $ids = explode('_', $ids);
        DB::table('customer_notification')
            ->where('id', $ids[1])
            ->update([
                'seen' => 1,
            ]);
        //get all unseen notification of this customer
        $allNotifications = (new functionController)->allNotifications(Session::get('customer_id'));
        session(['customerAllNotifications' => $allNotifications]);

        return redirect('user/all-notifications');
    }

    //function to make birthday notification seen
    public function birthdayNotification($id)
    {
        $id = explode('_', $id);
        $today = date('Y-m-d');
        //make notification seen
        DB::table('customer_notification')
            ->where('id', $id[0])
            ->update([
                'seen' => 1,
            ]);

        return redirect('users/'.Session::get('customer_username'));
        //check if the birthday gift is invalid or not
        $invalid = DB::table('birthday_wish')
            ->where('customer_id', $id[1])
            ->where('used', 0)
            ->where('expiry_date', '>=', $today)
            ->count();
        if ($invalid == 1) { //gift is not expired or used
            return redirect('all_coupons/birthday');
        } else { //gift is expired or used
            return redirect()->back()->with([
                'birthdayGiftExpired' => 'Sorry, you have already availed this gift or it\'s expired!',
            ]);
        }
    }

    //function to make acceptFollowRequestNotification seen & other works
    public function acceptFollowRequestNotification($id)
    {
        $id = explode('_', $id);
        //make notification seen
        DB::table('customer_notification')
            ->where('id', $id[1])
            ->update([
                'seen' => 1,
            ]);
        //get username from source id
        $username = DB::table('customer_account as ca')
            ->join('follow_customer as fc', 'fc.following', 'ca.customer_id')
            ->select('ca.customer_username')
            ->where('fc.id', $id[0])
            ->first();
        //get all unseen notification of this customer
        $allNotifications = (new functionController)->allNotifications(Session::get('customer_id'));
        session(['customerAllNotifications' => $allNotifications]);

        return redirect('user-profile/'.$username->customer_username);
    }

    //function to make referNotification seen & other works
    public function referNotification($username, $id)
    {
        //make notification seen
        DB::table('customer_notification')
            ->where('id', $id)
            ->update([
                'seen' => 1,
            ]);
        //get all unseen notification of this customer
        $allNotifications = (new functionController)->allNotifications(Session::get('customer_id'));
        session(['customerAllNotifications' => $allNotifications]);

        return redirect('users/'.$username.'/info');
        //redirect to reward page when reward will be opened
//        return redirect('users/' . $username . '/rewards');
    }

    //function to make rewardNotification seen & other works
    public function rewardNotification($username, $id)
    {
        //make notification seen
        DB::table('customer_notification')
            ->where('id', $id)
            ->update([
                'seen' => 1,
            ]);
        //get all unseen notification of this customer
        $allNotifications = (new functionController)->allNotifications(Session::get('customer_id'));
        session(['customerAllNotifications' => $allNotifications]);

        return redirect('users/'.$username.'/credit_history');
        //redirect to reward page when reward will be opened
//        return redirect('users/' . $username . '/rewards');
    }

    //function to make dealRedeemNotification seen & other works
    public function dealRedeemNotification($username, $id)
    {
        //make notification seen
        DB::table('customer_notification')
            ->where('id', $id)
            ->update([
                'seen' => 1,
            ]);

        return redirect('users/'.$username.'/deals');
    }

    //function to make dealRejectNotification seen & other works
    public function dealRejectNotification($username, $id)
    {
        //make notification seen
        DB::table('customer_notification')
            ->where('id', $id)
            ->update([
                'seen' => 1,
            ]);
        //get all unseen notification of this customer
        $allNotifications = (new functionController)->allNotifications(Session::get('customer_id'));
        session(['customerAllNotifications' => $allNotifications]);

        return redirect()->back();
    }

    //function to show all notification of a user
    public function allNotifications()
    {
        // make all notification seen of this user
        DB::table('customer_notification')
            ->where('user_id', Session::get('customer_id'))
            ->update([
                'seen' => 1,
            ]);
        $allNotifications = (new functionController)->allNotifications(Session::get('customer_id'));
        session(['customerAllNotifications' => $allNotifications]);
        //fetch only follow notifications of this user
        $follow_requests = (new functionController)->allFollowRequests(Session::get('customer_id'));

        $partnerInfo = DB::table('partner_info as pi')
            ->join('partner_account as pa', 'pa.partner_account_id', '=', 'pi.partner_account_id')
            ->join('partner_profile_images as ppi', 'ppi.partner_account_id', '=', 'pi.partner_account_id')
            ->select('pi.partner_name', 'pi.partner_category', 'pi.partner_account_id', 'ppi.partner_profile_image')
            ->inRandomOrder()
            ->where('pa.active', 1)
            ->limit(4)
            ->get();

        $i = 0;
        foreach ($partnerInfo as $partner) {
            $main_branch = (new functionController)->mainBranchOfPartner($partner->partner_account_id);
            if (count($main_branch) > 0) {
                $partnerInfo[$i]->main_branch_id = $main_branch[0]->id;
            } else {
                unset($partnerInfo[$i]);
            }
            $i++;
        }
        //get info of top brands partners
        $topBrands = (new functionController)->topBrands();
        //get categories list
        $categories = Categories::all();

        return view('customer_all_notifications', compact('follow_requests', 'partnerInfo', 'topBrands', 'categories'));
    }

    //function to like post in user account
    public function likePost(Request $request)
    {
        $post_id = $request->input('post_id');
        //check if this customer already liked this post or not
        $previousLike = DB::table('likes_post')
            ->where('post_id', $post_id)
            ->where('liker_id', Session::get('customer_id'))
            ->count();
        if ($previousLike == 0) {
            //get poster type
            $post = Post::where('id', $post_id)->first();
            if ($post->poster_type == PostType::partner) {
                try {
                    DB::beginTransaction(); //to do query rollback
                    //insert into like post table
                    $like = new LikePost([
                        'post_id' => $post_id,
                        'liker_id' => session('customer_id'),
                        'liker_type' => LikerType::customer,
                    ]);
                    $like->save();

                    //get profile image from customer id
                    $customer = CustomerInfo::where('customer_id', Session::get('customer_id'))->first();
                    (new \App\Http\Controllers\Newsfeed\functionController())->setPostLikeNotification($post, $customer, $like->id);
                    DB::commit(); //to do query rollback
                } catch (\Exception $e) {
                    DB::rollback(); //rollback all successfully executed queries
                    return Response::json('Something went wrong');
                }
                $data['status'] = 'succeed';
                $data['total_likes_of_post'] = (new functionController)->total_likes_of_a_post($post_id);
                $data['post_id'] = $post_id;
                $data['like_id'] = $like->id;

                return Response::json($data);
            } else {
                try {
                    DB::beginTransaction(); //to do query rollback
                    //insert into like post table
                    $last_inserted_id = DB::table('likes_post')->insertGetId([
                        'post_id' => $post_id,
                        'liker_id' => Session::get('customer_id'),
                        'liker_type' => LikerType::customer,
                    ]);
                    DB::commit(); //to do query rollback
                } catch (\Exception $e) {
                    DB::rollback(); //rollback all successfully executed queries
                    return Response::json('Something went wrong');
                }
                $data['status'] = 'succeed';
                $data['total_likes_of_post'] = (new functionController)->total_likes_of_a_post($post_id);
                $data['post_id'] = $post_id;
                $data['like_id'] = $last_inserted_id;

                return Response::json($data);
            }
        } else {
            return Response::json('already liked');
        }
    }

    public function unLikePost(Request $request)
    {
        $post_id = $request->input('id');
        $source_id = $request->input('source_id');

        try {
            DB::beginTransaction(); //to do query rollback
            $post = Post::where('id', $post_id)->first();
            //delete post like
            $liked_post = LikePost::find($source_id);
            $liked_post->delete();

            DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            DB::rollBack();

            return Response::json($e);
        }

        //total likes of this post
        $likes_of_a_post = DB::table('likes_post')->where('post_id', $post_id)->count();

        $data['post_id'] = $post_id;
        $data['partner_id'] = $post->poster_id;
        $data['total_likes_of_post'] = $likes_of_a_post;

        event(new like_post($post->poster_id));

        return Response::json($data);
        //        //message to send as parameter
        //        $message = Session::get('customer_first_name'). ' '.Session::get('customer_last_name').' liked your review';
        //        $firebase_token = DB::table('customer_info')->select('firebase_token')->where('customer_id', $customer_id->customer_id)->first();
        //send notification to app
        //        (new jsonController)->functionSendGlobalPushNotification($message, $firebase_token->firebase_token);
    }

    //function to do backend of review like
    public function likeReview(Request $request)
    {
        $review_id = $request->input('id');

        if (Session::has('customer_id')) {
            $already_liked = DB::table('likes_review')
                ->where('review_id', $review_id)
                ->where('liker_id', Session::get('customer_id'))
                ->where('liker_type', LikerType::customer)
                ->count();
        } elseif (Session::has('partner_id')) {
            $already_liked = DB::table('likes_review')
                ->where('review_id', $review_id)
                ->where('liker_id', Session::get('partner_id'))
                ->where('liker_type', LikerType::partner)
                ->count();
        }

        if ($already_liked == 0) {
            try {
                DB::beginTransaction(); //to do query rollback
                if (Session::has('customer_id')) {
                    $liker_id = Session::get('customer_id');
                    $liker_type = LikerType::customer;
                    $img_link = Session::get('customer_profile_image');
                } elseif (Session::has('partner_id')) {
                    $liker_id = Session::get('partner_id');
                    $liker_type = LikerType::partner;
                    $img_link = Session::get('partner_profile_image');
                }
                $last_like_id = DB::table('likes_review')->insertGetId(
                    [
                        'review_id' => $review_id,
                        'liker_id' => $liker_id,
                        'liker_type' => $liker_type,
                    ]
                );

                //customer id of that review
                $customer_id = DB::table('review')->select('customer_id')->where('id', $review_id)->first();
                //insert info into customer notification table
                DB::table('customer_notification')->insert(
                    [
                        'user_id' => $customer_id->customer_id,
                        'image_link' => $img_link,
                        'notification_text' => 'liked your review.',
                        'notification_type' => 1,
                        'source_id' => $last_like_id,
                        'seen' => 0,
                    ]
                );
                DB::commit(); //to do query rollback
            } catch (\Exception $e) {
                DB::rollBack();
                exit();
            }
        } else {
            return Response::json('double click');
        }

        //total likes of this review
        $likes_of_a_review = DB::table('likes_review')->where('review_id', $review_id)->count();
        //total likes of the customer of this review
        $likes_of_a_user = (new functionController)->likeNumber($customer_id->customer_id);
        $data['review_id'] = $review_id;
        $data['customer_id'] = $customer_id->customer_id;
        if (Session::has('customer_id')) {
            $data['liker_id'] = Session::get('customer_id');
        } elseif (Session::has('partner_id')) {
            $data['liker_id'] = Session::get('partner_id');
        }
        $data['source_id'] = $last_like_id;
        $data['total_likes_of_a_review'] = $likes_of_a_review;
        $data['total_likes_of_a_user'] = $likes_of_a_user;
        //trigger 'livePushNotification' function to do live push notification
        event(new like_review($data));

//        (new pusherController)->liveLikeNotification($data);
        //message to send as parameter
        if (Session::has('customer_id')) {
            $message = Session::get('customer_full_name').' liked your review';
        } elseif (Session::has('partner_id')) {
            $message = Session::get('partner_name').' liked your review';
        }
        $customer = DB::table('customer_info')->where('customer_id', $customer_id->customer_id)->first();
        //send notification to app
        (new jsonController)->functionSendGlobalPushNotification($message, $customer, notificationType::like_review);
    }

    //function to do backend of review unlike
    public function unlikeReview(Request $request)
    {
        $review_id = $request->input('id');
        $source_id = $request->input('source_id');

        try {
            DB::beginTransaction(); //to do query rollback
            $customer_id = DB::table('review')->select('customer_id')->where('id', $review_id)->first();
            //delete customer notification
            DB::table('customer_notification')
                ->where('notification_type', 1)
                ->where('source_id', $source_id)
                ->delete();
            //delete review like
            DB::table('likes_review')
                ->where('id', $source_id)
                ->delete();

            DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            DB::rollBack();
            exit();
        }

        //total likes of this review
        $likes_of_a_review = DB::table('likes_review')->where('review_id', $review_id)->count();
        //total likes of customer of this review
        $likes_of_a_user = (new functionController)->likeNumber($customer_id->customer_id);
        $data['review_id'] = $review_id;
        $data['customer_id'] = $customer_id->customer_id;
        if (Session::has('customer_id')) {
            $data['liker_id'] = Session::get('customer_id');
        } elseif (Session::has('partner_id')) {
            $data['liker_id'] = Session::get('partner_id');
        }
        $data['total_likes_of_a_review'] = $likes_of_a_review;
        $data['total_likes_of_a_user'] = $likes_of_a_user;

        //trigger 'livePushNotification' function to do live push notification
        event(new append_notification($data));
        //        //message to send as parameter
        //        $message = Session::get('customer_first_name'). ' '.Session::get('customer_last_name').' liked your review';
        //        $firebase_token = DB::table('customer_info')->select('firebase_token')->where('customer_id', $customer_id->customer_id)->first();
        //send notification to app
        //        (new jsonController)->functionSendGlobalPushNotification($message, $firebase_token->firebase_token);
    }

    //Delete Review By Customer Himself
    public function reviewDelete($id)
    {
        $reviewer_id = DB::table('review')->where('id', $id)->first();
        if (Session::get('customer_id') != $reviewer_id->customer_id) {
            return redirect()->back()->with('try_again', 'Please try again!');
        }
        try {
            DB::beginTransaction(); //to do query rollback

            //delete review
            (new functionController)->deleteReview($id);

            DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            DB::rollBack(); //rollback all successfully executed queries
            return redirect()->back()->with('try_again', 'Please try again!');
        }
        $uri_segments = explode('/', url()->previous());
        if ($uri_segments[count($uri_segments) - 2] == 'review' || $uri_segments[count($uri_segments) - 2] == 'review-share') {
            return redirect('users/'.Session::get('customer_username'))->with('review_deleted', 'Your review has been deleted successfully.');
        } else {
            return redirect()->back()->with('review_deleted', 'Review deleted!');
        }
    }

    //function to accept follow request
    public function acceptFollowRequest(Request $request)
    {
        //get customer id from ajax function
        $id = $request->input('id');
        //update follow_request in follow_customer table
        try {
            DB::beginTransaction(); //to do query rollback

            DB::table('follow_customer')
                ->where('follower', $id)
                ->where('following', Session::get('customer_id'))
                ->update([
                    'follow_request' => 1,
                ]);
            //get profile image from customer id
            $profile_image = DB::table('customer_info')
                ->select('customer_profile_image')
                ->where('customer_id', Session::get('customer_id'))
                ->get();
            $profile_image = json_decode(json_encode($profile_image), true);
            $profile_image = $profile_image[0];
            //get id to use as source_id
            $follow_id = DB::table('follow_customer')
                ->select('id')
                ->where('follower', $id)
                ->where('following', Session::get('customer_id'))
                ->get();
            $follow_id = json_decode(json_encode($follow_id), true);
            $follow_id = $follow_id[0];
            //check if this notification has already been inserted or not
            $already_inserted = DB::table('customer_notification')
                ->where('user_id', $id)
                ->where('notification_type', 9)
                ->where('source_id', $follow_id['id'])
                ->count();
            if ($already_inserted == 1) {
                //do nothing
            } else {
                //insert into partner notification table
                DB::table('customer_notification')->insert([
                    'user_id' => $id,
                    'image_link' => $profile_image['customer_profile_image'],
                    'notification_text' => 'accepted your follow request.',
                    'notification_type' => 9,
                    'source_id' => $follow_id['id'],
                    'seen' => 0,
                ]);
            }

            DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            DB::rollback(); //rollback all successfully executed queries
            exit();
        }

        //trigger 'livePushNotification' function to do live push notification
        (new pusherController)->liveFollowRequestAcceptNotification($id);
        //message to send as parameter
        $message = Session::get('customer_first_name').' '.Session::get('customer_last_name').' accepted your follow request';
        $customer = DB::table('customer_info')->where('customer_id', $id)->first();
        //send notification to app
        (new jsonController)->functionSendGlobalPushNotification($message, $customer);

        return Response::json($id);
    }

    //function to ignore follow request
    public function ignoreFollowRequest(Request $request)
    {
        //get customer id from ajax function
        $id = $request->input('id');

        try {
            DB::beginTransaction(); //to do query rollback

            //get source id to use in customer_notification table
            $source_id = DB::table('follow_customer')
                ->where('follower', $id)
                ->where('following', Session::get('customer_id'))
                ->where('follow_request', 0)
                ->pluck('id');
            //delete follow request from follow_customer table
            DB::table('follow_customer')
                ->where('follower', $id)
                ->where('following', Session::get('customer_id'))
                ->where('follow_request', 0)
                ->delete();
            //delete follow notification from customer-notification table
            DB::table('customer_notification')
                ->where('user_id', Session::get('customer_id'))
                ->where('notification_type', 8)
                ->where('source_id', $source_id)
                ->delete();
            //count follow request
            $follow_request = DB::table('follow_customer')
                ->where('following', Session::get('customer_id'))
                ->where('follow_request', 0)
                ->count();

            DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            DB::rollback(); //rollback all successfully executed queries
            exit();
        }

        $data['request_number'] = $follow_request == 0 ? 0 : null;
        $data['follower'] = $id;

        return Response::json($data);
    }

    //function to send activation code through sms
    public function activeSuccessSMS($mobile_number, $name)
    {
        //send password via SMS
        $text_message = 'Dear '.$name.','."\r\n";
        $text_message .= 'Your Royalty Premium Membership is now Active!'."\r\n";
        $text_message .= 'Visit our exclusive partners & start your journey of boundless possibilities.'."\r\n";
        $text_message .= 'For more info, visit www.royaltybd.com'."\r\n";
        $text_message .= 'Regards,'."\r\n";
        $text_message .= 'Royalty Team';
        $user = 'Royaltybd';
        $pass = '66A6Q13d';
        $sid = 'RoyaltybdMasking';
        $url = 'http://sms.sslwireless.com/pushapi/dynamic/server.php';
        $param = "user=$user&pass=$pass&sms[0][0]= $mobile_number &sms[0][1]=".urlencode($text_message)."&sms[0][2]=123456789&sid=$sid";
        $crl = curl_init();
        curl_setopt($crl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($crl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($crl, CURLOPT_URL, $url);
        curl_setopt($crl, CURLOPT_HEADER, 0);
        curl_setopt($crl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($crl, CURLOPT_POST, 1);
        curl_setopt($crl, CURLOPT_POSTFIELDS, $param);
        $response = curl_exec($crl);
        curl_close($crl);
        //code successfully sent
        $message = 1;

        return Response::json($message);
    }

    //function for Reset user
    public function resetUserView($token)
    {
        $token_status = ResetUser::where('token', $token)->first();

        if (!$token_status) {
            return redirect('page-not-found');
        } else {
            if ($token_status->used == 1) {
                return redirect('page-not-found');
            } else {
                return view('resetUser', compact('token'));
            }
        }
    }

    //function for Reset user
    public function resetUserDone(Request $request, $token)
    {
        $token_info = ResetUser::where('token', $token)->first();
        $customer_id = $token_info->customer_id;

        $this->validate($request, [
            'password' => 'required|min:4',
        ]);
        $request->flashOnly(['password']);

        //get data from edit form
        $pin = $request->get('password');
        $encrypted_pin = (new functionController)->encrypt_decrypt('encrypt', $pin);

        if ((new loginFunctionController())->storeNewPin($customer_id, $encrypted_pin, $token)) {
            session(['pin_updated_from_phone' => 'pin_updated_successfully']);
            return redirect('login')->with([
                'user_reset' => 'Your user credentials have been updated!',
            ]);
        } else {
            return redirect()->back()->with('try_again', 'Please try again!');
        }
    }

    //function to send verification email to verify customer email from account
    public function sendMailVerification(Request $request)
    {
        $email = $request->input('verifying_mail');
        //check if user with this email exists or not
        $user = DB::table('customer_info')->where('customer_id', Session::get('customer_id'))->first();

        $send_email = (new otpFunctionController())->sendMailVerification($user->customer_email, $email, session('customer_id'));

        if ($send_email->status() == 201) {
            return \redirect()->back()->with('email_verify_fail', $send_email->getData()->result);
        } else {
            return \redirect()->back()->with('email verification sent', $send_email->getData()->result);
        }
    }

    //verify email from account
    public function verifyEmail(Request $request)
    {
        $code = $request->get('verifying_code');
        $verify = (new otpFunctionController())->verifyEmailOTP(session('reg_change_email'), $code);
        if ($verify->status() == 201) {
            return \redirect()->back()->with('email_verify_fail', $verify->getData()->result);
        } else {
            if (session('reg_change_email')) {
                Session::forget('reg_change_email');
            }

            return \redirect()->back()->with('email_verify_success', $verify->getData()->result);
        }
    }

    //function to send verification email to verify customer email at edit
    public function sendEditMailVerification(Request $request)
    {
        $email = $request->input('verifying_mail');
        if ((new JsonControllerV2())->mailExist($email, $email)) {
            return \redirect()->back()->with('edit_email_exist', 'Email already exists.');
        }
        if ($x = (new functionController2())->isVerificationMailSent($email)) {
            $current = Carbon::now();
            $dt = $x->created_at;
            $diff = $dt->diffInMinutes($current);

            return \redirect()->back()->with('code_already_sent', 'We have already sent your verification e-mail. Please check your inbox or other email folders. You will be able to re-send another verification email after '.(Constants::resend_time - $diff).' minutes.');
        }
        $send_email = (new otpFunctionController())->sendMailVerification($email, $email, session('customer_id'));

        if ($send_email->status() == 201) {
            return \redirect()->back()->with('edit_email_verify_fail', $send_email->getData()->result);
        } else {
            session(['reg_change_email' => $email]);
            session(['customer_email_verified' => 0]);
            session(['customer_email' => $email]);

            return \redirect()->back()->with('email verification sent', $send_email->getData()->result);
        }
    }

    //function for Reset user
    public function emailVerificationDone(Request $request, $token)
    {
        $verification_type = VerificationType::email_verification;

        $token_info = DB::table('reset_user')
            ->where('token', $token)
            ->where('verification_type', $verification_type)
            ->first();
        $customer_id = $token_info->customer_id;
        $is_used = $token_info->used;
        $email = $token_info->sent_value;

        if ($is_used == 1) {
            return redirect('page-not-found');
        } else {
            try {
                DB::beginTransaction(); //to do query rollback
                //check if user verifying current or new email
                $current_email = CustomerInfo::where('customer_id', $customer_id)->select('customer_email')->first();
                if ($email != $current_email->customer_email) {
                    SocialId::where('customer_id', $customer_id)
                        ->where('customer_social_type', 'google')
                        ->delete();
                }
                //update image path in database
                DB::table('reset_user')
                    ->where('token', $token)
                    ->update([
                        'used' => 1,
                    ]);
                //update customer info table
                DB::table('customer_info')
                    ->where('customer_id', $customer_id)
                    ->update([
                        'email_verified' => 1,
                        'customer_email' => $email,
                    ]);
                session(['customer_email' => $email]);
                DB::commit(); //to do query rollback
            } catch (\Exception $e) {
                DB::rollBack();

                return redirect()->back()->with('try_again', 'Please try again!');
            }
            //reload verify page if already verified on other device or window
            $data['customer_id'] = $customer_id;
            $data['email_verified'] = 1;
            event(new check_email_verify_status($data));
            session(['customer_email_verified' => 1]);

            return redirect('email-verify-success');
        }
    }

    //function for user email verification
    public function checkDuplicateEmail(Request $request)
    {
        $email = $request->input('email');
        $previous_email1 = DB::table('customer_info')
            ->where('customer_email', '!=', Session::get('customer_email'))
            ->where('customer_email', $email)
            ->count();
        $delivery_types = [3, 4, 6, 7];
        $previous_email2 = DB::table('info_at_buy_card')
            ->where('customer_email', $email)
            ->where('customer_email', '!=', Session::get('customer_email'))
            ->whereIn('delivery_type', $delivery_types)
            ->count();
        if ($previous_email1 > 0 || $previous_email2 > 0) {
            $email_exists = 1;
        } else {
            $email_exists = 0;
        }

        return Response::json($email_exists);
    }

    //function to update gender
    public function updateGender(Request $request)
    {
        $gender = $request->input('gender');

        $result = CustomerInfo::where('customer_id', Session::get('customer_id'))->update(['customer_gender' => $gender]);
        // $profile_completed = (new rewardFunctionController())->addProfileCompletionPoint(Session::get('customer_id'));
        //$profile_completed = (new rewardFunctionController())->profileCompletionPercentage(Session::get('customer_id'));

        $data['result'] = $result;
        $data['percent'] = 80;

        return Response::json($data);
    }

    //function to set user pin
    public function setPin(Request $request)
    {
        $pin = $request->input('pin');
        $result['status'] = 0;
        $result['text'] = '';
        if (! is_numeric($pin)) {
            $result['text'] = 'Only number is allowed';

            return Response::json($result);
        } elseif (strlen($pin) > 4) {
            $result['text'] = 'Please insert a 4 DIGIT PIN';

            return Response::json($result);
        }
        $encrypted_pin = (new functionController)->encrypt_decrypt('encrypt', $pin);
        $user = CustomerAccount::where('customer_id', Session::get('customer_id'))
            ->update(['pin' => $encrypted_pin]);
        $result['status'] = $user;

        return Response::json($result);
    }

    //function to update gender
    public function updateDOB(Request $request)
    {
        $year = $request->input('year');
        $month = $request->input('month');
        $day = $request->input('day');
        $date_of_birth = $year.'-'.$month.'-'.$day;
        $result = CustomerInfo::where('customer_id', Session::get('customer_id'))->update(['customer_dob' => $date_of_birth]);
        $profile_completed = (new rewardFunctionController())->addProfileCompletionPoint(Session::get('customer_id'));
//        $profile_completed = (new rewardFunctionController())->profileCompletionPercentage(Session::get('customer_id'));

        $data['result'] = $result;
        $data['percent'] = $profile_completed;

        return Response::json($data);
    }

    public function connectSocialAccount(Request $request)
    {
        $social_id = $request->input('requested_id');
        $social_type = $request->input('type');
        //check already exists or not
        $exists = SocialId::where('customer_social_id', $social_id)
            ->where('customer_social_type', $social_type)
            ->count();

        if ($exists == 0) {
            DB::table('social_id')->insert([
                'customer_id' => Session::get('customer_id'),
                'customer_social_id' => $social_id,
                'customer_social_type' => $social_type,
            ]);
            $google_connected = SocialId::where('customer_id', Session::get('customer_id'))
                ->where('customer_social_type', 'google')
                ->count();
            $facebook_connected = SocialId::where('customer_id', Session::get('customer_id'))
                ->where('customer_social_type', 'facebook')
                ->count();
            if ($google_connected == 1 && $facebook_connected == 1) {
                $hide = 1;
            } else {
                $hide = 0;
            }
            $result = 1;
        } else {
            $hide = 0;
            $result = 0;
        }

        return Response::json(['hide' => $hide, 'result' => $result], 200);
    }

    //liker list of reviews
    public function getReviewLikerList(Request $request)
    {
        $review_id = $request->input('review_id');
        $review_likes = LikesReview::where('review_id', $review_id)->get();

        $likerInfo = [];
        $i = 0;
        foreach ($review_likes as $like) {
            if ($like['liker_type'] == 1) {
                $info = DB::table('customer_info')
                    ->where('customer_id', $like['liker_id'])
                    ->select('customer_full_name as liker_name', 'customer_profile_image as profile_image')
                    ->first();
                $info = json_decode(json_encode($info), true);
            } else {
                $info = DB::table('partner_info as pi')
                    ->join('partner_profile_images as ppi', 'ppi.partner_account_id', '=', 'pi.partner_account_id')
                    ->where('pi.partner_account_id', $like['liker_id'])
                    ->select('pi.partner_name as liker_name', 'ppi.partner_profile_image as profile_image')
                    ->first();
                $info = json_decode(json_encode($info), true);
            }
            if ($info) {
                $likerInfo[$i]['liker_name'] = $info['liker_name'];
                $likerInfo[$i]['liker_image'] = $info['profile_image'];
                $i++;
            }
        }

        return Response::json($likerInfo);
    }

    //liker list of reviews
    public function getNewsFeedLikerList(Request $request)
    {
        $post_id = $request->input('post_id');
        $likes = LikePost::where('post_id', $post_id)->with('customer', 'partner.ProfileImage')->orderBy('id', 'DESC')->get();

        $likerInfo = [];
        $i = 0;
        foreach ($likes as $like) {
            $likerInfo[$i]['liker_name'] = $like->customer->customer_full_name;
            $likerInfo[$i]['liker_image'] = $like->customer->customer_profile_image;
            $i++;
        }

        return Response::json($likerInfo);
    }

    //function to mark all notification as read
    public function markAllNotificationsAsRead()
    {
        $customer_id = session('customer_id');
        (new functionController2())->markUserAllNotificationsAsRead($customer_id);

        return \redirect()->back();
    }
}//controller ends
