<?php

use App\CustomerInfo;
use App\Http\Controllers\functionController;
use App\Http\Controllers\homeController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
/*
|--------------------------------------------------------------------------
| Website & User Routes
|--------------------------------------------------------------------------
|
| Here you can find "website & user" related all routes.
|
*/

/*
 * @description Show website under construction page
 */
Route::get('/maintenancebreak', function () {
    $title = 'Under Construction | royaltybd.com';

    return view('maintenance-break', compact('title'));
});

/*
 * @description If you uncomment this middleware, website will go 'under construction'.
 * This is process 1, you can also do the same thing with the command 'php artisan down'.
 * 'php artisan up' will do the opposite
 */
// Route::group(['middleware' => ['WebsiteUnderConstruction']], function () {

/*
 * @description Show PURCHASE_ATTEMPT (for our uses only)
 */
// Route::get('attempt', function (){
//     $data = [];
//     $data["exp_date"] = '2019-10-10';
//    $data["name"] = 'test name';
//      $data["validity"] = '12 months';
//     $data["email_cover_image"] = 'https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/home-page/card.png';
//  $data["email_body"] = 'Sample Email body';
//      return view('emails.send_attempted_user_email', compact('data'));
//  });
/*
 * @description Show USER_USAGE_SUMMARY (for our uses only)
 */
// Route::get('test1234', function (){
//     $data = [];
//     $data["exp_date"] = '2019-10-10';
//     $data["name"] = 'test name';
//     $data["validity"] = '12 months';
//     $data["email_cover_image"] = 'https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/home-page/card.png';
//     $data["email_body"] = 'Sample Email body';
//     $branch_count = 100;
//     return view('emails.usage_summary', compact('data', 'branch_count'));
//     });
/*
 * @description Show upgrade email template (for our uses only)
 */
    // Route::get('upgrade_mail', function (){
    //     $data = [];
    //     $data["exp_date"] = '2019-10-10';
    //     $data["name"] = 'test name';
    //     $data["validity"] = '12 months';
    //     $data["email_cover_image"] = 'https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/home-page/card.png';
    //     $data["email_body"] = 'Sample Email body';
    //     return view('emails.upgrade', compact('data'));
    // });
/*
 * @description Show buy card email template (for our uses only)
 */
    // Route::get('buy_card_mail', function (){
    //     $data = [];
    //     $data["exp_date"] = '2019-10-10';
    //     $data["name"] = 'test name';
    //     $data["validity"] = '12 months';
    //     $data["email_cover_image"] = 'https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/home-page/card.png';
    //     $data["email_body"] = 'Sample Email body';
    //     return view('emails.buy_card', compact('data'));
    // });
/*
 * @description Show verification email template (for our uses only)
 */
    // Route::get('verification_mail', function (){
    //     $data = [];
    //     $data["verify_url"] = url("verify-email/kjfhkhi4uy538wi45yo8w74tyrsilfdhise475");
    //     $data["text"] = 'Test message';
    //     $data["code"] = 123456;
    //     $data["email_cover_image"] = 'https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/home-page/card.png';
    //     $data["email_body"] = 'Sample Email body';
    //     return view('emails.verification', compact('data'));
    // });
/*
 * @description Show USER_WELCOME email template (for our uses only)
 */
    // Route::get('welcome_mail', function (){
    //     $data = [];
    //     $data["exp_date"] = '2019-10-10';
    //     $data["name"] = 'test name';
    //     $data["validity"] = '12 months';
    //     $data["email_cover_image"] = 'https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/home-page/card.png';
    //     $data["email_body"] = 'Sample Email body';
    //     return view('emails.welcome', compact('data'));
    // });
/*
 * @description Show USER_EXPIRY_SAME_DAY email template (for our uses only)
 */
    //  Route::get('user_expiry', function (){
    //      $data = [];
    //      $data["exp_date"] = '2019-10-10';
    //      $data["name"] = 'test name';
    //      $data["validity"] = '12 months';
    //      $data["email_cover_image"] = 'https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/home-page/card.png';
    //      $data["email_body"] = 'Sample Email body';
    //      return view('emails.user_expiry', compact('data'));
    //  });
/*
 * @description Show USER_EXPIRED_AFTER_10_DAYS email template (for our uses only)
 */
    //  Route::get('user_expired', function (){
    //     $data = [];
    //     $data["exp_date"] = '2019-10-10';
    //     $data["name"] = 'test name';
    //     $data["validity"] = '12 months';
    //     $data["email_cover_image"] = 'https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/home-page/card.png';
    //     $data["email_body"] = 'Sample Email body';
    //     return view('emails.user_expired', compact('data'));
    // });
