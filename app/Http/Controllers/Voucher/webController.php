<?php

namespace App\Http\Controllers\Voucher;

use App\BranchVoucher;
use App\Categories;
use App\CustomerInfo;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Enum\PlatformType;
use App\Http\Controllers\Enum\SSLPaymentType;
use App\Http\Controllers\functionController2;
use App\Http\Controllers\Voucher\functionController as voucherFunctionController;
use App\PartnerBranch;
use App\VoucherHistory;
use App\VoucherPurchaseDetails;
use DateTime;
use DB;
use Illuminate\Http\Request;

class webController extends Controller
{
    public function getAllVouchers()
    {
        $vouchers = (new voucherFunctionController())->allVouchers('all');
        $banner_images = (new voucherFunctionController())->getVoucherBannerImages();
        $categories = Categories::orderBy('priority', 'DESC')->get();
        $total_vochuers = count($vouchers);
        $vouchers = (new functionController2())->getPaginatedData($vouchers, 18);

        //get divisions and area
        $divisions = DB::table('partner_branch as pb')
            ->select('pb.partner_division as name', 'd.id as id')
            ->join('partner_account as pa', 'pa.partner_account_id', '=', 'pb.partner_account_id')
            ->join('division as d', 'd.name', '=', 'pb.partner_division')
            ->where('pb.active', 1)
            ->where('pa.active', 1)
            ->groupBy('pb.partner_division')
            ->groupBy('d.id')
            ->get();

        foreach ($divisions as $division) {
            $areas = DB::table('partner_branch as pb')
                ->select('pb.partner_area as area_name', 'a.id as id')
                ->join('partner_account as pa', 'pa.partner_account_id', '=', 'pb.partner_account_id')
                ->join('area as a', 'a.area_name', '=', 'pb.partner_area')
                ->where('pb.active', 1)
                ->where('pa.active', 1)
                ->where('a.division_id', $division->id)
                ->groupBy('pb.partner_area')
                ->groupBy('a.id')
                ->get();
            $division->areas = $areas;
        }
        $divisions = $divisions->where('name', 'Dhaka')->first();
        $rat_deals_count = [];
        $rat_deals_count['four'] = $vouchers->where('branch.info.rating.average_rating', '>', 4)->count();
        $rat_deals_count['three'] = $vouchers->where('branch.info.rating.average_rating', '>', 3)->count();
        $rat_deals_count['two'] = $vouchers->where('branch.info.rating.average_rating', '>', 2)->count();
        $rat_deals_count['one'] = $vouchers->where('branch.info.rating.average_rating', '>', 1)->count();

        return view('voucher.index', compact('vouchers', 'total_vochuers', 'categories', 'divisions', 'rat_deals_count', 'banner_images'));
    }

    public function getSortedVouchers(Request $request)
    {
        $category = $request->input('category');
        $data = (new voucherFunctionController())->allVouchers($category);

        return response()->json($data);
    }

    public function getBranchVouchers($branch_id)
    {
        $partner = PartnerBranch::where('id', $branch_id)->select('partner_account_id')->first();
        $partnerInfo = (new voucherFunctionController())->branchAllVouchers($partner->partner_account_id, $branch_id);
        $categories = Categories::all();
        foreach ($partnerInfo->branches[0]->vouchers as $key => $voucher) {
            $purchased = VoucherPurchaseDetails::where('voucher_id', $voucher->id)->with('ssl', 'refund')->get();
            $purchased = $purchased->where('ssl.status', 1);
            $purchased = $purchased->where('refund.refund_status', '!=', 1);
            $voucher->purchased = count($purchased);

            $voucher_date = $voucher->date_duration;
            if (new DateTime($voucher_date[0]['from']) <= new DateTime(date('d-m-Y'))
                && new DateTime($voucher_date[0]['to']) >= new DateTime(date('d-m-Y'))) {
                if (($voucher->counter_limit - count($purchased)) <= 0) {
                    unset($partnerInfo->branches[0]->vouchers[$key]);
                } else {
                    if (session('customer_id') && $voucher->scan_limit != null) {
                        $ssl_ids = VoucherHistory::where('customer_id', session('customer_id'))->pluck('ssl_id');
                        $cur_user_purchased = VoucherPurchaseDetails::whereIn('ssl_id', $ssl_ids)->where('voucher_id', $voucher->id)->with('ssl', 'refund')->get();
                        $cur_user_purchased = $cur_user_purchased->where('ssl.status', 1);
                        $cur_user_purchased = $cur_user_purchased->where('refund.refund_status', '!=', 1);
                        $cur_user_purchased = $voucher->scan_limit - $cur_user_purchased->count();
                        $voucher->scan_limit = $cur_user_purchased < 0 ? 0 : $cur_user_purchased;
                    }
                }
            } else {
                unset($partnerInfo->branches[0]->vouchers[$key]);
            }
        }
        $cur_credit = (new \App\Http\Controllers\Reward\functionController())->getRoyaltyPoints(session('customer_id'), false);

        return view('voucher/merchantalldeals', compact('partnerInfo', 'categories', 'cur_credit'));
    }

