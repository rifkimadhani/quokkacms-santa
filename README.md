# ALPHA CMS 2

CMS ini di buat dari cms ott2 dan cms homeconnect.
CMS ini mempergunakan Code igniter 4.3.

#### ADB not running
Utk masalah adb pada ubuntu 20.04
solusi nya dgn membuat folder /usr/sbin/.android

`sudo mkdir /usr/sbin/.android`

`sudo chmod o+w /usr/sbin/.android`

============================

#### Changes database
- trole, rubah role_id dari auto increment --> not auto increment
- vroomservice_order