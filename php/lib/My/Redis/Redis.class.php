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

        $defaultConfig = [
            'type' => 'direct', // direct: 直连, sentinel: 由sentinel决定host与port
            'password' => 'redispassword', // redis auth 密码
            'master_name' => 'mymaster', // master name
            'direct' => [
                'masters' => [
                    [
                        'host' => '127.0.0.1',
                        'port' => '6379'
                    ]
                ],
                'slaves' => [
                    [
                        'host' => '127.0.0.1',
                        'port' => '6381'
                    ],
                    [
                        'host' => '127.0.0.1',
                        'port' => '6382'
                    ]
                ],
            ],
            'sentinel' => [
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
        ];

        $this->setConfig($defaultConfig);
        $this->setConfig($config);

        $this->masterName = $this->configs['master_name'];

        if('sentinel' === $this->configs['type']){ // sentinel方式
            $this->sentinel = new RedisSentinel(); //创建sentinel

            /* 根据配置添加sentinel */
            foreach ($this->configs['sentinel']['sentinels'] as $s) {
                $this->sentinel->addnode($s['host'], $s['port']);
            }
        }

        $this->pool['master'] = new RedisMaster($this->getMasterConfigs());
        $this->pool['slave'] = new RedisSlave($this->getSlaveConfigs());
    }

    /**
     * 获取master配置
     */
    public function getMasterConfigs(){
        if('sentinel' === $this->configs['type']){
            return $this->getMasterConfigsBySentinel();
        }
        $randomMaster = rand(0, (count($this->configs['direct']['masters']) - 1)); // 随机取一个master的配置
        $config = [
            'host' => $this->configs['direct']['masters'][$randomMaster]['host'],
            'port' => $this->configs['direct']['masters'][$randomMaster]['port'],
            'password' = $this->configs['password']
        ];
        return $config;
    }

    /**
     * 获取slave配置
     */
    public function getSlaveConfigs(){
        if('sentinel' === $this->configs['type']){
            return $this->getSlaveConfigsBySentinel();
        }
        if(0 === count($this->configs['direct']['slaves'])){ // 没有slave则取master
            return $this->getMasterConfigs();
        }
        $randomSlave = rand(0, (count($this->configs['direct']['slaves']) - 1)); // 随机取一个slave的配置
        $config = [
            'host' => $this->configs['direct']['slaves'][$randomSlave]['host'],
            'port' => $this->configs['direct']['slaves'][$randomSlave]['port'],
            'password' = $this->configs['password']
        ];
        return $config;
    }

    /**
     * 通过sentinel获取master配置
     */
    public function getMasterConfigsBySentinel(){
        $masters = $this->sentinel->get_masters($this->masterName);
        $config = [
            'host' => $masters[0],
            'port' => $masters[1],
            'password' => $this->configs['password']
        ];
        return $config;
    }

    /**
     * 通过sentinel获取slave配置
     */
    public function getSlaveConfigsBySentinel($sentinel, $masterName){
        $slaves = $this->sentinel->get_slaves($this->masterName);
        if(0 === count($slaves)){ // 没有slave则取master
            return $this->getMasterConfigsBySentinel();
        }
        $random = rand(0, (count($slaves) - 1)); // 随机取一个slave的配置
        $config = [
            'host' => $slaves[$random]['ip'],
            'port' => $slaves[$random]['port']
            'password' => $this->configs['password']
        ];
        return $config;
    }

    /**
     * 设置配置
     */
    private function setConfig($config) {
        $this->configs = array_merge($this->configs, $config);
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
     * 切换database
     */
    private function select($index = 0) {
        $this->pool['master']->getHandler()->select($index);
        $this->pool['slave']->getHandler()->select($index);
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



