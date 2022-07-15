<?php

namespace App\Http\Controllers\TransactionRequest\v2;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Enum\DeliveryType;
use App\Http\Controllers\TransactionRequest\v2\functionController as merchantFunctionController;
use App\PartnerBranch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class analyticsController extends Controller
{
    public function dashboard(Request $request)
    {
        $month_range = 12;
        $branch_id = session('branch_id');
        $current_month = date('m');
        $month_name_array = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $array_tran = DB::select("select DATE_FORMAT(posted_on, '%b') as month_name, count(DATE(posted_on)) as total_count, MONTH(posted_on) as month_number
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
                                          and branch_id='$branch_id' and deleted_at is null 
                                        group by month_number, month_name
                                        order by month_number asc");
        $data1 = [];
        for ($i = 0; $i < count($month_name_array); $i++) {
            for ($j = 0; $j < count($array_tran); $j++) {
                if (($array_tran[$j]->month_number - 1) == $i) {
                    $data1[$i] = $array_tran[$j]->total_count;
                    break;
                } else {
                    $data1[$i] = 0;
                }
            }
        }

        $final_data1 = [];
        $index = 0;
        for ($i = $current_month - 1; $i >= ($current_month - $month_range); $i--) {
            if ($i < 0) {
                $final_data1[$index++] = $data1[$i + 12];
            } else {
                $final_data1[$index++] = $data1[$i];
            }
        }

        $branch = PartnerBranch::find($branch_id);
        $partner_id = $branch->partner_account_id;
        $array_visit = DB::select("select DATE_FORMAT(visited_on, '%b') as month_name, count(DATE(visited_on)) as total_count, MONTH(visited_on) as month_number
                                        from rbd_statistics
                                        where visited_on >= last_day(now()) + interval 1 day - interval '$month_range' month
                                          and partner_id='$partner_id'
                                        group by month_number, month_name
                                        order by month_number asc");
        $data2 = [];
        for ($i = 0; $i < count($month_name_array); $i++) {
            for ($j = 0; $j < count($array_visit); $j++) {
                if (($array_visit[$j]->month_number - 1) == $i) {
                    $data2[$i] = $array_visit[$j]->total_count;
                    break;
                } else {
                    $data2[$i] = 0;
                }
            }
        }
        $final_data2 = [];
        $index = 0;
        for ($i = $current_month - 1; $i >= ($current_month - $month_range); $i--) {
            if ($i < 0) {
                $final_data2[$index++] = $data2[$i + 12];
            } else {
                $final_data2[$index++] = $data2[$i];
            }
        }
        $final_data1 = array_reverse($final_data1);
        $final_data2 = array_reverse($final_data2);

        return response()->json(['tran_values'=>$final_data1, 'visit_values'=>$final_data2]);
    }

    public function peakHour(Request $request)
    {
        $sort = $request->post('sort');
        if ($sort == 'true') {
            $from = $request->post('from');
            $to = $request->post('to');
            $allTime = false;
        } else {
            $allTime = true;
            $from = $to = null;
        }
        $data = (new merchantFunctionController())->getPeakHour(session('branch_id'), $from, $to, $allTime);

        return \response()->json($data);
    }

    public function profileVisit(Request $request)
    {
        $month_range = $request->input('period');
        $branch_id = session('branch_id');
        $current_month = date('m');
        $month_name_array = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        $branch = PartnerBranch::find($branch_id);
        $partner_id = $branch->partner_account_id;
        $array_profile_visit = DB::select("select DATE_FORMAT(visited_on, '%b') as month_name, count(DATE(visited_on)) as total_count, 
                                            MONTH(visited_on) as month_number
                                        from rbd_statistics
                                        where visited_on >= last_day(now()) + interval 1 day - interval '$month_range' month
                                           and partner_id='$partner_id'
                                        group by month_number, month_name
                                        order by month_number asc");

        $data = [];
        for ($i = 0; $i < count($month_name_array); $i++) {
            $data[$i]['month'] = $month_name_array[$i];
            if (count($array_profile_visit) > 0) {
                for ($j = 0; $j < count($array_profile_visit); $j++) {
                    if (($array_profile_visit[$j]->month_number - 1) == $i) {
                        $data[$i]['visit_count'] = $array_profile_visit[$j]->total_count;
                        break;
                    } else {
                        $data[$i]['visit_count'] = 0;
                    }
                }
            } else {
                $data[$i]['visit_count'] = 0;
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
//        $data = (new merchantFunctionController())->getProfileVisit(session('branch_id'));
        return \response()->json($final_data);
    }

    public function transactionStatistics(Request $request)
    {
        $month_range = $request->input('period');
        $branch_id = session('branch_id');

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
                                          and delivery_type = $trial_user and branch_id='$branch_id' and deleted_at is null 
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
                                          and delivery_type != $trial_user and branch_id='$branch_id' and deleted_at is null 
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
                                          and delivery_type = $trial_user and deleted_at is null 
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
                                          and delivery_type != $trial_user and deleted_at is null 
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
}
