<?php

namespace App\Http\Controllers\admin;

use App\BranchFacility;
use App\Categories;
use App\Http\Controllers\Controller;
use App\Http\Controllers\functionController;
use App\Rules\unique_if_changed;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BranchFacilitiesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        $facilities = BranchFacility::all();
        foreach ($facilities as $key => $facility) {
            if (count($facility->category_ids) > 0) {
                $categories = Categories::whereIn('id', $facility->category_ids)
                    ->orderBy('priority', 'DESC')
                    ->get()
                    ->pluck('name');
                $facility->categories = $categories;
            }
        }

        return view('admin.production.branchFacilities.index', compact('facilities'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create()
    {
        $categories = Categories::orderBy('priority', 'DESC')->get();

        return view('admin.production.branchFacilities.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:branch_facilities,name',
        ]);

        $image_url = null;

        if ($request->hasFile('icon')) {
            $file = $request->file('icon');
            $image_url = (new functionController)->uploadProPicToAWS($file, 'dynamic-images/branch_facilities');
        }
        $categories = Categories::all();
        $ids = [];
        foreach ($categories as $key => $category) {
            if ($request->input($category->type) != null) {
                array_push($ids, $category->id);
            }
        }

        $facility = new BranchFacility();
        $facility->name = $request->input('name');
        $facility->icon = $image_url;
        $facility->category_ids = $ids;
        $facility->save();

        return redirect('admin/branch_facilities')->with('status', 'New facility added successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\BranchFacility  $branchFacility
     * @return \Illuminate\Http\Response
     */
    public function show(BranchFacility $branchFacility)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\BranchFacility  $branchFacility
     * @return View
     */
    public function edit(BranchFacility $branchFacility)
    {
        $categories = Categories::orderBy('priority', 'DESC')->get();

        return view('admin.production.branchFacilities.edit', compact('branchFacility', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\BranchFacility  $branchFacility
     * @return RedirectResponse
     */
    public function update(Request $request, BranchFacility $branchFacility)
    {
        $this->validate($request, [
            'name' => 'required', new unique_if_changed($branchFacility->id, 'branch_facilities', 'name', 'id', 'Already taken'
        ), ]);

        $image_url = $branchFacility->icon;
        if ($request->hasFile('icon')) {
            $file = $request->file('icon');
            $image_url = (new functionController)->uploadProPicToAWS($file, 'dynamic-images/branch_facilities');
        }
        $categories = Categories::all();
        $ids = [];
        foreach ($categories as $key => $category) {
            if ($request->input($category->type) != null) {
                array_push($ids, $category->id);
            }
        }

        $branchFacility->name = $request->input('name');
        $branchFacility->icon = $image_url;
        $branchFacility->category_ids = $ids;
        $branchFacility->save();

        return redirect('admin/branch_facilities')->with('status', 'Facility updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\BranchFacility  $branchFacility
     * @return RedirectResponse
     */
    public function destroy(BranchFacility $branchFacility)
    {
        $image_path = $branchFacility->icon;
        $branchFacility->delete();

        $image_exploded_path = explode('/', $image_path);
        if (strpos($image_path, 'https://royalty-bd.s3.ap-southeast-1.amazonaws.com/') !== false) {
            Storage::disk('s3')->delete('dynamic-images/branch_facilities/'.end($image_exploded_path));
        }

        return redirect('admin/branch_facilities')->with('delete', 'Facility deleted successfully.');
    }
}
