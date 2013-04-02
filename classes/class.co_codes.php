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
	
	private static $full_nasdaq_equity_file = "store/dealdata_nasdaq.csv";
	private static $full_nasdaq_equity_file_local_destination = "/from_co-codes/deals.csv";
	
	private static $num_cols_in_company_csv = 23;
	private static $num_cols_in_nasdaq_eq_csv = 25;
	
	private static $allowed_img_extensions = array("gif","png","jpg","jpeg");
	
	private static $num_logo_refetch_attempt = 3;
	
	private static $img_obj;
	
	public static $debug;
	
	private $db;
	
	private $company_obj;
	private $trans_obj;
	
	
	private function __construct(){
	}
	
	public static function create(){
		/****************
		before starting a run, let us remove the old log file
		****************/
		self::$debug = new debug(FILE_PATH."/from_co-codes/log.txt",false);
		
		global $g_config;
		
		$temp = db::create($g_config['db_host'],$g_config['db_user'],$g_config['db_password'],$g_config['db_name']);
		if($temp===false){
			self::$debug->print_r("cannot connect to database");
			return false;
		}
		$obj = new co_codes();
		$obj->db = $temp;
		
		$company_obj = company_proxy::create();
		
		if(!$company_obj){
			self::$debug->print_r("cannot create company proxy");
			return false;
		}
		$obj->company_obj = $company_obj;
		
		$trans_obj = transaction_proxy::create();
		
		if(!$trans_obj){
			self::$debug->print_r("cannot create transaction proxy");
			return false;
		}
		$obj->trans_obj = $trans_obj;
		
		require_once(FILE_PATH."/classes/class.image_util.php");
		self::$img_obj = new image_util();
		
		return $obj;
	}
	
	public function get_all_company_data(){
		$ok = $this->fetch_data("company data",self::$co_codes_url,self::$full_company_file,FILE_PATH.self::$full_company_file_local_destination,array($this,"process_row_data_for_company"));
		if(!$ok){
			/*******************
			the fetch_data function takes care of logging
			*******************/
			return false;
		}
		/**********
		now transfer
		***********/
		$ok = $this->sync_company_data();
		if(!$ok){
			self::$debug->print_r("error sync'ing the company data");
			return false;
		}
		self::$debug->print_r("sync'ed the company data");
		return true;
	}
	
	public function get_all_equity_deal_data(){
		$ok = $this->fetch_data("nasdaq equity deal data",self::$co_codes_url,self::$full_nasdaq_equity_file,FILE_PATH.self::$full_nasdaq_equity_file_local_destination,array($this,"process_row_data_for_nasdaq_eq"));
		if(!$ok){
			/*******************
			the fetch_data function takes care of logging
			*******************/
			return false;
		}
		/**********
		now transfer
		***********/
		$ok = $this->sync_nasdaq_eq_data();
		if(!$ok){
			self::$debug->print_r("error sync'ing the nasdaq eq data");
			return false;
		}
		self::$debug->print_r("sync'ed the nasdaq eq data");
		return true;
	}
	
	/****************************
	sng:1/apr/2013
	I assure you that this code is needed. This is not April Fool code
	
	When we are processing the company csv file, we try to fetch the logo. If that fails, we store the logo name in co_codes_company_refetch_logo.
	Later we try to fetch the logo again.
	We try three times
	***************/
	public function fetch_errored_logos(){
		$q = "select * from ".TP."co_codes_company_refetch_logo where num_attempt<".self::$num_logo_refetch_attempt;
		$ok = $this->db->select_query($q);
		if(!$ok){
			return false;
		}
		$logo_results = $this->db->get_result_set();
		$logo_cnt = $logo_results->row_count();
		if(0==$logo_cnt){
			self::$debug->print_r("no logos to refetch");
			return true;
		}
		for($i=0;$i<$logo_cnt;$i++){
			$logo_data = $logo_results->get_row();
			$co_codes_company_id = $logo_data['co_codes_company_id'];
			$co_codes_company_logo_url = $logo_data['logo_url'];
			/******************
			now check co_codes_company to see if we got the logo or not. It may happen that the next day, the logo could be downloaded properly.
			***************/
			$q = "select logo from ".TP."co_codes_company where id='".$co_codes_company_id."'";
			$ok = $this->db->select_query($q);
			if(!$ok){
				continue;
			}
			if(!$this->db->has_row()){
				/********
				hmm, no such record? deleted?better safe than sorry
				**********/
				self::$debug->print_r("co_codes_company id ".$co_codes_company_id." found in co_codes_company_refetch_logo not there in co_codes_company");
				return false;
			}
			$row = $this->db->get_row();
			if($row['logo']!=""){
				/************
				logo is there, so remove the entry from co_codes_company_refetch_logo
				***************/
				$q = "delete from ".TP."co_codes_company_refetch_logo WHERE co_codes_company_id='".$co_codes_company_id."'";
				$this->db->mod_query($q);
				continue;
			}else{
				/**************
				logo is not there, so try to refetch
				**************/
				$logo_name = "";
				$logo_refetch_needed = false;
				$logo_ok = self::download_logo($co_codes_company_logo_url,$logo_name,$logo_refetch_needed);
				if(!$logo_ok){
					/**************
					error, what do we do?, increase count or ignore or throw error?
					well, throw error
					******************/
					self::$debug->print_r("cannot refetch logo for ".$co_codes_company_id." from url ".$co_codes_company_logo_url);
					return false;
				}
				/***************
				Now? if refetch needed then we do that
				******************/
				if($logo_refetch_needed){
					/************
					update count
					*************/
					$q = "update ".TP."co_codes_company_refetch_logo set num_attempt=num_attempt+1 WHERE co_codes_company_id='".$co_codes_company_id."'";
					$this->db->mod_query($q);
					continue;
				}
				/********************
				But if it comes here? refetch needed is false so we check logo_name. If that is blank, we delete the entry (I mean
				if there was error, we would have got refetch eq true
				*********************/
				if(""==$logo_name){
					$q = "delete from ".TP."co_codes_company_refetch_logo WHERE co_codes_company_id='".$co_codes_company_id."'";
					$this->db->mod_query($q);
					continue;
				}
				/**************************
				so we have logo. we update the co_codes_company table and set in_sync to n
				that will trigger a refresh in the next run
				and in the meanwhile, we delete the entry
				**********************/
				$q = "update ".TP."co_codes_company set logo='".$logo_name."',in_sync='n' WHERE id='".$co_codes_company_id."'";
				$ok = $this->db->mod_query($q);
				if(!$ok){
					continue;
				}
				/*********************
				updated, so remove the entry
				***************/
				$q = "delete from ".TP."co_codes_company_refetch_logo WHERE co_codes_company_id='".$co_codes_company_id."'";
				$this->db->mod_query($q);
				continue;
			}
			/******************************************************/
		}
		return true;
	}
	
	public function get_hashcode_of_file($remote_file_path_name){
		$hash_source = self::$co_codes_url."info/obj_info.php?file=".$remote_file_path_name."&property=hash";
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
	
	/******************************************************************
	$file_desc: small text about the file that apear in log file
	ex company data OR nasdaq equity deal data
	
	$remote_site_url: url of co-codes
	ex self::$co_codes_url
	
	$remote_file_path: server path of the remote file
	ex
	self::$full_company_file
	self::$full_nasdaq_equity_file
	
	$destination: place where the file will be stored locally
	ex
	FILE_PATH.self::$full_company_file_local_destination
	FILE_PATH.self::$full_nasdaq_equity_file_local_destination
	
	$processing_callback: callback function to process the csv files
	ex
	array($this,"process_row_data_for_company")
	array($this,"process_row_data_for_nasdaq_eq")
	**************************************/
	private function fetch_data($file_desc,$remote_site_url,$remote_file_path,$destination,$processing_callback){
		$source = $remote_site_url.$remote_file_path;
		/********
		fetch the csv file
		
		I think this class should know where to put the fetched file. I mean, today it is a csv file, tomorrow it could be different.
		The caller should not know that a file is being fetched and stored
		**********/
		self::$debug->print_r("fetching ".$file_desc." file from co-codes");
		
		/***********
		maybe we should delete the existing version? It might cause trouble
		*************/
		if(file_exists($destination)){
			unlink($destination);
		}
		$ok = self::fetch_and_store_remote_file($source,$destination);
		if(!$ok){
			self::$debug->print_r("error fetching ".$file_desc." file");
			return false;
		}
		self::$debug->print_r("fetched ".$file_desc." file");
		/***************
		check integrity of the downloaded file
		**************/
		$local_hash = self::get_hashcode_of_local_file($destination);
		$remote_hash = $this->get_hashcode_of_file($remote_file_path);
		
		if($local_hash!==$remote_hash){
			self::$debug->print_r($file_desc." file mangled during download");
			return false;
		}
		self::$debug->print_r("verified integrity of the ".$file_desc." file");
		/****************
		now process the csv file
		*****************/
		$ok = self::process_csv($destination,$processing_callback);
		if(!$ok){
			self::$debug->print_r("error processing the ".$file_desc." file");
			return false;
		}
		self::$debug->print_r("processed the ".$file_desc." file");
		/*******************
		after the parsing, we remove the downloaded file. No need to clutter the server
		********************/
		///unlink($destination);
		///self::$debug->print_r("removed the ".$file_desc." file");
		return true;
	}
	
	private function process_row_data_for_company($data_array){
		/************
		first we make sure that we have the required number of columns
		****************/
		$col_cnt = count($data_array);
		if($col_cnt!==self::$num_cols_in_company_csv){
			self::$debug->print_r("invalid number of columns");
			return false;
		}
		$co_code_data_arr = array();
		/****************************************
		Col_0: CCS Identifier "SEC000940062"
		If this starts with CCS, it means it does not appear in SEC (securities and exchange commission) database, else it starts with SEC
		
		The current co code ID is so important in duplicate check that if it is not found, we raise error
		****************/
		if(!isset($data_array[0])){
			self::$debug->print_r("co-codes unique identifier not specified");
			return false;
		}
		$co_code_data_arr['curr_co_codes_id'] = trim($data_array[0]);
		
		if(""===$co_code_data_arr['curr_co_codes_id']){
			/***********
			666TODO: we should tell co-codes about this
			**********/
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
			self::$debug->print_r("company name not specified");
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
			$co_code_data_arr['co_code_timestamp'] = trim($data_array[22]);
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
		$action = $this->check_if_company_record_exists($co_code_data_arr['curr_co_codes_id'],$co_code_data_arr['prev_co_codes_id'],$co_code_data_arr['co_code_timestamp'],$co_code_data_arr);
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
	
	sng:27/feb/2013
	It seems, the timestamp based approach is not possible here. The co-code is just not updating the stamp
	and instead of wasting time on that, let us revert to serial scan.
	
	If there is difference in timestamp, we note it. If not, we scan all the fields (the data from co-code csv and what we have in our
	intermediate co-code company database
	
	Return: false on error
	"ignore" if the record is to be skipped
	"update" if the record is to be updated
	"insert" if the record is to be inserted
	************************************/
	private function check_if_company_record_exists($curr_co_codes_id,$prev_co_codes_id,$timestamp,$co_code_data_arr){
		/************
		look for the current identifier, also get the timestamp if found
		$q = "select curr_co_codes_id,co_code_timestamp from ".TP."co_codes_company where curr_co_codes_id='".$this->db->escape_string($curr_co_codes_id)."'";
		
		sng:27/feb/2013
		The code of co-code.com is not updating the timestamp when it is changing the company data. We need to compare field by field
		**************/
		$q = "select * from ".TP."co_codes_company where curr_co_codes_id='".$this->db->escape_string($curr_co_codes_id)."'";
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
				
				sng:27/feb/2013
				Given that co-codes.com is not updating the timestamp, let's eyeball it
				****************/
				if($this->company_fields_same($co_code_data_arr,$stored_rec)){
					/**********
					all same so nothing to update
					**************/
					return "ignore";
				}else{
					/*****************
					some fields are different, update
					*************/
					return "update";
				}
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
				$q = "select id,curr_co_codes_id,co_code_timestamp from ".TP."co_codes_company where curr_co_codes_id='".$this->db->escape_string($prev_co_codes_id)."'";
				
				sng:27/feb/2013
				The code of co-code.com is not updating the timestamp when it is changing the company data. We need to compare field by field
				***********/
				$q = "select * from ".TP."co_codes_company where curr_co_codes_id='".$this->db->escape_string($prev_co_codes_id)."'";
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
					$updt_q = "update ".TP."co_codes_company set curr_co_codes_id='".$this->db->escape_string($curr_co_codes_id)."' where id='".$stored_rec['id']."'";
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
						/********************
						sng:27/feb/2013
						yes, I know that the timestamp is same. Given that co-codes.com is not updating the timestamp, let's eyeball it
						********************/
						if($this->company_fields_same($co_code_data_arr,$stored_rec)){
							/**********
							all same so nothing to update
							**************/
							return "ignore";
						}else{
							/*****************
							some fields are different, update
							*************/
							return "update";
						}
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
	/*********************
	sng:27/feb/2013
	
	We get these data from co-code csv
	$co_code_data_arr['curr_co_codes_id']
	$co_code_data_arr['prev_co_codes_id']
	$co_code_data_arr['co_code_timestamp']
	
	we do not store logo url in our intermediate table, so cannot check this. Beside, change in logo must have chnaged timestamp in the record
	(unless, we have different method of notification)
	$co_code_data_arr['logo_url']
	We check on the following which can change in the database of co-codes.com
	
	
	
	$co_code_data_arr['company_short_name']
	$co_code_data_arr['company_name_sec']
	$co_code_data_arr['company_name_reuters']
	$co_code_data_arr['company_name_bloomberg']
	$co_code_data_arr['company_name_stoxx']
	$co_code_data_arr['company_name_lse']
	$co_code_data_arr['company_name_google']
	$co_code_data_arr['country_code']
	$co_code_data_arr['sector']
	$co_code_data_arr['industry']
	$co_code_data_arr['cik']
	$co_code_data_arr['ric']
	$co_code_data_arr['bloomberg_code']
	$co_code_data_arr['isin']
	$co_code_data_arr['sedol']
	$co_code_data_arr['google_code']
	
	So, what happened to timestamp? Well, it seems the code at co-codes.com is not updating the timestamps properly.
	If they do that, this code is not called at all
	*********************/
	private function company_fields_same($co_code_data_arr,$stored_co_code_record){
		/************
		Just one difference will return false.
		No need to show which fields differed
		
		Why not just compare the arrays? Well there are some fields extra in stored record which will guarantee
		that comparison fails. So this stupid code
		***********/
		if($co_code_data_arr['company_short_name']!=$stored_co_code_record['short_name']){
			return false;
		}
		if($co_code_data_arr['company_name_sec']!=$stored_co_code_record['company_name_sec']){
			return false;
		}
		if($co_code_data_arr['company_name_reuters']!=$stored_co_code_record['company_name_reuters']){
			return false;
		}
		if($co_code_data_arr['company_name_bloomberg']!=$stored_co_code_record['company_name_bloomberg']){
			return false;
		}
		if($co_code_data_arr['company_name_stoxx']!=$stored_co_code_record['company_name_stoxx']){
			return false;
		}
		if($co_code_data_arr['company_name_lse']!=$stored_co_code_record['company_name_lse']){
			return false;
		}
		if($co_code_data_arr['company_name_google']!=$stored_co_code_record['company_name_google']){
			return false;
		}
		if($co_code_data_arr['country_code']!=$stored_co_code_record['country_code']){
			return false;
		}
		if($co_code_data_arr['sector']!=$stored_co_code_record['sector']){
			return false;
		}
		if($co_code_data_arr['industry']!=$stored_co_code_record['industry']){
			return false;
		}
		if($co_code_data_arr['cik']!=$stored_co_code_record['cik']){
			return false;
		}
		if($co_code_data_arr['ric']!=$stored_co_code_record['ric']){
			return false;
		}
		if($co_code_data_arr['bloomberg_code']!=$stored_co_code_record['bloomberg_code']){
			return false;
		}
		if($co_code_data_arr['isin']!=$stored_co_code_record['isin']){
			return false;
		}
		if($co_code_data_arr['sedol']!=$stored_co_code_record['sedol']){
			return false;
		}
		if($co_code_data_arr['google_code']!=$stored_co_code_record['google_ticker']){
			return false;
		}
		return true;
	}
	
	/******************
	Since the steps are same, we use a flag to produce the queries
	******************/
	private function insert_update_company_record($data_arr,$update=false){
		$q = "";
		
		/**********************
		We blindly download the logo, without checking whether the name is same or not. In fact, it may happen that
		the logo has changed but its URL is same. So, we just download
		
		sng:7/mar/2013
		We need to know if we need to refetch it later
		*****************/
		$logo_name = "";
		$logo_refetch_needed = false;
		$logo_ok = self::download_logo($data_arr['logo_url'],$logo_name,$logo_refetch_needed);
		
		if(!$logo_ok){
			$logo_name = "";
		}
		/*********************************
		The case of the logo.
		If we are inserting the record, there is no problem. If we are updating the records then, we need to consider:
		there is no logo (in our table) and admin change logo [no prob, our code has downloaded the logo]
		
		there is no logo (in our table) and admin decide that there should be no logo [nothing is downloaded previously, so no issue]
		
		there is logo (in our table) and admin change logo (and give it same name and hence logo url)
		there is logo (in our table) and admin change logo (and give it a different name)
		[We have downloaded the logo and stored it with new name, so we can delete the old logo from thumbnails folder]
		
		there is logo (in our table) and admin decide that actually there is no logo (and in the csv, the field is blank)
		[We have downloaded the prev logo, and we need to delete that]
		
		sng:8/mar/2013
		Idiot. It will create 'invalid logo' in suggestion table (should we implement that, so NO DELETE
		*******************/
		
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
			we are updating. since we do not know wheher we already synced the rec previously
			or not, we do not set the company id, but set in_sync to n so that we compare
			this with the company table
			
			NOTE: if the ccsid has changed, we already placed the curr ccsid, so we can check with the curr_co_codes_id
			***************/
			$temp_q.=",in_sync='n' WHERE curr_co_codes_id='".$this->db->escape_string($data_arr['curr_co_codes_id'])."'";
		}
		$q.=$temp_q;
		
		$ok = $this->db->mod_query($q);
		if(!$ok){
			return false;
		}
		
		/****************************
		sng:7/mar/2013
		Now that we have inserted a new record OR updated an existing record, we check if we need a logo refetch or not.
		Refetch may be needed if we have failed to get the logo for now. The idea is to try again later.
		
		We need the logo url and the record id
		**************************/
		if($logo_refetch_needed){
			$co_codes_company_id = 0;
			if(!$update){
				/**********************
				we are inserting, so let's get the last insert id
				************/
				$co_codes_company_id = $this->db->last_insert_id();
			}else{
				/*******************
				we are updating, so get the id using the curr_co_codes_id
				************/
				$id_q = "select id from ".TP."co_codes_company WHERE curr_co_codes_id='".$this->db->escape_string($data_arr['curr_co_codes_id'])."'";
				$id_ok = $this->db->select_query($id_q);
				if(!$id_ok){
					/************
					well bad luck, anyway, since all the other steps are done, we get out
					*************/
					return true;
				}
				if(!$this->db->has_row()){
					/************
					well bad luck, anyway, since all the other steps are done, we get out
					*************/
					return true;
				}
				$id_row = $this->db->get_row();
				$co_codes_company_id = $id_row['id'];
			}
			if(!$co_codes_company_id){
				/************
				well bad luck, anyway, since all the other steps are done, we get out
				*************/
				return true;
			}
			/*********************
			sng:1/apr/2013
			No this is not april fool joke. Duplication can happen. Of course, we have set unique key on co_codes_company_id
			so insert with duplicate co_codes_company_id will fail.
			However, we can do better, we can update the record
			
			How can there be duplicate?
			Say on 1/apr the code fails to fetch logo for record 1.
			The record is inserted
			
			On 2/Apr, the logo was changed, the timestamp in csv changed. During import, the code again fails to fetch logo for record 1.
			The record is inserted again
			
			Without the unique constraint and update clause, the blind insert will insert 2 records for id=1
			However, the update clause will not work without unique index
			**************************/
			$logo_q = "insert into ".TP."co_codes_company_refetch_logo set co_codes_company_id='".$co_codes_company_id."',logo_url='".$data_arr['logo_url']."',num_attempt='0' on duplicate key update logo_url='".$data_arr['logo_url']."',num_attempt='0'";
			$this->db->mod_query($logo_q);
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
			self::$debug->print_r("error in query");
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
		
		sng:1/mar/2013
		We also maintain a mapping table that match co-codes industry name to our name. That way, if there is a mismatch
		we can look it up.
		*****/
		$sectors = array();
		$q = "select distinct sector from ".TP."sector_industry_master";
		$ok = $this->db->select_query($q);
		if(!$ok){
			self::$debug->print_r("error in query");
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
			self::$debug->print_r("error in query");
			return false;
		}
		$cnt = $this->db->row_count();
		for($i=0;$i<$cnt;$i++){
			$row = $this->db->get_row();
			$industries[] = $row['industry'];
		}
		/***********************************************
		sng:1/mar/2013
		**********/
		$co_code_industry_name_mapping = array();
		$q = "select * from ".TP."co_codes_industry";
		$ok = $this->db->select_query($q);
		if(!$ok){
			self::$debug->print_r("error in query");
			return false;
		}
		$cnt = $this->db->row_count();
		for($i=0;$i<$cnt;$i++){
			$row = $this->db->get_row();
			$co_code_industry_name_mapping[$row['co_codes_name']] = $row['our_name'];
		}
		/*********************************************/
		$q = "select * from ".TP."co_codes_company where in_sync='n'";
		$ok = $this->db->select_query($q);
		if(!$ok){
			self::$debug->print_r("error in query");
			return false;
		}
		$co_code_company_results = $this->db->get_result_set();
		$co_code_data_cnt = $co_code_company_results->row_count();
		if(0==$co_code_data_cnt){
			self::$debug->print_r("no new company data from co-codes");
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
					/**************
					we let co-code know about this
					****************/
					self::$debug->print_r("unknown country code ".$co_code_company_data['country_code']." for ".$co_code_company_data['curr_co_codes_id']);
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
					/***********
					We let co-codes know about this
					****************/
					self::$debug->print_r("unknown sector ".$co_code_company_data['sector']." for ".$co_code_company_data['curr_co_codes_id']);
				}else{
					$co_code_company['sector'] = $co_code_company_data['sector'];
				}
			}
			
			$co_code_company['industry'] = "";
			if(""!=$co_code_company_data['industry']){
				if(!in_array($co_code_company_data['industry'],$industries)){
					/************************
					sng:1/mar/2013
					check the mapping
					**********/
					if(!array_key_exists($co_code_company_data['industry'],$co_code_industry_name_mapping)){
						/******************
						not in our list, not in mapping, we let co-codes know about this
						*****************/
						self::$debug->print_r("unknown industry ".$co_code_company_data['industry']." for ".$co_code_company_data['curr_co_codes_id']);
					}else{
						/********************
						get our name from the mapping
						******************/
						$co_code_company['industry'] = $co_code_industry_name_mapping[$co_code_company_data['industry']];
					}
				}else{
					/***********
					This is in our list, so this industry name is valid
					**************/
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
					self::$debug->print_r("cannot store corrective suggestion");
					continue;
				}
				if(!$company_id_exists){
					/******************************
					since we do not know why this is the case, we could log an error here or
					we can create a new company record, update the company_id in the co-code table
					666 - for now, since we are not deleting any company, we assume that this will not happen
					***********************/
					self::$debug->print_r("company sync error ".$co_code_company_data['company_id']." not found in our company table");
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
				666 for now, since we are not adding company from another source, this will not happen
				******************/
				$exists = false;
				$ok = $this->company_obj->company_exists($co_code_company,$exists);
				if(!$ok){
					self::$debug->print_r("cannot check if the company exists or not");
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
						self::$debug->print_r("error adding company");
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
	
	private function process_row_data_for_nasdaq_eq($data_array){
		/************
		first we make sure that we have the required number of columns
		****************/
		$col_cnt = count($data_array);
		if($col_cnt!==self::$num_cols_in_nasdaq_eq_csv){
			self::$debug->print_r("invalid number of columns");
			return false;
		}
		$co_code_data_arr = array();
		/****************************************
		Col_0: co-code unique code "i9501"
		
		The i is for IPO, the s is for SPO
		This acts as our key, so this has to be there
		***********************/
		if(!isset($data_array[0])){
			$co_code_data_arr['co_code_unique_code'] = "";
		}else{
			$co_code_data_arr['co_code_unique_code'] = trim($data_array[0]);
		}
		if(""===$co_code_data_arr['co_code_unique_code']){
			self::$debug->print_r("co-codes nasdaq eq deal unique identifier not specified");
			return false;
			/***********
			666TODO: we should tell co-codes about this record, but how to get the record number?
			**********/
		}
		
		/*************************************
		Col_1: CCS Identifier "SEC001093567"
		
		We assume that the detail of the company doing the deal is already there in co-code side and they are giving us the ccsid
		and we can use that to lookup the detail of the company.
		We do not use any detail here to create company record for now.
		666 Later, we need a way to handle this
		***********/
		$co_code_data_arr['curr_co_code_company_id'] = "";
		if(isset($data_array[1])){
			$co_code_data_arr['curr_co_code_company_id'] = trim($data_array[1]);
		}
		if(""===$co_code_data_arr['curr_co_code_company_id']){
			self::$debug->print_r("Deal company code not specified for ".$co_code_data_arr['co_code_unique_code']);
			return false;
		}
		/**********************************
		Col_2: Old CCS Identifier ""
		We can ignore this
		*****************/
		
		/*******************
		At least one must be present
		/*****************************************
		Information about the deal company
		What we will do is use the company ccsid to get the company info.
		(666 - this company may or may not be there in the co-code company table)
		(if the company is not found, or if the ccsid here is blank, we just skip this record)
		We ignore the following
		
		Col_3: Short name "1 800 Contacts"
		Col_4: Country code "US"
		Col_5: Country name "UNITED STATES"
		Col_6: Industry ""
		Col_7: Supersector ""
		Col_8: Sector ""
		Col_9: Subsector ""
		Col_10: CIK "0001050122"
		Col_11: RIC ""
		Col_12: Bloomberg Ticker ""
		Col_13: ISIN ""
		Col_14: SEDOL ""
		Col_15: Google Ticker ""
		Col_16: Date when this company record inserted/updated (NOT when the deal record was inserted/updated "2012-10-05 10:39:36"
		*************************/
		
		/********************************
		Col_17: Date in yyyy-mm-dd when the deal took place "1998-02-10"
		if blank, we set to 0000-00-00 00:00:00
		***********/
		if(!isset($data_array[17])){
			$co_code_data_arr['date_of_deal'] = "";
		}else{
			$co_code_data_arr['date_of_deal'] = trim($data_array[17]);
		}
		if(""===$co_code_data_arr['date_of_deal']){
			/**********
			we store only date for date of deal
			**********/
			$co_code_data_arr['date_of_deal'] = "0000-00-00";
		}
		
		/***************************
		Col_18: Deal size in USD "27500000.00"
		If this is blank, we set it to 0, meaning 'value undisclosed'
		*************/
		if(!isset($data_array[18])){
			$co_code_data_arr['deal_value'] = 0.0;
		}else{
			$co_code_data_arr['deal_value'] = trim($data_array[18]);
		}
		if(""===$co_code_data_arr['deal_value']){
			$co_code_data_arr['deal_value'] = 0.0;
		}else{
			$co_code_data_arr['deal_value'] = (float)$co_code_data_arr['deal_value'];
		}
		
		/**************************
		Col_19: Type IPO or SPO "IPO"
		IPO is Equity: Common Equity: IPOs
		SPO is Equity: Common Equity: Secondaries
		This could be blank. In that case we will treat the deal as Equity: Common Equity
		*******************/
		$co_code_data_arr['deal_type'] = "";
		if(isset($data_array[19])){
			$co_code_data_arr['deal_type'] = $data_array[19];
		}
		
		/********************************
		Col_20: Lead underwriters "Morgan Keegan and Co., Inc;McDonald and Co. Securities, Inc;"
		These are banks with role of Bookrunner
		Could be blank
		****************/
		$co_code_data_arr['lead_underwriters'] = "";
		if(isset($data_array[20])){
			$co_code_data_arr['lead_underwriters'] = $data_array[20];
		}
		
		/**********************************
		Col_21: Company counsel "Kirkland and Ellis;"
		These are law firms with role of 'Advisor to Company'
		Could be blank
		**************/
		$co_code_data_arr['company_councel'] = "";
		if(isset($data_array[21])){
			$co_code_data_arr['company_councel'] = $data_array[21];
		}
		
		/***************************************
		Col_22: Underwriters counsel (these underwriters and councels should be separated by ';') "Squire, Sanders and Dempsey L.L.P;"
		These are law firms with role of 'Advisor to Banks'
		could be blank
		********************/
		$co_code_data_arr['underwriter_councel'] = "";
		if(isset($data_array[22])){
			$co_code_data_arr['underwriter_councel'] = $data_array[22];
		}
		
		/**********************************
		Col_23: Date when this deal record was inserted/updated (NOT when the company record was inserted/updated) "2013-01-18 10:00:30"
		************/
		if(!isset($data_array[23])){
			$co_code_data_arr['co_code_timestamp'] = "";
		}else{
			$co_code_data_arr['co_code_timestamp'] = trim($data_array[23]);
		}
		if(""===$co_code_data_arr['co_code_timestamp']){
			$co_code_data_arr['co_code_timestamp'] = "0000-00-00 00:00:00";
		}
		
		/********************************
		Col_24: Long company name
		We ignore this. We get the data of the company doing the deal from co codes unique id for company
		*************************/
		
		/***********************************
		We request the deal data each day. Problem is, how do we know that we have already stored the record?
		This is where the the concept of identifier helps us.
		Each record in the nasdaq csv is tagged with this unique id along with a timestamp (yyyy-mm-dd hh:mm:ss).
		We check these against our table and decide whether to ignore the record, insert the record, update the record.
		*****************************/
		$action = $this->check_if_nasdaq_eq_record_exists($co_code_data_arr['co_code_unique_code'],$co_code_data_arr['co_code_timestamp']);
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
			$ok = $this->insert_update_nasdaq_eq_record($co_code_data_arr,true);
			if(!$ok){
				return false;
			}else{
				return true;
			}
		}
		if("insert"==$action){
			$ok = $this->insert_update_nasdaq_eq_record($co_code_data_arr,false);
			if(!$ok){
				return false;
			}else{
				return true;
			}
		}
		/**********************
		Either we can update the real tables or we can put the data in intermediate table and then update the real tables.
		If we use intermediate table, we can store the timestamp. That way, in the next parsing, we do not bother with data rows
		that has not changed. Of course, we bother with change in deal information only (not company information)
		******************************/
		return true;
	}
	/***************************
	1) check for co_code_unique_code
	1.1) found, check timestamp
	1.1.1) timestamp same - the record has not changed - ignore
	1.1.2) timestamp different - the record has changed - update
	1.2) not found - this is new record, insert
	
	Return: false on error
	"ignore" if the record is to be skipped
	"update" if the record is to be updated
	"insert" if the record is to be inserted
	*******************************************/
	private function check_if_nasdaq_eq_record_exists($co_code_unique_code,$timestamp){
		$q = "select co_code_unique_code,co_code_timestamp from ".TP."co_codes_nasdaq_eq_deals where co_code_unique_code='".$this->db->escape_string($co_code_unique_code)."'";
		$ok = $this->db->select_query($q);
		if(!$ok){
			return false;
		}
		$row_count = $this->db->row_count();
		/***************
		We already checked that the $co_code_unique_code is non-blank.
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
			/******************
			Not found, so this is a new deal record
			**************/
			return "insert";
		}
	}
	
	private function insert_update_nasdaq_eq_record($data_arr,$update=false){
		$q = "";
		
		if($update){
			$q.="update ".TP."co_codes_nasdaq_eq_deals set ";
		}else{
			$q.="insert into ".TP."co_codes_nasdaq_eq_deals set co_code_unique_code='".$this->db->escape_string($data_arr['co_code_unique_code'])."',";
		}
		$temp_q = "curr_co_code_company_id='".$this->db->escape_string($data_arr['curr_co_code_company_id'])."'
		,date_of_deal='".$data_arr['date_of_deal']."'
		,deal_value='".$data_arr['deal_value']."'
		,deal_type='".$this->db->escape_string($data_arr['deal_type'])."'
		,lead_underwriters='".$this->db->escape_string($data_arr['lead_underwriters'])."'
		,company_councel='".$this->db->escape_string($data_arr['company_councel'])."'
		,underwriter_councel='".$this->db->escape_string($data_arr['underwriter_councel'])."'
		,co_code_timestamp='".$data_arr['co_code_timestamp']."'";
		
		if(!$update){
			/**********
			we are inserting, so we need to transfer the record and sync these two
			************/
			$temp_q.=",deal_id='0',deal_in_sync='n'";
		}else{
			/**********
			we are updating. since we do not know wheher we already synced the rec previously
			or not, we do not set the deal id, but set deal_in_sync to n so that we compare
			this with the company table
			***************/
			$temp_q.=",deal_in_sync='n' WHERE co_code_unique_code='".$this->db->escape_string($data_arr['co_code_unique_code'])."'";
		}
		$q.=$temp_q;
		
		$ok = $this->db->mod_query($q);
		if(!$ok){
			return false;
		}
		
		return true;
	}
	
	/*****************
	transfer the nasdaq equity deal data from the co_codes_nasdaq_eq_deals table to transaction table
	we consider only those records which has deal_in_sync - n
	**********************/
	private function sync_nasdaq_eq_data(){
		$q = "select * from ".TP."co_codes_nasdaq_eq_deals where deal_in_sync='n'";
		$ok = $this->db->select_query($q);
		if(!$ok){
			return false;
		}
		$co_code_deal_data_results = $this->db->get_result_set();
		$co_code_deal_data_cnt = $co_code_deal_data_results->row_count();
		if(0==$co_code_deal_data_cnt){
			self::$debug->print_r("no new nasdaq equity data from co-codes");
			return true;
		}
		/****************************************************/
		for($i=0;$i<$co_code_deal_data_cnt;$i++){
			$co_code_deal_data = $co_code_deal_data_results->get_row();
			$deal_data = array();
			
			$deal_data['date_of_deal'] = $co_code_deal_data['date_of_deal'];
			/********
			Since we have only one date, we assume it is date when the deal was closed and set in_calculation accordingly
			***********/
			$deal_data['date_closed'] = $deal_data['date_of_deal'];
			/**************************************************************/
			$deal_data['currency'] = "USD";
			/******************************************************/
			$deal_data['deal_cat_name'] = "Equity";
			/*******
			The subtype Equity is now 'Common Equity'
			The sub sub type IPO is now IPOs
			The sub sub type Additional is now Secondaries
			****/
			$deal_data['deal_subcat1_name'] = "Common Equity";
			
			$deal_data_deal_type = $co_code_deal_data['deal_type'];
			if($deal_data_deal_type=="IPO"){
				$deal_data['deal_subcat2_name'] = "IPOs";
			}else if($deal_data_deal_type=="SPO"){
				$deal_data['deal_subcat2_name'] = "Secondaries";
			}else{
				$deal_data['deal_subcat2_name'] = "";
			}
			/*******************************************************/
			$deal_data_deal_value = $co_code_deal_data['deal_value'];
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
				/******************
				666: In our database, since we are storing in 6 decimal place, we round up here
				************/
				$deal_data['value_in_billion'] = round($deal_data['value_in_billion'],6);
				
				$deal_data['value_range_id'] = 0;
				//this takes value in million
				$ok = $this->trans_obj->front_get_value_range_id_from_value($deal_data_deal_value/1000000,$deal_data['value_range_id']);
				if(!$ok){
					return false;
				}
			}
			/******************
			666 for now, we only consider those deals where we have the co-code company unique identifier
			*******************/
			$deal_data['curr_co_code_company_id'] = $co_code_deal_data['curr_co_code_company_id'];
			
			$deal_data['lead_underwriters'] = $co_code_deal_data['lead_underwriters'];
			$deal_data['company_councel'] = $co_code_deal_data['company_councel'];
			$deal_data['underwriter_councel'] = $co_code_deal_data['underwriter_councel'];
			/**************************************************************
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
			/**********************************************
			We are ready for the transfer
			
			check if we already transferred this record in prev run or not.
			we will know that if the deal_id is not 0 in co-codes nasdaq eq deals table
			*********************************/
			if($co_code_deal_data['deal_id']!=0){
				/******************
				We have this record in our transaction table. This is an update.
				
				if all ok, we set is_sync = y
				***************/
				self::$debug->print_r("deal id ".$co_code_deal_data['deal_id']." needs to be updated and synced");
				
				$deal_id_exists = false;
				/****************
				sng:20/mar/2013
				Now we use two flags
				*****************/
				$recompute_tombstone_points_for_banks = false;
				$recompute_tombstone_points_for_law_firms = false;
				$ok = $this->trans_obj->correction_suggested($co_code_deal_data['deal_id'],$deal_data,$deal_id_exists,$recompute_tombstone_points_for_banks,$recompute_tombstone_points_for_law_firms);
				if(!$ok){
					self::$debug->print_r("cannot store corrective suggestion for deal ".$co_code_deal_data['deal_id']);
					continue;
				}
				if(!$deal_id_exists){
					/******************************
					since we do not know why this is the case, we could log an error here or
					we can create a new transaction record, update the deal_id in the co-code table
					666 - for now, since we are not deleting any deal, we assume that this will not happen
					***********************/
					self::$debug->print_r("deal sync error ".$co_code_deal_data['deal_id']." not found in our transaction table");
					continue;
				}
				/********
				no error, so assumed synced
				sng:19/mar/2013
				Not so fast
				
				sng:20/mar/2013
				**********/
				/************
				sng:22/mar/2013
				by default, we assume that we can sync
				one error and we set to false
				*****************/
				$mark_as_synced = true;
				if($recompute_tombstone_points_for_banks){
					$ok_bank = $this->trans_obj->recompute_tombstone_points_for_deal($co_code_deal_data['deal_id'],"bank");
					if(!$ok_bank){
						self::$debug->print_r("cannot recompute tombstone points for deal ".$co_code_deal_data['deal_id']);
						$mark_as_synced = false;
					}
				}
				if($recompute_tombstone_points_for_law_firms){
					$ok_law_firm = $this->trans_obj->recompute_tombstone_points_for_deal($co_code_deal_data['deal_id'],"law firm");
					if(!$ok_law_firm){
						self::$debug->print_r("cannot recompute tombstone points for deal ".$co_code_deal_data['deal_id']);
						$mark_as_synced = false;
					}
				}
				if(!$mark_as_synced){
					/****************
					sng:22/mar/2013
					we do not sync
					**********/
					continue;
				}
				/*************************
				all ok, sync
				**********************/
				$q = "update ".TP."co_codes_nasdaq_eq_deals set deal_in_sync='y' where id='".$co_code_deal_data['id']."'";
				$ok = $this->db->mod_query($q);
				if($ok){
					self::$debug->print_r("deal id ".$co_code_deal_data['deal_id']." updated and synced");
				}
				continue;
			}else{
				/*****************************
				this record was not transferred from co-code nasdaq eq
				now let us see if this deal exists in our transaction table (because it may happen
				that we have added the deal from another source
				666 for now, since we are not adding deal from another source, this will not happen
				******************/
				$exists = false;
				$ok = $this->trans_obj->deal_exists($deal_data,$exists);
				if(!$ok){
					self::$debug->print_r("cannot check if the deal exists or not");
					continue;
				}
				if(!$exists){
					
					/**************************
					the deal does not exists in our transaction table
					we add this deal
					we add the deal companies
					we add the deal partners
					we set original suggestion for the deal
					we set original suggestion for the deal companies
					we set original suggestion for the deal partners
					
					we get the deal id
					we update the co-code nasdaq eq deal table with deal_in_sync=y and set the deal_id
					***********************/
					$created_deal_id = 0;
					$ok = $this->trans_obj->add_deal($deal_data,$created_deal_id);
					if(!$ok){
						self::$debug->print_r("error adding deal");
						continue;
					}else{
						$q = "update ".TP."co_codes_nasdaq_eq_deals set deal_id='".$created_deal_id."',deal_in_sync='y' where id='".$co_code_deal_data['id']."'";
						$this->db->mod_query($q);
						/*******
						never mind if not OK
						************/
						continue;
					}
				}else{
					/*******************
					$co_code_deal_data['deal_id'] is 0. This means we have not moved the deal
					record from co-code nasdaq eq deal table to our table (thereby creating the deal)
					yet the deal exists in our table.
					This means, the deal was created from another source.
					since the deal exists, we might put its deal id to the corresponding record in co-codes table
					666 Since we are not adding any deal via front end, we can leave this for now.
					
					************/
				}
			}
			/*********************
			next record
			***********************/
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
							self::$debug->print_r("aborted");
							break;
						case 2:
							self::$debug->print_r("timeout");
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
		self::$debug->print_r("processing csv file");
		
		ini_set('auto_detect_line_endings',true);
		
		$r_handle = fopen($source,'r');
		if(!$r_handle){
			self::$debug->print_r("cannot open the source file");
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
				self::$debug->print_r("error processing row ".$row_num);
			}else{
				$has_row = true;
				/*****
				since we do not set this to false in case of error, a single correct row will set this to true
				and we will have usable data
				**********/  
			}
			$row_num++;
		}
		fclose($r_handle);
		self::$debug->print_r("finished processing csv file");
		
		if($has_row){
			return true;
		}else{
			return false;
		}
	}
	/**********************
	sng:7/mar/2013
	We now want to trap the case where we have the logo but could not fetch it.
	We will store the logo url in a table so that we can refetch it later
	
	Normally refetch is not needed.
	***********************/
	private static function download_logo($url,&$logo_name,&$refetch_needed){
		$refetch_needed = false;
		
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
			self::$debug->print_r("invalid image extension ".$extension);
			return false;
		}
		
		$destination = FILE_PATH."/from_co-codes/logo/".$original_name;
		
		$ok = self::fetch_and_store_remote_file($url,$destination);
		if(!$ok){
			/****************
			we need to log that there was a logo but we failed to fetch it.
			*****************/
			self::$debug->print_r("could not fetch the logo from ".$url);
			/**************
			sng:7/mar/2013
			We also notify that we need a refetch on this logo
			***************/
			$refetch_needed = true;
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
666TODO
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
			co_codes::$debug->print_r("cannot connect to database");
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
		$q = "select * from ".TP."company where company_id='".$company_id."'";
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
			$moved = copy(FILE_PATH."/from_co-codes/logo/thumbnails/".$data_arr['logo'],FILE_PATH."/uploaded_img/logo/thumbnails/".$data_arr['logo']);
			if($moved){
				$logo = $data_arr['logo'];
			}
		}
		
		$date_added = date("Y-m-d H:i:s");
		
		$q = "insert into ".TP."company set name='".$this->db->escape_string($data_arr['company_name'])."'
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
		$q = "insert into ".TP."company_suggestions set
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
			$id_q = "insert into ".TP."company_identifiers set company_id='".$company_id."',identifier_id='".$identifiers['cik']."',value='".$this->db->escape_string($cik)."'";
			$ok = $this->db->mod_query($id_q);
			if($ok){
				$identifier_q.=",('".$company_id."','0','".$date_added."','".$identifiers['cik']."','".$this->db->escape_string($cik)."','n')";
			}
		}
		
		if($ric!==""){
			$id_q = "insert into ".TP."company_identifiers set company_id='".$company_id."',identifier_id='".$identifiers['ric']."',value='".$this->db->escape_string($ric)."'";
			$ok = $this->db->mod_query($id_q);
			if($ok){
				$identifier_q.=",('".$company_id."','0','".$date_added."','".$identifiers['ric']."','".$this->db->escape_string($ric)."','n')";
			}
		}
		
		if($bloomberg!==""){
			$id_q = "insert into ".TP."company_identifiers set company_id='".$company_id."',identifier_id='".$identifiers['bloom']."',value='".$this->db->escape_string($bloomberg)."'";
			$ok = $this->db->mod_query($id_q);
			if($ok){
				$identifier_q.=",('".$company_id."','0','".$date_added."','".$identifiers['bloom']."','".$this->db->escape_string($bloomberg)."','n')";
			}
		}
		
		if($isin!==""){
			$id_q = "insert into ".TP."company_identifiers set company_id='".$company_id."',identifier_id='".$identifiers['isin']."',value='".$this->db->escape_string($isin)."'";
			$ok = $this->db->mod_query($id_q);
			if($ok){
				$identifier_q.=",('".$company_id."','0','".$date_added."','".$identifiers['isin']."','".$this->db->escape_string($isin)."','n')";
			}
		}
		
		if($sedol!==""){
			$id_q = "insert into ".TP."company_identifiers set company_id='".$company_id."',identifier_id='".$identifiers['sedol']."',value='".$this->db->escape_string($sedol)."'";
			$ok = $this->db->mod_query($id_q);
			if($ok){
				$identifier_q.=",('".$company_id."','0','".$date_added."','".$identifiers['sedol']."','".$this->db->escape_string($sedol)."','n')";
			}
		}
		
		if($google!==""){
			$id_q = "insert into ".TP."company_identifiers set company_id='".$company_id."',identifier_id='".$identifiers['goog']."',value='".$this->db->escape_string($google)."'";
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
			$identifier_q = "INSERT INTO ".TP."company_identifiers_suggestions(company_id,suggested_by,date_suggested,identifier_id,`value`,is_correction) values".$identifier_q;
			$this->db->mod_query($identifier_q);
		}
		/**********************************/
		return true;
	}
	
	/**************
	here we assume that it is admin who is suggesting the correction
	
	It might happen that the company with this ID is not there.
	Since we are not sure that really happened, we just send a flag
	
	sng:7/mar/2013
	NOTE
	We have now decided that data from co-codes will overwrite existing data
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
		
		$q = "select identifier_id,`value` from ".TP."company_identifiers where company_id='".$company_id."'";
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
				$q = "update ".TP."company set name='".$this->db->escape_string($suggested_name)."' where company_id='".$company_id."'";
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
					
					sng:1/mar/2013
					It seems, there will be name change. We should force the name change so that the latest name is visible.
					666 Is this a good idea? Also, should we make some note in some table that the company Novo is now Nova?
					*********/
					$q = "update ".TP."company set name='".$this->db->escape_string($suggested_name)."' where company_id='".$company_id."'";
					$ok = $this->db->mod_query($q);
					if($ok){
						$changes_made.=",name updated";
					}else{
						$changes_made.=",name suggested";
					}
					/************************/
				}
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
				$q = "update ".TP."company set hq_country='".$this->db->escape_string($suggested_hq_country)."' where company_id='".$company_id."'";
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
					sng:7/mar/2013
					Now we update
					*********/
					$q = "update ".TP."company set hq_country='".$this->db->escape_string($suggested_hq_country)."' where company_id='".$company_id."'";
					$ok = $this->db->mod_query($q);
					if($ok){
						$changes_made.=",hq country updated";
					}else{
						$changes_made.=",hq country suggested";
					}
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
				$q = "update ".TP."company set sector='".$this->db->escape_string($suggested_sector)."' where company_id='".$company_id."'";
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
					sng:7/mar/2013
					We now update
					*********/
					$q = "update ".TP."company set sector='".$this->db->escape_string($suggested_sector)."' where company_id='".$company_id."'";
					$ok = $this->db->mod_query($q);
					if($ok){
						$changes_made.=",sector updated";
					}else{
						$changes_made.=",sector suggested";
					}
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
				$q = "update ".TP."company set industry='".$this->db->escape_string($suggested_industry)."' where company_id='".$company_id."'";
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
					sng:7/mar/2013
					*********/
					$q = "update ".TP."company set industry='".$this->db->escape_string($suggested_industry)."' where company_id='".$company_id."'";
					$ok = $this->db->mod_query($q);
					if($ok){
						$changes_made.=",industry updated";
					}else{
						$changes_made.=",industry suggested";
					}
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
					$q = "update ".TP."company set logo='".$suggested_logo."' where company_id='".$company_id."'";
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
					sng:7/mar/2013
					Now we update the logo
					*********/
					
					$moved = copy(FILE_PATH."/from_co-codes/logo/thumbnails/".$suggested_logo,FILE_PATH."/uploaded_img/logo/thumbnails/".$suggested_logo);
					if($moved){
						$q = "update ".TP."company set logo='".$suggested_logo."' where company_id='".$company_id."'";
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
				}
			}
		}else{
			/********************
			sng:8/mar/2013
			If there is no logo suggestion in co-codes
			it could be because admin has removed the logo
			or maybe we failed to fetch
			We take a chance. We blank out the logo, but we do not delete the logo file. It will mess up the suggestions (which still
			refers to the old logo)
			666 For now we assume that no front end member is uploading any logo
			*******************/
			$q = "update ".TP."company set logo='' where company_id='".$company_id."'";
			$ok = $this->db->mod_query($q);
			if($ok){
				$changes_made.=",logo removed";
			}else{
				$changes_made.=",logo removal suggested";
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
		$q = "insert into ".TP."company_suggestions set
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
				$id_q = "insert into ".TP."company_identifiers set company_id='".$company_id."',identifier_id='".$identifiers['cik']."',`value`='".$this->db->escape_string($cik)."'";
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
					$id_q = "update ".TP."company_identifiers set `value`='".$this->db->escape_string($cik)."' where company_id='".$company_id."' and identifier_id='".$identifiers['cik']."'";
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
						sng:3/mar/2013
						Now we update
						****************/
						$id_q = "update ".TP."company_identifiers set `value`='".$this->db->escape_string($cik)."' where company_id='".$company_id."' and identifier_id='".$identifiers['cik']."'";
						$ok = $this->db->mod_query($id_q);
						if($ok){
							$suggestion_status_note = "updated";
						}else{
							$suggestion_status_note = "suggested";
						}
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
				$id_q = "insert into ".TP."company_identifiers set company_id='".$company_id."',identifier_id='".$identifiers['ric']."',`value`='".$this->db->escape_string($ric)."'";
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
					$id_q = "update ".TP."company_identifiers set `value`='".$this->db->escape_string($ric)."' where company_id='".$company_id."' and identifier_id='".$identifiers['ric']."'";
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
						sng:3/mar/2013
						Now we update
						****************/
						$id_q = "update ".TP."company_identifiers set `value`='".$this->db->escape_string($ric)."' where company_id='".$company_id."' and identifier_id='".$identifiers['ric']."'";
						$ok = $this->db->mod_query($id_q);
						if($ok){
							$suggestion_status_note = "updated";
						}else{
							$suggestion_status_note = "suggested";
						}
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
				$id_q = "insert into ".TP."company_identifiers set company_id='".$company_id."',identifier_id='".$identifiers['bloom']."',`value`='".$this->db->escape_string($bloomberg)."'";
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
					$id_q = "update ".TP."company_identifiers set `value`='".$this->db->escape_string($bloomberg)."' where company_id='".$company_id."' and identifier_id='".$identifiers['bloom']."'";
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
						sng:7/mar/2013
						Now we update
						****************/
						$id_q = "update ".TP."company_identifiers set `value`='".$this->db->escape_string($bloomberg)."' where company_id='".$company_id."' and identifier_id='".$identifiers['bloom']."'";
						$ok = $this->db->mod_query($id_q);
						if($ok){
							$suggestion_status_note = "updated";
						}else{
							$suggestion_status_note = "suggested";
						}
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
				$id_q = "insert into ".TP."company_identifiers set company_id='".$company_id."',identifier_id='".$identifiers['isin']."',`value`='".$this->db->escape_string($isin)."'";
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
					$id_q = "update ".TP."company_identifiers set `value`='".$this->db->escape_string($isin)."' where company_id='".$company_id."' and identifier_id='".$identifiers['isin']."'";
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
						sng:7/mar/2013
						Now we update
						****************/
						$id_q = "update ".TP."company_identifiers set `value`='".$this->db->escape_string($isin)."' where company_id='".$company_id."' and identifier_id='".$identifiers['isin']."'";
						$ok = $this->db->mod_query($id_q);
						if($ok){
							$suggestion_status_note = "updated";
						}else{
							$suggestion_status_note = "suggested";
						}
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
				$id_q = "insert into ".TP."company_identifiers set company_id='".$company_id."',identifier_id='".$identifiers['sedol']."',`value`='".$this->db->escape_string($sedol)."'";
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
					$id_q = "update ".TP."company_identifiers set `value`='".$this->db->escape_string($sedol)."' where company_id='".$company_id."' and identifier_id='".$identifiers['sedol']."'";
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
						sng:7/mar/2013
						Now we update
						****************/
						$id_q = "update ".TP."company_identifiers set `value`='".$this->db->escape_string($sedol)."' where company_id='".$company_id."' and identifier_id='".$identifiers['sedol']."'";
						$ok = $this->db->mod_query($id_q);
						if($ok){
							$suggestion_status_note = "updated";
						}else{
							$suggestion_status_note = "suggested";
						}
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
				$id_q = "insert into ".TP."company_identifiers set company_id='".$company_id."',identifier_id='".$identifiers['goog']."',`value`='".$this->db->escape_string($google)."'";
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
					$id_q = "update ".TP."company_identifiers set `value`='".$this->db->escape_string($google)."' where company_id='".$company_id."' and identifier_id='".$identifiers['goog']."'";
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
						sng:7/mar/2013
						Now we update
						****************/
						$id_q = "update ".TP."company_identifiers set `value`='".$this->db->escape_string($google)."' where company_id='".$company_id."' and identifier_id='".$identifiers['goog']."'";
						$ok = $this->db->mod_query($id_q);
						if($ok){
							$suggestion_status_note = "updated";
						}else{
							$suggestion_status_note = "suggested";
						}
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
			$identifier_suggestion_q = "INSERT INTO ".TP."company_identifiers_suggestions(company_id,suggested_by,date_suggested,identifier_id,`value`,status_note,is_correction) values".$identifier_suggestion_q;
			$this->db->mod_query($identifier_suggestion_q);
		}
		/********************************************/
		return true;
	}
}
/*****************************
we are using a proxy class in place of the actual transaction class
because cron codes use new db codes and we do not require all features
******************/
class transaction_proxy{
	
