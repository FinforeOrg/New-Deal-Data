<?php
/****
sng: 18/mar/2010
methods regarding regions and countries
Countries are grouped into regions
*******/
require_once("classes/class.magic_quote.php");
class country{
	public function get_all_country_list(&$data_arr,&$data_count){
		$q = "select * from ".TP."country_master order by name";
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
	
	public function get_all_country_list_for_region($region_id,&$data_arr,&$data_count){
		$q = "select rc.id,rc.region_id,c.id as country_id,c.name from ".TP."region_country_list as rc left join ".TP."country_master as c on(rc.country_id=c.id) where region_id='".$region_id."' order by c.name";
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
	
	public function remove_country_from_region($region_id,$country_id,&$msg){
		$q = "delete from ".TP."region_country_list where region_id='".$region_id."' and country_id='".$country_id."'";
		$result = mysql_query($q);
		if(!$result){
			return false;
		}
		$msg = "The country is removed from this region";
		return true;
	}
	
	public function get_all_region_list(&$data_arr,&$data_count){
		/*************************
		sng:23/feb/2011
		we only get active region list
		
		sng:24/feb/2011
		We now order by display order
		************************/
		$q = "select * from ".TP."region_master where is_active='y' order by display_order,name";
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
	/***************
	sng:23/feb/2011
	Now that the inactive regions are not shown, we need another method for admin to see active/inactive
	
	sng:24/feb/2011
	We need to sort by display order
	*****************/
	public function admin_get_all_region_list(&$data_arr,&$data_count){
		$q = "select * from ".TP."region_master order by display_order,name";
		$res = mysql_query($q);
		if(!$res){
			//echo mysql_error();
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
	
	public function get_region_data($region_id,&$data_arr){
		$q = "select * from ".TP."region_master where id='".$region_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$data_count = mysql_num_rows($res);
		if($data_count == 0){
			//no such region?
			return false;
		}
		//recs so
		$data_arr = mysql_fetch_assoc($res);
		return true;
	}
	
	public function remove_region($region_id,&$msg){
		//to remove a region, remove the countries for this region first from region country mapping, then remove the region
		$q = "delete from ".TP."region_country_list where region_id='".$region_id."'";
		$result = mysql_query($q);
		if(!$result){
			return false;
		}
		//now delete the region
		$q = "delete from ".TP."region_master where id='".$region_id."'";
		$result = mysql_query($q);
		if(!$result){
			return false;
		}
		$msg = "The region is moved";
		return true;
	}
	
	/*******************
	sng:23/feb/2011
	support to activate/deactivate region
	***************/
	public function toggle_region_active($region_id,$is_active,&$msg){
		
		$q = "update ".TP."region_master set is_active='".$is_active."' where id='".$region_id."'";
		$result = mysql_query($q);
		if(!$result){
			return false;
		}
		$msg = "The region is";
		if($is_active=='y'){
			$msg.=" active";
		}else{
			$msg.=" inactive";
		}
		return true;
	}
	
	/*******************
	sng:24/feb/2011
	support to activate/deactivate region
	***************/
	public function set_region_display_order($region_id,$display_order,&$msg){
		
		$q = "update ".TP."region_master set display_order='".$display_order."' where id='".$region_id."'";
		$result = mysql_query($q);
		if(!$result){
			return false;
		}
		$msg = "The display order has been updated";
		
		return true;
	}
	
	public function create_country($data_arr,&$validation_passed,&$err_arr){
		//validation
		$validation_passed = true;
		if($data_arr['name'] == ""){
			$err_arr['name'] = "Please specify the country name";
			$validation_passed = false;
		}else{
			//check for duplicate country name
			$q = "select count(name) as cnt from ".TP."country_master where name='".$data_arr['name']."'";
			$res = mysql_query($q);
			if(!$res){
				return false;
			}
			$row = mysql_fetch_assoc($res);
			if($row['cnt'] > 0){
				//this country name exists
				$err_arr['name'] = "This country name exists, specify another one.";
				$validation_passed = false;
			}
		}
		
		
		/////////////////////////////////////
		if(!$validation_passed){
			//no need to proceed
			return true;
		}
		///////////////////////////////////////////////////////
		//insert data
		$q = "insert into ".TP."country_master set name='".$data_arr['name']."'";
		$result = mysql_query($q);
		if(!$result){
			return false;
		}
		/////////////////
		//data inserted
		$validation_passed = true;
		return true;
	}
	
	public function create_region($data_arr,&$validation_passed,&$err_arr){
		//validation
		$validation_passed = true;
		if($data_arr['name'] == ""){
			$err_arr['name'] = "Please specify the region name";
			$validation_passed = false;
		}else{
			//check for duplicate region name
			$q = "select count(name) as cnt from ".TP."region_master where name='".$data_arr['name']."'";
			$res = mysql_query($q);
			if(!$res){
				return false;
			}
			$row = mysql_fetch_assoc($res);
			if($row['cnt'] > 0){
				//this country name exists
				$err_arr['name'] = "This region name exists, specify another one.";
				$validation_passed = false;
			}
		}
		
		
		/////////////////////////////////////
		if(!$validation_passed){
			//no need to proceed
			return true;
		}
		///////////////////////////////////////////////////////
		//insert data
		$q = "insert into ".TP."region_master set name='".$data_arr['name']."'";
		$result = mysql_query($q);
		if(!$result){
			return false;
		}
		/////////////////
		//data inserted
		$validation_passed = true;
		return true;
	}
	
	public function add_country_to_region($region_id,$country_id,&$validation_passed,&$err_arr){
		//validation
		
		$validation_passed = true;
		if($country_id == ""){
			$err_arr['country'] = "Please specify the country";
			$validation_passed = false;
		}else{
			//check for duplicate country entry for this region
			$q = "select count(id) as cnt from ".TP."region_country_list where region_id='".$region_id."' and country_id='".$country_id."'";
			$res = mysql_query($q);
			if(!$res){
				return false;
			}
			$row = mysql_fetch_assoc($res);
			if($row['cnt'] > 0){
				//this country exists for this region
				$err_arr['country'] = "This country has already been added to this region, specify another one.";
				$validation_passed = false;
			}
		}
		
		/////////////////////////////////////
		if(!$validation_passed){
			//no need to proceed
			return true;
		}
		///////////////////////////////////////////////////////
		//insert data
		$q = "insert into ".TP."region_country_list set region_id='".$region_id."', country_id='".$country_id."'";
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
$g_country = new country(); 
?>