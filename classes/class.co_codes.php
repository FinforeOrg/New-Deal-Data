<?php
/**********************
sng:22/jan/2013

This encapsulates the co-codes API. We use this to get data from co-codes.com

dependency
include/minimal_bootstrap.php
classes/class.db.php [definition loaded config set in minimal bootstrap, we get the config and create the obj]
classes/class.debug.php [the definition is included in minimal bootstrap, we just create the obj here]
classes/class.image_util.php [we load this one]
***********************/
class co_codes{
	
	private static $co_codes_url = "http://co-codes.com/";
	private static $full_company_file = "store/dealdata_ccsid.csv";
	private static $full_company_file_local_destination = "/from_co-codes/companies.csv";
	
	private static $num_cols_in_company_csv = 23;
	
	private static $allowed_img_extensions = array("gif","png","jpg","jpeg");
	
	private static $img_obj;
	
	private static $debug;
	
	private $db;
	
	private $company_obj;
	
	
	private function __construct(){
	}
	
	public static function create(){
		self::$debug = new debug(FILE_PATH."/cron/log.txt");
		
		global $g_config;
		$temp = db::create($g_config['db_host'],$g_config['db_user'],$g_config['db_password'],$g_config['db_name']);
		if($temp===false){
			self::$debug->print_r("cannot connect to database\r\n");
			return false;
		}
		$obj = new co_codes();
		$obj->db = $temp;
		
		$company_obj = company_proxy::create();
		
		if(!$company_obj){
			self::$debug->print_r("cannot create company proxy\r\n");
			return false;
		}
		$obj->company_obj = $company_obj;
		
		
		
		require_once(FILE_PATH."/classes/class.image_util.php");
		self::$img_obj = new image_util();
		
		return $obj;
	}
	
	public function get_all_company_data(){
		/********
		fetch the csv file
		
		I think this class should know where to put the fetched file. I mean, today it is a csv file, tomorrow it could be different.
		The caller should not know that a file is being fetched and stored
		**********/
		self::$debug->print_r("fetching company data file from co-codes\r\n");
		
		$source = self::$co_codes_url.self::$full_company_file;
		$destination = FILE_PATH.self::$full_company_file_local_destination;
		
		$ok = self::fetch_and_store_remote_file($source,$destination);
		if(!$ok){
			self::$debug->print_r("error fetching co-codes company data file\r\n");
			return false;
		}
		self::$debug->print_r("fetched co-codes company data file\r\n");
		/***************
		check integrity of the downloaded file
		**************/
		$local_hash = self::get_hashcode_of_local_file($destination);
		$remote_hash = $this->get_hashcode_of_file(self::$full_company_file);
		if($local_hash!==$remote_hash){
			self::$debug->print_r("file mangled during download\r\n");
			return false;
		}
		self::$debug->print_r("verified integrity of the file\r\n");
		/****************
		now process the csv file
		*****************/
		$ok = self::process_csv($destination,array($this,"process_row_data_for_company"));
		if(!$ok){
			self::$debug->print_r("error processing the file\r\n");
			return false;
		}
		self::$debug->print_r("processed the file\r\n");
		/*******************
		after the parsing, we remove the downloaded file. No need to clutter the server
		********************/
		unlink($destination);
		self::$debug->print_r("removed the company data file\r\n");
		/**********
		now transfer
		***********/
		$ok = $this->sync_company_data();
		if(!$ok){
			self::$debug->print_r("error importing the company data\r\n");
			return false;
		}
		return true;
	}
	
	public function get_all_equity_deal_data(){
		return false;
	}
	
	public function get_hashcode_of_file($remote_file_path_name){
		$hash_source = self::$co_codes_url."dev/info/obj_info.php?file=".$remote_file_path_name."&property=hash";
		$hash_p = fopen($hash_source,"r");
		if($hash_p){
			$hash_code = fread($hash_p,1024*16);
			/****
			This code just return a plain text containing the sha1 hash code
			*****/
			$expected_hash_code = trim($hash_code);
			return $expected_hash_code;
		}else{
			return false;
		}
	}
	
	
	
