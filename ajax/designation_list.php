<?php
/***
used by codes to fetch designation list for a membership type
********/
include("../include/global.php");
require_once("classes/class.member.php");

if(isset($_POST['membership_type'])){
	//////////////////////////////////////////////////////////////////////////////////
	$member_type = $_POST['membership_type'];
	/////////////////////////////////////////////////////////////////////////////////
	// Is the string length greater than 0?
	if(strlen($member_type) >0) {
		//get designation list
		$data_arr = array();
		$data_cnt = 0;
		$success = $g_mem->get_all_designation_list_by_type($member_type,$data_arr,$data_cnt);
		if(!$success){
			//do nothing
			//echo "error";
		}else{
			//echo "count is ".$data_cnt;
			//return;
			?>
			<option value="">Select</option>
			<?php
			for($i=0;$i<$data_cnt;$i++){
				?>
				<option value="<?php echo $data_arr[$i]['designation'];?>"><?php echo $data_arr[$i]['designation'];?></option>
				<?php
			}
		}
	}else{
		//do nothing
		//echo "null";
	}
}
?>