/*
 * @description Show USER_EXPIRING_IN_10_DAYS email template (for our uses only)
 */
    // Route::get('user_expiring', function (){
    //     $data = [];
    //     $data["exp_date"] = '2019-10-10';
    //     $data["name"] = 'test name';
    //     $data["validity"] = '12 months';
    //     $data["email_cover_image"] = 'https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/home-page/card.png';
    //     $data["email_body"] = 'Sample Email body';
    //     return view('emails.user_expiring', compact('data'));
    // });
    /*
 * @description Show donation THANK_YOU_DONATION user email template (for our uses only)
 */
    // Route::get('thankyou', function (){
    //     $data = [];
    //     $data["exp_date"] = '2019-10-10';
    //     $data["name"] = 'test name';
    //     $data["validity"] = '12 months';
    //     $data["email_cover_image"] = 'https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/home-page/card.png';
    //     $data["email_body"] = 'Sample Email body';
    //     return view('emails.thankyou-donation', compact('data'));
    // });
   /* @description Show deal purchse user email template (for our uses only)
    */
    // Route::get('dealpurchase', function (){
    //     $voucher_details = \App\VoucherPurchaseDetails::where('ssl_id', 1)->with('voucher')->get();
    //     $data = [];
    //     foreach ($voucher_details as $key => $value) {
    //         $exp_date = json_decode($value->voucher->date_duration, true)[0]['to'];
    //         $data[$key]['heading'] = $value->voucher->heading;
    //         $data[$key]['quantity'] = $value->quantity;
    //         $data[$key]['price'] = intval($value->voucher->selling_price * $value->quantity);
    //         $data[$key]['exp_date'] = date('F d, Y', strtotime($exp_date));
    //     }
    //     $subject = 'Thank you for purchasing deal';
    //     return view('emails.dealpurchase', compact('data', 'subject'));
    // });
/*
* @description Show membership purchase cancel page (for our uses only)
*/
// Route::get('payment_cancel', function (){
//     $username = collect();
//    $username->customer_username = 'sohel';
//     return view('payment_cancel', compact('username'));
// });
///*
//* @description Show renew cancel page (for our uses only)
//*/
//Route::get('renew_cancel', function (){
//    $username = collect();
//    $username->customer_username = 'sohel';
//    return view('renew.cancel', compact('username'));
//});
/*
 * @description Check if facebook id already exists or not in database (deprecated)
 */
    Route::post('checkPreFbId', 'homeController@checkFbId');
/*
 * @description Check if phone number already exists or not in database
 */
    Route::post('verifyPrePhone', 'homeController@verifyPhone');
/*
 * @description Get phone number through fb account kit (deprecated)
 */
    Route::post('getPhoneFromFB', 'homeController@getPhoneFromFB');
/*
 * @description Check if email already exists or not in database
 */
    Route::post('checkPreEmail', 'homeController@checkRegEmail');
/*
 * @description Check if username already exists or not in database (deprecated)
 */
    Route::post('checkPreUsername', 'homeController@checkRegUsername');
/*
 * @description Check if refer code exists or not in database at registration
 */
    Route::post('checkRegReferCode', 'LoginRegister\webController@checkRegReferCode');
/*
 * @description Show coming soon page
 */
    Route::get('/coming-soon', function () { //for apps
        return view('under_construction');
    });
/*
 * @description Show a static page who are visiting website through mobile device
 */
    Route::get('/royaltybdforphone', function () {
        return view('mobiledevice.mobiledeviceland');
    });
/*
 * @description Redirect mobile user to play/app store from website
 */
    Route::get('rbdapp', function () {
        return view('redirectMobileUserToStore');
    });
/*
 * @description Show home page of the website
 */
    Route::get('/', 'homeController@homePage');
/*
 * @description Show page with message that user's browser has js disabled
 */
    Route::get('javascript-disabled', function () {
        return view('errors.no-script');
    });
    /*
     *  * @description Show online deal homepage
 */
Route::get('/onlinedeals', function () { //for apps
    return view('onlinedeals.index');
});
//  * @description Show online deal details
//  */
Route::get('/online-deal-details', function () { //for apps
    return view('onlinedeals.onlinedealdetails');
});

    //new offers page
    //Route::get('/home', 'Categories\webController@homePage');
    //
    // Route::get('/offers_copy', 'Categories\webController@getAllCatWisePartners');
    // Route::get('/offers_copy/{category}', 'Categories\webController@getMainCatWisePartners');
    // Route::get('/offers_copy/{category}/{sub_cat_1}', 'Categories\webController@subCat1WisePartner');
    // Route::get('/offers_copy/{category}/{sub_cat_1}/{sub_cat_2}', 'Categories\webController@subCat2WisePartner');

    //route to show all partners or by category
