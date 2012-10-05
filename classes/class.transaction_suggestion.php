<?php
/******************
sng:20/mar/2012

We put methods dealing with deal edit suggestions
*******************/
class transaction_suggestion{
	
	/******************************
	sng:26/mar/2012
	If a member posts correction on a deal, it is interpreted as "you are saying the deal, subject to your edits, is correct"
	
	sng:25/apr/2012
	If the member has posted the deal, the poster will not be marked as verifier
	*****************************/
	public function mark_member_as_verifier($deal_id,$mem_id){
		require_once("classes/class.transaction_verification.php");
		$trans_verify = new transaction_verification();
		$msg = "";
		$ok = $trans_verify->verification_by_member($deal_id,$mem_id,$msg);
		/******************************************
		If error, well it is not a big deal
		It may also happen that the member is sending edit twice, may be one for each section. No problem. The code
		checks if the member has already confirmed the deal or not. If so, no new record is
		inserted and an error message is returned which we ignore
		
		We also ignore the confirm message since no place to show.
		******************/
	}
	
	/*******************
	sng:30/apr/2012
	We now store the suggestions in transaction_note-suggestions
	Also, we store date and time.
	
	When we store the deal, we also store the original note here so that we can get the original suggestion.
	To distinguish, we store the subsequent corrections with is_correction = y
	
	Since we are also updating the actual note associated with the deal, we set status note to added only if note
	updation is a success
	*************************/
	public function front_submit_note($deal_id,$mem_id,$note,&$msg){
		$db = new db();
		
		if($note==""){
			$msg = "Please specify the note";
			return true;
		}
		
		/*********************
		sng:27/apr/2012
		we also update the deal note. We no longer need admin intervention.
		Let us see how this works out
		
		sng:5/oct/2012
		We have moved this funciton to new class
		***********************/
		require_once("classes/class.transaction_note.php");
		$trans_note = new transaction_note();
		$ok = $trans_note->front_append_to_note($deal_id,$note);
		
		$status_note = "suggested";
		if($ok){
			$status_note = "added";
		}
		$report_date = date("Y-m-d H:i:s");
		$q = "insert into ".TP."transaction_note_suggestions set deal_id='".$deal_id."', suggested_by='".$mem_id."', date_suggested='".$report_date."', note='".mysql_real_escape_string($note)."',status_note='".$status_note."',is_correction='y'";
		
		$ok = $db->mod_query($q);
		if(!$ok){
			return false;
		}
		$msg = "Your suggestion has been stored";
		
		$this->mark_member_as_verifier($deal_id,$mem_id);
		return true;
	}
	
	/*********************
	sng:2/may/2012
	We need to change this a bit.
	Now when sources are suggested during edit, they are also added to the deal.
	We need to do some validation though:
	1) The source url cannot be blank
	2) The source url must not be a duplicate
	3) It must not be there in the current sources for the deal
	*************************/
	public function front_submit_sources($deal_id,$mem_id,$links_arr,&$validation_passed,&$msg){
		$db = new db();
		
		$report_date = date("Y-m-d H:i:s");
		
		$validation_passed = true;
		$link_cnt = count($links_arr);
		if(0 == $link_cnt){
			$validation_passed = false;
			$msg = "No sources specified";
			return true;
		}
		
		$has_source = false;
		for($source_i=0;$source_i<$link_cnt;$source_i++){
			/**********************
			it may happen that the items are all blank
			************/
			if($links_arr[$source_i]!=""){
				$has_source = true;
				break;
			}
		}
		if(!$has_source){
			$validation_passed = false;
			$msg = "Please specify at least one link";
			return true;
		}
		
		/**************
		Now we get the list of sources currently associated with the deal
		and create our lookup array
		****************/
		require_once("classes/class.transaction_source.php");
		$trans_source = new transaction_source();
		$current_sources_arr = NULL;
		$current_sources_count = 0;
		$lookup_current_sources = array();
		
		$ok = $trans_source->get_deal_sources($deal_id,$current_sources_arr);
		if(!$ok){
			return false;
		}
		$current_sources_count = count($current_sources_arr);
		for($i=0;$i<$current_sources_count;$i++){
			$lookup_current_sources[] = $current_sources_arr[$i]['source_url'];
		}
		/**********
		lookup array to remember the siurce urls submitted and processed. This to ensure that
		in the submission the duplicates are filtered out
		***************/
		$lookup_submitted_sources = array();
		
		for($source_i=0;$source_i<$link_cnt;$source_i++){
			$source_url = $links_arr[$source_i];
			if($source_url == ""){
				continue;
			}
			if(array_search($source_url,$lookup_current_sources)===false){
				if(array_search($source_url,$lookup_submitted_sources)===false){
					//remember to populate lookup_submitted_sources
					$lookup_submitted_sources[] = $source_url;
					$suggestion_status = "suggested";
					//if we mage to add, we set this to added
					$source_add_validation_passed = false;
					$source_add_validation_err = array();
					$ok = $trans_source->front_add_source_for_deal($deal_id,$source_url,$source_add_validation_passed,$source_add_validation_err);
					if($ok && $source_add_validation_passed){
						$suggestion_status = "added";
					}
					/********************************
					Now we create the query fragment to add this as a suggestion
					**********************************/
					$q.=",('".$deal_id."','".$mem_id."','".$report_date."','".mysql_real_escape_string($source_url)."','".$suggestion_status."','y')";
				}else{
					/*************
					this has been processed already so ignore
					***************/
				}
			}else{
				/***********
				this is already stored with the deal so not really a suggestion, ignore
				************/
			}
		}
		
		if($q == ""){
			$validation_passed = false;
			$msg = "There is no effective suggestion";
			return true;
		}
		/************************************************/
		//get rid of the first ,
		$q = substr($q,1);
		$q = "insert into ".TP."transaction_sources_suggestions (deal_id,suggested_by,date_suggested,source_url,status_note,is_correction) values ".$q;
		$ok = $db->mod_query($q);
		if(!$ok){
			return false;
		}else{
			$msg = "Your suggestion has been stored";
			$validation_passed = true;
			$this->mark_member_as_verifier($deal_id,$mem_id);
			return true;
		}
		
	}
	
