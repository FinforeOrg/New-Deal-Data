<?php
/****************
This is used to search for banker. Visitor type banker's name

sng:1/jun/2010
It has been decided that visitors should not be able to see banker/lawyer data. So, we show
message if the user is not logged in instead of showing the matched members
***************/
include("include/global.php");
require_once("classes/class.member.php");
require_once("classes/class.magic_quote.php");
require_once("classes/class.account.php");
///////////////////////
if(!$g_account->is_site_member_logged()){
	$g_view['page_heading'] = "Banker search result";
	$g_view['content_view'] = "restricted.php";
	require("content_view.php");
	exit;
}
/////////////////////////////////////////////////
if(isset($_POST['myaction'])&&($_POST['myaction']=="search")){
	//search request	
	//pagination support
	if(!isset($_POST['start'])||($_POST['start']=="")){
		$g_view['start_offset'] = 0;
	}else{
		$g_view['start_offset'] = $_POST['start'];
	}
	$g_view['num_to_show'] = 10;
	$g_view['data'] = array();
	$g_view['data_count'] = 0;
	$g_view['total_data_count'] = 0;
	$success = $g_mem->front_search_for_member_of_type_paged($_POST['top_search_term'],"banker",$g_view['start_offset'],$g_view['num_to_show']+1,$g_view['data'],$g_view['data_count'],$g_view['total_data_count']);
	
	if(!$success){
		die("Cannot search for banker");
	}
	/////////////////////////////////////////
	$g_view['search_form_input'] = $g_mc->view_to_view($_POST['top_search_term']);
	
}else{
	$g_view['search_form_input'] = "enter name of the banker";
	$g_view['data_count'] = 0;
}
////////////////////////////////
require_once("default_metatags.php");
/////////////////////////////////////////////////////////////////////////////////
$g_view['page_heading'] = "Banker search result";
if(isset($g_view['total_data_count'])&&($g_view['total_data_count'] > 0)){
	$g_view['page_heading'].=" [".$g_view['total_data_count']." found]";
}
/***
sng:19/may/2010
Now the default search handle search of banker
******/
$g_view['content_view'] = "banker_search_view.php";
require("content_view.php");
?>