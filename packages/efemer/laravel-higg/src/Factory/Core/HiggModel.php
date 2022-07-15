<?php

namespace Efemer\Higg\Factory\Core;

use Carbon\Carbon;
use Efemer\Higg\Factory\Handlers\FormHandler;
use Efemer\Higg\Helpers\ElasticSearchHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\MessageBag;
use Efemer\Higg\Factory\Traits\FormHandlerTrait;
use Illuminate\Support\Str;
use Illuminate\Console\Command;

class HiggModel extends Model {

	use FormHandlerTrait;

	public $alias = 'model';

	protected $fields = [
		'id' => [ 'cast' => CAST_INTEGER, 'name' => 'ID' ]
	];

	/*
	 * example fields
	private $fields = [
		'id' => [ 'cast' => CAST_INTEGER, 'name' => 'ID' ],

		'username' => [
			'label' => 'Username',
			'description' => 'Unique account name',
			'hint' => 'john.doe',
			'readonly' => false,
			'validations' => 'required|min:5|max:100',      // auto handler fieldNameValidation
			'guard' => false,
			'map' => false,                                 // associated
			'editor' => 'text',
			'order' => 1,
			'default' => '',
			'handles' => [
				'format' => null,                           // auto handler fieldNameFormat
				'read' => null,                             // auto handler fieldNameRead
				'edit' => null,                             // auto handler fieldNameEdit
				'delete' => null,                           // auto handler fieldNameDelete
				'guard' => null,                            // auto handler fieldNameGuard
				'validate' => null,                         // auto handler fieldNameValidation
				'cast' => null,                             // auto handler fieldNameCast
				'map' => null,                              // auto handler fieldNameMap
			],
			'events' => null
		],

		'name' => [
			'cast' => CAST_STRING,
			'name' => 'Name',
			'protected' => true,
			'validation' => 'required|min:1',
			'readonly' => true
		],
		'params.facebook_id' => [ 'cast' => CAST_STRING, 'name' => 'Facebook ID' ],
	];
	*/

	// date mutators
	protected $dates = [
		'created_at',
		'updated_at',
	];
	// date storage format
	protected $dateFormat = 'Y-m-d H:i:s';      // U

	// touch associated models
	// protected $touches = ['post'];

	// protected $casts = [
	//    'is_admin' => 'boolean',
	// ];


	protected  $forms = [];
	protected  $browser = [];
	protected  $columns = [];            // table columns
	protected  $cached = [];              // cached queries
	protected  $activities = [];           // model log
	protected  $notices = null;          // action messages - error, info
	protected  $changed = [];
	protected  $snapshot = [];
	protected  $handles = [           // model action callbacks
		'beforeRead' => null,       // record read
		'afterRead' => null,
		'beforeSave' => null,       // record save
		'afterSave' => null,
		'beforeDelete' => null,
		'afterDelete' => null,

	];

	protected $table = 'samples';
	private $locked = false;

	function __construct(){
		parent::__construct();
		$this->notices = new MessageBag();
	}

	// assign -- set field values
	function assign($data, $skipRequiredCheck = false){
		$this->reset();
		// $this->takeSnapshot();

		$edited_keys = array_keys($data);
		$required_keys = [];
		if ($this->isNew() && !$skipRequiredCheck) {
			$fields = $this->getFields(true);
			$required_keys = array_keys($fields);
		}

		$fields = $this->getFields();
		$keys = [];
		foreach ($fields as $key => $field) {
			if (!isset($field['virtual']) || !$field['virtual']) {
				if (in_array($key, $edited_keys)) $keys[$key] = $key;
				else if (in_array($key, $required_keys)) $keys[$key] = $key;
			}
		}
		// if ($this->alias == 'project.terms_of_service') pr($keys);

		/*
		$orderedKeys = array_flip($keys); $order = 1;
		foreach ($orderedKeys as $k => $v) $orderedKeys[$k] = count($keys);
		foreach($this->getFields() as $fieldKey => $field) {
			if (in_array($fieldKey, $keys)) {
				$orderedKeys[$fieldKey] = $order;
			} else {
				// $orderedKeys[$fieldKey] = $order * -1;
			}
			$order++;
		}
		asort($orderedKeys);
		*/
		$keys = array_keys($keys);
		// pr($keys);
		// if ($this->alias == 'project.terms_of_service') pr($keys);
		// @todo apply order from config
		foreach($keys as $fieldKey) {
			$value = array_get($data, $fieldKey);
			$field = $this->getField($fieldKey);
			if ($field->isVirtual()) continue;
			if (empty_field_value($value)) {
				$value = $field->getDefaultValue();
			}
			$this->logFieldChanges($fieldKey, $value);
			$field->setValue($value);
		}

		return $this;

	}

	// @todo filter keys before mass assignment
	function silentAssign($data){               // DANGER
		foreach ($data as $col => $val) {
			if (strpos($col, '.') === false) {
				$this->{$col} = $val;
			} else {
				$parts = explode('.', $col);
				$parent = array_shift($parts);
				$valuePath = implode('.', $parts);
				$stash = array_get($this->model, $parent, []);
				array_set($stash, $valuePath, $val);
				$this->{$parent} = $stash;
			}
		}
		return $this;
	}

