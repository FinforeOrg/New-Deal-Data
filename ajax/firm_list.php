<?php
/***
used by codes to fetch company list so that hint can be shown
********/
include("../include/global.php");
require_once("classes/class.company.php");
require_once("classes/class.magic_quote.php");
if(isset($_POST['search_string'])){
	$search_string = $g_mc->view_to_db($_POST['search_string']);
	//////////////////////////////////////////////////////////////////////////////////
	//given member type, we set company type
	$company_type = $_POST['type'];
	/////////////////////////////////////////////////////////////////////////////////
	// Is the string length greater than 0?
	if(strlen($search_string) >0) {
		//get company list
		$data_arr = array();
		$data_cnt = 0;
		$success = $g_company->filter_company_name_list_by_type_name($company_type,$search_string,false,$data_arr,$data_cnt);
		if(!$success){
			//do nothing
			//echo "error";
		}else{
			//echo "count is ".$data_cnt;
			//return;
			for($i=0;$i<$data_cnt;$i++){
				?>
				<li onClick="fill('<?php echo $data_arr[$i]['name'];?>')"><?php echo $data_arr[$i]['name'];?></li>
				<?php
			}
		}
	}else{
		//do nothing
		//echo "null";
	}
}
?>