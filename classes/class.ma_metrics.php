<?php
require_once("classes/db.php");
class ma_metrics{
	
	public function admin_get_all_region_country_list(&$data_arr,&$data_count){
		global $g_db;
		
		$q = "select id,name from ".TP."ma_metrics_region_country order by name";
		$success = $g_db->select_query($q);
		if(!$success){
			return false;
		}
		$data_count = $g_db->row_count();
		if($data_count == 0){
			//no data, return
			return true;
		}
		$data_arr = $g_db->get_result_set_as_array();
		return true;
	}
	
	public function admin_get_all_sector_industry_list(&$data_arr,&$data_count){
		global $g_db;
		
		$q = "select id,name from ".TP."ma_metrics_sector_industry order by name";
		$success = $g_db->select_query($q);
		if(!$success){
			return false;
		}
		$data_count = $g_db->row_count();
		if($data_count == 0){
			//no data, return
			return true;
		}
		$data_arr = $g_db->get_result_set_as_array();
		return true;
	}
	
	public function admin_get_series_detail($series_id,&$data){
		global $g_db;
		
		$q = "select series_id,rc.name as region_country_name,si.name as sector_industry_name,type_name from ".TP."ma_metrics_series as s left join ".TP."ma_metrics_region_country as rc on(s.metrics_region_country_id=rc.id) left join ".TP."ma_metrics_sector_industry as si on(s.metrics_sector_industry_id=si.id) left join ".TP."ma_metrics_types as t on(s.metrics_type_id=t.type_id) where series_id='".$series_id."'";
		
		$success = $g_db->select_query($q);
		if(!$success){
			return false;
		}
		$data_count = $g_db->row_count();
		if($data_count == 0){
			//no such data, hmm, error
			return false;
		}
		$data = $g_db->get_row();
		return true;
	}
	
	public function admin_get_all_series(&$data_arr,&$data_count){
		global $g_db;
		
		$q = "select series_id,rc.name as region_country_name,si.name as sector_industry_name,type_name from ".TP."ma_metrics_series as s left join ".TP."ma_metrics_region_country as rc on(s.metrics_region_country_id=rc.id) left join ".TP."ma_metrics_sector_industry as si on(s.metrics_sector_industry_id=si.id) left join ".TP."ma_metrics_types as t on(s.metrics_type_id=t.type_id) order by type_name,region_country_name,sector_industry_name";
		
		$success = $g_db->select_query($q);
		if(!$success){
			return false;
		}
		$data_count = $g_db->row_count();
		if($data_count == 0){
			//no data, return
			return true;
		}
		$data_arr = $g_db->get_result_set_as_array();
		return true;
	}
	
	public function admin_add_series($data_arr,&$validation_passed,&$err_arr){
		global $g_db;
		
		$validation_passed = true;
		if($data_arr['metrics_region_country_id'] == ""){
			$validation_passed = false;
			$err_arr['metrics_region_country_id'] = "Please select Region / Country";
		}
		
		//sector / industry may be blank for average
		
		if($data_arr['metrics_type_id'] == ""){
			$validation_passed = false;
			$err_arr['metrics_type_id'] = "Please select Metrics Type";
		}
		
		if(!$validation_passed){
			return true;
		}
		/***********************************************************
		all specified, look for duplicate
		**/
		if($data_arr['metrics_sector_industry_id']==""){
			$metrics_sector_industry_id = 0;
		}else{
			$metrics_sector_industry_id = $data_arr['metrics_sector_industry_id'];
		}
		
		$q = "select count(*) as cnt from ".TP."ma_metrics_series where metrics_type_id='".$data_arr['metrics_type_id']."' and metrics_region_country_id='".$data_arr['metrics_region_country_id']."' and metrics_sector_industry_id='".$metrics_sector_industry_id."'";
		
		$success = $g_db->select_query($q);
		if(!$success){
			return false;
		}
		$row = $g_db->get_row();
		if($row['cnt'] > 0){
			//data exists
			$validation_passed = false;
			$err_arr['metrics_type_id'] = "Series exists";
		}
		if(!$validation_passed){
			return true;
		}
		/**************************************************************
		add
		***/
		$q = "insert into ".TP."ma_metrics_series set metrics_type_id='".$data_arr['metrics_type_id']."', metrics_region_country_id='".$data_arr['metrics_region_country_id']."', metrics_sector_industry_id='".$metrics_sector_industry_id."'";
		
		$success = $g_db->mod_query($q);
		if(!$success){
			return false;
		}
		return true;

		
	}
	
