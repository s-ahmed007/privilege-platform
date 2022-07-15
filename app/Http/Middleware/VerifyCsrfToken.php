<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'login.json',
        'customer_data.json',
        'send_activation_code.json',
        'card_activate.json',
        'failed_ssl_info.json',
        'subscribe.json',
        'unsubscribe.json',
        'make_wish.json',
        'v2/branch_login.json',
        'v2/branch_payment_status.json',
        'v2/partner_branch_list.json',
        'v2/checkCardPromo.json',
        'v2/toFilterPartners.json',
        'v2/verify_email.json',
        'v2/news_feed.json',
        'v2/like_post.json',
        'v2/unlike_post.json',
        'v2/create_user.json',
        'v2/update_gender.json',
        'v2/update_dob.json',
        'v2/update_image.json',
        'v2/partner_profile.json',
        'v2/branch_transaction_history.json',
        'v2/insert_ssl_info.json',
        'v2/check_user.json',
        'v2/bill_calculate.json',
        'v2/confirm_bill.json',
        'v2/partner_branch_account.json',
        'v2/user_profile.json',
        'v2/user_reviews.json',
        'v2/user_transactions.json',
        'v2/user_requested_coupons.json',
        'v2/user_visited_list.json',
        'v2/user_social_login.json',
        'v2/user_login.json',
        'v2/partner.json',
        'v2/nearby_partners.json',
        'v2/partner_reviews.json',
        'v2/partner_discounts.json',
        'v2/partner_gallery.json',
        'v2/partner_menu.json',
        'v2/check_phone.json',
        'v2/connect_with_social.json',
        'follow_partner.json',
        'unfollow_partner.json',
        'edit_customer_data.json',
        'customer_notifications.json',
        'select_coupon.json',
        'registration.json',
        'follow_customer.json',
        'unfollow_customer.json',
        'liked_notification.json',
        'make_review.json',
        'customer_transaction_sort.json',
        'like_review.json',
        'unlike_review.json',
        'delete_review.json',
        'single_review.json',
        'single_liked_review.json',
        'customer_reviews.json',
        'accept_follow_request.json',
        'ignore_follow_request.json',
        'birthday_coupon.json',
        'image_upload.json',
        'seen_notification.json',
        'social_login.json',
        'save_ftoken.json',
        'send_ssl_info.json',
        'update_ssl_info.json',
        'cancel_follow_request.json',
        'reset_password_email.json',
        'partner_notifications.json',
        'partner_seen_notification.json',
        'notification_liked_post.json',
        'partner_transaction_history.json',
        'partner_followers.json',
        'partner_reviews.json',
        'partner_reply.json',
        'check_user.json',
        'bill_calculate.json',
        'confirm_bill.json',
        'partner_account.json',
        'custom_text.json',
        'payment-check',
        'pre-payment-check',
        'category_facilities',
        'payment_success',
        'payment_fail',
        'payment_cancel',
        'deal_success',
        'deal_fail',
        'deal_cancel',
        'donation_success',
        'donation_fail',
        'donation_cancel',
        'renew_fail',
        'renew_cancel'

    ];
}
