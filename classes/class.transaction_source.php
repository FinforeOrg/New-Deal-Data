<?php
/**************************
sng:30/apr/2012
We use this class to manage the source URLs associated with a deal
******************/
class transaction_source{
	
	public function get_deal_sources($deal_id,&$data_arr){
		$db = new db();
		$q = "select * from ".TP."transaction_sources where transaction_id='".$deal_id."'";
		
		$ok = $db->select_query($q);
		if(!$ok){
			return false;
		}
		if(!$db->has_row()){
			//no sources specified
			return true;
		}
		$data_arr = $db->get_result_set_as_array();
		return true;
	}
	/***********************************************************
	We put the code to associate one or more URLs with a deal from the front end (called
	when we add a deal)
	
	The urls are sent as a simple array.
	It may happen that the array element is blank
	
	We use a lookup array to check whether same url has been sent twice or not
	Call this ONLY when you are ADDING a deal. Otherwise do not call
	*****************************/
	public function front_set_sources_for_deal($deal_id,$sources,$suggestion_mem_id,$deal_add_date_time){
		
		
		$db = new db();
		$q = "";
		
		$suggestion_data_arr = array();
		/****************
		lookup array to store the URLS specified
		****************/
		$specified_urls = array();
		
		$url_count = count($sources);
		for($url_i=0;$url_i<$url_count;$url_i++){
			$source_url = $sources[$url_i];
			if($source_url!=""){
				/*******************
				url given, so first check if it has already been suggested or not
				******************/
				if(array_search($source_url,$specified_urls)!==false){
					/**************
					already processed, ignore
					************/
					continue;
				}else{
					/***********
					add to array
					***********/
					$specified_urls[] = $source_url;
				}
				/********************************/
				$q.=",('".$deal_id."','".mysql_real_escape_string($source_url)."')";
				/****************
				Need to prepare the suggestion array to log the submission of sources for the deal
				*****************/
				$suggestion_data_arr[] = array('source_url'=>$source_url);
			}else{
				//url is blank, ignore
			}
		}
		if($q!=""){
			$q = substr($q,1);
		}
		$q = "insert into ".TP."transaction_sources (transaction_id,source_url) values ".$q;
		$ok = $db->mod_query($q);
		if(!$ok){
			return false;
		}else{
			/*****************
			Part of suggestion tracking. When we add sources with deal submission, we also record the fact in the suggestion table
			******************/
			require_once("classes/class.transaction_suggestion.php");
			$trans_suggestion = new transaction_suggestion();
			$trans_suggestion->deal_source_added_via_deal_submission($deal_id,$suggestion_mem_id,$deal_add_date_time,$suggestion_data_arr);
			return true;
		}
	}
	/******************
	sng:6/oct/2012
	When admin add any sources, those gets added to the deal right away, and we send a notification so that the submission can be stored as 'suggestion'.
	That way, we can show the 'history'
	
	Admin can specify one one or more URLs
	
	We need to do some validation though:
	1) The suggested source url cannot be blank
	2) The suggested source url must not be a duplicate
	3) It must not be there in the current sources for the deal
	***/
	public function admin_add_sources_for_deal($deal_id,$sources,&$validation_passed,&$msg){
		$db = new db();
		$validation_passed = true;
		
		$link_cnt = count($sources);
		if(0 == $link_cnt){
			$validation_passed = false;
			$msg = "No sources specified";
			return true;
		}
		/**********************************************/
		$has_source = false;
		for($source_i=0;$source_i<$link_cnt;$source_i++){
			/**********************
			it may happen that the items are all blank
			************/
			if($sources[$source_i]!=""){
				$has_source = true;
				break;
			}
		}
		if(!$has_source){
			$validation_passed = false;
			$msg = "Please specify at least one source URL";
			return true;
		}
		/************************************************************************
		Now we get the list of sources currently associated with the deal
		and create our lookup array
		****************/
		$current_sources_arr = NULL;
		$current_sources_count = 0;
		$lookup_current_sources = array();
		
		$ok = $this->get_deal_sources($deal_id,$current_sources_arr);
		if(!$ok){
			return false;
		}
		$current_sources_count = count($current_sources_arr);
		for($i=0;$i<$current_sources_count;$i++){
			$lookup_current_sources[] = $current_sources_arr[$i]['source_url'];
		}
		/********************************************************************************************
		lookup array to remember the source urls submitted and processed. This to ensure that
		in the submission the duplicates are filtered out
		***************/
		$lookup_submitted_sources = array();
		$add_q = "";
		
		
		for($source_i=0;$source_i<$link_cnt;$source_i++){
			$source_url = $sources[$source_i];
			if($source_url == ""){
				//blank submission, ignore
				continue;
			}
			if(array_search($source_url,$lookup_current_sources)!==false){
				//this is already stored with the deal so not really a suggestion, ignore
				continue;
			}
			if(array_search($source_url,$lookup_submitted_sources)!==false){
				//this has been processed already so ignore
				continue;
			}
			/******************
			not blank, does not exists currently, not a duplicate of previous suggestion
			remember to populate lookup_submitted_sources
			We also create the addition query
			***********/
			$lookup_submitted_sources[] = $source_url;
			$add_q.=",('".$deal_id."','".mysql_real_escape_string($source_url)."')";
		}
		/*******************************************************
		It may happen that there is nothing to add because all are in current list
		so we check if we have a data to insert
		***********/
		if($add_q == ""){
			$validation_passed = false;
			$msg = "There is no effective suggestion";
			return true;
		}
		/**********************************
		get rid of the first ','
		******/
		$add_q = substr($add_q,1);
		$add_q = "insert into ".TP."transaction_sources(transaction_id,source_url) values ".$add_q;
		$time_now = date("Y-m-d H:i:s");
		
		$ok = $db->mod_query($add_q);
		if(!$ok){
			return false;
		}
		/**********************
		now send the notification
		********************/
		require_once("classes/class.transaction_suggestion.php");
		$trans_suggest = new transaction_suggestion();
		$ok = $trans_suggest->deal_sources_added_via_admin($deal_id,0,$time_now,$lookup_submitted_sources);
		/*********
		never mind if error
		**********/
		$validation_passed = true;
        return true;
	}
	/************
	sng:6/oct/2012
	When admin delete one or more sources, those are deleted. The corresponding suggestions do not change their status.
	We also collect the sources and create a new 'suggestion' with status 'deleted' by admin
	
	Check the source_id_arr. If we use checkbox, it may not be set.
	It could also be blank.
	***********/
	public function admin_delete_sources_for_deal($deal_id,$source_id_arr,&$validation_passed,&$msg){
		$db = new db();
		
		$validation_passed = true;
		
		if(!isset($source_id_arr)){
			$validation_passed = false;
			$msg = "No sources selected, nothing to delete";
			return true;
		}
		$source_count = count($source_id_arr);
		if(0==$source_count){
			$validation_passed = false;
			$msg = "No sources selected, nothing to delete";
			return true;
		}
		/**************
		So we have some source ids
		Before we delete, we need to get the source URL so that we can create the suggestion (after the deletion is successfull)
		***************/
		$sources_removed = array();
		
		for($i=0;$i<$source_count;$i++){
		
			$source_id = $source_id_arr[$i];
			
			$q = "select source_url from ".TP."transaction_sources where id='".$source_id."' and transaction_id='".$deal_id."'";
			$ok = $db->select_query($q);
			if(!$ok){
				return false;
			}
			if(!$db->has_row()){
				//ignore
				continue;
			}
			$curr_source_row = $db->get_row();
			$curr_source_url = $curr_source_row['source_url'];
			//try to delete
			$del_q = "delete from ".TP."transaction_sources where id='".$source_id."' and transaction_id='".$deal_id."'";
			$ok = $db->mod_query($del_q);
			if(!$ok){
				return false;
			}
			//deleted, add to suggestion list
			$sources_removed[] = $curr_source_url;
		}
		/*************
		Now notify
		**************/
		require_once("classes/class.transaction_suggestion.php");
		$trans_suggestion = new transaction_suggestion();
		$ok = $trans_suggestion->deal_sources_removed_via_admin($deal_id,$sources_removed);
		/*********
		never mind if not ok
		************/
		return true;
	}
	
