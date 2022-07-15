<?php

namespace App\Http\Controllers\TransactionRequest\v2;

use App\BranchOffers;
use App\BranchScanner;
use App\BranchUser;
use App\BranchUserNotification;
use App\BranchVoucher;
use App\CustomerInfo;
use App\CustomerTransactionRequest;
use App\Events\review_reply_notification;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Enum\GlobalTexts;
use App\Http\Controllers\Enum\LikerType;
use App\Http\Controllers\Enum\PartnerBranchNotificationType;
use App\Http\Controllers\Enum\PartnerRequestType;
use App\Http\Controllers\Enum\PlatformType;
use App\Http\Controllers\Enum\TransactionRequestStatus;
use App\Http\Controllers\jsonController;
use App\Http\Controllers\PartnerBranchController;
use App\Http\Controllers\TransactionRequest\functionController;
use App\Http\Controllers\TransactionRequest\v2\functionController as merchantFunctionController;
use App\Http\Controllers\Voucher\functionController as voucherFunctionController;
use App\LeaderboardPrizes;
use App\PartnerBranch;
use App\Review;
use App\ReviewComment;
use App\ScannerPrizeHistory;
use App\ScannerPrizes;
use App\ScannerReward;
use App\TransactionTable;
use App\VoucherPayment;
use App\VoucherPurchaseDetails;
use App\Wish;
use DateTime;
use DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;

class webController extends Controller
{
    public function remainingDays($exp_date)
    {
        $curDate = date('Y-m-d');
        $cur_date = new DateTime($curDate);
        $expiry_date = new DateTime($exp_date);
        $interval = date_diff($cur_date, $expiry_date);
        $daysRemaining = $interval->format('%R%a');

        return $daysRemaining;
    }

    /**
     * Login for branch user.
     *
     * @param
     * @return mixed
     */
    public function login(Request $request)
    {
        $this->validate($request, [
            'phone'  => 'required|numeric',
            'pin'  => 'required|numeric',
        ]);
        $request->flashOnly(['phone', 'pin']);
        $phone = '+88'.$request->post('phone');
        $pin = $request->post('pin');
        $user = BranchUser::where([['phone', $phone], ['pin_code', $pin]])
            ->with('branchScanner.branch.info')->first();

        if (! $user) {
            return redirect()->back()->with('login_error', 'Wrong phone number or PIN');
        } elseif ($this->remainingDays($user->branchScanner->branch->info->expiry_date) <= 0) {
            return redirect()->back()->with('login_error', GlobalTexts::partner_account_expired_login_msg);
        } elseif ($user->active == 0 || $user->branchScanner->branch->active == 0 || $user->branchScanner->branch->info->account->active == 0) {
            return redirect()->back()->with('login_error', GlobalTexts::merchant_end_deactivated_msg);
        } else {
            session(['branch_id' => $user->branchScanner->branch->id]);
            session(['branch_user_id' => $user->id]);
            session(['branch_user_username_v2' => $user->username]);
            session(['branch_user_role' => $user->role]);
            session(['branch_user_full_name' => $user->branchScanner->full_name]);
            session(['branch_area' => $user->branchScanner->branch->partner_area]);
            session(['partner_pro_img' => $user->branchScanner->branch->info->profileImage->partner_profile_image]);

            return redirect('partner/branch/requests');

            // if ($user->role > 100){
            //     return redirect('partner/branch/dashboard');
            // }else{
            //     return redirect('partner/branch/requests');
            // }
        }
    }