	/*
	// submit form
	function action($formName, $action = 'submit', $data = null, $query = null){

		$data = is_null($data) ? $this->getData() : $data;
		if (empty($query)) {
			if (!$this->isNew()) {
				$query = [ 'id' => $this->primaryId() ];
			}
		}

		$form = new FormHandler($this, $formName, [ 'data' => $data, 'query' => $query] );
		$form->submitForm($action);

	}
	*/

	// get fields config array
	function getFields($requiredOnly = false){
		if ($requiredOnly) {
			$fields = [];
			foreach ($this->fields as $key => $config) {
				if (is_array($config)) {
					$rules = array_get($config, 'validations', false);
					if ($rules) {
						if ((strpos($rules, 'required') !== false) || (strpos($rules, 'present') !== false)) {
							$fields[$key] = $config;
						}
					}
				}
			}
			return $fields;
		}
		return $this->fields;
	}
	// add field config to fields config array
	function addField($fieldName, $fieldConfig){
		$this->fields[$fieldName] = $fieldConfig;
	}
	function getFieldConfig($field){
		return array_get($this->getFields(), $field, [ 'label' => $field ]);
	}

	function getFieldValue($fieldKey){
		return $this->getField($fieldKey)->value();
	}

	// get raw value
	function getValue($key, $else = null){
		return array_get($this, $key, $else);
	}

	function pluckValues($keys, $raw = true){
		$values = [];
		foreach ($keys as $key) {
			if ($raw) $values[$key] = $this->getValue($key);
			else $values[$key] = $this->getFieldValue($key);
		}
		return $values;
	}

	function columnValue($column){
		return isset($this->{$column}) ? $this->{$column} : null;
	}

	function setFieldValue($fieldKey, $value){
		$this->getField($fieldKey)->setValue($value);
	}

	function setValue($column, $value){
		$field = $this->getField($column);
		$field->value = $value;
		$field->flushKeyValue();        // seriously!
	}

	// return HiggModelField object
	function getField($fieldKey, $reset = false) {
		if ($reset) return new HiggField($this, $fieldKey);
		if ($this->inCache($fieldKey)) {
			return $this->inCache($fieldKey);
		} else {
			$field = new HiggField($this, $fieldKey);
			return $this->inCache($fieldKey, $field);
		}
	}

	function isField($key){
		return isset($this->fields[$key]);
	}

	function primaryId(){
		// return $this->columnValue('id');
		return $this->columnValue($this->primaryKey);
	}

	function passwordValid($args){
		$field = $args[0];
		if (strlen($field->value) < 2) {
			$field->setError('Password needs to be more than 2 chars long');
			return false;
		}
		return true;
	}

	function preStore($options = []){
		$this->handle(HANDLE_BEFORE_STORE, $options);
		// serialize
		foreach ($this->fields as $fieldName => $fieldConfig) {

			// remove virtual fields
			if ( strpos($fieldName, '.') === FALSE && isset($fieldConfig['virtual']) && $fieldConfig['virtual']) {
				unset($this[$fieldName]);
				continue;
			}

			if (strpos($fieldName, '.') === FALSE) {
				$this->getField($fieldName)->serialize();
			}
		}

	}

	function store($data = null){

		$this->notices = new MessageBag();
		if (!empty($data)) $this->assign($data);
		// pr($this->toArray());
		// pr($this->error());

		$this->preStore();          // serialize and stuff

		if (!$this->error()) {
			try {
				// pr($this->toArray());
				$saved = $this->save();
				if ($saved) $this->postStore();
				return (bool)$saved;

			}
			catch (QueryException $ex) {
				logError($ex->getMessage());
				// \Bugsnag::notifyException($ex);
				$error = 'Failed to reach the information membrane!';
				if (is_local()) {
					$error = $ex->getMessage();
				}
				$this->error($error);
			}

		}

		return false;
	}

	function postStore(){
		// deserialize
		foreach ($this->fields as $fieldName => $fieldConfig) {
			if (strpos($fieldName, '.') === FALSE) {
				$field = $this->getField($fieldName);
				$field->deserialize();
			}
		}
		$this->handle(HANDLE_AFTER_STORE);
	}

	function handle($handle, $args = []){
		if (method_exists($this, $handle)) {
			return call_user_func([$this, $handle], $args);
		} else if (function_exists($handle)) {
			return call_user_func($handle, $args);
		}
		return null;
	}

	function hasHandle($handle){
		return method_exists($this, $handle);
	}

	function postRead(){
		foreach ($this->fields as $fieldName => $fieldConfig) {
			if (strpos($fieldName, '.') === FALSE) {
				$field = $this->getField($fieldName);
				$field->deserialize();
			}
		}
		return $this;
	}

	function takeSnapshot(){
		$this->snapshot = $this->toArray();
	}

	function logFieldChanges($fieldKey, $newValue){
		$current = $this->getValue($fieldKey);
		if (md5( !is_string($current) ? json_encode($current) : $current ) != md5( !is_string($newValue) ? json_encode($newValue) : $newValue )) {
			$this->changed[$fieldKey] = [
				'new' => $newValue,
				'previous' => $current
			];
		}
	}

	function changedFields(){
		return $this->changed;
	}