/*
 * @description Show all/category wise offers
 * @param category category of partners
 */
    Route::get('/offers/{category}', 'homeController@allOffers');
    //Route::get('/offers/{category?}', function () {//for apps
    //    return view('under_construction');
    //});
    //route to show all partners by division
    //Route::get('{division?}/offers', 'homeController@divisionOffers');

    //route to show partners in hotspot
    //Route::get('hotspot/{name}', 'homeController@hotspotPartners');

    //Route::get('/partnerjoin', function () {
    //    return view('under_construction');
    //});
/*
 * @description Show influencer program page
 */
    Route::get('influencer-program', 'homeController@influencerProgram');
/*
 * @description Save influencer request to DB
 */
    Route::post('influencer-request', 'homeController@influencerRequest');
/*
 * @description Save partner join page
 */
    Route::get('/partner-join', function () {
        $title = 'Partner Join | royaltybd.com';
        $categories = \App\Categories::orderBy('priority', 'DESC')->get();

        return view('partnerjoin', compact('title', 'categories'));
    });

    //Route::get('/joinform', function () {
    //    return view('under_construction');
    //});
/*
 * @description Save about us page
 */
    Route::get('/about-us', function () {
        $title = 'About Royalty | royaltybd.com';

        return view('aboutus', compact('title'));
    });
/*
 * @description Save partner contact us page
 */
    Route::get('/contact', function () {
        $title = 'Contact Royalty | royaltybd.com';

        return view('contact-us', compact('title'));
    });
/*
 * @description Show top referrers & reviewers
 */
    // Route::get('/top-referrals', 'homeController@topReferrals');

/*
 * @description Show reward page
 */
    Route::get('/royaltyrewards', function () {
        $title = 'Rewards | royaltybd.com';
        $prices = \App\AllAmounts::all();

        return view('royaltyrewards', compact('title', 'prices'));
    })->middleware('customerLoginCheck');

/*
 * @description Show blog page
 */
    Route::get('/blog', 'homeController@blog');

/*
 * @description Show category wise blog page
 * @param category category of blog
 */
    Route::get('{category}/blog', 'homeController@categoryBlog');
/*
 * @description Show single blog page
 * @param heading heading of blog
 */
    Route::get('/blog/{heading}', 'homeController@singleBlogPost');

/*
 * @description Blog shared route
 */
    Route::get('blog-share/{id}', function ($id) {
        $decrypted_id = (new functionController)->postShareEncryption('decrypt', $id);
        $blog_heading = \App\BlogPost::findOrFail($decrypted_id)->heading;

        return redirect('blog/'.$blog_heading);
    });

/*
 * @description Show login page
 */
    Route::get('login', function () {
        $title = 'Login/Sign Up Now! | royaltybd.com';

        return view('login', compact('title'));
    });
/*
 * @description Check phone number at login
 */
    Route::post('checkPhoneNumber', 'LoginRegister\webController@checkPhone');
/*
 * @description Save phone number to account kit stat table
 */
    Route::post('setStatNumber', 'LoginRegister\webController@setStatNumber');
/*
 * @description Check OTP sent to phone at registration
 */
    Route::post('check_code_phone', 'LoginRegister\webController@checkCodePhone');
/*
 * @description Direct login after pin validation
 */
    Route::post('direct-login', 'LoginRegister\webController@directLogin');
/*
 * @description Update phone verify status in account kit stat table
 */
    Route::post('updateStatNumber', 'LoginRegister\webController@updateStatNumber');
/*
 * @description Check pin at user login
 */
    Route::post('checkPinPass', 'LoginRegister\webController@checkPinPass');
/*
 * @description Show sign-up page
 */
    Route::get('signup', 'LoginRegister\webController@signUpView');
/*
 * @description Login check with facebook id
 */
    Route::post('fb-login-check', 'loginController@fbIdExistence');
/*
 * @description Login check with google id
 */
    Route::post('google-login-check', 'loginController@googleIdExistence');
/*
 * @description Login after social id checking
 * @param id ID of social account stored in database
 */
    Route::get('login/{id}', 'loginController@socialLogin');
/*
 * @description Show terms & condition page
 */
    Route::get('/terms&conditions', function () {
        $title = 'Terms & Conditions | royaltybd.com';
        $prices = \App\AllAmounts::all();

        return view('terms&conditions', compact('title', 'prices'));
    });
