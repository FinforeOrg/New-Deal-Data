<?php
/***
sng:21/aug/2010
This one contains methods that are relevant in Make Me Top feature
This class may use other classes as required
****/
class make_me_top{
	
	/***
	fetch a single pending request. We only fetch the job id because processing will be done in
	background and that code will use job id to get the detail.
	Chances are, this code will be run in a cron job, so be very careful
	job_id: reference to send job id
	found: reference to sent true if job found, false if no job pending
	return false on error, true otherwise.
	
	sng: 30/aug/2010
	Now we get all pending requests. we also check whether the request has already been scheduled or not. If a request is
	scheduled, the is_scheduled flag is y
	After starting a background process on a request, mark that request as scheduled
	***/
	public function get_all_pending_requests(&$data_arr,&$data_count){
		$q = "select job_id from ".TP."top_search_request where status='pending' and is_scheduled='n' order by submitted_on";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$data_count = mysql_num_rows($res);
		if(0==$data_count){
			//no job found
			return true;
		}
		for($i=0;$i<$data_count;$i++){
			$data_arr[$i] = mysql_fetch_assoc($res);
		}
		return true;
	}
	
	/*****
	4/sep/2010
	We now run a request processing jobs for few mins only and then set it to not running.
	we cannot take pending(yet to be scheduled) or finished(all over) or partial (job terminated with error)
	*********/
	public function get_all_paused_jobs(&$data_arr,&$data_count){
		
		$q = "select h.job_id from ".TP."top_search_request_processing_helper as h left join ".TP."top_search_request as r on(h.job_id=r.job_id) where h.is_running='n' and is_scheduled='y' and status='in progress'";
		$res = mysql_query($q);
		if(!$res){
			//echo mysql_error();
			return false;
		}
		$data_count = mysql_num_rows($res);
		if(0==$data_count){
			//no job found
			return true;
		}
		for($i=0;$i<$data_count;$i++){
			$data_arr[$i] = mysql_fetch_assoc($res);
		}
		return true;
	}
	
	public function request_scheduled($job_id){
		$q = "update ".TP."top_search_request set is_scheduled='y' where job_id='".$job_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		//add to tombstone_top_search_request_processing_helper so that it is there when it is needed
		$q = "insert into ".TP."top_search_request_processing_helper set job_id='".$job_id."', is_running='y'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		return true;
	}
	
	public function resume_job($job_id){
		$q = "update ".TP."top_search_request_processing_helper set is_running='y' where job_id='".$job_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		return true;
	}
	/***
	fetch the specified request
	job_id: id of the request data that is to be fetched
	data_arr: a reference to an array used to pass the request fields
	return true if request found, false if request not found.
	Chances are, this will be called in background task, so return false even in case of db error
	
	sng: 4/sep/2010
	just selected the specific fields from top_search_request
	
	sng: 18/sep/2010
	select the field extended_search
	***/
	public function fetch_request($job_id,&$data_arr){
		$q = "select job_id,mem_id,submitted_on,company_id,type,option_country,option_deal_type,option_sector_industry,rank_requested,extended_search,status,is_scheduled,started_on,dbg_last_processing_time,finished_on,dbg_status from ".TP."top_search_request where job_id='".$job_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$cnt = mysql_num_rows($res);
		if(0==$cnt){
			return false;
		}
		$data_arr = mysql_fetch_assoc($res);
		return true;
	}
	
	public function fetch_request_processing_data($job_id,&$data_arr){
		$q = "select h.country_preset_vector,h.sector_industry_preset_vector,h.deal_type_preset_vector,h.deal_size_preset_vector,h.deal_date_preset_vector,h.ranking_criteria_vector,h.cache,h.offset_permutation_vector,h.next_permutation_offset,h.is_running,r.* from ".TP."top_search_request_processing_helper as h left join ".TP."top_search_request as r on(h.job_id=r.job_id) where h.job_id='".$job_id."'";
		$res = mysql_query($q);
		if(!$res){
			//echo mysql_error();
			return false;
		}
		$cnt = mysql_num_rows($res);
		if(0==$cnt){
			return false;
		}
		$data_arr = mysql_fetch_assoc($res);
		return true;
	}
	
	/***
	notification that a pending job request is about to start. set the status to in progress
	job_id: job id
	no need to notify whether status is updated or not
	
	sng:1/sep/2010
	We store when we are starting on this
	**/
	public function starting_request_processing($job_id){
		$datetime_now = date("Y-m-d H:i:s");
		$q = "update ".TP."top_search_request set status='in progress',started_on='".$datetime_now."' where job_id='".$job_id."'";
		$res = mysql_query($q);
	}
	/***
	notification that a request processing is complete. set the status to finished
	job_id: job id
	no need to notify whether status is updated or not
	
	sng:1/sep/2010
	We store when we have finished
	**/
	public function request_processing_completed($job_id){
		$datetime_now = date("Y-m-d H:i:s");
		$q = "update ".TP."top_search_request set status='finished',finished_on='".$datetime_now."' where job_id='".$job_id."'";
		mysql_query($q);
		$this->request_processing_over($job_id);
	}
	
