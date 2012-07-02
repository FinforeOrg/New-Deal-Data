<?php
/****
sng:06/oct/2010
In case admin has to manually resend the activation email
***/
require_once("../../include/global.php");
require_once("classes/class.member.php");
/////////////////////////////////////////////
$success = $g_mem->resend_activation_email($_POST['uid']);
if($success){
	echo "sent";
}else{
	echo "Cound not send";
}
?>