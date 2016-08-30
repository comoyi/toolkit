<?php

namespace My\Redis;

/**
 * redis slave
 */
class RedisSlave {

    /* 配置项 */
    private $configs = [];

    private $handler = false;

    /**
     * 构造
     */
    public function __construct($config, $sentinel = null, $masterName = null){
        $this->setConfigs($config);
        if(!is_null($sentinel) && !is_null($masterName)){
            $this->setConfigs($this->getConnectConfigs($sentinel, $masterName));
        }
        $this->connect();
    }

    /**
     * 获取配置
     */
    public function getConnectConfigs($sentinel, $masterName){
        $slaves = $sentinel->get_slaves($masterName);
        $random = rand(0, (count($slaves) - 1)); //随机取一个slave的配置
        $config = [
            'redis_host' => $slaves[$random]['ip'],
            'redis_port' => $slaves[$random]['port']
        ];
        return $config;
    }

    /**
     * 设置配置
     */
    public function setConfigs($configs){
        $this->configs = array_merge($this->configs, $configs);
    }

    /**
     * 连接
     */
    public function connect(){
        $this->handler = new \Redis();
        $this->handler->connect($this->configs['redis_host'], $this->configs['redis_port']);
        $this->auth();
    }

    /**
     * 验证
     */
    public function auth(){
        $this->handler->auth($this->configs['redis_password']);
    }

    /**
     * 获取连接
     */
    public function getHandler(){
        return $this->handler;
    }

}
