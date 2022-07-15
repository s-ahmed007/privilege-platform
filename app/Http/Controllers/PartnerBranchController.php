<?php

namespace App\Http\Controllers;

use App\AllAmounts;
use App\BranchIpAddresses;
use App\BranchScanner;
use App\BranchUser;
use App\Http\Controllers\Enum\BranchUserRole;
use App\LeaderboardPrizes;
use App\PartnerAccount;
use App\PartnerBranch;
use App\Rules\unique_if_changed;
use App\ScannerPrizeHistory;
use App\ScannerPrizes;
use App\ScannerReward;
use App\TransactionTable;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;

class PartnerBranchController extends Controller
{
    //function to show all branches of partners
    public function allBranches()
    {
        //get all partners info for admin panel
        $allPartners = PartnerAccount::with('info', 'branches')->get();
        //get total partner number
        $partner_number = PartnerAccount::count();

        return view('admin/production/branch/allBranches', compact('allPartners', 'partner_number'));
    }

    //function to show all scanners altogether
    public function scannerList()
    {
//        $branch_info = PartnerBranch::where('id', $branch_id)->with('info')->first();

        $users = BranchScanner::with('branchUser', 'transactions', 'branch.info', 'scannerReward')->get();

        return view('admin.production.branch.scannerList', compact('users'));
    }

    //function to get all Scanners of a branch
    public function allScanners($branch_id)
    {
        //$users = BranchUser::all();
        $branch_info = PartnerBranch::where('id', $branch_id)->with('info')->first();

        $users = BranchScanner::with('branchUser')->where('branch_id', $branch_id)->with('transactions')->get();

        return view('admin.production.branch.allScanners', compact('users', 'branch_info', 'branch_id'));
    }

    //function to show user create view
    public function createScanner($branch_id)
    {
        $branch = PartnerBranch::where('id', $branch_id)->with('info')->first();

        return view('admin.production.branch.createScanner', compact('branch'));
    }

    //function to store new branch Scanner
    public function storeBranchScanner(Request $request, $branch_id)
    {
        $this->validate($request, [
            'full_name' => 'required',
            'username' => 'required',
            'pin_code' => 'required|numeric|digits:4',
            'phone_number' => 'required|unique:branch_user,phone|min:14',
            'designation' => 'required',
            'branch_user_role' => 'required',

        ]);
        $request->flashOnly('full_name', 'username', 'pin_code', 'phone_number', 'designation', 'branch_user_role');
        $cur_users = BranchScanner::where('branch_id', $branch_id)->count();
        if ($cur_users > 0) {
            return \redirect()->back()->with('error', 'You can not add more than one user!');
        }
        $full_name = $request->get('full_name');
        $username = $request->get('username');
        $pin_code = $request->get('pin_code');
        $phone = $request->get('phone_number');
        $designation = $request->get('designation');
        $branch_user_role = $request->get('branch_user_role');

        if ($branch_user_role == BranchUserRole::branchOwner) {
            $branch = PartnerBranch::find($branch_id);
            if ($branch->owner_id) {
                return \redirect()->back()->with('error', 'Owner already exists');
            }
        }

        $branchAllUser = new BranchUser();
        $branchAllUser->username = $username;
        $branchAllUser->pin_code = $pin_code;
        $branchAllUser->phone = $phone;
        $branchAllUser->role = $branch_user_role;
        $branchAllUser->active = 1;

        $branchAllUser->save();

        $branchScanner = new BranchScanner();
        $branchScanner->full_name = $full_name;
        $branchScanner->designation = $designation;
        $branchScanner->branch_user_id = $branchAllUser->id;
        $branchScanner->branch_id = $branch_id;
        $branchScanner->ip_authorized = 0;

        $branchScanner->save();

        $ScannerReward = new ScannerReward();
        $ScannerReward->scanner_id = $branchScanner->id;
        $ScannerReward->point = 0;
        $ScannerReward->point_used = 0;

        $ScannerReward->save();

        if ($branch_user_role == BranchUserRole::branchOwner) {
            $branch = PartnerBranch::find($branch_id);
            $branch->owner_id = $branchAllUser->id;
            $branch->save();
        }

        return Redirect('manage-branch-scanners/'.$branch_id)->with('created', 'Scanner User Created Successfully');
    }

