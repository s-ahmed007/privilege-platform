<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
//===========================================================================================
//================================= Royalty Bangladesh App API ===========================================
//===========================================================================================
//version 3
Route::post('v3/customer', 'LoginRegister\apiController@getCustomer');
Route::post('v3/auth/check', 'LoginRegister\apiController@matchCredential');
Route::post('v3/customer/create', 'LoginRegister\apiController@createCustomer');
Route::post('v3/stats/account_kit/create', 'LoginRegister\apiController@createStats');
Route::post('v3/stats/account_kit/update', 'LoginRegister\apiController@updateStats');
//OTP verifications
Route::post('v3/customer/verify/email/send', 'OTP\apiController@sendMailVerification');
Route::post('v3/customer/verify/phone/send', 'OTP\apiController@sendPhoneVerification');
Route::post('v3/customer/verify/email/check', 'OTP\apiController@verifyMailOTP');
Route::post('v3/customer/verify/phone/check', 'OTP\apiController@verifyPhoneOTP');

Route::post('v3/reset/pin', 'LoginRegister\apiController@resetPinEmail');
Route::post('v3/reset/phone/pin', 'LoginRegister\apiController@resetPinPhone');
Route::post('v3/reset/phone/check-otp', 'LoginRegister\apiController@checkPinResetOTP');
Route::post('v3/reset-pin', 'LoginRegister\apiController@storeNewPin');

Route::group(['middleware' => ['IsUserLoggedIn']], function () {
    Route::post('app/checkReferralCode.json', 'JsonControllerV2@checkReferralCodes');
    Route::post('app/customer_notification', 'JsonControllerV2@customerNotifications');
    Route::post('notifications/mark/all', 'JsonControllerV3@markAllNotification');
    Route::post('app/customer_nearby', 'JsonControllerV2@partnersNearCustomer');
    Route::post('app/customer_sorted_transaction', 'JsonControllerV2@getUserSortedTransaction');
    Route::post('app/log_events', 'JsonControllerV2@logEvents');
    Route::post('app/get_branch_offers', 'JsonControllerV2@getBranchOffers');
    Route::post('app/recentlyViewed', 'JsonControllerV2@recentlyViewed');
    Route::post('app/getCardDetails', 'JsonControllerV2@getCardDetailList');
    Route::post('app/getReviewLikes', 'JsonControllerV2@getReviewLikeList');
    Route::post('app/getPostLikes', 'JsonControllerV2@getPostLikeList');
    Route::get('app/minCardValue', 'JsonControllerV2@getMinCardValue');

    Route::post('app/scan_partner', 'JsonControllerV2@getBranchByScan');
    Route::post('app/check_branch_user', 'JsonControllerV2@checkBranchUser');
    Route::post('app/confirm_offer_transaction', 'JsonControllerV2@confirmOfferTransaction');
    Route::post('app/incluencerUsage', 'JsonControllerV2@influencerUsage');
    Route::post('app/getPincode', 'JsonControllerV2@getPin');
    Route::post('app/setPincode', 'JsonControllerV2@setPin');
    Route::post('renew/insert', 'Renew\apiController@insertSSLInfo');
    Route::post('renew/success', 'Renew\apiController@renewSuccess');
    Route::post('virtual/user/create', 'Renew\apiController@makeVirtualTrialUser');

    //rewards
    Route::post('user/points/royalty', 'JsonControllerV3@getRoyaltyPoints');
    Route::post('user/points', 'Reward\apiController@getAllPoints');

    Route::post('rewards/branches', 'Reward\apiController@getPartnerRewards');
    Route::get('rewards/royalty', 'Reward\apiController@getRoyaltyRewards');

    Route::post('rewards/add', 'Reward\apiController@addRewardToProfile');
    Route::post('rewards/redeemed', 'Reward\apiController@getRedeemedRewards');
    Route::post('rewards/point_history', 'Reward\apiController@getPointHistory');
    Route::post('rewards/add/profile_point', 'Reward\apiController@addProfileCompletingReward');

    //invite friends for reward points
    Route::post('invite/friend', 'Reward\apiController@getInviteFriendText');

    //version 3

    Route::post('v3/pin', 'LoginRegister\apiController@getPin');
    Route::post('v3/pin/update', 'LoginRegister\apiController@setPin');
    Route::post('v3/transaction/request', 'JsonControllerV3@createTransactionRequest');
    Route::post('v3/checkScannerPin', 'JsonControllerV3@checkBranchUserPin');
    Route::post('v3/offer_transaction', 'JsonControllerV3@confirmOfferTransaction');

    //Membership prices
    Route::post('v3/membership/prices', 'Membership\apiController@getPrices');

    //categories
    Route::get('categories', 'Categories\apiController@categories');
    Route::post('subcategories', 'Categories\apiController@subCategories');
    Route::post('subcategories/2', 'Categories\apiController@secondSubCategories');
    Route::post('category/partners', 'Categories\apiController@main_cat_partners');
    Route::post('subcategory/partners', 'Categories\apiController@sub_cat_partners');
    Route::post('subcategory/2/partners', 'Categories\apiController@second_sub_cat_partners');

    //login sessions
    Route::post('session/login/create', 'LoginSession\apiController@createSession');
    Route::post('session/login/check', 'LoginSession\apiController@checkSession');

    //Log events
    Route::post('log', 'JsonControllerV3@getAndroidReport');

    //Statistics
    Route::post('stat/search', 'JsonControllerV3@createSearchStat');

    //vouchers
    Route::post('vouchers', 'Voucher\apiController@getAllVouchers');
    Route::post('branch-vouchers', 'Voucher\apiController@getBranchVouchers');
    Route::post('sort-vouchers', 'Voucher\apiController@sortVouchers');
    Route::post('voucher/insert', 'Voucher\apiController@insertSSLInfo');
    Route::post('voucher/success', 'Voucher\apiController@voucherPurchaseSuccess');
    Route::post('purchased_vouchers', 'Voucher\apiController@purchasedVouchers');
    Route::post('avail_voucher', 'Voucher\apiController@availVoucher');
    Route::post('user/voucher_refund_request', 'Voucher\apiController@voucherRefundRequest');
    Route::post('voucher/purchase_details', 'Voucher\apiController@purchaseDetails');

    //dynamic links
    Route::get('homepage_link', 'JsonControllerV3@homepageLink');

});