    /**
     * Dashboard of merchant.
     *
     * @param
     * @return
     */
    public function dashboard()
    {
        $id = session('branch_user_id');
        $user = BranchUser::where('id', $id)->with('branchScanner.scannerReward')->first();
        $data = (new merchantFunctionController())->getDashboardMetrics($user->branchScanner->branch_id);
        $point = $user->branchScanner->scannerReward->point;

        $top_transactors = (new merchantFunctionController())->getTopTransactors($user->branchScanner->branch_id, 5);
        $reviews = (new \App\Http\Controllers\Review\functionController())->getReviews(session('branch_id'), null, LikerType::partner);
        $reviews = $reviews->take(5);
        $leaderBoard = (new PartnerBranchController())->leaderBoardData();
        $leaderBoard = array_slice($leaderBoard, 0, 5);
        $allNotifications = $this->getAllNotifications($id);

        return view('partner-dashboard.dashboard', compact('allNotifications', 'point', 'data',
            'top_transactors', 'reviews', 'leaderBoard'));
    }

    /**
     * All top customers.
     */
    public function allTopCustomers()
    {
        $id = session('branch_user_id');
        $user = BranchUser::where('id', $id)->with('branchScanner.scannerReward')->first();
        $data = (new merchantFunctionController())->getDashboardMetrics($user->branchScanner->branch_id);
        $point = $user->branchScanner->scannerReward->point;

        $top_transactors = (new merchantFunctionController())->getTopTransactors($user->branchScanner->branch_id, 'all');
        $allNotifications = $this->getAllNotifications($id);

        return view('partner-dashboard.top_transactors', compact('allNotifications', 'point', 'data', 'top_transactors'));
    }

    public function getAllNotifications($branch_user_id)
    {
        $five_days_before = date('Y-m-d', strtotime('-6 days'));
//        $from = date('2020-06-01');
        $notifications = BranchUserNotification::where('branch_user_id', $branch_user_id)
            ->where('posted_on', '>', $five_days_before)
            ->orderBy('id', 'DESC')
            ->get();
        foreach ($notifications as $notification) {
            $customer = CustomerInfo::where('customer_id', $notification->customer_id)->first();
            $notification->image = $customer->customer_profile_image;
            $notification->customer_name = $customer->customer_full_name;
            if ($notification->notification_type == PartnerBranchNotificationType::TRANSACTION_REQUEST) {
                $request = CustomerTransactionRequest::where('id', $notification->source_id)->with('offer', 'redeem')->first();
                $request->offer->date_duration = $request->offer->date_duration[0];
                $request->offer->weekdays = $request->offer->weekdays[0];
                $notification->request = $request;
            }
        }
//        $notifications->unseen_count = BranchUserNotification::unseenCount($branch_user_id);
        $notifications->unseen_count = $notifications->where('seen', 0)->count();

        return $notifications;
    }

    public function getTransactionRequests()
    {
        $allNotifications = $this->getAllNotifications(\session('branch_user_id'));
        $tran_notifications = $allNotifications->where('notification_type', PartnerBranchNotificationType::TRANSACTION_REQUEST);
        $tran_notifications = $tran_notifications->where('request.status', 0);
        $tran_notifications = array_values(json_decode(json_encode($tran_notifications), true));
        return response()->json($tran_notifications, 200);
    }

    public function viewTransactionNotification($id)
    {
        $notification = BranchUserNotification::find($id);
        $notification->seen = 1;
        $notification->save();
        if ($notification->transactionRequest->status == TransactionRequestStatus::PENDING) {
            return redirect('partner/branch/requests');
        } else {
            return redirect('partner/branch/transactions');
        }
    }

    public function viewPostLikeNotification($id)
    {
        $notification = BranchUserNotification::find($id);
        $notification->seen = 1;
        $notification->save();

        return redirect('partner/branch/post/'.$notification->likedPost->post_id);
    }

    public function viewReviewNotification($id)
    {
        $notification = BranchUserNotification::find($id);
        $notification->seen = 1;
        $notification->save();
        $user = BranchUser::where('id', session('branch_user_id'))->with('branchScanner.scannerReward')->first();
        $point = $user->branchScanner->scannerReward->point;

        $reviews = (new \App\Http\Controllers\Review\functionController())->getReview(session('branch_id'), $notification->review->id);
        $reviews = (new \App\Http\Controllers\functionController2())->getPaginatedData($reviews, 10);

        $allNotifications = (new self())->getAllNotifications(session('branch_user_id'));
        $all_tab = true;

        return view('partner-dashboard.review', compact('allNotifications', 'point', 'reviews', 'all_tab'));
    }

