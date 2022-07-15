<?php

namespace App\Http\Controllers\admin\categories;

use App\Categories;
use App\CategoryRelation;
use App\Http\Controllers\Controller;
use App\PartnerCategoryRelation;
use App\PartnerInfo;
use App\SubCat1;
use App\SubCat2;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class catRelController extends Controller
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
        $cat_rels = CategoryRelation::with('mainCategory', 'sub_cat_1', 'sub_cat_2')->orderBy('id', 'DESC')->get();
        $main_cats = Categories::orderBy('id', 'DESC')->get();
        $sub_cat_1 = SubCat1::orderBy('id', 'DESC')->get();
        $sub_cat_2 = SubCat2::orderBy('id', 'DESC')->get();

        return view('admin.production.categories.cat_rel.index', compact('cat_rels', 'main_cats', 'sub_cat_1', 'sub_cat_2'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
//        $main_cats = Categories::all();
//        $sub_cat_1 = SubCat1::all();
//        $sub_cat_2 = SubCat2::all();
//        return view('admin.production.categories.cat_rel.create', compact('main_cats', 'sub_cat_1', 'sub_cat_2'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $this->validate($this->request, [
            'main_cat' => 'required',
            'sub_cat_1' => 'required_with:sub_cat_2',
            'sub_cat_2' => 'nullable',
        ]);
        $this->request->flashOnly(['main_cat', 'sub_cat_1', 'sub_cat_2']);

        $main_cat = $this->request->get('main_cat');
        $sub_cat_1 = $this->request->get('sub_cat_1');
        $sub_cat_2 = $this->request->get('sub_cat_2');

        $exists = CategoryRelation::where([['main_cat', $main_cat], ['sub_cat_1_id', $sub_cat_1], ['sub_cat_2_id', $sub_cat_2]])->count();
        if ($exists == 0) {
            $relation = new CategoryRelation();
            $relation->main_cat = $main_cat;
            $relation->sub_cat_1_id = $sub_cat_1;
            $relation->sub_cat_2_id = $sub_cat_2;
            $relation->save();
        } else {
            return redirect()->back()->with('try_again', 'Relation already exists.');
        }

        return redirect()->back()->with('success', 'Relation successfully created.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $relation = CategoryRelation::find($id);
        if ($relation) {
            $main_cats = Categories::orderBy('id', 'DESC')->get();
            $sub_cat_1 = SubCat1::orderBy('id', 'DESC')->get();
            $sub_cat_2 = SubCat2::orderBy('id', 'DESC')->get();

            return view('admin.production.categories.cat_rel.edit', compact('relation', 'main_cats',
                'sub_cat_1', 'sub_cat_2'));
        } else {
            return redirect()->back()->with('try_again', 'Relation not found');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $this->validate($this->request, [
            'main_cat' => 'required',
            'sub_cat_1' => 'required_with:sub_cat_2',
            'sub_cat_2' => 'nullable',
        ]);
        $this->request->flashOnly(['main_cat', 'sub_cat_1', 'sub_cat_2']);

        $main_cat = $this->request->get('main_cat');
        $sub_cat_1 = $this->request->get('sub_cat_1');
        $sub_cat_2 = $this->request->get('sub_cat_2');

        $exists = CategoryRelation::where([['main_cat', $main_cat], ['sub_cat_1_id', $sub_cat_1], ['sub_cat_2_id', $sub_cat_2]])->count();
        if ($exists == 0) {
            $relation = CategoryRelation::find($id);
            $relation->main_cat = $main_cat;
            $relation->sub_cat_1_id = $sub_cat_1;
            $relation->sub_cat_2_id = $sub_cat_2;
            $relation->save();
        } else {
            return redirect()->back()->with('try_again', 'Relation already exists.');
        }

        return redirect()->back()->with('success', 'Relation successfully updated.');
    }

    /**
     * Assign partner to specified category relation.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function assignPartnerView($id)
    {
        $partners = PartnerInfo::orderBy('id', 'DESC')->get();
        $cat_rel = CategoryRelation::with('mainCategory', 'sub_cat_1', 'sub_cat_2')
            ->where('id', $id)->orderBy('id', 'DESC')->first();
        $assigned_partners = PartnerCategoryRelation::where('cat_rel_id', $id)->with('info')->orderBy('id', 'DESC')->get();

        return view('admin.production.categories.cat_rel.assign_partner', compact('partners', 'cat_rel', 'assigned_partners'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return RedirectResponse
     */
    public function destroy($id)
    {
        $exists = PartnerCategoryRelation::where('cat_rel_id', $id)->count();
        if ($exists > 0) {
            return redirect()->back()->with('try_again', 'Please delete the partner-category relation first');
        }
        $cat_rel = CategoryRelation::find($id);
        $cat_rel->delete();

        return redirect()->back()->with('success', 'Category relation deleted successfully');
    }

    /**
     * Assign partner to specified category relation.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function storeAssignPartner($id)
    {
        $this->validate($this->request, [
            'partner' => 'required',
        ]);
        $this->request->flashOnly(['partner']);
        $partner = $this->request->partner;

        $exists = PartnerCategoryRelation::where('cat_rel_id', $id)->where('partner_id', $partner)->count();

        if ($exists == 0) {
            $partner_cat_rel = new PartnerCategoryRelation();
            $partner_cat_rel->cat_rel_id = $id;
            $partner_cat_rel->partner_id = $partner;
            $partner_cat_rel->save();
        } else {
            return redirect()->back()->with('try_again', 'Partner already assigned to this category');
        }

        return redirect()->back()->with('success', 'Partner successfully assigned');
    }

    /**
     * Assign partner to specified category relation.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function removeAssignedPartner($id)
    {
        PartnerCategoryRelation::where('id', $id)->delete();

        return redirect()->back()->with('success', 'Partner successfully removed');
    }
}
