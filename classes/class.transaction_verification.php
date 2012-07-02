<?php
/*******************
sng:6/mar/2012
*****************/
class transaction_verification{
	
	/************
	sng:6/mar/2012
	A member has clicked the [confirm detail] button in the deal detail page
	
	sng:25/apr/2012
	If I am the submitter of the deal, I cannot be a verifier
	****************/
	public function verification_by_member($deal_id,$mem_id,&$response_msg){
		$db = new db();
		
		$q = "select added_by_mem_id from ".TP."transaction where id='".mysql_real_escape_string($deal_id)."'";
		$ok = $db->select_query($q);
		if(!$ok){
			return false;
		}
		$row = $db->get_row();
		if($row['added_by_mem_id'] == $mem_id){
			$response_msg = "As the submitter of this deal, you cannot verify it";
			return true;
		}
		
		$q = "select count(*) as cnt from ".TP."transaction_verifiers where deal_id='".mysql_real_escape_string($deal_id)."' and mem_id='".mysql_real_escape_string($mem_id)."'";
		$ok = $db->select_query($q);
		if(!$ok){
			return false;
		}
		$row = $db->get_row();
		if($row['cnt'] > 0){
			/*******
			this member has already verified the deal as ok
			*********/
			$response_msg = "You have already confirmed the detail";
			return true;
		}
		$q = "insert into ".TP."transaction_verifiers set deal_id='".mysql_real_escape_string($deal_id)."', mem_id='".mysql_real_escape_string($mem_id)."', date_verified='".date('Y-m-d H:i:s')."'";
		$ok = $db->mod_query($q);
		if(!$ok){
			return false;
		}
		$response_msg = "Thank you for confirming the deal detail";
		return true;
	}
	
	/************
	sng:6/mar/2012
	It will be like
	Verified by 2 Bankers at Morgan Stanley
	Verified by 1 Lawyer at Freshfields
	We do not show the individuals
	Verified by Matt Kraus at Morgan Stanley
	
	Also, it may happen that the member is now working in another bank
	it is the individual that is doing the "Confirm Details". Not doing it on behalf of his firm.
	************/
	public function member_verification_summery($deal_id,&$data_arr,&$data_cnt){
		$db = new db();
		$q = "SELECT count( * ) AS cnt, m.member_type, c.name FROM ".TP."transaction_verifiers AS v LEFT JOIN ".TP."member AS m ON ( v.mem_id = m.mem_id ) LEFT JOIN ".TP."company AS c ON ( m.company_id = c.company_id ) WHERE deal_id = '".$deal_id."' GROUP BY m.member_type, c.name";
		$ok = $db->select_query($q);
		if(!$ok){
			return false;
		}
		$data_cnt = $db->row_count();
		if(0==$data_cnt){
			//no data
			return true;
		}
		$data_arr = $db->get_result_set_as_array();
		return true;
	}
	
	public function admin_get_admin_unverified_deals_paged($start_offset,$num_to_fetch,&$data_arr,&$data_count){
		$db = new db();
        
		$q = $q = "SELECT t.id,t.value_in_billion,t.date_of_deal,deal_cat_name,deal_subcat1_name,deal_subcat2_name,t.value_range_id,t.added_on,vrm.display_text as fuzzy_value ,m.f_name,m.l_name,m.designation,w.name as work_company FROM ".TP."transaction AS t LEFT JOIN ".TP."transaction_value_range_master as vrm ON (t.value_range_id=vrm.value_range_id) LEFT JOIN ".TP."member as m on(t.added_by_mem_id=m.mem_id) left join ".TP."company as w on(m.company_id=w.company_id) where t.admin_verified='n' order by added_on limit ".$start_offset.",".$num_to_fetch;
		
		$ok = $db->select_query($q);
		if(!$ok){
			return false;
		}
		$data_count = $db->row_count();
		if(0==$data_count){
			//empty
			return true;
		}
		$data_arr = $db->get_result_set_as_array();
		
		require_once("classes/class.transaction_company.php");
		$g_trans_comp = new transaction_company();
		
		for($i=0;$i<$data_count;$i++){
			$transaction_id = $data_arr[$i]['id'];
			$data_arr[$i]['participants'] = NULL;
			//get the participants, just names
			$success = $g_trans_comp->get_deal_participants($transaction_id,$data_arr[$i]['participants']);
			if(!$success){
				return false;
			}
		}
        
        return true;
	}
}
?>