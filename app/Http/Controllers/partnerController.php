<?php

namespace App\Http\Controllers;

use App\Area;
use App\Categories;
use App\CustomerInfo;
use App\CustomerNotification;
use App\Division;
use App\Events\offer_availed;
use App\Http\Controllers\Enum\Constants;
use App\Http\Controllers\Enum\LikerType;
use App\Http\Controllers\Enum\notificationType;
use App\LikePost;
use App\OpeningHours;
use App\PartnerAccount;
use App\PartnerBranch;
use App\PartnerCategoryRelation;
use App\PartnerFacilities;
use App\PartnerGalleryImages;
use App\PartnerInfo;
use App\PartnerJoinForm;
use App\PartnerMenuImages;
use App\PartnerNotification;
use App\PartnerPostHeader;
use App\PartnerProfileImage;
use App\Post;
use App\Review;
use App\transaction_table;
use App\TransactionTable;
use Auth;
use Datetime;
use DB;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use Khill\Lavacharts\Lavacharts;
use Response;
use Session;
use View;

class partnerController extends Controller
{
    //function for saving registration info of partner from registration form
    public function savePartnerData(Request $request)
    {
        $this->validate($request, [
            'businessName' => 'unique:partner_join_form,business_name',
        ]);
        $request->flashOnly(['businessName']);

        //get all data from form
        $partnerInfo['name'] = $request->get('businessName');
        $partnerInfo['number'] = $request->get('businessNumber');
        $partnerInfo['email'] = $request->get('businessEmail');
        $partnerInfo['owner'] = $request->get('ownerName');
        $partnerInfo['address'] = $request->get('businessAddress');
        $partnerInfo['fb_link'] = $request->get('fb_link') != null ? $request->get('fb_link') : '#';
        $partnerInfo['web_link'] = $request->get('web_link') != null ? $request->get('web_link') : '#';
        $partnerInfo['partnerDiv'] = $request->get('partnerDiv');
        $partnerInfo['area'] = $request->get('partnerArea');
        $partnerInfo['category'] = $request->get('businessCategory');

        $partner_join = new PartnerJoinForm([
                'business_name' => $partnerInfo['name'],
                'business_number' => $partnerInfo['number'],
                'business_email' => $partnerInfo['email'],
                'business_address' => $partnerInfo['address'],
                'full_name' => $partnerInfo['owner'],
                'partner_division' => $partnerInfo['partnerDiv'],
                'business_area' => $partnerInfo['area'],
                'business_category' => $partnerInfo['category'],
                'fb_link' => $partnerInfo['fb_link'],
                'website' => $partnerInfo['web_link'],
            ]);
        $partner_join->save();
        (new \App\Http\Controllers\AdminNotification\functionController())->newPartnerRequestNotification($partner_join);

        Session::flash('join_status', 'Thank you for reaching to us. We will get back to you soon.');

        return redirect()->back();
    }

    // Function for transaction history
    public function partnerTransaction(Request $request)
    {
        $partnerID = $request->session()->get('partner_id');
        //get partner info from id
        $partner_data = DB::table('partner_info')
            ->select('partner_name', 'partner_email', 'expiry_date')
            ->where('partner_account_id', $partnerID)
            ->get();
        $array = get_object_vars($partner_data[0]);

        $transactions = DB::table('transaction_table as tt')
            ->join('partner_branch as pb', 'pb.id', '=', 'tt.branch_id')
            ->select('tt.customer_id', 'tt.amount_spent', 'tt.discount_amount')
            ->where('pb.partner_account_id', $partnerID)
            ->get();
        $amount_sum = DB::table('transaction_table as tt')
            ->join('partner_branch as pb', 'pb.id', '=', 'tt.branch_id')
            ->where('pb.partner_account_id', $partnerID)
            ->sum('tt.amount_spent');
        $discount_sum = DB::table('transaction_table as tt')
            ->join('partner_branch as pb', 'pb.id', '=', 'tt.branch_id')
            ->where('pb.partner_account_id', $partnerID)
            ->sum('tt.discount_amount');
        //get partner profile image
        $partner_image = DB::table('partner_profile_images')
            ->select('partner_profile_image')
            ->where('partner_account_id', $partnerID)
            ->get();
        $image = get_object_vars($partner_image[0]);
        //total customer number partner has been visited
        $customer_number = DB::table('transaction_table as tt')
            ->join('partner_branch as pb', 'pb.id', '=', 'tt.branch_id')
            ->select('tt.customer_id')
            ->groupBy('tt.customer_id')
            ->where('pb.partner_account_id', $partnerID)
            ->get();
        $customer_number = count($customer_number);
        //total number of card used
        $card_used = DB::table('transaction_table as tt')
            ->join('partner_branch as pb', 'pb.id', '=', 'tt.branch_id')
            ->where('pb.partner_account_id', $partnerID)
            ->count();
        //check partner validity
        $curDate = date('Y-m-d');
        $exp_date = Session::get('partner_expiry_date');
        $cur_date = new DateTime($curDate);
        $exp_date = new DateTime($exp_date);
        $interval = $cur_date->diff($exp_date);
        $monthRemaining = $interval->format('%R%m');
        $daysRemaining = $interval->format('%R%d');

        //all followers info of the partner
        $following = DB::table('follow')
            ->where('partner_id', $partnerID)
            ->get();
        $following = json_decode(json_encode($following), true);
        $followers_info = [];
        foreach ($following as $value) {
            $follower_info = DB::table('customer_account')
                ->join('customer_info', 'customer_account.customer_id', '=', 'customer_info.customer_id')
                ->select('customer_account.customer_id', 'customer_account.customer_username', 'customer_info.customer_profile_image')
                ->where('customer_account.customer_id', $value['customer_id'])
                ->first();
            $follower_info = json_decode(json_encode($follower_info), true);
            array_push($followers_info, $follower_info);
        }

        return view('partnerAccount', compact('array', 'transactions', 'image', 'amount_sum', 'discount_sum', 'customer_number', 'card_used', 'monthRemaining', 'daysRemaining', 'followers_info'));
    }

    //function to check user validity && user requests
    public function checkUser()
    {
        $customer_id = $_POST['id'];
        $today = date('Y-m-d');
        //check if customer id exists or not
        $info = DB::table('customer_info')
            ->where('customer_id', $customer_id)
            ->get();
        $info = json_decode(json_encode($info), true);

        if (count($info) == 0) {
            $data['invalid_user'] = 'Invalid User';

            return Response::json($data);
        } elseif (count($info) == 1 && $info[0]['expiry_date'] < $today) {
            $data['invalid_user'] = 'User Expired';

            return Response::json($data);
        } elseif (count($info) == 1 && $info[0]['card_active'] == 1) {
            $data['invalid_user'] = 'Card Inactive';

            return Response::json($data);
        } else {
            $requests = DB::table('bonus_request as brq')
                ->join('all_coupons as acp', 'acp.id', '=', 'brq.coupon_id')
                ->join('partner_branch as pb', 'pb.id', '=', 'acp.branch_id')
                ->select('acp.reward_text', 'acp.coupon_type', 'brq.*')
                ->where('brq.customer_id', $customer_id)
                ->where('pb.partner_account_id', Session::get('partner_id'))
                ->where('brq.used', 0)
                ->where('brq.expiry_date', '>=', $today)
                ->get();
            $requests = json_decode(json_encode($requests), true);
            if (count($requests) > 0) {
                $data['requests'] = $requests;
            }
            $customerInfo = DB::table('customer_info as ci')
                ->join('user_type as ut', 'ut.id', '=', 'ci.customer_type')
                ->select(
                    'ci.customer_id',
                    'ci.customer_first_name',
                    'ci.customer_last_name',
                    'ci.customer_contact_number',
                    'ci.customer_profile_image',
                    'ut.type'
                )
                ->where('customer_id', $customer_id)
                ->get();
            $customerInfo = json_decode(json_encode($customerInfo), true);
            $customerInfo = $customerInfo[0];
            $data['customerInfo'] = $customerInfo;

            return Response::json($data);
        }
    }

    //function to calculate customer bill
    public function calculateBill()
    {
        $bill = $_POST['bill'];
        $customerID = $_POST['customer_id'];
        $partnerID = $_POST['partner_id'];
        $discount_percentage = DB::table('customer_info as ci')
            ->join('discount as dis', 'dis.user_type', '=', 'ci.customer_type')
            ->select('discount_percentage')
            ->where('ci.customer_id', $customerID)
            ->where('dis.partner_account_id', $partnerID)
            ->get();
        $discount_percentage = json_decode(json_encode($discount_percentage), true);
        $discount_percentage = $discount_percentage[0]['discount_percentage'];

        if ($_POST['customerRequest'] != 0) {
            //get refer bonus amount from database
            $refer_bonus_from_db = DB::table('all_amounts')->select('price')->where('type', 'refer_bonus')->first();

            $customerRequest = explode('_', $_POST['customerRequest']);
            $transaction_details['requestCode'] = (new functionController)->encrypt_decrypt('encrypt', $customerRequest[1]);
            if ($customerRequest[0] == 1) {
                $payable_amount = $bill;
                $discount = 0;
            } elseif ($customerRequest[0] == 2) {
                $discount = ((($bill * $discount_percentage) / 100) + $refer_bonus_from_db->price);
                $payable_amount = $bill - $discount;
                $payable_amount < 0 ? $payable_amount = 0 : $payable_amount = $payable_amount;
            } else {
                $payable_amount = $bill;
                $discount = 0;
            }
        } else {
            $discount = (($bill * $discount_percentage) / 100);
            $payable_amount = $bill - $discount;
            $transaction_details['requestCode'] = (new functionController)->encrypt_decrypt('encrypt', $_POST['customerRequest']);
        }

        $transaction_details['customerID'] = $customerID;
        $transaction_details['bill'] = intval($bill);
        $transaction_details['discount'] = intval($discount);
        $transaction_details['bill_amount'] = intval($payable_amount);
        //encrypt values to send to database
        $transaction_details['encrypted_customerID'] = (new functionController)->encrypt_decrypt('encrypt', $customerID);
        $transaction_details['encrypted_bill'] = (new functionController)->encrypt_decrypt('encrypt', intval($bill));
        $transaction_details['encrypted_discount'] = (new functionController)->encrypt_decrypt('encrypt', intval($discount));
        $transaction_details['encrypted_bill_amount'] = (new functionController)->encrypt_decrypt('encrypt', intval($payable_amount));

        return Response::json($transaction_details);
    }

    //Function for confirming discount and store transaction data in database
    public function confirmDiscount(Request $request)
    {
        //get all values from confirm modal
        $partnerID = $request->session()->get('partner_id');
        $customerID = (new functionController)->encrypt_decrypt('decrypt', $request->get('customer_id'));
        $bill_amount = (new functionController)->encrypt_decrypt('decrypt', $request->get('final_bill'));
        $discount = (new functionController)->encrypt_decrypt('decrypt', $request->get('discount'));
        $requestCode = (new functionController)->encrypt_decrypt('decrypt', $request->get('requestCode'));

        if ($requestCode != 0) {
            //get request id from bonus request table by request code
            $req_id = DB::table('bonus_request')->select('req_id')->where('request_code', $requestCode)->first();
            $req_id = $req_id->req_id;
            $notif_text = 'You availed coupon at ';
        } else {
            $req_id = null;
            $notif_text = 'You got a discount at ';
        }

        try {
            DB::beginTransaction(); //to do query rollback

            //insert transaction info to transaction table
            DB::table('transaction_table')->insert(
                [
                    'partner_account_id' => $partnerID,
                    'customer_id' => $customerID,
                    'amount_spent' => $bill_amount,
                    'discount_amount' => $discount,
                    'req_id' => $req_id,
                ]
            );
            //make bonus request used with req_id
            if ($requestCode != null) {
                DB::table('bonus_request')
                    ->where('customer_id', $customerID)
                    ->where('req_id', $req_id)
                    ->update(['used' => 1]);
            }
            $last_transaction_id = DB::table('transaction_table')
                ->select('id')
                ->orderBy('id', 'DESC')
                ->take(1)
                ->get();
            $last_transaction_id = json_decode(json_encode($last_transaction_id), true);
            $last_transaction_id = $last_transaction_id[0]['id'];
            //insert transaction info into customer notification table
            DB::table('customer_notification')->insert(
                [
                    'user_id' => $customerID,
                    'image_link' => Session::get('partner_profile_image'),
                    'notification_text' => $notif_text,
                    'notification_type' => 3,
                    'source_id' => $last_transaction_id,
                    'seen' => 0,
                ]
            );

            DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            DB::rollBack(); //rollback all successfully executed queries
            return redirect()->back()->with('try_again', 'Please try again!');
        }
        if (Session::has('partner_id')) {
            //get all unseen notification of this partner
            $unseenNotifications = (new functionController)->partnerUnseenNotifications(Session::get('partner_id'));
            session(['unseenNotifications' => $unseenNotifications]);
        }
        //trigger 'setPusher' function to do live push notification for website
        event(new offer_availed($customerID));
//        (new pusherController)->liveDiscountNotification($customerID);
        //message to send as parameter
        $message = $notif_text.Session::get('partner_name');
        $customer = DB::table('customer_info')->where('customer_id', $customerID)->first();
        //send notification to app
        (new jsonController)->functionSendGlobalPushNotification($message, $customer, notificationType::transaction);

        return Redirect('partners/'.Session::get('partner_username'));
    }