	/*****************************************************************************************
	sng:23/apr/2012
	We have changed the front end a bit.
	
	The array is like:
		
	companies[]	s n holding
	companies[]	gg com [new suggestion]
	
	new_entry_0:n
	new_entry_1:y
	
	participant_note_0:
	participant_note_1:
	
	participant_role_0:
	participant_role_1: select role?
	
	deal_id	18689
	
	Since the caller now returns data as json, we have added another parameter - validation passed
	
	validation is simple in concept: There has to be at least one suggestion. We check for
	1) No companies submitted in the POST. This can happen if there is no companies currently associated with the deal AND no company name was specified by the member
	
	2) It may also happen that there are no companies currently associated with the deal. However, the member has sent a blank input. In that case the
	count won't be zero but there won't be any company
	
	Now it becomes complicated. There can be suggestions but it can get rejected due to many reason, like member sending firm that is already
	associated with the deal and with same role, in effect not really a suggestion.
	
	3) Companies that are added using input box are sent with new_entry_nnn = y. It is assumed that the member will not
	type any company currently associated with the deal.
		
	We first scan for these new entries and process. Of course, do check for company name is there or not.
	Then check whether it is one of the current companies or not, since, the current companies are also sent with new_entry_nnn = n. If so, ignore
	***************************
	4) Then we scan again and look for the companies with new_entry_nnn = n. These are current companies, so the names will be there.
	We need to check whether the role or footnote that is sent is different from the role or footnote that is there currently. If both are same
	it is not a suggestion at all.
	
	5) If different, and the current role is not set, it means, the member has suggested a role and we set the role
	
	6) If different, and the current footnote is not set, it means, the member has suggested a footnote and we set the footnote
	
	7) if the current role is set, it may mean, the member has suggested a different role or not suggested a role at all. In any case, we do not set the role
	
	8) Same for footnote
	
	We now use transaction_companies_suggestions with a flag is_correction=y
	************/
	public function front_submit_participants($deal_id,$mem_id,$data_arr,&$validation_passed,&$msg){
		
		$db = new db();
		/******
		basic validation: check if any firm is there in the data or not
		******/
		$company_count = count($data_arr['companies']);
		if(0 == $company_count){
			$validation_passed = false;
			$msg = "At least one company suggestion has to be sent";
			return true;;
		}
		
		/*************************
		basic validation: at least one firm name has to be there in the input
		***************************/
		$has_company = false;
		for($company_i=0;$company_i<$company_count;$company_i++){
			if($data_arr['companies'][$company_i]!=""){
				$has_company = true;
				//there is a company so break
				break;
			}
		}
		if(!$has_company){
			$validation_passed = false;
			$msg = "At least one company suggestion has to be sent";
			return true;
		}
		
		/****************
		We first build some lookup array
		company_name to company_id
		company_name to role_id
		company_name to footnote
		for the current companies for this deal
		***********************/
		require_once("classes/class.transaction_company.php");
		$trans_comp = new transaction_company();
		
		$deal_current_companies = NULL;
		$deal_current_companies_count = 0;
		
		$arr_current_company_name_vs_id = array();
		$arr_current_company_name_vs_role_id = array();
		$arr_current_company_name_vs_footnote = array();
		
		$ok = $trans_comp->get_deal_participants_detailed($deal_id,$deal_current_companies);
		if(!$ok){
			return false;
		}
		$deal_current_companies_count = count($deal_current_companies);
		for($i=0;$i<$deal_current_companies_count;$i++){
			$arr_current_company_name_vs_id[$deal_current_companies[$i]['company_name']] = $deal_current_companies[$i]['company_id'];
			$arr_current_company_name_vs_role_id[$deal_current_companies[$i]['company_name']] = $deal_current_companies[$i]['role_id'];
			$arr_current_company_name_vs_footnote[$deal_current_companies[$i]['company_name']] = $deal_current_companies[$i]['footnote'];
		}
		
		/*************************************************
		Now we loop through the companies again.
		Currently associated firms are sent with new_entry_nnn = n
		
		Companies that are added using input box are sent with new_entry_nnn = y. It is assumed that the member will not
		type any company currently associated with the deal.
		
		We first scan for these new entries and process. Of course, do check for company name is there nor not.
		Then check whether it is one of the current companies or not, since, the current companies are also sent with new_entry_nnn = n. If so, ignore
		*****************************************/
		require_once("classes/class.company.php");
		$comp = new company();
		require_once("classes/class.transaction_company.php");
		$trans_comp = new transaction_company();
		
		
		$q = "";
		/************
		We now use date and time to differentiate between two submissions by same user on same deal on same date
		**************/
		$report_date = date("Y-m-d H:i:s");
		
		/******************
		sng:24/apr/2012
		for the new entries, since the user is typing the names, we need to ignore the duplicates.
		We do that by keeping a lookup array for the new entries
		********************/
		$arr_new_entry_suggested = array();
		
		for($company_i=0;$company_i<$company_count;$company_i++){
			if($data_arr['companies'][$company_i]!=""){
				if($data_arr['new_entry_'.$company_i] == 'n'){
					/*********
					The user did not sent this record by clicking the Add More button. Those record has new_entry == y
					we skip the records that are not sent as part of new addition by the user
					**********/
					continue;
				}else{
					/************
					Since the name of the currently associated companies are shown, the user will not use
					the Add More button to specify the same company. However, we still check
					****************/
					if(array_key_exists($data_arr['companies'][$company_i],$arr_current_company_name_vs_id)){
						//ignore
						continue;
					}
					/***********
					a new company suggestion
					We get the role id and the footnote.
					Note: we assume that the front end will remove any default text from the footnote text box
					
					sng:24/apr/2012
					But before that, we do another check, the duplicate when typing new company
					*******************/
					
					if(array_search($data_arr['companies'][$company_i],$arr_new_entry_suggested)!==false){
						/****************
						already processed, ignore
						******************/
						continue;
					}else{
						/******************
						add to look up array for future
						*****************/
						$arr_new_entry_suggested[] = $data_arr['companies'][$company_i];
					}
				
					$company_role_id = 0;
					if(isset($data_arr['participant_role_'.$company_i])&&($data_arr['participant_role_'.$company_i]!="")){
						$company_role_id = $data_arr['participant_role_'.$company_i];
					}
					$company_footnote = "";
					if(isset($data_arr['participant_note_'.$company_i])&&($data_arr['participant_note_'.$company_i]!="")){
						$company_footnote = $data_arr['participant_note_'.$company_i];
					}
					$company_added = false;
					$suggestion_status = "suggested";
					/************
					we keep this as 'suggested' for now. If we manage to add the company as a participant
					then we set this to 'added'
					**********/
					/******************
					Here we try to get the id of the company , creating the firm if needed and add it to the deal as partner
					****/
					$company_name = $data_arr['companies'][$company_i];
					$company_found = false;
					$company_id = 0;
					$ok = company_id_from_name($company_name,'company',$company_id,$company_found);
					if(!$ok){
						/******
						we have not changed anything yet, so we return false, that is, abort
						*****/
						return false;
					}
					if(!$company_found){
						//create it
						$ok = $comp->front_quick_create_company_blf($mem_id,$company_name,'company',$company_id);
						if(!$ok){
							/******
							we have not changed anything yet, so we return false
							*****/
							return false;
						}
					}else{
						/*********
						company found, that means we have the company id. So we just add this company as participant
						************/
					}
					$record_arr = array('company_name'=>$company_name,'company_id'=>$company_id,'role_id'=>$company_role_id,'footnote'=>$company_footnote);
					$add_company_validation_passed = false;
					$err_arr = array();
					$ok = $trans_comp->front_add_participant_for_deal($deal_id,$record_arr,$add_company_validation_passed,$err_arr);
					
					if($ok && $add_company_validation_passed){
						$suggestion_status = "added";
					}
					/********************************
					Now we create the query fragment to add this as a suggestion
					**********************************/
					$q.=",('".$deal_id."','".$mem_id."','".$report_date."','".mysql_real_escape_string($company_name)."','".$company_role_id."','".mysql_real_escape_string($company_footnote)."','".$suggestion_status."','y')";
					/******************************************************************/
				}
			}else{
				/**********
				blank company name sent, ignore
				*********/
			}
		}
		
		/**************************************************************
		Now we scan again and look for the companies with new_entry_nnn = n. These are current companies, and in the front end
		the user can only change the role OR the footnote. Since the company name cannot be changed, the company name is sent as it is
		and we will find it in our lookup array.
		We need to check if the role that is sent OR the footnote is different or not. If both matches what is there already, it is not
		a suggestion at all and we ignore it.
		Anything that is not set is updated else the suggestion is just stored
		/**********************************************/
		
		for($company_i=0;$company_i<$company_count;$company_i++){
			if($data_arr['companies'][$company_i]==""){
				/********
				company name not sent, ignore
				********/
				continue;
			}
			if($data_arr['new_entry_'.$company_i] == 'y'){
				/*************
				this is a new entry (the user clicked Add Company button to get this. This is not one of the current entries. Ignore
				****************/
				continue;
			}
			//so we have an old entry.
			$company_name = $data_arr['companies'][$company_i];
			$company_role_id = 0;
			$company_footnote = "";
			
			if(isset($data_arr['participant_role_'.$company_i])&&($data_arr['participant_role_'.$company_i]!="")){
				$company_role_id = $data_arr['participant_role_'.$company_i];
			}
					
			if(isset($data_arr['participant_note_'.$company_i])&&($data_arr['participant_note_'.$company_i]!="")){
				$company_footnote = $data_arr['participant_note_'.$company_i];
			}
			
			$existing_id_of_this_company = $arr_current_company_name_vs_id[$company_name];
			$existing_role_id_of_this_company = $arr_current_company_name_vs_role_id[$company_name];
			$existing_footnote_of_this_company = $arr_current_company_name_vs_footnote[$company_name];
			
			/*********************************************************************
			so we have the suggested role id, suggested footnote and the existing role id and the 
			existing footnote for this company.
			
			There are two items for which we can make a change - the role id and the footnote.
			We maintain a variable - changes_made - so that we can store the status of the suggestion.
			
			We first check for role id, then footnote.
			*************************/
			$changes_made = "";
			
			if(0 == $company_role_id){
				/************
				no role suggested for this company
				so nothing to change
				****************/
			}else{
				/**********************
				role suggested. Is it same as the original role? If so, nothing new is
				suggested here.
				**********************/
				if($company_role_id == $existing_role_id_of_this_company){
					/******************
					nothing new suggested
					*********************/
				}else{
					/*****************************
					role is suggested and it differs from the existing role. It means either
					the existing role is not set OR the existing role is set and it is different.
					
					If the existing role is NOT set, only then we update, else we just make note of the suggestion.
					*******************************/
					if(0 == $existing_role_id_of_this_company){
						
						
						$ok = $trans_comp->front_update_participant_role_for_deal($deal_id,$existing_id_of_this_company,$company_role_id);
						if($ok){
							$changes_made.=",role set";
						}else{
							$changes_made.=",role suggested";
						}
						
					}else{
						$changes_made.=",role suggested";
					}
				}
			}
			/*************************
			Now the footnote
			*********/
			if("" == $company_footnote){
				/************
				no footnote suggested for this company
				so nothing to change
				****************/
			}else{
				/**********************
				footnote suggested. Is it same as the original footnote? If so, nothing new is
				suggested here.
				**********************/
				if($company_footnote == $existing_footnote_of_this_company){
					/******************
					nothing new suggested
					*********************/
				}else{
					/*****************************
					footnote is suggested and it differs from the existing footnote. It means either
					the existing footnote is not set OR the existing footnote is set and it is different.
					
					If the existing footnote is NOT set, only then we update, else we just make note of the suggestion.
					*******************************/
					if("" == $existing_footnote_of_this_company){
						$ok = $trans_comp->front_update_participant_footnote_for_deal($deal_id,$existing_id_of_this_company,$company_footnote);
						if($ok){
							$changes_made.=",footnote set";
						}else{
							$changes_made.=",footnote suggested";
						}
						
					}else{
						$changes_made.=",footnote suggested";
					}
				}
			}
			/*********************
			Now here we check whether we made any change or not
			*********/
			if($changes_made != ""){
				//get rid of the first ','
				$changes_made = substr($changes_made,1);
				$q.=",('".$deal_id."','".$mem_id."','".$report_date."','".mysql_real_escape_string($company_name)."','".$company_role_id."','".mysql_real_escape_string($company_footnote)."','".$changes_made."','y')";
			}
			/*****************************************************/
		}
		/**************************************************/
		if($q == ""){
			$validation_passed = false;
			$msg = "There is no effective suggestion";
			return true;
		}
		/************************************************/
		//get rid of the first ,
		$q = substr($q,1);
		//we already checked that at least one firm is there, so the query is not blank
		$q = "insert into ".TP."transaction_companies_suggestions (deal_id,suggested_by,date_suggested,company_name,role_id,footnote,status_note,is_correction) values ".$q;
		$ok = $db->mod_query($q);
		if(!$ok){
			return false;
		}else{
			$msg = "Your suggestion has been stored";
			$validation_passed = true;
			$this->mark_member_as_verifier($deal_id,$mem_id);
			return true;
		}
	}
	
