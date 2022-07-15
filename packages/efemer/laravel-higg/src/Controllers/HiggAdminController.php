<?php

namespace Efemer\Higg\Controllers;

use Efemer\Higg\Traits\ViewRenderTrait;
use Efemer\Force\Factory\Models\User;

class HiggAdminController extends BaseController {

    use ViewRenderTrait;

    protected $viewNamespace = 'higg';
    protected $viewPrefix = 'admin';

    function getIndex(){
        return '404';
    }

    function getDebug(){

        $data = [
            //'id' => 232,
            'user_scope' => 'nuforce',
            'username' => 'efemer',
            'identities' => [
                'facebook' => 'Connect:23'
            ],
            'queues' => [
                'mobile_verification' => 'Mobile:57'
            ],
            'verified' => 0,
            'locked' => 0,
            'salt' => 'efe132',
            'access_token' => str_random(8)
        ];

        $user = new User();
        // $user->assign($data);
        // dd($user->toArray());

        $user = $user->find(1);
        // $user->postRead();
        //$res = $user->toArray();
        $res = $user->store($data);
        pr($res);

        //dd($_SERVER);

    } // demo

} // end