	function nothingChanged(){
		return empty($this->changedFields());
	}

	function isChanged($fieldKey){
		return isset($this->changed[$fieldKey]);
	}

	function getBrowserConfig(){
		if (empty($this->browser)) {
			$fields = [];
			foreach ($this->getFields() as $fieldName => $config) {
				$fields[] = $fieldName;
			}
			return [ 'fields' => $fields ];
		}
		return $this->browser;
	}

	function toHumanize($fields = null, $flatKey = true, $raw = false, $camelKey = true){
		if (is_null($fields)) $fields = array_keys($this->fields);
		$data = [];
		foreach ($fields as $fieldKey) {

			$field = $this->getField($fieldKey);
			// if ($field->cast == CAST_ARRAY) continue;
			if ( !$raw && $field->humanizeOn()) {
				$value = $field->deserialize()->humanize();
			} else {
				$value = $field->deserialize()->value();
			}

			if ($flatKey) {
				$key = $field->keyPath();
				if (empty($key)) $key = $field->keyParent();
			} else {
				$key = $fieldKey;
			}
			if ($camelKey) $key = $field->camelName($key);
			// pr($key);
			array_set($data, $key, $value);
			// $data[$key] = $value;
		}
		return $data;
	}

	function toFlatArray(){
		return $this->toHumanize(null, true, true, false);
	}

	function toData(){
		return $this->toHumanize(null, false, true, false);
	}

	function toCamelized($fields = null, $raw = null){
		return $this->toHumanize($fields);
	}

	function toTable($collection, $options = []){
		$browser = $this->getBrowserConfig();

		$humanize = array_get($options, 'humanize', true);
		$transform = array_get($options, 'transform', '');

		$showColumns = array_get($options, 'columns', false);

		$columns = []; $keys = [ 'id' ];

		// vd($humanize);

		foreach ($browser['fields'] as $fieldName => $fieldConfig) {
			if (is_array($fieldConfig)) $fieldConfig['key'] = $fieldName;
			else $fieldConfig = [ 'key' => $fieldConfig ];
			$keys[] = $fieldConfig['key'];

			$field = $this->getField($fieldConfig['key']);
			if (!$field->isGuarded()) {

				$fieldConfig['hidden'] = isset($fieldConfig['hidden']) ? true : false;
				if (!$fieldConfig['hidden']) {
					$fieldConfig['label'] = array_get($fieldConfig, 'label', $field->getLabel());
					$fieldConfig['order'] = $field->order;
					$fieldConfig['name'] = $field->camelName();
					$fieldConfig['params'] = array_get($fieldConfig, 'params', []);
					$fieldConfig['description'] = $field->description;
					$columns[] = $fieldConfig;
				}
			}

		}

		$rows = [];
		foreach ($collection as $model) {
			if (!empty($transform)) {
				if (method_exists($model, $transform)) {
					$rows[] = $model->$transform();
				} else {
					$rows[] = $model;
				}
			} else {
				$rows[] = $model->toHumanize($keys, !$humanize, !$humanize);
			}
		}

		$actions = array_get($browser, 'actions', false);
		$tools = array_get($browser, 'tools', false);

		$form = new FormHandler();
		$filterForm = $form->initModelForm($this, 'filter-form');
		$filters = $filterForm->getFormControls();

		$return = [
			'data' => $rows, 'count' => $collection->count(),
			// 'columns' => $showColumns ? $columns : [],
			// 'actions' => $actions, 'filters' => $filters, 'tools' => $tools
		];

		if ($showColumns) $return['columns'] = $columns;
		if (!empty($actions)) $return['actions'] = $actions;
		if (!empty($filters)) $return['filters'] = $filters;
		if (!empty($tools)) $return['tools'] = $tools;

		return $return;
	}

	// @todo implement field guard, populate virtual fields
	function getData($keys = null){
		$data = $this->toArray();
		if (!empty($keys)) $data = collect($data)->only($keys);
		return $data;
	}

	function counter($handle){
		if ($this->inCache($handle)) return $this->inCache($handle);
		$value = $this->handle($handle);
		if (empty($value)) $value = 0;
		return $this->inCache($handle, $value);
	}

	function humanized($keys = null, $flat = true){
		if (empty($keys)) $keys = $this->getFields();
		$data = [];
		foreach ($keys as $key => $config) {
			$field = $this->getField($key);
			if ($field->cast != CAST_ARRAY) {
				if ($flat) {
					$name = $field->camelName();
					$data[$name] = $field->humanize();
				} else {
					$data[$key] = [
						'key' => $key,
						'label' => $field->label,
						'name' => $field->camelName(),
						'raw' => $this->getValue($key),
						'value' => $field->humanize(),
					];
				}
			}
		}
		return $data;
	}

	// real columns
	function getColumnNames(){
		if (!empty($this->columns)) return $this->columns;
		$connection = $this->connection;
		$db = \DB::connection($connection);
		$sql = "SHOW COLUMNS FROM " . $this->getTable();
		$raw = $db->select($sql);
		$this->columns = array();
		foreach($raw as $c) {
			// $columns[$c->Field] = array( 'name' => $c->Field, 'type' => $c->Type );
			$this->columns[] = $c->Field;
		}
		return $this->columns;
	}

