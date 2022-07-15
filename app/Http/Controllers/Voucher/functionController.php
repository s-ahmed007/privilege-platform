<?php

namespace App\Http\Controllers\Voucher;

use App\BranchVoucher;
use App\Categories;
use App\CustomerInfo;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Enum\DealRefundStatus;
use App\Http\Controllers\Enum\notificationType;
use App\Http\Controllers\Enum\ssl_validation_type;
use App\Http\Controllers\functionController2;
use App\PartnerInfo;
use App\VoucherHistory;
use App\VoucherPayment;
use App\VoucherPurchaseDetails;
use App\VoucherRefund;
use App\VoucherSslInfo;
use DateTime;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class functionController extends Controller
{
    //get all vouchers
    public function allVouchers($category)
    {
        $vouchers = BranchVoucher::with('branch.info.profileImage', 'branch.info.rating')->where('active', 1)->orderBy('id', 'DESC')->get();

        if ($category == 'all') {
            //nothing
        } else {
            $cat = Categories::where('type', $category)->first();
            $vouchers = $vouchers->where('branch.info.partner_category', $cat->id);
        }
        $date = date('d-m-Y');
        foreach ($vouchers as $key => $voucher) {
            //check expiry
            $voucher_date = $voucher->date_duration;
            try {
                if (
                    new DateTime($voucher_date[0]['from']) <= new DateTime($date)
                    && new DateTime($voucher_date[0]['to']) >= new DateTime($date)
                ) {
                    $expiry_status = false;
                } else {
                    $expiry_status = true;
                }
            } catch (\Exception $e) {
                $expiry_status = true;
            }
            $voucher->expired = $expiry_status;
            $voucher->weekdays = $voucher->weekdays[0];
            $voucher->date_duration = $voucher->date_duration[0];
            if (count($voucher->time_duration) > 0) {
                $voucher->time_duration = $voucher->time_duration;
            } else {
                $voucher->time_duration = null;
            }
        }

        if ($vouchers) {
            foreach ($vouchers as $key => $value) {
                if ($value['expired']) {
                    //unset specific array index if not match
                    unset($vouchers[$key]);
                }
            }
            $vouchers = array_values($vouchers->toArray());
        } else {
            return null;
        }

        return $vouchers;
    }

    public function activeVouchers($branch_id)
    {
        $date = date('d-m-Y');
        $week_Day = strtolower(date('D'));
        $time = date('H:i');
        $branch_vouchers = BranchVoucher::where('branch_id', $branch_id)->where('active', 1)->orderBy('priority', 'DESC')->get();

        foreach ($branch_vouchers as $key => $voucher) {
            //check expiry
            $voucher_date = $voucher->date_duration;
            try {
                if (
                    new DateTime($voucher_date[0]['from']) <= new DateTime($date)
                    && new DateTime($voucher_date[0]['to']) >= new DateTime($date)
                ) {
                    $expiry_status = false;
                } else {
                    $expiry_status = true;
                }
            } catch (\Exception $e) {
                $expiry_status = true;
            }
            $voucher->expired = $expiry_status;
            $voucher->weekdays = $voucher->weekdays[0];
            $voucher->date_duration = $voucher->date_duration[0];
            if (count($voucher->time_duration) > 0) {
                $voucher->time_duration = $voucher->time_duration;
            } else {
                $voucher->time_duration = null;
            }
        }
        if ($branch_vouchers) {
            foreach ($branch_vouchers as $key => $value) {
                if ($value['expired']) {
                    //unset specific array index if not match
                    unset($branch_vouchers[$key]);
                }
            }
            $branch_vouchers = array_values($branch_vouchers->toArray());
        } else {
            return null;
        }

        return $branch_vouchers;
    }

    public function branchAllVouchers($partner_account_id, $branch_id)
    {
        $partnerInfo = PartnerInfo::where('partner_account_id', $partner_account_id)->with(['category',
            'branches' => function ($query) use ($branch_id) {
                $query->where('id', '=', $branch_id);
            },
            'profileImage',
            'branches.vouchers' => function ($query) {
                $query->where('active', 1)->orderBy('priority', 'DESC');
            },
            'rating', 'reviews',
        ])->first();

        return $partnerInfo;
    }

    //get single purchase details
    public function singlePurchaseDetails($id)
    {
        $purchase_details = VoucherPurchaseDetails::where('id', $id)->with('voucher', 'refund', 'ssl')->first();
        if ($purchase_details) {
            $purchase_details->voucher->weekdays = $purchase_details->voucher->weekdays[0];
            $purchase_details->voucher->date_duration = $purchase_details->voucher->date_duration[0];
            if (count($purchase_details->voucher->time_duration) > 0) {
                $purchase_details->voucher->time_duration = $purchase_details->voucher->time_duration;
            } else {
                $purchase_details->voucher->time_duration = null;
            }
            if ($purchase_details->voucher->redeem_duration) {
                $purchase_details->expiry_date = date('Y-m-d', strtotime($purchase_details->created_at.' + '.$purchase_details->voucher->redeem_duration.' days'));
            } else {
                $purchase_details->expiry_date = date('Y-m-d', strtotime($purchase_details->voucher->date_duration['to']));
            }
            if ($purchase_details->redeemed == 0 && $purchase_details->expiry_date >= date('Y-m-d')) {
                $purchase_details->available = true;
            } else {
                $purchase_details->available = false;
            }

            return $purchase_details;
        } else {
            return null;
        }
    }

    public function getSSLTranId($prefix)
    {
        $random_text = '';
        $codeAlphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $codeAlphabet .= 'abcdefghijklmnopqrstuvwxyz';
        $codeAlphabet .= '0123456789';
        $max = strlen($codeAlphabet); // edited
        for ($i = 0; $i < 15; $i++) {
            $random_text .= $codeAlphabet[random_int(0, $max - 1)];
        }
        $random_text = $prefix.$random_text;

        return $random_text;
    }

    public function insertSSLVoucher($customer_id, $tran_id, $amount, $credit, $platform)
    {
        try {
            \DB::beginTransaction();

            $temp_ssL_data = new VoucherSslInfo();
            $temp_ssL_data->customer_id = $customer_id;
            $temp_ssL_data->status = ssl_validation_type::not_valid;
            $temp_ssL_data->tran_id = $tran_id;
            $temp_ssL_data->amount = $amount;
            $temp_ssL_data->credit = $credit;
            $temp_ssL_data->platform = $platform;
            $temp_ssL_data->save();

            \DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            \DB::rollBack(); //rollback all successfully executed queries
            return null;
        }

        return $temp_ssL_data;
    }

    //not being used right now
    public function getPromoIDForDeal($prefix)
    {
        A:
        $random_text = '';
        $codeAlphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $codeAlphabet .= 'abcdefghijklmnopqrstuvwxyz';
        $codeAlphabet .= '0123456789';
        $max = strlen($codeAlphabet);
        for ($i = 0; $i < 5; $i++) {
            $random_text .= $codeAlphabet[random_int(0, $max - 1)];
        }
        $random_text = $prefix.$random_text;

        $promo_exist = VoucherPurchaseDetails::where('promo_id', $random_text)->count();
        if ($promo_exist > 0) {
            goto A;
        }

        return $random_text;
    }

    public function insertVoucherDetails($voucher_id, $quantity, $ssl_id)
    {
        try {
            \DB::beginTransaction();

            for ($i = 0; $i < $quantity; $i++) {
                $purchase_details = new VoucherPurchaseDetails();
                $purchase_details->voucher_id = $voucher_id;
                $purchase_details->ssl_id = $ssl_id;
                $purchase_details->save();
            }

            \DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            \DB::rollBack(); //rollback all successfully executed queries
            return null;
        }

        return $purchase_details;
    }

    public function sendVoucherPurchasedEmail($email, $ssl_id)
    {
        $ssl_info = \App\VoucherSslInfo::where('id', $ssl_id)->first();
        $history = VoucherHistory::where('ssl_id', $ssl_id)->first();
        $order['date'] = date('F d, Y', strtotime($history->created_at));
        $order['order_id'] = $history->order_id;
        $order['partner_name'] = $history->branch->info->partner_name;
        $order['partner_area'] = $history->branch->partner_area;
        $order['amount'] = $ssl_info->amount;
        $order['credit_used'] = $ssl_info->credit;

        $voucher_details = VoucherPurchaseDetails::where('ssl_id', $ssl_id)->with('voucher')->get();
        $voucher_details = $voucher_details->groupBy('voucher_id');
        $vouchers = [];
        $i = 0;
        foreach ($voucher_details as $key => $value) {
            if ($value[0]->voucher->redeem_duration) {
                $exp_date = date('d-m-Y', strtotime($history->created_at.' + '.$value[0]->voucher->redeem_duration.' days'));
            } else {
                $exp_date = $value[0]->voucher->date_duration[0]['to'];
            }
            $vouchers[$i]['heading'] = $value[0]->voucher->heading;
            $vouchers[$i]['quantity'] = count($value);
            $vouchers[$i]['price'] = intval($value[0]->voucher->selling_price * count($value));
            $vouchers[$i]['exp_date'] = date('F d, Y', strtotime($exp_date));
            $i++;
        }
        $data['order'] = $order;
        $data['deals'] = $vouchers;

        $subject = 'Thank you for purchasing deal';

        try {
            $mg = (new functionController2())->getMailGun();
            $mg->messages()->send('mail.royaltybd.com', [
                'from' => 'Royalty no-reply@royaltybd.com',
                'to' => $email,
                'subject' => $subject,
                'html' => view('emails.dealpurchase', ['data' => $data])->render(),
            ]);
            return Response::json(['result' => 'E-mail sent successfully!']);
        } catch (\Exception $exception) {
            return Response::json(['result' => 'Internal Server Error']);
        }
    }

    public function sendVoucherRefundEmail($email, $voucher)
    {
        $data['heading'] = $voucher->heading;
        $data['price'] = intval($voucher->selling_price);

        $subject = 'Your refund request has been approved';

        try {
            $mg = (new functionController2())->getMailGun();
            $mg->messages()->send('mail.royaltybd.com', [
                'from' => 'Royalty no-reply@royaltybd.com',
                'to' => $email,
                'subject' => $subject,
                'html' => view('emails.dealrefund', ['data' => $data])->render(),
            ]);
            return Response::json(['result' => 'E-mail sent successfully!']);
        } catch (\Exception $exception) {
            return Response::json(['result' => 'Internal Server Error']);
        }
    }

    public function getOrderIDForDeal()
    {
        A:
        $random_text = '';
        // $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        // $codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet = '0123456789';
        $max = strlen($codeAlphabet);
        for ($i = 0; $i < 10; $i++) {
            $random_text .= $codeAlphabet[random_int(0, $max - 1)];
        }

        $id_exist = VoucherHistory::where('order_id', $random_text)->count();
        if ($id_exist > 0) {
            goto A;
        }

        return $random_text;
    }

    public function saveVoucherPurchasedInfo($tran_id, $amount, $tran_date, $store_amount, $val_id, $card_type, $card_no, $currency, $bank_tran_id, $card_issuer, $card_brand, $card_issuer_country, $card_issuer_country_code, $currency_amount)
    {
        try {
            \DB::beginTransaction();

            $ssl_info = VoucherSslInfo::where('tran_id', $tran_id)->where('amount', $amount)->first();
            $ssl_info->status = ssl_validation_type::valid;
            $ssl_info->tran_date = $tran_date;
            $ssl_info->store_amount = $store_amount;
            $ssl_info->val_id = $val_id;
            $ssl_info->card_type = $card_type;
            $ssl_info->card_no = $card_no;
            $ssl_info->currency = $currency;
            $ssl_info->bank_tran_id = $bank_tran_id;
            $ssl_info->card_issuer = $card_issuer;
            $ssl_info->card_brand = $card_brand;
            $ssl_info->card_issuer_country = $card_issuer_country;
            $ssl_info->card_issuer_country_code = $card_issuer_country_code;
            $ssl_info->currency_amount = $currency_amount;
            $ssl_info->save();

            $voucher_details = VoucherPurchaseDetails::where('ssl_id', $ssl_info->id)->with('voucher')->get();

            $order_id = $this->getOrderIDForDeal();

            $voucher_history = new VoucherHistory();
            $voucher_history->customer_id = $ssl_info->customer_id;
            $voucher_history->branch_id = $voucher_details[0]->voucher->branch_id;
            $voucher_history->ssl_id = $ssl_info->id;
            $voucher_history->order_id = $order_id;
            $voucher_history->save();

            //creating text for admin notification
            $voucher_details = $voucher_details->groupBY('voucher_id');
            $txt = '';
            $i = 1;
            foreach ($voucher_details as $key => $value) {
                if ($i < count($voucher_details)) {
                    $txt .= $value[0]->voucher->heading.'('.count($value).')'.', ';
                } else {
                    $txt .= $value[0]->voucher->heading.'('.count($value).')'.' of '.$value[0]->voucher->branch->info->partner_name.', '.$value[0]->voucher->branch->partner_area.'.';
                }
                $i++;
                continue;
            }

            (new \App\Http\Controllers\AdminNotification\functionController())->newVoucherPurchaseNotification($ssl_info, $txt);

            \DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            \DB::rollBack(); //rollback all successfully executed queries
            return null;
        }
        $customer = CustomerInfo::where('customer_id', $ssl_info->customer_id)->where('email_verified', 1)->first();
        if ($customer) {
            $this->sendVoucherPurchasedEmail($customer->customer_email, $ssl_info->id);
        }

        return $voucher_history;
    }

    public function purchasedData($customer_id)
    {
        $data = DB::table('voucher_history as vh')
                ->join('partner_branch as pb', 'pb.id', '=', 'vh.branch_id')
                ->join('partner_info as pi', 'pi.partner_account_id', '=', 'pb.partner_account_id')
                ->join('partner_profile_images as ppi', 'ppi.partner_account_id', '=', 'pi.partner_account_id')
                ->join('voucher_purchase_details as vpd', 'vpd.ssl_id', '=', 'vh.ssl_id')
                ->join('branch_vouchers as bv', 'bv.id', '=', 'vpd.voucher_id')
                ->leftjoin('voucher_refunds as vr', 'vr.purchase_id', '=', 'vpd.id')
                ->select('bv.id as voucher_id', 'bv.heading', 'bv.date_duration', 'bv.redeem_duration', 'vh.created_at', 'vpd.id', 'vpd.redeemed', 'vpd.review_id', 'pi.partner_account_id', 'pb.id as branch_id', 'pi.partner_name', 'pb.partner_area', 'ppi.partner_profile_image', 'ppi.partner_cover_photo', 'vr.refund_status')
                ->where('vh.customer_id', $customer_id)
                ->orderBy('vh.created_at', 'DESC')
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

        return $data;
    }

    public function purchasedVouchers($customer_id)
    {
        $data = $this->purchasedData($customer_id);

        $result['all'] = $data;
        $result['available'] = collect();
        $result['expired'] = collect();
        $result['redeemed'] = $data->where('redeemed', 1);

        $today = date('d-m-Y');
        $i = $j = 0;
        foreach ($data as $key => $value) {
            if ($value->redeemed == 0 && new DateTime($value->expiry_date) >= new DateTime($today)) {
                $result['available'][$i] = $value;
                $i++;
            }
            if ($value->redeemed == 0 && new DateTime($value->expiry_date) < new DateTime($today)) {
                $result['expired'][$i] = $value;
                $j++;
            }
        }

        return $result;
    }

    public function getVoucherBannerImages()
    {
        return [
            [
                'category' => 'all',
                'image' => 'https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/deals/home/deal-banner-web-app.png',
            ],
            [
                'category' => 'food_and_drinks',
                'image' => 'https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/deals/Category/food.png',
            ],
            [
                'category' => 'health_and_fitness',
                'image' => 'https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/deals/Category/health.png',
            ],
            [
                'category' => 'lifestyle',
                'image' => 'https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/deals/Category/lifestyle.png',
            ],
            [
                'category' => 'beauty_and_spa',
                'image' => 'https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/deals/Category/beauty.png',
            ],
            [
                'category' => 'entertainment',
                'image' => 'https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/deals/Category/entertainment.png',
            ],
            [
                'category' => 'getaways',
                'image' => 'https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/deals/Category/getaway.png',
            ],
        ];
    }

    public function saveVoucherRefundRequest($purchase_id, $comment, $customer_id)
    {
        try {
            \DB::beginTransaction();

            $purchase_details = VoucherPurchaseDetails::where('id', $purchase_id)->with('voucher', 'voucherHistory.customer')->first();

            if ($purchase_details) {
                $refund = new VoucherRefund();
                $refund->customer_id = $customer_id;
                $refund->purchase_id = $purchase_id;
                $refund->ssl_id = $purchase_details->ssl_id;
                $refund->refund_status = DealRefundStatus::REQUESTED;
                $refund->comment = $comment;
                $refund->save();

                $msg = $purchase_details->voucherHistory->customer->customer_full_name.' has submitted a refund request for deal "'.$purchase_details->voucher->heading.' of '.$purchase_details->voucher->branch->info->partner_name.', '.$purchase_details->voucher->branch->partner_area.'".';

                (new \App\Http\Controllers\AdminNotification\functionController())->newVoucherRefundRequestNotification($refund, $msg);
            }

            \DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            \DB::rollBack(); //rollback all successfully executed queries
            return null;
        }

        return $refund;
    }

    public function merchantDealList($branch_id)
    {
        $branch_vouchers = BranchVoucher::where('branch_id', $branch_id)->where('active', 1)->orderBy('created_at', 'DESC')->get();
        if ($branch_vouchers) {
            foreach ($branch_vouchers as $key => $voucher) {
                $voucher->weekdays = $voucher->weekdays[0];
                $voucher->date_duration = $voucher->date_duration[0];
                if (count($voucher->time_duration) > 0) {
                    $voucher->time_duration = $voucher->time_duration;
                } else {
                    $voucher->time_duration = null;
                }
                $purchased = VoucherPurchaseDetails::where('voucher_id', $voucher['id'])->with('ssl', 'refund')->get();
                $purchased = $purchased->where('ssl.status', 1);
                $purchased = $purchased->where('refund.refund_status', '!=', DealRefundStatus::ACCEPTED);
                $branch_vouchers[$key]['purchased'] = count($purchased);
                $branch_vouchers[$key]['redeemed'] = $purchased->where('redeemed', 1)->count();
            }
        }

        return $branch_vouchers;
    }

    public function dealPaymentHistory($branch_id)
    {
        $payment_details = VoucherPayment::where('branch_id', $branch_id)->first();

        return $payment_details;
    }

    public function branchDealPurchased($branch_id, $sort = false, $sel_month = null)
    {
        if ($sort) {
            $transactions = DB::table('voucher_history as vh')
                            ->join('voucher_purchase_details as vpd', 'vpd.ssl_id', '=', 'vh.ssl_id')
                            ->join('branch_vouchers as bv', 'bv.id', '=', 'vpd.voucher_id')
                            ->join('customer_info as ci', 'ci.customer_id', '=', 'vh.customer_id')
                            ->join('customer_notification as cn', function ($join) {
                                $join->on('cn.user_id', '=', 'ci.customer_id');
                                $join->on('cn.source_id', '=', 'vpd.id');
                            })
                            ->select('bv.heading', 'ci.customer_full_name', 'ci.customer_profile_image', 'cn.posted_on')
                            ->where('vh.branch_id', $branch_id)
                            ->where('vpd.redeemed', 1)
                            ->where('vpd.updated_at', 'like', $sel_month.'%')
                            ->where('cn.notification_type', notificationType::deal)
                            ->orderBy('vh.updated_at', 'DESC')
                            ->get();
        } else {
            $transactions = DB::table('voucher_history as vh')
                            ->join('voucher_purchase_details as vpd', 'vpd.ssl_id', '=', 'vh.ssl_id')
                            ->join('branch_vouchers as bv', 'bv.id', '=', 'vpd.voucher_id')
                            ->join('customer_info as ci', 'ci.customer_id', '=', 'vh.customer_id')
                            ->join('customer_notification as cn', function ($join) {
                                $join->on('cn.user_id', '=', 'ci.customer_id');
                                $join->on('cn.source_id', '=', 'vpd.id');
                            })
                            ->select('bv.heading', 'ci.customer_full_name', 'ci.customer_profile_image', 'cn.posted_on')
                            ->where('vh.branch_id', $branch_id)
                            ->where('vpd.redeemed', 1)
                            ->where('cn.notification_type', notificationType::deal)
                            ->orderBy('vh.updated_at', 'DESC')
                            ->get();
        }

        return $transactions;
    }
}
