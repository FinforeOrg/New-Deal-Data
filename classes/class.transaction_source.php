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
	
	/*****************************
	sng:2/may/2012
	In case a new source url is specified during edit. We add the url to the list of deal sources
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