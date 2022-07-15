<?php

function pr(){
	array_map(function ($x) {
		echo '<pre>' . print_r($x, true) . '</pre>';
	}, func_get_args());
	exit(0);
}

function vd(){      // var_dump
	array_map(function ($x) {
		echo '<pre>'; var_dump($x); echo '</pre>';
	}, func_get_args());
	exit(0);
}

function post($key = null, $else = null){
	$keys = request()->all();
	$jsonKeys = request()->json()->all();
	if (!empty($jsonKeys)) $keys = array_merge($keys, $jsonKeys);
	return is_null($key) ? $keys : array_get($keys, $key, $else);
}

// get list of class methods
function get_native_class_methods($classPath){

	$excludeNamespaces = [ 'Illuminate\Routing' ];
	$excludeMethods = [ 'render' ];

	$class = new ReflectionClass($classPath);
	$methods = $class->getMethods();
	$ownMethods = [];
	foreach($methods as $method) {
		if (!$method->isPublic()) continue;
		if (StrHelper::startsWith($method->class, $excludeNamespaces) ) continue;
		if (StrHelper::startsWith($method->name, $excludeMethods) ) continue;
		$ownMethods[] = $method->name;
	}
	return $ownMethods;
}

// get config item
function cfg($key, $value = null){
	return web()->config(strtolower($key), $value);
}

// generate asset url
function asset_url($path, $cdn = null) {
	// if (is_array($path)) return asset_helper()->urls($path, $cdn);
	// else return asset_helper()->url($path, $cdn);
	return assetUrl($path);
}

function urlto($path){
	//return asset_url($path);
	return url($path, [], is_secure());
}

function is_console(){
	return php_sapi_name() == 'cli';
}

function is_debug(){
	return env('APP_DEBUG', true);
}
function is_local(){
	return env('APP_ENV') == 'local';
}
function is_production(){
	return env('APP_ENV') == 'production';
}
function is_dev(){
	return env('APP_ENV') == 'dev';
}
function is_live(){
	return is_production();
}

