<?php
/*********
this is used to download the make me top search result data
This can be accessed only by logged in members.
since this opens in a new window, we just check whether the user is logged in or not
************/
include("include/global.php");
require_once("classes/class.account.php");
////////////////////
if(!$g_account->is_site_member_logged()){
	?>
	<html>
	<head>
	<title>make me top search result data</title>
	</head>
	<body>
	<h1>You need to be logged in to download make me top search result data</h1>
	</body>
	</html>
	<?php
	exit;
}
/////////////////////////////////////////
require_once("classes/class.make_me_top.php");
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
/////////////////////////////////////////////////////
$g_view['search_result_firms'] = array();
$g_view['search_result_firms_count'] = 0;
$success = $g_maketop->get_search_result_firms($g_view['job_id'],$g_view['search_result_id'],$g_view['search_result_firms'],$g_view['search_result_firms_count']);
if(!$success){
	die("Cannot get the firms for the search result");
}
/////////////////////////////////////
require_once("classes/class.simple_excel_writer.php");
$filename = "make_me_top_search_result_".date("Y-m-d").".xls";
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
$caption = "deal-data.com make me top search result data downloaded on ".date("M d Y");
$g_exl_writer->xlsWriteLabel(0,0,$caption);

$caption = "Your firm is #".$g_view['result_data']['rank_of_firm']." based on the following parameters";
$g_exl_writer->xlsWriteLabel(1,0,$caption);
//store the criteria
$row = 2;
$g_exl_writer->xlsWriteLabel($row,0,"Country");
$g_exl_writer->xlsWriteLabel($row,1,$g_view['result_data']['country_name']);
$row++;

$g_exl_writer->xlsWriteLabel($row,0,"Sector/Industry");
$g_exl_writer->xlsWriteLabel($row,1,$g_view['result_data']['sector_name']);
$row++;

$g_exl_writer->xlsWriteLabel($row,0,"Deal Type");
$g_exl_writer->xlsWriteLabel($row,1,$g_view['result_data']['deal_name']);
$row++;

$g_exl_writer->xlsWriteLabel($row,0,"Size");
$g_exl_writer->xlsWriteLabel($row,1,$g_view['result_data']['size_name']);
$row++;

$g_exl_writer->xlsWriteLabel($row,0,"Date");
$g_exl_writer->xlsWriteLabel($row,1,$g_view['result_data']['date_name']);
$row++;

$g_exl_writer->xlsWriteLabel($row,0,"Ranking Criteria");
if($g_view['result_data']['ranking_criteria']=="num_deals") $ranking = "Total number of tombstones";
if($g_view['result_data']['ranking_criteria']=="total_deal_value") $ranking = "Total tombstone value";
if($g_view['result_data']['ranking_criteria']=="total_adjusted_deal_value") $ranking = "Total adjusted value";
$g_exl_writer->xlsWriteLabel($row,1,$ranking);
$row++;

//the header
$g_exl_writer->xlsWriteLabel($row,0,"Rank");
$g_exl_writer->xlsWriteLabel($row,1,"Firm");
if($g_view['result_data']['ranking_criteria']!="num_deals"){
	$ranking.=" (in million $)";
}
$g_exl_writer->xlsWriteLabel($row,2,$ranking);
$row++;
//////////////////////////////////////////
for($i=0;$i<$g_view['search_result_firms_count'];$i++){
	$g_exl_writer->xlsWriteNumber($row,0,$i+1);
	$g_exl_writer->xlsWriteLabel($row,1,$g_view['search_result_firms'][$i]['firm_name']);
	if($g_view['result_data']['ranking_criteria']!="num_deals"){
		$data = convert_billion_to_million_for_display_round_as_num($g_view['search_result_firms'][$i]['stat_value']);
	}else{
		//number of deals, just show the number
		$data = $g_view['search_result_firms'][$i]['stat_value'];
	}
	$g_exl_writer->xlsWriteNumber($row,2,$data);
	$row++;
}

// close the stream
$g_exl_writer->xlsEOF(); 
?>