#!/bin/sh
#
# open cron
# crontab -e
#
# 40 15 * * * /opt/lampp/htdocs/ott2/cron/backup_db.sh

path=/data/db
password=$1
database=$2
filename="$database-$(date '+%Y%m%d-%H%M%S').sql"

#/opt/lampp/bin/mysqldump --opt -u root --password=$password $database > $path/$filename
/opt/lampp/bin/mysqldump --opt -e --triggers --single-transaction -u root --password=$password $database > $path/$filename

#delete file backup if more than 60 days old
find $path -name "*.sql" -type f -mtime +60 -delete

