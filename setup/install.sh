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
read -s -p "Enter your mysql password: `echo $'\n> '`" password
read -p "Enter the mysql hostname: " hostname
read -p "Enter your app URL: " appurl
read -p "Enter URL to your mySQL REST API: " apiurl
read -p "Enter your reCAPTCHA site key: " sitekey
read -p "Enter your reCAPTCHA secret: " secretkey
read -p "Enter your app name: " appname
read -p "Enter your name: " appauthor
read -p "Enter app version: " appversion


sed -i -e "s/ENTER_YOUR_APP_URL/${appurl}/g" ${workingdir}/config.php
sed -i -e "s/ENTER_URL_TO_MYSQL_REST_API/${apiurl}/g" ${workingdir}/config.php
sed -i -e "s/ENTER_RECAPTCHA_SITE_KEY/${sitekey}/g" ${workingdir}/config.php
sed -i -e "s/ENTER_RECAPTCHA_SECRET_KEY/${secretkey}/g" ${workingdir}/config.php
sed -i -e "s/ENTER_APP_NAME/${appname}/g" ${workingdir}/config.php
sed -i -e "s/ENTER_APP_URL/${appurl}/g" ${workingdir}/config.php
sed -i -e "s/ENTER_APP_AUTHOR/${appauthor}/g" ${workingdir}/config.php
sed -i -e "s/ENTER_APP_LANGUGAGE/${appversion}/g" ${workingdir}/config.php

sed -i -e "s/myuser/${username}/g" ${workingdir}/setup/database_setup.sql
sed -i -e "s/myhost/${hostname}/g" ${workingdir}/setup/database_setup.sql

mysql -h $hostname -u $username -p$password < ${workingdir}/setup/database_setup.sql

echo "Don't forget to edit your config.php file"