Route::post('voucher/payment_history', 'Voucher\apiController@paymentHistory');

//===========================================================================================
//================================= Royalty Bangladesh App API ===========================================
//===========================================================================================

//===========================================================================================
//================================= Royalty Bangladesh Merchant API ===========================================
//===========================================================================================
Route::post('branch_user_login', 'JsonBranchUserController@authenticate');
Route::get('point_prizes', 'JsonBranchUserController@pointPrizes');
Route::get('leaderboard', 'JsonBranchUserController@getLeaderBoard');
Route::get('check_m_version', 'JsonBranchUserController@merchantVersionControl');
Route::post('check_jwt', 'JsonBranchUserController@manageJWTCredential');
Route::group(['middleware' => ['auth:api']], function () {
    Route::get('branch_user', 'JsonBranchUserController@getAuthenticatedUser');
    Route::post('update_f_token', 'JsonBranchUserController@setFirebaseToken');
    Route::post('check_customer', 'JsonBranchUserController@checkCustomer');
    Route::post('calculate_bill', 'JsonBranchUserController@calculateBill');
    Route::post('request_scan_prize', 'JsonBranchUserController@requestScannerPrize');
    Route::get('remove_f_token', 'JsonBranchUserController@removeFirebaseToken');
    Route::get('scanner_prize_history', 'JsonBranchUserController@scannerPrizeRequestHistory');
    Route::get('partner_transaction_history', 'JsonBranchUserController@getPartnerTransactionHistory');
    Route::post('sort_partner_transaction_history', 'JsonBranchUserController@sortPartnerTransactionHistory');
    Route::post('bonus_coupon_transaction', 'JsonBranchUserController@confirmCouponTransaction');
    Route::post('offer_transaction', 'JsonBranchUserController@confirmOfferTransaction');
    Route::get('coupon_payment_stats', 'JsonBranchUserController@rbdCouponPayment');
    Route::get('scan_offers', 'JsonBranchUserController@getBranchOffers');
    Route::get('branch/rewards', 'JsonBranchUserController@getAllRewards');
    Route::get('payment/info', 'JsonBranchUserController@getPaymentInfo');
    Route::get('notifications', 'JsonBranchUserController@getNotificationList');
    Route::post('notification/seen', 'JsonBranchUserController@setSeenNotification');
    Route::get('notification/count', 'JsonBranchUserController@getNotificationCount');
    Route::post('transaction/request/update', 'JsonBranchUserController@transactionRequestUpdate');
    Route::get('merchant/metrics', 'JsonBranchUserController@getDashboardMetrics');
    Route::get('merchant/top_transactor', 'JsonBranchUserController@getTopTransactor');
    //review
    Route::get('merchant/reviews', 'JsonBranchUserController@getReviews');
    Route::post('merchant/review/single', 'JsonBranchUserController@getReview');
    Route::post('merchant/review/reply', 'JsonBranchUserController@replyReview');
    Route::post('merchant/review/reply/delete', 'JsonBranchUserController@deleteReplyReview');
    Route::post('merchant/review/reply/edit', 'JsonBranchUserController@editReplyReview');
    Route::post('merchant/review/like', 'JsonBranchUserController@likeReview');
    Route::post('merchant/review/unlike', 'JsonBranchUserController@unlikeReview');
    //post
    Route::get('merchant/posts', 'Newsfeed\merchantApiController@getAllPosts');
    Route::post('merchant/post/single', 'Newsfeed\merchantApiController@getPost');
    Route::post('merchant/post', 'Newsfeed\merchantApiController@addPost');
    Route::post('merchant/post/edit', 'Newsfeed\merchantApiController@editPost');
    Route::post('merchant/post/delete', 'Newsfeed\merchantApiController@deletePost');
    Route::post('merchant/post/image', 'Newsfeed\merchantApiController@postImageUpload');

    Route::post('merchant/peak_hour', 'JsonBranchUserController@getPeakHour');
    Route::post('request/offer', 'JsonBranchUserController@addOfferRequest');

    Route::get('merchant/deals', 'JsonBranchUserController@getDeals');
    Route::post('merchant/deal_payment_history', 'JsonBranchUserController@getDealPaymentHistory');
    Route::post('merchant/deal_redeemed', 'JsonBranchUserController@dealRedeemed');
});
//===========================================================================================
//================================= Royalty Bangladesh Merchant API ===========================================
//===========================================================================================

