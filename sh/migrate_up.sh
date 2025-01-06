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

cmd="
USE ${database};
SET FOREIGN_KEY_CHECKS=0;
-- START CMD ------------------------------------------------------

-- ttheme - add field last_update
ALTER TABLE ttheme ADD COLUMN IF NOT EXISTS last_update DATETIME DEFAULT CURRENT_TIMESTAMP;

-- tfacility - add field ord
ALTER TABLE tfacility ADD COLUMN IF NOT EXISTS ord INT DEFAULT 100;

-- table visitor
CREATE TABLE IF NOT EXISTS tvisitor (
    id INT PRIMARY KEY AUTO_INCREMENT,
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    page_url VARCHAR(255),
    visit_date DATETIME,
    visit_count INT DEFAULT 1,
    create_date DATETIME NULL DEFAULT current_timestamp,
    update_date DATETIME NULL DEFAULT current_timestamp ON UPDATE CURRENT_TIMESTAMP
);

-- END CMD ------------------------------------------------------
SET FOREIGN_KEY_CHECKS=1;"

# Execute the commands
$path/mysql -u $username -p -e "$cmd"

#ttheme - add field last_update
#$path/mysql -u $username -p -e "USE ${database}; ALTER TABLE ttheme ADD last_update DATETIME DEFAULT CURRENT_TIMESTAMP;"

#facility sorting
# - tfacility - add field ord
#$path/mysql -u $username -p -e "USE ${database}; ALTER TABLE tfacility ADD ord INT DEFAULT 100;"