	/**********************************************************************
	sng:13/apr/2012
	We have changed the front end a bit.
	
	The array for bank is like:
	
	partner_role_1	
	partner_role_2	
	partner_role_3	3
	partner_role_4	4
	
	new_entry_0:n
	new_entry_1:n
	new_entry_2:n
	new_entry_3:y
		
	firms[]	Goldman Sachs
	firms[]	JPMorgan
	firms[]	UBS
	firms[]	Deutsche Bank [new suggestion]
	
	deal_id	18663
	partner_type	bank
	
	ditto for law firms, except
	partner_type	law firm
	
	Since the caller now returns data as json, we have added another parameter - validation passed
	
	validation is simple in concept: There has to be at least one suggestion. We check for
	1) No firms submitted in the POST. This can happen if there is no firms currently associated with the deal AND no firm name was specified by the member
	2) It may also happen that there are no firms currently associated with the deal. However, the member has sent a blank input. In that case the
	count won't be zero but there won't be any bank
	
	Now it becomes complicated. There can be suggestions but it can get rejected due to many reason, like member sending firm that is already
	associated with the deal and with same role, in effect not really a suggestion.
	
	3) Firms that are added using input box are sent with new_entry_nnn = y. It is assumed that the member will not
	type any firm currently associated with the deal.
		
	We first scan for these new entries and process. Of course, do check for firm name is there nor not.
	Then check whether it is one of the current firms or not, since, the current firms are also sent with new_entry_nnn = n. If so, ignore
	
	4) Then we scan again and look for the firms with new_entry_nnn = n. These are current firms, so the names will be there.
	We need to check whether the role that is sent is different from the role that is there currently. If so, it is not a suggestion at all.
	
	5) If different, and the current role is not set, it means, the member has suggested a role and we set the role
	
	6) if the current role is set, it may mean, the member has suggested a different role or not suggested a role at all. In any case, we do not set the role
	
	sng:6/apr/2012
	We now use transaction_partners_suggestions with a flag is_correction=y
	************/
	public function front_submit_partners($deal_id,$firm_type,$mem_id,$data_arr,&$validation_passed,&$msg){
		$db = new db();
		
		/******
		basic validation: check if any firm is there in the data or not
		******/
		$firm_count = count($data_arr['firms']);
		if(0 == $firm_count){
			$validation_passed = false;
			$msg = "At least one firm suggestion has to be sent";
			return true;;
		}
		/*************************
		basic validation: at least one firm name has to be there in the input
		***************************/
		$has_firm = false;
		for($firm_i=0;$firm_i<$firm_count;$firm_i++){
			if($data_arr['firms'][$firm_i]!=""){
				$has_firm = true;
				//there is a firm so break
				break;
			}
		}
		if(!$has_firm){
			$validation_passed = false;
			$msg = "At least one firm suggestion has to be sent";
			return true;
		}
		
		
		
		/****************
		We first build some lookup array
		firm_name to firm_id
		firm_name to role_id
		for the current firms for this deal
		***********************/
		require_once("classes/class.transaction_partner.php");
		$trans_partner = new transaction_partner();
		
		$deal_current_partners = NULL;
		$deal_current_partners_count = 0;
		
		$arr_current_partner_name_vs_id = array();
		$arr_current_partner_name_vs_role_id = array();
		
		$ok = $trans_partner->get_all_partners_data_by_type($deal_id,$firm_type,$deal_current_partners,$deal_current_partners_count);
		if(!$ok){
			return false;
		}
		for($i=0;$i<$deal_current_partners_count;$i++){
			$arr_current_partner_name_vs_id[$deal_current_partners[$i]['company_name']] = $deal_current_partners[$i]['partner_id'];
			$arr_current_partner_name_vs_role_id[$deal_current_partners[$i]['company_name']] = $deal_current_partners[$i]['role_id'];
		}
		/*************************************************
		Now we loop through the firms again.
		Currently associated firms are sent with new_entry_nnn = n
		
		Firms that are added using input box are sent with new_entry_nnn = y. It is assumed that the member will not
		type any firm currently associated with the deal.
		
		We first scan for these new entries and process. Of course, do check for firm name is there nor not.
		Then check whether it is one of the current firms or not, since, the current firms are also sent with new_entry_nnn = n. If so, ignore
		*****************************************/
		require_once("classes/class.company.php");
		$comp = new company();
		require_once("classes/class.transaction.php");
		$trans = new transaction();
		
		$add_firm_validation_passed = false;
		$err_arr = array();
		
		
		$q = "";
		/************
		sng:16/apr/2012
		We now use date and time to differentiate between two submissions by same user on same deal on same date
		**************/
		$report_date = date("Y-m-d H:i:s");
		
		/******************
		sng:24/apr/2012
		for the new entries, since the user is typing the names, we need to ignore the duplicates.
		We do that by keeping a lookup array for the new entries
		********************/
		$arr_new_entry_suggested = array();
		
		for($firm_i=0;$firm_i<$firm_count;$firm_i++){
			if($data_arr['firms'][$firm_i]!=""){
				if($data_arr['new_entry_'.$firm_i] == 'n'){
					continue;
				}else{
					if(array_key_exists($data_arr['firms'][$firm_i],$arr_current_partner_name_vs_id)){
						//ignore
						continue;
					}
					//a new suggestion
					/**************
					sng:24/apr/2012
					We ignore duplicate suggestion of new firms
					***************/
					if(array_search($data_arr['firms'][$firm_i],$arr_new_entry_suggested)!==false){
						/****************
						already processed, ignore
						******************/
						continue;
					}else{
						/******************
						add to look up array for future
						*****************/
						$arr_new_entry_suggested[] = $data_arr['firms'][$firm_i];
					}
					/******************************/
					$firm_role_id = 0;
					if(isset($data_arr['partner_role_'.$firm_i])&&($data_arr['partner_role_'.$firm_i]!="")){
						$firm_role_id = $data_arr['partner_role_'.$firm_i];
					}
					
					$firm_added = false;
					$suggestion_status = "suggested";
					//if we manage to add the firm as partner, we change this to 'added'
					/******************
					Here we try to get the id of the firm , creating the firm if needed and add it to the deal as partner
					****/
					$firm_name = $data_arr['firms'][$firm_i];
					$firm_found = false;
					$firm_id = 0;
					$ok = company_id_from_name($firm_name,$firm_type,$firm_id,$firm_found);
					if(!$ok){
						/******
						we have not changed anything yet, so we return false
						*****/
						return false;
					}
					if(!$firm_found){
						//create it
						$ok = $comp->front_quick_create_company_blf($mem_id,$firm_name,$firm_type,$firm_id);
						if(!$ok){
							/******
							we have not changed anything yet, so we return false
							*****/
							return false;
						}
						$record_arr = array();
						$record_arr['firm_name'] = $firm_name;
						$record_arr['partner_id'] = $firm_id;
						$record_arr['transaction_id'] = $deal_id;
						$record_arr['role_id'] = $firm_role_id;
						/***********
						sng:23/apr/2012
						add_partner() is in transaction, not here
						******************/
						$ok = $trans->add_partner($record_arr,$firm_type,$add_firm_validation_passed,$err_arr);
						if($ok && $add_firm_validation_passed){
							$suggestion_status = "added";
						}
					}else{
						//firm found
						$record_arr = array();
						$record_arr['firm_name'] = $firm_name;
						$record_arr['partner_id'] = $firm_id;
						$record_arr['transaction_id'] = $deal_id;
						$record_arr['role_id'] = $firm_role_id;
						$ok = $trans->add_partner($record_arr,$firm_type,$add_firm_validation_passed,$err_arr);
						if($ok && $add_firm_validation_passed){
							$suggestion_status = "added";
						}
					}
					
					$q.=",('".$deal_id."','".$mem_id."','".$report_date."','".mysql_real_escape_string($data_arr['firms'][$firm_i])."','".$firm_type."','".$firm_role_id."','".$suggestion_status."','y')";
					/*********************************/
				}
			}
		}
		/**********************
		Now we scan again and look for the firms with new_entry_nnn = n. These are current firms, so the names will be there.
		We need to check whether the role that is sent is different from the role that is there currently. If so, it is not
		a suggestion at all.
		/**********************************************/
		require_once("classes/class.deal_support.php");
		$deal_support = new deal_support();
		
		for($firm_i=0;$firm_i<$firm_count;$firm_i++){
			if($data_arr['firms'][$firm_i]==""){
				continue;
			}
			if($data_arr['new_entry_'.$firm_i] == 'y'){
				continue;
			}
			//so we have an old entry.
			$firm_name = $data_arr['firms'][$firm_i];
			$firm_role_id = 0;
			if(isset($data_arr['partner_role_'.$firm_i])&&($data_arr['partner_role_'.$firm_i]!="")){
				$firm_role_id = $data_arr['partner_role_'.$firm_i];
			}
			$existing_role_id_of_this_firm = $arr_current_partner_name_vs_role_id[$firm_name];
			if($existing_role_id_of_this_firm == $firm_role_id){
				//ignore
				continue;
			}
			/************************
			This is where things become more interesting. The suggested role is different that what is stored but should we
			blindly change it?
			****************************/
			$suggestion_status = "role suggested";
			if($existing_role_id_of_this_firm == 0){
				/*********
				role not set and we already checked that member suggested role is different, so definitely member has suggested a role
				so try to set the role
				************/
				
				
				$msg = "";
				$record_arr = array();
				$record_arr['role'] = $firm_role_id;
				$record_arr['transaction_id'] = $deal_id;
				$record_arr['partner_id'] = $arr_current_partner_name_vs_id[$firm_name];
				
				$ok = $deal_support->set_deal_partner_role($record_arr,$firm_type,$msg);
				if($ok){
					$suggestion_status = "role set";
				}
			}else{
				/***********
				role is set. So there is no question of changing anything. We also checked that the member suggested role is different. This means
				either different role suggested OR no role suggested. If no role suggeste, ignore
				****************/
				if($firm_role_id == 0){
					continue;
				}
				
			}
			$q.=",('".$deal_id."','".$mem_id."','".$report_date."','".$firm_name."','".$firm_type."','".$firm_role_id."','".$suggestion_status."','y')";
		}
		/**************************************************/
		if($q == ""){
			$validation_passed = false;
			$msg = "There is no effective suggestion";
			return true;
		}
		/************************************************/
		//get rid of the first ,
		$q = substr($q,1);
		//we already checked that at least one firm is there, so the query is not blank
		$q = "insert into ".TP."transaction_partners_suggestions (deal_id,suggested_by,date_suggested,partner_name,partner_type,role_id,status_note,is_correction) values ".$q;
		$ok = $db->mod_query($q);
		if(!$ok){
			return false;
		}else{
			$msg = "Your suggestion has been stored";
			$validation_passed = true;
			$this->mark_member_as_verifier($deal_id,$mem_id);
			return true;
		}
	}
	