	public function request_processing_error($job_id,$err_msg){
		$datetime_now = date("Y-m-d H:i:s");
		$q = "update ".TP."top_search_request set status='partial',finished_on='".$datetime_now."' where job_id='".$job_id."'";
		mysql_query($q);
		$q = "insert into ".TP."top_search_request_error set msg='".addslashes($err_msg)."',job_id='".$job_id."'";
		mysql_query($q);
		$this->request_processing_over($job_id);
	}
	
	private function request_processing_over($job_id){
		global $g_mc,$g_view;
		
		//remove the entry from helper table
		$q = "delete from ".TP."top_search_request_processing_helper where job_id='".$job_id."'";
		mysql_query($q);
		/*****
		sng:9/sep/2010
		Notify the member that the job is complete. Send an email
		
		sng:6/oct/2010
		In the email, embed the url of the mmt listing page. That page require login but the code is written so that
		after login the user is taken to mmt listing page
		*********/
		$sender_email = $g_view['site_emails']['contact_email'];
		$headers = "From: ".$sender_email."\r\n";
		$subject = "deal-data.com make me top request processing complete";
		//now get the member detail who submitted the job
		$q = "select submitted_on,rank_requested,f_name,l_name,work_email,opc.name as country_name,opd.name as deal_name,ops.name as sector_name from ".TP."top_search_request as r left join ".TP."member as m on(r.mem_id=m.mem_id) left join ".TP."top_search_option_country as opc on(r.option_country=opc.option_id) left join ".TP."top_search_option_deal_type as opd on (r.option_deal_type=opd.option_id) left join ".TP."top_search_option_sector_industry as ops on(r.option_sector_industry=ops.option_id) where job_id='".$job_id."'";
		$res = mysql_query($q);
		if(!$res){
			return;
		}
		$cnt = mysql_num_rows($res);
		if(0==$cnt){
			return;
		}
		$row = mysql_fetch_assoc($res);
		$to = $row['work_email'];
		$message = $row['f_name']." ".$row['l_name']."\r\n\r\n";
		$message.="The result of your make me top request, submitted on ".$row['submitted_on'].", is now available for your perusal.\r\n";
		$message.="Access it at the url <a href=\"http://www.deal-data.com/make_me_top.php\">http://www.deal-data.com/make_me_top.php</a>\r\n";
		$message.="The request details:\r\n";
		$message.="Country: ".$row['country_name']."\r\n";
		$message.="Deal type: ".$row['deal_name']."\r\n";
		$message.="Sector / Industry: ".$row['sector_name']."\r\n";
		$message.="Rank requested: ".$row['rank_requested']."\r\n";
		/****
		sng:19/oct/2010
		use the mailer class
		****/
		require_once("classes/class.mailer.php");
		$mailer = new mailer();
		$success = $mailer->mail($to,$subject,$message);
		//mail($to,$subject,$message,$headers);
	}
	
	/***
	get all the country preset ids for this country option id
	country_option_id: option id for country
	extended_search: if true, get all preset, never mind wither it primary or not. If false, then get only the presets that are primary
	data_arr: a refernce to send the data
	data_count: number of result found
	return false on db error, true otherwise
	****/
	public function get_all_country_preset_ids($country_option_id,$extended_search,&$data_arr,&$data_count){
		return $this->get_all_preset_ids("country",$country_option_id,$extended_search,$data_arr,$data_count);
	}
	public function get_all_sector_industry_preset_ids($sector_industry_option_id,$extended_search,&$data_arr,&$data_count){
		return $this->get_all_preset_ids("sector_industry",$sector_industry_option_id,$extended_search,$data_arr,$data_count);
	}
	public function get_all_deal_type_preset_ids($deal_type_option_id,$extended_search,&$data_arr,&$data_count){
		return $this->get_all_preset_ids("deal_type",$deal_type_option_id,$extended_search,$data_arr,$data_count);
	}
	
	/***
	for this we do not select any option and there is no concept of is primary.
	We consider all that are in the preset value table
	
	sng:19/nov/2010
	Now we have is primary on this, so need the parameter extended search
	***/
	public function get_all_deal_size_preset_ids(&$data_arr,$extended_search,&$data_count){
		$q = "select preset_id from ".TP."preset_deal_size_value";
		/*******************************************************/
		if(!$extended_search){
			$q.=" where is_primary='Y'";
		}
		/***********************************************/
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$data_count = mysql_num_rows($res);
		if(0==$data_count){
			//no presets for this option, no need to proceed
			return true;
		}
		for($i=0;$i<$data_count;$i++){
			$row = mysql_fetch_assoc($res);
			$data_arr[$i] = $row['preset_id'];
		}
		return true;
	}
	/***
	for this we do not select any option and there is no concept of is primary.
	We consider all that are in the preset value table
	
	sng:19/nov/2010
	Now we have is primary on this, so need the parameter extended search
	***/
	public function get_all_deal_date_preset_ids(&$data_arr,$extended_search,&$data_count){
		$q = "select preset_id from ".TP."preset_deal_date_value";
		/*******************************************************/
		if(!$extended_search){
			$q.=" where is_primary='Y'";
		}
		/***********************************************/
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$data_count = mysql_num_rows($res);
		if(0==$data_count){
			//no presets for this option, no need to proceed
			return true;
		}
		for($i=0;$i<$data_count;$i++){
			$row = mysql_fetch_assoc($res);
			$data_arr[$i] = $row['preset_id'];
		}
		return true;
	}
	
