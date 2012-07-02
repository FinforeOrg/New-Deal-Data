<?php
/***
/***
sng: 25/oct/2010
used by codes to fetch company list so that hint can be shown
The codes that call this requirs the user to be logged in, so I keep a check here also.
********/
include("../include/global.php");
require_once("classes/class.account.php");
///////////////
if(!$g_account->is_site_member_logged()){
	echo "You need to login first";
	return;
}
require_once("classes/class.company.php");
require_once("classes/class.magic_quote.php");
if(isset($_POST['search_string'])){
	$search_string = $g_mc->view_to_db($_POST['search_string']);
	//////////////////////////////////////////////////////////////////////////////////
	//given member type, we set company type
	$member_type = $_POST['type'];
	if($member_type == 'banker') $company_type = 'bank';
	else if($member_type == 'lawyer') $company_type = 'law firm';
	else if($member_type == 'company rep') $company_type = 'company';
	/*********************
	sng:5/apr/2011
	Data partners are all associated with company. Were they wanted to be assiciated with bank, they would have selected bank as member type
	**************************/
	else if($member_type == 'data partner') $company_type = 'company';
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