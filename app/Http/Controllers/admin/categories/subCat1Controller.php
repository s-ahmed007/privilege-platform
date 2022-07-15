<?php

namespace App\Http\Controllers\admin\categories;

use App\CategoryRelation;
use App\Http\Controllers\Controller;
use App\PartnerCategoryRelation;
use App\SubCat1;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class subCat1Controller extends Controller
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
        $categories = SubCat1::orderBy('id', 'DESC')->get();

        return view('admin.production.categories.sub_cat_1.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.production.categories.sub_cat_1.create');
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
            'category_name' => 'required',
        ]);
        $this->request->flashOnly(['category_name']);

        $category_name = $this->request->post('category_name');
        DB::table('sub_cat_1')
            ->insert([
                'cat_name' => $category_name,
            ]);
        // if (Session::has('user_profile_image_name')) {
        //     //just update the new image info
        //     Storage::disk('s3')->put('dynamic-images/categories/' . Session::get('user_profile_image_name'), Session::get('user_profile_image'), 'public');
        //     $image_url = Storage::disk('s3')->url('dynamic-images/categories/' . Session::get('user_profile_image_name'));

        //     //update category info in database
        //     DB::table('sub_cat_1')
        //         ->insert([
        //             'cat_name' => $category_name,
        //             'icon' => $image_url
        //         ]);

        //     //remove session of cropped image
        //     $this->request->session()->forget('user_profile_image_name');
        //     $this->request->session()->forget('user_profile_image');
        // } else {
        //     return redirect()->back()->with('status', 'Please select image');
        // }
        return redirect('admin/sub_cat_1')->with('success', 'Category updated successfully');
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
        $category = SubCat1::findOrFail($id);

        return view('admin.production.categories.sub_cat_1.edit', compact('category'));
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
            'category_name' => 'required',
        ]);
        $this->request->flashOnly(['category_name']);

        $category_name = $this->request->post('category_name');
        DB::table('sub_cat_1')
            ->where('id', $id)
            ->update([
                'cat_name' => $category_name,
            ]);
        // if (Session::has('user_profile_image_name')) {
        //     //get current image name
        //     $get_current_image_name = SubCat1::select('icon')->where('id', $id)->first();

        //     $image_path = $get_current_image_name->icon;
        //     $exploded_path = explode('/', $image_path);

        //     if (strpos($image_path, 'https://royalty-bd.s3.ap-southeast-1.amazonaws.com/') !== false) {
        //         //remove previous profile image from folder
        //         Storage::disk('s3')->delete('dynamic-images/categories/' . end($exploded_path));
        //         //update new image info
        //         Storage::disk('s3')->put('dynamic-images/categories/' . Session::get('user_profile_image_name'), Session::get('user_profile_image'), 'public');
        //         $image_url = Storage::disk('s3')->url('dynamic-images/categories/' . Session::get('user_profile_image_name'));
        //     } else {
        //         //just update the new image info
        //         Storage::disk('s3')->put('dynamic-images/categories/' . Session::get('user_profile_image_name'), Session::get('user_profile_image'), 'public');
        //         $image_url = Storage::disk('s3')->url('dynamic-images/categories/' . Session::get('user_profile_image_name'));
        //     }

        //     //update category info in database
        //     DB::table('sub_cat_1')
        //         ->where('id', $id)
        //         ->update([
        //             'cat_name' => $category_name,
        //             'icon' => $image_url
        //         ]);

        //     //remove session of cropped image
        //     $this->request->session()->forget('user_profile_image_name');
        //     $this->request->session()->forget('user_profile_image');
        //     //when only info is selected
        // } else {
        //     //update category info in database
        //     DB::table('sub_cat_1')
        //         ->where('id', $id)
        //         ->update([
        //             'cat_name' => $category_name,
        //         ]);
        // }
        return redirect('admin/sub_cat_1')->with('success', 'Category updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return RedirectResponse
     */
    public function destroy($id)
    {
//        $rel_id = CategoryRelation::where('sub_cat_1_id', $id)->get()->pluck('id');
//        $exists = PartnerCategoryRelation::whereIn('cat_rel_id', $rel_id)->count();
//        if ($exists > 0) {
//            return redirect()->back()->with('try_again', 'You can not delete this category');
//        }
        CategoryRelation::where('sub_cat_1_id', $id)->update([
            'sub_cat_1_id' => null,
            'sub_cat_2_id' => null,
        ]);

        $cat = SubCat1::find($id);
        $cat->delete();

        return redirect()->back()->with('success', 'Sub cat 1 deleted successfully');
    }
}