    //function to make partner review notification seen
    public function unseen_review_notification_of_partner($id)
    {
        $ids = explode('_', $id);
        DB::table('partner_notification')
            ->where('id', $ids[1])
            ->update([
                'seen' => 1,
            ]);
        //get all unseen notification of this partner
        $unseenNotifications = (new functionController)->partnerUnseenNotifications(session('partner_id'));
        session(['unseenNotifications' => $unseenNotifications]);
        //get all seen notification of this partner
        $seenNotifications = (new functionController)->partnerSeenNotifications(session('partner_id'));
        session(['seenNotifications' => $seenNotifications]);
        $encrypted_id = (new functionController)->socialShareEncryption('encrypt', $ids[0]);

        return redirect('review/'.$encrypted_id);
    }

    //function to make partner follow notification seen
    public function unseen_follow_notification_of_partner($id)
    {
        DB::table('partner_notification')
            ->where('id', $id)
            ->update([
                'seen' => 1,
            ]);
        $customer_username = DB::table('follow_partner as fp')
            ->join('partner_notification as pn', 'pn.source_id', '=', 'fp.id')
            ->join('customer_account as ca', 'ca.customer_id', '=', 'fp.follower')
            ->select('ca.customer_username')
            ->where('pn.id', $id)
            ->first();
        //get all unseen notification of this partner
        $unseenNotifications = (new functionController)->partnerUnseenNotifications(session('partner_id'));
        session(['unseenNotifications' => $unseenNotifications]);
        //get all seen notification of this partner
        $seenNotifications = (new functionController)->partnerSeenNotifications(session('partner_id'));
        session(['seenNotifications' => $seenNotifications]);

        return Redirect('/user-profile/'.$customer_username->customer_username);
    }

    //function to make partner like post notification seen
    public function post_like_notification_of_partner($ids)
    {
        $ids = explode('_', $ids);
        DB::table('partner_notification')
            ->where('id', $ids[0])
            ->update([
                'seen' => 1,
            ]);
        //get post id from source id
        $post_id = DB::table('likes_post')->select('post_id')->where('id', $ids[1])->first();
        //get single post details
        $single_post = DB::table('post')->where('id', $post_id->post_id)->first();

        $single_post->total_likes = (new functionController)->total_likes_of_a_post($post_id->post_id);

        //return to partner account with post id
        session(['single_noti' => $single_post]);

        return redirect('partners/'.Session::get('partner_username'))->with([
            'post_notification_seen' => $post_id->post_id,
        ]);
    }

    //function to show seen review notification of partner
    public function seen_review_notification_of_partner($id)
    {
        $ids = explode('_', $id);
        DB::table('partner_notification')
            ->where('id', $ids[1])
            ->update([
                'seen' => 1,
            ]);
        //get all unseen notification of this partner
        $unseenNotifications = (new functionController)->partnerUnseenNotifications(session('partner_id'));
        session(['unseenNotifications' => $unseenNotifications]);
        //get all seen notification of this partner
        $seenNotifications = (new functionController)->partnerSeenNotifications(session('partner_id'));
        session(['seenNotifications' => $seenNotifications]);
        $encrypted_id = (new functionController)->socialShareEncryption('encrypt', $ids[0]);

        return redirect('review/'.$encrypted_id);
    }

    //function to make partner follow notification seen
    public function seen_follow_notification_of_partner($id)
    {
        DB::table('partner_notification')
            ->where('id', $id)
            ->update([
                'seen' => 1,
            ]);
        $customer_username = DB::table('follow_partner as fp')
            ->join('partner_notification as pn', 'pn.source_id', '=', 'fp.id')
            ->join('customer_account as ca', 'ca.customer_id', '=', 'fp.follower')
            ->select('ca.customer_username')
            ->where('pn.id', $id)
            ->first();
        //get all unseen notification of this partner
        $unseenNotifications = (new functionController)->partnerUnseenNotifications(session('partner_id'));
        session(['unseenNotifications' => $unseenNotifications]);
        //get all seen notification of this partner
        $seenNotifications = (new functionController)->partnerSeenNotifications(session('partner_id'));
        session(['seenNotifications' => $seenNotifications]);

        return Redirect('/user-profile/'.$customer_username->customer_username);
    }

    //function to login partner admin
    public function partnerAdminLogin(Request $request)
    {
        $code = $request->input('code');
        $DBcode = DB::table('partner_account')->where('partner_account_id', Session::get('partner_id'))->pluck('admin_code');
        if ($code == $DBcode[0]) {
            session(['partner_admin' => 'admin logged in']);

            return Response::json('1');
        } else {
            return Response::json('0');
        }
    }

    //function to show partner admin dashboard
    public function partnerAdminDashboard($username)
    {
        $partnerInfo = PartnerAccount::with('branches')->where('partner_account_id', Session::get('partner_id'))->first();
        //fetch the main branch (by default : show main branch info)
        $branch_info = PartnerBranch::where([['main_branch', 1], ['partner_account_id', $partnerInfo->partner_account_id]])
            ->with(['info'])->first();

        $statistics = (new functionController)->analyticsOfPartner($partnerInfo->partner_account_id, $branch_info->id);

        return view('partner-admin.production.index', ['statistics' => $statistics, 'partnerInfo' => $partnerInfo, 'branch_id' => $branch_info->id]);
    }

    //function to get all notification of a specific partner
    public function allNotifications()
    {
        $partnerInfo = DB::table('partner_info as pi')
            ->join('partner_profile_images as ppi', 'ppi.partner_account_id', '=', 'pi.partner_account_id')
            ->select('pi.partner_name', 'pi.partner_category', 'pi.partner_account_id', 'ppi.partner_profile_image')
            ->inRandomOrder()
            ->limit(4)
            ->get();
        $partnerInfo = json_decode(json_encode($partnerInfo), true);

        $i = 0;
        foreach ($partnerInfo as $partner) {
            $main_branch = (new functionController)->mainBranchOfPartner($partner['partner_account_id']);
            if (count($main_branch) > 0) {
                $partnerInfo[$i]['main_branch_id'] = $main_branch[0]->id;
            } else {
                unset($partnerInfo[$i]);
            }
            $i++;
        }
        //get info of top brands partners
        $topBrands = (new functionController)->topBrands();
        //get categories list
        $categories = Categories::all();

        $allNotifications = (new functionController)->partnerAllNotifications(Session::get('partner_id'));
        session(['partnerAllNotifications' => $allNotifications]);

        return view('partner_all_notifications', compact('partnerInfo', 'topBrands', 'categories'));
    }

    //================================= partner admin panel =================================================
    //function to get discount details of a partner
    public function editDiscount()
    {
        $discountInfo = DB::table('discount')
            ->where('partner_account_id', Session::get('partner_id'))
            ->get();
        $discountInfo = json_decode(json_encode($discountInfo), true);
        $tnc = DB::table('tnc_for_partner')->where('partner_account_id', Session::get('partner_id'))->get();
        $tnc = json_decode(json_encode($tnc), true);
        $discountInfo['tnc'] = $tnc[0]['terms&condition'];

        return view('partner-admin.production.edit_discount', compact('discountInfo'));
    }

    //function to get discount details of a partner
    public function editDiscountDone(Request $request)
    {
        $this->validate($request, [
            'discount_for_gold' => 'required',
            'discount_for_platinum' => 'required',
            'discount_details_for_gold' => 'required',
            'discount_details_for_platinum' => 'required',
            'tnc_for_partner' => 'required',
            'discount_expiry_for_gold' => 'required',
            'discount_expiry_for_platinum' => 'required',
        ]);
        $request->flashOnly(['discount_for_gold', 'discount_for_platinum', 'discount_details_for_gold', 'discount_details_for_platinum', 'tnc_for_partner', 'discount_expiry_for_gold', 'discount_expiry_for_platinum']);
        $discount_for_gold = $request->get('discount_for_gold');
        $discount_for_platinum = $request->get('discount_for_platinum');
        $discount_details_for_gold = $request->get('discount_details_for_gold');
        $discount_details_for_platinum = $request->get('discount_details_for_platinum');
        $tnc_for_partner = $request->get('tnc_for_partner');
        $discount_expiry_for_gold = $request->get('discount_expiry_for_gold');
        $discount_expiry_for_platinum = $request->get('discount_expiry_for_platinum');

        try {
            DB::beginTransaction(); //to do query rollback

            //update discount for gold
            DB::table('discount')
                ->where('partner_account_id', Session::get('partner_id'))
                ->where('user_type', 1)
                ->update([
                    'discount_percentage' => $discount_for_gold,
                    'discount_details' => $discount_details_for_gold,
                    'expiry_date' => $discount_expiry_for_gold,
                ]);
            //update discount for platinum
            DB::table('discount')
                ->where('partner_account_id', Session::get('partner_id'))
                ->where('user_type', 2)
                ->update([
                    'discount_percentage' => $discount_for_platinum,
                    'discount_details' => $discount_details_for_platinum,
                    'expiry_date' => $discount_expiry_for_platinum,
                ]);
            //update terms&condition for partner
            DB::table('tnc_for_partner')
                ->where('partner_account_id', Session::get('partner_id'))
                ->update([
                    'terms&condition' => $tnc_for_partner,
                ]);

            DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            DB::rollBack(); //rollback all successfully executed queries
            return redirect()->back()->with('try_again', 'Please try again!');
        }

        return redirect('partner/admin-dashboard/edit-discount')->with('updated', 'Successful');
    }

    //function to get subcategory details of a partner view
    public function editSubCategory()
    {
        $subcatInfo = PartnerAccount::where('partner_account_id', Session::get('partner_id'))->with('categoryRelation', 'info')->first();

        return view('partner-admin.production.edit_subcategory', compact('subcatInfo'));
    }

    //UPDATE PARTNER Subcategories (Sub Cats / Tertiary Cat)
    public function editSubcategoryDone()
    {
        //get category from partner id
        $partner_id = $_POST['partner_id'];
        $is_checked = $_POST['is_checked'];
        $rel_id = $_POST['rel_id'];
        $data['rel_id'] = $rel_id;
        if ($is_checked == 1) {
            PartnerCategoryRelation::where('partner_id', $partner_id)->where('cat_rel_id', $rel_id)->delete();
            $data['is_checked'] = 0;
        } else {
            $cat_rel_insert = new PartnerCategoryRelation(['partner_id' => $partner_id, 'cat_rel_id' => $rel_id]);
            $cat_rel_insert->save();
            $data['is_checked'] = 1;
        }

        return Response::json($data);
    }

