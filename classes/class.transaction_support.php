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
}
?>