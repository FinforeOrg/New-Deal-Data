<?php
/******************
given a company_id and the field, it returns an image that suggest whether there
is any suggestions or note
******************************/
require_once("../include/global.php");

require_once("classes/class.account.php");
if(!$g_account->is_admin_logged()){
	header("Content-type: image/png");
	echo file_get_contents("images/light-bulb-off.png");
	exit;
}

require_once("classes/class.company.php");

$company_id = $_GET['company_id'];
$company_field = $_GET['data_name'];

$found = $g_company->admin_has_data_correction_on_company($company_id,$company_field);
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