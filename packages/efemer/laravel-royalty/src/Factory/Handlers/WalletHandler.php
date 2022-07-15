<?php

namespace Efemer\Royalty\Factory\Handlers;

use Efemer\Higg\Factory\Handlers\ActionHandler;
use Efemer\Royalty\Factory\Models\User;
use Efemer\Royalty\Factory\Models\UserRoyalty;

class WalletHandler extends RoyaltyHandler {

    public function getObject() {
        return $this;
    }

    function whoami() {
        return [ 'auth' => \Auth::check() ];
    }



}