/*
 * @description Show privacy & policy page
 */
    Route::get('/privacypolicy', function () {
        $title = 'Privacy & Policy | royaltybd.com';

        return view('privacy_policy', compact('title'));
    });
/*
 * @description Show faq page
 */
    Route::get('/faq', 'homeController@faq');
/*
 * @description Show career page
 */
    Route::get('careers', 'homeController@career');
/*
 * @description Show job details page
 * @param position job position
 */
    Route::get('job_opening_details/{position}', 'homeController@jobOpeningDetails');
/*
 * @description Show user wish page
 */
    Route::get('/yourWish', function () {
        $title = 'Make A Wish | royaltybd.com';

        return view('makeWish', compact('title'));
    })->middleware('customerLoginCheck');
/*
 * @description Save user wish data in database
 */
    Route::post('makeWish', 'homeController@makeWish')->middleware('customerLoginCheck');
/*
 * @description Show how it works page
 */
    Route::get('how_it_works', function () {
        $title = 'How It Works? | royaltybd.com';

        return view('how_it_works', compact('title'));
    });

    /*
     * @description Show donation page
     */
    Route::get('/donate', 'donate\donationController@index');

    Route::get('/pay_donation', function () {
        return view('donation.payment_form');
    });
    /*
     * @description Save donation info
     */
    Route::post('/save_donation', 'donate\donationController@saveDonation');

    /*
     * @description Donation success url from ssl commerze
     */
    Route::post('/donation_success', function () {
        return view('donation.success');
    });
    /*
     * @description Donation fail url from ssl commerze
     */
    Route::post('/donation_fail', function () {
        return view('donation.fail');
    });
    /*
     * @description Donation cancel url from ssl commerze
     */
    Route::post('/donation_cancel', function () {
        return view('donation.cancel');
    });

    // /**
    //  * @description Deal success page
    //  */
    // Route::post('/voucher_success', function(){
    //     return view('voucher.success');
    // });
    // /**
    //  * @description Deal fail page
    //  */
    // Route::post('/voucher_fail', function(){
    //     return view('voucher.fail');
    // });
    // /**
    //  * @description Deal cancel page
    //  */
    // Route::post('/voucher_cancel', function(){
    //     return view('voucher.cancel');
    // });

    /*
 * @description Show fail success cancel page test
 */
    // Route::get('renewsuccess', function (){
    //     $data = [];
    //     $data["exp_date"] = '2019-10-10';
    //     $data["name"] = 'test name';
    //     $data["validity"] = '12 months';
    //     $data["email_cover_image"] = 'https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/home-page/card.png';
    //     $data["email_body"] = 'Sample Email body';
    //     return view('renew.success', compact('data'));
    // });

    //Route::get('results', function () {
    //    return view('results');
    //});

    //Route::get('results', function () {
    //    return view('under_construction');
    //});
/*
 * @description Show reward page for app
 */
    Route::get('rrapp', function () {
        $title = 'Rewards | royaltybd.com';
        $prices = \App\AllAmounts::all();

        return view('royaltyrewards_app', compact('title', 'prices'));
    });

    //route to single review for social share purpose
    Route::get('review-share/{id}', function ($id) {
        $decrypted_id = (new functionController)->socialShareEncryption('decrypt', $id);
        $singleReviewDetails = (new homeController)->singleReviewDetails($decrypted_id);

        return view('single-review-details', compact('singleReviewDetails'));
    });

/*
 * @description Show single review details page
 * @param id ID of review
 */
    Route::get('review/{id}', 'homeController@singleReview');

    //route to single post for social share purpose
    Route::get('post-share/{id}', function ($id) {
        $decrypted_id = (new functionController)->postShareEncryption('decrypt', $id);
        $singlePostDetails = (new homeController)->singlePostDetails($decrypted_id);
        $singlePostDetails = $singlePostDetails[0];

        return view('single-post-details', compact('singlePostDetails'));
    });
    //route to view single post details
    Route::get('post/{id}', function ($id) {
        $decrypted_id = (new functionController)->postShareEncryption('decrypt', $id);
        $singlePostDetails = (new homeController)->singlePostDetails($decrypted_id);
        $singlePostDetails = $singlePostDetails[0];

        return view('single-post-details', compact('singlePostDetails'));
    });
/*
 * @description Show card select page for guest user
 */
//    Route::get('/select-card', 'homeController@selectCard')->middleware('DenyBuyCardPage');//turned off paid membership
/*
 * @description User activates trial
 * @param id customer id
 */
    Route::get('activate_trial/{id?}', 'Renew\webController@makeTrialUser');
