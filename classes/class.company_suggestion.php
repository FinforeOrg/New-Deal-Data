<?php
/*************************
sng:7/may/2012

Now we create a bank/law firm directly from suggestion. However, since data can change later,
we store the original suggestion in the company_suggestions table with a flag is_correction=n

There are 3 places where a bank/law firm gets created - admin, front end, while adding a deal.
****************************/
class company_suggestion{
	
	/**************
	When we add a firm record by adding it directly or during deal creation
	we store the original suggestion data for that firm
	***************/
	public function firm_added_via_front($mem_id,$date_added,$firm_id,$firm_name,$firm_type,$logo){
		$db = new db();
		
		$q = "insert into ".TP."company_suggestions set
		company_id='".$firm_id."',
		suggested_by='".$mem_id."',
		date_suggested='".$date_added."',
		name='".mysql_real_escape_string($firm_name)."',
		type='".mysql_real_escape_string($firm_type)."',
		logo='".mysql_real_escape_string($logo)."',
		is_correction='n'";
		
		$ok = $db->mod_query($q);
		if(!$ok){
			return false;
		}
		return true;
	}
	
	/**************
	sng:18/may/2012
	When we add a company record by adding it directly or during deal creation
	we store the original suggestion data for that company
	
	The company details are taken via an associative array
	name
	country_of_headquarters
	company_sector
	company_industry
	
	The logo is separate
	
	The case of identifiers:
	The identifier data keys are identifier_id_<identifier-id>
	We also send the identifier-ids in array format through the key 'identifier_ids'. So just get the 
	elements from 'identifier_ids', construct 'identifier_id_<identifier-id>' and get the identifier value
	for the particular identifier
	
	It may happen that no identifier is sent. In that case the 'identifier_ids' will not be set.
	***************/
	public function company_added_via_front($mem_id,$date_added,$company_id,$data_arr,$logo){
		$db = new db();
		
		$q = "insert into ".TP."company_suggestions set
		company_id='".$company_id."',
		suggested_by='".$mem_id."',
		date_suggested='".$date_added."',
		name='".mysql_real_escape_string($data_arr['name'])."',
		type='company',
		hq_country='".mysql_real_escape_string($data_arr['country_of_headquarters'])."',
		sector='".mysql_real_escape_string($data_arr['company_sector'])."',
		industry='".mysql_real_escape_string($data_arr['company_industry'])."',
		logo='".mysql_real_escape_string($logo)."',
		is_correction='n'";
		
		$ok = $db->mod_query($q);
		if(!$ok){
			return false;
		}
		
		/**********************
		Now the identifiers
		***************/
		$identifier_q = "";
		$identifier_count = 0;
		
		if(isset($data_arr['identifier_ids'])){
			$identifier_count = count($data_arr['identifier_ids']);
			for($j=0;$j<$identifier_count;$j++){
				$identifier_id = $data_arr['identifier_ids'][$j];
				$key = "identifier_id_".$identifier_id;
				if($data_arr[$key]!=""){
					$identifier_q.=",('".$company_id."','".$mem_id."','".$date_added."','".$identifier_id."','".mysql_real_escape_string($data_arr[$key])."','n')";
				}
			}
			if($identifier_q!=""){
				$identifier_q = substr($identifier_q,1);
			}
			if($identifier_q!=""){
				$identifier_q = "INSERT INTO ".TP."company_identifiers_suggestions(company_id,suggested_by,date_suggested,identifier_id,`value`,is_correction) values".$identifier_q;
				$success = $db->mod_query($identifier_q);
				if(!$success){
					return false;
				}
			}
		}else{
			//no company identifier data sent
		}
		return true;
	}
	