	public function admin_delete_series($series_id){
		global $g_db;
		
		/*****************************************
		first delete the data ponts for the series
		**/
		$q = "delete from ".TP."ma_metrics_data where metrics_series_id='".$series_id."'";
		$success = $g_db->mod_query($q);
		if(!$success){
			return false;
		}
		/***********************
		now delete the series
		**/
		$q = "delete from ".TP."ma_metrics_series where series_id='".$series_id."'";
		$success = $g_db->mod_query($q);
		return $success;
	}
	public function admin_get_series_data_points($series_id,&$data_arr,&$data_count){
		global $g_db;
		
		$q = "select data_id,`year`,`quarter`,`value` from ".TP."ma_metrics_data where metrics_series_id='".$series_id."' order by `year`,`quarter`";
		
		$success = $g_db->select_query($q);
		if(!$success){
			return false;
		}
		$data_count = $g_db->row_count();
		if($data_count == 0){
			//no data, return
			return true;
		}
		$data_arr = $g_db->get_result_set_as_array();
		return true;
	}
	
	public function admin_add_data_point($series_id,$data_arr,&$validation_passed,&$err_arr){
		global $g_db;
		
		$validation_passed = true;
		if($data_arr['year'] == ""){
			$validation_passed = false;
			$err_arr['year'] = "Please specify the year";
		}
		
		if($data_arr['quarter'] == ""){
			$validation_passed = false;
			$err_arr['quarter'] = "Please specify the quarter";
		}
		
		if($data_arr['value'] == ""){
			$validation_passed = false;
			$err_arr['value'] = "Please specify the value";
		}
		
		if(!$validation_passed){
			return true;
		}
		/***********************************************************
		all specified, look for duplicate
		**/
		
		
		$q = "select count(*) as cnt from ".TP."ma_metrics_data where metrics_series_id='".$data_arr['series_id']."' and `year`='".$data_arr['year']."' and `quarter`='".$data_arr['quarter']."'";
		
		$success = $g_db->select_query($q);
		if(!$success){
			return false;
		}
		$row = $g_db->get_row();
		if($row['cnt'] > 0){
			//data exists
			$validation_passed = false;
			$err_arr['value'] = "This data point exists in the series";
		}
		if(!$validation_passed){
			return true;
		}
		/**************************************************************
		add
		***/
		$q = "insert into ".TP."ma_metrics_data set metrics_series_id='".$series_id."',`year`='".$data_arr['year']."', `quarter`='".$data_arr['quarter']."', `value`='".$data_arr['value']."'";
		
		$success = $g_db->mod_query($q);
		if(!$success){
			return false;
		}
		return true;

		
	}
	
	public function get_all_types(&$data_arr,&$data_count){
		global $g_db;
		
		$q = "select type_id,type_name from ".TP."ma_metrics_types";
		$success = $g_db->select_query($q);
		if(!$success){
			return false;
		}
		$data_count = $g_db->row_count();
		if($data_count == 0){
			//no data, return
			return true;
		}
		$data_arr = $g_db->get_result_set_as_array();
		return true;
	}
	
	public function get_all_region_list(&$data_arr,&$data_count){
		global $g_db;
		
		$q = "select id,name from ".TP."ma_metrics_region_country where is_region='y' order by name";
		$success = $g_db->select_query($q);
		if(!$success){
			return false;
		}
		$data_count = $g_db->row_count();
		if($data_count == 0){
			//no data, return
			return true;
		}
		$data_arr = $g_db->get_result_set_as_array();
		return true;
	}
	
	public function get_all_country_list(&$data_arr,&$data_count){
		global $g_db;
		
		$q = "select id,name from ".TP."ma_metrics_region_country where is_region='n' order by name";
		$success = $g_db->select_query($q);
		if(!$success){
			return false;
		}
		$data_count = $g_db->row_count();
		if($data_count == 0){
			//no data, return
			return true;
		}
		$data_arr = $g_db->get_result_set_as_array();
		return true;
	}
	
