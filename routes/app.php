<?php

use Illuminate\Support\Facades\Route;
use App\AllAmounts;

//===========================================================================================
//================================= json for app ============================================
//===========================================================================================

Route::group(['middleware' => ['cors', 'IsUserLoggedIn']], function () {
    Route::get('avt.json', 'jsonController@homepageJson');
});
Route::post('/category_facilities', 'jsonController@categoryWiseFacilities');
Route::group(['middleware' => ['IsUserLoggedIn']], function () {
    Route::get('/json_offers/{category?}', 'jsonController@allOffers');

    Route::get('map.json', 'jsonController@partnerLocationList');
    //partners in hotspot
    Route::get('json_hotspot/{name}', 'jsonController@hotspotPartners');
    Route::post('customer_data.json', 'jsonController@userProfile');
    Route::post('edit_customer_data.json', 'jsonController@editProfile');

    //subscription section
    Route::post('subscribe.json', 'jsonController@subscribe');
    Route::post('unsubscribe.json', 'jsonController@unsubscribe');
    Route::post('make_wish.json', 'jsonController@makeWish');
    //review section
    Route::post('make_review.json', 'jsonController@createReview');
    Route::post('like_review.json', 'jsonController@like');
    Route::post('unlike_review.json', 'jsonController@unlike_review');
    Route::post('delete_review.json', 'jsonController@deleteReview');
    //search function section
    Route::get('auto_search.json', 'jsonController@autocomplete');
    //notifications section
    Route::post('customer_notifications.json', 'jsonController@customerNotifications');
    //image upload
    Route::post('image_upload.json', 'jsonController@imageUpload');
    //notification
    Route::post('seen_notification.json', 'jsonController@seenNotification');
    //logout
    Route::get('logout.json', 'jsonController@logout');
    //coupons
    Route::post('select_coupon.json', 'jsonController@select_coupon');
    //review share url
    Route::get('share_review.json', 'jsonController@reviewUrl');
    //save firebase token
    Route::post('save_ftoken.json', 'jsonController@saveFirebaseToken');
    //refer bonus partners
    Route::get('refer_bonus_partner.json', 'jsonController@getReferBonusPartners');
    //filter
    //all location
    Route::get('filter_area.json', 'jsonController@getFilterLocation');
    //all amounts
    Route::get('prices.json', 'jsonController@getPrices');
    Route::post('update_ssl_info.json', 'jsonController@SSLSuccessPayment');
    Route::post('failed_ssl_info.json', 'jsonController@sslTransactionFailed');

    Route::post('send_activation_code.json', 'jsonController@activationSMS');
    Route::post('card_activate.json', 'jsonController@activateCard');
    //single review
    Route::post('single_review.json', 'jsonController@singleReview');
    Route::post('single_liked_review.json', 'jsonController@singleLikeReview');
    //customer reviews
    Route::post('customer_reviews.json', 'jsonController@customerReviews');

    Route::post('v2/insert_ssl_info.json', 'JsonControllerV2@insertSSLInfo');
    //check promo code at buy card
    Route::post('v2/checkCardPromo.json', 'JsonControllerV2@couponValidityCheck');

    Route::post('v2/toFilterPartners.json', 'JsonControllerV2@getPartnerToFilter');
    //email verification
    Route::post('v2/verify_email.json', 'JsonControllerV2@sendMailVerification');
    //for posts
    Route::post('v2/news_feed.json', 'JsonControllerV2@news_feed');
    Route::post('v2/like_post.json', 'JsonControllerV2@like_post');
    Route::post('v2/unlike_post.json', 'JsonControllerV2@unlike_post');
    Route::get('v2/share_post.json', 'JsonControllerV2@feedUrl');

    Route::post('v2/update_gender.json', 'JsonControllerV2@update_gender');
    Route::post('v2/update_dob.json', 'JsonControllerV2@update_dob');
    Route::post('v2/update_image.json', 'JsonControllerV2@update_profile_image');
    //User Reviews
    Route::post('v2/user_reviews.json', 'JsonControllerV2@getUserReviews');
    //User Visited List
    Route::post('v2/user_visited_list.json', 'JsonControllerV2@getUserVisitedList');
    //only partner profile
    Route::post('v2/partner.json', 'JsonControllerV2@getPartnerProfile');
    //Partner Reviews
    Route::post('v2/partner_reviews.json', 'JsonControllerV2@getPartnerReviews');
    //Partner images gallery, menu
    Route::post('v2/partner_gallery.json', 'JsonControllerV2@getPartnerGallery');
    Route::post('v2/partner_menu.json', 'JsonControllerV2@getPartnerMenu');
});

