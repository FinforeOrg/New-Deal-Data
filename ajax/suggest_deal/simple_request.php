<?php
/******************
sng:15/feb/2012
Handles simple deal suggestion request.

unlike detailed submission this form will not change based on deal type selected. Hence we
can write the processing code in a single file.

Problem is, for priviledged members, we need to create the deal directly and that SQL is different. Of course, we
can offload it to appropriate include files.
(This is not a concern now. A deal record is created for both privileged/non-privileged members
*********************/
require_once("../../include/global.php");
require_once("classes/class.member.php");
require_once("classes/class.company.php");

$result = array();
$result['status'] = 0;
$result['msg'] = "";
/**************
sng:27/aug/2012
Now we need better error reporting
validation will done by the transaction::front_create_deal_from_simple_suggestion which will populate the err
**************/
$result['err'] = array();
//print_r($_POST);

/*************************
sng:29/aug/2012
Let us use the member::is_member_favoured instead
*****************************/
$is_favoured = false;
$ok = $g_mem->is_member_favoured($_SESSION['mem_id'],$is_favoured);

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
/*********************
sng:29/aug/2012
It has been decided that deal posted by any member will be treated as active deal. In case someone posts bogus data, those
deals will be flagged.
For minimum disruption, we just ignore the deal_active parameter for now
***********************/
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