<?php

namespace Efemer\Higg\Factory\Core;

use Carbon\Carbon;
use Validator;
use Illuminate\Support\Str;

class HiggField {

	public $model;
	public $fieldConfig;

	public $key;
	public $value;
	public $originalValue;
	public $default;                // fieldNameDefaultValue
	public $cast;                   // fieldNameCast
	public $length;
	public $label;
	public $description;
	public $help;                   // fieldNameHelp
	public $placeholder;            // fieldNamePlaceholder
	public $hidden;                   // fieldNameHint
	public $order;
	public $options;                // fieldNameOptions
	public $validations;            // fieldNameValidate
	public $guarded;                // fieldNameGuard
	public $readOnly;
	public $handles;
	public $editor;                 // [ type, validators ]
	public $silentAssign;
	public $index;

	public $error = false;

	function __construct(HiggModel &$model, $key){
		$this->model = $model;
		$this->key = $key;
		$this->init();
	}

	function init(){

		$fieldConfig = $this->model->getFieldConfig($this->key);
		if (is_string($fieldConfig)) $fieldConfig = [ 'label' => $fieldConfig ];
		$this->fieldConfig = $fieldConfig;

		$this->cast = $this->config('cast', CAST_STRING);
		$this->length = $this->config('length');
		$this->label = $this->getLabel();
		$this->index = $this->config('index');
		$this->description = $this->config('description', '');
		$this->help = $this->getHelp();
		$this->placeholder = $this->getPlaceholder();
		$this->hidden = $this->config('hidden', false);
		$this->guarded = $this->isGuarded();
		$this->readOnly = $this->isReadOnly();
		$this->silentAssign = $this->config('silent');

		$this->default = $this->getDefaultValue();
		$this->order = $this->config('order', 0);
		$this->options = $this->config('options', []);
		$this->editor = $this->getEditorConfig();
		$this->handles = $this->config('handles', []);

		$this->validations = $this->config('validations', '');
		$this->value = $this->keyValue();

	}

	function toEditorConfig($formEditorConfig = [], $data = []) {

		$config = [];
		$config['name'] = $this->camelName();
		$config['key'] = $this->key;
		$config['label'] = array_get($formEditorConfig, 'label', $this->label);
		$config['order'] = array_get($formEditorConfig, 'order', $this->order);
		$config['readonly'] = array_get($formEditorConfig, 'readonly', $this->isReadOnly());
		$config['actions'] = array_get($formEditorConfig, 'actions', false);

		$editor = $this->editor;
		$config['editor'] = array_get($formEditorConfig, 'editor', array_get($editor, 'type', EDITOR_TEXT));
		$config['validators'] = array_get($formEditorConfig, 'validators', array_get($editor, 'validators', []));

		$config['params'] = array_get($formEditorConfig, 'params', []);
		$config['params'] = array_merge($editor, $config['params']);
		if (!isset($config['params']['type']))
			$config['params']['type'] = $config['editor'] == EDITOR_TEXT ? 'text' : '';
		if (!empty($this->help)) $config['params']['help'] = $this->help;
		if (!empty($this->placeholder)) $config['params']['placeholder'] = $this->placeholder;
		if (!empty($this->class)) $config['params']['class'] = $this->class;
		if ($this->error) {
			$config['params']['state'] = 'error';
			$config['params']['help'] = $this->error;
		}

		// attach csrf token for file input
		if ($config['editor'] == 'fileinput') {
			$config['params']['csrf'] = csrf_token();
			$config['params']['thumbUrl'] = thumb_url($this->keyValue());
		}

		$default = array_get($formEditorConfig, 'default');
		if (!is_null($default)) $this->fieldConfig['default'] = $default;
		$config['value'] = $this->keyValue($default);
		if (isset($data[$this->key])) $config['value'] = $data[$this->key];

		$options = $this->getOptions(array_get($formEditorConfig, 'options'));
		if (!empty($options)) {
			$config['options'] = [];
			foreach ($options as $k => $v) {

				if ( is_array($v) && !isset($v['label']) ) {

					$opts = [];
					foreach ($v as $o => $ov) {
						$v = [ 'label' => $ov ];
						$v['value'] = $o;
						$opts[] = $v;
					}
					$config['options'][] = [ 'label' => ucwords($k), 'options' => $opts ];

				} else {

					if (!is_array($v)) $v = [ 'label' => $v ];
					$v['value'] = $k;
					$config['options'][] = $v;

				}

			}
		}

		// construct validators from validation rules
		if (!empty($this->validations) && empty($config['validators'])) {
			$rules = explode('|', $this->validations);
			foreach ($rules as $rule) {
				if (strpos($rule, 'required') === 0) $config['validators']['required'] = true;
				elseif (strpos($rule, 'min') === 0) $config['validators']['minLength'] = explode(':',$rule)[1];
				elseif (strpos($rule, 'max') === 0) $config['validators']['maxLength'] = explode(':',$rule)[1];
				elseif (strpos($rule, 'regex') === 0) $config['validators']['pattern'] = explode(':',$rule)[1];
			}
		}

		return $config;
		//return array_merge($config, $formEditorConfig);
	}

