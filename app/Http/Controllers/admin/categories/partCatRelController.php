<?php

namespace App\Http\Controllers\admin\categories;

use App\Http\Controllers\Controller;
use App\PartnerAccount;
use App\PartnerCategoryRelation;
use Illuminate\Http\Request;

class partCatRelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $relations = PartnerCategoryRelation::with('info', 'categoryRelation.mainCategory',
            'categoryRelation.sub_cat_1', 'categoryRelation.sub_cat_2')->orderBy('id', 'DESC')->get();

        return view('admin.production.categories.part_cat_rel.index', compact('relations'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        $relation = PartnerCategoryRelation::where('id', $id)->with('categoryRelation.mainCategory', 'categoryRelation.sub_cat_1', 'categoryRelation.sub_cat_2')->first();
        $partners = PartnerAccount::where('active', 1)->with('info')->get();

        return view('admin.production.categories.part_cat_rel.edit', compact('relation', 'partners'));
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
        $relation = PartnerCategoryRelation::find($id);
        $relation->partner_id = $request->get('partner_id');
        $relation->save();

        return redirect('admin/part_cat_relation')->with('success', 'Relation updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $relation = PartnerCategoryRelation::find($id);
        $relation->delete();

        return redirect()->back()->with('success', 'Relation deleted successfully');
    }
}
