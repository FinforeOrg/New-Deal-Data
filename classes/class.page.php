<?php
require_once("classes/class.magic_quote.php");
class page{
	/******
	var to hold the page data
	*********/
	var $meta_title;
	var $meta_keywords;
	var $meta_description;
	var $heading;
	var $content;
	var $banner_img;
	
	/****************
	sng:12/dec/2011
	dirty workaround. we want to use the deal data db but table prefix
	dstacx to detach data for data-cx.com
	At this stage, the easiest approach will be to use the table name directly via the use of a variable
	*******************/
	private static $tbl_name = "datacx_pages";
	
	/****
	get the data and populate the fields
	return false if cannot get data
	*****/
	function get_page_data($page_name){
		global $g_mc;
		$q = "select * from ".page::$tbl_name." where page_name='".$page_name."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$cnt = mysql_num_rows($res);
		if(0==$cnt){
			//no rows found
			return false;
		}
		//////////////////////////////
		$row = mysql_fetch_assoc($res);
		$this->meta_title = $g_mc->db_to_view($row['meta_title']);
		$this->meta_keywords = $g_mc->db_to_view($row['meta_keywords']);
		$this->meta_description = $g_mc->db_to_view($row['meta_description']);
		$this->heading = $g_mc->db_to_view($row['heading']);
		$this->content = $g_mc->db_to_view($row['content']);
		$this->banner_img = $row['banner_img'];
		return true;
	}
	
	/***
	get data for all pages, used in listing
	**/
	function get_all_pages(&$num_pages,&$page_data_arr){
		$g_mc = new magic_quote();
		$q = "select page_id,page_name,heading from ".page::$tbl_name."";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$num_pages = mysql_num_rows($res);
		if($num_pages == 0){
			return true;
		}
		for($i=0;$i<$num_pages;$i++){
			$row = mysql_fetch_assoc($res);
			$page_data_arr[$i] = array();
			$page_data_arr[$i]['page_id'] = $row['page_id'];
			$page_data_arr[$i]['page_name'] = $g_mc->db_to_view($row['page_name']);
			$page_data_arr[$i]['heading'] = $g_mc->db_to_view($row['heading']);
		}
		return true;
	}
	
	/***
	update the data for a page
	***/
	function set_page_data($page_name,$page_data_arr){
		global $g_mc;
		$q = "update ".page::$tbl_name." set meta_title='".$g_mc->view_to_db($page_data_arr['meta_title'])."', meta_keywords='".$g_mc->view_to_db($page_data_arr['meta_keywords'])."', meta_description='".$g_mc->view_to_db($page_data_arr['meta_description'])."',heading='".$g_mc->view_to_db($page_data_arr['heading'])."', content='".$g_mc->view_to_db($page_data_arr['content'])."' where page_name='".$page_name."'";
		$success = mysql_query($q);
		if(!$success){
			return false;
		}else{
			return true;
		}
	}
	
	
}
$g_page = new page();
?>