#### php安装xdebug扩展
```
...
```

#### 启用xdebug扩展
```
; 在php配置文件里加上xdebug扩展
zend_extension=xdebug.so
```

#### 配置xdebug
```

; 允许远程调试
xdebug.remote_enable = 1

xdebug.remote_connect_back = 1

; 设置debug主机地址 
; 该配置项优先级比xdebug.remote_connect_back低会被覆盖
; 调试api时如果设置的是xdebug.remote_connect_back = 1 xdebug会把调试信息推送到调用者所在ip导致IDE接收不到调试信息
xdebug.remote_host = 192.168.11.77

; 监听的端口
; 如果端口冲突可以更换端口
xdebug.remote_port = 9000

xdebug.max_nesting_level = 512

; attempt to debug every php script
; 开启后所有请求都会进行debug 关闭后请求时带XDEBUG_SESSION_START参数才会进行debug 
xdebug.remote_autostart = 1

; ide key
;xdebug.idekey = PHPSTORM

; log file
;xdebug.remote_log = /tmp/xdebug.log
```

#### 重启fpm
```
service php5-fpm restart
```

#### 设置path mapping将本地代码与远程代码建立对应关系

#### 开启9000端口（linux系统下可能被防火墙拦截没有开启9000端口）
```
iptables -I INPUT -p tcp --dport 9000 -j ACCEPT
```

#### 开启IDE调试监听（PhpStorm的监听按钮是那个电话图标的）
```
Run->Start Listening for PHP Debug Connections
```

#### 查看端口状态
```
netstat -tunpl
```

#### 打断点

#### 请求php程序会停在打断点的行

#### 其他
```
首行中断，将在执行至第一行代码时中断。在不清楚项目的入口结构时有用。
```
