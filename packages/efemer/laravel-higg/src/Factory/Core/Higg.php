<?php

namespace Efemer\Higg\Factory\Core;

use Carbon\Carbon;

use Config;
use Request;
use Hash;
use Auth;
use Bitly;
use Mail;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;

use Bugsnag;
use Log;
use Validator;
use Twilio\Rest\Client as Twilio;

class Higg {

	public $noticeBox;
	public $activityBox;
	public $displaySession;
	public $cached;
	public $currentUser;

	public $context = [];

	function __construct(){
		$this->noticeBox = new MessageBag();
		$this->activityBox = new MessageBag();
		$this->displaySession = [];
		$this->cached = [];
	}

	function boot(){
		$this->detectContext();
		event('higg.boot');
	}

	// get app contexts list
	function contextProfiles($profile = null){
		$contextProfiles = config('higg.context.profiles');
		if (empty($contextProfiles)) {
			$contextProfiles = [
				'web' => '/'
			];
		}
		return (empty($profile)) ?
			$contextProfiles : array_get($contextProfiles, $profile);
	}

	function inCache($key, $set = null){
		if (!is_null($set)) $this->cached[$key] = $set;
		return array_get($this->cached, $key, $set);
	}

	public function isContext($match){
		$current = $this->currentContext();
		return $current == $match;
	}

	public function currentContext(){
		return array_get($this->context, 'current');
	}

	public function contextHomeUrl(){
		$url = array_get($this->context, 'homeUrl');
		return url($url);
	}

	// detect app context from path
	public function detectContext(){
		$path = Request::path();
		$path = ($path == '/') ? $path : '/'.$path;
		$contextProfiles = $this->contextProfiles();
		$contextPath = '/';
		foreach ($contextProfiles as $context => $match) {
			if (is_callable($match)) {
				$hasContext = $match();
				if (!empty($hasContext)) {
					$contextPath = $hasContext;
					break;
				}
			} else {
				if (Str::startsWith($path, $match)) {
					$contextPath = $match;
					break;
				}
			}
		}
		$this->setContext($context, $contextPath);
	}

	// set context settings
	function setContext($context, $path){

		$this->context = [
			'current' => $context,
			'homeUrl' => $path,
		];

		// merge context configs with web config keys
		$this->loadConfig('web', config_path($context . '.php'));

		event('higg.context');
	}

	function loadConfig($namespace, $configFile){
		if (file_exists($configFile)) {
			$config = Config::get($namespace, []);
			$moreConfig = require $configFile;
			$mergeConfig = array_merge($config, $moreConfig);
			Config::set($namespace, $mergeConfig);
		}
	}

	function hasError(){
		return $this->noticeBox->has(NOTICE_ERROR);
	}
	function allError(){
		return $this->noticeBox->get(NOTICE_ERROR);
	}
	function error($message = null){
		if (empty($message)) {
			return $this->hasError() ? $this->noticeBox->first(NOTICE_ERROR) : false;
		}
		$this->noticeBox->add(NOTICE_ERROR, $message);
	}
	function hasSuccess(){
		return $this->noticeBox->has(NOTICE_SUCCESS);
	}
	function allSuccess(){
		return $this->noticeBox->get(NOTICE_SUCCESS);
	}
	function success($message = null){
		if (empty($message)) {
			return $this->hasSuccess() ? $this->noticeBox->first(NOTICE_SUCCESS) : false;
		}
		$this->noticeBox->add(NOTICE_SUCCESS, $message);
	}

	function bugsnag($error){
		$this->bugsnag($error);
	}

	function log($message, $type = null, $data = []){
		monolog($message, $type, $data);
	}

	function hash($token){
		return Hash::make($token);
	}

	function uniqid($prefix = '', $more_entropy = null){
		if (is_null($more_entropy)) $more_entropy = empty($prefix) ? true : false;
		$uniqid = uniqid($prefix, $more_entropy);
		return str_replace( ['-', '.'], '', $uniqid );
	}

	function token(){
		return random_int(212345, 912345);
	}

	function isValid($data, $rules, $errors = []){
		$validator = Validator::make($data, $rules, $errors );
		if ($validator->fails()) {
			foreach ($validator->errors()->all() as $message) {
				$this->error($message);
				// pr($message);
			}
			return false;
		}
		return true;
	}

	/*

Array
(
	[status_code] => 200
	[status_txt] => OK
	[data] => Array
		(
			[url] => http://bit.ly/2DpC9T2
			[hash] => 2DpC9T2
			[global_hash] => 2j6B8
			[long_url] => https://google.com/
			[new_hash] => 0
		)

)

	 */