    //function to show edit view of Scanner
    public function editScanner($scanner_id)
    {
        $user = BranchScanner::with('branchUser')->where('branch_user_id', $scanner_id)->first();

        return view('admin.production.branch.editScanner', compact('user'));
    }

    //function to store new user
    public function updateScannerInfo(Request $request, $scanner_id)
    {
        $this->validate($request, [
            'full_name' => 'required',
            'username' => 'required',
            'pin_code' => 'required|numeric|digits:4',
            'phone_number' => ['required', 'min:14', new unique_if_changed($scanner_id, 'branch_user', 'phone', 'id', 'The phone number has already been taken')],
            'designation' => 'required',
            'branch_user_role' => 'required',

        ]);
        $request->flashOnly('full_name', 'username', 'pin_code', 'phone_number', 'designation', 'branch_user_role');

        $full_name = $request->get('full_name');
        $username = $request->get('username');
        $pin_code = $request->get('pin_code');
        $phone = $request->get('phone_number');
        $designation = $request->get('designation');
        $branch_user_role = $request->get('branch_user_role');
        $scanner = BranchScanner::all()->where('branch_user_id', $scanner_id)->first();
        $branch = PartnerBranch::find($scanner->branch_id);

        if ($branch_user_role == BranchUserRole::branchOwner) {
            if (! $branch->owner_id) {
                $branch->owner_id = $scanner_id;
                $branch->save();
            } else {
                if ($branch->owner_id != $scanner_id) {
                    return \redirect()->back()->with('error', 'Owner already exist');
                }
            }
        } else {
            if ($branch->owner_id && $branch->owner_id == $scanner_id) {
                $branch->owner_id = null;
                $branch->save();
            }
        }

        BranchScanner::where('branch_user_id', $scanner_id)->update([
            'full_name' => $full_name,
            'designation' => $designation,
        ]);

        BranchUser::where('id', $scanner_id)->update([
            'username' => $username,
            'pin_code' => $pin_code,
            'phone' => $phone,
            'role' => $branch_user_role,
        ]);

        return Redirect('manage-branch-scanners/'.$scanner->branch_id)->with('updated', 'Information Updated Successfully');
    }

    //function to active/deactive user
    public function branchUserApproval(Request $request)
    {
        $status = $request->input('status');
        $scanner_id = $request->input('userId');
        if ($status == 2) {
            $user = BranchUser::find($scanner_id);
            $user->active = 0;
            $user->save();
        } else {
            $user = BranchUser::find($scanner_id);
            $user->active = 1;
            $user->save();
        }

        return Response::json($status);
    }

    //function to delete branch Scanner
    public function deleteScanner($user_id)
    {
        $scanner = BranchScanner::where('branch_user_id', $user_id)->with('branchUser')->first();
        $branch = PartnerBranch::find($scanner->branch_id);
        $branch_id = $scanner->branch_id;

        if ($scanner->branchUser->role == BranchUserRole::branchOwner) {
            if ($branch->owner_id && $branch->owner_id == $user_id) {
                $branch->owner_id = null;
                $branch->save();
            }
        }

        try {
            DB::beginTransaction(); //to do query rollback

            $user = BranchUser::find($user_id);
            $user->delete();

            DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            DB::rollBack(); //rollback all successfully executed queries
            return redirect()->back()->with('try_again', 'Please try again!');
        }

        return Redirect('manage-branch-scanners/'.$branch_id)->with('user_deleted', 'One Scanner deleted');
    }

    //function to get all users of a branch
    public function allIpAddresses($branch_id)
    {
        $ipAddress = BranchIpAddresses::all();
        $branch_info = PartnerBranch::where('id', $branch_id)->with('info')->first();

        return view('admin.production.branch.allIpAddresses', compact('ipAddress', 'branch_info', 'branch_id'));
    }

    //function to show ip address create view
    public function createIpAddress($branch_id)
    {
        return view('admin.production.branch.createIpAddress', compact('branch_id'));
    }

