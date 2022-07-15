<?php

namespace Efemer\Higg\Factory\Models;

use Hash;
use Higg;
use Efemer\Higg\Factory\Core\HiggModel;
use Efemer\Higg\Factory\Handlers\FormHandler;

class User extends HiggModel {

    public $alias = 'higg.user';
    protected $table = 'users';

    protected $fields = [

        'id' => [ 'label' => 'User ID' ],
        'user_type' => [ 'label' => 'User Type', 'default' => 'customer', 'validations' => 'required' ],
        'full_name' => [ 'label' => 'Name', 'validations' => 'required|min:3', 'order' => 1],

        'username' => [ 'label' => 'Username', 'readonly' => false, 'validations' => 'required|min:3|max:100' ],
        'primary_email' => [ 'label' => 'Email', 'readonly' => false ],
        'primary_mobile' => [ 'label' => 'Mobile', 'readonly' => false ],
        'account_code' => [ 'label' => 'Account Code' ],
        'user_roles' => [ 'label' => 'Roles' ],
        'photo_url' => [ 'label' => 'URL' ],

        'details' => [ 'cast' => CAST_ARRAY ],
        'details.first_name' => [ 'label' => 'First name' ],
        'details.last_name' => [ 'label' => 'Last name' ],
        'details.title' => [ 'label' => 'Title' ],
        'details.gender' => [ 'label' => 'Title' ],
        'details.date_of_birth' => [ 'label' => 'Title', 'cast' => CAST_DATE ],
        'details.avatar_url' => [ 'label' => 'Avatar', 'cast' => CAST_STRING ],
        // 'details.avatarFileId' => [ 'label' => 'Avatar File ID', 'cast' => CAST_STRING ],
        'details.country_code' => [ 'label' => 'Country COde', 'cast' => CAST_STRING ],
        'details.dial_prefix' => [ 'label' => 'Dial Prefix', 'cast' => CAST_STRING ],

        // 'details.primary_email' => [ 'label' => 'Primary Email', 'validations' => 'required|email' ],
        // 'details.optional_email' => [ 'label' => 'Optional Email', 'validations' => 'email' ],
        // 'details.primary_mobile' => [ 'label' => 'Mobile Number' ],

        // 'details.location_country' => [ 'label' => 'Country' ],
        // 'details.location_state' => [ 'label' => 'State' ],
        // 'details.location_city' => [ 'label' => 'City' ],
        // 'details.ref_code' => [ 'label' => 'Reference Code' ],

        'details.website' => [ 'label' => 'Website' ],
        'details.twitter' => [ 'label' => 'Twitter Handle' ],
        'details.facebook' => [ 'label' => 'Facebook Page' ],

        'params' => [ 'cast' => CAST_ARRAY ],
        'params.tags' => [ 'label' => 'Account Tags'],
        'params.verified_on' => [ 'label' => 'Verified On'],

        'settings' => [ 'cast' => CAST_ARRAY ],
        'settings.master_password' => [ 'label' => 'User master password for all login' ],

        'verified' => [ 'cast' => CAST_BOOLEAN, 'validations' => 'required', 'default' => 0 ],
        'status' => [ 'cast' => CAST_STRING, 'validations' => 'required', 'default' => 'inactive' ],

        'secret' => [  'cast' => CAST_PASSWORD, 'label' => 'Secret', 'readonly' => true, 'validations' => 'min:3|max:50' ],

        'access_token' => [ 'label' => 'Token' ],
        'remember_token' => [ 'label' => 'Remember' ],
        'reset_token' => [ 'label' => 'Reset' ],
        'uniqid' => [ 'label' => 'uniqid' ],

        'created_at' => [ 'cast' => CAST_DATETIME ],
        'updated_at' => [ 'cast' => CAST_DATETIME ],

    ];


