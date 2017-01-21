## svn常用操作

#### 查看当前分支提交记录
```
svn log --stop-on-copy `svn info | awk '$1=="URL:"{print $NF}'`
```

#### 查看状态
```
svn st
```

#### 查看未提交的修改
```
svn diff
```

#### 提交
```
svn ci -m '1.a style of real time monitor' --username name --no-auth-cache
```

#### 设置编辑器
```
export SVN_EDITOR=vim
```

#### 设置要忽略的文件
```
svn pe svn:ignore compiled/
```

#### 设置全局忽略文件
```
编辑 ~/.subversion/config文件 搜索 global-ignores 然后添加要忽略的文件名 ._.DS_Store .DS_Store
```
