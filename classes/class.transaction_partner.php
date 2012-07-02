<?php
/*******************
sng:4/apr/2012

Holds methods related to banks and law firms associated with a deal
**********************/
class transaction_partner{
	
	/*********
	Just get the id and names of the banks or law firms for the given deal and partner type
	***********/
	public function get_all_partners_data_by_type($transaction_id,$type,&$data_arr,&$data_count){
		
		$db = new db();
		$q = "select t.partner_id,t.role_id,c.name as company_name from ".TP."transaction_partners as t left join ".TP."company as c on(t.partner_id=c.company_id) where t.transaction_id='".$transaction_id."' AND t.partner_type='".$type."' order by company_name";
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
}
?>