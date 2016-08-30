#! /bin/bash

#
# tmux开发环境初始化脚本
# author: Michael Chi
# 2016-07-03 11:32:17
#

# tmux
tmux='tmux'

# vim
vim='vim -u ~/.spf13-vim-3/.vimrc'
#vim='vim'

# mysql
mysql='mysql'
mysql_host='localhost'
mysql_user='user'
mysql_password='password'

# redis
redis_cli='/opt/redis/bin/redis-cli'
redis_cli_host='localhost'
redis_cli_port='6000'
redis_cli_password='redis123456'

# 项目名称
project_template='work-template'
project_michael='work-michael'

# 初始化项目环境
function init_project_template()
{
    # work path
    local work_path='/home/vagrant/mydata/code/template'
    cd ${work_path}

    local cmd_debug_log='touch /tmp/debug_`date +%Y%m%d`.log && chown nobody:nobody /tmp/debug_`date +%Y%m%d`.log && tail -f /tmp/debug_`date +%Y%m%d`.log'

    ${tmux} -2 new-session -d -s ${project_template}

    # vim
    ${tmux} new-window -d -n vim -t ${project_template}
    ${tmux} send-keys -t "${project_template}:vim" "cd ${work_path}" C-m
    ${tmux} send-keys -t "${project_template}:vim" "${vim}" C-m

    # log
    ${tmux} new-window -d -n log -t ${project_template}
    ${tmux} send-keys -t "${project_template}:log" "${cmd_debug_log}" C-m

    # git
    ${tmux} new-window -d -n git -t ${project_template}
    ${tmux} send-keys -t "${project_template}:git" "cd ${work_path}" C-m

#    # svn
#    ${tmux} new-window -d -n svn -t ${project_template}
#    ${tmux} send-keys -t "${project_template}:svn" "cd ${work_path}" C-m

    # mysql
    ${tmux} new-window -d -n mysql -t ${project_template}
    ${tmux} send-keys -t "${project_template}:mysql" "${mysql} -h${mysql_host} -u${mysql_user} -p${mysql_password}" C-m

    # redis
    ${tmux} new-window -d -n redis -t ${project_template}
    ${tmux} send-keys -t "${project_template}:redis" "${redis_cli} -h ${redis_cli_host} -p ${redis_cli_port} -a ${redis_cli_password}" C-m

    # workspace
    ${tmux} new-window -d -n workspace -t ${project_template}
    ${tmux} send-keys -t "${project_template}:workspace" "cd ${work_path}" C-m

    # x
    ${tmux} new-window -d -n x -t ${project_template}
    ${tmux} send-keys -t "${project_template}:x" "cd ${work_path}" C-m

    ${tmux} select-window -t vim

}

# 初始化项目环境
function init_project_michael()
{
    # work path
    local work_path='/home/vagrant/mydata/code/michael'
    cd ${work_path}

    local cmd_debug_log='touch /tmp/debug_`date +%Y%m%d`.log && chown nobody:nobody /tmp/debug_`date +%Y%m%d`.log && tail -f /tmp/debug_`date +%Y%m%d`.log'

    ${tmux} -2 new-session -d -s ${project_michael}

    # vim
    ${tmux} new-window -d -n vim -t ${project_michael}
    ${tmux} send-keys -t "${project_michael}:vim" "cd ${work_path}" C-m
    ${tmux} send-keys -t "${project_michael}:vim" "${vim}" C-m

    # log
    ${tmux} new-window -d -n log -t ${project_michael}
    ${tmux} send-keys -t "${project_michael}:log" "${cmd_debug_log}" C-m

    # git
    ${tmux} new-window -d -n git -t ${project_michael}
    ${tmux} send-keys -t "${project_michael}:git" "cd ${work_path}" C-m

#    # svn
#    ${tmux} new-window -d -n svn -t ${project_michael}
#    ${tmux} send-keys -t "${project_michael}:svn" "cd ${work_path}" C-m

    # mysql
    ${tmux} new-window -d -n mysql -t ${project_michael}
    ${tmux} send-keys -t "${project_michael}:mysql" "${mysql} -h${mysql_host} -u${mysql_user} -p${mysql_password}" C-m

    # redis
    ${tmux} new-window -d -n redis -t ${project_michael}
    ${tmux} send-keys -t "${project_michael}:redis" "${redis_cli} -h ${redis_cli_host} -p ${redis_cli_port} -a ${redis_cli_password}" C-m

    # workspace
    ${tmux} new-window -d -n workspace -t ${project_michael}
    ${tmux} send-keys -t "${project_michael}:workspace" "cd ${work_path}" C-m

    # x
    ${tmux} new-window -d -n x -t ${project_michael}
    ${tmux} send-keys -t "${project_michael}:x" "cd ${work_path}" C-m

    ${tmux} select-window -t vim

}

${tmux} has-session -t ${project_template}
if [ $? != 0 ]; then
    init_project_template
else
    ${tmux} kill-session -t ${project_template}
    init_project_template
fi

#${tmux} has-session -t ${project_michael}
#if [ $? != 0 ]; then
#    init_project_michael
#else
#    ${tmux} kill-session -t ${project_michael}
#    init_project_michael
#fi

${tmux} -2 attach-session -t ${project_template}
#${tmux} -2 attach-session -t ${project_michael}

