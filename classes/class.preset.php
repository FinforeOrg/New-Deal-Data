<?php
/****
sng:29/july/2010
This is where I put the codes regarding the search presets. That way, the overburdened transaction
is not burdened further.

This along with the class make_me_top has the required methods
***********/
class preset{
	/***
	2/sep/2010
	******/
	public function admin_get_all_top_search_request_paged($status,$start_offset,$num_to_fetch,&$data_arr,&$data_count){
		if($status == "in_progress") $status = "in progress";
		global $g_mc;
		
		$q = "select req.job_id,req.mem_id,req.submitted_on,req.company_id,req.type,req.option_country,req.option_deal_type,req.option_sector_industry,req.rank_requested,req.status,req.is_scheduled,req.started_on,req.dbg_last_processing_time,req.finished_on,req.dbg_status,TIMEDIFF(req.dbg_last_processing_time,req.started_on) AS time_elapsed,c.name as country_name,d.name as deal_name,i.name as industry_name from ".TP."top_search_request as req left join ".TP."top_search_option_country as c on(req.option_country=c.option_id) left join ".TP."top_search_option_deal_type as d on(req.option_deal_type=d.option_id) left join ".TP."top_search_option_sector_industry as i on(req.option_sector_industry=i.option_id) where status='".$status."' order by submitted_on limit ".$start_offset.",".$num_to_fetch;
		
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$data_count = mysql_num_rows($res);
		if(0==$data_count){
			return true;
		}
		for($i=0;$i<$data_count;$i++){
			$data_arr[$i] = mysql_fetch_assoc($res);
			$data_arr[$i]['country_name'] = $g_mc->db_to_view($data_arr[$i]['country_name']);
			$data_arr[$i]['deal_name'] = $g_mc->db_to_view($data_arr[$i]['deal_name']);
			$data_arr[$i]['industry_name'] = $g_mc->db_to_view($data_arr[$i]['industry_name']);
		}
		return true;
	}
	/****
	sng: 17/aug/2010
	
	sng: 4/sep/2010
	getting specific fields of top_search_request
	
	sng:21/sep/2010
	support for extended_search
	
	sng:05/oct/2010
	Get the requests which are not marked as archived
	*********/
	public function front_get_all_top_search_request($mem_id,&$data_arr,&$data_count){
		global $g_mc;
		
		$q = "select req.job_id,req.mem_id,req.submitted_on,req.company_id,req.type,req.option_country,req.option_deal_type,req.option_sector_industry,req.rank_requested,req.extended_search,req.status,req.is_scheduled,req.started_on,req.dbg_last_processing_time,req.finished_on,req.dbg_status,c.name as country_name,d.name as deal_name,i.name as industry_name from ".TP."top_search_request as req left join ".TP."top_search_option_country as c on(req.option_country=c.option_id) left join ".TP."top_search_option_deal_type as d on(req.option_deal_type=d.option_id) left join ".TP."top_search_option_sector_industry as i on(req.option_sector_industry=i.option_id) where mem_id='".$mem_id."' and req.is_archived='n' order by submitted_on";
		
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$data_count = mysql_num_rows($res);
		if(0==$data_count){
			return true;
		}
		for($i=0;$i<$data_count;$i++){
			$data_arr[$i] = mysql_fetch_assoc($res);
			$data_arr[$i]['country_name'] = $g_mc->db_to_view($data_arr[$i]['country_name']);
			$data_arr[$i]['deal_name'] = $g_mc->db_to_view($data_arr[$i]['deal_name']);
			$data_arr[$i]['industry_name'] = $g_mc->db_to_view($data_arr[$i]['industry_name']);
		}
		return true;
	}
	/******************************
	sng:05/oct/2010
	*********/
	public function front_get_archived_top_search_request($mem_id,&$data_arr,&$data_count){
		global $g_mc;
		
		$q = "select req.job_id,req.mem_id,req.submitted_on,req.company_id,req.type,req.option_country,req.option_deal_type,req.option_sector_industry,req.rank_requested,req.extended_search,req.status,req.is_scheduled,req.started_on,req.dbg_last_processing_time,req.finished_on,req.dbg_status,c.name as country_name,d.name as deal_name,i.name as industry_name from ".TP."top_search_request as req left join ".TP."top_search_option_country as c on(req.option_country=c.option_id) left join ".TP."top_search_option_deal_type as d on(req.option_deal_type=d.option_id) left join ".TP."top_search_option_sector_industry as i on(req.option_sector_industry=i.option_id) where mem_id='".$mem_id."' and req.is_archived='y' order by submitted_on";
		
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$data_count = mysql_num_rows($res);
		if(0==$data_count){
			return true;
		}
		for($i=0;$i<$data_count;$i++){
			$data_arr[$i] = mysql_fetch_assoc($res);
			$data_arr[$i]['country_name'] = $g_mc->db_to_view($data_arr[$i]['country_name']);
			$data_arr[$i]['deal_name'] = $g_mc->db_to_view($data_arr[$i]['deal_name']);
			$data_arr[$i]['industry_name'] = $g_mc->db_to_view($data_arr[$i]['industry_name']);
		}
		return true;
	}
	/****
	sng:26/aug/2010
	In this, we try to get the search request data for a job. It may happen that user is clicking
	a link in email to get this. So, if the data is not found, we tell that instead of returning false.
	
	sng:4/sep/2010
	just taking specific columns from top_search_request
	*********/
	public function front_get_top_search_request($job_id,&$data_arr,&$found){
		global $g_mc;
		
		$q = "select req.job_id,req.mem_id,req.submitted_on,req.company_id,req.type,req.option_country,req.option_deal_type,req.option_sector_industry,req.rank_requested,req.extended_search,req.status,req.is_scheduled,req.started_on,req.dbg_last_processing_time,req.finished_on,req.dbg_status,c.name as country_name,d.name as deal_name,i.name as industry_name from ".TP."top_search_request as req left join ".TP."top_search_option_country as c on(req.option_country=c.option_id) left join ".TP."top_search_option_deal_type as d on(req.option_deal_type=d.option_id) left join ".TP."top_search_option_sector_industry as i on(req.option_sector_industry=i.option_id) where job_id='".$job_id."'";
		
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$data_count = mysql_num_rows($res);
		if(0==$data_count){
			$found = false;
			return true;
		}
		$found = true;
		$data_arr = mysql_fetch_assoc($res);
		$data_arr['country_name'] = $g_mc->db_to_view($data_arr['country_name']);
		$data_arr['deal_name'] = $g_mc->db_to_view($data_arr['deal_name']);
		$data_arr['industry_name'] = $g_mc->db_to_view($data_arr['industry_name']);
		return true;
	}
	
