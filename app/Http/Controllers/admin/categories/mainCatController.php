<?php

namespace App\Http\Controllers\admin\categories;

use App\Categories;
use App\CategoryRelation;
use App\Http\Controllers\Controller;
use App\Http\Controllers\functionController;
use App\PartnerInfo;
use App\Rules\unique_if_changed;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class mainCatController extends Controller
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
        $categories = Categories::orderBy('priority', 'DESC')->get();

        return view('admin.production.categories.main.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.production.categories.main.create');
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
            'category_name'    => 'required|unique:categories,name',
            'category_type'    => 'required|unique:categories,type',
        ]);
        $this->request->flash('category_name', 'category_type');

        $category_name = $this->request->post('category_name');
        $category_type = $this->request->post('category_type');

        // if ($this->request->hasFile('category_banner')){
        //     $file = $this->request->file('category_banner');
        //     $banner_url = (new functionController)->uploadProPicToAWS($file, 'dynamic-images/categories');
        // }

        if (Session::has('user_profile_image_name')) {
            //just update the new image info
            Storage::disk('s3')->put('dynamic-images/categories/'.Session::get('user_profile_image_name'), Session::get('user_profile_image'), 'public');
            $image_url = Storage::disk('s3')->url('dynamic-images/categories/'.Session::get('user_profile_image_name'));

            //update category info in database
            DB::table('categories')
                ->insert([
                    'name' => $category_name,
                    'type' => $category_type,
                    'icon' => $image_url,
                    'priority' => 100,
                ]);

            //remove session of cropped image
            $this->request->session()->forget('user_profile_image_name');
            $this->request->session()->forget('user_profile_image');
        //when only info is selected
        } else {
            return redirect()->back()->with('status', 'Please select image');
        }

        return redirect('admin/main_cat')->with('success', 'Category created successfully');
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
        $category = Categories::findOrFail($id);

        return view('admin.production.categories.main.edit', compact('category'));
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
            'category_name'    => ['required', new unique_if_changed($id, 'categories', 'name', 'id', 'Category name has already been taken')],
            'category_type'    => ['required', new unique_if_changed($id, 'categories', 'type', 'id', 'Category type has already been taken')],
            'priority'         => 'required|numeric',
        ]);

        $category_name = $this->request->post('category_name');
        $category_type = $this->request->post('category_type');
        $priority = $this->request->post('priority');

        $category = Categories::find($id);

        // if ($this->request->hasFile('category_banner')){
        //     $banner_image_path = $get_current_image_name->icon;
        //     $banner_exploded_path = explode('/', $banner_image_path);
        //     if (strpos($banner_image_path, 'https://royalty-bd.s3.ap-southeast-1.amazonaws.com/') !== false) {
        //         Storage::disk('s3')->delete('dynamic-images/categories/' . end($banner_exploded_path));
        //     }
        //     $file = $this->request->file('category_banner');
        //     $banner_url = (new functionController)->uploadProPicToAWS($file, 'dynamic-images/categories');
        // }

        if (Session::has('user_profile_image_name')) {
            //get current image name
            $get_current_image_name = Categories::select('icon')->where('id', $id)->first();

            $image_path = $get_current_image_name->icon;
            $exploded_path = explode('/', $image_path);

            if (strpos($image_path, 'https://royalty-bd.s3.ap-southeast-1.amazonaws.com/') !== false) {
                //remove previous profile image from folder
                Storage::disk('s3')->delete('dynamic-images/categories/'.end($exploded_path));
                //update new image info
                Storage::disk('s3')->put('dynamic-images/categories/'.Session::get('user_profile_image_name'), Session::get('user_profile_image'), 'public');
                $image_url = Storage::disk('s3')->url('dynamic-images/categories/'.Session::get('user_profile_image_name'));
            } else {
                //just update the new image info
                Storage::disk('s3')->put('dynamic-images/categories/'.Session::get('user_profile_image_name'), Session::get('user_profile_image'), 'public');
                $image_url = Storage::disk('s3')->url('dynamic-images/categories/'.Session::get('user_profile_image_name'));
            }

            //update category info in database
            $category->name = $category_name;
            $category->type = $category_type;
            $category->icon = $image_url;
            $category->priority = $priority;
            $category->save();

            //remove session of cropped image
            $this->request->session()->forget('user_profile_image_name');
            $this->request->session()->forget('user_profile_image');
        //when only info is selected
        } else {
            //update category info in database
            $category->name = $category_name;
            $category->type = $category_type;
            $category->priority = $priority;
            $category->save();
        }

        return redirect('admin/main_cat')->with('success', 'Category updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return RedirectResponse
     */
    public function destroy($id)
    {
        $exists = PartnerInfo::where('partner_category', $id)->count();
        if ($exists > 0) {
            return redirect()->back()->with('try_again', 'You can not delete this category');
        }
        $category = Categories::findOrFail($id);
        $category->delete();

        return redirect()->back()->with('success', 'Category deleted successfully');
    }

    //function to update image url
    public function update_icon_link($id, $image_url, $table)
    {
        DB::table($table)->where('id', $id)
            ->update([
                'icon'=>$image_url,
            ]);
    }
}
