<?php
/****
called in ajax code, to generate issuance data table

We use a new var because chanrt and issuance data must not clash
***/
include("../include/global.php");
require_once("classes/class.statistics.php");
///////////////////////////////////////
//the data are in $_POST, so
$g_view['issuance_data'] = array();
$g_view['issuance_data']['max_value'] = 0;
$g_view['issuance_data']['stat_count'] = 0;
$g_view['issuance_data']['stat_data'] = array();

$success = $g_stat->generate_issuance_data($_POST,$g_view['issuance_data']['stat_data'],$g_view['issuance_data']['max_value'],$g_view['issuance_data']['stat_count']);
if(!$success){
	//treat it as no data
	$g_view['issuance_data']['stat_count'] = 0;
	return;
}
///////////////////////////////////
//set in session
$_SESSION['issuance_data'] = $g_view['issuance_data'];
if (isset($_GET['version']) && $_GET['version'] == '2') {
    require_once('classes/class.chart.php');
    $newData = array();
    foreach ($g_view['issuance_data']['stat_data'] as $data) {
        $newData[$data['short_name']] =  $data['value'];
    }

    $chart = new chart($newData);
    echo $chart->getHtml();   
}
?>