	/*****************
	Get the suggestions for this firm
	To get the original suggestion, get_original is true
	*****************/
	public function fetch_firms($firm_id,$get_original,&$data_arr,&$data_count){
		$db = new db();
		$q = "select suggested_by,date_suggested,name,logo,status_note,member_type,work_email from ".TP."company_suggestions as n left join ".TP."member as m on(n.suggested_by=m.mem_id) where n.company_id='".$firm_id."'";
		
		if($get_original){
			$q.=" and is_correction='n'";
		}else{
			$q.=" and is_correction='y'";
		}
		
		$q.=" order by date_suggested";
		$ok = $db->select_query($q);
		if(!$ok){
			return false;
		}
		$data_count = $db->row_count();
		if(0==$data_count){
			return true;
		}
		$data_arr = $db->get_result_set_as_array();
		return true;
	}
	
	/*****************
	sng:23/may/2012
	Get the suggestions for this company
	To get the original suggestion, get_original is true
	*****************/
	public function fetch_suggestions_for_company($company_id,$get_original,&$data_arr,&$data_count){
		$db = new db();
		$q = "select suggested_by,date_suggested,name,hq_country,sector,industry,logo,status_note,member_type,work_email from ".TP."company_suggestions as n left join ".TP."member as m on(n.suggested_by=m.mem_id) where n.company_id='".$company_id."'";
		
		if($get_original){
			$q.=" and is_correction='n'";
		}else{
			$q.=" and is_correction='y'";
		}
		
		$q.=" order by date_suggested";
		$ok = $db->select_query($q);
		if(!$ok){
			return false;
		}
		$data_count = $db->row_count();
		if(0==$data_count){
			return true;
		}
		$data_arr = $db->get_result_set_as_array();
		return true;
	}
	
	/*****************
	sng:23/may/2012
	Get the identifier values suggested for this company
	To get the original suggestion, get_original is true
	*****************/
	public function fetch_suggestions_for_company_identifiers($company_id,$get_original,&$data_arr,&$data_count){
		$db = new db();
		$q = "select suggested_by,date_suggested,n.identifier_id,value,status_note,im.name as identifier_name,member_type,work_email from ".TP."company_identifiers_suggestions as n left join ".TP."company_identifier_master as im on(n.identifier_id=im.identifier_id) left join ".TP."member as m on(n.suggested_by=m.mem_id) where n.company_id='".$company_id."'";
		
		if($get_original){
			$q.=" and is_correction='n'";
		}else{
			$q.=" and is_correction='y'";
		}
		
		$q.=" order by date_suggested,suggested_by,identifier_name";
		$ok = $db->select_query($q);
		if(!$ok){
			return false;
		}
		$data_count = $db->row_count();
		if(0==$data_count){
			return true;
		}
		$data_arr = $db->get_result_set_as_array();
		return true;
	}
	/*******************
	sng:23/may/2012
	**************/
	public function fetch_suggestions_for_company_identifiers_with_grouping($company_id,$get_original,&$group_data_arr,&$group_count){
		$data_count = 0;
		$data_arr = NULL;
		
		$ok = $this->fetch_suggestions_for_company_identifiers($company_id,$get_original,$data_arr,$data_count);
		if(!$ok){
			return false;
		}
		
		if(0==$data_count){
			$group_count = 0;
			return true;
		}
		/*************
		here we do grouping, based on member id and date
		****************/
		$curr_member = -1;
		//we do not start with 0 because deal added by admin gets 0 as mem id
		$curr_date = "0000-00-00 00:00:00";
		$group_data_arr = array();
		$suggestion_columns_head = -1;
		$group_count = 0;
		
		for($i=0;$i<$data_count;$i++){
			if(($data_arr[$i]['suggested_by']!=$curr_member)||($data_arr[$i]['date_suggested']!=$curr_date)){
				/*******
				start of new suggestion section
				update the curr data and the head pointer
				create a new group
				******/
				$curr_member = $data_arr[$i]['suggested_by'];
				$curr_date = $data_arr[$i]['date_suggested'];
				$suggestion_columns_head++;
				
				$group_data_arr[$suggestion_columns_head] = array();
				
				/**********************
				If this is suggested by a member, (suggested_by != 0 ) we can set the member, else set it as Admin
				***********************/
				if($data_arr[$i]['suggested_by']!=0){
					$work_email = $data_arr[$i]['work_email'];
					$tokens = explode('@',$work_email);
					$work_email_suffix = $tokens[1];
				
					$group_data_arr[$suggestion_columns_head]['suggested_by'] = $data_arr[$i]['member_type']."@".$work_email_suffix;
				}else{
					$group_data_arr[$suggestion_columns_head]['suggested_by'] = 'Admin';
				}
				if($data_arr[$i]['date_suggested']!="0000-00-00 00:00:00"){
					$group_data_arr[$suggestion_columns_head]['suggested_on'] = date('jS M Y',strtotime($data_arr[$i]['date_suggested']));
				}else{
					$group_data_arr[$suggestion_columns_head]['suggested_on'] = 'N/A';
				}
				$group_data_arr[$suggestion_columns_head]['suggested_identifiers_count'] = 0;
				$group_data_arr[$suggestion_columns_head]['suggested_identifiers'] = array();
			}else{
				/************
				use the current suggested by unit
				************/
			}
			$group_data_arr[$suggestion_columns_head]['suggested_identifiers'][] = $data_arr[$i];
			$group_data_arr[$suggestion_columns_head]['suggested_identifiers_count']++;
		}
		$group_count = $suggestion_columns_head+1;
		/**********
		suggestion_columns_head is zero based
		*************/
		return true;
	}
	
