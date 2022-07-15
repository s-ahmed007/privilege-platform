<?php

namespace App\Http\Controllers;

use App\Admin;
use App\AdminActivityNotification;
use App\AllAmounts;
use App\BranchOffers;
use App\BranchScanner;
use App\BranchUserNotification;
use App\CardDelivery;
use App\CardPrice;
use App\CardPromoCodes;
use App\Categories;
use App\CustomerAccount;
use App\CustomerInfo;
use App\CustomerLoginSession;
use App\CustomerNotification;
use App\Events\merchant_notification;
use App\Events\offer_availed;
use App\Events\user_force_logout;
use App\Http\Controllers\Enum\AdminNotificationType;
use App\Http\Controllers\Enum\AdminScannerType;
use App\Http\Controllers\Enum\Constants;
use App\Http\Controllers\Enum\DeliveryType;
use App\Http\Controllers\Enum\GlobalTexts;
use App\Http\Controllers\Enum\LoginStatus;
use App\Http\Controllers\Enum\MembershipPriceType;
use App\Http\Controllers\Enum\PlatformType;
use App\Http\Controllers\Enum\PostType;
use App\Http\Controllers\Enum\PromoType;
use App\Http\Controllers\Enum\SellerRole;
use App\Http\Controllers\Enum\SentMessageType;
use App\Http\Controllers\Enum\TransactionRequestStatus;
use App\Http\Controllers\functionController as mainFunctionController;
use App\Http\Controllers\LoginRegister\functionController as loginFunctionController;
use App\Http\Controllers\Renew\functionController as renewFunctionController;
use App\Http\Controllers\TransactionRequest\functionController;
use App\InfoAtBuyCard;
use App\LikePost;
use App\PartnerAccount;
use App\PartnerBranch;
use App\PartnerGalleryImages;
use App\PartnerInfo;
use App\PartnerMenuImages;
use App\PartnerProfileImage;
use App\Post;
use App\Review;
use App\ReviewComment;
use App\ScannerReward;
use App\SentMessageHistory;
use App\TopBrands;
use App\TransactionTable;
use App\TrendingOffers;
use App\Wish;
use Carbon\Carbon;
use DateTime;
use DB;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Laracsv\Export;
use function Sodium\compare;

class adminController2 extends Controller
{
    //change credentials view
    public function settings()
    {
        $admins = Admin::all();

        return view('admin.production.change_credentials', compact('admins'));
    }

    //store new admin credentials
    public function changePassword(Request $request)
    {
        $this->validate($request, [
            'sup_pass' => 'nullable|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
            'sprt_pass' => 'nullable|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
            'int_pass' => 'nullable|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
        ]);
        $request->flashOnly(['sup_pass', 'sprt_pass', 'int_pass']);

        $sup_pass = $request->get('sup_pass');
        $sprt_pass = $request->get('sprt_pass');
        $int_pass = $request->get('int_pass');
        if ($sup_pass == null && $sprt_pass == null && $int_pass == null) {
            return redirect()->back();
        }
        if ($sup_pass != null) {
            $pass = (new mainFunctionController())->encrypt_decrypt('encrypt', $sup_pass);
            Admin::where('username', 'rbd_sup_adm')->update(['password' => $pass]);
        }
        if ($sprt_pass != null) {
            $pass = (new mainFunctionController())->encrypt_decrypt('encrypt', $sprt_pass);
            Admin::where('username', 'rbd_sprt_adm')->update(['password' => $pass]);
        }
        if ($int_pass != null) {
            $pass = (new mainFunctionController())->encrypt_decrypt('encrypt', $int_pass);
            Admin::where('username', 'rbd_int_adm')->update(['password' => $pass]);
        }

        return redirect()->back()->with('status', 'Password changed successfully');
    }

    //branch of specific partner
    public function getBranch(Request $request)
    {
        $partner_id = $request->input('partner_id');
        $branch = PartnerBranch::where('partner_account_id', $partner_id)->get();
        $branches = $branch->map(function ($row) {
            return [
                'id' => $row->id,
                'partner_id' => $row->partner_account_id,
                'area' => $row->partner_area,
                'address' => $row->partner_address,
            ];
        });

        return Response::json($branches);
    }

    //offer of specific branch
    public function getOffer(Request $request)
    {
        $branch_id = $request->input('branch_id');
        $offers = BranchOffers::offers($branch_id)->orderBy('id', 'DESC')->get();
        $date = date('d-m-Y');

        foreach ($offers as $key => $branch_offer) {
            if (
                new DateTime($branch_offer['date_duration'][0]['from']) <= new DateTime($date)
                && new DateTime($branch_offer['date_duration'][0]['to']) >= new DateTime($date)
            ) {
                //
            } else {
                unset($offers[$key]);
            }
        }

        $offers = $offers->map(function ($row) {
            return [
                'id' => $row->id,
                'branch_id' => $row->branch_id,
                'offer' => $row->offer_description,
            ];
        });

        return Response::json($offers);
    }

    //manual transaction view
    public function addManualTransaction()
    {
        $allPartners = PartnerInfo::orderBy('partner_name', 'ASC')->get();
        $transactions = TransactionTable::manualTransaction()->with('branch.info.account', 'customer', 'offer')
            ->orderBy('posted_on', 'DESC')->get();
        $allTransactions = $transactions->map(function ($row) {
            // if ($row->branch->info->account->active == 1) {
            return (new adminController)->getSingleTranInfo($row);
            // }
        })->filter();

        return view('admin.production.manual_transaction', compact('allPartners', 'allTransactions'));
    }

