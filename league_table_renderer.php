<?php
/***
sng:1/may/2010
the ajax/league_table_creator.php has set the ranking criteria so based on that
we can set the stat value label format
********/
include("include/global.php");
require_once("classes/class.barchart.php");
//////////////////////////
$g_barchart->show_legend_detail(false);
$font_path_name = FILE_PATH."/font/tahoma.ttf";
$g_barchart->set_font($font_path_name);
$g_barchart->set_dimension(400,275);
$g_barchart->set_bar_gap(30);
$g_barchart->set_bar_width(40);
if($_SESSION['chart_data']['ranking_criteria']!="num_deals"){
	$g_barchart->set_stat_value_label_format("$%nbn");
}
$g_barchart->render($_SESSION['chart_data']['stat_data'],$_SESSION['chart_data']['max_value'],$_SESSION['chart_data']['stat_count']);
unset($_SESSION['chart_data']);
//remove the chart data from session so as not to clutter
?>