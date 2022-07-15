<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//if anytime needed to update user info from ssl response
//Route::get('update-info-from-SSL-response', 'homeController@updateInfoFromSSLResponse');

//===========================================================================================
//========================== route to know partner branch password ==========================
//===========================================================================================
// Route::get('partner-pass-1131/{username}', 'homeController@partnerPass');
// Route::get('partner-branch-pass-1131/{username}', 'homeController@partnerBranchPass');
// Route::get('user-pin-3524/{username}', 'homeController@UserPin');

//this is for one time use
//Route::get('upgrade_customers', 'Renew\apiController@upgradeCustomers');

//===========================================================================================
//================================= test routes =============================================
//===========================================================================================

//test route to test resize image & upload
Route::post('resize-image', 'adminController@resizeImage');
//test route to get browser info
Route::get('browser', 'homeController@show');

// Route::get('dealrefund', function (){
// 	$voucher = \App\BranchVoucher::find(1);
// 	$data['heading'] = $voucher->heading;
//     $data['price'] = intval($voucher->selling_price);
//         // dd($data);
//     return view('emails.dealrefund', compact('data'));
// });
// Route::get('dealpurchase', function (){
//     $ssl_info = \App\VoucherSslInfo::where('id', 25)->first();
//     $history = \App\VoucherHistory::where('ssl_id', 25)->first();
//     $order['date'] = date('F d, Y', strtotime($history->created_at));
//     $order['order_id'] = $history->order_id;
//     $order['partner_name'] = $history->branch->info->partner_name;
//     $order['partner_area'] = $history->branch->partner_area;
//     $order['amount'] = $ssl_info->amount;
//     $order['credit_used'] = $ssl_info->credit;
//     $voucher_details = \App\VoucherPurchaseDetails::where('ssl_id', 25)->with('voucher')->get();
//     $voucher_details = $voucher_details->groupBy('voucher_id');
//     $vouchers = [];
//     $i=0;
//     foreach ($voucher_details as $key => $value) {
//         if ($value[0]->voucher->redeem_duration) {
//             $exp_date = date('d-m-Y', strtotime($value[0]->created_at. ' + '.$value[0]->voucher->redeem_duration.' days'));
//         }else{
//             $exp_date = $value[0]->voucher->date_duration[0]['to'];
//         }
//         $vouchers[$i]['heading'] = $value[0]->voucher->heading;
//         $vouchers[$i]['quantity'] = count($value);
//         $vouchers[$i]['price'] = intval($value[0]->voucher->selling_price * count($value));
//         $vouchers[$i]['exp_date'] = date('F d, Y', strtotime($exp_date));
//         $i++;
//     }
//     $data['order'] = $order;
//     $data['deals'] = $vouchers;
// // dd($data);
//     $subject = 'Thank you for purchasing deal';

//     return view('emails.dealpurchase', compact('data', 'subject'));
// });
