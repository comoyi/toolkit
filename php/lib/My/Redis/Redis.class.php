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
            'hGet',
            'hMGet',
            'hGetAll',
            'sMembers',
            'zRange',
            'exists'
        ]; //只读的操作
       if (in_array($command, $readOnlyCommands)) {
            $masterOrSlave = 'slave';
       }
       return $masterOrSlave;
    }

    /**
     * 获取连接
     *
     * @param string $masterOrSlave [master / slave]
     * @return
     */
    private function getHandler($masterOrSlave) {
       $handler = $this->pool[$masterOrSlave]->getHandler();
       return $handler;
    }

    /**
     * 切换database
     *
     * @param int $index db索引
     */
    public function select($index = 0) {
        $this->pool['master']->getHandler()->select($index);
        $this->pool['slave']->getHandler()->select($index);
    }

    /**
     * 执行lua脚本
     *
     * eval是PHP关键字PHP7以下不能作为方法名
     *
     * @param string $script 脚本代码
     * @param array $args 传给脚本的KEYS, ARGV组成的索引数组（不是key-value对应，是先KEYS再ARGV的索引数组，KEYS, ARGV数量可以不同） 例：['key1', 'key2', 'argv1', 'argv2', 'argv3']
     * @param int $quantity 传给脚本的KEY数量
     * @return
     */
    public function evaluate ($script, $args, $quantity) {
        $result = $this->getHandler($this->judge(__FUNCTION__))->eval($script, $args, $quantity);
        return $result;
    }

    /**
     * 获取key对应的值
     *
     * @param string $key key
     * @return
     */
    public function get($key){
        $result = $this->getHandler($this->judge(__FUNCTION__))->get($key);
        return $result;
    }

    /**
     * 设置key - value
     *
     * @param string $key key
     * @param string $value value
     * @return
     */
    public function set($key, $value){
        $result = $this->getHandler($this->judge(__FUNCTION__))->set($key, $value);
        return $result;
    }

    /**
     * 设置key - value同时设置剩余有效期
     *
     * @param string $key key
     * @param int $seconds 剩余有效期 （单位：s / 秒）
     * @param string $value
     * @return
     */
    public function setEx($key, $seconds, $value){
        $result = $this->getHandler($this->judge(__FUNCTION__))->setEx($key, $seconds, $value);
        return $result;
    }

    /**
     * 设置key - value （仅在当前key不存在时有效）
     *
     * @param string $key key
     * @param string $value value
     * @return
     */
    public function setNx($key, $value){
        $result = $this->getHandler($this->judge(__FUNCTION__))->setNx($key, $value);
        return $result;
    }

    /**
     * 获取hash一个指定字段的值
     *
     * @param string $key key
     * @param string $field 字段
     * @return
     */
    public function hGet($key, $field){
        $result = $this->getHandler($this->judge(__FUNCTION__))->hGet($key, $field);
        return $result;
    }

    /**
     * 获取hash多个指定字段的值
     *
     * @param string $key key
     * @param array $array 字段索引数组
     * @return
     */
    public function hMGet($key, $array){
        $result = $this->getHandler($this->judge(__FUNCTION__))->hMGet($key, $array);
        return $result;
    }

    /**
     * 获取整个hash的值
     *
     * @param string $key key
     * @return
     */
    public function hGetAll($key){
        $result = $this->getHandler($this->judge(__FUNCTION__))->hGetAll($key);
        return $result;
    }

    /**
     * 设置hash一个字段
     *
     * @param string $key key
     * @param string $field 字段
     * @param string $value 值
     * @return
     */
    public function hSet($key, $field, $value){
        $result = $this->getHandler($this->judge(__FUNCTION__))->hSet($key, $field, $value);
        return $result;
    }

    /**
     * 设置hash多个字段
     *
     * @param string $key key
     * @param array $array 要设置的hash字段 例：['field1' => 'value1', 'field2' => 'value2']
     * @return
     */
    public function hMSet($key, $array){
        $result = $this->getHandler($this->judge(__FUNCTION__))->hMSet($key, $array);
        return $result;
    }

    /**
     * 往set集合添加成员
     *
     * @param string $key key
     * @param string $member 成员
     * @return
     */
    public function sAdd($key, $member){
        $result = $this->getHandler($this->judge(__FUNCTION__))->sAdd($key, $member);
        return $result;
    }

    /**
     * 获取集合所有成员
     *
     * @param string $key key
     * @return
     */
    public function sMembers($key){
        $result = $this->getHandler($this->judge(__FUNCTION__))->sMembers($key);
        return $result;
    }

    /**
     * 删除集合里的成员
     *
     * @param string $key key
     * @param string $member 成员
     * @return
     */
    public function sRem($key, $member){
        $result = $this->getHandler($this->judge(__FUNCTION__))->sRem($key, $member);
        return $result;
    }

    /**
     * 往有序集合添加成员
     *
     * @param string $key key
     * @param int $score score
     * @param string $value value
     * @return
     */
    public function zAdd($key, $score, $value){
        $result = $this->getHandler($this->judge(__FUNCTION__))->zAdd($key, $score, $value);
        return $result;
    }

    /**
     * 获取有序集合成员
     *
     * @param string $key key
     * @param int $start 起始值
     * @param int $stop 截止值
     * @param bool $isWithScore 是否包含score值
     * @return
     */
    public function zRange($key, $start, $stop, $isWithScore = false){
        $result = $this->getHandler($this->judge(__FUNCTION__))->zRange($key, $start, $stop, $isWithScore);
        return $result;
    }

    /**
     * 根据value移除有序集合成员
     * @param string $key key
     * @param string $value value值
     * @return
     */
    public function zRem($key, $value){
        $result = $this->getHandler($this->judge(__FUNCTION__))->zRem($key, $value);
        return $result;
    }

    /**
     * 根据排名范围移除有序集合成员
     *
     * @param string $key key
     * @param int $start 起始排名
     * @param int $stop 截止排名
     * @return
     */
    public function zRemRangeByRank($key, $start, $stop){
        $result = $this->getHandler($this->judge(__FUNCTION__))->zRemRangeByRank($key, $start, $stop);
        return $result;
    }

    /**
     * 根据score范围移除有序集合成员
     *
     * @param string $key key
     * @param int $min 起始score
     * @param int $max 截止score
     * @return
     */
    public function zRemRangeByScore($key, $min, $max){
        $result = $this->getHandler($this->judge(__FUNCTION__))->zRemRangeByScore($key, $min, $max);
        return $result;
    }

    /**
     * 设置剩余有效时长
     *
     * @param string $key key
     * @param int $exp 剩余时长 （单位：秒）
     * @return
     */
    public function expire($key, $exp){
        $result = $this->getHandler($this->judge(__FUNCTION__))->expire($key, $exp);
        return $result;
    }

    /**
     * 删除key
     *
     * @param string $key key
     * @return
     */
    public function del($key){
        $result = $this->getHandler($this->judge(__FUNCTION__))->del($key);
        return $result;
    }

    /**
     * 判断key是否存在
     *
     * @param $key
     * @return
     */
    public function exists($key){
        $result = $this->getHandler($this->judge(__FUNCTION__))->exists($key);
        return $result;
    }

    /**
     * 发布消息到指定频道
     * @param string $channel 频道
     * @param string $message 消息内容
     * @return
     */
    public function publish($channel, $message){
        $result = $this->getHandler($this->judge(__FUNCTION__))->publish($channel, $message);
        return $result;
    }

    /**
     * 自增 - 增幅为1
     *
     * @param string $key key
     * @return
     */
    public function incr($key) {
        $result = $this->getHandler($this->judge(__FUNCTION__))->incr($key);
        return $result;
    }

    /**
     * 自增 - 增幅为指定值
     *
     * @param string $key key
     * @param int $amount 增加的数值
     * @return
     */
    public function incrBy($key, $amount) {
        $result = $this->getHandler($this->judge(__FUNCTION__))->incrBy($key, $amount);
        return $result;
    }

    /**
     * 添加到队列头
     *
     * @param string $key key
     * @param string $value value
     * @return
     */
    public function lPush($key, $value) {
        $result = $this->getHandler($this->judge(__FUNCTION__))->lPush($key, $value);
        return $result;
    }

    /**
     * 添加到队列尾
     *
     * @param string $key key
     * @param string $value value
     * @return
     */
    public function rPush($key, $value) {
        $result = $this->getHandler($this->judge(__FUNCTION__))->rPush($key, $value);
        return $result;
    }

    /**
     * 增加有序集合score值
     *
     * @param string $key key
     * @param int $amount 增长的数值
     * @param string $value value值
     * @return
     */
    public function zIncrBy($key, $amount, $value) {
        $result = $this->getHandler($this->judge(__FUNCTION__))->zIncrBy($key, $amount, $value);
        return $result;
    }

}



