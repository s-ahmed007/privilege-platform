<?php

namespace Efemer\Higg\Factory\Handlers;

use Config;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use View;
use Higg;

class ActionHandler {

	protected $configStoreKey = 'actions';

	protected $key;
	protected $title;
	protected $description;

	protected $element;
	protected $class;
	protected $routerLink;
	protected $href;
	protected $content;
	protected $icon;

	protected $children;
	protected $handle;
	protected $view;
	protected $params;

	protected $origins;
	protected $guard;

	protected $data = [];
	protected $query = [];
	protected $respondWithJson = true;
	protected $output = null;
	protected $object = null;


	function init($config){

		if (is_string($config)) $config = $this->isKnown($config);

		$this->key = array_get($config, 'key', '');

		$this->title = array_get($config, 'title', false);
		$this->description = array_get($config, 'description', false);

		$this->element = array_get($config, 'element', false);
		$this->class = array_get($config, 'class', false);
		$this->routerLink = array_get($config, 'routerLink', false);
		$this->href = array_get($config, 'href', false);
		$this->content = array_get($config, 'content', false);
		$this->icon = array_get($config, 'icon', false);

		$this->children = array_get($config, 'children', false);
		$this->handle = array_get($config, 'handle', false);
		$this->view = array_get($config, 'view', false);
		$this->params = array_get($config, 'params', []);

		$this->origins = array_get($config, 'origins', false);
		$this->guard = array_get($config, 'guard', false);

		return $this;
	}

	function config(){
		return [
			'key' => $this->key,
			'title' => $this->title,
			'description' => $this->description,

			'element' => $this->element,
			'class' => $this->class,
			'routerLink' => $this->routerLink,
			'href' => $this->href,
			'content' => $this->content,
			'icon' => $this->icon,

			'handle' => $this->handle,
			'children' => $this->children,
			'view' => $this->view,
			'params' => $this->params,
			'origins' => $this->origins,
			'guard' => $this->guard
		];
	}

	function navItem(){
		$children = $this->childNavItems();
		$item = [
			'title' => $this->title(),
			'routerLink' => $this->panelPrefix() . $this->routerLink(),
			'icon' => $this->icon()
		];
		if (!empty($children)) {
			$item['children'] = $children;
		}
		return $item;
	}

	function hasChildren(){
		return !empty($this->children);
	}

	function childNavItems(){
		$actions = [];
		if ($this->hasChildren()) {
			foreach ($this->children as $config) {
				$navItem = (new ActionHandler())->init($config)->navItem();
				$actions[] = $navItem;
			}
		}
		return $actions;
	}

	function parseRequest(){
		return $this->setData(post('data'))
		            ->setQuery(post('query'))
		            ->setParams(post('query'))
		            ->returnJson(post('transform'));
	}

	function setData($data){
		if (!empty($data)) $this->data = $data;
		return $this;
	}

	function setView($view){
		$this->view = $view;
	}

	function renderView($data = []){
		if (!empty($this->view) && View::exists($this->view)) {
			return View::make($this->view, $data)->render();
		}
		return '';
	}

	function setQuery($query){
		if (!empty($query)) $this->query = $query;
		return $this;
	}

	function setParams($params){
	    if (empty($this->params)) $this->params = [];
		if (!empty($params) && is_array($params)) {
			$this->params = array_merge($this->params, $params);
		}
		return $this;
	}

	function returnJson($json = null){
		if (is_null($json)) $json = true;
		else if (is_string($json) && strtolower($json) == 'json') $json = true;
		$this->respondWithJson = (bool)$json;
		return $this;
	}

	function register($key, $action){
		$key = "{$this->configStoreKey}.{$key}";
		Config::set($key, $action);
		return $this;
	}

	// check if action is defined in config tree - quick.new_user
	function isKnown($key){
		$key = "{$this->configStoreKey}.{$key}";
		$config = config($key);
		if (empty($config)) return false;
		if (is_string($config)) {
			$config = [ 'handle' => $config ];
		} else if (is_array($config)) {
			if (!isset($config['handle'])) {
				$config = [ 'handle' => $config ];
			}
		}
		return $config;
	}

	// quick
	function filter($group = null, $origins = null){
		$key = $this->configStoreKey;
		if (!is_null($group)) $key = "{$key}.{$group}";
		$actions = config($key);
		if (!empty($actions)) {
			$actions = collect($actions)
				->map(function($config) use ($origins) {
					$action = new ActionHandler();
					$action->init($config);
					if (!$action->isGuarded()) {
						if ($action->isOriginAllowed($origins)) return $action;
					}
					return false;
				})->filter(function($value){
					return (bool)$value;
				});
			return $actions;
		}
		return null;
	}

	function isGuarded(){
		return resolve('higg.auth')->isGuarded($this->guard);
	}

	function isOriginAllowed($origin = null){
		// @todo compare with allowed origins
		return true;
	}

	function icon(){
		return $this->icon;
	}

	function href(){
		return url($this->href);
	}

	function title(){
		return $this->title;
	}

	function routerLink(){
		return $this->routerLink;
	}

	function content(){
		return $this->content;
	}

	function panelPrefix(){
		/*
		if (isProvider()) {
			return business()->orgCode();
		} else if (isInternal()) {
			return 'admin';
		}
		*/
		return 'app';
	}

