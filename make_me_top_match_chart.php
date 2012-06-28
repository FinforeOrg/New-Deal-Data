<?php
require_once("include/global.php");
require_once("check_mem_login.php");
require_once("classes/class.make_me_top.php");
require_once("nifty_functions.php");
/////////////////////////////////////////
$g_view['job_id'] = $_GET['job_id'];
$g_view['back_start_offset'] = $_GET['start'];
$g_view['search_result_id'] = $_GET['result_id'];

$g_view['result_data'] = NULL;
$g_view['result_found'] = false;
$success = $g_maketop->search_result($g_view['search_result_id'],$g_view['result_data'],$g_view['result_found']);
if(!$success){
	die("Cannot get the result");
}
/////////////////////////////////////////
//given these param presets, make a search and show the result
//we pass job id so that rank requested can be found out (since we get that many data)
$g_view['search_result_firms'] = array();
$g_view['search_result_firms_count'] = 0;
$success = $g_maketop->get_search_result_firms($g_view['job_id'],$g_view['search_result_id'],$g_view['search_result_firms'],$g_view['search_result_firms_count']);
if(!$success){
	die("Cannot get the firms for the search result");
}
//////////////////////////////////////////////////
//we need to show a barchart also. since we have the data, we can put it in session so that
//league_table_renderer.php can create the chart
//the code is taken from league_table_creator
$g_view['chart_data'] = array();
$g_view['chart_data']['max_value'] = 0;
$g_view['chart_data']['stat_count'] = $g_view['search_result_firms_count'];
$g_view['chart_data']['stat_data'] = array();
$g_view['chart_data']['ranking_criteria'] = $g_view['result_data']['ranking_criteria'];
//put the name value pair
//just like in class statistics::generate_ranking
$max_value = "";
for($i=0;$i<$g_view['chart_data']['stat_count'];$i++){
	$g_view['chart_data']['stat_data'][$i] = array();
	$g_view['chart_data']['stat_data'][$i]['name'] = $g_view['search_result_firms'][$i]['firm_name'];
	$g_view['chart_data']['stat_data'][$i]['short_name'] = $g_view['search_result_firms'][$i]['short_name'];
	//if the stat is not total num of deals, then it is in billion and has a high precision, correct to 2 decimal place
	if(($g_view['chart_data']['ranking_criteria']=="total_deal_value")||($g_view['chart_data']['ranking_criteria']=="total_adjusted_deal_value")){
		$g_view['chart_data']['stat_data'][$i]['value'] = round($g_view['search_result_firms'][$i]['stat_value'],2);
	}else{
		$g_view['chart_data']['stat_data'][$i]['value'] = $g_view['search_result_firms'][$i]['stat_value'];
	}
	if($max_value == ""){
		$max_value = $g_view['chart_data']['stat_data'][$i]['value'];
	}else{
		if($g_view['chart_data']['stat_data'][$i]['value'] > $max_value){
			$max_value = $g_view['chart_data']['stat_data'][$i]['value'];
		}
	}
}
$g_view['chart_data']['max_value'] = $max_value;
$_SESSION['chart_data'] = $g_view['chart_data'];
$newData = array();
foreach($_SESSION['chart_data']['stat_data'] as $values) {
         if (!strlen($values['short_name'])) {
            if (strlen($values['name']) > 6) {
                if (substr_count($values['name'],' ') > 0) {
                    $words = explode(' ', $values['name']);
                    $index = '';
                    foreach($words as $word) {
                         $index .= strtoupper(substr($word,0,1));
                    }
                } 
            } else {
               $index =  $values['name'];
            }
        } else {
           $index = $values['short_name'];
        }
        if (isset($newData[$index])) {
            $index .= ' ';
        }
       $newData[$index] =  $values['value']; 
}
require_once('classes/class.chart.php');
$chart = new chart($newData);
if ($_SESSION['chart_data']['ranking_criteria'] != 'num_deals') {
    $chart->labelRegex = '$%sbn';   
}
$chartHtml = $chart->getHtml(true);
////////////////////////////////////////////////////////////
require_once("default_metatags.php");
$g_view['content_view'] = "make_me_top_match_chart_view.php";
require("content_view.php");
////////////////////////////////////////////////////////////
?>