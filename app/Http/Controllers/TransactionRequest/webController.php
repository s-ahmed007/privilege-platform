<?php

namespace App\Http\Controllers\TransactionRequest;

use App\BranchOffers;
use App\BranchScanner;
use App\BranchUser;
use App\BranchUserNotification;
use App\CustomerInfo;
use App\CustomerTransactionRequest;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Enum\PartnerBranchNotificationType;
use App\Http\Controllers\Enum\PlatformType;
use App\Http\Controllers\Enum\TransactionRequestStatus;
use App\Http\Controllers\PartnerBranchController;
use App\LeaderboardPrizes;
use App\ScannerPrizeHistory;
use App\ScannerPrizes;
use App\ScannerReward;
use App\TransactionTable;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class webController extends Controller
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Login for branch user.
     *
     * @param
     * @return Response
     */
    public function login()
    {
        $this->validate($this->request, [
            'phone' => 'required|numeric',
            'pin' => 'required|numeric',
        ]);
        $this->request->flashOnly(['phone', 'pin']);
        $phone = '+880'.$this->request->post('phone');
        $pin = $this->request->post('pin');
        $user = BranchUser::where([['phone', $phone], ['pin_code', $pin]])
            ->with('branchScanner.branch.info')->first();
        if ($user) {
            session(['branch_id' => $user->branchScanner->branch->id]);
            session(['branch_user_id' => $user->id]);
            session(['branch_user_username' => $user->username]);
            session(['branch_user_role' => $user->role]);
            session(['branch_user_full_name' => $user->branchScanner->full_name]);
            session(['partner_name' => $user->branchScanner->branch->info->partner_name]);
            session(['branch_area' => $user->branchScanner->branch->partner_area]);
            session(['partner_pro_img' => $user->branchScanner->branch->info->profileImage->partner_profile_image]);

            return redirect('branch/requests');
        } else {
            return redirect()->back()->with('login_error', 'Wrong phone number or PIN');
        }
    }

    /**
     * Get all notification of a branch.
     *
     * @param BranchUserUsername
     * @return Response
     */
    public function get_all_requests()
    {
        $id = session('branch_user_id');
        $notifications = (new functionController)->getAllBranchUserTransactionNotification($id);
        $user = BranchUser::where('id', $id)
            ->with('branchScanner.branch.info.profileImage', 'branchScanner.branch.info.discount', 'branchScanner.scannerReward')
            ->first();
        //offers
        $sorted_offers = $user->branchScanner->branch->offers;
        $i = 0;
        foreach ($sorted_offers as $branch_offer) {
            $offer_use_count = TransactionTable::where('offer_id', $branch_offer->id)->count();
            $branch_offer['offer_use_count'] = $offer_use_count;
            $i++;
        }
        //rewards
        $sorted_rewards = $user->branchScanner->branch->rewards;
        $i = 0;
        foreach ($sorted_rewards as $branch_offer) {
            $offer_use_count = TransactionTable::where('offer_id', $branch_offer->id)->count();
            $branch_offer['offer_use_count'] = $offer_use_count;
            $i++;
        }
        $point = $user->branchScanner->scannerReward->point;
        $notification_count = (new functionController())->merchantNotificationCount(session('branch_id'));
        $payment_info = (new \App\Http\Controllers\Reward\functionController())->branchPayments(session('branch_id'));

        return view('transactionRequest.index', compact('notifications', 'user', 'sorted_offers', 'payment_info',
            'sorted_rewards', 'point', 'notification_count'));
    }

    /**
     * Update transaction request status.
     *
     * @param RequestID, Status
     * @return Response
     */
    public function update_request_status($notification_id, $requestID, $status)
    {
        BranchUserNotification::where('id', $notification_id)->update(['seen' => 1]);
        $request = (new functionController)->updateTransactionRequest($requestID, $status, session('branch_user_id'),
            false, PlatformType::web);
        if ($request->status == TransactionRequestStatus::ACCEPTED) {
            return redirect('branch/requests')->with('accepted', 'Transaction successful.');
        } elseif ($request->status == TransactionRequestStatus::DECLINED) {
            (new \App\Http\Controllers\AdminNotification\functionController())->transactionRequestRejectNotification($request);

            return redirect('branch/requests')->with('rejected', 'Transaction rejected.');
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
            ->select(
                'tt.customer_id',
                'tt.amount_spent',
                'tt.posted_on',
                'tt.discount_amount',
                'bs.full_name',
                'ci.customer_full_name',
                'ci.customer_profile_image',
                'tt.offer_id'
            )
            ->where('tt.branch_id', $user->branchScanner->branch->id)
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
                        $transactions[$i]['offer_description'] = $offer->offer_description;
                    } else {
                        unset($transactions[$i]);
//                        $transactions[$i]['offer_status'] = 'Reward';
                    }
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

        return view('transactionRequest.transactionHistory', compact('transactions', 'point', 'notification_count'));
    }

    /**
     * Get transaction history of this branch.
     * @param BranchUserId
     * @return Response
     */
    public function sort_transaction_request()
    {
        $year = $this->request->post('year');
        $month = $this->request->post('month');

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

        return view('transactionRequest.pointPrizes', compact('prizes', 'scanner', 'point', 'notification_count'));
    }

    public function requestScanPrize()
    {
        $id = $this->request->post('prize');
        $comment = $this->request->post('comment') ? $this->request->post('comment') : null;
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

        return redirect('branch/scanner_prize_history/')->with('success', 'Your request has been accepted. You will get a call from our team shortly.');
    }

    public function scannerPrizeHistory()
    {
        $user = BranchUser::where('id', session('branch_user_id'))->with('branchScanner.scannerReward')->first();
        $scanner_prize_history = ScannerPrizeHistory::where('scanner_id', $user->branchScanner->id)->orderBy('posted_on', 'DESC')->get();
        $point = $user->branchScanner->scannerReward->point;
        $notification_count = (new functionController())->merchantNotificationCount(session('branch_id'));

        return view('transactionRequest.scannerPrizeHistory', compact('scanner_prize_history', 'point', 'notification_count'));
    }

    public function scannerLeaderboard()
    {
        $leaderBoard = (new PartnerBranchController())->leaderBoardData();
        $user = BranchUser::where('id', session('branch_user_id'))->with('branchScanner.scannerReward')->first();
        $point = $user->branchScanner->scannerReward->point;
        $prize = LeaderboardPrizes::where('month', date('m'))->first();
        $notification_count = (new functionController())->merchantNotificationCount(session('branch_id'));

        return view('transactionRequest.scannerLeaderBoard', compact('leaderBoard', 'point', 'prize', 'notification_count'));
    }

    /**
     * Logout existing branch user.
     */
    public function logout()
    {
        $this->request->session()->flush();

        return redirect('/transaction-request');
    }
}
