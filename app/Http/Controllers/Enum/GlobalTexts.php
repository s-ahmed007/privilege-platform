<?php
/**
 * Created by PhpStorm.
 * User: Sohel
 * Date: 5/19/2018
 * Time: 12:08 PM.
 */

namespace App\Http\Controllers\Enum;

abstract class GlobalTexts
{
    //show when a customer reaches to the daily based offer limit in the application.
    const offer_wise_transaction_error = 'You cannot avail the same offer more than once in a day at a particular outlet. You can avail other offers if available at this outlet.';
    const pending_transaction_error = 'Your previous request is pending. Please ask the partner to accept your request.';
    const already_requested_transaction = 'The offer request is already pending. Please check the notification page.';
    const deactivated_partner_no_review = 'This partner is currently not available.';
    const reward_required_field_phone_txt = 'Please enter your phone';
    const reward_required_field_email_txt = 'Please enter your email';
    const reward_required_field_del_add_txt = 'Please enter your delivery address';

    //merchant expiry and deactivation issue
    const partner_account_expired_login_msg = 'Your contract has expired, to renew please contact Royalty Merchant Support Team (+8801312620202)';
    const merchant_end_deactivated_msg = 'Your account is deactivated. To activate please contact Royalty Merchant Support Team (+8801312620202)';

    //review moderation text
    const review_moderation_text = 'Your review is now under moderation. After moderation, it will be posted on the partner profile.';
    const review_submitted_text = 'Thank you for your rating.';
    const scanner_pin_error = 'Incorrect pin.';
}
