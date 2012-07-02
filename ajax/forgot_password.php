<?php
/****
sng:4/oct/2010
get the work email, get the user, email the password to the work email
*******/
require_once("../include/global.php");
require_once("classes/class.account.php");
$msg = "";
$success = $g_account->email_password_of_site_member($_POST['work_email'],$msg);
if(!$success){
	echo "Problem encountered while checking. Please try again.";
}else{
	echo $msg;
}
?>