    public function storeIpAddress(Request $request, $branch_id)
    {
        $this->validate($request, [
            'ip_address' => 'required',
        ]);
        $request->flashOnly('ip_address');
        $ip_address = $request->get('ip_address');

        //check if this ip already assigned to this branch
        $exists = BranchIpAddresses::where([['branch_id', '=', $branch_id], ['ip_address', '=', $ip_address]])->count();

        if ($exists == 0) {
            $branchIp = new BranchIpAddresses();
            $branchIp->branch_id = $branch_id;
            $branchIp->ip_address = $ip_address;
            $branchIp->save();
        } else {
            return Redirect()->back()->with('ip_exists', 'Ip already assigned to this branch');
        }

        return Redirect('manage-branch-ip-address/'.$branch_id)->with('created', 'New Ip Address Created Successfully');
    }

    //function to show edit view of user
    public function editBranchIpAddress($ip_id)
    {
        $ipAddress = BranchIpAddresses::where('id', $ip_id)->first();

        return view('admin.production.branch.editIpAddress', compact('ipAddress'));
    }

    //function to store new user
    public function updateBranchIpAddress(Request $request, $branch_id, $ip_id)
    {
        $this->validate($request, [
            'ip_address' => 'required',
        ]);
        $request->flashOnly('ip_address');
        $ip_address = $request->get('ip_address');
        //check if this ip already assigned to this branch
        $exists = BranchIpAddresses::where([['id', '!=', $ip_id], ['branch_id', '=', $branch_id], ['ip_address', '=', $ip_address]])->count();

        if ($exists == 0) {
            $branchIp = BranchIpAddresses::find($ip_id);
            $branchIp->ip_address = $ip_address;
            $branchIp->save();
        } else {
            return Redirect()->back()->with('ip_exists', 'Ip already assigned to this branch');
        }

        return Redirect('manage-branch-ip-address/'.$branchIp->branch_id)->with('updated', 'Information Updated Successfully');
    }

    //function to delete branch user
    public function deleteBranchIpAddress($ip_id)
    {
        $branchIp = BranchIpAddresses::find($ip_id);
        $branchIp->delete();

        return Redirect('manage-branch-ip-address/'.$branchIp->branch_id)->with('ip_deleted', 'One Ip Address deleted');
    }

    //function to get all users of a branch
    public function scannerRequest()
    {
        $prizeHistory = ScannerPrizeHistory::with('branchScanner.branchUser', 'branchScanner.branch.info')
            ->orderBy('posted_on', 'DESC')->get();

        return view('admin.production.branch.branchUserRequests', compact('prizeHistory'));
    }

    //function to Accept branch user requests
    public function scannerRequestAccept(Request $request)
    {
        $status = $request->input('status');
        $id = $request->input('id');
        try {
            DB::beginTransaction(); //to do query rollback

            $value = $status == 1 ? 1 : 0;
            ScannerPrizeHistory::where('id', $id)->update(['status' => $value]);
            (new \App\Http\Controllers\AdminNotification\functionController())->adminAcceptedScannerPrizeRequest($id);

            DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            DB::rollBack(); //rollback all successfully executed queries
            return Response::json('failed');
        }

        return Response::json($status);
    }

