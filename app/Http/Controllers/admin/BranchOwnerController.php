<?php

namespace App\Http\Controllers\admin;

use App\BranchOwner;
use App\Http\Controllers\Controller;
use App\Http\Controllers\functionController;
use App\PartnerBranch;
use App\Rules\unique_if_changed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class BranchOwnerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $owners = BranchOwner::with('branches.info')->get();

        return view('admin.production.branchOwner.index', compact('owners'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.production.branchOwner.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'phone' => 'required|numeric',
            'username' => 'required|unique:branch_owner,username',
            'password' => 'required',
        ]);
        $request->flashOnly(['name', 'phone', 'username', 'password']);

        $name = $request->get('name');
        $phone = $request->get('phone');
        $username = $request->get('username');
        $password = $request->get('password');
        $password = (new functionController)->encrypt_decrypt('encrypt', $password);

        try {
            DB::beginTransaction(); //to do query rollback

            BranchOwner::insert([
                'name' => $name,
                'phone' => $phone,
                'username' => $username,
                'password' => $password,
                'active' => 1,
            ]);

            DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            DB::rollBack(); //rollback all successfully executed queries
            return redirect()->back()->with('try_again', 'Please try again!');
        }

        return redirect('/branch-owner/')->with('status', 'New owner added!');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        dd('show');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $owner = BranchOwner::findOrFail($id);

        $password = (new functionController)->encrypt_decrypt('decrypt', $owner->password);
        $owner->password = $password;

        return view('admin.production.branchOwner.edit', compact('owner'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'phone' => 'required|numeric',
            'username' => ['required', new unique_if_changed(
                $id,
                'branch_owner',
                'username',
                'id',
                'Username has already been taken'
            )],
            'password' => 'required',
        ]);
        $request->flashOnly(['name', 'phone', 'username', 'password']);

        $name = $request->get('name');
        $phone = $request->get('phone');
        $username = $request->get('username');
        $password = $request->get('password');
        $password = (new functionController)->encrypt_decrypt('encrypt', $password);

        try {
            DB::beginTransaction(); //to do query rollback

            BranchOwner::where('id', $id)->update([
                'name' => $name,
                'phone' => $phone,
                'username' => $username,
                'password' => $password,
            ]);

            DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            DB::rollBack(); //rollback all successfully executed queries
            return redirect()->back()->with('try_again', 'Please try again!');
        }

        return redirect('/branch-owner/')->with('status', 'Owner updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $owner = BranchOwner::find($id);
        $owner->delete();

        return redirect()->back()->with('status', 'One owner deleted!');
    }

    //get branch owner info
    public function getBranchOwner(Request $request)
    {
        $branch_id = $request->input('branch_id');
        $owner = PartnerBranch::where('id', $branch_id)->with('owner')->first();

        return Response::json($owner);
    }

    //function to assign owner to branch
    public function assignOwner(Request $request)
    {
        $owner_id = $request->input('owner_id');
        $branch_id = $request->input('branch_id');

        try {
            DB::beginTransaction(); //to do query rollback

            PartnerBranch::where('id', $branch_id)->update([
                'owner_id' => $owner_id,
            ]);

            DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            DB::rollBack(); //rollback all successfully executed queries
            return Response::json(0);
        }

        return Response::json(1);
    }

    public function branches($id)
    {
        $branches = PartnerBranch::where('owner_id', $id)->with('info', 'owner')->get();

        return view('admin.production.branchOwner.branches', compact('branches'));
    }
}
