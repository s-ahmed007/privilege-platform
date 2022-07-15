<?php

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here you can find RBD admin panel related all routes.
|
*/
/*
 * @description Show admin login page
 */
Route::get('/adminDashboard', 'adminController@loginView');
/*
 * @description Admin login here
 */
Route::post('/loginAdmin', 'adminController@loginAdmin');

Route::group(['middleware' => ['RbdSuperAdminLoginCheck']], function () {
    /*
     * @description Show change password page
     */
    Route::get('/admin/settings', 'adminController2@settings');
    /*
     * @description Save admin new password in database
     */
    Route::post('/admin/change_password', 'adminController2@changePassword');
});

Route::group(['middleware' => ['rbdAdminLoginCheck']], function () {
    /*
     * @description Admin logout
     */
    Route::get('/adminLogout', 'adminController@adminLogout');
    /*
     * @description Show dashboard page
     */
    Route::get('/dashboard', 'adminController@dashboard');
    /*
     * @description Show customer leader board page
     */
    Route::get('admin/customer/scan_leaderboard', function () {
        return view('admin.production.analytics.leaderboard.user_scan');
    });
    /*
     * @description Show customer credit leader board page
     */
    Route::get('admin/customer/credit_leaderboard', function () {
        return view('admin.production.analytics.leaderboard.user_credit');
    });
    /*
     * @description Sort customer credit leader board
     */
    Route::post('admin/sort_user_credit_leaderboard', 'admin\AnalyticsController@sortUserCreditLeaderboard');
    /*
     * @description Show partner scan leader board page
     */
    Route::get('admin/partner/scan_leaderboard', function () {
        return view('admin.production.analytics.leaderboard.partner_scan');
    });
    /*
     * @description Show search analytics page
     */
    Route::get('admin/analytics/search', function () {
        return view('admin.production.analytics.search_analytics');
    });
    /*
     * @description Get searched partner list
     */
    Route::post('admin/analytics/search/partners', 'admin\AnalyticsController@getSearchedPartners');
    /*
     * @description Get searched keys list
     */
    Route::post('admin/analytics/search/keys', 'admin\AnalyticsController@getSearchKeysWithoutPartners');
    /*
     * @description Get searched keys of specific partner
     * @param id branch id
     */
    Route::get('admin/analytics/search_keys_of_partner/{branch_id}', 'admin\AnalyticsController@getSearchKeysOfPartners');
    /*
     * @description Get partner leader board in dashboard
     */
    Route::post('admin/dashboard_partner_leaderboard', 'adminController2@dashboardPartnerLeaderboard');
    /*
     * @description Sort partner scan leader board
     */
    Route::post('admin/sort_partner_scan_leaderboard', 'admin\AnalyticsController@sortPartnerLeaderBoard');
    /*
     * @description Show platform analytics page
     */
    Route::get('/admin/analytics', 'adminController@allAnalytics');
    /*
     * @description Show transaction analytics page
     */
    Route::get('/admin/transaction_analytics', function () {
        return view('admin.production.analytics.transaction_analytics');
    });
    /*
     * @description Get all counter for dashboard
     */
    Route::post('/admin/allCounters', 'admin\AnalyticsController@allCounters');
    /*
     * @description Get platform wise profile visit data
     */
    Route::post('/admin/visit_analytics', 'admin\AnalyticsController@visitAnalytics');
    /*
     * @description Show all partner profile visit page
     */
    Route::get('/admin/all_partner_visits', 'admin\AnalyticsController@allPartnerVisitAnalytics');
    /*
     * @description Get daily transaction analytics data
     */
    Route::post('/admin/daily_user_transaction_analytics', 'admin\AnalyticsController@perDayUserTransactionAnalytics');
    /*
     * @description Get weekly transaction analytics data
     */
    Route::post('/admin/weekly_user_transaction_analytics', 'admin\AnalyticsController@perWeekUserTransactionAnalytics');
    /*
     * @description Get periodic transaction analytics data
     */
    Route::post('/admin/periodic_user_transaction_analytics', 'admin\AnalyticsController@periodicUserTransactionAnalytics');
    /*
     * @description Show active members page
     */
    Route::get('/admin/active_member_tran_analytics', 'admin\AnalyticsController@activeMemberTransactionAnalytics');
    /*
     * @description Sort active members
     */
    Route::post('/admin/sort_monthly_active_member', 'admin\AnalyticsController@monthlyActiveMemberAnalytics');
    /*
     * @description Show expired (active/inactive) members page
     */
    Route::get('/admin/expired_members/{status}', 'admin\AnalyticsController@activeInactiveExpiredMembers');
    Route::post('/admin/registration_analytics', 'admin\AnalyticsController@registrationAnalytics');
    Route::post('/admin/peak_hour_analytics', 'admin\AnalyticsController@peakHourAnalytics');
    Route::get('/admin/membership_analytics', 'adminController@membershipAnalytics');
    Route::post('/admin/get_per_day_user_analytics', 'admin\AnalyticsController@perDayUserAnalytics');
    Route::post('/admin/get_weekly_user_analytics', 'admin\AnalyticsController@perWeekUserAnalytics');
    Route::post('/admin/get_periodic_user_analytics', 'admin\AnalyticsController@periodicUserAnalytics');
    Route::post('/admin/get_gender_analytics', 'admin\AnalyticsController@genderAnalytics');
    Route::post('/admin/get_platform_wise_reg_analytics', 'admin\AnalyticsController@platformWiseRegAnalytics');
    Route::post('/admin/get_age_analytics', 'admin\AnalyticsController@ageAnalytics');
    Route::post('/admin/get_app_version_analytics', 'admin\AnalyticsController@appVersionAnalytics');

    Route::get('/admin/all_notifications', 'adminController2@allNotifications');
    Route::post('/admin/sort_all_notification', 'adminController2@sortAllNotifications');
    Route::get('/pdf/generate/activity-report', 'adminController2@generateActivityReport');
    Route::post('/pdf/generate/emails', 'adminController2@generateEmailListFromAllCustomers');

    Route::post('/admin/get_emails_to_print_test', 'adminController2@getEmailsToPrint'); //testing route (can be deleted)
    Route::post('/generate_email_pdf_test', 'adminController2@generatePDFTest'); //testing route (can be deleted)

    //route to show all new partners who wants to join with us
    Route::get('admin/partner-offer-request', 'adminController2@partnerOfferRequest')->middleware('rbdAdminLoginCheck');
    Route::get('admin/partner-request', 'adminController@newPartners')->middleware('rbdAdminLoginCheck');
    //route to check Partner exists in top brands/trending offers before delete
    Route::post('existsInTrendingBrands', 'adminController@existsInTrendingBrands');
    //route to delete new partner
    Route::get('delete-new-partner/{id}', 'adminController@deleteNewPartner');
    //route to get all birthdays
    //Route::get('birthdays', 'adminController@birthdayList')->middleware('rbdAdminLoginCheck');

    //route to view log file
    //    Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
    //route to send birthday wishes to all
    Route::get('birthdays', 'adminController@birthdayWish');
    //route to show card prices
    Route::get('admin/membership_prices', 'Membership\membershipPriceController@membershipPrices');
    Route::post('admin/add_membership_price', 'Membership\membershipPriceController@addMembershipPrice');
    Route::post('admin/update_membership_price', 'Membership\membershipPriceController@updateMembershipPrice');
    Route::delete('admin/delete_membership_price/{id}', 'Membership\membershipPriceController@deleteMembershipPrice');

    Route::get('admin/other_prices', 'adminController@cardPrice');
    //route to add card prices
//    Route::post('add_card_price', 'adminController@addCardPrice');//not being used
    //route to change card prices
//    Route::post('change_card_price', 'adminController@changeCardPrice');//not being used
    //route to change card prices
    Route::post('change_other_prices', 'adminController@changeOtherPrices');
    //route to delete a price item
//    Route::get('delete-price-item/{item_id}', 'adminController@deletePriceItem');//not being used
    //route to show card delivery customer list
    Route::get('card-delivery/{status}', 'adminController@cardDeliveryList');
    Route::get('purchase/history/{status}', 'admin\PurchaseHistoryController@getAllPurchaseHistory');
    //route to activate trial
    Route::get('admin/activate_trial/{customer_id}', 'adminController2@activateTrial');
    //route to show point customization
    Route::get('customizePoints/{partner_id}', 'adminController@customizePoints');
    //route to update customized points
    Route::post('update_points/{partner_id}', 'adminController@updatePoints');
    //route to update customized points
    Route::post('delete-customized-point/{partner_id}', 'adminController@deleteCustomizedPoint');
    //route to change card delivery status
    Route::post('/change_delivery_status', 'adminController@change_delivery_status');
    //route to update delivery type
    Route::post('/update_delivery_type', 'adminController@update_delivery_type');
    //route to update actual price
    Route::post('/update_actual_price', 'adminController@update_actual_price');

    Route::get('/customers/card_users', 'adminController@cardUsers');
    Route::get('/customers/upgraded', 'adminController@upgradedMembers');
    Route::get('/customers/renewed', 'adminController@renewedMembers');
    Route::get('/customers/card_holders', 'adminController@cardHolders');
    Route::get('/customers/guest', 'adminController@guestCustomers');
    Route::get('/customers/spot', 'adminController@spotCustomers');
    Route::get('/customers/expired/trial', 'adminController@expiredTrialCustomers');
    Route::get('/customers/expired/premium', 'adminController@expiredPremiumCustomers');
    Route::get('/customers/expiring', 'adminController@expiringCustomers');
    Route::get('/customers/recent', 'adminController@recentMembershipOrders');
    Route::get('/customers/active', 'adminController@activeCustomers');
    Route::get('/customers/inactive/trial', 'adminController@inactiveTrialCustomers');
    Route::get('/customers/inactive/premium', 'adminController@inactivePremiumCustomers');
    Route::get('/customers/influencer', 'adminController@influencerCustomers');
    Route::get('/customers/trial', 'adminController@getAllTrialUsers');
    Route::get('/customers/influencer-payment', 'adminController@influencerPayment');
    Route::post('/pay-influencer', 'adminController@payInfluencer');
    Route::get('/customers/b2b2c/{client_id}', 'adminController@b2b2cCustomers');

    Route::get('/partners-all-transactions/{status}', 'adminController@AllTransactions');
    Route::get('/transactionList/active', 'admin\ScanSummeryController@transactionList');
    Route::get('/transactionList/active/{current}', 'admin\ScanSummeryController@sortedTransactionList');
    Route::get('/transactionList/active/{old}', 'admin\ScanSummeryController@sortedTransactionList');
    Route::post('/delete-transaction/{id}', 'adminController2@removeTransaction');

    Route::get('/transactionList/inactive', 'admin\ScanSummeryController@inactivePartners');
    Route::get('/transactionList/inactive/{current}', 'admin\ScanSummeryController@sortedInactivePartners');
    Route::get('/transactionList/inactive/{old}', 'admin\ScanSummeryController@sortedInactivePartners');

    Route::get('/rbd-coupon-payment', 'adminController@couponPayment');
    Route::post('/pay-partner-for-coupon', 'adminController@payPartnerForCoupon');

    Route::get('all-scanners', 'PartnerBranchController@scannerList');
    Route::get('partner-branches', 'PartnerBranchController@allBranches');
    Route::get('manage-branch-scanners/{branch_id}', 'PartnerBranchController@allScanners');
    Route::get('create-branch-scanner/{branch_id}', 'PartnerBranchController@createScanner');
    Route::post('store-branch-scanner/{branch_id}', 'PartnerBranchController@storeBranchScanner');
    Route::get('edit-branch-scanner/{scanner_id}', 'PartnerBranchController@editScanner');
    Route::post('update-branch-scanner/{scanner_id}', 'PartnerBranchController@updateScannerInfo');
    Route::get('delete-branch-scanner/{scanner_id}', 'PartnerBranchController@deleteScanner');
    Route::post('/branchUserApproval', 'PartnerBranchController@branchUserApproval');
    Route::get('/scanner-request', 'PartnerBranchController@scannerRequest');
    Route::post('/scannerRequestAccept', 'PartnerBranchController@scannerRequestAccept');
    Route::get('/scanner-leader-board', 'PartnerBranchController@scannerLeaderBoard');
    Route::post('/sort-scanner-leaderboard', 'PartnerBranchController@sortScannerLeaderBoard');

    Route::get('manage-branch-ip-address/{branch_id}', 'PartnerBranchController@allIpAddresses');
    Route::get('create-branch-ip-address/{branch_id}', 'PartnerBranchController@createIpAddress');
    Route::post('store-branch-ip-address/{branch_id}', 'PartnerBranchController@storeIpAddress');
    Route::get('edit-branch-ip-address/{ip_id}', 'PartnerBranchController@editBranchIpAddress');
    Route::post('update-branch-ip-address/{branch_id}/{ip_id}', 'PartnerBranchController@updateBranchIpAddress');
    Route::get('delete-branch-ip-address/{ip_id}', 'PartnerBranchController@deleteBranchIpAddress');

    Route::get('branch-user-scanner-prizes', 'PartnerBranchController@scannerPrizes');
    Route::get('create-scanner-prize', 'PartnerBranchController@createScannerPrize');
    Route::post('store-scanner-prize', 'PartnerBranchController@storeScannerPrize');
    Route::get('edit-scanner-prize/{id}', 'PartnerBranchController@editScannerPrize');
    Route::post('update-scanner-prize/{id}', 'PartnerBranchController@updateScannerPrize');
    Route::get('delete-scanner-prize/{id}', 'PartnerBranchController@deleteScannerPrize');

    Route::get('branch-user-leaderboard-prizes', 'PartnerBranchController@leaderboardPrizes');
    Route::get('create-leaderboard-prize', 'PartnerBranchController@createLeaderboardPrize');
    Route::post('store-leaderboard-prize', 'PartnerBranchController@storeLeaderboardPrize');
    Route::get('edit-leaderboard-prize/{id}', 'PartnerBranchController@editLeaderboardPrize');
    Route::post('update-leaderboard-prize/{id}', 'PartnerBranchController@updateLeaderboardPrize');
    Route::get('delete-leaderboard-prize/{id}', 'PartnerBranchController@deleteLeaderboardPrize');

    Route::get('card-seller', 'CardSaleController@allSellers');
    Route::post('seller-approval', 'CardSaleController@sellerApproval');

    Route::get('create-card-seller', 'CardSaleController@createSeller');
    Route::post('store-seller', 'CardSaleController@storeSeller');
    Route::get('edit-seller/{seller_id}', 'CardSaleController@editSeller');
    Route::get('pay-seller/{seller_id}', 'CardSaleController@paySeller');
    Route::post('update-seller/{seller_id}', 'CardSaleController@updateSellerInfo');
    Route::get('admin/seller_sales_history/{seller_id}', 'CardSaleController@salesHistory');

    Route::get('assign-card/{user_id}', 'CardSaleController@assignCard');
    Route::get('assigned-card/{user_id}', 'CardSaleController@assignedCard');
    Route::post('store-assigned-card/{user_id}', 'CardSaleController@storeAssignedCard');
    Route::get('edit-assigned-card/{id}', 'CardSaleController@editAssignedCard');
    Route::post('update-assigned-card/{id}', 'CardSaleController@updateAssignedCard');
    Route::get('delete-assigned-card/{id}', 'CardSaleController@deleteAssignedCard');

    Route::get('/seller-requests', 'CardSaleController@sellerRequest');
    Route::post('/sellerRequestAccept', 'CardSaleController@sellerRequestAccept');

    //resource route for openings
    Route::resource('openings', 'OpeningController');
    //route to Active/Deactive opening
    Route::post('active-opening/{id}', 'OpeningController@activate_opening');
    Route::post('deactive-opening/{id}', 'OpeningController@deactivate_opening');
});

