<?php

namespace App\Http\Controllers\LoginRegister;

use App\CustomerAccount;
use App\CustomerInfo;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Enum\Constants;
use App\Http\Controllers\Enum\PlatformType;
use App\Http\Controllers\functionController;
use App\Http\Controllers\functionController2;
use App\Http\Controllers\jsonController;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\LoginRegister\functionController as loginFunctionController;

class apiController extends Controller
{
    public function getCustomer(Request $request)
    {
        $phone = $request->post('phone');
        $customer = (new loginFunctionController())->getCustomer($phone);
        $result = [];
        if ($customer) {
            if ($customer->account->isSuspended == 1) {
                $result['status'] = 'suspended';
                $result['message'] = 'Your account is suspended. Please contact our customer support at
                 support@royaltybd.com or call us at +880–963-862-0202';
            } elseif ($customer->account->moderator_status == 1) {
                $result['status'] = 'deactivated';
                $result['message'] = 'Your account has been deactivated as your card maybe lost/damaged. Please contact
                 our customer support at support@royaltybd.com or call us at +880-963-862-0202.';
            } else {
                $result['status'] = 'active';
                $result['message'] = 'User Found';
            }
            return response()->json($result, 200);
        } else {
            return response()->json(null, 404);
        }
    }

    public function createStats(Request $request)
    {
        $phone = $request->post('phone');

        return response()->json((new loginFunctionController())->setStatNumber($phone), 200);
    }

    public function updateStats(Request $request)
    {
        $id = $request->post('id');
        $status = $request->post('status');
        $phone = $request->post('phone');

        return response()->json((new loginFunctionController())->updateStatNumber($id, $status, $phone), 200);
    }

    public function getPin(Request $request)
    {
        $customerID = JWTAuth::toUser(JWTAuth::getToken())->customer_id;

        return response()->json(['pin' => (new loginFunctionController())->getPin($customerID)], 200);
    }

    public function setPin(Request $request)
    {
        $customerID = JWTAuth::toUser(JWTAuth::getToken())->customer_id;
        $pin = $request->post('pin');

        return response()->json(['pin' => (new loginFunctionController())->setPin($customerID, $pin)], 200);
    }

    public function matchCredential(Request $request)
    {
        $form_data = $request->all();

        if (!isset($form_data['phone']) || !isset($form_data['type']) || !isset($form_data['value'])) {
            return response()->json('All fields are required', 400);
        }
        $phone = $form_data['phone'];
        $type = $form_data['type'];
        $value = $form_data['value'];
        $user = CustomerInfo::where('customer_contact_number', $phone)->first();
        if ($user) {
            $customer_id = $user->customer_id;
        } else {
            return response()->json("User not found", 400);
        }

        $valid = (new loginFunctionController())->matchCustomer($customer_id, $type, $value);
        if ($valid) {
            $customer = (new loginFunctionController())->getCustomer($phone);
            $customer['token'] = JWTAuth::fromUser($customer);
            return response()->json($customer, 200);
        } else {
            return response()->json($valid, 400);
        }
    }

    public function createCustomer(Request $request)
    {
        $platform = $request->header('platform', null);
        $name = $request->post('name');
        $email = $request->post('email');
        $phone = $request->post('phone');
        $pin = null;
        $refer_code = $request->post('refer_code');
        if ((new jsonController)->emailExist($email)) {
            return response()->json(['message' => 'Email already exists.'], 406);
        } elseif ((new jsonController)->phoneNumberExist($phone)) {
            return response()->json(['message' => 'Phone number already exists.'], 406);
        } elseif ($refer_code && ! (new loginFunctionController())->getReferrerWithCode($refer_code)) {
            return response()->json(['message' => 'Invalid refer code.'], 406);
        } else {
            $customer = (new loginFunctionController())
                ->register($name, $email, $phone, $pin, $platform, (new loginFunctionController())
                    ->getReferrerWithCode($refer_code));
            $customer['token'] = JWTAuth::fromUser($customer);
            return response()->json($customer, 200);
        }
    }

    public function resetPinEmail(Request $request)
    {
        $email = $request->post('email');

        return response()->json(['result' => (new loginFunctionController())->send_reset_pin_email($email)]);
    }

    public function resetPinPhone(Request $request)
    {
        $phone = $request->post('phone');
        if ($x = (new functionController2())->isResetSMSSent($phone)) {
            $current = Carbon::now();
            $dt = $x->created_at;
            $diff = $dt->diffInMinutes($current);

            return response()->json(['result' => 'You have already requested for pin reset. You can request for another one in next '.
                (Constants::resend_time - $diff).' minutes.', ], 200);
        } else {
            return response()->json(['result' => (new loginFunctionController())->send_reset_pin_sms($phone)]);
        }
    }

    public function checkPinResetOTP(Request $request)
    {
        if (empty($request->otp) || !is_numeric($request->otp) || strlen($request->otp) <> 6) {
            return response()->json(['result' => 'Please provide a valid code.'], 400);
        }
        $exists = (new loginFunctionController())->checkOTP($request->phone, $request->otp);
        if ($exists) {
            return response()->json(['result' => $exists], 200);
        } else {
            return response()->json(['result' => 'Code did not match. Please try again.'], 400);
        }
    }

    public function storeNewPin(Request $request)
    {
        if (empty($request->pin) || !is_numeric($request->pin) || strlen($request->pin) <> 4) {
            return response()->json(['result' => 'Please provide a valid PIN.'], 400);
        }

        $data = $request->all();
        $customer_id = $data['customer_id'];
        $pin = $data['pin'];
        $token = $data['token'];

        $encrypted_pin = (new functionController)->encrypt_decrypt('encrypt', $pin);
        if ((new loginFunctionController())->storeNewPin($customer_id, $encrypted_pin, $token)) {
            return response()->json(['result' => 'PIN set successfully'], 200);
        }
        return response()->json(['result' => 'Something went wrong. Please try again.'], 400);
    }
}