	function attrs(){
		$attributes = [];

		if ($this->href) $attributes['href'] = $this->href;
		if ($this->routerLink) $attributes['routerLink'] = $this->routerLink;
		if ($this->class) $attributes['class'] = $this->class;

		if (empty($attributes)) return '';
		$properties = collect($attributes)->map(function($property, $attr){
			return "{$attr}=\"{$property}\"";
		});
		if (!empty($properties)) return $properties->implode(" ");
		return '';
	}

	function getObject(){
		$objectName = is_array($this->handle) ? $this->handle[0] : $this->handle;
		if (function_exists($objectName)) {
			return $this;
		}
		if (empty($this->object)) {
			try {
				$object = resolve($objectName);
				if (!empty($this->query)) {
					$object = $object->getObject($this->query);
				}
				$this->object = $object;
			} catch (\Exception $ex) {
				// Higg::error('Action handler does not exist. ' . $ex->getMessage());
				Higg::error($ex->getMessage());
			}
		}
		return $this->object;
	}

	function getHandler(){
		$object = $this->getObject();
		if (!empty($object)) {
			if ($object instanceof ActionHandler) {
				$object->init($this->config());
				return $object;
			}
		}
		return $this;
	}

	function getQuery($field = null, $else = null){
		if (is_null($field)) return $this->query;
		return array_get($this->query, $field, $else);
	}

	function getData($field = null, $else = null){
		if (is_null($field)) return $this->data;
		return array_get($this->data, $field, $else);
	}

	function getParam($field = null, $else = null){
		if (is_null($field)) return $this->params;
		return array_get($this->params, $field, $else);
	}

	// user.new_customer
	function exec(){

		if (!$this->handle) return null;

		if (!$this->isGuarded()) {
			if ($this->isOriginAllowed()) {

				if (is_string($this->handle) && function_exists($this->handle)) {

					$this->output = call_user_func($this->handle, $this->data);

				} else {

					$methodName = 'handle';
					if (is_array($this->handle) && isset($this->handle[1])) {
						$methodName = $this->handle[1];
					}

					try {

						$object = $this->getObject();

						if (method_exists($object, 'boot')) {
							$response = $object->boot();
							if (!is_null($response)) {
								return $response;
							}
						}

						if (method_exists($object, $methodName)) {
							$response = call_user_func([$object, $methodName], $this );
							if (is_array($response)) {
                                if (isset($response['error'])) {
                                    Higg::error($response['error']);
                                    unset($response['error']);
                                }
                                if (isset($response['success'])) {
                                    Higg::success($response['success']);
                                    unset($response['success']);
                                }
                            }
                            $this->output = $response;
						} else {
							Higg::error('Action handler does not know what to do.');
						}

					} catch (\Exception $ex) {

						if (config('higg.bugsnag.handle', false)) {
							// $bugsnag = Bugsnag::make(env('BUGSNAG_API_KEY'));
							$bugsnag = \Bugsnag::make(config('higg.bugsnag.api_key'));
							$bugsnag->notifyException($ex);
						}

						Higg::error($ex->getMessage());
					}

				}

			} else Higg::error('Action not allowed from this origin');
		} else Higg::error('Action not permitted');

		return $this->output;

	} // end exec

	function hasError(){
		return Higg::hasError();
	}

	function respond($result = null){

		if ($result instanceof JsonResponse) {
			return higg()->respondWithJson($result);
		}

		if (is_null($result)) {
			// if (!isset($this->output['data'])) $result = [ 'data' => $this->output ];
			// else $result = $this->output;
			$result = $this->output;
		}

		if (is_bool($result)) {
			$result = [
				'error' => Higg::error(),
				'success' => Higg::success(),
				'data' => $this->output
			];
		}
		/*
		else {
			if (!isset($result['data'])) {
				$result = [ 'data' => $result ];
			}
		}
		*/

		if (!isset($result['error']) && Higg::error()) $result['error'] = Higg::error();
		if (!isset($result['success']) && Higg::success()) $result['success'] = Higg::success();

		$html = $this->renderView($result);
		if (!empty($html)) $result['html'] = $html;
		return response()->json($result);

		/*
		if ($this->respondWithJson) {

			$html = $this->renderView($result);
			if (!empty($html)) $result['html'] = $html;
			return response()->json($result);

		} else {

			if (empty($this->view)) {
				$this->content = $this->htmlResponse($result);

				$object = $this->getObject();
				if (method_exists($object, 'htmlResponse')) {
					$this->content = call_user_func( [$object, 'htmlResponse'], $result );
				}

				return response($this->content);
			} else {
				return response()->view($this->view, $result);
			}

		}
		*/

	}

	function toArray(){
		return [
			'data' => $this->data,
			'query' => $this->query,
			'config' => [
				'key' => $this->key
			]
		];
	}

	function htmlResponse($result = null){
		if (empty($result)) $result = [];
		$error = array_get($result, 'error', false);
		$success = array_get($result, 'success', false);
		if ($error) {
			if (!is_string($error)) $error = 'Something went wrong!';
			return '<div class="alert alert-danger"><button class="close" data-close="alert"></button><span> '.$error.' </span></div>';
		} else {
			if (!is_string($success)) $success = 'completed, your action has.';
			return '<div class="alert alert-danger"><button class="close" data-close="alert"></button><span> '.$success.' </span></div>';
		}
	}



} // end Action