//route for login registration & edit
Route::post('login.json', 'jsonController@loginCheck');

//follow partner section
Route::post('follow_partner.json', 'jsonController@followPartner');
Route::post('unfollow_partner.json', 'jsonController@unfollowPartner');
//follow customer section
Route::post('follow_customer.json', 'jsonController@followCustomer');
Route::post('unfollow_customer.json', 'jsonController@unfollowCustomer');
Route::post('cancel_follow_request.json', 'jsonController@cancelFollowRequest');
Route::post('accept_follow_request.json', 'jsonController@acceptFollowRequestNotification');
Route::post('ignore_follow_request.json', 'jsonController@ignoreFollowRequest');

//review section
Route::post('notification_liked_post.json', 'jsonController@singleLikedPost');

//forgot password section
Route::post('reset_password_email.json', 'jsonController@sendMail');
//search function section
Route::get('search.json', 'jsonController@searchWebsite');

//notifications section
Route::post('partner_notifications.json', 'jsonController@partnerNotifications');

//Registration
Route::post('registration.json', 'jsonController@registration');
//notification
Route::post('partner_liked_notification.json', 'jsonController@partnerLikedNotification');
Route::post('partner_seen_notification.json', 'jsonController@seenPartnerNotification');

//logout
Route::get('partner_logout.json', 'jsonController@partnerLogout');

//coupons
Route::post('birthday_coupon.json', 'jsonController@birthdayNotification');
//recent activity
Route::get('recent_activity.json', 'jsonController@recentActivity');


Route::post('send_ssl_info.json', 'jsonController@insertSSLInfo');
Route::post('partner_transaction_history.json', 'jsonController@getPartnerTransactionHistory');
Route::post('partner_followers.json', 'jsonController@getPartnerFollowers');
Route::post('partner_reviews.json', 'jsonController@getPartnerReviews');

Route::post('partner_reply.json', 'jsonController@partnerReplyReview');
Route::post('check_user.json', 'jsonController@checkUser');
Route::post('bill_calculate.json', 'jsonController@calculateBill');

Route::post('partner_account.json', 'jsonController@partnerAccountInfo');

Route::post('customer_transaction_sort.json', 'jsonController@customerTransactionSort');
//custom message
Route::post('custom_text.json', 'jsonController@sendCustomSMS');

//app version control
Route::get('app_version.json', 'jsonController@versionControl');
Route::get('ios_app_version.json', 'jsonController@iOSVersionControl');
Route::get('checkout_app_version.json', 'jsonController@checkoutVersionControl');

//===========================================================================================
//================================= api version 2 ===========================================
//===========================================================================================
//branch payment status
Route::post('v2/branch_payment_status.json', 'JsonControllerV2@rbdCouponPayment');
//branch login
Route::post('v2/branch_login.json', 'JsonControllerV2@branchLogin');
//branch_profile
Route::post('v2/partner_branch_account.json', 'JsonControllerV2@partnerBranchAccountInfo');
//branch transaction
Route::post('v2/check_user.json', 'JsonControllerV2@checkUser');
Route::post('v2/bill_calculate.json', 'JsonControllerV2@calculateBill');

Route::post('v2/branch_transaction_history.json', 'JsonControllerV2@getPartnerTransactionHistory');
Route::post('v2/partner_branch_list.json', 'JsonControllerV2@partnerBranchList');
Route::post('v2/partner_profile.json', 'JsonControllerV2@partnerProfile');

//for registration purpose
Route::post('v2/create_user.json', 'JsonControllerV2@registration');
//User Profile
Route::post('v2/user_profile.json', 'JsonControllerV2@userProfile');

//User Transactions
Route::post('v2/user_transactions.json', 'JsonControllerV2@getUserTransactions');
//User Requested Coupons
Route::post('v2/user_requested_coupons.json', 'JsonControllerV2@getUserRequestedCoupons');
//Login with and without socials
Route::post('v2/user_social_login.json', 'JsonControllerV2@checkSocialLogin');
//near by partners
Route::post('v2/nearby_partners.json', 'JsonControllerV2@getP2PNearby');

//Partner discounts
Route::post('v2/partner_discounts.json', 'JsonControllerV2@getPartnerDiscounts');

