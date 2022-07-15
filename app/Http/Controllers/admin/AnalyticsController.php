<?php

namespace App\Http\Controllers\admin;

use App\AllAmounts;
use App\CustomerAccount;
use App\CustomerActivitySession;
use App\CustomerHistory;
use App\CustomerInfo;
use App\CustomerPoint;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Enum\AdminScannerType;
use App\Http\Controllers\Enum\CustomerType;
use App\Http\Controllers\Enum\DeliveryType;
use App\Http\Controllers\Enum\PlatformType;
use App\Http\Controllers\functionController;
use App\Http\Controllers\functionController2;
use App\Http\Controllers\jsonController;
use App\PartnerAccount;
use App\PartnerBranch;
use App\PartnerInfo;
use App\RbdStatistics;
use App\TransactionTable;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Claims\Custom;

class AnalyticsController extends Controller
{
    public function allCounters()
    {
        $result = (new functionController())->usersPartnersNumber();

        return response()->json($result);
    }

    //function to get user leaderboard
    public function sortUserLeaderboard(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');
        $from = $from.' 00:00:00';
        $to = $to.' 23:59:59';
        $user_count_to_show = $request->input('user_count_to_show');
        $leaderboard_data = DB::select("select sum(tt.transaction_point) as total_point, count(tt.customer_id) as transaction_count,
                                       tt.customer_id, (select ch.type from customer_history as ch where tt.customer_id = ch.customer_id
                                            order by ch.id desc limit 1) as user_type, ci.customer_contact_number, ci.customer_full_name
                                    from transaction_table as tt
                                         join customer_info ci on tt.customer_id = ci.customer_id
                                    where tt.posted_on >= '$from' and tt.posted_on <= '$to' and deleted_at is null 
                                    group by tt.customer_id, user_type, ci.customer_contact_number, ci.customer_full_name
                                    order by transaction_count desc, total_point desc");
        if ($user_count_to_show != null) {
            $leaderboard_data = array_slice($leaderboard_data, 0, $user_count_to_show);
        }

        return \response()->json($leaderboard_data);
    }

    public function sortUserCreditLeaderboard(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');
        $from = $from.' 00:00:00';
        $to = $to.' 23:59:59';
        $leaderboard_data = DB::select("select customer_id,sum(transaction_point) credit_sum
                                        from
                                            (
                                                select customer_id, transaction_point
                                                from transaction_table
                                                where posted_on >= '$from' and posted_on <= '$to'
                                                union all
                                                select customer_id,point
                                                from customer_points
                                                where created_at >= '$from' and created_at <= '$to'
                                            ) t
                                        group by customer_id order by credit_sum DESC ");
        foreach ($leaderboard_data as $key => $user) {
            $customer = CustomerInfo::where('customer_id', $user->customer_id)->first();
            if ($customer) {
                $user->customer = $customer;
            } else {
                unset($leaderboard_data[$key]);
                continue;
            }
            $user->history = CustomerHistory::where('customer_id', $user->customer_id)->orderBy('id', 'DESC')->first();
        }

        return \response()->json($leaderboard_data);
    }

    public function sortPartnerLeaderBoard(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');
        $from = $from.' 00:00:00';
        $to = $to.' 23:59:59';
        $partner_count_to_show = $request->input('partner_count_to_show');

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

        //till current day
        $i = 0;
        foreach ($branches as $branch) {
            $current_transaction_count = TransactionTable::where('branch_id', $branch->id)
                ->where('branch_user_id', '!=', AdminScannerType::accept_tran_req)
                ->where('branch_user_id', '!=', AdminScannerType::manual_transaction)
                ->where('posted_on', '>=', $from)->where('posted_on', '<=', $to)->count();
            $current_admin_transaction_count = TransactionTable::where('branch_id', $branch->id)
                ->where(function ($query) {
                    $query->where('branch_user_id', '=', AdminScannerType::accept_tran_req)
                        ->orWhere('branch_user_id', '=', AdminScannerType::manual_transaction);
                })
                ->where('posted_on', '>=', $from)->where('posted_on', '<=', $to)->count();
            $leaderBoard[$i]['profile_image'] = $branch->info->profileImage->partner_profile_image;
            $leaderBoard[$i]['partner_name'] = $branch->info->partner_name;
            $leaderBoard[$i]['area'] = $branch->partner_area;
            $leaderBoard[$i]['branch_id'] = $branch->id;
            $leaderBoard[$i]['branch_point'] = $current_transaction_count * $scan_point;
            $leaderBoard[$i]['admin_point'] = $current_admin_transaction_count * $scan_point;
            $leaderBoard[$i]['point'] = ($current_admin_transaction_count * $scan_point) + ($current_transaction_count * $scan_point);
            $leaderBoard[$i]['prev_date'] = \Carbon\Carbon::yesterday()->toDateString();
            $leaderBoard[$i]['prev_index'] = null;
            $i++;
        }
        $array_point = array_column($leaderBoard, 'point');
        $array_name = array_column($leaderBoard, 'partner_name');
        array_multisort($array_point, SORT_DESC, $array_name, SORT_ASC, $leaderBoard);
        if ($partner_count_to_show != null) {
            $leaderBoard = array_slice($leaderBoard, 0, $partner_count_to_show);
        }

        return \response()->json($leaderBoard);

//        $selected_time = $year . '-' . $month;
//        $cur_time = date("Y-m");
//        if ($cur_time == $selected_time) {
//            $leaderBoard = (new PartnerBranchController())->leaderBoardData();
//        } else {
//            $accounts = PartnerAccount::where('active', 1)->with('branches.info.profileImage')->get();
//            $i = 0;
//            foreach ($accounts as $account) {
//                if ($account->active == 1) {
//                    foreach ($account->branches as $branch) {
//                        if ($branch->active == 1) {
//                            $branches[$i] = $branch;
//                            $i++;
//                        }
//                    }
//                }
//            }
//            $scan_point = AllAmounts::all();
//
//            //till current day
//            $i = 0;
//            foreach ($branches as $branch) {
//                $current_transaction_count = TransactionTable::where('branch_id', $branch->id)
//                    ->where('posted_on', 'like', $selected_time . '%')->count();
//                $leaderBoard[$i]["profile_image"] = $branch->info->profileImage->partner_profile_image;
//                $leaderBoard[$i]["partner_name"] = $branch->info->partner_name;
//                $leaderBoard[$i]["area"] = $branch->partner_area;
//                $leaderBoard[$i]["branch_id"] = $branch->id;
//                $leaderBoard[$i]["point"] = $current_transaction_count * $scan_point[10]['price'];
//                $leaderBoard[$i]["prev_date"] = \Carbon\Carbon::yesterday()->toDateString();
//                $leaderBoard[$i]["prev_index"] = null;
//                $i++;
//            }
//            $array_point = array_column($leaderBoard, 'point');
//            $array_name = array_column($leaderBoard, 'partner_name');
//            array_multisort($array_point, SORT_DESC, $array_name, SORT_ASC, $leaderBoard);
//        }
//        if ($partner_count_to_show != null){
//            $leaderBoard = array_slice($leaderBoard, 0, $partner_count_to_show);
//        }
//        return \response()->json($leaderBoard);
    }

    public function visitAnalytics(Request $request)
    {
        $sort = $request->post('sort');

        if ($sort == 'true') {
            $year = $request->post('year');
            $month = $request->post('month');
            $partner = $request->post('partner');

            $visits = $this->sortRbdPartnerVisitsAnalytics($year, $month, $partner);

            return response()->json($visits);
        } else {
            $partner_visits = DB::select('select count(partner_id) as total, partner_id, pi.partner_name
                    from rbd_statistics
                            join partner_info as pi on partner_id = pi.partner_account_id
                    group by partner_id, pi.partner_name
                    order by total desc
                    limit 5');

            $visits = [];
            foreach ($partner_visits as $key => $visit) {
                $visits[$key]['partner'] = $visit->partner_name;
                $visits[$key]['total'] = $visit->total;
                $app_visit = RbdStatistics::where([['partner_id', $visit->partner_id], ['browser_data', 'like', 'Android Application%']])->count();
                $ios_app_visit = RbdStatistics::where([['partner_id', $visit->partner_id], ['browser_data', 'like', 'iOS Application%']])->count();
                $visits[$key]['android_app'] = $app_visit;
                $visits[$key]['ios_app'] = $ios_app_visit;
                $visits[$key]['web'] = $visit->total - $app_visit;
            }

            return response()->json($visits);
        }
    }

    public function allPartnerVisitAnalytics()
    {
        $partner_visits = DB::select('select count(partner_id) as total, partner_id, pi.partner_name
                    from rbd_statistics
                            join partner_info as pi on partner_id = pi.partner_account_id
                    group by partner_id, pi.partner_name
                    order by total desc');
        $visits = [];
        foreach ($partner_visits as $key => $visit) {
            $visits[$key]['partner'] = $visit->partner_name;
            $visits[$key]['total'] = $visit->total;
            $app_visit = RbdStatistics::where([['partner_id', $visit->partner_id], ['browser_data', 'like', 'Android Application%']])->count();
            $visits[$key]['app'] = $app_visit;
            $visits[$key]['web'] = $visit->total - $app_visit;
        }

        return view('admin.production.analytics.all_partner_visit_analytics', compact('visits'));
    }

    public function tran_data($transactions)
    {
        $months = [];
        $result = [];
        foreach ($transactions as $key => $transaction) {
            array_push($months, ''.$key);
        }
        for ($i = 1; $i <= 12; $i++) {
            $index = $i <= 9 ? '0'.$i : ''.$i;
            if (in_array($i, $months)) {
                $result[$i]['all'] = count($transactions[$index]);
                $result[$i]['card'] = count($transactions[$index]->where('customerHistory.type', CustomerType::card_holder));
                $result[$i]['trial'] = count($transactions[$index]->where('customerHistory.type', CustomerType::trial_user));
            } else {
                $result[$i]['all'] = 0;
                $result[$i]['card'] = 0;
                $result[$i]['trial'] = 0;
            }
        }

        return $result;
    }

    public function reg_data($year)
    {
//        $guest = DB::select("select count(customer_id) as count, DATE_FORMAT(member_since, '%$year-%m') as month
//                    from customer_info
//                    where customer_type=3
//                      and DATE_FORMAT(member_since, '%Y') = '$year'
//                    group by DATE_FORMAT(member_since, '%$year-%m')");

        $trial = DB::select("select count(stt.tran_date) as count, DATE_FORMAT(stt.tran_date, '%$year-%m') as month
                    from ssl_transaction_table as stt
                         join customer_history ch on stt.id = ch.ssl_id
                    where stt.tran_date is not null
                      and ch.type = 3
                      and stt.status = 1
                      and DATE_FORMAT(tran_date, '%Y') = '$year'
                    group by DATE_FORMAT(stt.tran_date, '%$year-%m')");

        $card_user = DB::select("select count(stt.tran_date) as count, DATE_FORMAT(stt.tran_date, '%$year-%m') as month
                    from ssl_transaction_table as stt
                         join customer_history ch on stt.id = ch.ssl_id
                    where stt.tran_date is not null
                      and ch.type = 1
                      and stt.status = 1
                      and DATE_FORMAT(tran_date, '%Y') = '$year'
                    group by DATE_FORMAT(stt.tran_date, '%$year-%m')");

        $data = [];
        //guest user data
//        $guest_user = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
//        for ($i = 1; $i <= 12; $i++) {
//            if (isset($guest[$i - 1])) {
//                $month = explode('-', $guest[$i - 1]->month);
//                $guest_user[(int)($month[1] - 1)] = $guest[$i - 1]->count;
//            }
//        }

        //trial user data
        $trial_user = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        for ($i = 1; $i <= 12; $i++) {
            if (isset($trial[$i - 1])) {
                $month = explode('-', $trial[$i - 1]->month);
                $trial_user[(int) ($month[1] - 1)] = $trial[$i - 1]->count;
            }
        }

        //card user data
        $card_holder = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        for ($i = 1; $i <= 12; $i++) {
            if (isset($card_user[$i - 1])) {
                $month = explode('-', $card_user[$i - 1]->month);
                $card_holder[(int) ($month[1] - 1)] = $card_user[$i - 1]->count;
            }
        }

//        for ($i = 1; $i <= 12; $i++) {
//            $monthName = date("M", mktime(0, 0, 0, $i, 10));
//            $data[$monthName]['guest_user'] = $guest_user[$i - 1];
//        }
        for ($i = 1; $i <= 12; $i++) {
            $monthName = date('M', mktime(0, 0, 0, $i, 10));
            $data[$monthName]['trial_user'] = $trial_user[$i - 1];
        }
        for ($i = 1; $i <= 12; $i++) {
            $monthName = date('M', mktime(0, 0, 0, $i, 10));
            $data[$monthName]['card_user'] = $card_holder[$i - 1];
        }

        return $data;
    }

    public function registrationAnalytics(Request $request)
    {
        $sort = $request->post('sort');
        if ($sort == 'true') {
            $year = $request->post('year');
            $data = $this->reg_data($year);
        } else {
            $year = '2019';
            $data = $this->reg_data($year);
        }

        return response()->json($data);

        $sort = $request->post('sort');
        if ($sort == 'true') {
            $year = $request->post('year');
            $month = $request->post('month');

            if (! $year) {
                return response()->json('missing_params');
            }
            $result = (new functionController)->sortRbdRegUserStatistics($year, $month);

            return response()->json($result);
        } else {
            $allCustomers = DB::table('customer_info as ci')
                ->join('customer_history', function ($join) {
                    $join->on('customer_history.customer_id', '=', 'ci.customer_id')
                        ->on('customer_history.id', '=', DB::raw('(SELECT max(id) from customer_history WHERE customer_history.customer_id = ci.customer_id)'));
                })
                ->join('ssl_transaction_table as stt', 'stt.id', '=', 'customer_history.ssl_id')
                ->select('ci.customer_type', 'customer_history.type', 'stt.tran_date')
                ->get();
            $guest = CustomerInfo::where('customer_type', 3)->count();

            $card_user = $trial_users = 0;
            foreach ($allCustomers as $customer) {
                if ($customer->type == 1) {
                    $card_user++;
                } elseif ($customer->type == 3) {
                    $trial_users++;
                }
            }
            $total_users = count($allCustomers) + $guest;

            return response()->json(['guest_user' => $guest, 'trial_user' => $trial_users, 'card_user' => $card_user, 'allCustomers' => $total_users]);
        }
    }

    public function peakHourAnalytics(Request $request)
    {
        $sort = $request->post('sort');
        if ($sort == 'true') {
            $from = $request->post('from');
            $to = $request->post('to');
        } else {
            $from = $to = date('Y-m-d');
        }
        $from = $from.' 00:00:00';
        $to = $to.' 23:59:59';

        $web = PlatformType::web;
        $android = PlatformType::android;
        $ios = PlatformType::ios;
        $data = [];
        $web_peak_hour = DB::select("select HOUR(created_at) as hours, count(HOUR(created_at)) as total_count
                            from customer_activity_sessions
                            where platform = '$web'
                            and created_at >= '$from'
                            and created_at <= '$to'
                            group by hours
                            order by total_count desc");
        $web_24_peak_hour = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        for ($i = 0; $i <= 23; $i++) {
            if (isset($web_peak_hour[$i])) {
                $web_24_peak_hour[(int) ($web_peak_hour[$i]->hours)] = $web_peak_hour[$i]->total_count;
            }
        }

        $android_peak_hour = DB::select("select HOUR(created_at) as hours, count(HOUR(created_at)) as total_count
                            from customer_activity_sessions
                            where platform = '$android'
                            and created_at >= '$from'
                            and created_at <= '$to'
                            group by hours
                            order by total_count desc");
        $android_24_peak_hour = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        for ($i = 0; $i <= 23; $i++) {
            if (isset($android_peak_hour[$i])) {
                $android_24_peak_hour[(int) ($android_peak_hour[$i]->hours)] = $android_peak_hour[$i]->total_count;
            }
        }

        $ios_peak_hour = DB::select("select HOUR(created_at) as hours, count(HOUR(created_at)) as total_count
                            from customer_activity_sessions
                            where platform = '$ios'
                            and created_at >= '$from'
                            and created_at <= '$to'
                            group by hours
                            order by total_count desc");
        $ios_24_peak_hour = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        for ($i = 0; $i <= 23; $i++) {
            if (isset($ios_peak_hour[$i])) {
                $ios_24_peak_hour[(int) ($ios_peak_hour[$i]->hours)] = $ios_peak_hour[$i]->total_count;
            }
        }

        for ($i = 0; $i <= 23; $i++) {
            $data[$i]['web'] = $web_24_peak_hour[$i];
            $data[$i]['android'] = $android_24_peak_hour[$i];
            $data[$i]['ios'] = $ios_24_peak_hour[$i];
        }

        return \response()->json($data);
    }

    //sort Rbd Partner Transaction Analytics Json
    public function sortRbdPartnerTransactionAnalytics($year, $attr)
    {
        if (! $year) {
            return response()->json('missing_params');
        }
        $attr = explode(' => ', $attr);
        if (isset($attr[0]) && isset($attr[1])) {
            $partner_info = DB::table('partner_info as pi')
                ->join('partner_branch as pb', 'pb.partner_account_id', '=', 'pi.partner_account_id')
                ->select('pb.partner_email', 'pb.partner_address', 'pi.partner_name', 'pb.id')
                ->where('pi.partner_name', 'like', '%'.$attr[0].'%')
                ->where('pb.partner_address', 'like', '%'.$attr[1].'%')
                ->get();
            $partner_info = json_decode(json_encode($partner_info), true);

            if ($partner_info == null) {
                return response()->json('invalid_partner');
            }
            $partner_branch_id = $partner_info[0]['id'];

            $transactions = TransactionTable::where('posted_on', 'like', date($year).'%')
                ->where('branch_id', $partner_branch_id)
                ->with('customerHistory')
                ->get()
                ->groupBy(function ($d) {
                    return Carbon::parse($d->posted_on)->format('m');
                });

            return $transactions;
        } else {
            $transactions = TransactionTable::where('posted_on', 'like', date($year).'%')
                ->with('customerHistory')
                ->get()
                ->groupBy(function ($d) {
                    return Carbon::parse($d->posted_on)->format('m');
                });

            return $transactions;
        }
    }

    //sort registered users analytics of rbd json
    public function sortRbdUserStatsAnalyticsJson(Request $request)
    {
        $area = $request->get('statsAnalyticsByArea');

        if (! $area) {
            return response()->json('missing_params');
        }

        $sortedUserStatsAnalytics = (new functionController)->sortRbdUserAreaStatistics($area, 'json');

        return $sortedUserStatsAnalytics;
    }

    //sort analytics of rbd json
    public function sortRbdPartnerVisitsAnalytics($year, $month, $attr)
    {
        if (! $year) {
            return 'missing_params';
        }

        $attr = explode(' => ', $attr);
        if (isset($attr[0]) && isset($attr[1])) {
            //get all users & partners statistics
            $partner = $attr[0];
            //id from partner name
            $partner_id = DB::table('partner_info')->select('partner_account_id')->where('partner_name', $partner)->first();
            if ($partner_id == null) {
                return 'invalid_partner';
            }
            $partner_id = $partner_id->partner_account_id;

            if ($partner != null) {
                //get partner visit statistics sorted
                $sortedRbdAnalytics = (new functionController)->sortRbdPartnerVisitStatistics($year, $month, $partner_id);
            } else {
                //get partner visit statistics sorted
                $sortedRbdAnalytics = (new functionController)->sortRbdPartnerVisitStatistics($year, $month, $partner);
            }

            return $sortedRbdAnalytics;
        } else {
            $sortedRbdAnalytics = (new functionController)->sortRbdPartnerVisitStatistics($year, $month, null);

            return $sortedRbdAnalytics;
        }
    }

    public function perDayUserAnalytics(Request $request)
    {
        $showing_date = $request->input('date');

        $trial_user = CustomerType::trial_user;
        $premium_user = CustomerType::card_holder;
        $data = [];
        $hourly_trial_user = DB::select("select HOUR(created_at) as hours, count(HOUR(created_at)) as total_count
                                    from customer_history
                                    where created_at like '$showing_date%' and type = '$trial_user'
                                    group by hours
                                    order by hours asc ");
        $trial_24_hour = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        for ($i = 0; $i <= 23; $i++) {
            if (isset($hourly_trial_user[$i])) {
                $trial_24_hour[(int) ($hourly_trial_user[$i]->hours)] = $hourly_trial_user[$i]->total_count;
            }
        }

        $hourly_premium_user = DB::select("select HOUR(created_at) as hours, count(HOUR(created_at)) as total_count
                                    from customer_history
                                    where created_at like '$showing_date%' and type = '$premium_user'
                                    group by hours
                                    order by hours asc ");
        $premium_24_hour = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        for ($i = 0; $i <= 23; $i++) {
            if (isset($hourly_premium_user[$i])) {
                $premium_24_hour[(int) ($hourly_premium_user[$i]->hours)] = $hourly_premium_user[$i]->total_count;
            }
        }

        for ($i = 0; $i <= 23; $i++) {
            $data[$i]['all'] = $trial_24_hour[$i] + $premium_24_hour[$i];
            $data[$i]['trial_user'] = $trial_24_hour[$i];
            $data[$i]['premium_user'] = $premium_24_hour[$i];
        }

        return \response()->json($data);
    }

    public function perWeekUserAnalytics(Request $request)
    {
        $showing_date = $request->input('date');
        $date_7_days_before = Carbon::createFromFormat('Y-m-d', $showing_date)->subDays(8)->toDateString();

        $trial_user = DeliveryType::virtual_card;
        $array_trial_user = DB::select("select DATE(tran_date) as date, count(DATE(tran_date)) as total_count
                                            from ssl_transaction_table
                                            join card_delivery cd on ssl_transaction_table.id = cd.ssl_id
                                            where tran_date >= '$date_7_days_before' and tran_date <= '$showing_date'
                                              and delivery_type = '$trial_user'  and status = 1
                                            group by date
                                            order by date asc");
        $data_7_days = [];
        for ($i = 1; $i < 8; $i++) {
            $n_date = Carbon::createFromFormat('Y-m-d', $showing_date)->subDays($i)->toDateString();
            $print_date = Carbon::createFromFormat('Y-m-d', $showing_date)->subDays($i)->format('d M, Y');
            $found = false;
            for ($j = 0; $j < count($array_trial_user); $j++) {
                if ($array_trial_user[$j]->date == (string) $n_date) {
                    $data_7_days[$i]['date'] = (string) $print_date;
                    $data_7_days[$i]['trial_count'] = $array_trial_user[$j]->total_count;
                    $found = true;
                    break;
                }
            }
            if (! $found) {
                $data_7_days[$i]['date'] = (string) $print_date;
                $data_7_days[$i]['trial_count'] = 0;
            }
        }

        $array_premium_user = DB::select("select DATE(tran_date) as date, count(DATE(tran_date)) as total_count
                                            from ssl_transaction_table
                                            join card_delivery cd on ssl_transaction_table.id = cd.ssl_id
                                            where tran_date >= '$date_7_days_before' and tran_date <= '$showing_date'
                                              and delivery_type != '$trial_user'  and status = 1
                                            group by date
                                            order by date asc");
        for ($i = 1; $i < 8; $i++) {
            $n_date = Carbon::createFromFormat('Y-m-d', $showing_date)->subDays($i)->toDateString();
            $found = false;
            for ($j = 0; $j < count($array_premium_user); $j++) {
                if ($array_premium_user[$j]->date == (string) $n_date) {
                    $data_7_days[$i]['premium_count'] = $array_premium_user[$j]->total_count;
                    $found = true;
                    break;
                }
            }
            if (! $found) {
                $data_7_days[$i]['premium_count'] = 0;
            }
        }

        return \response()->json($data_7_days);
    }

    public function periodicUserAnalytics(Request $request)
    {
        $month_range = $request->input('period');
        $current_month = date('m');
        $month_name_array = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $trial_user = DeliveryType::virtual_card;
        $array_trial_user = DB::select("select DATE_FORMAT(tran_date,'%b') as month_name, count(DATE(tran_date)) as total_count, MONTH(tran_date) as month_number
                                        from ssl_transaction_table
                                                 join card_delivery cd on ssl_transaction_table.id = cd.ssl_id
                                        where tran_date >= last_day(now()) + interval 1 day - interval '$month_range' month
                                          and delivery_type = '$trial_user'
                                        group by month_number,month_name
                                        order by month_number asc");

        $array_premium_user = DB::select("select DATE_FORMAT(tran_date,'%b') as month_name, count(DATE(tran_date)) as total_count, MONTH(tran_date) as month_number
                                        from ssl_transaction_table
                                                 join card_delivery cd on ssl_transaction_table.id = cd.ssl_id
                                        where tran_date >= last_day(now()) + interval 1 day - interval '$month_range' month
                                          and delivery_type != '$trial_user'
                                        group by month_number,month_name
                                        order by month_number asc");

        $data = [];
        for ($i = 0; $i < count($month_name_array); $i++) {
            $data[$i]['month'] = $month_name_array[$i];
            for ($j = 0; $j < count($array_trial_user); $j++) {
                if (($array_trial_user[$j]->month_number - 1) == $i) {
                    $data[$i]['trial_count'] = $array_trial_user[$j]->total_count;
                    break;
                } else {
                    $data[$i]['trial_count'] = 0;
                }
            }

            for ($k = 0; $k < count($array_premium_user); $k++) {
                if (($array_premium_user[$k]->month_number - 1) == $i) {
                    $data[$i]['premium_count'] = $array_premium_user[$k]->total_count;
                    break;
                } else {
                    $data[$i]['premium_count'] = 0;
                }
            }
        }
        $final_data = [];
        $index = 0;
        for ($i = $current_month - 1; $i >= ($current_month - $month_range); $i--) {
            if ($i < 0) {
                $data[$i + 12]['year'] = date('y') - 1;
                $final_data[$index++] = $data[$i + 12];
            } else {
                $data[$i]['year'] = date('y');
                $final_data[$index++] = $data[$i];
            }
        }

        return \response()->json($final_data);
    }

    public function periodicUserTransactionAnalytics(Request $request)
    {
        $month_range = $request->input('period');
        $partner = $request->input('partner');
        if ($partner) {
            $partner = explode('=>', $partner);
            $branch_id = $partner[2];
        } else {
            $branch_id = null;
        }

        $current_month = date('m');
        $month_name_array = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $trial_user = DeliveryType::virtual_card;
        if ($branch_id != null) {
            $array_trial_user = DB::select("select DATE_FORMAT(posted_on, '%b') as month_name, count(DATE(posted_on)) as total_count, MONTH(posted_on) as month_number
                                        from transaction_table
                                                 join card_delivery cd on cd.id in (SELECT id
                                                                                   FROM card_delivery
                                                                                   WHERE id IN (
                                                                                       SELECT MAX(id)
                                                                                       FROM card_delivery
                                                                                       GROUP BY customer_id
                                                                                   )
                                                                                     and cd.customer_id = transaction_table.customer_id)
                                        where posted_on >= last_day(now()) + interval 1 day - interval '$month_range' month
                                          and delivery_type = $trial_user and branch_id='$branch_id'
                                        group by month_number, month_name
                                        order by month_number asc");
            $array_premium_user = DB::select("select DATE_FORMAT(posted_on, '%b') as month_name, count(DATE(posted_on)) as total_count, MONTH(posted_on) as month_number
                                        from transaction_table
                                                 join card_delivery cd on cd.id in (SELECT id
                                                                                   FROM card_delivery
                                                                                   WHERE id IN (
                                                                                       SELECT MAX(id)
                                                                                       FROM card_delivery
                                                                                       GROUP BY customer_id
                                                                                   )
                                                                                     and cd.customer_id = transaction_table.customer_id)
                                        where posted_on >= last_day(now()) + interval 1 day - interval '$month_range' month
                                          and delivery_type != $trial_user and branch_id='$branch_id'
                                        group by month_number, month_name
                                        order by month_number asc");
        } else {
            $array_trial_user = DB::select("select DATE_FORMAT(posted_on, '%b') as month_name, count(DATE(posted_on)) as total_count, MONTH(posted_on) as month_number
                                        from transaction_table
                                                 join card_delivery cd on cd.id in (SELECT id
                                                                                   FROM card_delivery
                                                                                   WHERE id IN (
                                                                                       SELECT MAX(id)
                                                                                       FROM card_delivery
                                                                                       GROUP BY customer_id
                                                                                   )
                                                                                     and cd.customer_id = transaction_table.customer_id)
                                        where posted_on >= last_day(now()) + interval 1 day - interval '$month_range' month
                                          and delivery_type = $trial_user
                                        group by month_number, month_name
                                        order by month_number asc");
            $array_premium_user = DB::select("select DATE_FORMAT(posted_on, '%b') as month_name, count(DATE(posted_on)) as total_count, MONTH(posted_on) as month_number
                                        from transaction_table
                                                 join card_delivery cd on cd.id in (SELECT id
                                                                                   FROM card_delivery
                                                                                   WHERE id IN (
                                                                                       SELECT MAX(id)
                                                                                       FROM card_delivery
                                                                                       GROUP BY customer_id
                                                                                   )
                                                                                     and cd.customer_id = transaction_table.customer_id)
                                        where posted_on >= last_day(now()) + interval 1 day - interval '$month_range' month
                                          and delivery_type != $trial_user
                                        group by month_number, month_name
                                        order by month_number asc");
        }

        $data = [];
        for ($i = 0; $i < count($month_name_array); $i++) {
            $data[$i]['month'] = $month_name_array[$i];
            if (count($array_trial_user) > 0) {
                for ($j = 0; $j < count($array_trial_user); $j++) {
                    if (($array_trial_user[$j]->month_number - 1) == $i) {
                        $data[$i]['trial_count'] = $array_trial_user[$j]->total_count;
                        break;
                    } else {
                        $data[$i]['trial_count'] = 0;
                    }
                }
            } else {
                $data[$i]['trial_count'] = 0;
            }

            if (count($array_premium_user) > 0) {
                for ($k = 0; $k < count($array_premium_user); $k++) {
                    if (($array_premium_user[$k]->month_number - 1) == $i) {
                        $data[$i]['premium_count'] = $array_premium_user[$k]->total_count;
                        break;
                    } else {
                        $data[$i]['premium_count'] = 0;
                    }
                }
            } else {
                $data[$i]['premium_count'] = 0;
            }
        }
        $final_data = [];
        $index = 0;
        for ($i = $current_month - 1; $i >= ($current_month - $month_range); $i--) {
            if ($i < 0) {
                $data[$i + 12]['year'] = date('y') - 1;
                $final_data[$index++] = $data[$i + 12];
            } else {
                $data[$i]['year'] = date('y');
                $final_data[$index++] = $data[$i];
            }
        }

        return \response()->json($final_data);
    }

    public function perDayUserTransactionAnalytics(Request $request)
    {
        $showing_date = $request->input('date');
        $partner = $request->input('partner');
        if ($partner) {
            $partner = explode('=>', $partner);
            $branch_id = $partner[2];
        } else {
            $branch_id = null;
        }

        $trial_user = DeliveryType::virtual_card;
        $data = [];
        if ($branch_id != null) {
            $hourly_trial_user = DB::select("select HOUR(posted_on) as hours,  count(HOUR(posted_on)) as total_count
                                            from transaction_table
                                                     join card_delivery cd on cd.id in (SELECT id
                                                                                        FROM card_delivery
                                                                                        WHERE id IN (
                                                                                            SELECT MAX(id)
                                                                                            FROM card_delivery
                                                                                            GROUP BY customer_id
                                                                                        )
                                                                                          and cd.customer_id = transaction_table.customer_id)
                                            where posted_on like '$showing_date%'
                                              and delivery_type = '$trial_user' and branch_id = '$branch_id'
                                            group by hours
                                            order by hours asc");

            $hourly_premium_user = DB::select("select HOUR(posted_on) as hours,  count(HOUR(posted_on)) as total_count
                                            from transaction_table
                                                     join card_delivery cd on cd.id in (SELECT id
                                                                                        FROM card_delivery
                                                                                        WHERE id IN (
                                                                                            SELECT MAX(id)
                                                                                            FROM card_delivery
                                                                                            GROUP BY customer_id
                                                                                        )
                                                                                          and cd.customer_id = transaction_table.customer_id)
                                            where posted_on like '$showing_date%'
                                              and delivery_type != '$trial_user' and branch_id = '$branch_id'
                                            group by hours
                                            order by hours asc ");
        } else {
            $hourly_trial_user = DB::select("select HOUR(posted_on) as hours,  count(HOUR(posted_on)) as total_count
                                            from transaction_table
                                                     join card_delivery cd on cd.id in (SELECT id
                                                                                        FROM card_delivery
                                                                                        WHERE id IN (
                                                                                            SELECT MAX(id)
                                                                                            FROM card_delivery
                                                                                            GROUP BY customer_id
                                                                                        )
                                                                                          and cd.customer_id = transaction_table.customer_id)
                                            where posted_on like '$showing_date%'
                                              and delivery_type = '$trial_user'
                                            group by hours
                                            order by hours asc");

            $hourly_premium_user = DB::select("select HOUR(posted_on) as hours,  count(HOUR(posted_on)) as total_count
                                            from transaction_table
                                                     join card_delivery cd on cd.id in (SELECT id
                                                                                        FROM card_delivery
                                                                                        WHERE id IN (
                                                                                            SELECT MAX(id)
                                                                                            FROM card_delivery
                                                                                            GROUP BY customer_id
                                                                                        )
                                                                                          and cd.customer_id = transaction_table.customer_id)
                                            where posted_on like '$showing_date%'
                                              and delivery_type != '$trial_user'
                                            group by hours
                                            order by hours asc ");
        }

        $trial_24_hour = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        for ($i = 0; $i <= 23; $i++) {
            if (isset($hourly_trial_user[$i])) {
                $trial_24_hour[(int) ($hourly_trial_user[$i]->hours)] = $hourly_trial_user[$i]->total_count;
            }
        }

        $premium_24_hour = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        for ($i = 0; $i <= 23; $i++) {
            if (isset($hourly_premium_user[$i])) {
                $premium_24_hour[(int) ($hourly_premium_user[$i]->hours)] = $hourly_premium_user[$i]->total_count;
            }
        }

        for ($i = 0; $i <= 23; $i++) {
            $data[$i]['all'] = $trial_24_hour[$i] + $premium_24_hour[$i];
            $data[$i]['trial_user'] = $trial_24_hour[$i];
            $data[$i]['premium_user'] = $premium_24_hour[$i];
        }

        return \response()->json($data);
    }

    public function perWeekUserTransactionAnalytics(Request $request)
    {
        $showing_date = $request->input('date');
        $partner = $request->input('partner');
        if ($partner) {
            $partner = explode('=>', $partner);
            $branch_id = $partner[2];
        } else {
            $branch_id = null;
        }

        $date_7_days_before = Carbon::createFromFormat('Y-m-d', $showing_date)->subDays(8)->toDateString();

        $trial_user = DeliveryType::virtual_card;
        if ($branch_id != null) {
            $array_trial_user = DB::select("select DATE(posted_on) as date, count(DATE(posted_on)) as total_count
                                        from transaction_table
                                                 join card_delivery cd on cd.id in (SELECT id
                                                                                    FROM card_delivery
                                                                                    WHERE id IN (
                                                                                        SELECT MAX(id)
                                                                                        FROM card_delivery
                                                                                        GROUP BY customer_id
                                                                                    )
                                                                                      and cd.customer_id = transaction_table.customer_id)
                                        where posted_on >= '$date_7_days_before' and posted_on <= '$showing_date'
                                          and delivery_type = $trial_user and branch_id = '$branch_id'
                                        group by date
                                        order by date asc");

            $array_premium_user = DB::select("select DATE(posted_on) as date, count(DATE(posted_on)) as total_count
                                        from transaction_table
                                                 join card_delivery cd on cd.id in (SELECT id
                                                                                    FROM card_delivery
                                                                                    WHERE id IN (
                                                                                        SELECT MAX(id)
                                                                                        FROM card_delivery
                                                                                        GROUP BY customer_id
                                                                                    )
                                                                                      and cd.customer_id = transaction_table.customer_id)
                                        where posted_on >= '$date_7_days_before' and posted_on <= '$showing_date'
                                          and delivery_type != $trial_user and branch_id = '$branch_id'
                                        group by date
                                        order by date asc");
        } else {
            $array_trial_user = DB::select("select DATE(posted_on) as date, count(DATE(posted_on)) as total_count
                                        from transaction_table
                                                 join card_delivery cd on cd.id in (SELECT id
                                                                                    FROM card_delivery
                                                                                    WHERE id IN (
                                                                                        SELECT MAX(id)
                                                                                        FROM card_delivery
                                                                                        GROUP BY customer_id
                                                                                    )
                                                                                      and cd.customer_id = transaction_table.customer_id)
                                        where posted_on >= '$date_7_days_before' and posted_on <= '$showing_date'
                                          and delivery_type = $trial_user
                                        group by date
                                        order by date asc");

            $array_premium_user = DB::select("select DATE(posted_on) as date, count(DATE(posted_on)) as total_count
                                        from transaction_table
                                                 join card_delivery cd on cd.id in (SELECT id
                                                                                    FROM card_delivery
                                                                                    WHERE id IN (
                                                                                        SELECT MAX(id)
                                                                                        FROM card_delivery
                                                                                        GROUP BY customer_id
                                                                                    )
                                                                                      and cd.customer_id = transaction_table.customer_id)
                                        where posted_on >= '$date_7_days_before' and posted_on <= '$showing_date'
                                          and delivery_type != $trial_user
                                        group by date
                                        order by date asc");
        }

        $data_7_days = [];
        for ($i = 1; $i < 8; $i++) {
            $n_date = Carbon::createFromFormat('Y-m-d', $showing_date)->subDays($i)->toDateString();
            $print_date = Carbon::createFromFormat('Y-m-d', $showing_date)->subDays($i)->format('d M, Y');
            $found = false;
            for ($j = 0; $j < count($array_trial_user); $j++) {
                if ($array_trial_user[$j]->date == (string) $n_date) {
                    $data_7_days[$i]['date'] = (string) $print_date;
                    $data_7_days[$i]['trial_count'] = $array_trial_user[$j]->total_count;
                    $found = true;
                    break;
                }
            }
            if (! $found) {
                $data_7_days[$i]['date'] = (string) $print_date;
                $data_7_days[$i]['trial_count'] = 0;
            }
        }

        for ($i = 1; $i < 8; $i++) {
            $n_date = Carbon::createFromFormat('Y-m-d', $showing_date)->subDays($i)->toDateString();
            $found = false;
            for ($j = 0; $j < count($array_premium_user); $j++) {
                if ($array_premium_user[$j]->date == (string) $n_date) {
                    $data_7_days[$i]['premium_count'] = $array_premium_user[$j]->total_count;
                    $found = true;
                    break;
                }
            }
            if (! $found) {
                $data_7_days[$i]['premium_count'] = 0;
            }
        }

        return \response()->json($data_7_days);
    }

    public function getSearchedPartners()
    {
        $partners = DB::select('select count(branch_id) as search_count,branch_id,pi.partner_name,
                                GROUP_CONCAT(pb.partner_address) AS partner_address
                                from search_stats
                                join partner_branch pb on search_stats.branch_id = pb.id
                                join partner_info pi on pb.partner_account_id = pi.partner_account_id
                                where search_stats.branch_id is not null  and search_stats.created_at BETWEEN CURDATE() - INTERVAL 30 DAY AND SYSDATE()
                                group by branch_id,pi.partner_name
                                order by search_count desc');

        return \response()->json($partners);
    }

    public function getSearchKeysOfPartners($branch_id)
    {
        $keys = DB::select("select count(`key`) as search_key_count, `key`
                            from search_stats
                            where branch_id = '$branch_id' and created_at BETWEEN CURDATE() - INTERVAL 30 DAY AND SYSDATE()
                            group by `key`");

        return view('admin.production.analytics.search_key_of_partner', compact('keys'));
    }

    public function getSearchKeysWithoutPartners()
    {
        $keys = DB::select('select count(`key`) search_key_count, `key`
                            from search_stats
                            where `key` not in (select `key` from search_stats where branch_id is not null group by `key`)
                              and created_at BETWEEN CURDATE() - INTERVAL 30 DAY AND SYSDATE()
                            group by `key`
                            order by search_key_count desc');

        return \response()->json($keys);
    }

    public function getVerifiedEmailPercentage()
    {
        $verified_user_count = CustomerInfo::where('email_verified', 1)->count();
        $all_user_count = CustomerInfo::count();

        return round(($verified_user_count / $all_user_count) * 100, 2);
    }

    public function getCompletedProfilePercentage()
    {
        $verified_user_count = CustomerInfo::where('customer_profile_image', '!=', 'https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/registration/user.png')
            ->where('customer_gender', '!=', null)
            ->where('customer_dob', '!=', null)
            ->count();
        $all_user_count = CustomerInfo::count();

        return round(($verified_user_count / $all_user_count) * 100, 2);
    }

    public function getPlatformWiseRegistrationPercentage()
    {
        $total_accounts = CustomerAccount::where('platform', '!=', null)
            ->where('platform', '!=', PlatformType::sales_app)
            ->where('platform', '!=', PlatformType::rbd_admin)
            ->count();
        $total_web_accounts = CustomerAccount::where('platform', PlatformType::web)->count();
        $total_ios_accounts = CustomerAccount::where('platform', PlatformType::ios)->count();
        $total_android_accounts = CustomerAccount::where('platform', PlatformType::android)->count();

        $ios_percentage = ($total_ios_accounts / $total_accounts) * 100;
        $web_percentage = ($total_web_accounts / $total_accounts) * 100;
        $android_percentage = ($total_android_accounts / $total_accounts) * 100;

        $res['android_percentage'] = round($android_percentage, 2);
        $res['ios_percentage'] = round($ios_percentage, 2);
        $res['web_percentage'] = round($web_percentage, 2);

        return $res;
    }

    public function getGenderWisePercentageData()
    {
        $total_users = CustomerInfo::where('customer_gender', '!=', null)->count();
        $male_users = CustomerInfo::where('customer_gender', 'male')->count();
        $female_users = CustomerInfo::where('customer_gender', 'female')->count();

        $male_percentage = ($male_users / $total_users) * 100;
        $female_percentage = ($female_users / $total_users) * 100;

        $res['male_percentage'] = round($male_percentage, 2);
        $res['female_percentage'] = round($female_percentage, 2);

        return $res;
    }

    public function getAgeRange($gender)
    {
        $ranges = [ // the start of each age-range.
            '13-17' => 13,
            '18-24' => 18,
            '25-34' => 25,
            '35-44' => 35,
            '45-54' => 45,
            '55-64' => 55,
            '65+' => 65,
        ];
        if ($gender == 'all') {
            $output = CustomerInfo::where('customer_dob', '!=', null)
                ->get()
                ->map(function ($user) use ($ranges) {
                    $age = Carbon::parse($user->customer_dob)->age;
                    foreach ($ranges as $key => $breakpoint) {
                        if ($breakpoint >= $age) {
                            $user->range = $key;
                            break;
                        }
                    }

                    return $user;
                })
                ->mapToGroups(function ($user, $key) {
                    return [$user->range => $user];
                })
                ->map(function ($group) {
                    return count($group);
                });
        } else {
            $output = CustomerInfo::where('customer_dob', '!=', null)->where('customer_gender', $gender)
                ->get()
                ->map(function ($user) use ($ranges) {
                    $age = Carbon::parse($user->customer_dob)->age;
                    foreach ($ranges as $key => $breakpoint) {
                        if ($breakpoint >= $age) {
                            $user->range = $key;
                            break;
                        }
                    }

                    return $user;
                })
                ->mapToGroups(function ($user, $key) {
                    return [$user->range => $user];
                })
                ->map(function ($group) {
                    return count($group);
                });
        }

        return $output;
    }

    public function genderAnalytics()
    {
        return response()->json($this->getGenderWisePercentageData());
    }

    public function platformWiseRegAnalytics()
    {
        return response()->json($this->getPlatformWiseRegistrationPercentage());
    }

    public function ageAnalytics()
    {
        $data['all'] = $this->getAgeRange('all');
        $data['undefined_gender'] = $this->getAgeRange(null);
        $data['male'] = $this->getAgeRange('male');
        $data['female'] = $this->getAgeRange('female');

        return response()->json($data);
    }

    public function getAppVersionAnalytics()
    {
        $ios_running_version = (new jsonController())->getIOSversionData()['version'];
        $android_running_version = (new jsonController())->getAndroidVersionData()['version'];

        $android_old_version_sessions = CustomerActivitySession::where('platform', PlatformType::android)
            ->orderBy('id', 'DESC')->get()->unique('physical_address');

        $ios_old_version_sessions = CustomerActivitySession::where('platform', PlatformType::ios)
            ->orderBy('id', 'DESC')->get()->unique('physical_address');

        $android_current_version_sessions = CustomerActivitySession::where('platform', PlatformType::android)
            ->where('version', $android_running_version)
            ->orderBy('id', 'DESC')->get()->unique('physical_address');

        $ios_current_version_sessions = CustomerActivitySession::where('platform', PlatformType::ios)
            ->where('version', $ios_running_version)
            ->orderBy('id', 'DESC')->get()->unique('physical_address');

        $android['old_version'] = count($android_old_version_sessions) - count($android_current_version_sessions);
        $android['running_version'] = count($android_current_version_sessions);
        $android['running_version_code'] = $android_running_version;

        $ios['old_version'] = count($ios_old_version_sessions) - count($ios_current_version_sessions);
        $ios['running_version'] = count($ios_current_version_sessions);
        $ios['running_version_code'] = $ios_running_version;

        $res['android'] = $android;
        $res['ios'] = $ios;

        return $res;
    }

    public function appVersionAnalytics()
    {
        return response()->json($this->getAppVersionAnalytics());
    }

    public function merchantTransactionPercentageAnalytics(Request $request)
    {
        $sort = $request->input('sort');
        $category = $request->input('category');
        $area = $request->input('area');
        if ($sort == 'true') {
            if ($area) {
                $branches = PartnerBranch::where('partner_area', $area)->withCount('transaction')->get();
            } else {
                $branches = PartnerBranch::withCount('transaction')->get();
            }
            $branches = collect($branches)->where('info.partner_category', $category)
                ->sortByDesc('transaction_count')->take(10);
            $branch_ids = $branches->pluck('id');
            $tran_count = TransactionTable::whereIn('branch_id', $branch_ids)->count();
            $branches = collect($branches)->map(function ($item) use ($tran_count) {
                if ($tran_count > 0) {
                    $item->percentage = round(($item->transaction_count / $tran_count) * 100, 2);
                } else {
                    $item->percentage = 0;
                }

                return $item;
            });
        } else {
            $branches = PartnerBranch::withCount('transaction')->with('info')->get();
            $branches = collect($branches)->sortByDesc('transaction_count')->take(10);
            $tran_count = TransactionTable::count();
            $branches = collect($branches)->map(function ($item) use ($tran_count) {
                $item->percentage = round(($item->transaction_count / $tran_count) * 100, 2);

                return $item;
            });
        }
        $branches = array_values($branches->toArray());

        return response()->json($branches);
    }

    //function to show all active customers
    public function monthlyActiveCustomers($from, $to)
    {
        $from = $from.' 00:00:00';
        $to = $to.' 23:59:59';
        $users_ids = TransactionTable::where('posted_on', '>=', $from)->where('posted_on', '<=', $to)
            ->get()->unique('customer_id')->pluck('customer_id');
        $users = CustomerInfo::whereIn('customer_id', $users_ids)->get();
        foreach ($users as $user) {
            $user->monthlyTranCount = $user->monthlyTransactionCount($from, $to);
        }
        $users = $users->sortByDesc('monthlyTranCount');

        return $users;
    }

    //function to show all active customers
    public function recurringCustomers()
    {
        $prev_sorting_time = date('Y-m', strtotime(date('Y-m').' -1 month'));
        $sorting_time = date('Y-m');
        $users_ids_1 = TransactionTable::where('posted_on', 'like', $sorting_time.'%')
            ->get()->unique('customer_id')->pluck('customer_id');
        $users_ids_2 = TransactionTable::where('posted_on', 'like', $prev_sorting_time.'%')
            ->get()->unique('customer_id')->pluck('customer_id');
        $users_ids = array_intersect($users_ids_1->toArray(), $users_ids_2->toArray());
        $users = CustomerInfo::whereIn('customer_id', $users_ids)->get();

        return $users;
    }

    //transaction analytics of active members
    public function activeMemberTransactionAnalytics()
    {
        $from = date('Y-m-01');
        $to = date('Y-m-d');
        $active_users = $this->monthlyActiveCustomers($from, $to);
        $recurring_users = $this->recurringCustomers();

        return view('admin.production.analytics.active_mem_tran_analytics', compact( 'to',
            'active_users', 'recurring_users'));
    }

    //monthly active member analytics
    public function monthlyActiveMemberAnalytics(Request $request)
    {
        $from = $request->post('from');
        $to = $request->post('to');
        $active_users = $this->monthlyActiveCustomers($from, $to);
        $recurring_users = $this->recurringCustomers();

        return view('admin.production.analytics.active_mem_tran_analytics', compact('from', 'to',
            'active_users', 'recurring_users'));
    }

    //active/inactive expired members
    public function activeInactiveExpiredMembers($status)
    {
        $profileInfo = CustomerInfo::with('customerHistory', 'latestSSLTransaction')
            ->withCount('branchTransactions')
            ->where('customer_type', '!=', 3)
            ->where('expiry_date', '<=', date('Y-m-d'))
            ->get();
        $profileInfo = $profileInfo->where('customerHistory', '!=', null)
            ->sortByDesc('branch_transactions_count');
        if ($status == 'active') {
            $users = $profileInfo->where('branch_transactions_count', '>', 0);
            $tab_title = 'Active';
        } else {
            $users = $profileInfo->where('branch_transactions_count', 0);
            $tab_title = 'Inactive';
        }
        $users = (new functionController2())->getPaginatedData($users, 10);

        return view('admin.production.analytics.active_inactive_expired_members', compact('users',
            'tab_title', 'status'));
    }
}
