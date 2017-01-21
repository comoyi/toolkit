## steps for use vpn

### install
```
sudo yum -y install pptp pptp-setup
```

### stop firewall or set iptables
```
systemctl stop firewalld.service

# or

sudo iptables -I INPUT -p gre -j ACCEPT
```

### copy script
```
sudo cp /usr/share/doc/ppp-2.4.5/scripts/pon /usr/sbin/
sudo cp /usr/share/doc/ppp-2.4.5/scripts/poff /usr/sbin/
```

### setup vpn config
```
sudo pptpsetup --create <tunnel-name> --server <server> --username <username> --password <password> --encrypt
```

### start vpn
```
sudo pon <tunnel-name>
```

#### add a route to the local network 192.168.11.x via "ppp0".
```
sudo route add -net 192.168.11.0 netmask 255.255.255.0 dev ppp0
```

### stop vpn
```
sudo poff <tunnel-name>
```
