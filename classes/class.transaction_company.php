<?php
/*****************
sng:1/feb/2012
We use this class to manage the companies associated with a deal
******************/
class transaction_company{
	
	/****************************
	sng:9/mar/2012
	We put the code to associate one or more companies with a deal from the front end
	check if data is there or not
	In simple submission, we send only the company names
	in detailed submission, we have company roles and footnotes for each participating company
	
	sng:18/apr/2012
	We add two more arguments to support the provision that we need to log the addition of companies
	
	sng:24/apr/2012
	Do check if same company has been specified again or not. You can do that by keeping a simple lookup array.
	******************************/
	public function front_set_participants_for_deal($deal_id,$data,$suggestion_mem_id,$deal_add_date_time){
		require_once("classes/class.company.php");
		$comp = new company();
		
		$db = new db();
		$q = "";
		
		$suggestion_data_arr = array();
		
		/****************
		sng:24/apr/2012
		lookup array to store the companies specified
		****************/
		$specified_companies = array();
		
		$company_count = count($data['companies']);
		$company_found = false;
		$company_id = 0;
		for($company_i=0;$company_i<$company_count;$company_i++){
			//this can be blank, so we check
			if($data['companies'][$company_i]!=""){
				/*******************
				sng:24/apr/2012
				company name given, so first check if it has already been suggested or not
				******************/
				if(array_search($data['companies'][$company_i],$specified_companies)!==false){
					/**************
					already processed, ignore
					************/
					continue;
				}else{
					/***********
					add to array
					***********/
					$specified_companies[] = $data['companies'][$company_i];
				}
				/********************************/
				$ok = company_id_from_name($data['companies'][$company_i],'company',$company_id,$company_found);
				if(!$ok){
					continue;
				}else{
					if(!$company_found){
						//create it
						$ok = $comp->front_quick_create_company_blf($suggestion_mem_id,$data['companies'][$company_i],'company',$company_id);
						if(!$ok){
							continue;
						}
					}
					//we have the company
					
					//get the role id
					//remember that the simple deal submission has no role or footnote
					$key = "company_participant_role_".$company_i;
					if(isset($data[$key])&&($data[$key]!="")){
						$company_role_id = $data[$key];
					}else{
						$company_role_id = 0;
					}
					//get the footnote
					$key = "company_participant_note_".$company_i;
					if(isset($data[$key])&&($data[$key]!="")){
						$company_footnote = $data[$key];
					}else{
						$company_footnote = "";
					}
					/***********
					see the suggest_a_deal_view.php to see what text is used as default text
					**************/
					if($company_footnote=="footnote") $company_footnote="";
					
					$q.=",('".$deal_id."','".$company_id."','".$company_role_id."','".mysql_real_escape_string($company_footnote)."')";
					/****************
					sng:18/apr/2012
					Need to prepare the suggestion array to log the submission of companies for the deal
					*****************/
					$suggestion_data_arr[] = array('company_name'=>$data['companies'][$company_i],'role_id'=>$company_role_id,'footnote'=>$company_footnote);
					
				}
			}else{
				//this is blank to skip
			}
		}
		if($q!=""){
			$q = substr($q,1);
		}
		$q = "insert into ".TP."transaction_companies (transaction_id,company_id,role_id,footnote) values ".$q;
		$ok = $db->mod_query($q);
		if(!$ok){
			return false;
		}else{
			/*****************
			sng:18/apr/2012
			Part of suggestion tracking. When we add participant company with deal submission, we also record the fact in the suggestion table
			******************/
			require_once("classes/class.transaction_suggestion.php");
			$trans_suggestion = new transaction_suggestion();
			$trans_suggestion->participant_added_via_deal_submission($deal_id,$suggestion_mem_id,$deal_add_date_time,$suggestion_data_arr);
			return true;
		}
	}
	
