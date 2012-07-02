<?php
$cmd = "/usr/bin/php -f /mnt/stor3-wc2-dfw1/494675/www.mytombstones.com/web/content/cron/trash4.php";
	exec($cmd . " > /dev/null &");
	//print_r(passthru($cmd,$output));
?>