    protected $browser = [
        'fields' => [
            'id' => [ 'hidden' => true ],
            'full_name' => [ 'label' => 'Name', 'class' => 'highlight' ],
            'account_code' => [ 'label' => 'Scope' ],
            'primary_email' => [ 'label' => 'Email' ],
            'status' => [ 'label' => 'Status' ],
            'created_at' => [ 'label' => 'Since' ]
        ],
        'actions' => [
            [ 'label' => 'Account', 'icon' => 'icon-user', 'routerLink' => 'admin/accounts/edit', 'routeParams' => ['id'] ],
            [ 'label' => 'Contact', 'icon' => 'icon-pointer', 'routerLink' => 'admin/accounts/contacts', 'routeParams' => ['id'] ],
        ]
    ];

/*
    protected $forms = [
        'account-form' => [
            'controls' => [
                [ 'key' => 'username' ],
                [ 'key' => 'account_code' ],
                [ 'key' => 'full_name', 'order' => 3, 'inputGroup' => 'input-group', 'button' => 'Click!', 'label' => 'Display Name' ],
                [ 'key' => 'params.primary_email', 'order' => 2 ],
                [ 'key' => 'params.primary_mobile', 'order' => 3  ],
                [ 'key' => 'params.ref_code', 'inputGroup' => 'input-group', 'icon' => 'fa fa-envelope', 'order' => 4, 'label' => 'Ref Code', 'editor' => 'text' ],
            ],
            'success' => 'New account has been created'
        ],
        'update-user' => [
            'controls' => [
                [ 'key' => 'username', 'validators' => [ 'required' => true ] ],
                [ 'key' => 'params.primary_email', 'validators' => [ 'required' => true ] ],
                [ 'key' => 'full_name', 'label' => 'Display Name', 'validators' => [ 'required' => true ] ],
            ],
            'success' => 'Account information updated'
        ],
        'user-email-identity' => [
            'controls' => [
                [ 'key' => 'type', 'label' => 'Identity Type', 'editor' => 'dropdown', 'options' => [ ['value' => 'email', 'label' => 'Email'], ['value' => 'mobile', 'label' => 'Mobile Number'] ], 'validators' => [ 'required' => true ] ],
                [ 'key' => 'identifier', 'label' => 'Identifier', 'validators' => [ 'required' => true ] ],
                [ 'key' => 'default', 'editor' => 'checkbox', 'options' => [ ['value' => 'email', 'label' => 'Email'], ['value' => 'mobile', 'label' => 'Mobile Number'] ] ],
                [ 'key' => 'usage', 'editor' => 'radio', 'label' => 'Can be used for', 'options' => [ ['value' => 'notify', 'label' => 'Notification'], ['value' => 'verify', 'label' => 'Verification'] ] ],
            ]
        ]
    ];


    protected $browser = [
        'fields' => [
            'id' => [ 'hidden' => true ],
            'username',
            'full_name',
            'userscope',
            'details.primary_email',
            'verified',
            'locked'
        ],
        'actions' => [
            [ 'label' => 'Edit', 'icon' => 'icon-wrench', 'id' => 'username', 'redirect' => 'admin/personnel/accounts/update' ],
            [ 'label' => 'Identity', 'icon' => 'icon-key', 'id' => 'id', 'redirect' => 'admin/personnel/accounts/identity' ],
            [ 'label' => 'Delete', 'icon' => 'icon-trash', 'id' => 'username', 'form' => 'force.user/delete-user', 'params' => ['id'], 'confirm' => 'Are you sure?' ],
        ]
    ];

    */

    /*
    function createUserSubmit($form){
        $data = $form->getFormData();
        $done = $this->store($data);

        if ($done) {
            $success = $this->displayName() . ', you have a new account with us. <br>We sent you an email to activate your account.';
            $this->success($success);

            // $this->postAccountUpdate();

            return true;
        }

        $this->error('New user account registration is not allowed atm. <a class="alert-link">Check here for details on this.<a>', NOTICE_ERROR);
        return false;
    }
    */

    function primaryEmail(){
        return $this->getValue('primary_email');
    }

    function notifyEmail(){
        return $this->getValue('primary_email');
    }

    function primaryMobile(){
        return $this->getValue('primary_mobile');
    }

    function verifyUrl(){
        $token = $this->accessToken();
        $salt = base64_encode("t:{$token}");
        return url( '/auth/verify/' . $salt );
    }

    function verifiedHumanize(){
        $value = $this->getFieldValue('verified');
        return (empty($value)) ? 'unverified' : 'verified';
    }

    function statusDefault(){
        return 'inactive';
    }

    function statusHumanize(){
        $value = $this->getFieldValue('active');
        return ucwords($value);
    }

    function isVerified(){
        return (bool)$this->getValue('verified');
    }

    function accountStatus(){
        return $this->getValue('status');
    }

    function isStatus($status){
        return $this->isMatch('status', $status);
    }

    function isActive(){
        return $this->isStatus('active');
    }

    function isInactive(){
        return $this->isStatus('inactive');
    }

    // compare account secret password
    function matchSecret($match) {
        $secret = $this->getValue('secret');
        if (Hash::needsRehash($secret)) {
            $this->error('You account secret has been deprecated');
        }
        return Hash::check($match, $secret);
    }

    function displayName(){
        return $this->getValue('full_name');
    }

    function displayNameValidate($value){
        if (empty($value)) {
            $this->getField('full_name')->setError('We need a name to call you with!');
            return false;
        }
        return true;
    }

    function usernameValidate($value = null){
        $where = $this->isNew() ? [] : [ ['id', '!=', $this->primaryId()] ];
        $where[] = [ 'username', $value ];
        $exists = $this->findOne($where);
        return empty($exists) ? true : false;
    }

    function uniqidDefault(){
        return uniqid();
    }

    function accessToken(){
        return $this->getValue('access_token');
    }

    function accessTokenDefault(){
        return uniqid();
    }

    function retouchAccessToken(){
        return $this->setValue('access_token', uniqid());
    }

    function has_unverified_identities(){
        $user_id = $this->primaryId();
        $where = [ ['user_id', $user_id], [ 'verified', 0 ] ];
        $unverified = resolve('identity')->filter( [ 'where' => $where ] );
        return $unverified;
    }

