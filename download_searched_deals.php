<?php
/*********
this is used to download the deals from deal search page, based on filter condition.
This can be accessed pnly by logged in members.
since this opens in a new window, we just check whether the user is logged in or not

sng:30/sep/2010
We are using a different library to write to excel. That allows us to store date
************/
include("include/global.php");
ini_set("include_path",ini_get("include_path").":/var/www/lib/php_writeexcel_0_3_0");
//ini_set("include_path",ini_get("include_path").";E:/xampp/htdocs/deal-data/lib/php_writeexcel_0_3_0");

require_once("classes/class.account.php");
require_once("nifty_functions.php");
////////////////////
/****************
sng:10/nov/2011
We allow to download
****************/
/*if(!$g_account->is_site_member_logged()){
	?>
	<html>
	<head>
	<title>deal data</title>
	</head>
	<body>
	<h1>You need to be logged in to download deal data</h1>
	</body>
	</html>
	<?php
	exit;
}*/
////////////////////////////////////////
require_once("classes/class.magic_quote.php");
require_once("classes/class.transaction.php");
/**********************
sng:2/nov/2011
We cannot send data like >= in POST. The sanitiser will erase it.
So we base64 encoded in deal_search_filter_form_view.php
and we decode it here again
************************/
$_POST['deal_size'] = base64_decode($_POST['deal_size']);

$g_view['start_offset'] = 0;
/*****
if number_of_deals is blank, it means, show all. In that case we get the 100 deals as shown in the search listing
else to extract how many deals to get, since transaction::front_deal_search_page does not do its own limiting. It get
whatever number of records we want

see also the logic in deal_search_view,php

sng:31/oct/2011
We have another dummy value for number_of_deals called 'size'. Watch out for that

sng:7/nov/2011
if num of deals is blank then allow to download 100 largest deals that satisfies the other filter.
Since the size filter and number_of_deals are independent in transaction::front_deal_search_paged,
we can simulate by using $_POST['number_of_deals'] = "top:100"
the 'top part triggesrs 'order by value_in_billion desc'
********/
$g_view['start_offset'] = 0;
if(($_POST['number_of_deals']=="")||($_POST['number_of_deals']=="size")){
	$g_view['num_to_download'] = 100;
	$_POST['number_of_deals'] = "top:100";
}else{
	$num_deals_tokens = explode(":",$_POST['number_of_deals']);
	$g_view['num_to_download'] = $num_deals_tokens[1];
}

$g_view['data'] = array();
$g_view['data_count'] = 0;
//get the data
$success = $g_trans->front_deal_search_paged($_POST,$g_view['start_offset'],$g_view['num_to_download'],$g_view['data'],$g_view['data_count']);
if(!$success){
	die("Cannot get deal data");
}
/////////////////////////////////////
require_once("class.writeexcel_workbook.inc.php");
require_once("class.writeexcel_worksheet.inc.php");

$filename = "deal_data_".date("Y-m-d").".xls";
$tmp_filename = date("Y-m-d")."_".time().".xls";
$work_file_name = FILE_PATH."/temp_files/".$tmp_filename;
$fname = fopen($work_file_name,"x");
$workbook = &new writeexcel_workbook($fname);
$worksheet = &$workbook->addworksheet();
$f_date =& $workbook->addformat();
//date like 18 Feb 2010
$f_date->set_num_format("d mmm yyyy");

$f_int =& $workbook->addformat();
$f_int->set_num_format("0");

$f_float =& $workbook->addformat();
$f_float->set_num_format("0");
////////////////////////////////////////////////////////
//the caption
$caption = "data-cx.com deal data downloaded on ".date("M d Y");
$worksheet->write(0, 0,$caption);
//store the criteria
$row = 1;

if($_POST['region'] != ""){
	$worksheet->write($row,0,"Region");
	$worksheet->write($row,1,$_POST['region']);
	$row++;
}
if($_POST['country'] != ""){
	$worksheet->write($row,0,"Country");
	$worksheet->write($row,1,$_POST['country']);
	$row++;
}
if($_POST['deal_cat_name'] != ""){
	$worksheet->write($row,0,"Deal Category");
	$worksheet->write($row,1,$_POST['deal_cat_name']);
	$row++;
}
if($_POST['deal_subcat1_name'] != ""){
	$worksheet->write($row,0,"Deal Sub-category");
	$worksheet->write($row,1,$_POST['deal_subcat1_name']);
	$row++;
}
if($_POST['deal_subcat2_name'] != ""){
	$worksheet->write($row,0,"Deal Sub sub-category");
	$worksheet->write($row,1,$_POST['deal_subcat2_name']);
	$row++;
}
if($_POST['sector'] != ""){
	$worksheet->write($row,0,"Sector");
	$worksheet->write($row,1,$_POST['sector']);
	$row++;
}
if($_POST['industry'] != ""){
	$worksheet->write($row,0,"Industry");
	$worksheet->write($row,1,$_POST['industry']);
	$row++;
}
/**************
sng:2/nov/2011
We now use the clean function to beautify
*******************/
if($_POST['year'] != ""){
	$labels = array();
	Util::clean_deal_filter_date($_POST['year'],$labels);
	$worksheet->write($row,0,"Year");
	$worksheet->write($row,1,$labels[0]);
	$row++;
}
/******************************************
sng:27/oct/2011
We need to store the deal size also. The post data is in base64 encoded to slip past the sanitizer

sng:2/nov/2011
We have already decoded it, so we do not decode it again here
*******************************************/
if($_POST['deal_size'] != ""){
	$labels = array();
	Util::clean_deal_filter_size($_POST['deal_size'],$labels);
	$worksheet->write($row,0,"Deal size");
	$worksheet->write($row,1,$labels[0]);
	$row++;
}
/******************************
sng:27/jan/2011
Now we have deal value range id for size filter dropdown. We need proper label
***********************/
if($_POST['value_range_id']!=""){
	$worksheet->write($row,0,"Deal size");
	$worksheet->write($row,1,Util::get_label_for_deal_value_range_id($_POST['value_range_id']));
	$row++;
}
/****************
sng:31/oct/2011
We have another dummy value for number_of_deals called "size"
******************/
if(($_POST['number_of_deals'] != "")&&($_POST['number_of_deals']!="size")){
	$labels = array();
	Util::clean_deal_filter_num_deals($_POST['number_of_deals'],$labels);
	$worksheet->write($row,0,"Show");
	$worksheet->write($row,1,$labels[0]);
	$row++;
}
$row++;
//the header
/****
sng:23/july/2010
We need the country, sector, industry of the company doing the deal
*********/
$worksheet->write($row,0,"#");
$worksheet->write($row,1,"Participant");
$worksheet->write($row,2,"Country");
$worksheet->write($row,3,"Sector");
$worksheet->write($row,4,"Industry");