    //store manual transaction value
    public function storeManualTransaction(Request $request)
    {
        $this->validate($request, [
            'partner_branch' => 'required',
            'branch_offer' => 'required',
            'customer_id' => 'required',
            'date' => 'required',
        ]);
        $request->flashOnly(['partner_branch', 'branch_offer', 'customer_id', 'date']);

        $branch_id = $request->get('partner_branch');
        $customer_id = $request->get('customer_id');
        $offer_id = $request->get('branch_offer');
        $date = $request->get('date');
        $offer = BranchOffers::where('id', $offer_id)->with('customizedPoint')->first();
        $point = $offer['point'];
        $week_Day = strtolower($request->get('weekday'));
        $time = $request->get('time');
        $date2 = Carbon::parse($date)->format('Y-m-d');
        $posted_on = $date2.' '.$time.':00';
        $other_prices = AllAmounts::all();
        $daily_point_limit = $other_prices->where('type', 'daily_point_limit')->first()->price;

        $customer = CustomerInfo::where('customer_id', $customer_id)->first();
        $branch = PartnerBranch::where('id', $branch_id)->with('info.profileImage')->first();

        if ($customer->customer_type == 3) {//guest can not be transacted
            return redirect()->back()->with('error', 'Not a premium member');
        }

        if ($offer->customizedPoint) {
            $date_valid = false;
            $time_valid = false;
            $week_valid = false;
            $customize_point_date = $offer->customizedPoint->date_duration;
            $customize_point_week = $offer->customizedPoint->weekdays;
            $customize_point_times = $offer->customizedPoint->time_duration;

            try {
                if (
                    new DateTime($customize_point_date[0]['from']) <= new DateTime($date)
                    && new DateTime($customize_point_date[0]['to']) >= new DateTime($date)
                ) {
                    $date_valid = true;
                }
            } catch (\Exception $e) {
                $date_valid = false;
            }
            if (count($customize_point_times) > 0) {
                foreach ($customize_point_times as $customize_point_time) {
                    try {
                        if (
                            new DateTime($customize_point_time['from']) <= new DateTime($time)
                            && new DateTime($customize_point_time['to']) >= new DateTime($time)
                        ) {
                            $time_valid = true;
                            break;
                        }
                    } catch (\Exception $e) {
                    }
                }
            } else {
                $time_valid = true;
            }

            if ($customize_point_week[0][$week_Day] == 1) {
                $week_valid = true;
            }

            if ($date_valid && $time_valid && $week_valid) {
                $point = $point * $offer->customizedPoint->point_multiplier;
            } else {
                $point = $point * 1;
            }
        } else {
            $point = $point * 1;
        }
        $today_point = TransactionTable::where('customer_id', $customer_id)->where('posted_on', 'like', $date2.'%')->sum('transaction_point');

        if ($today_point >= $daily_point_limit) {
            $new_point = 0;
        } else {
            if (($point + $today_point) > $daily_point_limit) {
                $new_point = $daily_point_limit - $today_point;
            } else {
                $new_point = $point;
            }
        }

        $branch_transaction_count = TransactionTable::where('branch_id', $branch_id)
            ->where('customer_id', $customer_id)
            ->where('offer_id', $offer_id)
            ->where('posted_on', 'like', $date2.'%')
            ->count();
        if ($branch_transaction_count >= Constants::branch_transaction_count) {
            return redirect()->back()->with('error', GlobalTexts::offer_wise_transaction_error);
        }
        if ($offer->active == 0) {
            return redirect()->back()->with('error', 'This offer is not valid.');
        } elseif ($offer->counter_limit != null) {
            $offer_count = TransactionTable::where('offer_id', $offer_id)->count();
            if ($offer->counter_limit <= $offer_count) {
                return redirect()->back()->with('error', 'The offer has reached it\'s limit.');
            }
        } elseif ($offer->scan_limit != null) {
            $offer_count = TransactionTable::where('offer_id', $offer_id)->where('customer_id', $customer_id)->count();
            if ($offer->scan_limit <= $offer_count) {
                return redirect()->back()->with('error', 'This customer has reached the offer limit.');
            }
        }

        \DB::beginTransaction();
        try {
            //insert transaction
            $transaction = new TransactionTable([
                'branch_id' => $branch_id,
                'customer_id' => $customer_id,
                'posted_on' => $posted_on,
                'transaction_point' => $new_point,
                'branch_user_id' => AdminScannerType::manual_transaction,
                'offer_id' => $offer_id,
            ]);
            $transaction->save();
            //insert notification
            $notif_text = $new_point.' credits have been added to your account from ';
            $customer_notification = new CustomerNotification([
                'user_id' => $customer_id,
                'image_link' => $branch->info->profileImage->partner_profile_image,
                'notification_text' => $notif_text,
                'notification_type' => 3,
                'source_id' => $transaction->id,
                'seen' => 0,
            ]);
            $customer_notification->save();

            (new \App\Http\Controllers\AdminNotification\functionController())->manualTransactionNotification($transaction);

            \DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            \DB::rollBack(); //rollback all successfully executed queries
            return redirect()->back()->with('error', 'Please try again!');
        }
        //trigger 'setPusher' function to do live push notification for website
        event(new offer_availed($customer_id));
        //message to send as parameter
        $message = $notif_text.$branch->info->partner_name.', '.$branch->partner_area;
        //send notification to app
        (new jsonController)->sendFirebaseDiscountNotification($message, $customer, 3, $branch->id, $transaction->id);
        (new \App\Http\Controllers\TransactionRequest\functionController())->onTransactionSendReferPoints($customer_id);

        return redirect()->back()->with('success', 'Transaction successful');
    }

    //function to change customer suspension
    public function customerSuspension()
    {
        $userId = $_POST['userId'];
        $status = $_POST['status'];
        $result[0] = $status;
        $result[1] = $userId;

        if ($status == 1) { //unsuspend this user
            DB::table('customer_account')
                ->where('customer_id', $userId)
                ->update(['isSuspended' => 0]);

            return Response::json($result);
        } else { //suspend this user
            DB::table('customer_account')
                ->where('customer_id', $userId)
                ->update(['isSuspended' => 1]);
            $exist = CustomerLoginSession::where('customer_id', $userId)->where('status', LoginStatus::logged_in)->orderBy('id', 'DESC')->first();

            if ($exist) {
                $exist->status = LoginStatus::kicked;
                $exist->save();
            }
            $data['customer_id'] = $userId;
            //trigger event to user force logout
            event(new user_force_logout($data));

            return Response::json($result);
        }
    }

    //partner subcategory edit view
    public function editSubCategory($id)
    {
        $subcatInfo = PartnerAccount::where('partner_account_id', $id)->with('categoryRelation', 'info')->first();

        return view('admin.production.edit_subcategory', compact('subcatInfo'));
    }