	private function process_row_data_for_company($data_array){
		/************
		first we make sure that we have the required number of columns
		****************/
		$col_cnt = count($data_array);
		if($col_cnt!==self::$num_cols_in_company_csv){
			self::$debug->print_r("invalid number of columns\r\n");
			return false;
		}
		$co_code_data_arr = array();
		/****************************************
		Col_0: CCS Identifier "SEC000940062"
		If this starts with CCS, it means it does not appear in SEC (securities and exchange commission) database, else it starts with SEC
		
		The current co code ID is so important in duplicate check that if it is not found, we raise error
		****************/
		if(!isset($data_array[0])){
			self::$debug->print_r("co-codes unique identifier not specified\r\n");
			return false;
		}
		$co_code_data_arr['curr_co_codes_id'] = trim($data_array[0]);
		if(""===$co_code_data_arr['curr_co_codes_id']){
			self::$debug->print_r("co-codes unique identifier not specified\r\n");
			return false;
		}
		
		/******************
		Col_1: Old CCS Identifier - in case it has been changed because a company appeared in SEC ""
		****************/
		if(!isset($data_array[1])){
			$co_code_data_arr['prev_co_codes_id'] = "";
		}else{
			$co_code_data_arr['prev_co_codes_id'] = trim($data_array[1]);
		}
		
		/************************
		Col_2: Short name "1ST FINANCIAL SERVICES"
		***********************/
		if(!isset($data_array[2])){
			$co_code_data_arr['company_short_name'] = "";
		}else{
			$co_code_data_arr['company_short_name'] = trim($data_array[2]);
		}
		if(""===$co_code_data_arr['company_short_name']){
			self::$debug->print_r("company name not specified\r\n");
			return false;
		}
		/**************************
		Col_3: Full name in SEC "1st Financial Services CORP"
		Col_4: Full name in Reuters "1st Financial Services Corporation"
		Col_5: Full name in Bloomberg "1st Financial Services Corp (FFIS)"
		Col_6: Full name in STOXX ""
		Col_7: Full name in LSE ""
		Col_8: Full name in Google ""
		**********************************/
		if(!isset($data_array[3])){
			$co_code_data_arr['company_name_sec'] = "";
		}else{
			$co_code_data_arr['company_name_sec'] = trim($data_array[3]);
		}
		
		if(!isset($data_array[4])){
			$co_code_data_arr['company_name_reuters'] = "";
		}else{
			$co_code_data_arr['company_name_reuters'] = trim($data_array[4]);
		}
		
		if(!isset($data_array[5])){
			$co_code_data_arr['company_name_bloomberg'] = "";
		}else{
			$co_code_data_arr['company_name_bloomberg'] = trim($data_array[5]);
		}
		
		if(!isset($data_array[6])){
			$co_code_data_arr['company_name_stoxx'] = "";
		}else{
			$co_code_data_arr['company_name_stoxx'] = trim($data_array[6]);
		}
		
		if(!isset($data_array[7])){
			$co_code_data_arr['company_name_lse'] = "";
		}else{
			$co_code_data_arr['company_name_lse'] = trim($data_array[7]);
		}
		
		if(!isset($data_array[8])){
			$co_code_data_arr['company_name_google'] = "";
		}else{
			$co_code_data_arr['company_name_google'] = trim($data_array[8]);
		}
		/****************
		Col_9: Logo "http://co-codes.com/store/logos/0001434743.jpg"
		****************/
		if(!isset($data_array[9])){
			$co_code_data_arr['logo_url'] = "";
		}else{
			$co_code_data_arr['logo_url'] = trim($data_array[9]);
		}
		/***************************************
		Col_10: Country code "US"
		Col_11: Country name "USA"
		
		country code and country
		Since the co-codes country name is not the way we want, we will only use the country code
		*****************************************/
		if(!isset($data_array[10])){
			$co_code_data_arr['country_code'] = "";
		}else{
			$co_code_data_arr['country_code'] = trim($data_array[10]);
		}
		/*****************************************
		Col_12: Industry "Financials" (need)
		Col_13: Supersector "Banks"
		Col_14: Sector "Banks" (need)
		Col_15: Subsector ""
		
		However, the way we have arranged our data, our sector is icb_industry and industry is icb_sector
		**********************************/
		if(!isset($data_array[12])){
			$icb_industry = "";
		}else{
			$icb_industry = trim($data_array[12]);
		}
		
		if(!isset($data_array[14])){
			$icb_sector = "";
		}else{
			$icb_sector = trim($data_array[14]);
		}
		
		$co_code_data_arr['sector'] = $icb_industry;
		$co_code_data_arr['industry'] = $icb_sector;
		/*********************************************
		Col_16: CIK "0001434743"
		Col_17: RIC "FFIS.OB"
		Col_18: Bloomberg Ticker "FFIS:US"
		Col_19: ISIN ""
		Col_20: SEDOL ""
		Col_21: Google Ticker ""
		****************************************/
		if(!isset($data_array[16])){
			$co_code_data_arr['cik'] = "";
		}else{
			$co_code_data_arr['cik'] = trim($data_array[16]);
		}
		
		if(!isset($data_array[17])){
			$co_code_data_arr['ric'] = "";
		}else{
			$co_code_data_arr['ric'] = trim($data_array[17]);
		}
		
		if(!isset($data_array[18])){
			$co_code_data_arr['bloomberg_code'] = "";
		}else{
			$co_code_data_arr['bloomberg_code'] = trim($data_array[18]);
		}
		
		if(!isset($data_array[19])){
			$co_code_data_arr['isin'] = "";
		}else{
			$co_code_data_arr['isin'] = trim($data_array[19]);
		}
		
		if(!isset($data_array[20])){
			$co_code_data_arr['sedol'] = "";
		}else{
			$co_code_data_arr['sedol'] = trim($data_array[20]);
		}
		
		if(!isset($data_array[21])){
			$co_code_data_arr['google_code'] = "";
		}else{
			$co_code_data_arr['google_code'] = trim($data_array[21]);
		}
		/***********************
		Col_22: Date when updated "2012-10-05 10:55:15"
		***************************************************************/
		if(!isset($data_array[22])){
			$co_code_data_arr['co_code_timestamp'] = "";
		}else{
			$co_code_data_arr['co_code_timestamp'] = trim($data_array[21]);
		}
		/**********
		since we are storing datetime, we check if this is blank. If so, we convert it
		**************/
		if($co_code_data_arr['co_code_timestamp']===""){
			$co_code_data_arr['co_code_timestamp'] = "0000-00-00 00:00:00";
		}
		/**********************
		The full company name, we take it as the SEC name
		**************/
		$co_code_data_arr['full_name'] = $co_code_data_arr['company_name_sec'];
		
		/***********************************
		We request the company data each day. Problem is, how do we know that we have already stored the company record?
		This is where the the concept of co-codes unique identifier helps us.
		Each record in the company csv is tagged with this unique id along with a timestamp (yyyy-mm-dd hh:mm:ss).
		We check these against our table and decide whether to ignore the record, insert the record, update the record.
		*****************************/
		$action = $this->check_if_company_record_exists($co_code_data_arr['curr_co_codes_id'],$co_code_data_arr['prev_co_codes_id'],$co_code_data_arr['co_code_timestamp']);
		if($action===false){
			/*******
			the called function must have logged the error
			*********/
			return false;
		}
		if("ignore"==$action){
			return true;
		}
		if("update"==$action){
			$ok = $this->insert_update_company_record($co_code_data_arr,true);
			if(!$ok){
				return false;
			}else{
				return true;
			}
		}
		if("insert"==$action){
			$ok = $this->insert_update_company_record($co_code_data_arr,false);
			if(!$ok){
				return false;
			}else{
				return true;
			}
		}
		/**********************
		Either we can update the real tables or we can put the data in intermediate table and then update the real tables.
		If we use intermediate table, we can store the timestamp. That way, in the next parsing, we do not bother with data rows
		that has not changed (the fetching of logo, creating thumb, inserting in table all takes time).
		******************************/
		return true;
	}
	
	/*******************************
	co-codes.com unique identifier
	If we have a valid CIK, we use SECXXXXXXXXXXX
	If we do not have valid CIK, we use CCSXXXXXXXXX
	Say Pendaargast Inc has no CIK. We assign it the key CCS00000078.
	Four months later the company gets the CIK 667.
	Change the co-codes identifier to SEC00000667.
	But how we will know of the change?
	
	Sounds like the CSV needs two new columns.
	1. Current Unique Identifier 
	2. Old Unique Identifier IF there has been a change. 
	Column 2 would be blank if there was no change.
	
	My side of the parsing
	
	
	1) Read current identifier
	1.1) Found in our table. So we do not bother with the old identifier column. We check the timestamp etc
	1.2) Not found in our table. Read the old identifier column
	1.2.1) It is blank. This means we are dealing with totally new record. We insert the company data.
	1.2.2) It is non blank. We check if the old one is there in the record.
	1.2.2.1) It is there. This means the identifier changed. We update the identifier in our table.
	We also check the timestamp and update rest of the record or skip updating rest of the record.
	1.2.2.2) It is not there. This means, we somehow missed the original record. We just add the record but we store the current identifier.
	
	Return: false on error
	"ignore" if the record is to be skipped
	"update" if the record is to be updated
	"insert" if the record is to be inserted
	************************************/
	private function check_if_company_record_exists($curr_co_codes_id,$prev_co_codes_id,$timestamp){
		/************
		look for the current identifier, also get the timestamp if found
		**************/
		$q = "select curr_co_codes_id,co_code_timestamp from ".TP."co_codes_company where curr_co_codes_id='".$this->db->escape_string($curr_co_codes_id)."'";
		$ok = $this->db->select_query($q);
		if(!$ok){
			return false;
		}
		$row_count = $this->db->row_count();
		/***************
		We already checked that the $curr_co_codes_id is non-blank.
		So either there is a hit or there is a  miss
		*******************/
		if($row_count !== 0){
			/*******
			this record exists in the intermediate table
			***********/
			$stored_rec = $this->db->get_row();
			/***********
			This record is there. Check the stored timestamp
			*************/
			if($stored_rec['co_code_timestamp']==$timestamp){
				/*********
				in the csv, the record is same as what we have in the intermediate database (as the timestamp is same)
				so skip
				*********/
				return "ignore";
			}else{
				/***********
				this is updated version of what we have, so we need to update our record
				************/
				return "update";
			}
		}else{
			/*********
			Not found. Now, it may happen that the ID changed. So check again, with the $prev_co_codes_id. The $prev_co_codes_id may be blank in the csv
			*************/
			if(""===$prev_co_codes_id){
				/******
				There was no prev ID for this company, and the current ID did not match any record. This is a brand new record. We add
				***********/
				return "insert";
			}else{
				/**********
				There is a prev ID for this company. See if we get a match. If so, we may have to update it.
				***********/
				$q = "select id,curr_co_codes_id,co_code_timestamp from ".TP."co_codes_company where curr_co_codes_id='".$this->db->escape_string($prev_co_codes_id)."'";
				$ok = $this->db->select_query($q);
				if(!$ok){
					return false;
				}
				$row_count = $this->db->row_count();
				if($row_count!==0){
					/*******************
					We have the company, but with the old co-code ID. We need to update the ID.
					We also need to check the timestamp to see if anything changed or not
					****************/
					$stored_rec = $this->db->get_row();
					if($stored_rec['co_code_timestamp']!==$timestamp){
						$need_update = true;
					}else{
						/**********
						timestamp is same so we just update the co-codes id in our table and that's that
						************/
						$need_update = false;
					}
					/*********
					first thing first, update the co-code id of the company record in our table
					*****/
					$updt_q = "update ".TP."co_codes_company set curr_co_codes_id='".$this->db->escape_string($curr_co_codes_id)."' where id='".$row['id']."'";
					$ok = $this->db->mod_query($updt_q);
					if(!$ok){
						return false;
					}
					/*****
					Now see if we need to update or skip. Updating the co codes ID is not a real update
					****/
					if($need_update){
						return "update";
					}else{
						return "ignore";
					}
				}else{
					/********************
					No match with the old co-code id or current co-code id. We add
					*******************/
					return "insert";
				}
			}
		}
	}
	
