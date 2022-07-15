<?php

namespace App\Http\Controllers\Reward;

use App\Helpers\LengthAwarePaginator;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Reward\functionController as rewardFunctionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class apiController extends Controller
{
    public function addProfileCompletingReward(Request $request)
    {
        $customer_id = JWTAuth::toUser(JWTAuth::getToken())->customer_id;

        return Response::json((new functionController())->addProfileCompletionPoint($customer_id), 200);
    }

    public function getPartnerRewards(Request $request)
    {
        $customer_id = JWTAuth::toUser(JWTAuth::getToken())->customer_id;
        $search_key = $request->post('key');
        $transacted_branches = (new functionController())->getPartnerRewards($customer_id, $search_key);

        //pagination
        // Get current page form url e.x. &page=1
        $currentPage = LengthAwarePaginator::resolveCurrentPage();

        // Create a new Laravel collection from the array data
        $itemCollection = collect($transacted_branches);

        // Define how many items we want to be visible in each page
        $perPage = 10;

        // Slice the collection to get the items to display in current page
        $currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->values();

        // Create our paginator and pass it to the view
        $paginatedItems = new LengthAwarePaginator($currentPageItems, count($itemCollection), $perPage);

        $paginatedItems->setPath('');
        $paginatedItems->setArrayName('partners');

        return Response::json($paginatedItems, 200);
    }

    public function getRoyaltyRewards()
    {
        $rewards = (new functionController())->getRoyaltyRewards();

        //pagination
        // Get current page form url e.x. &page=1
        $currentPage = LengthAwarePaginator::resolveCurrentPage();

        // Create a new Laravel collection from the array data
        $itemCollection = collect($rewards);

        // Define how many items we want to be visible in each page
        $perPage = 10;

        // Slice the collection to get the items to display in current page
        $currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->values();

        // Create our paginator and pass it to the view
        $paginatedItems = new LengthAwarePaginator($currentPageItems, count($itemCollection), $perPage);

        $paginatedItems->setPath('');
        $paginatedItems->setArrayName('rewards');

        return Response::json($paginatedItems, 200);
    }

    public function addRewardToProfile(Request $request)
    {
        $rewards = $request->post('rewards');

        $redeemed = (new functionController())->addRewardToProfile($rewards);
        if (isset($redeemed['error']) && $redeemed['error'] == true) {
            return response()->json(['message' => $redeemed['message']], 400);
        } elseif ($redeemed == null) {
            return response()->json(['message' => 'Something went wrong.'], 400);
        } else {
            return response()->json($redeemed, 200);
        }
    }

    public function getRedeemedRewards(Request $request)
    {
        $customer_id = JWTAuth::toUser(JWTAuth::getToken())->customer_id;
        $redeems = (new functionController())->getAllRedeemedRewards($customer_id);

        //pagination
        // Get current page form url e.x. &page=1
        $currentPage = LengthAwarePaginator::resolveCurrentPage();

        // Create a new Laravel collection from the array data
        $itemCollection = collect($redeems);

        // Define how many items we want to be visible in each page
        $perPage = 10;

        // Slice the collection to get the items to display in current page
        $currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->values();

        // Create our paginator and pass it to the view
        $paginatedItems = new LengthAwarePaginator($currentPageItems, count($itemCollection), $perPage);

        $paginatedItems->setPath('');
        $paginatedItems->setArrayName('redeems');

        return Response::json($paginatedItems, 200);
    }

    public function getAllPoints(Request $request)
    {
        $customer_id = JWTAuth::toUser(JWTAuth::getToken())->customer_id;
        $life_time = $request->post('life_time');
        if (! $life_time) {
            $life_time = false;
        }
        $points = (new functionController())->collectAllPoints($customer_id, $life_time);

        return Response::json($points, 200);
    }

    public function getPointHistory(Request $request)
    {
        $customer_id = JWTAuth::toUser(JWTAuth::getToken())->customer_id;
        $point_history = (new rewardFunctionController())->pointHistory($customer_id);

        return response()->json($point_history, 200);
    }

    public function getInviteFriendText(Request $request)
    {
        $customer_id = JWTAuth::toUser(JWTAuth::getToken())->customer_id;

        return response()->json(['result' => (new functionController())->inviteFriendsWithRefer($customer_id)]);
    }
}