    public function transactionBreakDown(Request $request)
    {
        $from = array_get($request->all(), 'from', date('Y-m-01'));
        $to = array_get($request->all(), 'to', date('Y-m-d'));
        $status = array_get($request->all(), 'status', 1);
        $active = array_get($request->all(), 'active', 1);
        $selected_area = array_get($request->all(), 'area', 'all');

        $from_date = $from.' 00:00:00';
        $to_date = $to.' 23:59:59';

        $grand_total = 0;
        if ($selected_area != 'all') {
            $branches = PartnerBranch::with('info', 'account')->where('partner_area', $selected_area)->get();
        } else {
            $branches = PartnerBranch::with('info', 'account')->get();
        }
        foreach ($branches as $key1 => $branch) {
            $transactions = TransactionTable::where('branch_id', $branch->id)->where('posted_on', '>=', $from_date)
                ->where('posted_on', '<=', $to_date)->get();

            $total_transaction = $transactions->count();
            $qr_transaction = $transactions->where('transaction_request_id', '!=', null)->count();
            $branch->total_transaction = $total_transaction;
            $branch->card_transaction = $transactions->where('transaction_request_id', null)->count();
            $branch->qr_transaction = $qr_transaction;
            $grand_total = $grand_total + $total_transaction;
            if ($status != 'all') {
                if ($branch->account->active != $status && $branch->active != $status) {
                    unset($branches[$key1]);
                }
            }
            if ($active != 'all') {
                if ($active == 1) {
                    if ($total_transaction == 0) {
                        unset($branches[$key1]);
                    }
                } else {
                    if ($total_transaction > 0) {
                        unset($branches[$key1]);
                    }
                }
            }
        }
        $categories = Categories::all();
        $area = PartnerBranch::select('partner_area')->distinct('partner_area')->get();

        return view('admin.production.scan_analytics', compact(
            'branches',
            'grand_total',
            'from',
            'to',
            'status',
            'active',
            'categories',
            'selected_area',
            'area'
        ));
    }

    public function branchOffersBreakDown(Request $request, $branch_id)
    {
        $month = array_get($request->all(), 'month', date('m'));
        $year = array_get($request->all(), 'year', date('Y'));

        $grand_total = 0;
        $partner = PartnerBranch::where('id', $branch_id)->with('info')->first();
        $offers = BranchOffers::offers($branch_id)->orderBy('id', 'DESC')->get();
        foreach ($offers as $offer) {
            $transactions = TransactionTable::where('offer_id', $offer->id)->get();
            if ($month != null && $year != null) {
                foreach ($transactions as $key => $value) {
                    $ex = explode('-', $value['posted_on']);
                    //checking if DB=>"month,year" & selected=>"month,year" are same or not
                    if ($ex[0] != $year || $ex[1] != $month) {
                        //unset specific array index if not match
                        unset($transactions[$key]);
                    }
                }
            }
            $total_transaction = $transactions->count();
            $offer->total_transaction = $total_transaction;
            $grand_total = $grand_total + $total_transaction;
        }

        return view('admin.production.branch_offer_analytics', compact(
            'offers',
            'partner',
            'grand_total',
            'year',
            'month'
        ));
    }

    //transaction requests
    public function transactionRequests()
    {
        $status = 'Pending';
        $notifications = (new functionController)->getAllBranchUserNotification(0);

        return view('admin.production.transactionRequests.index', compact('notifications', 'status'));
    }

    //transaction requests
    public function acceptedTransactionRequests()
    {
        $status = 'Accepted';
        $notifications = (new functionController)->getAllBranchUserNotification(0, 1);

        return view('admin.production.transactionRequests.index', compact('notifications', 'status'));
    }

    //transaction requests
    public function declinedTransactionRequests()
    {
        $status = 'Rejected';
        $notifications = (new functionController)->getAllBranchUserNotification(0, 2);

        return view('admin.production.transactionRequests.index', compact('notifications', 'status'));
    }

    public function update_request_status($notification_id, $requestID, $branch_user_id, $status)
    {
        $branch_id = BranchScanner::where('branch_user_id', $branch_user_id)->select('branch_id')->first();
        BranchUserNotification::where('source_id', $requestID)->update(['seen' => 1]);
        $request = (new functionController)->updateTransactionRequest($requestID, $status, $branch_user_id, $rbd_admin = true, PlatformType::rbd_admin);
        (new functionController())->updateMerchantNotificationCount($branch_id->branch_id, null, $requestID, $status);
        if ($request->status == TransactionRequestStatus::ACCEPTED) {
            return redirect('admin/transaction_requests')->with('accepted', 'Transaction successful.');
        } elseif ($request->status == TransactionRequestStatus::DECLINED) {
            (new \App\Http\Controllers\AdminNotification\functionController())->transactionRequestRejectNotification($request);

            return redirect('admin/transaction_requests')->with('rejected', 'Transaction declined.');
        }
    }

    public function addOffers()
    {
        $branches = PartnerBranch::with('info')->get();

        return view('admin.production.addOffers', compact('branches'));
    }

    public function addRewards()
    {
        $branches = PartnerBranch::with('info')->get();

        return view('admin.production.addRewards', compact('branches'));
    }

    public function addVouchers()
    {
        $branches = PartnerBranch::with('info')->get();

        return view('admin.production.addVouchers', compact('branches'));
    }

    //function to activate user's trial option
    public function activateTrial($customer_id)
    {
        $month = CardPrice::where([['platform', PlatformType::web], ['type', MembershipPriceType::buy], ['price', 0]])->first()->month;
        $amount = 0;
        $tran_id = (new JsonControllerV2())->getSSLTransactionId();
        $delivery_type = DeliveryType::virtual_card;
        $promo_id = 0;
        $tran_date = date('Y-m-d H:i:s');

        $customer = CustomerAccount::where('customer_id', $customer_id)->first();

        $renewInfo = (new renewFunctionController())->insertInfoRenew($customer, $tran_id, $month, $delivery_type, $promo_id, $amount, true, PlatformType::rbd_admin);
        if (! $renewInfo) {
            return redirect('customers/guest')->with('try_again', 'Something went wrong in making trial.');
        }

        $renewSSL = (new renewFunctionController())->insertSSLRenew($customer_id, $tran_id, $amount, PlatformType::rbd_admin);
        if (! $renewSSL) {
            return redirect('customers/guest')->with('try_again', 'Something went wrong in making trial.');
        }

        $info = (new renewFunctionController())->updateSSLRenew($amount, $tran_id, $tran_date, '', '0.00', 'CASH', '', 'BDT',
            '', '', '', 'BD', '', '0.00', $customer_id, null, \session('admin_id'));

        if (! $info) {
            return redirect('customers/guest')->with('try_again', 'Something went wrong in making trial.');
        } else {
            $month_txt = $month > 1 ? ' months ' : ' month ';
            InfoAtBuyCard::where('customer_id', $customer_id)->delete();

            return redirect('customers/guest')->with('status', 'You have successfully activated '.
                $month.$month_txt.'free trial for '.$customer->info->customer_full_name.'.');
        }
    }

