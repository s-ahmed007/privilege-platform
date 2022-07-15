<?php

namespace App\Http\Controllers\Wheel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class webController extends Controller
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function calculatePrize()
    {
        $segmentNumber = 3;

        return response()->json($segmentNumber);
    }
}
