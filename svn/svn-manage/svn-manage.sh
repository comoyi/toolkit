#!/bin/bash

# Michael Chi
# 2016-06-18

#
# 1.create branch
# 2.merge
#

echo "svn-manage"
echo ""

# config
svn_username='svn_username'
svn_password='svn_password'
repo_path='svn://192.168.0.77/repos/mc'
trunk_dir_name='trunk'
branch_dir_name='branches'

# color
color_reset='\033[0m'
color_red='\033[1;31m'
color_green='\033[1;32m'
color_yellow='\033[1;33m'
color_blue='\033[1;34m'

trunk_path="${repo_path}/${trunk_dir_name}"
branch_path="${repo_path}/${branch_dir_name}"
echo "--- repo info ---"
echo "repo_path:   ${repo_path}"
echo "trunk_path:  ${trunk_path}"
echo "branch_path: ${branch_path}"
echo ""

# create branch
function create_branch(){
    local project_name=${1}
    echo "project_name: ${project_name}"
    local branch_name="${2}"
    echo "branch_name:  ${branch_name}"
    local source_url="${trunk_path}/${project_name}"
    echo "source_url:      ${source_url}"
    local destination_url="${branch_path}/${branch_name}"
    echo "destination_url: ${destination_url}"
    local confirm=
    read -p 'are you sure? [y/n]: ' confirm
    if [ "y" = "${confirm}" ]; then
        svn cp -m '[create branch]' --parents --username "${svn_username}" --password "${svn_password}" --no-auth-cache "${source_url}" "${destination_url}"
        if [ "0" = "$?" ]; then
            printf "${color_green}success${color_reset}\n"
        else
            printf "${color_red}failed${color_reset}\n"
        fi
    else
        printf "${color_yellow}cancelled${color_reset}\n"
    fi
}

# merge
function merge(){
    local source_url=${1}
    echo "source_url: ${source_url}"
    local work_copy_path=${2}
    echo "work_copy_path: ${work_copy_path}"
    local start_rev=$(svn log --username chic --password ccsvn --no-auth-cache --stop-on-copy "${source_url}"  -q | tac | sed -n '2, 1p' | awk '{print $1}')
    printf "start_rev: ${color_red}${start_rev}${color_reset}\n"
    local confirm=
    read -p 'are you sure? [y/n]: ' confirm
    if [ "y" = "${confirm}" ]; then
        svn merge --username ${svn_username} --password ${svn_password} --no-auth-cache --accept postpone -r "${start_rev}":HEAD "${source_url}" "${work_copy_path}"
        if [ "0" = "$?" ]; then
            printf "${color_green}success${color_reset}\n"
        else
            printf "${color_red}failed${color_reset}\n"
        fi
    else
        printf "${color_yellow}cancelled${color_reset}\n"
    fi
}

sub_command=${1}

case "${sub_command}" in
    "branch")
        echo "--- create branch ---"
        project_name=${2}
        branch_name=${3}
        create_branch "${project_name}" "${branch_name}"
        ;;
    "merge")
        echo "--- merge ---"
        source_url=${2}
        work_copy_path=${3}
        merge "${source_url}" "${work_copy_path}"
        ;;
    "help")
        echo "--- help ---"
        case "${2}" in
            "branch")
                echo "
                svn_manage.sh branch project 2016.07.01.22.36.05/project
                --- create branch ---
                project_name: project
                branch_name:  2016.07.01.22.36.05/project
                source_url:      svn://192.168.0.77/repos/mc/trunk/project
                destination_url: svn://192.168.0.77/repos/mc/branches/2016.07.01.22.36.05/project
                are you sure? [y/n]: y
                success
                "
                ;;
            "merge")
                echo "
                svn_manage.sh merge svn://192.168.0.77/repos/mc/branches/2016.07.01.22.36.05/project /home/html/project
                --- merge ---
                source_url: svn://192.168.0.77/repos/mc/branches/2016.07.01.22.36.05/project
                work_copy_path: /home/html/project
                start_rev: r1991
                are you sure? [y/n]: y
                success
                "
                ;;
            *)
                printf "
                svn_manage.sh help branch
                svn_manage.sh help merge
                \n"
                ;;
        esac
        ;;
    *)
        echo "--- help ---"
        echo "create        create branch"
        ;;
esac

