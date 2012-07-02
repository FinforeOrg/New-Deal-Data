<?php
/********
methods regarding blogs
************/
require_once("classes/class.magic_quote.php");
class blog{
	/****************
	sng:12/dec/2011
	dirty workaround. we want to use the deal data db but table prefix
	dstacx to detach data for data-cx.com
	At this stage, the easiest approach will be to use the table name directly via the use of a variable
	*******************/
	private static $tbl_name = "datacx_blog";
	
	public function get_all_post_list_paged($start_offset,$num_to_fetch,&$data_arr,&$data_count){
		global $g_mc;
		$q = "select * from ".blog::$tbl_name." order by posted_on desc limit ".$start_offset.",".$num_to_fetch;
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$data_count = mysql_num_rows($res);
		if($data_count == 0){
			//no recs
			return true;
		}
		//recs so
		while($row = mysql_fetch_assoc($res)){
			$row['title'] = $g_mc->db_to_view($row['title']);
			$row['content'] = $g_mc->db_to_view($row['content']);
			$data_arr[] = $row;
		}
		return true;
	}
	public function add_post($data_arr,&$validation_passed,&$err_arr){
		global $g_mc;
		//validation
		$validation_passed = true;
		if($data_arr['title'] == ""){
			$err_arr['title'] = "Please specify the title";
			$validation_passed = false;
		}
		if($data_arr['content'] == ""){
			$err_arr['content'] = "Please specify the content";
			$validation_passed = false;
		}
		/////////////////////////////////////
		if(!$validation_passed){
			//no need to proceed
			return true;
		}
		///////////////////////////////////////////////////////
		//insert data
		$curr_date = date('Y-m-d H:i:s');
		$q = "insert into ".blog::$tbl_name." set title='".$g_mc->view_to_db($data_arr['title'])."',content='".$g_mc->view_to_db($data_arr['content'])."',posted_on='".$curr_date."'";
		$result = mysql_query($q);
		if(!$result){
			return false;
		}
		/////////////////
		//data inserted
		$validation_passed = true;
		return true;
	}
	
	public function delete_post($blog_id){
		$q = "delete from ".blog::$tbl_name." where blog_id='".$blog_id."'";
		$result = mysql_query($q);
		if($result===false){
			return false;
		}else{
			return true;
		}
	}
	
	/********************
	sng:29/sep/2011
	get a particular blog entry
	***************/
	public function get_entry($blog_id,&$data_arr){
		global $g_mc;
		$q = "select * from ".blog::$tbl_name." where blog_id='".$blog_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$data_count = mysql_num_rows($res);
		if($data_count == 0){
			//no such entry
			return false;
		}
		//recs so
		$data_arr = mysql_fetch_assoc($res);
		$data_arr['title'] = $g_mc->db_to_view($data_arr['title']);
		$data_arr['content'] = $g_mc->db_to_view($data_arr['content']);
		return true;
	}
	
	public function edit_post($blog_id,$data_arr,&$validation_passed,&$err_arr){
		global $g_mc;
		//validation
		$validation_passed = true;
		if($data_arr['title'] == ""){
			$err_arr['title'] = "Please specify the title";
			$validation_passed = false;
		}
		if($data_arr['content'] == ""){
			$err_arr['content'] = "Please specify the content";
			$validation_passed = false;
		}
		/////////////////////////////////////
		if(!$validation_passed){
			//no need to proceed
			return true;
		}
		///////////////////////////////////////////////////////
		//insert data
		$q = "update ".blog::$tbl_name." set title='".$g_mc->view_to_db($data_arr['title'])."',content='".$g_mc->view_to_db($data_arr['content'])."' where blog_id='".$blog_id."'";
		$result = mysql_query($q);
		if(!$result){
			return false;
		}
		/////////////////
		//data inserted
		$validation_passed = true;
		return true;
	}
}
$g_blog = new blog();
?>