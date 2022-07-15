<?php

namespace Efemer\Higg\Factory\Core;

use Redis;

class RedisStore {

    function isLive(){
        return config('higg.redis', false);
    }

    function json($key, $data = null){
        if (!$this->isLive()) return null;
        if (is_null($data)) {
            $data = $this->read($key);
            return json_decode($data, true);
        }
        $this->write($key,json_encode($data));
        return $data;
    }

    function read($key){
        return Redis::get($key);
    }

    function write($key, $value){
        Redis::set($key, $value);
    }

    function delete($key){
        Redis::del($key);
    }

    function increment($key){
        if ($this->exists($key)) $this->write($key, 0);
        Redis::incr($key);
    }

    function exists($key){
        return !!Redis::exists($key);
    }

    function expire($key, $ttl = SECOND_ONE_DAY){
        Redis::expire($key, (integer)$ttl);
    }


} // end