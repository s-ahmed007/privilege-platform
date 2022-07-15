<?php

namespace App\Http\Controllers\OTP;

use App\CustomerAccount;
use App\CustomerInfo;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Enum\Constants;
use App\Http\Controllers\Enum\ssl_validation_type;
use App\Http\Controllers\Enum\VerificationType;
use App\Http\Controllers\functionController2;
use App\Http\Controllers\JsonControllerV2;
use App\ResetUser;
use App\SslTransactionTable;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;

class functionController extends Controller
{
    public function getRandomPin()
    {
        $digits = 6; // Amount of digits
        return str_pad(rand(0, pow(10, $digits) - 1), $digits, '0', STR_PAD_LEFT);
    }

    public function getPins($sent_value, $verification_type)
    {
        return ResetUser::where('sent_value', $sent_value)->where('used', 0)->where('verification_type', $verification_type)->get();
    }

    public function verifyEmailOTP($email, $pin)
    {
        $email_pins = $this->getPins($email, VerificationType::email_verification);
        if (count($email_pins) > 0) {
            $verify_pin = collect($email_pins)->whereIn('token', (new \App\Http\Controllers\functionController)->encrypt_decrypt('encrypt', $pin))->first();
            if ($verify_pin) {
                ResetUser::where('id', $verify_pin->id)
                    ->update([
                        'used' => 1,
                    ]);
                try {
                    DB::beginTransaction(); //to do query rollback
                    CustomerInfo::where('customer_id', $verify_pin->customer_id)
                        ->update([
                            'email_verified' => 1,
                            'customer_email' => $verify_pin->sent_value,
                        ]);
                    DB::commit(); //to do query rollback
                } catch (\Exception $e) {
                    DB::rollBack();

                    return response()->json(['result' => 'Something went wrong. Please try again later.'], 201);
                }

                return response()->json(['result' => 'Email verified.'], 200);
            } else {
                return response()->json(['result' => 'Invalid verification code.'], 201);
            }
        } else {
            return response()->json(['result' => 'Something went wrong please try again.'], 201);
        }
    }

    public function verifyPhoneOTP($phone, $pin, $verification_type)
    {
        $phone_pins = $this->getPins($phone, $verification_type);
        if (count($phone_pins) > 0) {
            $verify_pin = collect($phone_pins)->whereIn('token', $pin)->first();
            if ($verify_pin) {
                ResetUser::where('id', $verify_pin->id)
                    ->update([
                        'used' => 1,
                    ]);

                return response()->json(['result' => 'Phone number verification successful.'], 200);
            } else {
                return response()->json(['result' => 'Invalid verification code.'], 201);
            }
        } else {
            return response()->json(['result' => 'Something went wrong please try again.'], 201);
        }
    }

    //function to send verification email to verify customer email
    public function sendMailVerification($prev_mail, $email, $customer_id)
    {
        //check if user with this email exists or not
        $user = CustomerAccount::where('customer_id', $customer_id)->with('info')->first();
        $verification_type = VerificationType::email_verification;
        if ((new JsonControllerV2())->mailExist($email, $prev_mail)) {
            return response()->json(['result' => 'Email already exists.'], 201);
        } elseif ($x = (new functionController2())->isVerificationMailSent($email)) {
            $current = Carbon::now();
            $dt = $x->created_at;
            $diff = $dt->diffInMinutes($current);

            return response()->json(['result' => 'We have already sent your verification e-mail. Please check your inbox or other email folders. You will be able to re-send another verification email after '.(Constants::resend_time - $diff).' minutes.'], 201);
        } elseif ($user) {
            //generate reset token
            $email_token = $this->getRandomPin();
            $verification_token = (new \App\Http\Controllers\functionController)->encrypt_decrypt('encrypt', $email_token);
            $this->storeResetUser($user->customer_id, $verification_token, $verification_type, $email);
            try {
                DB::beginTransaction(); //to do query rollback
                CustomerInfo::where('customer_id', $user->customer_id)
                    ->update([
                        'email_verified' => 0,
                        'customer_email' => $email,
                    ]);
                if ($prev_mail != $email) {
                    DB::table('subscribers')->where('email', $prev_mail)->delete();
                }
                DB::commit(); //to do query rollback
            } catch (\Exception $e) {
                DB::rollBack();

                return response()->json(['result' => 'Something went wrong. Please try again.'], 201);
            }
            if ((new functionController2())->sendVerificationEmail($email, $user->info->customer_full_name, $verification_token)) {
                return response()->json(['result' => 'A verification link has been sent to your E-mail. Please check your E-mail to verify.'], 200);
            } else {
                return response()->json(['result' => 'Internal Server Error. Please try again.'], 201);
            }
        } else {
            return response()->json(['result' => 'User does not exist!'], 201);
        }
    }

    public function storeResetUser($customer_id, $token, $verification_type, $sent_value)
    {
        try {
            DB::beginTransaction(); //to do query rollback
            $reset_user = new ResetUser();
            $reset_user->customer_id = $customer_id;
            $reset_user->token = $token;
            $reset_user->verification_type = $verification_type;
            $reset_user->sent_value = $sent_value;
            $reset_user->save();
            DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['result' => 'Internal Server Error. Please try again.'], 201);
        }
    }

    //function to send verification phone to verify customer phone
    public function sendPhoneVerification($phone, $verification_type)
    {
        $pin = $this->getRandomPin();
        //check if user with this email exists or not
        if ($x = (new functionController2())->isVerificationPhoneOTPSent($phone, $verification_type)) {
            $current = Carbon::now();
            $dt = $x->created_at;
            $diff = $dt->diffInMinutes($current);

            return response()->json(['result' => 'We have already sent your verification code. You will be able to re-send another verification code after '.(Constants::resend_time - $diff).' minutes.'], 201);
        } else {
            //generate reset token
            $this->storeResetUser(null, $pin, $verification_type, $phone);
            if ($verification_type == VerificationType::phone_verification) {
                $text_message = 'Use '.$pin.' as your phone number verification code for Royalty.';
            } else {
                $text_message = 'Use '.$pin.' as your spot sale verification code for Royalty.';
            }

            if ((new \App\Http\Controllers\Renew\apiController())->sendSms($phone, $text_message)) {
                if ($verification_type == VerificationType::phone_verification) {
                    $msg = 'A verification code has been sent to your phone.';
                } else {
                    $msg = 'A verification code has been sent to sales agent\'s phone.';
                }

                return response()->json(['result' => $msg], 200);
            } else {
                return response()->json(['result' => 'Unable to send the verification code. Please try again!'], 201);
            }
        }
    }
}
