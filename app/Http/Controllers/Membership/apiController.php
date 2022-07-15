<?php

namespace App\Http\Controllers\Membership;

use App\Http\Controllers\Controller;
use App\Http\Controllers\LoginRegister\functionController;
use Illuminate\Http\Request;

class apiController extends Controller
{
    public function getPrices(Request $request)
    {
        $platform = $request->post('platform');
        $type = $request->post('type');

        return (new membershipPriceController())->getMembershipPrices($platform, $type);
    }
}