//resource route for card promo
Route::resource('card-promo', 'CardPromoController');

//route to change moderator status in customer account table
Route::post('/customerApproval', 'adminController@customerApproval');
//route to change suspension status in customer account table
Route::post('/customerSuspension', 'adminController2@customerSuspension');

//route to change moderator status in partner post table
Route::post('/postApproval', 'adminController@postApproval')->middleware('rbdAdminLoginCheck');
//route to show info of unapproved accounts or posts
Route::get('/under-moderation/{param}', 'adminController@underModeration');
//route to show all approved posts of partners
Route::get('/allPosts', 'adminController@allApprovedPosts');
//route to delete a particular Area
Route::get('deleteArea/{id}', 'adminController@delete_area')->middleware('rbdAdminLoginCheck');
//route to update a particular Area Name
Route::post('/updateAreaName', 'adminController@update_area_name')->middleware('rbdAdminLoginCheck');
//route to show areas under selected division
Route::post('/selectedAreaList', 'adminController@selected_area_list');
//route to search review by id
Route::post('/searchReview', 'adminController@searchReview');
//route to search review by customer
Route::get('customerByKey', 'adminController@customerByKey');
//route to search review by customer
Route::post('purchase/history/customerByKey', 'adminController@searchCustomerByKeyForPurchaseHistory');
//route to search review by customer
Route::get('customerNameByKey', 'adminController@customerNameByKey');
//route to search review by partner
Route::get('partnerByKey', 'adminController@partnerByKey');
//route to search review by partner
Route::get('partnerNameByKey', 'adminController@partnerNameByKey');
//route to search review by partner
Route::get('partnerByKeyName', 'adminController@partnerByKeyName');
//route to show all reviews by customers
Route::get('admin/allCustomerReviews', 'adminController@allCustomerReviews');
Route::get('admin/allCustomerDeletedReviews', 'adminController@allCustomerDeletedReviews');
Route::get('admin/pending_reviews', 'adminController@allCustomerPendingReviews');
Route::get('admin/approveReview/{id}', 'adminController@approveReview');
Route::get('admin/rejectReview/{id}', 'adminController@rejectReview');
Route::get('admin/pending_review_replies', 'adminController@allPendingReviewReplies');
Route::get('admin/edit-review-reply/{id}', 'adminController@editReviewReply');
Route::post('admin/edit-review-reply/{id}', 'adminController@updateReviewReply');
Route::get('admin/approveReviewReply/{id}', 'adminController@approveReviewReply');
Route::get('admin/rejectReviewReply/{id}', 'adminController@rejectReviewReply');