	/***********************
	sng:18/apr/2012
	We track the addition of participating companies when a deal is added so that even if new companies are added later
	or roles are changed, we know what was the original submission.
	
	Since this is not addition of participant company via correction, is_correction is n
	
	the data_arr has
	company_name
	role_id
	footnote
	************************/
	public function participant_added_via_deal_submission($deal_id,$member_id,$deal_added_on,$data_arr){
		$db = new db();
		
		$q = "";
		$is_correction = 'n';
		$cnt = count($data_arr);
		if(0 == $cnt){
			//no data specified, return
			return true;
		}
		for($i = 0;$i < $cnt; $i++){
			/******
			do check whether company name given or not
			role and footnote can be blank
			*****/
			if($data_arr[$i]['company_name'] == ""){
				//skip this
				continue;
			}
			$q.=",('".$deal_id."','".$member_id."','".$deal_added_on."','".mysql_real_escape_string($data_arr[$i]['company_name'])."','".$data_arr[$i]['role_id']."','".mysql_real_escape_string($data_arr[$i]['footnote'])."','".$is_correction."')";
		}
		if($q == ""){
			//all data skipped
			return true;
		}
		//get rid of the first ','
		$q = substr($q,1);
		$q = "insert into ".TP."transaction_companies_suggestions (deal_id,suggested_by,date_suggested,company_name,role_id,footnote,is_correction) values ".$q;
		$ok = $db->mod_query($q);
		if(!$ok){
			return false;
		}else{
			return true;
		}
	}
	/***********************
	sng:6/apr/2012
	We track the addition of partners when a deal is added so that even if new partners are added later
	or roles are changed, we know what was the original submission.
	
	Since this is not addition of partners via correction, is_correction is n
	
	the data_arr has
	partner_name
	partner_type
	role_id
	************************/
	public function partners_added_via_deal_submission($deal_id,$member_id,$deal_added_on,$data_arr){
		$db = new db();
		
		$q = "";
		$is_correction = 'n';
		$cnt = count($data_arr);
		if(0 == $cnt){
			//no data specified, return
			return true;
		}
		for($i = 0;$i < $cnt; $i++){
			/******
			do check whether firm name and type given or not
			role can be blank
			*****/
			if(($data_arr[$i]['partner_name'] == "")||($data_arr[$i]['partner_type'] == "")){
				//skip this
				continue;
			}
			$q.=",('".$deal_id."','".$member_id."','".$deal_added_on."','".mysql_real_escape_string($data_arr[$i]['partner_name'])."','".$data_arr[$i]['partner_type']."','".$data_arr[$i]['role_id']."','".$is_correction."')";
		}
		if($q == ""){
			//all data skipped
			return true;
		}
		//get rid of the first ','
		$q = substr($q,1);
		$q = "insert into ".TP."transaction_partners_suggestions (deal_id,suggested_by,date_suggested,partner_name,partner_type,role_id,is_correction) values ".$q;
		$ok = $db->mod_query($q);
		if(!$ok){
			return false;
		}else{
			return true;
		}
	}
	
	/***********************
	sng:30/apr/2012
	We track the addition of note when a deal is added so that even if note is changed later, we will know what was the original submission.
	
	Since this is not addition of note via correction, is_correction is n
	and we do not put any status note
	
	This is called when we change the note transaction::update_note.
	Those function first set the note and then call this function to notify.
	Do not call this directly.
	************************/
	public function note_added_via_deal_submission($deal_id,$member_id,$deal_added_on,$note){
		$db = new db();
		
		$q = "";
		$is_correction = 'n';
		if($note==""){
			//nothing to add
			return true;
		}
		
		$q = "insert into ".TP."transaction_note_suggestions set deal_id='".$deal_id."', suggested_by='".$member_id."', date_suggested='".$deal_added_on."', note='".mysql_real_escape_string($note)."',is_correction='".$is_correction."'";
		
		$ok = $db->mod_query($q);
		/***********
		no need to hang the system if error
		************/
		return true;
	}
	
	/*****************
	sng:2/may/2012
	*********************/
	public function deal_source_added_via_deal_submission($deal_id,$member_id,$deal_added_on,$data_arr){
		$db = new db();
		
		$q = "";
		$is_correction = 'n';
		$cnt = count($data_arr);
		if(0 == $cnt){
			//no data specified, return
			return true;
		}
		for($i = 0;$i < $cnt; $i++){
			/******
			do check whether source url given or not
			*****/
			if($data_arr[$i]['source_url'] == ""){
				//skip this
				continue;
			}
			$q.=",('".$deal_id."','".$member_id."','".$deal_added_on."','".mysql_real_escape_string($data_arr[$i]['source_url'])."','".$is_correction."')";
		}
		if($q == ""){
			//all data skipped
			return true;
		}
		//get rid of the first ','
		$q = substr($q,1);
		$q = "insert into ".TP."transaction_sources_suggestions (deal_id,suggested_by,date_suggested,source_url,is_correction) values ".$q;
		$ok = $db->mod_query($q);
		if(!$ok){
			return false;
		}else{
			return true;
		}
	}
	
