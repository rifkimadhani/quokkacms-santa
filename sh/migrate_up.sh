#!/bin/bash -x

#mysql path
path=/opt/lampp/bin/

username=$1
database=$2

#ttheme - add field last_update
$path/mysql -u $username -p -e "USE ${database}; ALTER TABLE ttheme ADD last_update DATETIME DEFAULT CURRENT_TIMESTAMP;"