	/*****************************
	sng:24/apr/2012
	In case a new company is specified during edit. We add the company as participant
	to the deal
	*********************/
	public function front_add_participant_for_deal($deal_id,$participant_data,&$validation_passed,&$err_arr){
		$db = new db();
		
		$validation_passed = true;
		if($participant_data['company_name']==""){
			$validation_passed = false;
			$err_arr['company_id'] = "Please specify";
		}else{
			//company specified but was it selected from list
			if($participant_data['company_id']==""){
				$validation_passed = false;
				$err_arr['company_id'] = "Not found, please create it first";
			}
		}
		
		/**********
		for front end, we relax validation a bit. We do not require the role
		**************/
		
		if(!$validation_passed){
			return true;
		}
		/***********************
		now check if the company is in the deal or not
		***************************/
		$q = "select count(*) as cnt from ".TP."transaction_companies where transaction_id='".$deal_id."' and company_id='".$participant_data['company_id']."'";
		$ok = $db->select_query($q);
		if(!$ok){
			return false;
		}
		$row = $db->get_row();
		if($row['cnt']>0){
			//already added to this deal
			$validation_passed = false;
			$err_arr['company_id'] = "Already added";
		}
		if(!$validation_passed){
			return true;
		}
		/******************
		now add
		******************/
		$q = "insert into ".TP."transaction_companies set transaction_id='".$deal_id."', company_id='".$participant_data['company_id']."', role_id='".$participant_data['role_id']."', footnote='".mysql_real_escape_string($participant_data['footnote'])."'";
		$ok = $db->mod_query($q);
		if(!$ok){
			return false;
		}
		return true;
	}
	/*******************
	sng:25/apr/2012
	*******************/
	public function front_update_participant_role_for_deal($deal_id,$company_id,$role_id){
		$db = new db();
		$q = "update ".TP."transaction_companies set role_id='".$role_id."' where transaction_id='".$deal_id."' and company_id='".$company_id."'";
		$ok = $db->mod_query($q);
		if(!$ok){
			return false;
		}
		return true;
	}
	public function front_update_participant_footnote_for_deal($deal_id,$company_id,$footnote){
		$db = new db();
		$q = "update ".TP."transaction_companies set footnote='".mysql_real_escape_string($footnote)."' where transaction_id='".$deal_id."' and company_id='".$company_id."'";
		$ok = $db->mod_query($q);
		if(!$ok){
			return false;
		}
		return true;
	}
	/***********
	sng:1/feb/2012
	Just get the names of the participants only. Used in listing codes
	Return blank array if there is no record
	
	sng:18/oct/2012
	Let us add the country/sector/industry.
	
	In old DD, those front end codes not only showed the company name but also showed the country/sector/industry when listing deals.
	Those front end codes called transaction::front_deal_search_paged which in turn call this.
	In new DD, with the concept of participants, we still need to support those codes.
	*************/
	public function get_deal_participants($deal_id,&$participant_list){
		$db = new db();
		$q = "select tc.company_id,name as company_name,hq_country,sector,industry from ".TP."transaction_companies as tc left join ".TP."company as c on (tc.company_id=c.company_id) where transaction_id='".$deal_id."'";
		$ok = $db->select_query($q);
		if(!$ok){
			return false;
		}
		$cnt = $db->row_count();
		if(0==$cnt){
			$participant_list = array();
			return true;
		}
		$participant_list = $db->get_result_set_as_array();
		return true;
	}
	
