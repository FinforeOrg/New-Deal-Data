<?php
/***
used by admin codes to fetch deal subtypes for a deal type
********/
include("../../include/global.php");
require_once("classes/class.transaction.php");

if(isset($_POST['deal_cat_name'])){
	//////////////////////////////////////////////////////////////////////////////////
	$deal_cat_name = $_POST['deal_cat_name'];
	/////////////////////////////////////////////////////////////////////////////////
	// Is the string length greater than 0?
	if(strlen($deal_cat_name) >0) {
		//get designation list
		$data_arr = array();
		$data_cnt = 0;
		$success = $g_trans->get_all_category_subtype1_for_category_type($deal_cat_name,$data_arr,$data_cnt);
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
				<option value="<?php echo $data_arr[$i]['subtype1'];?>"><?php echo $data_arr[$i]['subtype1'];?></option>
				<?php
			}
		}
	}else{
		//do nothing
		//echo "null";
	}
}
?>