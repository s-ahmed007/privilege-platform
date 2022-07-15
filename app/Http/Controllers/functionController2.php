<?php

namespace App\Http\Controllers;

use App\CardSellerInfo;
use App\CustomerHistory;
use App\CustomerInfo;
use App\CustomerNotification;
use App\Helpers\LengthAwarePaginator;
use App\Http\Controllers\Enum\Constants;
use App\Http\Controllers\Enum\CustomerType;
use App\Http\Controllers\Enum\DeliveryType;
use App\Http\Controllers\Enum\SentMessageType;
use App\Http\Controllers\Enum\VerificationType;
use App\PartnerBranch;
use App\ResetUser;
use App\SearchStat;
use App\SentMessageHistory;
use App\SslTransactionTable;
use DateTime;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Mailgun\Mailgun;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;

class functionController2 extends Controller
{
    public function cardDeliveryData($status)
    {
        if ($status == 'all') {
            $card_delivery_list = DB::table('customer_info as ci')
                ->leftjoin('card_delivery', function ($join) {
                    $join->on('card_delivery.customer_id', '=', 'ci.customer_id')
                        ->on('card_delivery.id', '=', DB::raw('(SELECT max(id) from card_delivery WHERE card_delivery.customer_id = ci.customer_id)'));
                })
                ->leftjoin('customer_card_promo_usage as ccpu', 'ccpu.ssl_id', '=', 'card_delivery.ssl_id')
                ->leftjoin('card_promo as cp', 'cp.id', '=', 'ccpu.promo_id')
                ->join('ssl_transaction_table as stt', 'stt.id', '=', 'card_delivery.ssl_id')
                ->select(
                    'ci.customer_full_name',
                    'ci.customer_id as cid',
                    'ci.delivery_status',
                    'ci.customer_contact_number',
                    'ci.customer_email',
                    'ci.customer_type',
                    'ci.month',
                    'card_delivery.*',
                    'ccpu.promo_id',
                    'cp.code as promo_code',
                    'stt.tran_date',
                    'stt.tran_id',
                    'stt.id as stt_id',
                    'stt.amount',
                    'stt.platform'
                )
                ->orderBy('card_delivery.id', 'DESC')
                ->where('ci.delivery_status', '!=', 0)
                ->where('stt.status', 1)
                ->where('ci.customer_type', 2)
                ->paginate(20);
        } elseif ($status == 'ordered') {
            $card_delivery_list = DB::table('customer_info as ci')
                ->leftjoin('card_delivery', function ($join) {
                    $join->on('card_delivery.customer_id', '=', 'ci.customer_id')
                        ->on('card_delivery.id', '=', DB::raw('(SELECT max(id) from card_delivery WHERE card_delivery.customer_id = ci.customer_id)'));
                })
                ->leftjoin('customer_card_promo_usage as ccpu', 'ccpu.ssl_id', '=', 'card_delivery.ssl_id')
                ->leftjoin('card_promo as cp', 'cp.id', '=', 'ccpu.promo_id')
                ->join('ssl_transaction_table as stt', 'stt.id', '=', 'card_delivery.ssl_id')
                ->select(
                    'ci.customer_full_name',
                    'ci.customer_id as cid',
                    'ci.delivery_status',
                    'ci.customer_contact_number',
                    'ci.customer_email',
                    'ci.customer_type',
                    'ci.month',
                    'card_delivery.*',
                    'ccpu.promo_id',
                    'cp.code as promo_code',
                    'stt.tran_date',
                    'stt.tran_id',
                    'stt.id as stt_id',
                    'stt.amount',
                    'stt.platform'
                )
                ->orderBy('card_delivery.id', 'DESC')
                ->where('card_delivery.delivery_type', '!=', 11)
                ->where('ci.delivery_status', 1)
                ->where('stt.status', 1)
                ->where('ci.customer_type', 2)
                ->paginate(20);
        } elseif ($status == 'delivered') {
            $card_delivery_list = DB::table('customer_info as ci')
                ->leftjoin('card_delivery', function ($join) {
                    $join->on('card_delivery.customer_id', '=', 'ci.customer_id')
                        ->on('card_delivery.id', '=', DB::raw('(SELECT max(id) from card_delivery WHERE card_delivery.customer_id = ci.customer_id)'));
                })
                ->leftjoin('customer_card_promo_usage as ccpu', 'ccpu.ssl_id', '=', 'card_delivery.ssl_id')
                ->leftjoin('card_promo as cp', 'cp.id', '=', 'ccpu.promo_id')
                ->join('ssl_transaction_table as stt', 'stt.id', '=', 'card_delivery.ssl_id')
                ->select(
                    'ci.customer_full_name',
                    'ci.customer_id as cid',
                    'ci.delivery_status',
                    'ci.customer_contact_number',
                    'ci.customer_email',
                    'ci.customer_type',
                    'ci.month',
                    'card_delivery.*',
                    'ccpu.promo_id',
                    'cp.code as promo_code',
                    'stt.tran_date',
                    'stt.tran_id',
                    'stt.id as stt_id',
                    'stt.amount',
                    'stt.platform'
                )
                ->orderBy('card_delivery.id', 'DESC')
                ->where('ci.delivery_status', 3)
                ->where('stt.status', 1)
                ->where('ci.customer_type', 2)
                ->paginate(20);
        } elseif ($status == 'free_trial') {
            $date = date('2019-10-17');
            $card_delivery_list = DB::table('customer_info as ci')
                ->leftjoin('card_delivery', function ($join) {
                    $join->on('card_delivery.customer_id', '=', 'ci.customer_id')
                        ->on('card_delivery.id', '=', DB::raw('(SELECT max(id) from card_delivery WHERE card_delivery.customer_id = ci.customer_id)'));
                })
                ->join('ssl_transaction_table as stt', 'stt.id', '=', 'card_delivery.ssl_id')
                ->select(
                    'ci.customer_full_name',
                    'ci.customer_id as cid',
                    'ci.delivery_status',
                    'ci.customer_contact_number',
                    'ci.customer_email',
                    'ci.customer_type',
                    'ci.month',
                    'card_delivery.*',
                    'stt.tran_date',
                    'stt.tran_id',
                    'stt.id as stt_id',
                    'stt.amount',
                    'stt.platform'
                )
                ->orderBy('card_delivery.id', 'DESC')
                ->where('card_delivery.delivery_type', 11)
                ->where('stt.status', 1)
                ->where('stt.tran_date', '>', $date)
                ->where('ci.customer_type', 2)
                ->paginate(20);
        }

        return $card_delivery_list;
    }