	private function get_all_preset_ids($option_name,$option_id,$extended_search,&$data_arr,&$data_count){
		if($option_name=="country") $table = "top_search_option_country_preset_mapping";
		if($option_name=="sector_industry") $table = "top_search_option_sector_industry_preset_mapping";
		if($option_name=="deal_type") $table = "top_search_option_deal_type_preset_mapping";
		
		$q = "select preset_id from ".TP.$table." where option_id='".$option_id."'";
		if(!$extended_search){
			$q.=" and is_primary='Y'";
		}
		
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$data_count = mysql_num_rows($res);
		if(0==$data_count){
			//no presets for this option, no need to proceed
			return true;
		}
		for($i=0;$i<$data_count;$i++){
			$row = mysql_fetch_assoc($res);
			$data_arr[$i] = $row['preset_id'];
		}
		return true;
	}
	/***
	get the country names for a given country preset id.
	The country names are returned in '' so that those can be used in sql
	the keys in the cache is the preset id
	return false on db error
	**/
	public function get_countries_from_preset($preset_id,&$cache_arr,&$data_count){
		$q = "select name from ".TP."preset_country_value as cp left join ".TP."country_master as cm on(cp.country_id=cm.id) where preset_id='".$preset_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$data_count = mysql_num_rows($res);
		if(0==$data_count){
			return true;
		}
		$data_arr = array();
		for($i=0;$i<$data_count;$i++){
			$row = mysql_fetch_assoc($res);
			$data_arr[$i] = array();
			$data_arr[$i]['country'] = "'".$row['name']."'";
		}
		$cache_arr[$preset_id] = $data_arr;
		return true;
	}
	
	/***
	get the sector industry names for a given sector industry preset id.
	These are returned within ''
	If some point is just '', then there is no data there and is not considered in query
	the keys in the cache is the preset id
	return false on db error
	**/
	public function get_sector_industries_from_preset($preset_id,&$cache_arr,&$data_count){
		$q = "select sector,industry from ".TP."preset_sector_industry_value where preset_id='".$preset_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$data_count = mysql_num_rows($res);
		if(0==$data_count){
			return true;
		}
		$data_arr = array();
		for($i=0;$i<$data_count;$i++){
			$row = mysql_fetch_assoc($res);
			$data_arr[$i] = array();
			$data_arr[$i]['sector'] = "'".trim($row['sector'])."'";
			$data_arr[$i]['industry'] = "'".trim($row['industry'])."'";
		}
		$cache_arr[$preset_id] = $data_arr;
		return true;
	}
	
	public function get_deal_type_subtype_from_preset($preset_id,&$cache_arr,&$data_count){
		$q = "select type,subtype1,subtype2 from ".TP."preset_deal_type_value where preset_id='".$preset_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$data_count = mysql_num_rows($res);
		if(0==$data_count){
			return true;
		}
		$data_arr = array();
		for($i=0;$i<$data_count;$i++){
			$row = mysql_fetch_assoc($res);
			$data_arr[$i] = array();
			$data_arr[$i]['type'] = "'".$row['type']."'";
			$data_arr[$i]['subtype1'] = "'".$row['subtype1']."'";
			$data_arr[$i]['subtype2'] = "'".$row['subtype2']."'";
		}
		$cache_arr[$preset_id] = $data_arr;
		return true;
	}
	/***
	here a preset is mapped to one item only
	**/
	public function get_deal_size_from_preset($preset_id,&$cache_arr){
		$q = "select from_billion,to_billion from ".TP."preset_deal_size_value where preset_id='".$preset_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$data_count = mysql_num_rows($res);
		if(0==$data_count){
			return false;
		}
		$data_arr = array();
		$row = mysql_fetch_assoc($res);
		$data_arr[0] = array();
		$data_arr[0]['from_billion'] = $row['from_billion'];
		$data_arr[0]['to_billion'] = $row['to_billion'];
		$cache_arr[$preset_id] = $data_arr;
		return true;
	}
	
	public function get_deal_date_from_preset($preset_id,&$cache_arr){
		$q = "select date_from,date_to from ".TP."preset_deal_date_value where preset_id='".$preset_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$data_count = mysql_num_rows($res);
		if(0==$data_count){
			return false;
		}
		$data_arr = array();
		$row = mysql_fetch_assoc($res);
		$data_arr[0] = array();
		$data_arr[0]['date_from'] = "'".$row['date_from']."'";
		$data_arr[0]['date_to'] = "'".$row['date_to']."'";
		$cache_arr[$preset_id] = $data_arr;
		return true;
	}
	