    public function offerAvailedNotification($id)
    {
        $notification = BranchUserNotification::find($id);
        $notification->seen = 1;
        $notification->save();

        return redirect('partner/branch/transactions');
    }

    public function dealAvailedNotification($id)
    {
        $notification = BranchUserNotification::find($id);
        $notification->seen = 1;
        $notification->save();

        return redirect('partner/branch/deal_purchased');
    }

    public function viewAllNotifications()
    {
        BranchUserNotification::where('seen', 0)->update(['seen' => 1]);
        $allNotifications = $this->getAllNotifications(\session('branch_user_id'));
        $user = BranchUser::where('id', session('branch_user_id'))->with('branchScanner.scannerReward')->first();
        $point = $user->branchScanner->scannerReward->point;

        return view('partner-dashboard.all_notifications', compact('allNotifications', 'point'));
    }

    public function getNotificationViewForPusher()
    {
        $allNotifications = $this->getAllNotifications(\session('branch_user_id'));
        $notification_view = (new \App\Http\Controllers\TransactionRequest\v2\functionController())->getNotificationView($allNotifications);

        return \response()->json(['notification_view'=>$notification_view, 'unseen_notification'=>$allNotifications->unseen_count]);
    }

    /**
     * Get all notification of a branch.
     *
     * @param
     * @return Response
     */
    public function get_all_requests()
    {
        $id = session('branch_user_id');
        $allNotifications = $this->getAllNotifications($id);
        $five_days_before = date('Y-m-d', strtotime('-6 days'));
        $tran_notifications = $allNotifications->where('notification_type', PartnerBranchNotificationType::TRANSACTION_REQUEST)
            ->where('posted_on', '>', $five_days_before);
        $user = BranchUser::where('id', $id)
            ->with('branchScanner.branch.info.profileImage', 'branchScanner.branch.info.discount', 'branchScanner.scannerReward')
            ->first();
        $point = $user->branchScanner->scannerReward->point;
//        $notification_count = (new functionController())->merchantNotificationCount(session('branch_id'));
//        $allNotifications = $this->getNotificationView($allNotifications);
        return view('partner-dashboard.index', compact('allNotifications', 'tran_notifications', 'point'));
    }

    public function allOffers()
    {
        $id = session('branch_user_id');
        $scanner = BranchScanner::where('id', $id)->with('branch.offers')->first();
        $date = date('d-m-Y');
        //offers
        $sorted_offers = $scanner->branch->offers;
        foreach ($sorted_offers as $branch_offer) {
            if (
                new DateTime($branch_offer['date_duration'][0]['from']) <= new DateTime($date)
                && new DateTime($branch_offer['date_duration'][0]['to']) >= new DateTime($date)
            ) {
                $branch_offer['expired'] = false;
            } else {
                $branch_offer['expired'] = true;
            }
            $offer_use_count = TransactionTable::where('offer_id', $branch_offer->id)->count();
            $branch_offer['offer_use_count'] = $offer_use_count;
        }
        $sorted_offers = $sorted_offers->sortBy('expired');
        $user = BranchUser::where('id', $id)->with('branchScanner.scannerReward')->first();
        $point = $user->branchScanner->scannerReward->point;
        $allNotifications = $this->getAllNotifications($id);

        return view('partner-dashboard.offers', compact('allNotifications', 'sorted_offers',
            'point'));
    }

