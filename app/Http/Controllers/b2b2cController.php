<?php

namespace App\Http\Controllers;

use App\Admin;
use App\B2b2cInfo;
use App\BlogPost;
use App\Post;
use App\Rules\unique_if_changed;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class b2b2cController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $allClients = B2b2cInfo::orderBy('id', 'DESC')->get();

        return view('admin.production.b2b2cClients.index', compact('allClients'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.production.b2b2cClients.create');
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
            'name' => 'required',
            'phone' => 'required|unique:b2b2c_info,phone',
            'email' => 'required|unique:b2b2c_info,email',
            'username' => 'required|unique:admin,username',
            'password' => 'required',
        ]);
        $request->flashOnly(['name', 'phone', 'email', 'username', 'password']);

        $name = $request->get('name');
        $phone = $request->get('phone');
        $email = $request->get('email');
        $username = $request->get('username');
        $password = $request->get('password');
        $file = $request->file('image');
        $password = preg_replace('/\s+/', '', $password);
        $encrypted_password = (new functionController)->encrypt_decrypt('encrypt', $password);

        //image is being resized & uploaded here
        $image_url = (new functionController)->uploadImageToAWS($file, 'dynamic-images/b2b2c-client');

        try {
            DB::beginTransaction(); //to do query rollback

            $allClient = new B2b2cInfo();
            $allClient->name = $name;
            $allClient->phone = $phone;
            $allClient->email = $email;
            $allClient->image = $image_url;
            $allClient->save();

            $adminInfo = new Admin();
            $adminInfo->username = $username;
            $adminInfo->password = $encrypted_password;
            $adminInfo->type = 'b2b2c';
            $adminInfo->b2b2c_id = $allClient->id;
            $adminInfo->save();

            DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            DB::rollBack(); //rollback all successfully executed queries
            return redirect()->back()->with('try_again', 'Please try again!');
        }

        return redirect('admin/b2b2c-clients/')->with('status', 'Client Created Successfully!');
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
     * @return \Illuminate\Http\Response
     */
    public function editClient($id)
    {
        $client_info = DB::table('b2b2c_info as bi')
            ->join('admin as ad', 'ad.b2b2c_id', '=', 'bi.id')
            ->where('bi.id', $id)
            ->where('ad.type', 'b2b2c')
            ->first();

        return view('admin.production.b2b2cClients.edit', compact('client_info'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateClientInfo(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'phone' => ['required', new unique_if_changed($id, 'b2b2c_info', 'phone', 'id', 'phone has already been taken')],
            'email' => ['required', new unique_if_changed($id, 'b2b2c_info', 'email', 'id', 'email has already been taken')],
            'username' => ['required', new unique_if_changed($id, 'admin', 'username', 'b2b2c_id', 'username has already been taken')],
            'password' => 'required',
        ]);
        $request->flashOnly(['name', 'phone', 'email', 'username', 'password']);

        $name = $request->get('name');
        $phone = $request->get('phone');
        $email = $request->get('email');
        $username = $request->get('username');
        $password = $request->get('password');
        $file = $request->file('image');
        $password = preg_replace('/\s+/', '', $password);
        $encrypted_password = (new functionController)->encrypt_decrypt('encrypt', $password);

        try {
            DB::beginTransaction(); //to do query rollback

            //upload image to aws & save url to DB
            if ($request->hasFile('image')) {
                //get the updating instance
                $b2b2c_info = B2b2cInfo::findOrFail($id);

                $file = $request->file('image');
                //at first delete the previous image
                $image_path = $b2b2c_info->image;
                $exploded_path = explode('/', $image_path);
                //remove previous profile image from bucket
                Storage::disk('s3')->delete('dynamic-images/b2b2c-client/'.end($exploded_path));

                //image is being resized & uploaded here
                $image_url = (new functionController)->uploadImageToAWS($file, 'dynamic-images/b2b2c-client');

                B2b2cInfo::where('id', $id)->update([
                    'name' => $name,
                    'phone' => $phone,
                    'email' => $email,
                    'image' => $image_url,
                ]);
            } else {
                B2b2cInfo::where('id', $id)->update([
                    'name' => $name,
                    'phone' => $phone,
                    'email' => $email,
                ]);
            }

            Admin::where('b2b2c_id', $id)->update([
                'username' => $username,
                'password' => $encrypted_password,
            ]);

            DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            DB::rollBack(); //rollback all successfully executed queries
            return redirect()->back()->with('try_again', 'Please try again!');
        }

        return redirect('admin/b2b2c-clients/')->with('status', 'Client Updated Successfully!');
    }

    //function to delete b2b2c client
    public function deleteClient($user_id)
    {
        try {
            DB::beginTransaction(); //to do query rollback

            $user = B2b2cInfo::find($user_id);
            $user->delete();

            DB::table('admin')
                ->where('type', 'b2b2c')
                ->where('b2b2c_id', $user_id)
                ->delete();

            DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            DB::rollBack(); //rollback all successfully executed queries
            return redirect()->back()->with('try_again', 'Please try again!');
        }

        return Redirect('admin/b2b2c-clients/')->with('user_deleted', 'One Seller deleted');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
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

        return redirect('admin/b2b2c-clients/')->with('status', 'Post Deleted Successfully!');
    }
}