    //UPDATE PARTNER FACILITIES
    public function editFacilitiesDone(Request $request)
    {
        //get category from partner id
        $partner_id = Session::get('partner_id');
        $is_checked = $_POST['is_checked'];
        $facility_title = $_POST['facility_title'];
        $data['facility_title'] = $facility_title;
        if ($is_checked == 1) {
            //update partner facilities
            if ($facility_title == 'card_payment') {
                DB::table('partner_facilities')
                    ->where('partner_account_id', Session::get('partner_id'))
                    ->update([
                        'card_payment' => 0,
                    ]);
            } elseif ($facility_title == 'kids_area') {
                DB::table('partner_facilities')
                    ->where('partner_account_id', Session::get('partner_id'))
                    ->update([
                        'kids_area' => 0,
                    ]);
            } elseif ($facility_title == 'outdoor_seating') {
                DB::table('partner_facilities')
                    ->where('partner_account_id', Session::get('partner_id'))
                    ->update([
                        'outdoor_seating' => 0,
                    ]);
            } elseif ($facility_title == 'smoking_area') {
                DB::table('partner_facilities')
                    ->where('partner_account_id', Session::get('partner_id'))
                    ->update([
                        'smoking_area' => 0,
                    ]);
            } elseif ($facility_title == 'reservation') {
                DB::table('partner_facilities')
                    ->where('partner_account_id', Session::get('partner_id'))
                    ->update([
                        'reservation' => 0,
                    ]);
            } elseif ($facility_title == 'wifi') {
                DB::table('partner_facilities')
                    ->where('partner_account_id', Session::get('partner_id'))
                    ->update([
                        'wifi' => 0,
                    ]);
            } elseif ($facility_title == 'online_booking') {
                DB::table('partner_facilities')
                    ->where('partner_account_id', Session::get('partner_id'))
                    ->update([
                        'online_booking' => 0,
                    ]);
            } elseif ($facility_title == 'seating_area') {
                DB::table('partner_facilities')
                    ->where('partner_account_id', Session::get('partner_id'))
                    ->update([
                        'seating_area' => 0,
                    ]);
            } elseif ($facility_title == 'concierge') {
                DB::table('partner_facilities')
                    ->where('partner_account_id', Session::get('partner_id'))
                    ->update([
                        'concierge' => 0,
                    ]);
            }

            $data['is_checked'] = 0;

            return Response::json($data);
        } else {
            //update partner facilities
            if ($facility_title == 'card_payment') {
                DB::table('partner_facilities')
                    ->where('partner_account_id', Session::get('partner_id'))
                    ->update([
                        'card_payment' => 1,
                    ]);
            } elseif ($facility_title == 'kids_area') {
                DB::table('partner_facilities')
                    ->where('partner_account_id', Session::get('partner_id'))
                    ->update([
                        'kids_area' => 1,
                    ]);
            } elseif ($facility_title == 'outdoor_seating') {
                DB::table('partner_facilities')
                    ->where('partner_account_id', Session::get('partner_id'))
                    ->update([
                        'outdoor_seating' => 1,
                    ]);
            } elseif ($facility_title == 'smoking_area') {
                DB::table('partner_facilities')
                    ->where('partner_account_id', Session::get('partner_id'))
                    ->update([
                        'smoking_area' => 1,
                    ]);
            } elseif ($facility_title == 'reservation') {
                DB::table('partner_facilities')
                    ->where('partner_account_id', Session::get('partner_id'))
                    ->update([
                        'reservation' => 1,
                    ]);
            } elseif ($facility_title == 'wifi') {
                DB::table('partner_facilities')
                    ->where('partner_account_id', Session::get('partner_id'))
                    ->update([
                        'wifi' => 1,
                    ]);
            } elseif ($facility_title == 'online_booking') {
                DB::table('partner_facilities')
                    ->where('partner_account_id', Session::get('partner_id'))
                    ->update([
                        'online_booking' => 1,
                    ]);
            } elseif ($facility_title == 'seating_area') {
                DB::table('partner_facilities')
                    ->where('partner_account_id', Session::get('partner_id'))
                    ->update([
                        'seating_area' => 1,
                    ]);
            } elseif ($facility_title == 'concierge') {
                DB::table('partner_facilities')
                    ->where('partner_account_id', Session::get('partner_id'))
                    ->update([
                        'concierge' => 1,
                    ]);
            }
            $data['is_checked'] = 1;

            return Response::json($data);
        }
    }

    //function to get opening hours of a partner view
    public function editOpeningHours()
    {
        $openingHours = DB::table('opening_hours')
            ->where('partner_account_id', Session::get('partner_id'))
            ->get();
        $openingHours = json_decode(json_encode($openingHours), true);
        $openingHours = $openingHours[0];

        return view('partner-admin.production.edit_openingHours', compact('openingHours'));
    }

    //function to update opening hours of a partner backend
    public function editOpeningHoursDone(Request $request)
    {
        $this->validate($request, [
            'sat' => 'required',
            'sun' => 'required',
            'mon' => 'required',
            'tues' => 'required',
            'wed' => 'required',
            'thu' => 'required',
            'fri' => 'required',
        ]);
        $request->flashOnly(['sat', 'sun', 'mon', 'tues', 'wed', 'thu', 'fri']);

        $sat = ($request->get('sat')) != null ? $request->get('sat') : 0;
        $sun = ($request->get('sun')) != null ? $request->get('sun') : 0;
        $mon = ($request->get('mon')) != null ? $request->get('mon') : 0;
        $tue = ($request->get('tues')) != null ? $request->get('tues') : 0;
        $wed = ($request->get('wed')) != null ? $request->get('wed') : 0;
        $thu = ($request->get('thu')) != null ? $request->get('thu') : 0;
        $fri = ($request->get('fri')) != null ? $request->get('fri') : 0;

        //update attribute in attribute table
        DB::table('opening_hours')
            ->where('partner_account_id', Session::get('partner_id'))
            ->update([
                'sat' => $sat,
                'sun' => $sun,
                'mon' => $mon,
                'tue' => $tue,
                'wed' => $wed,
                'thurs' => $thu,
                'fri' => $fri,
            ]);

        return redirect('partner/admin-dashboard/edit-opening-hours')->with('updated', 'Successful');
    }

    //function to edit basic info view page
    public function editBasicInfo()
    {
        $partnerInfo = (new functionController)->partnerData(Session::get('partner_id'));
        $partnerInfo->makeVisible('admin_code');

        session(['partner_profile_image' => $partnerInfo->info->profileImage->partner_profile_image]);
        //$mob_init = explode('+880', $partnerInfo['partner_mobile']);
        //$partnerInfo['mobile'] = $mob_init[1];

        return view('partner-admin.production.edit_info', compact('partnerInfo'));
    }

    //function to update basic info of a partner backend
    public function storeBasicInfo(Request $request)
    {
        $this->validate($request, [
            'type' => 'required',
            'admin_code' => 'required',
            'about' => 'required',
            'password' => 'sometimes|nullable|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
            'confirm_pass' => 'sometimes|same:password',
        ]);
        $request->flashOnly(['type', 'admin_code', 'about', 'password', 'confirm_pass']);

        $category = $request->get('category');
        $ownerName = $request->get('owner') != null ? $request->get('owner') : '';
        $type = $request->get('type');
        $admin_code = $request->get('admin_code');
        $facebook = $request->get('facebook') != null ? $request->get('facebook') : '';
        $website = $request->get('website') != null ? $request->get('website') : '';
        $instagram = $request->get('instagram') != null ? $request->get('instagram') : '';
        $about = $request->get('about');
        $password = $request->get('password');

        try {
            DB::beginTransaction(); //to do query rollback
            //update password
            if ($password != null) {
                $encrypted_password = (new functionController)->encrypt_decrypt('encrypt', $password);
                //update password in partner account table
                PartnerAccount::where('partner_account_id', Session::get('partner_id'))
                    ->update([
                        'password' => $encrypted_password,
                    ]);
            }
            //update partner info in database
            PartnerInfo::where('partner_account_id', Session::get('partner_id'))->update([
                'owner_name' => $ownerName,
                'partner_category' => $category,
                'partner_type' => $type,
                'facebook_link' => $facebook,
                'website_link' => $website,
                'instagram_link' => $instagram,
                'partner_type' => $type,
                'about' => $about,
            ]);
            //update admin_code in partner account table
            PartnerAccount::where('partner_account_id', Session::get('partner_id'))
                ->update([
                    'admin_code' => $admin_code,
                ]);
            DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            dd($e);
            DB::rollBack(); //rollback all successfully executed queries
            return redirect()->back()->with('try_again', 'Please try again!');
        }

        return redirect('partner/edit-basic-info')->with('updated', 'Successful');
    }

    //========== function for showing all Branches info ============
    public function allBranches()
    {
        //get all basic branch info for partner admin panel
        $allBranches = PartnerAccount::where('partner_account_id', Session::get('partner_id'))
            ->with('branches')->first();

        return view('partner-admin/production/allBranches', compact('allBranches'));
    }

    //function to edit post in partner admin view
    public function editBranch($id)
    {
        //get all branch information for partner admin panel
        $branchInfo = PartnerBranch::where('id', $id)
            ->with('facilities', 'openingHours', 'account.info')->first();

        $all_areas = Area::all();
        $all_divs = Division::all();

        return view('partner-admin.production.edit_branch', compact('branchInfo', 'all_areas', 'all_divs'));
    }

    //function to update basic info of a partner backend
    public function editBranchDone(Request $request, $branch_id)
    {
        $this->validate($request, [
            'branch_mobile' => 'required',
            'branch_email' => 'required|email',
            'sat' => 'required',
            'sun' => 'required',
            'mon' => 'required',
            'tues' => 'required',
            'wed' => 'required',
            'thu' => 'required',
            'fri' => 'required',
            'branch_address' => 'required',
            'branch_location' => 'required',
            'longitude' => 'required',
            'latitude' => 'required',
        ]);
        $request->flashOnly([
            'branch_mobile', 'branch_email', 'sat', 'sun', 'mon', 'tues', 'wed',
            'thu', 'fri', 'branch_address', 'branch_location', 'longitude', 'latitude',
        ]);

        $sat = ($request->get('sat')) != null ? $request->get('sat') : 0;
        $sun = ($request->get('sun')) != null ? $request->get('sun') : 0;
        $mon = ($request->get('mon')) != null ? $request->get('mon') : 0;
        $tue = ($request->get('tues')) != null ? $request->get('tues') : 0;
        $wed = ($request->get('wed')) != null ? $request->get('wed') : 0;
        $thu = ($request->get('thu')) != null ? $request->get('thu') : 0;
        $fri = ($request->get('fri')) != null ? $request->get('fri') : 0;
        $address = $request->get('branch_address');
        $area = $request->get('area');
        $division = $request->get('division');
        $email = $request->get('branch_email');
        $contact = $request->get('branch_mobile');
        $location = $request->get('branch_location');
        $longitude = $request->get('longitude');
        $latitude = $request->get('latitude');
        $is_main = ($request->get('main_branch')) == null ? 0 : 1;
        //facilities
        $card_payment = ($request->get('card_payment')) == null ? 0 : 1;
        $kids_area = ($request->get('kids_area')) == null ? 0 : 1;
        $outdoor_seating = ($request->get('outdoor_seating')) == null ? 0 : 1;
        $smoking_area = ($request->get('smoking_area')) == null ? 0 : 1;
        $reservation = ($request->get('reservation')) == null ? 0 : 1;
        $wifi = ($request->get('wifi')) == null ? 0 : 1;
        $online_booking = ($request->get('online_booking')) == null ? 0 : 1;
        $seating_area = ($request->get('seating_area')) == null ? 0 : 1;
        $concierge = ($request->get('concierge')) == null ? 0 : 1;

        try {
            DB::beginTransaction(); //to do query rollback
            //update branch opening hours in database
            OpeningHours::where('branch_id', $branch_id)->update([
                'sat' => $sat,
                'sun' => $sun,
                'mon' => $mon,
                'tue' => $tue,
                'wed' => $wed,
                'thurs' => $thu,
                'fri' => $fri,
            ]);
            //update partner branch info in database
            PartnerBranch::where('id', $branch_id)->update([
                'partner_email' => $email,
                'partner_mobile' => $contact,
                'partner_address' => $address,
                'partner_location' => $location,
                'longitude' => $longitude,
                'latitude' => $latitude,
                'partner_area' => $area,
                'partner_division' => $division,
                'main_branch' => $is_main,
            ]);
            if ($is_main == 1) {
                //Change other branches to Not_Main
                PartnerBranch::where('id', '!=', $branch_id)
                    ->where('partner_account_id', Session::get('partner_id'))
                    ->update([
                        'main_branch' => 0,
                    ]);
            }
            //update branch opening hours in database
            PartnerFacilities::where('branch_id', $branch_id)->update([
                'card_payment' => $card_payment,
                'kids_area' => $kids_area,
                'outdoor_seating' => $outdoor_seating,
                'smoking_area' => $smoking_area,
                'reservation' => $reservation,
                'wifi' => $wifi,
                'seating_area' => $seating_area,
                'online_booking' => $online_booking,
                'concierge' => $concierge,
            ]);
            DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            DB::rollBack(); //rollback all successfully executed queries
            dd($e);

            return redirect()->back()->with('try_again', 'Please try again!');
        }

        return redirect('allBranches')->with('updated', 'Successful');
    }