    public function allRewards()
    {
        $id = session('branch_user_id');
        $scanner = BranchScanner::where('id', $id)->with('branch.rewards.rewardRedeems')->first();
        $date = date('d-m-Y');
        //rewards
        $sorted_rewards = $scanner->branch->rewards;
        foreach ($sorted_rewards as $branch_offer) {
            if (
                new DateTime($branch_offer['date_duration'][0]['from']) <= new DateTime($date)
                && new DateTime($branch_offer['date_duration'][0]['to']) >= new DateTime($date)
            ) {
                $branch_offer['expired'] = false;
            } else {
                $branch_offer['expired'] = true;
            }
            $branch_offer['offer_use_count'] = $branch_offer->rewardRedeems->sum('quantity');
        }
        $sorted_rewards = $sorted_rewards->sortBy('expired');
        $user = BranchUser::where('id', $id)->with('branchScanner.scannerReward')->first();
        $point = $user->branchScanner->scannerReward->point;
        $payment_info = (new \App\Http\Controllers\Reward\functionController())->branchPayments(session('branch_id'));

        $allNotifications = $this->getAllNotifications($id);

        return view('partner-dashboard.rewards', compact(
            'allNotifications', 'sorted_rewards', 'point', 'payment_info'));
    }

    public function allDeals()
    {
        $id = session('branch_user_id');
        $scanner = BranchScanner::where('id', $id)->with('branch.rewards.rewardRedeems')->first();
        $user = BranchUser::where('id', $id)->with('branchScanner.scannerReward')->first();
        $point = $user->branchScanner->scannerReward->point;

        $branch_vouchers = (new \App\Http\Controllers\Voucher\functionController())->merchantDealList($scanner->branch->id);
        $payment_details = VoucherPayment::where('branch_id', $scanner->branch->id)->with('paidHistory')->first();
        $allNotifications = $this->getAllNotifications($id);

        return view('partner-dashboard.deals', compact('allNotifications', 'point', 'branch_vouchers', 'payment_details'));
    }

    public function dealPurchased()
    {
        $id = session('branch_user_id');
        $scanner = BranchScanner::where('id', $id)->with('branch.rewards.rewardRedeems')->first();
        $user = BranchUser::where('id', $id)->with('branchScanner.scannerReward')->first();
        $point = $user->branchScanner->scannerReward->point;

        $transactions = (new \App\Http\Controllers\Voucher\functionController())->branchDealPurchased($scanner->branch->id, false, null);
        $year = $month = null;
        $allNotifications = $this->getAllNotifications($id);

        return view('partner-dashboard.deal_transactions', compact('allNotifications', 'point', 'transactions', 'year', 'month'));
    }

    public function sortDealPurchased(Request $request)
    {
        $year = $request->get('year');
        $month = $request->get('month');
        if ($year != null && $month != null) {
            $sel_month = $year.'-'.$month;
        } elseif ($year != null && $month == null) {
            $sel_month = $year;
        } else {
            return redirect()->back()->with('error', 'Please select value.');
        }

        $id = session('branch_user_id');
        $scanner = BranchScanner::where('id', $id)->with('branch.rewards.rewardRedeems')->first();
        $user = BranchUser::where('id', $id)->with('branchScanner.scannerReward')->first();
        $point = $user->branchScanner->scannerReward->point;

        $transactions = (new \App\Http\Controllers\Voucher\functionController())->branchDealPurchased($scanner->branch->id, true, $sel_month);

        $allNotifications = $this->getAllNotifications($id);

        return view('partner-dashboard.deal_transactions', compact('allNotifications', 'point', 'transactions', 'year', 'month'));
    }

    public function dealPaymentHistory()
    {
        $id = session('branch_user_id');
        $scanner = BranchScanner::where('id', $id)->with('branch.rewards.rewardRedeems')->first();
        $user = BranchUser::where('id', $id)->with('branchScanner.scannerReward')->first();
        $point = $user->branchScanner->scannerReward->point;

        $payment_details = (new voucherFunctionController())->dealPaymentHistory($scanner->branch->id);

        $allNotifications = $this->getAllNotifications($id);

        return view('partner-dashboard.deal_payments', compact('allNotifications', 'point', 'payment_details'));
    }

