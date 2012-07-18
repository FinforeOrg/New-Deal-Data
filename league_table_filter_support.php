<?php
/****
it is assumed that $_POST has submitted data

sng:18/july/2012 Otherwise we set to blank
*********/
if(!isset($_POST['deal_cat_name'])){
	$_POST['deal_cat_name'] = "";
}
if(!isset($_POST['deal_subcat1_name'])){
	$_POST['deal_subcat1_name'] = "";
}
if(!isset($_POST['sector'])){
	$_POST['sector'] = "";
}
//////////////////
//fetch Category names
$g_view['cat_list'] = array();
$g_view['cat_count'] = 0;
//$success = $g_trans->get_all_category_list($g_view['cat_list'],$g_view['cat_count']);
$success = $g_trans->get_all_category_type("type",$g_view['cat_list'],$g_view['cat_count']);
if(!$success){
	die("Cannot get category list");
}
////////////////////////////
//fetch subcategories for this category
$g_view['subcat_list'] = array();
$g_view['subcat_count'] = 0;
$success = $g_trans->get_all_category_subtype1_for_category_type($_POST['deal_cat_name'],$g_view['subcat_list'],$g_view['subcat_count']);
if(!$success){
	die("Cannot get sub category list");
}
//////////////////////////////////////////////////
//fetch sub subcategories for this category
$g_view['sub_subcat_list'] = array();
$g_view['sub_subcat_count'] = 0;
$success = $g_trans->get_all_category_subtype2_for_category_type($_POST['deal_cat_name'],$_POST['deal_subcat1_name'],$g_view['sub_subcat_list'],$g_view['sub_subcat_count']);
if(!$success){
	die("Cannot get sub sub category list");
}
///////////////////////////////////
//fetch regions
$g_view['region_list'] = array();
$g_view['region_count'] = 0;
$success = $g_country->get_all_region_list($g_view['region_list'],$g_view['region_count']);
if(!$success){
	die("Cannot get region list");
}
//////////////////////////////////////////
//fetch countries
$g_view['country_list'] = array();
$g_view['country_count'] = 0;
$success = $g_country->get_all_country_list($g_view['country_list'],$g_view['country_count']);
if(!$success){
	die("Cannot get country list");
}
////////////////////////////////////////////
/***
sng:19/may/2010
We show sector and fetch industry as per the sector selected
*******/
//fetch sector types
$g_view['sector_list'] = array();
$g_view['sector_count'] = 0;
$success = $g_company->get_all_sector_list($g_view['sector_list'],$g_view['sector_count']);
if(!$success){
	die("Cannot get sector list");
}
////////////////////////////////////////////
/***
sng:19/jul/2010
Now logged in user can filter via industry also. For that, we fetch industries based
on sector selected. We fetch the industries anyway and decide in the view whehter
to show or not.
*******/
//fetch industry types
$g_view['industry_list'] = array();
$g_view['industry_count'] = 0;
$success = $g_company->get_all_industry_for_sector($_POST['sector'],$g_view['industry_list'],$g_view['industry_count']);
if(!$success){
	die("Cannot get industry list");
}
//////////////////////////////
/***
sng:23/july/2010
There is another filter on deal size
*******/
$g_view['deal_size_filter_list'] = array();
$g_view['deal_size_filter_list_count'] = 0;
$success = $g_trans->front_get_deal_size_filter_list($g_view['deal_size_filter_list'],$g_view['deal_size_filter_list_count']);
if(!$success){
	die("Cannot get deal size filter list");
}
//////////////////////
?>