    //function to add new post in partner admin
    public function addPost(Request $request)
    {
        $this->validate($request, [
            'postHeader' => 'required',
            'postCaption' => 'required',
        ]);
        $request->flashOnly(['postHeader', 'postCaption']);
        $postHeader = $request->get('postHeader');
        $postCaption = $request->get('postCaption');

        //upload image to aws & save url to DB
        $file = $request->file('postImage');
        //image is being resized & uploaded here

        $image_url = (new functionController)->uploadImageToAWS($file, 'dynamic-images/partner_post_image');

        try {
            DB::beginTransaction(); //to do query rollback

            //insert into partner post table
            Post::insert([
                'partner_account_id' => Session::get('partner_id'),
                'image_url' => $image_url,
                'caption' => $postCaption,
            ]);
            //get last post id
            $last_post_id = DB::table('partner_post')->select('id')->orderBy('id', 'DESC')->first();
            $last_post_id = json_decode(json_encode($last_post_id), true);
            $last_post_id = $last_post_id['id'];
            //insert into partner_post_header table
            PartnerPostHeader::insert([
                'post_id' => $last_post_id,
                'header' => $postHeader,
            ]);

            DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            $image_url = explode('/', $image_url);
            Storage::disk('s3')->delete('dynamic-images/partner_post_image/'.end($image_url));
            DB::rollBack(); //rollback all successfully executed queries
            return redirect()->back()->with('try_again', 'Please try again!');
        }

        return redirect('partner/admin-dashboard/all-posts')->with('updated', 'Successful');
    }

    //function to show all posts in partner admin
    public function allPosts()
    {
        $allPosts = Post::where('poster_id', Session::get('partner_id'))
            ->orderBy('id', 'DESC')
            ->get();

        return view('partner-admin.production.allPosts', compact('allPosts'));
    }

    //function to edit post in partner admin view
    public function editPost($id)
    {
        $post = Post::findOrFail($id);

        return view('partner-admin.production.editPost', compact('post'));
    }

    //function to edit post in partner admin backend
    public function editPostDone(Request $request, $id)
    {
        $this->validate($request, [
            'header' => 'required',
            'caption' => 'required',
        ]);
        $header = $request->get('header');
        $caption = $request->get('caption');

        $post = Post::findOrFail($id);

        if ($request->hasFile('postImage')) {
            //at first delete the previous image
            $get_current_image_path = DB::table('partner_post')
                ->where('id', $id)
                ->get();
            $get_current_image_path = json_decode(json_encode($get_current_image_path), true);
            $image_path = $get_current_image_path[0]['image_url'];
            $exploded_path = explode('/', $image_path);
            //remove previous post image from bucket
            Storage::disk('s3')->delete('dynamic-images/partner_post_image/'.end($exploded_path));
            //upload image to aws & save url to DB
            $file = $request->file('postImage');
            //image is being resized & uploaded here
            $image_url = (new functionController)->uploadImageToAWS($file, 'dynamic-images/partner_post_image');

            //image path saved to the database
            $post->image_url = $image_url;
        }
        //update table with caption
        $post->caption = $caption;
        $post->save();

        //update table with header
        PartnerPostHeader::where('post_id', $id)
            ->update([
                'header' => $header,
            ]);

        //return to all post page
        return redirect('partner/admin-dashboard/all-posts')->with('updated', 'Successful');
    }

    //function to delete post
    public function deletePost($id)
    {
        //get source ids of this post from likes_post table
        $source_ids = LikePost::select('id')->where('post_id', $id)->get();
        //$source_ids = json_decode(json_encode($source_ids),true);

        //at first delete the post image
        // $get_current_image_path = DB::table('partner_post')
        //     ->where('id', $id)
        //     ->get();

        // $get_current_image_path = json_decode(json_encode($get_current_image_path),true);
        // $get_current_image_path = $get_current_image_path[0]['image_url'];
        $post = Post::findOrFail($id);
        $get_current_image_path = $post->image_url;
        $exploded_path = explode('/', $get_current_image_path);

        try {
            DB::beginTransaction(); //to do query rollback

            //delete post from partner_post table
            $post->delete();
            if ($source_ids != null) {
                // delete notifications from partner notification table
                foreach ($source_ids as $source) {
                    PartnerNotification::where('partner_account_id', Session::get('partner_id'))
                        ->where('notification_type', 7)
                        ->where('source_id', $source['id'])
                        ->delete();
                }
            }
            //remove previous post image from bucket
            Storage::disk('s3')->delete('dynamic-images/partner_post_image/'.end($exploded_path));
            DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            DB::rollBack(); //rollback all successfully executed queries
            return redirect()->back()->with('try_again', 'Please try again!');
        }

        return redirect('partner/admin-dashboard/all-posts')->with('updated', 'Successful');
    }

    //function to get profile image of partner
    public function profileImage()
    {
        $profileImage = PartnerProfileImage::where('partner_account_id', Session::get('partner_id'))
            ->get();
        $profileImage = json_decode(json_encode($profileImage), true);
        $profileImage = $profileImage[0];

        return view('partner-admin.production.profileImage', compact('profileImage'));
    }

    //function to crop image in partner admin
    public function imageCrop(Request $request)
    {
        $data = $_POST['partnerProfileImage'];
        list($type, $data) = explode(';', $data);
        list(, $data) = explode(',', $data);
        $data = base64_decode($data);
        $imageName = time().'.jpg';
        Session::put('partner_profile_image_name', $imageName);
        Session::put('partner_profile_image', $data);

        echo 'Image Uploaded';
    }

    //function to update profile image in partner admin backend
    public function uploadCroppedImage()
    {
        $get_current_image_name = PartnerProfileImage::where('partner_account_id', Session::get('partner_id'))
            ->get();
        $get_current_image_name = json_decode(json_encode($get_current_image_name), true);
        $image_path = $get_current_image_name[0]['partner_profile_image'];
        $exploded_path = explode('/', $image_path);

        try {
            DB::beginTransaction(); //to do query rollback

            //Upload File to s3
            Storage::disk('s3')->put('dynamic-images/partner_pro_pic/'.Session::get('partner_profile_image_name'), Session::get('partner_profile_image'), 'public');
            $url = Storage::disk('s3')->url('dynamic-images/partner_pro_pic/'.Session::get('partner_profile_image_name'));
            //update image path with new image name
            PartnerProfileImage::where('partner_account_id', Session::get('partner_id'))
                ->update([
                    'partner_profile_image' => $url,
                ]);
            //update image path in customer notification table
            CustomerNotification::where('image_link', $image_path)
                ->update([
                    'image_link' => $url,
                ]);
            //remove previous profile image from bucket
            Storage::disk('s3')->delete('dynamic-images/partner_pro_pic/'.end($exploded_path));
            DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            $url = explode('/', $url);
            Storage::disk('s3')->delete('dynamic-images/partner_pro_pic/'.end($url));
            DB::rollBack(); //rollback all successfully executed queries
            return redirect()->back()->with('try_again', 'Please try again!');
        }

        return redirect('partner/admin-dashboard/profile-image')->with('updated', 'Successful');
    }

    //function to get Cover pic of partner
    public function coverPic()
    {
        $coverPic = PartnerProfileImage::where('partner_account_id', Session::get('partner_id'))->first();

        return view('partner-admin.production.coverPic', compact('coverPic'));
    }

    //function to update Cover Pic
    public function updateCoverPic(Request $request)
    {
        $this->validate($request, [
            'coverPic' => 'required',
        ]);
        //upload cover pic to AWS & save path to DB
        $image_file = $request->file('coverPic');
        //image is being resized & uploaded here
        $image_url = (new functionController)->uploadImageToAWS($image_file, 'dynamic-images/partner_cover_pics');

        $coverPic = PartnerProfileImage::where('partner_account_id', Session::get('partner_id'))->first();
        $image_path = $coverPic->partner_cover_photo;
        $exploded_path = explode('/', $image_path);

        try {
            DB::beginTransaction(); //to do query rollback

            //image path saved to the database
            $coverPic->partner_cover_photo = $image_url;
            $coverPic->save();

            //remove previous profile image from bucket
            Storage::disk('s3')->delete('dynamic-images/partner_cover_pics/'.end($exploded_path));

            DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            $image_url = explode('/', $image_url);
            Storage::disk('s3')->delete('dynamic-images/partner_cover_pics/'.end($image_url));
            DB::rollBack(); //rollback all successfully executed queries
            return redirect()->back()->with('try_again', 'Please try again!');
        }

        return redirect('partner/admin-dashboard/cover-photo')->with('updated', 'Successful');
    }

    //function to get menu images of partner
    public function menuImages()
    {
        $menuImages = PartnerMenuImages::where('partner_account_id', Session::get('partner_id'))
            ->get();
        $menuImages = json_decode(json_encode($menuImages), true);

        return view('partner-admin.production.menuImages', compact('menuImages'));
    }

    //function to delete menu of partner
    public function deleteMenuImage($id)
    {
        $partner_menu_image = PartnerMenuImages::findOrFail($id);
        //$get_current_image_path = json_decode(json_encode($get_current_image_path),true);
        $get_current_image_path = $partner_menu_image->partner_menu_image;
        $exploded_path = explode('/', $get_current_image_path);

        try {
            DB::beginTransaction(); //to do query rollback

            //remove image path from partner_menu_images table

            $partner_menu_image->delete();
            //remove menu image from bucket
            Storage::disk('s3')->delete('dynamic-images/partner_menu_image/'.end($exploded_path));

            DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            DB::rollBack(); //rollback all successfully executed queries
            return redirect()->back()->with('try_again', 'Please try again!');
        }

        return redirect('partner/admin-dashboard/menu-images')->with('updated', 'Successful');
    }

    //function to add new menu image
    public function addMenuImage(Request $request)
    {
        $this->validate($request, [
            'menu' => 'required',
        ]);
        //upload menu image to AWS & save path to DB
        $files = $request->file('menu');
        $countPreviousImages = PartnerMenuImages::where('partner_account_id', Session::get('partner_id'))
            ->count();

        if ($countPreviousImages + count($files) <= 20) {
            foreach ($files as $file) {
                try {
                    DB::beginTransaction(); //to do query rollback
                    //image is being resized & uploaded here
                    $image_url = (new functionController)->uploadImageToAWS($file, 'dynamic-images/partner_menu_image');
                    //image path saved to the database
                    PartnerMenuImages::insert([
                        'partner_account_id' => Session::get('partner_id'),
                        'partner_menu_image' => $image_url,
                    ]);
                    DB::commit(); //to do query rollback
                } catch (\Exception $e) {
                    $image_url = explode('/', $image_url);
                    Storage::disk('s3')->delete('dynamic-images/partner_menu_image/'.end($image_url));
                    DB::rollBack(); //rollback all successfully executed queries
                    return redirect()->back()->with('try_again', 'Please try again!');
                }
            }
        } else {
            return redirect()->back()->with('try_again', 'Number of images exceeds the limit!');
        }

        return redirect('partner/admin-dashboard/menu-images')->with('updated', 'Successful');
    }

    //function to get gallery images of partner
    public function galleryImages()
    {
        $galleryImages = PartnerGalleryImages::where('partner_account_id', Session::get('partner_id'))
            ->get();

        return view('partner-admin.production.galleryImages', compact('galleryImages'));
    }

    //function to delete gallery of partner
    public function deleteGalleryImage($id)
    {
        $partner_gallery_image = PartnerGalleryImages::findOrFail($id);

        $get_current_image_path = $partner_gallery_image->partner_gallery_image;
        $exploded_path = explode('/', $get_current_image_path);
        try {
            DB::beginTransaction(); //to do query rollback
            //remove image path from partner_gallery_images table
            $partner_gallery_image->delete();
            //remove gallery image from bucket
            Storage::disk('s3')->delete('dynamic-images/partner_gallery_image/'.end($exploded_path));
            DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            DB::rollBack(); //rollback all successfully executed queries
            return redirect()->back()->with('try_again', 'Please try again!');
        }

        return redirect('partner/admin-dashboard/gallery-images')->with('updated', 'Successful');
    }