    //============function for make cardholder view==================
    public function upgradeMembership($id)
    {
        $profileInfo = CustomerInfo::where('customer_id', $id)->first();

        if ($profileInfo->customer_type == 3) {
            $mem_plans = CardPrice::where([['platform', PlatformType::web], ['type', MembershipPriceType::buy],
                ['price', '!=', 0], ])->orderBy('price', 'ASC')->get();
            $upgrade_mem = true;
            $mem_type = PromoType::CARD_PURCHASE;
            $delivery_type = DeliveryType::home_delivery;
        } else {
            $delivery = CardDelivery::where('customer_id', $profileInfo->customer_id)->orderBy('id', 'DESC')->first();
            $exp_status = (new functionController2)->getExpStatusOfCustomer($profileInfo->expiry_date);
            if ($exp_status != 'active') {
                $mem_type = PromoType::RENEW;
                $delivery_type = DeliveryType::renew;
            } else {
                if ($delivery->delivery_type == DeliveryType::virtual_card) {
                    $mem_type = PromoType::UPGRADE;
                    $delivery_type = DeliveryType::home_delivery;
                }
//                else{
//                    $mem_type = PromoType::RENEW;
//                }
            }
            $mem_plans = CardPrice::where([['platform', PlatformType::web], ['type', MembershipPriceType::renew]])
                ->orderBy('price', 'ASC')->get();
            $upgrade_mem = false;
        }

        return view('admin.production.make_cardholder', compact('profileInfo', 'mem_plans',
            'upgrade_mem', 'mem_type', 'delivery_type'));
    }

    public function upgradeMembershipDone($old_customer_id, Request $request)
    {
        $this->validate($request, [
            'mem_plan' => 'required',
            'price' => 'required',
        ]);
        $request->flashOnly(['mem_plan', 'price']);

        $mem_plan = $request->get('mem_plan');
        $month = explode('-', $mem_plan)[0];
        $amount = $request->get('price');
        $tran_id = (new JsonControllerV2())->getSSLTransactionId();
        $customer = CustomerAccount::where('customer_id', $old_customer_id)->with('info')->first();
        $delivery_type = $request->get('delivery_type');

        if ($request->get('promo_code') != null) {
            $promo_id = CardPromoCodes::where('code', $request->get('promo_code'))->first()->id;
        } else {
            $promo_id = 0;
        }
        $tran_date = date('Y-m-d H:i:s');

        $renewInfo = (new renewFunctionController())->insertInfoRenew($customer, $tran_id, $month, $delivery_type,
            $promo_id, $amount, PlatformType::rbd_admin, true);
        if (! $renewInfo) {
            return redirect()->back()->with('try_again', 'Something went wrong in making card holder.');
        }

        $renewSSL = (new renewFunctionController())->insertSSLRenew($old_customer_id, $tran_id, $amount, PlatformType::rbd_admin);
        if (! $renewSSL) {
            return redirect()->back()->with('try_again', 'Something went wrong in making card holder.');
        }

        $info = (new renewFunctionController())->updateSSLRenew($amount, $tran_id, $tran_date, '',
            '0.00', 'CASH', '', 'BDT', '', '',
            '', 'BD', '', '0.00',
            $old_customer_id, $seller_id = SellerRole::fromAdmin, \session('admin_id'));

        if (! $info) {
            return redirect()->back()->with('try_again', 'Something went wrong in making card holder.');
        } else {
            //TO Update all other customer tables
//            (new mainFunctionController())->updateCustomerId($old_customer_id, $new_customer_id, 1);
            $mon_txt = $month > 1 ? ' months' : ' month';
            if ($request->get('mem_change') == PromoType::UPGRADE) {
                $msg = 'You have successfully upgraded '.$old_customer_id.' to '.$month.$mon_txt.' card user.';
            } else {
                $msg = 'You have successfully renewed '.$old_customer_id.' to '.$month.$mon_txt.' card user.';
            }

            return redirect('customers/card_users')->with('status', $msg);
        }
    }

    //function to view manual registration page
    public function manualRegistration()
    {
        return view('admin.production.manual_registration');
    }

