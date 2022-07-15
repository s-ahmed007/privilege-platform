<?php

namespace App\Http\Controllers;

use App\BlogCategory;
use App\BlogPost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class blogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        $allblogs = BlogPost::orderBy('priority', 'DESC')->orderBy('posted_on', 'DESC')->with('BlogCategory')->get();
        $all_categories = BlogCategory::all();

        return view('admin.production.blogs.index', compact('allblogs', 'all_categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create()
    {
        $all_categories = BlogCategory::all();

        return view('admin.production.blogs.create', compact('all_categories'));
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
            'blogCategory' => 'required',
            'blogHeader' => 'required',
            'blogBody' => 'required',
            'blogImage' => 'required',
        ]);

        $category = $request->get('blogCategory');
        $heading = $request->get('blogHeader');
        $details = $request->get('blogBody');
        $file = $request->file('blogImage');
        $priority = $request->get('priority');

        //image is being resized & uploaded here
        $image_url = (new functionController)->uploadImageToAWS($file, 'dynamic-images/rbd-blog');

        $time = date('Y-m-d H:i:s');

        $blog = new BlogPost();
        $blog->image_url = $image_url;
        $blog->details = $details;
        $blog->heading = $heading;
        $blog->category_id = $category;
        $blog->active_status = 1;
        $blog->posted_on = $time;
        $blog->priority = (int) $priority;
        $blog->save();

        return redirect('admin/blog-post')->with('status', 'Post Created Successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //dd('show');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return View
     */
    public function edit($id)
    {
        $blog = BlogPost::findOrFail($id);
        $all_categories = BlogCategory::all();

        return view('admin.production.blogs.edit', compact('blog', 'all_categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'blogCategory' => 'required',
            'blogHeader' => 'required',
            'blogBody' => 'required',
            'blogPostedOn' => 'required',
        ]);
        $request->flashOnly(['blogCategory', 'blogHeader', 'blogBody', 'blogPostedOn']);

        $category = $request->get('blogCategory');
        $heading = $request->get('blogHeader');
        $details = $request->get('blogBody');
        $time = date('Y-m-d H:i:s', strtotime($request->get('blogPostedOn')));
        $priority = $request->get('priority');

        //get the updating instance
        $blog = BlogPost::findOrFail($id);

        //upload image to aws & save url to DB
        if ($request->hasFile('blogImage')) {
            $file = $request->file('blogImage');
            //at first delete the previous image
            $image_path = $blog->image_url;
            $exploded_path = explode('/', $image_path);
            //remove previous profile image from bucket
            Storage::disk('s3')->delete('dynamic-images/rbd-blog/'.end($exploded_path));

            //image is being resized & uploaded here
            $image_url = (new functionController)->uploadImageToAWS($file, 'dynamic-images/rbd-blog');

            $blog->image_url = $image_url;
            $blog->details = $details;
            $blog->heading = $heading;
            $blog->category_id = $category;
            $blog->active_status = 1;
            $blog->posted_on = $time;
            $blog->priority = $priority;
            $blog->save();
        } else {
            $blog->details = $details;
            $blog->heading = $heading;
            $blog->category_id = $category;
            $blog->posted_on = $time;
            $blog->priority = $priority;
            $blog->save();
        }

        return redirect('admin/blog-post')->with('status', 'Post Updated Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return RedirectResponse
     */
    public function destroy($id)
    {
        $blog = BlogPost::findOrFail($id);
        $image_path = $blog->image_url;
        //at first delete the previous image
        $exploded_path = explode('/', $image_path);
        //remove previous profile image from bucket
        Storage::disk('s3')->delete('dynamic-images/admin_post_image/'.end($exploded_path));
        $blog->delete();

        return redirect('admin/blog-post')->with('status', 'Post Deleted Successfully!');
    }

    //function to store new blog category
    public function addBlogCategory(Request $request)
    {
        $this->validate($request, [
           'category_name' => 'required|unique:blog_category,category',
        ]);
        $request->flashOnly(['category_name']);
        $category_name = $request->get('category_name');
        $BlogCategory = new BlogCategory();
        $BlogCategory->category = $category_name;
        $BlogCategory->save();

        return redirect('admin/blog-post')->with('status', 'Category added successfully');
    }

    //function to update blog category
    public function updateBlogCategory(Request $request)
    {
        $category_id = $request->input('category_id');
        $category_name = $request->input('category_name');
        $category = BlogCategory::findOrFail($category_id);
        $category->category = $category_name;
        $category->save();

        return Response::json(['result' => true, 'category_id' => $category_id]);
    }

    //function to delete blog category
    public function deleteBlogCategory(Request $request)
    {
        $category_id = $request->input('category_id');
        $post_exists = BlogPost::where('category_id', $category_id)->count();
        $result = false;
        if ($post_exists == 0) {
            $result = BlogCategory::findOrFail($category_id)->delete();
        }

        return Response::json(['result' => $result, 'category_id' => $category_id]);
    }

    //function to update blog status
    public function updateBlogStatus($id, $status)
    {
        BlogPost::where('id', $id)->update(['active_status'=> $status]);

        return redirect()->back()->with('status', 'Status updated successfully');
    }
}