	/***
	the preset ids are in preset_id_vector
	The array of values for each id is in value_lookup_cache
	*********/
	public function search($job_id,$preset_id_vector,$value_lookup_cache,$firm_id,$firm_type,$rank_requested,&$err_msg){
		
		global $g_mc;
		/******
		sng:7/oct/2010
		We need the stat value also. since the stat field can have different names, we use a variable
		*********/
		if($preset_id_vector['ranking_criteria']=="num_deals"){
			$ranking_by = "num_deals";
			$stat = "count( * ) AS num_deals";
			$stat_name = "num_deals";
		}
		if($preset_id_vector['ranking_criteria']=="total_deal_value"){
			$ranking_by = "total_deal_value";
			$stat = "sum( value_in_billion ) AS total_deal_value";
			$stat_name = "total_deal_value";
		}
		if($preset_id_vector['ranking_criteria']=="total_adjusted_deal_value"){
			$ranking_by = "total_adjusted_deal_value";
			$stat = "sum( adjusted_value_in_billion ) AS total_adjusted_deal_value";
			$stat_name = "total_adjusted_deal_value";
		}
		$company_filter = "";
		$company_filter_clause = "";
		/***************************
		there can be one or more countries
		*******************************/
		/****************************************************************************
		sng: 7/dec/2010
		We no longer match hq country of the company. We match it with transaction::deal_country
		
		$key = $preset_id_vector['country'];
		$value_arr = $value_lookup_cache['country'][$key];
		$csv = "";
		if(count($value_arr) > 0){
			for($i=0;$i<count($value_arr);$i++){
				$csv.=",".$value_arr[$i]['country'];
			}
			$csv = substr($csv,1);
			$company_filter_clause.=" and hq_country IN (".$csv.")";
		}
		********************************************************************************/
		/*********************************
		there can be one or more sector industry tuples
		*********/
		/***********************************************************************
		sng: 7/dec/2010
		we no longer match sector and industry with the company doing the deal. We match it with transaction::deal_sector and deal_industry
		
		$key = $preset_id_vector['sector_industry'];
		$value_arr = $value_lookup_cache['sector_industry'][$key];
		$csv = "";
		if(count($value_arr) > 0){
			for($i=0;$i<count($value_arr);$i++){
				$csv.="~(";
				$data = "";
				if($value_arr[$i]['sector']!="''"){
					$data="sector=".$value_arr[$i]['sector'];
				}
				if($value_arr[$i]['industry']!="''"){
					if($data!=""){
						$data.=" and ";
					}
					$data.="industry=".$value_arr[$i]['industry'];
				}
				$csv.=$data.")";
			}
			$csv = substr($csv,1);
			$csv = str_replace("~"," OR ",$csv);
			$company_filter_clause.=" and (".$csv.")";
		}
		*******************************************************************************/
		if($company_filter_clause != ""){
			$company_filter.=" and company_id IN (select company_id from ".TP."company where 1=1".$company_filter_clause.")";
		}
		$q = "SELECT partner_id,".$stat." FROM ".TP."transaction_partners AS p LEFT JOIN ".TP."transaction AS t ON ( p.transaction_id = t.id ) WHERE partner_type = '".$firm_type."'";
		
		/***********************************************************************************************************
		sng: 7/dec/2010
		We no longer match hq country of the company. We match it with transaction::deal_country
		
		sng: 7/dec/2010
		we no longer match sector and undustry with the company doing the deal. We match it with transaction::deal_sector and deal_industry
		
		The only thing is, in the cache, the values are enclosed by '', so we need to remove those so that we can use the LIKE clause
		*****/
		$key = $preset_id_vector['country'];
		$value_arr = $value_lookup_cache['country'][$key];
		$csv = "";
		if(count($value_arr) > 0){
			for($i=0;$i<count($value_arr);$i++){
				$csv.="~(";
				$data = "";
				if($value_arr[$i]['country']!="''"){
					$temp = str_replace("'","",$value_arr[$i]['country']);
					$data="deal_country LIKE '%".$temp."%'";
				}
				$csv.=$data.")";
				
			}
			$csv = substr($csv,1);
			$csv = str_replace("~"," OR ",$csv);
			$q.=" and (".$csv.")";
		}
		
		$key = $preset_id_vector['sector_industry'];
		$value_arr = $value_lookup_cache['sector_industry'][$key];
		$csv = "";
		if(count($value_arr) > 0){
			for($i=0;$i<count($value_arr);$i++){
				$csv.="~(";
				$data = "";
				if($value_arr[$i]['sector']!="''"){
					$temp = str_replace("'","",$value_arr[$i]['sector']);
					$data="deal_sector like '%".$temp."%'";
				}
				
				if($value_arr[$i]['industry']!="''"){
					if($data!=""){
						$data.=" and ";
					}
					$temp = str_replace("'","",$value_arr[$i]['industry']);
					$data.="deal_industry like '%".$temp."%'";
				}
				$csv.=$data.")";
			}
			$csv = substr($csv,1);
			$csv = str_replace("~"," OR ",$csv);
			$q.=" and (".$csv.")";
		}
		/**************************************************************************************************************/
		/*********************************
		there can be one or more deal type/subtype/subsub type tuple
		*************/
		$key = $preset_id_vector['deal_type'];
		$value_arr = $value_lookup_cache['deal_type'][$key];
		$csv = "";
		if(count($value_arr) > 0){
			for($i=0;$i<count($value_arr);$i++){
				$csv.="~(";
				$data = "";
				if($value_arr[$i]['type']!="''"){
					$data.="deal_cat_name=".$value_arr[$i]['type'];
				}
				if($value_arr[$i]['subtype1']!="''"){
					if($data!=""){
						$data.=" and ";
					}
					$data.="deal_subcat1_name=".$value_arr[$i]['subtype1'];
				}
				if($value_arr[$i]['subtype2']!="''"){
					if($data!=""){
						$data.=" and ";
					}
					$data.="deal_subcat2_name=".$value_arr[$i]['subtype2'];
				}
				$csv.=$data.")";
			}
			$csv = substr($csv,1);
			$csv = str_replace("~"," OR ",$csv);
			$q.=" and (".$csv.")";
		}
		/******************************************
		there can be one or more deal size range
		***********/
		$key = $preset_id_vector['deal_size'];
		$value_arr = $value_lookup_cache['deal_size'][$key];
		$csv = "";
		if(count($value_arr) > 0){
			for($i=0;$i<count($value_arr);$i++){
				$csv.="~(";
				$data = "";
				if($value_arr[$i]['from_billion']!=0){
					$data.="value_in_billion>=".$value_arr[$i]['from_billion'];
				}
				if($value_arr[$i]['to_billion']!=0){
					if($data!=""){
						$data.=" and ";
					}
					$data.="value_in_billion<=".$value_arr[$i]['to_billion'];
				}
				$csv.=$data.")";
			}
			$csv = substr($csv,1);
			$csv = str_replace("~"," OR ",$csv);
			$q.=" and (".$csv.")";
		}
		/***********************************
		there can be one or more date range
		***********/
		$key = $preset_id_vector['deal_date'];
		$value_arr = $value_lookup_cache['deal_date'][$key];
		$csv = "";
		if(count($value_arr) > 0){
			for($i=0;$i<count($value_arr);$i++){
				$csv.="~(";
				$data = "";
				if($value_arr[$i]['date_from']!="'0000-00-00'"){
					$data.="date_of_deal>=".$value_arr[$i]['date_from'];
				}
				if($value_arr[$i]['date_to']!="'0000-00-00'"){
					if($data!=""){
						$data.=" and ";
					}
					$data.="date_of_deal<=".$value_arr[$i]['date_to'];
				}
				$csv.=$data.")";
			}
			$csv = substr($csv,1);
			$csv = str_replace("~"," OR ",$csv);
			$q.=" and (".$csv.")";
		}
		/*************************************************************/
		if($company_filter!=""){
			$q.=$company_filter;
		}
		/***************************************************************/
		$q.=" GROUP BY partner_id";
		/*****************************************************************/
		if($ranking_by != ""){
			$q.=" ORDER BY ".$ranking_by." DESC";
		}
		/*******
		sng:27/aug/2010
		We also store the query stmt that got us the hit. That way, later we can run the query to get the result for the param combo quickly.
		We take store the query stmt without the limit part so that we can set the limit when we requese the query stmt later
		The downside is that, if deal related data is changed, this may have to updated else this may give wrong result.
		To help the user, we store the date time when this result is generated.
		************/
		$q_stmt = $q;
		$date_now = date("Y-m-d H:i:s");
		/***************************************************************/
		$q.=" limit 0,".$rank_requested;
		/**************************************************************
		
	
		
		
		/***********************************************/
		$q_res = mysql_query($q);
		if(!$q_res){
			//echo mysql_error();
			$err_msg = "db error: ".mysql_error()." q: ".$q;
			return false;
		}
		$q_res_count = mysql_num_rows($q_res);
		if(0 == $q_res_count){
			return true;
		}
		//create array and see if the given company id has a place there. If so, store the vector and the position
		/**********
		sng:7/oct/2010
		We also want the stat value of the firm if we have a hit. So we store the stat value also
		*********/
		$pos_arr = array();
		$stat_value_of_firm = "";
		for($i=0;$i<$q_res_count;$i++){
			$q_res_row = mysql_fetch_assoc($q_res);
			$pos_arr[$i] = $q_res_row['partner_id'];
			if($q_res_row['partner_id']==$firm_id){
				$stat_value_of_firm = $q_res_row[$stat_name];
			}
		}
		/********
		sng:4/sep/2010
		Free up the result, since this is running inside a looooooooooong loop.
		anything that helps
		***********/
		mysql_free_result($q_res);
		
		$place = array_search($firm_id,$pos_arr);
		
		if($place===false){
			//not found
			return true;
		}
		//we have a hit, increase $place since it is zero based, and we need it 1 based
		
		$position = $place+1;
		$q = "insert into ".TP."top_search_request_hits set job_id='".$job_id."', country_preset_id='".$preset_id_vector['country']."', sector_industry_preset_id='".$preset_id_vector['sector_industry']."', deal_type_preset_id='".$preset_id_vector['deal_type']."', deal_size_preset_id='".$preset_id_vector['deal_size']."', deal_date_preset_id='".$preset_id_vector['deal_date']."',ranking_criteria='".$preset_id_vector['ranking_criteria']."', rank_of_firm='".$position."',stat_value_of_firm='".$stat_value_of_firm."',query='".addslashes($q_stmt)."',date_generated='".$date_now."'";
		mysql_query($q);
		return true;
	}
	
