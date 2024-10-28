#!/bin/bash -x

# Check if username and database parameters are provided
if [ -z "$1" ] || [ -z "$2" ]; then
    echo "Usage: $0 <username> <database>"
    exit 1
fi

#mysql path
path=/opt/lampp/bin/

username=$1
database=$2

#ttheme - add field last_update
$path/mysql -u $username -p -e "USE ${database}; ALTER TABLE ttheme ADD last_update DATETIME DEFAULT CURRENT_TIMESTAMP;"

#facility sorting
# - tfacility - add field ord
$path/mysql -u $username -p -e "USE ${database}; ALTER TABLE tfacility ADD ord INT DEFAULT 100;"
