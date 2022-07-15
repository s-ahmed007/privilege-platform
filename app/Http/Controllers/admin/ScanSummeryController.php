<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\functionController;
use App\PartnerBranch;
use DB;

class ScanSummeryController extends Controller
{
    public function transactionListData()
    {
        //get all customers info for admin panel
        $transactionInfo = DB::select('SELECT COUNT(branch_id) as no_of_tran, 
                            SUM(discount_amount) as tot_discount, 
                            SUM(amount_spent) as tot_amount,
                            branch_id
                            FROM transaction_table
                            GROUP BY branch_id
                            ORDER BY no_of_tran DESC;');
        $transactionInfo = json_decode(json_encode($transactionInfo), true);

        $i = 0;
        foreach ($transactionInfo as $branch) {
            $branch_info = (new functionController)->BranchInfoOfPartner($branch['branch_id']);
            if (isset($branch_info->id)) {
                $transactionInfo[$i]['area'] = $branch_info->partner_area;
            }
            $transactionInfo[$i]['name'] = $branch_info->info->partner_name;
            $transactionInfo[$i]['address'] = $branch_info->partner_address;
            $transactionInfo[$i]['partner_active'] = $branch_info->account->active;
            $transactionInfo[$i]['branch_active'] = $branch_info->active;
            $i++;
        }
        $transactionList = $transactionInfo;

        return $transactionList;
    }

    //function for showing all customers info
    public function transactionList()
    {
        $transactionList = $this->transactionListData();
        $status = '';

        return view('admin.production.scanSummery.activePartners', compact('transactionList', 'status'));
    }

    public function sortedTransactionList($status)
    {
        $transactionList = $this->transactionListData();
        if ($status == 'current') {
            $i = 0;
            foreach ($transactionList as $value) {
                if ($value['partner_active'] == 0) {
                    unset($transactionList[$i]);
                } elseif ($value['branch_active'] == 0) {
                    unset($transactionList[$i]);
                }
                $i++;
            }
        } else {
            $i = 0;
            foreach ($transactionList as $value) {
                if ($value['branch_active'] == 1) {
                    unset($transactionList[$i]);
                }
                $i++;
            }
        }
        $transactionList = array_values($transactionList);

        return view('admin.production.scanSummery.activePartners', compact('transactionList', 'status'));
    }

    //function to show all inactive partners
    public function inactivePartners()
    {
        //get inactive partner info for admin panel
        $tt_ids = DB::table('transaction_table')->select('branch_id')->distinct('branch_id')->get();
        $tt_ids = json_decode(json_encode($tt_ids), true);

        $profileInfo = PartnerBranch::whereNotIn('id', $tt_ids)->with('info')->get();
        $status = '';

        return view('admin.production.scanSummery.inactivePartners', compact('profileInfo', 'status'));
    }

    //function to show all sorted inactive partners
    public function sortedInactivePartners($status)
    {
        //get inactive partner info for admin panel
        $tt_ids = DB::table('transaction_table')->select('branch_id')->distinct('branch_id')->get();
        $tt_ids = json_decode(json_encode($tt_ids), true);
        $profileInfo = PartnerBranch::whereNotIn('id', $tt_ids)->with('info.account')->get();
        if ($status == 'current') {
            $i = 0;
            foreach ($profileInfo as $value) {
                if ($value->info->account->active == 0) {
                    unset($profileInfo[$i]);
                } elseif ($value->active == 0) {
                    unset($profileInfo[$i]);
                }
                $i++;
            }
        } else {
            $i = 0;
            foreach ($profileInfo as $value) {
                if ($value->active == 1) {
                    unset($profileInfo[$i]);
                }
                $i++;
            }
        }

        return view('admin.production.scanSummery.inactivePartners', compact('profileInfo', 'status'));
    }
}
