<?php

namespace App\Http\Controllers\admin;

use App\BranchOffers;
use App\BranchRewardPayment;
use App\CustomerRewardRedeem;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Enum\AdminScannerType;
use App\Http\Controllers\Enum\GlobalTexts;
use App\Http\Controllers\Enum\RewardRequiredFieldsType;
use App\Http\Controllers\functionController;
use App\Http\Controllers\functionController2;
use App\Http\Controllers\JsonBranchUserController;
use App\Http\Controllers\jsonController;
use App\Http\Controllers\Reward\functionController as rewardFunctionController;
use App\PartnerBranch;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RewardController extends Controller
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create()
    {
        $branch_id = $this->request->get('branch_id');
        if ($branch_id != AdminScannerType::royalty_branch_id) {
            $partner_info = PartnerBranch::where('id', $branch_id)->with('info')->first();
        } else {
            $partner_info = null;
        }

        return view('admin.production.branchReward.create', compact('partner_info'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return RedirectResponse
     */
    public function store()
    {
        $this->validate($this->request, [
            'selling_points' => 'required|numeric',
            'actual_price' => 'required|numeric',
            'date_from2' => 'required',
            'date_to2' => 'required',
            'reward_description' => 'required',
            'reward_full_description' => 'required',
            'tnc' => 'required',
        ]);

        $this->request->flashOnly(['selling_points', 'actual_price', 'date_from2', 'date_to2', 'reward_description',
            'reward_full_description', 'tnc']);

        //new reward
        $branch_id = $this->request->get('branch_id');
        $date_from2 = date('d-m-Y', strtotime($this->request->get('date_from2')));
        $date_to2 = date('d-m-Y', strtotime($this->request->get('date_to2')));
        $points = $this->request->get('points') == null ? 0 : $this->request->get('points');
        $reward_description = $this->request->get('reward_description');
        $price = $this->request->get('price') == null ? 0 : $this->request->get('price');
        $actual_price = $this->request->get('actual_price') == null ? 0 : $this->request->get('actual_price');
        $counter_limit = $this->request->get('counter_limit');
        $scan_limit = $this->request->get('scan_limit');
        $reward_full_description = $this->request->get('reward_full_description');
        $tnc = $this->request->get('tnc');
        $valid_for = $this->request->get('valid_for') == null ? 1 : $this->request->get('valid_for');
        $selling_point = $this->request->get('selling_points');

        $image_url = '';
        if ($this->request->hasFile('offerImage')) {
            $image = $this->request->file('offerImage');
            //image is being resized & uploaded here
            $image_url = (new functionController)->uploadImageToAWS($image, 'dynamic-images/rbd-offers');
        }

        //weekdays
        $sat2 = ($this->request->get('sat2')) == null ? 0 : 1;
        $sun2 = ($this->request->get('sun2')) == null ? 0 : 1;
        $mon2 = ($this->request->get('mon2')) == null ? 0 : 1;
        $tue2 = ($this->request->get('tue2')) == null ? 0 : 1;
        $wed2 = ($this->request->get('wed2')) == null ? 0 : 1;
        $thu2 = ($this->request->get('thu2')) == null ? 0 : 1;
        $fri2 = ($this->request->get('fri2')) == null ? 0 : 1;

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

        $req_fields = [];
        if ($this->request->get('phone')) {
            $req_fields_phone = [
                'type' => RewardRequiredFieldsType::phone,
                'text' => GlobalTexts::reward_required_field_phone_txt,
            ];
            array_push($req_fields, $req_fields_phone);
        }

        if ($this->request->get('email')) {
            $req_fields_email = [
                'type' => RewardRequiredFieldsType::email,
                'text' => GlobalTexts::reward_required_field_email_txt,
            ];
            array_push($req_fields, $req_fields_email);
        }

        if ($this->request->get('del_add')) {
            $req_fields_del_add = [
                'type' => RewardRequiredFieldsType::del_add,
                'text' => GlobalTexts::reward_required_field_del_add_txt,
            ];
            array_push($req_fields, $req_fields_del_add);
        }

        if ($this->request->get('others')) {
            $req_fields_others = [
                'type' => RewardRequiredFieldsType::others,
                'text' => $this->request->get('others_value'),
            ];
            array_push($req_fields, $req_fields_others);
        }
        $week2 = json_encode($week2);
        $date2 = json_encode($date2);
        $hour2 = json_encode($hour2);
        $req_fields = json_encode($req_fields);

        try {
            DB::beginTransaction(); //to do query rollback

            BranchOffers::insert([
                'branch_id' => $branch_id,
                'date_duration' => $date2,
                'weekdays' => $week2,
                'time_duration' => $hour2,
                'point' => $points,
                'active' => 1,
                'offer_description' => $reward_description,
                'price' => $price,
                'counter_limit' => $counter_limit,
                'scan_limit' => $scan_limit,
                'tnc' => $tnc,
                'valid_for' => $valid_for,
                'actual_price' => $actual_price,
                'offer_full_description' => $reward_full_description,
                'image' => $image_url,
                'selling_point' => $selling_point,
                'created_at' => Carbon::now()->toDateTimeString(),
                'required_fields' => $req_fields,
            ]);

            if ($branch_id == AdminScannerType::royalty_branch_id) {
                $reward = BranchOffers::where('branch_id', $branch_id)->orderBy('id', 'DESC')->first();
                (new \App\Http\Controllers\AdminNotification\functionController())->newRbdRewardAddNotification($reward);
            }
            DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            DB::rollBack(); //rollback all successfully executed queries
            return redirect()->back()->with('try_again', 'Please try again!');
        }

        return redirect('/admin/reward/'.$branch_id)->with('status', 'New reward added!');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return View
     */
    public function show($id)
    {
        if ($id == AdminScannerType::royalty_branch_id) {
            $offers = BranchOffers::rewards($id)->with('rewardRedeems')->orderBy('id', 'DESC')->get();
            $branch_info = null;
            $show_tabs = true;
        } else {
            $offers = BranchOffers::rewards($id)->with('branch.info')->orderBy('id', 'DESC')->get();
            $branch_info = PartnerBranch::where('id', $id)->with('info')->first();
            $show_tabs = false;
        }

        $show_branch_info = false;

        return view('admin.production.branchReward.index', compact(
            'offers',
            'branch_info',
            'id',
            'show_branch_info',
            'show_tabs'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return View
     */
    public function edit($id)
    {
        $offer_details = BranchOffers::where('id', $id)->first();

        $time_duration_count2 = 0;
        $time_to_array2 = [];
        $time_from_array2 = [];
        if (isset($offer_details->time_duration)) {
            $time_duration_count2 = count($offer_details->time_duration);
            $point_details2 = $offer_details->time_duration;
            if ($time_duration_count2 > 0) {
                for ($i = 0; $i < $time_duration_count2; $i++) {
                    $time_to_array2[$i] = $point_details2[$i]['to'];
                    $time_from_array2[$i] = $point_details2[$i]['from'];
                }
            }
        }
        if ($offer_details->branch_id == AdminScannerType::royalty_branch_id) {
            $partner_info = null;
        } else {
            $partner_info = PartnerBranch::where('id', $offer_details->branch_id)->with('info')->first();
        }

        $phone = $email = $del_add = $others = null;
        if ($offer_details->required_fields && count($offer_details->required_fields) > 0) {
            for ($i = 0; $i < count($offer_details->required_fields); $i++) {
                if ($offer_details->required_fields[$i]['type'] == RewardRequiredFieldsType::phone) {
                    $phone = $offer_details->required_fields[$i]['text'];
                } elseif ($offer_details->required_fields[$i]['type'] == RewardRequiredFieldsType::email) {
                    $email = $offer_details->required_fields[$i]['text'];
                } elseif ($offer_details->required_fields[$i]['type'] == RewardRequiredFieldsType::del_add) {
                    $del_add = $offer_details->required_fields[$i]['text'];
                } elseif ($offer_details->required_fields[$i]['type'] == RewardRequiredFieldsType::others) {
                    $others = $offer_details->required_fields[$i]['text'];
                }
            }
        }

        return view('admin.production.branchReward.edit', compact(
            'offer_details',
            'partner_info',
            'time_duration_count2',
            'time_from_array2',
            'time_to_array2',
            'phone',
            'email',
            'del_add',
            'others'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update($id)
    {
        $this->validate($this->request, [
            'selling_points' => 'required|numeric',
            'actual_price' => 'required|numeric',
            'date_from2' => 'required',
            'date_to2' => 'required',
            'reward_description' => 'required',
            'reward_full_description' => 'required',
            'tnc' => 'required',
            'priority' => 'required',
        ]);

        $this->request->flashOnly(['selling_points', 'actual_price', 'date_from2', 'date_to2',
            'reward_description', 'reward_full_description', 'tnc', 'priority']);

        //new reward
        $branch_id = $this->request->get('branch_id');

        $date_from2 = date('d-m-Y', strtotime($this->request->get('date_from2')));
        $date_to2 = date('d-m-Y', strtotime($this->request->get('date_to2')));
        $points = $this->request->get('points') == null ? 0 : $this->request->get('points');
        $reward_description = $this->request->get('reward_description');
        $price = $this->request->get('price') == null ? 0 : $this->request->get('price');
        $actual_price = $this->request->get('actual_price') == null ? 0 : $this->request->get('actual_price');
        $counter_limit = $this->request->get('counter_limit');
        $scan_limit = $this->request->get('scan_limit');
        $reward_full_description = $this->request->get('reward_full_description');
        $tnc = $this->request->get('tnc');
        $valid_for = $this->request->get('valid_for') == null ? 1 : $this->request->get('valid_for');
        $selling_point = $this->request->get('selling_points');
        $priority = $this->request->get('priority');

        if ($this->request->hasFile('offerImage')) {
            $image = $this->request->file('offerImage');
            //image is being resized & uploaded here
            $image_url = (new functionController)->uploadImageToAWS($image, 'dynamic-images/rbd-offers');
        } else {
            $reward = BranchOffers::findOrFail($id);
            $image_url = $reward->image;
        }

        //weekdays
        $sat2 = ($this->request->get('sat2')) == null ? 0 : 1;
        $sun2 = ($this->request->get('sun2')) == null ? 0 : 1;
        $mon2 = ($this->request->get('mon2')) == null ? 0 : 1;
        $tue2 = ($this->request->get('tue2')) == null ? 0 : 1;
        $wed2 = ($this->request->get('wed2')) == null ? 0 : 1;
        $thu2 = ($this->request->get('thu2')) == null ? 0 : 1;
        $fri2 = ($this->request->get('fri2')) == null ? 0 : 1;

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
        //required fields
        $req_fields = [];
        if ($this->request->get('phone')) {
            $req_fields_phone = [
                'type' => RewardRequiredFieldsType::phone,
                'text' => GlobalTexts::reward_required_field_phone_txt,
            ];
            array_push($req_fields, $req_fields_phone);
        }

        if ($this->request->get('email')) {
            $req_fields_email = [
                'type' => RewardRequiredFieldsType::email,
                'text' => GlobalTexts::reward_required_field_email_txt,
            ];
            array_push($req_fields, $req_fields_email);
        }

        if ($this->request->get('del_add')) {
            $req_fields_del_add = [
                'type' => RewardRequiredFieldsType::del_add,
                'text' => GlobalTexts::reward_required_field_del_add_txt,
            ];
            array_push($req_fields, $req_fields_del_add);
        }

        if ($this->request->get('others')) {
            $req_fields_others = [
                'type' => RewardRequiredFieldsType::others,
                'text' => $this->request->get('others_value'),
            ];
            array_push($req_fields, $req_fields_others);
        }

        $week2 = json_encode($week2);
        $date2 = json_encode($date2);
        $hour2 = json_encode($hour2);
        $req_fields = json_encode($req_fields);

        try {
            DB::beginTransaction(); //to do query rollback

            BranchOffers::where('id', $id)
                ->update([
                    'branch_id' => $branch_id,
                    'date_duration' => $date2,
                    'weekdays' => $week2,
                    'time_duration' => $hour2,
                    'point' => $points,
                    'active' => 1,
                    'offer_description' => $reward_description,
                    'price' => $price,
                    'counter_limit' => $counter_limit,
                    'scan_limit' => $scan_limit,
                    'tnc' => $tnc,
                    'valid_for' => $valid_for,
                    'actual_price' => $actual_price,
                    'offer_full_description' => $reward_full_description,
                    'priority' => $priority,
                    'image' => $image_url,
                    'selling_point' => $selling_point,
                    'updated_at' => Carbon::now()->toDateTimeString(),
                    'required_fields' => $req_fields,
                ]);

            DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            DB::rollBack(); //rollback all successfully executed queries
            return redirect()->back()->with('try_again', 'Please try again!');
        }
        if ($this->request->get('prev_url') == url('admin/partner_rewards')) {
            return redirect($this->request->get('prev_url'))->with('status', 'Reward updated successfully!');
        } else {
            return redirect('/admin/reward/'.$branch_id)->with('status', 'Reward updated successfully!');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //check if can be deleted or not
        $exists = CustomerRewardRedeem::where('offer_id', $id)->count();
        if ($exists > 0) {
            return redirect()->back()->with('try_again', 'You can not delete this reward');
        }
        $branch_offer = BranchOffers::findOrFail($id);

        try {
            DB::beginTransaction(); //to do query rollback

            $branch_offer->delete();

            DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            DB::rollBack(); //rollback all successfully executed queries
            return redirect()->back()->with('try_again', 'Please try again!');
        }

        return redirect()->back()->with('delete', 'One reward deleted!');
    }

    public function partnerRewards()
    {
        $offers = BranchOffers::where('branch_id', '!=', AdminScannerType::royalty_branch_id)->where('selling_point', '!=', null)
            ->with('branch.info', 'rewardRedeems')->orderBy('id', 'DESC')->get();
        $show_branch_info = true;
        $show_tabs = true;

        return view('admin.production.branchReward.index', compact('offers', 'show_branch_info', 'show_tabs'));
    }

    public function activateReward($id)
    {
        BranchOffers::where('id', $id)->update(['active' => 1]);

        return redirect()->back();
    }

    public function deactivateReward($id)
    {
        BranchOffers::where('id', $id)->update(['active' => 0]);

        return redirect()->back();
    }

    public function redeemedRewards($status)
    {
        $type = 'royalty';
        $redeems = $this->getRedeems($status, $type);

        return view('admin.production.branchReward.redeemed_rewards', compact('redeems', 'status', 'type'));
    }

    public function partnerRedeemedRewards($status)
    {
        $type = 'partner';
        $redeems = $this->getRedeems($status, $type);

        return view('admin.production.branchReward.redeemed_rewards', compact('redeems', 'status', 'type'));
    }

    public function getRedeems($status, $type)
    {
        if ($status == 'all') {
            $redeems = CustomerRewardRedeem::with('reward', 'customer')->orderBy('id', 'DESC')->get();
        } elseif ($status == 'requested') {
            $redeems = CustomerRewardRedeem::where('used', 0)->with('reward', 'customer')->orderBy('id', 'DESC')->get();
        } elseif ($status == 'dispatched') {
            $redeems = CustomerRewardRedeem::where('used', 1)->with('reward', 'customer')->orderBy('id', 'DESC')->get();
        } else {
            $redeems = [];
        }
        $redeems = collect($redeems);
        if ($type == 'royalty') {
            return $redeems->where('reward.branch_id', AdminScannerType::royalty_branch_id);
        } else {
            return $redeems->where('reward.branch_id', '!=', AdminScannerType::royalty_branch_id);
        }
    }

    public function dispatchReward($id)
    {
        $redeem = CustomerRewardRedeem::where('id', $id)->first();
        $redeem->used = 1;
        $redeem->save();
        if ($redeem->reward->branch_id == AdminScannerType::royalty_branch_id) {
            return redirect('admin/redeemed_reward/royalty/dispatched')->with('success', 'Successfully Dispatched');
        } else {
            return redirect('admin/redeemed_reward/partner/dispatched')->with('success', 'Successfully Dispatched');
        }
    }

    public function rewardPayment()
    {
        $branches = PartnerBranch::all();
        foreach ($branches as $branch) {
            $branch->rewards = (new rewardFunctionController())->getSpecificPartnerReward($branch->id, true);
            $branch->reward_count = count((new rewardFunctionController())->getSpecificPartnerReward($branch->id, true));
        }
        $branches = collect($branches)->where('reward_count', '>', 0);
//        dd($branches->toArray());
        $branches = (new functionController2())->getPaginatedData($branches, 10);

        return view('admin.production.branchReward.payment', compact('branches'));
    }

    public function partnerWithBranchForSearch()
    {
        $keyword = $this->request->term;
        $partners = DB::table('partner_info as pi')
            ->join('partner_branch as pb', 'pb.partner_account_id', '=', 'pi.partner_account_id')
            ->select('pi.partner_name', 'pb.partner_address')
            ->where('partner_name', 'like', '%'.$keyword.'%')
            ->get();

        $partners = json_decode(json_encode($partners), true);
        if (count($partners) == 0) {
            $result[] = 'No partner found';
        } else {
            foreach ($partners as $partner) {
                $result[] = $partner['partner_name'].'=>'.$partner['partner_address'];
            }
        }

        return $result;
    }

    public function getSinglePartnerForPayment()
    {
        $keyword = $this->request->get('partner');
        $keyword = explode('=>', $keyword);

        $branches = PartnerBranch::where('partner_address', $keyword[1])->paginate(10);
        foreach ($branches as $branch) {
            $branch->rewards = (new rewardFunctionController())->getSpecificPartnerReward($branch->id, true);
        }

        return view('admin.production.branchReward.payment', compact('branches'));
    }

    public function royaltyRewardCosting()
    {
        $rewards = BranchOffers::where('branch_id', AdminScannerType::royalty_branch_id)
            ->with('rewardRedeems')
            ->where('selling_point', '!=', null)
            ->orderBy('id', 'DESC')
            ->paginate(10);

        return view('admin.production.branchReward.royalty_cost', compact('rewards'));
    }

    public function clearRewardPayment($branch_id)
    {
        $payment = (new \App\Http\Controllers\Reward\functionController())->branchPayments($branch_id);
        $amount = $this->request->get('amount_to_pay');
        if ($amount > $payment['due']) {
            return redirect()->back()->with('error', 'Please enter correct amount');
        }

        $branch_payment = new BranchRewardPayment();
        $branch_payment->branch_id = $branch_id;
        $branch_payment->amount = $amount;
        $branch_payment->save();

        return redirect()->back()->with('success', 'Payment successful.');
    }
}
