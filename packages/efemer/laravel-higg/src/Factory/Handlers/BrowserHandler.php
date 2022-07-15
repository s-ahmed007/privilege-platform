<?php

namespace Efemer\Higg\Factory\Handlers;

use Efemer\Higg\Factory\Core\HiggModel;
use Illuminate\Support\Str;

class BrowserHandler {

	public $model;
	public $config;
	public $options = [];

	function __construct($modelAlias = null){
		$this->model = new HiggModel();
		if (!empty($modelAlias)) {
			$this->model = app()->make($modelAlias);
		}
		$this->config = $this->model->getBrowserConfig();
	}

	function query($options = []){
		$this->options = $options;

		// $transform = array_get($options, 'transform', '');

		if (!$this->model->allowed(ACCESS_PRIVILEGE_BROWSE)) {
			return ['error' => 'Protected territory, keep away!'];
		}

		return $this->browse();

		/*
		switch($transform) {
			default:
				return $this->browse();

		}
		*/
	}

	function whereCondition($conditions){
		if (empty($this->options)) $this->options = [];
		$where = array_get($this->options, 'where');
		foreach ($conditions as $field => $value) {
			if (is_array($value)) $where[] = $value;
			else $where[$field] = $value;
		}
		$this->options['where'] = $where;
		return $this;
	}

	function getWhereConditions(){
		if (empty($this->options)) $this->options = [];
		return array_get($this->options, 'where', []);
	}

	function isKeyValuedWhere(){
		$where = $this->getWhereConditions();
		if (!empty($where)) {
			foreach ($where as $key => $value) {
				if (is_string($key)) return false;
			}
		}
		return true;
	}

	function buildQuery($where){
		$default = array_get($this->config, 'query', []);
		return array_merge($where, $default);
	}

	function browse($options = []){

		if (!empty($options)) $this->options = array_merge($this->options, $options);

		$continue = $this->model->handle('beforeBrowse', $this);
		if ($continue === false) {
			$error = $this->model->error() ? $this->model->error() : higg()->error();
			return [
				'error' => empty($error) ? true : $error
			];
		}

		$options = $this->options;

		// $transform = array_get($options, 'transform', '');
		$where = $this->buildQuery(array_get($options, 'where', []));
		$select = array_get($options, 'select', []);
		$order = array_get($options, 'order', []);
		// $humanize = array_get($options, 'humanize', true);
		// $table = array_get($options, 'table', false);

		$page = array_get($options, 'page', false);
		$limit = array_get($options, 'limit', 10);
		$chunkCount = array_get($options, 'chunkCount', 5);

		$response = [];

		if ($page) {
			$count = $this->model->count($where);
			$pageCount = ceil($count/$limit);
			$nextPage = ($page+1) <= $pageCount ? $page+1 : $pageCount;
			$prevPage = ($page-1) >= 1 ? $page-1 : 1;

			$chunks = [];
			$lowerChunk = ($lowerChunk = (($page + 1) - $chunkCount)) >= 1 ? $lowerChunk : 1;
			$leftoverChunk = ($page < $chunkCount) ? $chunkCount - $page : 0;
			$upperChunk = ($upperChunk = ($page + $chunkCount) + $leftoverChunk) <= $pageCount ? $upperChunk : $pageCount;
			for($lowerChunk; $lowerChunk <= $upperChunk; $lowerChunk++ ) $chunks[] = $lowerChunk;

			$response = [
				'page' => (int)$page,
				'limit' => $limit,
				'totalCount' => $count,
				'pageCount' => $pageCount,
				'nextPage' => $nextPage,
				'prevPage' => $prevPage,
				'pageChunks' => $chunks,
			];
		}

		$response['data'] = [];
		$response['count'] = 0;

		$filterOptions = compact('where', 'select', 'order', 'page', 'limit');
		$collection = $this->model->filter($filterOptions);
		$browserData = $this->model->toTable($collection, $options);
		$response = array_merge($response, $browserData);
		return $response;
	}





} //