//route to edit a particular review by Admin
Route::get('/edit-review/{id?}', 'adminController@editReview');
Route::get('/reviewEditDone/{id?}', 'adminController@reviewEditDone');
//route to delete a particular review by Admin
Route::get('admin/deleteReview/{id}', 'adminController@deleteReview')->middleware('rbdAdminLoginCheck');
//route to delete a particular review by User Himself
Route::get('reviewDelete/{id}', 'customerController@reviewDelete')->middleware('customerLoginCheck');
//route to search partner wise post
Route::post('/searchPartnerPost', 'adminController@searchPartnerPost');
//route to get posts by partner name
Route::post('PostByPartnerName', 'adminController@PostByPartnerName');

Route::get('/form_upload', 'adminController@partnerFormUpload');
Route::post('/admin/load_sub_cats', 'adminController@loadSubCats');
Route::get('/allPartners', 'adminController@allPartners');
Route::get('/allPartners/activated', 'adminController@allActivatedPartners');
Route::get('/allPartners/deactivated', 'adminController@allDeactivatedPartners');
Route::get('/allPartners/about_to_expire', 'adminController@allAboutToExpirePartners');
Route::get('/allPartners/expired', 'adminController@allExpirePartners');
Route::post('/partner-branch-change-status/partner/{partner_branch_id}', 'adminController@partnerBranchChangeStatus');
Route::get('/changePartnerStatus', 'adminController@changePartnerStatus');
Route::post('/partner-change-status/partner/{partner_account_id}', 'adminController@partnerChangeStatus');
Route::post('/delete-branch/{branch_id}', 'adminController@deleteBranch');
Route::get('adminTransac/{id?}', 'adminController@adminTransac');
//Route::post('/searchPartner', 'adminController@searchPartner');