	public function front_submit_deal_data($deal_id,$mem_id,$data_arr,&$msg){
	
		$db = new db();
		/****************
		sng:31/aug/2012
		We need to know the type of deal. If it is Debt/Equity, we set in_calculation = 1 if
		completion date is not set in the current data and it is specified here (that is, the deal was in
		'announced' state and now a member is marking it as 'completed'
		
		Basically, we are not interested in Debt/Equity deals which are only 'announced'. How do we know that
		it has only been announced? In the deal submission, we get only the date of announcement.
		See simple deal submission and detailed deal submission process
		**********************/
		$q = "select deal_cat_name,deal_subcat1_name from ".TP."transaction where id='".$deal_id."'";
		$success = $db->select_query($q);
		if(!$success){
			return false;
		}
		if(!$db->has_row()){
			return false;
		}
		$row = $db->get_row();
		
		$deal_cat = $row['deal_cat_name'];
		$deal_sub_cat = strtolower($row['deal_subcat1_name']);
		/*****************************************************************/
		
		$q = "";
		/****************************************
		sng:1/sep/2012
		This may not be needed. It was a hack for M&A deal to allow members to mark it as completed
		Now we will use custom list
		
		if(isset($data_arr['deal_subcat1_name'])&&($data_arr['deal_subcat1_name']!='')){
			$q.=",deal_subcat1_name='".mysql_real_escape_string($data_arr['deal_subcat1_name'])."'";
		}
		***************************/
		
		if(isset($data_arr['date_rumour'])&&($data_arr['date_rumour']!='')){
			$q.=",date_rumour='".mysql_real_escape_string($data_arr['date_rumour'])."'";
		}
		
		if(isset($data_arr['date_announced'])&&($data_arr['date_announced']!='')){
			$q.=",date_announced='".mysql_real_escape_string($data_arr['date_announced'])."'";
		}
		
		if(isset($data_arr['date_closed'])&&($data_arr['date_closed']!='')){
			$q.=",date_closed='".mysql_real_escape_string($data_arr['date_closed'])."'";
		}
		
		if(isset($data_arr['payment_type'])&&($data_arr['payment_type']!='')){
			$q.=",payment_type='".mysql_real_escape_string($data_arr['payment_type'])."'";
		}
		
		if(isset($data_arr['equity_payment_percent'])&&($data_arr['equity_payment_percent']!='')){
			$q.=",equity_payment_percent='".(float)$data_arr['equity_payment_percent']."'";
		}
		
		if(isset($data_arr['target_listed_in_stock_exchange'])&&($data_arr['target_listed_in_stock_exchange']=='y')){
			$q.=",target_listed_in_stock_exchange='y'";
		}else{
			//not sent, treat as n OR set as n
			$q.=",target_listed_in_stock_exchange='n'";
		}
		
		if(isset($data_arr['target_stock_exchange_name'])&&($data_arr['target_stock_exchange_name']!='')){
			$q.=",target_stock_exchange_name='".mysql_real_escape_string($data_arr['target_stock_exchange_name'])."'";
		}
		
		if(isset($data_arr['takeover_id'])&&($data_arr['takeover_id']!='')){
			$q.=",takeover_id='".(int)$data_arr['takeover_id']."'";
		}
		
		if(isset($data_arr['termination_fee_million'])&&($data_arr['termination_fee_million']!='')){
			$q.=",termination_fee_million='".(float)$data_arr['termination_fee_million']."'";
		}
		
		if(isset($data_arr['end_date_termination_fee'])&&($data_arr['end_date_termination_fee']!='')){
			$q.=",end_date_termination_fee='".mysql_real_escape_string($data_arr['end_date_termination_fee'])."'";
		}
		
		if(isset($data_arr['fee_percent_to_sellside_advisor'])&&($data_arr['fee_percent_to_sellside_advisor']!='')){
			$q.=",fee_percent_to_sellside_advisor='".(float)$data_arr['fee_percent_to_sellside_advisor']."'";
		}
		
		if(isset($data_arr['fee_percent_to_buyside_advisor'])&&($data_arr['fee_percent_to_buyside_advisor']!='')){
			$q.=",fee_percent_to_buyside_advisor='".(float)$data_arr['fee_percent_to_buyside_advisor']."'";
		}
		
		if(isset($data_arr['revenue_ltm_million'])&&($data_arr['revenue_ltm_million']!='')){
			$q.=",revenue_ltm_million='".(float)$data_arr['revenue_ltm_million']."'";
		}
		
		if(isset($data_arr['revenue_mry_million'])&&($data_arr['revenue_mry_million']!='')){
			$q.=",revenue_mry_million='".(float)$data_arr['revenue_mry_million']."'";
		}
		
		if(isset($data_arr['revenue_ny_million'])&&($data_arr['revenue_ny_million']!='')){
			$q.=",revenue_ny_million='".(float)$data_arr['revenue_ny_million']."'";
		}
		
		if(isset($data_arr['ebitda_ltm_million'])&&($data_arr['ebitda_ltm_million']!='')){
			$q.=",ebitda_ltm_million='".(float)$data_arr['ebitda_ltm_million']."'";
		}
		
		if(isset($data_arr['ebitda_mry_million'])&&($data_arr['ebitda_mry_million']!='')){
			$q.=",ebitda_mry_million='".(float)$data_arr['ebitda_mry_million']."'";
		}
		
		if(isset($data_arr['ebitda_ny_million'])&&($data_arr['ebitda_ny_million']!='')){
			$q.=",ebitda_ny_million='".(float)$data_arr['ebitda_ny_million']."'";
		}
		
		if(isset($data_arr['net_income_ltm_million'])&&($data_arr['net_income_ltm_million']!='')){
			$q.=",net_income_ltm_million='".(float)$data_arr['net_income_ltm_million']."'";
		}
		
		if(isset($data_arr['net_income_mry_million'])&&($data_arr['net_income_mry_million']!='')){
			$q.=",net_income_mry_million='".(float)$data_arr['net_income_mry_million']."'";
		}
		
		if(isset($data_arr['net_income_ny_million'])&&($data_arr['net_income_ny_million']!='')){
			$q.=",net_income_ny_million='".(float)$data_arr['net_income_ny_million']."'";
		}
		
		if(isset($data_arr['date_year_end_of_recent_financial_year'])&&($data_arr['date_year_end_of_recent_financial_year']!='')){
			$q.=",date_year_end_of_recent_financial_year='".mysql_real_escape_string($data_arr['date_year_end_of_recent_financial_year'])."'";
		}
		/**************************************************************/
		if(isset($data_arr['years_to_maturity'])&&($data_arr['years_to_maturity']!='')){
			$q.=",years_to_maturity='".mysql_real_escape_string($data_arr['years_to_maturity'])."'";
		}
		
		if(isset($data_arr['maturity_date'])&&($data_arr['maturity_date']!='')){
			$q.=",maturity_date='".mysql_real_escape_string($data_arr['maturity_date'])."'";
		}
		
		if(isset($data_arr['coupon'])&&($data_arr['coupon']!='')){
			$q.=",coupon='".mysql_real_escape_string($data_arr['coupon'])."'";
		}
		
		if(isset($data_arr['current_rating'])&&($data_arr['current_rating']!='')){
			$q.=",current_rating='".mysql_real_escape_string($data_arr['current_rating'])."'";
		}
		
		if(isset($data_arr['format'])&&($data_arr['format']!='')){
			$q.=",format='".mysql_real_escape_string($data_arr['format'])."'";
		}
		
		if(isset($data_arr['guarantor'])&&($data_arr['guarantor']!='')){
			$q.=",guarantor='".mysql_real_escape_string($data_arr['guarantor'])."'";
		}
		
		if(isset($data_arr['collateral'])&&($data_arr['collateral']!='')){
			$q.=",collateral='".mysql_real_escape_string($data_arr['collateral'])."'";
		}
		
		if(isset($data_arr['seniority'])&&($data_arr['seniority']!='')){
			$q.=",seniority='".mysql_real_escape_string($data_arr['seniority'])."'";
		}
		
		if(isset($data_arr['year_to_call'])&&($data_arr['year_to_call']!='')){
			$q.=",year_to_call='".mysql_real_escape_string($data_arr['year_to_call'])."'";
		}
		
		if(isset($data_arr['call_date'])&&($data_arr['call_date']!='')){
			$q.=",call_date='".mysql_real_escape_string($data_arr['call_date'])."'";
		}
		
		if(isset($data_arr['redemption_price'])&&($data_arr['redemption_price']!='')){
			$q.=",redemption_price='".mysql_real_escape_string($data_arr['redemption_price'])."'";
		}
		
		if(isset($data_arr['base_fee'])&&($data_arr['base_fee']!='')){
			$q.=",base_fee='".(float)$data_arr['base_fee']."'";
		}
		
		if(isset($data_arr['margin_including_ratchet'])&&($data_arr['margin_including_ratchet']!='')){
			$q.=",margin_including_ratchet='".mysql_real_escape_string($data_arr['margin_including_ratchet'])."'";
		}
		
		if(isset($data_arr['fee_upfront'])&&($data_arr['fee_upfront']!='')){
			$q.=",fee_upfront='".(float)$data_arr['fee_upfront']."'";
		}
		
		if(isset($data_arr['fee_commitment'])&&($data_arr['fee_commitment']!='')){
			$q.=",fee_commitment='".(float)$data_arr['fee_commitment']."'";
		}
		
		if(isset($data_arr['fee_utilisation'])&&($data_arr['fee_utilisation']!='')){
			$q.=",fee_utilisation='".(float)$data_arr['fee_utilisation']."'";
		}
		
		if(isset($data_arr['fee_arrangement'])&&($data_arr['fee_arrangement']!='')){
			$q.=",fee_arrangement='".(float)$data_arr['fee_arrangement']."'";
		}
		/*************************************************************************************/
		
		if(isset($data_arr['reference_price'])&&($data_arr['reference_price']!='')){
			$q.=",reference_price='".(float)$data_arr['reference_price']."'";
		}
		
		if(isset($data_arr['conversion_price'])&&($data_arr['conversion_price']!='')){
			$q.=",conversion_price='".(float)$data_arr['conversion_price']."'";
		}
		
		if(isset($data_arr['currency_reference_price'])&&($data_arr['currency_reference_price']!='')){
			$q.=",currency_reference_price='".mysql_real_escape_string($data_arr['currency_reference_price'])."'";
		}
		
		if(isset($data_arr['conversion_premia_percent'])&&($data_arr['conversion_premia_percent']!='')){
			$q.=",conversion_premia_percent='".(float)$data_arr['conversion_premia_percent']."'";
		}
		
		if(isset($data_arr['num_shares_underlying_million'])&&($data_arr['num_shares_underlying_million']!='')){
			$q.=",num_shares_underlying_million='".(float)$data_arr['num_shares_underlying_million']."'";
		}
		
		if(isset($data_arr['curr_num_shares_outstanding_million'])&&($data_arr['curr_num_shares_outstanding_million']!='')){
			$q.=",curr_num_shares_outstanding_million='".(float)$data_arr['curr_num_shares_outstanding_million']."'";
		}
		
		if(isset($data_arr['avg_daily_trading_vol_million'])&&($data_arr['avg_daily_trading_vol_million']!='')){
			$q.=",avg_daily_trading_vol_million='".(float)$data_arr['avg_daily_trading_vol_million']."'";
		}
		
		if(isset($data_arr['shares_underlying_vs_adtv_ratio'])&&($data_arr['shares_underlying_vs_adtv_ratio']!='')){
			$q.=",shares_underlying_vs_adtv_ratio='".(float)$data_arr['shares_underlying_vs_adtv_ratio']."'";
		}
		
		if(isset($data_arr['dividend_protection'])&&($data_arr['dividend_protection']=='y')){
			$q.=",dividend_protection='y'";
		}else{
			//not sent, treat as n OR set as n
			$q.=",dividend_protection='n'";
		}
		/****************************************************************************/
		
		if(isset($data_arr['offer_price'])&&($data_arr['offer_price']!='')){
			$q.=",offer_price='".(float)$data_arr['offer_price']."'";
		}
		
		if(isset($data_arr['num_primary_shares_million'])&&($data_arr['num_primary_shares_million']!='')){
			$q.=",num_primary_shares_million='".(float)$data_arr['num_primary_shares_million']."'";
		}
		
		if(isset($data_arr['num_secondary_shares_million'])&&($data_arr['num_secondary_shares_million']!='')){
			$q.=",num_secondary_shares_million='".(float)$data_arr['num_secondary_shares_million']."'";
		}
		
		if(isset($data_arr['num_shares_outstanding_after_deal_million'])&&($data_arr['num_shares_outstanding_after_deal_million']!='')){
			$q.=",num_shares_outstanding_after_deal_million='".(float)$data_arr['num_shares_outstanding_after_deal_million']."'";
		}
		
		if(isset($data_arr['free_float_percent'])&&($data_arr['free_float_percent']!='')){
			$q.=",free_float_percent='".(float)$data_arr['free_float_percent']."'";
		}
		
		if(isset($data_arr['greenshoe_included'])&&($data_arr['greenshoe_included']=='y')){
			$q.=",greenshoe_included='y'";
		}else{
			//not sent, treat as n OR set as n
			$q.=",greenshoe_included='n'";
		}
		
		if(isset($data_arr['ipo_stock_exchange'])&&($data_arr['ipo_stock_exchange']!='')){
			$q.=",ipo_stock_exchange='".mysql_real_escape_string($data_arr['ipo_stock_exchange'])."'";
		}
		
		if(isset($data_arr['price_at_end_of_first_day'])&&($data_arr['price_at_end_of_first_day']!='')){
			$q.=",price_at_end_of_first_day='".(float)$data_arr['price_at_end_of_first_day']."'";
		}
		
		if(isset($data_arr['date_first_trading'])&&($data_arr['date_first_trading']!='')){
			$q.=",date_first_trading='".mysql_real_escape_string($data_arr['date_first_trading'])."'";
		}
		
		if(isset($data_arr['1_day_price_change'])&&($data_arr['1_day_price_change']!='')){
			$q.=",1_day_price_change='".(float)$data_arr['1_day_price_change']."'";
		}
		
		if(isset($data_arr['incentive_fee'])&&($data_arr['incentive_fee']!='')){
			$q.=",incentive_fee='".(float)$data_arr['incentive_fee']."'";
		}
		/*****************************************************************************************/
		
		if(isset($data_arr['price_per_share_before_deal_announcement'])&&($data_arr['price_per_share_before_deal_announcement']!='')){
			$q.=",price_per_share_before_deal_announcement='".(float)$data_arr['price_per_share_before_deal_announcement']."'";
		}
		
		if(isset($data_arr['date_price_per_share_before_deal_announcement'])&&($data_arr['date_price_per_share_before_deal_announcement']!='')){
			$q.=",date_price_per_share_before_deal_announcement='".mysql_real_escape_string($data_arr['date_price_per_share_before_deal_announcement'])."'";
		}
		
		if(isset($data_arr['discount_to_last'])&&($data_arr['discount_to_last']!='')){
			$q.=",discount_to_last='".(float)$data_arr['discount_to_last']."'";
		}
		/*****************************************************************************************/
		
		if(isset($data_arr['date_ex_rights'])&&($data_arr['date_ex_rights']!='')){
			$q.=",date_ex_rights='".mysql_real_escape_string($data_arr['date_ex_rights'])."'";
		}
		
		if(isset($data_arr['subscription_ratio'])&&($data_arr['subscription_ratio']!='')){
			$q.=",subscription_ratio='".mysql_real_escape_string($data_arr['subscription_ratio'])."'";
		}
		
		if(isset($data_arr['terp'])&&($data_arr['terp']!='')){
			$q.=",terp='".(float)$data_arr['terp']."'";
		}
		
		if(isset($data_arr['discount_to_terp'])&&($data_arr['discount_to_terp']!='')){
			$q.=",discount_to_terp='".(float)$data_arr['discount_to_terp']."'";
		}
		
		if(isset($data_arr['subscription_rate_percent'])&&($data_arr['subscription_rate_percent']!='')){
			$q.=",subscription_rate_percent='".(float)$data_arr['subscription_rate_percent']."'";
		}
		
		if(isset($data_arr['rump_placement'])&&($data_arr['rump_placement']=='y')){
			$q.=",rump_placement='y'";
		}else{
			//not sent, treat as n OR set as n
			$q.=",rump_placement='n'";
		}
		
		if(isset($data_arr['num_shares_sold_in_rump_million'])&&($data_arr['num_shares_sold_in_rump_million']!='')){
			$q.=",num_shares_sold_in_rump_million='".(float)$data_arr['num_shares_sold_in_rump_million']."'";
		}
		
		if(isset($data_arr['price_per_share_in_rump'])&&($data_arr['price_per_share_in_rump']!='')){
			$q.=",price_per_share_in_rump='".(float)$data_arr['price_per_share_in_rump']."'";
		}
		/************************************************************************/
		
		
		if($q == ""){
			$msg = "Please specify at least one suggestion";
			return true;
		}
		
		/*************************
		sng:31/aug/2012
		If debt/equity and if comlpletion date is given, see if date_closed is set for the deal or not
		*********************/
		$deal_cat = strtolower($deal_cat);
		if(("debt"==$deal_cat)||("equity"==$deal_cat)){
			if(isset($data_arr['date_closed'])&&($data_arr['date_closed']!='')){
				$date_q = "select date_closed from ".TP."transaction_extra_detail where transaction_id='".$deal_id."'";
				$success = $db->select_query($date_q);
				if(!$success){
					return false;
				}
				if(!$db->has_row()){
					return false;
				}
				$row = $db->get_row();
				
				if((""==$row['date_closed'])||("0000-00-00"==$row['date_closed'])){
					//date closed not set, set it
					$updt_q = "update ".TP."transaction_extra_detail set date_closed='".mysql_real_escape_string($data_arr['date_closed'])."' where transaction_id='".$deal_id."'";
					$ok = $db->mod_query($updt_q);
					if($ok){
						/**********
						sng:11/sep/2012
						Since we are setting the closed date, this will now become date of the deal
						************/
						$updt_q = "update ".TP."transaction set date_of_deal='".mysql_real_escape_string($data_arr['date_closed'])."',in_calculation='1' where id='".$deal_id."'";
						$db->mod_query($updt_q);
					}else{
						//we don't mind if this fails
					}
				}else{
					//date closed is set for the deal, do nothing
				}
			}else{
				//no suggestion, set nothing
			}
		}else{
			/****************
			sng:1/sep/2012
			M&A deal. If the current subcat is already 'completed' then ignore any suggestion. Completed deals cannot change to Pending
			*********************/
			if($deal_sub_cat=="completed"){
			}else{
				/************
				Now it becomes interesting.
				Did the user sent any deal_completion_status
				
				sng:12/sep/2012
				It could also happen that closing date has been sent but completion status is not 'completed'
				***********/
				if(isset($data_arr['date_closed'])&&($data_arr['date_closed']!='0000-00-00')){
					if(!isset($data_arr['deal_completion_status'])||($data_arr['deal_completion_status']!='completed')){
						$msg = "Non completed deal cannot have closing date";
						return true;
					}
				}
				
				if(isset($data_arr['deal_completion_status'])&&($data_arr['deal_completion_status']!='')){
					$stat_update_q = "";
					if($data_arr['deal_completion_status']=="pending"){
						$stat_update_q = "deal_subcat1_name='Pending',in_calculation='1'";
					}
					if($data_arr['deal_completion_status']=="completed"){
						
						/***************
						sng:5/sep/2012
						So, I am setting the deal as completed. In that case, what about the date of completion? What about the date of deal?
						sng:11/sep/2012
						We do not have concept of error here, only message and return. Ok, let us check for completion date. If not set
						we create a message and exit here.
						***************/
						if(!isset($data_arr['date_closed'])||($data_arr['date_closed']=='')){
							$msg = "Please specify the closing date for the M&a deal if you want to mark it as completed";
							return true;
						}
						/*********
						sng:11/sep/2012
						closing date set
						
						First we need to set the closing date
						then that closing date becomes the date of the deal 
						and the subcat is changed and flag set
						**************/
						$extra_updt_q = "update ".TP."transaction_extra_detail set date_closed='".mysql_real_escape_string($data_arr['date_closed'])."' where transaction_id='".$deal_id."'";
						$ok = $db->mod_query($extra_updt_q);
						if(!$ok){
							return false;
						}
						$stat_update_q = "date_of_deal='".mysql_real_escape_string($data_arr['date_closed'])."',deal_subcat1_name='Completed',in_calculation='1'";
					}
					if($data_arr['deal_completion_status']=="lost"){
						$stat_update_q = "deal_subcat1_name='Pending',in_calculation='0'";
					}
					if($data_arr['deal_completion_status']=="cancelled"){
						$stat_update_q = "deal_subcat1_name='Pending',in_calculation='0'";
					}
					if($stat_update_q!=""){
						$stat_update_q = "update ".TP."transaction set ".$stat_update_q." where id='".$deal_id."'";
						$ok = $db->mod_query($stat_update_q);
					}
				}
			}
		}
		
		
		$report_date = date("Y-m-d");
		$q = "insert into ".TP."transaction_edit_suggestion_detail set deal_id='".$deal_id."', suggested_by='".$mem_id."', date_suggested='".$report_date."'".$q;
		
		$ok = $db->mod_query($q);
		if(!$ok){
			return false;
		}
		$msg = "Your suggestion has been stored";
		$this->mark_member_as_verifier($deal_id,$mem_id);
		return true;
		
	}
	
