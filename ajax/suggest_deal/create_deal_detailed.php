<?php
/********************
sng:9/mar/2012
called inside the doTakeSubmit() of request.php to handle
the case of detailed deal submission and creating a deal from that.

You can use

$result = array();
$result['status'] = 0;
myJson($result);
return;
************************/

/************
in the suggestion, the dates are in m/d/y format. The database is y-m-d format
***************/
function fotmat_date_for_suggestion($date){
	return date("Y-m-d",strtotime($date));
}

/*******************************
We must validate it here again. Just simple flag
at least 
deal_type
date of deal
value: the exact value has to be specified in detailed submission
companies: at least one
thing is, even if no company is specified, the companies array is posted, with blank elements
ditto for banks
*****************************/
$result = array();
if(!isset($_POST['deal_cat_name'])||($_POST['deal_cat_name']=="")){
	$result['status'] = 0;
	$result['msg'] = "One or more mandatory information was not specified";
	myJson($result);
	return;
}

/**************************
for detailed submission, there is no date of deal. There are only
announced_date
closed_date
If closed_date is specified, that is taken as deal_date else announced_date is taken as deal_date
so at least one must be specified

exrights_date
rumour_date
These two are only for display.
**********************************/
if(!isset($_POST['closed_date'])||($_POST['closed_date']=="")){
	if(!isset($_POST['announced_date'])||($_POST['announced_date']=="")){
		$result['status'] = 0;
		$result['msg'] = "One or more mandatory information was not specified";
		myJson($result);
		return;
	}
}

/**********************************
In detailed submission form, we specify the exact value
deal_size
implied_deal_size (for M&A)
**********************************/
if(!isset($_POST['deal_size'])||($_POST['deal_size']=="")){
	if(!isset($_POST['implied_deal_size'])||($_POST['implied_deal_size']=="")){
		$result['status'] = 0;
		$result['msg'] = "One or more mandatory information was not specified";
		myJson($result);
		return;
	}
}

/*******************************/
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
	myJson($result);
	return;
}
/************************************************/
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
	myJson($result);
	return;
}
/************************************************/
require_once("classes/class.member.php");
require_once("classes/class.deal_support.php");
$mem = new member();
$deal_support = new deal_support();


$suggestion_mem_id = $_SESSION['mem_id'];
$date_time_now = date("Y-m-d H:i:s");

//just a dummy
$q = "insert into ".TP."transaction set company_id='0'";

/****************
currency and deals value
deal value in USD million OR implied deal size in USD [M&A]
local currency for the deal
local currency rate
deal value in local currency million OR implied deal size in local currency [M&A]
*******************/

if(isset($_POST['deal_size'])&&($_POST['deal_size']!="")){
	$deal_value = (float)$_POST['deal_size'];
}else{
	if(isset($_POST['implied_deal_size'])&&($_POST['implied_deal_size']!="")){
		$deal_value = (float)$_POST['implied_deal_size'];
	}else{
		$deal_value = 0.0;
	}
}
if($deal_value < 0.0){
	$deal_value = 0.0;
}
$q.=",value_in_billion='".($deal_value/1000)."'";
/****************
we need the deal value range id 
****************/
$value_range_id = 0;
$ok = $deal_support->front_get_value_range_id_from_value($deal_value,$value_range_id);
if(!$ok){
	$result['status'] = 0;
	$result['msg'] = "Internal error, cannot get deal value range id";
	myJson($result);
	return;
}
$q.=",value_range_id='".$value_range_id."'";

if(isset($_POST['local_currency'])&&($_POST['local_currency']!="")){
	$q.=",currency='".mysql_real_escape_string($_POST['local_currency'])."'";
}

if(isset($_POST['local_currency_rate'])&&($_POST['local_currency_rate']!="")){
	$q.=",exchange_rate='".(float)$_POST['local_currency_rate']."'";
}

if(isset($_POST['deal_value'])&&($_POST['deal_value']!="")){
	$q.=",value_in_billion_local_currency='".((float)($_POST['deal_value'])/1000)."'";
}else{
	if(isset($_POST['implied_deal_size_local'])&&($_POST['implied_deal_size_local']!="")){
		$q.=",value_in_billion_local_currency='".((float)($_POST['implied_deal_size_local'])/1000)."'";
	}
}
/************
In the suggestion, the date is sent as m/d/y, we need it as y-m-d
*************/
if(isset($_POST['closed_date'])&&($_POST['closed_date']!="")){
	$date_of_deal = fotmat_date_for_suggestion($_POST['closed_date']);
}else{
	/****
	we already checked that both cannot be blank
	****/
	$date_of_deal = fotmat_date_for_suggestion($_POST['announced_date']);
}
$q.=",date_of_deal='".$date_of_deal."'";