Route::get('/edit_partner/{branch_id}', 'adminController@editPartner');
Route::post('/partnerEditDone/{partner}/{branch}', 'adminController@partnerEditDone');

Route::post('addPartner', 'adminController@addPartner');
//route to add branch
Route::post('check-main-branch-exist', 'adminController@checkMainBranchExist');
Route::get('admin/add-branch', function () {
    $all_partners = \App\PartnerInfo::all();

    return view('admin.production.addBranchPartnerList', compact('all_partners'));
});
Route::get('admin/add-branch/{id}', 'adminController@addBranch');
Route::post('store-branch', 'adminController@storeBranch');

//route to show add coupon view page
Route::get('coupon', 'adminController@couponAdd');

//route to do add coupon backend in controller
Route::post('addCoupon', 'adminController@addCoupon');

//route to do edit a coupon backend in controller
Route::get('edit_coupon/{id?}', 'adminController@editCoupon')->middleware('rbdAdminLoginCheck');
//route to do function for edit a coupon
Route::post('edit_coupon_done', 'adminController@editCouponDone')->middleware('rbdAdminLoginCheck');

//route to do edit refer bonus backend in controller
Route::get('refer-bonus', 'adminController@referBonus')->middleware('rbdAdminLoginCheck');
//route to do function for edit refer bonus
Route::post('edit_refer_bonus', 'adminController@editReferBonus')->middleware('rbdAdminLoginCheck');