	public function front_submit_top_search_request($mem_id,$data_arr,&$validation_passed,&$err_arr){
		//right now we are not validating for blank
		$time_now = date("Y-m-d H:i:s");
		//validation
		$validation_passed = true;
		if($data_arr['top_search_option_country']==""){
			$validation_passed = false;
			$err_arr['top_search_option_country'] = "Please select";
		}
		if($data_arr['top_search_option_sector_industry']==""){
			$validation_passed = false;
			$err_arr['top_search_option_sector_industry'] = "Please select";
		}
		if($data_arr['top_search_option_deal_type']==""){
			$validation_passed = false;
			$err_arr['top_search_option_deal_type'] = "Please select";
		}
		if(!$validation_passed){
			//no need to proceed
			return true;
		}
		/////////////////////////////////////////
		$job_id = $mem_id."-".time();
		if($_SESSION['member_type']=="banker") $company_type = "bank";
		if($_SESSION['member_type']=="lawyer") $company_type = "law firm";
		if($_SESSION['member_type']=="company rep") $company_type = "company";
		
		/*****************
		sng: 18/sep/2010
		It has been decided that the member can choose whether to do a fast (a non extended search only on primary presets
		or full (slow but all presets)
		
		There is a check box called fast search. Ticking it means fast search (extended: n)
		**********/
		$extended_search = 'y';
		//by default
		if(isset($data_arr['extended_search'])){
			if($data_arr['extended_search']=="n"){
				$extended_search = 'n';
			}
		}
		/***********
		sng:28/sep/2010
		Now the member can specify that consider only those hits where my firm is
		within top 3. If rank_requested is sent, it is used, else the default_rank_requested is used
		*************/
		$rank_requested = $data_arr['default_rank_requested'];
		if(isset($data_arr['rank_requested'])){
			if($data_arr['rank_requested']!=""){
				$rank_requested = $data_arr['rank_requested'];
			}
		}
		
		$q = "insert into ".TP."top_search_request set job_id='".$job_id."', mem_id='".$mem_id."', submitted_on='".$time_now."',company_id='".$_SESSION['company_id']."',type='".$company_type."',option_country='".$data_arr['top_search_option_country']."',option_deal_type='".$data_arr['top_search_option_deal_type']."',option_sector_industry='".$data_arr['top_search_option_sector_industry']."',rank_requested='".$rank_requested."',extended_search='".$extended_search."',status='pending'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$validation_passed = true;
		return true;
	}
	/*********************************************
	sng:12/jan/2011
	It may happen that the member wants to rerun a mmt jobs, with same params and conditions.
	In that case, first check it is finished or not. If finished, no background code is dealing with it.
	***********/
	public function front_rerun_top_search_request($mem_id,$job_id){
		$q = "select status from ".TP."top_search_request where job_id='".$job_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$cnt = mysql_num_rows($res);
		if(0==$cnt){
			//no such data
			return false;
		}
		$row = mysql_fetch_assoc($res);
		if($row['status']!="finished"){
			//we are not supposed to re run mmt jobs that are not finished
			return false;
		}
		//delete the hits
		$del_q = "delete from ".TP."top_search_request_error where job_id='".$job_id."'";
		mysql_query($del_q);
		
		$del_q = "delete from ".TP."top_search_request_processing_helper where job_id='".$job_id."'";
		mysql_query($del_q);
		
		$del_q = "delete from ".TP."top_search_request_hits where job_id='".$job_id."'";
		mysql_query($del_q);
		
		//reset everything and put submitted on as today
		$time_now = date("Y-m-d H:i:s");
		$updt_q = "update ".TP."top_search_request set submitted_on='".$time_now."',status='pending',is_scheduled='n',started_on='0000-00-00 00:00:00',dbg_last_processing_time='0000-00-00 00:00:00',finished_on='0000-00-00 00:00:00',dbg_status='', hits='0',is_archived='n' where job_id='".$job_id."'";
		$result = mysql_query($updt_q);
		if(!$result){
			//echo mysql_error();
			return false;
		}
		return true;
	}
	/*****
	sng:5/oct/2010
	We need support to mark a request as archived
	***/
	public function archive_top_search_request($job_id){
		$q = "update ".TP."top_search_request set is_archived='y' where job_id='".$job_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		return true;
	}
	
