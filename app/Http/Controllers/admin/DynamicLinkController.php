<?php

namespace App\Http\Controllers\admin;

use App\DynamicLink;
use App\Http\Controllers\Controller;
use App\Http\Controllers\functionController;
use App\Rules\unique_if_changed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DynamicLinkController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $links = DynamicLink::orderBy('created_at', 'DESC')->get();

        return view('admin.production.dynamicLink.index', compact('links'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.production.dynamicLink.create');
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
            'banner_image' => 'required',
            'tag' => 'required|unique:dynamic_links,tag',
        ]);

        $image_url = null;

        if ($request->hasFile('banner_image')) {
            $file = $request->file('banner_image');
            $image_url = (new functionController)->uploadProPicToAWS($file, 'dynamic-images/dynamic_link');
        }

        $data[] = [
            'redirect_url' => $request->input('redirect_url'),
            'image_url' => $image_url,
            'active' => $request->input('active') != null ? $request->input('active') : 0,
        ];

        $link = new DynamicLink();
        $link->values = $data;
        $link->tag = $request->input('tag');
        $link->save();

        return redirect('admin/dynamic_links')->with('status', 'New link saved successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\DynamicLink  $dynamicLink
     * @return \Illuminate\Http\Response
     */
    public function show(DynamicLink $dynamicLink)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\DynamicLink  $dynamicLink
     * @return \Illuminate\Http\Response
     */
    public function edit(DynamicLink $dynamicLink)
    {
        return view('admin.production.dynamicLink.edit', compact('dynamicLink'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\DynamicLink  $dynamicLink
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DynamicLink $dynamicLink)
    {
        $this->validate($request, [
            'tag' => 'required', new unique_if_changed($dynamicLink->id, 'dynamic_links', 'tag', 'id', 'Already assigned'
        ), ]);
        $image_url = $dynamicLink->values[0]['image_url'];

        if ($request->hasFile('banner_image')) {
            $file = $request->file('banner_image');
            $image_url = (new functionController)->uploadProPicToAWS($file, 'dynamic-images/dynamic_link');
        }

        $data[] = [
            'redirect_url' => $request->input('redirect_url'),
            'image_url' => $image_url,
            'active' => $request->input('active') != null ? $request->input('active') : 0,
        ];

        $dynamicLink->values = $data;
        $dynamicLink->tag = $request->input('tag');
        $dynamicLink->save();

        return redirect('admin/dynamic_links')->with('status', 'Link updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\DynamicLink  $dynamicLink
     * @return \Illuminate\Http\Response
     */
    public function destroy(DynamicLink $dynamicLink)
    {
        $image_path = $dynamicLink->values[0]['image_url'];
        $dynamicLink->delete();

        $image_exploded_path = explode('/', $image_path);
        if (strpos($image_path, 'https://royalty-bd.s3.ap-southeast-1.amazonaws.com/') !== false) {
            Storage::disk('s3')->delete('dynamic-images/dynamic_link/'.end($image_exploded_path));
        }

        return redirect('admin/dynamic_links')->with('delete', 'Link deleted successfully.');
    }
}