//Route to show all coupons in admin panel
Route::get('/allCoupons', 'adminController@allCoupons');
//route to show all news in admin panel
Route::get('/allNews', 'adminController@allNews');
//route to add new news from admin panel
Route::get('pressAdmin', function () {
    return view('admin.production.news_add');
});
//route to add Newsletter
Route::get('addNewsletter', 'adminController@addNewsletter')->middleware('rbdAdminLoginCheck');
//route to send push notification
Route::get('send-push-notification/{type}', 'adminController@sendPushNotificationView');
Route::post('send-push-notification', 'adminController@sendPushNotification');
Route::post('sending-push-notification', 'adminController@sendingPushNotification');
Route::post('admin/saveSentMessage', 'adminController@saveSentMessage');
Route::get('admin/scheduled-notification', 'adminController@scheduledNotifications');
Route::get('admin/edit_scheduled_notification/{id}', 'adminController@editScheduledNotification');
Route::post('admin/update_scheduled_notification/{id}', 'adminController@updateScheduledNotification');

//route to save news to database
Route::post('/addNews', 'adminController@addNews');
//edit news view page
Route::get('/edit-news/{id}', 'adminController@editNews');
//update news info
Route::post('/edit-news/{id}', 'adminController@updateNews');
//delete news
Route::get('/delete-news/{id}', 'adminController@deleteNews');
//route to add new division & area view
Route::get('add-division-area', 'adminController@addDivisionArea');
//route to store new division
Route::post('add-division', 'adminController@addDivision');
//route to store new area
Route::post('add-area', 'adminController@addArea');

//used before rbd migrate
Route::post('partner-delete/{id}', 'adminController@deletePartner')->middleware('RbdSuperAdminLoginCheck');

Route::group(['middleware' => ['rbdAdminLoginCheck']], function () {
    //route to show all wishes in admin panel
    Route::get('admin/allWishes', 'adminController@allWishes');
    Route::get('/delete_wish/{id}', 'adminController@deleteWish');
    //route for search customer by id in admin panel
    Route::post('/customerById', 'adminController@searchCustomerByKey');
    Route::get('/customerById', 'adminController@searchCustomerByKey');
    Route::post('/deliveryCustomerById', 'adminController@searchDeliveryCustomerByKey');
    Route::get('/deliveryCustomerById', 'adminController@searchDeliveryCustomerByKey');
    Route::post('/freeTrialUserById', 'adminController@searchFreeTrialUserByKey');
    Route::get('/freeTrialUserById', 'adminController@searchFreeTrialUserByKey');
    Route::post('/customerForSMS', 'adminController@searchCustomerForSMS');
    Route::get('/customerForSMS', 'adminController@searchCustomerForSMS');
    Route::post('/cod-customer', 'adminController@searchCustomerByCard');
    Route::get('/cod-customer', 'adminController@searchCustomerByCard');
    Route::get('/customerByCod', 'adminController@customerByCod');
    Route::post('/temp-customer', 'adminController@searchTempCustomer');
    Route::get('/temp-customer', 'adminController@searchTempCustomer');
    Route::get('/customerByTemp', 'adminController@customerByTemp');
    //Route::get('/card-delivery-success', 'adminController@codSuccess');
    //route for search customer by id in admin panel
    Route::post('/partnerByName', 'adminController@searchPartnerByName');
    Route::get('/partnerByName', 'adminController@searchPartnerByName');

    Route::get('/edit-user/{id}', 'adminController@editUser');
    Route::get('/admin/upgrade-membership/{id?}', 'adminController2@upgradeMembership');
    Route::post('/admin/upgradeMembershipDone/{id?}', 'adminController2@upgradeMembershipDone');

    //Route to edit lost customer
    Route::get('/edit-lost-user/{id?}', 'adminController@editLostUser');
    Route::get('/lostUserEditDone/{id?}', 'adminController@lostUserEditDone');
    Route::post('/editUserImage', 'adminController@editUserImage');
    Route::get('/customerEditDone/{id?}', 'adminController@editDone');
});

Route::group(['middleware' => ['RbdSuperAdminLoginCheck']], function () {
    Route::post('delete-user/{id}', 'adminController@deleteCustomer');
    Route::get('admin/user-delete/{id}', 'adminController2@deleteUser');
    Route::get('delete-temp-customer/{id}', 'adminController@deleteTempCustomer');
    Route::get('decline-cod/{id}', 'adminController@deleteCODCustomer');
});