	function short_url($url){
		// require_once('../../Library/bitly.php');
		$params = array();
		$params['access_token'] = config('higg.bitly.token');
		$params['longUrl'] = $url;
		// $params['domain'] = 'j.mp';
		// $params['hash'] = array('dYhyia','dYhyia','abc123');
		// $results = bitly_get('expand', $params, true);
		$results = bitly_get('shorten', $params);
		$newUrl = array_get($results, 'data.url');
		return !empty($newUrl) ? $newUrl: $url;

		/*
		$response = Bitly::shorten($url);
		if (isset($response['status_txt']) && $response['status_txt'] == 'OK') {
			$url = array_get($response, 'data.url');
			// @todo log short url api call
		}
		return $url;
		*/
	}

	function carbon($date = null){
		return new Carbon($date);
	}

	function date($date = null){
		$format = 'Y-m-d';
		if (!is_null($date)) {
			if (!($date instanceof Carbon)) {
				$date = new Carbon($date);
			}
			return $date->format($format);
		}
		return date($format);
	}
	function datetime($date = null){
		$format = 'Y-m-d H:i:s';
		if (!is_null($date)) {
			if (!($date instanceof Carbon)) {
				$date = new Carbon($date);
			}
			return $date->format($format);
		}
		return date($format);
	}

	function timezoneIdentifiers(){
		$zones = \DateTimeZone::listIdentifiers();
		$list = [];
		foreach ($zones as $zone) $list[$zone] = $zone;
		return $list;
	}

	// currently logged in user or manually set session user
	function user($setUser = null){
		if (Auth::check()){
			if (!$this->currentUser)
				return $this->currentUser = resolve('user')->getObject(Auth::id());
		}
		if (!is_null($setUser))
			return $this->currentUser = $setUser;
		return $this->currentUser;
	}

	function mail($view, $data, $options = []){
		if (isMailDisabled()) return;

		$subject = array_get($options, 'subject', "You've got mail");
		if (is_local()) {
			array_set($options, 'to', config('defaults.mail.to'));
			array_set($options, 'toName', config('defaults.mail.toName'));
			array_set($options, 'subject', 'DEV: ' . $subject);
		}
		// dd($options);
		Mail::send($view, $data, function($mail) use ($options) {

			$to = array_get($options, 'to', 'efemer@gmail.com');
			$toName = array_get($options, 'toName', 'John');
			$from = array_get($options, 'from', config('defaults.mail.from') );
			$fromName = array_get($options, 'fromName', config('defaults.mail.fromName'));
			$subject = array_get($options, 'subject', "You've got mail");
			$bcc = array_get($options, 'bcc', config('defaults.mail.bcc'));

			$mail->to($to, $toName)->from($from, $fromName)->subject($subject)->bcc($bcc);

		});
	}

	function sms($to, $message){
		$twilioConfig = config('higg.twilio');
		if (empty($twilioConfig)) return;

		$accountId = array_get($twilioConfig, 'account_id');
		$token = array_get($twilioConfig, 'token');
		$from = array_get($twilioConfig, 'from');
		$bcc = array_get($twilioConfig, 'bcc');
		$twilio = new Twilio($accountId, $token);
		// $twilio = new Twilio($accountId, $token, $from);
		// $twilio = new Twilio()

		if (!Str::startsWith($to, '1')) {
			if (!Str::startsWith($to, '880')) {
				$to = '1'.$to;
			}
		}

		if (Str::startsWith($to, '+') !== 0) $to = '+' . $to;

		// $res = $twilio->message($to, $message);
		$res = $twilio->messages->create($to, [ 'from' => $from, 'body' => $message]);
		if (!empty($bcc)) {
			// $twilio->message($bcc, $message);
			$twilio->messages->create($to, [ 'from' => $from, 'body' => 'BCC: ' . $message]);
		}

		$data = json_decode((string)$res, true);
		// logDebug('SMS sent to ' . $to, $data);
		return $data;
	}

	function respondWithJson($data){
		$headers = [
			'Content-type' => 'application/json'
		];
		return response( json_encode($data), 200, $headers );
	}

	function listCommonColorCodes(){
		$colors = [
			'none' => '',
			'blue' => '#89C4F4', 'red' => '#F3565D', 'green' => '#1bbc9b',
			'purple' => '#9b59b6', 'grey' => '#95a5a6', 'yellow' => '#F8CB00'
		];
		$colors = array_flip($colors);
		foreach ($colors as $c => $n) $colors[$c] = ucwords($n);
		return $colors;
	}

	function displaySession(){
		return resolve('higg.session')->createSession();
	}

} // end Higg
