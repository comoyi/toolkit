### delete default route
```
sudo route del default
```

### add default route
```
sudo route add default dev ppp0

# example
# sudo route add default dev ppp0
```

### set default route
```
sudo route add default metric <metric> gw <gateway> dev <device>

# example
# sudo route add default metric 600 gw 192.168.31.1 dev wlp3s0
```
