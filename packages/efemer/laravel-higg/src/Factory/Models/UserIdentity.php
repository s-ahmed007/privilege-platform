<?php

namespace Efemer\Higg\Factory\Models;

use Efemer\Higg\Factory\Core\HiggModel;
use Efemer\Higg\Factory\Handlers\FormHandler;
use Hash;
use Higg;
use Validator;

class UserIdentity extends HiggModel {

    public $table = 'user_identities';
    public $alias = 'higg.identity';

    protected $fields = [

        'user_id' => [ 'label' => 'User ID', 'validations' => 'required', 'readonly' => true, 'editor' => 'hidden' ],
        'pipeline' => [ 'label' => 'Identity', 'editor' => 'dropdown', 'validations' => 'required', 'default' => 'email', 'readonly' => true ],
        'identifier' => [ 'label' => 'Identifier', 'validations' => 'required', 'readonly' => true ],
        'secret' => [  'cast' => CAST_PASSWORD, 'label' => 'Secret', 'readonly' => false, 'validations' => 'required|min:4', 'inputGroup' => 'input-group', 'icon' => 'fa fa-key' ],

        'active' => [ 'label' => 'Active Identity', 'readonly' => true, 'validations' => 'required' ],
        'verified' => [ 'label' => 'Verified Identity', 'readonly' => true, 'validations' => 'required' ],

        'expires_at' => [ 'cast' => CAST_DATETIME, 'label' => 'Expires On', 'readonly' => true ],
        'issued_at' => [ 'cast' => CAST_DATETIME, 'label' => 'Issued At', 'validations' => 'required', 'readonly' => true ],
        'token' => [ 'label' => 'Unique Token', 'readonly' => true, 'validations' => 'required' ],
        'renew_token_at' => [ 'cast' => CAST_DATETIME, 'label' => 'Renew On', 'readonly' => true ],
        'last_used_at' => [ 'cast' => CAST_DATETIME, 'label' => 'Last Used On', 'readonly' => true ],

        'details' => [ 'cast' => CAST_ARRAY ],
        'details.alias' => [ 'label' => 'Alias', 'cast' => CAST_STRING, 'validations' => 'required' ],
        'details.blocked_origin' => [ 'label' => 'Blocked', 'cast' => CAST_STRING ],
        'details.allowed_origin' => [ 'label' => 'Allowed', 'cast' => CAST_STRING ],

        'params' => [ 'cast' => CAST_ARRAY ],
        'params.username' => [ 'label' => 'Username', 'readonly' => true ],
        'params.display_name' => [ 'label' => 'Display Name', 'editor' => 'hidden', 'readonly' => true ],
        'params.account_code' => [ 'label' => 'Account Code' ],
        'params.user_type' => [ 'label' => 'User Type' ],
        'params.secret_hint' => [ 'label' => 'Secret Hint' ],
        'params.verification_issued_at' => [ 'label' => 'Verification Issued On', 'cast' => CAST_DATETIME ],

        'created_at' => [ 'cast' => CAST_DATETIME ],
        'updated_at' => [ 'cast' => CAST_DATETIME ],

    ];

    protected $forms = [
        'entry-form' => [
            'controls' => [
                [ 'key' => 'user_id', 'params' => [ 'type' => 'hidden' ] ],
                [ 'key' => 'params.username', 'params' => [ 'type' => 'hidden' ] ],
                [ 'key' => 'params.display_name', 'params' => [ 'type' => 'hidden' ] ],
                [ 'key' => 'params.account_code', 'params' => [ 'type' => 'hidden' ] ],
                [ 'key' => 'params.user_type', 'params' => [ 'type' => 'hidden' ] ],

                [ 'key' => 'details.alias' ],
                [ 'key' => 'pipeline' ],
                [ 'key' => 'identifier' ],
                [ 'key' => 'secret', 'editor' => 'password' ],
                [ 'key' => 'active', 'params' => [ 'type' => 'hidden' ] ],
            ],
            // 'actions' => [ 'create', 'update', 'delete' ]
        ]
    ];

    protected $browser = [
        'fields' => [
            'id' => [ 'hidden' => true ],
            'user_id' => [ 'hidden' => true ],
            'params.display_name',
            'pipeline',
            'identifier',
            'secret',
            'active',
            'verified'
        ],
        'actions' => [
            [ 'label' => 'Edit', 'icon' => 'icon-key', 'redirect' => 'admin/personnel/accounts/identity/edit' ],
            [ 'label' => 'Delete', 'icon' => 'icon-trash', 'form' => 'force.user/delete-user', 'params' => [ 'id' ], 'confirm' => 'Are you sure about removing this identity?' ],
        ]
    ];

    function entryFormSubmit(FormHandler $form){
        $data = $form->getFormData();
        $this->assign($data);
        if (!$this->error()) {
            $identifier = $this->getValue('identifier');
            $new = $this->isNew();
            $done = $this->store($data);
            if (!$done) {
                $this->error($new ? "Failed to create identity with $identifier" : "Failed to update $identifier");
            } else {
                $this->success($new ? "New identity created with $identifier" : "Identity $identifier information updated");
            }
            return $done;
        }
        return false;
    }

