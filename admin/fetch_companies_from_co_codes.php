<?php
/**********
sng:15/oct/2012

Allow admin to fetch company data from co-codes.com.
Since lots of processing is involved, the code that does the actual work runs in the background

see table background_slave_monitor where slave names are listed

sng:27/oct/2012
Now the code at co-codes.com notify us and that triggers the fetching.
We just use this to show the last time the code was run.

Of course, it may happen that the slave is just running then. In that case the code waits and poll again
************/
require_once("../include/global.php");
require_once ("admin/checklogin.php");

require_once("classes/class.background_slave_controller.php");
$master = new background_slave_controller();

$g_view['heading'] = "Fetch companies from co-codes.com";
$g_view['content_view'] = "admin/fetch_companies_from_co_codes_view.php";
require_once("admin/content_view.php");
?>