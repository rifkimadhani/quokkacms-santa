#!/bin/bash -x

#melakukan setting access ke semua folder yg di butuhkan

#check apakah ada user daemon ?
user_php="$1"

#if parameter kosong maka otomatis set ke daemon (xampp)
if [ -z "$user_php" ]
then
    user_php="daemon"
fi

#get current username before sudo
user=${SUDO_USER}

#mkdir /opt/lampp/htdocs/ott2/assets/content

#folder ini utk adb
#mkdir /usr/sbin/.android
#chown /usr/sbin/.android ${user_php}:${user_php} #rubah owner shg bisa di akses oleh php

#rubah owner dan group dari main folder
chown --verbose --recursive ${user}:${user_php} .. *

#user_php tdk boleh write file php
#disable write access utk user_php, hanya owner saja yg bisa write ke file php
#chmod --verbose --recursive g-w ..

#folder yg bisa di write utk user_php
chmod --verbose --recursive g+rw ../public/filemanager/thumbs
chmod --verbose --recursive g+rw ../public/assets
chmod --verbose --recursive g+rw ../writable

#disable lib bawaan php, aapt bermasalah dgn lib bawaan dari php. lib ini sdh ada dari ubuntu
mv /opt/lampp/lib/libstdc++.so.6.0.19 /opt/lampp/lib/libstdc++.so.6.0.19.bak