    public function branchProfile()
    {
        $id = session('branch_user_id');
        $user = BranchUser::where('id', $id)
            ->with('branchScanner.branch.info.profileImage', 'branchScanner.branch.info.discount', 'branchScanner.scannerReward')
            ->first();
        $data = (new merchantFunctionController())->getDashboardMetrics($user->branchScanner->branch_id);

        $point = $user->branchScanner->scannerReward->point;
        $allNotifications = $this->getAllNotifications($id);

        return view('partner-dashboard.profile', compact('allNotifications', 'user', 'point', 'data'));
    }

    /**
     * Update transaction request status.
     *
     * @param RequestID, Status
     * @return RedirectResponse
     */
    public function update_request_status($notification_id, $requestID, $status)
    {
        BranchUserNotification::where('id', $notification_id)->update(['seen' => 1]);
        $request = (new functionController)
            ->updateTransactionRequest(
                $requestID,
                $status,
                session('branch_user_id'),
                false,
                PlatformType::web
            );
        if ($request->status == TransactionRequestStatus::ACCEPTED) {
            return redirect('partner/branch/requests')->with('accepted', 'Transaction successful.');
        } elseif ($request->status == TransactionRequestStatus::DECLINED) {
            (new \App\Http\Controllers\AdminNotification\functionController())
                ->transactionRequestRejectNotification($request);

            return redirect('partner/branch/requests')->with('rejected', 'Transaction rejected.');
        }
    }

    /**
     * Get transaction history of this branch.
     * @param BranchUserId
     * @return Response
     */
    public function all_transactions()
    {
        $id = session('branch_user_id');
        $user = BranchUser::where('id', $id)->with('branchScanner.branch')->first();
        $point = $user->branchScanner->scannerReward->point;

        $transactions = DB::table('transaction_table as tt')
            ->leftJoin('customer_info as ci', 'ci.customer_id', '=', 'tt.customer_id')
            ->leftJoin('branch_user as bu', 'bu.id', '=', 'tt.branch_user_id')
            ->leftJoin('branch_scanner as bs', 'bu.id', '=', 'bs.branch_user_id')
            ->leftJoin('customer_reward_redeems as crr', 'crr.id', '=', 'tt.redeem_id')
            ->select(
                'tt.customer_id',
                'tt.amount_spent',
                'tt.posted_on',
                'tt.discount_amount',
                'bs.full_name',
                'ci.customer_full_name',
                'ci.customer_profile_image',
                'tt.offer_id',
                'crr.quantity'
            )
            ->where('tt.branch_id', $user->branchScanner->branch->id)
            ->where('tt.deleted_at', null)
            ->orderBy('tt.posted_on', 'DESC')
            ->get();
        $transactions = json_decode(json_encode($transactions), true);
        $i = 0;
        foreach ($transactions as $transaction) {
            if ($transaction['offer_id'] != null && $transaction['offer_id'] != 0) {
                $offer = BranchOffers::where('id', $transaction['offer_id'])->first();
                if ($offer) {
                    if ($offer->selling_point == null) {
                        $transactions[$i]['offer_status'] = 'Offer';
                    } else {
                        $transactions[$i]['offer_status'] = 'Reward';
                    }
                    $transactions[$i]['offer_description'] = $offer->offer_description;
                } else {
                    $transactions[$i]['offer_status'] = '';
                    $transactions[$i]['offer_description'] = 'Not found';
                }
            } else {
                $transactions[$i]['offer_status'] = '';
                $transactions[$i]['offer_description'] = 'Discount';
            }
            $i++;
        }
        $year = $month = null;
        $notification_count = (new functionController())->merchantNotificationCount(session('branch_id'));
        $allNotifications = $this->getAllNotifications($id);

        return view('partner-dashboard.transactionHistory', compact(
            'allNotifications', 'transactions', 'point',
            'notification_count', 'year', 'month'));
    }

