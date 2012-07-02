<?php
error_reporting(E_ALL);
/****************************
sng:20/oct/2010
This is called from cmd line. A mmt job id is passed and it replicate the process
of slave_make_me_top_search but instead of running the query, it just logs those to the file
mmt_query_log.txt
*******************************/
set_time_limit(0);
include(dirname(dirname(__FILE__))."/include/global.php");
require_once(dirname(dirname(__FILE__))."/classes/class.make_me_top.php");
require_once(dirname(dirname(__FILE__))."/classes/class.magic_quote.php");
/*****************************************/
if($argc < 2){
	//job id not present
	exit;
}
$job_id = $argv[1];
//open the file
$fp = fopen("./mmt_query_log.txt","w");

//get the job detail
$job_data = NULL;
$found = $g_maketop->fetch_request($job_id,$job_data);
if(!$found){
	echo "job data not found";
	exit;
}
$extended_search = true;
$cache = array();
/******************************************
get the country presets
**********/
$country_preset_vector = array();
$country_preset_count = 0;
$success = $g_maketop->get_all_country_preset_ids($job_data['option_country'],$extended_search,$country_preset_vector,$country_preset_count);
if(!$success){
	echo "Cannot get country presets";
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
		echo "Cannot get country from preset";
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
	echo "Cannot get sector presets";
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
		echo "Cannot get sector from preset";
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
	echo "Cannot get deal type presets";
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
		echo "Cannot get deal type from preset";
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
	echo "Cannot get size presets";
	exit;
}
/*************************************************
for each, get the list of deal sizes
***********/
$cache['deal_size'] = array();
for($i=0;$i<$deal_size_preset_count;$i++){
	$success = $g_maketop->get_deal_size_from_preset($deal_size_preset_vector[$i],$cache['deal_size']);
	if(!$success){
		echo "Cannot get size from preset";
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
	echo "Cannot get date presets";
	exit;
}
/******************************************************
for each, get the date values
*****************/
$cache['deal_date'] = array();
for($i=0;$i<$deal_date_preset_count;$i++){
	$success = $g_maketop->get_deal_date_from_preset($deal_date_preset_vector[$i],$cache['deal_date']);
	if(!$success){
		echo "Cannot get date from preset";
		exit;
	}
}
/**************************************************
The ranking criterias are only 3 types, we hard code these
***********/
$ranking_criteria_vector = array("num_deals","total_deal_value","total_adjusted_deal_value");
$ranking_criteria_count = count($ranking_criteria_vector);
/***************************************************/
$sweeps_done = 0;
$total_sweeps = $country_preset_count*$sector_industry_preset_count*$deal_type_preset_count*$deal_size_preset_count*$deal_date_preset_count*$ranking_criteria_count;
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
	
	$err_msg = "";
	$query_stmt = "";
	$success = search($job_id,$preset_id_vector,$cache,$job_data['company_id'],$job_data['type'],$job_data['rank_requested'],$err_msg,$query_stmt);
	if(!$success){
		echo $err_msg;
		exit;
	}
	//store the query statement
	fwrite($fp,$query_stmt);
	fwrite($fp,"\n\n");
	echo ".";
	$sweeps_done++;
}
fclose($fp);
echo "done";
exit;
function search($job_id,$preset_id_vector,$value_lookup_cache,$firm_id,$firm_type,$rank_requested,&$err_msg,&$query_stmt){
		
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
	/*********************************
	there can be one or more sector industry tuples
	*********/
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
	if($company_filter_clause != ""){
		$company_filter.=" and company_id IN (select company_id from ".TP."company where 1=1".$company_filter_clause.")";
	}
	$q = "SELECT partner_id,".$stat." FROM ".TP."transaction_partners AS p LEFT JOIN ".TP."transaction AS t ON ( p.transaction_id = t.id ) WHERE partner_type = '".$firm_type."'";
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
	/***************************************************************/
	$q.=" limit 0,".$rank_requested;
	/**************************************************************/
	$query_stmt = $q;
	
	return true;
}
?>