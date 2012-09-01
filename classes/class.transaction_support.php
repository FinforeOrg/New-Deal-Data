<?php
/**********************
Stuff that are related to deals like currency, stock exchange, type/subtype etc
***********/
class transaction_support{
	
	public function get_category_tree() {
        $q = "select * from ".TP."transaction_type_master ";
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
}
?>