    public function sort_all_transactions(Request $request)
    {
        $year = $request->get('year');
        $month = $request->get('month');
        if ($year != null && $month != null) {
            $sel_month = $year.'-'.$month;
        } elseif ($year != null && $month == null) {
            $sel_month = $year;
        } else {
            return redirect()->back()->with('error', 'Please select value.');
        }
        $id = session('branch_user_id');
        $user = BranchUser::where('id', $id)->with('branchScanner.branch')->first();
        $point = $user->branchScanner->scannerReward->point;

        $transactions = DB::table('transaction_table as tt')
            ->leftJoin('customer_info as ci', 'ci.customer_id', '=', 'tt.customer_id')
            ->leftJoin('branch_user as bu', 'bu.id', '=', 'tt.branch_user_id')
            ->leftJoin('branch_scanner as bs', 'bu.id', '=', 'bs.branch_user_id')
            ->leftJoin('customer_reward_redeems as crr', 'crr.id', '=', 'tt.redeem_id')
            ->select(
                'tt.customer_id',
                'tt.amount_spent',
                'tt.posted_on',
                'tt.discount_amount',
                'bs.full_name',
                'ci.customer_full_name',
                'ci.customer_profile_image',
                'tt.offer_id',
                'crr.quantity'
            )
            ->where('tt.branch_id', $user->branchScanner->branch->id)
            ->where('tt.posted_on', 'like', $sel_month.'%')
            ->where('tt.deleted_at', null)
            ->orderBy('tt.posted_on', 'DESC')
            ->get();
        $transactions = json_decode(json_encode($transactions), true);
        $i = 0;
        foreach ($transactions as $transaction) {
            if ($transaction['offer_id'] != null && $transaction['offer_id'] != 0) {
                $offer = BranchOffers::where('id', $transaction['offer_id'])->first();
                if ($offer) {
                    if ($offer->selling_point == null) {
                        $transactions[$i]['offer_status'] = 'Offer';
                    } else {
                        $transactions[$i]['offer_status'] = 'Reward';
                    }
                    $transactions[$i]['offer_description'] = $offer->offer_description;
                } else {
                    $transactions[$i]['offer_status'] = '';
                    $transactions[$i]['offer_description'] = 'Not found';
                }
            } else {
                $transactions[$i]['offer_status'] = '';
                $transactions[$i]['offer_description'] = 'Discount';
            }
            $i++;
        }
        $notification_count = (new functionController())->merchantNotificationCount(session('branch_id'));
        $allNotifications = $this->getAllNotifications($id);

        return view('partner-dashboard.transactionHistory', compact('allNotifications', 'transactions', 'point',
            'notification_count', 'year', 'month'));
    }

    /**
     * Get transaction history of this branch.
     * @param BranchUserId
     * @return Response
     */
    public function sort_transaction_request(Request $request)
    {
        $year = $request->post('year');
        $month = $request->post('month');

        $notifications = BranchUserNotification::where('branch_user_id', session('branch_user_id'))
            ->with('branchUser')->orderBy('id', 'DESC')->get();
        foreach ($notifications as $notification) {
            $customer = CustomerInfo::where('customer_id', $notification->customer_id)->first();
            $notification->image = $customer->customer_profile_image;
            $notification->customer_name = $customer->customer_full_name;
            if ($notification->notification_type == PartnerBranchNotificationType::TRANSACTION_REQUEST) {
                $request = CustomerTransactionRequest::where([['id', $notification->source_id], ['status', 1]])
                    ->with('offer', 'customerInfo')->first();
                $notification->request = $request;
            }
        }

        return view('transactionRequest.transactionHistory', compact('notifications'));
    }

    public function pointPrizes()
    {
        $prizes = ScannerPrizes::all();
        $scanner = ScannerReward::where('scanner_id', session('branch_user_id'))->first();
        $point = $scanner->point;
        $notification_count = (new functionController())->merchantNotificationCount(session('branch_id'));
        $allNotifications = $this->getAllNotifications(\session('branch_user_id'));

        return view('partner-dashboard.pointPrizes', compact('allNotifications',
            'prizes', 'scanner', 'point', 'notification_count'));
    }