    public function getVoucherDetails($id)
    {
        $voucher = (new voucherFunctionController())->singleVoucher($id);

        return $voucher;
    }

    public function sslConfig($customer, $price, $tran_id)
    {
        $post_data = [];

        $post_data['store_id'] = env('SSL_STORE_ID');
        $post_data['store_passwd'] = env('SSL_STORE_PASS');

        $post_data['total_amount'] = $price;
        $post_data['currency'] = 'BDT';
        $post_data['tran_id'] = $tran_id;
        $post_data['success_url'] = url('/deal_success?_token='.csrf_token());
        $post_data['fail_url'] = url('/deal_fail?_token='.csrf_token());
        $post_data['cancel_url'] = url('/deal_cancel?_token='.csrf_token());

        //==========================================================================================
        // CUSTOMER INFORMATION (mandatory)
        $post_data['cus_name'] = $customer->customer_full_name;
        $post_data['cus_email'] = $customer->customer_email;
        $post_data['cus_phone'] = $customer->customer_contact_number;
        //SHIPMENT INFORMATION
        $post_data['ship_name'] = 'Store Test';
        $post_data['ship_add1 '] = 'Dhaka';
        $post_data['ship_add2'] = 'Dhaka';
        $post_data['ship_city'] = 'Dhaka';
        $post_data['ship_state'] = 'Dhaka';
        $post_data['ship_postcode'] = '1000';
        $post_data['ship_country'] = 'Bangladesh';

        // OPTIONAL PARAMETERS
        $post_data['value_a'] = SSLPaymentType::VOUCHER;

        // REQUEST SEND TO SSLCOMMERZ
        if (env('RBD_SERVER') == 'PRODUCTION') {
            $direct_api_url = 'https://securepay.sslcommerz.com/gwprocess/v3/api.php'; //live url
        } else {
            $direct_api_url = 'https://sandbox.sslcommerz.com/gwprocess/v3/api.php'; //sandbox url
        }

        $handle = curl_init();
        curl_setopt($handle, CURLOPT_URL, $direct_api_url);
        curl_setopt($handle, CURLOPT_TIMEOUT, 30);
        curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($handle, CURLOPT_POST, 1);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false); // KEEP IT FALSE IF YOU RUN FROM LOCAL PC

        $content = curl_exec($handle);