//===========================================================================================
//================================= Royalty Bangladesh Sales Force App API ===========================================
//===========================================================================================

Route::post('sales_user_login', 'JsonSalesController@authenticate');
Route::get('check_s_version', 'JsonSalesController@versionControl');
Route::post('encrypt/spot-sell', 'JsonSalesController@salesEncrypt');
Route::post('sales/spot/decrypt', 'JsonSalesController@salesDecrypt');
Route::group(['middleware' => ['auth:sales_api']], function () {
    Route::get('sales_user', 'JsonSalesController@getAuthenticatedUser');
    Route::get('sales/promos', 'JsonSalesController@getPromos');
    Route::get('assigned_cards', 'JsonSalesController@getAssignedCardList');
    Route::get('sold_cards', 'JsonSalesController@getSoldCardList');
    Route::get('total_sale_value', 'JsonSalesController@getTotalSaleValue');
    Route::get('balance_redeem_history', 'JsonSalesController@getBalanceRedeemHistory');
    Route::get('balance', 'JsonSalesController@getBalance');
    Route::get('remove_sales_f_token', 'JsonSalesController@removeFirebaseToken');
    Route::post('sales_f_token', 'JsonSalesController@setFirebaseToken');
    Route::post('cards_to_sell', 'JsonSalesController@getCardsForSell');
    Route::post('manualRegistration', 'JsonSalesController@manualRegistration');
    Route::post('sales/trial/user', 'JsonSalesController@createVirtualUser');
    Route::post('spot_purchase', 'JsonSalesController@spotPurchase');
    Route::post('verify/user', 'JsonSalesController@checkUser');
});
Route::post('spot_purchase_from_user', 'JsonSalesController@spotPurchaseFromUser');

//===========================================================================================
//================================= Royalty Bangladesh Sales Force App API ===========================================
//===========================================================================================
//test route to test api
//Route::get('convert/email/verification', 'ActivitySession\functionController@makeUsersEmailVerified');
//Route::get('renew/4/customers', 'Renew\apiController@renew4ExpiredUser');
//Route::get('customers/fix', 'Renew\apiController@fixExpiry');
