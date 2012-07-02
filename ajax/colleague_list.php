<?php
/***
used by codes to fetch list of colleagues from full name
********/
include("../include/global.php");
require_once("classes/class.member.php");
require_once("classes/class.magic_quote.php");

if(isset($_POST['name'])){
	$name = $g_mc->view_to_db($_POST['name']);
	/////////////////////////////////////////////////////////////////////////////////
	// Is the string length greater than 0?
	if(strlen($name) >0) {
		//get colleague list
		$data_arr = array();
		$data_cnt = 0;
		$success = $g_mem->ajax_get_members_for_delegates($name,$_POST['type'],$_POST['company_id'],10,$data_arr,$data_cnt);
		if(!$success){
			//do nothing
			//echo "error";
		}else{
			//echo "count is ".$data_cnt;
			//return;
			for($i=0;$i<$data_cnt;$i++){
				?>
				<li onClick="fill('<?php echo $data_arr[$i]['mem_id'];?>','<?php echo $data_arr[$i]['f_name'];?>','<?php echo $data_arr[$i]['l_name'];?>')"><?php echo $data_arr[$i]['f_name'];?> <?php echo $data_arr[$i]['l_name'];?>, <?php echo $data_arr[$i]['designation'];?>[<?php echo $data_arr[$i]['work_email'];?>]</li>
				<?php
			}
		}
	}else{
		//do nothing
		//echo "null";
	}
}
?>