    //function to store manual registration info
    public function storeManualRegistration(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'phone' => 'required|unique:customer_info,customer_contact_number',
            'email' => 'required|email|unique:customer_info,customer_email',
        ]);
        $request->flashOnly(['name', 'phone', 'email']);

        $name = $request->get('name');
        $phone = $request->get('phone');
        $email = $request->get('email');
        $pin = null;

        try {
            DB::beginTransaction(); //to do query rollback

            (new loginFunctionController)->register($name, $email, $phone, $pin, PlatformType::rbd_admin);

            DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Something went wrong, please try again.');
        }

        return redirect('customers/guest')->with('status', 'User created successfully.');
    }

    //delete user function
    public function deleteUser($customerId)
    {
        $review_ids = Review::where('customer_id', $customerId)->pluck('id');

        DB::beginTransaction(); //to do query rollback
        try {
            //delete all reviews
            foreach ($review_ids as $id) {
                (new mainFunctionController())->deleteReview($id);
            }
            //delete all post likes
            LikePost::where('liker_id', $customerId)->delete();

            //delete like review notification from customer_notification table
            DB::table('customer_notification as cn')
                ->join('likes_review as lr', 'cn.source_id', '=', 'lr.id')
                ->where('lr.liker_id', $customerId)
                ->where('cn.notification_type', 1)
                ->delete();
            //delete follow customer notification from customer_notification table
            DB::table('customer_notification as cn')
                ->join('follow_customer as fc', 'fc.id', '=', 'cn.source_id')
                ->where('fc.follower', $customerId)
                ->where('cn.notification_type', 8)
                ->delete();
            //delete Accept follow request notification from customer_notification table
            DB::table('customer_notification as cn')
                ->join('follow_customer as fc', 'fc.id', '=', 'cn.source_id')
                ->where('fc.following', $customerId)
                ->where('cn.notification_type', 9)
                ->delete();
            //delete refer notification from notification table
            DB::table('customer_notification')
                ->where('user_id', $customerId)
                ->where('notification_type', 10)
                ->delete();
            //delete like post notification from like_post table
            DB::table('partner_notification as pn')
                ->join('likes_post as lp', 'lp.id', '=', 'pn.source_id')
                ->where('lp.liker_id', $customerId)
                ->where('pn.notification_type', 7)
                ->delete();
            //delete review notification from partner notification table
            DB::table('partner_notification as pn')
                ->join('review as rev', 'rev.id', '=', 'pn.source_id')
                ->where('rev.customer_id', $customerId)
                ->where('pn.notification_type', 2)
                ->delete();

            //get current image name
            $get_current_image_name = CustomerInfo::where('customer_id', $customerId)->select('customer_profile_image')->first();
            $image_path = $get_current_image_name->customer_profile_image;

            dd($customerId);
            DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            DB::rollBack(); //rollback all successfully executed queries
            return redirect('customers/guest')->with('try_again', 'Please try again!');
        }
        if (strpos($image_path, 'https://royalty-bd.s3.ap-southeast-1.amazonaws.com/') !== false) {
            $exploded_path = explode('/', $image_path);
            //remove previous profile image from folder
            Storage::disk('s3')->delete('dynamic-images/users/'.end($exploded_path));
        }
    }

    //function to update partner cover photo
    public function partnerCoverPhoto($partner_id)
    {
        $cover_photo = PartnerProfileImage::where('partner_account_id', $partner_id)->first();

        return view('/admin/production/coverPhoto', compact('cover_photo'));
    }

    //function to store partner cover photo
    public function updatePartnerCoverPhoto(Request $request, $partner_id)
    {
        if (Session::has('user_profile_image_name')) {
            //get current image name
            $get_current_image_name = PartnerProfileImage::where('partner_account_id', $partner_id)->first();
            $image_path = $get_current_image_name->partner_cover_photo;
            $exploded_path = explode('/', $image_path);

            if (strpos($image_path, 'https://royalty-bd.s3.ap-southeast-1.amazonaws.com/') !== false) {
                //remove previous profile image from folder
                Storage::disk('s3')->delete('dynamic-images/partner_cover_pics/'.end($exploded_path));
                //update new image info
                Storage::disk('s3')->put('dynamic-images/partner_cover_pics/'.Session::get('user_profile_image_name'), Session::get('user_profile_image'), 'public');
                $image_url = Storage::disk('s3')->url('dynamic-images/partner_cover_pics/'.Session::get('user_profile_image_name'));
            } else {
                //just update the new image info
                Storage::disk('s3')->put('dynamic-images/partner_cover_pics/'.Session::get('user_profile_image_name'), Session::get('user_profile_image'), 'public');
                $image_url = Storage::disk('s3')->url('dynamic-images/partner_cover_pics/'.Session::get('user_profile_image_name'));
            }

            $get_current_image_name->partner_cover_photo = $image_url;
            $get_current_image_name->save();
            //remove session of cropped image
            $request->session()->forget('user_profile_image_name');
            $request->session()->forget('user_profile_image');
        } else {
            return redirect()->back()->with('try_again', 'Please try again');
        }

        return redirect()->back()->with('updated', 'Cover photo updated successfully');
    }

    //function to get user leaderboard
    public function dashboardUserLeaderboard(Request $request)
    {
        $year = $request->input('year');
        $month = $request->input('month');
//        $selected_month = $year.'-'.$month;
        $selected_month = date('Y-m');
        $cur_date = date('Y-m-d');
        $leaderBoard = [];
        //till prev day
        $prev_data = DB::select("select sum(tt.transaction_point) as total_point, count(tt.customer_id) as transaction_count,
                                       tt.customer_id, (select ch.type from customer_history as ch where tt.customer_id = ch.customer_id
                                            order by ch.id desc limit 1) as user_type, ci.customer_contact_number, ci.customer_full_name
                                    from transaction_table as tt
                                         join customer_info ci on tt.customer_id = ci.customer_id
                                    where tt.posted_on like '$selected_month%' and tt.posted_on not like '$cur_date%'
                                    group by tt.customer_id, user_type, ci.customer_contact_number, ci.customer_full_name
                                    order by transaction_count desc, total_point desc");
        //till current day
        $leaderboard_data = DB::select("select sum(tt.transaction_point) as total_point, count(tt.customer_id) as transaction_count,
                                       tt.customer_id, (select ch.type from customer_history as ch where tt.customer_id = ch.customer_id
                                    order by ch.id desc limit 1) as user_type, ci.customer_contact_number, ci.customer_full_name
                                    from transaction_table as tt
                                        join customer_info ci on tt.customer_id = ci.customer_id
                                    where tt.posted_on like '$selected_month%'
                                    group by tt.customer_id, user_type, ci.customer_contact_number, ci.customer_full_name
                                    order by transaction_count desc, total_point desc");
        $i = 0;
        foreach ($leaderboard_data as $user) {
            $leaderBoard[$i]['customer_full_name'] = $user->customer_full_name;
            $leaderBoard[$i]['customer_contact_number'] = $user->customer_contact_number;
            $leaderBoard[$i]['user_type'] = $user->user_type;
            $leaderBoard[$i]['transaction_count'] = $user->transaction_count;
            for ($j = 0; $j < count($prev_data); $j++) {
                if ($prev_data[$j]->customer_id == $user->customer_id) {
                    $leaderBoard[$i]['prev_index'] = $j;
                    break;
                }
            }
            $i++;
        }
        $leaderBoard = array_slice($leaderBoard, 0, 10);

        return \response()->json($leaderBoard);
    }

    //function to get partner leaderboard
    public function dashboardPartnerLeaderboard(Request $request)
    {
        $year = $request->input('year');
        $month = $request->input('month');
//        $selected_month = $year.'-'.$month;
        $selected_month = date('Y-m');
        $accounts = PartnerAccount::where('active', 1)->with('branches.info.profileImage')->get();
        $i = 0;
        foreach ($accounts as $account) {
            if ($account->active == 1) {
                foreach ($account->branches as $branch) {
                    if ($branch->active == 1) {
                        $branches[$i] = $branch;
                        $i++;
                    }
                }
            }
        }
        $scan_point = AllAmounts::where('type', 'per_card_scan')->first()->price;

        //till previous day
        $i = 0;
        foreach ($branches as $branch) {
            $previous_transaction_count = TransactionTable::where('branch_id', $branch->id)
                ->where('branch_user_id', '!=', AdminScannerType::accept_tran_req)
                ->where('branch_user_id', '!=', AdminScannerType::manual_transaction)
                ->where('posted_on', 'like', date('Y-m').'%')
                ->where('posted_on', 'not like', date('Y-m-d').'%')->count();
            $previous_admin_transaction_count = TransactionTable::where('branch_id', $branch->id)
                ->where(function ($query) {
                    $query->where('branch_user_id', '=', AdminScannerType::accept_tran_req)
                        ->orWhere('branch_user_id', '=', AdminScannerType::manual_transaction);
                })
                ->where('posted_on', 'like', date('Y-m').'%')
                ->where('posted_on', 'not like', date('Y-m-d').'%')->count();
            $previous_day_leaderBoard[$i]['profile_image'] = $branch->info->profileImage->partner_profile_image;
            $previous_day_leaderBoard[$i]['partner_name'] = $branch->info->partner_name;
            $previous_day_leaderBoard[$i]['area'] = $branch->partner_area;
            $previous_day_leaderBoard[$i]['branch_id'] = $branch->id;
            $previous_day_leaderBoard[$i]['branch_point'] = $previous_transaction_count * $scan_point;
            $previous_day_leaderBoard[$i]['admin_point'] = $previous_admin_transaction_count * $scan_point;
            $previous_day_leaderBoard[$i]['point'] = ($previous_admin_transaction_count * $scan_point) + ($previous_transaction_count * $scan_point);
            $i++;
        }
        $array_point = array_column($previous_day_leaderBoard, 'point');
        $array_name = array_column($previous_day_leaderBoard, 'partner_name');
        array_multisort($array_point, SORT_DESC, $array_name, SORT_ASC, $previous_day_leaderBoard);

        //till current day
        $i = 0;
        foreach ($branches as $branch) {
            $current_transaction_count = TransactionTable::where('branch_id', $branch->id)
                ->where('branch_user_id', '!=', AdminScannerType::accept_tran_req)
                ->where('branch_user_id', '!=', AdminScannerType::manual_transaction)
                ->where('posted_on', 'like', $selected_month.'%')->count();
            $current_admin_transaction_count = TransactionTable::where('branch_id', $branch->id)
                ->where(function ($query) {
                    $query->where('branch_user_id', '=', AdminScannerType::accept_tran_req)
                        ->orWhere('branch_user_id', '=', AdminScannerType::manual_transaction);
                })
                ->where('posted_on', 'like', $selected_month.'%')->count();
            $leaderBoard[$i]['profile_image'] = $branch->info->profileImage->partner_profile_image;
            $leaderBoard[$i]['partner_name'] = $branch->info->partner_name;
            $leaderBoard[$i]['area'] = $branch->partner_area;
            $leaderBoard[$i]['branch_id'] = $branch->id;
            $leaderBoard[$i]['branch_point'] = $current_transaction_count * $scan_point;
            $leaderBoard[$i]['admin_point'] = $current_admin_transaction_count * $scan_point;
            $leaderBoard[$i]['point'] = ($current_admin_transaction_count * $scan_point) + ($current_transaction_count * $scan_point);
            $leaderBoard[$i]['prev_date'] = \Carbon\Carbon::yesterday()->toDateString();
            for ($j = 0; $j < count($previous_day_leaderBoard); $j++) {
                if ($previous_day_leaderBoard[$j]['branch_id'] == $branch->id) {
                    $leaderBoard[$i]['prev_index'] = $j;
                    break;
                }
            }
            $i++;
        }
        $array_point = array_column($leaderBoard, 'point');
        $array_name = array_column($leaderBoard, 'partner_name');
        array_multisort($array_point, SORT_DESC, $array_name, SORT_ASC, $leaderBoard);
        $leaderboard_data = array_slice($leaderBoard, 0, 10);

        return \response()->json($leaderboard_data);
    }

    //function to get all notification for admin
    public function allNotifications()
    {
        AdminActivityNotification::where('seen', 0)->update(['seen' => 1]);
        $notifications = AdminActivityNotification::where('created_at', 'like', date('Y-m-d').'%')
            ->orderBy('created_at', 'desc')->get();

        $from = date('Y-m-d');
        $to = date('Y-m-d');

        return view('admin.production.all_notifications', compact('notifications', 'from', 'to'));
    }

    //function to get sorted all notification for admin
    public function sortAllNotifications(Request $request)
    {
        $from = date($request->get('from_date'));
        $to = date($request->get('to_date'));

        $notifications = AdminActivityNotification::whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->orderBy('created_at', 'desc')->get();

        return view('admin.production.all_notifications', compact('notifications', 'from', 'to'));
    }

    public function generateActivityReport()
    {
        $from = $_GET['from_date'];
        $to = $_GET['to_date'];
        $notifications = AdminActivityNotification::whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->orderBy('created_at', 'desc')->get();

        if (count($notifications) > 0) {
            $options = new Options();
            $options->set('isRemoteEnabled', true);
            $pdf = new Dompdf($options);
            $pdf->loadHtml(
                View::make('pdf.notification_report', compact('notifications', 'from', 'to'))->render()
            );
            $pdf->render();

            return $pdf->stream(date('m_d_Y').'_report.pdf', ['Attachment' => $pdf->output()]);
        } else {
            return redirect()->back()->with('try_again', 'Nothing to generate.');
        }
    }

    public function getEmailsToPrint(Request $request)//testing function (can be deleted)
    {
        $user_type = $request->input('user_type');

        $emails = CustomerInfo::pluck('customer_email');

        return \response()->json($emails);
    }

    public function generatePDFTest(Request $request)//testing function (can be deleted)
    {
        $emails = $request->post('emails');
        $title = $request->post('title');
        if (count($emails) > 0) {
            $options = new Options();
            $options->set('isRemoteEnabled', true);
            $pdf = new Dompdf($options);
            $pdf->loadHtml(
                View::make('pdf.customer_email_list', compact('emails', 'title'))->render()
            );
            $pdf->render();

            return \response()->json($pdf->stream(date('m_d_Y_').$title.'email_list.pdf', ['Attachment' => $pdf->output()]));
        }
    }

    public function generateEmailListFromAllCustomers(Request $request)
    {
//        set_time_limit(0);//run this function forever to generate emails
        $emails = $request->post('emails');
        $title = $request->post('title');
        $emails = json_decode($emails, true);
        $emails_count = count($emails);
        if ($emails_count > 0) {
            //for text file
            $content = '';
            foreach ($emails as $key => $email) {
                if ($emails_count - 1 == $key) {
                    $content .= $email."\n";
                } else {
                    $content .= $email.','."\n";
                }
            }

            // file name that will be used in the download
            $fileName = date('m_d_Y_').$title.'_email_list.txt';

            // use headers in order to generate the download
            $headers = [
                'Content-type' => 'text/plain',
                'Content-Disposition' => sprintf('attachment; filename="%s"', $fileName),
            ];

            // make a response, with the content, a 200 response code and the headers
            return Response::make($content, 200, $headers);

        //for pdf file
//            $options = new Options();
//            $options->set('isRemoteEnabled', true);
//            $pdf = new Dompdf($options);
//            $pdf->loadHtml(
//                View::make('pdf.customer_email_list', compact('emails', 'title'))->render()
//            );
//            $pdf->render();
//            return $pdf->stream(date("m_d_Y_") . $title . 'email_list.pdf', array('Attachment' => $pdf->output()));
        } else {
            return redirect()->back()->with('try_again', 'Nothing to generate.');
        }
    }

    //generate csv of users with app version
    public function generateAppVersionCSV()
    {
        $data = DB::select('SELECT cas.customer_id, cas.platform, cas.version, ci.customer_full_name
                                FROM customer_activity_sessions cas
                                join customer_info ci on ci.customer_id = cas.customer_id
                                WHERE cas.version is not null
                                  and cas.id IN (SELECT MAX(id)
                                             FROM customer_activity_sessions
                                             GROUP BY customer_id)');
        $data = collect($data);
        $fields = ['customer_id', 'customer_full_name', 'platform', 'version'];
        $this->generateCSV($data, $fields, 'user_app_version.csv');
    }

    //generate csv of email verified users
    public function generateEmailVerifiedCSV()
    {
        $data = CustomerInfo::where('email_verified', 1)->select('customer_id', 'customer_full_name', 'customer_email')->get();
        $fields = ['customer_id'=>'CUSTOMER_ID', 'customer_full_name' => 'NAME', 'customer_email'=>'EMAIL'];
        $this->generateCSV($data, $fields, 'verified_emails.csv');
    }

    //generate csv of email unverified users
    public function generateEmailUnverifiedCSV()
    {
        $data = CustomerInfo::where('email_verified', 0)->select('customer_id', 'customer_full_name', 'customer_email')->get();
        $fields = ['customer_id'=>'CUSTOMER_ID', 'customer_full_name' => 'NAME', 'customer_email'=>'EMAIL'];
        $this->generateCSV($data, $fields, 'unverified_emails.csv');
    }

    //generate csv of profile completed users
    public function generateProfileCompletedCSV()
    {
        $data = CustomerInfo::where('customer_gender', '!=', null)
            ->where('customer_dob', '!=', null)
            ->where('customer_profile_image', '!=', 'https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/registration/user.png')
            ->select('customer_id', 'customer_full_name')
            ->get();
        $fields = ['customer_id'=>'CUSTOMER_ID', 'customer_full_name' => 'NAME'];
        $this->generateCSV($data, $fields, 'profile_completed.csv');
    }

    //generate csv of profile not completed users
    public function generateProfileNotCompletedCSV()
    {
        $data = CustomerInfo::where('customer_gender', null)
            ->orWhere('customer_dob', null)
            ->orWhere('customer_profile_image', 'https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/registration/user.png')
            ->select('customer_id', 'customer_full_name')
            ->get();
        $fields = ['customer_id'=>'CUSTOMER_ID', 'customer_full_name' => 'NAME'];
        $this->generateCSV($data, $fields, 'profile_not_completed.csv');
    }

    public function generateCSV($data, $fields, $file)
    {
        $csvExporter = new \Laracsv\Export();
        $csvExporter->build($data, $fields)->download($file);
        die();
    }

    public function trendingBrands()
    {
        $selectedTopBrands = TopBrands::with('info')->orderBy('order_num', 'ASC')->get();
        $selectedTrendingOff = TrendingOffers::with('info')->orderBy('order_num', 'ASC')->get();
        //get all partners
        $allPartners = PartnerAccount::where('active', 1)->with('info.branches')->get();

        foreach ($allPartners as $key => $partner) {
            if (count($partner->info->branches) == 0) {
                unset($allPartners[$key]);
            }
        }

        return view('admin.production.addTrendingBrands', compact('allPartners', 'selectedTopBrands', 'selectedTrendingOff'));
    }

    //function to add Trending offers
    public function addTrendingOffer(Request $request)
    {
        $partner_id = $request->get('partner2');
        $exists = TrendingOffers::where('partner_account_id', $partner_id)->count();
        if ($exists == 0) {
            $offers = TrendingOffers::all()->max('order_num');
            TrendingOffers::insert(['partner_account_id' => $partner_id, 'order_num' => $offers + 1]);

            return redirect('admin/trendingBrands')->with('trending partners added', 'New partner added successfully');
        } else {
            return redirect('admin/trendingBrands')->with('operation failed', 'Partner already exists');
        }
    }

    public function updateTrendingOffersOrder(Request $request)
    {
        $ids = explode(',', $request->input('sort_order'));
        for ($i = 0; $i < count($ids); $i++) {
            $top = TrendingOffers::find($ids[$i]);
            $top->order_num = $i + 1;
            $top->save();
        }

        return \response()->json($ids);
    }

    //function to remove trending offer
    public function removeTrendPartner($id)
    {
        TrendingOffers::where('id', $id)->delete();

        return redirect()->back()->with('deleted', 'Partner deleted successfully');
    }

    //function to add Top Brands
    public function addTopBrand(Request $request)
    {
        $partner_id = $request->get('partner1');
        $exists = TopBrands::where('partner_account_id', $partner_id)->count();
        if ($exists == 0) {
            $offers = TopBrands::all()->max('order_num');
            TopBrands::insert(['partner_account_id' => $partner_id, 'order_num' => $offers + 1]);

            return redirect('admin/trendingBrands')->with('trending partners added', 'New partner added successfully');
        } else {
            return redirect('admin/trendingBrands')->with('operation failed', 'Partner already exists');
        }
    }

    public function updateTopBrandsOrder(Request $request)
    {
        $ids = explode(',', $request->input('sort_order'));
        for ($i = 0; $i < count($ids); $i++) {
            $top = TopBrands::find($ids[$i]);
            $top->order_num = $i + 1;
            $top->save();
        }

        return \response()->json($ids);
    }

    //function to remove Top Brands
    public function removeTopBrand($id)
    {
        TopBrands::where('id', $id)->delete();

        return redirect()->back()->with('deleted', 'Partner deleted successfully');
    }

    public function partnerOfferRequest()
    {
        // get all wishes from the database
        $wishes = Wish::offerRequest()->get();
        //send all data to customer wishes page
        return view('/admin/production/partner_offer_request', compact('wishes'));
    }

    public function removeTransaction($transaction_id)
    {
        $transaction = TransactionTable::find($transaction_id);
        $scan_point = AllAmounts::where('type', 'per_card_scan')->first()->price;
        if ($transaction->branchUser) {
            ScannerReward::where('scanner_id', $transaction->branchUser->branchScanner->id)
                ->where('point', '>=', $scan_point)
                ->decrement('point', $scan_point);
        }
        $transaction->delete();

        return redirect()->back()->with('status', 'Transaction deleted');
    }

    //admin sidebar notification counts
    public function getSidebarNotificationCount()
    {
        $result = [];
        $result['activity_notification'] = \App\AdminActivityNotification::where('seen', 0)->count();
        $result['scanner_request_count'] = \App\ScannerPrizeHistory::where('status', 0)->count();
        $user_reward_redeem_count = \App\CustomerRewardRedeem::where('used', 0)->with('reward')->get();
        $result['user_royalty_reward_redeem_count'] = collect($user_reward_redeem_count)->where('reward.branch_id',
            \App\Http\Controllers\Enum\AdminScannerType::royalty_branch_id)->count();
        $result['user_partner_reward_redeem_count'] = collect($user_reward_redeem_count)->where('reward.branch_id', '!=',
            \App\Http\Controllers\Enum\AdminScannerType::royalty_branch_id)->count();
         $tran_req = \App\BranchUserNotification::where('notification_type', \App\Http\Controllers\Enum\PartnerBranchNotificationType::TRANSACTION_REQUEST)
             ->with('transactionRequest')->get();
         $tran_req = $tran_req->unique('source_id');
         $tran_req = collect($tran_req)->where('transactionRequest.status', 0);
         $result['transaction_request_count'] = count($tran_req);
        $result['partner_post_request_count'] = Post::where('poster_type', PostType::partner)->where('push_status', 0)->count();
        $result['pending_reviews'] = Review::where('moderation_status', 0)->count();
        $result['pending_replies'] = ReviewComment::where('moderation_status', 0)->count();

        return $result;
    }

    //sent message history
    public function sentMessageHistory($type)
    {
        $history = SentMessageHistory::where('sent', 1)->orderBy('id', 'DESC')->get();
        $tab_title = 'All';
        if ($type == 'sms') {
            $history = $history->where('type', SentMessageType::sms);
            $tab_title = 'SMS';
        } elseif ($type == 'push') {
            $history = $history->where('type', SentMessageType::push_notification);
            $tab_title = 'Push Notification';
        }

        return view('admin.production.sent_message_history', compact('history', 'tab_title'));
    }

    public function allReferLeaderBoard(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');
        $from = $from.' 00:00:00';
        $to = $to.' 23:59:59';
        $referrer_ids = CustomerInfo::where('referrer_id', '!=', null)
            ->where('member_since', '>=', $from)
            ->where('member_since', '<=', $to)
            ->get()->unique('referrer_id')->pluck('referrer_id');
        $data = CustomerInfo::whereIn('customer_id', $referrer_ids)
            ->with('latestSSLTransaction.cardDelivery')
            ->orderBy('reference_used', 'DESC')->get();
        //current referrence used
        foreach ($data as $key => $value) {
            $count = CustomerInfo::where('referrer_id', $value->customer_id)
            ->where('member_since', '>=', $from)
            ->where('member_since', '<=', $to)
            ->count();
            $value->cur_referred = $count;
        }

        return \response()->json($data);
    }

    //function to show menu images
    public function menuImage($partner_id)
    {
        $menuImages = PartnerMenuImages::where('partner_account_id', $partner_id)->get();
        $partner_name = PartnerInfo::where('partner_account_id', $partner_id)->select('partner_name')->first();

        return view('admin.production.partnerMenuImages', compact('menuImages', 'partner_id', 'partner_name'));
    }

    //function to add new menu image
    public function addMenuImage(Request $request, $partner_id)
    {
        $this->validate($request, [
            'menu' => 'required',
        ]);
        //insert gallery images in database
        if ($request->hasFile('menu')) {
            $files = $request->file('menu');
            $countPreviousImages = PartnerMenuImages::where('partner_account_id', $partner_id)->count();

            if ($countPreviousImages + count($files) <= 20) {
                foreach ($files as $file) {
                    try {
                        DB::beginTransaction(); //to do query rollback
                        //image is being resized & uploaded here
                        $image_url = (new \App\Http\Controllers\functionController)->uploadImageToAWS($file, 'dynamic-images/partner_gallery_image');
                        //image path saved to the database
                        PartnerMenuImages::insert([
                            'partner_account_id' => $partner_id,
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
        } else {
            return redirect()->back()->with('try_again', 'Please try again!');
        }

        return redirect('admin/partner-menu-images/'.$partner_id)->with('updated', 'Successfullly Image added');
    }

    //function to add menu image caption
    public function addMenuCaption(Request $request)
    {
        //collect caption & image id from ajax request
        $imageId = $request->input('id');
        $caption = $request->input('caption');
        //update image caption
        PartnerMenuImages::where('id', $imageId)->update(['image_caption' => $caption]);

        $updated[0] = 'updated';
        $updated[1] = $imageId;

        return Response::json($updated);
    }

    //function to add menu image caption
    public function pinMenuImage($partner_id, $img_id)
    {
        try {
            DB::beginTransaction(); //to do query rollback
            //update pinned image
            PartnerMenuImages::where('partner_account_id', $partner_id)->update(['pinned' => 0]);
            PartnerMenuImages::where('id', $img_id)->update(['pinned' => 1]);
            DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            DB::rollBack(); //rollback all successfully executed queries
            return redirect()->back()->with('try_again', 'Please try again!');
        }

        return redirect()->back()->with('pinned_img_changed', 'Pinned image changed.');
    }

    //function to delete menu image of partner
    public function deleteMenuImage($id)
    {
        $partner_menu_image = PartnerMenuImages::findOrFail($id);

        $get_current_image_path = $partner_menu_image->partner_menu_image;
        $exploded_path = explode('/', $get_current_image_path);
        try {
            DB::beginTransaction(); //to do query rollback
            //remove image path from partner_gallery_images table
            $partner_menu_image->delete();
            //remove gallery image from bucket
            Storage::disk('s3')->delete('dynamic-images/partner_menu_image/'.end($exploded_path));
            DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            DB::rollBack(); //rollback all successfully executed queries
            return redirect()->back()->with('try_again', 'Please try again!');
        }

        return redirect('admin/partner-menu-images/'.$partner_menu_image->partner_account_id)
            ->with('updated', 'Successfully deleted');
    }
}
