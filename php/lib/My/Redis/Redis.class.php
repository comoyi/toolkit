<?php

namespace My\Redis;

use My\Redis\RedisSentinel;
use My\Redis\RedisMaster;
use My\Redis\RedisSlave;

/**
 * redis操作类
 */
class Redis {

    /* 配置项 */
    private $configs = [];

    /* 用于执行redis操作的池 */
    private $pool;

    /* 哨兵对象 */
    private $sentinel;

    /* 要操作的master名称 */
    private $masterName;

    /**
     * 构造函数
     */
    public function __construct($config = []){
        $redisSentinel = [
            'redis_password' => 'redispassword',
            'redis_master_name' => 'mastername',
            'redis_sentinel' => [
                'sentinels' => [
                    [
                        'host' => '127.0.0.1',
                        'port' => '5000'
                    ],
                    [
                        'host' => '127.0.0.1',
                        'port' => '5001'
                    ]
                ]
            ]
        ]
        $defaultConfig = ['redis_password' => $redisSentinel['redis_password'], 'redis_master_name' => $redisSentinel['redis_master_name'], 'redis_sentinel' => $redisSentinel['redis_sentinel']];
        $this->setConfig($defaultConfig);
        $this->setConfig($config);
        $this->sentinel = new RedisSentinel(); //创建sentinel

        /* 根据配置添加sentinel */
        foreach ($this->configs['redis_sentinel']['sentinels'] as $s) {
            $this->sentinel->addnode($s['host'], $s['port']);
        }

        $this->masterName = $this->configs['redis_master_name'];
        $this->pool['master'] = new RedisMaster(['redis_password' => $this->configs['redis_password']], $this->sentinel, $this->masterName);
        $this->pool['slave'] = new RedisSlave(['redis_password' => $this->configs['redis_password']], $this->sentinel, $this->masterName);
    }

    /**
     * 设置配置
     */
    private function setConfig($config) {
        $this->configs = array_merge($this->configs, $config);
        //file_put_contents('/tmp/debug_'.date('Ymd').'.log', '[line: ' . __LINE__ . ']' . '[$this->configs]' . var_export($this->configs, true) . PHP_EOL, FILE_APPEND);
    }

    /**
     * 判断只读还是读写
     */
    private function judge($command) {
        $masterOrSlave = 'master';
        $readOnlyCommands = [
            'hget',
            'hmget',
            'hgetall',
            'smembers',
            'zrange',
            'exists'
        ]; //只读的操作
       if (in_array($command, $readOnlyCommands)) {
            $masterOrSlave = 'slave';
       }
       return $masterOrSlave;
    }

    /**
     * 获取连接
     */
    private function getHandler($masterOrSlave) {
       $handler = $this->pool[$masterOrSlave]->getHandler();
       return $handler;
    }

    /**
     * 同redis手册
     */
    public function redis_eval ($script, $args, $quantity) {
        $result= $this->getHandler($this->judge(__FUNCTION__))->eval($script, $args, $quantity);
        return $result;
    }

    /**
     * 同redis手册
     */
    public function hget($key, $field){
        $result= $this->getHandler($this->judge(__FUNCTION__))->hget($key, $field);
        return $result;
    }
 
    /**
     * 同redis手册
     */
    public function hmget($key, $array){
        $result= $this->getHandler($this->judge(__FUNCTION__))->hmget($key, $array);
        return $result;
    }

    /**
     * 同redis手册
     */
    public function hgetall($key){
        $result= $this->getHandler($this->judge(__FUNCTION__))->hgetall($key);
        return $result;
    }

    /**
     * 同redis手册
     */
    public function hset($key, $field, $value){
        $result= $this->getHandler($this->judge(__FUNCTION__))->hset($key, $field, $value);
        return $result;
    }

    /**
     * 同redis手册
     */
    public function hmset($key, $array){
        $result= $this->getHandler($this->judge(__FUNCTION__))->hmset($key, $array);
        return $result;
    }

    /**
     * 同redis手册
     */
    public function sadd($key, $value){
        $result= $this->getHandler($this->judge(__FUNCTION__))->sadd($key, $value);
        return $result;
    }

    /**
     * 同redis手册
     */
    public function zadd($key, $score, $value){
        $result= $this->getHandler($this->judge(__FUNCTION__))->zadd($key, $score, $value);
        return $result;
    }

    /**
     * 同redis手册
     */
    public function smembers($key){
        $result= $this->getHandler($this->judge(__FUNCTION__))->smembers($key);
        return $result;
    }

    /**
     * 同redis手册
     */
    public function srem($key, $member){
        $result= $this->getHandler($this->judge(__FUNCTION__))->srem($key, $member);
        return $result;
    }

    /**
     * 同redis手册
     */
    public function zrange($key, $start, $stop){
        $result= $this->getHandler($this->judge(__FUNCTION__))->zrange($key, $start, $stop);
        return $result;
    }

    /**
     * 同redis手册
     */
    public function zrem($key, $member){
        $result= $this->getHandler($this->judge(__FUNCTION__))->zrem($key, $member);
        return $result;
    }

    /**
     * 同redis手册
     */
    public function zremrangebyscore($key, $min, $max){
        $result= $this->getHandler($this->judge(__FUNCTION__))->zremrangebyscore($key, $min, $max);
        return $result;
    }

    /**
     * 同redis手册
     */
    public function expire($key, $exp){
        $result= $this->getHandler($this->judge(__FUNCTION__))->expire($key, $exp);
        return $result;
    }

    /**
     * 同redis手册
     */
    public function del($key){
        $result= $this->getHandler($this->judge(__FUNCTION__))->del($key);
        return $result;
    }

    /**
     * 同redis手册
     */
    public function exists($key){
        $result= $this->getHandler($this->judge(__FUNCTION__))->exists($key);
        return $result;
    }

    /**
     * 同redis手册
     */
    public function publish($channel, $message){
        $result= $this->getHandler($this->judge(__FUNCTION__))->publish($channel, $message);
        return $result;
    }

}



