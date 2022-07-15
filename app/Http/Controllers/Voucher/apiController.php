<?php

namespace App\Http\Controllers\Voucher;

use App\BranchUserNotification;
use App\BranchVoucher;
use App\Categories;
use App\CustomerAccount;
use App\CustomerInfo;
use App\CustomerNotification;
use App\CustomerPoint;
use App\Events\like_post;
use App\Helpers\LengthAwarePaginator;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Enum\DealRefundStatus;
use App\Http\Controllers\Enum\notificationType;
use App\Http\Controllers\Enum\PartnerBranchNotificationType;
use App\Http\Controllers\Enum\PlatformType;
use App\Http\Controllers\Enum\PointType;
use App\Http\Controllers\jsonController;
use App\Http\Controllers\Voucher\functionController as voucherFunctionController;
use App\PartnerBranch;
use App\VoucherHistory;
use App\VoucherPayment;
use App\VoucherPurchaseDetails;
use DateTime;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as Input;
use Illuminate\Support\Facades\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class apiController extends Controller
{
    //get all vouchers
    public function getAllVouchers()
    {
        $category = Input::get('category');
        $data = (new voucherFunctionController())->allVouchers($category);

        $paginatedItems = $this->getPaginatedData($data, 10, 'vouchers');

        return Response::json($paginatedItems, 200);
    }

    //get single branch vouchers
    public function getBranchVouchers()
    {
        $branch_id = Input::get('branch_id');
        $customer_id = JWTAuth::toUser(JWTAuth::getToken())->customer_id;
        $partner = PartnerBranch::where('id', $branch_id)->select('partner_account_id')->first();
        $branch_vouchers = (new voucherFunctionController())->activeVouchers($branch_id);
        if ($branch_vouchers) {
            foreach ($branch_vouchers as $key => $voucher) {
                $purchased = VoucherPurchaseDetails::where('voucher_id', $voucher['id'])->with('ssl', 'refund')->get();
                $purchased = $purchased->where('ssl.status', 1);
                $purchased = $purchased->where('refund.refund_status', '!=', DealRefundStatus::ACCEPTED);
                $branch_vouchers[$key]['purchased'] = count($purchased);
                if (($voucher['counter_limit'] - count($purchased)) <= 0) {
                    unset($branch_vouchers[$key]);
                } else {
                    if ($customer_id && $voucher['scan_limit'] != null) {
                        $cur_user_purchased = VoucherHistory::where('customer_id', $customer_id)->with('voucherDetails.ssl')->get();
                        $branch_vouchers[$key]['scan_limit'] = $voucher['scan_limit'] - $cur_user_purchased->where('voucherDetails.ssl', 1)->count();
                    }
                }
            }
            $paginatedItems = $this->getPaginatedData($branch_vouchers, 10, 'branch_vouchers');

            return response()->json($paginatedItems, 200);
        } else {
            $error = 'Partner does not exist.';

            return response()->json(['error' => $error], 201);
        }
    }

    //sort voucher with params
    public function sortVouchers(Request $request)
    {
        $category = $request->input('category');
        $area = $request->input('area');
        $division = $request->input('division');
        $price = $request->input('price');
        $rating = $request->input('rating');

        $result = (new voucherFunctionController())->allVouchers($category);
        $result = collect($result);

        if ($division) {
            $result = $result->where('branch.partner_division', $division);
        }
        if ($area) {
            $result = $result->where('branch.partner_area', $area);
        }
        if ($price) {
            if ($price == 'htl') {
                $result = $result->sortByDesc('selling_price');
            } else {
                $result = $result->sortBy('selling_price');
            }
        }
        if ($rating) {
            $result = $result->where('branch.info.rating.average_rating', '>=', $rating);
        }
        $paginatedItems = $this->getPaginatedData($result, 10, 'vouchers');

        return Response::json($paginatedItems, 200);
    }

    // get single voucher details
    public function getVoucherDetails($id)
    {
        $voucher = (new voucherFunctionController())->singleVoucher($id);

        return Response::json($voucher, 200);
    }

    public function insertSSLInfo(Request $request)
    {
        $customer_id = JWTAuth::toUser(JWTAuth::getToken())->customer_id;
        $voucher_details = Input::get('voucher_details');
        $creditUsed = Input::get('credit_used');

        $total_amount = 0;
        foreach ($voucher_details as $key => $value) {
            $voucher = BranchVoucher::where('id', $value['voucher_id'])->first();
            if ($voucher->scan_limit != null && $value['quantity'] > $voucher->scan_limit) {
                return response()->json('Please select exact quantity.', 403);
            }
            $total_amount += $voucher->selling_price * $value['quantity'];
        }
        $error = false;
        $tran_id = (new voucherFunctionController())->getSSLTranId('RVOUCHER');

        $customer = CustomerAccount::where('customer_id', $customer_id)->with('info')->first();
        $platform = $request->header('platform', null);

        $used_credits = 0;
        if ($creditUsed == 'true') {
            $cur_credit = (new \App\Http\Controllers\Reward\functionController())->getRoyaltyPoints($customer_id, false);
            if ($cur_credit >= $total_amount) {
                $used_credits = $total_amount;
                $total_amount = 0;
            } elseif (($total_amount - $cur_credit) > 0 && ($total_amount - $cur_credit) < 10) {
                $cur_credit -= $total_amount - $cur_credit;
                $total_amount = $total_amount - $cur_credit;
                $used_credits = $cur_credit;
            } else {
                $used_credits = $cur_credit;
                $total_amount = $total_amount - $cur_credit;
            }
        }

        try {
            \DB::beginTransaction();

            $voucherSSL = (new voucherFunctionController())->insertSSLVoucher($customer_id, $tran_id, $total_amount, $used_credits, $platform);

            foreach ($voucher_details as $value) {
                $voucherDetails = (new voucherFunctionController())->insertVoucherDetails($value['voucher_id'], $value['quantity'], $voucherSSL->id);
                if (! $voucherDetails) {
                    $error = true;
                }
            }
            if ($error) {
                return response()->json('Something went wrong', 403);
            }

            \DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            \DB::rollBack(); //rollback all successfully executed queries
            return Response::json(['result' => $e->getMessage()], 201);
        }

        return Response::json(['result' => $tran_id], 200);
    }

    public function voucherPurchaseSuccess(Request $request)
    {
        $customer_id = JWTAuth::toUser(JWTAuth::getToken())->customer_id;
        $tran_id = $request->post('tran_id');
        $amount = $request->post('amount');
        $tran_date = $request->post('tran_date');
        $val_id = $request->post('val_id');
        $store_amount = $request->post('store_amount');
        $card_type = $request->post('card_type');
        $currency = $request->post('currency');
        $card_no = $request->post('card_no');
        $bank_tran_id = $request->post('bank_tran_id');
        $card_issuer = $request->post('card_issuer');
        $card_brand = $request->post('card_brand');
        $card_issuer_country = $request->post('card_issuer_country');
        $card_issuer_country_code = $request->post('card_issuer_country_code');
        $currency_amount = $request->post('currency_amount');

        $info = (new voucherFunctionController())->saveVoucherPurchasedInfo($tran_id, $amount, $tran_date, $store_amount, $val_id, $card_type, $card_no, $currency, $bank_tran_id, $card_issuer, $card_brand, $card_issuer_country, $card_issuer_country_code, $currency_amount);

        if (! $info) {
            return Response::json(['message' => 'Something went wrong with the payment.'], 403);
        } else {
            $msg = 'Congratulations! Your payment is successful';

            return Response::json(['result' => $msg], 200);
        }
    }

    //paginated data common function
    public function getPaginatedData($data, $per_page, $array_name)
    {
        // Get current page form url e.x. &page=1
        $currentPage = LengthAwarePaginator::resolveCurrentPage();

        // Create a new Laravel collection from the array data
        $itemCollection = collect($data);

        // Define how many items we want to be visible in each page
        $perPage = $per_page;

        // Slice the collection to get the items to display in current page
        $currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->values();

        // Create our paginator and pass it to the view
        $paginatedItems = new LengthAwarePaginator($currentPageItems, count($itemCollection), $perPage);
        $paginatedItems->setPath('');
        $paginatedItems->setArrayName($array_name);

        return $paginatedItems;
    }

    public function availVoucher(Request $request)
    {
        $customer_id = JWTAuth::toUser(JWTAuth::getToken())->customer_id;
        $purchase_id = $request->input('purchase_id');
        $scanner_id = $request->input('branch_user_id');

        $voucher_details = DB::table('voucher_history as vh')
            ->select('vpd.*')
            ->join('voucher_purchase_details as vpd', 'vpd.ssl_id', '=', 'vh.ssl_id')
            ->where('vh.customer_id', $customer_id)
            ->where('vpd.id', $purchase_id)
            ->where('vpd.redeemed', 0)
            ->first();

        if ($voucher_details) {
            try {
                \DB::beginTransaction();

                $voucher = BranchVoucher::where('id', $voucher_details->voucher_id)->with('branch')->first();

                if ($voucher->commission_type == 1) {
                    $debit = $voucher->commission;
                    $credit = $voucher->selling_price - $debit;
                } else {
                    $debit = ($voucher->selling_price * $voucher->commission) / 100;
                    $credit = $voucher->selling_price - $debit;
                }

                $voucher_payment = VoucherPayment::where('branch_id', $voucher->branch_id)->first();
                if ($voucher_payment) {
                    $voucher_payment->increment('credit', $credit);
                    $voucher_payment->increment('debit', $debit);
                }

                VoucherPurchaseDetails::where('id', $voucher_details->id)->update(['redeemed' => 1]);
                if ($voucher->point != null && $voucher->point != 0) {
                    $customer_point = new CustomerPoint();
                    $customer_point->customer_id = $customer_id;
                    $customer_point->point = intval($voucher->point);
                    $customer_point->point_type = PointType::deal_redeem_point;
                    $customer_point->source_id = $purchase_id;
                    $customer_point->save();

                    $notif_text = 'You have earned '.$voucher->point.' credits from redeeming a deal at '.$voucher->branch->info->partner_name.', '.$voucher->branch->partner_area.'.';
                } else {
                    $notif_text = 'You redeemed a deal at '.$voucher->branch->info->partner_name.', '.$voucher->branch->partner_area.'.';
                }

                $notif_type = notificationType::deal;
                $customer_notification = new CustomerNotification([
                    'user_id' => $customer_id,
                    'image_link' => $voucher->branch->info->profileImage->partner_profile_image,
                    'notification_text' => $notif_text,
                    'notification_type' => $notif_type,
                    'source_id' => $voucher_details->id,
                    'seen' => 0,
                ]);
                $customer_notification->save();

                $customer = CustomerInfo::where('customer_id', $customer_id)->first();
                $merchant_notif_text = $customer->customer_full_name.' has availed a deal.';

                $notification = new BranchUserNotification();
                $notification->branch_user_id = $scanner_id;
                $notification->customer_id = $customer_id;
                $notification->notification_text = $merchant_notif_text;
                $notification->notification_type = PartnerBranchNotificationType::DEAL_AVAILED;
                $notification->source_id = $voucher_details->id;
                $notification->seen = 0; //not seen
                $notification->posted_on = date('Y-m-d H:i:s');
                $notification->save();

                \DB::commit(); //to do query rollback
            } catch (\Exception $e) {
                \DB::rollBack(); //rollback all successfully executed queries
                return Response::json(['result' => $e->getMessage()], 403);
            }
            //send notification to app
            (new jsonController)->sendFirebaseDiscountNotification($notif_text, $customer, $notif_type, $voucher->branch_id, $purchase_id);
            //this not actually for post like notification, only to append new notification to merchant account
            event(new like_post($voucher->branch_id));

            return Response::json(['result' => 'Operation Successful.'], 200);
        } else {
            return Response::json(['result' => 'Operation Failed.'], 403);
        }
    }

    public function purchasedVouchers(Request $request)
    {
        $customer_id = JWTAuth::toUser(JWTAuth::getToken())->customer_id;
        $type = $request->input('type');
        $data = (new voucherFunctionController())->purchasedData($customer_id);

        $today = date('d-m-Y');
        $result = [];
        if ($type == 'available') {
            $i = 0;
            foreach ($data as $key => $value) {
                if ($value->redeemed == 0 && new DateTime($value->expiry_date) >= new DateTime($today)) {
                    $result[$i] = $value;
                    $i++;
                }
            }
        } elseif ($type == 'expired') {
            $i = 0;
            foreach ($data as $key => $value) {
                if ($value->redeemed == 0 && new DateTime($value->expiry_date) < new DateTime($today)) {
                    $result[$i] = $value;
                    $i++;
                }
            }
        } elseif ($type == 'redeemed') {
            $result = $data->where('redeemed', 1);
        } else {
            $result = $data;
        }
        $paginatedItems = $this->getPaginatedData($result, 10, 'vouchers');

        return Response::json($paginatedItems, 200);
    }

    public function purchaseDetails(Request $request)
    {
        $id = $request->input('purchase_id');
        $purchase_details = (new voucherFunctionController())->singlePurchaseDetails($id);

        if ($purchase_details) {
            return Response::json($purchase_details, 200);
        } else {
            return Response::json(['result' => 'Details not found.'], 403);
        }
    }

    public function voucherRefundRequest(Request $request)
    {
        $purchase_id = $request->input('purchase_id');
        $customer_id = JWTAuth::toUser(JWTAuth::getToken())->customer_id;
        $comment = $request->input('comment');

        $result = (new voucherFunctionController())->saveVoucherRefundRequest($purchase_id, $comment, $customer_id);

        if ($result) {
            return Response::json(['result' => 'Request has been successfully submitted.'], 200);
        } else {
            return Response::json(['result' => 'Operation Failed.'], 403);
        }
    }

    public function paymentHistory(Request $request)
    {
        $branch_id = $request->input('branch_id');

        $payment_details = (new voucherFunctionController())->dealPaymentHistory($branch_id);
        if ($payment_details && isset($payment_details->paidHistory)) {
            $paginatedItems = $this->getPaginatedData($payment_details->paidHistory, 10, 'breakdown');
            $data = collect($payment_details)->forget('paid_history');
            return Response::json(['payment_details' => $data, 'breakdown' => $paginatedItems], 200);
        } else {
            return Response::json(['result' => 'Data not found'], 404);
        }
    }
}
