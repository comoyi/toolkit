```
---
ip: "192.168.10.77"
memory: 2048
cpus: 1
provider: virtualbox

box: develop-0.0.3
hostname: develop
name: develop

networks:
#    # company - pc - windows
#    - type: "public_network"
#      ip: "192.168.11.111"

    # company - laptop - centos
    - type: "public_network"
      ip: "192.168.11.248"

#    # home - pc - windows
#    - type: "public_network"
#      ip: "192.168.31.55"

#    # home - laptop - windows
#    - type: "public_network"
#      ip: "192.168.31.66"

#    # home - laptop - centos
#    - type: "public_network"
#      ip: "192.168.31.77"

authorize: ~/.ssh/id_rsa.pub

keys:
    - ~/.ssh/id_rsa

folders:
    - map: ~/shared
      to: /home/vagrant/data

sites:
    - map: homestead.app
      to: /home/vagrant/data/code/Laravel/public

    - map: demo.com
      to: /home/vagrant/data/code/demo

    - map: test.com
      to: /home/vagrant/data/code/test

    - map: jxw.com
      to: /home/vagrant/data/code/jxw.com
      type: jxw
    - map: admin.jxw.com
      to: /home/vagrant/data/code/jxw.com
      type: jxw-admin
    - map: interface.jxw.com
      to: /home/vagrant/data/code/jxw.com
      type: jxw-interface

    - map: jxy.com
      to: /home/vagrant/data/code/jxy.com
      type: jxy
    - map: admin.jxy.com
      to: /home/vagrant/data/code/jxy.com
      type: jxy-admin
    - map: interface.jxy.com
      to: /home/vagrant/data/code/jxy.com
      type: jxy-interface
    - map: service.jxy.com
      to: /home/vagrant/data/code/jxy.com
      type: jxy-service

    - map: jxz.com
      to: /home/vagrant/data/code/jxz.com
      type: jxz
    - map: admin.jxz.com
      to: /home/vagrant/data/code/jxz.com
      type: jxz-admin
    - map: interface.jxz.com
      to: /home/vagrant/data/code/jxz.com
      type: jxz-interface

#databases:
#    - homestead

# customize $_SERVER params
variables:
    - key: APP_ENV
      value: local
    - key: CODE_ENV
      value: develop

# blackfire:
#     - id: foo
#       token: bar
#       client-id: foo
#       client-token: bar

# ports:
#     - send: 93000
#       to: 9300
#     - send: 7777
#       to: 777
#       protocol: udp
```