    public function requestScanPrize(Request $request)
    {
        $id = $request->post('prize');
        $comment = $request->post('comment') ? $request->post('comment') : null;
        $prize = ScannerPrizes::where('id', $id)->first();

        $user = BranchUser::where('id', session('branch_user_id'))->with('branchScanner')->first();
        try {
            \DB::beginTransaction();
            $scanner_prize = new ScannerPrizeHistory([
                'text' => $prize->text,
                'point' => $prize->point,
                'scanner_id' => $user->branchScanner->id,
                'status' => 0,
                'posted_on' => date('Y-m-d H:i:s'),
                'request_comment' => $comment,
            ]);
            $scanner_prize->save();
            $scanner_reward = ScannerReward::where('scanner_id', $user->branchScanner->id)->first();
            $scanner_reward->decrement('point', $prize->point);
            $scanner_reward->increment('point_used', $prize->point);
            (new \App\Http\Controllers\AdminNotification\functionController())->newScannerRequestNotification($scanner_prize);

            \DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            \DB::rollBack(); //rollback all successfully executed queries
            return redirect()->back()->with('try_again', 'Please try again!');
        }

        return redirect('partner/branch/scanner_prize_history/')->with('success', 'Your request has been accepted. You will get a call from our team shortly.');
    }

    public function scannerPrizeHistory()
    {
        $user = BranchUser::where('id', session('branch_user_id'))->with('branchScanner.scannerReward')->first();
        $scanner_prize_history = ScannerPrizeHistory::where('scanner_id', $user->branchScanner->id)->orderBy('posted_on', 'DESC')->get();
        $point = $user->branchScanner->scannerReward->point;
        $notification_count = (new functionController())->merchantNotificationCount(session('branch_id'));
        $allNotifications = $this->getAllNotifications(session('branch_user_id'));

        return view('partner-dashboard.scannerPrizeHistory', compact('allNotifications',
            'scanner_prize_history', 'point', 'notification_count'));
    }

    public function scannerLeaderboard()
    {
        $leaderBoard = (new PartnerBranchController())->leaderBoardData();
        $user = BranchUser::where('id', session('branch_user_id'))->with('branchScanner.scannerReward')->first();
        $point = $user->branchScanner->scannerReward->point;
        $prize = LeaderboardPrizes::where('month', date('m'))->first();
        $notification_count = (new functionController())->merchantNotificationCount(session('branch_id'));
        $allNotifications = $this->getAllNotifications(\session('branch_user_id'));

        return view('partner-dashboard.scannerLeaderBoard', compact('allNotifications', 'leaderBoard', 'point', 'prize', 'notification_count'));
    }

    public function howItWorks()
    {
        $user = BranchUser::where('id', session('branch_user_id'))->with('branchScanner.scannerReward')->first();
        $point = $user->branchScanner->scannerReward->point;
        $allNotifications = $this->getAllNotifications(\session('branch_user_id'));
        //        $notification_count = (new functionController())->merchantNotificationCount(session('branch_id'));
        return view('partner-dashboard.how_it_works', compact('allNotifications', 'point'));
    }

    public function transactionStatistics()
    {
        $user = BranchUser::where('id', session('branch_user_id'))->with('branchScanner.scannerReward')->first();
        $point = $user->branchScanner->scannerReward->point;
        $allNotifications = $this->getAllNotifications(\session('branch_user_id'));

        return view('partner-dashboard.analytics.transaction', compact('allNotifications', 'point'));
    }

    public function profileVisit()
    {
        $user = BranchUser::where('id', session('branch_user_id'))->with('branchScanner.scannerReward')->first();
        $point = $user->branchScanner->scannerReward->point;
        $allNotifications = $this->getAllNotifications(\session('branch_user_id'));

        return view('partner-dashboard.analytics.profileVisit', compact('allNotifications', 'point'));
    }