	/****
	sng:3/sep/2010
	for a job, there can be many hits. We want to filter by different fields.
	For that we need the preset ids and names for the field from the hits table
	*******/
	public function get_presets_for_result($job_id,$field_name,&$data_arr,&$data_count){
		global $g_mc;
		
		$q = "";
		if($field_name == "rank_of_firm"){
			$q = "select distinct rank_of_firm as preset_id,rank_of_firm as preset_name from ".TP."top_search_request_hits where job_id='".$job_id."' order by rank_of_firm";
		}
		if($field_name == "country_preset_id"){
			$q = "select distinct country_preset_id as preset_id,name as preset_name from ".TP."top_search_request_hits as h left join ".TP."preset_country as c on(h.country_preset_id=c.preset_id) where job_id='".$job_id."' order by name";
		}
		if($field_name == "sector_industry_preset_id"){
			$q = "select distinct sector_industry_preset_id as preset_id,name as preset_name from ".TP."top_search_request_hits as h left join ".TP."preset_sector_industry as c on(h.sector_industry_preset_id=c.preset_id) where job_id='".$job_id."' order by name";
		}
		if($field_name == "deal_type_preset_id"){
			$q = "select distinct deal_type_preset_id as preset_id,name as preset_name from ".TP."top_search_request_hits as h left join ".TP."preset_deal_type as c on(h.deal_type_preset_id=c.preset_id) where job_id='".$job_id."' order by name";
		}
		if($field_name == "deal_size_preset_id"){
			$q = "select distinct deal_size_preset_id as preset_id,name as preset_name from ".TP."top_search_request_hits as h left join ".TP."preset_deal_size_value as c on(h.deal_size_preset_id=c.preset_id) where job_id='".$job_id."' order by name";
		}
		if($field_name == "deal_date_preset_id"){
			$q = "select distinct deal_date_preset_id as preset_id,name as preset_name from ".TP."top_search_request_hits as h left join ".TP."preset_deal_date_value as c on(h.deal_date_preset_id=c.preset_id) where job_id='".$job_id."' order by name";
		}
		if($q == ""){
			return false;
		}
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$data_count = mysql_num_rows($res);
		if(0 == $data_count){
			return true;
		}
		for($i=0;$i<$data_count;$i++){
			$data_arr[$i] = mysql_fetch_assoc($res);
			$data_arr[$i]['preset_name'] = $g_mc->db_to_view($data_arr[$i]['preset_name']);
		}
		return true;
	}
	/****
	sng:3/sep/2010
	given the job id, get the hits. a hit consistes of the preset ids for which the search gave favourable result
	There can be 1000 of hits. So the member might send filter parameters to restrict the results shown. The filter params
	may or may not be set and if set, may or may not be blank
	*********/
	public function search_result_paged($job_id,$filter_params,$from,$num_to_fetch,&$data_arr,&$data_count,&$total_count){
		global $g_mc;
		$filter = "";
		if(isset($filter_params['rank_of_firm'])&&($filter_params['rank_of_firm']!="")){
			$filter.=" and rank_of_firm='".$filter_params['rank_of_firm']."'";
		}
		
		if(isset($filter_params['country_preset_id'])&&($filter_params['country_preset_id']!="")){
			$filter.=" and country_preset_id='".$filter_params['country_preset_id']."'";
		}
		
		if(isset($filter_params['sector_industry_preset_id'])&&($filter_params['sector_industry_preset_id']!="")){
			$filter.=" and sector_industry_preset_id='".$filter_params['sector_industry_preset_id']."'";
		}
		
		if(isset($filter_params['deal_type_preset_id'])&&($filter_params['deal_type_preset_id']!="")){
			$filter.=" and deal_type_preset_id='".$filter_params['deal_type_preset_id']."'";
		}
		
		if(isset($filter_params['deal_size_preset_id'])&&($filter_params['deal_size_preset_id']!="")){
			$filter.=" and deal_size_preset_id='".$filter_params['deal_size_preset_id']."'";
		}
		
		if(isset($filter_params['deal_date_preset_id'])&&($filter_params['deal_date_preset_id']!="")){
			$filter.=" and deal_date_preset_id='".$filter_params['deal_date_preset_id']."'";
		}
		
		if(isset($filter_params['ranking_criteria'])&&($filter_params['ranking_criteria']!="")){
			$filter.=" and ranking_criteria='".$filter_params['ranking_criteria']."'";
		}
		
		//first get the total number of results found till now
		$q = "select count(*) as cnt from ".TP."top_search_request_hits where job_id='".$job_id."'";
		if($filter != ""){
			$q.=$filter;
		}
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$row = mysql_fetch_assoc($res);
		$total_count = $row['cnt'];
		if(0==$total_count){
			//no data, return
			return true;
		}
		/********************
		sng:21/jan/2011
		We now may have to sort by stat_value_of_firm, but that is stored as varchar. Instead of chnaging db type, we take another aliased field by casting the value
		to decimal and sorting by that field
		***************/
		$q = "select h.*,cast(stat_value_of_firm as DECIMAL(15,6)) as stat_order,c.name as country_name,s.name as sector_name,d.name as deal_name,size.name as size_name,dt.name as date_name,ranking_criteria from ".TP."top_search_request_hits as h left join ".TP."preset_country as c on(h.country_preset_id=c.preset_id) left join ".TP."preset_sector_industry as s on(h.sector_industry_preset_id=s.preset_id) left join ".TP."preset_deal_type as d on(h.deal_type_preset_id=d.preset_id) left join ".TP."preset_deal_size_value as size on(h.deal_size_preset_id=size.preset_id) left join ".TP."preset_deal_date_value as dt on(h.deal_date_preset_id=dt.preset_id) where job_id='".$job_id."'";
		if($filter!=""){
			$q.=$filter;
		}
		/*******************************
		sng:18/jan/2011
		We now have support for sorting by multiple fields. The col names are passed as csv, in the order
		they are to be sorted
		The values are same as the filter names
		rank_of_firm
		country_preset_id
		sector_industry_preset_id
		deal_type_preset_id
		deal_size_preset_id
		deal_date_preset_id
		
		sng:21/jan/2011
		We want to make these two sortable also.
		ranking_criteria
		stat_value_of_firm
		Problem is, sorting by stat requires sorting by ranking criteria first. If only one is present, or none , we have no problem.
		We sort by ranking criteria or if stat is present, we sort by ranking criteria,stat.
		If both are present, then we have a problem. In that case, we give priority to stat and delete ranking criteria( since stat will
		trigger sort by ranking criteria). We do not delete the ranking criteria, we just set that key to empty string so that there is
		only stat
		The stat are to be sorted in desc order
		*******/
		$order_clause = "";
		if(isset($_POST['sort_by_cols'])&&($_POST['sort_by_cols']!="")){
			$sort_tokens = explode(",",$_POST['sort_by_cols']);
			$sort_token_count = count($sort_tokens);
			
			if(in_array("ranking_criteria",$sort_tokens)&&in_array("stat_value_of_firm",$sort_tokens)){
				$k = array_search("ranking_criteria",$sort_tokens);
				$sort_tokens[$k] = "";
			}
			
			for($t=0;$t<$sort_token_count;$t++){
				$sort_col_id = $sort_tokens[$t];
				if($sort_col_id=="rank_of_firm"){
					$order_clause.=",rank_of_firm";
				}
				if($sort_col_id=="country_preset_id"){
					$order_clause.=",country_name";
				}
				if($sort_col_id=="sector_industry_preset_id"){
					$order_clause.=",sector_name";
				}
				if($sort_col_id=="deal_type_preset_id"){
					$order_clause.=",deal_name";
				}
				if($sort_col_id=="deal_size_preset_id"){
					$order_clause.=",size_name";
				}
				if($sort_col_id=="deal_date_preset_id"){
					$order_clause.=",date_name";
				}
				if($sort_col_id=="ranking_criteria"){
					$order_clause.=",ranking_criteria";
				}
				if($sort_col_id=="stat_value_of_firm"){
					$order_clause.=",ranking_criteria,stat_order desc";
				}
			}
		}
		
		$order_clause = substr($order_clause,1);
		if($order_clause!=""){
			$q.=" order by ".$order_clause;
		}else{
			$q.=" order by rank_of_firm";
		}
		$q.=" limit ".$from.",".$num_to_fetch;
		/****************************************************************************/
		$res = mysql_query($q);
		if(!$res){
			echo mysql_error();
			return false;
		}
		$data_count = mysql_num_rows($res);
		if(0==$data_count){
			return true;
		}
		for($i=0;$i<$data_count;$i++){
			$data_arr[$i] = mysql_fetch_assoc($res);
			$data_arr[$i]['country_name'] = $g_mc->db_to_view($data_arr[$i]['country_name']);
			$data_arr[$i]['sector_name'] = $g_mc->db_to_view($data_arr[$i]['sector_name']);
			$data_arr[$i]['deal_name'] = $g_mc->db_to_view($data_arr[$i]['deal_name']);
			$data_arr[$i]['size_name'] = $g_mc->db_to_view($data_arr[$i]['size_name']);
			$data_arr[$i]['date_name'] = $g_mc->db_to_view($data_arr[$i]['date_name']);
		}
		return true;
	}
	
