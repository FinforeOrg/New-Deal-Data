<?php
error_reporting(E_ALL);
/***
called in cron job to do a long search. The job id is passed via command line argument.
$argv[0] is name of this file.
$argv[1] will contain the job id

sng:6/sep/2010
Now there can be 3rd arg, resume

$argv[2] is resume

We now keep track of the starting time and after each loop, we see if the time limit has passed or not. If passed, store the state of execution and
set is_running to n and store the internal variables
***/
/*****************************************************************
sng:16/sep/2010
We have a dedicated server where script has no time limit. Hence we set the time limit to unlimited
ini_set("max_execution_time","43200");
//set to 12hr
//increase this as needed
***************************************************************************/
set_time_limit(0);
require_once(dirname(dirname(__FILE__))."/include/global.php");
require_once(dirname(dirname(__FILE__))."/classes/class.make_me_top.php");
require_once(dirname(dirname(__FILE__))."/classes/class.magic_quote.php");



if($argc < 2){
	//job id not present
	exit;
}
$job_id = $argv[1];
$is_resumed = false;
if($argc >= 3){
	//check
	if($argv[2] == "resume"){
		$is_resumed = true;
	}
}
/**************************************************************************
sng:16/sep/2010
although we keep the check for the resume flag , it is no longer used.
The cron scheduler no longer call the slave with resume option
******************************************************************************/
//$is_resumed = true;$job_id = "728-1283858730";

/***
sng:6/sep/2010
********/
$time_started = time();
$time_limit = 18000;
//5 hr
/**************************************************************************
sng:16/sep/2010
These variables are no longer used since the script can run for any amount of time
*********************************************************************************/
//get the job detail
$job_data = NULL;
$found = $g_maketop->fetch_request($job_id,$job_data);
if(!$found){
	$g_maketop->request_processing_error($job_id,"The job was not found in the queue. Try resubmitting it.");
	exit;
}