//////////
//user checking by phone number
Route::post('v2/check_phone.json', 'JsonControllerV2@isRoyaltyMember');
Route::post('v2/connect_with_social.json', 'JsonControllerV2@connectWithSocial');
//===========================================================================================
//================================api share for ios==========================================
//===========================================================================================

Route::middleware(['HostCheck'])->group(function () {

    //Registration & forgot password
    Route::post('share/v2/registration', 'jsonController@registration');
    Route::post('share/v2/reset_password_email', 'jsonController@sendMail');
    //image upload
    Route::post('share/v2/image_upload', 'jsonController@imageUpload');
    //route for login, profile & edit
    Route::post('share/v2/login', 'jsonController@loginCheck');

    Route::post('share/v2/customer_data', 'jsonController@userProfile');
    Route::post('share/v2/edit_customer_data', 'jsonController@editProfile');
    //customer suggestions and news letters
    Route::post('share/v2/subscribe', 'jsonController@subscribe');
    Route::post('share/v2/unsubscribe', 'jsonController@unsubscribe');
    Route::post('share/v2/make_wish', 'jsonController@makeWish');
    //notifications section
    Route::post('share/v2/customer_notifications', 'jsonController@customerNotifications');
    //refer bonus partners
    Route::get('share/v2/refer_bonus_partner', 'jsonController@getReferBonusPartners');
    Route::post('share/v2/select_coupon', 'jsonController@select_coupon');
    //google map partners
    Route::get('share/v2/map', 'jsonController@partnerLocationList');
    //home
    Route::get('share/v2/avt', 'jsonController@homepageJson');
    //search function section
    Route::get('share/v2/auto_search', 'jsonController@autocomplete');
    //logout
    Route::get('share/v2/logout', 'jsonController@logout');
    //category wise offers
    Route::get('share/v2/json_offers/{category?}', 'jsonController@allOffers');
    //partner branch list
    Route::post('share/v2/partner_branch_list', 'JsonControllerV2@partnerBranchList');
    //partner branch profile
    Route::post('share/v2/partner_profile', 'JsonControllerV2@partnerProfile');
    //ssl info at buy card
    Route::post('share/v2/send_ssl_info', 'jsonController@insertSSLInfo');
    Route::post('share/v2/update_ssl_info', 'jsonController@SSLSuccessPayment');
    Route::post('share/v2/failed_ssl_info', 'jsonController@sslTransactionFailed');
    //card activation
    Route::post('share/v2/send_activation_code', 'jsonController@activationSMS');
    Route::post('share/v2/card_activate', 'jsonController@activateCard');
    //all amounts
    Route::get('share/v2/prices', 'jsonController@getPrices');
});

//===========================================================================================
//================================Blade links For App========================================
//===========================================================================================

Route::get('/appabt', function () {
    return view('aboutus_app');
});
Route::get('/appfaq', function () {
    $mem_plan_renew = \App\CardPrice::where('platform', \App\Http\Controllers\Enum\PlatformType::web)
        ->where('type', \App\Http\Controllers\Enum\MembershipPriceType::renew)
        ->where('month', 1)->first();
    $mem_plans_buy = \App\CardPrice::where('platform', \App\Http\Controllers\Enum\PlatformType::web)
        ->where('type', \App\Http\Controllers\Enum\MembershipPriceType::buy)
        ->where('month', '!=', 1)
        ->orderBy('month', 'ASC')->get();
    $other_amounts = AllAmounts::all();

    return view('faqspage_app', compact('mem_plan_renew', 'mem_plans_buy', 'other_amounts'));
});
Route::get('/apppnc', function () {
    return view('privacy_policy_app');
});
Route::get('/apptnc', function () {
    $prices = \App\AllAmounts::all();

    return view('terms&conditions_app', compact('prices'));
});
Route::get('/apphiw', function () {
    return view('how_it_works_app');
});
Route::get('/apphiwm', function () {
    return view('how_it_works_merchant_app');
});
Route::get('/apphiws', function () {
    return view('how_it_works_sales_app');
});
Route::get('/apphiwr', function () {
    $other_amounts = AllAmounts::all();

    return view('how_it_works_refer_app', compact('other_amounts'));
});

Route::get('partnernews_guideline', function () {
    return view('/partner-dashboard/post/partnernews_guideline');
});
Route::get('/appblog', 'homeController@appBlog');
Route::get('{category}/appblog', 'homeController@categoryAppBlog');
Route::get('/app-blog-single/{heading}', 'homeController@appBlogSingle');