    public function leaderBoardData()
    {
        $previous_day_leaderBoard = [];
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
        $scan_point = AllAmounts::all();

        //till previous day
        $i = 0;
        foreach ($branches as $branch) {
            $previous_transaction_count = TransactionTable::where('branch_id', $branch->id)->where('posted_on', 'like', date('Y-m').'%')
                ->where('posted_on', 'not like', date('Y-m-d').'%')->count();
            $previous_day_leaderBoard[$i]['profile_image'] = $branch->info->profileImage->partner_profile_image;
            $previous_day_leaderBoard[$i]['partner_name'] = $branch->info->partner_name;
            $previous_day_leaderBoard[$i]['area'] = $branch->partner_area;
            $previous_day_leaderBoard[$i]['branch_id'] = $branch->id;
            $previous_day_leaderBoard[$i]['point'] = $previous_transaction_count * $scan_point[10]['price'];
            $i++;
        }
        $array_point = array_column($previous_day_leaderBoard, 'point');
        $array_name = array_column($previous_day_leaderBoard, 'partner_name');
        array_multisort($array_point, SORT_DESC, $array_name, SORT_ASC, $previous_day_leaderBoard);

        //till current day
        $i = 0;
        foreach ($branches as $branch) {
            $current_transaction_count = TransactionTable::where('branch_id', $branch->id)->where('posted_on', 'like', date('Y-m').'%')->count();
            $leaderBoard[$i]['profile_image'] = $branch->info->profileImage->partner_profile_image;
            $leaderBoard[$i]['partner_name'] = $branch->info->partner_name;
            $leaderBoard[$i]['area'] = $branch->partner_area;
            $leaderBoard[$i]['branch_id'] = $branch->id;
            $leaderBoard[$i]['point'] = $current_transaction_count * $scan_point[10]['price'];
            $leaderBoard[$i]['prev_date'] = \Carbon\Carbon::yesterday()->toDateString();
            for ($j = 0; $j < count($previous_day_leaderBoard); $j++) {
                if ($previous_day_leaderBoard[$j]['branch_id'] == $branch->id) {
                    $leaderBoard[$i]['prev_index'] = $j;
                    break;
                }
            }
            $i++;
        }
        $array_point = array_column($leaderBoard, 'point');
        $array_name = array_column($leaderBoard, 'partner_name');
        array_multisort($array_point, SORT_DESC, $array_name, SORT_ASC, $leaderBoard);

        //monthly leader board prize
//        $leaderboard_monthly_prize = LeaderboardPrizes::where('month', Carbon::today()->month)->first();

        return $leaderBoard;
    }

    //function to get scanner leader board of current month
    public function scannerLeaderBoard()
    {
        $leaderBoard = $this->leaderBoardData();
        $year = date('Y');
        $month = date('m');

        return view('admin.production.branch.scannerLeaderBoard', compact('leaderBoard', 'year', 'month'));
    }

    //function to sort scanner leader board of specific month
    public function sortScannerLeaderBoard(Request $request)
    {
        $year = $request->get('year');
        $month = $request->get('month');
        $selected_time = $year.'-'.$month;
        $cur_time = date('Y-m');
        if ($cur_time == $selected_time) {
            return $this->scannerLeaderBoard();
        } else {
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
            $scan_point = AllAmounts::all();

            //till current day
            $i = 0;
            foreach ($branches as $branch) {
                $current_transaction_count = TransactionTable::where('branch_id', $branch->id)
                    ->where('posted_on', 'like', $selected_time.'%')->count();
                $leaderBoard[$i]['profile_image'] = $branch->info->profileImage->partner_profile_image;
                $leaderBoard[$i]['partner_name'] = $branch->info->partner_name;
                $leaderBoard[$i]['area'] = $branch->partner_area;
                $leaderBoard[$i]['branch_id'] = $branch->id;
                $leaderBoard[$i]['point'] = $current_transaction_count * $scan_point[10]['price'];
                $leaderBoard[$i]['prev_date'] = \Carbon\Carbon::yesterday()->toDateString();
                $leaderBoard[$i]['prev_index'] = null;
                $i++;
            }
            $array_point = array_column($leaderBoard, 'point');
            $array_name = array_column($leaderBoard, 'partner_name');
            array_multisort($array_point, SORT_DESC, $array_name, SORT_ASC, $leaderBoard);

            return view('admin.production.branch.scannerLeaderBoard', compact('leaderBoard', 'year', 'month'));
        }
    }

    //function to show all point prizes
    public function scannerPrizes()
    {
        $prizes = ScannerPrizes::orderBy('id', 'DESC')->get();

        return view('admin.production.branch.allScannerPrizes', compact('prizes'));
    }

    //function to add point prize
    public function createScannerPrize()
    {
        return view('admin.production.branch.createScannerPrize');
    }

