<?php
include("../../include/global.php");
///////////////////////////////////
$company_count = 0;
$bank_count = 0;
$law_count = 0;
$rows_scanned = 0;
$deals_count = 0;
$rows_scanned = 0;
$msg_block = "";
$num_bank_cols = $_POST['num_bank_cols'];
$num_law_firm_cols = $_POST['num_law_firm_cols'];
////////////////////////////////////////////////////////
require_once '../excel_reader2.php';
$data = new Spreadsheet_Excel_Reader("../data/".$_POST['data_file'],false);
$row_count = $data->rowcount($sheet_index=0);
$col_count = $data->colcount($sheet_index=0);
/***
sng:24/may/2010
In the excel file, the column M&A target industry actually contains sector name
*******/
echo "row count ".$row_count;
$data_col_mapping = array('date_of_deal'=>1,'company_name'=>2,'deal_cat_name'=>6,'deal_subcat1_name'=>7,'deal_subcat2_name'=>8,'deal_million'=>9,'deal_million_in_local_currency'=>10,'currency'=>11,'exchange_rate'=>12,'coupon'=>13,'maturity_date'=>14,'target_company_name'=>15,'target_country'=>16,'target_sector'=>17);
//get the data, row always start with 1, and row 1 contains header, so
for($row=2;$row<=$row_count;$row++){
	echo "scanning ".$row;
	//get the deal data first
	$company_name = addslashes(trim($data->val($row,$data_col_mapping['company_name'])));
	if($company_name==""){
		//we do not know which company did this deal so skip, go to next
		continue;
	}
	/****
	get the company id from name. scan company table for company type 'company'
	It is assumed that duplicate company rows are removed, so we get the first id of the matching data
	The company name may not be there for type company, in that case, we can insert.
	***/
	$q = "select company_id from ".TP."company where type='company' and name='".$company_name."'";
	$res = mysql_query($q);
	if(!$res){
		echo "db error while checking for company id at row ".$row;
		
		exit;
	}
	//check for record
	$num = mysql_num_rows($res);
	if(0 == $num){
		$company_col_mapping = array('hq_country'=>3,'sector'=>4,'industry'=>5);
			
		$hq_country = trim($data->val($row,$company_col_mapping['hq_country']));
		$sector = trim($data->val($row,$company_col_mapping['sector']));
		$industry = trim($data->val($row,$company_col_mapping['industry']));
		//////////////////////////////////////////////////////////////////////
		//insert
		$q = "insert into ".TP."company set name='".$company_name."', type='company', hq_country='".$hq_country."', sector='".$sector."', industry='".$industry."'";
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
	//we have the company id, so get the rest of the data
	$date_of_deal = trim($data->val($row,$data_col_mapping['date_of_deal']));
	//convert this to date value
	$date_of_deal = date("Y-m-d",strtotime($date_of_deal));
	$deal_cat_name = trim($data->val($row,$data_col_mapping['deal_cat_name']));
	$deal_subcat1_name = trim($data->val($row,$data_col_mapping['deal_subcat1_name']));
	$deal_subcat2_name = trim($data->val($row,$data_col_mapping['deal_subcat2_name']));
	$deal_million = trim($data->val($row,$data_col_mapping['deal_million']));
	/***
	sng:13/may/2010
	the value in million may contain thousand separator. We assume that it is comma, and remove it
	***/
	$deal_million = str_replace(",","",$deal_million);
	/*********************************/
	$value_in_billion = $deal_million/1000;
	/***
	sng:24/may/2010
	We now have value in local currency, local currency and exchange rate
	******/
	$currency = trim($data->val($row,$data_col_mapping['currency']));
	$exchange_rate = trim($data->val($row,$data_col_mapping['exchange_rate']));
	$local_million = trim($data->val($row,$data_col_mapping['deal_million_in_local_currency']));
	$local_million = str_replace(",","",$local_million); 
	$value_in_billion_local_currency = $local_million/1000;
	
	$coupon = trim($data->val($row,$data_col_mapping['coupon']));
	$maturity_date = trim($data->val($row,$data_col_mapping['maturity_date']));
	$target_company_name = trim($data->val($row,$data_col_mapping['target_company_name']));
	$target_company_name = addslashes($target_company_name);
	$target_country = trim($data->val($row,$data_col_mapping['target_country']));
	$target_sector = trim($data->val($row,$data_col_mapping['target_sector']));
	///////////////////////////////////////////////////////////////////////////////
	//insert the deal data
	$q = "insert into ".TP."transaction set company_id='".$company_id."',value_in_billion='".$value_in_billion."',currency='".$currency."',exchange_rate='".$exchange_rate."',value_in_billion_local_currency='".$value_in_billion_local_currency."',date_of_deal='".$date_of_deal."',deal_cat_name='".$deal_cat_name."',deal_subcat1_name='".$deal_subcat1_name."',deal_subcat2_name='".$deal_subcat2_name."',coupon='".$coupon."',maturity_date='".$maturity_date."',target_company_name='".$target_company_name."',target_country='".$target_country."',target_sector='".$target_sector."'";
	$result = mysql_query($q);
	if(!$result){
		//echo mysql_error();
		echo "db error while inserting deal at row ".$row;
			
		exit;
	}
	$deal_id = mysql_insert_id();
	$deals_count++;
	//////////////////////////////////////////////////////////////////////////////////////////
	//now scan for associate bank and law firms by scanning the row
	$bank_col_start = 18;
	$bank_col_end = $bank_col_start + ($num_bank_cols-1);
	//we also need to keep track of how many banks we got for this deal, because we need to
	//set the adjusted deal value for the banks involved which is 
	//value in billion/num of banks in this deal
	//also, we need the id of the partner records so that we can update quickly
	//we will use a csv
	//////////////////////////////////////////////////////////////////////////////
	$num_banks_in_this = 0;
	$bank_partner_record_id_csv = "";
	////////////////////////////////////////////////////
	for($col=$bank_col_start;$col<=$bank_col_end;$col++){
		$bank_name = addslashes(trim($data->val($row,$col)));
		if($bank_name == ""){
			//skip
			continue;
		}
		//get the company id from name. scan company table for company type 'bank'
		$q = "select company_id from ".TP."company where type='bank' and name='".$bank_name."'";
		$res = mysql_query($q);
		if(!$res){
			echo "db error while checking for bank at row ".$row." col ".$col;
				
			exit;
		}
		//check for record
		$num = mysql_num_rows($res);
		if(0 == $num){
			//////////////////////////////////////////////////////////////////////
			//insert bank
			$q = "insert into ".TP."company set name='".$bank_name."', type='bank'";
			$result = mysql_query($q);
			if(!$result){
				echo "db error while inserting bank at row ".$row." col ".$col;
				
				exit;	
			}else{
				$bank_count++;
				//get the bank id
				$bank_id = mysql_insert_id();
					
			}
		}else{
			//get the bank data from db
			$data_row = mysql_fetch_assoc($res);
			$bank_id = $data_row['company_id'];
		}
		//////////////////////////////////////////////////////////////
		//we have the bank so, insert as associate
		$q = "insert into ".TP."transaction_partners set transaction_id='".$deal_id."', partner_id='".$bank_id."', partner_type='bank'";
		$result = mysql_query($q);
		if(!$result){
			echo "db error while inserting bank as associate at row ".$row." col ".$col;
				
			exit;		
		}
		/////////////////////////////////////////
		//bank inserted so
		//get the partner record id
		$bank_partner_record_id_csv.=",".mysql_insert_id();
		$num_banks_in_this++;
	}
	//banks inserted, now we calculate the adjusted value
	//provided there were banks in this
	if($num_banks_in_this > 0){
		$bank_adjusted_value_in_billion = $value_in_billion/$num_banks_in_this;
		$bank_partner_record_id_csv = substr($bank_partner_record_id_csv,1);
		//now we need to set this value for all the banks for this deal
		$bank_adjust_value_q = "update ".TP."transaction_partners set adjusted_value_in_billion='".$bank_adjusted_value_in_billion."' where id IN (".$bank_partner_record_id_csv.")";
		$result = mysql_query($bank_adjust_value_q);
		if(!$result){
			echo "db error while updating adjusted deal value for banks for transaction at row ".$row;
			
			exit;	
		}
	}
	///////////////////////////////////////////////////////////////////////////
	//law firms
	$law_col_start = $bank_col_end+1;
	$law_col_end = $law_col_start + ($num_law_firm_cols-1);
	////////////////////////////////////////////////////////
	//we also need to keep track of how many law firms we got for this deal, because we need to
	//set the adjusted deal value for the law firms involved which is 
	//value in billion/num of law firms in this deal
	//also, we need the id of the partner records so that we can update quickly
	//we will use a csv
	$num_law_firms_in_this = 0;
	$law_firm_partner_record_id_csv = "";
	//////////////////////////////////////////////////////////////////////////////
	for($col=$law_col_start;$col<=$law_col_end;$col++){
		$law_name = addslashes(trim($data->val($row,$col)));
		if($law_name == ""){
			//skip
			continue;
		}
		//get the company id from name. scan company table for company type 'law firm'
		$q = "select company_id from ".TP."company where type='law firm' and name='".$law_name."'";
		$res = mysql_query($q);
		if(!$res){
			echo "db error while checking for law firm id at row ".$row." col ".$col;
				
			exit;	
		}
		//check for record
		$num = mysql_num_rows($res);
		if(0 == $num){
			//////////////////////////////////////////////////////////////////////
			//insert law firm
			$q = "insert into ".TP."company set name='".$law_name."', type='law firm'";
			$result = mysql_query($q);
			if(!$result){
				echo "db error while inserting new law firm for row ".$row." col ".$col;
					
				exit;	
			}else{
				$law_count++;
				//get the law firm id
				$law_id = mysql_insert_id();
			}
		}else{
			//get the law firm data from db
			$data_row = mysql_fetch_assoc($res);
			$law_id = $data_row['company_id'];
		}
		//////////////////////////////////////////////////////////////
		//we have the law firm so, insert as associate
		$q = "insert into ".TP."transaction_partners set transaction_id='".$deal_id."', partner_id='".$law_id."', partner_type='law firm'";
		$result = mysql_query($q);
		if(!$result){
			echo "db error while inserting associate law firm for transaction at row ".$row." col ".$col;
			exit;	
		}
		/////////////////////////////////////////
		//law firm inserted so
		//get the partner record id
		$law_firm_partner_record_id_csv.=",".mysql_insert_id();
		$num_law_firms_in_this++;
	}
	//law firms inserted, now we calculate the adjusted value
	//provided there were law firms in this
	if($num_law_firms_in_this > 0){
		$law_firm_adjusted_value_in_billion = $value_in_billion/$num_law_firms_in_this;
		$law_firm_partner_record_id_csv = substr($law_firm_partner_record_id_csv,1);
		//now we need to set this value for all the law firms for this deal
		$law_firm_adjust_value_q = "update ".TP."transaction_partners set adjusted_value_in_billion='".$law_firm_adjusted_value_in_billion."' where id IN (".$law_firm_partner_record_id_csv.")";
		$result = mysql_query($law_firm_adjust_value_q);
		if(!$result){
			echo "db error while updating adjusted deal value for law firms for transaction at row ".$row;

			exit;
		}
	}
	$rows_scanned++;
}
//scanning over
//send the stats
echo $rows_scanned."|".$company_count."|".$bank_count."|".$law_count."|".$deals_count;
exit;
?>