	function allowed($privilege = null){
		return $this->can($privilege);
	}

	function can($doWhat){
		$camel = Str::studly($doWhat);
		$method = "can$camel";
		$ret = $this->handle($method);
		if ($ret === FALSE) {
			logDebug('Failed attempt to ' . $doWhat . ' ' . class_basename($this), [ 'user_id' => isLogged() ? Auth::id() : '' ] );
		}
		return $ret === true;
	}

	// permission currently logged in user has on the initiated object
	function youCan(){
		if (isLogged()) {
			if ($this->can(ACCESS_PRIVILEGE_MANAGE)) return ACCESS_PRIVILEGE_MANAGE;
			else if ($this->can(ACCESS_PRIVILEGE_EDIT)) return ACCESS_PRIVILEGE_EDIT;
			else if ($this->can(ACCESS_PRIVILEGE_READ)) return ACCESS_PRIVILEGE_READ;
		}
		return '';
	}

	// reset model instance
	function reset(){
		$this->notices = new MessageBag();
		$this->changed = [];
	}

	function error($message = null){
		if (empty($message)) {
			if ($this->notices->has(NOTICE_ERROR)) {
				return $this->notices->first(NOTICE_ERROR);
			}
			return false;
		}
		$this->notices->add(NOTICE_ERROR, $message);
	}

	function success($message = null){
		if (empty($message)) {
			if ($this->notices->has(NOTICE_SUCCESS)) {
				return $this->notices->first(NOTICE_SUCCESS);
			}
			return false;
		}
		$this->notices->add(NOTICE_SUCCESS, $message);
	}

	// get from redis store if not than try from mysql
	function redisGetObject($id){
		$redis = redisClient();
		$key = "$this->alias:$id";
		$object = new static();
		if ($redis->isLive()) {
			if ($redis->exists($key)) {
				$data = $redis->json($key);
				return $object->silentAssign($data);
			} else {
				$object = $this->getObject($id);
				if (!empty($object)) {
					$redis->json($key, $object->toArray());
					return $object;
				}
				return;
			}
		}
		return $this->getObject($id);
	}

	function newModel(){
		// return new self();
		return new static();
	}

	function getObject($id) {
		if (empty($id)) return;
		if (!is_array($id)) {
			$where = [ $this->primaryKey => $id ];
		} else {
			$where = $id;
		}
		return $this->findOne( [ 'where' => $where ] );
	}

	function findOne($options = [], $last = false){
		if (!isset($options['where'])) {
			$options = [ 'where' => $options ];
		}
		if ($last) {
			$options['order'] = 'id desc';
		}
		$collection = $this->filter($options);
		if ($collection->count()) return $collection->first();
		return null;
	}

	// where, select, distinct, order, limit
	function filter($options = []) : Collection {

		$where = array_get($options, 'where', []);
		$select = array_get($options, 'select', []);
		$distinct = (bool)array_get($options, 'distinct', false);
		$order = array_get($options, 'order', []);
		$limit = array_get($options, 'limit', '10');
		$page = array_get($options, 'page', '1');

		$return = array_get($options, 'return', 'collection');

		$builder = $this->buildQuery(compact('where', 'select', 'distinct', 'order', 'limit', 'page'));

		switch($return) {
			default: $collection = $builder->get();
		}

		// pr($collection);

		$collection->each(function($model){
			// $model->postRead();
			// pr($model->toArray());
			return $model->postRead();
		});

		return $collection;
	}

	// provide [model, closure] or [joinWithModelName, where_clause]
	function join($join, &$builder = null){
		if (is_null($builder)) $builder = $this->newQuery();
		if (empty($conditions)) return $builder;

		if (is_object($join[0])) {

			$joiningModel = $join[0];
			$closure = $join[1];
			$joiningTable = $joiningModel->getTable();
			$builder->join($joiningTable, $closure);

			/*
			$selects = $this->getColumnNames();
			foreach($joiningModel->getColumnNames() as $column) {
				$selects[] = $joiningModel->getTable() . ".{$column} as " . $joiningModel->getTable() . ".{$column}";
			}
			$this->select($selects, $builder);
			*/

		} else {

			// [ 'label_maps', 'products.id = label_maps.map_to' ]
			if (count($join) == 2) {
				$compare = explode(' ', $join[1]);
				if (count($compare) == 3) {
					$builder->join($join[0], $compare[0], $compare[1], $compare[2] );
				}
			}
		}

		return $builder;
	}

	function select($columns, $builder = null){
		if (is_null($builder)) $builder = $this->newQuery();
		if (empty($columns)) return $builder;

		if (is_string($columns)) {
			$columns = [ \DB::raw($columns) ];
		}
		return $builder->addSelect($columns);
	}

	function distinct($builder = null){
		if (is_null($builder)) $builder = $this->newQuery();
		if (empty($columns)) return $builder;
		return $builder->distinct();
	}

	/*

	// where, orWhere, whereBetween, whereNotBetween
	// whereIn, whereNotIn, whereNull, whereNotNull,
	// whereDate / whereMonth / whereDay / whereYear
	// whereColumn
	// orWhere with grouping closure
	// whereExists closure
	// where json column


	[ 'id' => 32 ]
	[ 'id', 'is', 32 ]
	[ 'id', 'compare', '=', 'other_id' ]
	id is 32

	*/

