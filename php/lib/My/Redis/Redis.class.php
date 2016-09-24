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
            'password' => $this->configs['password']
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
            'password' => $this->configs['password']
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
    public function getSlaveConfigsBySentinel(){
        $slaves = $this->sentinel->get_slaves($this->masterName);
        if(0 === count($slaves)){ // 没有slave则取master
            return $this->getMasterConfigsBySentinel();
        }
        $random = rand(0, (count($slaves) - 1)); // 随机取一个slave的配置
        $config = [
            'host' => $slaves[$random]['ip'],
            'port' => $slaves[$random]['port'],
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
            'get',
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
    public function evaluate ($script, $args, $quantity) {
        $result= $this->getHandler($this->judge(__FUNCTION__))->eval($script, $args, $quantity);
        return $result;
    }

    /**
     * 同redis手册
     */
    public function get($key){
        $result= $this->getHandler($this->judge(__FUNCTION__))->get($key);
        return $result;
    }

    /**
     * 同redis手册
     */
    public function set($key, $value){
        $result= $this->getHandler($this->judge(__FUNCTION__))->set($key, $value);
        return $result;
    }

    /**
     * 同redis手册
     */
    public function setEx($key, $seconds, $value){
        $result= $this->getHandler($this->judge(__FUNCTION__))->setEx($key, $seconds, $value);
        return $result;
    }

    /**
     * 同redis手册
     */
    public function setNx($key, $value){
        $result= $this->getHandler($this->judge(__FUNCTION__))->setNx($key, $value);
        return $result;
    }

    /**
     * 同redis手册
     */
    public function hGet($key, $field){
        $result= $this->getHandler($this->judge(__FUNCTION__))->hGet($key, $field);
        return $result;
    }

    /**
     * 同redis手册
     */
    public function hMGet($key, $array){
        $result= $this->getHandler($this->judge(__FUNCTION__))->hMGet($key, $array);
        return $result;
    }

    /**
     * 同redis手册
     */
    public function hGetAll($key){
        $result= $this->getHandler($this->judge(__FUNCTION__))->hGetAll($key);
        return $result;
    }

    /**
     * 同redis手册
     */
    public function hSet($key, $field, $value){
        $result= $this->getHandler($this->judge(__FUNCTION__))->hSet($key, $field, $value);
        return $result;
    }

    /**
     * 同redis手册
     */
    public function hMSet($key, $array){
        $result= $this->getHandler($this->judge(__FUNCTION__))->hMSet($key, $array);
        return $result;
    }

    /**
     * 同redis手册
     */
    public function sAdd($key, $value){
        $result= $this->getHandler($this->judge(__FUNCTION__))->sAdd($key, $value);
        return $result;
    }

    /**
     * 同redis手册
     */
    public function zAdd($key, $score, $value){
        $result= $this->getHandler($this->judge(__FUNCTION__))->zAdd($key, $score, $value);
        return $result;
    }

    /**
     * 同redis手册
     */
    public function sMembers($key){
        $result= $this->getHandler($this->judge(__FUNCTION__))->sMembers($key);
        return $result;
    }

    /**
     * 同redis手册
     */
    public function sRem($key, $member){
        $result= $this->getHandler($this->judge(__FUNCTION__))->sRem($key, $member);
        return $result;
    }

    /**
     * 同redis手册
     */
    public function zRange($key, $start, $stop){
        $result= $this->getHandler($this->judge(__FUNCTION__))->zRange($key, $start, $stop);
        return $result;
    }

    /**
     * 同redis手册
     */
    public function zRem($key, $member){
        $result= $this->getHandler($this->judge(__FUNCTION__))->zRem($key, $member);
        return $result;
    }

    /**
     * 同redis手册
     */
    public function zRemRangeByScore($key, $min, $max){
        $result= $this->getHandler($this->judge(__FUNCTION__))->zRemRangeByScore($key, $min, $max);
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



