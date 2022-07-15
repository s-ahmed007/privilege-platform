<?php

namespace App\Http\Controllers\Reward;

use App\BranchOffers;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Reward\functionController as rewardFunctionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use function Sodium\add;

class webController extends Controller
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function rewardDetails()
    {
        $reward_id = $this->request->post('id');

        $reward = BranchOffers::findOrFail($reward_id);

        $result = [];
        if ($reward) {
            $result['reward'] = $reward;
        } else {
            $result['error'] = 'Reward not found';
        }

        return Response::json($result);
    }

    public function rewardRedeemConfirm()
    {
        $reward_details = $this->request->post('data');
        foreach ($reward_details as $key => $value) {
            $reward_details[$key]['customer_id'] = session('customer_id');
        }
        $reward = (new rewardFunctionController())->addRewardToProfile($reward_details);
        if (isset($reward['error']) && $reward['error'] == true) {
            return response()->json($reward['message'], 403);
        } elseif ($reward == null) {
            return response()->json('Something went wrong', 403);
        } else {
            return response()->json(session('customer_username'), 200);
        }
    }
}