	// [ ['id', '=', 32] ]
	function where($conditions, $builder = null){
		if (is_null($builder)) $builder = $this->newQuery();
		if (empty($conditions)) return $builder;

		if (is_string($conditions)) {
			$builder->whereRaw($conditions);
			return $builder;
		}

		if (isset($conditions[0]) && !is_array($conditions[0])) {   // single clause
			$conditions = [ $conditions ];
		} else {
			$key = key($conditions);
			if (is_string($key)) {
				$where = [];
				foreach ($conditions as $k => $v) $where[] = [ $k, $v ];
				$conditions = $where;
			}

		}


		foreach ($conditions as $where) {

			$operator = '=';

			if (count($where) == 2) {
				list($column, $match) = $where;

				$parts = explode(' ', $column);
				$column = $parts[0];
				if (isset($parts[1])) $operator = $parts[1];

				/** @var HiggField $field */
				$field = $this->getField($column);
				if ( $field->key !== 'id' && $field->isString()) {
					$operator = 'LIKE';
					$match = "%$match%";
				}
				else if($field->isDateTime()) {
					$operator = 'date';
				}
			}

			else if (count($where) == 3) list($column, $operator, $match) = $where;
			else if (count($where) == 4) list($column, $operator, $match, $param) = $where;
			else continue;

			switch (strtolower($operator)) {

				case 'in':
                    $builder->whereIn($column, $match);
                    break;
				case 'notIn': $builder->whereNotIn($column, $match); break;
				case 'between': $builder->whereBetween($column, $match); break;
				case 'notBetween': $builder->whereNotBetween($column, $match); break;
				case 'null': $builder->whereNull($column); break;
				case 'notNull': $builder->whereNotNull($column); break;

				case 'date': $builder->whereDate($column, $match); break;
				case 'month': $builder->whereMonth($column, $match); break;
				case 'day': $builder->whereDay($column, $match); break;
				case 'year': $builder->whereYear($column, $match); break;

				case 'column':
					if ( !isset($param) ) $builder = $builder->whereColumn($column, $match);
					else $builder = $builder->whereColumn($column, $param, $match);
					break;

				case 'or': $builder->orWhere($column, $match); break;
				// case 'like': $builder->where($column, 'like', $match); break;
				case 'raw': $builder->where($column, \DB::raw($match)); break;

				default: $builder->where($column, $operator, $match);

			} // end switch

		} // end foreach

		return $builder;
	} // end where clause

	// groupBy, having, havingRaw

	// orderBy, random
	function orderBy($orders, $builder = null){
		if (is_null($builder)) $builder = $this->newQuery();
		if (empty($orders)) return $builder;

		if (is_string($orders)) {
			if (strtolower($orders) == 'random') {
				return $builder->inRandomOrder();
			}
			$orders = [$orders];
		}

		foreach ($orders as $order) {
			$orderBy = explode(" ", $order);
			if (count($orderBy) == 1) $orderBy[1] = 'asc';
			$builder->orderBy($orderBy[0], $orderBy[1]);
		}

		return $builder;
	}

	// take 10 from page 1
	function take($limit = 10, $page = 1, $builder = null){
		if (is_null($builder)) $builder = $this->newQuery();
		$skip = ( $page - 1) * $limit;
		$take = $limit;
		return $builder->skip($skip)->take($take);
	}

	function count($builder = null){
		if (is_null($builder)) $builder = $this->newQuery();
		if (is_array($builder)) {
			$builder = $this->where($builder);
		}
		return $builder->count();
	}

	function sum($column, $builder = null){
		if (is_null($builder)) $builder = $this->newQuery();
		return $builder->sum($column);
	}

	// [ join, where, select ]
	function buildQuery($options = []){

		$model = $this;
		$builder = $model->newQuery();

		$builder = $this->join(array_get($options, 'join', []), $builder);
		$builder = $this->select(array_get($options, 'select', []), $builder);
		$builder = $this->where(array_get($options, 'where', []), $builder);
		$builder = $this->orderBy(array_get($options, 'order', []), $builder);

		$limit = array_get($options, 'limit', 10);
		$page = array_get($options, 'page', 1);
		if (!empty($page)) {
			$builder = $this->take( $limit, $page, $builder );
		}

		return $builder;

	}

	function isNew(){
		return !isset($this[$this->primaryKey]);
	}

	function inCache($key, $set = null){
		if (!is_null($set)) $this->cached[$key] = $set;
		return array_get($this->cached, $key);
	}

	function isCached($key, $set = null){
		if (!$this->isNew()) {
			$key = "$key:" . $this->primaryId();
		}
		$cached = array_get($this->cached, $key);
		if (empty($cached)) {
			if (!is_null($set)) {
				if ($set instanceof \Closure) {
					$cached = $set();
				} else {
					$cached = $set;
				}
				$this->inCache($key, $cached);
			}
		}
		return $cached;
	}

	function unlock(){
		$this->locked = false;
		return $this;
	}

