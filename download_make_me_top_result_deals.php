<?php
/*********
this is used to download the deals behind make me top search result
Since there may be 1000s of matched deals, we download only the biggest 25 deals
This can be accessed only by logged in members.
since this opens in a new window, we just check whether the user is logged in or not

sng:04/oct/2010
We are using a different library to write to excel. That allows us to store date
************/
include("include/global.php");
ini_set("include_path",ini_get("include_path").":/var/www/home/lib/php_writeexcel_0_3_0");
require_once("classes/class.account.php");
////////////////////
if(!$g_account->is_site_member_logged()){
	?>
	<html>
	<head>
	<title>make me top search result deals</title>
	</head>
	<body>
	<h1>You need to be logged in to download make me top search result deals</h1>
	</body>
	</html>
	<?php
	exit;
}
/////////////////////////////////////////
require_once("classes/class.make_me_top.php");
require_once("classes/class.transaction.php");
require_once("nifty_functions.php");
$g_view['job_id'] = $_POST['job_id'];
$g_view['search_result_id'] = $_POST['search_result_id'];
/////////////////////////////////////////
$g_view['result_data'] = NULL;
$g_view['result_found'] = false;
$success = $g_maketop->search_result($g_view['search_result_id'],$g_view['result_data'],$g_view['result_found']);
if(!$success){
	die("Cannot get the result");
}
////////////////////////////////
if($_POST['number_of_deals']==""){
	$g_view['num_to_download'] = 100;
}else{
	$g_view['num_to_download'] = $_POST['number_of_deals'];
}
$g_view['data'] = array();
$g_view['data_count'] = 0;
//get the data
$success = $g_trans->get_make_me_top_search_result_deals($g_view['job_id'],$g_view['search_result_id'],$g_view['num_to_download'],$g_view['data'],$g_view['data_count']);
if(!$success){
	die("Cannot get deal data");
}
/////////////////////////////////////////////////////
require_once("class.writeexcel_workbook.inc.php");
require_once("class.writeexcel_worksheet.inc.php");

$filename = "make_me_top_search_result_deals_".date("Y-m-d").".xls";
$tmp_filename = date("Y-m-d")."_".time().".xls";
$work_file_name = FILE_PATH."temp_files/".$tmp_filename;
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
$caption = "deal-data.com make me top search result deal data downloaded on ".date("M d Y");
$worksheet->write(0,0,$caption);
$caption = "Your firm is #".$g_view['result_data']['rank_of_firm']." based on the following parameters";
$worksheet->write(1,0,$caption);

//store the criteria
$row = 2;
$worksheet->write($row,0,"Country");
$worksheet->write($row,1,$g_view['result_data']['country_name']);
$row++;

$worksheet->write($row,0,"Sector/Industry");
$worksheet->write($row,1,$g_view['result_data']['sector_name']);
$row++;

$worksheet->write($row,0,"Deal Type");
$worksheet->write($row,1,$g_view['result_data']['deal_name']);
$row++;

$worksheet->write($row,0,"Size");
$worksheet->write($row,1,$g_view['result_data']['size_name']);
$row++;

$worksheet->write($row,0,"Date");
$worksheet->write($row,1,$g_view['result_data']['date_name']);
$row++;

$worksheet->write($row,0,"Ranking Criteria");
if($g_view['result_data']['ranking_criteria']=="num_deals") $ranking = "Total number of tombstones";
if($g_view['result_data']['ranking_criteria']=="total_deal_value") $ranking = "Total tombstone value";
if($g_view['result_data']['ranking_criteria']=="total_adjusted_deal_value") $ranking = "Total adjusted value";
$worksheet->write($row,1,$ranking);
$row++;
//the header
$worksheet->write($row,0,"#");
$worksheet->write($row,1,"Company");
$worksheet->write($row,2,"Country");
$worksheet->write($row,3,"Sector");
$worksheet->write($row,4,"Industry");

$worksheet->write($row,5,"Date");
$worksheet->write($row,6,"Type");
$worksheet->write($row,7,"Value (in million USD)");
/****
sng:1/oct/2010
We need to show how many banks and law firms
***/
$worksheet->write($row,8,"No. of Bank(s)");
$worksheet->write($row,9,"Bank(s)");
$worksheet->write($row,10,"No. of Law Firm(s)");
$worksheet->write($row,11,"Law Firm(s)");
$row++;
//////////////////////////////////////////
for($j=0;$j<$g_view['data_count'];$j++){
	$worksheet->write($row,0,$j+1,$f_int);
	$worksheet->write($row,1,$g_view['data'][$j]['company_name']);
	
	$worksheet->write($row,2,$g_view['data'][$j]['hq_country']);
	$worksheet->write($row,3,$g_view['data'][$j]['sector']);
	$worksheet->write($row,4,$g_view['data'][$j]['industry']);
	
	$date_data = $g_view['data'][$j]['date_of_deal'];
	$worksheet->write($row, 5,  (float)(getDays1900($date_data)+2),$f_date);
	$deal_type = $g_view['data'][$j]['deal_cat_name'];
	if(($g_view['data'][$j]['deal_cat_name']=="M&A")&&($g_view['data'][$j]['target_company_name']!="")){
		/************************************************
		sng:28/july/2010
		check if the subtype is Completed or not
		**********/
		if(strtolower($g_view['data'][$j]['deal_subcat1_name'])=="completed"){
			$deal_type.=". Acquisition of ".$g_view['data'][$j]['target_company_name'];
		}else{
			$deal_type.=". Proposed acquisition of ".$g_view['data'][$j]['target_company_name'];
		}
		/******************************************/
		//$deal_type.=". Acquisition of ".$g_view['data'][$j]['target_company_name'];
	}
	$worksheet->write($row,6,$deal_type);
	
	/***
	sng:23/july/2010
	We need the value as number, not text. But sometime, the value is not there and we need to send a text. Let us send this
	directly.
	*******/
	if($g_view['data'][$j]['value_in_billion']==0){
		$worksheet->write($row,7,"Not disclosed");
	}else{
		/***
		sng:23/july/2010
		We need the value as number, not text
		$deal_value = convert_billion_to_million_for_display_round($g_view['data'][$j]['value_in_billion']);
		*******/
		$worksheet->write($row,7,convert_billion_to_million_for_display_round_as_num($g_view['data'][$j]['value_in_billion']),$f_float);
	}
	
	
	$banks_csv = "";
	$bank_cnt = count($g_view['data'][$j]['banks']);
	for($banks_i=0;$banks_i<$bank_cnt;$banks_i++){
		$banks_csv.=", ".$g_view['data'][$j]['banks'][$banks_i]['name'];
	}
	$banks_csv = substr($banks_csv,1);
	if($bank_cnt > 0){
		$worksheet->write($row,8,$bank_cnt,$f_int);
	}else{
		//no banks
		$worksheet->write($row,8,"n/a");
	}
	$worksheet->write($row,9,$banks_csv);
	
	$law_csv = "";
	$law_cnt = count($g_view['data'][$j]['law_firms']);
	for($law_i=0;$law_i<$law_cnt;$law_i++){
		$law_csv.=", ".$g_view['data'][$j]['law_firms'][$law_i]['name'];
	}
	$law_csv = substr($law_csv,1);
	if($law_cnt > 0){
		$worksheet->write($row,10,$law_cnt,$f_int);
	}else{
		//no law firm
		$worksheet->write($row,10,"n/a");
	}
	$worksheet->write($row,11,$law_csv);
	
	$row++;
}
////////////////////////////////////////////////
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
////////////////////////////
?>