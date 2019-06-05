<?php

namespace app\index\model;
use Redis;

class RedisModel extends Redis{

    public function __construct() {
        
    }
    public function getConnect(){
        $redis = new Redis();
        $redis->connect('127.0.0.1', 6379);
        return $redis;
    }


}
