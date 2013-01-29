<?php
/*******************
sng:22/jan/2013

This is specifically used to fetch equity IPO and Additional (SPO) deal data from co-codes.
The code at co-codes get these from NASDAQ

sng:29/jan/2013
deal subtype Additional is Secondaries
deal subtype IPO is IPOs
deal subtype Equity is Common Equity
************/
require(dirname(dirname(__FILE__))."/include/minimal_bootstrap.php");

$conn = mysql_connect($g_config['db_host'],$g_config['db_user'],$g_config['db_password']);
if($conn===false){
	print_r("cannot connect to db\r\n");
	die();
}
mysql_select_db($g_config['db_name'],$conn);

print_r("started\r\n");

print_r("fetching deal data file from co-codes");
$data_source = "http://co-codes.com/store/dealdata_nasdaq.csv";
$data_destination = FILE_PATH."/from_co-codes/deals.csv";

$ok = fetch_and_store_remote_file($data_source,$data_destination);
if(!$ok){
	$master->set_status_note($worker_name,"error fetching co-codes deal data file\r\n");
	exit;
}
print_r("fetched co-codes deal data file\r\n");

$msg = "";
$ok = process_deal_file($data_destination);
if(!$ok){
	print_r("Error processing deal data file\r\n");
	return;
}
print_r("processed deal data file");
unlink($data_destination);
print_r("removed co-codes deal data file");
/**********************************************************************************/
?>
<?php
function process_deal_file($source){
	
	$scanned = 0;
	$error = 0;
	
	$r_handle = fopen($source,'r');
	if(!$r_handle){
		return false;
	}
	while (($csv_data = fgetcsv($r_handle, 10240, ',', '"','|')) !== false) {
		echo "scanning ".$scanned."\r\n";
		$ok = process_deal_record($csv_data);
		if(!$ok){
			$error++;
		}
		$scanned++;
	}
	fclose($r_handle);
	echo "\r\n done scanned ".$scanned." error ".$error."\r\n";
	return true;
}
?>
<?php
/************
Col_0: "i9501",
Col_1: CCS Identifier "SEC001093567",
Col_2: Old CCS Identifier "",
Col_3: Short name "1 800 Contacts",
Col_4: Country code "US",
Col_5: Country name "UNITED STATES",
Col_6: Industry "",
Col_7: Supersector "",
Col_8: Sector "",
Col_9: Subsector "",
Col_10: CIK "0001050122",
Col_11: RIC "",
Col_12: Bloomberg Ticker "",
Col_13: ISIN "",
Col_14: SEDOL "",
Col_15: Google Ticker "",
Col_16: Date when this company record inserted/updated (NOT when the deal record was inserted/updated "2012-10-05 10:39:36",
Col_17: Date in yyyy-mm-dd when the deal took place "1998-02-10",
Col_18: Deal size in million USD "27500000.00",
Col_19: Type IPO or SPO "IPO",
Col_20: Lead underwriters "Morgan Keegan and Co., Inc;McDonald and Co. Securities, Inc;",
Col_21: Company counsel "Kirkland and Ellis;",
Col_22: Underwriters counsel (these underwriters and councels should be separated by ';') "Squire, Sanders and Dempsey L.L.P;",
Col_23: Date when this deal record was inserted/updated (NOT when the company record was inserted/updated) "2013-01-18 10:00:30"
*************/
function process_deal_record($row_data){
	global $conn;
	
	$num_cols_expected = 24;
	$col_cnt = count($row_data);
	if($col_cnt!==$num_cols_expected){
		return false;
	}
	/************************************************************************
	company data including identifier
	****/
	$company_data = array();
	
	$company_data['company_name'] = trim($row_data[3]);
	$company_data['country_code'] = trim($row_data[4]);
	/*****
	we use this to get our own country name from our own lookup table
	***/
	/*********************************************
	sector and industry
	******/
	//level 1
	$icb_industry = trim($row_data[6]);
	//level 2
	$icb_supersector = trim($row_data[7]);
	//level 3
	$icb_sector = trim($row_data[8]);
	//level 4
	$icb_subsector = trim($row_data[9]);
	
	/******************
	we are interested in level 1 and level 3
	However, the way we have arranged our data, our sector is icb_industry and industry is icb_sector
	******************/
	$company_data['sector'] = $icb_industry;
	$company_data['industry'] = $icb_sector;
	/****************************
	identifiers
	************/
	$company_data['cik'] = trim($row_data[10]);
	$company_data['ric'] = trim($row_data[11]);
	$company_data['bloomberg_code'] = trim($row_data[12]);
	$company_data['isin'] = trim($row_data[13]);
	$company_data['sedol'] = trim($row_data[14]);
	$company_data['google_code'] = trim($row_data[15]);
	/****************************************************************/
	$deal_data = array();
	
	$deal_data['date_of_deal'] = trim($row_data[17]);
	if($deal_data['date_of_deal']==""){
		$deal_data['date_of_deal'] = "0000-00-00";
	}
	/********
	Since we have only one date, we assume it is date when the deal was closed and set in_calculation accordingly
	***********/
	$deal_data['date_closed'] = $deal_data['date_of_deal'];
	
	$deal_data['currency'] = "USD";
	$deal_data['deal_cat_name'] = "Equity";
	$deal_data['deal_subcat1_name'] = "Common Equity";
	
	$deal_data_deal_type = trim($row_data[19]);
	if($deal_data_deal_type=="IPO"){
		$deal_data['deal_subcat2_name'] = "IPOs";
	}else if($deal_data_deal_type=="SPO"){
		$deal_data['deal_subcat2_name'] = "Secondaries";
	}else{
		$deal_data['deal_subcat2_name'] = "";
	}
	
	$deal_data_deal_value = trim($row_data[18]);
	if($deal_data_deal_value==""){
		/***
		treat it as undisclosed
		***/
		$deal_data['value_in_billion'] = "0.0";
		$deal_data['value_range_id'] = 0;
	}else{
		$deal_data_deal_value = (float)($deal_data_deal_value);
		if($deal_data_deal_value=="0.0"){
			/***
			treat it as undisclosed
			***/
			$deal_data['value_in_billion'] = "0.0";
			$deal_data['value_range_id'] = 0;
		}else{
			/********
			convert to billion
			*********/
			$deal_data['value_in_billion'] = $deal_data_deal_value/1000000000;
			$deal_data['value_range_id'] = 0;
			//this takes value in million
			$ok = front_get_value_range_id_from_value($deal_data_deal_value/1000000,$deal_data['value_range_id']);
			if(!$ok){
				return false;
			}
		}
	}
	$date_time_now = date("Y-m-d H:i:s");
	/********
	for cron jobs, we assume admin has not verified the deal
	but we activate the deal and consider that in calculation
	********/
	$deal_data['admin_verified'] = 'n';
	$deal_data['is_active'] = 'y';
	
	if($deal_data['date_closed']!="0000-00-00"){
		$deal_data['in_calculation'] = 1;
	}else{
		$deal_data['in_calculation'] = 0;
	}
	
	$deal_data['last_edited'] = $date_time_now;
	$deal_data['added_on'] = $date_time_now;
	
	/*************************
	add the deal data
	************************/
	$q = "insert into zzz_tombstone_transaction set value_in_billion='".$deal_data['value_in_billion']."'
	,value_range_id='".$deal_data['value_range_id']."'
	,currency='".$deal_data['currency']."'
	,date_of_deal='".$deal_data['date_of_deal']."'
	,deal_cat_name='".$deal_data['deal_cat_name']."'
	,deal_subcat1_name='".$deal_data['deal_subcat1_name']."'
	,deal_subcat2_name='".$deal_data['deal_subcat2_name']."'
	,added_on='".$deal_data['added_on']."'
	,last_edited='".$deal_data['added_on']."'
	,admin_verified='".$deal_data['admin_verified']."'
	,is_active='".$deal_data['is_active']."'
	,in_calculation='".$deal_data['in_calculation']."'";
	
	$res = mysql_query($q,$conn);
	if($res===false){
		return false;
	}
	$deal_data['deal_id'] = mysql_insert_id($conn);
	/*******
	deal added, add extra record
	***********/
	$q = "insert into zzz_tombstone_transaction_extra_detail set transaction_id = '".$deal_data['deal_id']."'";
	/*****
	Since we have a single date, it is considered as closing date. If that date is 0, we assume that only 
	announced date was set (although in that case date_announced is merely 0
	***********/
	if($deal_data['date_closed']!="0000-00-00"){
		$q.=",date_closed='".$deal_data['date_closed']."'";
	}
	$res = mysql_query($q,$conn);
	if($res===false){
		return false;
	}
	/*********
	never mind note, source
	come to companies.
	
	Here we only have a single company as participant.
	We add the id of the company.
	We have no role
	*******************/
	$deal_company_id = 0;
	
	$ok = get_company_id($company_data,$deal_company_id);
	if(!$ok){
		/************
		cannot get the company id. Is this a big deal? well, we cannot add it as participant
		************/
	}else{
		if($deal_company_id!=0){
			$q = "insert into zzz_tombstone_transaction_companies set transaction_id='".$deal_data['deal_id']."',company_id='".$deal_company_id."'";
			$res = mysql_query($q,$conn);
			if($res===false){
				/**********
				well, too bad
				********/
			}
		}
	}
	/************
	partners
	partner roles
	adjustd value
	
	banks / law firms
	**********************/
	$firm_data = array();
	$firm_data['lead_underwriter'] = trim($row_data[20]);
	$firm_data['company_councel'] = trim($row_data[21]);
	$firm_data['underwriter_councel'] = trim($row_data[22]);
	$ok = process_firms($firm_data,$deal_data['deal_id'],$deal_data['value_in_billion']);
	/******
	never mind if not ok
	******/
	return true;
	
}
?>
<?php
/****************
Is the company there in the company master list? If so, get the company id,
else add the company and then get the company id

Now the problem is, how do we check? We use combination of
name
country code
sector
industry
***********************/
function get_company_id($data_arr,&$company_id){
	global $conn;
	
	$company_id = 0;
	/*******************************************************
	now we see if we have this record already
	*******/
	$q = "select company_id from zzz_tombstone_company where name='".mysql_real_escape_string($data_arr['company_name'])."' and type='company' and hq_country_code='".mysql_real_escape_string($data_arr['country_code'])."' and sector='".mysql_real_escape_string($data_arr['sector'])."' and industry='".mysql_real_escape_string($data_arr['industry'])."'";
	$res = mysql_query($q,$conn);
	if($res===false){
		/**********
		better bail out than insert duplicate
		*****/
		return false;
	}
	$res_count = mysql_num_rows($res);
	if(0==$res_count){
		/*****************
		Not found, create the company, including the identifiers
		
		we first get the country from country code
		21/dec/2012
		The country code can be blank
		**************/
		$country_name = "";
		if($data_arr['country_code']!=""){
			$q = "select name from ".TP."country_master where iso_3166_1_alpha_2_code='".mysql_real_escape_string($data_arr['country_code'])."'";
			$res = mysql_query($q,$conn);
			if($res===false){
				/******
				nothing to do
				******/
			}else{
				$res_count = mysql_num_rows($res);
				if(0==$res_count){
					/********
					nothing to do
					*********/
				}else{
					$rec = mysql_fetch_assoc($res);
					$country_name = $rec['name'];
				}
			}
		}else{
			/*********
			country code not given so we cannot find country name, leave it as blank
			******/
			$country_name = "";
		}
		/***************************************************/
		$q = "insert into zzz_tombstone_company set name='".mysql_real_escape_string($data_arr['company_name'])."',type='company',hq_country_code='".mysql_real_escape_string($data_arr['country_code'])."',hq_country='".mysql_real_escape_string($country_name)."',sector='".mysql_real_escape_string($data_arr['sector'])."',industry='".mysql_real_escape_string($data_arr['industry'])."'";
		$res = mysql_query($q,$conn);
		if($res===false){
			/*****
			could not insert company data
			****/
			return false;
		}
		/********
		get the id
		******/
		$company_id = mysql_insert_id($conn);
	}else{
		/******
		company record found
		******/
		$rec = mysql_fetch_assoc($res);
		$company_id = $rec['company_id'];
	}
	/********************************************
	identifiers. Some may be blank
	ISIN 1
	Bloomberg Ticker 2
	Reuters Instrument Code 3
	Google Finance Code 4
	Central Index Key 5
	SEDOL 6
	THIS IS NOT THE WAY TO DO THIS. USE LOOKUP TABLE
	**************/
	if($company_id!=0){
		$id_q = "";
		
		if($data_arr['cik']!=""){
			$id_q.=",('".$company_id."','5','".mysql_real_escape_string($data_arr['cik'])."')";
		}
		
		if($data_arr['ric']!=""){
			$id_q.=",('".$company_id."','3','".mysql_real_escape_string($data_arr['ric'])."')";
		}
		
		if($data_arr['bloomberg_code']!=""){
			$id_q.=",('".$company_id."','2','".mysql_real_escape_string($data_arr['bloomberg_code'])."')";
		}
		
		if($data_arr['isin']!=""){
			$id_q.=",('".$company_id."','1','".mysql_real_escape_string($data_arr['isin'])."')";
		}
		
		if($data_arr['sedol']!=""){
			$id_q.=",('".$company_id."','6','".mysql_real_escape_string($data_arr['sedol'])."')";
		}
		
		if($data_arr['google_code']!=""){
			$id_q.=",('".$company_id."','4','".mysql_real_escape_string($data_arr['google_code'])."')";
		}
		
		if($id_q!=""){
			$id_q = substr($id_q,1);
			$q = "insert into zzz_tombstone_company_identifiers (company_id,identifier_id,`value`) values ".$id_q;
			$res = mysql_query($q,$conn);
				if($res===false){
				/*****
				could not insert identifiers, not a big deal
				****/
			}
		}
	}
	return true;
}

