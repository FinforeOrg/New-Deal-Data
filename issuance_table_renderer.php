<?php
/***
sng:24/july/2010
The values are billion dollar
********/
include("include/global.php");
require_once("classes/class.barchart.php");
//////////////////////////
$g_barchart->show_legend_detail(false);
$font_path_name = FILE_PATH."/font/tahoma.ttf";
$g_barchart->set_font($font_path_name);
$g_barchart->set_dimension(500,275);
$g_barchart->set_bar_gap(30);
$g_barchart->set_bar_width(40);
$g_barchart->set_stat_value_label_format("$%nbn");

$g_barchart->render($_SESSION['issuance_data']['stat_data'],$_SESSION['issuance_data']['max_value'],$_SESSION['issuance_data']['stat_count']);
unset($_SESSION['issuance_data']);
//remove the issuance data from session so as not to clutter
?>