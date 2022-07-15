<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Partner Panel Routes
|--------------------------------------------------------------------------
|
| Here you can find partner panel related all routes.
|
*/

/*
 * @description These routes are not being used now
 */

//    Route::get('partnerLogout', 'partnerController@partnerLogout');
//
//    Route::get('partnerTransaction', 'partnerController@partnerTransaction');
//
//    Route::get('/branch-account/{branch_id}', 'loginController@branchAccount')->middleware('partnerLoginCheck');
//
//    Route::post('/partner/admin', 'partnerController@partnerAdminLogin');
//
//    Route::get('/partner/adminDashboard/{username}', 'partnerController@partnerAdminDashboard');
//
//    //route to make notification seen
//    Route::get('unseen_review_notification_of_partner/{id?}', 'partnerController@unseen_review_notification_of_partner');
//    Route::get('unseen_follow_notification_of_partner/{id?}', 'partnerController@unseen_follow_notification_of_partner');
//    Route::get('post_like_notification_of_partner/{id?}', 'partnerController@post_like_notification_of_partner');
//
//    Route::get('seen_review_notification_of_partner/{id?}', 'partnerController@seen_review_notification_of_partner');
//    Route::get('seen_follow_notification_of_partner/{ids?}', 'partnerController@seen_follow_notification_of_partner');
//
//    //route to show all notification of partner
//    Route::get('partner/all-notifications', 'partnerController@allNotifications');
//
//    //route to sort transaction history of partner month wise
//    Route::post('sort-partner-transaction-history', 'partnerController@sortTransactionHistory');
//
//    //route to calculate customer bill
//    Route::post('calculateBill', 'partnerController@calculateBill');
//    //route to save transaction details in database
//    Route::post('/confirmDiscount', 'partnerController@confirmDiscount');
//
//    //=============================================================================================
//    //================================partner admin panel==========================================
//    //=============================================================================================
//    //route to sort sales analytics
//    Route::post('/sort-sales-analytics', 'partnerController@sortSalesAnalytics');
//    Route::post('/sort-sales-analytics-json', 'partnerController@sortSalesAnalyticsJson');
//    //route to sort transaction analytics
//    Route::post('/sort-transaction-analytics', 'partnerController@sortTransactionAnalytics');
//    Route::post('/sort-transaction-analytics-json', 'partnerController@sortTransactionAnalyticsJson');
//    //route to sort gender analytics
//    Route::post('/sort-gender-analytics', 'partnerController@sortGenderAnalytics');
//    Route::post('/sort-gender-analytics-json', 'partnerController@sortGenderAnalyticsJson');
//    //route to sort ageGender analytics
//    Route::post('/sort-ageGender-analytics', 'partnerController@sortAgeGenderAnalytics');
//    Route::post('/sort-ageGender-analytics-json', 'partnerController@sortAgeGenderAnalyticsJson');
//
//    //route to edit basic info
//    Route::get('partner/edit-basic-info', 'partnerController@editBasicInfo');
//    //route to store updated info
//    Route::post('/storeBasicInfo', 'partnerController@storeBasicInfo');
//    //route to view all branches
//    Route::get('/allBranches', 'partnerController@allBranches');
//    //route to view Branch edit page
//    Route::get('/editBranch/{branch_id}', 'partnerController@editBranch');
//    //route to edit branch info
//    Route::post('/branchEditDone/{branch_id}', 'partnerController@editBranchDone');
//
//    //route to logout
//    Route::get('partnerAdminLogout', 'partnerController@partnerAdminLogout');
//
//    // route to edit partner discount info view
//    Route::get('/partner/admin-dashboard/edit-discount', 'partnerController@editDiscount');
//    Route::post('/editDiscounts', 'partnerController@editDiscountDone');
//
//    // route to edit partner attribute (sub categories/tertiary categories) info view
//    Route::get('/partner/admin-dashboard/edit-subcategory', 'partnerController@editSubCategory');
//    Route::post('/editSubcategory', 'partnerController@editSubcategoryDone');
//
//    // route to edit partner facilities info view
//    Route::post('/editFacilities', 'partnerController@editFacilitiesDone');
//
//    // route to edit partner opening hours info view
//    Route::get('/partner/admin-dashboard/edit-opening-hours', 'partnerController@editOpeningHours');
//    Route::post('/editOpeningHours', 'partnerController@editOpeningHoursDone');
//
//    //route to show all posts of partner
//    Route::get('partner/admin-dashboard/all-posts', 'partnerController@allPosts');
//
//    //route to add post backend
//    Route::post('addPost', 'partnerController@addPost');
//
//    //route to delete post
//    Route::get('delete-post/{id}', 'partnerController@deletePost')->middleware('partnerAdminLoginCheck');
//
//    //route to edit post view
//    Route::get('edit-post/{id}', 'partnerController@editPost');
//    Route::post('editPost/{id}', 'partnerController@editPostDone');
//
//    //route to edit profile image of partner
//    Route::get('partner/admin-dashboard/profile-image', 'partnerController@profileImage');
//    Route::post('cropPartnerProfileImage', 'partnerController@uploadCroppedImage');
//    Route::post('cropImage', 'partnerController@imageCrop');
//
//    //route to edit cover pic for partnr profile
//    Route::get('partner/admin-dashboard/cover-photo', 'partnerController@coverPic');
//    Route::post('updateCoverPic', 'partnerController@updateCoverPic');
//
//    //route to edit menu image of partner
//    Route::get('partner/admin-dashboard/menu-images', 'partnerController@menuImages');
//    Route::get('delete-menu-image/{id}', 'partnerController@deleteMenuImage')->middleware('partnerAdminLoginCheck');
//    Route::post('addMenuImages', 'partnerController@addMenuImage');
//
//    //route to edit gallery image of partner
//    Route::get('partner/admin-dashboard/gallery-images', 'partnerController@galleryImages');
//    Route::get('delete-gallery-image/{id}', 'partnerController@deleteGalleryImage')->middleware('partnerAdminLoginCheck');
//    Route::post('addGalleryImages', 'partnerController@addGalleryImage');
//    Route::post('addGalleryCaption', 'partnerController@addGallerycaption');
//
//    Route::resource('partner/post', 'partnerPostController')->middleware('partnerAdminLoginCheck');

