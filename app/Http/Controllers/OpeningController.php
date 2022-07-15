<?php

namespace App\Http\Controllers;

use App\Opening;
use Illuminate\Http\Request;

class OpeningController extends Controller
{
    public function index(Request $request)
    {
        $openings = Opening::all();

        return view('admin.production.openings.index', compact('openings'));
    }

    public function create(Request $request)
    {
        return view('admin.production.openings.create');
    }

    public function store(Request $request)
    {
        // echo "<pre>";
        // print_r($request->all());
        // echo "</pre>";

        $request->validate([
            'position' => 'required',
            'duration' => 'required',
            'salary' => 'required',
            'deadline' => 'required',
            'requirements' => 'required',
        ]);

        $opening = new Opening;
        $opening->position = $request->position;
        $opening->duration = $request->duration;
        $opening->salary = $request->salary;
        $opening->deadline = $request->deadline;
        $opening->requirements = $request->requirements;
        $opening->save();

        return redirect('/openings')->with('status', 'Opening Created Successfully!');
    }

    public function destroy($id)
    {
        $opening = Opening::findOrFail($id);
        $opening->delete();

        return redirect('/openings')->with('status', 'Opening Deleted Successfully!');
    }

    public function edit($id)
    {
        $opening = Opening::findOrFail($id);

        return view('admin.production.openings.edit', compact('opening'));
    }

    public function update($id, Request $request)
    {
        // echo "<pre>";
        // print_r($request->all());
        // echo "</pre>";

        $request->validate([
            'position' => 'required',
            'duration' => 'required',
            'salary' => 'required',
            'deadline' => 'required',
            'requirements' => 'required',
        ]);

        $opening = Opening::findOrFail($id);
        $opening->position = $request->position;
        $opening->duration = $request->duration;
        $opening->salary = $request->salary;
        $opening->deadline = $request->deadline;
        $opening->requirements = $request->requirements;
        $opening->save();

        return redirect('/openings')->with('status', 'Opening Updated Successfully!');
    }

    //Opening Active/Deactive
    public function activate_opening($id)
    {
        Opening::where('id', $id)->update(['active' => 1]);

        return redirect()->back()->with('status', 'Activated successfully');
    }

    public function deactivate_opening($id)
    {
        Opening::where('id', $id)->update(['active' => 0]);

        return redirect()->back()->with('status', 'Deactivated successfully');
    }
}