	function setValue($newValue){

		if (is_null($newValue)) {
			$newValue = $this->getDefaultValue();
		}

		$this->changeValue($newValue);

		$args = [ &$this, $newValue];

		// GUARD
		$guarded = $this->handle(HANDLE_KEY_GUARD, $args);
		if ($guarded === true) {
			$else = 'Much force you must have to edit ' . $this->label;
			$error = $this->getError($else);
			$this->setError($error);
			// $this->resetValue();
			return false;  // quit for $guarded key
		}

		// EDIT
		$handledValue = $this->handle(HANDLE_KEY_EDIT, $newValue);
		if (!is_null($handledValue)) {
			$newValue = $this->value = $handledValue;
		}

		// VALIDATE
		$handledValue = $this->handle(HANDLE_KEY_VALIDATE, $newValue);
		if ($handledValue === false) {
			$else = "Wrong you have done with " . $this->label;
			$error = $this->getError($else);
			$this->setError($error);
			// $this->resetValue();
			return false; // quit for invalid value
		}

		if (!$this->isValid()) return false;

		$this->castValue($this->value, $args);
		$this->flushKeyValue();

	}

	function setError($message){
		$this->error = $message;
		$this->model->error($message);
	}

	// handle validation rule
	function isValid(){

		if (!empty($this->validations)) {
			$assignableKey = $this->keyPath();
			$rules = [ $assignableKey => $this->validations ];
			$data = [ $assignableKey => $this->value ];
			$validator = Validator::make($data, $rules );
			if ($validator->fails()) {
				/*
				$errors = $validator->errors();
				$messages = [];
				foreach ($errors->all() as $message) {
					$messages[] = $message;
				}
				$else = implode("\n", $messages);
				$error = $this->getError($else);
				$this->setError($error);
				*/
				$this->setError( 'Please provide relevant information for ' . $this->label);
				// $this->resetValue();

				return false; // quit with validation error
			}
		}

		return true;
	}

	function isBoolean(){ return $this->cast === CAST_BOOLEAN; }
	function isNumeric(){ return $this->cast === CAST_INTEGER; }
	function isDouble(){ return $this->cast === CAST_DOUBLE; }
	function isChar(){ return $this->cast === CAST_CHAR; }
	function isString(){ return $this->cast === CAST_STRING; }
	function isLongString(){
		return $this->cast === CAST_STRING && $this->length === -1;
	}
	function isDate(){ return $this->cast === CAST_DATETIME || $this->cast === CAST_DATE; }
	function isDateTime(){ return $this->cast === CAST_DATETIME; }
	function isArray(){ return $this->cast === CAST_ARRAY; }


