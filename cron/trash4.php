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
ini_set("max_execution_time","43200");
//set to 12hr
//increase this as needed
require_once(dirname(dirname(__FILE__))."/include/global.php");
require_once(dirname(dirname(__FILE__))."/classes/class.make_me_top.php");
require_once(dirname(dirname(__FILE__))."/classes/class.magic_quote.php");


$is_resumed = false;$job_id = "728-1283949427";

/***
sng:6/sep/2010
********/
$time_started = time();
//$time_limit = 600;
$time_limit = 60;
//1 min

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
	*******/
	//$extended_search = false;
	$extended_search = true;
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
	$success = $g_maketop->get_all_deal_size_preset_ids($deal_size_preset_vector,$deal_size_preset_count);
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
	$success = $g_maketop->get_all_deal_date_preset_ids($deal_date_preset_vector,$deal_date_preset_count);
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
	**************/
	$offset_permutation = array();
	/******dbg**********/
	$g_maketop->request_processing_error($job_id,"starting permutation");
	/*****dbg********/
	/*********dng**********/
	$joker = 1;
	/********dbg********/
	for($x1=0;$x1<$country_preset_count;$x1++){
		for($x2=0;$x2<$sector_industry_preset_count;$x2++){
			for($x3=0;$x3<$deal_type_preset_count;$x3++){
				for($x4=0;$x4<$deal_size_preset_count;$x4++){
					for($x5=0;$x5<$deal_date_preset_count;$x5++){
						for($x6=0;$x6<$ranking_criteria_count;$x6++){
						
							$temp_arr = array();
							$temp_arr['country_preset_vector_offset'] = $x1;
							$temp_arr['sector_industry_preset_vector_offset'] = $x2;
							$temp_arr['deal_type_preset_vector_offset'] = $x3;
							$temp_arr['deal_size_preset_vector_offset'] = $x4;
							$temp_arr['deal_date_preset_vector_offset'] = $x5;
							$temp_arr['ranking_criteria_vector_offset'] = $x6;
							$offset_permutation[] = $temp_arr;
							/******dbg**********/
							$delta = time()-$time_started;
							$msg = "done ".$joker." by ".$delta;
							$g_maketop->request_processing_error($job_id,$msg);
							/*****dbg********/
							$joker++;
							/***
							sleep might help but one sec is too much
							***/
							usleep(1);
						}
					}
				}
			}
		}
	}
	$sweeps_done = 0;
	/******dbg**********/
	$g_maketop->request_processing_error($job_id,"permutation over");
	/*****dbg********/
	/*********************************************************************/
}else{
	//some parts were done before, we are resuming from where we left off
	//get the saved state
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
	$offset_permutation = array();
	$country_preset_count = count($country_preset_vector);
	$sector_industry_preset_count = count($sector_industry_preset_vector);
	$deal_type_preset_count = count($deal_type_preset_vector);
	$deal_size_preset_count = count($deal_size_preset_vector);
	$deal_date_preset_count = count($deal_date_preset_vector);
	$ranking_criteria_count = count($ranking_criteria_vector);
	
	
	for($x1=0;$x1<$country_preset_count;$x1++){
		for($x2=0;$x2<$sector_industry_preset_count;$x2++){
			for($x3=0;$x3<$deal_type_preset_count;$x3++){
				for($x4=0;$x4<$deal_size_preset_count;$x4++){
					for($x5=0;$x5<$deal_date_preset_count;$x5++){
						for($x6=0;$x6<$ranking_criteria_count;$x6++){
							$temp_arr = array();
							$temp_arr['country_preset_vector_offset'] = $x1;
							$temp_arr['sector_industry_preset_vector_offset'] = $x2;
							$temp_arr['deal_type_preset_vector_offset'] = $x3;
							$temp_arr['deal_size_preset_vector_offset'] = $x4;
							$temp_arr['deal_date_preset_vector_offset'] = $x5;
							$temp_arr['ranking_criteria_vector_offset'] = $x6;
							$offset_permutation[] = $temp_arr;
						}
					}
				}
			}
		}
	}
	
	
	$sweeps_done = $next_permutation_offset;
}
////////////////////////////////////////////////////////
$total_sweeps = count($offset_permutation);

for($j=$sweeps_done;$j<$total_sweeps;$j++){
	$preset_id_vector = array();
	$preset_id_vector['country'] = $country_preset_vector[$offset_permutation[$j]['country_preset_vector_offset']];
	$preset_id_vector['sector_industry'] = $sector_industry_preset_vector[$offset_permutation[$j]['sector_industry_preset_vector_offset']];
	$preset_id_vector['deal_type'] = $deal_type_preset_vector[$offset_permutation[$j]['deal_type_preset_vector_offset']];
	$preset_id_vector['deal_size'] = $deal_size_preset_vector[$offset_permutation[$j]['deal_size_preset_vector_offset']];
	$preset_id_vector['deal_date'] = $deal_date_preset_vector[$offset_permutation[$j]['deal_date_preset_vector_offset']];
	$preset_id_vector['ranking_criteria'] = $ranking_criteria_vector[$offset_permutation[$j]['ranking_criteria_vector_offset']];
	
	
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
	$dbg_msg = "Done ".$sweeps_done." of ".$total_sweeps." for ".$job_id;
	$dbg_q = "update ".TP."top_search_request set dbg_status='".$dbg_msg."',dbg_last_processing_time='".date("Y-m-d H:i:s")."' where job_id='".$job_id."'";
	mysql_query($dbg_q);
	
	/********
	sng:6/sep/2010
	Do we have time left?
	*********/
	if(time()-$time_started > $time_limit){
		//store internal state
		/*******
		sng:6/sep/2010
		as the number of permutation can be very big (20000), we do not store it
		**********/
		$err_msg = "";
		$success = $g_maketop->store_execution_state($job_id,$country_preset_vector,$sector_industry_preset_vector,$deal_type_preset_vector,$deal_size_preset_vector,$deal_date_preset_vector,$ranking_criteria_vector,$cache,$j+1,$err_msg);
		if(!$success){
			/*****
			if this does not store, we cannot do a thing
			However, we cannot resume it also, so better mark as partial
			*******/
			$g_maketop->request_processing_error($job_id,$err_msg);
			exit;
		}
		//keep the status to in progress
		//pause execution
		exit;
	}
}
//job complete
$g_maketop->request_processing_completed($job_id);
?>