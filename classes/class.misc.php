<?php
/*****
sng: This class contains odds and ends, bits and pieces for our use
***********/
require_once("classes/class.magic_quote.php");
class misc{
	/***
	To check for duplicate company names after a bulk upload
	Used in admin
	
	sng:6/feb/2011
	support for private note
	*******/
	public function get_all_duplicate_firms(&$data_arr,&$data_count){
		global $g_mc;
		
		$q = "SELECT company_id,name,type,hq_country,sector,industry,private_note FROM ".TP."company WHERE name IN (SELECT name FROM (SELECT name, count( name ) AS cnt FROM ".TP."company GROUP BY name HAVING cnt >1) AS dup_names) ORDER BY name";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$data_count = mysql_num_rows($res);
		if(0==$data_count){
			return true;
		}
		while($row = mysql_fetch_assoc($res)){
			$row['name'] = $g_mc->db_to_view($row['name']);
			$row['private_note'] = $g_mc->db_to_view($row['private_note']);
			$data_arr[] = $row;
		}
		return true;
	}
	
	/***
	sng:19/jul/2010
	To check for duplicate company names. The matching is done by taking the first 6 letters
	Used in admin
	
	sng:6/feb/2011
	Support for private note
	*******/
	public function get_all_probable_duplicate_firms($start,$num_to_fetch,&$data_arr,&$data_count){
		global $g_mc;
		
		$q = "SELECT company_id,name,type,private_note FROM ".TP."company WHERE substring( name, 1, 6 ) IN (SELECT name_prefix FROM (SELECT name_prefix, count( * ) AS cnt_name_prefix FROM (SELECT substring( name, 1, 6 ) AS name_prefix FROM ".TP."company ORDER BY name_prefix) AS A GROUP BY A.name_prefix HAVING cnt_name_prefix >1) AS B) order by name limit ".$start.",".$num_to_fetch;
		$res = mysql_query($q);
		if(!$res){
			
			return false;
		}
		$data_count = mysql_num_rows($res);
		if(0==$data_count){
			return true;
		}
		while($row = mysql_fetch_assoc($res)){
			$row['name'] = $g_mc->db_to_view($row['name']);
			$row['private_note'] = $g_mc->db_to_view($row['private_note']);
			$data_arr[] = $row;
		}
		return true;
	}
	
	/***
	This operation scans the company table to find countries of HQs that are not in the country master table
	sng:11/May/2010
	Way this query works, it does not return the number of rows inserted, so leave that
	***/
	public function update_country_master_from_company_hq_countries(){
		$q = "INSERT INTO ".TP."country_master( name ) SELECT DISTINCT hq_country FROM ".TP."company WHERE hq_country!='' and hq_country NOT IN (SELECT name FROM ".TP."country_master)";
		$result = mysql_query($q);
		if(!$result){
			//echo mysql_error();
			return false;
		}else{
			$countries_inserted = $result;
			return true;
		}
	}
	
