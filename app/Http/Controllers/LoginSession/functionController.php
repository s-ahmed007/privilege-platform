<?php

namespace App\Http\Controllers\LoginSession;

use App\CustomerLoginSession;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Enum\LoginStatus;
use App\Http\Controllers\Enum\PlatformType;
use Illuminate\Http\Request;

class functionController extends Controller
{
    public function store($customer_id, $platform, $physical_address, $ip_address, $status)
    {
        $login = null;
        $exist = CustomerLoginSession::where('customer_id', $customer_id)->where('status', LoginStatus::logged_in)
            ->orderBy('id', 'DESC')->first();
        if ($exist) {
            if ($exist->platform != PlatformType::web) {
                if ($exist->physical_address != $physical_address) {
                    if ($exist->status == LoginStatus::logged_in && $status == LoginStatus::logged_in) {
                        $this->saveSession(
                            $customer_id,
                            $exist->platform,
                            $exist->physical_address,
                            $exist->ip_address,
                            LoginStatus::kicked
                        );
                        $login = $this->saveSession($customer_id, $platform, $physical_address, $ip_address, $status);
                    } else {
                        $login = $this->saveSession($customer_id, $platform, $physical_address, $ip_address, $status);
                    }
                } else {
                    $login = $this->saveSession($customer_id, $platform, $physical_address, $ip_address, $status);
                }
            } else {
                $login = $this->saveSession($customer_id, $platform, $physical_address, $ip_address, $status);
            }
        } else {
            $login = $this->saveSession($customer_id, $platform, $physical_address, $ip_address, $status);
        }

        return $login;
    }

    public function saveSession($customer_id, $platform, $physical_address, $ip_address, $status)
    {
        $login = new CustomerLoginSession();
        $login->customer_id = $customer_id;
        $login->platform = $platform;
        $login->physical_address = $physical_address;
        $login->ip_address = $ip_address;
        $login->status = $status;
        $login->save();

        return $login;
    }

    public function checkSession($customer_id, $platform, $physical_address, $ip_address, $version = null)
    {
        (new \App\Http\Controllers\ActivitySession\functionController())
            ->saveSession($customer_id, $platform, $physical_address, $ip_address, $version);
        $exist = CustomerLoginSession::where('customer_id', $customer_id)
            ->where('platform', $platform)
            ->where('physical_address', $physical_address)
            ->orderBy('id', 'DESC')->first();
        if (! $exist) {
            return $this->store($customer_id, $platform, $physical_address, $ip_address, LoginStatus::logged_in);
        } else {
            return $exist;
        }
    }
}