Route::group(['middleware' => ['rbdAdminLoginCheck']], function () {
    //route to show all hotspots in admin panel
    Route::get('allHotspots', 'adminController@allHotspots');

    //route to show all partners to be deleted from a hotspot
    Route::get('hotspotPartners', 'adminController@hotspotPartners');

    //route to show all partners list based on Hotspot
    Route::post('hotspotPartnerList', 'adminController@hotspotPartnerList');

    //route to delete partner from hotspot
    Route::get('deleteHotspotPartner/{id}', 'adminController@deleteHotspotPartner');

    //route to add hotspot from admin panel view
    Route::get('/addHotspot', 'adminController@addToHotspot');
    //route to add hotspot from admin panel backend
    Route::post('addHotspot', 'adminController@addHotspot');
    //route to add top trending offers & top brands from admin panel backend
    Route::get('admin/trendingBrands', 'adminController2@trendingBrands');

    Route::post('admin/addTrendingOffer', 'adminController2@addTrendingOffer');
    Route::post('admin/trendingOffer/update_order', 'adminController2@updateTrendingOffersOrder');
    Route::get('admin/removeTrendPartner/{id}', 'adminController2@removeTrendPartner');

    Route::post('admin/addTopBrand', 'adminController2@addTopBrand');
    Route::post('admin/topBrands/update_order', 'adminController2@updateTopBrandsOrder');
    Route::get('admin/removeTopBrand/{id}', 'adminController2@removeTopBrand');

    Route::get('admin/featuredPartners', 'FeaturedPartners\functionController@featuredPartners');
    Route::post('admin/addFeaturedPartner/{id}', 'FeaturedPartners\functionController@addFeaturedPartners');
    Route::get('admin/removeFeaturedPartner/{id}', 'FeaturedPartners\functionController@removeFeaturedPartners');

    Route::post('admin/category/update_order', 'FeaturedPartners\functionController@updateFeaturedOrder');
    //branch facilities
    Route::resource('admin/branch_facilities', 'admin\BranchFacilitiesController');

    //route to add partner to a hotspot
    Route::post('/addPartnerToHotspot', 'adminController@addPartnerToHotspot');
    //route to delete hotspot
    Route::get('delete_hotspot/{id}', 'adminController@deleteHotspot');
    //route to view influencer requests
    Route::get('admin/influencer-requests', 'adminController@influencerRequests');
    //route to delete
    Route::delete('delete_influencer_request/{id}', 'adminController@deleteInfluencerRequest');
    //route to generate qr for branch transaction
    Route::get('branch-qr/{partner_id}/{branch_id}', 'functionController@partnerBranchQr');

    //route to manage all categories
    Route::resource('admin/main_cat', 'admin\categories\mainCatController');
    Route::resource('admin/sub_cat_1', 'admin\categories\subCat1Controller');
    Route::resource('admin/sub_cat_2', 'admin\categories\subCat2Controller');
    Route::resource('admin/category_relation', 'admin\categories\catRelController');
    Route::get('/admin/category_relation/assign_partner/{id}', 'admin\categories\catRelController@assignPartnerView');
    Route::post('/admin/category_relation/assign_partner/{id}', 'admin\categories\catRelController@storeAssignPartner');
    Route::delete('/admin/category_relation/remove_assigned_partner/{rel_id}', 'admin\categories\catRelController@removeAssignedPartner');
    Route::resource('admin/part_cat_relation', 'admin\categories\partCatRelController');

    //route to add promo code view
    Route::get('addPromo', function () {
        return view('admin.production.add_promo_code');
    });
    //route to add promo code backend
    Route::post('add_promo_code', 'adminController@addPromoCode');
    //route t show all promo codes
    Route::get('allPromo', 'adminController@allPromoCodes');
    //route to search promo code by partner name
    Route::post('/searchPromo', 'adminController@searchPromo');
    //route t edit promo code view
    Route::get('edit_promo/{id}', 'adminController@editPromoCode');
    //route to edit promo code backend
    Route::post('edit_promo_code/{id}', 'adminController@edit_promo_code');
    //route to delete promo from database
    Route::get('delete_promo/{id}', 'adminController@deletePromo');

    //route to Active/Deactive card promo
    Route::get('active-card-promo/{id}', 'adminController@activate_card_promo');
    Route::get('deactive-card-promo/{id}', 'adminController@deactivate_card_promo');
    //route of contacts
    Route::get('/all-contacts', 'adminController@allContacts');
    Route::get('/delete-contact/{id}', 'adminController@deleteContact');

    //route to posts resources
    Route::resource('admin/post', 'adminPostController');
    //route to Active/Deactive post
    Route::get('active-post/{id}', 'adminPostController@activate_post');
    Route::get('deactive-post/{id}', 'adminPostController@deactivate_post');
    //route to pin/unpin post
    Route::post('unpin-post/{id}', 'adminPostController@unpinPost');
    Route::post('pin-post/{id}', 'adminPostController@pinPost');

    //route to blog resources
    Route::resource('admin/blog-post', 'blogController');
    Route::get('admin/update_blog_status/{id}/{status}', 'blogController@updateBlogStatus');
    Route::post('admin/add_blog_category', 'blogController@addBlogCategory');
    Route::post('admin/update_blog_category', 'blogController@updateBlogCategory');
    Route::post('admin/delete_blog_category', 'blogController@deleteBlogCategory');
    //route to add top trending offers & top brands from admin panel backend
    Route::get('blogCategories', 'adminController@blogCategories');

    //route to edit Thumb pic for partnr profile
    Route::get('edit_pro_pic/{partner_id}', 'adminController@proPic');
    Route::post('updateProPic/{partner_id}', 'adminController@updateProPic');
    //route to edit cover photo for partnr profile
    Route::get('admin/partner_cover_photo/{partner_id}', 'adminController2@partnerCoverPhoto');
    Route::post('update_partner_cover_photo/{partner_id}', 'adminController2@updatePartnerCoverPhoto');

    //route to edit gallery pic for partner profile
    Route::get('partner-gallery-images/{partner_id}', 'adminController@galleryImage');
    Route::get('delete-partner-gallery-image/{id}', 'adminController@deleteGalleryImage');
    Route::post('addGalleryImages/{partner_id}', 'adminController@addGalleryImage');
    Route::post('addPartnerGalleryCaption', 'adminController@addGallerycaption');
    Route::get('pin_gallery_image/{partner_id}/{img_id}', 'adminController@pinGalleryImage');
    //route to edit menu pic for partner profile
    Route::get('admin/partner-menu-images/{partner_id}', 'adminController2@menuImage');
    Route::get('admin/delete-partner-menu-image/{id}', 'adminController2@deleteMenuImage');
    Route::post('admin/addMenuImages/{partner_id}', 'adminController2@addMenuImage');
    Route::post('admin/addPartnerMenuCaption', 'adminController2@addMenuCaption');
    Route::get('admin/pin_menu_image/{partner_id}/{img_id}', 'adminController2@pinMenuImage');

    //route to edit partner sub category
    Route::get('/partner-subcategory/{id}', 'adminController2@editSubCategory');

    //admin add offers
    Route::get('/admin/offers/add', 'adminController2@addOffers');
    Route::get('/admin/rewards/add', 'adminController2@addRewards');
    Route::get('/admin/royalty/offers/add', 'adminController2@addRoyaltyOffers');
    Route::get('/admin/deal/add', 'adminController2@addVouchers');

    //admin reward section
    Route::resource('admin/reward', 'admin\RewardController');
    Route::get('admin/partner_rewards', 'admin\RewardController@partnerRewards');
    Route::get('admin/redeemed_reward/royalty/{status?}', 'admin\RewardController@redeemedRewards');
    Route::get('admin/redeemed_reward/partner/{status?}', 'admin\RewardController@partnerRedeemedRewards');
    Route::get('admin/dispatch_reward/{id}', 'admin\RewardController@dispatchReward');
    Route::get('activate-reward/{id}', 'admin\RewardController@activateReward');
    Route::get('deactivate-reward/{id}', 'admin\RewardController@deactivateReward');
    Route::get('admin/rewards/payment', 'admin\RewardController@rewardPayment');
    Route::get('admin/rewards/partnerWithBranch', 'admin\RewardController@partnerWithBranchForSearch');
    Route::post('admin/rewards/getSinglePartnerForPayment', 'admin\RewardController@getSinglePartnerForPayment');
    Route::get('admin/royalty/rewards/costing', 'admin\RewardController@royaltyRewardCosting');
    Route::post('admin/rewards/clear_payment/{branch_id}', 'admin\RewardController@clearRewardPayment');

    /*
     * @description Voucher CRUD routes
     */
    Route::resource('admin/vouchers', 'admin\VoucherController');
    /*
     * @description Change voucher active status
     */
    Route::post('admin/voucher/change_status/{id}', 'admin\VoucherController@changeVoucherStatus');
    /*
     * @description Show all purchased deals
     */
    Route::get('admin/deals/{type}', 'admin\VoucherController@allDeals');
    Route::get('admin/deals/purchased/{type}', 'admin\VoucherController@purchasedDeals');
    Route::get('admin/deals_payment', 'admin\VoucherController@dealPayment');
    Route::post('admin/pay_branch_voucher/{branch_id}', 'admin\VoucherController@payBranchForVoucher');
    Route::get('admin/deal_refund_requests/{type}', 'admin\VoucherController@voucherRefundRequests');
    Route::post('admin/accept_deal_refund_request', 'admin\VoucherController@acceptVoucherRefundRequests');
    Route::post('admin/reject_deal_refund_request/{id}', 'admin\VoucherController@rejectVoucherRefundRequests');
    Route::delete('admin/delete_deal_refund_request/{id}', 'admin\VoucherController@deleteVoucherRefundRequests');

    //CSV generate
    Route::get('admin/generate/csv', function () {
        return view('admin.production.generate_csv');
    });
    Route::get('admin/generate/csv/app_version', 'adminController2@generateAppVersionCSV');
    Route::get('admin/generate/csv/email_verified', 'adminController2@generateEmailVerifiedCSV');
    Route::get('admin/generate/csv/email_not_verified', 'adminController2@generateEmailUnverifiedCSV');
    Route::get('admin/generate/csv/profile_completed', 'adminController2@generateProfileCompletedCSV');
    Route::get('admin/generate/csv/profile_not_completed', 'adminController2@generateProfileNotCompletedCSV');

    //donation
    Route::get('admin/donation', 'admin\donationController@allDonations');

    //dynamic links
    Route::resource('admin/dynamic_links', 'admin\DynamicLinkController');
});