	public function get_all_sector_list(&$data_arr,&$data_count){
		global $g_db;
		
		$q = "select id,name from ".TP."ma_metrics_sector_industry where is_sector='y' order by name";
		$success = $g_db->select_query($q);
		if(!$success){
			return false;
		}
		$data_count = $g_db->row_count();
		if($data_count == 0){
			//no data, return
			return true;
		}
		$data_arr = $g_db->get_result_set_as_array();
		return true;
	}
	
	public function get_all_industry_list(&$data_arr,&$data_count){
		global $g_db;
		
		$q = "select id,name from ".TP."ma_metrics_sector_industry where is_sector='n' order by name";
		$success = $g_db->select_query($q);
		if(!$success){
			return false;
		}
		$data_count = $g_db->row_count();
		if($data_count == 0){
			//no data, return
			return true;
		}
		$data_arr = $g_db->get_result_set_as_array();
		return true;
	}
	
	/******************************************
	get initial region/country id and sector/industry id for which there is data points for both metrics
	and use that to fetch data for the initial charts on page load
	**************************/
	function get_featured_series(&$has_data,&$featured_metrics_region_country_id,&$featured_metrics_sector_industry_id){
		global $g_db;
		
		$q = "SELECT metrics_region_country_id, metrics_sector_industry_id, count( * ) AS cnt FROM (SELECT metrics_type_id, metrics_region_country_id, metrics_sector_industry_id FROM ".TP."ma_metrics_data AS d LEFT JOIN ".TP."ma_metrics_series AS s ON ( d.metrics_series_id = s.series_id ) WHERE metrics_sector_industry_id != '0' GROUP BY metrics_type_id, metrics_region_country_id, metrics_sector_industry_id) AS A GROUP BY metrics_region_country_id, metrics_sector_industry_id having cnt > '1' order by rand() limit 0,1";
		
		$success = $g_db->select_query($q);
		if(!$success){
			return false;
		}
		
		$has_data = $g_db->has_row();
		if(!$has_data){
			return true;
		}
		
		$row = $g_db->get_row();
		$featured_metrics_region_country_id = $row['metrics_region_country_id'];
		$featured_metrics_sector_industry_id = $row['metrics_sector_industry_id'];
		return true;
	}
	/***************************************************
	param_arr
	metrics_type_id [what kind of chart data?]
	metrics_region_country_id
	metrics_sector_industry_id
	********************/
	public function ajax_front_fetch_series_data($param_arr,&$result_arr){
		global $g_db;
		/******************************************************************
		validation
		****/
		if(!isset($param_arr['metrics_type_id'])||($param_arr['metrics_type_id']=="")){
			$result_arr['has_data'] = 0;
			$result_arr['msg'] = "Metrics type not specified";
			return true;
		}
		/***************
		we can either send region id or country id.
		if country is present, then it override region (since country make it more specific)
		So, check the country first. If present, we use that
		Else we check for region and use that if present.
		If both absent, it is error
		************/
		if(isset($param_arr['country_id'])&&($param_arr['country_id']!="")){
			$metrics_region_country_id = $param_arr['country_id'];
		}else{
			//country not present, check for region
			if(isset($param_arr['region_id'])&&($param_arr['region_id']!="")){
				$metrics_region_country_id = $param_arr['region_id'];
			}else{
				//both absent, error
				$result_arr['has_data'] = 0;
				$result_arr['msg'] = "Region or Country not specified";
				return true;
			}
		}
		
		/***************
		we can either send sector id or industry id.
		if industry is present, then it override sector (since industry make it more specific)
		So, check the industry first. If present, we use that
		Else we check for sector and use that if present.
		If both absent, it is error
		************/
		if(isset($param_arr['industry_id'])&&($param_arr['industry_id']!="")){
			$metrics_sector_industry_id = $param_arr['industry_id'];
		}else{
			//industry not present, check for sector
			if(isset($param_arr['sector_id'])&&($param_arr['sector_id']!="")){
				$metrics_sector_industry_id = $param_arr['sector_id'];
			}else{
				//both absent, error
				$result_arr['has_data'] = 0;
				$result_arr['msg'] = "Sector or Industry not specified";
				return true;
			}
		}
		return $this->ajax_fetch_series_data($metrics_region_country_id,$metrics_sector_industry_id,$param_arr['metrics_type_id'],$result_arr);
	}
	/*******************************************
	result
	has_data: 1 if there is data to return
	msg: to send message to the caller
	region_country_title: name of the region or country, used to create the label
	sector_industry_title: name of the sector or industry, used to create the label
	points: array of data points for the given region/country and sector/industry
	avg: array of data points for the given region/country and average series
	labels: array of the year-quarters, to be used as lebels ox X axis
	*********************************************/
	public function ajax_fetch_series_data($metrics_region_country_id,$metrics_sector_industry_id,$metric_type_id,&$result_arr){
		global $g_db;
		/*******************************************************************
		we need to generate title, so get the region/country name and sector/industry name
		***/
		$q = "select name from ".TP."ma_metrics_region_country where id='".$metrics_region_country_id."'";
		$success = $g_db->select_query($q);
		if(!$success){
			return false;
		}
		if(!$g_db->has_row()){
			//invalid region/country
			$result_arr['has_data'] = 0;
			$result_arr['msg'] = "Invalid region or country specified";
			return true;
		}
		$row = $g_db->get_row();
		$result_arr['region_country_title'] = $row['name'];
		/*************************************************************************/
		$q = "select name from ".TP."ma_metrics_sector_industry where id='".$metrics_sector_industry_id."'";
		$success = $g_db->select_query($q);
		if(!$success){
			return false;
		}
		if(!$g_db->has_row()){
			//invalid sector/industry
			$result_arr['has_data'] = 0;
			$result_arr['msg'] = "Invalid sector or industry specified";
			return true;
		}
		$row = $g_db->get_row();
		$result_arr['sector_industry_title'] = $row['name'];
		
		/****************************************************************
		get the data series as well as the average series. For average series, the sector_industry_id is 0
		since we are returning 2 series, we write a self join query to get the two series in separate columns instead of
		two similar tables
		
		The labels are like 1Q 09
		**/
		$q = "select * from ((select `year`,`quarter`,concat(`quarter`,'Q',' ',substring(`year`,3)) as series_yq, `value` as series_value from ".TP."ma_metrics_data as d left join ".TP."ma_metrics_series as s on(d.metrics_series_id=s.series_id) where metrics_type_id='".$metric_type_id."' and metrics_region_country_id='".$metrics_region_country_id."' and metrics_sector_industry_id='".$metrics_sector_industry_id."' order by `year`,`quarter`) as series left join (select `year`,`quarter`,concat(`quarter`,'Q',' ',substring(`year`,3)) as avg_yq,`value` as avg_value from ".TP."ma_metrics_data as d left join ".TP."ma_metrics_series as s on(d.metrics_series_id=s.series_id) where metrics_type_id='".$metric_type_id."' and metrics_region_country_id='".$metrics_region_country_id."' and metrics_sector_industry_id='0' order by `year`,`quarter`) as avg_series on series.series_yq=avg_series.avg_yq)";
		
		$success = $g_db->select_query($q);
		if(!$success){
			return false;
		}
		
		$data_count = $g_db->row_count();
		if(0 == $data_count){
			//no data
			$result_arr['has_data'] = 0;
			$result_arr['msg'] = "No data points found";
			return true;
		}
		
		/*********************************************************************
		has data, so create the 3 arrays
		right now, we do not check for null or break in the year Quarter series
		****/
		$result_arr['has_data'] = 1;
		$result_arr['points'] = array();
		$result_arr['avg'] = array();
		$result_arr['labels'] = array();
		
		for($j=0;$j<$data_count;$j++){
			$row = $g_db->get_row();
			$result_arr['points'][$j] = (float)$row['series_value'];
			$result_arr['avg'][$j] = (float)$row['avg_value'];
			/**************
			sng:18/oct/2011
			hack, if some data point is not available, we are storing 0.0
			We send null so that the caller knows that there is no data.
			
			This also helps with our chart renderer jqplot. For the LineRenderer
			the line can break on NULL
			******************/
			if($result_arr['points'][$j]==0.0){
				$result_arr['points'][$j]=NULL;
			}
			if($result_arr['avg'][$j]==0.0){
				$result_arr['avg'][$j]=NULL;
			}
			$result_arr['labels'][$j] = $row['series_yq'];
		}
		return true;
	}
}
$g_ma_metrics = new ma_metrics();
?>