<?php

namespace App\Http\Controllers\Enum;

abstract class AdminNotificationType
{
    const membership_purchased = 1;
    const activated_free_trial = 2;
    const buy_card_attempt = 3;
    const new_partner_added = 4;
    const new_branch_added = 5;
    const new_offer_added = 6;
    const new_area_added = 7;
    const partner_status_changed = 8;
    const new_transaction = 9;
    const new_transaction_request = 10;
    const manual_transaction = 11;
    const new_scanner_request = 12;
    const card_sold_from_sales_app = 13;
    const user_review = 14;
    const rbd_reward_added = 15;
    const new_partner_request = 16;
    const user_wish = 17;
    const new_influencer_request = 18;
    const new_contact = 19;
    const rbd_reward_request = 20;
    const partner_reward_added = 21;
    const renew_membership = 22;
    const renew_attempt = 23;
    const transaction_request_reject = 24;
    const new_user_added = 25;
    const partner_post_added = 26;
    const edit_partner_post = 27;
    const delete_partner_post = 28;
    const partner_offer_request = 29;
    const branch_status_changed = 30;
    const scanner_prize_req_accept = 31;
    const review_under_moderation = 32;
    const partner_expiry_notification = 33;
    const new_donation_added = 34;
    const new_voucher_purchased = 35;
    const new_voucher_added = 36;
    const new_voucher_refund_request = 37;
    const voucher_refund_request_accept = 38;
    const review_reply_notification = 39;
}
