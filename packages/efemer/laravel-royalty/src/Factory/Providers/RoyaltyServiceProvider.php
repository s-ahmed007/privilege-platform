<?php

namespace Efemer\Royalty\Factory\Providers;

use Efemer\Higg\Factory\Traits\ServiceProviderTrait;
use Illuminate\Support\ServiceProvider;

require_once __DIR__ . '/../../Library/defines.php';

class RoyaltyServiceProvider extends ServiceProvider {

	use ServiceProviderTrait;

	public $packageName = 'royalty';
	public $packageNamespace = '\Efemer\Royalty';
	public $packageDir = __DIR__ . '/../../..';

	public $views = [];
	public $configs = [
		'royalty' => 'royalty',
		'higg' => 'higg',
	];
	public $required = [];
	public $facades = [];

	public $bindings = [

        MODEL_CUSTOMER_ACCOUNT => '\Efemer\Royalty\Factory\Models\CustomrAccount',

        HANDLER_ROYALTY => '\Efemer\Royalty\Factory\Handlers\RoyaltyHandler',
        HANDLER_WALLET => '\Efemer\Royalty\Factory\Handlers\WalletHandler',

	];

	public $actions = [

		'royalty' => [
			'debug' => [ HANDLER_ROYALTY, 'debug' ],

		],

	];

	function onBoot(){

		$this->publishes([
			__DIR__.'/../../../config/royalty.php' => config_path('royalty.php'),
		]);

		if (file_exists(__DIR__.'/../../../migrations')) {
			$this->loadMigrationsFrom(__DIR__.'/../../../migrations');
		}

	}

	function isActive(){
		return true;
	}


}