    //function to add new gallery image
    public function addGalleryImage(Request $request)
    {
        $this->validate($request, [
            'gallery' => 'required',
        ]);
        //insert gallery images in database
        if ($request->hasFile('gallery')) {
            $files = $request->file('gallery');
            $countPreviousImages = PartnerGalleryImages::where('partner_account_id', Session::get('partner_id'))->count();

            if ($countPreviousImages + count($files) <= 20) {
                foreach ($files as $file) {
                    try {
                        DB::beginTransaction(); //to do query rollback
                        //image is being resized & uploaded here
                        $image_url = (new functionController)->uploadImageToAWS($file, 'dynamic-images/partner_gallery_image');
                        //image path saved to the database
                        PartnerGalleryImages::insert(
                            [
                                'partner_account_id' => Session::get('partner_id'),
                                'partner_gallery_image' => $image_url,
                            ]
                        );
                        DB::commit(); //to do query rollback
                    } catch (\Exception $e) {
                        $image_url = explode('/', $image_url);
                        Storage::disk('s3')->delete('dynamic-images/partner_gallery_image/'.end($image_url));
                        DB::rollBack(); //rollback all successfully executed queries
                        return redirect()->back()->with('try_again', 'Please try again!');
                    }
                }
            } else {
                return redirect()->back()->with('try_again', 'Number of images exceeds the limit!');
            }
        } else {
            dd('image not set');
        }

        return redirect('partner/admin-dashboard/gallery-images')->with('updated', 'Successful');
    }

    //function to add gallery image caption in partner admin panel
    public function addGalleryCaption(Request $request)
    {
        //collect caption & image id from ajax request
        $imageId = $request->input('id');
        $caption = $request->input('caption');
        //update image caption
        PartnerGalleryImages::where('id', $imageId)
            ->update(['image_caption' => $caption]);

        $updated[0] = 'updated';
        $updated[1] = $imageId;

        return Response::json($updated);
    }

    //function to sort transaction history month wise
    public function sortTransactionHistory(Request $request)
    {
        $month = $request->input('month');
        $year = $request->input('year');
        $branch_id = $request->input('branch_id');
        $check_all_year = 0;
        $check_all_month = 0;
        $allTransactions = TransactionTable::where('branch_id', $branch_id)->with('customer', 'bonus.coupon', 'offer')
            ->orderBy('id', 'DESC')->get();

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
        } elseif ($year == 'all' && $month == 'all') {
            $check_all_month = 1;
        }

        $output = '';
        $point_sum = 0;
        if (count($allTransactions) > 0) {
            $output .= '<div class="table" style="text-align: center">';
            $output .= '<div class="row header table_row t-his-row">';
            $output .= '<div class="cell">Date & Time</div>';
            $output .= '<div class="cell">Customer Name</div>';
            $output .= '<div class="cell">Points</div>';
            $output .= '<div class="cell">Offers Availed</div>';
            $output .= '</div>'; //table_row

            foreach ($allTransactions as $tr) {
                $point_sum += $tr->transaction_point;
                $output .= '<div class="row table_row t-his-row">';
                $output .= '<div class="cell" data-title="Date & Time">';

                $posted_on = date('Y-M-d H:i:s', strtotime($tr->posted_on));
                $created = \Carbon\Carbon::createFromTimeStamp(strtotime($posted_on));

                $output .= date_format($created, 'd-m-y &#9202 h:i A').'</div>';
                $output .= '<div class="cell" data-title="Customer Name">';
                $output .= '<b><p>'.$tr->customer->customer_full_name.'</b></p>';
                $output .= '</div>';

                $output .= '<div class="cell" data-title="Points">'.$tr->transaction_point.'</div>';

                if ($tr->offer != null) {
                    $offer = $tr->offer->offer_description;
                } elseif ($tr->bonus != null) {
                    $offer = $tr->bonus->coupon->coupon_details;
                } else {
                    $offer = 'Discount Availed';
                }
                $output .= '<div class="cell" data-title="Offers Availed">'.$offer.'</div>';
                $output .= '</div>';
            }
            $output .= '<div class="row table_row">';
            $output .= '<div class="cell total" data-title="Total"></div>';
            $output .= '<div class="cell" data-title="Customer Name">';
            $output .= '<i class="minus-icon" aria-hidden="true"></i>';
            $output .= '</div>';

            $output .= '<div class="cell" data-title="Points"><b>'.$point_sum.'</b></div>';
            $output .= '<div class="cell" data-title="Offers Availed"><i class="minus-icon"></i></div>';
            $output .= '</div>';
            $output .= '</div>';
        } elseif (count($allTransactions) == 0 && $check_all_month == 1) {
            $output .= '<h4 class="no-info">No offers availed yet.</h4>';
        } elseif (count($allTransactions) == 0 && $check_all_year == 1) {
            $output .= '<h4 class="no-info">No offers availed during this time.</h4>';
        } else {
            $output .= '<h4 class="no-info">No offers availed during this time.</h4>';
        }

