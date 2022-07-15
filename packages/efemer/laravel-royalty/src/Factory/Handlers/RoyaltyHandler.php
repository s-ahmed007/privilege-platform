<?php

namespace Efemer\Royalty\Factory\Handlers;

use Efemer\Higg\Factory\Handlers\ActionHandler;

class RoyaltyHandler extends ActionHandler {

	public function getObject() {
		return $this;
	}

	function debug() {
		return 'this is jacks last hope!'
	}

}