/*
 * @description If you uncomment this middleware, partner panel will go 'under construction'.
 * This is process 1, you can also do the same thing with the command 'php artisan down'.
 * 'php artisan up' will do the opposite
 */
// Route::group(['middleware' => ['WebsiteUnderConstruction']], function () {

    //*************** Partner dashboard ******************/
    Route::get('transaction-request', function () {
        return view('transactionRequest.login');
    });
    Route::post('branch_user_login', 'TransactionRequest\webController@login');
    Route::group(['middleware' => ['BranchUserLoginCheck']], function () {
        Route::get('branch/requests', 'TransactionRequest\webController@get_all_requests');
        Route::get('branch/all-transactions', 'TransactionRequest\webController@all_transactions');
        Route::post('branch/sort_transaction_request', 'TransactionRequest\webController@sort_transaction_request');
        Route::get(
            'update_transaction_request/{notification_id}/{request_id}/{status}',
            'TransactionRequest\webController@update_request_status'
        );
        Route::post('checkUser', 'TransactionRequest\webCheckoutController@checkUser');
        Route::post('confirm_offer_transaction', 'TransactionRequest\webCheckoutController@confirmOfferTransaction');
        Route::get('branch/point_prizes', 'TransactionRequest\webController@pointPrizes');
        Route::post('branch/request_scan_prize', 'TransactionRequest\webController@requestScanPrize');
        Route::get('branch/scanner_prize_history', 'TransactionRequest\webController@scannerPrizeHistory');
        Route::get('branch/leaderboard', 'TransactionRequest\webController@scannerLeaderboard');
    });
    Route::get('branch_user_logout', 'TransactionRequest\webController@logout');