        return Response::json($output);
    }

    //function for Review Loadmore
    public function reviewLoadmore(Request $request)
    {
        $position = $request->input('position');
        $branch_id = $request->input('partner_id');
        $total_reviews = $request->input('total_reviews');
        $customer_id = session('customer_id');
        $review_loadmore = Constants::review_loadmore;
        $position = $position * $review_loadmore;

        $output = '';

        // $transactions = TransactionTable::where('branch_id', $branch_id)
        //     ->where('review_id', '!=', null)
        //     ->orderBy('id', 'DESC')
        //     ->with('review.customerInfo', 'review.partnerInfo.profileImage', 'review.likes', 'review.comments')
        //     ->offset($position)
        //     ->take($review_loadmore)
        //     ->get();
        // $transactions = collect($transactions)->sortByDesc('review.id');
        // $reviews = $transactions->map(function ($transaction) use ($customer_id) {
        //     $like = $transaction->review->likes->where('liker_id', $customer_id)->first();
        //     if ($like) {
        //         $liked = 1;
        //         $source_id = $like->id;
        //     } else {
        //         $liked = 0;
        //         $source_id = 0;
        //     }
        //     $transaction->review->previous_like = $liked;
        //     $transaction->review->previous_like_id = $source_id;
        //     return $transaction->review;
        // });

        $reviews = (new \App\Http\Controllers\Review\functionController())->getReviews($branch_id, $customer_id, LikerType::customer);
        $reviews = $reviews->where('heading', '!=', 'n/a')->where('body', '!=', 'n/a');
        $reviews = $reviews->slice($position)->take($review_loadmore);

        // return Response::json($reviews);

        if (($position + $review_loadmore) >= $total_reviews) {
            $loadmore = 0;
        } else {
            $loadmore = 1;
        }

        $row = count($reviews);
        foreach ($reviews as $key => $review) {
            //REVIEW SECTION
            $output .= '<div class="whitebox-inner-box-inner" id="review-id-'.$review['id'].'">';
            $output .= '<div class="row">';
            $output .= '<div class="col-md-2 col-sm-2 col-xs-3">';
            //User profile picture
            $output .= '<div class="comment-avatar center">';
            //$output .= "<a href=\"".url('user-profile/'.$reviews[$i]['customer_username'])."\" target=\"_blank\">";
            $output .= '<a>';
            $output .= '<img src="'.asset($review['customerInfo']['customer_profile_image']).
                '" class="img-circle img-40 primary-border lazyload" alt="Royalty user-pic">';
            $output .= '</a>';
            //User Review box
            $output .= '<p class="comment-name reviewer-name mt">'.$review['customerInfo']['customer_full_name'].'</p>';

            $output .= '<p>';
            $review_number = (new functionController)->reviewNumber($review['customer_id']);
            $output .= "<i class=\"bx bx-edit user-total-reviews\"></i><span> $review_number </span></a>";
            $like_number = (new functionController)->likeNumber($review['customer_id']);
            $output .= '<i class="bx bx-like likes_of_user_'.$review['customer_id']."\"><span> $like_number </span></i>";
            $output .= '</p>';

            //social media buttons START
            if ($review['heading'] != null && $review['heading'] != 'n/a') {
                $heading = str_replace("'", '', $review['heading']);
                $heading = str_replace('"', '', $heading);
                $heading = trim(preg_replace('/\s+/', ' ', $heading));
            } else {
                $heading = '';
            }

            if ($review['body'] != null && $review['body'] != 'n/a') {
                $body = str_replace("'", '', $review['body']);
                $body = str_replace('"', '', $body);
                $body = trim(preg_replace('/\s+/', ' ', $body));
            } else {
                $body = '';
            }
            $newline = '\n';
            $pretext = 'Review about';
            $partner_name = str_replace("'", "\'", $review['partnerInfo']['partner_name']);
            $review_body = $body;
            $review_head = $heading;
            $enc_review_id = (new functionController)->socialShareEncryption('encrypt', $review['id']);
            $review_url = url('/review/'.$enc_review_id);

            $output .= '</div>';
            $output .= '</div>';
            $output .= '<div class="col-md-10 col-sm-10 col-xs-9">';
            //User Review box
            $output .= '<div class="whitebox">';
            $output .= '<div class="comment-head">';
            // if(Session::has('customer_id') || Session::has('partner_id')) {
            $output .= '<div class="social-buttons">';
            //Twitter share button code
            $output .= "<span onclick=\"window.open('https://twitter.com/intent/tweet?text=' +
			 encodeURIComponent('$pretext $partner_name $newline $newline$review_head $newline$review_body $newline $newline$review_url')); return false;\">";
            $output .= '<a href="#"><i class="bx bxl-twitter" style="padding-right: 5px;"></i></a></span>';

            //Facebook share button code
            $output .= '<span>';
            $review_url = 'https://www.facebook.com/sharer.php?u=http%3A%2F%2Froyaltybd.club%2Freview-share%2F'.$enc_review_id;
            $output .= "<a href=\"$review_url\" target=\"_blank\">";
            $output .= '<i class="bx bxl-facebook-circle" style="padding-right: 5px;"></i>';
            $output .= '</a>';
            $output .= '</span>';

            if (Session::get('customer_id') == $review['customer_id']) {
                $output .= '<p align="middle"><a class="btn btn-danger btn-xs" href="'.url('/reviewDelete/'.$review['id'])."\"
                             onclick=\"return confirm('Are you sure you want to delete this review?')\"><i class=\"delete-icon\" aria-hidden=\"true\"></i></a></p>";
            }

            $output .= '</div>';
            //social media buttons END
            // }

            $output .= '</div>';

            $output .= '<div class="comment-content">';
            $output .= '<div class="review-star">';
            if ($review['rating'] == 1) {
                $output .= '<div class="reviewer-star-rating-div"><i class="bx bxs-star yellow"></i><i class="bx bx-star"></i>
                        <i class="bx bx-star"></i><i class="bx bx-star"></i><i class="bx bx-star"></i></div>';
            } elseif ($review['rating'] == 2) {
                $output .= '<div class="reviewer-star-rating-div"><i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i>
                        <i class="bx bx-star"></i><i class="bx bx-star"></i><i class="bx bx-star"></i></div>';
            } elseif ($review['rating'] == 3) {
                $output .= '<div class="reviewer-star-rating-div"><i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i>
                        <i class="bx bxs-star yellow"></i><i class="bx bx-star"></i><i class="bx bx-star"></i></div>';
            } elseif ($review['rating'] == 4) {
                $output .= '<div class="reviewer-star-rating-div"><i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i>
                        <i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i><i class="bx bx-star"></i></div>';
            } else {
                $output .= '<div class="reviewer-star-rating-div"><i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i>
                        <i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i></div>';
            }
            $output .= '</div>';

            $posted_on = date('Y-M-d H:i:s', strtotime($review['posted_on']));
            $created = \Carbon\Carbon::createFromTimeStamp(strtotime($posted_on));

            $output .= '<span class="review-post-date">'.$created->diffForHumans().'</span>';
            $output .= '<p class="review-head bold">'.$review_head.'</p>';
            $output .= '<p class="review-description">'.$review_body.'</p>';

            //Like option starts
            $output .= '<div class="like-button" style="display: inline-flex;">';
            if (count($review['likes']) > 0) {
                $onclick = 'onclick="getReviewLikerList('.$review['id'].')"';
            } else {
                $onclick = '';
            }
            if (Session::has('customer_id') && $review['previous_like'] == 1) {
                $like_text = count($review['likes']) > 1 ? ' likes' : ' like';
                $output .= '<div class="like-content" title="Like">';
                $output .= '<button class="btn-like unlike-review"  id="principalSelect-'.$review['id'].'" value="'.$review['id'].'" data-source="'.$review['previous_like_id'].'"><i class="love-f-icon"></i></button>';
                $output .= '</div>';
                $output .= '<p class="likes-on-review" '.$onclick.'id="likes_of_review_'.$review['id'].'">'.
                    count($review['likes']).$like_text.'</p>';
            } elseif (Session::has('customer_id') && $review['previous_like'] == 0 && Session::get('customer_id') != $review['customer_id']) {
                $like_text = count($review['likes']) > 1 ? ' likes' : ' like';
                $output .= '<div class="like-content">';
                $output .= '<button class="btn-like like-review" id="principalSelect-'.$review['id'].'" 
                    value="'.$review['id'].'"  data-source="'.$review['previous_like_id'].'">
                            <i class="love-e-icon"></i></button>';
                $output .= '</div>';
                $output .= '<p class="likes-on-review" '.$onclick.' id="likes_of_review_'.$review['id'].'">'.
                    count($review['likes']).$like_text.'</p>';
            } elseif (session('customer_id') && session('customer_id') == $review['customer_id']) {
                $like_text = count($review['likes']) > 1 ? ' likes' : ' like';
                //$output .= "<div class=\"like-content\"  title=\"You can not like your own review\">";
                //$output .= "<button class=\"btn-like\"><i class=\"love-e-icon\" aria-hidden=\"true\"></i></button>";
                //$output .= "</div>";
                $output .= '<p class="likes-on-review" '.$onclick.' id="likes_of_review_'.$review['id'].'">'.
                    count($review['likes']).$like_text.'</p>';
            } elseif (Session::has('partner_id') && $review['liked'] == 0 && Session::get('partner_id') == $partner_id) {
                $like_text = $review['total_likes_of_a_review'] > 1 ? ' likes' : ' like';
                $output .= '<div class="like-content">';
                $output .= '<button class="btn-like like-review" id="principalSelect-'.$review['id'].'" value="'.$review['id'].'"  data-source="'.$review['source_id'].'">
                            <i class="love-e-icon" aria-hidden="true"></i></button>';
                $output .= '</div>';
                $output .= '<p class="likes-on-review" '.$onclick.' id="likes_of_review_'.$review['id'].'">'.
                    count($review['likes']).$like_text.'</p>';
            } else {
                $like_text = count($review['likes']) > 1 ? ' likes' : ' like';
                $output .= '<div class="like-content">';
                $output .= '<button class="btn-like" data-toggle="modal" data-target="#nonClickableLike"><i class="love-e-icon" aria-hidden="true"> </i></button>';
                $output .= '</div>';
                $output .= '<p class="likes-on-review" '.$onclick.' id="likes_of_review_'.$review['id'].'">'.count($review['likes']).$like_text.'</p>';
            }

            $output .= '</div>';
            //Like option ends
            $output .= '<p class="review-liability">';
            $output .= 'This review is the subjective opinion of a Royalty member and not of Royalty.';
            $output .= '</p>';
            $output .= '</div>';
            $output .= '</div>';
            $output .= '</div>';
            $output .= '</div>';

            //partner reply
            if (isset($review['comments'][0])) {
                $output .= '<div class="row m-0 pull-right">';
                $output .= '<div class="col-md-10 col-md-offset-2 col-sm-11 col-sm-offset-1 col-xs-11 col-xs-offset-1">';
                //Partner reply box
                $output .= '<div class="whitebox comment-box-partner">';
                $output .= '<div class="comment-content comment-content-partner">';
                $output .= '<p class="comment-name partner-response">'.$review['partnerInfo']['partner_name'].
                    '<span style="font-weight: 100;"> responded to this review</span></p>';

                $posted_on = date('Y-M-d H:i:s', strtotime($review['comments'][0]['posted_on']));
                $created = \Carbon\Carbon::createFromTimeStamp(strtotime($posted_on));

                $output .= '<p class="partner-reply-date">'.$created->diffForHumans().'</p>';
                $output .= '<p class="partner-reply">'.$review['comments'][0]['comment'].'</p>';
                $output .= '</div>';
                $output .= '</div>';
                $output .= '</div>';
                $output .= '</div>';
            }

            if (session('partner_id') && $review['partner_account_id'] == session('partner_id') && empty($review['comments'][0]['comment'])) {
                $output .= '<div class="row">';
                $output .= '<div class="">';
                //Partner reply box
                $output .= '<div class="whitebox partner-color" style="display: grid;">';
                $output .= '<form action="'.url('replyReview/'.$review['id']).'" method="post" style="float: left;padding: 15px 0">';
                $output .= csrf_field();
                $output .= '<div class="form-group">';
                $output .= '<textarea name="reply" id="review'.$key.'" cols="78" rows="4" placeholder="Your reply goes here..." required class="form-control" maxlength="500" 
                    onkeyup="replyChars('.$key.');"></textarea>';
                $output .= '</div>';
                $output .= '<p align="right" style="font-size: small; margin-top: -10px">
                                    <span id="charNum'.$key.'">0/500</span>
                                 </p>';
                $output .= '<input type="hidden" name="customerID" value="'.$review['customer_id'].'">';
                $output .= '<input type="hidden" name="review_id" value="'.$review['id'].'">';
                $output .= '<br>';
                $output .= '<button type="submit" class="btn btn-primary"  style="float: right;margin-right: unset">Reply</button>';
                $output .= '</form>';
                $output .= '</div>';
                $output .= '</div>';
                $output .= '</div>';
                $output .= '</div>';
                $output .= '</div>';
            }
            $output .= '</div>';
        }
        $output = $output == '' ? 'error' : $output;
        $result['output'] = $output;
        $result['status'] = $loadmore;

        return Response::json($result);
    }

    //function to sort sales analytics
    public function sortSalesAnalytics(Request $request)
    {
        $year = $request->get('salesAnalyticsByYear');
        $month = $request->get('salesAnalyticsByMonth');
        $branch_id = $request->get('salesAnalyticsByBranch');

        if (! ($year && $month && $branch_id)) {
            return redirect()->back()->with('try_again', 'Year, Month And Branch Must Be Set! Please try again!');
        }

        if ($month == '01') {
            $month_name = 'January';
        } elseif ($month == '02') {
            $month_name = 'February';
        } elseif ($month == '03') {
            $month_name = 'March';
        } elseif ($month == '04') {
            $month_name = 'April';
        } elseif ($month == '05') {
            $month_name = 'May';
        } elseif ($month == '06') {
            $month_name = 'June';
        } elseif ($month == '07') {
            $month_name = 'July';
        } elseif ($month == '08') {
            $month_name = 'August';
        } elseif ($month == '09') {
            $month_name = 'September';
        } elseif ($month == '10') {
            $month_name = 'October';
        } elseif ($month == '11') {
            $month_name = 'November';
        } elseif ($month == '12') {
            $month_name = 'December';
        }

        $sortedSales = new Lavacharts();
        $data = $sortedSales->DataTable();
        $data->addStringColumn('Days')
            ->addNumberColumn('Sales');
        //set every day's sales statistics value
        for ($i = 1; $i <= 31; $i++) {
            $j = $i < 10 ? 0 : '';
            //get every day's total sales for this branch only
            $total_spent = TransactionTable::where('branch_id', $branch_id)->where('posted_on', 'like', $year.'-'.$month.'-'.$j.$i.'%')
                ->sum('amount_spent');

            $data->addRow([
                $i, $total_spent,
            ]);
        }
        $sortedSales->AreaChart('Sales', $data, [
            'title' => 'Daily sales analysis of '.$month_name.' \n(Amount of money in BDT against number of days)',
            'legend' => [
                'position' => 'out',
            ],
        ]);

        $partnerId = Session::get('partner_id');
        $partnerInfo = PartnerAccount::with('branches')->where('partner_account_id', $partnerId)
            ->first();

        return view('partner-admin.production.index', compact('sortedSales', 'year', 'month', 'branch_id', 'partnerInfo'));
    }

    //function to sort sales analytics json
    public function sortSalesAnalyticsJson(Request $request)
    {
        $year = $request->get('salesAnalyticsByYear');
        $month = $request->get('salesAnalyticsByMonth');
        $branch_id = $request->get('salesAnalyticsByBranch');

        if (! ($year && $month && $branch_id)) {
            return response()->json('missing_params');
        }

        if ($month == '01') {
            $month_name = 'January';
        } elseif ($month == '02') {
            $month_name = 'February';
        } elseif ($month == '03') {
            $month_name = 'March';
        } elseif ($month == '04') {
            $month_name = 'April';
        } elseif ($month == '05') {
            $month_name = 'May';
        } elseif ($month == '06') {
            $month_name = 'June';
        } elseif ($month == '07') {
            $month_name = 'July';
        } elseif ($month == '08') {
            $month_name = 'August';
        } elseif ($month == '09') {
            $month_name = 'September';
        } elseif ($month == '10') {
            $month_name = 'October';
        } elseif ($month == '11') {
            $month_name = 'November';
        } elseif ($month == '12') {
            $month_name = 'December';
        }

        $sortedSales = new Lavacharts();
        $data = $sortedSales->DataTable();
        $data->addStringColumn('Days')
            ->addNumberColumn('Sales');
        //set every day's sales statistics value
        for ($i = 1; $i <= 31; $i++) {
            $j = $i < 10 ? 0 : '';
            //get every day's total sales for this branch only
            $total_spent = TransactionTable::where('branch_id', $branch_id)->where('posted_on', 'like', $year.'-'.$month.'-'.$j.$i.'%')
                ->sum('amount_spent');

            $data->addRow([
                $i, $total_spent,
            ]);
        }

        return $data->toJson();
    }

    //function to get sorted transaction statistics values
    public function sortTransactionAnalytics(Request $request)
    {
        $year = $request->get('transactionAnalyticsByYear');
        $branch_id = $request->get('transactionAnalyticsByBranch');

        if (! ($year && $branch_id)) {
            return redirect()->back()->with('try_again', 'Year And Branch Must Be Set! Please try again!');
        }

        $sortedTransactions = new Lavacharts();
        $data = $sortedTransactions->DataTable();
        //get all transactions of this partner
        $partnerId = Session::get('partner_id');
        $partnerInfo = PartnerAccount::with('branches')->where('partner_account_id', $partnerId)
            ->first();
        //get all transactions of this partner for this branch only
        $transactions = TransactionTable::where('branch_id', $branch_id)->where('posted_on', 'like', $year.'%')->get();

        $MONTHS['January'] = $MONTHS['February'] = $MONTHS['March'] = $MONTHS['April'] = $MONTHS['May'] = $MONTHS['June'] =
        $MONTHS['July'] = $MONTHS['August'] = $MONTHS['September'] = $MONTHS['October'] = $MONTHS['November'] = $MONTHS['December'] = 0;
        if ($transactions) {
            foreach ($transactions as $transaction) {
                $time = strtotime($transaction['posted_on']);
                $month = date('F', $time);
                $MONTHS[$month] = $MONTHS[$month] + 1;
            }
            $MONTHS = json_decode(json_encode($MONTHS), true);
        }

        $data->addStringColumn('Month')
            ->addNumberColumn('Transactions')
            ->addRow(['Jan', $MONTHS['January']])
            ->addRow(['Feb', $MONTHS['February']])
            ->addRow(['Mar', $MONTHS['March']])
            ->addRow(['Apr', $MONTHS['April']])
            ->addRow(['May', $MONTHS['May']])
            ->addRow(['Jun', $MONTHS['June']])
            ->addRow(['Jul', $MONTHS['July']])
            ->addRow(['Aug', $MONTHS['August']])
            ->addRow(['Sept', $MONTHS['September']])
            ->addRow(['Oct', $MONTHS['October']])
            ->addRow(['Nov', $MONTHS['November']])
            ->addRow(['Dec', $MONTHS['December']]);

        $sortedTransactions->AreaChart('Transaction', $data, [
            'title' => 'Monthly transaction analysis of '.$year.' \n(Number of customers against Months)',
            'legend' => [
                'position' => 'out',
            ],
        ]);

        return view('partner-admin.production.index', compact('sortedTransactions', 'year', 'month', 'branch_id', 'partnerInfo'));
    }

    //function to get sorted transaction statistics values json
    public function sortTransactionAnalyticsJson(Request $request)
    {
        $year = $request->get('transactionAnalyticsByYear');
        $branch_id = $request->get('transactionAnalyticsByBranch');

        if (! ($year && $branch_id)) {
            return response()->json('missing_params');
        }

        $sortedTransactions = new Lavacharts();
        $data = $sortedTransactions->DataTable();
        //get all transactions of this partner
        $partnerId = Session::get('partner_id');
        $partnerInfo = PartnerAccount::with('branches')->where('partner_account_id', $partnerId)
            ->first();
        //get all transactions of this partner for this branch only
        $transactions = TransactionTable::where('branch_id', $branch_id)->where('posted_on', 'like', $year.'%')->get();

        $MONTHS['January'] = $MONTHS['February'] = $MONTHS['March'] = $MONTHS['April'] = $MONTHS['May'] = $MONTHS['June'] =
        $MONTHS['July'] = $MONTHS['August'] = $MONTHS['September'] = $MONTHS['October'] = $MONTHS['November'] = $MONTHS['December'] = 0;
        if ($transactions) {
            foreach ($transactions as $transaction) {
                $time = strtotime($transaction['posted_on']);
                $month = date('F', $time);
                $MONTHS[$month] = $MONTHS[$month] + 1;
            }
            $MONTHS = json_decode(json_encode($MONTHS), true);
        }

        $data->addStringColumn('Month')
            ->addNumberColumn('Transactions')
            ->addRow(['Jan', $MONTHS['January']])
            ->addRow(['Feb', $MONTHS['February']])
            ->addRow(['Mar', $MONTHS['March']])
            ->addRow(['Apr', $MONTHS['April']])
            ->addRow(['May', $MONTHS['May']])
            ->addRow(['Jun', $MONTHS['June']])
            ->addRow(['Jul', $MONTHS['July']])
            ->addRow(['Aug', $MONTHS['August']])
            ->addRow(['Sept', $MONTHS['September']])
            ->addRow(['Oct', $MONTHS['October']])
            ->addRow(['Nov', $MONTHS['November']])
            ->addRow(['Dec', $MONTHS['December']]);

        return $data->toJson();
    }

    //function to sort gender analytics
    public function sortGenderAnalytics(Request $request)
    {
        $year = $request->get('genderAnalyticsByYear');
        $month = $request->get('genderAnalyticsByMonth');
        $branch_id = $request->get('genderAnalyticsByBranch');

        if (! ($year && $branch_id)) {
            return redirect()->back()->with('try_again', 'Year And Branch Must Be Set! Please try again!');
        }

        $sortedGender = new Lavacharts();
        $data = $sortedGender->DataTable();

        $partnerId = Session::get('partner_id');
        $partnerInfo = PartnerAccount::with('branches')->where('partner_account_id', $partnerId)
            ->first();
        if ($year != null && $month == null) {
            //get all transactions of this partner
            // $transactions = DB::table('transaction_table as tt')
            //     ->join('partner_branch as pb','pb.id','=','tt.branch_id')
            //     ->select('tt.customer_id')
            //     ->where('pb.partner_account_id', Session::get('partner_id'))
            //     ->where('tt.posted_on', 'like', $year . '%')
            //     ->get();

            // $transactions = TransactionTable::whereHas('branch', function ($query) use ($partnerId){
            //     $query->where('partner_account_id', $partnerId);
            // })->select('customer_id')->where('posted_on', 'like', $year . '%')->get();

            //get all transactions of this partner for this branch only
            $transactions = TransactionTable::where('branch_id', $branch_id)->select('customer_id')->where('posted_on', 'like', $year.'%')->get();
        } else {
            //get all transactions of this partner
            // $transactions = DB::table('transaction_table as tt')
            //     ->join('partner_branch as pb','pb.id','=','tt.branch_id')
            //     ->select('tt.customer_id')
            //     ->where('pb.partner_account_id', Session::get('partner_id'))
            //     ->where('tt.posted_on', 'like', $year . '-' . $month . '%')
            //     ->get();

            // $transactions = TransactionTable::whereHas('branch', function ($query) use ($partnerId){
            //     $query->where('partner_account_id', $partnerId);
            // })->select('customer_id')->where('posted_on', 'like', $year . '-' . $month . '%')->get();

            //get all transactions of this partner for this branch only
            $transactions = TransactionTable::where('branch_id', $branch_id)->select('customer_id')->where('posted_on', 'like', $year.'-'.$month.'%')->get();
        }

        //initialize gender counter
        $m = $f = 0;
        foreach ($transactions as $transaction) {
            $customer_info = CustomerInfo::select('customer_gender')
                ->where('customer_id', $transaction['customer_id'])
                ->get();

            $customer_info[0]['customer_gender'] == 'male' ? $m++ : $f++;
        }
        $data->addStringColumn('Reasons')
            ->addNumberColumn('Percent')
            ->addRow(['Male', $m])
            ->addRow(['Female', $f]);

        if ($m == 0 && $f == 0) {
            $sortedGender->PieChart('visitPartner', $data, [
                'title' => 'No transaction has been done yet',
                'is3D' => true,
                'slices' => [
                    ['offset' => 0], //0.2
                    ['offset' => 0], //0.25
                    ['offset' => 0], //0.3
                ],
            ]);
        } else {
            $sortedGender->PieChart('visitPartner', $data, [
                'title' => 'Gender Demographics',
                'is3D' => true,
                'slices' => [
                    ['offset' => 0], //0.2
                    ['offset' => 0], //0.25
                    ['offset' => 0], //0.3
                ],
            ]);
        }

        return view('partner-admin.production.index', compact('sortedGender', 'year', 'month', 'branch_id', 'partnerInfo'));
    }

    //function to sort gender analytics json
    public function sortGenderAnalyticsJson(Request $request)
    {
        $year = $request->get('genderAnalyticsByYear');
        $month = $request->get('genderAnalyticsByMonth');
        $branch_id = $request->get('genderAnalyticsByBranch');

        if (! ($year && $branch_id)) {
            return response()->json('missing_params');
        }

        $sortedGender = new Lavacharts();
        $data = $sortedGender->DataTable();

        $partnerId = Session::get('partner_id');
        $partnerInfo = PartnerAccount::with('branches')->where('partner_account_id', $partnerId)
            ->first();
        if ($year != null && $month == null) {

            //get all transactions of this partner for this branch only
            $transactions = TransactionTable::where('branch_id', $branch_id)->select('customer_id')->where('posted_on', 'like', $year.'%')->get();
        } else {

            //get all transactions of this partner for this branch only
            $transactions = TransactionTable::where('branch_id', $branch_id)->select('customer_id')->where('posted_on', 'like', $year.'-'.$month.'%')->get();
        }

        //initialize gender counter
        $m = $f = 0;
        foreach ($transactions as $transaction) {
            $customer_info = CustomerInfo::select('customer_gender')
                ->where('customer_id', $transaction['customer_id'])
                ->get();

            $customer_info[0]['customer_gender'] == 'male' ? $m++ : $f++;
        }
        $data->addStringColumn('Reasons')
            ->addNumberColumn('Percent')
            ->addRow(['Male', $m])
            ->addRow(['Female', $f]);

        return $data->toJson();
    }

    //function to sort age & gender
    public function sortAgeGenderAnalytics(Request $request)
    {
        $year = $request->get('ageGenderAnalyticsByYear');
        $month = $request->get('ageGenderAnalyticsByMonth');
        $branch_id = $request->get('ageGenderAnalyticsByBranch');
        $sortedAgeGender = new Lavacharts();
        $data = $sortedAgeGender->DataTable();

        if (! ($year && $branch_id)) {
            return redirect()->back()->with('try_again', 'Year And Branch Must Be Set! Please try again!');
        }

        $partnerId = Session::get('partner_id');
        $partnerInfo = PartnerAccount::with('branches')->where('partner_account_id', $partnerId)
            ->first();

        //get all transactions of this partner for this branch only
        if ($year != null && $month == null) { //when only year selected
            // $transactions = DB::table('transaction_table as tt')
            //     ->join('partner_branch as pb','pb.id','=','tt.branch_id')
            //     ->select('tt.customer_id')
            //     ->where('pb.partner_account_id', Session::get('partner_id'))
            //     ->where('tt.posted_on', 'like', $year . '%')
            //     ->groupby('tt.customer_id')
            //     ->get();

            // $transactions = TransactionTable::whereHas('branch', function ($query) use ($partnerId){
            //     $query->where('partner_account_id', $partnerId);
            // })->select('customer_id')->where('posted_on', 'like', $year . '%')->groupby('customer_id')->get();

            $transactions = TransactionTable::where('branch_id', $branch_id)->select('customer_id')->where('posted_on', 'like', $year.'%')->groupby('customer_id')->get();

            // $totalTransactions = DB::table('transaction_table as tt')
            //     ->join('partner_branch as pb','pb.id','=','tt.branch_id')
            //     ->where('tt.posted_on', 'like', $year . '%')
            //     ->where('pb.partner_account_id', Session::get('partner_id'))
            //     ->count();

            // $totalTransactions = TransactionTable::whereHas('branch', function ($query) use ($partnerId){
            //     $query->where('partner_account_id', $partnerId);
            // })->where('posted_on', 'like', $year . '%')->count();

            $totalTransactions = TransactionTable::where('branch_id', $branch_id)->where('posted_on', 'like', $year.'%')->count();

            $users = [];
            $i = 0;
            foreach ($transactions as $transaction) {
                //get dob & gender of each customer
                $info = CustomerInfo::select('customer_dob', 'customer_gender')
                    ->where('customer_id', $transaction['customer_id'])
                    ->get();
                $info = json_decode(json_encode($info), true);
                $info = $info[0];
                //get how many times customer visited the partner
                // $visited = DB::table('transaction_table as tt')
                //     ->join('partner_branch as pb','pb.id','=','tt.branch_id')
                //     ->where('tt.posted_on', 'like', $year . '%')
                //     ->where('pb.partner_account_id', Session::get('partner_id'))
                //     ->where('tt.customer_id', $transaction['customer_id'])
                //     ->count();

                // $visited = TransactionTable::whereHas('branch', function ($query) use ($partnerId){
                //                 $query->where('partner_account_id', $partnerId);
                //             })->where('posted_on', 'like', $year . '%')->where('customer_id', $transaction['customer_id'])->count();

                //get how many times customer visited the partner for this branch only
                $visited = TransactionTable::where('branch_id', $branch_id)->where('posted_on', 'like', $year.'%')->where('customer_id', $transaction['customer_id'])->count();

                $age = (new functionController)->getAge($info['customer_dob'], date('Y-m-d'));
                $users[$transaction['customer_id']][$i]['age'] = $age;
                $users[$transaction['customer_id']][$i]['gender'] = $info['customer_gender'];
                $users[$transaction['customer_id']][$i]['total_visits'] = $visited;
                $i++;
            }
        } else { //when year & month are selected or nothing is selected
            // $transactions = DB::table('transaction_table as tt')
            //     ->join('partner_branch as pb','pb.id','=','tt.branch_id')
            //     ->select('tt.customer_id')
            //     ->where('pb.partner_account_id', Session::get('partner_id'))
            //     ->where('tt.posted_on', 'like', $year . '-' . $month . '%')
            //     ->groupby('tt.customer_id')
            //     ->get();

            // $transactions = TransactionTable::whereHas('branch', function ($query) use ($partnerId){
            //     $query->where('partner_account_id', $partnerId);
            // })->select('customer_id')->where('posted_on', 'like', $year . '-' . $month . '%')->groupby('customer_id')->get();

            $transactions = TransactionTable::where('branch_id', $branch_id)->select('customer_id')->where('posted_on', 'like', $year.'-'.$month.'%')->groupby('customer_id')->get();

            // $totalTransactions = DB::table('transaction_table as tt')
            //     ->join('partner_branch as pb','pb.id','=','tt.branch_id')
            //     ->where('tt.posted_on', 'like', $year . '-' . $month . '%')
            //     ->where('pb.partner_account_id', Session::get('partner_id'))
            //     ->count();

            // $totalTransactions = TransactionTable::whereHas('branch', function ($query) use ($partnerId){
            //     $query->where('partner_account_id', $partnerId);
            // })->where('posted_on', 'like', $year . '-' . $month . '%')->count();

            $totalTransactions = TransactionTable::where('branch_id', $branch_id)->where('posted_on', 'like', $year.'-'.$month.'%')->count();

            $users = [];
            $i = 0;
            foreach ($transactions as $transaction) {
                //get dob & gender of each customer
                $info = CustomerInfo::select('customer_dob', 'customer_gender')
                    ->where('customer_id', $transaction['customer_id'])
                    ->get();
                $info = json_decode(json_encode($info), true);
                $info = $info[0];
                //get how many times customer visited the partner
                // $visited = DB::table('transaction_table as tt')
                //     ->join('partner_branch as pb','pb.id','=','tt.branch_id')
                //     ->where('tt.posted_on', 'like', $year . '-' . $month . '%')
                //     ->where('pb.partner_account_id', Session::get('partner_id'))
                //     ->where('tt.customer_id', $transaction['customer_id'])
                //     ->count();

                // $visited = TransactionTable::whereHas('branch', function ($query) use ($partnerId){
                //                 $query->where('partner_account_id', $partnerId);
                //             })->where('posted_on', 'like', $year . '-' . $month . '%')->where('customer_id', $transaction['customer_id'])->count();

                //get how many times customer visited the partner for this branch only
                $visited = TransactionTable::where('branch_id', $branch_id)->where('posted_on', 'like', $year.'-'.$month.'%')->where('customer_id', $transaction['customer_id'])->count();

                $age = (new functionController)->getAge($info['customer_dob'], date('Y-m-d'));
                $users[$transaction['customer_id']][$i]['age'] = $age;
                $users[$transaction['customer_id']][$i]['gender'] = $info['customer_gender'];
                $users[$transaction['customer_id']][$i]['total_visits'] = $visited;
                $i++;
            }
        }

        //get visiting info of male & female according to age
        //initialize some variables
        $male10 = $female10 = $male20 = $female20 = $male30 = $female30 = $male40 = $female40 = $male50 = $female50 = $male50plus = $female50plus = 0;
        $result['0-10']['male'] = $result['0-10']['female']
            = $result['10-20']['male'] = $result['10-20']['female']
            = $result['20-30']['male'] = $result['20-30']['female']
            = $result['30-40']['male'] = $result['30-40']['female']
            = $result['40-50']['male'] = $result['40-50']['female']
            = $result['50+']['male'] = $result['50+']['female'] = 0;
        //sum total visits of each customer
        foreach ($users as $key => $value) {
            foreach ($value as $user) {
                if (0 <= $user['age'] && $user['age'] <= 10) {
                    $user['gender'] == 'male' ? $male10 += $user['total_visits'] : $female10 += $user['total_visits'];
                } elseif (10 < $user['age'] && $user['age'] < 20) {
                    $user['gender'] == 'male' ? $male20 += $user['total_visits'] : $female20 += $user['total_visits'];
                } elseif (20 < $user['age'] && $user['age'] < 30) {
                    $user['gender'] == 'male' ? $male30 += $user['total_visits'] : $female30 += $user['total_visits'];
                } elseif (30 < $user['age'] && $user['age'] < 40) {
                    $user['gender'] == 'male' ? $male40 += $user['total_visits'] : $female40 += $user['total_visits'];
                } elseif (40 < $user['age'] && $user['age'] < 50) {
                    $user['gender'] == 'male' ? $male50 += $user['total_visits'] : $female50 += $user['total_visits'];
                } else {
                    $user['gender'] == 'male' ? $male50plus += $user['total_visits'] : $female50plus += $user['total_visits'];
                }
            }
        }
        //set male & female percentage according to age
        if ($male10 != 0) {
            $result['0-10']['male'] = round(($male10 / $totalTransactions) * 100);
        }
        if ($female10 != 0) {
            $result['0-10']['female'] = round(($female10 / $totalTransactions) * 100);
        }

        if ($male20 != 0) {
            $result['10-20']['male'] = round(($male20 / $totalTransactions) * 100);
        }
        if ($female20 != 0) {
            $result['10-20']['female'] = round(($female20 / $totalTransactions) * 100);
        }

        if ($male30 != 0) {
            $result['20-30']['male'] = round(($male30 / $totalTransactions) * 100);
        }
        if ($female30 != 0) {
            $result['20-30']['female'] = round(($female30 / $totalTransactions) * 100);
        }

        if ($male40 != 0) {
            $result['30-40']['male'] = round(($male40 / $totalTransactions) * 100);
        }
        if ($female40 != 0) {
            $result['30-40']['female'] = round(($female40 / $totalTransactions) * 100);
        }

        if ($male50 != 0) {
            $result['40-50']['male'] = round(($male50 / $totalTransactions) * 100);
        }
        if ($female50 != 0) {
            $result['40-50']['female'] = round(($female50 / $totalTransactions) * 100);
        }

        if ($male50plus != 0) {
            $result['50+']['male'] = round(($male50plus / $totalTransactions) * 100);
        }
        if ($female50plus != 0) {
            $result['50+']['female'] = round(($female50plus / $totalTransactions) * 100);
        }

        $data->addStringColumn('Age')
            ->addNumberColumn('Male')
            ->addNumberColumn('Female')
            ->setDateTimeFormat('Y')
            ->addRow(['0-10', $result['0-10']['male'], $result['0-10']['female']])
            ->addRow(['10-20', $result['10-20']['male'], $result['10-20']['female']])
            ->addRow(['20-30', $result['20-30']['male'], $result['20-30']['female']])
            ->addRow(['30-40', $result['30-40']['male'], $result['30-40']['female']])
            ->addRow(['40-50', $result['40-50']['male'], $result['40-50']['female']])
            ->addRow(['50+', $result['50+']['male'], $result['50+']['female']]);

        $sortedAgeGender->ColumnChart('ageGender', $data, [
            'title' => 'Age & gender statistics (Percentage of Gender ratio against Age)',
            'titleTextStyle' => [
                'fontSize' => 14,
            ],
            'legend' => [
                'position' => 'out',
            ],
        ]);

        return view('partner-admin.production.index', compact('sortedAgeGender', 'year', 'month', 'branch_id', 'partnerInfo'));
    }

    //function to sort age & gender json
    public function sortAgeGenderAnalyticsJson(Request $request)
    {
        $year = $request->get('ageGenderAnalyticsByYear');
        $month = $request->get('ageGenderAnalyticsByMonth');
        $branch_id = $request->get('ageGenderAnalyticsByBranch');
        $sortedAgeGender = new Lavacharts();
        $data = $sortedAgeGender->DataTable();

        if (! ($year && $branch_id)) {
            return response()->json('missing_params');
        }

        $partnerId = Session::get('partner_id');
        $partnerInfo = PartnerAccount::with('branches')->where('partner_account_id', $partnerId)
            ->first();

        //get all transactions of this partner for this branch only
        if ($year != null && $month == null) { //when only year selected
            $transactions = TransactionTable::where('branch_id', $branch_id)->select('customer_id')->where('posted_on', 'like', $year.'%')->groupby('customer_id')->get();

            $totalTransactions = TransactionTable::where('branch_id', $branch_id)->where('posted_on', 'like', $year.'%')->count();

            $users = [];
            $i = 0;
            foreach ($transactions as $transaction) {
                //get dob & gender of each customer
                $info = CustomerInfo::select('customer_dob', 'customer_gender')
                    ->where('customer_id', $transaction['customer_id'])
                    ->get();
                $info = json_decode(json_encode($info), true);
                $info = $info[0];

                //get how many times customer visited the partner for this branch only
                $visited = TransactionTable::where('branch_id', $branch_id)->where('posted_on', 'like', $year.'%')->where('customer_id', $transaction['customer_id'])->count();

                $age = (new functionController)->getAge($info['customer_dob'], date('Y-m-d'));
                $users[$transaction['customer_id']][$i]['age'] = $age;
                $users[$transaction['customer_id']][$i]['gender'] = $info['customer_gender'];
                $users[$transaction['customer_id']][$i]['total_visits'] = $visited;
                $i++;
            }
        } else { //when year & month are selected or nothing is selected

            $transactions = TransactionTable::where('branch_id', $branch_id)->select('customer_id')->where('posted_on', 'like', $year.'-'.$month.'%')->groupby('customer_id')->get();

            $totalTransactions = TransactionTable::where('branch_id', $branch_id)->where('posted_on', 'like', $year.'-'.$month.'%')->count();

            $users = [];
            $i = 0;
            foreach ($transactions as $transaction) {
                //get dob & gender of each customer
                $info = CustomerInfo::select('customer_dob', 'customer_gender')
                    ->where('customer_id', $transaction['customer_id'])
                    ->get();
                $info = json_decode(json_encode($info), true);
                $info = $info[0];

                //get how many times customer visited the partner for this branch only
                $visited = TransactionTable::where('branch_id', $branch_id)->where('posted_on', 'like', $year.'-'.$month.'%')->where('customer_id', $transaction['customer_id'])->count();

                $age = (new functionController)->getAge($info['customer_dob'], date('Y-m-d'));
                $users[$transaction['customer_id']][$i]['age'] = $age;
                $users[$transaction['customer_id']][$i]['gender'] = $info['customer_gender'];
                $users[$transaction['customer_id']][$i]['total_visits'] = $visited;
                $i++;
            }
        }

        //get visiting info of male & female according to age
        //initialize some variables
        $male10 = $female10 = $male20 = $female20 = $male30 = $female30 = $male40 = $female40 = $male50 = $female50 = $male50plus = $female50plus = 0;
        $result['0-10']['male'] = $result['0-10']['female']
            = $result['10-20']['male'] = $result['10-20']['female']
            = $result['20-30']['male'] = $result['20-30']['female']
            = $result['30-40']['male'] = $result['30-40']['female']
            = $result['40-50']['male'] = $result['40-50']['female']
            = $result['50+']['male'] = $result['50+']['female'] = 0;
        //sum total visits of each customer
        foreach ($users as $key => $value) {
            foreach ($value as $user) {
                if (0 <= $user['age'] && $user['age'] <= 10) {
                    $user['gender'] == 'male' ? $male10 += $user['total_visits'] : $female10 += $user['total_visits'];
                } elseif (10 < $user['age'] && $user['age'] < 20) {
                    $user['gender'] == 'male' ? $male20 += $user['total_visits'] : $female20 += $user['total_visits'];
                } elseif (20 < $user['age'] && $user['age'] < 30) {
                    $user['gender'] == 'male' ? $male30 += $user['total_visits'] : $female30 += $user['total_visits'];
                } elseif (30 < $user['age'] && $user['age'] < 40) {
                    $user['gender'] == 'male' ? $male40 += $user['total_visits'] : $female40 += $user['total_visits'];
                } elseif (40 < $user['age'] && $user['age'] < 50) {
                    $user['gender'] == 'male' ? $male50 += $user['total_visits'] : $female50 += $user['total_visits'];
                } else {
                    $user['gender'] == 'male' ? $male50plus += $user['total_visits'] : $female50plus += $user['total_visits'];
                }
            }
        }
        //set male & female percentage according to age
        if ($male10 != 0) {
            $result['0-10']['male'] = round(($male10 / $totalTransactions) * 100);
        }
        if ($female10 != 0) {
            $result['0-10']['female'] = round(($female10 / $totalTransactions) * 100);
        }

        if ($male20 != 0) {
            $result['10-20']['male'] = round(($male20 / $totalTransactions) * 100);
        }
        if ($female20 != 0) {
            $result['10-20']['female'] = round(($female20 / $totalTransactions) * 100);
        }

        if ($male30 != 0) {
            $result['20-30']['male'] = round(($male30 / $totalTransactions) * 100);
        }
        if ($female30 != 0) {
            $result['20-30']['female'] = round(($female30 / $totalTransactions) * 100);
        }

        if ($male40 != 0) {
            $result['30-40']['male'] = round(($male40 / $totalTransactions) * 100);
        }
        if ($female40 != 0) {
            $result['30-40']['female'] = round(($female40 / $totalTransactions) * 100);
        }

        if ($male50 != 0) {
            $result['40-50']['male'] = round(($male50 / $totalTransactions) * 100);
        }
        if ($female50 != 0) {
            $result['40-50']['female'] = round(($female50 / $totalTransactions) * 100);
        }

        if ($male50plus != 0) {
            $result['50+']['male'] = round(($male50plus / $totalTransactions) * 100);
        }
        if ($female50plus != 0) {
            $result['50+']['female'] = round(($female50plus / $totalTransactions) * 100);
        }

        $data->addStringColumn('Age')
            ->addNumberColumn('Male')
            ->addNumberColumn('Female')
            ->setDateTimeFormat('Y')
            ->addRow(['0-10', $result['0-10']['male'], $result['0-10']['female']])
            ->addRow(['10-20', $result['10-20']['male'], $result['10-20']['female']])
            ->addRow(['20-30', $result['20-30']['male'], $result['20-30']['female']])
            ->addRow(['30-40', $result['30-40']['male'], $result['30-40']['female']])
            ->addRow(['40-50', $result['40-50']['male'], $result['40-50']['female']])
            ->addRow(['50+', $result['50+']['male'], $result['50+']['female']]);

        return $data->toJson();
    }

    //function to partner admin logout
    public function partnerAdminLogout(Request $request)
    {
        $request->session()->forget('partner_admin');
        Auth::logout();
        echo '<script>window.top.close();</script>';
    }
}//controller ends
