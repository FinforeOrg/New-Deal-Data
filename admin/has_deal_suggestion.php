<?php
/******************
given a deal id and the field, it returns an image that suggest whether there
is any suggestions or note
******************************/
require_once("../include/global.php");

require_once("classes/class.account.php");
if(!$g_account->is_admin_logged()){
	header("Content-type: image/png");
	echo file_get_contents("images/light-bulb-off.png");
	exit;
}

require_once("classes/class.deal_support.php");
$g_deal_support = new deal_support();

$deal_id = $_GET['deal_id'];
$transaction_field = $_GET['data_name'];

$found = $g_deal_support->admin_has_data_correction_on_deal($deal_id,$transaction_field);
if(!$found){
	//echo "no suggestion";
	header("Content-type: image/png");
	echo file_get_contents("images/light-bulb-off.png");
	exit;
}else{
	//echo "suggestion";
	header("Content-type: image/gif");
	echo file_get_contents("images/suggestion.gif");
	exit;
}
?>