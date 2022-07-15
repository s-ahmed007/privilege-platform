<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatingIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('all_amounts', function (Blueprint $table) {
            $table->index('type');
        });

        Schema::table('all_coupons', function (Blueprint $table) {
            $table->index('branch_id');
            $table->index('coupon_type');
        });

        Schema::table('blog_post', function (Blueprint $table) {
            $table->index('category_id');
        });

        Schema::table('bonus_request', function (Blueprint $table) {
            $table->index('customer_id');
            $table->index('used');
            $table->index('expiry_date');
        });

        Schema::table('branch_credit_redeemed', function (Blueprint $table) {
            $table->index('branch_id');
        });

        Schema::table('branch_offers', function (Blueprint $table) {
            $table->index('branch_id');
            $table->index('active');
            $table->index('selling_point');
        });

        Schema::table('branch_scanner', function (Blueprint $table) {
            $table->index('branch_id');
            $table->index('branch_user_id');
        });

        Schema::table('branch_user', function (Blueprint $table) {
            $table->index('phone');
            $table->index('f_token');
            $table->index('pin_code');
        });

        Schema::table('branch_user_notification', function (Blueprint $table) {
            $table->index('branch_user_id');
            $table->index('customer_id');
            $table->index('notification_type');
            $table->index('source_id');
            $table->index('seen');
        });

        Schema::table('branch_vouchers', function (Blueprint $table) {
            $table->index('branch_id');
            $table->index('active');
        });

        Schema::table('card_prices', function (Blueprint $table) {
            $table->index('platform');
            $table->index('type');
            $table->index('month');
        });

        Schema::table('card_promo', function (Blueprint $table) {
            $table->index('code');
            $table->index('influencer_id');
        });

        Schema::table('card_seller_account', function (Blueprint $table) {
            $table->index('phone');
        });

        Schema::table('card_seller_info', function (Blueprint $table) {
            $table->index('seller_account_id');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->index('type');
        });

        Schema::table('category_relation', function (Blueprint $table) {
            $table->index('main_cat');
            $table->index('sub_cat_1_id');
        });

        Schema::table('customer_account', function (Blueprint $table) {
            $table->index('customer_id');
            $table->index('customer_username');
            $table->index('pin');
            $table->index('moderator_status');
            $table->index('platform');
        });

        Schema::table('customer_activity_sessions', function (Blueprint $table) {
            $table->index('customer_id');
            $table->index('platform');
            $table->index('created_at');
            $table->index('version');
        });

        Schema::table('customer_card_promo_usage', function (Blueprint $table) {
            $table->index('customer_id');
            $table->index('promo_id');
        });

        Schema::table('customer_history', function (Blueprint $table) {
            $table->index('customer_id');
            $table->index('seller_id');
            $table->index('type');
            $table->index('created_at');
        });

        Schema::table('customer_info', function (Blueprint $table) {
            $table->index('customer_id');
            $table->index('customer_email');
            $table->index('customer_contact_number');
            $table->index('customer_type');
            $table->index('expiry_date');
            $table->index('delivery_status');
        });

        Schema::table('customer_login_sessions', function (Blueprint $table) {
            $table->index('customer_id');
            $table->index('platform');
            $table->index('physical_address');
            $table->index('status');
        });

        Schema::table('customer_miscellaneous', function (Blueprint $table) {
            $table->index('customer_id');
            $table->index('miscellaneous_id');
        });

        Schema::table('customer_notification', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('notification_type');
            $table->index('source_id');
            $table->index('seen');
            $table->index('posted_on');
        });

        Schema::table('customer_points', function (Blueprint $table) {
            $table->index('customer_id');
            $table->index('point_type');
            $table->index('source_id');
            $table->index('created_at');
        });

        Schema::table('customer_reward', function (Blueprint $table) {
            $table->index('customer_id');
        });

        Schema::table('customer_reward_redeems', function (Blueprint $table) {
            $table->index('offer_id');
            $table->index('customer_id');
            $table->index('used');
            $table->index('created_at');
        });

        Schema::table('customer_transaction_request', function (Blueprint $table) {
            $table->index('offer_id');
            $table->index('customer_id');
            $table->index('status');
            $table->index('posted_on');
        });

        Schema::table('featured_deals', function (Blueprint $table) {
            $table->index('partner_account_id');
            $table->index('category_id');
        });

        Schema::table('info_at_buy_card', function (Blueprint $table) {
            $table->index('customer_id');
            $table->index('tran_id');
            $table->index('customer_username');
            $table->index('customer_full_name');
            $table->index('customer_email');
            $table->index('customer_contact_number');
            $table->index('delivery_type');
        });

        Schema::table('leaderboard_prizes', function (Blueprint $table) {
            $table->index('month');
        });

        Schema::table('openings', function (Blueprint $table) {
            $table->index('position');
            $table->index('active');
        });

        Schema::table('opening_hours', function (Blueprint $table) {
            $table->index('branch_id');
        });

        Schema::table('partner_account', function (Blueprint $table) {
            $table->index('active');
        });

        Schema::table('partner_branch', function (Blueprint $table) {
            $table->index('username');
            $table->index('partner_account_id');
            $table->index('partner_area');
            $table->index('main_branch');
            $table->index('active');
        });

        Schema::table('partner_facilities', function (Blueprint $table) {
            $table->index('branch_id');
        });

        Schema::table('partner_gallery_images', function (Blueprint $table) {
            $table->index('partner_account_id');
            $table->index('pinned');
        });

        Schema::table('partner_info', function (Blueprint $table) {
            $table->index('partner_account_id');
            $table->index('partner_name');
        });

        Schema::table('partner_menu_images', function (Blueprint $table) {
            $table->index('partner_account_id');
        });

        Schema::table('partner_notification', function (Blueprint $table) {
            $table->index('partner_account_id');
            $table->index('notification_type');
            $table->index('source_id');
            $table->index('seen');
            $table->index('posted_on');
        });

        Schema::table('partner_profile_images', function (Blueprint $table) {
            $table->index('partner_account_id');
        });

        Schema::table('post', function (Blueprint $table) {
            $table->index('poster_id');
            $table->index('moderate_status');
            $table->index('poster_type');
            $table->index('pinned_post');
            $table->index('scheduled_at');
        });

        Schema::table('rating', function (Blueprint $table) {
            $table->index('partner_account_id');
        });

        Schema::table('rbd_coupon_payment', function (Blueprint $table) {
            $table->index('branch_id');
        });

        Schema::table('rbd_influencer_payment', function (Blueprint $table) {
            $table->index('influencer_id');
        });

        Schema::table('rbd_statistics', function (Blueprint $table) {
            $table->index('customer_id');
            $table->index('partner_id');
            $table->index('visited_on');
        });

        Schema::table('reset_user', function (Blueprint $table) {
            $table->index('customer_id');
            $table->index('token');
            $table->index('used');
            $table->index('verification_type');
            $table->index('sent_value');
        });

        Schema::table('review', function (Blueprint $table) {
            $table->index('partner_account_id');
            $table->index('customer_id');
            $table->index('rating');
            $table->index('posted_on');
            $table->index('admin_id');
            $table->index('moderation_status');
        });

        Schema::table('review_comment', function (Blueprint $table) {
            $table->index('review_id');
            $table->index('deleted_at');
            $table->index('moderation_status');
        });

        Schema::table('scanner_prize_history', function (Blueprint $table) {
            $table->index('scanner_id');
        });

        Schema::table('scanner_reward', function (Blueprint $table) {
            $table->index('scanner_id');
        });

        Schema::table('search_stats', function (Blueprint $table) {
            $table->index('customer_id');
            $table->index('branch_id');
            $table->index('created_at');
        });

        Schema::table('seller_balance', function (Blueprint $table) {
            $table->index('seller_id');
        });

        Schema::table('seller_commission_history', function (Blueprint $table) {
            $table->index('seller_id');
        });

        Schema::table('seller_credit_redeemed', function (Blueprint $table) {
            $table->index('seller_account_id');
        });

        Schema::table('sent_message_history', function (Blueprint $table) {
            $table->index('type');
            $table->index('scheduled_at');
            $table->index('sent');
        });

        Schema::table('share_post', function (Blueprint $table) {
            $table->index('sharer_id');
        });

        Schema::table('ssl_transaction_table', function (Blueprint $table) {
            $table->index('customer_id');
            $table->index('status');
            $table->index('tran_date');
            $table->index('tran_id');
            $table->index('amount');
        });

        Schema::table('tnc_for_partner', function (Blueprint $table) {
            $table->index('partner_account_id');
        });

        Schema::table('top_brands', function (Blueprint $table) {
            $table->index('partner_account_id');
        });

        Schema::table('transaction_table', function (Blueprint $table) {
            $table->index('branch_id');
            $table->index('customer_id');
            $table->index('posted_on');
            $table->index('deleted_at');
        });

        Schema::table('trending_offers', function (Blueprint $table) {
            $table->index('partner_account_id');
        });

        Schema::table('voucher_history', function (Blueprint $table) {
            $table->index('customer_id');
            $table->index('branch_id');
            $table->index('ssl_id');
            $table->index('order_id');
        });

        Schema::table('voucher_payments', function (Blueprint $table) {
            $table->index('branch_id');
        });

        Schema::table('voucher_purchase_details', function (Blueprint $table) {
            $table->index('voucher_id');
            $table->index('ssl_id');
            $table->index('review_id');
        });

        Schema::table('voucher_ssl_info', function (Blueprint $table) {
            $table->index('customer_id');
            $table->index('status');
            $table->index('tran_date');
            $table->index('amount');
        });

        Schema::table('wish', function (Blueprint $table) {
            $table->index('customer_id');
            $table->index('partner_request_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
