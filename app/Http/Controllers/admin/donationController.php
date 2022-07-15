<?php

namespace App\Http\Controllers\admin;

use App\Donation;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class donationController extends Controller
{
    public function allDonations()
    {
        $donations = Donation::where('status', 1)->orderBy('id', 'DESC')->get();

        return view('admin.production.donation.index', compact('donations'));
    }
}
