<?php
/***
used in admin to get the form elements to edit deal data for different deal types
********/
require_once("../../include/global.php");

require_once("classes/class.account.php");
if(!$g_account->is_admin_logged()){
	echo "admin not logged";
	return;
}

require_once("classes/class.transaction.php");
require_once("classes/class.deal_support.php");
require_once("classes/class.country.php");
require_once("classes/class.company.php");

$g_deal_support = new deal_support();

$deal_cat_name = $_GET['deal_cat_name'];
$deal_subcat1_name = $_GET['deal_subcat1_name'];
$deal_subcat2_name = $_GET['deal_subcat2_name'];
$deal_id = $_GET['deal_id'];

if(strtolower($deal_cat_name) == "m&a"){
	include("../deal_data_ma.php");
	return;
}
if((strtolower($deal_cat_name) == "debt")&&(strtolower($deal_subcat1_name) == "loan")){
	include("../deal_data_loan.php");
	return;
}
if((strtolower($deal_cat_name) == "debt")&&(strtolower($deal_subcat1_name) == "bond")){
	include("../deal_data_bond.php");
	return;
}
if((strtolower($deal_cat_name) == "equity")&&(strtolower($deal_subcat1_name) == "convertible")){
	include("../deal_data_convertible.php");
	return;
}
if((strtolower($deal_cat_name) == "equity")&&(strtolower($deal_subcat1_name) == "preferred")){
	include("../deal_data_preferred.php");
	return;
}


if((strtolower($deal_cat_name) == "equity")&&(strtolower($deal_subcat1_name) == "equity")&&(strtolower($deal_subcat2_name)=="additional")){
	include("../deal_data_eq_additional.php");
	return;
}
if((strtolower($deal_cat_name) == "equity")&&(strtolower($deal_subcat1_name) == "equity")&&(strtolower($deal_subcat2_name)=="ipo")){
	include("../deal_data_eq_ipo.php");
	return;
}
if((strtolower($deal_cat_name) == "equity")&&(strtolower($deal_subcat1_name) == "equity")&&(strtolower($deal_subcat2_name)=="rights issue")){
	include("../deal_data_eq_rights.php");
	return;
}
?>