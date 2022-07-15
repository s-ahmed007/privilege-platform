<?php

namespace App\Http\Controllers\b2b2c;

use App\Admin;
use App\AllAmounts;
use App\B2b2cInfo;
use App\B2b2cUser;
use App\CardDelivery;
use App\CustomerAccount;
use App\CustomerInfo;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Enum\AdminRole;
use App\Http\Controllers\Enum\DeliveryType;
use App\Http\Controllers\functionController;
use App\Http\Controllers\jsonController;
use App\Http\Controllers\JsonControllerV2;
use App\RoyaltyLogEvents;
use App\Rules\unique_if_changed;
use App\Subscribers;
use App\TransactionTable;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class b2b2cAdminController extends Controller
{
    //function for login of admin
    public function login(Request $request)
    {
        $username = $request->get('username');
        $password = $request->get('password');

        $admin_info = Admin::where([['username', '=', $username], ['type', '=', 'b2b2c']])->first();

        if ($admin_info) {
            $decrypted_password = (new functionController)->encrypt_decrypt('decrypt', $admin_info->password);
            if ($password == $decrypted_password) {
                session(['client-admin' => AdminRole::clientAdmin]);
                session(['client-admin-id' => $admin_info->b2b2c_id]);

                return redirect('/client/dashboard');
            } else {
                return redirect('client/adminDashboard')->with('wrong info', 'Login credential invalid.');
            }
        } else {
            return redirect('client/adminDashboard')->with('wrong info', 'Login credential invalid.');
        }
    }

    //function for client admin dashboard
    public function dashboard()
    {
        $users = B2b2cUser::where('b2b2c_id', Session::get('client-admin-id'))->get();
        $client = B2b2cInfo::where('id', Session::get('client-admin-id'))->first();
        session(['client-admin-image' => $client->image]);
        session(['client-admin-name' => $client->name]);

        return view('b2b2c.index', compact('users'));
    }

    //function get customer list
    public function allCustomers()
    {
        $customers = B2b2cUser::where('b2b2c_id', Session::get('client-admin-id'))->with('customerInfo')
            ->orderBy('id', 'DESC')->paginate(20);

        $current_page = $customers->currentPage();
        $per_page = $customers->perPage();
        $i = ($current_page * $per_page) - $per_page + 1;
        foreach ($customers as $list) {
            $list->serial = $i;
            $i++;
        }

        return view('b2b2c.allCustomers', compact('customers'));
    }

    //function to card delivery customer list
    public function cardDeliveryList()
    {
        $card_delivery_list = DB::table('customer_info as ci')
            ->join('b2b2c_user as bu', 'bu.customer_id', '=', 'ci.customer_id')
            ->leftjoin('card_delivery as cd', 'cd.customer_id', '=', 'ci.customer_id')
            ->select('ci.customer_full_name', 'ci.customer_id', 'ci.delivery_status',
                'ci.customer_contact_number', 'ci.customer_email', 'ci.customer_type', 'ci.member_since', 'cd.*')
            ->orderBy('cd.id', 'DESC')
            ->where('bu.b2b2c_id', Session::get('client-admin-id'))
            ->where('cd.delivery_type', '=', DeliveryType::b2b2c_user)
            ->get();

        return view('b2b2c.card-delivery-list', compact('card_delivery_list'));
    }

    //function to card delivery customer list
    public function allTransactions()
    {
        $allTransactions = TransactionTable::with(['b2b2cUser'=> function ($query) {
            $query->where('b2b2c_id', '=', Session::get('client-admin-id'));
        }])->get();
        foreach ($allTransactions as $key => $value) {
            if ($value->b2b2cUser == '') {
                unset($allTransactions[$key]);
            }
        }

        return view('b2b2c.allTransactions', compact('allTransactions'));
    }

    //function to add customer
    public function addCustomer()
    {
        return view('b2b2c.add_customer');
    }

    //function to store customer info
    public function storeCustomer(Request $request)
    {
        $this->validate($request, [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:customer_info,customer_email',
            'phone' => 'required|unique:customer_info,customer_contact_number',
            'password' => 'required|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
            'customer_id' => 'required|unique:customer_account,customer_id',
            'shipping_address' => 'required',
        ]);
        $request->flashOnly('first_name', 'last_name', 'email', 'phone', 'password', 'customer_id', 'shipping_address');

        $first_name = $request->get('first_name');
        $last_name = $request->get('last_name');
        $email = $request->get('email');
        $phone = $request->get('phone');
        $password = $request->get('password');
        $customer_id = $request->get('customer_id');
        $customer_id_6 = substr($customer_id, -6);
        $shipping_address = $request->get('shipping_address');
        // make password encrypted
        $password = preg_replace('/\s+/', '', $password);
        $encrypted_password = (new functionController)->encrypt_decrypt('encrypt', $password);
        $image_url = 'https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/registration/user.png';

        //generate referral number
        A://come back to regenerate refer code again if exists
        $token = '';
        $codeAlphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $codeAlphabet .= 'abcdefghijklmnopqrstuvwxyz';
        $codeAlphabet .= '0123456789';
        $max = strlen($codeAlphabet); // edited
        for ($i = 0; $i < 5; $i++) {
            $token .= $codeAlphabet[random_int(0, $max - 1)];
        }
        $refer_exists = DB::table('customer_info')->where('referral_number', $token)->count();
        //regenerate refer number if already exists
        if ($refer_exists > 0) {
            goto A;
        }
        try {
            DB::beginTransaction(); //to do query rollback

            if ((new jsonController)->usernameExist((new JsonControllerV2)->getUsernameFromEmail($email))) {
                B:
                $username = (new JsonControllerV2)->randomUsername($first_name);
                if ((new jsonController)->usernameExist($username)) {
                    goto B;
                }
            } else {
                $username = (new JsonControllerV2)->getUsernameFromEmail($email);
            }

            //save data in customer_account table
            DB::table('customer_account')->insert([
                'customer_id' => $customer_id,
                'customer_serial_id' => $customer_id_6,
                'customer_username' => $username,
                'password' => $encrypted_password,
                'moderator_status' => 2,
            ]);
            //save data in customer_info table
            DB::table('customer_info')->insert([
                'customer_id' => $customer_id,
                'customer_first_name' => $first_name,
                'customer_last_name' => $last_name,
                'customer_full_name' => $first_name.' '.$last_name,
                'customer_email' => $email,
                'customer_contact_number' => $phone,
                'customer_profile_image' => $image_url,
                'customer_type' => 2,
                'month' => 0,
                'expiry_date' => '1971-03-26',
                'member_since' => date('Y-m-d'),
                'referral_number' => $token,
                'firebase_token' => 0,
                'card_active' => 1,
                'card_activation_code' => 0,
                'delivery_status' => 1,
            ]);
            //save email in subscribers table
            $subscribers = Subscribers::firstOrNew(['email' => $email]);
            $subscribers->save();

            //insert info into card delivery table
            $card_delivery = new CardDelivery();
            $card_delivery->customer_id = $customer_id;
            $card_delivery->delivery_type = DeliveryType::b2b2c_user;
            $card_delivery->shipping_address = $shipping_address;
            $card_delivery->order_date = date('Y-m-d');
            $card_delivery->save();

            //enlist this customer to b2b2c user list
            $b2b2c_user = new B2b2cUser();
            $b2b2c_user->b2b2c_id = Session::get('client-admin-id');
            $b2b2c_user->customer_id = $customer_id;
            $b2b2c_user->save();

            DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);

            return view('errors.404');
        }

        return redirect('client/customers')->with('status', 'Customer added successfully');
    }

    //function to edit customer
    public function editCustomer($customer_id)
    {
        $customer_info = B2b2cUser::where('customer_id', $customer_id)->with('customerInfo.cardDelivery')->first();

        if ($customer_info) {
            return view('b2b2c.editCustomer', compact('customer_info'));
        } else {
            return redirect('page-not-found');
        }
    }

    //function to update customer info
    public function updateCustomer(Request $request, $customer_id)
    {
        $this->validate($request, [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => ['required', 'email', new unique_if_changed($customer_id, 'customer_info', 'customer_email', 'customer_id', 'Email has already been taken')],
            'phone' => ['required', new unique_if_changed($customer_id, 'customer_info', 'customer_contact_number', 'customer_id', 'Phone has already been taken')],
            'customer_id' => ['required', new unique_if_changed($customer_id, 'customer_account', 'customer_id', 'customer_id', 'This card has already been assigned to someone')],
            'shipping_address' => 'required',
        ]);
        $request->flashOnly('first_name', 'last_name', 'email', 'phone', 'password', 'customer_id', 'shipping_address');

        $first_name = $request->get('first_name');
        $last_name = $request->get('last_name');
        $email = $request->get('email');
        $phone = $request->get('phone');
        $id = $request->get('customer_id');
        $customer_id_6 = substr($id, -6);
        $shipping_address = $request->get('shipping_address');

        try {
            DB::beginTransaction(); //to do query rollback

            $prev_email = CustomerInfo::where('customer_id', $customer_id)->select('customer_email')->first();
            //save data in customer_info table
            DB::table('customer_info')->where('customer_id', $customer_id)->update([
                'customer_id' => $id,
                'customer_first_name' => $first_name,
                'customer_last_name' => $last_name,
                'customer_full_name' => $first_name.' '.$last_name,
                'customer_email' => $email,
                'customer_contact_number' => $phone,
            ]);
            if ($prev_email != $email) {
                Subscribers::where('email', $prev_email)->delete();
                //update email_verified status of customer
                DB::table('customer_info')->where('customer_id', $customer_id)
                    ->update(['email_verified' => 0]);
            }

            //TO Update all other customer tables
            (new functionController)->updateCustomerId($customer_id, $id, 1);

            DB::table('customer_account')->where('customer_id', $customer_id)
                ->update(['customer_serial_id' => $customer_id_6]);
            DB::table('card_delivery')->where('customer_id', $customer_id)
                ->update(['shipping_address' => $shipping_address]);

            DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);

            return view('errors.404');
        }

        return redirect('client/customers')->with('status', 'Information updated successfully');
    }

    //function for logout of admin
    public function logout(Request $request)
    {
        $request->session()->flush();
        Auth::logout();

        return redirect('client/adminDashboard');
    }
}