    function findByAccessToken($token){
        $user = resolve('user')->getObject( [ 'access_token' => $token ] );
        return $user;
    }

    function hasIdentity($pipeline, $identifier){
        return !!$this->getIdentity($pipeline, $identifier);
    }

    function findIdentity($pipeline, $identifier){
        $where = [
            [ 'pipeline', $pipeline ],
            [ 'identifier', $identifier ]
        ];
        return (new UserIdentity())->findOne($where);
    }

    function getIdentity($pipeline, $identifier){
        $cached = base64_encode("$pipeline|$identifier");
        if ($this->inCache($cached)) return $this->inCache($cached);
        $where = [
            [ 'user_id', $this->primaryId() ],
            [ 'pipeline', $pipeline ],
            [ 'identifier', $identifier ]
        ];
        $identity = resolve('identity')->findOne($where);
        if (!empty($identity)) return $this->inCache($cached, $identity);
        return null;
    }

    function getAllIdentity(){
        if (!$this->isNew()) {
            $where = [ ['user_id', $this->primaryId()] ];
            return resolve('identity')->filter( ['where' => $where] );
        }
        return null;
    }

    function secretValidate($value){
        if (!empty($value) && strlen($value) > 3) {
            return true;
        }
        return false;
    }

    function isPrimaryEmail($email){
        return $this->isMatch('primary_email', $email);
    }

    function isPrimaryMobile($mobile){
        return $this->isMatch('primary_mobile', $mobile);
    }

    // create user identity
    function checkPrimaryAccountIdentity(){
        // create email identity
        $this->checkPrimaryEmailIdentity();
        // create mobile identity
        $this->checkPrimaryMobileIdentity();
        // build account tags
    }

    function checkPrimaryEmailIdentity(){
        $email = $this->primaryEmail();
        if (!empty($email) && !$this->hasIdentity('email', $email)) {
            $identity = $this->findOrCreateAccountIdentity('email', $email);
            if (!$identity) {
                logError('user identity create failed for ' . $email);
            } else {
                if (!$this->isVerified()) {
                    // $identity->initiateVerification();
                }
            }
            return $identity;
        }
    }

    function checkPrimaryMobileIdentity(){
        $mobile = $this->primaryMobile();
        if (!empty($mobile) && !$this->hasIdentity('mobile', $mobile)) {
            return $this->findOrCreateAccountIdentity('mobile', $mobile);
        }
    }

    function findOrCreateAccountIdentity($pipeline, $identifier): UserIdentity {
        if ($this->hasIdentity($pipeline, $identifier)) {
            return $this->getIdentity($pipeline, $identifier);
        }
        $data = [
            'user_id' => $this->primaryId(),
            'pipeline' => $pipeline,
            'identifier' => $identifier
        ];

        $form = new FormHandler('identity.entry-form', [ 'data' => $data, 'action' => 'submit' ]);
        $response = $form->handleSubmit();
        if ($form->isSucceeded()) {
            return $form->object();
        }
        abort(HTTP_NOT_ACCEPTABLE, $form->error());
    }

    function primaryEmailIdentity() : UserIdentity {
        return $this->getIdentity('email', $this->primaryEmail());
    }

    function primaryMobileIdentity() : UserIdentity {
        return $this->getIdentity('mobile', $this->primaryMobile());
    }

    function getAccountVerificationLink(){
        $username = $this->getValue('username');
        $token = $this->getValue('access_token');
        $salt = base64_encode("{$username}:{$token}");
        if ($this->isMatch('user_type', 'customer')) {
            return url('/hello/verify/'.$salt);
        }
        return url('/auth/verify/'.$salt);
    }


    function verifyAccount(){
        $data = [
            'verified' => 1,
            'params.verified_on' => Higg::datetime()
        ];
        if (!$this->isVerified()) {
            if(!$this->store($data)) {
                $data = $this->toArray();
                logError('Could not confirm verified field', $data);
                return false;
            }
        }
        return true;
    }

    function isSinglePasswordLogin(){
        return (bool)$this->getValue('settings.master_password', true);
    }

    function activateWithPassword($secret){
        $data = [
            'id' => $this->primaryId(),
            'status' => 'active',
            'access_token' => $this->accessTokenDefault(),
            'secret' => $secret
        ];
        $done = $this->store($data);
        if ($done) {

            if ($this->isSinglePasswordLogin()) {
                $all = $this->getAllIdentity();
                if (!empty($all)) {
                    foreach ($all as $identity) {
                        $identity->changeSecret($secret);
                    }
                }
            }

            $identity = $this->primaryEmailIdentity();
            $options = [
                'to' => $this->primaryEmail(),
                'toName' => $this->displayName(),
                'subject' => 'Welcome to NuForce Field Services Network'
            ];
            higg()->mail('emails.identity.user_account_activated', [ 'user' => $this, 'identity' => $identity ], $options);

        }
        return $done;
    }




} // end form