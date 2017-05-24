#!/bin/bash

workingdir=$(pwd)
wwwroot=/var/www
if [[ ! $EUID -eq 0 ]]; then
    exec sudo $0 $@ || echo "FOG Installation must be run as root user"
    exit 1 # Fail Sudo
fi


read -p "Enter the path to the repository [$workingdir]: " tempwork

if [ -n "$tempwork" ];
then
	workingdir=$tempwork
fi

read -p "Enter the path to your www root for this app [$wwwroot]: " tempwww

if [ -n "$tempwww" ];
then
	wwwroot=$tempwww
fi

find $workingdir -name '*.php' -print | cpio -pvdumB $wwwroot

read -p "Enter your mysql username: " username
read -p -s "Enter your mysql password: " password
read -p "Enter the mysql hostname: " hostname

sed -i -e "s/myuser/${username}/g" ${workingdir}/setup/database_setup.sql
sed -i -e "s/myhost/${hostname}/g" ${workingdir}/setup/database_setup.sql

mysql -h $hostname -u $username -p$password < ${workingdir}/setup/database_setup.sql

echo "Don't forget to edit your config.php file"