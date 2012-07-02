<?php
/******
This class is for setting the various options for the site.
See table sitesetup
*********/
require_once("classes/class.magic_quote.php");
class sitesetup{
	/****************
	sng:12/dec/2011
	dirty workaround. we want to use the deal data db but table prefix
	dstacx to detach data for data-cx.com
	At this stage, the easiest approach will be to use the table name directly via the use of a variable
	*******************/
	private static $tbl_name = "datacx_sitesetup";
	
	public function get_metatags(&$data_arr){
		global $g_mc;
		
		$q = "select meta_title,meta_keywords,meta_description from ".sitesetup::$tbl_name;
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$data_arr = mysql_fetch_assoc($res);
		$data_arr['meta_title'] = $g_mc->db_to_view($data_arr['meta_title']);
		$data_arr['meta_keywords'] = $g_mc->db_to_view($data_arr['meta_keywords']);
		$data_arr['meta_description'] = $g_mc->db_to_view($data_arr['meta_description']);
		return true;
	}
	
	public function set_metatags($data_arr){
		global $g_mc;
		//we do not put validation on these
		/****
		sng:10/may/2010
		if db to view is used to get data, then view to db should be used to put data
		***/
		$q = "update ".sitesetup::$tbl_name." set meta_title='".$g_mc->view_to_db($data_arr['meta_title'])."', meta_keywords='".$g_mc->view_to_db($data_arr['meta_keywords'])."', meta_description='".$g_mc->view_to_db($data_arr['meta_description'])."'";
		$result = mysql_query($q);
		if(!$result){
			return false;
		}else{
			return true;
		}
	}
	
	/***
	sng:10/may/2010
	We need a way to put the site in maintenance, so
	***/
	public function get_maintenance_info(&$data_arr){
		global $g_mc;
		
		$q = "select site_in_maintenance,site_in_maintenance_text from ".sitesetup::$tbl_name;
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$data_arr = mysql_fetch_assoc($res);
		$data_arr['site_in_maintenance_text'] = $g_mc->db_to_view($data_arr['site_in_maintenance_text']);
		return true;
	}
	public function set_maintenance_info($data_arr){
		global $g_mc;
		//we do not put validation on these
		$q = "update ".sitesetup::$tbl_name." set site_in_maintenance='".$data_arr['site_in_maintenance']."', site_in_maintenance_text='".$g_mc->view_to_db($data_arr['site_in_maintenance_text'])."'";
		$result = mysql_query($q);
		if(!$result){
			return false;
		}else{
			return true;
		}
	}
	/***
	sng:3/june/2010
	
	sng:28/jan/2011
	Added mem_related_email
	****/
	public function get_site_emails(&$data_arr){
		
		$q = "select contact_email,registration_email,registration_notification_email,suggestion_email,mem_related_email from ".sitesetup::$tbl_name;
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$data_arr = mysql_fetch_assoc($res);
		return true;
	}
	/***
	sng:3/june/2010
	
	sng:28/jan/2011
	Added mem_related_email
	****/
	public function set_site_emails($data_arr){
		global $g_mc;
		//we do not put validation on these
		$q = "update ".sitesetup::$tbl_name." set contact_email='".$data_arr['contact_email']."',registration_email='".$data_arr['registration_email']."',registration_notification_email='".$data_arr['registration_notification_email']."',suggestion_email='".$data_arr['suggestion_email']."',mem_related_email='".$data_arr['mem_related_email']."'";
		$result = mysql_query($q);
		if(!$result){
			return false;
		}else{
			return true;
		}
	}
	
	/***********************************
	sng:12/oct/2010
	SMTP credentials
	*******/
	public function get_smtp(&$data_arr){
		
		$q = "select smtp_host,smtp_port,smtp_user,smtp_pass from ".sitesetup::$tbl_name;
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$data_arr = mysql_fetch_assoc($res);
		return true;
	}
	public function set_smtp($data_arr,&$validation_passed,&$err_arr){
		//validation
		$validation_passed = true;
		if($data_arr['smtp_host']==""){
			$validation_passed = false;
			$err_arr['smtp_host'] = "Please specify the smtp host";
		}
		if($data_arr['smtp_port']==""){
			$validation_passed = false;
			$err_arr['smtp_port'] = "Please specify the smtp port";
		}
		if($data_arr['smtp_user']==""){
			$validation_passed = false;
			$err_arr['smtp_user'] = "Please specify the smtp username";
		}
		if($data_arr['smtp_pass']==""){
			$validation_passed = false;
			$err_arr['smtp_pass'] = "Please specify the smtp password";
		}
		if(!$validation_passed){
			//no need to proceed
			return true;
		}
		//validation passed, update
		$q = "update ".sitesetup::$tbl_name." set smtp_host='".$data_arr['smtp_host']."',smtp_port='".$data_arr['smtp_port']."',smtp_user='".$data_arr['smtp_user']."',smtp_pass='".$data_arr['smtp_pass']."'";
		$result = mysql_query($q);
		if($result){
			$validation_passed = true;
			return true;
		}else{
			return false;
		}
	}
}
$g_site = new sitesetup();
?>