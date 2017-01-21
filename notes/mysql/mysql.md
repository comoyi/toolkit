## mysql

#### 导出结构不导出数据
```
mysqldump --opt -d -u root -p db_name > db_name.sql
```

#### 导入
```
mysql -u root -p db_name < db_name.sql
```


---

## SQL语句

#### 建库
```
CREATE DATABASE mydbname CHARACTER SET utf8 COLLATE utf8_general_ci;
```

#### 建表
```
CREATE TABLE `tel_records` (
    `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
    `record_id` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '通话记录ID',
    `task_id` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '标识',
    `number` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '400号码',
    `caller_number` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '主叫号码',
    `client_number` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '被叫号码' ,
    `type` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'in' COMMENT '呼入/呼出',
    `start_time` int(11) NOT NULL DEFAULT 0 COMMENT '呼叫开始时间',
    `duration` int(11) NOT NULL DEFAULT 0 COMMENT '通话时长',
    `cost`  decimal(10,3) NOT NULL DEFAULT 0 COMMENT '消费金额' ,
    `caller_province` varchar(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '省份',
    `caller_city` varchar(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '城市',
    `status` varchar(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '接听状态',
    `record_file` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '录音文件的绝对地址',
    `remark` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '备注',
    `caller_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '来电人姓名',
    `client_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '接听人员',
    PRIMARY KEY  (`id`),
    KEY `number` (`number`)
);
```

#### 修改表
```
ALTER TABLE `call_simple_record` ADD COLUMN `session_id` varchar(40) NOT NULL DEFAULT '' COMMENT '通话session_id';
ALTER TABLE `call_simple_record` ADD COLUMN `end_time` int(10) NOT NULL DEFAULT 0 COMMENT '通话结束时间';
ALTER TABLE `call_simple_record` ADD INDEX `session_id` USING BTREE (`session_id`);
```
