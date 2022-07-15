<?php

namespace App\Http\Controllers;

use App\AllAmounts;
use App\BranchOwner;
use App\CustomerType;
use App\PartnerAccount;
use App\PartnerBranch;
use App\PartnerProfileImage;
use App\Rating;
use App\Review;
use App\SocialId;
use App\transaction_table;
use App\TransactionTable;
use Auth;
use Datetime;
use function GuzzleHttp\Promise\all;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ViewErrorBag;
use Mail;
use View;

class loginController extends Controller
{
    //function for specific branch profile
    public function branchAccount($branch_id)
    {
        return $this->partnerLogin($branch_id);
    }

    //function for partner profile
    public function partnerProfile($username)
    {
        //Login check when one use replace his username with another user in the search bar
        if (Session::get('partner_username') == $username) {
            //nothing to do
        } else {
            return redirect('partners/'.Session::get('partner_username'));
        }

        return $this->partnerLogin(Session::get('current_branch_id'));
    }

    public function partnerLogin($branch_id)
    {
        $branch_info = PartnerBranch::where([['id', $branch_id], ['owner_id', Session::get('owner_id')]])->with(['info.profileImage'])->first();
        $name = $branch_info->info->partner_name;
        $partner_image = $branch_info->info->profileImage->partner_profile_image;

        session(['partner_profile_image' => $partner_image]);
        session(['partner_name' => $name]);
        session(['partner_email' => $branch_info->partner_email]);
        session(['partner_mobile' => $branch_info->partner_mobile]);
        session(['partner_address' => $branch_info->partner_address]);
        session(['partner_expiry_date' => $branch_info->info->expiry_date]);
        session(['partner_id' => $branch_info->partner_account_id]);
        session(['owner_id' => $branch_info->owner_id]);

        //total customer number Branch has been visited
        $customer_number = TransactionTable::where('branch_id', $branch_info->id)->groupBy('customer_id')->count();
        //total number of card used in branch
        $card_used = TransactionTable::where('branch_id', $branch_info->id)->count();
        $starAverage = Rating::where('partner_account_id', $branch_info->partner_account_id)->first();
        if ($starAverage) {
            session(['partner_average' => $starAverage->average_rating]);
        }
        //check partner validity
        $curDate = date('Y-m-d');
        $exp_date = $branch_info->info->expiry_date;
        $cur_date = new DateTime($curDate);
        $exp_date = new DateTime($exp_date);
        $interval = $cur_date->diff($exp_date);
        $yearRemaining = $interval->format('%R%y');
        $monthRemaining = $interval->format('%R%m');
        $daysRemaining = $interval->format('%R%d');
        session(['yearRemaining' => $yearRemaining]);
        session(['monthRemaining' => $monthRemaining]);
        session(['daysRemaining' => $daysRemaining]);
        //all followers info of the partner
        //        $followers_info = (new functionController)->followerListOfPartner($branch_info->partner_account_id);

        //get all notifications of this partner
        $allNotifications = (new functionController)->partnerAllNotifications($branch_info->partner_account_id);
        session(['partnerAllNotifications' => $allNotifications]);

        //get transaction history of this partner from transaction table
        $transactionHistory = (new functionController)->branchTransaction($branch_info->id);

        $all_branches = PartnerAccount::where('partner_account_id', Session::get('partner_id'))
            ->with(['branches' => function ($query) {
                $query->where('owner_id', Session::get('owner_id'));
            }])->first();
        //        $recentFollowers = (new functionController)->recentFollowers($branch_info->partner_account_id);

        //all reviews of this partner
        $reviews = (new functionController)->partnerAllReviews($branch_info->partner_account_id);
        //all posts of this partner
        $allPosts = (new functionController)->allPosts($branch_info->partner_account_id);

        //get top 5 user of this partner according to transaction
        $topUsers = (new functionController)->topUsersTransactionInBranch($branch_info->id);

        //get top 5 user of this partner according to review number
        $topReviewers = (new functionController)->topReviewers($branch_info->partner_account_id);

        //send all data to the respective account page
        $title = 'Royalty - '.$branch_info->info->partner_name;

        return view('/partnerAccount', [
            'partner_data' => $branch_info, 'card_used' => $card_used,
            'customer_number' => $customer_number, 'yearRemaining' => $yearRemaining, 'monthRemaining' => $monthRemaining,
            'daysRemaining' => $daysRemaining, 'transactionHistory' => $transactionHistory, 'reviews' => $reviews,
            'starAverage' => $starAverage, 'allPosts' => $allPosts, 'topUsers' => $topUsers, 'allBranches' => $all_branches,
            'topReviewers' => $topReviewers, 'title' => $title,
        ]);
    }

    //function to login check with facebook
    public function fbIdExistence(Request $request)
    {
        $fb_id = $request->input('fb_id');
        //check if this fb id already exist or not
        $prev_id = DB::table('social_id')
            ->where('customer_social_id', $fb_id)
            ->first();
        if (! empty($prev_id)) {
            $encrypted_id = (new functionController)->encrypt_decrypt('encrypt', $prev_id->customer_id);
            session(['social_checked' => $encrypted_id]);

            return Response::json($encrypted_id);
        } else {
            return Response::json(0);
        }
    }

    //function to login check with google
    public function googleIdExistence(Request $request)
    {
        $google_id = $request->input('google_id');
        //check if this fb id already exist or not
        $prev_id = DB::table('social_id')
            ->where('customer_social_id', $google_id)
            ->get();
        if (count($prev_id) > 0) {
            $encrypted_id = (new functionController)->encrypt_decrypt('encrypt', $prev_id[0]->customer_id);
            session(['social_checked' => $encrypted_id]);
        }

        return Response::json(count($prev_id) > 0 ? $encrypted_id : 0);
    }

    //function to login with facebook
    public function socialLogin($id)
    {
        //check if user with this username already logged in or not
        if (Session::get('social_checked') == $id) {
            //nothing to do
        } else {
            return redirect('/login');
        }

        $decrypted_id = (new functionController)->encrypt_decrypt('decrypt', $id);
        $username = DB::table('customer_account')->where('customer_id', $decrypted_id)->first();
        session(['customer_id' => $decrypted_id]);
        session(['customer_username' => $username->customer_username]);

        //redirect to userProfile function
        return \redirect('users/'.$username->customer_username);
    }

    //function for logout of partner
    public function partnerLogout(Request $request)
    {
        $request->session()->flush();
        Auth::logout();

        return redirect('/login');
    }
}
