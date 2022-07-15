<?php

namespace Efemer\Higg\Controllers;

use Efemer\Higg\Factory\Handlers\ActionHandler;
use Efemer\Higg\Factory\Handlers\BrowserHandler;
use Efemer\Higg\Factory\Handlers\FormHandler;
use Illuminate\Support\Str;
use Response;
use Higg;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class HiggApiController extends BaseApiController {

	// get form manifest and data
	// Route::match(['GET','POST'],    '/form/{formMap}/{submit?}', 'ApiController@handleForm');
	function handleForm(){

		$formMap = $this->routeParams('formMap');
		if (empty($formMap)) {
			$formMap = $this->routeParams('action');
		}
		// $submit = $this->routeParams('action');
		$body = post();

		$form = new FormHandler($formMap, $body);

		if ($form->isSubmitted()) {
			if ($form->canEdit()) {
				$response = $form->handleSubmit();
			} else {
				abort(HTTP_FORBIDDEN, "You are not allowed in this area.");
			}
		} else {
			if ($form->canRead()) {

				if ($form->isTransform('request')) {
					$jsonRequestSample = $form->controlsToJsonRequest();
					return higg()->respondWithJson($jsonRequestSample);
				} else {
					$response = $form->getManifest();
				}

			} else {
				abort(HTTP_FORBIDDEN, "You are not allowed in this area.");
			}
		}
		return Response::json($response);

		/*
		$formHandler = new FormHandler($params['object'], $params['formName'], $post);
		if ($formHandler->isSubmitted()) {      // has data in post()
			$action = array_get($params, 'action', 'submit');
			$response = $formApi->submitForm($action);
		} else {
			$response = $formApi->getFormManifest();
		}

		return Response::json($response);
		*/

	} // process form


	// object, where, select
	// Route::match(['GET','POST'],    '/browser/{object}/{transform?}', 'ApiController@handleBrowser');
	function handleBrowser($params = null){

		$defaults = [
			'page' => false,
			'limit' => 10,
			'table' => true,
			'humanize' => true,
			'where' => [],
			'transform' => ''
		];

		$post = post();
		$params = $this->routeParams();
		if (isset($params['action'])) {
			$params['object'] = $params['action'];
		}
		$options = array_merge($defaults, $params, $post);
		$browserApi = new BrowserHandler($params['object']);
		$response = $browserApi->query($options);

		return Response::json($response);

	}

	// api/action/key - POST data, query, json
	function handleAction(){

		$params = $this->routeParams();
		if (isset($params['action'])) $actionKey = $params['action'];
		else $actionKey = array_get($params, 'key');

		$transform = array_get($params, 'transform');

		if (is_null($transform)) $json = null;
		else $json = ($transform == 'json') ? true : false;

		$handler = new ActionHandler();
		if ($handler->isKnown($actionKey)) {

			$handler->init($actionKey);
			$customHandler = $handler->getHandler();
			if ($customHandler instanceof ActionHandler) {
				$handler = $customHandler;
			}

			$result = $handler->returnJson($json)     // content type
			                  ->parseRequest()    // parse post data
			                  ->exec();       // handle

			if ($result instanceof \Illuminate\Http\Response) {
				return $result;
			} else if ($result instanceof BinaryFileResponse) {
				return $result;
			}

		} else {
			if (!Higg::hasError()) Higg::error('Unknown action requested.');
		}

		return $handler->respond();
	}

	function handleMethod(){

		$method = $this->routeParams('method');
		$method = Str::camel($method);
		$operation = $this->routeParams('operation');
		if (method_exists($this, $method)) {
			return call_user_func([$this, $method], $operation);
		} else if (function_exists($method)) {
			return call_user_func($method, $operation);
		}

	}




} // end
