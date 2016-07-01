
# svn-manage

#### change config

vim svn-manage.sh
change the following config into your own

```
# config
svn_username='svn_username'
svn_password='svn_password'
repo_path='svn://192.168.0.77/repos/mc'
trunk_dir_name='trunk'
branch_dir_name='branches'
```

#### create branch

```bash
[root@vm project]# /home/michael/mydata/toolkit/svn/svn-manage/svn-manage.sh branch project 2016.07.01.22.36.05/project
svn-manage

--- repo info ---
repo_path:   svn://192.168.0.77/repos/mc
trunk_path:  svn://192.168.0.77/repos/mc/trunk
branch_path: svn://192.168.0.77/repos/mc/branches

--- create branch ---
project_name: project
branch_name:  2016.07.01.22.36.05/project
source_url:      svn://192.168.0.77/repos/mc/trunk/project
destination_url: svn://192.168.0.77/repos/mc/branches/2016.07.01.22.36.05/project
are you sure? [y/n]: y

Committed revision 2067.
success
```

#### merge

```bash
[root@vm project]# /home/michael/mydata/toolkit/svn/svn-manage/svn-manage.sh merge svn://192.168.0.77/repos/mc/branches/2016.07.01.22.36.05/project /home/html/project/
svn-manage

--- repo info ---
repo_path:   svn://192.168.0.77/repos/mc
trunk_path:  svn://192.168.0.77/repos/mc/trunk
branch_path: svn://192.168.0.77/repos/mc/branches

--- merge ---
source_url: svn://192.168.0.77/repos/mc/branches/2016.07.01.22.36.05/project
work_copy_path: /home/html/project/
start_rev: r2055
are you sure? [y/n]: y
--- Merging r2056 through r2057 into '/home/html/project':
U    /home/html/project/app/module/common/js/preload.js
U    /home/html/project/app/module/common/js/classes/receive/LoginResult.js
U    /home/html/project/app/module/common/js/classes/object/Account.js
U    /home/html/project/app/index.html
success

```