    public function peakHour()
    {
        $user = BranchUser::where('id', session('branch_user_id'))->with('branchScanner.scannerReward')->first();
        $point = $user->branchScanner->scannerReward->point;
        $allNotifications = $this->getAllNotifications(\session('branch_user_id'));

        return view('partner-dashboard.analytics.peakhour', compact('allNotifications', 'point'));
    }

    public function allReviews()
    {
        $user = BranchUser::where('id', session('branch_user_id'))->with('branchScanner.scannerReward')->first();
        $point = $user->branchScanner->scannerReward->point;
        $reviews = (new \App\Http\Controllers\Review\functionController())->getReviews(session('branch_id'), null, LikerType::partner);
        //        $ratings = (new \App\Http\Controllers\Review\functionController())->getRatings(session('branch_id'));

        $reviews = (new \App\Http\Controllers\functionController2())->getPaginatedData($reviews, 10);
        $allNotifications = (new self())->getAllNotifications(session('branch_user_id'));
        $all_tab = false;

        return view('partner-dashboard.review', compact('allNotifications', 'point', 'reviews', 'all_tab'));
    }

    public function replyReview(Request $request)
    {
        $this->validate($request, [
            'reply' => 'required',
        ]);
        $reply = $request->get('reply');
        $review_id = $request->get('review_id');

        if (strlen($reply) > 500) {
            return redirect()->back()->with('try_again', 'Please try again!');
        }

        //check if this review exists or not
        $review_exists = Review::where('id', $review_id)->first();
        if ($review_exists) {
            $reply = (new \App\Http\Controllers\Review\functionController())->replyToReview($review_id, $reply);
            $msg = $review_exists->partnerInfo->partner_name.' has replied to a review of '.$review_exists->customer->customer_full_name;
            (new \App\Http\Controllers\AdminNotification\functionController())->reviewReplyNotification($msg, $reply->id);
        } else {
            return redirect()->back()->with('review_does_not_exist', 'Review does not exist');
        }
        //back to review page
        return redirect('partner/branch/review')->with('reply_success', 'Your reply to this review is under moderation.
            After moderation it will be posted on your profile.');
    }

    public function editReviewReply(Request $request, $id)
    {
        $this->validate($request, [
            'reply' => 'required',
        ]);
        $reply = $request->get('reply');
        (new \App\Http\Controllers\Review\functionController())->editReviewReply($id, $reply);

        return redirect()->back()->with('reply_success', 'Your reply edited successfully.');
    }

    public function deleteReviewReply($id)
    {
        (new \App\Http\Controllers\Review\functionController())->deleteReviewReply($id);

        return redirect()->back()->with('reply_success', 'Your reply deleted successfully.');
    }

    public function likeReview(Request $request)
    {
        $review_id = $request->input('review_id');
        (new \App\Http\Controllers\Review\functionController())->likeReview(session('branch_id'), LikerType::partner, $review_id);
    }

    public function unlikeReview(Request $request)
    {
        $like_id = $request->input('source_id');

        (new \App\Http\Controllers\Review\functionController())->unlikeReview($like_id);
    }

    public function offerRequestView()
    {
        $user = BranchUser::where('id', session('branch_user_id'))->with('branchScanner.scannerReward')->first();
        $point = $user->branchScanner->scannerReward->point;
        $allNotifications = (new self())->getAllNotifications(session('branch_user_id'));

        return view('partner-dashboard.offer_request', compact('allNotifications', 'point'));
    }

    public function storeOfferRequest(Request $request)
    {
        $this->validate($request, [
            'comment' => 'required',
        ]);
        $request->flashOnly('comment');
        $comment = $request->get('comment');
        (new merchantFunctionController())->addPartnerRequest(\session('branch_user_id'), $comment,
            PartnerRequestType::offer_request);

        return redirect()->back()->with(['success' => 'We got your request. Our team will contact you soon.']);
    }

    public function setLocale($lang)
    {
        \session(['getLocale' => $lang]);

        return back();
    }

    /**
     * Logout existing branch user.
     */
    public function logout(Request $request)
    {
        $request->session()->flush();

        return redirect('/partner');
    }
}
