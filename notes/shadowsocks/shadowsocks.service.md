#### shadowsocks.service
```
[Unit]
Description=shadowsocks
After=network.target

[Service]
Type=forking
PIDFile=/var/run/shadowsocks.pid
ExecStart=/usr/bin/ssserver --pid-file /var/run/shadowsocks.pid -c /etc/shadowsocks.json -d start
ExecReload=/usr/bin/ssserver --pid-file /var/run/shadowsocks.pid -c /etc/shadowsocks.json -d restart
ExecStop=/usr/bin/ssserver --pid-file /var/run/shadowsocks.pid -c /etc/shadowsocks.json -d stop
PrivateTmp=true

[Install]  
WantedBy=multi-user.target
```
