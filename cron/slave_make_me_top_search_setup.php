<?php
/***
called in cron job to do a long search. The job id is passed via command line argument.
$argv[0] is name of this file.
$argv[1] will contain the job id

The task here is to get the various preset_ids adn create the offset permutation array and store in a
table so that real progress can start.
We also set the status of the job to in progress
***/
ini_set("max_execution_time","43200");
//set to 12hr
//increase this as needed
require_once(dirname(dirname(__FILE__))."/include/global.php");
require_once(dirname(dirname(__FILE__))."/classes/class.make_me_top.php");
require_once(dirname(dirname(__FILE__))."/classes/class.magic_quote.php");
if($argc < 2){
	//job id not present
	exit;
}
$job_id = $argv[1];

/*$job_id = "663-1283607328";*/

//get the job detail
$job_data = NULL;
$found = $g_maketop->fetch_request($job_id,$job_data);
if(!$found){
	exit;
}

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
We have all the preset id vectors, now create the permutation of the offsets and create the
offset permutation vector
********************************/
$offset_permutation_vector = array();

for($x1=0;$x1<$country_preset_count;$x1++){
	for($x2=0;$x2<$sector_industry_preset_count;$x2++){
		for($x3=0;$x3<$deal_type_preset_count;$x3++){
			for($x4=0;$x4<$deal_size_preset_count;$x4++){
				for($x5=0;$x5<$deal_date_preset_count;$x5++){
					for($x6=0;$x6<$ranking_criteria_count;$x6++){
						$temp = array();
						$temp['country_offset'] = $x1;
						$temp['sector_industry_offset'] = $x2;
						$temp['deal_type_offset'] = $x3;
						$temp['deal_size_offset'] = $x4;
						$temp['deal_date_offset'] = $x5;
						$temp['ranking_criteria_offset'] = $x6;
						$offset_permutation_vector[] = $temp;
					}
				}
			}
		}
	}
}
/*************************************************/
//store the vectors in database
$q = "insert into ".TP."top_search_request_processing_helper set job_id='".$job_id."',country_preset_vector='".serialize($country_preset_vector)."',sector_industry_preset_vector='".serialize($sector_industry_preset_vector)."',deal_type_preset_vector='".serialize($deal_type_preset_vector)."',deal_size_preset_vector='".serialize($deal_size_preset_vector)."',deal_date_preset_vector='".serialize($deal_date_preset_vector)."',ranking_criteria_vector='".serialize($ranking_criteria_vector)."',offset_permutation_vector='".serialize($offset_permutation_vector)."',next_permutation_offset='0',is_running='n'";
mysql_query($q); 
/****
sng:4/sep/2010
close any db connection opened
*******/
mysql_close();
?>