	/***************
	sng:14/may/2012
	
	The member may suggest a name change or a logo or both.
	It is ok if the member just send the logo OR a name.
	If name is specified, we can check if it is really a suggestion or not by matching against the current name but
	we cannot do so for the logo.
	******************/
	public function front_submit_firm_corrective_suggestion($firm_id,$member_id,$data_arr,&$validation_passed,&$msg){
		$db = new db();
		$changes_made = "";
		//get the current data
		require_once("classes/class.company.php");
		$comp = new company();
		
		$current_data = NULL;
		$ok = $comp->get_company($firm_id,$current_data);
		if(!$ok){
			return false;
		}
		$firm_current_name = $current_data['name'];
		$suggested_name = $data_arr['name'];
		
		$firm_current_logo = $current_data['logo'];
		$suggested_logo = "";
		//we will create our own new name
		
		if($suggested_name != ""){
			if($suggested_name != $firm_current_name){
				$changes_made.=",name suggested";
			}else{
				/******
				not really a suggestion since the name is same.
				we also set the suggestion to blank
				******/
				$suggested_name = "";
			}
		}
		/*****************
		note: since the name is always there, we can have only name suggestion.
		For logo it is different. It may happen that there was no logo originally.
		if so, the suggested logo is set as the logo of that firm
		***********************/
		if($_FILES['logo']['name']!=""){
			require_once("classes/class.image_util.php");
			$img_util = new image_util();
			$uploaded_img_name = time()."_".clean_filename(basename($_FILES['logo']['name']));
			$ok = $img_util->create_resized($_FILES['logo']['tmp_name'],LOGO_PATH."/thumbnails",$uploaded_img_name,200,200,false);
			if($ok){
				$suggested_logo = $uploaded_img_name;
				if($firm_current_logo != ""){
					//store as suggestion
					$changes_made.=",logo suggested";
				}else{
					//try to set the logo
					$q = "update ".TP."company set logo='".mysql_real_escape_string($suggested_logo)."' where company_id='".$firm_id."'";
					$ok = $db->mod_query($q);
					if($ok){
						$changes_made.=",logo added";
					}else{
						$changes_made.=",logo suggested";
					}
				}
			}else{
				/***********
				sng:18/may/2012
				could not store the logo so cannot treat it as suggestion
				**************/
				$suggested_logo = "";
			}
		}
		/***************
		if there is a suggestion, store it else validation error
		****************/
		if($changes_made == ""){
			$validation_passed = false;
			$msg = "No effective suggestion";
			return true;
		}
		/***************
		there are some changes, so create the query
		****************/
		$changes_made = substr($changes_made,1);
		$q = "insert into ".TP."company_suggestions set company_id='".$firm_id."',suggested_by='".$member_id."',date_suggested='".date("Y-m-d H:i:s")."',name='".mysql_real_escape_string($suggested_name)."',type='".mysql_real_escape_string($current_data['type'])."',logo='".mysql_real_escape_string($suggested_logo)."',status_note='".$changes_made."',is_correction='y'";
		$ok = $db->mod_query($q);
		if(!$ok){
			return false;
		}
		$validation_passed = true;
		$msg = "Your suggestion has been stored";
		return true;
	}
	/*****************
	sng:30/may/2012
	*********/
	public function front_submit_company_corrective_suggestion($company_id,$member_id,$data_arr,&$validation_passed,&$msg){
		$db = new db();
		
		//get the current data
		require_once("classes/class.company.php");
		$comp = new company();
		$current_data = NULL;
		$ok = $comp->get_company($company_id,$current_data);
		if(!$ok){
			return false;
		}
		$company_current_name = $current_data['name'];
		$suggested_name = $data_arr['name'];
		
		$company_current_hq_country = $current_data['hq_country'];
		$suggested_hq_country = $data_arr['hq_country'];
		
		$company_current_sector = $current_data['sector'];
		$suggested_sector = $data_arr['sector'];
		
		$company_current_industry = $current_data['industry'];
		$suggested_industry = $data_arr['industry'];
		
		$company_current_logo = $current_data['logo'];
		$suggested_logo = "";
		//we will create our own new name
		
		$changes_made = "";
		/*******************
		check each and see if there is a suggestion value and whether
		it is different from what is stored currently
		*************************/
		if($suggested_name != ""){
			if($company_current_name == ""){
				/************
				no name is set currently so we try to set the name
				if we can do so, we store the suggestion with status of 'name added' else the default of 'name suggested'
				We insert the suggestion record later
				**************/
				$q = "update ".TP."company set name='".mysql_real_escape_string($suggested_name)."' where company_id='".$company_id."'";
				$ok = $db->mod_query($q);
				if($ok){
					$changes_made.=",name added";
				}else{
					$changes_made.=",name suggested";
				}
			}else{
				if($company_current_name == $suggested_name){
					/**************
					we are specifying what is there currently so this is not
					really a suggestion. We set the $suggested_name to blank
					so that when we run the insert query, the value is not stored for 'name'
					***************/
					$suggested_name = "";
				}else{
					/************
					this is a valid suggestion. We make a note.
					*********/
					$changes_made.=",name suggested";
				}
			}
		}
		/************************************************************************************************/
		if($suggested_hq_country != ""){
			if($company_current_hq_country == ""){
				/************
				no hq_country is set currently so we try to set the hq_country
				if we can do so, we store the suggestion with status of 'hq country added' else the default of 'hq country suggested'
				We insert the suggestion record later
				**************/
				$q = "update ".TP."company set hq_country='".mysql_real_escape_string($suggested_hq_country)."' where company_id='".$company_id."'";
				$ok = $db->mod_query($q);
				if($ok){
					$changes_made.=",hq country added";
				}else{
					$changes_made.=",hq country suggested";
				}
			}else{
				if($company_current_hq_country == $suggested_hq_country){
					/**************
					we are specifying what is there currently so this is not
					really a suggestion. We set the $suggested_hq_country to blank
					so that when we run the insert query, the value is not stored for 'hq_country'
					***************/
					$suggested_hq_country = "";
				}else{
					/************
					this is a valid suggestion. We make a note.
					*********/
					$changes_made.=",hq country suggested";
				}
			}
		}
		/*******************************************************************************************/
		if($suggested_sector != ""){
			if($company_current_sector == ""){
				/************
				no sector is set currently so we try to set the sector
				if we can do so, we store the suggestion with status of 'sector added' else the default of 'sector suggested'
				We insert the suggestion record later
				**************/
				$q = "update ".TP."company set sector='".mysql_real_escape_string($suggested_sector)."' where company_id='".$company_id."'";
				$ok = $db->mod_query($q);
				if($ok){
					$changes_made.=",sector added";
				}else{
					$changes_made.=",sector suggested";
				}
			}else{
				if($company_current_sector == $suggested_sector){
					/**************
					we are specifying what is there currently so this is not
					really a suggestion. We set the $suggested_sector to blank
					so that when we run the insert query, the value is not stored for 'sector'
					***************/
					$suggested_sector = "";
				}else{
					/************
					this is a valid suggestion. We make a note.
					*********/
					$changes_made.=",sector suggested";
				}
			}
		}
		/********************************************************************************************/
		if($suggested_industry != ""){
			if($company_current_industry == ""){
				/************
				no industry is set currently so we try to set the industry
				if we can do so, we store the suggestion with status of 'industry added' else the default of 'industry suggested'
				We insert the suggestion record later
				**************/
				$q = "update ".TP."company set industry='".mysql_real_escape_string($suggested_industry)."' where company_id='".$company_id."'";
				$ok = $db->mod_query($q);
				if($ok){
					$changes_made.=",industry added";
				}else{
					$changes_made.=",industry suggested";
				}
			}else{
				if($company_current_industry == $suggested_industry){
					/**************
					we are specifying what is there currently so this is not
					really a suggestion. We set the $suggested_industry to blank
					so that when we run the insert query, the value is not stored for 'industry'
					***************/
					$suggested_industry = "";
				}else{
					/************
					this is a valid suggestion. We make a note.
					*********/
					$changes_made.=",industry suggested";
				}
			}
		}
		/*****************************************************************************/
		if($_FILES['logo']['name']!=""){
			require_once("classes/class.image_util.php");
			$img_util = new image_util();
			$uploaded_img_name = time()."_".clean_filename(basename($_FILES['logo']['name']));
			$ok = $img_util->create_resized($_FILES['logo']['tmp_name'],LOGO_PATH."/thumbnails",$uploaded_img_name,200,200,false);
			if($ok){
				$suggested_logo = $uploaded_img_name;
				if($company_current_logo != ""){
					//store as suggestion
					$changes_made.=",logo suggested";
				}else{
					//try to set the logo
					$q = "update ".TP."company set logo='".$suggested_logo."' where company_id='".$company_id."'";
					$ok = $db->mod_query($q);
					if($ok){
						$changes_made.=",logo added";
					}else{
						$changes_made.=",logo suggested";
					}
				}
			}else{
				/***********
				could not store the logo so cannot treat it as suggestion. Set it to blank
				**************/
				$suggested_logo = "";
			}
		}
		/******************************************************************************/
		/***************
		if there is a suggestion, store it else validation error
		****************/
		if($changes_made == ""){
			$validation_passed = false;
			$msg = "No effective suggestion";
			return true;
		}
		
		/***************
		there are some changes, so create the query
		****************/
		$changes_made = substr($changes_made,1);
		$q = "insert into ".TP."company_suggestions set company_id='".$company_id."',suggested_by='".$member_id."',date_suggested='".date("Y-m-d H:i:s")."',name='".mysql_real_escape_string($suggested_name)."',type='company',hq_country='".mysql_real_escape_string($suggested_hq_country)."',sector='".mysql_real_escape_string($suggested_sector)."',industry='".mysql_real_escape_string($suggested_industry)."',logo='".mysql_real_escape_string($suggested_logo)."',status_note='".$changes_made."',is_correction='y'";
		$ok = $db->mod_query($q);
		if(!$ok){
			return false;
		}
		$validation_passed = true;
		$msg = "Your suggestion has been stored";
		return true;
	}
	