	/*****************************
	sng:2/may/2012
	In case a new source url is specified during edit. We add the url to the list of deal sources
	
	Note: This is called by transaction_suggestion::front_submit_sources()
	The workflow is - we first save the 'source url' as suggestion and then add to the sources for the deal. That is why
	we do not store this again as 'suggestion'.
	
	This means - DO NOT CALL THIS DIRECTLY. GO VIA SUGGESTION FIRST
	*********************/
	public function front_add_source_for_deal($deal_id,$source_url,&$validation_passed,&$err_arr){
		$db = new db();
		
		$validation_passed = true;
		if($source_url==""){
			$validation_passed = false;
			$err_arr['source_url'] = "Please specify" ;
		}
		
		if(!$validation_passed){
			return true;
		}
		/***********************
		now check if the source is in the deal or not
		***************************/
		$q = "select count(*) as cnt from ".TP."transaction_sources where transaction_id='".$deal_id."' and source_url='".$source_url."'";
		$ok = $db->select_query($q);
		if(!$ok){
			return false;
		}
		$row = $db->get_row();
		if($row['cnt']>0){
			//already added to this deal
			$validation_passed = false;
			$err_arr['source_url'] = "Already added";
		}
		if(!$validation_passed){
			return true;
		}
		/******************
		now add
		******************/
		$q = "insert into ".TP."transaction_sources set transaction_id='".$deal_id."', source_url='".mysql_real_escape_string($source_url)."'";
		$ok = $db->mod_query($q);
		if(!$ok){
			return false;
		}
		return true;
	}
}
?>