if(!$is_resumed){
	
	//this is a normal start
	/*************************************************************************/
	//now the job is about to start, set the status
	$g_maketop->starting_request_processing($job_id);
	///////////////////////////////////////////////////
	/****
	sng:31/aug/2010
	since the code is running very fast. client wants the search to be on all presets mapped to the search option,
	never mind whether it is primary or not
	for that we set extended search to true
	
	sng: 18/sep/2010
	Now we get the value of this from stored job data
	extended_search: y means all search, n means only primary
	*******/
	if($job_data['extended_search']=='n'){
		//only primary
		$extended_search = false;
	}
	if($job_data['extended_search']=='y'){
		//get all
		$extended_search = true;
	}else{
		//only primary
		$extended_search = false;
	}
	$cache = array();
	/******************************************
	get the country presets
	**********/
	$country_preset_vector = array();
	$country_preset_count = 0;
	$success = $g_maketop->get_all_country_preset_ids($job_data['option_country'],$extended_search,$country_preset_vector,$country_preset_count);
	if(!$success){
		exit;
	}
	/***************************************************
	for each country preset id, get the country names
	*****/
	$cache['country'] = array();
	$dummy = 0;
	for($i=0;$i<$country_preset_count;$i++){
		$success = $g_maketop->get_countries_from_preset($country_preset_vector[$i],$cache['country'],$dummy);
		if(!$success){
			exit;
		}
	}
	/*********************************************
	get the sector industry presets
	*********/
	$sector_industry_preset_vector = array();
	$sector_industry_preset_count = 0;
	$success = $g_maketop->get_all_sector_industry_preset_ids($job_data['option_sector_industry'],$extended_search,$sector_industry_preset_vector,$sector_industry_preset_count);
	if(!$success){
		exit;
	}
	/***************************************************
	for each, get the sector industry tuple
	*******/
	$cache['sector_industry'] = array();
	$dummy = 0;
	for($i=0;$i<$sector_industry_preset_count;$i++){
		$success = $g_maketop->get_sector_industries_from_preset($sector_industry_preset_vector[$i],$cache['sector_industry'],$dummy);
		if(!$success){
			exit;
		}
	}
	/***********************************************
	get the deal type
	********/
	$deal_type_preset_vector = array();
	$deal_type_preset_count = 0;
	$success = $g_maketop->get_all_deal_type_preset_ids($job_data['option_deal_type'],$extended_search,$deal_type_preset_vector,$deal_type_preset_count);
	if(!$success){
		exit;
	}
	/***************************************************
	for each, get the list of deal type / subtype /sub sub type
	***********/
	$cache['deal_type'] = array();
	$dummy = 0;
	for($i=0;$i<$deal_type_preset_count;$i++){
		$success = $g_maketop->get_deal_type_subtype_from_preset($deal_type_preset_vector[$i],$cache['deal_type'],$dummy);
		if(!$success){
			exit;
		}

	}
	/***************************************************
	get all the deal size presets
	**********/
	$deal_size_preset_vector = array();
	$deal_size_preset_count = 0;
	$success = $g_maketop->get_all_deal_size_preset_ids($deal_size_preset_vector,$extended_search,$deal_size_preset_count);
	if(!$success){
		exit;
	}
	/*************************************************
	for each, get the list of deal sizes
	***********/
	$cache['deal_size'] = array();
	for($i=0;$i<$deal_size_preset_count;$i++){
		$success = $g_maketop->get_deal_size_from_preset($deal_size_preset_vector[$i],$cache['deal_size']);
		if(!$success){
			exit;
		}
	}
	/*************************************************
	get all the deal date presets
	********/
	$deal_date_preset_vector = array();
	$deal_date_preset_count = 0;
	$success = $g_maketop->get_all_deal_date_preset_ids($deal_date_preset_vector,$extended_search,$deal_date_preset_count);
	if(!$success){
		exit;
	}
	/******************************************************
	for each, get the date values
	*****************/
	$cache['deal_date'] = array();
	for($i=0;$i<$deal_date_preset_count;$i++){
		$success = $g_maketop->get_deal_date_from_preset($deal_date_preset_vector[$i],$cache['deal_date']);
		if(!$success){
			exit;
		}
	}
	/**************************************************
	The ranking criterias are only 3 types, we hard code these
	***********/
	$ranking_criteria_vector = array("num_deals","total_deal_value","total_adjusted_deal_value");
	$ranking_criteria_count = count($ranking_criteria_vector);
	/******************************************************
	we have all the vectors, do permutation on all 6
	for each, we create a vector and create query and run it
	
	we only take $job_data['rank_requested'] number of results and try to see if $job_data['company_id'] is there or not.
	if there, we store the vector in database against the $job_id and store the rank and chart? If we just store the vector data
	we can create the chart at runtime, but change to database may affect the chart. Today it can be at 3 for a vector
	but tomorrow it can be 4 for the same vector. So we also store the query. That way we can generate the chart jut by running the query
	
	for debugging purpose, we track how many sweeps are done out of total number of sweeps
	/*************************************************/
	/****
	sng:6/sep/2010
	do a permutation of the vector array offsets and store in permutation array
	
	sng:8/sep/2010
	No need for permutation array. We will get the offsets from the general count
	**************/
	
	$sweeps_done = 0;
	/*********************************************************************/
}else{
	//some parts were done before, we are resuming from where we left off
	//get the saved state
	/****************************************************************************************
	sng:16/sep/2010
	We now have a dedicated server where script can run for any amount of time.
	This means, we no longer have to impose a timeout and save the state, to be resumed later.
	That means, this block is useless
	
	$country_preset_vector = NULL;
	$sector_industry_preset_vector = NULL;
	$deal_type_preset_vector = NULL;
	$deal_size_preset_vector = NULL;
	$deal_date_preset_vector = NULL;
	$ranking_criteria_vector = NULL;
	$cache = NULL;
	$next_permutation_offset = 0;
	
	$success = $g_maketop->retrieve_execution_state($job_id,$country_preset_vector,$sector_industry_preset_vector,$deal_type_preset_vector,$deal_size_preset_vector,$deal_date_preset_vector,$ranking_criteria_vector,$cache,$offset_permutation,$next_permutation_offset);
	
	/***
	sng:6/sep/2010
	since we are not srtoring that huge offset_permutation, we need to recreate it
	************/
	/****************************************************************************************
	$country_preset_count = count($country_preset_vector);
	$sector_industry_preset_count = count($sector_industry_preset_vector);
	$deal_type_preset_count = count($deal_type_preset_vector);
	$deal_size_preset_count = count($deal_size_preset_vector);
	$deal_date_preset_count = count($deal_date_preset_vector);
	$ranking_criteria_count = count($ranking_criteria_vector);
	
	$sweeps_done = $next_permutation_offset;
	*********************************************************************************************/
}
////////////////////////////////////////////////////////
/*******
sng: 8/sep/2010
We are not using the offset permutation array (since code is getting timed out).
Nor we are creating nested loops (since state cannot be saved for that)
That means, total count has to be calculated by multiplying the vector counts
We get the offsets from the general count
*******************/
$total_sweeps = $country_preset_count*$sector_industry_preset_count*$deal_type_preset_count*$deal_size_preset_count*$deal_date_preset_count*$ranking_criteria_count;
/*****************************************************
sng:12/nov/2010
We do not update the status after every iteration. We use a counter and update the status after every 100 or 1000 iteration
and at the last iteration
This cuts down db access and should improve running time
***************/
$update_counter = 0;
$update_status = false;
/******************************************/
for($j=$sweeps_done;$j<$total_sweeps;$j++){
	$num = $j;
	$ranking_criteria_vector_offset = $num%$ranking_criteria_count;
	$num = floor($num/$ranking_criteria_count);
	
	$deal_date_preset_vector_offset = $num%$deal_date_preset_count;
	$num = floor($num/$deal_date_preset_count);
	
	$deal_size_preset_vector_offset = $num%$deal_size_preset_count;
	$num = floor($num/$deal_size_preset_count);
	
	$deal_type_preset_vector_offset = $num%$deal_type_preset_count;
	$num = floor($num/$deal_type_preset_count);
	
	$sector_industry_preset_vector_offset = $num%$sector_industry_preset_count;
	$country_preset_vector_offset = floor($num/$sector_industry_preset_count);
	
	$preset_id_vector = array();
	$preset_id_vector['country'] = $country_preset_vector[$country_preset_vector_offset];
	$preset_id_vector['sector_industry'] = $sector_industry_preset_vector[$sector_industry_preset_vector_offset];
	$preset_id_vector['deal_type'] = $deal_type_preset_vector[$deal_type_preset_vector_offset];
	$preset_id_vector['deal_size'] = $deal_size_preset_vector[$deal_size_preset_vector_offset];
	$preset_id_vector['deal_date'] = $deal_date_preset_vector[$deal_date_preset_vector_offset];
	$preset_id_vector['ranking_criteria'] = $ranking_criteria_vector[$ranking_criteria_vector_offset];
	
	
	/****
	now our vector is ready to be given to search code to produce query
	we send the rank requested also, along with company id and company type
	the cache is send for lookup of preset id to item values
	in case of error, the function return error message which we can store in db
	****/
	$err_msg = "";
	$success = $g_maketop->search($job_id,$preset_id_vector,$cache,$job_data['company_id'],$job_data['type'],$job_data['rank_requested'],$err_msg);
	if(!$success){
		$g_maketop->request_processing_error($job_id,$err_msg);
		exit;
	}
	/***
	done a sweep
	update dbg_status and dbg_last_processing_time
	**/
	$sweeps_done++;
	/*********************************************************************
	sng:12/nov/2010
	if this is the last iteration, update. Otherwise, see if the counter has reached 100 or not
	************/
	$update_counter++;
	
	if($j == ($total_sweeps - 1)){
		$update_status = true;
	}else{
		if($update_counter == 100){
			$update_status = true;
			$update_counter = 0;
		}else{
			//do nothing
		}
	}
	if($update_status){
		$dbg_msg = "Done ".$sweeps_done." of ".$total_sweeps." for ".$job_id;
		$dbg_q = "update ".TP."top_search_request set dbg_status='".$dbg_msg."',dbg_last_processing_time='".date("Y-m-d H:i:s")."' where job_id='".$job_id."'";
		mysql_query($dbg_q);
		$update_status = false;
	}
	/*********************************************************************/
	
	
	
	/********
	sng:6/sep/2010
	Do we have time left?
	*********/
	/**************************************************
	sng:16/sep/2010
	We longer need a self imposed time limit since the code can run for any amount of time
	So, no need to check and save state
	**************************************************************
	if(time()-$time_started > $time_limit){
		//store internal state
		/*******
		sng:6/sep/2010
		as the number of permutation can be very big (20000), we do not store it
		**********
		$err_msg = "";
		$success = $g_maketop->store_execution_state($job_id,$country_preset_vector,$sector_industry_preset_vector,$deal_type_preset_vector,$deal_size_preset_vector,$deal_date_preset_vector,$ranking_criteria_vector,$cache,$j+1,$err_msg);
		if(!$success){
			/*****
			if this does not store, we cannot do a thing
			However, we cannot resume it also, so better mark as partial
			*******
			$g_maketop->request_processing_error($job_id,$err_msg);
			exit;
		}
		//keep the status to in progress
		//pause execution
		exit;
	}
	********************************************************************************/
}
//job complete
$g_maketop->request_processing_completed($job_id);
?>