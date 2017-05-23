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

mkdir -p $wwwroot/functions
mkdir -p $wwwroot/user
mkdir -p $wwwroot/css

#find $workingdir -name "*.php" -exec cp -i {} -t $wwwroot \;