$worksheet->write($row,5,"Date");
$worksheet->write($row,6,"Type");
$worksheet->write($row,7,"Value (in million USD)");
/************
sng:5/mar/2012
We show whether this deal is approved by admin or not
*************/
$worksheet->write($row,8,"Verified");
/****
sng:1/oct/2010
We need to show how many banks and law firms
***/
$worksheet->write($row,9,"No. of Bank(s)");
$worksheet->write($row,10,"Bank(s)");

$worksheet->write($row,11,"No. of Law Firm(s)");
$worksheet->write($row,12,"Law Firm(s)");
$row++;
/////////////////////////////////////
//////////////////////////////////////////
for($j=0;$j<$g_view['data_count'];$j++){
	$worksheet->write($row,0,$j+1,$f_int);
	
	$worksheet->write($row,1,Util::deal_participants_to_csv($g_view['data'][$j]['participants']));
	
	$worksheet->write($row,2,$g_view['data'][$j]['hq_country']);
	$worksheet->write($row,3,$g_view['data'][$j]['sector']);
	$worksheet->write($row,4,$g_view['data'][$j]['industry']);
	
	$date_data = $g_view['data'][$j]['date_of_deal'];
	$worksheet->write($row, 5,  (float)(getDays1900($date_data)+2),$f_date);
	
	
	
	/***********************************
	sng:24/jan/2012
	show type/sub type/sub sub type for all
	************************************/
	$worksheet->write($row,6,get_deal_type_for_listing($g_view['data'][$j]['deal_cat_name'],$g_view['data'][$j]['deal_subcat1_name'],$g_view['data'][$j]['deal_subcat2_name']));
	
	
	/***
	sng:23/july/2010
	We need the value as number, not text. But sometime, the value is not there and we need to send a text. Let us send this
	directly.
	
	sng:23/jan/2012
	sometime we do not have the exact value but have a range id. In that case we show the range text.
	*******/
	if(($g_view['data'][$j]['value_in_billion']==0.0)&&($g_view['data'][$j]['value_range_id']==0)){
		$worksheet->write($row,7,"Not disclosed");
	}elseif($g_view['data'][$j]['value_in_billion'] > 0){
		/***
		sng:23/july/2010
		We need the value as number, not text
		$deal_value = convert_billion_to_million_for_display_round($g_view['data'][$j]['value_in_billion']);
		*******/
		$worksheet->write($row,7,convert_billion_to_million_for_display_round_as_num($g_view['data'][$j]['value_in_billion']),$f_float);
	}else{
		//only value range
		$worksheet->write($row,7,$g_view['data'][$j]['fuzzy_value']);
	}
	/************
	sng:5/mar/2012
	We show whether this deal is approved by admin or not
	*************/
	if($g_view['data'][$j]['admin_verified']=='y'){
		$worksheet->write($row,8,"Yes");
	}else{
		$worksheet->write($row,8,"No");
	}
	/****
	sng:1/oct/2010
	we need to show the number of banks / law firms
	sng; 4/oct/2010
	If there are no bank / law firm, show n/a instead of 0
	*****/
	$banks_csv = "";
	$bank_cnt = count($g_view['data'][$j]['banks']);
	for($banks_i=0;$banks_i<$bank_cnt;$banks_i++){
		$banks_csv.=", ".$g_view['data'][$j]['banks'][$banks_i]['name'];
	}
	$banks_csv = substr($banks_csv,1);
	if($bank_cnt > 0){
		$worksheet->write($row,9,$bank_cnt,$f_int);
	}else{
		//no banks
		$worksheet->write($row,9,"n/a");
	}
	$worksheet->write($row,10,$banks_csv);
	
	$law_csv = "";
	$law_cnt = count($g_view['data'][$j]['law_firms']);
	for($law_i=0;$law_i<$law_cnt;$law_i++){
		$law_csv.=", ".$g_view['data'][$j]['law_firms'][$law_i]['name'];
	}
	$law_csv = substr($law_csv,1);
	if($law_cnt > 0){
		$worksheet->write($row,11,$law_cnt,$f_int);
	}else{
		//no law firm
		$worksheet->write($row,11,"n/a");
	}
	$worksheet->write($row,12,$law_csv);
	
	$row++;
}
//////////////////////////////////////////////////////////////////
$workbook->close();
// set headers
header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: private",false);
header("Content-Description: File Transfer");
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=".$filename);
header("Content-Transfer-Encoding: binary");
header("Content-Length: ".filesize($work_file_name));
readfile($work_file_name);
unlink($work_file_name);
////////////////////////////
function getDays1900($date){
	$stop = unixtojd(strtotime($date));
	$start = gregoriantojd(1, 1, 1900);
	return ($stop - $start);
}
?>