	function castValue($value = null, $args = []){
		$value = is_null($value) ? $this->value : $value;

		$handle = $this->handle(HANDLE_KEY_CAST, $args);
		if (!is_null($handle)) return $handle;

		if (empty($value)) return null;

		switch($this->cast) {

			case CAST_ARRAY:
			case CAST_LIST:
				return (array)$this->value;

			case CAST_REAL:
			case CAST_INTEGER:
				$this->value = intval($value); break;

			case CAST_STRING:
			case CAST_CHAR:
				$this->value = strval($value);
				break;

			case CAST_FLOAT:
			case CAST_DOUBLE:
                $this->value = $value;
			    if (is_double($this->value)) {
                    $this->value = (double)$this->value;
                }
				break;

			case CAST_BOOLEAN:
				$this->value = boolval($value) == 1 ? 1 : 0; break;

			case CAST_DATETIME:
			case CAST_DATE:
				try {
					$date = new Carbon($value);
					$format = "Y-m-d";
					if ($this->cast == CAST_DATETIME) $format = "Y-m-d H:i:s";
					$this->value = $date->format($format);
				} catch (Exception $ex) {
					$this->setError($this->key . ' does not seem to have correct date.');
				}
				break;

			case CAST_TIME:
				$parts = explode(":", $value);
				$parts = array_map('intval', $parts);
				if (count($parts) == 1) {
					$this->value = "{$parts[0]}:00:00";
				} elseif (count($parts) == 2) {
					$this->value = "{$parts[0]}:{$parts[1]}:00";
				} elseif (count($parts) == 3) {
					$this->value = "{$parts[0]}:{$parts[1]}:{$parts[2]}";
				} else {
					$this->setError($this->key . ' does not seem to have correct time.');
				}
				break;

			case CAST_POINT:
				if (is_array($value)) $this->value = implode(" ", $value);
				break;

			case CAST_PASSWORD:
				// @TODO implement salted hash
				$this->value = \Higg::hash($this->value);
				break;

			case CAST_EMAIL:
				$this->value = strtolower(trim($value));
				break;
		}

		return true;
	}

	function serialize(){
		// if (empty($this->value)) return $this;
		// @todo handle serialize callback for field
		if ($this->cast == CAST_ARRAY && is_array($this->value())) {
			$this->value = json_encode($this->value);
			$this->flushKeyValue();
		}
		else if ($this->cast == CAST_LIST && is_array($this->value())) {
			$this->value = json_encode($this->value);
			$this->flushKeyValue();
		}
		return $this;
	}

	function deserialize(){
		// @todo handle deserialize callback for field

		if (empty($this->value)) return $this;

		if ($this->cast == CAST_ARRAY && is_string($this->value)) {
			$this->value = json_decode($this->value, true);
		}
		else if ($this->cast == CAST_LIST && is_string($this->value)) {
			$this->value = json_decode($this->value, true);
		}
		else if ($this->cast == CAST_DATETIME) {
			$this->value = higg()->datetime($this->value);
		}

		$this->flushKeyValue();
		return $this;
	}

	function humanizeOn(){
		return $this->config('humanize', true);
	}

	function humanize(){
		$value = $this->value();
		$fieldName = $this->camelName();
		$humanize = $this->model->handle($fieldName . 'Humanize', $this->value );
		/*
        $options = $this->getOptions();
        if (is_null($value)) {
            switch($this->cast) {
                case CAST_DATETIME:
                    // $value = Higg::datetime($this->value);
                    $value = $this->model->humanizeDateTime($this->value);
                    break;
                default:
                    $value = $this->value();
                    if ( !is_array($value) && isset($options[$value])) {
                        $value = $options[$value];
                    }
                    // end default
            }
        }
		*/
		return is_null($humanize) ? $value : $humanize ;
	}

	function humanizeOptions(){
		$options = $this->getOptions();
		$value = $this->value();
		if (!empty($options) && is_array($options)) {
			return array_get($options, $value);
		}
		return $value;
	}

	// options
	function handle($key_handle, $args = null){

		$defaultMethod = Str::camel($this->camelName() . ' ' . $key_handle);
		$definedMethods = array_get($this->handles, $key_handle);
		if (!is_array($definedMethods)) $definedMethods = [ $definedMethods ];
		$definedMethods[] = $defaultMethod;

		$return  = null;
		foreach ($definedMethods as $method) {
			if (!empty($method)) {
				$called = $this->model->handle($method, $args);
				if (!is_null($called)) $return = $called;
			}
		}
		return $return;
	}