function is_secure() {
	if (!is_local()) return true;
	else if (is_console()) return false;
	return
		(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
		|| $_SERVER['SERVER_PORT'] == 443;
}


// returns notice type to monolog level
function monolog_level($notice_type){
	switch($notice_type) {
		case NOTICE_EMERGENCY: return Monolog\Logger::EMERGENCY;
		case NOTICE_ALERT: return Monolog\Logger::ALERT;
		case NOTICE_CRITICAL: return Monolog\Logger::CRITICAL;
		case NOTICE_ERROR: return Monolog\Logger::ERROR;
		case NOTICE_WARNING: return Monolog\Logger::WARNING;
		case NOTICE_NOTICE: return Monolog\Logger::NOTICE;
		case NOTICE_INFO: return Monolog\Logger::INFO;
		case NOTICE_DEBUG: return Monolog\Logger::DEBUG;
	}
}

function log_label($level){
	switch($level) {
		case Monolog\Logger::EMERGENCY: return NOTICE_EMERGENCY;
		case Monolog\Logger::ALERT: return NOTICE_ALERT;
		case Monolog\Logger::CRITICAL: return NOTICE_CRITICAL;
		case Monolog\Logger::ERROR: return NOTICE_EMERGENCY;
		case Monolog\Logger::WARNING: return NOTICE_WARNING;
		case Monolog\Logger::NOTICE: return NOTICE_NOTICE;
		case Monolog\Logger::INFO: return NOTICE_INFO;
		case Monolog\Logger::DEBUG: return NOTICE_DEBUG;
	}
}

// return notice type to google log severity type
function google_logger_level($notice_type){
	switch($notice_type) {
		case NOTICE_EMERGENCY: return Google\Cloud\Logging\Logger::EMERGENCY;
		case NOTICE_ALERT: return Google\Cloud\Logging\Logger::ALERT;
		case NOTICE_CRITICAL: return Google\Cloud\Logging\Logger::CRITICAL;
		case NOTICE_ERROR: return Google\Cloud\Logging\Logger::ERROR;
		case NOTICE_WARNING: return Google\Cloud\Logging\Logger::WARNING;
		case NOTICE_NOTICE: return Google\Cloud\Logging\Logger::NOTICE;
		case NOTICE_INFO: return Google\Cloud\Logging\Logger::INFO;
		case NOTICE_DEBUG: return Google\Cloud\Logging\Logger::DEBUG;
	}
	return Google\Cloud\Logging\Logger::DEFAULT_LEVEL;
}

// log application events
function monolog($message, $type = 'info', $data = []){

	if ($message instanceof \Exception) {
		bugsnag($message);
		$type = 'critical';
		$message = $message->getMessage();
		$data['code'] = $message->getCode();
		$data['file'] = $message->getFile();
		$data['line'] = $message->getLine();
		$data['trace'] = $message->getTraceAsString();
	}

	switch($type) {
		case 'emergency': Log::emergency($message, $data); break;
		case 'alert': Log::alert($message, $data); break;
		case 'critical': Log::critical($message, $data); break;
		case 'error': Log::error($message, $data); break;
		case 'warning': Log::warning($message, $data); break;
		case 'notice': Log::notice($message, $data); break;
		case 'debug': Log::debug($message, $data); break;
		case 'info': Log::info($message, $data); break;
	}

	if (config('higg.google.logger.handle', false) == true) {
		google_logger($message, $type, $data);
	}

}

// report exceptions to bugsnag
function bugsnag(\Exception $error){
	if (config('higg.bugsnag.handle', false)) return;
	if ($error instanceof \Exception == false) return;

	$user = [ 'clientId' => Request::getClientIp() ];
	if (isLogged()) {
		$user = [
			'userId' => $user->getValue('id'),
			'username' => $user->getValue('username'),
			'orgCode' => $user->getValue('org_code'),
			'clientIp' => Request::getClientIp()
		];
	}

	Bugsnag::notifyException($error, function ($report) use ($user) {
		$report->setSeverity('error');
		if ($user) $report->setUser($user);
	});
}

function google_logger($message, $type = null, $customLabels = []){

	$logging = new Google\Cloud\Logging\LoggingClient([
		'projectId' => config('higg.google.logger.projectId'),
		'keyFilePath' => config('higg.google.logger.keyFilePath')
	]);

	$severity = is_integer($type) ? $type : google_logger_level($type);
	$logger = $logging->logger($type);
	$options = [ 'severity' => $severity ];
	if (!empty($customLabels)) {
		$options['labels'] = [];
		foreach ($customLabels as $k => $v) {
			$options['labels'][$k] = is_array($v) ? json_encode($v) : (string)$v;
		}
	}

	// https://googlecloudplatform.github.io/google-cloud-php/#/docs/v0.23.0/logging/logger
	$entry = $logger->entry($message,  $options);
	$logger->write($entry);
}

function logCritical($message, $data = []){ monolog($message, NOTICE_CRITICAL, $data); }
function logInfo($message, $data = []){ monolog($message, NOTICE_INFO, $data); }
function logNotice($message, $data = []){ monolog($message, NOTICE_NOTICE, $data); }
function logError($message, $data = []){ monolog($message, NOTICE_ERROR, $data); }
function logDebug($message, $data = []){ monolog($message, NOTICE_DEBUG, $data); }

// check is user is logged in
function isLoggedIn(){ return app('auth')->check(); }
function isLogged(){ return app('auth')->check(); }
function userId(){ return app('auth')->id(); }
function isMailDisabled(){ return config('higg.mail.disable'); }

//
// return global helpers
//

function higg() : \Efemer\Higg\Factory\Core\Higg { return app('higg'); }
function user(){ return higg()->user(); }
function web(){ return app('higg.web'); }
function page(){ return app('higg.page'); }
function html(){ return app('html'); }
function form(){ return app('form'); }
function asset_helper(){ return app('higg.asset'); }
function auth_helper(){ return app('higg.auth'); }
function displaySession() : \Efemer\Higg\Factory\Models\DisplaySession { return higg()->displaySession(); }

function assetUrl($path = ''){
	// $url = env('ASSET_BASE');
	// if (is_local()) $url = 'http://localhost:4200';
	return url($path);
}

function appUrl($path = ''){
	$url = env('APP_URL');
	if (is_local()) $url = 'http://localhost:4200';
	return $url . $path;
}
function baseUrl($path = ''){
	$url = env('APP_BASE_URL', 'http://localhost');
	return $url . $path;
}
// end helpers

//
function empty_field_value($value){
	if (is_array($value)) {
		if (empty($value)) return true;
		return false;
	}
	if (is_null($value) || strlen((string)$value) == 0) return true;
	return false;
}

function redisClient() : \Efemer\Higg\Factory\Core\RedisStore {
	return resolve('higg.redis');
}

// return member from array
// if not exists than call else()
// or return $else
function array_get_with_else($arr, $get, $else = null) {
    $value = array_get($arr, $get);
    if (!is_null($value)) {
        if (is_callable($else)) {
            $value = $else($arr, $get, $value);
            if (!is_null($value)) return $value;
        }
        return $value;
    }
    if (is_callable($else)) {
        return $else($arr, $get, $value);
    }
    return $else;
}


function array_get_this_or_that($arr, $keys = null) {
    if (is_string($keys)) {
        return array_get($arr, $keys);
    }
    else if (is_array($keys)) {
        foreach ($keys as $key) {
            $val = array_get($arr, $key);
            if (!is_null($val)) return $val;
        }
    }
    return null;
}