function process_firms($firm_data_arr,$deal_id,$deal_value_in_billion){
	global $conn;
	
	/*******************************
	Roles for banks
	Lead Underwriter = Bookrunner [role id 2]
	
	Underwriter = Co-lead manager
	
	for law firms
	Company Counsel = Advisor to Company [role id 14]
	Underwriter Counsel  = Advisor to Banks [role id 16]
	*********************/
	/****
	for multi row partner insert query
	*****/
	$q = "";
	/*************************
	How many banks?
	We only have Lead underwriters so only these are banks
	**************************************/
	$partner_type = "bank";
	$transaction_id = $deal_id;
	
	/*********
	build the bank array
	***********/
	$banks = array();
	$banks_offset = 0;
	
	$temp_banks_tokens = explode(";",$firm_data_arr['lead_underwriter']);
	$temp_banks_tokens_count = count($temp_banks_tokens);
	for($i=0;$i<$temp_banks_tokens_count;$i++){
		$bank_name = trim($temp_banks_tokens[$i]);
		if(($bank_name!="")&&($bank_name!="--")){
			$partner_id = 0;
			$ok = get_firm_id($bank_name,$partner_type,$partner_id);
			if(!$ok){
				continue;
			}
			if($partner_id!=0){
				$banks[$banks_offset] = array();
				$banks[$banks_offset]['partner_id'] = $partner_id;
				$banks[$banks_offset]['role_id'] = 2;
				$banks_offset++;
			}
		}
	}
	
	
	/*******************
	Now see how many banks we managed to enter and based on that calculate the adjusted value
	This may be 0
	******************/
	$num_banks = count($banks);
	if($num_banks > 0){
		$adjusted_value_in_billion = $deal_value_in_billion/$num_banks;
		/******************
		Now we loop the bank arr and create the multi row query for partners
		****************/
		
		for($i=0;$i<$num_banks;$i++){
			$q.=",('".$transaction_id."','".$banks[$i]['partner_id']."','".$banks[$i]['role_id']."','".$partner_type."','".$adjusted_value_in_billion."')";
		}
	}
	
	
	/*************************
	How many law firms?
	We have Company counsel and Underwriters counsel so two sets of data
	**************************************/
	$partner_type = "law firm";
	$transaction_id = $deal_id;
	
	/*********
	build the law firm array
	***********/
	$law_firms = array();
	$law_firms_offset = 0;
	
	$temp_law_firms_tokens = explode(";",$firm_data_arr['company_councel']);
	$temp_law_firms_tokens_count = count($temp_law_firms_tokens);
	for($i=0;$i<$temp_law_firms_tokens_count;$i++){
		$law_firm_name = trim($temp_law_firms_tokens[$i]);
		if(($law_firm_name!="")&&($law_firm_name!="--")){
			$partner_id = 0;
			$ok = get_firm_id($law_firm_name,$partner_type,$partner_id);
			if(!$ok){
				continue;
			}
			if($partner_id!=0){
				$law_firms[$law_firms_offset] = array();
				$law_firms[$law_firms_offset]['partner_id'] = $partner_id;
				$law_firms[$law_firms_offset]['role_id'] = 14;
				$law_firms_offset++;
			}
		}
	}
	
	$temp_law_firms_tokens = explode(";",$firm_data_arr['underwriter_councel']);
	$temp_law_firms_tokens_count = count($temp_law_firms_tokens);
	for($i=0;$i<$temp_law_firms_tokens_count;$i++){
		$law_firm_name = trim($temp_law_firms_tokens[$i]);
		if(($law_firm_name!="")&&($law_firm_name!="--")){
			$partner_id = 0;
			$ok = get_firm_id($law_firm_name,$partner_type,$partner_id);
			if(!$ok){
				continue;
			}
			if($partner_id!=0){
				$law_firms[$law_firms_offset] = array();
				$law_firms[$law_firms_offset]['partner_id'] = $partner_id;
				$law_firms[$law_firms_offset]['role_id'] = 16;
				$law_firms_offset++;
			}
		}
	}
	/*******************
	Now see how many law firms we managed to enter and based on that calculate the adjusted value
	This can be 0
	******************/
	$num_law_firms = count($law_firms);
	if($num_law_firms > 0){
		$adjusted_value_in_billion = $deal_value_in_billion/$num_law_firms;
		/******************
		Now we loop the law firm arr and create the multi row query for partners
		****************/
		for($i=0;$i<$num_law_firms;$i++){
			$q.=",('".$transaction_id."','".$law_firms[$i]['partner_id']."','".$law_firms[$i]['role_id']."','".$partner_type."','".$adjusted_value_in_billion."')";
		}
	}
	
	if($q!=""){
		$q = substr($q,1);
		$q = "insert into zzz_tombstone_transaction_partners (transaction_id,partner_id,role_id,partner_type,adjusted_value_in_billion) values ".$q;
		
		$res = mysql_query($q,$conn);
		if($res===false){
			return false;
		}
		return true;
	}
	/**********
	nothing to add
	************/
	return true;
}
?>
<?php
function get_firm_id($firm_name,$firm_type,&$firm_id){
	global $conn;
	
	/************
	we see if we already have this firm. If so, we return the id else we create the firm and then return the id
	************/
	$q = "select company_id from zzz_tombstone_company where name='".mysql_real_escape_string($firm_name)."' and type = '".$firm_type."'";
	$res = mysql_query($q,$conn);
	if(!$res){
		return false;
	}
	$res_count = mysql_num_rows($res);
	if(0==$res_count){
		/*******
		we need to create
		********/
		$q = "insert into zzz_tombstone_company set name='".mysql_real_escape_string($firm_name)."', type = '".$firm_type."'";
		$res = mysql_query($q,$conn);
		if($res===false){
			return false;
		}
		$firm_id = mysql_insert_id($conn);
	}else{
		/****
		found
		***/
		$rec = mysql_fetch_assoc($res);
		$firm_id = $rec['company_id'];
	}
	return true;
}
?>
<?php
/*************
fetch the remote file from 'source' and store it in 'destination'
Using fopen instead of curl as curl is not installed in the remote server
*********/
function fetch_and_store_remote_file($source,$destination){
	$fp = fopen($source,"rb");
	$local = fopen($destination,"wb");
	
	if($fp&&$local){
		while(!feof($fp)){
			fwrite($local,fread($fp, 1024*8));
			flush();
			/*********
			check connection status
			0 - normal
			1 - aborted
			2 - timeout
			************/
			if(connection_status()!=0) {
				fclose($fp);
				fclose($local);
				return false;
			}
		}
		fclose($fp);
		fclose($local);
		return true;
	}else{
		//error opening file
		return false;
	}
}

