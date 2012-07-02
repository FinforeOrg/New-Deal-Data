<?php
include("../include/global.php");
require_once ("admin/checklogin.php");
/////////////////////////////////////
$g_view['err'] = array();
$g_view['msg'] = "";
$g_view['input'] = array();
//////////////////////////////////////
?>

<?php
$rows_scanned = 0;
$mem_count = 0;
$company_count = 0;
$msg_block = "";
/////////////////////////////////////////////////////////////////////////
if(isset($_POST['action'])&&($_POST['action']=="extract_ghosts")){
	process_data();
}
//////////////////////////////
$g_view['heading'] = "Upload Ghost Members";
$g_view['content_view'] = "admin/script_upload_ghost_members_view.php";
include("admin/content_view.php");
//////////////////////////////
function process_data(){
	global $rows_scanned, $mem_count,$msg_block,$company_count;
	
	require_once 'excel_reader2.php';
	$data = new Spreadsheet_Excel_Reader("data/".$_POST['data_file'],false);
	$row_count = $data->rowcount($sheet_index=0);
	$col_count = $data->colcount($sheet_index=0);
	//echo "rows: ".$row_count;
	//return;
	///////////////////////////////////////////////////////
	
	////////////////////////////////////////////
	$data_col_mapping = array('name'=>1,'member_type'=>2,'company_name'=>3,'designation'=>4,'posting_country'=>6);
	//get the data, row always start with 1, and row 1 contains header, so
	for($row=2;$row<=$row_count;$row++){
		$mem_name = trim($data->val($row,$data_col_mapping['name']));
		//the name is not given as first name, last name so we will have to split the data, provided it
		//is not blank and then we put slashes
		if($mem_name == ""){
			continue;
		}
		//name apecified
		$mem_name_token = explode(" ",$mem_name);
		$f_name = addslashes($mem_name_token[0]);
		if(isset($mem_name_token[1])) $l_name = addslashes($mem_name_token[1]);
		else $l_name = "";
		///////////////////////////////
		$member_type = trim($data->val($row,$data_col_mapping['member_type']));
		if($member_type == ""){
			//we do not know what kind of member so skip
			continue;
		}
		//////////////////////////////////
		$company_name = addslashes(trim($data->val($row,$data_col_mapping['company_name'])));
		if($company_name==""){
			//we do not know in which company this member works so skip
			continue;
		}
		/****
		get the company id from name. scan company table for company type 'company'
		It is assumed that duplicate company rows are removed, so we get the first id of the matching data
		The company name may not be there for type company, in that case, we can insert.
		***/
		if($member_type == "banker") $company_type = "bank";
		if($member_type == "lawyer") $company_type = "law firm";
		if($member_type == "company rep") $company_type = "company";
		/*********************
		sng:5/apr/2011
		Data partners are all associated with company. Were they wanted to be assiciated with bank, they would have selected bank as member type
		**************************/
		if($member_type == 'data partner') $company_type = 'company';
		$q = "select company_id from ".TP."company where type='".$company_type."' and name='".$company_name."'";
		$res = mysql_query($q);
		if(!$res){
			echo "db error while checking for company id at row ".$row;
			
			exit;
		}
		//check for record
		$num = mysql_num_rows($res);
		if(0 == $num){
			//insert
			$q = "insert into ".TP."company set name='".$company_name."', type='".$company_type."'";
			$result = mysql_query($q);
			if(!$result){
				echo "db error while inserting new company for row ".$row;
				
				exit;
					
			}else{
				$company_count++;
				//get the company id
				$company_id = mysql_insert_id();
			}
		}else{
			//get the company data from db
			$data_row = mysql_fetch_assoc($res);
			$company_id = $data_row['company_id'];
		}
		////////////////////////////////////////////////////////////////////////////////////
		//we have the company id
		$designation = trim($data->val($row,$data_col_mapping['designation']));
		$posting_country = trim($data->val($row,$data_col_mapping['posting_country']));
		
		///////////////////////////////////////////////////////////////////////////////
		//check if thie member exists or not
		$q = "select count(*) as cnt from ".TP."member where f_name='".$f_name."' and l_name='".$l_name."'";
		$res = mysql_query($q);
		if(!$res){
			echo "db error while checking for member for row ".$row;
			exit;
		}
		$data_row = mysql_fetch_assoc($res);
		if($data_row['cnt'] > 0){
			//this member exists, skip
			continue;
		}
		//insert the member data data
		$q = "insert into ".TP."member set f_name='".$f_name."',l_name='".$l_name."',member_type='".$member_type."',company_id='".$company_id."',designation='".$designation."',posting_country='".$posting_country."',is_ghost='Y',blocked='N'";
		$result = mysql_query($q);
		if(!$result){
			//echo mysql_error();
			echo "db error while inserting member at row ".$row;
				
			exit;
		}
		
		$mem_count++;
		
		///////////////////////////////////////////////
		$rows_scanned++;
	}
	//scanning over
}
?>