	/***********
	sng:1/feb/2012
	Get the full details regarding the participating company
	Return blank array if there is no record
	
	sng:22/mar/2012
	We need the role_id in some places
	*************/
	public function get_deal_participants_detailed($deal_id,&$participant_list){
		$db = new db();
		$q = "select tc.company_id,tc.role_id,name as company_name,hq_country,sector,industry,role_name,footnote from ".TP."transaction_companies AS tc left join ".TP."company as c on (tc.company_id=c.company_id) left join ".TP."transaction_company_role_master as trm on(tc.role_id=trm.role_id) where transaction_id='".$deal_id."'";
		$ok = $db->select_query($q);
		if(!$ok){
			return false;
		}
		$cnt = $db->row_count();
		if(0==$cnt){
			$participant_list = array();
			return true;
		}
		$participant_list = $db->get_result_set_as_array();
		return true;
	}
	/*************************
	sng:23/feb/2012
	Get the participant's logos for a deal
	*************************/
	public function get_deal_participants_logos($deal_id,&$logo_list){
		$db = new db();
		$q = "select logo from ".TP."transaction_companies AS tc left join ".TP."company as c on (tc.company_id=c.company_id) where transaction_id='".$deal_id."' and logo!='' order by tc.id";
		$ok = $db->select_query($q);
		if(!$ok){
			return false;
		}
		$cnt = $db->row_count();
		if(0==$cnt){
			$logo_list = array();
			return true;
		}
		$logo_list = $db->get_result_set_as_array();
		return true;
	}
	/***********
	sng:13/feb/2012
	*************/
	public function admin_get_participants_for_deal($deal_id,&$participant_list,&$participant_count){
		$db = new db();
		$q = "select tc.company_id,name as company_name,hq_country,sector,industry,tc.role_id,role_name,tc.footnote from ".TP."transaction_companies AS tc left join ".TP."company as c on (tc.company_id=c.company_id) left join ".TP."transaction_company_role_master as trm on(tc.role_id=trm.role_id) where transaction_id='".$deal_id."'";
		$ok = $db->select_query($q);
		if(!$ok){
			return false;
		}
		$participant_count = $db->row_count();
		if(0==$participant_count){
			return true;
		}
		$participant_list = $db->get_result_set_as_array();
		return true;
	}
	/*****************
	sng:14/feb/2012
	*******************/
	public function admin_add_participant_for_deal($deal_id,$participant_data,&$validation_passed,&$err_arr){
		$db = new db();
		
		$validation_passed = true;
		if($participant_data['company_name']==""){
			$validation_passed = false;
			$err_arr['company_id'] = "Please specify";
		}else{
			//company specified but was it selected from list
			if($participant_data['company_id']==""){
				$validation_passed = false;
				$err_arr['company_id'] = "Not found, please create it first";
			}
		}
		
		if($participant_data['role_id'] == ""){
			$validation_passed = false;
			$err_arr['role_id'] = "Please specify";
		}
		
		if(!$validation_passed){
			return true;
		}
		/***********************
		now check if the company is in the deal or not
		***************************/
		$q = "select count(*) as cnt from ".TP."transaction_companies where transaction_id='".$deal_id."' and company_id='".$participant_data['company_id']."'";
		$ok = $db->select_query($q);
		if(!$ok){
			return false;
		}
		$row = $db->get_row();
		if($row['cnt']>0){
			//already added to this deal
			$validation_passed = false;
			$err_arr['company_id'] = "Already added";
		}
		if(!$validation_passed){
			return true;
		}
		/******************
		now add
		******************/
		$q = "insert into ".TP."transaction_companies set transaction_id='".$deal_id."', company_id='".$participant_data['company_id']."', role_id='".$participant_data['role_id']."', footnote='".mysql_real_escape_string($participant_data['footnote'])."'";
		$ok = $db->mod_query($q);
		if(!$ok){
			return false;
		}
		return true;
	}
	/*************************
	sng:14/feb/2012
	************************/
	public function admin_remove_participant_for_deal($deal_id,$company_id){
		$db = new db();
		$q = "delete from ".TP."transaction_companies where transaction_id='".$deal_id."' and company_id='".$company_id."'";
		$ok = $db->mod_query($q);
		if(!$ok){
			return false;
		}
		return true;
	}
	/*************************
	sng:14/feb/2012
	************************/
	public function admin_update_participant_for_deal($deal_id,$data_arr){
		$db = new db();
		$q = "update ".TP."transaction_companies set role_id='".$data_arr['role_id']."',footnote='".mysql_real_escape_string($data_arr['footnote'])."' where transaction_id='".$deal_id."' and company_id='".$data_arr['company_id']."'";
		$ok = $db->mod_query($q);
		if(!$ok){
			return false;
		}
		return true;
	}
	/**********************************************deal company role section**************************************/
	/******************************
	sng:30/jan/2012
	Now we have one or more companies associated with a deal. For each company we have a role. We define the list of role via admin
	********************/
	public function admin_get_all_deal_company_roles(&$data_arr,&$data_count){
		global $g_db;
		
		$q = "select * from ".TP."transaction_company_role_master order by for_deal_type,role_name";
		$ok = $g_db->select_query($q);
		if(!$ok){
			return false;
		}
		$data_count = $g_db->row_count();
		if($data_count == 0){
			//no recs
			return true;
		}
		//recs so
		$data_arr = $g_db->get_result_set_as_array();
		return true;
	}
	public function admin_add_deal_company_role($data_arr,&$validation_passed,&$err_arr){
		global $g_db;
		//validation
		$validation_passed = true;
		if($data_arr['role_name'] == ""){
			$err_arr['role_name'] = "Please specify the role name";
			$validation_passed = false;
		}
		
		if($data_arr['for_deal_type'] == ""){
			$err_arr['for_deal_type'] = "Please select the deal type";
			$validation_passed = false;
		}
		
		
		
		if(!$validation_passed){
			return true;
		}
		//at least all the items are there, so do the duplicate check
		$q = "select count(*) as cnt from ".TP."transaction_company_role_master where role_name='".mysql_real_escape_string($data_arr['role_name'])."' AND for_deal_type='".mysql_real_escape_string($data_arr['for_deal_type'])."'";
		$ok = $g_db->select_query($q);
		
		if(!$ok){
			return false;
		}
		$row = $g_db->get_row();
		if($row['cnt'] > 0){
			//this name exists
			$err_arr['for_deal_type'] = "This role already exists";
			$validation_passed = false;
		}
		
		
		if(!$validation_passed){
			//no need to proceed
			return true;
		}
		
		//insert data
		$q = "insert into ".TP."transaction_company_role_master set role_name='".mysql_real_escape_string($data_arr['role_name'])."',for_deal_type='".mysql_real_escape_string($data_arr['for_deal_type'])."'";
		$ok = $g_db->mod_query($q);
		if(!$ok){
			return false;
		}
		/////////////////
		//data inserted
		$validation_passed = true;
		return true;
	}
	public function admin_update_deal_company_role($role_id,$data_arr,&$validation_passed,&$msg){
		global $g_db;
		//validation
		$validation_passed = true;
		if($data_arr['role_name'] == ""){
			$msg = "Please specify the role name";
			$validation_passed = false;
		}
		
		if(!$validation_passed){
			return true;
		}
		
		//at least all the items are there, so do the duplicate check
		$q = "select count(*) as cnt from ".TP."transaction_company_role_master where role_name='".mysql_real_escape_string($data_arr['role_name'])."' AND for_deal_type='".mysql_real_escape_string($data_arr['for_deal_type'])."'";
		$ok = $g_db->select_query($q);
		
		if(!$ok){
			return false;
		}
		$row = $g_db->get_row();
		if($row['cnt'] > 0){
			//this name exists
			$msg = "This role already exists";
			$validation_passed = false;
		}
		
		
		if(!$validation_passed){
			//no need to proceed
			return true;
		}
		
		//update data
		$q = "update ".TP."transaction_company_role_master set role_name='".mysql_real_escape_string($data_arr['role_name'])."' where role_id='".$role_id."'";
		$ok = $g_db->mod_query($q);
		if(!$ok){
			return false;
		}
		/////////////////
		//data inserted
		$validation_passed = true;
		return true;
	}
	/**********************
	sng:8/feb/2012
	***********************/
	public function get_all_deal_company_roles_for_deal_type($deal_type,&$data_arr,&$data_count){
		global $g_db;
		
		$q = "select * from ".TP."transaction_company_role_master where for_deal_type='".$deal_type."' order by role_name";
		$ok = $g_db->select_query($q);
		if(!$ok){
			return false;
		}
		$data_count = $g_db->row_count();
		if($data_count == 0){
			//no recs
			return true;
		}
		//recs so
		$data_arr = $g_db->get_result_set_as_array();
		return true;
	}
	/**********************************************deal company role section**************************************/
}
?>