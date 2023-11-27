#!/bin/bash -x

app_id=com.madeiraresearch.hoteliptv3
folder=/data/mr/$app_id

if [ -z "$1" ]; then
    echo "Usage: uninstall-app.sh <ip-address>"
    exit
fi

ip=$1

adb connect $ip:5555
adb root

# remote folder
adb -s $ip:5555 shell "rm -r $folder"

# uninstall hoteliptv3
adb -s $ip:5555 shell "pm uninstall $app_id"

adb disconnect $ip:5555
