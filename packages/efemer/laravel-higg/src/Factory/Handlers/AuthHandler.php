<?php

namespace Efemer\Higg\Factory\Handlers;

use Auth;

class AuthHandler {

    public $user;

    function getUser($where = null){
        if (!empty($where)) {
            return app('user')->findOne($where);
        } else {
            return app('user');
        }
    }

    function getIdentity($where = null){
        if (!empty($where)) {
            return app('identity')->findOne($where);
        } else {
            return app('identity');
        }
    }

    function login($data){

        if (isset($data['username'])) {
            $this->loginByUsername($data);
        } else if (isset($data['email'])) {
            $this->loginByIdentity( 'email', $data);
        } else if (isset($data['mobile'])) {
            $this->loginByIdentity( 'mobile', $data);
        }

        return Auth::check();
    }

    function loginByUsername($data){

        if (isset($data['password'])) $data['secret'] = $data['password'];

        $validate = [ 'username' => 'required', 'secret' => 'required' ];
        $user = $this->getUser();
        if($user->isValid($validate, $data)) {
            $user = $this->getUser(['username' => $data['username']]);
            if ($this->canLogin($user)) {
                if ($user->matchSecret($data['secret'])) {

                    if ($user->doTwoFactorAuth()) {
                        // @todo implement 2FA for login
                        // $this->issueTwoFactorAuth();
                    }
                    higg()->success('Great! you are in!');
                    return Auth::loginUsingId($user->id);

                } else {
                    higg()->error('Something is wrong with your credentials.');
                }
            } else {
                higg()->error($user->error());
            }

        } else {
            higg()->error($user->error());
        }
        return false;
    }

    function loginByIdentity( $pipeline, $data ){

        if (isset($data['password'])) $data['secret'] = $data['password'];

        $secret = array_get($data, 'secret');
        $identifier = array_get($data, $pipeline);

        $identity = $this->getIdentity( [ 'pipeline' => $pipeline, 'identifier' => $identifier ] );
        if (empty($identity)) {
            higg()->error('Something is wrong with your credentials.');
            return false;
        }

        $user = $identity->getUser();
        if ($this->canLogin($user)) {
            if ($this->canLogin($identity)) {
                if ($identity->matchSecret($secret)) {

                    if ($user->doTwoFactorAuth()) {
                        // @todo implement 2FA for login
                        // $this->issuedoTwoFactorAuth();
                    }
                    higg()->success('Welcome ' . $user->displayName());
                    return Auth::loginUsingId($user->id);

                } else {
                    higg()->error('Something is wrong with your credentials.');
                }
            } else {
                higg()->error($identity->error());
            }
        } else {
            higg()->error($user->error());
        }

        return false;
    }

    function issuedoTwoFactorAuth(){
        higg()->success('Please verify second factor.');
    }

    function canLogin($model){
        if (empty($model)) {
            higg()->error('Something is wrong with your credentials.');
        } else {
            return $model->canLogin();
        }
        return false;
    }

    function parseIdentifierType($identifier){
        if (strpos($identifier, '@') !== FALSE) return 'email';
        else if (is_numeric($identifier) !== FALSE) return 'mobile';
        else return 'username';
    }

    function isGuarded($clause){

        switch($clause) {
            case 'auth':
                if (!isLogged()) return true;
                break;
        }

        return false;
    }

    function logout(){
        Auth::logout();
        return true;
    }

}
