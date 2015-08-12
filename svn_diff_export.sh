#!/bin/sh

# Michael_Chi
# export changes for svn

color_reset='\033[0m'
color_green='\033[1;32m'
color_red='\033[1;31m'
color_blue='\033[1;34m'
color_yellow='\033[1;33m'

revision_from=${1} # start version
revision_to=${2} # end version
repository_url=${3} # repository url
destination_path=${4} # path to store the export files

# show summarize
show_summarize()
{
    printf "${color_yellow}> Summarize${color_reset}\n"
    svn diff -r ${revision_from}:${revision_to} --summarize ${repository_url} | sort
}

# export changed files
export_change()
{
    printf "${color_yellow}> Start exporting changed files between revision ${color_red}${revision_from}${color_yellow} and revision ${color_red}${revision_to}${color_yellow} to ${color_blue}${destination_path}${color_reset}\n";
    changed_files=`svn diff -r ${revision_from}:${revision_to} --summarize ${repository_url} | sort | awk '/^[AM]/{print $NF}'`
    for file in ${changed_files}
    do
        tmp_svn_full_file_name=${file}
        tmp_full_file_name=${file#*${repository_url}}
        tmp_file_path=`dirname ${tmp_full_file_name}`
        tmp_file_name=${tmp_full_file_name#*${tmp_file_path}}
        tmp_destination_full_file_name=${destination_path}${tmp_full_file_name}
        tmp_destination_path=`dirname ${tmp_destination_full_file_name}`
        #printf "svn_full_name: ${tmp_svn_full_file_name} full_file_name: ${tmp_full_file_name} file_path: ${tmp_file_path} file_name: ${tmp_file_name} tmp_destination_full_file_name: ${tmp_destination_full_file_name} destination_path: ${tmp_destination_path}\n"
        [ ! -d ${tmp_destination_path} ] && mkdir -p ${tmp_destination_path}
        printf "${tmp_destination_full_file_name}\n"
        svn export --depth 'empty' --force -q -r ${revision_to} ${tmp_svn_full_file_name} ${tmp_destination_full_file_name}
    done
}

printf "${color_green}------- start -------${color_reset}\n";
show_summarize
export_change
printf "${color_green}-------  end  -------${color_reset}\n";
