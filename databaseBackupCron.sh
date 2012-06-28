#!/bin/sh
 mysqldump --opt -h localhost -u mihai -pimihai mytombstones > /var/www/backUps/db_backup.sql
 
gzip -f /var/www/backUps/db_backup.sql

tar czf /var/www/backUps/web_backup.tgz /var/www/home 