	public function search_result($result_id,&$data_arr,&$found){
		global $g_mc;
		$q = "select h.*,c.name as country_name,s.name as sector_name,d.name as deal_name,size.name as size_name,dt.name as date_name,ranking_criteria from ".TP."top_search_request_hits as h left join ".TP."preset_country as c on(h.country_preset_id=c.preset_id) left join ".TP."preset_sector_industry as s on(h.sector_industry_preset_id=s.preset_id) left join ".TP."preset_deal_type as d on(h.deal_type_preset_id=d.preset_id) left join ".TP."preset_deal_size_value as size on(h.deal_size_preset_id=size.preset_id) left join ".TP."preset_deal_date_value as dt on(h.deal_date_preset_id=dt.preset_id) where id='".$result_id."'";
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
		$data_arr['sector_name'] = $g_mc->db_to_view($data_arr['sector_name']);
		$data_arr['deal_name'] = $g_mc->db_to_view($data_arr['deal_name']);
		$data_arr['size_name'] = $g_mc->db_to_view($data_arr['size_name']);
		$data_arr['date_name'] = $g_mc->db_to_view($data_arr['date_name']);
		return true;
	}
	/**************
	sng:11/jan/2011
	If the mmt is 30 days old (calculated from date of submit), then, all the hits data
	for that job is deleted automatically, assuming it finished. However, the request data is not deleted. This way
	the same request can be re-run again.
	**********/
	public function delete_old_mmt_hits(){
		//get the finished jobs that are more than 30 days old
		$today = date("Y-m-d H:i:s");
		$q = "select job_id from ".TP."top_search_request where status='finished' and datediff('".$today."',submitted_on)>30";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		//for each, delete records
		while($row = mysql_fetch_assoc($res)){
			$job_id = $row['job_id'];
			
			$del_q = "delete from ".TP."top_search_request_error where job_id='".$job_id."'";
			mysql_query($del_q);
			
			$del_q = "delete from ".TP."top_search_request_processing_helper where job_id='".$job_id."'";
			mysql_query($del_q);
			
			$del_q = "delete from ".TP."top_search_request_hits where job_id='".$job_id."'";
			mysql_query($del_q);
			
			//since we have removed all hits data, update hit count in the request table
			$updt_q = "update ".TP."top_search_request set hits='0' where job_id='".$job_id."'";
			mysql_query($updt_q);
		}
		return true;
	}
	public function get_search_result_firms($job_id,$result_id,&$data_arr,&$data_count){
		global $g_mc;
		//get the rank requested
		$q = "select rank_requested from ".TP."top_search_request where job_id='".$job_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$cnt = mysql_num_rows($res);
		if(0==$cnt){
			return false;
		}
		$row = mysql_fetch_assoc($res);
		$rank_requested = $row['rank_requested'];
		/////////////////////////////////////////////////
		//we will get this many records
		
		//get the query used to do the search for this result
		$q = "select query,ranking_criteria from ".TP."top_search_request_hits where id='".$result_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$cnt = mysql_num_rows($res);
		if(0==$cnt){
			return false;
		}
		$row = mysql_fetch_assoc($res);
		$query = $row['query'];
		$ranking_criteria = $row['ranking_criteria'];
		
		//now create the query to get the result along with company name and company short name
		//we return short name so that we can create the bar chart
		/******
		sng:5/oct/2010
		Even if the trank requested is 3, we get the 5 elements
		//$q = "select r.*,c.name as firm_name,c.short_name from (".$query.") as r left join ".TP."company as c on (r.partner_id=c.company_id) limit 0,".$rank_requested;
		*******/
		$q = "select r.*,c.name as firm_name,c.short_name from (".$query.") as r left join ".TP."company as c on (r.partner_id=c.company_id) limit 0,5";
		$res = mysql_query($q);
		if(!$res){
			//echo mysql_error();
			return false;
		}
		$data_count = mysql_num_rows($res);
		if(0==$data_count){
			return true;
		}
		for($i=0;$i<$data_count;$i++){
			$row = mysql_fetch_assoc($res);
			$data_arr[$i] = array();
			$data_arr[$i]['firm_name'] = $g_mc->db_to_view($row['firm_name']);
			if($ranking_criteria=="num_deals") $key = "num_deals";
			if($ranking_criteria=="total_deal_value") $key = "total_deal_value";
			if($ranking_criteria=="total_adjusted_deal_value") $key = "total_adjusted_deal_value";
			$data_arr[$i]['stat_value'] = $row[$key];
		}
		return true;
	}
	
