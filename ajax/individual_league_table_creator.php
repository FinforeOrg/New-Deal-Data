<?php
/****
called in ajax code, to generate league table

sng:5/apr/2013
We now want to show chart and member names, so we use the code of league table

sng:1/may/2010
We need to store the ranking criteria also, so that, the renderer
can set the stat value label format for the chart
***/
require_once("../include/global.php");
require_once("classes/class.statistics.php");
require_once("classes/class.leagueTableChart.php");
///////////////////////////////////////
//the data are in $_POST, so
$g_view['chart_data'] = array();
$g_view['chart_data']['max_value'] = 0;
$g_view['chart_data']['stat_count'] = 0;
$g_view['chart_data']['stat_data'] = array();
$g_view['chart_data']['ranking_criteria'] = $_POST['ranking_criteria'];

/***********
sng:23/jul/2012
We cannot send conditions like >=23. The sanitizer will erase it. We base64_encode it in the forms and decode it here
*****************/
if(isset($_POST['deal_size'])){
	$_POST['deal_size'] = base64_decode($_POST['deal_size']);
}

$chart = new leagueTableChart($_POST);
$chart->setName((!isset($_REQUEST['chartName']) ? 'chart1' : $_REQUEST['chartName']));
$chart->get_individual_league_table_html();

exit();
?>