/*
 * @description Show registration success page
 */
//    Route::get('registration-success', function () {
//        return view('customerReg.registration-success');
//    });
//    Route::get('registration-success', 'RegistrationController@registrationSucceed');

    //route to check if FB id exists or not
//    Route::post('checkFbId', 'RegistrationController@checkFbId');

    //route to check if Google id exists or not
//    Route::post('checkGoogleId', 'RegistrationController@checkGoogleId');

/*
 * @description Store user info from registration page
 */
    Route::post('/registration', 'RegistrationController@registration');
/*
 * @description Show verify email page after registration success
 */
    Route::get('/registration/verify_email', 'RegistrationController@verifyEmailView');
/*
 * @description Store user email verify status after registration
 */
    Route::post('/registration/verify_email', 'RegistrationController@verifyEmail');
/*
 * @description Store user email verify status from account
 */
    Route::post('/useracc/verify_email', 'customerController@verifyEmail');
/*
 * @description Check refer code at buy membership page
 */
    Route::post('checkReferCode', 'homeController@checkReferCode');
/*
 * @description Check promo code at buy membership page
 */
    Route::post('checkCardPromoCode', 'homeController@cardPromoValidityCheck');
/*
 * @description Send OTP to seller at spot purchase by user
 */
    Route::post('sendSpotPurchaseSellerOTP', 'homeController@sendSpotPurchaseSellerOTP');
/*
 * @description Update membership info from user spot purchase
 */
    Route::post('spot_purchase_from_user', 'paymentController@spotPurchaseFromUser');
/*
 * @description Show success page from user spot purchase
 */
    Route::get('spot_purchase_success', 'paymentController@spotPurchaseFromUserSuccess');
/*
 * @description Store new membership info from buy membership page
 */
    Route::post('/confirm_buy_card', 'paymentController@transaction');
/*
 * @description IPN LISTENER for ssl commerze
 */
    Route::post('/payment-check', 'paymentController@paymentCheck');
/*
 * @description Payment success url from ssl commerze
 */
    Route::post('/payment_success', 'paymentController@paymentSucceed');
/*
 * @description Payment fail url from ssl commerze
 */
    Route::post('/payment_fail', 'paymentController@paymentFail');
/*
 * @description Payment cancel url from ssl commerze
 */
    Route::post('/payment_cancel', 'paymentController@paymentCancel');

    Route::get('order-success', 'paymentController@orderSuccess');
/*
 * @description Show session expired page
 */
    Route::get('page-session-expired', function () {
        return view('errors.page_session_out');
    });
/*
 * @description Generate QR & bar Code for customers
 */
    Route::get('generate-qr-bar', 'functionController@generateBarQR');
/*
 * @description Generate QR & bar Code for specific customer
 */
    Route::get('generate-customer-qr-bar', 'functionController@generateCustomBarQR');

/*
 * @description Generate scanner pin
 */
    Route::get('generate-scanner-pin', 'functionController@generateScannerPin');
/*
 * @description Set user pin
 */
    Route::post('set-pin', 'customerController@setPin');
/*
 * @description Update user gender
 */
    Route::post('update-gender', 'customerController@updateGender');
/*
 * @description Update user date of birth
 */
    Route::post('update-dob', 'customerController@updateDOB');
/*
 * @description Edit user date of birth
 */
    Route::post('edit-dob', 'customerController@editDOB');
/*
 * @description Delivery report api for delivery company
 */
    Route::get('/delivery-report', 'homeController@deliveryReport');
/*
 * @description Show all requested coupon in customer profile
 */
    Route::get('RequestedCoupon/{username}', 'customerController@RequestedCoupon');
/*
 * @description Like post from user profile
 */
    Route::post('likePost', 'customerController@likePost');
/*
 * @description Unlike post from user profile
 */
    Route::post('unLikePost', 'customerController@unLikePost');
/*
 * @description Load more review in partner profile
 */
    Route::post('reviewLoad', 'partnerController@reviewLoadmore');
/*
 * @description Get news feed liker list
 */
    Route::post('news_feed_liker_list', 'customerController@getNewsFeedLikerList');
/*
 * @description Get review liker list
 */
    Route::post('review_liker_list', 'customerController@getReviewLikerList');