$q.=",deal_cat_name='".$_POST['deal_cat_name']."'";
if(isset($_POST['deal_subcat1_name'])&&($_POST['deal_subcat1_name']!="")){
	$q.=",deal_subcat1_name='".$_POST['deal_subcat1_name']."'";
}else{
	$q.=",deal_subcat1_name='n/a'";
}
if(isset($_POST['deal_subcat2_name'])&&($_POST['deal_subcat2_name']!="")){
	$q.=",deal_subcat2_name='".$_POST['deal_subcat2_name']."'";
}else{
	$q.=",deal_subcat2_name='n/a'";
}

if(isset($_POST['email_participants'])&&($_POST['email_participants']=="on")){
	$q.=",email_participating_syndicates='y'";
}else{
	$q.=",email_participating_syndicates='n'";
}
$q.=",added_by_mem_id='".$suggestion_mem_id."'";
$q.=",added_on='".$date_time_now."'";
$q.=",last_edited='".$date_time_now."'";
/**************************************************
deals created from front end are not marked as 'admin verified' as admin is yet to see this
******************************/
$q.=",admin_verified='n'";
/*************************************
check out if the member is favoured or not. If favoured, the deal will be active else inactive
**************/
$member_favoured = false;
$ok = $mem->is_member_favoured($suggestion_mem_id,$member_favoured);
if(!$ok){
	$result['status'] = 0;
	$result['msg'] = "Cannot determine if the member is privileged or not";
	myJson($result);
	return;
}
if($member_favoured){
	$is_active = 'y';
}else{
	$is_active = 'n';
}
$q.=",is_active='".$is_active."'";

$db = new db();

$ok = $db->mod_query($q);
if(!$ok){
	$result['status'] = 0;
	$result['msg'] = $db->error();
	//$result['msg'] = "Error creating deal";
	myJson($result);
	return;
}
/**********************************************
deal data added, now add the extra data
****************/
$new_transaction_id = $db->last_insert_id();

require_once("classes/class.transaction.php");
$trans = new transaction();

/***********************************
ok, we have put the common data in the transaction table. However, there are some deal specific data that goes in
transaction table. To complicate the issue, some goes in transaction_extra table.

What we do here is call deal specific files to create the query to update the transaction table and at the same time
create query to update the transaction_extra table.
***************************************/
$update_transaction_q = "";
$transaction_extra_q = "";

if(strtolower($_POST['deal_cat_name']) == "m&a"){
	include("suggestion_ma.php");
}elseif(strtolower($_POST['deal_cat_name'])=="debt"){
	include("suggestion_debt.php");
}elseif(strtolower($_POST['deal_cat_name'])=="equity"&&strtolower($_POST['deal_subcat1_name'])=="convertible"){
	include("suggestion_eq_convertible.php");
}elseif(strtolower($_POST['deal_cat_name'])=="equity"&&strtolower($_POST['deal_subcat1_name'])=="preferred"){
	include("suggestion_eq_preferred.php");
}elseif(strtolower($_POST['deal_cat_name'])=="equity"&&strtolower($_POST['deal_subcat1_name'])=="equity"&&strtolower($_POST['deal_subcat2_name'])=="additional"){
	include("suggestion_eq_additional.php");
}elseif(strtolower($_POST['deal_cat_name'])=="equity"&&strtolower($_POST['deal_subcat1_name'])=="equity"&&strtolower($_POST['deal_subcat2_name'])=="ipo"){
	include("suggestion_eq_ipo.php");
}elseif(strtolower($_POST['deal_cat_name'])=="equity"&&strtolower($_POST['deal_subcat1_name'])=="equity"&&strtolower($_POST['deal_subcat2_name'])=="rights issue"){
	include("suggestion_eq_rights.php");
}else{
	
}
//remove the first ','
$update_transaction_q = substr($update_transaction_q,1);
if($update_transaction_q!=""){
	$update_transaction_q = "update ".TP."transaction set ".$update_transaction_q." where id='".$new_transaction_id."'";
	$db->mod_query($update_transaction_q);
	//never mind if error
}

/********************
now update the extra table
*******************/
//remove the first ','
$transaction_extra_q = substr($transaction_extra_q,1);
if($transaction_extra_q!=""){
	$transaction_extra_q = "insert into ".TP."transaction_extra_detail set transaction_id='".$new_transaction_id."',".$transaction_extra_q;
	$db->mod_query($transaction_extra_q);
	//never mind if error
}

