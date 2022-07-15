<?php

namespace App\Http\Controllers\TransactionRequest\v2;

use App\CustomerInfo;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Enum\PartnerBranchNotificationType;
use App\Http\Controllers\Enum\PartnerRequestType;
use App\Http\Controllers\Enum\PlatformType;
use App\PartnerBranch;
use App\RbdStatistics;
use App\Review;
use App\TransactionTable;
use App\Wish;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class functionController extends Controller
{
    public function getDashboardMetrics($branch_id)
    {
        $branch = PartnerBranch::find($branch_id);
        $metrics = [];
        if ($branch) {
            $metrics['total_transaction'] = $this->getTransactionCount($branch->id);
            $metrics['running_month_transaction'] = $this->getTransactionCount($branch->id, date('Y-m'));
            $metrics['total_review'] = $this->getReviewCount($branch_id);
            $metrics['running_month_review'] = $this->getReviewCount($branch_id, date('Y-m'));
            $metrics['rating'] = (new \App\Http\Controllers\Review\functionController())->getAverageBranchRating($branch_id);
            $metrics['rating_change'] = $this->getRatingChanges($branch->partner_account_id);
            $metrics['total_profile_visit'] = $this->getProfileVisitCount($branch->partner_account_id);
            $metrics['running_month_profile_visit'] = $this->getProfileVisitCount($branch->partner_account_id, date('Y-m'));
            $metrics['peak_hour'] = $this->getAvgPeakHour($branch->id);
        }

        return $metrics;
    }

    public function getTransactionCount($branch_id, $month = null)
    {
        if (! $month) {
            return TransactionTable::where('branch_id', $branch_id)->count();
        } else {
            return TransactionTable::where('branch_id', $branch_id)->where('posted_on', 'like', $month.'%')->count();
        }
    }

    public function getReviewCount($branch_id, $month = null)
    {
        if (! $month) {
            $transaction = TransactionTable::where('branch_id', $branch_id)
                ->with('review')
                ->where('review_id', '!=', null)
                ->get();

            return collect($transaction)->where('review.heading', '!=', null)
                ->where('review.body', '!=', null)->count();
        } else {
            $transaction = TransactionTable::where('branch_id', $branch_id)
                ->with('review')
                ->where('review_id', '!=', null)
                ->where('posted_on', 'like', $month.'%')
                ->get();

            return collect($transaction)->where('review.heading', '!=', null)
                ->where('review.body', '!=', null)->count();
        }
    }

    public function getRating($partner_account_id)
    {
        return Review::rating($partner_account_id);
    }

    public function getRatingChanges($partner_account_id)
    {
        return Review::ratingChanges($partner_account_id, 30);
    }

    public function getProfileVisitCount($partner_account_id, $month = null)
    {
        if (! $month) {
            return RbdStatistics::where('partner_id', $partner_account_id)->count();
        } else {
            return RbdStatistics::where('partner_id', $partner_account_id)->where('visited_on', 'like', $month.'%')->count();
        }
    }

    public function getAvgPeakHour($branch_id)
    {
        $peak_hour = $this->peakHourQuery($branch_id, null, null, true);

        if (count($peak_hour) > 0) {
            $time = date('h:i A', strtotime($peak_hour[0]->hours.':00:00'));
        } else {
            $time = 'N/A';
        }

        return $time;
    }

    public function getPeakHour($branch_id, $from = null, $to = null, $allTime = true)
    {
        if (! $from && ! $to) {
            $from = $to = date('Y-m-d');
        }
        $from = $from.' 00:00:00';
        $to = $to.' 23:59:59';

        $peak_hour = $this->peakHourQuery($branch_id, $from, $to, $allTime);
        $peak_24hour = [0, 0, 0, null, null, null, null, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        for ($i = 0; $i <= 23; $i++) {
            if (isset($peak_hour[$i])) {
                $peak_24hour[(int) ($peak_hour[$i]->hours)] = $peak_hour[$i]->total_count;
            }
        }

        return $peak_24hour;
    }

    public function peakHourQuery($branch_id, $from = null, $to = null, $allTime = false)
    {
        if (! $from && ! $to) {
            $from = $to = date('Y-m-d');
        }
        $from = $from.' 00:00:00';
        $to = $to.' 23:59:59';
        if ($allTime) {
            return DB::select("select HOUR(posted_on) as hours, count(HOUR(posted_on)) as total_count
                            from transaction_table
                            where branch_id = '$branch_id'
                            group by hours
                            order by total_count desc");
        } else {
            return DB::select("select HOUR(posted_on) as hours, count(HOUR(posted_on)) as total_count
                            from transaction_table
                            where branch_id = '$branch_id'
                            and posted_on >= '$from'
                            and posted_on <= '$to'
                            group by hours
                            order by total_count desc");
        }
    }

    public function getTopTransactors($branch_id, $limit)
    {
        if ($limit == 'all') {
            return DB::select("select count(tt.customer_id) as transaction_count, tt.customer_id,ci.customer_full_name,ci.customer_profile_image
                                        from transaction_table as tt
                                        join customer_info ci on tt.customer_id = ci.customer_id
                                        where tt.branch_id = $branch_id
                                        group by tt.customer_id,ci.customer_full_name,ci.customer_profile_image
                                        order by transaction_count desc");
        } else {
            return DB::select("select count(tt.customer_id) as transaction_count, tt.customer_id,ci.customer_full_name,ci.customer_profile_image
                                        from transaction_table as tt
                                        join customer_info ci on tt.customer_id = ci.customer_id
                                        where tt.branch_id = $branch_id
                                        group by tt.customer_id,ci.customer_full_name,ci.customer_profile_image
                                        order by transaction_count desc limit $limit");
        }
    }

    public function addPartnerRequest($branch_user_id, $comment, $type)
    {
        $request = new Wish([
            'customer_id' => $branch_user_id,
            'comment' => $comment,
            'posted_on' => date('Y-m-d H:i:s'),
            'partner_request_type' => $type,
        ]);
        $request->save();
        (new \App\Http\Controllers\AdminNotification\functionController())->partnerOfferRequest($request);

        return $request;
    }

    public function getNotificationView($notifications)
    {
        $output = '';
        \Carbon\Carbon::setLocale('en');
        foreach ($notifications as $notification) {
            if ($notification['notification_type'] == PartnerBranchNotificationType::TRANSACTION_REQUEST) { //transaction notification
                if ($notification['seen'] == 0) {
                    $output .= "<li class='unseen_notification'>";
                } else {
                    $output .= '<li>';
                }

                $output .= '<a class="notification_title_color" href="'.url('partner/branch/notification/transaction/'
                        .$notification['id']).'">';
                $output .= '<div class="col-md-3 col-sm-3 col-xs-3"><div class="notify-img">';
                $output .= '<img src="'.asset($notification->image).'" class="img-circle n-img img-40 primary-border w-100" alt="notif-img">';
                $output .= '</div></div>';
                $output .= '<div class="col-md-9 col-sm-9 col-xs-9 pd-l0">';
                if ($notification->request->redeem_id) {
                    $output .= '<p>'.$notification->notification_text.'Quantity: '.
                        $notification->request->redeem->quantity.'</p>';
                } else {
                    $output .= '<p>'.$notification->notification_text.'</p>';
                }
                $output .= '<p class="time"><i class="bx bx-time-five" aria-hidden="true"></i> ';

                $output .= Carbon::parse($notification->posted_on)->diffForHumans();

                $output .= '</p></div></a>';

                $output .= '</li>';
            } elseif ($notification['notification_type'] == PartnerBranchNotificationType::LIKE_POST) { //post like notification
                if ($notification['seen'] == 0) {
                    $output .= "<li class='unseen_notification'>";
                } else {
                    $output .= '<li>';
                }

                $output .= '<a class="notification_title_color" href="'.url('partner/branch/notification/post_like/'
                        .$notification['id']).'">';
                $output .= '<div class="col-md-3 col-sm-3 col-xs-3"><div class="notify-img">';
                $output .= '<img src="'.asset($notification->image).'" class="img-circle n-img img-40 primary-border w-100" alt="notif-img">';
                $output .= '</div></div>';
                $output .= '<div class="col-md-9 col-sm-9 col-xs-9 pd-l0">';
                $output .= '<p>'.$notification->notification_text.'</p>';
                $output .= '<p class="time"><i class="bx bx-time-five" aria-hidden="true"></i> ';

                $output .= Carbon::parse($notification->posted_on)->diffForHumans();

                $output .= '</p></div></a>';

                $output .= '</li>';
            } elseif ($notification['notification_type'] == PartnerBranchNotificationType::REVIEW_POST) {//review post notification
                if ($notification['seen'] == 0) {
                    $output .= "<li class='unseen_notification'>";
                } else {
                    $output .= '<li>';
                }
                $output .= '<a class="notification_title_color" href="'.url('partner/branch/notification/review/'.$notification['id']).'">';
                $output .= '<div class="col-md-3 col-sm-3 col-xs-3"><div class="notify-img">';
                $output .= '<img src="'.asset($notification->image).'" class="img-circle n-img img-40 primary-border w-100" alt="notif-img">';
                $output .= '</div></div>';
                $output .= '<div class="col-md-9 col-sm-9 col-xs-9 pd-l0">';
                $output .= '<p>'.$notification->notification_text.'</p>';
                $output .= '<p class="time"><i class="bx bx-time-five" aria-hidden="true"></i> ';
                $output .= Carbon::parse($notification->posted_on)->diffForHumans();
                $output .= '</p></div></a>';
                $output .= '</li>';
            } elseif ($notification['notification_type'] == PartnerBranchNotificationType::OFFER_AVAILED) {//offer/reward availed
                if ($notification['seen'] == 0) {
                    $output .= "<li class='unseen_notification'>";
                } else {
                    $output .= '<li>';
                }
                $output .= '<a class="notification_title_color" href="'.url('partner/branch/notification/offer_availed/'.$notification['id']).'">';
                $output .= '<div class="col-md-3 col-sm-3 col-xs-3"><div class="notify-img">';
                $output .= '<img src="'.asset($notification->image).'" class="img-circle n-img img-40 primary-border w-100" alt="notif-img">';
                $output .= '</div></div>';
                $output .= '<div class="col-md-9 col-sm-9 col-xs-9 pd-l0">';
                $output .= '<p>'.$notification->notification_text.'</p>';
                $output .= '<p class="time"><i class="bx bx-time-five" aria-hidden="true"></i> ';
                $output .= Carbon::parse($notification->posted_on)->diffForHumans();
                $output .= '</p></div></a>';
                $output .= '</li>';
            } elseif ($notification['notification_type'] == PartnerBranchNotificationType::DEAL_AVAILED) {//deal availed
                if ($notification['seen'] == 0) {
                    $output .= "<li class='unseen_notification'>";
                } else {
                    $output .= '<li>';
                }
                $output .= '<a class="notification_title_color" href="'.url('partner/branch/notification/deal_availed/'.$notification['id']).'">';
                $output .= '<div class="col-md-3 col-sm-3 col-xs-3"><div class="notify-img">';
                $output .= '<img src="'.asset($notification->image).'" class="img-circle n-img img-40 primary-border w-100" alt="notif-img">';
                $output .= '</div></div>';
                $output .= '<div class="col-md-9 col-sm-9 col-xs-9 pd-l0">';
                $output .= '<p>'.$notification->notification_text.'</p>';
                $output .= '<p class="time"><i class="bx bx-time-five" aria-hidden="true"></i> ';
                $output .= Carbon::parse($notification->posted_on)->diffForHumans();
                $output .= '</p></div></a>';
                $output .= '</li>';
            }
        }

        return $output;
    }

    public function getAllNotificationView($notifications)
    {
        $output = '';
        if (count($notifications) > 0) {
            foreach ($notifications as $notification) {
                $created = (new \App\Http\Controllers\functionController2())->createdAt($notification->posted_on);
                if ($notification['notification_type'] == PartnerBranchNotificationType::TRANSACTION_REQUEST) { //transaction notification
                    $output .= '<div class="row">';
                    $output .= '<div class="col-md-12 col-xs-12 border-bottom">';
                    $output .= '<div>';
                    $output .= '<a class="notification_title_color" href="'.url('partner/branch/notification/transaction/'.$notification->id).'">';
                    $output .= '<div class="col-md-1 col-sm-1 col-xs-1">';
                    $output .= '<div class="notify-img">';
                    $output .= '<img src="'.$notification->image.'"
                                 class="img-circle n-img img-40 primary-border w-100" alt="notif-img">';
                    $output .= '</div></div>';
                    $output .= '<div class="col-md-11 col-sm-11 col-xs-11 pd-l0">';
                    $output .= '<p>'.$notification->notification_text.'</p>';
                    $output .= '<p class="time">';
                    $output .= '<i class="bx bx-time-five" aria-hidden="true"></i>';
                    $output .= $created;
                    $output .= '</p></div></a></div></div></div>';
                } elseif ($notification['notification_type'] == PartnerBranchNotificationType::LIKE_POST) {
                    $output .= '<div class="row">';
                    $output .= '<div class="col-md-12 col-xs-12 border-bottom">';
                    $output .= '<div>';
                    $output .= '<a class="notification_title_color" href="'.url('partner/branch/notification/post_like/'.$notification->id).'">';
                    $output .= '<div class="col-md-1 col-sm-1 col-xs-1">';
                    $output .= '<div class="notify-img">';
                    $output .= '<img src="'.$notification->image.'"
                                 class="img-circle n-img img-40 primary-border w-100" alt="notif-img">';
                    $output .= '</div></div>';
                    $output .= '<div class="col-md-11 col-sm-11 col-xs-11 pd-l0">';
                    $output .= '<p>'.$notification->notification_text.'</p>';
                    $output .= '<p class="time">';
                    $output .= '<i class="bx bx-time-five" aria-hidden="true"></i>';
                    $output .= $created;
                    $output .= '</p></div></a></div></div></div>';
                } else {
                    $output .= '<div class="row">';
                    $output .= '<div class="col-md-12 col-xs-12 border-bottom">';
                    $output .= '<div>';
                    $output .= '<a class="notification_title_color" href="'.url('partner/branch/notification/review/'.$notification->id).'">';
                    $output .= '<div class="col-md-1 col-sm-1 col-xs-1">';
                    $output .= '<div class="notify-img">';
                    $output .= '<img src="'.$notification->image.'"
                                 class="img-circle n-img img-40 primary-border w-100" alt="notif-img">';
                    $output .= '</div></div>';
                    $output .= '<div class="col-md-11 col-sm-11 col-xs-11 pd-l0">';
                    $output .= '<p>'.$notification->notification_text.'</p>';
                    $output .= '<p class="time">';
                    $output .= '<i class="bx bx-time-five" aria-hidden="true"></i>';
                    $output .= $created;
                    $output .= '</p></div></a></div></div></div>';
                }
            }
        } else {
            $output .= __('partner/notification.no_notification');
        }

        return $output;
    }

    public function checkBranchUserPin($branch_id, $pin)
    {
        $scanner = DB::table('branch_scanner as bs')
                    ->join('branch_user as bu', 'bu.id', '=', 'bs.branch_user_id')
                    ->select('bu.*')
                    ->where('bs.branch_id', $branch_id)
                    ->where('bu.pin_code', $pin)
                    ->where('bu.active', 1)
                    ->first();

        return $scanner;
    }
}