    function afterForm(FormHandler $form){
        if ($form->name == 'entry-form' && $form->submit == 'action') {
            if (!$this->isVerified()) {
                $this->initiateVerification();
            }
        }
    }

    function activeDefault(){
        return 0;
    }

    /*
    function activeValidate($value){
        if (!$this->isVerified()) {
            if (!empty($value)) {
                $this->getField('active')->setError('Verify ' . $this->getIdentifier() . ' before activating it.' );
                return false;
            }
        }
        return true;
    }
    */
    function verifiedDefault(){
        return 0;
    }

    function tokenDefault(){
        return Higg::token();
    }

    function tokenEdit(){
        $this->setValue('renew_token_at', Higg::date('30 Days'));
    }

    function renewTokenAtDefault(){
        return Higg::date('30 Days');
    }

    function issuedAtEdit(){
        $value = $this->getValue('issued_at');
        if (empty($value)) {
            return Higg::datetime();
        }
        return $value;
    }


    function identifierValidate($identifier) {
        $user_id = $this->getValue('user_id');
        $pipeline = $this->getValue('pipeline');
        $where = [ ['identifier',$identifier], ['pipeline',$pipeline] ];
        $exists = $this->findOne($where);
        if ($exists && !$exists->isMatch('user_id', $user_id)) {
            $this->getField('identifier')->setError("The $pipeline $identifier is associated with existing user.");
            return false;
        }
        return true;
    }

    function getIdentifier(){
        return $this->getValue('identifier');
    }

    function getPipeline(){
        return $this->getValue('pipeline');
    }

    function pipelineOptions(){
        $options = [
            'email' => 'Email',
            'mobile' => 'Mobile',
            'device' => 'Device',
            // 'username' => 'Username'
        ];
        return $options;
    }

    function pipelineHumanize(){
        $options = $this->pipelineOptions();
        $pipeline = $this->getValue('pipeline');
        $value = array_get($options, $pipeline, $pipeline);
        return $value;
    }

    function getUser(){
        if ($user = $this->inCache('user')) return $user;
        $user_id = post('data.user_id');
        if (!$this->isNew()) $user_id = $this->getValue('user_id');
        if (!empty($user_id)) {
            $where[] = [ 'id', $user_id ];
            $user = app('user')->findOne($where);
            return $this->inCache( 'user', $user);
        }
        return null;
    }

    function secretDefault(){
        $secret = Higg::uniqid();
        return $secret;
    }

    function secretEdit($value = null){
        $this->setValue('params.secret_hint', base64_encode($value));
    }

    function userIdEdit(){
        $user = $this->user();
        if (!empty($user)) {
            $this->setValue('params.username', $user->getValue('username'));
            $this->setValue('params.display_name', $user->getValue('display_name'));
            $this->setValue('params.account_code', $user->getValue('account_code'));
        }
    }

    function detailsAliasDefault(){
        $user = $this->user();
        $alias = $this->getValue('details.alias');
        if (empty($alias)) {
            // $alias = $user->getValue('display_name') . '\'s ' . $this->getPipeline();
            $alias = ucwords($this->getPipeline());
            return $alias;
        }
        return $this->getValue('pipeline');
    }

    function userIdRead(){
        if ($this->isNew()) {
            $user_id = post('data.user_id');
            $user = app()->make('force.user')->findOne( [ 'id' => $user_id ] );
            if ($user) return $user->id;
        }
        return $this['user_id'];
    }

    function paramsUsernameRead(){
        $value = $this->getValue('params.username');
        if ($this->isNew()) {
            $user = $this->getUser();
            $value = $user ? $user->username : $value;
        }
        return $value;
    }

    function paramsAliasDefault(){
        $name = $this->getValue('params.display_name');
        if (!empty($name)) {
            $pipeline = $this->pipelineHumanize();
            return "{$name}'s {$pipeline}";
        }
        return '';
    }

    function paramsDisplayNameRead(){
        $value = $this->getValue('params.display_name');
        if ($this->isNew() && empty($value)) {
            $user = $this->getUser();
            $value = $user ? $user->username : $value;
        }
        return $value;
    }

    function firstName(){
        return $this->displayName(true);
    }

    function displayName($firstNameOnly = false){
        $name = $this->getValue('params.display_name');
        if ($firstNameOnly) {
            $parts = explode(" ", $name);
            if (isset($parts[0])) return $parts[0];
        }
        return $name;
    }

    function secretRead(){
        return '';
    }

    function isVerified(){
        return (bool)$this->getValue('verified');
    }

    function isActive(){
        return (bool)$this->getValue('active');
    }

    function isExpired(){
        return false;
    }

    function isPrimaryIdentity(){
        if ($this->isEmail()) {
            return $this->user()->isPrimaryEmail($this->getValue('identifier'));
        } else if ($this->isMobile()) {
            return $this->user()->isPrimaryMobile($this->getValue('identifier'));
        }
        return false;
    }

