<?php
/****************
sng:11/jan/2011
If the mmt is 30 days old (calculated from date of submit), then, all the hits data
for that job is deleted automatically, assuming it finished. However, the request data is not deleted. This way
the same request can be re-run again.
**********/
require_once(dirname(dirname(__FILE__))."/include/global.php");
require_once("classes/class.make_me_top.php");
$success = $g_maketop->delete_old_mmt_hits();
?>