<?php

namespace  Efemer\Higg\Factory\Handlers;

use Efemer\Higg\Factory\Core\HiggModel;
use Illuminate\Support\Str;

class FormHandler {

	public $name;
	public $controls = [];
	/** @var HiggModel $model */
	public $model;

	public $data = [];
	public $query = [];
	public $submitAction = false;

	public $view;
	public $guard;
	public $context;
	public $messages;
	public $actions;
	public $tools;

	public $isNew = null;
	public $output;     // is processed

	function __construct($map = null, $body = null){
		if (!is_null($map)) {
			$this->getForm($map, $body);
		}
	}

	function canRead(){
		return $this->model->can(ACCESS_PRIVILEGE_READ);
	}

	function canEdit(){
		return $this->model->can(ACCESS_PRIVILEGE_EDIT);
	}

	function getForm($map, $body = null){
		$parts = explode(".", $map);
		$formName = array_pop($parts);
		$modelAlias = implode('.', $parts);
		$model = app()->make($modelAlias);
		$model = $model->newModel();
		$config = $model->getFormConfig($formName);
		abort_unless($config, '404', $modelAlias . ' does not have any form by this name.');

		$config['model'] = $model;
		$config['name'] = $formName;
		return $this->initFrom($config, $body);
	}

	function initFrom($config, $body = null){

		$this->name = array_get($config, 'name');
		$this->controls = array_get($config, 'controls');
		$this->model = array_get($config, 'model');
		$this->messages = array_get($config, 'messages');
		$this->context = array_get($config, 'context');
		$this->view = array_get($config, 'view');
		$this->guard = array_get($config, 'guard');
		$this->actions = array_get($config, 'actions');
		$this->tools = array_get($config, 'tools');

		$this->parseRequest($body);
		$this->initHandle();
		$this->isNew = $this->model->isNew();

		return $this;
	}

	function initHandle($object = null, $query = null){
		if (is_null($query)) $query = $this->query;
		if (is_null($object)) $object = $this->model;
		if (is_object($object) && $object instanceof HiggModel) {
			$this->model = $object;
		} else {
			$this->model = resolve($object);
			$this->model = $this->model->newModel();
		}
		if (!empty($query)) {
			$found = $this->model->findOne($query);
			if ($found) {
				$this->model = $found;
			}
			//abort_unless($found, HTTP_NOT_FOUND, $this->formName() . ' failed to find the model');
		} else {
			if (!$this->isSubmitted()) {
				if (!empty($this->data))
					$this->model->silentAssign($this->data);
			}
		}
		return $this;
	}

	function initModelForm($model, $formName, $body = null){
		$config = $model->getFormConfig($formName);
		$config['model'] = $model;
		$config['name'] = $formName;
		return $this->initFrom($config, $body);
	}

	function parseRequest($post = null){
		$post = (is_null($post)) ? post() : $post;
		$this->data = array_get($post, 'data', []);
		$this->query = array_get($post, 'query', []);
		$this->setAction(array_get($post, 'action', false));
		return $this;
	}

	function isCreated(){
		return $this->isNew === true;
	}

	function isSubmitted(){
		return !empty($this->submitAction);
	}

	function getManifest(){
		$state = [
			'controls' => $this->getFormControls(),
			'actions' => $this->getFormActions(),
			'data' => $this->getFormData(),
			'query' => $this->getFormQuery(),
			'tools' => $this->getFormTools()
		];
		return $state;
	}

	function getControlConfig(){
		if (!is_array($this->controls) && $this->model->hasHandle($this->controls)) {
			$this->controls = $this->model->handle($this->controls, $this);
		}
		return $this->controls;
	}

	function getFormControls(){
		$controls = $this->getControlConfig();
		if (empty($controls)) return [];
		$data = $this->getFormData();
		$editors = [];
		foreach ($controls as $key => $control) {
			if (!is_array($control)) $control = [ 'key' => $control ];
			if (!isset($control['key'])) $control['key'] = $key;
			$field = $this->model->getField($control['key']);
			$control = $field->toEditorConfig($control, $data);
			$editors[] = $control;
		}
		return $editors;
	}

	function getFormControlValues(){
		$controls = $this->getFormControls();
		$values = [];
		foreach ($controls as $key => $control) {
			$values[$control['key']] = $control['value'];
		}
		return $values;
	}

	function getFormActions(){
		if (empty($this->actions)) {
			return $this->actions = [
				[ 'action' => 'submit', 'label' => 'Submit', 'color' => 'green' ],
				[ 'action' => 'reset', 'label' => 'Reset', 'color' => 'default', 'confirm' => true, 'warning' => 'This will discard changes you\'ve made, sure?' ]
			];
		}
		$filtered = [];
		foreach ((array)$this->actions as $action) {

			// skip this action if off method returns true
			if ( isset($action['off']) && method_exists($this->model, $action['off']) ) {
				$action['off'] = call_user_func( [ $this->model, $action['off'] ] );
				if ($action['off']) continue;
			}

			// only include this action if on method returns true
			if ( isset($action['on']) && method_exists($this->model, $action['on']) ) {
				$action['on'] = call_user_func( [ $this->model, $action['on'] ] );
			}

			$filtered[] = $action;
		}

		return $filtered;
	}

	function getFormTools(){
		return !empty($this->tools) ? (array)$this->tools : [];
	}

	function getFormKeys(){
		$controls = array_get($this->formDefinition, 'controls');
		$keys = collect($controls)->map(function($control){
			return $control['key'];
		});
		return $keys;
	}