    function canLogin(){
        if (!$this->isVerified()) {
            $this->error('Please verify your identity.');
        } else if (!$this->isActive()) {
            $this->error('Unfortunately your identity is locked.');
        } else if ($this->isExpired()) {
            $this->error('Unfortunately your identity is expired.');
        } else {
            return true;
        }
        return false;
    }

    function matchSecret($match) {
        $pipeline = $this->getValue('pipeline');

        switch($pipeline) {
            case 'mobile':
            case 'email':
                $secret = $this->getValue('secret');
                if (Hash::needsRehash($secret)) {
                    $this->error('You identity secret has been deprecated');
                }
                return Hash::check($match, $secret);
                break;
        }

        return false;
    }

    function changeSecret($secret){
        $field = $this->getField('secret');
        $field->setValue($secret);
        if (!$this->error()) {
            return $this->store();
        } else {
            Higg::error($this->error());
        }
        return false;
    }

    function tryToChangeSecret($data){
        $rules = [
            'password' => 'required|confirmed|min:6',
        ];
        $messages = [
            'required' => 'Enter a password to continue',
            'min' => 'Password must be 6 characters long',
            'confirmed' => 'Password confirmation did not match',
        ];
        $validator = Validator::make($data, $rules, $messages);
        if ($validator->fails()) {
            $this->error($validator->messages()->first());
            return false;
        }

        $assign = [
            'secret' => $data['password']
        ];

        $this->store($assign);
        if (!$this->error()) {
            $this->wasChanged('password');
            $this->success('Your '.$this->getPipeline().' password is changed.');
            return true;
        }
        return false;
    }

    function isEmail(){
        return $this->getValue('pipeline') == 'email';
    }

    function isMobile(){
        return $this->getValue('pipeline') == 'mobile';
    }

    function user($user_id = null) {
        if (is_null($user_id)) $user_id = $this->getValue('user_id');
        if (!$this->inCache('user')) {
            $user = resolve('user')->getObject($user_id);
            $this->inCache('user', $user);
        }
        return $this->inCache('user');
    }

    function initiateVerification(){

        // @todo generate new token on every call

        if ($this->user()->isVerified()) {
            return $this->notifyVerificationToken();
        }

    }

    function resetAccountToken(){
        $data = [
            'token' => Higg::token(6),
            'renew_token_at' => Higg::datetime('30 days')
        ];

        $done = $this->store($data);
        abort_unless($done, 'Something went wrong while trying to change token for identity');
        $this->wasChanged('token', $this->changedFields());
        return $this->success('Identity token is reset');
    }

    function notifyVerificationToken($force = false){
        if ($this->isAlreadyNotifiedForVerification() && !$force) return;

        if ($this->isEmail()) {
            $options = [
                'to' => $this->getValue('identifier'),
                'toName' => $this->getValue('params.display_name'),
                'subject' => 'Please verify your email'
            ];
            higg()->mail('emails.identity.email_verification_invite', [ 'identity' => $this, 'user' => $this->user() ], $options);
        }
        else if ($this->isMobile()) {
            $to = $this->getValue('identifier');
            $token = $this->getValue('token');
            $link = Higg::short_url($this->verificationLink());
            $message = "please visit {$link} and enter {$token} to verify.";
            higg()->sms($to, $message);
        }

        $this->updateVerificationIssuedTime();
    }

    function isAlreadyNotifiedForVerification(){
        return !!$this->getValue('params.verification_issued_at');
    }

    function updateVerificationIssuedTime(){
        $this->setValue('params.verification_issued_at', Higg::datetime());
        $this->store();
    }

    function verificationLink($shortLink = false){
        // @todo do not call account verification form here
        $link = $this->user()->getAccountVerificationLink();
        if ($this->isEmail()) {
            $params = [
                'data[email]=' . urlencode($this->getValue('identifier')),
                'data[token]=' . $this->getValue('token'),
            ];
            $link = $link . "?" . implode('&', $params) ;
        }
        if ($shortLink) {
            $link = Higg::short_url($link);
        }
        return $link;
    }

    function tryToVerify($token){
        if ($this->isMatch('token', $token)) {
            $data['verified'] = 1;
            $data['active'] = 1;
            $data['token'] = Higg::token();
            $data['renew_token_at'] = Higg::datetime('30 days');;
            $data['params.verified_on'] = Higg::datetime();
            if (!$this->store($data)) {
                if (is_dev()) Higg::error($this->error());
                else Higg::error('Something went wrong while trying to acknowledge your token');
                return false;
            } else {
                if ($this->isEmail()) {
                    $options = [
                        'to' => $this->getValue('identifier'),
                        'toName' => $this->getValue('params.display_name'),
                        'subject' => 'Your email has been verified'
                    ];
                    higg()->mail('emails.identity.email_verification_done', [ 'identity' => $this ], $options);
                }
                return true;
            }
        }
        return false;
    }


}
