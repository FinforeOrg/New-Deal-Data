<?php
//require_once("../include/global.php");
require_once(dirname(dirname(__FILE__))."/include/global.php");
require_once("classes/class.member.php");
//////////////////////////////////////////////
$g_mem->cron_delete_unverified_company_email_change_request();
?>