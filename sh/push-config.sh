#!/bin/bash -x

app_id=com.madeiraresearch.hoteliptv3
folder=/data/mr/$app_id

if [ -z "$1" ]; then
    echo "Usage: push-config.sh <ip-address>"
    exit
fi

ip=$1

#connect
adb connect $ip:5555
adb root

# CREATE FOLDER
adb -s $ip:5555 shell "mkdir -p $folder"
adb -s $ip:5555 shell "chmod -R 777 $folder"

# push config
adb -s $ip:5555 push config.json $folder

#disconnect
adb disconnect $ip:5555
