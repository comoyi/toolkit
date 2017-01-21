## 例子

#### 初始化

```bash
git init project
```
#### 进入项目目录

```bash
cd project
```

```
这个时候还没有master 需要添加文件add commit后才会创建master分支 如果这个时候开分支会提示错误
git branch branch-1
fatal: Not a valid object name: 'master'.
```

#### 创建一个说明文件

```bash
touch README.md
```

#### 添加

```bash
git add README.md
```

#### 提交

```bash
git commit -m 'init'
```

#### 现在有master了

```bash
git branch
```

```
* master
```

#### 开分支

```bash
git branch branch-1
```

#### 查看所有分支

```bash
git branch
```

```
  branch-1
* master
```

#### 切换到分支

```bash
git checkout branch-1
```

#### 当要恢复的文件名和某个分支名称相同的时候有可能会提示错误（因为区分不了你想切换分支还是恢复文件到修改前状态）还有就是当文件名以-或者--开头的时候 这些时候需要加上--

```bash
git checkout filename    # may be wrong
git checkout -- filename # right
```

#### git diff 

```
graph TD
A(HEAD)-- git diff --staged -->B(index)
A-- git diff HEAD -->C(working tree)
B-- git diff -->C
```

#### If file have staged, diff changes between working tree and index.
#### If file not staged, diff changes between the working tree and your HEAD.

```bash
git diff
```

#### If you mean the changes between the index and your HEAD.

```bash
git diff --staged
```

#### If you mean the changes between the working tree and your HEAD (i.e. both staged and unstaged changes together) this is just done with:

```bash
git diff HEAD
```

#### 将文件从index移除

```bash
git reset HEAD <file>
```

#### 解决冲突

```bash
git mergetool
```

```
vimdiff
co : 当前冲突使用我的版本
ct : 当前冲突使用别人的版本
cb : 当前冲突同时保留我和别人的版本
```

#### Show information about files in the index and the working tree

```
git ls-files
```

#### 常用命令

```bash
git branch      # 查看当前repo的所有分支；
git branch <branch> # 创建一个新的命名为<branch>的分支，注意这条命令不会checkout 
git branch -d <branch> # 删除指定的分支。如果还有一些unmerged changes，git是不允许你删除的。
git branch -D <branch> # 强制删除一个分支，即使该分支有未merge的变更。
git branch -m <branch> # rename current branch to <branch>


git checkout <existing-branch> # 这条命令checkout已经存在的一个分支，更新工作目录为对应分支版本；
git checkout -b <new-branch> # 以当前分支HEAD创建并且checkout到new-branch
git checkout -b <new-branch> <existing-branch> # 以指定<exisiting-branch>的HEAD创建一个new-branch
```

#### 删除远程分支
```
git push origin --delete branch-name
```