	/**********
	sng:23/nov/2010
	We need a function to remove all the finished mmt jobs and their associated data
	Since we are not storing any chart image, no file deletion code
	*******/
	public function delete_finished_top_search_requests_of_member($mem_id){
		$q = "select job_id from ".TP."top_search_request where mem_id='".$mem_id."' AND (status='finished' OR status='partial')";
		$res = mysql_query($q);
		while($row = mysql_fetch_assoc($res)){
			/********
			delete from error
			******/
			$job_id = $row['job_id'];
			$del_q = "delete from ".TP."top_search_request_error where job_id='".$job_id."'";
			mysql_query($del_q);
			/*********
			delete from helper
			*********/
			$del_q = "delete from ".TP."top_search_request_processing_helper where job_id='".$job_id."'";
			mysql_query($del_q);
			/*********
			delete from hits
			*********/
			$del_q = "delete from ".TP."top_search_request_hits where job_id='".$job_id."'";
			mysql_query($del_q);
		}
		/****
		now delete the recs
		***/
		$del_q = "delete from ".TP."top_search_request where mem_id='".$mem_id."' AND (status='finished' OR status='partial')";
		mysql_query($del_q);
		return true;
	}
	/************************************************************************************/
	public function admin_get_all_preset_for_deal_type(&$data_arr,&$data_count){
		global $g_mc;
		$q = "select * from ".TP."preset_deal_type";
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
			$row['name'] = $g_mc->db_to_view($row['name']);
			$data_arr[] = $row;
		}
		return true;
	}
	
	public function admin_get_all_preset_for_sector_industry(&$data_arr,&$data_count){
		global $g_mc;
		$q = "select * from ".TP."preset_sector_industry";
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
			$row['name'] = $g_mc->db_to_view($row['name']);
			$data_arr[] = $row;
		}
		return true;
	}
	
	public function admin_get_all_preset_for_country(&$data_arr,&$data_count){
		global $g_mc;
		$q = "select * from ".TP."preset_country";
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
			$row['name'] = $g_mc->db_to_view($row['name']);
			$data_arr[] = $row;
		}
		return true;
	}
	
	public function admin_get_all_preset_for_deal_value(&$data_arr,&$data_count){
		global $g_mc;
		$q = "select * from ".TP."preset_deal_size_value";
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
			$row['name'] = $g_mc->db_to_view($row['name']);
			$data_arr[] = $row;
		}
		return true;
	}
	
	public function admin_get_preset_for_deal_type($preset_id,&$data_arr){
		global $g_mc;
		$q = "select * from ".TP."preset_deal_type where preset_id='".$preset_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$data_count = mysql_num_rows($res);
		if($data_count == 0){
			//no such recs, problem
			return false;
		}
		//recs so
		$data_arr = mysql_fetch_assoc($res);
		$data_arr['name'] = $g_mc->db_to_view($data_arr['name']);
		return true;
	}
	
	public function admin_get_preset_for_sector_industry($preset_id,&$data_arr){
		global $g_mc;
		$q = "select * from ".TP."preset_sector_industry where preset_id='".$preset_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$data_count = mysql_num_rows($res);
		if($data_count == 0){
			//no such recs, problem
			return false;
		}
		//recs so
		$data_arr = mysql_fetch_assoc($res);
		$data_arr['name'] = $g_mc->db_to_view($data_arr['name']);
		return true;
	}
	
	public function admin_get_preset_for_country($preset_id,&$data_arr){
		global $g_mc;
		$q = "select * from ".TP."preset_country where preset_id='".$preset_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$data_count = mysql_num_rows($res);
		if($data_count == 0){
			//no such recs, problem
			return false;
		}
		//recs so
		$data_arr = mysql_fetch_assoc($res);
		$data_arr['name'] = $g_mc->db_to_view($data_arr['name']);
		return true;
	}
	
	public function admin_get_all_preset_for_deal_date(&$data_arr,&$data_count){
		global $g_mc;
		$q = "select * from ".TP."preset_deal_date_value";
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
			$row['name'] = $g_mc->db_to_view($row['name']);
			$data_arr[] = $row;
		}
		return true;
	}
	
	public function admin_get_preset_for_deal_size($preset_id,&$data_arr){
		global $g_mc;
		$q = "select * from ".TP."preset_deal_size_value where preset_id='".$preset_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$data_count = mysql_num_rows($res);
		if($data_count == 0){
			//no such recs, problem
			return false;
		}
		//recs so
		$data_arr = mysql_fetch_assoc($res);
		$data_arr['name'] = $g_mc->db_to_view($data_arr['name']);
		return true;
	}
	
	public function admin_get_preset_for_deal_date($preset_id,&$data_arr){
		global $g_mc;
		$q = "select * from ".TP."preset_deal_date_value where preset_id='".$preset_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$data_count = mysql_num_rows($res);
		if($data_count == 0){
			//no such recs, problem
			return false;
		}
		//recs so
		$data_arr = mysql_fetch_assoc($res);
		$data_arr['name'] = $g_mc->db_to_view($data_arr['name']);
		return true;
	}
	
	public function admin_add_preset_for_deal_type($data_arr,&$validation_passed,&$err_arr){
		global $g_mc;
		
		$validation_passed = true;
		if($data_arr['name']==""){
			$validation_passed = false;
			$err_arr['name'] = "Please specify name";
		}else{
			//check if duplicate
			$q = "select count(*) as cnt from ".TP."preset_deal_type where name='".$g_mc->view_to_db($data_arr['name'])."'";
			$res = mysql_query($q);
			if(!$res){
				return false;
			}
			$row = mysql_fetch_assoc($res);
			if(0==$row['cnt']){
				//not found
			}else{
				//found
				$validation_passed = false;
				$err_arr['name'] = "This name exists";
			}
		}
		if(!$validation_passed){
			//no need to proceed
			return true;
		}
		//all ok
		$q = "insert into ".TP."preset_deal_type set name='".$g_mc->view_to_db($data_arr['name'])."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		//inserted
		return true;
	}
	
	public function admin_add_preset_for_sector_industry($data_arr,&$validation_passed,&$err_arr){
		global $g_mc;
		
		$validation_passed = true;
		if($data_arr['name']==""){
			$validation_passed = false;
			$err_arr['name'] = "Please specify name";
		}else{
			//check if duplicate
			$q = "select count(*) as cnt from ".TP."preset_sector_industry where name='".$g_mc->view_to_db($data_arr['name'])."'";
			$res = mysql_query($q);
			if(!$res){
				return false;
			}
			$row = mysql_fetch_assoc($res);
			if(0==$row['cnt']){
				//not found
			}else{
				//found
				$validation_passed = false;
				$err_arr['name'] = "This name exists";
			}
		}
		if(!$validation_passed){
			//no need to proceed
			return true;
		}
		//all ok
		$q = "insert into ".TP."preset_sector_industry set name='".$g_mc->view_to_db($data_arr['name'])."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		//inserted
		return true;
	}
	
	public function admin_add_preset_for_country($data_arr,&$validation_passed,&$err_arr){
		global $g_mc;
		
		$validation_passed = true;
		if($data_arr['name']==""){
			$validation_passed = false;
			$err_arr['name'] = "Please specify name";
		}else{
			//check if duplicate
			$q = "select count(*) as cnt from ".TP."preset_country where name='".$g_mc->view_to_db($data_arr['name'])."'";
			$res = mysql_query($q);
			if(!$res){
				return false;
			}
			$row = mysql_fetch_assoc($res);
			if(0==$row['cnt']){
				//not found
			}else{
				//found
				$validation_passed = false;
				$err_arr['name'] = "This name exists";
			}
		}
		if(!$validation_passed){
			//no need to proceed
			return true;
		}
		//all ok
		$q = "insert into ".TP."preset_country set name='".$g_mc->view_to_db($data_arr['name'])."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		//inserted
		return true;
	}
	
	public function admin_add_preset_for_deal_value($data_arr,&$validation_passed,&$err_arr){
		/***
		this will store both name and value since this is not combo
		***/
		global $g_mc;
		
		$validation_passed = true;
		if($data_arr['name']==""){
			$validation_passed = false;
			$err_arr['name'] = "Please specify name";
		}else{
			//check if duplicate
			$q = "select count(*) as cnt from ".TP."preset_deal_size_value where name='".$g_mc->view_to_db($data_arr['name'])."'";
			$res = mysql_query($q);
			if(!$res){
				return false;
			}
			$row = mysql_fetch_assoc($res);
			if(0==$row['cnt']){
				//not found
			}else{
				//found
				$validation_passed = false;
				$err_arr['name'] = "This name exists";
			}
		}
		//both the value fields must not be blank
		if(($data_arr['from_billion']=="")&&($data_arr['to_billion']=="")){
			$validation_passed = false;
			$err_arr['from_billion'] = "At least one value must be specified";
		}
		//if both specified, the To must be greater than From
		if(($data_arr['from_billion']!="")&&($data_arr['to_billion']!="")){
			if($data_arr['to_billion'] <= $data_arr['from_billion']){
				$validation_passed = false;
				$err_arr['to_billion'] = "This must be greater than From";
			}
		}
		if(!$validation_passed){
			//no need to proceed
			return true;
		}
		//all ok
		$q = "insert into ".TP."preset_deal_size_value set name='".$g_mc->view_to_db($data_arr['name'])."',from_billion='".$data_arr['from_billion']."',to_billion='".$data_arr['to_billion']."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		//inserted
		return true;
	}
	
	public function admin_add_preset_for_deal_date($data_arr,&$validation_passed,&$err_arr){
		/***
		this will store both name and value since this is not combo
		***/
		global $g_mc;
		
		$validation_passed = true;
		if($data_arr['name']==""){
			$validation_passed = false;
			$err_arr['name'] = "Please specify name";
		}else{
			//check if duplicate
			$q = "select count(*) as cnt from ".TP."preset_deal_date_value where name='".$g_mc->view_to_db($data_arr['name'])."'";
			$res = mysql_query($q);
			if(!$res){
				return false;
			}
			$row = mysql_fetch_assoc($res);
			if(0==$row['cnt']){
				//not found
			}else{
				//found
				$validation_passed = false;
				$err_arr['name'] = "This name exists";
			}
		}
		//From must be specified
		if($data_arr['date_from']==""){
			$validation_passed = false;
			$err_arr['date_from'] = "Please specify date from";
		}
		//To can be blank
		
		
		//if both specified, the To must be greater than From
		if(($data_arr['date_from']!="")&&($data_arr['date_to']!="")){
			if($data_arr['date_to'] <= $data_arr['date_from']){
				$validation_passed = false;
				$err_arr['date_to'] = "This must be greater than From";
			}
		}
		if(!$validation_passed){
			//no need to proceed
			return true;
		}
		//all ok
		$q = "insert into ".TP."preset_deal_date_value set name='".$g_mc->view_to_db($data_arr['name'])."',date_from='".$data_arr['date_from']."',date_to='".$data_arr['date_to']."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		//inserted
		return true;
	}
	
	public function admin_edit_preset_for_deal_type($preset_id,$data_arr,&$validation_passed,&$err_arr){
		global $g_mc;
		
		$validation_passed = true;
		if($data_arr['name']==""){
			$validation_passed = false;
			$err_arr['name'] = "Please specify name";
		}else{
			//check if duplicate ,other than this
			$q = "select count(*) as cnt from ".TP."preset_deal_type where name='".$g_mc->view_to_db($data_arr['name'])."' and preset_id!='".$preset_id."'";
			$res = mysql_query($q);
			if(!$res){
				return false;
			}
			$row = mysql_fetch_assoc($res);
			if(0==$row['cnt']){
				//not found
			}else{
				//found
				$validation_passed = false;
				$err_arr['name'] = "This name exists";
			}
		}
		if(!$validation_passed){
			//no need to proceed
			return true;
		}
		//all ok
		$q = "update ".TP."preset_deal_type set name='".$g_mc->view_to_db($data_arr['name'])."' where preset_id='".$preset_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		//inserted
		return true;
	}
	
	public function admin_edit_preset_for_sector_industry($preset_id,$data_arr,&$validation_passed,&$err_arr){
		global $g_mc;
		
		$validation_passed = true;
		if($data_arr['name']==""){
			$validation_passed = false;
			$err_arr['name'] = "Please specify name";
		}else{
			//check if duplicate ,other than this
			$q = "select count(*) as cnt from ".TP."preset_sector_industry where name='".$g_mc->view_to_db($data_arr['name'])."' and preset_id!='".$preset_id."'";
			$res = mysql_query($q);
			if(!$res){
				return false;
			}
			$row = mysql_fetch_assoc($res);
			if(0==$row['cnt']){
				//not found
			}else{
				//found
				$validation_passed = false;
				$err_arr['name'] = "This name exists";
			}
		}
		if(!$validation_passed){
			//no need to proceed
			return true;
		}
		//all ok
		$q = "update ".TP."preset_sector_industry set name='".$g_mc->view_to_db($data_arr['name'])."' where preset_id='".$preset_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		//inserted
		return true;
	}
	
	public function admin_edit_preset_for_country($preset_id,$data_arr,&$validation_passed,&$err_arr){
		global $g_mc;
		
		$validation_passed = true;
		if($data_arr['name']==""){
			$validation_passed = false;
			$err_arr['name'] = "Please specify name";
		}else{
			//check if duplicate ,other than this
			$q = "select count(*) as cnt from ".TP."preset_country where name='".$g_mc->view_to_db($data_arr['name'])."' and preset_id!='".$preset_id."'";
			$res = mysql_query($q);
			if(!$res){
				return false;
			}
			$row = mysql_fetch_assoc($res);
			if(0==$row['cnt']){
				//not found
			}else{
				//found
				$validation_passed = false;
				$err_arr['name'] = "This name exists";
			}
		}
		if(!$validation_passed){
			//no need to proceed
			return true;
		}
		//all ok
		$q = "update ".TP."preset_country set name='".$g_mc->view_to_db($data_arr['name'])."' where preset_id='".$preset_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		//inserted
		return true;
	}
	
	public function admin_edit_preset_for_deal_size($preset_id,$data_arr,&$validation_passed,&$err_arr){
		global $g_mc;
		
		$validation_passed = true;
		if($data_arr['name']==""){
			$validation_passed = false;
			$err_arr['name'] = "Please specify name";
		}else{
			//check if duplicate ,other than this
			$q = "select count(*) as cnt from ".TP."preset_deal_size_value where name='".$g_mc->view_to_db($data_arr['name'])."' and preset_id!='".$preset_id."'";
			$res = mysql_query($q);
			if(!$res){
				return false;
			}
			$row = mysql_fetch_assoc($res);
			if(0==$row['cnt']){
				//not found
			}else{
				//found
				$validation_passed = false;
				$err_arr['name'] = "This name exists";
			}
		}
		if(!$validation_passed){
			//no need to proceed
			return true;
		}
		//all ok
		/***************
		sng:17/nov/2010
		admin can now mark this as primary, so we need to update that field
		it is a checkbox
		**************/
		if(isset($data_arr['is_primary'])&&('Y'==$data_arr['is_primary'])){
			$is_primary = 'Y';
		}else{
			$is_primary = 'N';
		}
		$q = "update ".TP."preset_deal_size_value set name='".$g_mc->view_to_db($data_arr['name'])."',is_primary='".$is_primary."' where preset_id='".$preset_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		//inserted
		return true;
	}
	
	public function admin_edit_preset_for_deal_date($preset_id,$data_arr,&$validation_passed,&$err_arr){
		global $g_mc;
		
		$validation_passed = true;
		if($data_arr['name']==""){
			$validation_passed = false;
			$err_arr['name'] = "Please specify name";
		}else{
			//check if duplicate. other than this
			$q = "select count(*) as cnt from ".TP."preset_deal_date_value where name='".$g_mc->view_to_db($data_arr['name'])."' and preset_id!='".$preset_id."'";
			$res = mysql_query($q);
			if(!$res){
				return false;
			}
			$row = mysql_fetch_assoc($res);
			if(0==$row['cnt']){
				//not found
			}else{
				//found
				$validation_passed = false;
				$err_arr['name'] = "This name exists";
			}
		}
		//From must be specified
		if($data_arr['date_from']==""){
			$validation_passed = false;
			$err_arr['date_from'] = "Please specify date from";
		}
		//To can be blank
		
		
		//if both specified, the To must be greater than From
		if(($data_arr['date_from']!="")&&($data_arr['date_to']!="")){
			if($data_arr['date_to'] <= $data_arr['date_from']){
				$validation_passed = false;
				$err_arr['date_to'] = "This must be greater than From";
			}
		}
		if(!$validation_passed){
			//no need to proceed
			return true;
		}
		//all ok
		/***************
		sng:17/nov/2010
		admin can now mark this as primary, so we need to update that field
		it is a checkbox
		**************/
		if(isset($data_arr['is_primary'])&&('Y'==$data_arr['is_primary'])){
			$is_primary = 'Y';
		}else{
			$is_primary = 'N';
		}
		$q = "update ".TP."preset_deal_date_value set name='".$g_mc->view_to_db($data_arr['name'])."',date_from='".$data_arr['date_from']."',date_to='".$data_arr['date_to']."',is_primary='".$is_primary."' where preset_id='".$preset_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		//updated
		return true;
	}
	
	public function admin_get_preset_values_for_deal_type($preset_id,&$data_arr,&$data_count){
		global $g_mc;
		$q = "select * from ".TP."preset_deal_type_value where preset_id='".$preset_id."' order by type, subtype1, subtype2";
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
			$data_arr[] = $row;
		}
		return true;
	}
	
	public function admin_get_preset_values_for_sector_industry($preset_id,&$data_arr,&$data_count){
		global $g_mc;
		$q = "select * from ".TP."preset_sector_industry_value where preset_id='".$preset_id."' order by sector, industry";
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
			$data_arr[] = $row;
		}
		return true;
	}
	
	public function admin_get_preset_values_for_country($preset_id,&$data_arr,&$data_count){
		global $g_mc;
		$q = "select p.*,c.name from ".TP."preset_country_value as p left join ".TP."country_master as c on(p.country_id=c.id) where preset_id='".$preset_id."' order by name";
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
			$data_arr[] = $row;
		}
		return true;
	}
	
	public function admin_add_preset_value_for_deal_type($preset_id,$data_arr,&$validation_passed,&$err_arr){
		$validation_passed = true;
		if($data_arr['type']==""){
			$validation_passed = false;
			$err_arr['type'] = "specify type";
		}
		
		/***
		subtype1 and subtype2 are optional. However if subtype2 is specified, subtype1 has to be specified
		**/
		if($data_arr['subtype2']!=""){
			if($data_arr['subtype1']==""){
				$validation_passed = false;
				$err_arr['subtype2'] = "You need to specify subtype1 first";
			}
		}
		if(!$validation_passed){
			return true;
		}
		//basic validation passed, check for duplicate
		$q = "select count(*) as cnt from ".TP."preset_deal_type_value where type='".$data_arr['type']."' and subtype1='".$data_arr['subtype1']."' and subtype2='".$data_arr['subtype2']."' and preset_id='".$preset_id."'";
		$res = mysql_query($q);
		if(!$res){
			//echo mysql_error();
			return false;
		}
		$row = mysql_fetch_assoc($res);
		if($row['cnt']!=0){
			$validation_passed = false;
			$err_arr['type'] = "These type/subtype are already there for this preset";
		}
		
		//////////////////////////////////////////////////////////////////
		if(!$validation_passed){
			return true;
		}
		///////////////////////////////
		//insert
		$q = "insert into ".TP."preset_deal_type_value set preset_id='".$preset_id."', type='".$data_arr['type']."', subtype1='".$data_arr['subtype1']."', subtype2='".$data_arr['subtype2']."'";
		$result = mysql_query($q);
		if(!$result){
			return false;
		}
		return true;
	}
	
	public function admin_add_preset_value_for_sector_industry($preset_id,$data_arr,&$validation_passed,&$err_arr){
		$validation_passed = true;
		if($data_arr['sector']==""){
			$validation_passed = false;
			$err_arr['type'] = "specify sector";
		}
		
		if(!$validation_passed){
			return true;
		}
		//basic validation passed, check for duplicate
		$q = "select count(*) as cnt from ".TP."preset_sector_industry_value where sector='".$data_arr['sector']."' and industry='".$data_arr['industry']."' and preset_id='".$preset_id."'";
		$res = mysql_query($q);
		if(!$res){
			//echo mysql_error();
			return false;
		}
		$row = mysql_fetch_assoc($res);
		if($row['cnt']!=0){
			$validation_passed = false;
			$err_arr['type'] = "These sector/industry are already there for this preset";
		}
		
		//////////////////////////////////////////////////////////////////
		if(!$validation_passed){
			return true;
		}
		///////////////////////////////
		//insert
		$q = "insert into ".TP."preset_sector_industry_value set preset_id='".$preset_id."', sector='".$data_arr['sector']."', industry='".$data_arr['industry']."'";
		$result = mysql_query($q);
		if(!$result){
			return false;
		}
		return true;
	}
	
	public function admin_add_preset_value_for_country($preset_id,$data_arr,&$validation_passed,&$err_arr){
		$validation_passed = true;
		if($data_arr['country_id']==""){
			$validation_passed = false;
			$err_arr['country_id'] = "specify country";
		}
		
		if(!$validation_passed){
			return true;
		}
		//basic validation passed, check for duplicate
		$q = "select count(*) as cnt from ".TP."preset_country_value where country_id='".$data_arr['country_id']."' and preset_id='".$preset_id."'";
		$res = mysql_query($q);
		if(!$res){
			//echo mysql_error();
			return false;
		}
		$row = mysql_fetch_assoc($res);
		if($row['cnt']!=0){
			$validation_passed = false;
			$err_arr['type'] = "The country is already there for this preset";
		}
		
		//////////////////////////////////////////////////////////////////
		if(!$validation_passed){
			return true;
		}
		///////////////////////////////
		//insert
		$q = "insert into ".TP."preset_country_value set preset_id='".$preset_id."', country_id='".$data_arr['country_id']."'";
		$result = mysql_query($q);
		if(!$result){
			return false;
		}
		return true;
	}
	
	public function admin_delete_preset_value_for_deal_type($value_id){
		$q = "delete from ".TP."preset_deal_type_value where id='".$value_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		return true;
	}
	
	public function admin_delete_preset_value_for_sector_industry($value_id){
		$q = "delete from ".TP."preset_sector_industry_value where id='".$value_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		return true;
	}
	
	public function admin_delete_preset_value_for_country($value_id){
		$q = "delete from ".TP."preset_country_value where id='".$value_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		return true;
	}
	
	public function admin_delete_preset_value_for_deal_value($preset_id){
		/***
		here the name and value are in single table since these are not combo
		********/
		$q = "delete from ".TP."preset_deal_size_value where preset_id='".$preset_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		return true;
	}
	public function admin_delete_preset_value_for_deal_date($preset_id){
		/***
		here the name and value are in single table since these are not combo
		********/
		$q = "delete from ".TP."preset_deal_date_value where preset_id='".$preset_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		return true;
	}
	/******************************************************************************/
	public function admin_get_all_top_search_option_for_country(&$data_arr,&$data_count){
		return $this->admin_get_all_top_search_option("country",$data_arr,$data_count);
	}
	public function front_get_all_top_search_option_for_country(&$data_arr,&$data_count){
		return $this->front_get_all_top_search_option("country",$data_arr,$data_count);
	}
	public function admin_get_all_top_search_option_for_sector_industry(&$data_arr,&$data_count){
		return $this->admin_get_all_top_search_option("sector_industry",$data_arr,$data_count);
	}
	public function front_get_all_top_search_option_for_sector_industry(&$data_arr,&$data_count){
		return $this->front_get_all_top_search_option("sector_industry",$data_arr,$data_count);
	}
	public function admin_get_all_top_search_option_for_deal_type(&$data_arr,&$data_count){
		return $this->admin_get_all_top_search_option("deal_type",$data_arr,$data_count);
	}
	public function front_get_all_top_search_option_for_deal_type(&$data_arr,&$data_count){
		return $this->front_get_all_top_search_option("deal_type",$data_arr,$data_count);
	}
	///////////
	public function admin_get_top_search_option_for_country($option_id,&$data_arr){
		return $this->admin_get_top_search_option("country",$option_id,$data_arr);
	}
	public function admin_get_top_search_option_for_sector_industry($option_id,&$data_arr){
		return $this->admin_get_top_search_option("sector_industry",$option_id,$data_arr);
	}
	public function admin_get_top_search_option_for_deal_type($option_id,&$data_arr){
		return $this->admin_get_top_search_option("deal_type",$option_id,$data_arr);
	}
	//////////////////
	public function admin_add_top_search_option_for_country($data_arr,&$validation_passed,&$err_arr){
		return $this->admin_add_top_search_option("country",$data_arr,$validation_passed,$err_arr);
	}
	public function admin_add_top_search_option_for_sector_industry($data_arr,&$validation_passed,&$err_arr){
		return $this->admin_add_top_search_option("sector_industry",$data_arr,$validation_passed,$err_arr);
	}
	public function admin_add_top_search_option_for_deal_type($data_arr,&$validation_passed,&$err_arr){
		return $this->admin_add_top_search_option("deal_type",$data_arr,$validation_passed,$err_arr);
	}
	///////////////
	public function admin_edit_top_search_option_for_country($option_id,$data_arr,&$validation_passed,&$err_arr){
		return $this->admin_edit_top_search_option("country",$option_id,$data_arr,$validation_passed,$err_arr);
	}
	public function admin_edit_top_search_option_for_sector_industry($option_id,$data_arr,&$validation_passed,&$err_arr){
		return $this->admin_edit_top_search_option("sector_industry",$option_id,$data_arr,$validation_passed,$err_arr);
	}
	public function admin_edit_top_search_option_for_deal_type($option_id,$data_arr,&$validation_passed,&$err_arr){
		return $this->admin_edit_top_search_option("deal_type",$option_id,$data_arr,$validation_passed,$err_arr);
	}
	////////////////
	public function admin_get_top_search_option_mapping_for_country($option_id,&$data_arr,&$data_count){
		return $this->admin_get_top_search_option_mapping("country",$option_id,$data_arr,$data_count);
	}
	public function admin_get_top_search_option_mapping_for_sector_industry($option_id,&$data_arr,&$data_count){
		return $this->admin_get_top_search_option_mapping("sector_industry",$option_id,$data_arr,$data_count);
	}
	public function admin_get_top_search_option_mapping_for_deal_type($option_id,&$data_arr,&$data_count){
		return $this->admin_get_top_search_option_mapping("deal_type",$option_id,$data_arr,$data_count);
	}
	///////////////////
	public function admin_add_top_search_option_mapping_for_country($option_id,$data_arr,&$validation_passed,&$err_arr){
		return $this->admin_add_top_search_option_mapping("country",$option_id,$data_arr,$validation_passed,$err_arr);
	}
	public function admin_add_top_search_option_mapping_for_sector_industry($option_id,$data_arr,&$validation_passed,&$err_arr){
		return $this->admin_add_top_search_option_mapping("sector_industry",$option_id,$data_arr,$validation_passed,$err_arr);
	}
	public function admin_add_top_search_option_mapping_for_deal_type($option_id,$data_arr,&$validation_passed,&$err_arr){
		return $this->admin_add_top_search_option_mapping("deal_type",$option_id,$data_arr,$validation_passed,$err_arr);
	}
	/////////////////
	public function admin_delete_top_search_option_mapping_for_country($id){
		return $this->admin_delete_top_search_option_mapping("country",$id);
	}
	public function admin_delete_top_search_option_mapping_for_sector_industry($id){
		return $this->admin_delete_top_search_option_mapping("sector_industry",$id);
	}
	public function admin_delete_top_search_option_mapping_for_deal_type($id){
		return $this->admin_delete_top_search_option_mapping("deal_type",$id);
	}
	/**********************************************************************************/
	private function admin_get_all_top_search_option($option_name,&$data_arr,&$data_count){
		global $g_mc;
		if($option_name == "country") $table = "top_search_option_country";
		if($option_name == "sector_industry") $table = "top_search_option_sector_industry";
		if($option_name == "deal_type") $table = "top_search_option_deal_type";
		
		/***********************
		sng:20/july/2011
		Now that we are grouping the items, we use that to order
		*********************/
		$q = "select * from ".TP.$table." order by group_name,display_order,name";
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
			$row['name'] = $g_mc->db_to_view($row['name']);
			$data_arr[] = $row;
		}
		return true;
	}
	
	/***
	sng:9/sep/2010
	We introduced a display order so that admin can group the options
	***/
	private function front_get_all_top_search_option($option_name,&$data_arr,&$data_count){
		global $g_mc;
		if($option_name == "country") $table = "top_search_option_country";
		if($option_name == "sector_industry") $table = "top_search_option_sector_industry";
		if($option_name == "deal_type") $table = "top_search_option_deal_type";
		
		/********************
		sng:20/july/2011
		Now we group by group_name
		************************/
		$q = "select * from ".TP.$table." order by group_name,display_order,name";
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
			$row['name'] = $g_mc->db_to_view($row['name']);
			$data_arr[] = $row;
		}
		return true;
	}
	
	private function admin_get_top_search_option($option_name,$option_id,&$data_arr){
		global $g_mc;
		if($option_name == "country") $table = "top_search_option_country";
		if($option_name == "sector_industry") $table = "top_search_option_sector_industry";
		if($option_name == "deal_type") $table = "top_search_option_deal_type";
		
		
		$q = "select * from ".TP.$table." where option_id='".$option_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$data_count = mysql_num_rows($res);
		if($data_count == 0){
			//no such recs, problem
			return false;
		}
		//recs so
		$data_arr = mysql_fetch_assoc($res);
		$data_arr['name'] = $g_mc->db_to_view($data_arr['name']);
		return true;
	}
	
	private function admin_add_top_search_option($option_name,$data_arr,&$validation_passed,&$err_arr){
		global $g_mc;
		
		if($option_name == "country") $table = "top_search_option_country";
		if($option_name == "sector_industry") $table = "top_search_option_sector_industry";
		if($option_name == "deal_type") $table = "top_search_option_deal_type";
		
		
		$validation_passed = true;
		if($data_arr['name']==""){
			$validation_passed = false;
			$err_arr['name'] = "Please specify name";
		}else{
			//check if duplicate
			$q = "select count(*) as cnt from ".TP.$table." where name='".$g_mc->view_to_db($data_arr['name'])."'";
			$res = mysql_query($q);
			if(!$res){
				return false;
			}
			$row = mysql_fetch_assoc($res);
			if(0==$row['cnt']){
				//not found
			}else{
				//found
				$validation_passed = false;
				$err_arr['name'] = "This name exists";
			}
		}
		if(!$validation_passed){
			//no need to proceed
			return true;
		}
		//all ok
		$q = "insert into ".TP.$table." set name='".$g_mc->view_to_db($data_arr['name'])."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		//inserted
		return true;
	}
	
	/***
	sng:9/sep/2010
	Added support for display order
	***/
	private function admin_edit_top_search_option($option_name,$option_id,$data_arr,&$validation_passed,&$err_arr){
		global $g_mc;
		if($option_name == "country") $table = "top_search_option_country";
		if($option_name == "sector_industry") $table = "top_search_option_sector_industry";
		if($option_name == "deal_type") $table = "top_search_option_deal_type";
		
		
		$validation_passed = true;
		if($data_arr['name']==""){
			$validation_passed = false;
			$err_arr['name'] = "Please specify name";
		}else{
			//check if duplicate ,other than this
			$q = "select count(*) as cnt from ".TP.$table." where name='".$g_mc->view_to_db($data_arr['name'])."' and option_id!='".$option_id."'";
			$res = mysql_query($q);
			if(!$res){
				return false;
			}
			$row = mysql_fetch_assoc($res);
			if(0==$row['cnt']){
				//not found
			}else{
				//found
				$validation_passed = false;
				$err_arr['name'] = "This name exists";
			}
		}
		if(($data_arr['display_order']=="")||($data_arr['display_order']==0)){
			$validation_passed = false;
			$err_arr['display_order'] = "Please specify order";
		}
		if(!$validation_passed){
			//no need to proceed
			return true;
		}
		//all ok
		/*******************
		sng:20/july/2011
		support for group name
		**************************/
		
		$q = "update ".TP.$table." set name='".$g_mc->view_to_db($data_arr['name'])."',group_name='".mysql_real_escape_string($data_arr['group_name'])."',display_order='".$data_arr['display_order']."' where option_id='".$option_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		//inserted
		return true;
	}
	
	private function admin_get_top_search_option_mapping($option_name,$option_id,&$data_arr,&$data_count){
		global $g_mc;
		
		if($option_name == "country"){
			$table = "top_search_option_country_preset_mapping";
			$preset_table = "preset_country";
		}
		if($option_name == "sector_industry"){
			$table = "top_search_option_sector_industry_preset_mapping";
			$preset_table = "preset_sector_industry";
		}
		if($option_name == "deal_type"){
			$table = "top_search_option_deal_type_preset_mapping";
			$preset_table = "preset_deal_type";
		}
		
		
		$q = "select m.*,p.name from ".TP.$table." as m left join ".TP.$preset_table." as p on(m.preset_id=p.preset_id) where m.option_id='".$option_id."'";
		$res = mysql_query($q);
		if(!$res){
			//echo mysql_error();
			return false;
		}
		$data_count = mysql_num_rows($res);
		if(0==$data_count){
			return true;
		}
		while($row = mysql_fetch_assoc($res)){
			$row['name'] = $g_mc->db_to_view($row['name']);
			$data_arr[] = $row;
		}
		return true;
	}
	
	private function admin_add_top_search_option_mapping($option_name,$option_id,$data_arr,&$validation_passed,&$err_arr){
		global $g_mc;
		if($option_name == "country") $table = "top_search_option_country_preset_mapping";
		if($option_name == "sector_industry") $table = "top_search_option_sector_industry_preset_mapping";
		if($option_name == "deal_type") $table = "top_search_option_deal_type_preset_mapping";
		
		//validation
		$validation_passed = true;
		if($data_arr['preset_id']==""){
			$validation_passed = false;
			$err_arr['preset_id'] = "Please select a preset";
		}else{
			//check if this preset is there for this option
			$q = "select count(*) as cnt from ".TP.$table." where option_id='".$option_id."' and preset_id='".$data_arr['preset_id']."'";
			$res = mysql_query($q);
			if(!$res){
				return false;
			}
			$row = mysql_fetch_assoc($res);
			if($row['cnt'] > 0){
				//present
				$validation_passed = false;
				$err_arr['preset_id'] = "This preset has already been selected";
			}
		}
		
		if(!$validation_passed){
			return true;
		}
		//insert
		$q = "insert into ".TP.$table." set option_id='".$option_id."', preset_id='".$data_arr['preset_id']."', is_primary='".$data_arr['is_primary']."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		return true;
	}
	
	private function admin_delete_top_search_option_mapping($option_name,$id){
		if($option_name == "country") $table = "top_search_option_country_preset_mapping";
		if($option_name == "sector_industry") $table = "top_search_option_sector_industry_preset_mapping";
		if($option_name == "deal_type") $table = "top_search_option_deal_type_preset_mapping";
		
		$q = "delete from ".TP.$table." where id='".$id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		return true;
	}
}
$g_preset = new preset();
?>