	function keyParent(){
		return explode(".", $this->key)[0];
	}
	function keyPath(){
		return trim(str_replace($this->keyParent(), '',$this->key), '.');
	}

	function keyValue($default = null){
		$default = is_null($default) ? $this->getDefaultValue() : $default;
		$parent = $this->keyParent();
		$path = $this->keyPath();

		$value = $this->handle(HANDLE_KEY_READ, $this);
		if (!is_null($value)) return $value;

		// $stash = array_get($this->model->toArray(), $parent, []);
		// $stash = isset($this->model[$parent]) ? $this->model[$parent] : null;
		$stash = $this->model->getValue($parent);
		if (!is_null($stash)) {
			if (empty($path)) $value = $stash;
			else if (is_array($stash)) $value = array_get($stash, $path);
		}

		return is_null($value) ? $default : $value;
	}

	function flushKeyValue(){
		$value = $this->value;
		$parent = $this->keyParent();
		$stash = array_get($this->model, $parent, []);
		$path = $this->keyPath();

		if (empty($path)) {
			$this->model->{$parent} = $value;
		} else {
			if (!is_array($stash)) {
				$stash = json_decode($stash, true);
			}
			array_set($stash, $path, $value);
			$this->model->{$parent} = $stash;
		}
	}


	function getDefaultValue(){
		$default = $this->handle(HANDLE_KEY_DEFAULT_VALUE);
		if (is_null($default)) $default = $this->config('default');
		return $default;
	}

	function isVirtual(){
		return !!$this->config('virtual', false);
	}

	function getLabel(){
		$else = Str::ucfirst($this->key);
		return $this->config('label', $else);
	}

	function getHelp(){
		$hint = $this->handle(HANDLE_KEY_HELP);
		if (is_null($hint)) {
			$hint = $this->config('help', '');
		}
		return $hint;
	}

	function getPlaceholder(){
		$hint = $this->handle(HANDLE_KEY_PLACEHOLDER);
		if (is_null($hint)) {
			$hint = $this->config('placeholder', '');
		}
		return $hint;
	}

	function isGuarded(){
		$guarded = $this->handle(HANDLE_KEY_GUARD);
		if (is_null($guarded)) $guarded = $this->config('guard');
		return $guarded;
	}

	function isHidden(){
		return $this->hidden;
	}

	function allowSilentAssign(){
		return !!$this->silentAssign;
	}

	function isReadOnly(){
		if ($this->model->isNew()) return false;
		return $this->config('readonly', false);
	}

	function getOptions($options = null){
		if (is_string($options)) {
			$options = $this->model->handle($options);
		} else {
			$customOptions = $this->handle(HANDLE_KEY_OPTIONS);
			if (!empty($customOptions)) $options = $customOptions;
			if (is_null($options)) {
				$options = $this->config('options', []);
			}
		}
		return $options;
	}

	function getEditorConfig($key = null, $else = null){
		$editor = $this->config('editor', EDITOR_TEXT);
		if (!is_array($editor)) $editor = [ 'type' => $editor ];
		if (!is_null($key)) return array_get($editor, $key, $else);
		return $editor;
	}

	function changeValue($newValue){
		// if ( $this->model->locked && (!$this->model->isNew() && $this->isReadOnly()) ) return false;
		// only changes value if its a new model and field config set to readonly false
		if ($this->isReadOnly()) return false;
		$this->originalValue = $this->value;
		$this->value = $newValue;
		return true;
	}

	function resetValue(){
		$this->value = $this->originalValue;
	}

	function value(){
		$this->value = $this->keyValue();
		return $this->value;
	}

	function camelName($key = null){
		if (is_null($key)) $key = $this->key;
		$key = str_replace('.','_',$this->key);
		return Str::camel($key);
	}

	function getError($else = null){
		$error = $this->error;
		$this->error = false;
		return empty($error) ? $else : $error;
	}

	function config($config, $else = null){
		return array_get($this->fieldConfig, $config, $else);
	}

	function __toString(){
		if (is_array($this->value)) return json_encode($this->value);
		return (string)$this->value;
	}




} // end HiggModelField