/*
 * @description Save partner request from join partner page
 */
    Route::post('partnerReg', 'partnerController@savePartnerData');

    //show public profile of partner
    Route::group(['middleware' => 'partnerActiveCheck'], function () {
        /*
         * @description Partner profile from search page
         */
        Route::get('/profile_from_search/{name}/{branch}/{key}', ['uses' => 'homeController@profileFromSearch']);
        /*
         * @description Show partner profile page
         */
        Route::get('/partner-profile/{name}/{branch}', ['uses' => 'homeController@profileFromOffer']);
    });

/*
 * @description Select reward page
 */
    Route::get('/selectreward', function () {
        $title = 'Rewards from Credits | royaltybd.com';

        return view('selectreward', compact('title'));
    });
/*
 * @description User Subscription
 */
    Route::post('/subscribe', 'homeController@subscribe');
/*
 * @description User unsubscribe
 */
    Route::post('/unsubscribe', 'customerController@unsubscribe');
/*
 * @description User subscribe again
 */
    Route::post('/subscribeAgain', 'customerController@subscribeAgain');
/*
 * @description Offer sorting in offers page
 */
    Route::post('sort-offers', 'homeController@sortingInOffersPage');
/*
 * @description Get division wise area in offer sorting page
 */
    Route::post('get-division-wise-area', 'homeController@getDivisionWiseArea');
/*
 * @description Get partner locations in offers page
 */
    Route::post('partner-locations-for-modal', 'homeController@PartnerLocationsForModal');
/*
 * @description Save user message from contact us form
 */
    Route::post('/user-contact', 'homeController@contactForm');
/*
 * @description Customer logout
 */
    Route::post('/customer_logout/{id?}', 'LoginRegister\webController@customerLogout');
/*
 * @description Show customer transaction history
 */
    Route::get('/customerTransaction', 'customerController@customerTransaction');

    // Route::get('online','homeController@online');
    Route::get('online', function () {
        return view('under_construction');
    });
/*
 * @description Show press page
 */
    Route::get('press', 'homeController@pressView');
    //Route::get('/press', function () {//for apps
    //    return view('under_construction');
    //});
/*
 * @description User creates review
 */
    Route::post('/createReview/{partner_id}/{transaction_id}', 'homeController@createReview');
/*
 * @description User likes review
 */
    Route::post('/like', 'customerController@likeReview');
/*
 * @description User unlikes review
 */
    Route::post('/unlike', 'customerController@unlikeReview');
/*
 * @description User follow partner
 */
    Route::post('/follow-partner', 'customerController@followPartner'); /*
 * @description User unfollow review
 */
    Route::post('/unfollow-partner', 'customerController@unfollowPartner');
/*
 * @description User follow other user
 */
    Route::post('/follow-user', 'customerController@followCustomer');
/*
 * @description User unfollow other user
 */
    Route::post('/unfollow-user', 'customerController@unfollowCustomer');
/*
 * @description User cancel follow request of other user
 */
    Route::post('/cancel-follow-request', 'customerController@cancelFollowRequest');

    //show profile of users
    Route::middleware(['customerLoginCheck'])->group(function () {
        /*
         * @description Show user newsfeed page
         */
        Route::get('/users/{username}', 'LoginRegister\webController@userNewsFeed')->name('profileNewsfeed');
        /*
         * @description Show user info page
         */
        Route::get('/users/{username}/info', 'LoginRegister\webController@userInfo')->name('profileInfo');
        /*
         * @description Show user statistics
         */
        Route::get('/users/{username}/statistics', 'LoginRegister\webController@userStatistics')->name('profileStat');
        /*
         * @description Show user rewards page
         */
        Route::get('/users/{username}/rewards', 'LoginRegister\webController@userRewards')->name('profileRewards');
        /*
         * @description Show user review page
         */
        Route::get('/users/{username}/reviews', 'LoginRegister\webController@userReviews')->name('profileReviews');
        /*
         * @description Show user availed offer page
         */
        Route::get('/users/{username}/offers', 'LoginRegister\webController@userOffers')->name('profileOffers');
        /*
         * @description Show user deals
         */
//        Route::get('/users/{username}/deals', 'LoginRegister\webController@userDeals')->name('profileDeals');
        /*
         * @description Show deal details in user profile
         */
        Route::get('/users/{username}/deal_details/{id}', 'LoginRegister\webController@dealDetails')
            ->name('profileDealDetails');
        /*
         * @description Save deal refund request
         */
        Route::post('/users/voucher_refund_request', 'Voucher\webController@voucherRefundRequest')
            ->name('voucherRefundRequest');
        /*
         * @description Show user credit history page
         */
        Route::get('/users/{username}/credit_history', 'LoginRegister\webController@userCreditHistory')
            ->name('profileCreditHistory');
        /*
         * @description Show single reward page in user account
         */
        Route::get('/users/{username}/reward/{id}', 'LoginRegister\webController@specificReward');
        /*
         * @description Show user credit usage history page
         */
        Route::get('/users/{username}/points/{partner?}', 'LoginRegister\webController@pointUsageHistory');

        //    Route::post('reward_details', 'Reward\webController@rewardDetails');
        /*
         * @description Store user reward redeem request
         */
        Route::post('reward_redeem_confirm', 'Reward\webController@rewardRedeemConfirm');
        /*
         * @description Show user edit page
         */
        Route::get('edit-profile', 'customerController@editByCustomerForm')->middleware('customerLoginCheck');
        /*
         * @description Store user new image in session
         */
        Route::post('/editUserImageSelf', 'customerController@editUserImageSelf');
        /*
         * @description Upload image to aws & store image url in database
         */
        Route::post('/updateUserProPic/{id}', 'customerController@updateUserProPic');
        /*
         * @description Update user email
         */
            //    Route::post('/updateUserEmail', 'customerController@updateUserEmail');
        /*
         * @description Update user username
         */
        Route::post('/updateUserUsername', 'customerController@updateUserUsername');
        /*
         * @description Update user pin
         */
        Route::post('/updateUserPin', 'customerController@updateUserPin');
        /*
         * @description Update user phone
         */
        Route::post('/updateUserPhone', 'customerController@updateUserPhone');
        /*
         * @description Show renew page
         */
        Route::get('renew_subscription', 'Renew\webController@renewView');
    });

    //show public profile of users
    //Route::group(['middleware' => 'userActiveCheck'], function () {
    //    Route::get('/user-profile/{username}', 'homeController@userPublicProfile');
    //});

