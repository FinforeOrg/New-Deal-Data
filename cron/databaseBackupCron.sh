#!/bin/sh
 mysqldump --opt -h mysql50-46.wc2.dfw1.stabletransit.com -u 494675_finfore -p'JJ96_mbPP' 494675_mytombstones > /mnt/stor3-wc2-dfw1/494675/www.mytombstones.com/db_backup.sql
 
gzip -f /mnt/stor3-wc2-dfw1/494675/www.mytombstones.com/db_backup.sql

tar czf /mnt/stor3-wc2-dfw1/494675/www.mytombstones.com/web_backup.tgz /mnt/stor3-wc2-dfw1/494675/www.mytombstones.com/web/content/