	function getFormData(){
		// call custom data only when form is not submitted
		if (!$this->isSubmitted()) {
			$data = $this->model->handle($this->camelName().'Data', $this);
			if (!empty($data)) return $data;
		}
		/*
		if (empty($this->data)) {
			$data = $this->model->handle($this->camelName().'Data', $this);
			if (!is_null($data)) return $data;
			return $this->model->getData();
		}
		*/
		return $this->data;
	}

	function getFormQuery(){
		return $this->query;
	}

	function resetOutput(){
		return $this->output = [
			'error' => false,
			'success' => false,
			// 'data' => $this->getFormData(),
			// 'controls' => $this->getFormControls(),
		];
	}

	function response($transform = null){
		array_set($this->output, 'controls', $this->getFormControls());
		// pr($this->output);
		array_set($this->output, 'data', $this->getFormData());
		array_set($this->output, 'query', $this->getFormQuery());

		switch ($transform) {
			case 'response':
				return \Higg::respondWithJson($this->output);
			case 'json':
				return json_encode($this->output);
			case 'request':
				return 'request';
			default:
				return $this->output;
		}

	}

	function setAction($action){
		$this->submitAction = strtolower($action);
	}

	function setError($error = null){
		$modelError = $this->model->error();
		$formError = array_get($this->messages, 'error');
		$defaultError = $this->formName() . ' raised error.';
		if (is_null($error)) {
			if (!empty($modelError)) $error = $modelError;
			else if (!empty($formError)) $error = $formError;
			else $error = $defaultError;
		}
		array_set($this->output, 'error', $error);
	}

	function error(){
		return array_get($this->output, 'error', false);
	}

	function setSuccess($success = null){
		if (is_null($success)) {
			$modelSuccess = $this->model->success();
			$formSuccess = array_get($this->messages, $this->submitAction);
			$formDefaultSuccess = array_get($this->messages, 'success');
			$defaultSuccess = $this->formName() . ' has been processed with ease';

			if (!empty($modelSuccess)) $success = $modelSuccess;
			else if (!empty($formSuccess)) $success = $formSuccess;
			else if (!empty($formDefaultSuccess)) $success = $formDefaultSuccess;
			else $success = $defaultSuccess;
		}
		array_set($this->messages, 'success', $success);
		array_set($this->output, 'success', $success);
	}

	function success(){
		return array_get($this->output, 'success', false);
	}

	function beforeForm(){
		$before = $this->camelName().'Before';
		if (!$this->model->hasHandle($before)) $before = 'beforeForm';
		return $this->model->handle($before, $this);
	}

	function afterForm(){
		$after = $this->camelName().'After';        // formNameAfter
		if (!$this->model->hasHandle($after)) $after = 'afterForm';
		$this->model->handle($after, $this);

		// return id as query for new records
		if ($this->isNew && empty($this->query)) {
			$this->query = [ 'id' => $this->model->primaryId() ];
		}

	}

	function nativeFormHandler(){
		$method = $this->camelName().ucwords($this->submitAction);   // formNameSubmit
		if (!$this->model->hasHandle($method)) $method = 'formSubmit';
		return $this->model->handle($method, $this);
	}

	function isAction($action){
		return strtolower($this->submitAction) == strtolower($action);
	}

	function isTransform($value) {
		$transform = post('transform');
		return $transform == $value;
	}

	function handleSubmit($action = null){
		$this->resetOutput();
		if (is_null($action)) $action = $this->submitAction;
		else $this->setAction($action);

		if (!$this->isSubmitted()) {
			$this->setError('Form has not meet submission requirement');
			return $this->response();
		}

		$data = $this->getFormData();

		if ($this->isGuarded()) {
			$this->setError();
			return $this->response();
		} // permitted to process this form

		if ($this->beforeForm() === FALSE) {
			$this->setError();
			return $this->response();
		} // beforeForm or formNameBefore handler check

		$handled = $this->nativeFormHandler();
		// vd($handled);
		if (is_null($handled)) {
			//pr($this->model->toArray());
			$this->model->assign($data);
			// pr($this->model->error());
			// pr($this->model->toArray());
			if(!$this->model->error()) {
				$handled = $this->handle($action);
			}
		} // no native form handler found

		if ($handled === true) {
			$this->setSuccess();
			$this->afterForm();
		} else {
			$this->setError();
		}

		return $this->response();

	}

	function handle($action = 'submit'){

		switch($action) {

			case 'delete':
				if (!$this->model->isNew()) {
					$this->model->delete();
					return true;
				}
				return false;

			case 'submit':
				return $this->model->store();


		} // end switch

	}

	function camelName(){
		$key = str_replace(['-', '.'],'_',$this->name);
		return Str::camel($key);
	}

	function formName(){
		$key = str_replace(['-', '.'],' ',$this->name);
		return Str::ucfirst($key);
	}

	function isGuarded(){
		$guarded = $this->model->handle($this->camelName() . 'Guard');
		// @todo do global guard
		return is_null($guarded) ? false : $guarded;
	}

	function canHandleAction($action){
		$actions = array_get($this->formDefinition, 'actions');
		return in_array($action, $actions);
	}

	function canHandleCreate(){
		return $this->canHandleAction('create');
	}
	function canHandleUpdate(){
		return $this->canHandleAction('update');
	}
	function canHandleDelete(){
		return $this->canHandleAction('delete');
	}

	function isSucceeded(){
		return !!array_get($this->output, 'success');
	}

	function object(){
		return $this->model;
	}

	function controlsToJsonRequest() {
		$controls = $this->getFormControls();
		$data = [];
		foreach ($controls as $control) {
			$data[$control['key']] = is_null($control['value']) ? '' : $control['value'];
		}
		return [ 'data' => $data, 'action' => '', 'query' => '' ];
	}

} //