<?php
/****
sng: 18/mar/2010
methods regarding companies

if company id is on some more table, update delete_company
*******/
require_once("classes/class.magic_quote.php");
require_once("classes/class.image_util.php");
require_once("classes/db.php");
class company{
	
	/*********************
	sng:28/sep/2012
	We need uniformity in the logo thumbnail size, so we define constants here
	************/
	private $thumb_fit_width = 200;
	private $thumb_fit_height = 200;
	
	public function get_all_company_list(&$data_arr,&$data_count){
		global $g_mc;
		$q = "select * from ".TP."company order by name";
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
			$row['brief_desc'] = $g_mc->db_to_view($row['brief_desc']);
			$data_arr[] = $row;
		}
		return true;
	}
	
	/****
	sng:4/jun/2010
	function to get the firms marked as top firms
	*******/
	public function get_top_firms_list(&$data_arr,&$data_count){
		global $g_mc;
		
		$q = "select company_id,name,type,logo,is_top_firm from ".TP."company where is_top_firm='Y' order by type,name";
		
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		//////////////////////////////
		$data_count = mysql_num_rows($res);
		if(0==$data_count){
			
			//no data so get out
			return true;
		}
		
		////////////////////////////
		for($i=0;$i<$data_count;$i++){
			$data_arr[$i] = mysql_fetch_assoc($res);
			$data_arr[$i]['name'] = $g_mc->db_to_view($data_arr[$i]['name']);
		}
		
		return true;
	}
	/****
	sng:4/jun/2010
	function to get the firms marked as top firms, filter by type, used by front end
	
	sng: 19/jul/2010
	Now, there can be default top firms and top firms in some category. If category id is blank, use the default, that
	is get the firms marked as top firm, else, get the category if and get the appropriate firms
	
	sng:22/july/2010
	Now, admin can mark a category as default. So if no category is sent, use that category
	*******/
	public function front_get_top_firms_list_by_type($company_type,$top_firm_cat_id,&$data_arr,&$data_count){
		global $g_mc;
		
		if($top_firm_cat_id == ""){
			//get the category id of default category
			$def_q = "select id from ".TP."top_firm_categories where is_default='Y'";
			$def_q_res = mysql_query($def_q);
			if(!$def_q){
				return false;
			}
			$def_q_res_cnt = mysql_num_rows($def_q_res);
			if(0==$def_q_res_cnt){
				//no default set, error
				return false;
			}
			$def_q_res_row = mysql_fetch_assoc($def_q_res);
			$top_firm_cat_id = $def_q_res_row['id'];
		}else{
			//already set
		}
		$q = "select c.company_id,name,type,logo,is_top_firm from ".TP."top_firm_list as t left join ".TP."company as c on(t.company_id=c.company_id) where t.top_cat_id='".$top_firm_cat_id."' and t.firm_type='".$company_type."'";
			//$q = "select company_id,name,type,logo,is_top_firm from ".TP."company where is_top_firm='Y' and type='".$company_type."'";
		
		
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		//////////////////////////////
		$data_count = mysql_num_rows($res);
		if(0==$data_count){
			
			//no data so get out
			return true;
		}
		
		////////////////////////////
		for($i=0;$i<$data_count;$i++){
			$data_arr[$i] = mysql_fetch_assoc($res);
			$data_arr[$i]['name'] = $g_mc->db_to_view($data_arr[$i]['name']);
		}
		
		return true;
	}
	
	/*************
	sng:9/jul/2010
	Now the top firms are not simple yes/no. They are now categorized
	*********/
	public function get_all_top_firms_categories(&$data_arr,&$data_count){
		global $g_mc;
		
		$q = "select * from ".TP."top_firm_categories";
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
	
	public function add_top_firms_category($param_arr,&$validation_passed,&$err_arr){
		global $g_mc;
		//validation
		$validation_passed = true;
		if($param_arr['name'] == ""){
			$validation_passed = false;
			$err_arr['name'] = "Please specify the category name";
		}
		
		if(!$validation_passed){
			return true;
		}
		//not checking for duplicate category name. Hope admin will not mess
		//insert
		$q = "insert into ".TP."top_firm_categories set name='".$g_mc->view_to_db($param_arr['name'])."'";
		$result = mysql_query($q);
		if(!$result){
			return false;
		}
		return true;
	}
	
	public function admin_get_top_firms($cat_id,$firm_type,&$data_arr,&$data_count){
		global $g_mc;
		$q = "select t.*,c.name from ".TP."top_firm_list as t left join ".TP."company as c on(t.company_id=c.company_id) where top_cat_id='".$cat_id."' and firm_type='".$firm_type."'";
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
	Now admin type the bank / law firm name and hint appears and admin select a firm
	This sets an id. So if id is not sent, it either means no firm is selected or something is typed
	which does not exists
	***/
	public function add_top_firm($data_arr,$cat_id,$firm_type,&$validation_passed,&$err_arr){
		global $g_mc;
		//validation
		$validation_passed = true;
		//first check if name is sent or not
		if($data_arr['firm_name']==""){
			$err_arr['company_id'] = "Please specify the ".$firm_type." name";
			$validation_passed = false;
			return true;
		}
		//if it comes here, something was typed
		if($data_arr['company_id'] == ""){
			$err_arr['company_id'] = "The ".$firm_type." name was not found";
			$validation_passed = false;
		}else{
			//company of this id cannot be added to this category twice, so check
			$q = "select count(*) as cnt from ".TP."top_firm_list where top_cat_id='".$cat_id."' and company_id='".$data_arr['company_id']."'";
			$res = mysql_query($q);
			if(!$res){
				return false;
			}
			$row = mysql_fetch_assoc($res);
			if($row['cnt']!=0){
				//this company is already in this top firm category
				$err_arr['company_id'] = "This has already been added to the category";
				$validation_passed = false;
			}
		}
		
		/////////////////////////////////////
		if(!$validation_passed){
			//no need to proceed
			return true;
		}
		//insert
		$q = "insert into ".TP."top_firm_list set company_id='".$data_arr['company_id']."', top_cat_id='".$cat_id."', firm_type='".$firm_type."'";
		$result = mysql_query($q);
		if(!$result){
			return false;
		}
		return true;
	}
	
	public function remove_top_firm($id,&$msg){
		$q = "delete from ".TP."top_firm_list where id='".$id."'";
		$result = mysql_query($q);
		if(!$result){
			return false;
		}
		$msg = "Deleted";
		return true;
	}
	
	/***
	22/july/2010
	Mark a cat as default. Since only one can be default, we first make all others non-default
	***/
	public function mark_top_firms_category_as_default($id){
		$q = "update ".TP."top_firm_categories set is_default='N'";
		$result = mysql_query($q);
		if(!$result){
			return false;
		}
		//now mark
		$q = "update ".TP."top_firm_categories set is_default='Y' where id='".$id."'";
		$result = mysql_query($q);
		if(!$result){
			return false;
		}
		return true;
	}
	/*****************************************************************************************/
	public function get_all_company_name_list_by_type($type,&$data_arr,&$data_count){
		global $g_mc;
		
		$q = "select company_id,name,type from ".TP."company where type='".$type."' order by name";
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
	/********
	sng:6/apr/2010
	get the list of companies that match the type and name.
	For name, either it can be strict match of the or match first few letters
	**********/
	public function filter_company_name_list_by_type_name($type,$name,$strict,&$data_arr,&$data_count){
		global $g_mc;
		
		$q = "select company_id,name,type from ".TP."company where type='".$type."'";
		if($strict){
			$q.=" and name='".$name."'";
		}else{
			$q.=" and name like '".$name."%'";
		}
		$q.=" order by name";
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
	
	public function ajax_get_company_name_list_by_type_name($name,$type,$num_to_fetch,&$data_arr,&$data_count){
		global $g_mc;
		
		$q = "select company_id,name,type from ".TP."company where type='".$type."' and name like '".$name."%' order by name limit 0,".$num_to_fetch;
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
	
	
	public function get_all_company_list_paged($start_offset,$num_to_fetch,&$data_arr,&$data_count){
		global $g_mc;
		
		$q = "select * from ".TP."company order by name limit ".$start_offset.",".$num_to_fetch;
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
			$row['brief_desc'] = $g_mc->db_to_view($row['brief_desc']);
			$data_arr[] = $row;
		}
		return true;
	}
	
	/***
	sng:7/july/2010
	utility for admin to see the companies that are without logo
	This takes the type of firm as parameter
	*********/
	public function get_all_firm_without_logo_list_paged($start_offset,$num_to_fetch,$company_type,&$data_arr,&$data_count){
		global $g_mc;
		
		$q = "select company_id,name from ".TP."company where logo='' order by name limit ".$start_offset.",".$num_to_fetch;
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
	sng:16/aug/2010
	utility to get all companies (not banks/law firms) that are not associated with any deals
	
	sng:21/sep/2011
	we also need the logo of the company
	****/
	public function get_all_company_without_deal_list_paged($start_offset,$num_to_fetch,&$data_arr,&$data_count){
		global $g_mc;
		
		$q = "select c.company_id,c.name,c.logo from (select company_id,name,logo from ".TP."company where type='company') as c left join (select distinct company_id from ".TP."transaction) as t on(c.company_id=t.company_id) where t.company_id is NULL limit ".$start_offset.",".$num_to_fetch;
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
	
	/*******
	sng:22/sep/2010
	utility to get all banks/law firms that are not as partners in any transaction
	**************/
	public function get_all_firm_without_deal_list_paged($type,$start_offset,$num_to_fetch,&$data_arr,&$data_count){
		global $g_mc;
		
		$q = "select c.company_id,c.name from (select company_id,name from ".TP."company where type='".$type."') as c left join (select distinct partner_id from ".TP."transaction_partners where partner_type='".$type."') as t on(c.company_id=t.partner_id) where t.partner_id is NULL limit ".$start_offset.",".$num_to_fetch;
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
	sng:13/aug/2010
	utility for admin to see the extra companies that are without logo
	The extra companies are seller or target companies in M&A deals.
	Currently we do not create a company entry for those
	*********/
	public function get_all_extra_company_without_logo_list_paged($start_offset,$num_to_fetch,&$data_arr,&$data_count){
		global $g_mc;
		/**
		Get the deal id, target company name from transaction data where deal type is M&A and target company logo is not present.
		Since we will do an Union, we somehow need a field that say whehter the company is target or seller.
		Get the deal id, seller company name from transaction data where deal type is M&A and seller company logo is not present.
		Since we will do an Union, we somehow need a field that say whehter the company is target or seller. There may not be a seller, in which case seller logo is not required for that deal
		Then we do union and from that total table we get limited rows
		***/

		$q = "select * from (SELECT id as deal_id, target_company_name AS company_name, 'target' AS company_type FROM ".TP."transaction WHERE deal_cat_name = 'M&A' AND target_company_logo = '' UNION SELECT id as deal_id, seller_company_name AS company_name, 'seller' AS company_type FROM ".TP."transaction WHERE deal_cat_name = 'M&A' AND seller_company_logo = '' AND seller_company_name != '') as A limit ".$start_offset.",".$num_to_fetch;
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
			$row['company_name'] = $g_mc->db_to_view($row['company_name']);
			$data_arr[] = $row;
		}
		return true;
	}
	/***
	sng:13/aug/2010
	function to get the logo from deal. We pass the company type: seller or target (see above) that select what field to use
	***/
	public function get_extra_company_logo($deal_id,$company_type,&$data){
		global $g_mc;
		if($company_type=="target"){
			$q = "select target_company_name as company_name,target_company_logo as logo from ".TP."transaction where id='".$deal_id."'";
		}else{
			if($company_type=="seller"){
				$q = "select seller_company_name as company_name,seller_company_logo as logo from ".TP."transaction where id='".$deal_id."'";
			}else{
				return false;
			}
		}
		
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$cnt = mysql_num_rows($res);
		if(0==$cnt){
			//no such deal?
			return false;
		}
		$data = mysql_fetch_assoc($res);
		$data['company_name'] = $g_mc->db_to_view($data['company_name']);
		return true;
	}
	
	/******************
	sng:28/sep/2012
	This was used when we wanted to have logo for seller company / target company in transaction table.
	However, we solved that by storing all the logo names in serialized form for a deal.
	
	Now we have one or more participating companies for deal (each with its own logo) so this is no longer needed
	*********************/
	public function edit_extra_company_logo($deal_id,$data_arr,$img_field_name,$image_destination_path,&$validation_passed,&$err_arr){
		return false;
	}
	
	/*****
	sng:23/july/2010
	utility for admin to see the companies for which the specified field is empty. This should be used only for companies of type
	company. Otherwise admin will be overwhelmed with banks and lawfirms for which most fields are blank
	********/
	public function get_all_companies_missing_info_paged($field_name,$company_type,$start_offset,$num_to_fetch,&$data_arr,&$data_count){
		global $g_mc;
		
		$q = "select company_id,name from ".TP."company where ".$field_name."='' and type='".$company_type."' order by name limit ".$start_offset.",".$num_to_fetch;
		//echo $q;
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
	
	/****
	sng:9/jul/2010
	Added support for the short_name field
	
	sng:6/feb/2011
	support for private_note
	*********/
	public function get_company($company_id,&$data_arr){
		global $g_mc;
		
		$q = "select * from ".TP."company where company_id='".$company_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$data_count = mysql_num_rows($res);
		if($data_count == 0){
			//no such company
			return false;
		}
		//recs so
		$data_arr = mysql_fetch_assoc($res);
		$data_arr['name'] = $g_mc->db_to_view($data_arr['name']);
		$data_arr['short_name'] = $g_mc->db_to_view($data_arr['short_name']);
		$data_arr['brief_desc'] = $g_mc->db_to_view($data_arr['brief_desc']);
		$data_arr['private_note'] = $g_mc->db_to_view($data_arr['private_note']);
		return true;
	}
	
	public function get_featured_company(&$data_arr){
		/****
		get a company id of type company marked as featured for front end
		if no record found, get a random company id from list of type cpmpany
		
		sng:19/apr/2010
		now admin do not set a company as featured, just get a random company id
		***********/
		//$q = "select company_id from ".TP."company where type='company' and is_featured='Y'";
		//$res = mysql_query($q);
		//if(!$res){
		//	return false;
		//}
		///////////////////////////////////////
		//$cnt = mysql_num_rows($res);
		//if(0==$cnt){
			//get a random id of type company
			$q_alt = "select company_id from ".TP."company where type='company' order by rand() limit 0,1";
			$q_alt_res = mysql_query($q_alt);
			if(!$q_alt_res){
				return false;
			}
			$cnt_alt = mysql_num_rows($q_alt_res);
			if(0==$cnt_alt){
				//no company of type company found, disaster
				return false;
			}else{
				$q_alt_res_row = mysql_fetch_assoc($q_alt_res);
				$featured_id = $q_alt_res_row['company_id'];
			}
		//}else{
			//featured found
		//	$res_row = mysql_fetch_assoc($res);
		//	$featured_id = $res_row['company_id'];
		//}
		//////////////////////////////////////////////////////////
		//get the company data
		$success = $this->get_company($featured_id,$data_arr);
		return $success;
	}
	
	public function mark_as_featured($company_id){
		//first unmark any record marked as featured
		$q = "update ".TP."company set is_featured='N' where is_featured='Y'";
		$result = mysql_query($q);
		if(!$result){
			return false;
		}
		/////////////////////////
		//mark the company as featured
		$q = "update ".TP."company set is_featured='Y' where company_id='".$company_id."'";
		$result = mysql_query($q);
		if(!$result){
			return false;
		}
		/////////
		return true;
	}
//////////////////////////////////sng:sector and industry master///////////////////////////////////////////////////////
	
	/***
	sng:30/mar/2010
	we streamlined this
	****/
	public function get_all_sector_list(&$data_arr,&$data_count){
		$q = "select distinct(sector) from ".TP."sector_industry_master";
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
	/***
	sng:30/mar/2010
	we streamlined this
	****/
	public function get_all_industry_list(&$data_arr,&$data_count){
		$q = "select distinct(industry) from ".TP."sector_industry_master";
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
	
	public function get_all_industry_for_sector($sector,&$data_arr,&$data_count){
		$q = "select distinct(industry) from ".TP."sector_industry_master where sector='".$sector."'";
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
	
	/******************
	1/sep/2010
	We allow admin to see the list of all sector/industry
	**********/
	public function get_all_sector_industry_list(&$data_arr,&$data_count){
		$q = "select * from ".TP."sector_industry_master order by sector,industry";
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
	
	/*********
	1/sep/2010
	We allow admin to enter sector industry
	****************/
	public function add_sector_industry($sector,$industry,&$validation_passed,&$err_arr){
		$validation_passed = true;
		if($sector == ""){
			$validation_passed = false;
			$err_arr['sector'] = "Specify sector";
		}
		if($industry == ""){
			$validation_passed = false;
			$err_arr['industry'] = "Specify industry";
		}
		if(!$validation_passed){
			return true;
		}
		//basic validation passed, check if duplicate
		$q = "select count(*) as cnt from ".TP."sector_industry_master where sector='".$sector."' and industry='".$industry."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$row = mysql_fetch_assoc($res);
		if($row['cnt'] > 0){
			//present
			$validation_passed = false;
			$err_arr['sector'] = "Sector industry exists";
		}
		if(!$validation_passed){
			return true;
		}
		//all passed
		$q = "insert into ".TP."sector_industry_master set sector='".$sector."',industry='".$industry."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		return true;
	}
	/*********
	sng:2/sep/2010
	we allow admin to delete sector/industry
	******/
	public function delete_sector_industry($id,&$msg){
		//get the sector industry name
		$q = "select * from ".TP."sector_industry_master where id='".$id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$cnt = mysql_num_rows($res);
		if(0 == $cnt){
			//not found
			$msg = "The sector/industry was not found";
			return true;
		}
		$row = mysql_fetch_assoc($res);
		$sector = $row['sector'];
		$industry = $row['industry'];
		////////////////////////////////////////////////////////
		//check if any company having that sector/indutry
		$q = "select count(*) as cnt from ".TP."company where sector='".$sector."' and industry='".$industry."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$row = mysql_fetch_assoc($res);
		if($row['cnt'] > 0){
			//present
			$msg = "This sector industry is attribute of one or more companies";
			return true;
		}
		//////////////////////////////////////////////////////
		//check if any deals has target company with this sector industry
		$q = "select count(*) as cnt from ".TP."transaction where target_sector='".$sector."' and target_industry='".$industry."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$row = mysql_fetch_assoc($res);
		if($row['cnt'] > 0){
			//present
			$msg = "This sector industry is attribute of one or more target companies in deals";
			return true;
		}
		//////////////////////////////////////////
		//check for seller sector
		$q = "select count(*) as cnt from ".TP."transaction where seller_sector='".$sector."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$row = mysql_fetch_assoc($res);
		if($row['cnt'] > 0){
			//present
			$msg = "This sector is attribute of one or more seller companies in deals";
			return true;
		}
		//none found, delete
		$q = "delete from ".TP."sector_industry_master where id='".$id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$msg = "deleted";
		return true;
	}
//////////////////////////////////sng:sector and industry master/////////////////////////////////////////////////////////
	
	
	
	
	public function add_company($data_arr,$img_field_name,$image_destination_path,&$validation_passed,&$err_arr){
	    $img_obj = new image_util();
		
		//validation
		$validation_passed = true;
		if($data_arr['name'] == ""){
			$err_arr['name'] = "Please specify the name";
			$validation_passed = false;
		}
		
		if($data_arr['type'] == ""){
			$err_arr['type'] = "Please specify the company type";
			$validation_passed = false;
		}
		
		if(($data_arr['name'] != "")&&($data_arr['type'] != "")){
			//check for duplicate company name
			/***
			sng:01/may/2010
			There can be a company which may have 2 entries. One as a company, another as a bank or law firm. So
			when we check for duplicate, we not only check on the name but also of the company type.
			So, there can be a Tomato Trash as a company and Tomato Trash as a bank.
			
			The problem is, we can do that test only when we have both the name and type
			*********/
			$q = "select count(*) as cnt from ".TP."company where name='".mysql_real_escape_string($data_arr['name'])."' and type='".$data_arr['type']."'";
			$res = mysql_query($q);
			if(!$res){
				return false;
			}
			$row = mysql_fetch_assoc($res);
			if($row['cnt'] > 0){
				//this company name exists
				$err_arr['name'] = "This ".$data_arr['type']." has already been added.";
				$validation_passed = false;
			}
		}
		if($data_arr['sector'] == ""){
			$err_arr['sector'] = "Please specify the sector of the company";
			$validation_passed = false;
		}
		if($data_arr['industry'] == ""){
			$err_arr['industry'] = "Please specify the industry of the company";
			$validation_passed = false;
		}
		if($data_arr['hq_country'] == ""){
			$err_arr['hq_country'] = "Please specify the head quarter of the company";
			$validation_passed = false;
		}
		if(!$validation_passed){
			//no need to proceed
			return true;
		}
		//try to upload logo if specified
		//upload company logo
		/////////////////////////////////////////////////////////////////////
		    if($_FILES[$img_field_name]['name']!='')
			{
				/***
				sng:23/sep/2010
				cannot have space in logo file name else problem when downloading to powerpoint
				
				sng:16/aug/2011
				we remove anything that is not alpha numeric or dot
				***/
				$noblank = clean_filename(basename($_FILES[$img_field_name]['name']));
				$upload_img_name = time()."_".$noblank;
				/******************
				sng:1/oct/2012
				We now directly create the logo thumb. The function checks whether the uploaded img is image file or not
				****************/
				$upload_src = $_FILES[$img_field_name]['tmp_name'];
				$success = $img_obj->create_resized($upload_src,$image_destination_path."/thumbnails",$upload_img_name,$this->thumb_fit_width,$this->thumb_fit_height,false);
				if(!$success){
					return false;
				}
			}
		///////////////////////////////////////////////////////
		//insert data
		/*********************
		sng:18/feb/2012
		Since admin is entering the data, we treat it as verified
		************************/
		$q = "insert into ".TP."company set name='".mysql_real_escape_string($data_arr['name'])."',type='".mysql_real_escape_string($data_arr['type'])."',industry='".mysql_real_escape_string($data_arr['industry'])."',sector='".mysql_real_escape_string($data_arr['sector'])."',hq_country='".mysql_real_escape_string($data_arr['hq_country'])."',logo='".$upload_img_name."',brief_desc='".mysql_real_escape_string($data_arr['brief_desc'])."',admin_verified='y'";
		$result = mysql_query($q);
		if(!$result){
			//echo mysql_error();
			return false;
		}
		/////////////////
		//data inserted
		$validation_passed = true;
		return true;
	}
	/***
	sng:7/may/2010
	same as adding a company, just, without any extra stuff, just the name, type, logo
	
	sng:9/jul/2010
	added support for abbreviated name for a bank/law firm. This is optional, since there is a default
	algorithm to generate abbreviated name (by taking first letter of each word).
	***/
	public function add_bank_lawfirm($data_arr,$img_field_name,$image_destination_path,&$validation_passed,&$err_arr){
		
	    $img_obj = new image_util();
		
		//validation
		$validation_passed = true;
		if($data_arr['name'] == ""){
			$err_arr['name'] = "Please specify the name";
			$validation_passed = false;
		}
		
		if($data_arr['type'] == ""){
			$err_arr['type'] = "Please specify the company type";
			$validation_passed = false;
		}
		
		if(($data_arr['name'] != "")&&($data_arr['type'] != "")){
			//check for duplicate company name
			/***
			sng:01/may/2010
			There can be a company which may have 2 entries. One as a company, another as a bank or law firm. So
			when we check for duplicate, we not only check on the name but also of the company type.
			So, there can be a Tomato Trash as a company and Tomato Trash as a bank.
			
			The problem is, we can do that test only when we have both the name and type
			*********/
			$q = "select count(*) as cnt from ".TP."company where name='".mysql_real_escape_string($data_arr['name'])."' and type='".$data_arr['type']."'";
			$res = mysql_query($q);
			if(!$res){
				return false;
			}
			$row = mysql_fetch_assoc($res);
			if($row['cnt'] > 0){
				//this company name exists
				$err_arr['name'] = "This ".$data_arr['type']." has already been added.";
				$validation_passed = false;
			}
		}
		
		if(!$validation_passed){
			//no need to proceed
			return true;
		}
		//try to upload logo if specified
		//upload company logo
		/////////////////////////////////////////////////////////////////////
		    if($_FILES[$img_field_name]['name']!='')
			{
				/***
				sng:23/sep/2010
				cannot have space in logo file name else problem when downloading to powerpoint
				
				sng:16/aug/2011
				we remove anything that is not alpha numeric or dot
				***/
				$noblank = clean_filename(basename($_FILES[$img_field_name]['name']));
				$upload_img_name = time()."_".$noblank;
				/******************
				sng:28/sep/2012
				We now directly create the logo thumb. The function checks whether the uploaded img is image file or not
				****************/
				$upload_src = $_FILES[$img_field_name]['tmp_name'];
				
				
				$success = $img_obj->create_resized($upload_src,$image_destination_path."/thumbnails",$upload_img_name,$this->thumb_fit_width,$this->thumb_fit_height,false);
				if(!$success){
					return false;
				}
			}
		///////////////////////////////////////////////////////
		//insert data
		/*********************
		sng:18/feb/2012
		Since admin is entering the data, we treat it as verified
		************************/
		$q = "insert into ".TP."company set name='".mysql_real_escape_string($data_arr['name'])."',short_name='".mysql_real_escape_string($data_arr['short_name'])."',type='".mysql_real_escape_string($data_arr['type'])."',logo='".$upload_img_name."',admin_verified='y'";
		$result = mysql_query($q);
		if(!$result){
			//echo mysql_error();
			return false;
		}
		/////////////////
		//data inserted
		$validation_passed = true;
		return true;
	}
	
	public function edit_company($company_id,$data_arr,$img_field_name,$image_destination_path,&$validation_passed,&$err_arr){
	    $img_obj = new image_util();
		
		//validation
		$validation_passed = true;
		
		if($data_arr['name'] == ""){
			$err_arr['name'] = "Please specify the name";
			$validation_passed = false;
		}
		
		if($data_arr['type'] == ""){
			$err_arr['type'] = "Please specify the company type";
			$validation_passed = false;
		}
		if(($data_arr['name'] != "")&&($data_arr['type'] != "")){
			//check for duplicate company name, considering other companies, and same type
			/***
			sng:01/may/2010
			There can be a company which may have 2 entries. One as a company, another as a bank or law firm. So
			when we check for duplicate, we not only check on the name but also of the company type.
			So, there can be a Tomato Trash as a company and Tomato Trash as a bank.
			
			The problem is, we can do that test only when we have both the name and type
			*********/
			$q = "select count(name) as cnt from ".TP."company where name='".mysql_real_escape_string($data_arr['name'])."' and type='".$data_arr['type']."' and company_id!='".$company_id."'";
			
			$res = mysql_query($q);
			if(!$res){
				return false;
			}
			$row = mysql_fetch_assoc($res);
			if($row['cnt'] > 0){
				//this company name exists
				$err_arr['name'] = "This ".$data_arr['type']." already exists.";
				$validation_passed = false;
			}
		}
		if($data_arr['sector'] == ""){
			$err_arr['sector'] = "Please specify the sector of the company";
			$validation_passed = false;
		}
		if($data_arr['industry'] == ""){
			$err_arr['industry'] = "Please specify the industry of the company";
			$validation_passed = false;
		}
		if($data_arr['hq_country'] == ""){
			$err_arr['hq_country'] = "Please specify the headquarter of the company";
			$validation_passed = false;
		}
		if(!$validation_passed){
			//no need to proceed
			return true;
		}
		///////////////////////////////////////////////////////
		//try to upload logo if specified
		//upload company logo
		/////////////////////////////////////////////////////////////////////
		    if($_FILES[$img_field_name]['name']!="")
			{
			   if($data_arr['backup_logo']!='')
				{
				/**********
				sng:20/sep/2011
				put a guard condition
				****************/
				if(file_exists($image_destination_path."/".$data_arr['backup_logo'])) unlink($image_destination_path."/".$data_arr['backup_logo']);
				if(file_exists($image_destination_path."/thumbnails/".$data_arr['backup_logo'])) unlink($image_destination_path."/thumbnails/".$data_arr['backup_logo']);
				}
				/***
				sng:23/sep/2010
				cannot have space in logo file name else problem when downloading to powerpoint
				
				sng:16/aug/2011
				we remove anything that is not alpha numeric or dot
				***/
				$noblank = clean_filename(basename($_FILES[$img_field_name]['name']));
				$upload_img_name = time()."_".$noblank;
				/******************
				sng:28/sep/2012
				We now directly create the logo thumb. The function checks whether the uploaded img is image file or not
				****************/
				$upload_src = $_FILES[$img_field_name]['tmp_name'];
				$success = $img_obj->create_resized($upload_src,$image_destination_path."/thumbnails",$upload_img_name,$this->thumb_fit_width,$this->thumb_fit_height,false);
				
				if(!$success){
					return false;
				}
			}
			else
			{
			  $upload_img_name = $data_arr['backup_logo'];
			}
			//////////////////////////////////////////////////////////////
		//insert data
		/**
		sng:1/apr/2010
		the company name has to be magic quoted
		
		sng:6/feb/2011
		support for private_note
		**/
		$q = "update ".TP."company set name= '".mysql_real_escape_string($data_arr['name'])."',type='".mysql_real_escape_string($data_arr['type'])."',industry='".mysql_real_escape_string($data_arr['industry'])."',sector='".mysql_real_escape_string($data_arr['sector'])."',hq_country='".mysql_real_escape_string($data_arr['hq_country'])."',logo='".$upload_img_name."',brief_desc='".mysql_real_escape_string($data_arr['brief_desc'])."',private_note='".mysql_real_escape_string($data_arr['private_note'])."' where company_id='".$company_id."'";
		$result = mysql_query($q);
		if(!$result){
			//echo mysql_error();
			return false;
		}
		/////////////////
		//data inserted
		$validation_passed = true;
		return true;
	}
	
	/***
	sng:9/jul/2010
	added support for the short name field. That field is optional
	********/
	public function edit_bank_lawfirm($company_id,$data_arr,$img_field_name,$image_destination_path,&$validation_passed,&$err_arr){
	    $img_obj = new image_util();
		
		//validation
		$validation_passed = true;
		
		if($data_arr['name'] == ""){
			$err_arr['name'] = "Please specify the name";
			$validation_passed = false;
		}
		
		if($data_arr['type'] == ""){
			$err_arr['type'] = "Please specify the company type";
			$validation_passed = false;
		}
		if(($data_arr['name'] != "")&&($data_arr['type'] != "")){
			//check for duplicate company name, considering other companies, and same type
			/***
			sng:01/may/2010
			There can be a company which may have 2 entries. One as a company, another as a bank or law firm. So
			when we check for duplicate, we not only check on the name but also of the company type.
			So, there can be a Tomato Trash as a company and Tomato Trash as a bank.
			
			The problem is, we can do that test only when we have both the name and type
			*********/
			$q = "select count(name) as cnt from ".TP."company where name='".mysql_real_escape_string($data_arr['name'])."' and type='".$data_arr['type']."' and company_id!='".$company_id."'";
			
			$res = mysql_query($q);
			if(!$res){
				return false;
			}
			$row = mysql_fetch_assoc($res);
			if($row['cnt'] > 0){
				//this company name exists
				$err_arr['name'] = "This ".$data_arr['type']." already exists.";
				$validation_passed = false;
			}
		}
		
		if(!$validation_passed){
			//no need to proceed
			return true;
		}
		///////////////////////////////////////////////////////
		//try to upload logo if specified
		//upload company logo
		/////////////////////////////////////////////////////////////////////
		    if($_FILES[$img_field_name]['name']!="")
			{
			   if($data_arr['backup_logo']!='')
				{
				/***********
				sng:20/sep/2011
				adding a guard condition
				
				sng:1/oct/2012
				now we create the thumbnail directly. We no longer store the uploaded image
				******************/
				
				if(file_exists($image_destination_path."/thumbnails/".$data_arr['backup_logo'])) unlink($image_destination_path."/thumbnails/".$data_arr['backup_logo']);
				}
				/***
				sng:23/sep/2010
				cannot have space in logo file name else problem when downloading to powerpoint
				
				sng:16/aug/2011
				we remove anything that is not alpha numeric or dot
				***/
				$noblank = clean_filename(basename($_FILES[$img_field_name]['name']));
				$upload_img_name = time()."_".$noblank;
				/******************
				sng:1/oct/2012
				We now directly create the logo thumb. The function checks whether the uploaded img is image file or not
				****************/
				$upload_src = $_FILES[$img_field_name]['tmp_name'];
				$success = $img_obj->create_resized($upload_src,$image_destination_path."/thumbnails",$upload_img_name,$this->thumb_fit_width,$this->thumb_fit_height,false);
				
				if(!$success){
					return false;
				}
			}
			else
			{
			  $upload_img_name = $data_arr['backup_logo'];
			}
			//////////////////////////////////////////////////////////////
		//insert data
		/**
		sng:1/apr/2010
		the company name has to be magic quoted
		
		sng:4/jun/2010
		There is a new attrib is_top_firm which decide whether this firm will be shown in top firm page or not
		
		sng:6/feb/2011
		support for private note
		**/
		$q = "update ".TP."company set name= '".mysql_real_escape_string($data_arr['name'])."',short_name='".mysql_real_escape_string($data_arr['short_name'])."',type='".$data_arr['type']."',logo='".$upload_img_name."', is_top_firm='".$data_arr['is_top_firm']."',private_note='".mysql_real_escape_string($data_arr['private_note'])."' where company_id='".$company_id."'";
		$result = mysql_query($q);
		if(!$result){
			//echo mysql_error();
			return false;
		}
		/////////////////
		//data inserted
		$validation_passed = true;
		return true;
	}
	
	/*****
	sng:5/apr/2010
	The DELETION code. If you add any angle, update this code
	
	sng: 24/sep/2010
	Updated
	*******/
	public function delete_company($company_id,$logo_path,&$msg){
		/****************************************************
		sng:28/july/2010
		check if it is in registration_favoured
		********/
		$q = "select count(*) as cnt from ".TP."registration_favoured where company_id='".$company_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$row = mysql_fetch_assoc($res);
		if($row['cnt'] > 0){
			$msg = "Cannot delete the company. This is associated with favoured emails";
			return true;
		}
		/*************************************************/
		//check if any member is associated with the company
		$q = "select count(mem_id) as cnt from ".TP."member where company_id='".$company_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$row = mysql_fetch_assoc($res);
		if($row['cnt'] > 0){
			$msg = "Cannot delete the company. A member is associated with it";
			return true;
		}
		////////////////////////////////////////////////////////////////
		//check member _work history
		$q = "select count(*) as cnt from ".TP."member_work_history where company_id='".$company_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$row = mysql_fetch_assoc($res);
		if($row['cnt'] > 0){
			$msg = "Cannot delete the company. A member used to work in this company. It is in work history";
			return true;
		}
		///////////////////////////////////////////////////////
		//we do not check the membership request table, because the company_id is not used there
		//We do not check top_firms_by_criteria. There is no easy way to check that in firm_data
		/////////////////////////////////////////////////
		//check transaction partner member
		$q = "select count(id) as cnt from ".TP."transaction_partner_members where partner_id='".$company_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$row = mysql_fetch_assoc($res);
		if($row['cnt'] > 0){
			$msg = "Cannot delete the company. A member of this company is associated in a transaction";
			return true;
		}
		/////////////////////////
		//check transaction partner
		$q = "select count(id) as cnt from ".TP."transaction_partners where partner_id='".$company_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$row = mysql_fetch_assoc($res);
		if($row['cnt'] > 0){
			$msg = "Cannot delete the company. This company is a partner in a transaction";
			return true;
		}
		//check transaction
		/*********************
		sng:14/feb/2012
		We no longer have a single company for a deal, we have a list of participants
		**********************/
		//$q = "select count(id) as cnt from ".TP."transaction where company_id='".$company_id."'";
		$q = "select count(id) as cnt from ".TP."transaction_companies where company_id='".$company_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$row = mysql_fetch_assoc($res);
		if($row['cnt'] > 0){
			$msg = "Cannot delete the company. This company is associated with a transaction";
			return true;
		}
		////////////////////////////////////////////////
		//check firm_chart
		$q = "select count(*) as cnt from ".TP."firm_chart where company_id='".$company_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$row = mysql_fetch_assoc($res);
		if($row['cnt'] > 0){
			//better safe than sorry. We are not deleting the entry.
			$msg = "Cannot delete the company. This company is associated with a firm chart";
			return true;
		}
		////////////////////////////////////////
		//check top_firm_list
		$q = "select count(*) as cnt from ".TP."top_firm_list where company_id='".$company_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$row = mysql_fetch_assoc($res);
		if($row['cnt'] > 0){
			//better safe than sorry. We are not deleting the entry.
			$msg = "Cannot delete the company. This company is a top firm";
			return true;
		}
		///////////////////////////////////////////
		//check top_search_request
		$q = "select count(*) as cnt from ".TP."top_search_request where company_id='".$company_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$row = mysql_fetch_assoc($res);
		if($row['cnt'] > 0){
			//better safe than sorry. We are not deleting the entry.
			$msg = "Cannot delete the company. This company is in a top search request";
			return true;
		}
		////////////////////////////////////////////////
		//check registration favored emails
		$q = "select count(*) as cnt from ".TP."registration_favoured where company_id='".$company_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$row = mysql_fetch_assoc($res);
		if($row['cnt'] > 0){
			$msg = "Cannot delete the company. This company is associated with a favoured email";
			return true;
		}
		//////////////////////////////
		//not associated with anything, so we can delete the row, but first we need the logo name
		$q = "select logo from ".TP."company where company_id='".$company_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$row = mysql_fetch_assoc($res);
		$logo_img = $row['logo'];
		if($logo_img!=""){
			/*********************
			sng:2/dec/2011
			we now use the constant LOGO_PATH when we call the function. That constant
			holds the logo path so all we need is the rest
			*******************/
			unlink($logo_path."/".$logo_img);
			unlink($logo_path."/thumbnails/".$logo_img);
		}
		//now delete
		$q = "delete from ".TP."company where company_id='".$company_id."'";
		$result = mysql_query($q);
		if(!$result){
			return false;
		}
		//////////////////////
		$msg = "Company deleted";
		return true;
	}
	////////////////////////////////////SNG front end company search////////////////////////////////////////////
	/***
	search only for companies and not banks or law firms
	match name or industry
	name is matched like search term + anything
	industry is matched like anything + search term + anything
	
	sng:19/apr/2010
	We put a support for the total count found
	
	sng:19/may/2010
	for industry, match search term + anything
	*****/
	public function front_company_search_paged($search_data,$start_offset,$num_to_fetch,&$data_arr,&$data_count,&$total_count){
		global $g_mc;
		
		$search_data = $g_mc->view_to_db($search_data);
		$total_count_q = "select count(*) as cnt from ".TP."company where type='company' and ( name like '".$search_data."%' or industry like '".$search_data."%') order by name";
		$total_count_q_res = mysql_query($total_count_q);
		if(!$total_count_q_res){
			return false;
		}
		$total_count_q_res_row = mysql_fetch_assoc($total_count_q_res);
		$total_count = $total_count_q_res_row['cnt'];
		
		$q = "select * from ".TP."company where type='company' and ( name like '".$search_data."%' or industry like '".$search_data."%') order by name limit ".$start_offset.",".$num_to_fetch;
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		//////////////////////////////
		$data_count = mysql_num_rows($res);
		if(0==$data_count){
			//no data so get out
			return true;
		}
		////////////////////////////
		for($i=0;$i<$data_count;$i++){
			$data_arr[$i] = mysql_fetch_assoc($res);
			$data_arr[$i]['name'] = $g_mc->db_to_view($data_arr[$i]['name']);
			$data_arr[$i]['brief_desc'] = $g_mc->db_to_view($data_arr[$i]['brief_desc']);
		}
		return true;
	}
	
	/*******
	sng:2/sep/2010
	Need another function to search for companied via drop down filters - region, country, sector, industry
	*********/
	public function front_company_extended_search_paged($search_params_arr,$start_offset,$num_to_fetch,&$data_arr,&$data_count,&$total_count){
		
		global $g_mc;
		$filter = "";
		
		$company_name = $g_mc->view_to_db($search_params_arr['company_name']);
		if($company_name!=""){
			$filter.=" and c.name like '".$company_name."%'";
		}
		
		//if country is specified, region is not considered
		if($search_params_arr['country']!=""){
			$filter.=" and hq_country='".$search_params_arr['country']."'";
		}else{
			//see if region is specified
			if($search_params_arr['region']!=""){
				//we need the country names for this region since hq_country store the name
				$filter.=" and hq_country IN(select name from ".TP."region_country_list as rc left join ".TP."country_master as cm on(rc.country_id=cm.id) where rc.region_id='".$search_params_arr['region']."')";
			}
		}
		
		if($search_params_arr['sector']!=""){
			$filter.=" and sector='".$search_params_arr['sector']."'";
		}
		
		if($search_params_arr['industry']!=""){
			$filter.=" and industry='".$search_params_arr['industry']."'";
		}
		//////////////////////////////////////////////////////////////////////
		//first build query for counting
		$total_count_q = "select count(*) as cnt from ".TP."company as c where type='company'";
		if($filter!=""){
			$total_count_q.=$filter;
		}
		$total_count_q.=" order by name";
		$total_count_q_res = mysql_query($total_count_q);
		if(!$total_count_q_res){
			return false;
		}
		$total_count_q_res_row = mysql_fetch_assoc($total_count_q_res);
		$total_count = $total_count_q_res_row['cnt'];
		////////////////////////////////////////////////////////////////////
		//now build query for search
		$q = "select * from ".TP."company as c where type='company'";
		if($filter!=""){
			$q.=$filter;
		}
		$q.=" order by name limit ".$start_offset.",".$num_to_fetch;
		
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		//////////////////////////////
		$data_count = mysql_num_rows($res);
		if(0==$data_count){
			//no data so get out
			return true;
		}
		////////////////////////////
		
		////////////////////////////
		for($i=0;$i<$data_count;$i++){
			$data_arr[$i] = mysql_fetch_assoc($res);
			$data_arr[$i]['name'] = $g_mc->db_to_view($data_arr[$i]['name']);
			$data_arr[$i]['brief_desc'] = $g_mc->db_to_view($data_arr[$i]['brief_desc']);
		}
		
		return true;
	}
	////////////////////////////////////SNG front end company search////////////////////////////////////////////
	/***
	sng:2/jun/2010
	This is used to search for banks or law firm
	***/
	public function front_firm_search_paged($search_data,$firm_type,$start_offset,$num_to_fetch,&$data_arr,&$data_count,&$total_count){
		global $g_mc;
		
		$search_data = $g_mc->view_to_db($search_data);
		$total_count_q = "select count(*) as cnt from ".TP."company where type='".$firm_type."' and name like '".$search_data."%' order by name";
		$total_count_q_res = mysql_query($total_count_q);
		if(!$total_count_q_res){
			return false;
		}
		$total_count_q_res_row = mysql_fetch_assoc($total_count_q_res);
		$total_count = $total_count_q_res_row['cnt'];
		
		$q = "select company_id,name,type,logo from ".TP."company where type='".$firm_type."' and name like '".$search_data."%' order by name limit ".$start_offset.",".$num_to_fetch;
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		//////////////////////////////
		$data_count = mysql_num_rows($res);
		if(0==$data_count){
			//no data so get out
			return true;
		}
		////////////////////////////
		for($i=0;$i<$data_count;$i++){
			$data_arr[$i] = mysql_fetch_assoc($res);
			$data_arr[$i]['name'] = $g_mc->db_to_view($data_arr[$i]['name']);
		}
		return true;
	}
	
	/***
	get company id of a competing company of this company.
	It has to be of same type, in same industry, but not this company
	*****/
	public function front_get_random_competing_company($company_id,&$rival_company_id,&$rival_found){
		//get the type and industry of this company
		$q = "select type,industry from ".TP."company where company_id='".$company_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$row = mysql_fetch_assoc($res);
		$type = $row['type'];
		$industry = $row['industry'];
		//now
		$q = "select company_id from ".TP."company where company_id!='".$company_id."' and type='".$type."' and industry='".$industry."' order by rand() limit 0,1";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$cnt = mysql_num_rows($res);
		if(0==$cnt){
			$rival_found = false;
			return true;
		}
		$row = mysql_fetch_assoc($res);
		$rival_company_id = $row['company_id'];
		$rival_found = true;
		return true;
	}
	/***
	function to search for company record
	This is for ADMIN
	At this moment, make a very simple search based on company name
	Like clause is used for name, first few chars are matched
	***/
	public function admin_search_for_company($search_params_arr,&$data_arr,&$data_count){
		
		global $g_mc;
		
		$search_name = $g_mc->view_to_db($search_params_arr['company_name']);
		$q = "select * from ".TP."company where name like '".$search_name."%'";
		
		if($search_params_arr['type']!=""){
			$q.=" AND type='".$search_params_arr['type']."'";
		}
		$q.=" order by name";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		//////////////////////////////
		$data_count = mysql_num_rows($res);
		if(0==$data_count){
			
			//no data so get out
			return true;
		}
		
		////////////////////////////
		for($i=0;$i<$data_count;$i++){
			$data_arr[$i] = mysql_fetch_assoc($res);
			$data_arr[$i]['name'] = $g_mc->db_to_view($data_arr[$i]['name']);
			$data_arr[$i]['brief_desc'] = $g_mc->db_to_view($data_arr[$i]['brief_desc']);
		}
		
		return true;
	}
	
	/*******
	sng: 2/sep/2010
	This is used to search for company of type company only, but this supports many more filters
	company_name: we just match the first few chars
	*************/
	public function admin_extended_search_for_company($search_params_arr,&$data_arr,&$data_count){
		global $g_mc;
		$filter = "";
		
		$company_name = $g_mc->view_to_db($search_params_arr['company_name']);
		if($company_name!=""){
			$filter.=" and c.name like '".$company_name."%'";
		}
		
		//if country is specified, region is not considered
		if($search_params_arr['country']!=""){
			$filter.=" and hq_country='".$search_params_arr['country']."'";
		}else{
			//see if region is specified
			if($search_params_arr['region']!=""){
				//we need the country names for this region since hq_country store the name
				$filter.=" and hq_country IN(select name from ".TP."region_country_list as rc left join ".TP."country_master as cm on(rc.country_id=cm.id) where rc.region_id='".$search_params_arr['region']."')";
			}
		}
		
		if($search_params_arr['sector']!=""){
			$filter.=" and sector='".$search_params_arr['sector']."'";
		}
		
		if($search_params_arr['industry']!=""){
			$filter.=" and industry='".$search_params_arr['industry']."'";
		}
		
		$q = "select * from ".TP."company as c where type='company'";
		if($filter!=""){
			$q.=$filter;
		}
		$q.=" order by name";
		//echo $q;
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		//////////////////////////////
		$data_count = mysql_num_rows($res);
		if(0==$data_count){
			
			//no data so get out
			return true;
		}
		
		////////////////////////////
		for($i=0;$i<$data_count;$i++){
			$data_arr[$i] = mysql_fetch_assoc($res);
			$data_arr[$i]['name'] = $g_mc->db_to_view($data_arr[$i]['name']);
			$data_arr[$i]['brief_desc'] = $g_mc->db_to_view($data_arr[$i]['brief_desc']);
		}
		
		return true;
	}
	/***********************************company identifiers*****************************************/
	/*********
	sng:7/sep/2011
	get the identifiers associated with the given company
	
	sng:9/dec/2011
	We now get all the identifiers from master list and the values for each for the particular company. This makes
	the UI easier as admin can see all the identifiers and can see for which ones values are missing.
	**************/
	public function admin_get_company_identifiers($company_id,&$data_arr,&$data_cnt){
		$db = new db();
		$q = "SELECT * FROM ".TP."company_identifier_master AS m LEFT JOIN (SELECT identifier_id as iden_id, value FROM ".TP."company_identifiers WHERE company_id = '".$company_id."') AS c ON ( m.identifier_id = c.iden_id ) ORDER BY m.name";
		$success = $db->select_query($q);
		if(!$success){
			return false;
		}
		$data_cnt = $db->row_count();
		if(0 == $data_cnt){
			//no data
			return true;
		}
		//has data
		$data_arr = $db->get_result_set_as_array();
		return true;
	}
	/***********************************
	sng:21/nov/2011
	We need to show the value for all the entries in identifier master.
	It may happen that a company may not have values for all the identifiers
	So let us create another function
	
	sng:8/dec/2011
	use an alias for identifier_id from company_identifiers, otherwise you will not get all
	the field value for identifier_id from company_identifier_master and there are codes that will need that field value
	even if it is not set for a company
	***********/
	public function front_get_company_identifiers($company_id,&$data_arr,&$data_cnt){
		$db = new db();
		$q = "SELECT * FROM ".TP."company_identifier_master AS m LEFT JOIN (SELECT identifier_id as iden_id, value FROM ".TP."company_identifiers WHERE company_id = '".$company_id."') AS c ON ( m.identifier_id = c.iden_id ) ORDER BY m.name";
		$success = $db->select_query($q);
		if(!$success){
			return false;
		}
		$data_cnt = $db->row_count();
		if(0 == $data_cnt){
			//no data
			return true;
		}
		//has data
		$data_arr = $db->get_result_set_as_array();
		return true;
	}
	
	
	/****************
	get the list of identifiers from master list
	****************/
	public function admin_get_identifier_options(&$data_arr,&$data_cnt){
		$db = new db();
		$q = "select * from ".TP."company_identifier_master";
		$success = $db->select_query($q);
		if(!$success){
			return false;
		}
		$data_cnt = $db->row_count();
		if(0 == $data_cnt){
			//no data
			return true;
		}
		//has data
		$data_arr = $db->get_result_set_as_array();
		return true;
	}
	/********************
	add identifier to the company
	***********************/
	public function admin_add_company_identifier($company_id,$identifier_id,$identifier_value,&$validation_passed,&$err_msg){
		$db = new db();
		$validation_passed = true;
		
		if($identifier_value==""){
			$validation_passed = false;
			$err_msg = "Please specify the identifier value";
		}
		if(!$validation_passed){
			return true;
		}
		//now do another check, for duplicate
		$has_identifier = false;
		$success = $this->has_company_identifier($company_id,$identifier_id,$has_identifier);
		if(!$success){
			return false;
		}
		if($has_identifier){
			$validation_passed = false;
			$err_msg = "This identifier exists";
		}
		if(!$validation_passed){
			return true;
		}
		//all validation passed
		//insert
		$q = "insert into ".TP."company_identifiers set company_id='".$company_id."',identifier_id='".$identifier_id."',value='".$identifier_value."'";
		$success = $db->mod_query($q);
		if(!$success){
			return false;
		}
		return true;
	}
	
	/******************
	check if the company has a particular identifier or not
	******************/
	public function has_company_identifier($company_id,$identifier_id,&$has_identifier){
		$db = new db();
		$q = "select count(*) as cnt from ".TP."company_identifiers where company_id='".$company_id."' and identifier_id='".$identifier_id."'";
		$success = $db->select_query($q);
		if(!$success){
			return false;
		}
		$row = $db->get_row();
		if(0==$row['cnt']){
			//not found
			$has_identifier = false;
		}else{
			$has_identifier = true;
		}
		return true;
	}
	/**********
	delete identifier for a company. Here we specify the record id
	
	sng:9/dec/2011
	We no longer need delete identifier value for a company
	*************/
	
	/**********
	edit identifier for a company. Here we specify the record id and the value
	
	sng:9/dec/2011
	We have changed the way we show the identifiers in admin company edit page. So now
	we send company_id, indentifier_id and value
	*************/
	public function admin_edit_company_identifier($company_id,$identifier_id,$value){
		$db = new db();
		$q = "update ".TP."company_identifiers set value='".$value."' where company_id='".$company_id."' AND identifier_id='".$identifier_id."'";
		$success = $db->mod_query($q);
		if(!$success){
			return false;
		}
		return true;
	}
	/***********************************end company identifiers*************************************/
	
	/***************************************company suggestion**************************************/
	/******************************
	sng:18/may/2012
	We now allow to submit a logo.
	Also, we now create the company record directly (admin_verified=n)
	Then we notify company_suggestion obj that a new company has been added.
	******************/
	public function ajax_front_new_company_suggestion($data_arr,&$validation_passed,&$err_msg,&$err_arr){
		$db = new db();
		//validation
		$validation_passed = true;
		
		if($data_arr['name'] == ""){
			$validation_passed = false;
			$err_arr['name'] = "Please specify the company name";
		}else{
			/*********************
			sng:18/may/2012
			now check if this company exists of not in the company table. Remember that
			tomato trash can exists as company and bank, so we need to search for the name and
			type
			***************/
			$company_exists = false;
			$ok = $this->company_blf_exists($data_arr['name'],'company',$company_exists);
			if(!$ok){
				return false;
			}
			if($company_exists){
				$validation_passed = false;
				$err_arr['name'] = "This already exists, please specify a new one";
			}
		}
		
		if($data_arr['country_of_headquarters'] == ""){
			$validation_passed = false;
			$err_arr['country_of_headquarters'] = "Please specify the country of HQ";
		}
		
		if($data_arr['company_sector'] == ""){
			$validation_passed = false;
			$err_arr['company_sector'] = "Please specify the sector";
		}
		
		if($data_arr['company_industry'] == ""){
			$validation_passed = false;
			$err_arr['company_industry'] = "Please specify the industry";
		}
		
		if(!$validation_passed){
			$err_msg = "One or more mandatory information was not specified";
			return true;
		}
		
		/**************
		sng:18/may/2012
		try to upload logo if specified
		****************/
		$uploaded_img_name = "";
		
		
		if($_FILES['logo']['name']!=""){
			require_once("classes/class.image_util.php");
			$img_util = new image_util();
			/***
			sng:23/sep/2010
			cannot have space in logo file name else problem when downloading to powerpoint
			
			sng:16/aug/2011
			we remove anything that is not alpha numeric or dot
			***/
			$uploaded_img_name = time()."_".clean_filename(basename($_FILES['logo']['name']));
			$ok = $img_util->create_resized($_FILES['logo']['tmp_name'],LOGO_PATH."/thumbnails",$uploaded_img_name,200,200,false);
			/*********
			never mind if not created. We enter blank value for logo
			***********/
			if(!$ok){
				$uploaded_img_name = "";
			}
		}
		
		/***************
		Now we insert the suggestion
		Since this is done from the front end, admin_verified is n
		******************/
		$q = "insert into ".TP."company set name='".mysql_real_escape_string($data_arr['name'])."',type='company',hq_country='".$data_arr['country_of_headquarters']."',sector='".$data_arr['company_sector']."',industry='".$data_arr['company_industry']."',logo='".$uploaded_img_name."',admin_verified='n'";
		
		$ok = $db->mod_query($q);
		if(!$ok){
			//echo mysql_error();
			return false;
		}
		$new_company_id = $db->last_insert_id();
		
		/******************
		now the identifiers.
		the identifier ids are sent via $_POST['identifier_ids']. For each id, the data field names
		are identifier_id_<id>
		
		sng:19/may/2012
		We now directly insert the identifiers instead of holding them as suggestions.
		************************/
		$identifier_q = "";
		
		$identifier_count = count($_POST['identifier_ids']);
		for($j=0;$j<$identifier_count;$j++){
			$identifier_id = $_POST['identifier_ids'][$j];
			$key = "identifier_id_".$identifier_id;
			if($_POST[$key]!=""){
				$identifier_q.=",('".$new_company_id."','".$identifier_id."','".mysql_real_escape_string($_POST[$key])."')";
			}
		}
		if($identifier_q!=""){
			$identifier_q = substr($identifier_q,1);
		}
		
		if($identifier_q!=""){
			$identifier_q = "INSERT INTO ".TP."company_identifiers(company_id,identifier_id,`value`) values".$identifier_q;
			$success = $db->mod_query($identifier_q);
			if(!$success){
				return false;
			}
		}
		$validation_passed = true;
		/*********************************
		now notify
		******************/
		require_once("classes/class.company_suggestion.php");
		$comp_suggestion = new company_suggestion();
		
		$suggestion_mem_id = $_SESSION['mem_id'];
		$suggestion_date = date("Y-m-d H:i:s");
		
		$ok = $comp_suggestion->company_added_via_front($suggestion_mem_id,$suggestion_date,$new_company_id,$data_arr,$uploaded_img_name);
		/***************
		never mind if error
		****************/
		return true;
		
	}
	/**************
	8/dec/2011
	To suggest correction to data field of an existing company.
	No validation is required since the user may suggest correction for only one field.
	******************/
	public function ajax_front_company_correction($data_arr,&$err_msg){
		$db = new db();
		
		/******************************************************
		now insert in the suggestion table
		we need to generate the suggestion id
		**********************/
		$suggestion_mem_id = $_SESSION['mem_id'];
		$suggestion_id = $suggestion_mem_id."-".time();
		$suggestion_date = date("Y-m-d H:i:s");
		
		$suggestion_q = "insert into ".TP."company_suggestions set
		company_suggestion_id='".$suggestion_id."',
		company_id='".mysql_real_escape_string($data_arr['company_id'])."',
		suggested_by='".$suggestion_mem_id."',
		date_suggested='".$suggestion_date."',
		name='".mysql_real_escape_string($data_arr['name'])."',
		type='company',
		hq_country='".mysql_real_escape_string($data_arr['country_of_headquarters'])."',
		sector='".mysql_real_escape_string($data_arr['company_sector'])."',
		industry='".mysql_real_escape_string($data_arr['company_industry'])."'";
		
		$success = $db->mod_query($suggestion_q);
		if(!$success){
			return false;
		}
		
		/******************
		now the identifiers.
		the identifier ids are sent via $_POST['identifier_ids']. For each id, the data field names
		are identifier_id_<id>
		We can use multi value INSERT statement
		************************/
		$suggestion_identifier_q = "";
		
		$identifier_count = count($_POST['identifier_ids']);
		for($j=0;$j<$identifier_count;$j++){
			$identifier_id = $_POST['identifier_ids'][$j];
			$key = "identifier_id_".$identifier_id;
			if($_POST[$key]!=""){
				$suggestion_identifier_q.=",('".$suggestion_id."','".$identifier_id."','".mysql_real_escape_string($_POST[$key])."')";
			}
		}
		if($suggestion_identifier_q!=""){
			$suggestion_identifier_q = substr($suggestion_identifier_q,1);
		}
		
		if($suggestion_identifier_q!=""){
			$suggestion_identifier_q = "INSERT INTO ".TP."company_suggestions_identifiers(company_suggestion_id,identifier_id,`value`) values".$suggestion_identifier_q;
			$success = $db->mod_query($suggestion_identifier_q);
			if(!$success){
				return false;
			}
		}
		//we will handle multiple logo later
		//all ok
		
		return true;
		
	}
	/************************************
	sng:7/dec/2011
	to suggest bank/law firm
	
	sng:7/may/2012
	We now make 3 changes.
	We now allow to upload a logo 
	We no longer take the abbreviated name
	and we now create the firm record directly (admin_verified=n)
	Then we notify company_suggestion obj that a new firm has been added.
	**********************************/
	public function ajax_front_new_firm_suggestion($data_arr,&$validation_passed,&$err_msg,&$err_arr){
		$db = new db();
		//validation
		$validation_passed = true;
		
		if($data_arr['name'] == ""){
			$validation_passed = false;
			$err_arr['name'] = "Please specify the firm name";
		}else{
			/*********************
			now check if this firm exists of not in the company table. Remember that
			tomato trash can exists as company and bank, so we need to search for the name and
			type
			***************/
			$firm_exists = false;
			$ok = $this->company_blf_exists($data_arr['name'],$data_arr['type'],$firm_exists);
			if(!$ok){
				return false;
			}
			if($firm_exists){
				$validation_passed = false;
				$err_arr['name'] = "This already exists, please specify a new one";
			}
		}
		//we no longer take abbreviated name
		
		if(!$validation_passed){
			$err_msg = "One or more mandatory information was not specified";
			return true;
		}
		
		
		/**************
		try to upload logo if specified
		****************/
		$uploaded_img_name = "";
		
		
		if($_FILES['logo']['name']!=""){
			require_once("classes/class.image_util.php");
			$img_util = new image_util();
			/***
			sng:23/sep/2010
			cannot have space in logo file name else problem when downloading to powerpoint
			
			sng:16/aug/2011
			we remove anything that is not alpha numeric or dot
			***/
			$uploaded_img_name = time()."_".clean_filename(basename($_FILES['logo']['name']));
			$ok = $img_util->create_resized($_FILES['logo']['tmp_name'],LOGO_PATH."/thumbnails",$uploaded_img_name,200,200,false);
			/*********
			never mind if not created. We enter blank value for logo
			***********/
			if(!$ok){
				$uploaded_img_name = "";
			}
		}
		/***************
		Now we insert the suggestion
		Since this is done from the front end, admin_verified is n
		******************/
		$q = "insert into ".TP."company set name='".mysql_real_escape_string($data_arr['name'])."',type='".mysql_real_escape_string($data_arr['type'])."',logo='".$uploaded_img_name."',admin_verified='n'";
		
		$ok = $db->mod_query($q);
		if(!$ok){
			//echo mysql_error();
			return false;
		}
		$new_company_id = $db->last_insert_id();
		$validation_passed = true;
		/*********************************
		now notify
		******************/
		require_once("classes/class.company_suggestion.php");
		$comp_suggestion = new company_suggestion();
		
		$suggestion_mem_id = $_SESSION['mem_id'];
		$suggestion_date = date("Y-m-d H:i:s");
		
		$ok = $comp_suggestion->firm_added_via_front($suggestion_mem_id,$suggestion_date,$new_company_id,$data_arr['name'],$data_arr['type'],$uploaded_img_name);
		/***************
		never mind if error
		****************/
		return true;
	}
	/************************************
	sng:8/dec/2011
	to suggest bank/law firm data correction
	No need for validation since user may only suggest a correction for a single field only
	**********************************/
	public function ajax_front_firm_correction($data_arr,&$err_msg){
		$db = new db();
		
		/******************************************************
		now insert in the suggestion table
		we need to generate the suggestion id
		**********************/
		$suggestion_mem_id = $_SESSION['mem_id'];
		$suggestion_id = $suggestion_mem_id."-".time();
		$suggestion_date = date("Y-m-d H:i:s");
		
		$suggestion_q = "insert into ".TP."company_suggestions set
		company_suggestion_id='".$suggestion_id."',
		company_id='".mysql_real_escape_string($data_arr['company_id'])."',
		suggested_by='".$suggestion_mem_id."',
		date_suggested='".$suggestion_date."',
		name='".mysql_real_escape_string($data_arr['name'])."',
		short_name='".mysql_real_escape_string($data_arr['short_name'])."',
		type='".mysql_real_escape_string($data_arr['type'])."'";
		
		
		$success = $db->mod_query($suggestion_q);
		if(!$success){
			return false;
		}
		
		
		//we will handle multiple logo later
		//all ok
		
		return true;
		
	}
	/*********************
	sng:7/dec/2011
    get the list of suggeasted banks / law firms for admin
	For new suggestions, company id is 0
	type: bank or law firm
	
	Note: for banks / law firms, only the following fields are needed
	name - name of the bank/law firm
	short_name - 2 or 3 letter acronym for the bank / law firm
	type
    ***/
    public function admin_get_suggested_blf_paged($start_offset,$num_to_fetch,&$data_arr,&$data_count){
        $db = new db();
        
        $q = "select company_suggestion_id,date_suggested,s.name,s.short_name,s.type,m.f_name,m.l_name,m.designation,w.name as work_company from ".TP."company_suggestions as s left join ".TP."member as m on(s.suggested_by=m.mem_id) left join ".TP."company as w on(m.company_id=w.company_id) where s.company_id='0' and (s.type='bank' OR s.type='law firm') order by s.date_suggested desc";
		
		$success = $db->select_query_limited($q,$start_offset,$num_to_fetch);
        
        if(!$success){
			//echo $db->error();
            return false;
        }
        $data_count = $db->row_count();
        if(0 == $data_count){
            return true;
        }
		$data_arr = $db->get_result_set_as_array();
        ////////////////////////////////////////
        
        return true;
    }
	/**********************
	sng:7/dec/2011
	For banks / law firms, there is no identifiers
	********/
	public function admin_reject_suggested_blf($company_suggestion_id,&$msg){
		$db = new db();
		
        $q = "delete from ".TP."company_suggestions where company_suggestion_id='".$company_suggestion_id."'";
        $success = $db->mod_query($q);
        if(!$success){
            return false;
        }
		
		
        $msg = "deleted";
        return true;
    }
	/**********************
	sng:7/dec/2011
	Get the record from the suggestion table and insert it into company table. Then, delete from suggestion table.
	For banks / law firms, there is no identifiers
	********/
	public function admin_accept_suggested_blf($company_suggestion_id,&$msg){
		$db = new db();
		
		$q = "select name,short_name,type from ".TP."company_suggestions where company_suggestion_id='".$company_suggestion_id."'";
		$success = $db->select_query($q);
		if(!$success){
			return false;
		}
		if(!$db->has_row()){
			return false;
		}
		$firm_row = $db->get_row();
		//before insert, check if the firm exists or not
		$q = "select count(*) as cnt from ".TP."company where name='".$row['name']."' and type='".$row['type']."'";
		$success = $db->select_query($q);
		if(!$success){
			return flase;
		}
		$row = $db->get_row();
		if($row['cnt']>0){
			//a firm by the same name exists
			
			$msg = "A firm by this name exists";
			//do not insert, get out
			return true;
		}
		//all ok, now insert
		$q = "insert into ".TP."company set name='".$firm_row['name']."',short_name='".$firm_row['short_name']."',type='".$firm_row['type']."'";
		$success = $db->mod_query($q);
		if(!$success){
			return false;
		}
		//now delete the record
		$success = $this->admin_reject_suggested_blf($company_suggestion_id,$msg);
		//never mind if not success
        
        $msg = "Accepted";
        return true;
    }
	/****************
	8/dec/2011
	admin: get the list of suggested companies
	For new suggestions, company id is 0
	type: company
	
	Note: for a company, short_name is not needed
    ***/
    public function admin_get_suggested_company_paged($start_offset,$num_to_fetch,&$data_arr,&$data_count){
        $db = new db();
        
        $q = "select company_suggestion_id,date_suggested,s.name,s.hq_country,s.sector,s.industry,'identifiers' as identifiers,'0' as identifier_count,m.f_name,m.l_name,m.designation,w.name as work_company from ".TP."company_suggestions as s left join ".TP."member as m on(s.suggested_by=m.mem_id) left join ".TP."company as w on(m.company_id=w.company_id) where s.company_id='0' and s.type='company' order by s.date_suggested desc";
		
		$success = $db->select_query_limited($q,$start_offset,$num_to_fetch);
        
        if(!$success){
			//echo $db->error();
            return false;
        }
        $data_count = $db->row_count();
        if(0 == $data_count){
            return true;
        }
		$data_arr = $db->get_result_set_as_array();
		/*************************
		now, for each suggestion, get the suggested identifiers
		***************/
		for($j=0;$j<$data_count;$j++){
			$temp_company_suggestion_id = $data_arr[$j]['company_suggestion_id'];
			$success = $this->get_suggested_company_identifiers($temp_company_suggestion_id,$data_arr[$j]['identifiers'],$data_arr[$j]['identifier_count']);
			if(!$success){
				return false;
			}
		}
        
        return true;
    }
	/**************
	sng:8/dec/2011
	************/
	public function get_suggested_company_identifiers($company_suggestion_id,&$data_arr,&$data_cnt){
		$db = new db();
		$q = "select * from ".TP."company_suggestions_identifiers as ci left join ".TP."company_identifier_master as cim on(ci.identifier_id=cim.identifier_id) where company_suggestion_id='".$company_suggestion_id."' order by cim.name";
		
		$success = $db->select_query($q);
		if(!$success){
			return false;
		}
		$data_cnt = $db->row_count();
		if(0 == $data_cnt){
			//no data
			return true;
		}
		//has data
		$data_arr = $db->get_result_set_as_array();
		return true;
	}
	/**********************
	sng:8/dec/2011
	For companies, remember to delete the suggested identifiers also
	********/
	public function admin_reject_suggested_company($company_suggestion_id,&$msg){
		$db = new db();
		
        $q = "delete from ".TP."company_suggestions where company_suggestion_id='".$company_suggestion_id."'";
        $success = $db->mod_query($q);
        if(!$success){
            return false;
        }
		$q = "delete from ".TP."company_suggestions_identifiers where company_suggestion_id='".$company_suggestion_id."'";
        $success = $db->mod_query($q);
        if(!$success){
            return false;
        }
		
        $msg = "deleted";
        return true;
    }
	/**********************
	sng:8/dec/2011
	Get the record from the suggestion table and insert it into company table. Then, delete from suggestion table.
	Same for the identifiers
	********/
	public function admin_accept_suggested_company($company_suggestion_id,&$msg){
		$db = new db();
		
		$q = "select name,hq_country,sector,industry from ".TP."company_suggestions where company_suggestion_id='".$company_suggestion_id."'";
		$success = $db->select_query($q);
		if(!$success){
			return false;
		}
		if(!$db->has_row()){
			return false;
		}
		$company_row = $db->get_row();
		
		/********************************************************
		now get the identifiers for this suggestion
		***********/
		$identifier_count = 0;
		$identifier_arr = NULL;
		$success = $this->get_suggested_company_identifiers($company_suggestion_id,$identifier_arr,$identifier_count);
		if(!$success){
			return false;
		}
		/**************************************************
		before insert, check if the company exists or not
		******/
		$q = "select count(*) as cnt from ".TP."company where name='".$row['company_row']."' and type='company'";
		$success = $db->select_query($q);
		if(!$success){
			return flase;
		}
		$row = $db->get_row();
		if($row['cnt']>0){
			//a firm by the same name exists
			
			$msg = "A company by this name exists";
			//do not insert, get out
			return true;
		}
		/***********************************
		all ok, now insert
		************/
		$q = "insert into ".TP."company set name='".$company_row['name']."',type='company',hq_country='".$company_row['hq_country']."',sector='".$company_row['sector']."',industry='".$company_row['industry']."'";
		$success = $db->mod_query($q);
		if(!$success){
			return false;
		}
		//get the auto generated id
		$company_id = $db->last_insert_id();
		/***************************************
		now insert the identifiers
		*********/
		$company_identifier_q = "";
		
		
		for($j=0;$j<$identifier_count;$j++){
			$company_identifier_q.=",('".$company_id."','".$identifier_arr[$j]['identifier_id']."','".$identifier_arr[$j]['value']."')";
		}
		if($company_identifier_q!=""){
			$company_identifier_q = substr($company_identifier_q,1);
		}
		
		if($company_identifier_q!=""){
			$company_identifier_q = "INSERT INTO ".TP."company_identifiers(company_id,identifier_id,`value`) values".$company_identifier_q;
			$success = $db->mod_query($company_identifier_q);
			//never mind if not success
		}
		//now delete the suggestion record
		$success = $this->admin_reject_suggested_company($company_suggestion_id,$msg);
		//never mind if not success
        
        $msg = "Accepted";
        return true;
    }
	/***************************************end company suggestion**********************************/
	/**************************************correction fetch******************************************/
	/********************************
	sng:8/dec/2011
	We now allow the members to specify corrections for each fields of a bank/law firm.
	
	Also, there can be more than one corrections suggested for a bank/law firm. What we do is, show only the banks/law firms that
	has one or more corrections and allow admin to edit the bank/law firm. In the edit page we show the corrections and who posted it.
	***************************/
	public function get_error_blfs_paged($start_offset,$num_to_fetch,&$data_arr,&$data_count){
		$db = new db();
		$q = "select r.company_id,c.name,c.type from (SELECT DISTINCT company_id FROM ".TP."company_suggestions WHERE company_id != '0' AND (type='bank' OR type='law firm')) as r left join ".TP."company as c on(r.company_id=c.company_id) order by c.name desc";
		
		$success = $db->select_query_limited($q,$start_offset,$num_to_fetch);
		if(!$success){
			return false;
		}
		
        $data_count = $db->row_count();
		
        if(0==$data_count){
            return true;
        }
        $data_arr = $db->get_result_set_as_array();
        
        return true;
    }
	/********************************
	sng:9/dec/2011
	This is for companies
	***************************/
	public function get_error_companies_paged($start_offset,$num_to_fetch,&$data_arr,&$data_count){
		$db = new db();
		$q = "select r.company_id,c.name,c.type from (SELECT DISTINCT company_id FROM ".TP."company_suggestions WHERE company_id != '0' AND type='company') as r left join ".TP."company as c on(r.company_id=c.company_id) order by c.name desc";
		
		$success = $db->select_query_limited($q,$start_offset,$num_to_fetch);
		if(!$success){
			return false;
		}
		
        $data_count = $db->row_count();
		
        if(0==$data_count){
            return true;
        }
        $data_arr = $db->get_result_set_as_array();
        
        return true;
    }
	/************
	sng: 9/dec/2011
	given a company, bank, law firm, there can be many corrections posted. when editing the company,bank.law firm, admin wants to know
	whether there is any corrections, sent for the given company, for the particular data, say name
	
	This can be used ONLY for a single field
	************/
	public function admin_has_data_correction_on_company($company_id,$data_name){
		$db = new db();
		$where = "";
		/********
		it may happen that the member has not given any suggestion for the particular field for this company
		********/
		$q = "select count(*) as cnt";
		/*****************
		map data to columns
		****************/
		if($data_name == "name"){
			$where = "name !=''";
		}elseif($data_name == "short_name"){
			$where = "short_name !=''";
		}
		elseif($data_name == "sector"){
			$where = "sector !=''";
		}elseif($data_name == "industry"){
			$where = "industry !=''";
		}elseif($data_name == "hq_country"){
			$where = "hq_country !=''";
		}
		
		$q.=" from ".TP."company_suggestions where company_id='".$company_id."' AND ".$where;
		$success = $db->select_query($q);
		if(!$success){
			//db error
			//echo $q;
			return false;
		}
		$row = $db->get_row();
		if($row['cnt'] == 0) return false;
		else return true;
	}
	/************************
	sng:9/dec/2011
	Given a company, there can be many suggestions for the different identifiers.
	***************************/
	public function admin_has_data_correction_on_company_identifier($company_id,$identifier_id){
		$db = new db();
		$q = "select count(*) as cnt from ".TP."company_suggestions_identifiers as si left join ".TP."company_suggestions as s on(si.company_suggestion_id=s.company_suggestion_id) where si.identifier_id='".$identifier_id."' and s.company_id='".$company_id."'";
		$success = $db->select_query($q);
		if(!$success){
			//db error
			return false;
		}
		$row = $db->get_row();
		if($row['cnt'] == 0) return false;
		else return true;
	}
	/************
	sng: 9/dec/2011
	given a company,bank,law firm, there can be many corrections posted. when editing the company, admin wants to know all the corrections, sent
	for the given company, for the particular data, say name
	
	This can be used ONLY for a single field
	************/
	public function admin_fetch_data_correction_on_company($company_id,$data_name,&$result_arr,&$result_count){
		$db = new db();
		
		$where = "";
		$nl2br_required = false;
		/********
		it may happen that the member has not given any suggestion for the particular field for this company
		********/
		$q = "select";
		/*****************
		map data to columns
		****************/
		if($data_name == "name"){
			$q.=" s.name as data";
			$where = "s.name !=''";
		}elseif($data_name == "short_name"){
			$q.=" s.short_name as data";
			$where = "s.short_name !=''";
		}elseif($data_name == "sector"){
			$q.=" s.sector as data";
			$where = "s.sector !=''";
		}elseif($data_name == "industry"){
			$q.=" s.industry as data";
			$where = "s.industry !=''";
		}elseif($data_name == "hq_country"){
			$q.=" s.hq_country as data";
			$where = "s.hq_country !=''";
		}
		
		$q.=",s.date_suggested,m.f_name,m.l_name,m.designation,w.name as work_company from ".TP."company_suggestions as s left join ".TP."member as m on(s.suggested_by=m.mem_id) left join ".TP."company as w on(m.company_id=w.company_id) where s.company_id='".$company_id."' AND ".$where." order by s.date_suggested";
		$success = $db->select_query($q);
		if(!$success){
			//db error
			echo $q;
			return false;
		}
		$result_count = $db->row_count();
		if(0 == $result_count){
			//no data
			return true;
		}
		$result_arr = $db->get_result_set_as_array();
		if($nl2br_required){
			for($i=0;$i<$result_count;$i++){
				$result_arr[$i]['data'] = nl2br($result_arr[$i]['data']);
			}
		}
		return true;
	}
	/******************
	sng:9/dec/2011
	For a company, there can be one for more corrections sent. Corrections can be sent for one or more identifiers.
	Admin need to see all the suggestions for a particular identifier for a company
	******************/
	public function admin_fetch_identifier_correction_on_company($company_id,$identifier_id,&$result_arr,&$result_count){
		$db = new db();
		$q = "select si.value as data,s.date_suggested,m.f_name,m.l_name,m.designation,w.name as work_company from ".TP."company_suggestions_identifiers as si left join ".TP."company_suggestions as s on (si.company_suggestion_id=s.company_suggestion_id) left join ".TP."member as m on(s.suggested_by=m.mem_id) left join ".TP."company as w on(m.company_id=w.company_id) where si.identifier_id='".$identifier_id."' and s.company_id='".$company_id."' order by s.date_suggested";
		
		$success = $db->select_query($q);
		if(!$success){
			//db error
			echo $q;
			return false;
		}
		$result_count = $db->row_count();
		if(0 == $result_count){
			//no data
			return true;
		}
		$result_arr = $db->get_result_set_as_array();
		
		return true;
	}
	/**************************************end correction fetch******************************************/
	
	/******************quick company/bank/law firm check or create***************************************
	These are used when creating a deal from simple submission where we only have the name.
	
	sng:10/may/2012
	When creating this, we also store it as original suggestion.
	The date is current date when we create the firm
	
	However, now this takes an extra id : mem_id : whose action resulted in creation of the firm record (maybe added / suggested a firm to a deal)
	I have updated all the calling codes for this function
	*****/
	public function front_quick_create_company_blf($mem_id,$name,$type,&$id){
		$db = new db();
		//check if this type of company already exists or not. If so, return that id
		//else create the company entry and return that id.
		//also, since admin is not creating it, verified is n
		$company_found = false;
		$company_id = 0;
		$ok = company_id_from_name($name,$type,$company_id,$company_found);
		if(!$ok){
			return false;
		}
		if($company_found){
			$id = company_id;
			return true;
		}
		//we have to create
		$q = "insert into ".TP."company set name='".mysql_real_escape_string($name)."',type='".$type."',admin_verified='n'";
		$ok = $db->mod_query($q);
		if(!$ok){
			return false;
		}
		$id = $db->last_insert_id();
		require_once("classes/class.company_suggestion.php");
		$comp_suggest = new company_suggestion();
		
		$creation_date = date("Y-m-d H:i:s");
		if($type=='bank' || $type=='law firm'){
			$comp_suggest->firm_added_via_front($mem_id,$creation_date,$id,$name,$type,"");
		}
		if($type=='company'){
			/************
			sng:18/may/2012
			**************/
			$data_arr = array();
			$data_arr['name'] = $name;
			$data_arr['country_of_headquarters'] = "";
			$data_arr['company_sector'] = "";
			$data_arr['company_industry'] = "";
			$comp_suggest->company_added_via_front($mem_id,$creation_date,$id,$data_arr,"");
		}
		return true;
	}
	
	/********************
	sng:7/may/2012
	quick check whether the company/bank/law firm by the name exists or not
	********************/
	public function company_blf_exists($name,$type,&$exists){
		$db = new db();
		$q = "select count(*) as cnt from ".TP."company where name='".mysql_real_escape_string($name)."' AND type='".mysql_real_escape_string($type)."'";
		$ok = $db->select_query($q);
		if(!$ok){
			return false;
		}
		$row = $db->get_row();
		if($row['cnt'] > 0){
			$exists = true;
		}else{
			$exists = false;
		}
		return true;
	}
	
	/******************quick company/bank/law firm check or create***************************************/
}
$g_company = new company(); 
?>