	/**
	 * compare field value
	 * @param string $field model field key
	 * @param string $matchWith compare to
	 * @param bool $strict type compare
	 * @return bool
	 */
	function isMatch($field, $matchWith, $strict = false){
		$value = $this->getValue($field);
		if ($strict) return $value === $matchWith;
		return $value == $matchWith;
	}

	function isUnique($fields, $value = null){
		if (!is_array($fields)) $fields = [ $fields => $value ];
		$builder = \DB::table($this->table);
		foreach ($fields as $field => $value) {
			$builder = $builder->where($field, $value);
		}
		if (!$this->isNew()) {
			$id = $this->primaryId();
			$builder->where($this->primaryKey, '!=', $id);
		}

		$count = $builder->count();
		return $count > 0 ? false : true;
	}

	function createdAtHumanize($value = null){
		if (is_null($value)) $value = $this->getValue('created_at');
		return $this->humanizeDateTime($value);
	}

	function updatedAtRead(){
		return $this->getUpdatedAt()->format("Y-m-d H:i:s");
	}

	function createdAtRead(){
		return $this->getCreatedAt()->format("Y-m-d H:i:s");
	}

	function getUpdatedAt(){
		$created = $this->getValue('updated_at');
		return new Carbon($created);
	}

	function getCreatedAt(){
		$created = $this->getValue('created_at');
		return new Carbon($created);
	}

	function updatedAtHumanize($value = null){
		if (is_null($value)) $value = $this->getValue('updated_at');
		return $this->humanizeDateTime($value);
	}

	function humanizeDateTime($value, $mode = null){
		if (empty($value)) return $value;
		$datetime = new Carbon($value);
		switch($mode) {
			default:
				return $datetime->diffForHumans(Carbon::now());
		}
	}

    function humanizeWithBadge($value, $color = '') {
	    if (!empty($color)) $color = "m-badge--" . $color;
        return '<span class="m-badge '.$color.' m-badge--wide font-weight-bold m-badge--rounded">'.$value.'</span>';
    }

    function humanizeDate($value, $mode = null){
		if (empty($value)) return $value;
		$datetime = new Carbon($value);
		switch($mode) {
			default:
				return $datetime->diffForHumans(Carbon::now());
		}
	}

	function who(){
		if (isset($this->name)) return $this->name;
		if (isset($this->display_name)) return $this->display_name;
		if (isset($this->title)) return $this->title;
		if (isset($this->label)) return $this->label;
		return $this->alias;
	}

	function objectMap(){
		return $this->objectPath();
	}

	function objectPath(){
		if (!$this->isNew()) {
			$alias = $this->alias;
			$id = $this->primaryId();
			return "$alias:$id";
		}
		return null;
	}

	// activity
	function _ob_activity($data = []){
		// return nuActivity()->setActivity($data);
	}

	function wasFailed($context = null, $payload, $by = null){
		if (is_null($context)) $context = $this->alias;
		return nuActivity()->happened(ACTIVITY_ACTION_FAILED, $context, $this, $payload, $by);
	}
	function wasEmailed($context = null, $payload, $by = null){
		if (is_null($context)) $context = $this->alias;
		return nuActivity()->happened(ACTIVITY_ACTION_EMAILED, $context, $this, $payload, $by);
	}
	function wasCreated($context = null, $payload = null, $by = null){
		if (is_null($context)) $context = $this->alias;
		return nuActivity()->happened(ACTIVITY_ACTION_CREATED, $context, $this, $payload, $by);
	}
	function wasUploaded($context = null, $payload = null, $by = null){
		if (is_null($context)) $context = $this->alias;
		return nuActivity()->happened(ACTIVITY_ACTION_UPLOADED, $context, $this, $payload, $by);
	}
	function wasChanged($context = null, $payload = null, $by = null){
		if (is_null($context)) $context = $this->alias;
		return nuActivity()->happened(ACTIVITY_ACTION_CHANGED, $context, $this, $payload, $by);
	}
	function hasDone($what, $context = null, $payload = null, $by = null){
		if (is_null($context)) $context = $this->alias;
		return nuActivity()->happened($what, $context, $this, $payload, $by);
	}
	function wasDeleted($context = null, $payload = null, $by = null){
		if (is_null($context)) $context = $this->alias;
		return nuActivity()->happened('deleted', $context, $this, $payload, $by);
	}
	// activity

	// elastic search
	function toIndexData() {
		$data = $this->toHumanize(null, false, true, false);
		$alias = $this->alias;
		if (!$this->isNew()) {
			$data[$alias.'_id'] = $this->primaryId();
		}
		return $data;
	}

	function getIndexKey(){
		return md5($this->objectMap());
	}

	function getIndexName(){
		return $this->table;
	}

	function getIndex($key = null){
		if (empty($key)) $key = $this->getIndexKey();
		$elastic = new IndexObject($this->getIndexName());
		$id = $elastic->queryById($key);
		return $id;
	}

	function deleteIndex($key = null){
		if (empty($key)) $key = $this->getIndexKey();
		$elastic = new IndexObject($this->getIndexName());
		$deleted = $elastic->elastic->deleteById($key);
		return $deleted == 'deleted';
	}

