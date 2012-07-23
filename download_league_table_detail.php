<?php
/*********
this is used to download the stat details for league table, based on filter condition.
This can be accessed pnly by logged in members.
since this opens in a new window, we just check whether the user is logged in or not
************/
include("include/global.php");
require_once("classes/class.account.php");
////////////////////
if(!$g_account->is_site_member_logged()){
	?>
	<html>
	<head>
	<title>league table data</title>
	</head>
	<body>
	<h1>You need to be logged in to download league table data</h1>
	</body>
	</html>
	<?php
	exit;
}
/////////////////////////////////////////
/************
sng:23/jul/2012
We cannot send conditions like >=23. The sanitizer will erase it. We base64_encode it in the forms and decode it here
*****************/
if(isset($_POST['deal_size'])){
	$_POST['deal_size'] = base64_decode($_POST['deal_size']);
}
require_once("classes/class.statistics.php");
$g_view['start_offset'] = 0;
$g_view['num_to_download'] = 100;
$g_view['data'] = array();
$g_view['data_count'] = 0;
//get the data
$success = $g_stat->front_generate_league_table_for_firms_paged($_POST,$g_view['start_offset'],$g_view['num_to_download'],$g_view['data'],$g_view['data_count']);
if(!$success){
	die("Cannot generate league table data");
}
/////////////////////////////////////
require_once("classes/class.simple_excel_writer.php");
$filename = "league_table_".date("Y-m-d").".xls";
// set headers
header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: public");
header("Content-Description: File Transfer");
header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=".$filename);
header("Content-Transfer-Encoding: binary");
////////////////////////////
// begin Excel stream
$g_exl_writer->xlsBOF();
//the caption
$caption = "deal-data.com league table data downloaded on ".date("M d Y");
$g_exl_writer->xlsWriteLabel(0,0,$caption);
//store the criteria
$row = 1;
$g_exl_writer->xlsWriteLabel($row,0,"For");
$g_exl_writer->xlsWriteLabel($row,1,$_POST['partner_type']);
$row++;

if($_POST['region'] != ""){
	$g_exl_writer->xlsWriteLabel($row,0,"Region");
	$g_exl_writer->xlsWriteLabel($row,1,$_POST['region']);
	$row++;
}

if($_POST['country'] != ""){
	$g_exl_writer->xlsWriteLabel($row,0,"Country");
	$g_exl_writer->xlsWriteLabel($row,1,$_POST['country']);
	$row++;
}

if($_POST['deal_cat_name'] != ""){
	$g_exl_writer->xlsWriteLabel($row,0,"Deal Category");
	$g_exl_writer->xlsWriteLabel($row,1,$_POST['deal_cat_name']);
	$row++;
}

if($_POST['deal_subcat1_name'] != ""){
	$g_exl_writer->xlsWriteLabel($row,0,"Deal Sub-category");
	$g_exl_writer->xlsWriteLabel($row,1,$_POST['deal_subcat1_name']);
	$row++;
}

if($_POST['deal_subcat2_name'] != ""){
	$g_exl_writer->xlsWriteLabel($row,0,"Deal Sub sub-category");
	$g_exl_writer->xlsWriteLabel($row,1,$_POST['deal_subcat2_name']);
	$row++;
}

if($_POST['sector'] != ""){
	$g_exl_writer->xlsWriteLabel($row,0,"Sector");
	$g_exl_writer->xlsWriteLabel($row,1,$_POST['sector']);
	$row++;
}

if($_POST['industry'] != ""){
	$g_exl_writer->xlsWriteLabel($row,0,"Industry");
	$g_exl_writer->xlsWriteLabel($row,1,$_POST['industry']);
	$row++;
}

if($_POST['year'] != ""){
	$g_exl_writer->xlsWriteLabel($row,0,"Year");
	$g_exl_writer->xlsWriteLabel($row,1,$_POST['year']);
	$row++;
}

/***
sng:23/july/2010
We have added another filter, deal size. The values are like
>=deal value in billion, <=deal value in billion
***/
if($_POST['deal_size']!=""){
	$g_exl_writer->xlsWriteLabel($row,0,"Deal size");
	$g_exl_writer->xlsWriteLabel($row,1,$_POST['deal_size']." billion");
	$row++;
}

if($_POST['ranking_criteria']=="num_deals") $ranking = "Number of deals";
if($_POST['ranking_criteria']=="total_deal_value") $ranking = "Total deal value";
if($_POST['ranking_criteria']=="total_adjusted_deal_value") $ranking = "Total adjusted deal value";

$g_exl_writer->xlsWriteLabel($row,0,"Ranking based on");
$g_exl_writer->xlsWriteLabel($row,1,$ranking);
$row++;
//the header
$g_exl_writer->xlsWriteLabel($row,0,"Rank");
$g_exl_writer->xlsWriteLabel($row,1,"Firm");
$g_exl_writer->xlsWriteLabel($row,2,"Number of deals");
$g_exl_writer->xlsWriteLabel($row,3,"Total deal value (in $ billion)");
$g_exl_writer->xlsWriteLabel($row,4,"Total adjusted deal value (in $ billion)");
$row++;
//////////////////////////////////////////
for($i=0;$i<$g_view['data_count'];$i++){
	$g_exl_writer->xlsWriteNumber($row,0,$i+1);
	$g_exl_writer->xlsWriteLabel($row,1,$g_view['data'][$i]['firm_name']);
	$g_exl_writer->xlsWriteNumber($row,2,$g_view['data'][$i]['num_deals']);
	$g_exl_writer->xlsWriteNumber($row,3,number_format($g_view['data'][$i]['total_deal_value'],2));
	$g_exl_writer->xlsWriteNumber($row,4,number_format($g_view['data'][$i]['total_adjusted_deal_value'],2));
	$row++;
}

// close the stream
$g_exl_writer->xlsEOF(); 
?>