//route to view sms send to customers
Route::get('/sms-customer/{type?}', 'adminController@smsExistingCustomers')->middleware('rbdAdminLoginCheck');
//route to view sms send to guests
Route::get('sms-guest', function () {
    return view('admin.production.sms.guest_customers_sms');
});
//route to view sms send to All Customers
Route::get('sms-all-customers', function () {
    return view('admin.production.sms.all_customers_sms');
});
//custom message to All Customers
Route::post('sendAllAdminSMS', 'adminController@sendCustomSMSToAll');
//custom message to a single phone
Route::post('sendAdminSMS', 'adminController@sendCustomSMS');
//route to view sms send to All Partners
Route::get('sms-all-partners', function () {
    return view('admin.production.sms.all_partners_sms');
});
//route to sms existing scanners
Route::get('sms_scanners', 'adminController@smsExistingScanners');
Route::get('admin/sent_message_history/{type}', 'adminController2@sentMessageHistory');

Route::middleware(['rbdAdminLoginCheck'])->group(function () {
    //route to view COD customers
    Route::get('/allCOD/', 'adminController@allCODCustomers');
    //Edit COD Customer requests
    Route::get('/edit-cod-user/{id?}', 'adminController@editCODUser');
    //Route::post('/editCODUserImage', 'adminController@editCODUserImage');
    Route::get('/CODEditDone/{id?}', 'adminController@CODEditDone');
    //route to view COD customers
    Route::get('/tempBuyCard/', 'adminController@allTempCustomers');
    Route::post('/admin/dashboard_user_leaderboard/', 'adminController2@dashboardUserLeaderboard');
    Route::post('/admin/sort_user_scan_leaderboard/', 'admin\AnalyticsController@sortUserLeaderboard');

    //route to view manual registration
    Route::get('/admin/manual_registration', 'adminController2@manualRegistration');
    Route::post('/admin/manual_registration', 'adminController2@storeManualRegistration');

    //route to approve cod and update into customer info
    Route::get('approve-cod/{id}', 'adminController@approveCOD');
    //route to update COD customer ID
    Route::post('update-cod', 'adminController@updateCOD');
    Route::get('card-active-by-admin/{id}', 'adminController@cardActiveByAdmin');

    //route to do manual transaction
    Route::get('manual-transaction', 'adminController2@addManualTransaction');
    Route::post('manual-transaction', 'adminController2@storeManualTransaction');
    Route::post('get-branch', 'adminController2@getBranch');
    Route::post('get-offer', 'adminController2@getOffer');

    //Branch user transaction request
//    Route::get('admin/transaction_requests', 'adminController2@transactionRequests');
//    Route::get(
//        'admin/update_transaction_request/{notification_id}/{request_id}/{branch_user_id}/{status}',
//        'adminController2@update_request_status'
//    );

    Route::get('admin/refer/leader-board', function () {
        return view('admin.production.analytics.refer_leader_board');
    });
    Route::post('admin/refer/leader-board', 'adminController2@allReferLeaderBoard');

     Route::get('admin/transaction_requests', 'adminController2@transactionRequests');
     Route::get('admin/transaction_requests/accepted', 'adminController2@acceptedTransactionRequests');
     Route::get('admin/transaction_requests/declined', 'adminController2@declinedTransactionRequests');
    Route::get(
        'admin/update_transaction_request/{notification_id}/{request_id}/{branch_user_id}/{status}',
        'adminController2@update_request_status'
    );

    //transaction breakdown
    Route::get('admin/scan_analytics', 'adminController2@transactionBreakDown');
    Route::post('admin/merchant_transaction_percentage_analytics', 'admin\AnalyticsController@merchantTransactionPercentageAnalytics');

    Route::get('branch_offer_analytics/{branch_id}', 'adminController2@branchOffersBreakDown');
    //===========================================================================================
    //================================B2B2C clients handling from ADMIN==========================
    //===========================================================================================

    Route::resource('admin/b2b2c-clients', 'b2b2cController');
    Route::get('b2b2c-clients', 'b2b2cController@allClient');
    Route::get('create-b2b2c-client', 'b2b2cController@createClient');
    Route::post('store-b2b2c-client', 'b2b2cController@store');
    Route::get('edit-b2b2c-client/{client_id}', 'b2b2cController@editClient');
    Route::post('update-b2b2c-client/{client_id}', 'b2b2cController@updateClientInfo');
    Route::get('delete-b2b2c-client/{client_id}', 'b2b2cController@deleteClient');
});
//************************* B2B2C clients handling from ADMIN ends *************************