/*
 * @description Search suggestion in website
 */
    Route::get('/autocomplete', 'homeController@autocomplete');
/*
 * @description Show search page with get & post method
 */
    Route::post('/search-website', 'homeController@searchWebsite');
    Route::get('/search-website', 'homeController@searchWebsite');
/*
 * @description Renew fail url from ssl commerze
 */
    Route::post('/renew_fail', function () {
        return view('renew.fail');
    });
/*
 * @description Renew cancel url from ssl commerze
 */
    Route::post('/renew_cancel', function () {
        return view('renew.cancel');
    });
/*
 * @description Confirm user renew
 */
    Route::post('confirm_renew', 'Renew\webController@confirmRenew');
/*
 * @description Get branches list from partner id
 */
    Route::post('branches_from_partner_id', 'customerController@BranchesFromPartnerId');
/*
 * @description Show send OTP page to reset pin
 */
    Route::get('reset_pin/send-sms', function () {
        $step = 1;
        return view('resetPassword', compact('step'));
    });
    /*
     * @description Send OTP to reset pin
     */
    Route::post('reset_pin/send-sms', 'LoginRegister\webController@resetPin');
/*
 * @description Show OTP check page
 */
    Route::get('reset_pin/check-otp', function () {
        $step = 2;
        return view('resetPassword', compact('step'));
    });
/*
* @description Check reset OTP
*/
    Route::post('reset_pin/check-otp', 'LoginRegister\webController@resetOTPCheck');

/*
 * @description Show reset pin page
 */
    Route::get('reset/{token?}', 'customerController@resetUserView');
/*
 * @description Save new pin
 */
    Route::get('/reset-user/{token?}', 'customerController@resetUserDone');

//route to send mail from controller
//Route::post('reset-password', 'homeController@resetPassword');

/*
 * @description Send email verification code/link
 */
    Route::post('send_edit_email_verification', 'customerController@sendEditMailVerification');
/*
 * @description Show email verification success page
 */
    Route::get('verify-email/{token?}', 'customerController@emailVerificationDone');

    //route to show shared review
    Route::get('reviews/{id?}', 'homeController@shareReview');
