<?php
/******************
sng:15/feb/2012
Handles simple deal suggestion request.

unlike detailed submission this form will not change based on deal type selected. Hence we
can write the processing code in a single file.

Problem is, for priviledged members, we need to create the deal directly and that SQL is different. Of course, we
can offload it to appropriate include files.
*********************/
require_once("../../include/global.php");
require_once("classes/class.member.php");
require_once("classes/class.company.php");

$result = array();
$result['status'] = 0;
$result['msg'] = "";

//print_r($_POST);
/**********************************
validation
at least deal_type has to be specified
***********************************/
if(!isset($_POST['deal_cat_name'])||($_POST['deal_cat_name']=="")){
	$result['status'] = 0;
	$result['msg'] = "One or more mandatory information was not specified";
	echo json_encode($result);
	exit;
}
/******************
date of deal
********************/
if($_POST['deal_date']==""){
	$result['status'] = 0;
	$result['msg'] = "One or more mandatory information was not specified";
	echo json_encode($result);
	exit;
}
/******************
either the exact value has to be specified or a range has to be specified, even if 'undisclosed'
*******************/
if($_POST['deal_value']==""){
	//check if range is specified or not
	if(!isset($_POST['value_range_id'])){
		$result['status'] = 0;
		$result['msg'] = "One or more mandatory information was not specified";
		echo json_encode($result);
		exit;
	}
}else{
	//deal value specified
}
/*******
even if no company is specified, the companies array is posted, with 3 blank elements
************/
$company_count = count($_POST['companies']);
$has_company = false;
for($company_i=0;$company_i<$company_count;$company_i++){
	if($_POST['companies'][$company_i]!=""){
		$has_company = true;
		//there is a company so break
		break;
	}
}
if(!$has_company){
	$result['status'] = 0;
	$result['msg'] = "One or more mandatory information was not specified";
	echo json_encode($result);
	exit;
}

/*********
even if no bank is specified, the banks array is posted with 3 blank elements
***********/
$bank_count = count($_POST['banks']);
$has_bank = false;
for($bank_i=0;$bank_i<$bank_count;$bank_i++){
	if($_POST['banks'][$bank_i]!=""){
		$has_bank = true;
		//there is a bank so break
		break;
	}
}
if(!$has_bank){
	$result['status'] = 0;
	$result['msg'] = "One or more mandatory information was not specified";
	echo json_encode($result);
	exit;
}

$company_data = NULL;
$ok = $g_company->get_company($_SESSION['company_id'],$company_data);
if(!$ok){
	$result['status'] = 0;
	$result['msg'] = "Internal error";
	echo json_encode($result);
	exit;
}
$company_name = $company_data['name'];
$is_favoured = false;

$ok = $g_mem->is_work_email_favoured($_SESSION['member_type'],$_SESSION['work_email'],$company_name,$is_favoured);
if(!$ok){
	$result['status'] = 0;
	$result['msg'] = "Internal error";
	echo json_encode($result);
	exit;
}
/*****************************
sng:20/feb/2012
Now we have that for both privileged and non privileged members, a deal record is to be created.
This allows us to send the deal_id to others for viewing. We can leverage the display and edit code.

For deals created from simple submission of non privileged members, the active flag is not set. That way
those do not appear in deal listing
**********************************/
if(!$is_favoured){
	$activate_deal = false;
}else{
	$activate_deal = true;
}
require_once("deal_creation_from_simple_suggestion.php");

$result['status'] = 0;
$result['msg'] = "Internal error";
echo json_encode($result);
exit;

/************
in the suggestion, the dates are in 15/Feb/2012 format. The database is y-m-d format
See nifty_function.php for why we are using date_to_timestamp.
Basically it happens with UK date format
Dates in the m/d/y or d-m-y formats are disambiguated by looking at the separator between the various components: 
if the separator is a slash (/), then the American m/d/y is assumed; whereas if the separator is a dash (-) or a dot (.), 
then the European d-m-y format is assumed
***************/
function fotmat_date_for_suggestion($date){
	return date("Y-m-d",date_to_timestamp($date));
}
?>