        $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);

        if ($code == 200 && ! (curl_errno($handle))) {
            curl_close($handle);
            $sslcommerzResponse = $content;
        } else {
            curl_close($handle);
            echo 'FAILED TO CONNECT WITH SSLCOMMERZ API';
            exit;
        }

        // PARSE THE JSON RESPONSE
        $sslcz = json_decode($sslcommerzResponse, true);

        if (isset($sslcz['GatewayPageURL']) && $sslcz['GatewayPageURL'] != '') {
            // THERE ARE MANY WAYS TO REDIRECT - Javascript, Meta Tag or Php Header Redirect or Other
            // echo "<script>window.location.href = '". $sslcz['GatewayPageURL'] ."';</script>";
            echo "<meta http-equiv='refresh' content='0;url=".$sslcz['GatewayPageURL']."'>";
            // header("Location: ". $sslcz['GatewayPageURL']);
            exit;
        } else {
            echo 'JSON Data parsing error!';
        }

        if (isset($sslcz['status']) && $sslcz['status'] == 'SUCCESS') {
            // VISA GATEWAY
            if (isset($sslcz['gw']['visa']) && $sslcz['gw']['visa'] != '') {
                echo '<h3>VISA</h3>';
                $sslcz_visa = explode(',', $sslcz['gw']['visa']);
                foreach ($sslcz_visa as $gw_value) {
                    if ($gw_value == 'dbbl_visa') {
                        echo "<a href='".$sslcz['redirectGatewayURL']."dbbl_visa'>DBBL VISA</a><br />";
                    }
                    if ($gw_value == 'brac_visa') {
                        echo "<a href='".$sslcz['redirectGatewayURL']."brac_visa'>BRAC VISA</a><br />";
                    }
                    if ($gw_value == 'city_visa') {
                        echo "<a href='".$sslcz['redirectGatewayURL']."city_visa'>CITY VISA</a><br />";
                    }
                    if ($gw_value == 'ebl_visa') {
                        echo "<a href='".$sslcz['redirectGatewayURL']."ebl_visa'>EBL VISA</a><br />";
                    }
                    if ($gw_value == 'visacard') {
                        echo "<a href='".$sslcz['redirectGatewayURL']."visacard'>VISA</a><br />";
                    }
                }
            } // END OF VISA

            // MASTER GATEWAY
            if (isset($sslcz['gw']['master']) && $sslcz['gw']['master'] != '') {
                echo '<h3>MASTER</h3>';
                $sslcz_visa = explode(',', $sslcz['gw']['master']);
                foreach ($sslcz_visa as $gw_value) {
                    if ($gw_value == 'dbbl_master') {
                        echo "<a href='".$sslcz['redirectGatewayURL']."dbbl_master'>DBBL MASTER</a><br />";
                    }
                    if ($gw_value == 'brac_master') {
                        echo "<a href='".$sslcz['redirectGatewayURL']."brac_master'>BRAC MASTER</a><br />";
                    }
                    if ($gw_value == 'city_master') {
                        echo "<a href='".$sslcz['redirectGatewayURL']."city_master'>CITY MASTER</a><br />";
                    }
                    if ($gw_value == 'ebl_master') {
                        echo "<a href='".$sslcz['redirectGatewayURL']."ebl_master'>EBL MASTER</a><br />";
                    }
                    if ($gw_value == 'mastercard') {
                        echo "<a href='".$sslcz['redirectGatewayURL']."mastercard'>MASTER</a><br />";
                    }
                }
            } // END OF MASTER

            // AMEX GATEWAY
            if (isset($sslcz['gw']['amex']) && $sslcz['gw']['amex'] != '') {
                echo '<h3>AMEX</h3>';
                $sslcz_visa = explode(',', $sslcz['gw']['amex']);
                foreach ($sslcz_visa as $gw_value) {
                    if ($gw_value == 'city_amex') {
                        echo "<a href='".$sslcz['redirectGatewayURL']."city_amex'>AMEX</a><br />";
                    }
                }
            } // END OF AMEX

            // OTHER CARDS GATEWAY
            if (isset($sslcz['gw']['othercards']) && $sslcz['gw']['othercards'] != '') {
                echo '<h3>OTHER CARDS</h3>';
                $sslcz_visa = explode(',', $sslcz['gw']['othercards']);
                foreach ($sslcz_visa as $gw_value) {
                    if ($gw_value == 'dbbl_nexus') {
                        echo "<a href='".$sslcz['redirectGatewayURL']."dbbl_nexus'>NEXUS</a><br />";
                    }

                    if ($gw_value == 'qcash') {
                        echo "<a href='".$sslcz['redirectGatewayURL']."qcash'>QCASH</a><br />";
                    }

                    if ($gw_value == 'fastcash') {
                        echo "<a href='".$sslcz['redirectGatewayURL']."fastcash'>FASTCASH</a><br />";
                    }
                }
            } // END OF OTHER CARDS

            // INTERNET BANKING GATEWAY
            if (isset($sslcz['gw']['internetbanking']) && $sslcz['gw']['internetbanking'] != '') {
                echo '<h3>INTERNET BANKING</h3>';
                $sslcz_visa = explode(',', $sslcz['gw']['internetbanking']);
                foreach ($sslcz_visa as $gw_value) {
                    if ($gw_value == 'city') {
                        echo "<a href='".$sslcz['redirectGatewayURL']."city'>CITYTOUCH</a><br />";
                    }

                    if ($gw_value == 'bankasia') {
                        echo "<a href='".$sslcz['redirectGatewayURL']."bankasia'>BANK ASIA</a><br />";
                    }

                    if ($gw_value == 'ibbl') {
                        echo "<a href='".$sslcz['redirectGatewayURL']."ibbl'>IBBL</a><br />";
                    }

                    if ($gw_value == 'mtbl') {
                        echo "<a href='".$sslcz['redirectGatewayURL']."mtbl'>MTBL</a><br />";
                    }
                }
            } // END OF INTERNET BANKING

            // MOBILE BANKING GATEWAY
            if (isset($sslcz['gw']['mobilebanking']) && $sslcz['gw']['mobilebanking'] != '') {
                echo '<h3>MOBILE BANKING</h3>';
                $sslcz_visa = explode(',', $sslcz['gw']['mobilebanking']);
                foreach ($sslcz_visa as $gw_value) {
                    if ($gw_value == 'dbblmobilebanking') {
                        echo "<a href='".$sslcz['redirectGatewayURL']."dbblmobilebanking'>DBBL MOBILE BANKING</a><br />";
                    }

                    if ($gw_value == 'bkash') {
                        echo "<a href='".$sslcz['redirectGatewayURL']."bkash'>Bkash</a><br />";
                    }

                    if ($gw_value == 'abbank') {
                        echo "<a href='".$sslcz['redirectGatewayURL']."abbank'>AB Direct</a><br />";
                    }

                    if ($gw_value == 'ibbl') {
                        echo "<a href='".$sslcz['redirectGatewayURL']."ibbl'>IBBL</a><br />";
                    }

                    if ($gw_value == 'mycash') {
                        echo "<a href='".$sslcz['redirectGatewayURL']."mycash'>MYCASH</a><br />";
                    }

                    if ($gw_value == 'ific') {
                        echo "<a href='".$sslcz['redirectGatewayURL']."ific'>IFIC</a><br />";
                    }

                    if ($gw_value == 'ific') {
                        echo "<a href='".$sslcz['redirectGatewayURL']."ific'>IFIC</a><br />";
                    }
                }
            } // END OF MOBILE BANKING
        } else {
            echo 'Invalid Credential!';
        }
    }

    public function confirmVoucherPurchase(Request $request)
    {
        $voucher_details = $request->post('data');
        $creditUsed = $request->post('creditUsed');

        $total_amount = 0;
        foreach ($voucher_details as $key => $value) {
            $voucher = BranchVoucher::where('id', $value['voucher_id'])->first();
            if ($voucher->scan_limit != null && $value['quantity'] > $voucher->scan_limit) {
                return response()->json('Please select exact quantity.', 403);
            }
            $total_amount += $voucher->selling_price * $value['quantity'];
        }
        $error = false;
        $customer_id = session('customer_id');
        $tran_id = (new voucherFunctionController())->getSSLTranId('RVOUCHER');

        $used_credits = 0;
        if ($creditUsed) {
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

            $voucherSSL = (new voucherFunctionController())->insertSSLVoucher($customer_id, $tran_id, $total_amount, $used_credits, PlatformType::web);

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
            return response()->json('Something went wrong', 403);
        }

        return response()->json(['customer' => $customer_id, 'amount' => $total_amount, 'used_credits' =>$used_credits, 'tran_id' => $tran_id, 'csrf' => csrf_token()], 200);
    }

    public function submitVoucherToSSL(Request $request)
    {
        $customer_id = $request->input('customer_id');
        $amount = $request->input('amount');
        $used_credits = $request->input('used_credits');
        $tran_id = $request->input('tran_id');
        $customer = CustomerInfo::where('customer_id', $customer_id)->first();

        if ($amount == 0) {
            $tran_date = date('Y-m-d H:i:s');
            (new voucherFunctionController())->saveVoucherPurchasedInfo($tran_id, $amount, $tran_date, null, null, null, null, null, null, null, null, null, null, null);

            return redirect('deal_success');
        } else {
            $this->sslConfig($customer, $amount, $tran_id);
        }
    }

    public function voucherRefundRequest(Request $request)
    {
        $this->validate($request, [
            'comment' => 'required',
        ]);
        $request->flashOnly(['comment']);

        $comment = $request->get('comment');
        $purchase_id = $request->get('purchase_id');

        (new voucherFunctionController())->saveVoucherRefundRequest($purchase_id, $comment, session('customer_id'));

        return redirect()->back()->with('ref_request_saved', 'Your request has been successfully submitted. We\'ll get back to you soon!');
    }
}