	function deleteGlobalIndex($key = null){
		if (empty($key)) $key = $this->getIndexKey();
		$elastic = new IndexObject("force_objects");
		$deleted = $elastic->elastic->deleteById($key);
		return $deleted == 'deleted';
	}

	function touchStamp(){
		$time = $this->freshTimestamp();
		$this->setUpdatedAt($time);
		$this->store();
		return $this;
	}
	/*
	function updateIndex(){
		// pr($this->getIndexName());
		$elastic = new IndexObject($this->getIndexName());
		$id = $elastic->index($this);
		if ($id === FALSE) {
			logError("Failed to update search index for " . $this->objectMap());
		}
		return $id;
	}
	*/
	function rebuildIndexConditions(){
		return [ 'page' => false ];
	}

	// @todo MUST employ pagination to optimize for large data set
	function rebuildIndex($options = [], Command $cmd = null){

		$global = array_get($options, 'global', 0);
		$flush = array_get($options, 'flush', 0);

		$conditions = $this->rebuildIndexConditions();
		$all = $this->filter( $conditions );
		// pr($all->count());
		$done = [];
		/** @var HiggModel $model */
		foreach ($all as $model) {

			if (!empty($flush)) {
				$indexKey = $model->getIndexKey();
				$model->deleteIndex($indexKey);
				if ($global) {
					$model->deleteGlobalIndex($indexKey);
				}
				if ($cmd) {
					$msg = 'DELETED: ' . $model->displayName();
					$cmd->error($msg);
				}
			} else {

				$done[] = $model->updateIndex();
				if ($global) {
					$model->updateGlobalIndex();
				}
				if ($cmd) {
					$msg = $model->primaryId() . ': ' . $model->displayName();
					$cmd->error($msg);
				}

			}

		}
		return $done;
	}

	//
	function queryIndex($query, $options = []){

		// $page = array_get($options, 'page', 1);
		$sort = array_get($options, 'sort', null);
		// $return = array_get($options, 'return', null);
		$queryType = array_get($options, 'query', 'query');

		$extra = [];
		if (!empty($sort)) $extra['sort'] = $sort;

		$elastic = new ElasticSearchHelper();
		$data = $elastic->use($this->getIndexName())->query($query, $queryType, $extra);
		return $data;

	} // end search

	//
	function filterIndex($filters, $options = []){

		$query = [ 'bool' => [ 'must' => [] ] ];

		if (!empty($filters)) {
			$must = array_get($query, 'bool.must');
			foreach ($filters as $key => $match) {
				if (is_array($match)) {
					$term = [ 'terms' => [ $key => $match ] ];
				} else {
					if (is_string($match)) $match = strtolower($match);
					$term = [ 'term' => [ $key => $match ] ];
				}
				$must[] = $term;
			}
			array_set($query, 'bool.must', $must);
		}

		// pr($query);

		// $page = array_get($options, 'page', 1);
		$sort = array_get($options, 'sort', null);
		$from = array_get($options, 'from', 0);
		$size = array_get($options, 'size', 100);
		$queryType = array_get($options, 'query', 'query');

		$extra = [];
		if (!empty($sort)) $extra['sort'] = $sort;
		if (!empty($from)) $extra['from'] = $from;
		if (!empty($size)) $extra['size'] = $size;

		$elastic = new ElasticSearchHelper();
		$data = $elastic->use($this->getIndexName())->query($query, $queryType, $extra);
		return $data;

	} // end search

	function search($filters = [], $options = []){
		return $this->filterIndex($filters, $options);
	}

	function searchPaginated($browserOptions = []) {
		if (empty($browserOptions)) $browserOptions = post();

		$where = array_get($browserOptions, 'where', []);
		$page = array_get($browserOptions, 'page', 1);
		$limit = array_get($browserOptions, 'limit', 100);
		$order = array_get($browserOptions, 'order', 'created_at DESC');
		// pr($order);

		if (empty($page)) $page = 1;
		$from = ($page - 1) * $limit;

		$filters = $where;

		if ($this->hasHandle('beforeSearch')) {
			$override = $this->handle('beforeSearch', $browserOptions);
			if (!empty($override)) {
				if ($override === false) return;
				$filters = array_merge($filters, $override);
			}
		}

		// lock within active business account
		$filters['business_id'] = businessId();

		$sort = null;
		$order = explode(" ", $order);
		if (count($order) == 2) {
			$sort = [ $order[0] => [ 'order' => $order[1] ] ];
		}
		$options = [
			'sort' => $sort,
			'from' => $from,
			'size' => $limit
		];
		$results = $this->filterIndex($filters, $options);

		$chunkCount = 10;
		$count = array_get($results, 'totalCount', 0);
		$pageCount = ceil($count/$limit);
		// $nextPage = ($page+1) <= $pageCount ? $page+1 : $pageCount;
		// $prevPage = ($page-1) >= 1 ? $page-1 : 1;

		$chunks = [];
		$lowerChunk = ($lowerChunk = (($page + 1) - $chunkCount)) >= 1 ? $lowerChunk : 1;
		$leftoverChunk = ($page < $chunkCount) ? $chunkCount - $page : 0;
		$upperChunk = ($upperChunk = ($page + $chunkCount) + $leftoverChunk) <= $pageCount ? $upperChunk : $pageCount;
		for($lowerChunk; $lowerChunk <= $upperChunk; $lowerChunk++ ) $chunks[] = $lowerChunk;
		$results['pageChunks'] = $chunks;
		$results['page'] = $page;
		$results['pageCount'] = $pageCount;

		return $results;

	}

