<?php
/**********************
Stuff that are related to deals like currency, stock exchange, type/subtype etc
***********/
class transaction_support{
	/***********
	sng:19/nov/2012
	We need to order by type/subtype and then by display order of subtype 2
	***************/
	public function get_category_tree() {
        $q = "select * from ".TP."transaction_type_master order by type,subtype1,subtype2_display_order";
        $res = mysql_query($q);
        $ret = array();
        if (!$res) {
            return $ret;
        }
        while($row = mysql_fetch_assoc($res)) {
			
            $ret[$row['type']][$row['subtype1']][] = $row['subtype2'];
            //var_dump($row);
        }
		
        //var_Dump($ret);
        return $ret;
    }
	/******************
	sng:19/nov/2012
	client wants to arrange the sub-sub-type as per the specified display order
	within the type and sub-type
	******************/
    public function get_all_category_type_subtype(&$data_arr,&$data_count){
        $q = "select  * from ".TP."transaction_type_master order by type, subtype1, subtype2_display_order";
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
	public function add_category_type_subtype($data_arr,&$validation_passed,&$err_arr){
        $validation_passed = true;
        if($data_arr['type']==""){
            $validation_passed = false;
            $err_arr['type'] = "specify type";
        }
        
        if($data_arr['subtype1']==""){
            $validation_passed = false;
            $err_arr['subtype1'] = "specify sub type";
        }
        
        if($data_arr['subtype2']==""){
            $validation_passed = false;
            $err_arr['subtype2'] = "specify sub sub type or n/a";
        }
        
        //////////////////////////////////////////////////
        if(($data_arr['type']!="")&&($data_arr['subtype1']!="")&&($data_arr['subtype2']!="")){
            
            //check if the trio is already there ot not
            $q = "select count(*) as cnt from ".TP."transaction_type_master where type='".$data_arr['type']."' and subtype1='".$data_arr['subtype1']."' and subtype2='".$data_arr['subtype2']."'";
            $res = mysql_query($q);
            if(!$res){
                return false;
            }
            ////////////////////////////
            $row = mysql_fetch_assoc($res);
            if($row['cnt']!=0){
                //the trio is there
                $validation_passed = false;
                $err_arr['type'] = "These type/subtype are already there";
            }
        }
        //////////////////////////////////////////////////////////////////
        if(!$validation_passed){
            return true;
        }
        ///////////////////////////////
        //insert
        $q = "insert into ".TP."transaction_type_master set type='".$data_arr['type']."', subtype1='".$data_arr['subtype1']."', subtype2='".$data_arr['subtype2']."'";
        $result = mysql_query($q);
        if(!$result){
            return false;
        }
        return true;
    }
	
	/***************
	sng:1/sep/2012
	*****************/
	public function deal_completion_status_types(&$data_arr,&$data_count){
		$db = new db();
		
		$q = "select * from ".TP."deal_completion_status_master";
		$ok = $db->select_query($q);
		if(!$ok){
			return false;
		}
		$data_count = $db->row_count();
		if($data_count == 0){
			//no recs
			return true;
		}
		//recs so
		$data_arr = $db->get_result_set_as_array();
		return true;
	}
	
	/********************
	sng:16/sep/2011
	given a deal id, get the deal type/sub type/sub sub type
	get_deal_type-from_deal_id
	**********************/
	public function get_deal_type($deal_id,&$data_arr){
		$db = new db();
		$q = "select deal_cat_name,deal_subcat1_name,deal_subcat2_name from ".TP."transaction where id='".$deal_id."'";
		$success = $db->select_query($q);
		if(!$success){
			return false;
		}
		if(!$db->has_row()){
			return false;
		}
		$data_arr = $db->get_row();
		return true;
	}
}
?>