	/***
	This operation scans the transaction table to find transaction type/subtypes that are not in the transaction type/subtype master table
	***/
	public function update_deal_type_master_from_deal_type(){
		$q = "insert into ".TP."transaction_type_master(type,subtype1,subtype2) select deal_cat_name,deal_subcat1_name,deal_subcat2_name from (SELECT `deal_cat_name` , `deal_subcat1_name` , `deal_subcat2_name`, concat(deal_cat_name,'|',deal_subcat1_name,'|',deal_subcat2_name) as tyro
FROM ".TP."transaction
GROUP BY `deal_cat_name` , `deal_subcat1_name` , `deal_subcat2_name` having tyro not in(SELECT concat( 
TYPE , '|', subtype1, '|', subtype2 ) as tyro
FROM ".TP."transaction_type_master)) as t";
		$result = mysql_query($q);
		if(!$result){
			//echo mysql_error();
			return false;
		}else{
			$countries_inserted = $result;
			return true;
		}
	}
	
	/***
	This operation scans the company table to find sector industry that are not in the sector industry master table
	***/
	public function update_sector_industry_master_from_company(){
		$q = "insert into ".TP."sector_industry_master(sector,industry) select sector,industry from (SELECT `sector` , `industry`, concat(sector,'|',industry) as tyro
FROM ".TP."company where sector!=''
GROUP BY `sector` , `industry` having tyro not in(SELECT concat( 
sector , '|', industry ) as tyro FROM ".TP."sector_industry_master)) as t";
		$result = mysql_query($q);
		if(!$result){
			//echo mysql_error();
			return false;
		}else{
			$countries_inserted = $result;
			return true;
		}
	}
	
	/***
	This finds out all the deals where the target company name contains a %
	*****/
	public function admin_get_target_name_with_percent_paged($start,$num_to_fetch,&$data_arr,&$data_count){
		global $g_mc;
		$q = "select id,value_in_billion,date_of_deal,deal_cat_name,deal_subcat1_name,deal_subcat2_name,target_company_name,name as company_name from ".TP."transaction as t left join ".TP."company as c on(t.company_id=c.company_id) where target_company_name regexp '%' limit ".$start.",".$num_to_fetch;
		$res = mysql_query($q);
		if(!$res){	
			return false;
		}
		$data_count = mysql_num_rows($res);
		if(0==$data_count){
			return true;
		}
		while($row = mysql_fetch_assoc($res)){
			$row['target_company_name'] = $g_mc->db_to_view($row['target_company_name']);
			$row['company_name'] = $g_mc->db_to_view($row['company_name']);
			$data_arr[] = $row;
		}
		return true;
	}
	
	/***
	This finds out all the deals where the target company or seller name contains the given character
	This use regexp to search
	*****/
	public function admin_get_seller_target_name_with_sp_char_paged($input,$start,$num_to_fetch,&$data_arr,&$data_count){
		global $g_mc;
		//escape the special char
		if($input == '('){
			$input = "\\\\".$input;
		}
		if($input == ')'){
			$input = "\\\\".$input;
		}
		if($input == '['){
			$input = "\\\\".$input;
		}
		if($input == ']'){
			$input = "\\\\".$input;
		}
		$q = "select id,value_in_billion,date_of_deal,deal_cat_name,deal_subcat1_name,deal_subcat2_name,target_company_name,seller_company_name,name as company_name from ".TP."transaction as t left join ".TP."company as c on(t.company_id=c.company_id) where target_company_name regexp '".$input."' OR seller_company_name regexp '".$input."' limit ".$start.",".$num_to_fetch;
		$res = mysql_query($q);
		if(!$res){
			echo mysql_error();
			return false;
		}
		$data_count = mysql_num_rows($res);
		if(0==$data_count){
			return true;
		}
		while($row = mysql_fetch_assoc($res)){
			$row['target_company_name'] = $g_mc->db_to_view($row['target_company_name']);
			$row['seller_company_name'] = $g_mc->db_to_view($row['seller_company_name']);
			$row['company_name'] = $g_mc->db_to_view($row['company_name']);
			$data_arr[] = $row;
		}
		return true;
	}
	
	/***
	This finds out all banks / law firm where the name contains the given character
	This use regexp to search
	*****/
	public function admin_get_blf_name_with_sp_char_paged($input_data,$start,$num_to_fetch,&$data_arr,&$data_count){
		global $g_mc;
		//escape the special char
		$input = $input_data['special_char'];
		if($input == '('){
			$input = "\\\\".$input;
		}
		if($input == ')'){
			$input = "\\\\".$input;
		}
		if($input == '['){
			$input = "\\\\".$input;
		}
		if($input == ']'){
			$input = "\\\\".$input;
		}
		$q = "select company_id,name,type from ".TP."company where name regexp '".$input."' and type='".$input_data['type']."' limit ".$start.",".$num_to_fetch;
		$res = mysql_query($q);
		if(!$res){
			echo mysql_error();
			return false;
		}
		$data_count = mysql_num_rows($res);
		if(0==$data_count){
			return true;
		}
		while($row = mysql_fetch_assoc($res)){
			$row['name'] = $g_mc->db_to_view($row['name']);
			$data_arr[] = $row;
		}
		return true;
	}
	
	/***
	This finds out all the company name that contains the given character
	This use regexp to search
	*****/
	public function admin_get_company_name_with_sp_char_paged($input,$start,$num_to_fetch,&$data_arr,&$data_count){
		global $g_mc;
		//escape the special char
		if($input == '('){
			$input = "\\\\".$input;
		}
		if($input == ')'){
			$input = "\\\\".$input;
		}
		if($input == '['){
			$input = "\\\\".$input;
		}
		if($input == ']'){
			$input = "\\\\".$input;
		}
		$q = "select company_id,name,hq_country,sector,industry from ".TP."company where name regexp '".$input."' and type='company' limit ".$start.",".$num_to_fetch;
		$res = mysql_query($q);
		if(!$res){
			echo mysql_error();
			return false;
		}
		$data_count = mysql_num_rows($res);
		if(0==$data_count){
			return true;
		}
		while($row = mysql_fetch_assoc($res)){
			$row['name'] = $g_mc->db_to_view($row['name']);
			$row['hq_country'] = $g_mc->db_to_view($row['hq_country']);
			$data_arr[] = $row;
		}
		return true;
	}
	
	/***
	This finds out all the company name that contains only uppercase letters
	This use regexp to search
	*****/
	public function admin_get_company_name_with_all_cap_paged($start,$num_to_fetch,&$data_arr,&$data_count){
		global $g_mc;
		
		$q = "select company_id,name,hq_country,sector,industry from ".TP."company where name REGEXP binary '^[A-Z]+$' and type='company' limit ".$start.",".$num_to_fetch;
		$res = mysql_query($q);
		if(!$res){
			echo mysql_error();
			return false;
		}
		$data_count = mysql_num_rows($res);
		if(0==$data_count){
			return true;
		}
		while($row = mysql_fetch_assoc($res)){
			$row['name'] = $g_mc->db_to_view($row['name']);
			$row['hq_country'] = $g_mc->db_to_view($row['hq_country']);
			$data_arr[] = $row;
		}
		return true;
	}
	
	/***
	This finds out all the bank/law firm name that contains only uppercase letters
	This use regexp to search
	*****/
	public function admin_get_blf_name_with_all_cap_paged($input_data,$start,$num_to_fetch,&$data_arr,&$data_count){
		global $g_mc;
		
		$q = "select company_id,name,type from ".TP."company where name REGEXP binary '^[A-Z]+$' and type='".$input_data['type']."' limit ".$start.",".$num_to_fetch;
		$res = mysql_query($q);
		if(!$res){
			echo mysql_error();
			return false;
		}
		$data_count = mysql_num_rows($res);
		if(0==$data_count){
			return true;
		}
		while($row = mysql_fetch_assoc($res)){
			$row['name'] = $g_mc->db_to_view($row['name']);
			$row['hq_country'] = $g_mc->db_to_view($row['hq_country']);
			$data_arr[] = $row;
		}
		return true;
	}
	/****
	sng:26/Oct/2010
	Find the deals with duplicate banks or law firms as partners
	partner_type: bank, law firm
	*******/
	public function admin_get_deals_with_duplicate_partner($partner_type,$start,$num_to_fetch,&$data_arr,&$data_count){
		global $g_mc;
		$q = "SELECT id, value_in_billion, date_of_deal, deal_cat_name, deal_subcat1_name, deal_subcat2_name, name AS company_name FROM (SELECT transaction_id, count( partner_id ) AS partner_cnt FROM ".TP."transaction_partners WHERE partner_type = '".$partner_type."' GROUP BY partner_id, transaction_id HAVING partner_cnt >1 ORDER BY transaction_id LIMIT ".$start." , ".$num_to_fetch.") AS dup LEFT JOIN ".TP."transaction AS t ON ( dup.transaction_id = t.id ) LEFT JOIN ".TP."company AS c ON ( t.company_id = c.company_id )";
		
		$res = mysql_query($q);
		if(!$res){	
			return false;
		}
		$data_count = mysql_num_rows($res);
		if(0==$data_count){
			return true;
		}
		while($row = mysql_fetch_assoc($res)){
			$row['company_name'] = $g_mc->db_to_view($row['company_name']);
			$data_arr[] = $row;
		}
		return true;
	}
	/***
	sng:12/nov/2010
	This finds out all the deals where the note has url
	*****/
	public function admin_get_deals_with_url_in_note_paged($start,$num_to_fetch,&$data_arr,&$data_count){
		global $g_mc;
		$q = "select id,value_in_billion,date_of_deal,deal_cat_name,deal_subcat1_name,deal_subcat2_name,name as company_name,note from ".TP."transaction as t left join ".TP."company as c on(t.company_id=c.company_id) left join ".TP."transaction_note as n on(t.id=n.transaction_id) where note regexp 'https?://' limit ".$start.",".$num_to_fetch;
		$res = mysql_query($q);
		if(!$res){	
			return false;
		}
		$data_count = mysql_num_rows($res);
		if(0==$data_count){
			return true;
		}
		while($row = mysql_fetch_assoc($res)){
			$row['note'] = $g_mc->db_to_view($row['note']);
			$row['company_name'] = $g_mc->db_to_view($row['company_name']);
			$data_arr[] = $row;
		}
		return true;
	}
	/***
	sng:12/nov/2010
	This finds out all the pending m&a deals, oldest first
	*****/
	public function admin_get_pending_ma_deals_paged($start,$num_to_fetch,&$data_arr,&$data_count){
		global $g_mc;
		$q = "select id,value_in_billion,date_of_deal,deal_cat_name,deal_subcat1_name,deal_subcat2_name,target_company_name,name as company_name from ".TP."transaction as t left join ".TP."company as c on(t.company_id=c.company_id) where deal_cat_name='M&A' and deal_subcat1_name='Pending' order by t.date_of_deal limit ".$start.",".$num_to_fetch;
		$res = mysql_query($q);
		if(!$res){	
			return false;
		}
		$data_count = mysql_num_rows($res);
		if(0==$data_count){
			return true;
		}
		while($row = mysql_fetch_assoc($res)){
			$row['target_company_name'] = $g_mc->db_to_view($row['target_company_name']);
			$row['company_name'] = $g_mc->db_to_view($row['company_name']);
			$data_arr[] = $row;
		}
		return true;
	}
	
	/************
	sng:13/nov/2010
	This tries to find out probable conflicting M&A deals, one Pending, one Completed
	This use a join of Pending m&a records with Completed m&a records with same company id and id of completed deal greater that id of pending deal
	I checked the db, the date of deal can be different. As for the deal value, they are same but what is the guarantee that during entry of pending deal, the deal value
	was approx and the a duplicate deal was added with with new value
	*********/
	public function admin_get_conflicting_ma_deals_paged($start,$num_to_fetch,&$data_arr,&$data_count){
		global $g_mc;
		$q = "SELECT t1.id as id_pending,t1.value_in_billion as value_in_billion_pending,t1.date_of_deal as date_of_deal_pending,t1.name as company_name_pending,t1.target_company_name as target_company_name_pending, t2.id as id_completed,t2.value_in_billion as value_in_billion_completed,t2.date_of_deal as date_of_deal_completed,t2.name as company_name_completed,t2.target_company_name as target_company_name_completed
FROM (SELECT id,trans1.company_id,value_in_billion,date_of_deal,name,target_company_name FROM ".TP."transaction as trans1 left join ".TP."company as c1 on(trans1.company_id=c1.company_id) WHERE deal_cat_name = 'M&A' AND deal_subcat1_name = 'Pending') AS t1, (SELECT id,trans2.company_id,value_in_billion,date_of_deal,name,target_company_name FROM ".TP."transaction as trans2 left join ".TP."company as c2 on(trans2.company_id=c2.company_id)
WHERE deal_cat_name = 'M&A' AND deal_subcat1_name = 'Completed') as t2 where t1.company_id=t2.company_id and t2.id>t1.id limit ".$start.",".$num_to_fetch;
		$res = mysql_query($q);
		if(!$res){	
			return false;
		}
		$data_count = mysql_num_rows($res);
		if(0==$data_count){
			return true;
		}
		while($row = mysql_fetch_assoc($res)){
			$row['company_name_pending'] = $g_mc->db_to_view($row['company_name_pending']);
			$row['target_company_name_pending'] = $g_mc->db_to_view($row['target_company_name_pending']);
			$row['company_name_completed'] = $g_mc->db_to_view($row['company_name_completed']);
			$row['target_company_name_completed'] = $g_mc->db_to_view($row['target_company_name_completed']);
			$data_arr[] = $row;
		}
		return true;
	}
	
	/*************
	sng:9/dec/2010
	get the deals with the given sector and industry. Since transaction table now support deal_sector (csv) and deal_industry (csv)
	we check those
	*********/
	public function admin_get_deals_by_sector_industry_paged($input_arr,$start,$num_to_fetch,&$data_arr,&$data_count){
		global $g_mc;
		$q = "select id,value_in_billion,date_of_deal,deal_cat_name,deal_subcat1_name,deal_subcat2_name,deal_sector,deal_industry,name as company_name from ".TP."transaction as t left join ".TP."company as c on(t.company_id=c.company_id) where 1=1";
		if($input_arr['deal_sector']!=""){
			$q.=" and deal_sector like '%".$input_arr['deal_sector']."%'";
			//not just only the given sector
			$q.=" and deal_sector!='".$input_arr['deal_sector']."'";
		}
		if($input_arr['deal_industry']!=""){
			$q.=" and deal_industry like '%".$input_arr['deal_industry']."%'";
		}
		$q.=" order by t.date_of_deal limit ".$start.",".$num_to_fetch;
		$res = mysql_query($q);
		if(!$res){	
			return false;
		}
		$data_count = mysql_num_rows($res);
		if(0==$data_count){
			return true;
		}
		while($row = mysql_fetch_assoc($res)){
			$row['company_name'] = $g_mc->db_to_view($row['company_name']);
			$data_arr[] = $row;
		}
		return true;
	}
	
	/****************
	sng:1/feb/2011
	get the list of probable duplicate deals
	
	sng:4/feb/2011
	support for private note
	*****************/
	public function admin_get_probable_duplicate_deals_paged($param_arr,$start,$num_to_fetch,&$data_arr,&$data_count){
		global $g_mc;
		
		$q = "select id, value_in_billion,date_of_deal,deal_cat_name,deal_subcat1_name,deal_subcat2_name,name as company_name,tn.note as deal_private_note from ".TP."transaction as t left join ".TP."company as c on(t.company_id=c.company_id) left join ".TP."transaction_private_note as tn on(t.id=tn.transaction_id) where concat( name, '|', year(date_of_deal),'|',deal_cat_name,'|',deal_subcat1_name,'|',deal_subcat2_name ) IN(select combo from (SELECT count(*) as deal_cnt,concat( name, '|', year(date_of_deal),'|',deal_cat_name,'|',deal_subcat1_name,'|',deal_subcat2_name ) AS combo FROM ".TP."transaction AS t LEFT JOIN ".TP."company AS c ON ( t.company_id = c.company_id ) where 1=1";
		if($param_arr['deal_cat_name']!=""){
			$q.=" and deal_cat_name='".$param_arr['deal_cat_name']."'";
		}
		if($param_arr['year']!=""){
			$q.=" and year(date_of_deal)='".$param_arr['year']."'";
		}
		$q.=" group by combo having deal_cnt > '1') as t) order by name,deal_cat_name,deal_subcat1_name,deal_subcat2_name,date_of_deal limit ".$start.",".$num_to_fetch;

		
		$res = mysql_query($q);
		if(!$res){	
			return false;
		}
		$data_count = mysql_num_rows($res);
		if(0==$data_count){
			return true;
		}
		while($row = mysql_fetch_assoc($res)){
			$row['company_name'] = $g_mc->db_to_view($row['company_name']);
			$row['deal_private_note'] = $g_mc->db_to_view($row['deal_private_note']);
			$data_arr[] = $row;
		}
		return true;
	}
}
$g_misc = new misc();
?>