//****************************************partner dashboard V2**********************************
/*
 * @description Show partner login page
 */
    Route::get('partner', function () {
        return view('partner-dashboard.login');
    });
/*
 * @description Partner login here
 */
    Route::post('partner/branch_user_login', 'TransactionRequest\v2\webController@login');

    Route::group(['prefix' => 'partner', 'middleware' => ['BranchLoginCheck', 'setLocal']], function () {
        /*
         * @description Set user preferred language to locale
         */
        Route::get('branch/setLocale/{lang}', 'TransactionRequest\v2\webController@setLocale');
        //owner routes access
        Route::group(['middleware' => ['BranchOwnerLoginCheck']], function () {
            /*
             * @description Show partner dashboard page
             */
            Route::get('branch/dashboard', 'TransactionRequest\v2\webController@dashboard');
            /*
             * @description Show all top customers page
             */
            Route::get('branch/all_top_customers', 'TransactionRequest\v2\webController@allTopCustomers');
            /*
             * @description Show transaction peak hour page
             */
            Route::get('branch/peak_hour', 'TransactionRequest\v2\webController@peakHour');
            /*
             * @description Get transaction peak hour data
             */
            Route::post('branch/get_peak_hour_data', 'TransactionRequest\v2\analyticsController@peakHour');
            /*
             * @description Get dashboard data (transaction & visit)
             */
            Route::post('branch/get_dashboard_data', 'TransactionRequest\v2\analyticsController@dashboard');
            /*
             * @description Show profile visit page
             */
            Route::get('branch/profile_visit', 'TransactionRequest\v2\webController@profileVisit');
            /*
             * @description Get profile visit data
             */
            Route::post('branch/get_profile_visit_data', 'TransactionRequest\v2\analyticsController@profileVisit');
            /*
             * @description Show transaction statistics page
             */
            Route::get('branch/transaction_statistics', 'TransactionRequest\v2\webController@transactionStatistics');
            /*
             * @description Get transaction statistics data
             */
            Route::post('branch/get_transaction_statistics', 'TransactionRequest\v2\analyticsController@transactionStatistics');
            /*
             * @index Show all blog page
             * @create Show the view to create a blog
             * @store Save a blog in database
             * @edit Show the view to edit a blog
             * @update Update blog data in database
             * @destroy Delete a blog from database
             */
            Route::resource('branch/post', 'Newsfeed\merchantWebController');
            /*
             * @description Show all pending blog
             */
            Route::get('branch/pending_post', 'Newsfeed\merchantWebController@pendingPosts');
            /*
             * @description Show all approved blog
             */
            Route::get('branch/approved_post', 'Newsfeed\merchantWebController@approvedPosts');
            /*
             * @description Show all reviews page
             */
            Route::get('branch/review', 'TransactionRequest\v2\webController@allReviews');
            /*
             * @description Save partner reply of review in database
             */
            Route::post('branch/replyReview/{ids}', 'TransactionRequest\v2\webController@replyReview');
            /*
             * @description Update review reply
             */
            Route::post('branch/editReviewReply/{id}', 'TransactionRequest\v2\webController@editReviewReply');
            /*
             * @description Delete review reply
             */
            Route::get('branch/deleteReviewReply/{id}', 'TransactionRequest\v2\webController@deleteReviewReply');
            /*
             * @description Partner like review
             */
            Route::post('branch/like_review', 'TransactionRequest\v2\webController@likeReview');
            /*
             * @description Partner unlike review
             */
            Route::post('branch/unlike_review', 'TransactionRequest\v2\webController@unlikeReview');
            /*
             * @description Show creating new offer request page
             */
            Route::get('branch/offer_request', 'TransactionRequest\v2\webController@offerRequestView');
            /*
             * @description Save new offer request
             */
            Route::post('branch/offer_request', 'TransactionRequest\v2\webController@storeOfferRequest');
            /*
             * @description Partner clicks post like notification
             */
            Route::get('branch/notification/post_like/{id}', 'TransactionRequest\v2\webController@viewPostLikeNotification');
            /*
             * @description Partner clicks review notification
             */
            Route::get('branch/notification/review/{id}', 'TransactionRequest\v2\webController@viewReviewNotification');
            /*
             * @description Partner clicks offer/reward availed notification
             */
            Route::get('branch/notification/offer_availed/{id}', 'TransactionRequest\v2\webController@offerAvailedNotification');
            /*
             * @description Partner clicks deal availed notification
             */
            Route::get('branch/notification/deal_availed/{id}', 'TransactionRequest\v2\webController@dealAvailedNotification');
        });
        /*
         * @description Show checkout/transaction request page
         */
         Route::get('branch/requests', 'TransactionRequest\v2\webController@get_all_requests');
        /*
         * @description Show all offers page
         */
        Route::get('branch/offers', 'TransactionRequest\v2\webController@allOffers');
        /*
         * @description Show all rewards page
         */
        Route::get('branch/rewards', 'TransactionRequest\v2\webController@allRewards');
        /*
         * @description Show all deals page
         */
//        Route::get('branch/deals', 'TransactionRequest\v2\webController@allDeals');
        /*
         * @description Show deal purchased page
         */
//        Route::get('branch/deal_purchased', 'TransactionRequest\v2\webController@dealPurchased');
        /*
         * @description Sort deal purchased
         */
        Route::post('branch/sort_deal_purchased', 'TransactionRequest\v2\webController@sortDealPurchased');
        /*
         * @description Show deal payment history page
         */
        Route::get('branch/deals/payments', 'TransactionRequest\v2\webController@dealPaymentHistory');

        /*
         * @description Show how it works page
         */
        Route::get('branch/how_it_works', 'TransactionRequest\v2\webController@howItWorks');
        /*
         * @description Show profile page
         */
        Route::get('branch/profile', 'TransactionRequest\v2\webController@branchProfile');
        /*
         * @description Show leader board page
         */
        Route::get('branch/leaderboard', 'TransactionRequest\v2\webController@scannerLeaderboard');
        /*
         * @description Show all transaction page
         */
        Route::get('branch/transactions', 'TransactionRequest\v2\webController@all_transactions');
        /*
         * @description Sort transaction history
         */
        Route::post('branch/sort_transaction_history', 'TransactionRequest\v2\webController@sort_all_transactions');
        /*
         * @description Show prize page
         */
        Route::get('branch/point_prizes', 'TransactionRequest\v2\webController@pointPrizes');
        /*
         * @description Save request of prize
         */
        Route::post('branch/request_scan_prize', 'TransactionRequest\v2\webController@requestScanPrize');
        /*
         * @description Show prize history page
         */
        Route::get('branch/scanner_prize_history', 'TransactionRequest\v2\webController@scannerPrizeHistory');
        /*
         * @description Validate user at checkout
         */
        Route::post('checkUser', 'TransactionRequest\v2\webCheckoutController@checkUser');
        /*
         * @description Save new transaction
         */
        Route::post('confirm_offer_transaction', 'TransactionRequest\v2\webCheckoutController@confirmOfferTransaction');
        /*
         * @description Accept/Reject new transaction request
         */
        Route::get(
            'update_transaction_request/{notification_id}/{request_id}/{status}',
            'TransactionRequest\v2\webController@update_request_status'
        );

        /*
         * @description Partner clicks transaction notification
         */
        Route::get('branch/notification/transaction/{id}', 'TransactionRequest\v2\webController@viewTransactionNotification');
        /*
         * @description Show partner all notifications page
         */
        Route::get('branch/notification/all', 'TransactionRequest\v2\webController@viewAllNotifications');
        /*
         * @description Get partner notification view to append with pusher
         */
        Route::post('notification_view_for_pusher', 'TransactionRequest\v2\webController@getNotificationViewForPusher');
        /*
         * @description Get partner transaction requests
         */
        Route::post('branch/transaction_requests', 'TransactionRequest\v2\webController@getTransactionRequests');

    });
    /*
     * @description Partner logout
     */
    Route::post('partner_logout', 'TransactionRequest\v2\webController@logout');

// });//maintenance middleware ends
