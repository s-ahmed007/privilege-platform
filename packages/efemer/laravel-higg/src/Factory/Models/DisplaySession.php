<?php

namespace Efemer\Higg\Factory\Models;

use Efemer\Higg\Factory\Core\RedisStore;
use Higg;
use Session;
use Request;
use Jenssegers\Agent\Agent;
use Efemer\Higg\Factory\Core\HiggModel;

class DisplaySession extends HiggModel {

    public $table = 'sys_display_sessions';

    public $fields = [

        'session_id' => [ 'label' => 'Session ID', 'validations' => 'required' ],
        'session_day' => [ 'label' => 'Date', 'cast' => CAST_ARRAY ],
        'user_id' => [ 'label' => 'User ID' ],
        'auth_token' => [ 'label' => 'Auth Token' ],
        'user_agent' => [ 'label' => 'User-agent' ],

        'user_device' => [ 'label' => 'Device' ],
        'user_form_factor' => [ 'label' => 'Device Form Factor' ],
        'user_platform' => [ 'label' => 'Platform' ],
        'user_platform_v' => [ 'label' => 'Platform Version' ],
        'user_language' => [ 'label' => 'Language' ],
        'user_browser' => [ 'label' => 'Browser' ],
        'user_browser_v' => [ 'label' => 'Browser Version' ],

        'user_demographic' => [ 'label' => 'Demographic', 'cast' => CAST_ARRAY ],
        'user_demographic.age_group' => [ 'label' => 'Age Group' ],
        'user_demographic.gender' => [ 'label' => 'Gender' ],

        'user_location' => [ 'label' => 'Location', 'cast' => CAST_ARRAY ],
        'user_location.country' => [ 'label' => 'Country' ],
        'user_location.state' => [ 'label' => 'State' ],
        'user_location.city' => [ 'label' => 'City' ],
        'user_location.street' => [ 'label' => 'Street' ],
        'user_location.latlng' => [ 'label' => 'Latlng' ],

        'user_tags' => [ 'label' => 'Tags' ],
        'http_ref' => [ 'label' => 'HTTP Ref' ],
        'user_ip' => [ 'label' => 'User IP Address' ],

        'params' => [ 'cast' => CAST_ARRAY ],
        'params.req_url' => [ 'label' => 'Request Url' ],

    ];

    function register(){

        if ($this->isKnown()) {
            $data = $this->cacheStore($this->cacheKey());
            $this->silentAssign($data);
            return $this;
        }

        $data = $this->sessionUser();
        $existing = $this->sessionUserExists();
        $session = array_merge($data, $existing);

        $new_session = new DisplaySession();
        $new_session->assign($session);
        $new_session->cacheStore();

        if (!$new_session->error()) {
            $done = $new_session->store();
            if ($done) {
                $new_session->cacheStore();
                return $new_session;
            }
            abort( HTTP_SERVICE_UNAVAILABLE, 'DISPLAY SESSION FAILED TO REGISTER!');
        }
        abort( HTTP_SERVICE_UNAVAILABLE, 'DISPLAY SESSION DATA INVALID!');
    }

    function cacheKey(){
        $date = str_replace('-', '', $this->sessionDay());
        $key = "session::".$this->sessionId();
        return $key;
    }

    function isKnown(){
        $redis = new RedisStore();
        $key = $this->cacheKey();
        return $redis->exists($key);
    }

    function cacheStore($key = null){
        $redis = new RedisStore();
        if (!is_null($key)) {
            return $redis->json($key);
        }
        $data = $this->getData();
        $key = $this->cacheKey();
        return $redis->json($key, $data);
    }

    function sessionDay(){
        return $this->getValue('session_day');
    }

    function sessionUserExists(){
        $session_id = $this->sessionId();
        $session = $this->getObject( [ 'session_id' => $session_id ] );
        if (!empty($session)) {
            return $session->getData();
        }
        return [];
    }

    function sessionId(){
        return Session::getId();
    }

    function sessionUser(){
        $agent = new Agent();
        $user = isLogged() ? Higg::user() : null;

        $device_form_factor = '';
        if ($agent->isDesktop()) $device_form_factor = 'desktop';
        if ($agent->isMobile()) $device_form_factor = 'mobile';
        if ($agent->isTablet()) $device_form_factor = 'tablet';

        $session_id = $this->sessionId();
        $languages = $agent->languages();
        if (is_array($languages)) $languages = implode(',', $languages);

        $data = [
            'session_id' => $session_id,
            'session_day' => Higg::date(),
            'user_id' => $user ? $user->primaryId() : '',
            'auth_token' => '',

            'user_agent' => Request::server('HTTP_USER_AGENT'),
            'user_device' => $agent->device(),
            'user_form_factor' => $device_form_factor,
            'user_platform' => $agent->platform(),
            'user_platform_v' => $agent->version($agent->platform()),
            'user_language' => $languages,
            'user_browser' => $agent->browser(),
            'user_browser_v' => $agent->version($agent->browser()),

            'user_demographic' => [
                'age_group' => '',
                'gender' => '',
            ],
            'user_location' => [
                'country' => '',
                'state' => '',
                'city' => '',
                'street' => '',
                'latlng' => '',
            ],

            'user_tags' => '',
            'http_ref' => Request::server('HTTP_REFERER'),
            'user_ip' => Request::ip(),

            'params' => [
                'req_url' => Request::fullUrl(),
            ]

        ];
        return $data;
    }


    function browser(){
        return $this->getValue('user_browser');
    }

    function ip(){
        return $this->getValue('user_ip');
    }


}