	private $db;
	
	private function __construct(){
	}
	
	public static function create(){
		global $g_config;
		
		$temp = db::create($g_config['db_host'],$g_config['db_user'],$g_config['db_password'],$g_config['db_name']);
		if($temp===false){
			co_codes::$debug->print_r("cannot connect to database");
			return false;
		}
		$obj = new transaction_proxy();
		$obj->db = $temp;
		
		return $obj;
	}
	
	/************
	sng:18/feb/2012
	Given a deal value in million, we should be able to get the deal_value_range_id
	For the special value of 0, the range id is 0 (undefined)
	
	see class deal_support
	*************/
	public function front_get_value_range_id_from_value($value_in_million,&$value_range_id){
	
		if($value_in_million == 0){
			$value_range_id = 0;
			return true;
		}
		
		$q = "select value_range_id,lower_value_limit_in_million from ".TP."transaction_value_range_master order by lower_value_limit_in_million desc";
		$ok = $this->db->select_query($q);
		
		if(!$ok){
			return false;
		}
		
		$res_count = $this->db->row_count();
		$slabs = array();
		
		for($i=0;$i<$res_count;$i++){
			$slabs[] = $this->db->get_row();
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
	
	/***************
	666 for now, we assume the deal does not exists in transaction table
	***************/
	public function deal_exists($data_arr,&$exists){
		$exists = false;
		return true;
	}
	
	/****************
	Here we distinguish between the case of db error and deal not found.
	Basically, if the deal id is not in our table, it means that admin has removed
	the deal. We might hanve to handle the case
	****************/
	public function get_deal($deal_id,&$exists,&$data_arr){
		$q = "select * from ".TP."transaction as t left join ".TP."transaction_extra_detail as e ON(t.id=e.transaction_id) where t.id='".$deal_id."'";
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
	/**************
	we have deal data
	We just set the data and do not bother with the original suggestion. It will only make the code complicated at this point.
	In fact, we do not even know whether the functionality is really needed or not.
	
	extra deal data
	deal companies
	deal companies suggestions
	deal partners
	deal partner suggestions
	*****************************/
	public function add_deal($deal_data,&$created_deal_id){
		/****************************
		666 for now, we need the company id first. the explanation is given later
		
		We also need the company name for the suggestion
		**********************/
		$q = "select c.company_id,c.name from ".TP."co_codes_company as cc left join ".TP."company as c on(cc.company_id=c.company_id) where curr_co_codes_id='".$deal_data['curr_co_code_company_id']."' and in_sync='y'";
		$ok = $this->db->select_query($q);
		if(!$ok){
			return false;
		}
		if(!$this->db->has_row()){
			/**************
			666 id of deal company not found, and we do not create company from deal data, so this is an error
			**********/
			co_codes::$debug->print_r("company id of co-code company ".$deal_data['curr_co_code_company_id']." not found");
			return false;
		}
		$row = $this->db->get_row();
		$deal_company_id = $row['company_id'];
		$deal_company_name = $row['name'];
		/*************************
		add the deal data
		************************/
		$date_time_now = date("Y-m-d H:i:s");
		$q = "insert into ".TP."transaction set value_in_billion='".$deal_data['value_in_billion']."'
		,value_range_id='".$deal_data['value_range_id']."'
		,currency='".$deal_data['currency']."'
		,date_of_deal='".$deal_data['date_of_deal']."'
		,deal_cat_name='".$deal_data['deal_cat_name']."'
		,deal_subcat1_name='".$deal_data['deal_subcat1_name']."'
		,deal_subcat2_name='".$deal_data['deal_subcat2_name']."'
		,added_on='".$date_time_now."'
		,last_edited='".$date_time_now."'
		,admin_verified='".$deal_data['admin_verified']."'
		,is_active='".$deal_data['is_active']."'
		,in_calculation='".$deal_data['in_calculation']."'";
		
		$ok = $this->db->mod_query($q);
		if(!$ok){
			return false;
		}
		$created_deal_id = $this->db->last_insert_id();
		$deal_data['deal_id'] = $created_deal_id;
		/*******
		deal added, add extra record
		***********/
		$q = "insert into ".TP."transaction_extra_detail set transaction_id = '".$deal_data['deal_id']."'";
		/*****
		Since we have a single date, it is considered as closing date. If that date is 0, we assume that only 
		announced date was set (although in that case date_announced is merely 0
		***********/
		if($deal_data['date_closed']!="0000-00-00"){
			$q.=",date_closed='".$deal_data['date_closed']."'";
		}
		$ok = $this->db->mod_query($q);
		/*****************
		in front end, we do not bother if this trigger error, so we do the same here - we ignore the error
		******************/
		/********************
		sng:18/mar/2013
		Now we put this as original suggestion
		We assume it is done by admin
		The deals are in USD
		********************/
		$val_in_million = $deal_data['value_in_billion']*1000;
		
		$sq = "insert into ".TP."transaction_suggestions_valuation set deal_id='".$deal_data['deal_id']."'
		,suggested_by='0'
		,date_suggested='".$date_time_now."'
		,value_in_million='".$val_in_million."'
		,currency='USD'
		,exchange_rate='1'
		,status_note=''
		,is_correction='n'";
		
		$ok = $this->db->mod_query($sq);
		/*********
		never mind note, source
		come to companies.
		
		Here we only have a single company as participant.
		We add the id of the company.
		We have no role
		classes/class.transaction_company.php
		front_set_participants_for_deal
		
		666 For now, we only considered those deals where we have the co-code unique identifiers for the deal company
		What we do is, use that id as a key in co-code company table and get the company id (the id we use in our company table).
		Of course, if the records are not sync'ed, we do not get the company id and then we are in trouble.
		Again, for now, we write this code in the beginning to get the company id first (before adding deal data)
		**********************************/
		$q = "insert into ".TP."transaction_companies set transaction_id='".$deal_data['deal_id']."',company_id='".$deal_company_id."'";
		$ok = $this->db->mod_query($q);
		if(!$ok){
			/**************
			bad luck
			666 what we need is transaction here
			**************/
			return false;
		}
		/******************************
		sng:18/mar/2013
		So we have inserted the participant company. We also put a suggestion
		
		For now, we do not have role of the participant
		*******************/
		$q = "insert into ".TP."transaction_companies_suggestions set deal_id='".$deal_data['deal_id']."'
		,suggested_by='0'
		,date_suggested='".$date_time_now."'
		,company_name='".$deal_company_name."'
		,role_id='0'
		,is_correction='n'";
		
		$ok = $this->db->mod_query($q);
		/************
		partners
		partner roles
		adjustd value
		
		banks / law firms
		*******************/
		$ok = $this->process_firms($deal_data,$deal_data['deal_id'],$deal_data['value_in_billion'],$date_time_now);
		if(!$ok){
			/**************
			bad luck
			666 what we need is transaction here
			**************/
			return false;
		}
		return true;
	}
	
	/**************
	here we assume that it is admin who is suggesting the correction
	
	It might happen that the transaction with this ID is not there.
	Since we are not sure that really happened, we just send a flag
	
	NOTE: Do watch out if the deal value has changed. That will require adjustment of tombstone point later
	But then changes in number of firms will require recomputation.
	What we do is, we maintain a flag for the deal and use it to notify the caller.
	(This is one side effect of changing deal data)
	***************/
	public function correction_suggested($deal_id,$data_arr,&$deal_id_exists,&$recompute_tombstone_points_for_banks,&$recompute_tombstone_points_for_law_firms){
		$current_data = NULL;
		$deal_exists = false;
		$ok = $this->get_deal($deal_id,$deal_exists,$current_data);
		if(!$ok){
			return false;
		}
		if(!$deal_exists){
			$deal_id_exists = false;
			return true;
		}
		$deal_id_exists = true;
		/*****************************
		so we have the current data and the suggested data
		we compare and update
		
		The change can be in
		deal data
		company data
		banks
		law firms
		************************/
		/****************************
		666 just like add deal, we first get the id of the company doing the deal. We do not proceed if the company id is not found
		For now, we have single company in the csv and we do not create the company if company not found
		
		sng:20/mar/2013
		we also need the company name for the suggestion
		**********************/
		$q = "select cc.company_id,c.name from ".TP."co_codes_company as cc left join ".TP."company as c on(cc.company_id=c.company_id) where cc.curr_co_codes_id='".$data_arr['curr_co_code_company_id']."' and cc.in_sync='y'";
		$ok = $this->db->select_query($q);
		if(!$ok){
			return false;
		}
		if(!$this->db->has_row()){
			/**************
			666 id of deal company not found, and we do not create company from deal data, so this is an error
			**********/
			co_codes::$debug->print_r("company id of co-code company ".$data_arr['curr_co_code_company_id']." not found");
			return false;
		}
		$row = $this->db->get_row();
		$suggested_deal_company_id = $row['company_id'];
		$suggested_deal_company_name = $row['name'];
		/****************************
		Now we see if we need to update the deal data.
		Note: since this is an update, we do not change added_on
		Also, we do not check admin_verified, is_active, in_calculation. These are all internal matter of deal-data
		and are not concerned with deal information
		
		We also compare the fields with current_data to see if update is really needed or not.
		
		NOTE: Do watch out if the deal value has changed. That will require adjustment of tombstone point later
		But then changes in number of firms will require recomputation.
		What we do is, we maintain a flag for the deal.
		
		666: We should also store the suggestion but that will merely complicate the code.
		at any rate, the current system will have to be trashed for a more robust architecture.
		
		666: Another idea - tag each datapoint with the source and see if the source matach. only then change. Example - if
		the deal value was obtained from nasdaq co-code and now if that same is suggesting different value then update
		****************************/
		$date_time_now = date("Y-m-d H:i:s");
		
		$deal_q = "";
		$deal_extra_q = "";
		/*************
		sng:20/mar/2013
		Now we use two separate flags
		*************/
		$recompute_tombstone_points_for_banks = false;
		$recompute_tombstone_points_for_law_firms = false;
		
		/*****************
		sng:19/mar/2013
		We need to store the valuation suggestion
		*****************/
		$val_suggest_q = "";
		/***********************************/
		$curr_value_in_billion = $current_data['value_in_billion'];
		$suggested_value_in_billion = $data_arr['value_in_billion'];
		if(($suggested_value_in_billion!=0.0)&&($suggested_value_in_billion!=$curr_value_in_billion)){
			$deal_q.=",value_in_billion='".$suggested_value_in_billion."'";
			/***********
			sng:19/mar/2013
			change in deal value, so
			
			sng:20/mar/2013
			We need to calculate tombstone points bank and law firm
			****************/
			$recompute_tombstone_points_for_banks = true;
			$recompute_tombstone_points_for_law_firms = true;
			//we will have to adjust tombtone points of partners
			$suggested_value_in_million = $suggested_value_in_billion*1000;
			$val_suggest_q.=",value_in_million='".$suggested_value_in_million."'";
		}
		/***********************************/
		$curr_value_range_id = $current_data['value_range_id'];
		$suggested_value_range_id = $data_arr['value_range_id'];
		if(($suggested_value_range_id!=0)&&($suggested_value_range_id!=$curr_value_range_id)){
			$deal_q.=",value_range_id='".$suggested_value_range_id."'";
		}
		/***********************************/
		$curr_currency = $current_data['currency'];
		$suggested_currency = $data_arr['currency'];
		if(($suggested_currency!="")&&($suggested_currency!=$curr_currency)){
			$deal_q.=",currency='".$suggested_currency."'";
			$val_suggest_q.=",currency='".$suggested_currency."'";
			/*********
			666 if currency change, we should also set the exchange rate and compute value in USD
			But we are not considering the complication of the currency change
			*********/
		}
		/***********************************/
		$curr_date_of_deal = $current_data['date_of_deal'];
		$suggested_date_of_deal = $data_arr['date_of_deal'];
		if(($suggested_date_of_deal!="0000-00-00")&&($suggested_date_of_deal!=$curr_date_of_deal)){
			$deal_q.=",date_of_deal='".$suggested_date_of_deal."'";
		}else{
			//suggestion is blank or same as current
		}
		/***********************************/
		$curr_deal_cat_name = $current_data['deal_cat_name'];
		$suggested_deal_cat_name = $data_arr['deal_cat_name'];
		if(($suggested_deal_cat_name!="")&&($suggested_deal_cat_name!=$curr_deal_cat_name)){
			$deal_q.=",deal_cat_name='".$suggested_deal_cat_name."'";
		}
		/***********************************/
		$curr_deal_subcat1_name = $current_data['deal_subcat1_name'];
		$suggested_deal_subcat1_name = $data_arr['deal_subcat1_name'];
		if(($suggested_deal_subcat1_name!="")&&($suggested_deal_subcat1_name!=$curr_deal_subcat1_name)){
			$deal_q.=",deal_subcat1_name='".$suggested_deal_subcat1_name."'";
		}
		/***********************************/
		$curr_deal_subcat2_name = $current_data['deal_subcat2_name'];
		$suggested_deal_subcat2_name = $data_arr['deal_subcat2_name'];
		if(($suggested_deal_subcat2_name!="")&&($suggested_deal_subcat2_name!=$curr_deal_subcat2_name)){
			$deal_q.=",deal_subcat2_name='".$suggested_deal_subcat2_name."'";
		}
		/***********************************/
		$curr_date_closed = $current_data['date_closed'];
		$suggested_date_closed = $data_arr['date_closed'];
		if(($suggested_date_closed!="0000-00-00")&&($suggested_date_closed!=$curr_date_closed)){
			$deal_extra_q.=",date_closed='".$suggested_date_closed."'";
		}else{
			//suggestion is blank or same as current
		}
		/***********************************/	
		if($deal_q!=""){
			$deal_q.=",last_edited='".$date_time_now."'";
			$deal_q = substr($deal_q,1);
			$q = "update ".TP."transaction set ".$deal_q." WHERE id='".$deal_id."'";
			/*****************************
			DELETE DELETE DELETE
			*****/
			self::$debug->print_r($q);
			/**********************************/
			$ok = $this->db->mod_query($q);
			if(!$ok){
				return false;
			}
			/**********************
			sng:19/mar/2013
			deal updated, so try to set the suggestion
			******************/
			if($val_suggest_q!=""){
				$val_suggest_q = substr($val_suggest_q,1);
				$val_suggest_q = "insert into ".TP."transaction_suggestions_valuation set deal_id='".$deal_id."',suggested_by='0',date_suggested='".$date_time_now."',".$val_suggest_q.",status_note='set',is_correction='y'";
				$this->db->mod_query($val_suggest_q);
			}
		}
		/****************************
		it may happen that only the date closed was changed (though unlikely since
		we compute date_closed from date of deal
		********************/
		if($deal_extra_q!=""){
			$deal_extra_q = substr($deal_extra_q,1);
			$q = "update ".TP."transaction_extra_detail set ".$deal_extra_q." WHERE transaction_id='".$deal_id."'";
			/***************************
			DELETE DELETE DELETE
			******/
			self::$debug->print_r($deal_extra_q);
			/*******************************/
			$ok = $this->db->mod_query($q);
			if(!$ok){
				//does not matter
			}
		}
		/************************
		since this is an update, we need the current list of participants.
		In our transaction_companies table, there can be one or more companies associated with a deal
		
		666 Here, since we are not bothering with role of the company (for now) we just create a list of participants and do
		a simple lookup.
		
		It may happen that currently there are no companies associated with deal (unlikely but we do not throw error
		if there is no participants)
		**************************/
		$curr_deal_company_ids = array();
		
		$q = "select company_id from ".TP."transaction_companies where transaction_id='".$deal_id."'";
		$ok = $this->db->select_query($q);
		if(!$ok){
			return false;
		}
		$cnt = $this->db->row_count();
		for($i=0;$i<$cnt;$i++){
			$row = $this->db->get_row();
			$curr_deal_company_ids[] = $row['company_id'];
		}
		/******************************************
		We already checked that we have a company suggestion. Now
		1) That company is already there (in the list of existing participants) - in that case, it is not really a suggestion for participant company
		2) The company is not there (in the list of existing participants), so we add the company as participant
		3) There could be companies (in the list of existing participants) that are not in the csv. Well, we do not delete them.
		666 It is the third case that has to be discussed
		
		Actually, what we need here is a tag - who inserted what data. Only then we can delete (e.g. the prev company was added from co-codes
		nasdaq dump, which now has different company so we replace)
		*************************/
		if(!in_array($suggested_deal_company_id,$curr_deal_company_ids)){
		
			$q = "insert into ".TP."transaction_companies set transaction_id='".$deal_id."',company_id='".$suggested_deal_company_id."'";
			/************************
			DELETE DELETE DELETE
			******/
			self::$debug->print_r($q);
			/*****************************/
			$ok = $this->db->mod_query($q);
			if(!$ok){
				/**************
				bad luck
				666 what we need is transaction here
				**************/
				return false;
			}
			/*********************
			sng:20/mar/2013
			Now add as suggestion
			************/
			$q = "insert into ".TP."transaction_companies_suggestions set deal_id='".$deal_id."'
			,suggested_by='0'
			,date_suggested='".$date_time_now."'
			,company_name='".$suggested_deal_company_name."'
			,role_id='0'
			,status_note='added'
			,is_correction='y'";
			$ok = $this->db->mod_query($q);
		}else{
			/*************
			this is already there so nothing to update
			****************/
		}
		/********************
		Now the partners
		
		remember that if banks count change or law firm count change, we need to trigger the recomputation
		we can store the current count of banks and current count of law firms
		then we update
		then we see the counts.
		if any count change, we trigger recomputation
		*******/
		$ok = $this->update_firms($deal_id,$data_arr,$date_time_now,$recompute_tombstone_points_for_banks,$recompute_tombstone_points_for_law_firms);
		if(!$ok){
			/**************
			bad luck
			666 what we need is transaction here
			**************/
			return false;
		}
		return true;
		
	}
	
	/***************
	sng:19/mar/2013
	In case the deal value change or number of firms change
	
	sng:20/mar/2013
	This could be improved. If deal value change, both banks and law firms are affected.
	However, in some cases, only bank or law firm is changed.
	
	So, we specify what changed.
	***************/
	public function recompute_tombstone_points_for_deal($deal_id,$partner_type){
		$q = "select value_in_billion from ".TP."transaction WHERE id='".$deal_id."'";
		$ok = $this->db->select_query($q);
		if(!$ok){
			return false;
		}
		if(!$this->db->has_row()){
			return false;
		}
		$row = $this->db->get_row();
		$value_in_billion = $row['value_in_billion'];
		
		$bank_count = 0;
		$law_firm_count = 0;
		
		/************
		sng:20/mar/2013
		***************/
		$q = "select count(*) as partner_cnt,partner_type from ".TP."transaction_partners WHERE transaction_id='".$deal_id."' AND partner_type='".$partner_type."'";
		$ok = $this->db->select_query($q);
		if(!$ok){
			return false;
		}
		$row_cnt = $this->db->row_count();
		if(0==$row_cnt){
			/****************
			we have the value for the deal but
			it may happen that there is no partners added to the deal yet
			
			Nothing to update
			*****************/
			return true;
		}
		/***********
		it may happen that there is only banks or law firms added. That is why we took default count of 0
		***************/
		for($i=0;$i<$row_cnt;$i++){
			$row = $this->db->get_row();
			
			if($row['partner_type']=="bank"){
				$bank_count = $row['partner_cnt'];
			}
			
			if($row['partner_type']=="law firm"){
				$law_firm_count = $row['partner_cnt'];
			}
		}
		if($bank_count > 0){
			$adjusted_value_in_billion = $value_in_billion/$bank_count;
			$q = "update ".TP."transaction_partners set adjusted_value_in_billion='".$adjusted_value_in_billion."' WHERE transaction_id='".$deal_id."' and partner_type='bank'";
			$ok = $this->db->mod_query($q);
			if(!$ok){
				return false;
			}
		}
		
		if($law_firm_count > 0){
			$adjusted_value_in_billion = $value_in_billion/$law_firm_count;
			$q = "update ".TP."transaction_partners set adjusted_value_in_billion='".$adjusted_value_in_billion."' WHERE transaction_id='".$deal_id."' and partner_type='law firm'";
			$ok = $this->db->mod_query($q);
			if(!$ok){
				return false;
			}
		}
		/********************
		666 Then we will have to update the adjusted value for the members
		*********************/
		return true;
	}
	
	private function process_firms($firm_data_arr,$deal_id,$deal_value_in_billion,$date_added){
		
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
		
		sng:19/mar/2013
		We also put the suggestion
		The suggestion is from admin and since this is the original suggestion, we do not set any note
		*****/
		$q = "";
		$suggest_q = "";
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
		
		$temp_banks_tokens = explode(";",$firm_data_arr['lead_underwriters']);
		$temp_banks_tokens_count = count($temp_banks_tokens);
		for($i=0;$i<$temp_banks_tokens_count;$i++){
			$bank_name = trim($temp_banks_tokens[$i]);
			if(($bank_name!="")&&($bank_name!="--")&&($bank_name!="n.a.")&&($bank_name!="Inc")){
				$partner_id = 0;
				$ok = $this->get_firm_id($bank_name,$partner_type,$partner_id);
				if(!$ok){
					continue;
				}
				if($partner_id!=0){
					$banks[$banks_offset] = array();
					$banks[$banks_offset]['partner_id'] = $partner_id;
					$banks[$banks_offset]['partner_name'] = $bank_name;
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
				
				$suggest_q.=",('".$transaction_id."','0','".$date_added."','".$banks[$i]['partner_name']."','".$partner_type."','".$banks[$i]['role_id']."','','n')";
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
			if(($law_firm_name!="")&&($law_firm_name!="--")&&($law_firm_name!="n.a.")&&($law_firm_name!="Inc")){
				$partner_id = 0;
				$ok = $this->get_firm_id($law_firm_name,$partner_type,$partner_id);
				if(!$ok){
					continue;
				}
				if($partner_id!=0){
					$law_firms[$law_firms_offset] = array();
					$law_firms[$law_firms_offset]['partner_id'] = $partner_id;
					$law_firms[$law_firms_offset]['partner_name'] = $law_firm_name;
					$law_firms[$law_firms_offset]['role_id'] = 14;
					$law_firms_offset++;
				}
			}
		}
		
		$temp_law_firms_tokens = explode(";",$firm_data_arr['underwriter_councel']);
		$temp_law_firms_tokens_count = count($temp_law_firms_tokens);
		for($i=0;$i<$temp_law_firms_tokens_count;$i++){
			$law_firm_name = trim($temp_law_firms_tokens[$i]);
			if(($law_firm_name!="")&&($law_firm_name!="--")&&($law_firm_name!="n.a.")&&($law_firm_name!="Inc")){
				$partner_id = 0;
				$ok = $this->get_firm_id($law_firm_name,$partner_type,$partner_id);
				if(!$ok){
					continue;
				}
				if($partner_id!=0){
					$law_firms[$law_firms_offset] = array();
					$law_firms[$law_firms_offset]['partner_id'] = $partner_id;
					$law_firms[$law_firms_offset]['partner_name'] = $law_firm_name;
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
				
				$suggest_q.=",('".$transaction_id."','0','".$date_added."','".$law_firms[$i]['partner_name']."','".$partner_type."','".$law_firms[$i]['role_id']."','','n')";
			}
		}
		
		if($q!=""){
			$q = substr($q,1);
			$q = "insert into ".TP."transaction_partners (transaction_id,partner_id,role_id,partner_type,adjusted_value_in_billion) values ".$q;
			
			$ok = $this->db->mod_query($q);
			if(!$ok){
				return false;
			}
			/**************
			partners added, now add the suggestion
			*****************/
			if($suggest_q!=""){
				$suggest_q = substr($suggest_q,1);
				$suggest_q = "insert into ".TP."transaction_partners_suggestions (deal_id,suggested_by,date_suggested,partner_name,partner_type,role_id,status_note,is_correction) values ".$suggest_q;
				$ok = $this->db->mod_query($suggest_q);
			}
			return true;
		}
		/**********
		nothing to add
		
		with new deal, we do not have to add members associated with deal and tombstone points for members.
		************/
		return true;
	}
	
	/************
	deal_id: id of the deal that we are considering
	suggested_deal_data: this has the new items
	deal_updated_on: date time when the deal was updated. This is needed for storing suggestion
	recompute_tombstone_points_for_banks
	recompute_tombstone_points_for_law_firms: pointer to set the flag - if recomputation of the tombstone points are needed or not.
	We cannot do that here. That will be done by some outer caller function as part of updating a deal. We just tell
	it whether recomputation is needed or not. (actually there are other changed that require recomputation, so we do it once,
	after we have finished all the deal changes.
	NOTE: ONLY set this IF you have to set it to TRUE. DO NOT TOUCH OTHERWISE
	
	remember that there can be member associated with firm
	now, if we are adding new firm, members are not a concern
	if role change, again, the members are not affected.
	if a firm is removed, the members are to be removed. (of course, since the number of firms changes
	we trigger recomputation, which in turn adjust the points for the members).
	****************/
	private function update_firms($deal_id,$suggested_deal_data,$deal_updated_on,&$recompute_tombstone_points_for_banks,&$recompute_tombstone_points_for_law_firms){
		/*******************************
		Roles for banks
		Lead Underwriter = Bookrunner [role id 2]
		
		Underwriter = Co-lead manager
		
		for law firms
		Company Counsel = Advisor to Company [role id 14]
		Underwriter Counsel  = Advisor to Banks [role id 16]
		*********************/
		
		/************
		we need current list of partners so that we can compare the suggestion against that list
		we store the id as key and the assoc array as value
		************/
		$curr_banks = array();
		$curr_law_firms = array();
		/********
		sng:21/mar/2013
		we also need the record id in case we want to modify or delete
		**************/
		$q = "select p.id,partner_id,role_id,partner_type,suggested_by,name,'n' AS has_changed,'n' AS in_suggestion from ".TP."transaction_partners as p left join ".TP."company as c on(p.partner_id=c.company_id) where transaction_id='".$deal_id."'";
		$ok = $this->db->select_query($q);
		if(!$ok){
			return false;
		}
		$cnt = $this->db->row_count();
		for($i=0;$i<$cnt;$i++){
			$row = $this->db->get_row();
			$partner_id = $row['partner_id'];
			
			if("bank"==$row['partner_type']){
				$curr_banks[$partner_id] = $row;
			}
			if("law firm"==$row['partner_type']){
				$curr_law_firms[$partner_id] = $row;
			}
		}
		/*******************
		in here, recomputation is required only if number of banks / law firm change (because points = deal_value/num firms).
		however, it may happen that we have 2 banks currently and after update, we add a new bank and delete an existing bank.
		The count remains same but we still need to assign tombstone point to the new bank.
		So, better watch for modification.
		***************************/
		
		/****************************************
		Now the suggestions
		We only have Lead underwriters so only these are banks
		*********************************/
		$banks = array();
		$banks_offset = 0;
		$partner_type = "bank";
		
		$temp_banks_tokens = explode(";",$suggested_deal_data['lead_underwriters']);
		$temp_banks_tokens_count = count($temp_banks_tokens);
		for($i=0;$i<$temp_banks_tokens_count;$i++){
			$bank_name = trim($temp_banks_tokens[$i]);
			if(($bank_name!="")&&($bank_name!="--")&&($bank_name!="n.a.")&&($bank_name!="Inc")){
				$partner_id = 0;
				$ok = $this->get_firm_id($bank_name,$partner_type,$partner_id);
				if(!$ok){
					continue;
				}
				if($partner_id!=0){
					$banks[$banks_offset] = array();
					$banks[$banks_offset]['partner_id'] = $partner_id;
					$banks[$banks_offset]['partner_name'] = $bank_name;
					$banks[$banks_offset]['role_id'] = 2;
					$banks_offset++;
				}
			}
		}
		/*****************************************
		Now the law firms
		We have Company counsel and Underwriters counsel so two sets of data
		******************/
		$law_firms = array();
		$law_firms_offset = 0;
		$partner_type = "law firm";
		
		$temp_law_firms_tokens = explode(";",$suggested_deal_data['company_councel']);
		$temp_law_firms_tokens_count = count($temp_law_firms_tokens);
		for($i=0;$i<$temp_law_firms_tokens_count;$i++){
			$law_firm_name = trim($temp_law_firms_tokens[$i]);
			if(($law_firm_name!="")&&($law_firm_name!="--")&&($law_firm_name!="n.a.")&&($law_firm_name!="Inc")){
				$partner_id = 0;
				$ok = $this->get_firm_id($law_firm_name,$partner_type,$partner_id);
				if(!$ok){
					continue;
				}
				if($partner_id!=0){
					$law_firms[$law_firms_offset] = array();
					$law_firms[$law_firms_offset]['partner_id'] = $partner_id;
					$law_firms[$law_firms_offset]['partner_name'] = $law_firm_name;
					$law_firms[$law_firms_offset]['role_id'] = 14;
					$law_firms_offset++;
				}
			}
		}
		$temp_law_firms_tokens = explode(";",$suggested_deal_data['underwriter_councel']);
		$temp_law_firms_tokens_count = count($temp_law_firms_tokens);
		for($i=0;$i<$temp_law_firms_tokens_count;$i++){
			$law_firm_name = trim($temp_law_firms_tokens[$i]);
			if(($law_firm_name!="")&&($law_firm_name!="--")&&($law_firm_name!="n.a.")&&($law_firm_name!="Inc")){
				$partner_id = 0;
				$ok = $this->get_firm_id($law_firm_name,$partner_type,$partner_id);
				if(!$ok){
					continue;
				}
				if($partner_id!=0){
					$law_firms[$law_firms_offset] = array();
					$law_firms[$law_firms_offset]['partner_id'] = $partner_id;
					$law_firms[$law_firms_offset]['partner_name'] = $law_firm_name;
					$law_firms[$law_firms_offset]['role_id'] = 16;
					$law_firms_offset++;
				}
			}
		}
		/************************************************************
		so we have the suggestions. we go through each:
		1) The firm is in the curr list and the role is same. we skip
		2) The firm is in the curr list and the role is different ???
		3) The firm is not in the curr list. In this case we add and add the suggestion and set recompute flag
		
		then we go through the curr list
		if the firm is not in the suggested list then???
		
		We also create the query to insert the banks and law firms
		*********************************************/
		$partner_type = "bank";
		$insert_q = "";
		$insert_suggestion_q = "";
		$num_banks = count($banks);
		for($i=0;$i<$num_banks;$i++){
			$suggested_id = $banks[$i]['partner_id'];
			if(array_key_exists($suggested_id,$curr_banks)){
				/**********************
				The firm already has been added as partner
				check if the roles are same. If not same, set the suggested role id and set the has_changed flag
				************************/
				$suggested_role_id = $banks[$i]['role_id'];
				$curr_role_id = $curr_banks[$suggested_id]['role_id'];
				if($curr_role_id!=$suggested_role_id){
					$curr_banks[$suggested_id]['role_id'] = $suggested_role_id;
					$curr_banks[$suggested_id]['has_changed'] = 'y';
				}
				/*************************
				The firm is in suggestion list and current data. We mark that
				*********************/
				$curr_banks[$suggested_id]['in_suggestion']='y';
				/*****************
				since this is not new, we need not insert it, so we go to the next
				*******************/
				continue;
			}
			/*********************
			Not in the current list. We will have to insert.
			since we are changing the partner list, we set the flag
			that will perform the recomputation and set the adjusted tombstone points
			***********************/
			$insert_q.=",('".$deal_id."','".$suggested_id."','".$banks[$i]['role_id']."','".$partner_type."')";
			$insert_suggestion_q.=",('".$deal_id."','0','".$deal_updated_on."','".$banks[$i]['partner_name']."','".$partner_type."','".$banks[$i]['role_id']."','added','y')";
			
			$recompute_tombstone_points_for_banks = true;
		}
		$partner_type = "law firm";
		$num_law_firms = count($law_firms);
		for($i=0;$i<$num_law_firms;$i++){
			$suggested_id = $law_firms[$i]['partner_id'];
			if(array_key_exists($suggested_id,$curr_law_firms)){
				/**********************
				The firm already has been added as partner
				check if the roles are same. If not same, set the suggested role id and set the has_changed flag
				************************/
				$suggested_role_id = $law_firms[$i]['role_id'];
				$curr_role_id = $curr_law_firms[$suggested_id]['role_id'];
				if($curr_role_id!=$suggested_role_id){
					$curr_law_firms[$suggested_id]['role_id'] = $suggested_role_id;
					$curr_law_firms[$suggested_id]['has_changed'] = 'y';
				}
				/*************************
				The firm is in suggestion list and current data. We mark that
				*********************/
				$curr_law_firms[$suggested_id]['in_suggestion']='y';
				/*****************
				since this is not new, we need not insert it, so we go to the next
				*******************/
				continue;
			}
			/*********************
			Not in the current list. We will have to insert.
			since we are changing the partner list, we set the flag
			that will perform the recomputation and set the adjusted tombstone points
			***********************/
			$insert_q.=",('".$deal_id."','".$suggested_id."','".$law_firms[$i]['role_id']."','".$partner_type."')";
			$insert_suggestion_q.=",('".$deal_id."','0','".$deal_updated_on."','".$law_firms[$i]['partner_name']."','".$partner_type."','".$law_firms[$i]['role_id']."','added','y')";
			
			$recompute_tombstone_points_for_law_firms = true;
		}
		/***********************
		Now run the queries
		************************/
		if($insert_q!=""){
			$insert_q = substr($insert_q,1);
			$insert_q = "insert into ".TP."transaction_partners (transaction_id,partner_id,role_id,partner_type) values ".$insert_q;
			/************************
			DELETE DELETE DELETE
			******/
			self::$debug->print_r($insert_q);
			/*****************************/
			$ok = $this->db->mod_query($insert_q);
			if(!$ok){
				return false;
			}
			/**************
			partners added, now add the suggestion
			*****************/
			if($insert_suggestion_q!=""){
				$insert_suggestion_q = substr($insert_suggestion_q,1);
				$insert_suggestion_q = "insert into ".TP."transaction_partners_suggestions (deal_id,suggested_by,date_suggested,partner_name,partner_type,role_id,status_note,is_correction) values ".$insert_suggestion_q;
				$ok = $this->db->mod_query($insert_suggestion_q);
			}
			/*************************/
		}
		/********************
		sng:21/mar/2013
		Now we iterate over the current banks and law firms and see which ones were modified.
		*********************/
		$partner_type = "bank";
		foreach($curr_banks as $curr_firm){
			if($curr_firm['has_changed']=='y'){
				$updt_q = "update ".TP."transaction_partners set role_id='".$curr_firm['role_id']."' WHERE id='".$curr_firm['id']."'";
				/************************
				DELETE DELETE DELETE
				******/
				self::$debug->print_r($updt_q);
				/*****************************/
				$ok = $this->db->mod_query($updt_q);
				if(!$ok){
					return false;
				}
				/**********
				the change in role is a new suggestion
				**********/
				$updt_suggest_q = "insert into ".TP."transaction_partners_suggestions set deal_id='".$deal_id."'
				,suggested_by='0'
				,date_suggested='".$deal_updated_on."'
				,partner_name='".$curr_firm['name']."'
				,partner_type='".$partner_type."'
				,role_id='".$curr_firm['role_id']."'
				,status_note='role updated'
				,is_correction='y'";
				$ok = $this->db->mod_query($updt_suggest_q);
			}
		}
		$partner_type = "law firm";
		foreach($curr_law_firms as $curr_firm){
			if($curr_firm['has_changed']=='y'){
				$updt_q = "update ".TP."transaction_partners set role_id='".$curr_firm['role_id']."' WHERE id='".$curr_firm['id']."'";
				/************************
				DELETE DELETE DELETE
				******/
				self::$debug->print_r($updt_q);
				/*****************************/
				$ok = $this->db->mod_query($updt_q);
				if(!$ok){
					return false;
				}
				/**********
				the change in role is a new suggestion
				**********/
				$updt_suggest_q = "insert into ".TP."transaction_partners_suggestions set deal_id='".$deal_id."'
				,suggested_by='0'
				,date_suggested='".$deal_updated_on."'
				,partner_name='".$curr_firm['name']."'
				,partner_type='".$partner_type."'
				,role_id='".$curr_firm['role_id']."'
				,status_note='role updated'
				,is_correction='y'";
				$ok = $this->db->mod_query($updt_suggest_q);
			}
		}
		/*******************************************
		sng:22/mar/2013
		Now we iterate over the current banks and law firms and see which ones are not in the suggestion list and we delete those.
		**********/
		$partner_type = "bank";
		foreach($curr_banks as $curr_firm){
			if($curr_firm['in_suggestion']=='n'){
				/******************
				first delete the members on the deal for this firm
				******************/
				$del_q = "delete from ".TP."transaction_partner_members WHERE transaction_id='".$deal_id."' AND partner_id='".$curr_firm['partner_id']."'";
				$ok = $this->db->mod_query($del_q);
				if(!$ok){
					return false;
				}
				/*************
				now delete the partner for the deal
				***********/
				$del_q = "delete from ".TP."transaction_partners WHERE id='".$curr_firm['id']."'";
				/************************
				DELETE DELETE DELETE
				******/
				self::$debug->print_r($del_q);
				/*****************************/
				$ok = $this->db->mod_query($del_q);
				if(!$ok){
					return false;
				}
				/************
				changing the number of partners, need recomputation of points
				*************/
				$recompute_tombstone_points_for_banks = true;
				/*****************
				The deletion is to be stored as suggestion
				******/
				$updt_suggest_q = "insert into ".TP."transaction_partners_suggestions set deal_id='".$deal_id."'
				,suggested_by='0'
				,date_suggested='".$deal_updated_on."'
				,partner_name='".$curr_firm['name']."'
				,partner_type='".$partner_type."'
				,role_id='".$curr_firm['role_id']."'
				,status_note='removed'
				,is_correction='y'";
				$ok = $this->db->mod_query($updt_suggest_q);
				/***************************/
			}
		}
		$partner_type = "law firm";
		foreach($curr_law_firms as $curr_firm){
			if($curr_firm['in_suggestion']=='n'){
				/******************
				first delete the members on the deal for this firm
				******************/
				$del_q = "delete from ".TP."transaction_partner_members WHERE transaction_id='".$deal_id."' AND partner_id='".$curr_firm['partner_id']."'";
				$ok = $this->db->mod_query($del_q);
				if(!$ok){
					return false;
				}
				/*************
				now delete the partner for the deal
				***********/
				$del_q = "delete from ".TP."transaction_partners WHERE id='".$curr_firm['id']."'";
				/************************
				DELETE DELETE DELETE
				******/
				self::$debug->print_r($del_q);
				/*****************************/
				$ok = $this->db->mod_query($del_q);
				if(!$ok){
					return false;
				}
				/******************
				changing the number of partners, need recomputation of points
				***********/
				$recompute_tombstone_points_for_law_firms = true;
				/*****************
				The deletion is to be stored as suggestion
				******/
				$updt_suggest_q = "insert into ".TP."transaction_partners_suggestions set deal_id='".$deal_id."'
				,suggested_by='0'
				,date_suggested='".$deal_updated_on."'
				,partner_name='".$curr_firm['name']."'
				,partner_type='".$partner_type."'
				,role_id='".$curr_firm['role_id']."'
				,status_note='removed'
				,is_correction='y'";
				$ok = $this->db->mod_query($updt_suggest_q);
				/***************************/
			}
		}
		/******************************************************/
		return true;
	}
	private function get_firm_id($firm_name,$firm_type,&$firm_id){
		
		/************
		we see if we already have this firm. If so, we return the id else we create the firm and then return the id
		************/
		$q = "select company_id from ".TP."company where name='".$this->db->escape_string($firm_name)."' and type = '".$firm_type."'";
		$ok = $this->db->select_query($q);
		if(!$ok){
			return false;
		}
		$res_count = $this->db->row_count();
		if(0==$res_count){
			/*******
			we need to create
			********/
			$q = "insert into ".TP."company set name='".$this->db->escape_string($firm_name)."', type = '".$firm_type."'";
			$ok = $this->db->mod_query($q);
			if(!$ok){
				return false;
			}
			$firm_id = $this->db->last_insert_id();
			/***************
			sng:20/mar/2013
			firm created, id obtained, now it is safe to set the suggestion
			but wait, for banks and law firms, we do not bother
			***************/
		}else{
			/****
			found
			***/
			$rec = $this->db->get_row();
			$firm_id = $rec['company_id'];
		}
		return true;
	}
}
?>