	public function front_submit_valuation($deal_id,$mem_id,$data_arr,&$msg){
		$db = new db();
		
		$q = "";
		
		if(isset($data_arr['value_in_million'])&&($data_arr['value_in_million']!='')){
			$q.=",value_in_million='".(float)$data_arr['value_in_million']."'";
		}
		
		if(isset($data_arr['currency'])&&($data_arr['currency']!='')){
			$q.=",currency='".mysql_real_escape_string(strtoupper($data_arr['currency']))."'";
		}
		
		if(isset($data_arr['exchange_rate'])&&($data_arr['exchange_rate']!='')){
			$q.=",exchange_rate='".(float)$data_arr['exchange_rate']."'";
		}
		
		if(isset($data_arr['value_in_million_local_currency'])&&($data_arr['value_in_million_local_currency']!='')){
			$q.=",value_in_million_local_currency='".(float)$data_arr['value_in_million_local_currency']."'";
		}
		
		if(isset($data_arr['acquisition_percentage'])&&($data_arr['acquisition_percentage']!='')){
			$q.=",acquisition_percentage='".(float)$data_arr['acquisition_percentage']."'";
		}
		
		if(isset($data_arr['enterprise_value_million_local_currency'])&&($data_arr['enterprise_value_million_local_currency']!='')){
			$q.=",enterprise_value_million_local_currency='".(float)$data_arr['enterprise_value_million_local_currency']."'";
		}
		
		if(isset($data_arr['total_debt_million_local_currency'])&&($data_arr['total_debt_million_local_currency']!='')){
			$q.=",total_debt_million_local_currency='".(float)$data_arr['total_debt_million_local_currency']."'";
		}
		
		if(isset($data_arr['cash_million_local_currency'])&&($data_arr['cash_million_local_currency']!='')){
			$q.=",cash_million_local_currency='".(float)$data_arr['cash_million_local_currency']."'";
		}
		
		if(isset($data_arr['adjustments_million_local_currency'])&&($data_arr['adjustments_million_local_currency']!='')){
			$q.=",adjustments_million_local_currency='".(float)$data_arr['adjustments_million_local_currency']."'";
		}
		
		if(isset($data_arr['net_debt_in_million_local_currency'])&&($data_arr['net_debt_in_million_local_currency']!='')){
			$q.=",net_debt_in_million_local_currency='".(float)$data_arr['net_debt_in_million_local_currency']."'";
		}
		
		if(isset($data_arr['implied_equity_value_in_million_local_currency'])&&($data_arr['implied_equity_value_in_million_local_currency']!='')){
			$q.=",implied_equity_value_in_million_local_currency='".(float)$data_arr['implied_equity_value_in_million_local_currency']."'";
		}
		
		if(isset($data_arr['dividend_on_top_of_equity_million_local_curency'])&&($data_arr['dividend_on_top_of_equity_million_local_curency']!='')){
			$q.=",dividend_on_top_of_equity_million_local_curency='".(float)$data_arr['dividend_on_top_of_equity_million_local_curency']."'";
		}
		
		if(isset($data_arr['deal_price_per_share'])&&($data_arr['deal_price_per_share']!='')){
			$q.=",deal_price_per_share='".(float)$data_arr['deal_price_per_share']."'";
		}
		
		if(isset($data_arr['total_shares_outstanding_million'])&&($data_arr['total_shares_outstanding_million']!='')){
			$q.=",total_shares_outstanding_million='".(float)$data_arr['total_shares_outstanding_million']."'";
		}
		
		if($q == ""){
			$msg = "Please specify at least one suggestion";
			return true;
		}
		/*********************************************
		sng:26/sep/2012
		We check if deal value is set for the transaction or not. If not set then we set the deal value (if it is in the suggestion)
		**********/
		if(isset($data_arr['value_in_million'])&&($data_arr['value_in_million']!='')){
			$deal_value_query = "select value_in_billion from ".TP."transaction where id='".$deal_id."'";
			$ok = $db->select_query($deal_value_query);
			if(!$ok){
				return false;
			}
			if(!$db->has_row()){
				return false;
			}
			$row = $db->get_row();
			if($row['value_in_billion']==0.0){
				/**********
				need to convert to billion
				and get value range id
				********************/
				require_once("classes/class.deal_support.php");
				$deal_support = new deal_support();
				$value_in_billion = (float)$data_arr['value_in_million']/1000;
				$value_range_id = 0;
				$ok = $deal_support->front_get_value_range_id_from_value($data_arr['value_in_million'],$value_range_id);
				if(!$ok){
					return false;
				}
				$value_updt_q = "update ".TP."transaction set value_in_billion='".$value_in_billion."',value_range_id='".$value_range_id."' where id='".$deal_id."'";
				$ok = $db->mod_query($value_updt_q);
			}
		}
		/**************************************************/
		
		$report_date = date("Y-m-d");
		$q = "insert into ".TP."transaction_edit_suggestion_valuation set deal_id='".$deal_id."', suggested_by='".$mem_id."', date_suggested='".$report_date."'".$q;
		
		$ok = $db->mod_query($q);
		if(!$ok){
			return false;
		}
		$msg = "Your suggestion has been stored";
		$this->mark_member_as_verifier($deal_id,$mem_id);
		return true;
	}
	/*****************************************************************************************/
	