	public function store_execution_state($job_id,$country_preset_vector,$sector_industry_preset_vector,$deal_type_preset_vector,$deal_size_preset_vector,$deal_date_preset_vector,$ranking_criteria_vector,$cache,$next_permutation_offset,&$err_msg){
		/*******
		sng:6/sep/2010
		as the number of permutation can be very big (20000), we do not store it
		**********/
		$q = "update ".TP."top_search_request_processing_helper set country_preset_vector='".addslashes(serialize($country_preset_vector))."',sector_industry_preset_vector='".addslashes(serialize($sector_industry_preset_vector))."',deal_type_preset_vector='".addslashes(serialize($deal_type_preset_vector))."',deal_size_preset_vector='".addslashes(serialize($deal_size_preset_vector))."',deal_date_preset_vector='".addslashes(serialize($deal_date_preset_vector))."',ranking_criteria_vector='".addslashes(serialize($ranking_criteria_vector))."',cache='".addslashes(serialize($cache))."',next_permutation_offset='".$next_permutation_offset."',is_running='n' where job_id='".$job_id."'";
		
		//keep the status to in progress
		
		$res = mysql_query($q);
		if(!$res){
			$err_msg = "cannot store state: ".mysql_error()." q: ".$q;
			return false;
		}
		//no db error, 1 row updated
		return true;
	}
	
