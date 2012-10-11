<?php
/*******************
sng:4/apr/2012

Holds methods related to banks and law firms associated with a deal
**********************/
class transaction_partner{
	
	/*********
	Just get the id and names of the banks or law firms for the given deal and partner type
	
	sng:11/oct/2012
	Let us also get the record id. That way, if we use this to edit a record, we can use the id to get to the proper record quickly
	***********/
	public function get_all_partners_data_by_type($transaction_id,$type,&$data_arr,&$data_count){
		
		$db = new db();
		$q = "select t.id,t.partner_id,t.role_id,c.name as company_name from ".TP."transaction_partners as t left join ".TP."company as c on(t.partner_id=c.company_id) where t.transaction_id='".$transaction_id."' AND t.partner_type='".$type."' order by company_name";
		$ok = $db->select_query($q);
		if(!$ok){
			return false;
		}
		$data_count = $db->row_count();
		if(0 == $data_count){
			return true;
		}
		$data_arr = $db->get_result_set_as_array();
		return true;
	}
	
	/*****
    sng:26/Oct/2010
    ordering the entries by the firm name
    ***/
    public function get_all_partner($transaction_id,$type,&$data_arr,&$data_count){
        global $g_mc;
        /***
        sng:13/apr/2010
        This code is used in lots of places, so i cannot change c.* as well as company name
        */
        $q = "select t.*,c.*,c.name as company_name from ".TP."transaction_partners as t left join ".TP."company as c on(t.partner_id=c.company_id) where t.transaction_id='".$transaction_id."' AND t.partner_type='".$type."' order by company_name";
        if (isset($_REQUEST['debug']))
            echo $q . PHP_EOL;
        $res = mysql_query($q);
        if(!$res){
            return false;
        }
        ///////////////////////////
        $data_count = mysql_num_rows($res);
        if(0 == $data_count){
            //no data to return so
            return true;
        }
        /////////////////
        for($i=0;$i<$data_count;$i++){
            $data_arr[$i] = mysql_fetch_assoc($res);
            /***
            sng:13/apr/2010
            magic quoted company name
            **/
            $data_arr[$i]['company_name'] = $g_mc->db_to_view($data_arr[$i]['company_name']);
            $data_arr[$i]['name'] = $g_mc->db_to_view($data_arr[$i]['name']);
        }
        return true;
    }
	/************
	sng:11/oct/2012
	record_id: value of id in transaction_partners table
	role_id could be blank. In that case, treat it as 0
	
	updated true/false
	Before updating, we do check if admin has actually sent any update or not
	
	If the current role is not set and admin does not specify anything, it is not a suggestion
	If the current role is not set and admin specify a role, we store it with status, role set
	
	If the current role is set and admin specify the same, it is not a suggestion
	If the current role is set and admin does not apecify a role, we store it with status, role removed
	If the current role is set and admin specify another, we store it with status, role updated
	
	Note: this is different from set_deal_partner_role (where we take deal_id, partner_id)
	
	first we try to set the role. If we succeed, only then we put a 'suggestion'.
	**************/
	public function admin_update_deal_partner_role($record_id,$role_id,&$updated,&$msg){
		$db = new db();
		if($role_id==""){
			$role_id = 0;
		}
		
		$q = "select transaction_id,role_id,partner_type,name from ".TP."transaction_partners as p left join ".TP."company as c on(p.partner_id=c.company_id)  where id='".$record_id."'";
		$ok = $db->select_query($q);
		if(!$ok){
			return false;
		}
		if(!$db->has_row()){
			return false;
		}
		$row = $db->get_row();
		$curr_role_id = $row['role_id'];
		$partner_name = $row['name'];
		$partner_type = $row['partner_type'];
		$deal_id = $row['transaction_id'];
		
		$role_status = "";
		
		if(0==$curr_role_id){
			//role not set currently
			if(0==$role_id){
				//admin specified nothing
				$msg = "No role suggestion specified";
				$updated = false;
				return true;
			}else{
				$role_status = "role set";
			}
		}else{
			//role set currently
			if($curr_role_id==$role_id){
				//admin specified same thing
				$msg = "No role suggestion specified";
				$updated = false;
				return true;
			}else{
				if(0==$role_id){
					//admin deliberately removed the role
					$role_status = "role removed";
				}else{
					$role_status = "role updated";
				}
			}
		}
		
		$updt_q = "update ".TP."transaction_partners set role_id='".$role_id."' where id='".$record_id."'";
		$ok = $db->mod_query($updt_q);
		if(!$ok){
			return false;
		}
		$updated = true;
		/****
		updated so put a suggestion
		suggested by is admin whose id is 0
		this is a correction
		****/
		$today = date("Y-m-d H:i:s");
		$suggest_q = "insert into ".TP."transaction_partners_suggestions set deal_id='".$deal_id."',suggested_by='0',date_suggested='".$today."',partner_name='".$partner_name."',partner_type='".$partner_type."',role_id='".$role_id."',status_note='".$role_status."',is_correction='y'";
		
		$ok = $db->mod_query($suggest_q);
		//never mind if error
		return true;
	}
}
?>