	/*****************
	sng:30/apr
	We now get the suggested notes from transaction_note_suggestions
	We show the suggestions in the order they were stored
	We also show the status
	
	Since we use this table to store the original suggestion also, we need a flag, get_original. If get_original then
	we check for is_correction=n (or exclude the corrective records)
	*****************/
	public function fetch_notes($deal_id,$get_original,&$data_arr,&$data_count){
		$db = new db();
		$q = "select date_suggested,note,status_note,member_type,work_email from ".TP."transaction_note_suggestions as n left join ".TP."member as m on(n.suggested_by=m.mem_id) where deal_id='".$deal_id."'";
		
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
	
	/******************
	sng:2/may/2012
	We now store each suggested source url in a row, no csv.
	We need to show the urls sorted by date but also grouped by member. So we order by date and mem id.
	We also store the original suggestion here (is_correction:n) so we use a flag to get the original suggestion
	********************/
	public function fetch_sources($deal_id,$get_original,&$data_arr,&$data_count){
		$db = new db();
		$q = "select suggested_by,date_suggested,source_url,member_type,work_email from ".TP."transaction_sources_suggestions as s left join ".TP."member as m on(s.suggested_by=m.mem_id) where deal_id='".$deal_id."'";
		
		if($get_original){
			$q.=" and is_correction='n'";
		}else{
			$q.=" and is_correction='y'";
		}
		
		$q.=" order by date_suggested,suggested_by";
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
	/***************
	sng:2/may/2012
	*****************/
	public function fetch_sources_with_grouping($deal_id,$get_original,&$group_data_arr,&$group_count){
		$data_count = 0;
		$data_arr = NULL;
		
		$ok = $this->fetch_sources($deal_id,$get_original,$data_arr,$data_count);
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
				$group_data_arr[$suggestion_columns_head]['suggested_sources_count'] = 0;
				$group_data_arr[$suggestion_columns_head]['suggested_sources'] = array();
			}else{
				/************
				use the current suggested by unit
				************/
			}
			$group_data_arr[$suggestion_columns_head]['suggested_sources'][] = $data_arr[$i];
			$group_data_arr[$suggestion_columns_head]['suggested_sources_count']++;
		}
		$group_count = $suggestion_columns_head+1;
		/**********
		suggestion_columns_head is zero based
		*************/
		return true;
	}
	