	/******************
	The case for handling identifiers is a bit different.
	A member can send values for one or more identifiers. We store a record for each
	
	The POST data is:
	company_id: 11571
	identifier_ids[] 1
	identifier_ids[] 2
	identifier_ids[] 3
	identifier_ids[] 4
	identifier_ids[] 5
	identifier_ids[] 6
	
	identifier_id_1	
	identifier_id_2	
	identifier_id_3	
	identifier_id_4	
	identifier_id_5	
	identifier_id_6
	
	Here is an assumption: The suggestion form send value for all identifiers (those values can be blank) and the search for current
	identifiers gives records for the same identifiers and all are same as the list of identifiers in the master list.
	
	also, in the company identifier table, there cannot be an identifier with blank value.
	In the current fetch list, if the identifier shows blank, it means that identifier is not set
	**********/
	public function front_submit_company_identifier_corrective_suggestion($company_id,$member_id,$data_arr,&$validation_passed,&$msg){
		$db = new db();
		/*****************
		We first check whether any identifier is sent or not. Remember that we also send the identifier ids in the
		array identifier_ids and the posted keys are identifier_id_<id>
		******************/
		$posted_identifier_id_count = count($data_arr['identifier_ids']);
		if(0 == $posted_identifier_id_count){
			//noting sent
			$msg = "No suggestion sent";
			return true;
		}
		
		/****************************
		Now get the current data for identifiers for this company and create a lookup array
		with identifier id as key, and identifier record as value so that we can check whether the suggested
		value is same or not OR whether the current data is blank or not
		***********************/
		require_once("classes/class.company.php");
		$comp = new company();
		$current_indentifier_data = NULL;
		$current_indentifier_data_count = 0;
		$ok = $comp->front_get_company_identifiers($company_id,$current_indentifier_data,$current_indentifier_data_count);
		if(!$ok){
			return false;
		}
		
		$current_identifier_lookup = array();
		for($i=0;$i<$current_indentifier_data_count;$i++){
			$key = $current_indentifier_data[$i]['identifier_id'];
			$current_identifier_lookup[$key] = $current_indentifier_data[$i];
		}
		/*********************
		Now we check each identifier id posted, create the post data key, get suggestion value
		We also create the query to insert the suggestion
		*********************/
		$q = "";
		$report_date = date("Y-m-d H:i:s");
		
		for($i=0;$i<$posted_identifier_id_count;$i++){
			$posted_identifier_id = $data_arr['identifier_ids'][$i];
			$posted_identifier_key = "identifier_id_".$posted_identifier_id;
			$posted_identifier_value = $data_arr[$posted_identifier_key];
			/******************
			if value is blank, we ignore it
			If it is same as what is stored, we ignore it
			***************/
			if($posted_identifier_value == ""){
				continue;
			}
			if($posted_identifier_value == $current_identifier_lookup[$posted_identifier_id]['value']){
				continue;
			}
			/***************
			Need to store this.
			But, we also check if the current value is blank or not. If blank, we set it
			****************/
			$status_note = "suggested";
			//default
			if($current_identifier_lookup[$posted_identifier_id]['value'] == ""){
				$updt_q = "insert into ".TP."company_identifiers set company_id='".$company_id."',identifier_id='".$posted_identifier_id."',value='".mysql_real_escape_string($posted_identifier_value)."'";
				/*****************
				Remember that if the value is not set, it means, the company does not have that identifier record
				******************/
				$ok = $db->mod_query($updt_q);
				if($ok){
					$status_note = "set";
				}
			}else{
				//we will just store the suggestion
			}
			$q.=",('".$company_id."','".$member_id."','".$report_date."','".$posted_identifier_id."','".mysql_real_escape_string($posted_identifier_value)."','".$status_note."','y')";
		}
		/*************
		If all entries are rejected, then q is blank
		*****************/
		if($q == ""){
			$validation_passed = false;
			$msg = "No suggestions sent";
			return true;
		}
		/****************
		there is suggestions. get rid of the first ',' and insert into the suggestion table
		*****************/
		$q = substr($q,1);
		$q = "insert into ".TP."company_identifiers_suggestions (company_id,suggested_by,date_suggested,identifier_id,value,status_note,is_correction) values ".$q;
		$ok = $db->mod_query($q);
		if(!$ok){
			return false;
		}else{
			$msg = "Your suggestion has been stored";
			$validation_passed = true;
			return true;
		}
		return true;
	}
}
?>