/**********************
regulatory links are converted to csv and stored in transaction_sources
even if no regulatory link is specified, the regulatory_links array is posted with blank elements

sng:2/may/2012
Now we no longer convert source urls to csv. We store each in its own row
***********/
require_once("classes/class.transaction_source.php");
$trans_source = new transaction_source();
$ok = $trans_source->front_set_sources_for_deal($new_transaction_id,$_POST['regulatory_links'],$suggestion_mem_id,$date_time_now);


/******************************
sng:13/mar/2012
Now we only have a single 'note' box in the detailed submission which is
used to capture different info for different kinds of deal. We just change the
label of the textbox.

sng:30/apr/2012
We now need to store the original submission of note in suggestion. For that we
need the mem who suggested the deal and the date added on
*************************/
$trans->update_note($new_transaction_id,$suggestion_mem_id,$date_time_now,$_POST['additional_deal_details_note']);

/****************
companies
Even if no companies are specified, array is returned with blank items

sng:18/apr/2012
We add two more arguments to support the provision that we need to log the addition of companies
****************/
require_once("classes/class.transaction_company.php");
$trans_com = new transaction_company();
$ok = $trans_com->front_set_participants_for_deal($new_transaction_id,$_POST,$suggestion_mem_id,$date_time_now);

/**********
banks and law firms

sng:10/apr/2012
We add two more arguments to support the provision that we need to log the addition of banks and law firms
*******/
$ok = $trans->front_set_partners_for_deal($new_transaction_id,$_POST,$suggestion_mem_id,$date_time_now);
/***********************************************************
Files
The files are loaded using ajax before the user submit the form.
The handler ajax/fileuploader.php store the filenames in table and store the
ids in session.
We check for the session variable and update the records
Since suggestion from both privileged/non-privileged members are stored as a deal record
and not as suggestion record, we update the transaction_id field.
**************************/
if(isset($_SESSION['suggestion_files_id'])){
	$id_csv = "";
	foreach($_SESSION['suggestion_files_id'] as $file_id){
		$id_csv.= ",'".$file_id."'";
	}
	$id_csv = substr($id_csv,1);
	$suggestion_file_q = "update ".TP."transaction_files SET transaction_id='".$new_transaction_id."' where file_id IN(".$id_csv.")";
	$db->mod_query($suggestion_file_q);
	//never mind if this is not a success, remove the ids from session
	unset($_SESSION['suggestion_files_id']);
}

/**********************
We need to notify the members whose email has been specified. This can be blank.
The emails are separated by ','
There is also that checkbox 'Do not attribute email notification to my account' - not_mine
**************************/

if($_POST['notification_email_list']!=""){
	$email_data = array();
	if(isset($_POST['not_mine'])){
		$email_data['use_poster_email'] = false;
	}else{
		$email_data['use_poster_email'] = true;
	}
	//we need the member data
	require_once("classes/class.member.php");
	$member = new member();
	$mem_data = NULL;
	$ok = $member->front_get_profile_data($suggestion_mem_id,$mem_data);
	if($ok){
		$email_data['company_name'] = $mem_data['company_name'];
		$email_data['work_email'] = $_SESSION['work_email'];
		$email_data['member_type'] = $_SESSION['member_type'];
		$email_data['deal_link'] = $g_http_path."/deal_detail.php?deal_id=".$new_transaction_id;
		//deal details
		$email_data['deal_type']=$_POST['deal_cat_name'];
		if(isset($_POST['deal_subcat1_name'])&&($_POST['deal_subcat1_name']!="")){
			$email_data['deal_type'].=", ".$_POST['deal_subcat1_name'];
		}
		if(isset($_POST['deal_subcat2_name'])&&($_POST['deal_subcat2_name']!="")){
			$email_data['deal_type'].=", ".$_POST['deal_subcat2_name'];
		}
		$email_data['deal_value'] = deal_value_for_display_round_for_deal_id($new_transaction_id);
		$email_data['companies'] = implode(", ",$_POST['companies']);
		$email_data['banks'] = implode(", ",$_POST['banks']);
		$email_data['law_firms'] = implode(", ",$_POST['law_firms']);
		
		require_once("classes/class.mailer.php");
		$mailer = new mailer();
		$subject = "New Deal Notification";
		$email_msg = $mailer->mail_from_template("emailTemplates/simple_deal_creation_user_notification.php",$email_data);
		
		$emails = explode(",",$_POST['notification_email_list']);
		$mail_cnt = count($emails);
		for($i=0;$i<$mail_cnt;$i++){
			$to = trim($emails[$i]);
			/***********
			sng:1/mar/2012
			clear the prev TO
			************/
			$mailer->clear_recipients();
			//now send
			$mailer->html_mail($to,$subject,$email_msg);
			
			//noting to do if error
		}
	}
	//no action if not found
}
/*************************************************************************
Now notify the syndicates. Hold
****************************/


$result['status'] = 1;
myJson($result);
return;

?>