#### 在包含[CMD]的行首添加//
```
sed -e "/\[CMD\]/s/^/\/\//" filename
```

## 一个例子
#### 加注释
```
sed -i -e "/window\.location\.href.*\[CMD\]/s/^/\/\//" -e "/window\.location\.href.*cmdStr/s/^/\/\//" `grep -r '' . | grep -iv '\.svn' | grep --color=auto  '\[CMD\]' | awk -F ':' '{print $1}' | sort -u | xargs`
```

#### 去除注释
```
sed -i -e "/window\.location\.href.*\[CMD\]/s/^\/\///" -e "/window\.location\.href.*cmdStr/s/^\/\///" `grep -r '' . | grep -iv '\.svn' | grep --color=auto  '\[CMD\]' | awk -F ':' '{print $1}' | sort -u | xargs`
```