	/**********
	requirement for this is bit different.
	we need to group all companies sent by some member on some date
	
	sng:19/apr/2012
	We show each suggestion columns ordered by date, so, we order by date, then by mem id, then by company name
	
	Since we are storing original submission of participant company (those that were specified during the deal submission),
	we can use this function to get that also. We just need another flag, get_original
	
	We also want to show status_note for the suggestion
	***************/
	public function fetch_participants($deal_id,$get_original,&$data_arr,&$data_count){
		$db = new db();
		$q = "select suggested_by,date_suggested,company_name,status_note,sc.role_id,footnote,role_name,member_type,work_email from ".TP."transaction_companies_suggestions as sc left join ".TP."transaction_company_role_master as rm on(sc.role_id=rm.role_id) left join ".TP."member as m on(sc.suggested_by=m.mem_id) where deal_id='".$deal_id."'";
		if($get_original){
			$q.=" and is_correction='n'";
		}else{
			$q.=" and is_correction='y'";
		}
		
		$q.=" order by date_suggested,suggested_by,company_name";
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
	
	/************************
	sng:19/apr/2012
	This method is used when we need to have grouped data for participant company suggestion
	
	Since we are storing original submission of participant companies (those that were specified during the deal submission),
	we can use this function to get that also. We just need another flag, get_original
	***********************/
	public function fetch_participants_with_grouping($deal_id,$get_original,&$group_data_arr,&$group_count){
		$data_count = 0;
		$data_arr = NULL;
		
		$ok = $this->fetch_participants($deal_id,$get_original,$data_arr,$data_count);
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
				$group_data_arr[$suggestion_columns_head]['suggested_companies_count'] = 0;
				$group_data_arr[$suggestion_columns_head]['suggested_companies'] = array();
			}else{
				/************
				use the current suggested by unit
				************/
			}
			$group_data_arr[$suggestion_columns_head]['suggested_companies'][] = $data_arr[$i];
			$group_data_arr[$suggestion_columns_head]['suggested_companies_count']++;
		}
		$group_count = $suggestion_columns_head+1;
		/**********
		suggestion_columns_head is zero based
		*************/
		return true;
	}
	
	/***************
	sng:109apr/2012
	Although we have method in transaction_company, we still write the code here because we want the result like we have for suggestion.
	This gets the current participants associated with the deal. We cannot use the original suggestion since companies may get added or deleted later.
	The suggested by and date suggested can be taken as mem id who suggested the deal and date when the deal was suggested
	*****************/
	public function get_current_participants_with_grouping($deal_id,&$group_data_arr,&$group_count){
		$db = new db();
		$q = "select t.added_by_mem_id as suggested_by,t.added_on as date_suggested,c.name as company_name,p.role_id,p.footnote,role_name,member_type,work_email from ".TP."transaction_companies as p left join ".TP."transaction as t on(p.transaction_id=t.id) left join ".TP."company as c on(p.company_id=c.company_id) left join ".TP."transaction_company_role_master as rm on(p.role_id=rm.role_id) left join ".TP."member as m on(t.added_by_mem_id=m.mem_id) where p.transaction_id='".$deal_id."' order by t.added_on,t.added_by_mem_id,company_name";
		
		
		$ok = $db->select_query($q);
		if(!$ok){
			return false;
		}
		$data_count = $db->row_count();
		if(0==$data_count){
			$group_count = 0;
			return true;
		}
		$data_arr = $db->get_result_set_as_array();
		
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
				$group_data_arr[$suggestion_columns_head]['suggested_companies_count'] = 0;
				$group_data_arr[$suggestion_columns_head]['suggested_companies'] = array();
			}else{
				/************
				use the current suggested by unit
				************/
			}
			$group_data_arr[$suggestion_columns_head]['suggested_companies'][] = $data_arr[$i];
			$group_data_arr[$suggestion_columns_head]['suggested_companies_count']++;
		}
		$group_count = $suggestion_columns_head+1;
		/**********
		suggestion_columns_head is zero based
		*************/
		return true;
	}
	
	/**********
	requirement for this is bit different.
	we need to group all banks/law firms sent by some member on some date
	
	sng:5/apr/2012
	We show each suggestion columns ordered by date, so, we order by date, then by mem id, then by partner name
	
	sng:6/apr/2012
	Since we are storing original submission of partners (those that were specified during the deal submission),
	we can use this function to get that also. We jsut need another flag, get_original
	
	sng:16/apr/2012
	We also want to show status_note for the suggestion
	***************/
	public function fetch_partners($deal_id,$partner_type,$get_original,&$data_arr,&$data_count){
		$db = new db();
		$q = "select suggested_by,date_suggested,partner_name,status_note,sp.role_id,role_name,member_type,work_email from ".TP."transaction_partners_suggestions as sp left join ".TP."transaction_partner_role_master as rm on(sp.role_id=rm.role_id) left join ".TP."member as m on(sp.suggested_by=m.mem_id) where deal_id='".$deal_id."' and sp.partner_type='".$partner_type."'";
		if($get_original){
			$q.=" and is_correction='n'";
		}else{
			$q.=" and is_correction='y'";
		}
		$q.=" order by date_suggested,suggested_by,partner_name";
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
	
	/************************
	sng:5/apr/2012
	This method is used when we need to have grouped data for partner suggestion
	
	sng:6/apr/2012
	Since we are storing original submission of partners (those that were specified during the deal submission),
	we can use this function to get that also. We jsut need another flag, get_original
	***********************/
	public function fetch_partners_with_grouping($deal_id,$partner_type,$get_original,&$group_data_arr,&$group_count){
		$data_count = 0;
		$data_arr = NULL;
		
		$ok = $this->fetch_partners($deal_id,$partner_type,$get_original,$data_arr,$data_count);
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
				$group_data_arr[$suggestion_columns_head]['suggested_firms_count'] = 0;
				$group_data_arr[$suggestion_columns_head]['suggested_firms'] = array();
			}else{
				/************
				use the current suggested by unit
				************/
			}
			$group_data_arr[$suggestion_columns_head]['suggested_firms'][] = $data_arr[$i];
			$group_data_arr[$suggestion_columns_head]['suggested_firms_count']++;
		}
		$group_count = $suggestion_columns_head+1;
		/**********
		suggestion_columns_head is zero based
		*************/
		return true;
	}
	
	/***************
	sng:10/apr/2012
	Although we have method in transaction_partner, we still write the code here because we want the result like we have for suggestion.
	This gets the current partners associated with the deal. We cannot use the original suggestion since partners may get added or deleted later.
	The suggested by and date suggested can be taken as mem id who suggested the deal and date when the deal was suggested
	*****************/
	public function get_current_partners_with_grouping($deal_id,$partner_type,&$group_data_arr,&$group_count){
		$db = new db();
		$q = "select t.added_by_mem_id as suggested_by,t.added_on as date_suggested,c.name as partner_name,p.role_id,role_name,member_type,work_email from ".TP."transaction_partners as p left join ".TP."transaction as t on(p.transaction_id=t.id) left join ".TP."company as c on(p.partner_id=c.company_id) left join ".TP."transaction_partner_role_master as rm on(p.role_id=rm.role_id) left join ".TP."member as m on(t.added_by_mem_id=m.mem_id) where p.transaction_id='".$deal_id."' and p.partner_type='".$partner_type."' order by t.added_on,t.added_by_mem_id,partner_name";
		
		
		$ok = $db->select_query($q);
		if(!$ok){
			return false;
		}
		$data_count = $db->row_count();
		if(0==$data_count){
			$group_count = 0;
			return true;
		}
		$data_arr = $db->get_result_set_as_array();
		
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
				$group_data_arr[$suggestion_columns_head]['suggested_firms_count'] = 0;
				$group_data_arr[$suggestion_columns_head]['suggested_firms'] = array();
			}else{
				/************
				use the current suggested by unit
				************/
			}
			$group_data_arr[$suggestion_columns_head]['suggested_firms'][] = $data_arr[$i];
			$group_data_arr[$suggestion_columns_head]['suggested_firms_count']++;
		}
		$group_count = $suggestion_columns_head+1;
		/**********
		suggestion_columns_head is zero based
		*************/
		return true;
	}
	
	public function fetch_detail_extra($deal_id,&$data_arr,&$data_count){
		
		$db = new db();
        
		$q = "select t.*,takeover_name,vrm.short_caption as fuzzy_value_short_caption,vrm.display_text as fuzzy_value,m.work_email,m.member_type from ".TP."transaction_edit_suggestion_detail as t left join ".TP."takeover_type_master as k on(t.takeover_id=k.takeover_id) LEFT JOIN ".TP."transaction_value_range_master as vrm ON (t.value_range_id=vrm.value_range_id) left join ".TP."member as m on(t.suggested_by=m.mem_id) where t.deal_id='".$deal_id."'";
         
        $result = $db->select_query($q);
        
        if(!$result){
			//echo $db->error();
            return false;
        }
		
		$data_count = $db->row_count();
		if(0 == $data_count){
			return true;
		}
        //////////////////////////////////
        $data_arr = $db->get_result_set_as_array();
        
		return true;
	}
	
	public function fetch_valuation($deal_id,&$data_arr,&$data_count){
		
		$db = new db();
        
		/*$q = "select t.*,vrm.short_caption as fuzzy_value_short_caption,vrm.display_text as fuzzy_value,m.work_email,m.member_type from ".TP."transaction_edit_suggestion_valuation as t LEFT JOIN ".TP."transaction_value_range_master as vrm ON (t.value_range_id=vrm.value_range_id) left join ".TP."member as m on(t.suggested_by=m.mem_id) where t.deal_id='".$deal_id."'";*/
        
		$q = "select v.*,m.work_email,m.member_type from ".TP."transaction_edit_suggestion_valuation as v left join ".TP."member as m on(v.suggested_by=m.mem_id) where v.deal_id='".$deal_id."'";
		
        $result = $db->select_query($q);
        
        if(!$result){
			//echo $db->error();
            return false;
        }
		
		$data_count = $db->row_count();
		if(0 == $data_count){
			return true;
		}
        //////////////////////////////////
        $data_arr = $db->get_result_set_as_array();
        
		return true;
	}
	
	/*******************************************************
	sng:24/mar/2012
	method to fetch the additional data for the given deal from the deal record itself and not from corrections.
	This is just a convenient method
	*****************************************/
	public function get_deal_detail_extra($deal_id,&$deal_data_arr,&$deal_found){
		
		$db = new db();
        
        if($deal_id==""){
            $deal_found = false;
            return true;
        }
        
		$q = "select t.id as deal_id,t.*,e.*,takeover_name,vrm.short_caption as fuzzy_value_short_caption,vrm.display_text as fuzzy_value,m.work_email,m.member_type from ".TP."transaction as t left join ".TP."transaction_extra_detail as e on(t.id=e.transaction_id) left join ".TP."takeover_type_master as k on(e.takeover_id=k.takeover_id) LEFT JOIN ".TP."transaction_value_range_master as vrm ON (t.value_range_id=vrm.value_range_id) left join ".TP."member as m on(t.added_by_mem_id=m.mem_id) where t.id='".$deal_id."'";
         
        $result = $db->select_query($q);
        
        if(!$result){
			//echo $db->error();
            return false;
        }
		
		if(!$db->has_row()){
            $deal_found = false;
            return true;
        }
        //////////////////////////////////
        $deal_data_arr = $db->get_row();
        $deal_found = true;
        
		
		return true;
	}
	
	/*******************************************************
	sng:5/apr/2012
	method to fetch just the deal data for the given deal from the deal record itself.
	This is just a convenient method
	*****************************************/
	public function get_deal_detail($deal_id,&$deal_data_arr,&$deal_found){
		
		$db = new db();
        
        if($deal_id==""){
            $deal_found = false;
            return true;
        }
        
		$q = "select t.id as deal_id,t.*,vrm.short_caption as fuzzy_value_short_caption,vrm.display_text as fuzzy_value,m.work_email,m.member_type from ".TP."transaction as t LEFT JOIN ".TP."transaction_value_range_master as vrm ON (t.value_range_id=vrm.value_range_id) left join ".TP."member as m on(t.added_by_mem_id=m.mem_id) where t.id='".$deal_id."'";
         
        $result = $db->select_query($q);
        
        if(!$result){
			//echo $db->error();
            return false;
        }
		
		if(!$db->has_row()){
            $deal_found = false;
            return true;
        }
        //////////////////////////////////
        $deal_data_arr = $db->get_row();
        $deal_found = true;
        
		
		return true;
	}
}
?>