//===========================================================================================
//================================Branch Offers==========================
//===========================================================================================
Route::middleware(['rbdAdminLoginCheck'])->group(function () {
    Route::resource('branch-offers', 'BranchOfferController');
    Route::get('active-offer/{id}', 'BranchOfferController@activateOffer');
    Route::get('deactive-offer/{id}', 'BranchOfferController@deactivateOffer');
    Route::get('add-custom-point/{id}', 'BranchOfferController@addCustomPoint');
    Route::post('store-custom-point', 'BranchOfferController@storeCustomPoint');
    Route::get('edit-custom-point/{id}', 'BranchOfferController@editCustomPoint');
    Route::post('update-custom-point', 'BranchOfferController@updateCustomPoint');
});
//************************************ Branch Offers ends ************************************

//===========================================================================================
//================================Branch Owner==========================
//===========================================================================================
Route::middleware(['rbdAdminLoginCheck'])->group(function () {
    Route::resource('branch-owner', 'admin\BranchOwnerController');
    Route::post('get-branch-owner', 'admin\BranchOwnerController@getBranchOwner');
    Route::post('assign-owner', 'admin\BranchOwnerController@assignOwner');
    Route::get('manage-branch/{id}', 'admin\BranchOwnerController@branches');

    Route::get('deactive-offer/{id}', 'BranchOfferController@deactivateOffer');
    Route::get('add-custom-point/{id}', 'BranchOfferController@addCustomPoint');
    Route::post('store-custom-point', 'BranchOfferController@storeCustomPoint');
    Route::get('edit-custom-point/{id}', 'BranchOfferController@editCustomPoint');
    Route::post('update-custom-point', 'BranchOfferController@updateCustomPoint');
});
//************************************ Branch Owner ends ************************************

//*************** Branch user transaction request */
//Route::middleware(['rbdAdminLoginCheck'])->group(function () {
//    Route::get('admin/transaction_requests', 'adminController2@transactionRequests');
//    Route::get('admin/transaction_requests/accepted', 'adminController2@acceptedTransactionRequests');
//    Route::get('admin/transaction_requests/declined', 'adminController2@declinedTransactionRequests');
//    Route::get(
//        'admin/update_transaction_request/{notification_id}/{request_id}/{branch_user_id}/{status}',
//        'adminController2@update_request_status'
//    );
//});


//*********************************************************************************************************************
//**********************************TEST ROUTES FOR TESTING TEAM*******************************************************
//*********************************************************************************************************************
Route::middleware(['rbdAdminLoginCheck'])->group(function () {
    Route::get('admin/send-user-expiry-notification', '\App\Helpers\UserExpiryNotification@sendNotification');
});
