<?php

namespace Efemer\Higg\Helpers;

use Elasticsearch\ClientBuilder;

class ElasticSearchHelper {

	public $useCollection = null;
	public $hosts = [];

	function __construct($hosts = null) {
		if (empty($hosts)) {
			$hosts = env('ELASTICSEARCH_HOST');
			if (empty($hosts)) $hosts = [ '127.0.0.1' ];
			else $hosts = explode(',', $hosts);
		}
		$this->hosts = $hosts;
	}

	function getHosts(){
		return $this->hosts;
	}

	function isActive(){
		$index = $this->defaultIndex();
		return empty($index) ? false : true;
	}

	function client(){
		$hosts = $this->getHosts();
		$client = ClientBuilder::create();
		$client->setHosts($hosts)->setRetries(0);
		return $client->build();
	}

	function createIndex($index, $body){
		$params = ['index' => $index, 'body' => $body];
		$this->client()->indices()->create($params);
	}

	function deleteType($index, $type) {
		// $params = [ 'index' => $index, 'type' => $type ];
		// return $this->client()->indices()->delete($params);
	}

	function deleteIndex($index){
		$params = ['index' => $index ];
		$this->client()->indices()->delete($params);
	}

	function deleteById($id){
		$payload = $this->useCollection;
		$payload['id'] = $id;
		try {
			$client = $this->client();
			$response = $client->delete($payload);
			// pr($response);
			return array_get($response, 'result');
		} catch (\Exception $ex) {
			if (is_local()) {
				print_r($ex->getMessage());
			}
			higg()->error($ex->getMessage());
			logError($ex->getMessage());
		}
		return false;
	}

	function defaultIndex(){
		return config('higg.elasticsearch.index');
	}

	function use($collection, $index = null){
        $this->useCollection = [ 'index' => $collection ];
	    return $this;
        /*
		if (is_null($index)) $index = config('higg.elasticsearch.index');
		$this->useCollection = [ 'index' => $index, 'type' => $collection ];
		return $this;
        */
	}

	// id, type, index?
	function read($id, $type = null){
		if (!is_null($type)) $this->use($type);
		if (is_array($id)) {
			$this->use($id['type'], array_get($id, 'index'));
			$id = $id['id'];
		}
		$payload = $this->useCollection;
		$payload['id'] = $id;
		try {
			$client = $this->client();
			$response = $client->get($payload);
			$found = array_get($response, 'found');
			if ($found) {
				return array_get($response, '_source');
			}
		} catch (\Exception $ex) {
			higg()->error($ex->getMessage());
			logError($ex->getMessage());
		}
	}

	function getById($id) {
		$payload = $this->useCollection;
		$payload['id'] = $id;
		try {
			$client = $this->client();
			$response = $client->get($payload);
			// pr($response);
			return array_get($response, '_source');
		} catch (\Exception $ex) {
			if (is_local()) pr($ex->getMessage());
			higg()->error($ex->getMessage());
			logError($ex->getMessage());
		}
		return false;
	}

	function index($object, $id = null){
		if (is_array($id)) {
			$this->use($id['type'], array_get($id, 'index'));
			$id = $id['id'];
		}
		$payload = $this->useCollection;
		if (!empty($id)) $payload['id'] = $id;
		$payload['body'] = $object;

		// pr($payload);

		try {
			$client = $this->client();
			$response = $client->index($payload);
			// pr($response);
			$result = array_get($response, 'result');
			if (in_array($result, ['created', 'updated'])) return $response['_id'];
		} catch (\Exception $ex) {
			if (is_local()) pr($ex->getMessage());
			higg()->error($ex->getMessage());
			logError($ex->getMessage());
		}
		return false;
	}

	// prefix, prefix
	function query($query, $queryType = 'query', $extraPayload = []){
		$payload = $this->useCollection;
		$body = array_merge([$queryType => $query], $extraPayload);
		$payload['body'] = $body;
		try {

			// pr($payload);
            // $payload['type'] = '_doc';
            // unset($payload['type']);
            // pr(json_encode($payload));
            // pr($payload);

			$client = $this->client();
			$response = $client->search($payload);
			// pr($response);

			$hits = array_get($response, 'hits.hits');
			$result = [
				'took' => array_get($response, 'took'),
				// 'total' => array_get($response, 'hits.total'),
				'totalCount' => array_get($response, 'hits.total.value'),
				'count' => count($hits),
			];
			// pr($result);

			if (!empty($hits)) {
				$result['data'] = collect($hits)->map(function($item){
					return $item['_source'];
				})->toArray();
			}
			return $result;

		} catch (\Exception $ex) {
			if (is_local()) pr($ex->getMessage());
			higg()->error($ex->getMessage());
			logError($ex->getMessage());
		}
		return null;
	}
	function prefixQuery($prefix){
		return $this->query($prefix, 'prefix');
	}

}