	/******************
	Since the steps are same, we use a flag to produce the queries
	******************/
	private function insert_update_company_record($data_arr,$update=false){
		$q = "";
		
		$logo_name = "";
		$logo_ok = self::download_logo($data_arr['logo_url'],$logo_name);
		if(!$logo_ok){
			$logo_name = "";
		}
		
		if($update){
			$q.="update ".TP."co_codes_company set ";
		}else{
			$q.="insert into ".TP."co_codes_company set curr_co_codes_id='".$this->db->escape_string($data_arr['curr_co_codes_id'])."',";
		}
		$temp_q = "short_name='".$this->db->escape_string($data_arr['company_short_name'])."'
		,full_name='".$this->db->escape_string($data_arr['full_name'])."'
		,company_name_sec='".$this->db->escape_string($data_arr['company_name_sec'])."'
		,company_name_reuters='".$this->db->escape_string($data_arr['company_name_reuters'])."'
		,company_name_bloomberg='".$this->db->escape_string($data_arr['company_name_bloomberg'])."'
		,company_name_stoxx='".$this->db->escape_string($data_arr['company_name_stoxx'])."'
		,company_name_lse='".$this->db->escape_string($data_arr['company_name_lse'])."'
		,company_name_google='".$this->db->escape_string($data_arr['company_name_google'])."'
		,logo='".$this->db->escape_string($logo_name)."'
		,country_code='".$this->db->escape_string($data_arr['country_code'])."'
		,sector='".$this->db->escape_string($data_arr['sector'])."'
		,industry='".$this->db->escape_string($data_arr['industry'])."'
		,cik='".$this->db->escape_string($data_arr['cik'])."'
		,ric='".$this->db->escape_string($data_arr['ric'])."'
		,bloomberg_code='".$this->db->escape_string($data_arr['bloomberg_code'])."'
		,isin='".$this->db->escape_string($data_arr['isin'])."'
		,sedol='".$this->db->escape_string($data_arr['sedol'])."'
		,google_ticker='".$this->db->escape_string($data_arr['google_code'])."'
		,co_code_timestamp='".$data_arr['co_code_timestamp']."'";
		
		if(!$update){
			/**********
			we are inserting, so we need to transfer the record and sync these two
			************/
			$temp_q.=",company_id='0',in_sync='n'";
		}else{
			/**********
			we are updating. since we do not know wheher we already aynced the rec previously
			or not, we do not set the company id, but set in_sync to n so that we compare
			this with the company table
			***************/
			$temp_q.=",in_sync='n'";
		}
		$q.=$temp_q;
		
		$ok = $this->db->mod_query($q);
		if(!$ok){
			return false;
		}
		
		return true;
	}
	/*****************
	transfer the company data from the co_code_company table to company table
	we consider only those records which has is_sync - n
	**********************/
	private function sync_company_data(){
		/*****************************************************
		create a lookup array with country code as key, and country name as value
		since we do not have code for supra national, we skip the blank code
		****/
		$country_codes = array();
		$q = "select * from ".TP."country_master where iso_3166_1_alpha_2_code!=''";
		
		$ok = $this->db->select_query($q);
		if(!$ok){
			self::$debug->print_r("error in query\r\n");
			return false;
		}
		$cnt = $this->db->row_count();
		for($i=0;$i<$cnt;$i++){
			$row = $this->db->get_row();
			$country_codes[$row['iso_3166_1_alpha_2_code']] = $row['name'];
		}
		/**********************************************
		Now we create a reference array for sector and industry. We check the sector/industry
		name from co-codes with what we have. If no match, we treat those as blank
		*****/
		$sectors = array();
		$q = "select distinct sector from ".TP."sector_industry_master";
		$ok = $this->db->select_query($q);
		if(!$ok){
			self::$debug->print_r("error in query\r\n");
			return false;
		}
		$cnt = $this->db->row_count();
		for($i=0;$i<$cnt;$i++){
			$row = $this->db->get_row();
			$sectors[] = $row['sector'];
		}
		/*********************************************/
		$industries = array();
		$q = "select distinct industry from ".TP."sector_industry_master";
		$ok = $this->db->select_query($q);
		if(!$ok){
			self::$debug->print_r("error in query\r\n");
			return false;
		}
		$cnt = $this->db->row_count();
		for($i=0;$i<$cnt;$i++){
			$row = $this->db->get_row();
			$industries[] = $row['industry'];
		}
		/*********************************************/
		$q = "select * from ".TP."co_codes_company where in_sync='n'";
		$ok = $this->db->select_query($q);
		if(!$ok){
			self::$debug->print_r("error in query\r\n");
			return false;
		}
		$co_code_company_results = $this->db->get_result_set();
		$co_code_data_cnt = $co_code_company_results->row_count();
		if(0==$co_code_data_cnt){
			self::$debug->print_r("no new company data from co-codes\r\n");
			return true;
		}
		for($i=0;$i<$co_code_data_cnt;$i++){
			$co_code_company_data = $co_code_company_results->get_row();
			
			/*******************************************************************/
			$co_code_company = array();
			
			$co_code_company['company_name'] = $co_code_company_data['short_name'];
			/************
			we already checked that we have short name in the co-codes company table
			**************/
			
			if(""==$co_code_company_data['country_code']){
				$co_code_company['country_name'] = "";
				$co_code_company['country_code'] = "";
			}else{
				if(!isset($country_codes[$co_code_company_data['country_code']])){
					self::$debug->print_r("The country code ".$co_code_company_data['country_code']." not found\r\n");
					$co_code_company['country_name'] = "";
					$co_code_company['country_code'] = "";
					//do not store this
				}else{
					$co_code_company['country_name'] = $country_codes[$co_code_company_data['country_code']];
					$co_code_company['country_code'] = $country_codes[$co_code_company_data['country_code']];
				}
			}
			
			$co_code_company['sector'] = "";
			if(""!=$co_code_company_data['sector']){
				if(!in_array($co_code_company_data['sector'],$sectors)){
					self::$debug->print_r("unknown sector ".$co_code_company_data['sector']."\r\n");
				}else{
					$co_code_company['sector'] = $co_code_company_data['sector'];
				}
			}
			
			$co_code_company['industry'] = "";
			if(""!=$co_code_company_data['industry']){
				if(!in_array($co_code_company_data['industry'],$industries)){
					self::$debug->print_r("unknown industry ".$co_code_company_data['industry']."\r\n");
				}else{
					$co_code_company['industry'] = $co_code_company_data['industry'];
				}
			}
			
			$co_code_company['logo'] = $co_code_company_data['logo'];
			
			$co_code_company['cik'] = $co_code_company_data['cik'];
			$co_code_company['ric'] = $co_code_company_data['ric'];
			$co_code_company['bloomberg_code'] = $co_code_company_data['bloomberg_code'];
			$co_code_company['isin'] = $co_code_company_data['isin'];
			$co_code_company['sedol'] = $co_code_company_data['sedol'];
			$co_code_company['google_code'] = $co_code_company_data['google_ticker'];
			/*******************************************************************
			Now that we have these
			$co_code_company['company_name']
			$co_code_company['country_name']
			$co_code_company['country_code']
			$co_code_company['logo']
			$co_code_company['sector']
			$co_code_company['industry']
			we are ready for the transfer
			*****************/
			
			/*************************************
			check if we already transferred this record in prev run or not.
			we will know that if the company_id is not 0 in co-codes company table
			********************/
			if($co_code_company_data['company_id']!=0){
				/****************************
				we have this record in company table.
				(of course, we must still check it. The company might not exists)
				we get the record from the company table
				we then match it with the co-code company table
				and take necessary action
				
				if all ok, we set is_sync = y
				*************************/
				$company_id_exists = false;
				$ok = $this->company_obj->correction_suggested($co_code_company_data['company_id'],$co_code_company,$company_id_exists);
				if(!$ok){
					self::$debug->print_r("cannot store corrective suggestion\r\n");
					continue;
				}
				if(!$company_id_exists){
					/******************************
					since we do not know why this is the case, we could log an error here or
					we can create a new company record, update the company_id in the co-code table
					666 - for now, since we are not deleting any company, we assume that this will not happen
					***********************/
					continue;
				}
				/********
				no error, so assumed synced
				**********/
				$q = "update ".TP."co_codes_company set in_sync='y' where id='".$co_code_company_data['id']."'";
				$this->db->mod_query($q);
				continue;
			}else{
				/*****************************
				this record was not transferred from co-code company
				now let us see if this company exists in our company table (because it may happen
				that we have added the company from another source
				******************/
				$exists = false;
				$ok = $this->company_obj->company_exists($co_code_company,$exists);
				if(!$ok){
					self::$debug->print_r("cannot check if the company exists or not\r\n");
					continue;
				}
				if(!$exists){
					/**************************
					the company does not exists in our company table
					we add this company
					we add the identifiers
					we set original suggestion for the company
					we set original suggestion for the identifiers
					
					we get the company id
					we update the co-code company table with is_sync=y and set the company_id
					***********************/
					$created_company_id = 0;
					$ok = $this->company_obj->add_company($co_code_company,$created_company_id);
					if(!$ok){
						self::$debug->print_r("error adding company\r\n");
						continue;
					}else{
						$q = "update ".TP."co_codes_company set company_id='".$created_company_id."',in_sync='y' where id='".$co_code_company_data['id']."'";
						$this->db->mod_query($q);
						/*******
						never mind if not OK
						************/
						continue;
					}
				}else{
					/*******************
					$co_code_company_data['company_id'] is 0. This means we have not moved the company
					record from co-code company table to our table (thereby creating the company)
					yet the company exists in our table.
					This means, the company was created from another source.
					since the company exists, we might put its company id to the corresponding record in co-codes table
					666 Since we are not adding any company via front end, we can leave this for now.
					
					************/
				}
			}
			/***************
			next record
			**************/
		}
		return true;
	}
	/*******************
	Utilities
	
	fetch the remote file from 'source' and store it in 'destination'
	Using fopen instead of curl as curl is not installed in the remote server
	******************/
	private static function fetch_and_store_remote_file($source,$destination){
	
		//get the file
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
				$conn_status = connection_status();
				if($conn_status!=0) {
					switch($conn_status){
						case 1:
							self::$debug->print_r("aborted\r\n");
							break;
						case 2:
							self::$debug->print_r("timeout\r\n");
							break;
						default:
							//do nothing	
					}
					fclose($fp);
					fclose($local);
					return false;
				}else{
					//all ok
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
	
	private static function get_hashcode_of_local_file($file_path_name){
		$local_hash = trim((string)(hash_file("sha1",$file_path_name)));
		return $local_hash;
	}
	
	private static function process_csv($source,$callback){
		/****************************
		delimiter: ,
		enclosure: "
		escape: |
		We assume that the " character will never appear in any data and hence the \ is never used for escaping.
		That is why we use the garbage value for escape so that we do not have the following
		
		"0000753762",
		"AKTIEBOLAGET VOLVO \PUBL\","AKTIEBOLAGET VOLVO \PUBL\","/var/www/store/logos/0000753762",
		"","Consumer Goods","Automobiles & Parts","Automobiles & Parts","","","","","",""
					
		The \" is the culprit
		The parser takes "AKTIEBOLAGET VOLVO \PUBL\","AKTIEBOLAGET VOLVO \PUBL\","/var/www/store/logos/0000753762" as col2 and "Consumer Goods" as col4
		and then we get Consumer Goods as country name
		
		We set the escaping character to |. We hope that there si no ' " ' character in the actual data and that use of ' " ' is safe
		
		The code seems to handle blank line well
		**********************************/
		self::$debug->print_r("processing csv file\r\n");
		ini_set('auto_detect_line_endings',true);
		
		$r_handle = fopen($source,'r');
		if(!$r_handle){
			self::$debug->print_r("cannot open the source file\r\n");
			return false;
		}
		
		/************
		how robust should be the parsing?
		should we allow few errors while processing a row? or should we terminate at the first hint of error?
		
		I think we should allow few errors, log it and go forward with the remaining correct rows
		
		of course, if we do not have any correct rows, we do return false
		***************/
		$row_num = 1;
		$has_row = false;
		
		while (($csv_data = fgetcsv($r_handle, 40960, ',', '"','|')) !== false) {
			$ok = call_user_func($callback,$csv_data);
			if(!$ok){
				self::$debug->print_r("error processing row ".$row_num."\r\n");
			}else{
				$has_row = true;
				/*****
				since we do not set this to false in case of error, a single correct row will set this to false
				and we will have usable data
				**********/  
			}
			$row_num++;
		}
		fclose($r_handle);
		self::$debug->print_r("finished processing csv file\r\n");
		if($has_row){
			return true;
		}else{
			return false;
		}
	}
	
	private static function download_logo($url,&$logo_name){
		if($url===""){
			return false;
		}
		/**********
		We need to clean the logo name and put a timestamp, the way we do in front end. Since we only
		need the thumbnail, we do that when creating the thumb.
		
		For the original image, we can store as it is (because we will delete it after creating the thumb
		************/
		$original_name = basename($url);
		$extension = strtolower(self::get_file_extension($original_name));
		if(!in_array($extension,self::$allowed_img_extensions)){
			self::$debug->print_r("invalid image extension ".$extension."\r\n");
			return false;
		}
		
		$destination = FILE_PATH."/from_co-codes/logo/".$original_name;
		
		$ok = self::fetch_and_store_remote_file($url,$destination);
		if(!$ok){
			return false;
		}
		
		/*********
		now create the thumb
		********/
		$noblank = self::clean_filename(basename($url));
		$upload_img_name = time()."_".$noblank;
		$upload_img_name = strtolower($upload_img_name);
		
		$thumb_fit_width = 200;
		$thumb_fit_height = 200;
		/********
		see classes/class.company.php
		********/
		$success = self::$img_obj->create_resized($destination,FILE_PATH."/from_co-codes/logo/thumbnails",$upload_img_name,$thumb_fit_width,$thumb_fit_height,false);
		if(!$success){
			self::$debug->print_r("could not create logo thumbnail from ".$original_name."\r\n");
			//delete the original
			unlink($destination);
			return false;
		}else{
			//thumb created so we delete the original
			unlink($destination);
			$logo_name = $upload_img_name;
		}
		return true;
	}	
	/**********
	see nifty_functions.php
	************/
	private static function clean_filename($name){
		$clean = preg_replace("/[^a-zA-Z0-9\.]*/","",$name);
		return $clean;
	}
	/*********************************
	17/nov/2011
	A funciton to get the file extension
	***********************************/
	private static function get_file_extension($filename){
		$ext = "";
		$pos = strrpos($filename,".");
		if($pos === false){
			return $ext;
		}
		$ext = substr($filename,$pos+1,strlen($filename));
		return $ext;
	}
}
function get_file_extension($filename){
	$ext = "";
	$pos = strrpos($filename,".");
	if($pos === false){
		return $ext;
	}
	$ext = substr($filename,$pos+1,strlen($filename));
	return $ext;
}
/******************
TODO
we might need the co-codes country code to our country code mapping.

at least UK is a suspect (we have GB)

TODO
The code that fetch equity data from co-codes will use the intermediate table to match co-codes unique id to find the corresponding company id
(and it will not create the company)
**************/

/*****************************
we are using a proxy class in place of the actual company class
because cron codes use new db codes
******************/
class company_proxy{
	
	private $db;
	
	private function __construct(){
	}
	
	public static function create(){
		global $g_config;
		$temp = db::create($g_config['db_host'],$g_config['db_user'],$g_config['db_password'],$g_config['db_name']);
		if($temp===false){
			self::$debug->print_r("cannot connect to database\r\n");
			return false;
		}
		$obj = new company_proxy();
		$obj->db = $temp;
		
		return $obj;
	}
	
	/***************
	this checks only the type 'company'
	666 for now, we assume the company does not exists in company table
	***************/
	public function company_exists($data_arr,&$exists){
		$exists = false;
		return true;
	}
	
	public function get_identifier_code_id_lookup(){
		$identifiers = array();
		
		$q = "select identifier_id,code from ".TP."company_identifier_master";
		$ok = $this->db->select_query($q);
		if(!$ok){
			return false;
		}
		$id_cnt = $this->db->row_count();
		for($i=0;$i<$id_cnt;$i++){
			$row = $this->db->get_row();
			$identifiers[$row['code']] = $row['identifier_id'];
		}
		return $identifiers;
	}
	
	/****************
	Here we distinguish between the case of db error and company not found.
	Basically, if the company id is not in our table, it means that admin has removed
	the company. We might hanve to handle the case
	****************/
	public function get_company($company_id,&$exists,&$data_arr){
		$q = "select * from zzz_".TP."company where company_id='".$company_id."'";
		$ok = $this->db->select_query($q);
		if(!$ok){
			return false;
		}
		
		if(!$this->db->has_row()){
			$exists = false;
			return true;
		}
		$exists = true;
		$data_arr = $this->db->get_row();
		return true;
	}
	/*****************
	we assume that the caller has already checked whether the company exists or not
	also, we already have the logo. we just need to move it to the proper folder
	
	also, we assume that the data is not admin verified
	
	We add the company data, then the identifier data and then set those as suggestions also (with is_correction=n)
	so that we know what was the original suggestion.
	*****************/
	public function add_company($data_arr,&$created_company_id){
	    
		/*************************
		we need to build a company identifier code/identifier id lookup table
		*****************************/
		$identifiers = array();
		
		$q = "select identifier_id,code from ".TP."company_identifier_master";
		$ok = $this->db->select_query($q);
		if(!$ok){
			return false;
		}
		$id_cnt = $this->db->row_count();
		for($i=0;$i<$id_cnt;$i++){
			$row = $this->db->get_row();
			$identifiers[$row['code']] = $row['identifier_id'];
		}
		/***************************************************************/
		$logo = "";
		
		if($data_arr['logo']!=""){
			$moved = copy(FILE_PATH."/from_co-codes/logo/thumbnails/".$data_arr['logo'],FILE_PATH."/zzz_uploaded_img/logo/thumbnails/".$data_arr['logo']);
			if($moved){
				$logo = $data_arr['logo'];
			}
		}
		
		$date_added = date("Y-m-d H:i:s");
		
		$q = "insert into zzz_".TP."company set name='".$this->db->escape_string($data_arr['company_name'])."'
		,type='company'
		,industry='".$this->db->escape_string($data_arr['industry'])."'
		,sector='".$this->db->escape_string($data_arr['sector'])."'
		,hq_country='".$this->db->escape_string($data_arr['country_name'])."'
		,logo='".$logo."'
		,admin_verified='n'";
		
		$ok = $this->db->mod_query($q);
		if(!$ok){
			return false;
		}
		/******************
		company record added, now get the generated id
		*******************/
		$company_id = $this->db->last_insert_id();
		$created_company_id = $company_id;
		
		/***************************
		we add this as original suggestion
		this is not a correction
		since this is auto code, we assume that this is set by admin
		no issue if not added
		************************/
		$q = "insert into zzz_".TP."company_suggestions set
		company_id='".$company_id."',
		suggested_by='0',
		date_suggested='".$date_added."',
		name='".$this->db->escape_string($data_arr['company_name'])."',
		type='company',
		hq_country='".$this->db->escape_string($data_arr['country_name'])."',
		sector='".$this->db->escape_string($data_arr['sector'])."',
		industry='".$this->db->escape_string($data_arr['industry'])."',
		logo='".$logo."',
		is_correction='n'";
		
		$ok = $this->db->mod_query($q);
		
		/***********************
		now add the company identifiers
		also, we create the identifier suggestion suggestion query as and when we get to add an identifier
		we assume that admin added this and this is not a correction
		********************/
		$identifier_q = "";
		
		$cik = $data_arr['cik'];
		$ric = $data_arr['ric'];
		$bloomberg = $data_arr['bloomberg_code'];
		$isin = $data_arr['isin'];
		$sedol = $data_arr['sedol'];
		$google = $data_arr['google_code'];
		
		/***********
		check company identifier master
		ISIN: isin
		Bloomberg Ticker: bloom
		Reuters Instrument Code: ric
		Google Finance Code: goog
		Central Index Key: cik
		SEDOL: sedol
		*************/
		
		if($cik!==""){
			$id_q = "insert into zzz_".TP."company_identifiers set company_id='".$company_id."',identifier_id='".$identifiers['cik']."',value='".$this->db->escape_string($cik)."'";
			$ok = $this->db->mod_query($id_q);
			if($ok){
				$identifier_q.=",('".$company_id."','0','".$date_added."','".$identifiers['cik']."','".$this->db->escape_string($cik)."','n')";
			}
		}
		
		if($ric!==""){
			$id_q = "insert into zzz_".TP."company_identifiers set company_id='".$company_id."',identifier_id='".$identifiers['ric']."',value='".$this->db->escape_string($ric)."'";
			$ok = $this->db->mod_query($id_q);
			if($ok){
				$identifier_q.=",('".$company_id."','0','".$date_added."','".$identifiers['ric']."','".$this->db->escape_string($ric)."','n')";
			}
		}
		
		if($bloomberg!==""){
			$id_q = "insert into zzz_".TP."company_identifiers set company_id='".$company_id."',identifier_id='".$identifiers['bloom']."',value='".$this->db->escape_string($bloomberg)."'";
			$ok = $this->db->mod_query($id_q);
			if($ok){
				$identifier_q.=",('".$company_id."','0','".$date_added."','".$identifiers['bloom']."','".$this->db->escape_string($bloomberg)."','n')";
			}
		}
		
		if($isin!==""){
			$id_q = "insert into zzz_".TP."company_identifiers set company_id='".$company_id."',identifier_id='".$identifiers['isin']."',value='".$this->db->escape_string($isin)."'";
			$ok = $this->db->mod_query($id_q);
			if($ok){
				$identifier_q.=",('".$company_id."','0','".$date_added."','".$identifiers['isin']."','".$this->db->escape_string($isin)."','n')";
			}
		}
		
		if($sedol!==""){
			$id_q = "insert into zzz_".TP."company_identifiers set company_id='".$company_id."',identifier_id='".$identifiers['sedol']."',value='".$this->db->escape_string($sedol)."'";
			$ok = $this->db->mod_query($id_q);
			if($ok){
				$identifier_q.=",('".$company_id."','0','".$date_added."','".$identifiers['sedol']."','".$this->db->escape_string($sedol)."','n')";
			}
		}
		
		if($google!==""){
			$id_q = "insert into zzz_".TP."company_identifiers set company_id='".$company_id."',identifier_id='".$identifiers['goog']."',value='".$this->db->escape_string($google)."'";
			$ok = $this->db->mod_query($id_q);
			if($ok){
				$identifier_q.=",('".$company_id."','0','".$date_added."','".$identifiers['goog']."','".$this->db->escape_string($google)."','n')";
			}
		}
		
		/**********************
		now try to add the identifier suggestion
		*******************/
		if($identifier_q!=""){
			$identifier_q = substr($identifier_q,1);
		}
		if($identifier_q!=""){
			$identifier_q = "INSERT INTO zzz_".TP."company_identifiers_suggestions(company_id,suggested_by,date_suggested,identifier_id,`value`,is_correction) values".$identifier_q;
			$this->db->mod_query($identifier_q);
		}
		/**********************************/
		return true;
	}
	
	/**************
	here we assume that it is admin who is suggesting the correction
	
	It might happen that the company with this ID is not there.
	Since we are not sure that really happened, we just send a flag
	***************/
	public function correction_suggested($company_id,$data_arr,&$company_id_exists){
		
		/*************************
		we need to create some lookup tables
		**************************/
		$identifiers = $this->get_identifier_code_id_lookup();
		if($identifiers === false){
			return false;
		}
		
		$current_data = NULL;
		$company_exists = false;
		$ok = $this->get_company($company_id,$company_exists,$current_data);
		if(!$ok){
			return false;
		}
		if(!$company_exists){
			$company_id_exists = false;
			return true;
		}
		$company_id_exists = true;
		
		/*******************************
		get the existing identifiers for this company and create a lookup table identifier id, value
		********************/
		$company_current_identifiers_value = array();
		
		$q = "select identifier_id,`value` from zzz_".TP."company_identifiers where company_id='".$company_id."'";
		$ok = $this->db->select_query($q);
		if(!$ok){
			return false;
		}
		$cnt = $this->db->row_count();
		for($i=0;$i<$cnt;$i++){
			$row = $this->db->get_row();
			$company_current_identifiers_value[$row['identifier_id']] = $row['value'];
		}
		/****************************************/
		
		
		$company_current_name = $current_data['name'];
		$suggested_name = $data_arr['company_name'];
		
		$company_current_hq_country_code = $current_data['hq_country_code'];
		$suggested_hq_country_code = $data_arr['country_code'];
		
		$company_current_hq_country = $current_data['hq_country'];
		$suggested_hq_country = $data_arr['country_name'];
		
		$company_current_sector = $current_data['sector'];
		$suggested_sector = $data_arr['sector'];
		
		$company_current_industry = $current_data['industry'];
		$suggested_industry = $data_arr['industry'];
		
		$company_current_logo = $current_data['logo'];
		$suggested_logo = $data_arr['logo'];
		/****************************
		note: when we store a row from co-code csv file, we download the logo file and give it a new unique name.
		that name is in the co-code table.
		The next day, if we download the logo again, we give it a new name.
		***********************/
		$changes_made = "";
		/*******************
		check each and see if there is a suggestion value and whether
		it is different from what is stored currently
		*************************/
		if($suggested_name != ""){
			if($company_current_name == ""){
				/************
				no name is set currently so we try to set the name
				if we can do so, we store the suggestion with status of 'name added' else the default of 'name suggested'
				We insert the suggestion record later
				**************/
				$q = "update zzz_".TP."company set name='".$this->db->escape_string($suggested_name)."' where company_id='".$company_id."'";
				$ok = $this->db->mod_query($q);
				if($ok){
					$changes_made.=",name added";
				}else{
					$changes_made.=",name suggested";
				}
			}else{
				if($company_current_name == $suggested_name){
					/**************
					we are specifying what is there currently so this is not
					really a suggestion. We set the $suggested_name to blank
					so that when we run the insert query, the value is not stored for 'name'
					***************/
					$suggested_name = "";
				}else{
					/************
					this is a valid suggestion. We make a note.
					*********/
					$changes_made.=",name suggested";
				}
			}
		}
		/************************************************************************************************/
		if($suggested_hq_country_code != ""){
			if($company_current_hq_country_code == ""){
				/************
				no hq_country_code is set currently so we try to set the hq_country_code
				We do not track the suggestion
				**************/
				$q = "update zzz_".TP."company set hq_country_code='".$this->db->escape_string($suggested_hq_country_code)."' where company_id='".$company_id."'";
				$ok = $this->db->mod_query($q);
				/************
				this is not critical so never mind if this is not ok
				****************/
			}
		}
		/*******************************************************************************************/
		if($suggested_hq_country != ""){
			if($company_current_hq_country == ""){
				/************
				no hq_country is set currently so we try to set the hq_country
				if we can do so, we store the suggestion with status of 'hq country added' else the default of 'hq country suggested'
				We insert the suggestion record later
				**************/
				$q = "update zzz_".TP."company set hq_country='".$this->db->escape_string($suggested_hq_country)."' where company_id='".$company_id."'";
				$ok = $this->db->mod_query($q);
				if($ok){
					$changes_made.=",hq country added";
				}else{
					$changes_made.=",hq country suggested";
				}
			}else{
				if($company_current_hq_country == $suggested_hq_country){
					/**************
					we are specifying what is there currently so this is not
					really a suggestion. We set the $suggested_hq_country to blank
					so that when we run the insert query, the value is not stored for 'hq_country'
					***************/
					$suggested_hq_country = "";
				}else{
					/************
					this is a valid suggestion. We make a note.
					*********/
					$changes_made.=",hq country suggested";
				}
			}
		}
		/*******************************************************************************************/
		if($suggested_sector != ""){
			if($company_current_sector == ""){
				/************
				no sector is set currently so we try to set the sector
				if we can do so, we store the suggestion with status of 'sector added' else the default of 'sector suggested'
				We insert the suggestion record later
				**************/
				$q = "update zzz_".TP."company set sector='".$this->db->escape_string($suggested_sector)."' where company_id='".$company_id."'";
				$ok = $this->db->mod_query($q);
				if($ok){
					$changes_made.=",sector added";
				}else{
					$changes_made.=",sector suggested";
				}
			}else{
				if($company_current_sector == $suggested_sector){
					/**************
					we are specifying what is there currently so this is not
					really a suggestion. We set the $suggested_sector to blank
					so that when we run the insert query, the value is not stored for 'sector'
					***************/
					$suggested_sector = "";
				}else{
					/************
					this is a valid suggestion. We make a note.
					*********/
					$changes_made.=",sector suggested";
				}
			}
		}
		/********************************************************************************************/
		if($suggested_industry != ""){
			if($company_current_industry == ""){
				/************
				no industry is set currently so we try to set the industry
				if we can do so, we store the suggestion with status of 'industry added' else the default of 'industry suggested'
				We insert the suggestion record later
				**************/
				$q = "update zzz_".TP."company set industry='".$this->db->escape_string($suggested_industry)."' where company_id='".$company_id."'";
				$ok = $this->db->mod_query($q);
				if($ok){
					$changes_made.=",industry added";
				}else{
					$changes_made.=",industry suggested";
				}
			}else{
				if($company_current_industry == $suggested_industry){
					/**************
					we are specifying what is there currently so this is not
					really a suggestion. We set the $suggested_industry to blank
					so that when we run the insert query, the value is not stored for 'industry'
					***************/
					$suggested_industry = "";
				}else{
					/************
					this is a valid suggestion. We make a note.
					*********/
					$changes_made.=",industry suggested";
				}
			}
		}
		/*****************************************************************************/
		if($suggested_logo != ""){
			if($company_current_logo == ""){
				/*******************
				no logo is set currently so we try to move the logo to our folder and then set the logo.
				if we can do so, we store the suggestion with status of 'logo added' else the default of 'logo suggested'
				We insert the suggestion record later
				*******************/
				$moved = copy(FILE_PATH."/from_co-codes/logo/thumbnails/".$suggested_logo,FILE_PATH."/uploaded_img/logo/thumbnails/".$suggested_logo);
				if($moved){
					$q = "update zzz_".TP."company set logo='".$suggested_logo."' where company_id='".$company_id."'";
					$ok = $this->db->mod_query($q);
					if($ok){
						$changes_made.=",logo added";
					}else{
						$changes_made.=",logo suggested";
					}
				}else{
					/**********************
					could not move the logo, ignore
					********************/
				}
				
			}else{
				if($company_current_logo == $suggested_logo){
					/**************
					we are specifying what is there currently so this is not
					really a suggestion. We set the $suggested_logo to blank
					so that when we run the insert query, the value is not stored for 'logo'
					***************/
					$suggested_logo = "";
				}else{
					/************
					this is a valid suggestion. We make a note.
					*********/
					$changes_made.=",logo suggested";
				}
			}
		}
		/**************
		it may happen that we already have the changes (after all, admin can also change data directly
		**********/
		if($changes_made == ""){
			return true;
		}
		$changes_made = substr($changes_made,1);
		$date_added = date("Y-m-d H:i:s");
		
		/*************
		This is not original suggestion, but a correction
		***************/
		$q = "insert into zzz_".TP."company_suggestions set
		company_id='".$company_id."',
		suggested_by='0',
		date_suggested='".$date_added."',
		name='".$this->db->escape_string($suggested_name)."',
		type='company',
		hq_country='".$this->db->escape_string($suggested_hq_country)."',
		sector='".$this->db->escape_string($suggested_sector)."',
		industry='".$this->db->escape_string($suggested_industry)."',
		logo='".$suggested_logo."',
		status_note='".$changes_made."',
		is_correction='y'";
		
		$ok = $this->db->mod_query($q);
		/******************************************************************************
		now the identifiers
		***************/
		$cik = $data_arr['cik'];
		$ric = $data_arr['ric'];
		$bloomberg = $data_arr['bloomberg_code'];
		$isin = $data_arr['isin'];
		$sedol = $data_arr['sedol'];
		$google = $data_arr['google_code'];
		
		/***********
		check company identifier master
		ISIN: isin
		Bloomberg Ticker: bloom
		Reuters Instrument Code: ric
		Google Finance Code: goog
		Central Index Key: cik
		SEDOL: sedol
		*************/
		$identifier_q = "";
		
		$identifier_suggestion_q = "";
		if($cik!==""){
			
			$suggestion_status_note = "";
			
			if(!isset($company_current_identifiers_value[$identifiers['cik']])){
				/*********
				identifier not set, we need to insert
				**************/
				$id_q = "insert into zzz_".TP."company_identifiers set company_id='".$company_id."',identifier_id='".$identifiers['cik']."',`value`='".$this->db->escape_string($cik)."'";
				$ok = $this->db->mod_query($id_q);
				if($ok){
					$suggestion_status_note = "set";
				}else{
					$suggestion_status_note = "suggested";
				}
			}else{
				if($company_current_identifiers_value[$identifiers['cik']]==""){
					/*************
					identifier set but is blank, so we update the record
					**************/
					$id_q = "update zzz_".TP."company_identifiers set `value`='".$this->db->escape_string($cik)."' where company_id='".$company_id."' and identifier_id='".$identifiers['cik']."'";
					$ok = $this->db->mod_query($id_q);
					if($ok){
						$suggestion_status_note = "set";
					}else{
						$suggestion_status_note = "suggested";
					}
				}else{
					/*********
					this identifier is set and we have current value, check if the suggested value is same or not
					*********/
					if($cik != $company_current_identifiers_value[$identifiers['cik']]){
						/****************
						we have a suggestion
						****************/
						$suggestion_status_note = "suggested";
					}else{
						/*************
						this is not really a suggestion, ignore
						***************/
					}
				}
			}
			
			if($suggestion_status_note != ""){
				$identifier_suggestion_q.=",('".$company_id."','0','".$date_added."','".$identifiers['cik']."','".$this->db->escape_string($cik)."','".$suggestion_status_note."','y')";
			}
		}
		/**********************************************************************************/
		if($ric!==""){
			
			$suggestion_status_note = "";
			
			if(!isset($company_current_identifiers_value[$identifiers['ric']])){
				/*********
				identifier not set, we need to insert
				**************/
				$id_q = "insert into zzz_".TP."company_identifiers set company_id='".$company_id."',identifier_id='".$identifiers['ric']."',`value`='".$this->db->escape_string($ric)."'";
				$ok = $this->db->mod_query($id_q);
				if($ok){
					$suggestion_status_note = "set";
				}else{
					$suggestion_status_note = "suggested";
				}
			}else{
				if($company_current_identifiers_value[$identifiers['ric']]==""){
					/*************
					identifier set but is blank, so we update the record
					**************/
					$id_q = "update zzz_".TP."company_identifiers set `value`='".$this->db->escape_string($ric)."' where company_id='".$company_id."' and identifier_id='".$identifiers['ric']."'";
					$ok = $this->db->mod_query($id_q);
					if($ok){
						$suggestion_status_note = "set";
					}else{
						$suggestion_status_note = "suggested";
					}
				}else{
					/*********
					this identifier is set and we have current value, check if the suggested value is same or not
					*********/
					if($ric != $company_current_identifiers_value[$identifiers['ric']]){
						/****************
						we have a suggestion
						****************/
						$suggestion_status_note = "suggested";
					}else{
						/*************
						this is not really a suggestion, ignore
						***************/
					}
				}
			}
			
			if($suggestion_status_note != ""){
				$identifier_suggestion_q.=",('".$company_id."','0','".$date_added."','".$identifiers['ric']."','".$this->db->escape_string($ric)."','".$suggestion_status_note."','y')";
			}
		}
		/***********************************************************************************/
		if($bloomberg!==""){
			
			$suggestion_status_note = "";
			
			if(!isset($company_current_identifiers_value[$identifiers['bloom']])){
				/*********
				identifier not set, we need to insert
				**************/
				$id_q = "insert into zzz_".TP."company_identifiers set company_id='".$company_id."',identifier_id='".$identifiers['bloom']."',`value`='".$this->db->escape_string($bloomberg)."'";
				$ok = $this->db->mod_query($id_q);
				if($ok){
					$suggestion_status_note = "set";
				}else{
					$suggestion_status_note = "suggested";
				}
			}else{
				if($company_current_identifiers_value[$identifiers['bloom']]==""){
					/*************
					identifier set but is blank, so we update the record
					**************/
					$id_q = "update zzz_".TP."company_identifiers set `value`='".$this->db->escape_string($bloomberg)."' where company_id='".$company_id."' and identifier_id='".$identifiers['bloom']."'";
					$ok = $this->db->mod_query($id_q);
					if($ok){
						$suggestion_status_note = "set";
					}else{
						$suggestion_status_note = "suggested";
					}
				}else{
					/*********
					this identifier is set and we have current value, check if the suggested value is same or not
					*********/
					if($bloomberg != $company_current_identifiers_value[$identifiers['bloom']]){
						/****************
						we have a suggestion
						****************/
						$suggestion_status_note = "suggested";
					}else{
						/*************
						this is not really a suggestion, ignore
						***************/
					}
				}
			}
			
			if($suggestion_status_note != ""){
				$identifier_suggestion_q.=",('".$company_id."','0','".$date_added."','".$identifiers['bloom']."','".$this->db->escape_string($bloomberg)."','".$suggestion_status_note."','y')";
			}
		}
		/***************************************************************************/
		if($isin!==""){
			
			$suggestion_status_note = "";
			
			if(!isset($company_current_identifiers_value[$identifiers['isin']])){
				/*********
				identifier not set, we need to insert
				**************/
				$id_q = "insert into zzz_".TP."company_identifiers set company_id='".$company_id."',identifier_id='".$identifiers['isin']."',`value`='".$this->db->escape_string($isin)."'";
				$ok = $this->db->mod_query($id_q);
				if($ok){
					$suggestion_status_note = "set";
				}else{
					$suggestion_status_note = "suggested";
				}
			}else{
				if($company_current_identifiers_value[$identifiers['isin']]==""){
					/*************
					identifier set but is blank, so we update the record
					**************/
					$id_q = "update zzz_".TP."company_identifiers set `value`='".$this->db->escape_string($isin)."' where company_id='".$company_id."' and identifier_id='".$identifiers['isin']."'";
					$ok = $this->db->mod_query($id_q);
					if($ok){
						$suggestion_status_note = "set";
					}else{
						$suggestion_status_note = "suggested";
					}
				}else{
					/*********
					this identifier is set and we have current value, check if the suggested value is same or not
					*********/
					if($isin != $company_current_identifiers_value[$identifiers['isin']]){
						/****************
						we have a suggestion
						****************/
						$suggestion_status_note = "suggested";
					}else{
						/*************
						this is not really a suggestion, ignore
						***************/
					}
				}
			}
			
			if($suggestion_status_note != ""){
				$identifier_suggestion_q.=",('".$company_id."','0','".$date_added."','".$identifiers['isin']."','".$this->db->escape_string($isin)."','".$suggestion_status_note."','y')";
			}
		}
		/******************************************************************/
		if($sedol!==""){
			
			$suggestion_status_note = "";
			
			if(!isset($company_current_identifiers_value[$identifiers['sedol']])){
				/*********
				identifier not set, we need to insert
				**************/
				$id_q = "insert into zzz_".TP."company_identifiers set company_id='".$company_id."',identifier_id='".$identifiers['sedol']."',`value`='".$this->db->escape_string($sedol)."'";
				$ok = $this->db->mod_query($id_q);
				if($ok){
					$suggestion_status_note = "set";
				}else{
					$suggestion_status_note = "suggested";
				}
			}else{
				if($company_current_identifiers_value[$identifiers['sedol']]==""){
					/*************
					identifier set but is blank, so we update the record
					**************/
					$id_q = "update zzz_".TP."company_identifiers set `value`='".$this->db->escape_string($sedol)."' where company_id='".$company_id."' and identifier_id='".$identifiers['sedol']."'";
					$ok = $this->db->mod_query($id_q);
					if($ok){
						$suggestion_status_note = "set";
					}else{
						$suggestion_status_note = "suggested";
					}
				}else{
					/*********
					this identifier is set and we have current value, check if the suggested value is same or not
					*********/
					if($sedol != $company_current_identifiers_value[$identifiers['sedol']]){
						/****************
						we have a suggestion
						****************/
						$suggestion_status_note = "suggested";
					}else{
						/*************
						this is not really a suggestion, ignore
						***************/
					}
				}
			}
			
			if($suggestion_status_note != ""){
				$identifier_suggestion_q.=",('".$company_id."','0','".$date_added."','".$identifiers['sedol']."','".$this->db->escape_string($sedol)."','".$suggestion_status_note."','y')";
			}
		}
		/******************************************************************/
		if($google!==""){
			
			$suggestion_status_note = "";
			
			if(!isset($company_current_identifiers_value[$identifiers['goog']])){
				/*********
				identifier not set, we need to insert
				**************/
				$id_q = "insert into zzz_".TP."company_identifiers set company_id='".$company_id."',identifier_id='".$identifiers['goog']."',`value`='".$this->db->escape_string($google)."'";
				$ok = $this->db->mod_query($id_q);
				if($ok){
					$suggestion_status_note = "set";
				}else{
					$suggestion_status_note = "suggested";
				}
			}else{
				if($company_current_identifiers_value[$identifiers['goog']]==""){
					/*************
					identifier set but is blank, so we update the record
					**************/
					$id_q = "update zzz_".TP."company_identifiers set `value`='".$this->db->escape_string($google)."' where company_id='".$company_id."' and identifier_id='".$identifiers['goog']."'";
					$ok = $this->db->mod_query($id_q);
					if($ok){
						$suggestion_status_note = "set";
					}else{
						$suggestion_status_note = "suggested";
					}
				}else{
					/*********
					this identifier is set and we have current value, check if the suggested value is same or not
					*********/
					if($google != $company_current_identifiers_value[$identifiers['goog']]){
						/****************
						we have a suggestion
						****************/
						$suggestion_status_note = "suggested";
					}else{
						/*************
						this is not really a suggestion, ignore
						***************/
					}
				}
			}
			
			if($suggestion_status_note != ""){
				$identifier_suggestion_q.=",('".$company_id."','0','".$date_added."','".$identifiers['goog']."','".$this->db->escape_string($google)."','".$suggestion_status_note."','y')";
			}
		}
		/***************************************************************/
		if($identifier_suggestion_q!=""){
			$identifier_suggestion_q = substr($identifier_suggestion_q,1);
		}
		if($identifier_suggestion_q!=""){
			$identifier_suggestion_q = "INSERT INTO zzz_".TP."company_identifiers_suggestions(company_id,suggested_by,date_suggested,identifier_id,`value`,status_note,is_correction) values".$identifier_suggestion_q;
			$this->db->mod_query($identifier_suggestion_q);
		}
		/********************************************/
		return true;
	}
}
?>