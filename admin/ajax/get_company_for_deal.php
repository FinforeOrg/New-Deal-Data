<?php
/***
used by admin to get company for adding a deal
********/
include("../../include/global.php");
require_once("classes/class.company.php");
require_once("classes/class.magic_quote.php");

if(isset($_POST['name'])){
	$name = $g_mc->view_to_db($_POST['name']);
	/////////////////////////////////////////////////////////////////////////////////
	// Is the string length greater than 0?
	if(strlen($name) >0) {
		//get colleague list
		$data_arr = array();
		$data_cnt = 0;
		$success = $g_company->ajax_get_company_name_list_by_type_name($name,$_POST['type'],10,$data_arr,$data_cnt);
		if(!$success){
			//do nothing
			//echo "error";
		}else{
			//echo "count is ".$data_cnt;
			//return;
			/*********
			sng:10/jan/2011
			If there is single quote in the name, the javascript fill function is in trouble
			*****/
			for($i=0;$i<$data_cnt;$i++){
				?>
				<li onClick="fill('<?php echo $data_arr[$i]['company_id'];?>','<?php echo addslashes($data_arr[$i]['name']);?>')"><?php echo $data_arr[$i]['name'];?></li>
				<?php
			}
		}
	}else{
		//do nothing
		//echo "null";
	}
}
?>