<?php
/**********************
sng:22/jan/2013

This encapsulates the co-codes API. We use this to get data from co-codes.com
***********************/
class co_codes{
	
	private static $co_codes_url = "http://co-codes.com/";
	private static $full_company_file = "store/dealdata_ccsid.csv";
	private static $full_company_file_local_destination = "/from_co-codes/companies.csv";
	private static $num_cols_in_company_csv = 23;
	
	private static $img_obj;
	
	private $db;
	
	private function __construct(){
	}
	
	public static function create(){
		global $g_config;
		$temp = db::create($g_config['db_host'],$g_config['db_user'],$g_config['db_password'],$g_config['db_name']);
		if($temp===false){
			print_r("cannot connect to database\r\n");
			return false;
		}
		$obj = new co_codes();
		$obj->db = $temp;
		
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
		print_r("fetching company data file from co-codes\r\n");
		
		$source = self::$co_codes_url.self::$full_company_file;
		$destination = FILE_PATH.self::$full_company_file_local_destination;
		
		$ok = self::fetch_and_store_remote_file($source,$destination);
		if(!$ok){
			print_r("error fetching co-codes company data file\r\n");
			return false;
		}
		print_r("fetched co-codes company data file\r\n");
		/***************
		check integrity of the downloaded file
		**************/
		$local_hash = self::get_hashcode_of_local_file($destination);
		$remote_hash = $this->get_hashcode_of_file(self::$full_company_file);
		if($local_hash!==$remote_hash){
			print_r("file mangled during download\r\n");
			return false;
		}
		print_r("verified integrity of the file\r\n");
		/****************
		now process the csv file
		*****************/
		$ok = self::process_csv($destination,array($this,"process_row_data_for_company"));
		if(!$ok){
			print_r("error processing the file\r\n");
			return false;
		}
		print_r("processed the file");
		/*******************
		after the parsing, we remove the downloaded file. No need to clutter the server
		********************/
		unlink($destination);
		print_r("removed the company data file\r\n");
		return true;
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
			print_r("invalid number of columns\r\n");
			return false;
		}
		$co_code_data_arr = array();
		/****************************************
		Col_0: CCS Identifier "SEC000940062"
		If this starts with CCS, it means it does not appear in SEC (securities and exchange commission) database, else it starts with SEC
		
		The current co code ID is so important in duplicate check that if it is not found, we raise error
		****************/
		if(!isset($data_array[0])){
			print_r("co-codes unique identifier not specified\r\n");
			return false;
		}
		$co_code_data_arr['curr_co_codes_id'] = trim($data_array[0]);
		if(""===$co_code_data_arr['curr_co_codes_id']){
			print_r("co-codes unique identifier not specified\r\n");
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
			print_r("company name not specified\r\n");
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
		Col_23: Full name of the company from SEC database
		We store this in the intermediate table but does not use in deal-data
		**************/
		if(!isset($data_array[23])){
			$co_code_data_arr['full_name'] = "";
		}else{
			$co_code_data_arr['full_name'] = trim($data_array[23]);
		}
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
							print_r("aborted\r\n");
							break;
						case 2:
							print_r("timeout\r\n");
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
		print_r("processing csv file\r\n");
		ini_set('auto_detect_line_endings',true);
		
		$r_handle = fopen($source,'r');
		if(!$r_handle){
			print_r("cannot open the source file\r\n");
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
				print_r("error processing row ".$row_num."\r\n");
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
		print_r("finished processing csv file\r\n");
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
		/**********
		TODO
		we need a check here
		get the extension, convert to lowercase, then match against the allowed (we allow .gif, .jpg, .jpeg, .png, should we aloow .bmp?)
		if fail, treat as no logo
		
		else, after creating thumbname, convert to lowercase
		************/
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
		$thumb_fit_width = 200;
		$thumb_fit_height = 200;
		/********
		see classes/class.company.php
		********/
		$success = self::$img_obj->create_resized($destination,FILE_PATH."/from_co-codes/logo/thumbnails",$upload_img_name,$thumb_fit_width,$thumb_fit_height,false);
		if(!$success){
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
When we transfer data, we create record in our table, we get company_id, but how do we put that id back in intermediate table?

TODO
The code that fetch equity data from co-codes will use the intermediate table to match co-codes unique id to find the corresponding company id
(and it will not create the company)
**************/
?>