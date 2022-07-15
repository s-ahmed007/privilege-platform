<?php

namespace App\Http\Controllers;

use App\BranchOffers;
use App\CustomerTransactionRequest;
use App\CustomizePoint;
use App\PartnerBranch;
use App\PartnerInfo;
use App\TransactionTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BranchOfferController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        dd('index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $branch_id = $_GET['id'];
        $partner_info = PartnerBranch::where('id', $branch_id)->with('info')->first();

        return view('admin.production.branchOffers.create', compact('partner_info'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'points' => 'required|numeric',
            'date_from2' => 'required',
            'date_to2' => 'required',
            'offer_full_description' => 'required',
            'tnc' => 'required',
            'valid_for' => 'required',
        ]);

        $request->flashOnly(['points', 'date_from2', 'date_to2', 'offer_full_description', 'tnc', 'valid_for']);

        //new offer
        $branch_id = $request->get('branch_id');
        $date_from2 = date('d-m-Y', strtotime($request->get('date_from2')));
        $date_to2 = date('d-m-Y', strtotime($request->get('date_to2')));
        $points = $request->get('points');
        $offer_description = $request->get('offer_description');
        $price = $request->get('price') == null ? 0 : $request->get('price');
        $actual_price = $request->get('actual_price') == null ? 0 : $request->get('actual_price');
        $counter_limit = $request->get('counter_limit');
        $scan_limit = $request->get('scan_limit');
        $offer_full_description = $request->get('offer_full_description');
        $tnc = $request->get('tnc');
        $valid_for = $request->get('valid_for');

        //weekdays
        $sat2 = ($request->get('sat2')) == null ? 0 : 1;
        $sun2 = ($request->get('sun2')) == null ? 0 : 1;
        $mon2 = ($request->get('mon2')) == null ? 0 : 1;
        $tue2 = ($request->get('tue2')) == null ? 0 : 1;
        $wed2 = ($request->get('wed2')) == null ? 0 : 1;
        $thu2 = ($request->get('thu2')) == null ? 0 : 1;
        $fri2 = ($request->get('fri2')) == null ? 0 : 1;

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
        $week2 = json_encode($week2);
        $date2 = json_encode($date2);
        $hour2 = json_encode($hour2);
        //dd($week2, $date2, $hour2);
        try {
            DB::beginTransaction(); //to do query rollback

            BranchOffers::insert([
                'branch_id' => $branch_id,
                'date_duration' => $date2,
                'weekdays' => $week2,
                'time_duration' => $hour2,
                'point' => $points,
                'active' => 1,
                'offer_description' => $offer_description,
                'price' => $price,
                'counter_limit' => $counter_limit,
                'scan_limit' => $scan_limit,
                'tnc' => $tnc,
                'valid_for' => $valid_for,
                'actual_price' => $actual_price,
                'offer_full_description' => $offer_full_description,
            ]);
            $offer = BranchOffers::where('branch_id', $branch_id)->orderBy('id', 'DESC')->first();
            (new \App\Http\Controllers\AdminNotification\functionController())->newOfferAddNotification($offer);

            DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            DB::rollBack(); //rollback all successfully executed queries
            return redirect()->back()->with('try_again', 'Please try again!');
        }

        return redirect('/branch-offers/'.$branch_id)->with('status', 'New offer added!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $offers = BranchOffers::offers($id)->with('branch.info')->orderBy('id', 'DESC')->get();
        $branch_info = PartnerBranch::where('id', $id)->with('info')->first();

        return view('admin.production.branchOffers.index', compact('offers', 'branch_info', 'id'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
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
        $partner_info = PartnerBranch::where('id', $offer_details->branch_id)->with('info')->first();

        return view('admin.production.branchOffers.edit', compact(
            'offer_details',
            'partner_info',
            'time_duration_count2',
            'time_from_array2',
            'time_to_array2'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'points' => 'required|numeric',
            'date_from2' => 'required',
            'date_to2' => 'required',
            'offer_full_description' => 'required',
            'tnc' => 'required',
            'valid_for' => 'required',
            'priority' => 'required',
        ]);

        $request->flashOnly(['points', 'date_from2', 'date_to2', 'offer_full_description', 'tnc', 'valid_for', 'priority']);

        $date_from2 = date('d-m-Y', strtotime($request->get('date_from2')));
        $date_to2 = date('d-m-Y', strtotime($request->get('date_to2')));
        $points = $request->get('points');
        $offer_description = $request->get('offer_description');
        $price = $request->get('price') == null ? 0 : $request->get('price');
        $actual_price = $request->get('actual_price') == null ? 0 : $request->get('actual_price');
        $counter_limit = $request->get('counter_limit');
        $scan_limit = $request->get('scan_limit');
        $offer_full_description = $request->get('offer_full_description');
        $tnc = $request->get('tnc');
        $valid_for = $request->get('valid_for');
        $priority = $request->get('priority');

        //weekdays
        $sat2 = ($request->get('sat2')) == null ? 0 : 1;
        $sun2 = ($request->get('sun2')) == null ? 0 : 1;
        $mon2 = ($request->get('mon2')) == null ? 0 : 1;
        $tue2 = ($request->get('tue2')) == null ? 0 : 1;
        $wed2 = ($request->get('wed2')) == null ? 0 : 1;
        $thu2 = ($request->get('thu2')) == null ? 0 : 1;
        $fri2 = ($request->get('fri2')) == null ? 0 : 1;

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

        try {
            DB::beginTransaction(); //to do query rollback

            $offer = BranchOffers::findOrFail($id);
            $offer->date_duration = $date2;
            $offer->weekdays = $week2;
            $offer->time_duration = $hour2;
            $offer->point = $points;
            $offer->offer_description = $offer_description;
            $offer->price = $price;
            $offer->counter_limit = $counter_limit;
            $offer->scan_limit = $scan_limit;
            $offer->tnc = $tnc;
            $offer->valid_for = $valid_for;
            $offer->actual_price = $actual_price;
            $offer->offer_full_description = $offer_full_description;
            $offer->priority = $priority;
            $offer->save();

            DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            DB::rollBack(); //rollback all successfully executed queries
            return redirect()->back()->with('try_again', 'Please try again!');
        }

        return redirect('/branch-offers/'.$offer->branch_id)->with('status', 'Offer updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //check if can be deleted or not
        $exists1 = CustomerTransactionRequest::where('offer_id', $id)->count();
        $exists2 = TransactionTable::where('offer_id', $id)->count();
        if (($exists1 || $exists2) > 0) {
            return redirect()->back()->with('try_again', 'You can not delete this offer');
        }
        $branch_offer = BranchOffers::findOrFail($id);

        try {
            DB::beginTransaction(); //to do query rollback

            CustomizePoint::where('id', $branch_offer->point_customize_id)->delete();
            $branch_offer->delete();

            DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            DB::rollBack(); //rollback all successfully executed queries
            return redirect()->back()->with('try_again', 'Please try again!');
        }

        return redirect()->back()->with('delete', 'One offer deleted!');
    }

    public function activateOffer($id)
    {
        BranchOffers::where('id', $id)->update(['active' => 1]);

        return redirect()->back();
    }

    public function deactivateOffer($id)
    {
        BranchOffers::where('id', $id)->update(['active' => 0]);

        return redirect()->back();
    }

    public function addCustomPoint($id)
    {
        $partner_info = BranchOffers::where('id', $id)->with('branch.info')->first();

        return view('admin.production.branchOffers.addCustomPoint', compact('partner_info'));
    }

    public function storeCustomPoint(Request $request)
    {
        $this->validate($request, [
            'point_customize_type' => 'required|numeric',
            'point_multiplier' => 'required|numeric',
            'date_from' => 'required',
            'date_to' => 'required',
        ]);

        $request->flashOnly(['point_customize_type', 'point_multiplier', 'date_from', 'date_to']);
        //customize point
        $offer_id = $request->get('offer_id');
        $point_type = $request->get('point_customize_type');
        $date_from = $request->get('date_from');
        $date_to = $request->get('date_to');
        $point_multiplier = $request->get('point_multiplier');
        $description = $request->get('description');

        //weekdays
        $sat = ($request->get('sat')) == null ? 0 : 1;
        $sun = ($request->get('sun')) == null ? 0 : 1;
        $mon = ($request->get('mon')) == null ? 0 : 1;
        $tue = ($request->get('tue')) == null ? 0 : 1;
        $wed = ($request->get('wed')) == null ? 0 : 1;
        $thu = ($request->get('thu')) == null ? 0 : 1;
        $fri = ($request->get('fri')) == null ? 0 : 1;

        //create json variable
        $week[] = [
            'sat' => $sat.'',
            'sun' => $sun.'',
            'mon' => $mon.'',
            'tue' => $tue.'',
            'wed' => $wed.'',
            'thu' => $thu.'',
            'fri' => $fri.'',
        ];
        $date[] = [
            'from' => $date_from,
            'to' => $date_to,
        ];

        $hour = [];
        if (isset($_POST['time_duration_from']) && isset($_POST['time_duration_to'])) {
            for ($i = 0; $i < count($_POST['time_duration_from']); $i++) {
                if ($_POST['time_duration_from'][$i] != '' && $_POST['time_duration_to'][$i] != '') {
                    $hour[] = [
                        'from' => $_POST['time_duration_from'][$i],
                        'to' => $_POST['time_duration_to'][$i],
                    ];
                }
            }
        } else {
            $hour = [];
        }

        $week = json_encode($week);
        $date = json_encode($date);
        $hour = json_encode($hour);
        try {
            DB::beginTransaction(); //to do query rollback
            $id = DB::table('point_customize')
                ->insertGetId([
                    'point_type' => $point_type,
                    'date_duration' => $date,
                    'weekdays' => $week,
                    'time_duration' => $hour,
                    'point_multiplier' => $point_multiplier,
                    'description' => $description,
                ]);

            $branch_offer = BranchOffers::findOrFail($offer_id);
            $branch_offer->point_customize_id = $id;
            $branch_offer->save();

            DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            DB::rollBack(); //rollback all successfully executed queries
            return redirect()->back()->with('try_again', 'Please try again!');
        }

        return redirect('/branch-offers/'.$branch_offer->branch_id)->with('status', 'Custom point added successfully!');
    }

    public function editCustomPoint($id)
    {
        $offer_details = BranchOffers::where('id', $id)->with('customizedPoint')->first();
        $time_duration_count = 0;
        $time_to_array = [];
        $time_from_array = [];

        if (isset($offer_details->customizedPoint->time_duration)) {
            $time_duration_count = count($offer_details->customizedPoint->time_duration);
            $point_details = $offer_details->customizedPoint->time_duration;
            if ($time_duration_count > 0) {
                for ($i = 0; $i < $time_duration_count; $i++) {
                    $time_to_array[$i] = $point_details[$i]['to'];
                    $time_from_array[$i] = $point_details[$i]['from'];
                }
            }
        }
        $partner_info = PartnerBranch::where('id', $offer_details->branch_id)->with('info')->first();

        return view('admin.production.branchOffers.editCustomPoint', compact(
            'offer_details',
            'partner_info',
            'time_duration_count',
            'time_from_array',
            'time_to_array'
        ));
    }

    public function updateCustomPoint(Request $request)
    {
        $this->validate($request, [
            'point_customize_type' => 'required|numeric',
            'point_multiplier' => 'required|numeric',
            'date_from' => 'required',
            'date_to' => 'required',
        ]);

        $request->flashOnly(['point_customize_type', 'point_multiplier', 'date_from', 'date_to']);
        //customize point
        $branch_id = $request->get('branch_id');
        $custom_point_id = $request->get('custom_point_id');
        $point_type = $request->get('point_customize_type');
        $date_from = $request->get('date_from');
        $date_to = $request->get('date_to');
        $point_multiplier = $request->get('point_multiplier');
        $description = $request->get('description');

        //weekdays
        $sat = ($request->get('sat')) == null ? 0 : 1;
        $sun = ($request->get('sun')) == null ? 0 : 1;
        $mon = ($request->get('mon')) == null ? 0 : 1;
        $tue = ($request->get('tue')) == null ? 0 : 1;
        $wed = ($request->get('wed')) == null ? 0 : 1;
        $thu = ($request->get('thu')) == null ? 0 : 1;
        $fri = ($request->get('fri')) == null ? 0 : 1;

        //create json variable
        $week[] = [
            'sat' => $sat.'',
            'sun' => $sun.'',
            'mon' => $mon.'',
            'tue' => $tue.'',
            'wed' => $wed.'',
            'thu' => $thu.'',
            'fri' => $fri.'',
        ];
        $date[] = [
            'from' => $date_from,
            'to' => $date_to,
        ];

        $hour = [];
        if (isset($_POST['time_duration_from']) && isset($_POST['time_duration_to'])) {
            for ($i = 0; $i < count($_POST['time_duration_from']); $i++) {
                if ($_POST['time_duration_from'][$i] != '' && $_POST['time_duration_to'][$i] != '') {
                    $hour[] = [
                        'from' => $_POST['time_duration_from'][$i],
                        'to' => $_POST['time_duration_to'][$i],
                    ];
                }
            }
        } else {
            $hour = [];
        }

        $week = json_encode($week);
        $date = json_encode($date);
        $hour = json_encode($hour);
        try {
            DB::beginTransaction(); //to do query rollback
            DB::table('point_customize')->where('id', $custom_point_id)
                ->update([
                    'point_type' => $point_type,
                    'date_duration' => $date,
                    'weekdays' => $week,
                    'time_duration' => $hour,
                    'point_multiplier' => $point_multiplier,
                    'description' => $description,
                ]);

            DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            DB::rollBack(); //rollback all successfully executed queries
            return redirect()->back()->with('try_again', 'Please try again!');
        }

        return redirect('/branch-offers/'.$branch_id)->with('status', 'Custom point updated successfully!');
    }
}