/**********
see nifty_functions.php
************/
function clean_filename($name){
	$clean = preg_replace("/[^a-zA-Z0-9\.]*/","",$name);
	return $clean;
}

/*********************************
17/nov/2011
A funciton to get the file extension
***********************************/
function get_file_extension($filename){
	$ext = "";
	$pos = strrpos($filename,".");
	if($pos === false){
		return $ext;
	}
	$ext = substr($filename,$pos+1,strlen($filename));
	return $ext;
}
?>
<?php
/************
sng:18/feb/2012
Given a deal value in million, we should be able to get the deal_value_range_id
For the special value of 0, the range id is 0 (undefined)

see class deal_support
*************/
function front_get_value_range_id_from_value($value_in_million,&$value_range_id){
	global $conn;
	
	if($value_in_million == 0){
		$value_range_id = 0;
		return true;
	}
	
	$q = "select value_range_id,lower_value_limit_in_million from ".TP."transaction_value_range_master order by lower_value_limit_in_million desc";
	$res = mysql_query($q,$conn);
	
	if(!$res){
		return false;
	}
	
	$res_count = mysql_num_rows($res);
	$slabs = array();
	
	for($i=0;$i<$res_count;$i++){
		$slabs[] = mysql_fetch_assoc($res);
	}
	
	$slab_count = $res_count;
	
	$value_range_id = 0;
	for($i=0;$i<$slab_count;$i++){
		if($value_in_million >= $slabs[$i]['lower_value_limit_in_million']){
			$value_range_id = $slabs[$i]['value_range_id'];
			break;
		}
	}
	
	return true;
}
?>