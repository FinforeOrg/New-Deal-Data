<?php
/***********************
sng:18/sep/2012

This is for members associated with a deal. For each bank/law firm associated with a deal,
there are one or more members of the bank/law firm who worked on that deal
*************************/
class transaction_member{
	/****************
	sng:18/sep/2012 
	**************/
	public function get_all_deal_members_by_type($deal_id,$member_type,&$data_arr,&$data_count){
		$db = new db();
		$q = "select pm.*,m.f_name,m.l_name,t.value_in_billion,t.value_range_id,c.name as firm_name from ".TP."transaction_partner_members as pm left join ".TP."member as m on(pm.member_id=m.mem_id) left join ".TP."transaction as t on(pm.transaction_id=t.id) left join ".TP."company as c on(pm.partner_id=c.company_id) where transaction_id='".(int)$deal_id."' and pm.member_type='".mysql_real_escape_string($member_type)."'";
		
        $ok = $db->select_query($q);
        if(!$ok){
			echo $db->error();
            return false;
        }
        $data_count = $db->row_count();
        if(0==$data_count){
            return true;
        }
        $data_arr = $db->get_result_set_as_array();
        return true;
    }
	
	/***
    sng:17/apr/2010
    adding a member to a deal team of a bank/law firm that was associated with the deal
    check:
    if that partner company is actually with the deal. If so
    check whether this member is already a part of the deal team or not.
    A member can be added only once, never mind the his/her company.
    If not present, the member can be added. But, when the deal was closed
    did the member worked for that partner company? We code a simpler validation.
    The member is either working for the partner company or worked for the parter company
    **********/
    public function add_deal_partner_team_member($deal_id,$deal_partner_id,$mem_id,&$mem_added,&$msg){
		$db = new db();
        //check if the partner company is actually associated with the deal
        $q = "select count(*) as cnt from ".TP."transaction_partners where transaction_id='".$deal_id."' and partner_id='".$deal_partner_id."'";
        $ok = $db->select_query($q);
        if(!$ok){
            return false;
        }
        $row = $db->get_row();
        if($row['cnt'] == 0){
            //this partner was not found for the deal
            $mem_added = false;
            $msg = "This firm was not associated with the deal";
            return true;
        }
        /*********************************************************************
        check if the member is already added or not
		**************/
        $q = "select count(*) as cnt from ".TP."transaction_partner_members where transaction_id='".$deal_id."' and member_id='".$mem_id."'";
        $ok = $db->select_query($q);
        if(!$ok){
            return false;
        }
        $row = $db->get_row();
        if($row['cnt']!=0){
            //this member found for this deal
            $mem_added = false;
            $msg = "This member is present in the deal team";
            return true;
        }
        /*********************************************************************************
        if the member work for the partner company, the partner id and id of the
        member's company will match, get the designation and weight
		************/
		$q = "select designation,member_type from ".TP."member where mem_id='".$mem_id."' and company_id='".$deal_partner_id."'";
		$ok = $db->select_query($q);
		if(!$ok){
			return false;
		}
        $cnt = $db->row_count();
        if($cnt==0){
			/***************************************************
            either the member is not found or the member does not work for the deal partner company
            we assume that member does not work there, so we check the history
            whether the member worked in that company at all
            we try to get the last postition at that company, that is why we order by year_from
			***************************/
            $q1 = "select member_type,designation from ".TP."member_work_history where mem_id='".$mem_id."' and company_id='".$deal_partner_id."' order by year_from desc limit 0,1";
            $ok = $db->select_query($q1);
            if(!$ok){
                return false;
            }
            $cnt1 = $db->row_count();
            if($cnt1==0){
                //not found in hostory so
                $mem_added = false;
                $msg = "Association with the firm was not found";
                return true;
            }else{
                //found
                $row1 = $db->get_row();
                $designation = $row1['designation'];
                $mem_type = $row1['member_type'];
            }
        }else{
            //member works, so get the designation and wight
            $row = $db->get_row();
            $designation = $row['designation'];
            $mem_type = $row['member_type'];
        }
        /***************************************
        now get the weight for this designation for this member type
		*****************/
        $q = "select deal_share_weight from ".TP."designation_master where designation='".$designation."' and member_type='".$mem_type."'";
        $ok = $db->select_query($q);
        if(!$ok){
            return false;
        }
        $cnt = $db->row_count();
        if($cnt==0){
            //no such designation for this member type, use default of 1
            $deal_share_weight = 1;
        }else{
            $row = $db->get_row();
            $deal_share_weight = $row['deal_share_weight'];
        }
        /***************************************
        insert
		*****************/
        $q = "insert into ".TP."transaction_partner_members set transaction_id='".$deal_id."', partner_id='".$deal_partner_id."', member_id='".$mem_id."',member_type='".$mem_type."',designation='".$designation."',deal_share_weight='".$deal_share_weight."'";
        $ok = $db->mod_query($q);
        if($ok){
            $mem_added = true;
            $msg = "Added to the deal team";
			/***********************************
            update members adjusted value for this firm's deal team
			****************/
            $success = $this->update_deal_team_members_adjusted_value($deal_id,$deal_partner_id);
            
            return true;
        }else{
            return false;
        }
        
    }
	
	public function update_deal_team_members_adjusted_value($deal_id,$deal_partner_id){
		//get the adjusted value for the partner company
		$db = new db();
        $q = "select adjusted_value_in_billion from ".TP."transaction_partners where transaction_id='".$deal_id."' and partner_id='".$deal_partner_id."'";
        $ok = $db->select_query($q);
        if(!$ok){
            return false;
        }
        $cnt = $db->row_count();
        if($cnt == 0){
            //no such deal and partner
            return false;
        }
        ////////////////////////////////////
        $row = $db->get_row();
        $partner_adjusted_value_in_billion = $row['adjusted_value_in_billion'];
        ///////////////////////////////////////////////
        //now get the sum of weight for all the members for this deal and partner
		/*********************
		sng:3/apr/2013
		By storing the designation weight here, we can do a quick sum. However, we are making a big assumption.
		Designation weight ONCE set IS NOT ALTERED ever.
		*******************/
        $q = "select sum(deal_share_weight) as sum_weight from ".TP."transaction_partner_members where transaction_id='".$deal_id."' and partner_id='".$deal_partner_id."'";
        $ok = $db->select_query($q);
        if(!$ok){
            return false;
        }
        $row = $db->get_row();
        $sum_wt = $row['sum_weight'];
        /***
        sng:12/may/2010
        if there are no members then there is no weights and sum is zero, so in that case we do not proceed
        ********/
        if(0==$sum_wt){
            return true;
        }
        $ratio = $partner_adjusted_value_in_billion/$sum_wt;
        //update the members
        $q = "update ".TP."transaction_partner_members set adjusted_value_in_billion=deal_share_weight*".$ratio." where transaction_id='".$deal_id."' and partner_id='".$deal_partner_id."'";
        
        $ok = $db->mod_query($q);
        if(!$result){
            return false;
        }
        return true;
	}
}
?>