<?php
/***
used by admin codes to fetch industries for a sector
********/
include("../../include/global.php");
require_once("classes/class.company.php");

if(isset($_POST['sector'])){
	//////////////////////////////////////////////////////////////////////////////////
	$sector = $_POST['sector'];
	/////////////////////////////////////////////////////////////////////////////////
	// Is the string length greater than 0?
	if(strlen($sector) >0) {
		//get designation list
		$data_arr = array();
		$data_cnt = 0;
		$success = $g_company->get_all_industry_for_sector($sector,$data_arr,$data_cnt);
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
				<option value="<?php echo $data_arr[$i]['industry'];?>"><?php echo $data_arr[$i]['industry'];?></option>
				<?php
			}
		}
	}else{
		//do nothing
		//echo "null";
	}
}
?>