	public function retrieve_execution_state($job_id,&$country_preset_vector,&$sector_industry_preset_vector,&$deal_type_preset_vector,&$deal_size_preset_vector,&$deal_date_preset_vector,&$ranking_criteria_vector,&$cache,&$offset_permutation,&$next_permutation_offset){
		
		$q = "select * from ".TP."top_search_request_processing_helper where job_id='".$job_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$cnt = mysql_num_rows($res);
		if(0 == $cnt){
			//not found, error
			return false;
		}
		$row = mysql_fetch_assoc($res);
		$country_preset_vector = unserialize(stripslashes($row['country_preset_vector']));
		$sector_industry_preset_vector = unserialize(stripslashes($row['sector_industry_preset_vector']));
		$deal_type_preset_vector = unserialize(stripslashes($row['deal_type_preset_vector']));
		$deal_size_preset_vector = unserialize(stripslashes($row['deal_size_preset_vector']));
		$deal_date_preset_vector = unserialize(stripslashes($row['deal_date_preset_vector']));
		$ranking_criteria_vector = unserialize($row['ranking_criteria_vector']);
		$cache = unserialize(stripslashes($row['cache']));
		/*******
		sng:6/sep/2010
		as the number of permutation can be very big (20000), we do not store it, so we do not retrieve it
		**********/
		$next_permutation_offset = $row['next_permutation_offset'];
		return true;
	}
}
$g_maketop = new make_me_top();
?>