    //function to store point prize
    public function storeScannerPrize(Request $request)
    {
        $this->validate($request, [
            'prize_text' => 'required',
            'prize_point' => 'required',
        ]);
        $request->flashOnly('prize_text', 'prize_point');
        $prize_text = $request->get('prize_text');
        $prize_point = $request->get('prize_point');

        $prize = new ScannerPrizes();
        $prize->text = $prize_text;
        $prize->point = $prize_point;
        $prize->save();

        return Redirect('branch-user-scanner-prizes')->with('created', 'New Prize Created Successfully');
    }

    //function to edit point prize
    public function editScannerPrize($prize_id)
    {
        $prize = ScannerPrizes::find($prize_id);

        return view('admin.production.branch.editScannerPrize', compact('prize'));
    }

    //function to update point prize
    public function updateScannerPrize(Request $request, $prize_id)
    {
        $this->validate($request, [
            'prize_text' => 'required',
            'prize_point' => 'required',
        ]);
        $request->flashOnly('prize_text', 'prize_point');
        $prize_text = $request->get('prize_text');
        $prize_point = $request->get('prize_point');

        $prize = ScannerPrizes::find($prize_id);
        $prize->text = $prize_text;
        $prize->point = $prize_point;
        $prize->save();

        return Redirect('branch-user-scanner-prizes')->with('updated', 'Prize Updated Successfully');
    }

    //function to delete point prize
    public function deleteScannerPrize($prize_id)
    {
        $prize = ScannerPrizes::find($prize_id);
        $prize->delete();

        return Redirect('branch-user-scanner-prizes')->with('deleted', 'Prize Deleted Successfully');
    }

    //function to show all point prizes
    public function leaderboardPrizes()
    {
        $prizes = LeaderboardPrizes::orderBy('id', 'DESC')->get();

        return view('admin.production.branch.allLeaderboardPrizes', compact('prizes'));
    }

    //function to add point prize
    public function createLeaderboardPrize()
    {
        return view('admin.production.branch.createLeaderboardPrize');
    }

    //function to store point prize
    public function storeLeaderboardPrize(Request $request)
    {
        $this->validate($request, [
            'prize_text' => 'required',
            'month_number' => 'required',
        ]);
        $request->flashOnly('prize_text', 'month_number');
        $prize_text = $request->get('prize_text');
        $monthNum = $request->get('month_number');
        $dateObj = DateTime::createFromFormat('!m', $monthNum);
        $monthName = $dateObj->format('F');

        $prize = new LeaderboardPrizes();
        $prize->month = $monthNum;
        $prize->month_name = $monthName;
        $prize->prize_text = $prize_text;
        $prize->save();

        return Redirect('branch-user-leaderboard-prizes')->with('created', 'New Prize Created Successfully');
    }

    //function to edit point prize
    public function editLeaderboardPrize($prize_id)
    {
        $prize = LeaderboardPrizes::find($prize_id);

        return view('admin.production.branch.editLeaderboardPrize', compact('prize'));
    }

    //function to update point prize
    public function updateLeaderboardPrize(Request $request, $prize_id)
    {
        $this->validate($request, [
            'prize_text' => 'required',
            'month_number' => 'required',
        ]);
        $request->flashOnly('prize_text', 'month_number');
        $prize_text = $request->get('prize_text');
        $monthNum = $request->get('month_number');
        $checkbox = $request->get('change_prize');
        $dateObj = DateTime::createFromFormat('!m', $monthNum);
        $monthName = $dateObj->format('F');

        $current_month = date('F');
        $next_month = date('F', strtotime($current_month.' next month'));
        if ($checkbox == 'on' && ($next_month == $monthName)) {
            $status = 1;
            session(['leaderboard_alert' => 1]);
        } else {
            $status = 0;
        }
        $prize = LeaderboardPrizes::find($prize_id);
        $prize->month = $monthNum;
        $prize->month_name = $monthName;
        $prize->prize_text = $prize_text;
        $prize->status = $status;
        $prize->save();

        return Redirect('branch-user-leaderboard-prizes')->with('updated', 'Prize Updated Successfully');
    }

    //function to delete point prize
    public function deleteLeaderboardPrize($prize_id)
    {
        $prize = LeaderboardPrizes::find($prize_id);
        $prize->delete();

        return Redirect('branch-user-leaderboard-prizes')->with('deleted', 'Prize Deleted Successfully');
    }
}
