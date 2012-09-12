<?php
require_once("include/global.php");
require_once("classes/class.transaction.php");

/***************
sng:16/july/2012
Now we embedd the league table here also
*************/
require("embedded_league_table.php");
/****************************************************
We get 5 most recent M&A deals, 5 most recent Equity deals, 5 most recent Debt deals
*******************/
$g_view['start_offset'] = 0;
$g_view['num_to_show'] = 5;

$params = array();
$params['deal_cat_name'] = "m&a";
$params['number_of_deals'] = "recent:5";

$g_view['ma_data'] = array();
$g_view['ma_data_count'] = 0;

$success = $g_trans->front_deal_search_paged($params,$g_view['start_offset'],$g_view['num_to_show'],$g_view['ma_data'],$g_view['ma_data_count']);
if(!$success){
	die("Cannot search for deal");
}
/****************************************************/
$params['deal_cat_name'] = "equity";
$params['number_of_deals'] = "recent:5";

$g_view['eq_data'] = array();
$g_view['eq_data_count'] = 0;

$success = $g_trans->front_deal_search_paged($params,$g_view['start_offset'],$g_view['num_to_show'],$g_view['eq_data'],$g_view['eq_data_count']);
if(!$success){
	die("Cannot search for deal");
}
/*****************************************************/
$params['deal_cat_name'] = "debt";
$params['number_of_deals'] = "recent:5";

$g_view['dbt_data'] = array();
$g_view['dbt_data_count'] = 0;

$success = $g_trans->front_deal_search_paged($params,$g_view['start_offset'],$g_view['num_to_show'],$g_view['dbt_data'],$g_view['dbt_data_count']);
if(!$success){
	die("Cannot search for deal");
}
/*****************************************************/



require_once("default_metatags.php");
/***************
sng:12/sep/2012
For the home page, the default top search bar selection is deals
****************/
if(!isset($_POST['top_search_area'])){
	$_POST['top_search_area'] = "deal";
}

$g_view['page_heading'] = "Welcome to Deal Data";
$g_view['show_help'] = true;
$g_view['content_view'] = "index_new_view.php";
require("content_view.php");
?>