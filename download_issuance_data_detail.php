<?php
/*********
this is used to download the stat details for issuance data, based on filter condition.
This can be accessed only by logged in members.
since this opens in a new window, we just check whether the user is logged in or not
************/
include("include/global.php");
require_once("classes/class.account.php");
require_once("classes/class.stat_help.php");
////////////////////
if(!$g_account->is_site_member_logged()){
	?>
	<html>
	<head>
	<title>league table data</title>
	</head>
	<body>
	<h1>You need to be logged in to download issuance data</h1>
	</body>
	</html>
	<?php
	exit;
}
/////////////////////////////////////////
require_once("classes/class.statistics.php");

$g_view['data'] = array();
$g_view['data_count'] = 0;
$g_view['max_value'] = 0;

//get the data
$success = $g_stat->generate_issuance_data($_POST,$g_view['data'],$g_view['max_value'],$g_view['data_count']);
if(!$success){
	die("Cannot generate issuance data");
}
/////////////////////////////////////
require_once("classes/class.simple_excel_writer.php");
$filename = "issuance_data_".date("Y-m-d").".xls";
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
$caption = "deal-data.com issuance data downloaded on ".date("M d Y");
$g_exl_writer->xlsWriteLabel(0,0,$caption);
//store the criteria
$row = 1;

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

/*********
sng: 27/11/2010
We have added grouping (quarterly, half yearly, yearly)
and start year quarter/half
***********/
if($_POST['month_division']!=""){
	$g_exl_writer->xlsWriteLabel($row,0,"Grouping");
	$show = "";
	if($_POST['month_division']=="q")$show = "Quarterly";
	if($_POST['month_division']=="h")$show = "Semi-Annual";
	if($_POST['month_division']=="y")$show = "Annual";
	$g_exl_writer->xlsWriteLabel($row,1,$show);
	$row++;
}
if($_POST['month_division_list']!=""){
	$g_exl_writer->xlsWriteLabel($row,0,"Start From");
	$g_exl_writer->xlsWriteLabel($row,1,$g_stat_h->convert_to_short_label($_POST['month_division_list'],$_POST['month_division']));
	$row++;
}
//the header
$g_exl_writer->xlsWriteLabel($row,0,"#");
$g_exl_writer->xlsWriteLabel($row,1,"year / Half / Quarter");
$g_exl_writer->xlsWriteLabel($row,2,"Total Issuance");
$row++;
//////////////////////////////////////////
for($i=0;$i<$g_view['data_count'];$i++){
	$g_exl_writer->xlsWriteNumber($row,0,$i+1);
	$g_exl_writer->xlsWriteLabel($row,1,$g_view['data'][$i]['short_name']);
	$g_exl_writer->xlsWriteNumber($row,2,$g_view['data'][$i]['value']);
	$row++;
}

// close the stream
$g_exl_writer->xlsEOF(); 
?>