	function searchOne($filters = [], $options = []){
		$results = $this->search($filters, $options);
		if (isset($results['count']) && $results['count'] > 0) {
			$data = $results['data'][0];
			return $data;
		}
		return null;
	}


	static function tableQuery() : Builder {
		return \DB::table((new static())->table);
	}

	static function tableExists() {
		/** @var HiggModel $model */
		$model = new static();
		return Schema::hasTable($model->table);
	}

	static function tableDropIfExists() {
		/** @var HiggModel $model */
		$model = new static();
		return Schema::dropIfExists($model->table);
	}

	static function tableCreate() {

		/** @var HiggModel $model */
		$model = new static();
		$fullTextIndexKeys = [];

		Schema::create($model->table, function(Blueprint $table) use ($model){

			$fields = $model->getFields();
			$newColumns = [];
			foreach ($fields as $key => $field) {
				if (!Schema::hasColumn( $model->table, $key)) {
					$newColumns[] = $key;
				}
			}

			/** @var HiggField $field */
			foreach ($newColumns as $key) {

				if (strpos($key, '.') !== FALSE ) continue;

				$field = $model->getField($key);
				// pr($field->key);

				if ($field->isVirtual()) continue;

				if ($field->key === $model->primaryKey) {
					$table->bigIncrements($field->key);
					continue;
				}

				if ($field->isLongString()) $table->text($field->key);
				elseif ($field->isString()) $table->string($field->key);
				elseif ($field->isChar()) {
					$length = empty($field->length) ? 10 : $field->length;
					$table->char($field->key, $length);
				}
				elseif ($field->isNumeric()) $table->integer($field->key);
				elseif ($field->isDouble()) $table->double($field->key, 10, 3);
				elseif ($field->isBoolean()) $table->tinyInteger($field->key);
				elseif ($field->isArray()) $table->longText($field->key);
				elseif ($field->isDate()) $table->date($field->key);
				elseif ($field->isDateTime()) {
					if (!in_array($field->key, [ $model::CREATED_AT, $model::UPDATED_AT ])) {
						$table->dateTime($field->key);
					}
				}

			} // end fields

			foreach ($newColumns as $key) {
				$field = $model->getField($key);
				if (!empty($field->index)) {
					switch($field->index) {
						case 'index': $table->index($key); break;
						case 'unique': $table->unique($key); break;
						case 'fulltext': $fullTextIndexKeys[] = $key; break;
					}
				}
			}

			// if ($model->timestamps) $table->timestamps();

		}); // end schema create

		// DB::statement('ALTER TABLE users ADD FULLTEXT fulltext_index (first_name, last_name, email)');
		if (!empty($fullTextIndexKeys)) {
			// $columns = implode(',', $fullTextIndexKeys);
			// \DB::statement("ALTER TABLE {$model->table} ADD FULLTEXT fulltext_index ({$columns})");
		}

	}


	static function buildTableSchema(Blueprint  &$table, $ignoreColumns = [ 'id', 'created_at', 'updated_at' ] ) {

		/** @var HiggModel $model */
		$model = new static();

		$fields = $model->getFields();
		$newColumns = [];
		foreach ($fields as $key => $field) {
			if (!Schema::hasColumn( $model->table, $key) && !in_array($key, $ignoreColumns) ) {
				$newColumns[] = $key;
			}
		}

		/** @var HiggField $field */
		foreach ($newColumns as $key) {

			if (strpos($key, '.') !== FALSE ) continue;

			$field = $model->getField($key);
			// pr($field->key);

			if ($field->isVirtual()) continue;

			if ($field->key === $model->primaryKey) {
				$table->bigIncrements($field->key);
				continue;
			}

			if ($field->isLongString()) $table->text($field->key)->nullable();
			elseif ($field->isString()) {
				$length = empty($field->length) ? 255 : $field->length;
				$table->string($field->key, $length)->nullable();
			}
			elseif ($field->isChar()) {
				$length = empty($field->length) ? 10 : $field->length;
				$table->char($field->key, $length)->nullable();
			}
			elseif ($field->isNumeric()) $table->integer($field->key)->nullable();
			elseif ($field->isDouble()) $table->double($field->key, 10, 3)->nullable();
			elseif ($field->isBoolean()) $table->tinyInteger($field->key)->nullable();
			elseif ($field->isArray()) $table->longText($field->key)->nullable();
			elseif ($field->isDateTime()) {
				if (!in_array($field->key, [ $model::CREATED_AT, $model::UPDATED_AT ])) {
					$table->dateTime($field->key)->nullable();
				}
			}
			elseif ($field->isDate()) $table->date($field->key)->nullable();

		} // end fields

		foreach ($newColumns as $key) {
			$field = $model->getField($key);
			if (!empty($field->index)) {
				switch($field->index) {
					case 'index': $table->index($key); break;
					case 'unique': $table->unique($key); break;
				}
			}
		}

		return $table;

	}

} // end MySQLModel