/*
 * @description Get user notification view to append with pusher
 */
    Route::post('customer_notification_view_for_pusher', 'pusherController@customerNotificationView');

    Route::group(['middleware' => 'customerLoginCheck'], function () {
        /*
         * @description User clicks liked review notification
         */
        Route::get('likedNotification/{ids?}', 'customerController@likedNotification');
        /*
         * @description User clicks transaction notification
         */
        Route::get('discountNotification/{noti_id?}/{cust_id}', 'customerController@discountNotification');
        /*
         * @description User clicks review replied notification
         */
        Route::get('replyNotification/{ids?}', 'customerController@replyNotification');
        /*
         * @description User clicks follow notification
         */
        Route::get('followNotification/{ids?}', 'customerController@followNotification');
        /*
         * @description User clicks birthday notification
         */
        Route::get('birthdayNotification/{id}', 'customerController@birthdayNotification');
        /*
         * @description User clicks accepted follow request notification
         */
        Route::get('acceptFollowRequestNotification/{id}', 'customerController@acceptFollowRequestNotification');
        /*
         * @description User clicks refer notification
         */
        Route::get('referNotification/{username}/{id}', 'customerController@referNotification');
        /*
         * @description User clicks reward notification
         */
        Route::get('rewardNotification/{username}/{id}', 'customerController@rewardNotification');
        /*
         * @description User clicks deal redeem notification
         */
        Route::get('dealNotification/{username}/{id}', 'customerController@dealRedeemNotification');
        /*
         * @description User clicks deal reject notification
         */
        Route::get('dealRejectNotification/{username}/{id}', 'customerController@dealRejectNotification');
        /*
         * @description Show user's all notifications page
         */
        Route::get('user/all-notifications', 'customerController@allNotifications');
        /*
         * @description User makes all notifications as read
         */
        Route::get('mark_user_all_notifications_as_read', 'customerController@markAllNotificationsAsRead');
        /*
         * @description User accepts follow request
         */
        Route::post('/accept-follow-request', 'customerController@acceptFollowRequest');
        /*
         * @description User ignores follow request
         */
        Route::post('/ignore-follow-request', 'customerController@ignoreFollowRequest');
    });
/*
 * @description Crop image & save in session
 */
    Route::post('postImage', 'homeController@imageCrop');
/*
 * @description Error page 500
 */
    Route::get('500', function () {
        abort(500);
    });
/*
 * @description Error page 404
 */
    Route::get('page-not-found', function () {
        abort(404);
    });
/*
 * @description Check if email exists or not other than his email
 */
    Route::post('checkDuplicateEmail', 'customerController@checkDuplicateEmail');
/*
 * @description Sort transaction history of customer month wise
 */
    Route::post('sort-customer-transaction-history', 'customerController@sortTransactionHistory');
/*
 * @description Update post share count
 */
    Route::post('/post-share-count', 'homeController@postShareCount');
/*
 * @description Connect social account to royalty account
 */
    Route::post('/connect-social-account', 'customerController@connectSocialAccount');
/*
 * @description Show email verify success page
 */
    Route::get('email-verify-success', function () {
        $title = 'Email Verification Successful | royaltybd.com';

        return view('email_verify_success', compact('title'));
    });
/*
 * @description for facebook business account
 */
    Route::get('fidaogk7y1kp1xwjs3vezjgwr1jy5l.html', function () {
        return view('fidaogk7y1kp1xwjs3vezjgwr1jy5l');
    });
/*
 * @description Show refer leader board page
 */
    Route::get('/refer_leaderboard', 'homeController@referLeaderboard');
/*
 * @description Show wheel of fortune page
 */
    Route::get('wheel_of_fortune', function () {
        return view('wheel.index');
    });
/*
 * @description Calculate wheel segment where it should stop
 */
    Route::post('calculate_prize', 'Wheel\webController@calculatePrize');

    //VOUCHER SECTION
    /*
     * @description Show Deals page
     */
//    Route::get('royaltydeals', 'Voucher\webController@getAllVouchers');

    /* @description Show deals details page
    */
//    Route::get('deals/{branch_id}', 'Voucher\webController@getBranchVouchers');
    /*
     * @description Get sorted deals according to category
     */
    Route::post('sort-deals', 'Voucher\webController@getSortedVouchers');
    /*
     * @description Save info before sending to SSL
     */
    Route::post('confirm_voucher_purchase', 'Voucher\webController@confirmVoucherPurchase');
    /*
     * @description Send request to SSL
     */
    Route::post('submit_voucher_to_ssl', 'Voucher\webController@submitVoucherToSSL');

    /*
     * @description Deal success url from ssl commerze
     */
    Route::post('/deal_success', function () {
        return view('voucher.success');
    });
    Route::get('/deal_success', function () {
        return view('voucher.success');
    });
    /*
     * @description Deal fail url from ssl commerze
     */
    Route::post('/deal_fail', function () {
        return view('voucher.fail');
    });
    /*
     * @description Deal cancel url from ssl commerze
     */
    Route::post('/deal_cancel', function () {
        return view('voucher.cancel');
    });

//    Route::get('/sitemap', function () {
//        header('application/xml');
//        return view('sitemap');
//    });
// });//maintenance middleware ends