    //    url shortener with bitly (currently not using)
    public function urlShortener($long_url)
    {
        $login = 'sohel223';
        $api_key = 'R_277f9e20068342d29715011c6ef3571d';
        $ch = curl_init('http://api.bitly.com/v3/shorten?login='.$login.'&apiKey='.$api_key.'&longUrl='.$long_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        $response = json_decode($result, true);

        return $response['data']['url'];
    }

    public function getExpiredCustomers($user_type = null)
    {
        if ($user_type == null) {
            $expired_customers = DB::table('customer_info as ci')
                ->join('customer_history', function ($join) {
                    $join->on('customer_history.customer_id', '=', 'ci.customer_id')
                        ->on('customer_history.id', '=', DB::raw('(SELECT max(id) from customer_history WHERE customer_history.customer_id = ci.customer_id)'));
                })
                ->select('ci.*')
                ->where('ci.expiry_date', '<', date('Y-m-d'))
                ->get();
        } else {
            $expired_customers = DB::table('customer_info as ci')
                ->join('customer_history', function ($join) {
                    $join->on('customer_history.customer_id', '=', 'ci.customer_id')
                        ->on('customer_history.id', '=', DB::raw('(SELECT max(id) from customer_history WHERE customer_history.customer_id = ci.customer_id)'));
                })
                ->select('ci.*')
                ->where('ci.expiry_date', '<', date('Y-m-d'))
                ->where('customer_history.type', $user_type)
                ->get();
        }

        return $expired_customers;
    }

    public function getReferrar($customerId)
    {
        $referrar = CustomerNotification::where([['source_id', $customerId], ['notification_type', 10]])->with('info')->first();
        if (! $referrar) {
            return null;
        }

        return $referrar->info;
    }

    //function to get exp status of a customer
    public function getExpStatusOfCustomer($exp_date)
    {
        return 'active';//turned off paid membership
        //get expiry date
        $curDate = date('Y-m-d');
        $cur_date = new DateTime($curDate);
        $expiry_date = new DateTime($exp_date);
        $interval = date_diff($cur_date, $expiry_date);
        $daysRemaining = (int) $interval->format('%R%a');
        session(['days_remaining' => $daysRemaining == 1 ? 1 : $daysRemaining - 1]);

        $status = '';
        if ($daysRemaining <= 0) { //expired
            $status = 'expired';
        } elseif ($daysRemaining <= 11) { //10 days to expire
            $status = '10 days remaining';
        } else {
            $status = 'active';
        }

        return $status;
    }

    public function daysRemaining($date)
    {
        //get expiry date
        $curDate = date('Y-m-d');
        $cur_date = new DateTime($curDate);
        $date2 = new DateTime($date);
        $interval = date_diff($cur_date, $date2);
        $daysRemaining = (int) $interval->format('%R%a');

        return $daysRemaining;
    }

    public function createdAt($date)
    {
        $daysRemaining = $this->daysRemaining($date);
        if ($daysRemaining > '-6') {
            $posted_on = date('Y-M-d H:i:s', strtotime($date));
            \Carbon\Carbon::setLocale('en');
            $created = \Carbon\Carbon::createFromTimeStamp(strtotime($posted_on));
            $created = $created->diffForHumans();
        } else {
            $created = date('M d, Y', strtotime($date));
        }

        return $created;
    }

    public function getMailGun()
    {
        return Mailgun::create('ab04f5d0afc1a6da73eb47e03bb435a1-816b23ef-8be12f89');
    }

    public function sendVerificationEmail($email, $name, $verification_token)
    {
        $message_text = 'Dear '.$name.','."\r\n\r\n";
        $message_text .= 'To confirm your email address, please insert the code on the verification page or click the button below.';

        $data['verify_url'] = url('verify-email/'.$verification_token);
        $data['text'] = $message_text;
        $data['code'] = (new functionController)->encrypt_decrypt('decrypt', $verification_token);

        try {
            $mg = $this->getMailGun();
            $mg->messages()->send('mail.royaltybd.com', [
                'from' => 'Royalty no-reply@royaltybd.com',
                'to' => $email,
                'subject' => 'Royalty Email Verification',
                'html' => view('emails.verification', ['data' => $data])->render(),
            ]);

            return $mg;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getVerificationEmail($email, $name, $verification_token)
    {
        //send mail
        $to = $email;
        $subject = 'Royalty Email Verification';
        $message_text = 'Dear '.$name.','."\r\n\r\n";
        $message_text .= 'To confirm your email address, please insert the code on the verification page or click the button below.';
        $data = [];
        $data['verify_url'] = url('verify-email/'.$verification_token);
        $data['text'] = $message_text;
        $data['code'] = (new functionController)->encrypt_decrypt('decrypt', $verification_token);
        $message = new Swift_Message($subject);
        $message->setFrom(['no-reply@royaltybd.com' => 'Royalty']);
        $message->setTo([$to => $name]);
        // If you want plain text instead, remove the second paramter of setBody
        $message->setBody(view('emails.verification', ['data' => $data])->render(), 'text/html');

        return $message;
    }

    public function getMailer()
    {
        //using mailgun mail service
        $smtpAddress = 'smtp.mailgun.org';
        $port = 587;
        $encryption = 'tls';
        $yourEmail = 'postmaster@mail.royaltybd.com';
        $yourPassword = '510d6536fd66d7981cfd967f8799137d-f696beb4-64f86687';

        // Prepare transport
        $transport = new Swift_SmtpTransport($smtpAddress, $port, $encryption);
        $transport->setUsername($yourEmail);
        $transport->setPassword($yourPassword);
        $mailer = new Swift_Mailer($transport);

        return $mailer;
    }

    public function isVerificationMailSent($email)
    {
        $counter = ResetUser::where('sent_value', $email)
            ->where('verification_type', VerificationType::email_verification)
            ->where('used', 0)->whereBetween('created_at', [now()->subMinutes(Constants::resend_time), now()])->first();
        if ($counter) {
            return $counter;
        } else {
            return null;
        }
    }

    public function isVerificationPhoneOTPSent($phone, $verification_type)
    {
        $counter = ResetUser::where('sent_value', $phone)
            ->where('verification_type', $verification_type)
            ->where('used', 0)->whereBetween('created_at', [now()->subMinutes(Constants::resend_time), now()])->first();
        if ($counter) {
            return $counter;
        } else {
            return null;
        }
    }

    public function isResetSMSSent($phone)
    {
        $counter = ResetUser::where('sent_value', $phone)
            ->where('verification_type', VerificationType::reset_password)
            ->where('used', 0)->whereBetween('created_at', [now()->subMinutes(Constants::resend_time), now()])->first();
        if ($counter) {
            return $counter;
        } else {
            return null;
        }
    }

    public function partnerOfferHeading($partner_account_id)
    {
        $date = date('d-m-Y');
        $main_branch = PartnerBranch::where('partner_account_id', $partner_account_id)->where('main_branch', 1)->with('offers')->first();
        if ($main_branch && $main_branch->offers) {
            $offers = $main_branch->offers->where('active', 1)->sortByDesc('priority');
            $sorted_offers = [];
            $i = 0;
            foreach ($offers as $offer) {
                $offer_date = $offer['date_duration'][0];
                try {
                    if (new DateTime($offer_date['from']) <= new DateTime($date) && new DateTime($offer_date['to']) >= new DateTime($date)) {
                        $sorted_offers[$i++] = $offer;
                    }
                } catch (\Exception $e) {
                }
            }

            $offer_size = count($sorted_offers);
            if ($offer_size > 0) {
                if (($offer_size - 1 > 1)) {
                    return $sorted_offers[0]->offer_description.' and '.($offer_size - 1).' more offers';
                } elseif (($offer_size - 1 == 1)) {
                    return $sorted_offers[0]->offer_description.' and '.($offer_size - 1).' more offer';
                } else {
                    return $sorted_offers[0]->offer_description;
                }
            } else {
                return 'No offer available';
            }
        } else {
            return 'No offer available';
        }
    }

    public function getBranchLocations($branches)
    {
        if (count($branches) > 0) {
            if (count($branches) > 1) {
                return count($branches).' Outlets';
            } else {
                foreach ($branches as $branch) {
                    return $branch->partner_area;
                }
            }
        } else {
            return 'Not available';
        }
    }

    public function makeCustomerHistory()
    {
        $tracks = SslTransactionTable::where('status', 1)->with('info.assignedCard', 'promoUsage', 'cardDelivery')->get();
        foreach ($tracks as $track) {
            $seller_id = null;
            $promo_id = null;
            $type = CustomerType::card_holder;
            if ($track->info && $track->info->assignedCard) {
                $seller_id = $track->info->assignedCard->seller_account_id;
            }
            if ($track->promoUsage) {
                $promo_id = $track->promoUsage->promo_id;
            }
            if ($track->cardDelivery && $track->cardDelivery->delivery_type == DeliveryType::virtual_card) {
                $trial_start_date = date('2019-10-17');
                if ($track->tran_date > $trial_start_date) {
                    $type = CustomerType::trial_user;
                } else {
                    $type = CustomerType::virtual_card_holder;
                }
            }

            $this->addToCustomerHistory($track->customer_id, $seller_id, $type, $track->id, $promo_id);
        }
    }

    public function addToCustomerHistory($customer_id, $seller_id, $type, $ssl_id, $promo_id, $admin_id = null)
    {
        $history = new CustomerHistory();
        $history->customer_id = $customer_id;
        $history->seller_id = $seller_id;
        $history->type = $type;
        $history->ssl_id = $ssl_id;
        $history->admin_id = $admin_id;
        $history->promo_id = $promo_id;
        $history->save();

        return $history;
    }

    public function createSearchStats($customer_id, $branch_id, $key)
    {
        if (!$key) {
            return null;
        }
        $stat = new SearchStat();
        $stat->customer_id = $customer_id;
        $stat->branch_id = $branch_id;
        $stat->key = $key;
        $stat->save();

        return $stat;
    }

    public function getCarousalImagesForWeb()
    {
        return [
            'https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/home-page/carousel/car-intro.png',
            'https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/home-page/carousel/car-food.png',
            'https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/home-page/carousel/car-health.png',
            'https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/home-page/carousel/car-life.png',
            'https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/home-page/carousel/car-beauty.png',
            'https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/home-page/carousel/car-ent.png',
            'https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/home-page/carousel/car-get.png',
        ];
    }

    public function markUserAllNotificationsAsRead($customer_id)
    {
        $result = CustomerNotification::where('user_id', $customer_id)->update(['seen' => 1]);

        return $result;
    }

    public function getPaginatedData($data, $per_page)
    {
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $col = collect($data);
        $perPage = $per_page;
        $currentPageSearchResults = $col->slice(($currentPage * $perPage) - $perPage, $perPage)->values();
        $result = new LengthAwarePaginator($currentPageSearchResults, count($col), $perPage, $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]);

        return $result;
    }

    //save sent message (sms & push notification)
    public function saveSentMessage($type, $title, $body, $to, $lang, $schedule, $image_url = null)
    {
        $sent = $schedule == null ? 1 : 0;

        $history = new SentMessageHistory();
        $history->type = $type;
        $history->title = $title;
        $history->body = $body;
        $history->image_url = $image_url;
        $history->to = $to;
        $history->language = $lang;
        $history->scheduled_at = $schedule;
        $history->sent = $sent;
        $history->save();

        return $history;
    }

    //open/closed of partner according to business hour
    public function compileHours($times, $timestamp)
    {
        $times = $times[strtolower(date('D', $timestamp))];
        if (! strpos($times, '-')) {
            return [];
        }
        $hours = explode(',', $times);
        $hours = array_map('explode', array_pad([], count($hours), '-'), $hours);
        $hours = array_map('array_map', array_pad([], count($hours), 'strtotime'), $hours, array_pad([], count($hours), array_pad([], 2, $timestamp)));
        end($hours);
        if ($hours[key($hours)][0] > $hours[key($hours)][1]) {
            $hours[key($hours)][1] = strtotime('+1 day', $hours[key($hours)][1]);
        }

        return $hours;
    }

    public function isOpen($now, $times)
    {
        $open = 0; // time until closing in seconds or 0 if closed
        // merge opening hours of today and the day before
        $hours = array_merge($this->compileHours($times, strtotime('yesterday', $now)), $this->compileHours($times, $now));
        foreach ($hours as $h) {
            if ($now >= $h[0] and $now < $h[1]) {
                $open = $h[1] - $now;

                return $open;
            }
        }

        return $open;
    }
}
