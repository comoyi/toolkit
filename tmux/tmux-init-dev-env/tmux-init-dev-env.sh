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
mysql_user='user'
mysql_password='password'

# redis
redis_cli='/opt/redis/bin/redis-cli'
redis_cli_port='6000'
redis_cli_password='redis123456'

# 项目名称
project_call='mc_work_call'
project_michael='mc_work_michael'

# 初始化项目环境
function init_project_call()
{
    local cmd_debug_log='touch /tmp/debug_`date +%Y%m%d`.log && chown nobody:nobody /tmp/debug_`date +%Y%m%d`.log && tail -f /tmp/debug_`date +%Y%m%d`.log'

    cd /home/html/call

    ${tmux} -2 new-session -d -s ${project_call}

    # vim
    ${tmux} new-window -d -n vim -t ${project_call}
    ${tmux} send-keys -t "${project_call}:vim" 'cd /home/html/call' C-m
    ${tmux} send-keys -t "${project_call}:vim" "${vim}" C-m

    # log
    ${tmux} new-window -d -n log -t ${project_call}
    ${tmux} send-keys -t "${project_call}:log" "${cmd_debug_log}" C-m

    # svn
    ${tmux} new-window -d -n svn -t ${project_call}
    ${tmux} send-keys -t "${project_call}:svn" 'cd /home/html/call' C-m

    # mysql
    ${tmux} new-window -d -n mysql -t ${project_call}
    ${tmux} send-keys -t "${project_call}:mysql" "${mysql} -u${mysql_user} -p${mysql_password} call" C-m

    # redis
    ${tmux} new-window -d -n redis -t ${project_call}
    ${tmux} send-keys -t "${project_call}:redis" "${redis_cli} -p ${redis_cli_port} -a ${redis_cli_password}" C-m

    # workspace
    ${tmux} new-window -d -n workspace -t ${project_call}
    ${tmux} send-keys -t "${project_call}:workspace" 'cd /home/html/call' C-m

    # x
    ${tmux} new-window -d -n x -t ${project_call}
    ${tmux} send-keys -t "${project_call}:x" 'cd /home/html/call' C-m


    ${tmux} select-window -t vim

}

# 初始化项目环境
function init_project_michael()
{
    local cmd_debug_log='touch /tmp/debug_`date +%Y%m%d`.log && chown nobody:nobody /tmp/debug_`date +%Y%m%d`.log && tail -f /tmp/debug_`date +%Y%m%d`.log'

    cd /home/html/michael

    ${tmux} -2 new-session -d -s ${project_michael}

    # vim
    ${tmux} new-window -d -n vim -t ${project_michael}
    ${tmux} send-keys -t "${project_michael}:vim" 'cd /home/html/michael' C-m
    ${tmux} send-keys -t "${project_michael}:vim" "${vim}" C-m

    # log
    ${tmux} new-window -d -n log -t ${project_michael}
    ${tmux} send-keys -t "${project_michael}:log" "${cmd_debug_log}" C-m

    # svn
    ${tmux} new-window -d -n svn -t ${project_michael}
    ${tmux} send-keys -t "${project_michael}:svn" 'cd /home/html/michael' C-m

    # mysql
    ${tmux} new-window -d -n mysql -t ${project_michael}
    ${tmux} send-keys -t "${project_michael}:mysql" "${mysql} -u${mysql_user} -p${mysql_password} " C-m

    # redis
    ${tmux} new-window -d -n redis -t ${project_michael}
    ${tmux} send-keys -t "${project_michael}:redis" "${redis_cli} -p ${redis_cli_port} -a ${redis_cli_password}" C-m

    # workspace
    ${tmux} new-window -d -n workspace -t ${project_michael}
    ${tmux} send-keys -t "${project_michael}:workspace" 'cd /home/html/michael' C-m

    # x
    ${tmux} new-window -d -n x -t ${project_michael}
    ${tmux} send-keys -t "${project_michael}:x" 'cd /home/html/michael' C-m


    ${tmux} select-window -t vim

}

#${tmux} has-session -t ${project_call}
#if [ $? != 0 ]; then
#    init_project_call
#else
#    ${tmux} kill-session -t ${project_call}
#    init_project_call
#fi

${tmux} has-session -t ${project_michael}
if [ $? != 0 ]; then
    init_project_michael
else
    ${tmux} kill-session -t ${project_michael}
    init_project_michael
fi

${tmux} -2 attach-session -t ${project_michael}

