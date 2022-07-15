<?php

namespace App\Http\Controllers\admin;

use App\BranchCreditRedeemed;
use App\BranchVoucher;
use App\CustomerInfo;
use App\CustomerNotification;
use App\CustomerPoint;
use App\Events\reward_notification;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Enum\DealRefundStatus;
use App\Http\Controllers\Enum\notificationType;
use App\Http\Controllers\Enum\PointType;
use App\Http\Controllers\jsonController;
use App\Http\Controllers\Voucher\functionController as voucherFunctionController;
use App\PartnerBranch;
use App\VoucherHistory;
use App\VoucherPayment;
use App\VoucherRefund;
use DateTime;
use DB;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $id = $_GET['branch_id'];
        $vouchers = [];
        $vouchers = BranchVoucher::where('branch_id', $id)->orderBy('id', 'DESC')->get();

        $branch_info = PartnerBranch::where('id', $id)->with('info')->first();

        return view('admin.production.vouchers.index', compact('vouchers', 'branch_info', 'id'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $branch_id = $_GET['id'];
        $partner_info = PartnerBranch::where('id', $branch_id)->with('info')->first();

        return view('admin.production.vouchers.create', compact('partner_info'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'date_from2' => 'required',
            'date_to2' => 'required',
            'heading' => 'required',
            'actual_price' => 'required',
            'discount_type' => 'required',
            'discount' => 'required',
            'selling_price' => 'required',
            'counter_limit' => 'required',
            'description' => 'required',
            'tnc' => 'required',
        ]);

        $request->flashOnly(['date_from2', 'date_to2', 'heading', 'actual_price', 'discount_type', 'discount', 'selling_price', 'counter_limit', 'description', 'tnc']);

        $branch_id = $request->get('branch_id');
        $date_from2 = date('d-m-Y', strtotime($request->get('date_from2')));
        $date_to2 = date('d-m-Y', strtotime($request->get('date_to2')));
        $heading = $request->get('heading');
        $points = $request->get('points');
        $actual_price = $request->get('actual_price') == null ? 0 : $request->get('actual_price');
        $discount_type = $request->get('discount_type');
        $discount = $request->get('discount');
        $selling_price = $request->get('selling_price') == null ? 0 : $request->get('selling_price');
        $counter_limit = $request->get('counter_limit');
        $scan_limit = $request->get('scan_limit');
        $description = $request->get('description');
        $tnc = $request->get('tnc');
        $commission_type = $request->get('commission_type');
        $commission = $request->get('commission');
        $valid_for = $request->get('valid_for');
        $redeem_duration = $request->get('redeem_duration');

        //weekdays
        $sat2 = ($request->get('sat2')) == null ? 0 : 1;
        $sun2 = ($request->get('sun2')) == null ? 0 : 1;
        $mon2 = ($request->get('mon2')) == null ? 0 : 1;
        $tue2 = ($request->get('tue2')) == null ? 0 : 1;
        $wed2 = ($request->get('wed2')) == null ? 0 : 1;
        $thu2 = ($request->get('thu2')) == null ? 0 : 1;
        $fri2 = ($request->get('fri2')) == null ? 0 : 1;

        //create json variable
        $week2[] = [
            'sat' => $sat2.'',
            'sun' => $sun2.'',
            'mon' => $mon2.'',
            'tue' => $tue2.'',
            'wed' => $wed2.'',
            'thu' => $thu2.'',
            'fri' => $fri2.'',
        ];
        //days
        $date2[] = [
            'from' => $date_from2,
            'to' => $date_to2,
        ];
        //hours
        $hour2 = [];
        if (isset($_POST['time_duration_from2']) && isset($_POST['time_duration_to2'])) {
            for ($i = 0; $i < count($_POST['time_duration_from2']); $i++) {
                if ($_POST['time_duration_from2'][$i] != '' && $_POST['time_duration_to2'][$i] != '') {
                    $hour2[] = [
                        'from' => $_POST['time_duration_from2'][$i],
                        'to' => $_POST['time_duration_to2'][$i],
                    ];
                }
            }
        } else {
            $hour2 = [];
        }

        $week2 = json_encode($week2);
        $date2 = json_encode($date2);
        $hour2 = json_encode($hour2);

        try {
            DB::beginTransaction(); //to do query rollback

            BranchVoucher::insert([
                'branch_id' => $branch_id,
                'date_duration' => $date2,
                'weekdays' => $week2,
                'time_duration' => $hour2,
                'point' => $points,
                'active' => 1,
                'heading' => $heading,
                'actual_price' => $actual_price,
                'selling_price' => $selling_price,
                'discount' => $discount,
                'discount_type' => $discount_type,
                'tnc' => $tnc,
                'description' => $description,
                'valid_for' => $valid_for,
                'priority' => 100,
                'counter_limit' => $counter_limit,
                'scan_limit' => $scan_limit,
                'commission_type' => $commission_type,
                'commission' => $commission,
                'redeem_duration' => $redeem_duration,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            if (VoucherPayment::where('branch_id', $branch_id)->count() == 0) {
                $voucher_payment = new VoucherPayment();
                $voucher_payment->branch_id = $branch_id;
                $voucher_payment->credit = 0;
                $voucher_payment->credit_used = 0;
                $voucher_payment->debit = 0;
                $voucher_payment->debit_used = 0;
                $voucher_payment->save();
            }

            $voucher = BranchVoucher::where('branch_id', $branch_id)->orderBy('id', 'DESC')->first();
            (new \App\Http\Controllers\AdminNotification\functionController())->newVoucherAddNotification($voucher);

            DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            DB::rollBack(); //rollback all successfully executed queries
            return redirect()->back()->with('try_again', 'Please try again!');
        }

        return redirect('/admin/vouchers/?branch_id='.$branch_id)->with('status', 'New deal added!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $voucher_details = BranchVoucher::where('id', $id)->first();

        $time_duration_count2 = 0;
        $time_to_array2 = [];
        $time_from_array2 = [];
        if (isset($voucher_details->time_duration)) {
            $time_duration_count2 = count($voucher_details->time_duration);
            $point_details2 = $voucher_details->time_duration;
            if ($time_duration_count2 > 0) {
                for ($i = 0; $i < $time_duration_count2; $i++) {
                    $time_to_array2[$i] = $point_details2[$i]['to'];
                    $time_from_array2[$i] = $point_details2[$i]['from'];
                }
            }
        }

        $partner_info = PartnerBranch::where('id', $voucher_details->branch_id)->with('info')->first();

        return view('admin.production.vouchers.edit', compact(
            'voucher_details',
            'partner_info',
            'time_duration_count2',
            'time_from_array2',
            'time_to_array2'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'date_from2' => 'required',
            'date_to2' => 'required',
            'heading' => 'required',
            'actual_price' => 'required',
            'discount_type' => 'required',
            'discount' => 'required',
            'selling_price' => 'required',
            'counter_limit' => 'required',
            'description' => 'required',
            'tnc' => 'required',
        ]);

        $request->flashOnly(['date_from2', 'date_to2', 'heading', 'actual_price', 'discount_type', 'discount', 'selling_price', 'counter_limit', 'description', 'tnc']);

        $date_from2 = date('d-m-Y', strtotime($request->get('date_from2')));
        $date_to2 = date('d-m-Y', strtotime($request->get('date_to2')));
        $heading = $request->get('heading');
        $points = $request->get('points');
        $actual_price = $request->get('actual_price') == null ? 0 : $request->get('actual_price');
        $discount_type = $request->get('discount_type');
        $discount = $request->get('discount');
        $selling_price = $request->get('selling_price') == null ? 0 : $request->get('selling_price');
        $counter_limit = $request->get('counter_limit');
        $scan_limit = $request->get('scan_limit');
        $description = $request->get('description');
        $tnc = $request->get('tnc');
        $commission_type = $request->get('commission_type');
        $commission = $request->get('commission');
        $priority = $request->get('priority');
        $counter_limit = $request->get('counter_limit');
        $valid_for = $request->get('valid_for');
        $redeem_duration = $request->get('redeem_duration');

        //weekdays
        $sat2 = ($request->get('sat2')) == null ? 0 : 1;
        $sun2 = ($request->get('sun2')) == null ? 0 : 1;
        $mon2 = ($request->get('mon2')) == null ? 0 : 1;
        $tue2 = ($request->get('tue2')) == null ? 0 : 1;
        $wed2 = ($request->get('wed2')) == null ? 0 : 1;
        $thu2 = ($request->get('thu2')) == null ? 0 : 1;
        $fri2 = ($request->get('fri2')) == null ? 0 : 1;

        //create json variable
        $week2[] = [
            'sat' => $sat2.'',
            'sun' => $sun2.'',
            'mon' => $mon2.'',
            'tue' => $tue2.'',
            'wed' => $wed2.'',
            'thu' => $thu2.'',
            'fri' => $fri2.'',
        ];
        $date2[] = [
            'from' => $date_from2,
            'to' => $date_to2,
        ];

        $hour2 = [];
        if (isset($_POST['time_duration_from2']) && isset($_POST['time_duration_to2'])) {
            for ($i = 0; $i < count($_POST['time_duration_from2']); $i++) {
                if ($_POST['time_duration_from2'][$i] != '' && $_POST['time_duration_to2'][$i] != '') {
                    $hour2[] = [
                        'from' => $_POST['time_duration_from2'][$i],
                        'to' => $_POST['time_duration_to2'][$i],
                    ];
                }
            }
        } else {
            $hour2 = [];
        }
        // $week2 = json_encode($week2);
        // $date2 = json_encode($date2);
        // $hour2 = json_encode($hour2);
        // dd($week2, $date2, $hour2);

        try {
            DB::beginTransaction(); //to do query rollback

            $voucher = BranchVoucher::findOrFail($id);
            $voucher->date_duration = $date2;
            $voucher->weekdays = $week2;
            $voucher->time_duration = $hour2;
            $voucher->point = $points;
            $voucher->heading = $heading;
            $voucher->actual_price = $actual_price;
            $voucher->selling_price = $selling_price;
            $voucher->discount = $discount;
            $voucher->discount_type = $discount_type;
            $voucher->tnc = $tnc;
            $voucher->description = $description;
            $voucher->valid_for = $valid_for;
            $voucher->priority = $priority;
            $voucher->counter_limit = $counter_limit;
            $voucher->scan_limit = $scan_limit;
            $voucher->commission_type = $commission_type;
            $voucher->commission = $commission;
            $voucher->redeem_duration = $redeem_duration;
            $voucher->save();

            DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            DB::rollBack(); //rollback all successfully executed queries
            return redirect()->back()->with('try_again', 'Please try again!');
        }

        return redirect($request->get('redirect_url'))->with('status', 'Voucher updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $canDelete = DB::table('voucher_purchase_details as vpd')
                ->join('voucher_ssl_info as vsi', 'vsi.id', '=', 'vpd.ssl_id')
                ->where('vsi.status', 1)
                ->where('vpd.voucher_id', $id)
                ->count();

        if ($canDelete > 0) {
            return redirect()->back()->with('delete', 'You can not delete this deal.');
        } else {
            $voucher = BranchVoucher::find($id);
            $voucher->delete();

            return redirect()->back()->with('status', 'Voucher deleted successfully!');
        }
    }

    /**
     * @description Change voucher active status
     */
    public function changeVoucherStatus($id)
    {
        $voucher = BranchVoucher::find($id);

        $status = $voucher->active == 1 ? 0 : 1;

        $voucher->active = $status;
        $voucher->save();

        return redirect()->back()->with('status', 'Status changed successfully!');
    }

    /**
     * @description Show all deals page
     */
    public function allDeals($type)
    {
        $vouchers = BranchVoucher::orderBy('id', 'DESC')->get();

        return view('admin.production.vouchers.all_vouchers', compact('vouchers'));
    }

    /**
     * @description Show all purchased deals page
     */
    public function purchasedDeals($type)
    {
        $data = DB::table('voucher_history as vh')
                ->join('customer_info as ci', 'ci.customer_id', '=', 'vh.customer_id')
                ->join('partner_branch as pb', 'pb.id', '=', 'vh.branch_id')
                ->join('partner_info as pi', 'pi.partner_account_id', '=', 'pb.partner_account_id')
                ->join('voucher_purchase_details as vpd', 'vpd.ssl_id', '=', 'vh.ssl_id')
                ->join('branch_vouchers as bv', 'bv.id', '=', 'vpd.voucher_id')
                ->join('voucher_ssl_info as vsi', 'vsi.id', '=', 'vh.ssl_id')
                ->leftjoin('voucher_refunds as vr', 'vr.purchase_id', '=', 'vpd.id')
                ->select('ci.customer_full_name', 'ci.customer_contact_number', 'bv.id as voucher_id', 'bv.heading', 'bv.date_duration', 'bv.redeem_duration', 'vh.created_at', 'vpd.id', 'vpd.redeemed', 'pi.partner_name', 'pb.partner_area', 'vsi.credit', 'vr.refund_status')
                ->orderBy('vpd.created_at', 'DESC')
                ->get();

        $data = $data->where('refund_status', '!=', DealRefundStatus::ACCEPTED);
        foreach ($data as $key => $value) {
            $value->date_duration = json_decode($value->date_duration, true)[0];
            if ($value->redeem_duration) {
                $value->expiry_date = date('d-m-Y', strtotime($value->created_at.' + '.$value->redeem_duration.' days'));
            } else {
                $value->expiry_date = $value->date_duration['to'];
            }
        }
        $result = collect();
        if ($type == 'redeemed') {
            $result = $data->where('redeemed', 1);
            $tab_title = 'All redeemed deals';
        } elseif ($type == 'expired') {
            $today = date('d-m-Y');
            foreach ($data as $key => $value) {
                if (new DateTime($value->expiry_date) < new DateTime($today)) {
                    $result->push($value);
                }
            }
            $tab_title = 'All expired deals';
        } else {
            $result = $data;
            $tab_title = 'All purchased deals';
        }

        return view('admin.production.vouchers.purchased', compact('result', 'tab_title'));
    }

    /**
     * @description Show branch deal payment page
     */
    public function dealPayment()
    {
        $branches = VoucherPayment::with('branch')->get();

        return view('admin.production.vouchers.payment', compact('branches'));
    }

    /**
     * @description Pay branch for voucher
     */
    public function payBranchForVoucher($branch_id)
    {
        $branch_balance = VoucherPayment::where('branch_id', $branch_id)->first();
        $branch_credit = $branch_balance->credit;
        $branch_debit = $branch_balance->debit;

        if ($branch_credit == 0) {
            return redirect()->back()->with('try_again', 'You do not have enough credit to pay.');
        }
        try {
            DB::beginTransaction(); //to do query rollback

            $branch_balance->decrement('credit', $branch_credit);
            $branch_balance->increment('credit_used', $branch_credit);

            $branch_balance->decrement('debit', $branch_debit);
            $branch_balance->increment('debit_used', $branch_debit);

            $redeem = new BranchCreditRedeemed();
            $redeem->branch_id = $branch_id;
            $redeem->credit = $branch_credit;
            $redeem->debit = $branch_debit;
            $redeem->save();

            DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            DB::rollBack(); //rollback all successfully executed queries
            return redirect()->back()->with('try_again', 'Please try again!');
        }

        return Redirect('admin/deals_payment')->with('updated', 'Branch payment successful');
    }

    /**
     * @description Show all voucher refund requests
     */
    public function voucherRefundRequests($type)
    {
        $all_requests = VoucherRefund::with('customer', 'purchaseDetails.voucher')->orderBy('created_at', 'DESC')->get();
        $all_requests = $all_requests->where('purchaseDetails.redeemed', 0);

        $tab_title = '';
        $requests = collect();
        if ($type == 'accepted') {
            $requests = $all_requests->where('refund_status', DealRefundStatus::ACCEPTED);
            $tab_title = 'Accepted';
        } elseif ($type == 'rejected') {
            $requests = $all_requests->where('refund_status', DealRefundStatus::REJECTED);
            $tab_title = 'Rejected';
        } else {
            $requests = $all_requests->where('refund_status', DealRefundStatus::REQUESTED);
            $tab_title = 'All';
        }

        return view('admin/production.vouchers.refund_requests', compact('requests', 'tab_title'));
    }

    /**
     * @description Accept voucher refund request
     */
    public function acceptVoucherRefundRequests(Request $request)
    {
        $request_id = $request->get('request_id');
        $refund_type = $request->get('refund_type');

        try {
            \DB::beginTransaction();

            $request = VoucherRefund::where('id', $request_id)->with('purchaseDetails.voucher')->first();
            $request->refund_status = DealRefundStatus::ACCEPTED;
            $request->refund_amount = intval($request->purchaseDetails->voucher->selling_price);
            $request->refund_type = $refund_type;
            $request->save();

            $customer_id = $request->customer_id;

            if ($refund_type == 1) {//refund as credit
                $customer_point = new CustomerPoint();
                $customer_point->customer_id = $customer_id;
                $customer_point->point = intval($request->purchaseDetails->voucher->selling_price);
                $customer_point->point_type = PointType::deal_refund_point;
                $customer_point->source_id = $request_id;
                $customer_point->save();

                $notif_text = 'Your refund request for '.$request->purchaseDetails->voucher->heading.' of '.$request->purchaseDetails->voucher->branch->info->partner_name.', '.$request->purchaseDetails->voucher->branch->partner_area.' has been accepted. '.intval($request->purchaseDetails->voucher->selling_price).' credits have been added to your account.';
                $notif_type = notificationType::reward;
                $customer_notification = new CustomerNotification([
                        'user_id' => $customer_id,
                        'image_link' => 'https://royalty-bd.s3.ap-southeast-1.amazonaws.com/dynamic-images/rbd-offers/ROYALTY-COIN-01_1570021729.png',
                        'notification_text' => $notif_text,
                        'notification_type' => $notif_type,
                        'source_id' => $request_id,
                        'seen' => 0,
                    ]);
                $customer_notification->save();

                $msg = $request->customer->customer_full_name.' was refunded '.$request->refund_amount.' credits for deal "'.$request->purchaseDetails->voucher->heading.' of '.$request->purchaseDetails->voucher->branch->info->partner_name.', '.$request->purchaseDetails->voucher->branch->partner_area.'".';

                (new \App\Http\Controllers\AdminNotification\functionController())->voucherRefundAcceptNotification($request_id, $msg);
            } else {//refund as cash
                (new voucherFunctionController())->sendVoucherRefundEmail($request->customer->customer_email, $request->purchaseDetails->voucher);
            }

            \DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            \DB::rollBack(); //rollback all successfully executed queries
            return redirect()->back()->with('try_again', 'Something went wrong. Please try again.');
        }
        // $receiver = CustomerInfo::where('customer_id', $customer_id)->first();

        // //make one event for pusher
        // event(new reward_notification($customer_id));
        // (new jsonController())->functionSendGlobalPushNotification($notif_text, $receiver, $notif_type);

        return redirect()->back()->with('status', 'Refund request accepted.');
    }

    /**
     * @description Reject voucher refund request
     */
    public function rejectVoucherRefundRequests($id)
    {
        try {
            \DB::beginTransaction();

            $request = VoucherRefund::where('id', $id)->with('purchaseDetails.voucher')->first();
            $request->refund_status = DealRefundStatus::REJECTED;
            $request->save();
            $customer_id = $request->customer_id;

            $notif_text = 'Your refund request for '.$request->purchaseDetails->voucher->heading.'  has been rejected.';
            $notif_type = notificationType::deal_refund_rejected;
            $customer_notification = new CustomerNotification([
                    'user_id' => $customer_id,
                    'image_link' => $request->purchaseDetails->voucher->branch->info->profileImage->partner_profile_image,
                    'notification_text' => $notif_text,
                    'notification_type' => $notif_type,
                    'source_id' => $id,
                    'seen' => 0,
                ]);
            $customer_notification->save();

            \DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            \DB::rollBack(); //rollback all successfully executed queries
            return redirect()->back()->with('try_again', 'Something went wrong. Please try again.');
        }
        //append new notification for user
        event(new reward_notification($customer_id));

        return redirect()->back()->with('status', 'Refund request rejected.');
    }

    /**
     * @description Delete voucher refund requests
     */
    public function deleteVoucherRefundRequests($id)
    {
        $request = VoucherRefund::find($id);
        $request->delete();

        return redirect()->back()->with('delete', 'Refund request deleted.');
    }
}
