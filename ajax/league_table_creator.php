<?php
/****
called in ajax code, to generate league table

sng:1/may/2010
We need to store the ranking criteria also, so that, the renderer
can set the stat value label format for the chart
***/
include("../include/global.php");
require_once("classes/class.statistics.php");
require_once("classes/class.leagueTableChart.php");
///////////////////////////////////////
//the data are in $_POST, so
$g_view['chart_data'] = array();
$g_view['chart_data']['max_value'] = 0;
$g_view['chart_data']['stat_count'] = 0;
$g_view['chart_data']['stat_data'] = array();
$g_view['chart_data']['ranking_criteria'] = $_POST['ranking_criteria'];

$chart = new leagueTableChart($_POST);
$chart->setName((!isset($_REQUEST['chartName']) ? 'chart1' : $_REQUEST['chartName']));
$chart->getHtml();

exit();