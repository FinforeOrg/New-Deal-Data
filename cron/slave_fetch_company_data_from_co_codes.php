<?php
/***********
It is assumed that at any given time, there is at most one running instance
*************/
require_once(dirname(dirname(__FILE__))."/include/config.php");
require_once(FILE_PATH."/include/minimal_bootstrap.php");

require_once(FILE_PATH."/classes/class.background_slave_controller.php");
$master = new background_slave_controller();

$worker_name = "fetch_company_data_co_codes";
$master->set_status_note($worker_name,"started, fetching remote file");

/***************************************************************
fetch the csv file
****/
//$source = "http://co-codes.com/store/dealdata.csv";
/*******
TEST
****/
$source = "http://localhost/co-codes/store/dealdata.csv";
$destination = FILE_PATH."/from_co-codes/deal-data.csv";

$ok = fetch_and_store_remote_file($source,$destination);
if(!$ok){
	$master->set_status_note($worker_name,"error fetching file");
	exit;
}
$master->set_status_note($worker_name,"fetched file");
/***************************************************************
files downloaded. It is a csv file. We parse it and get the items.
We are interested in
company name
logo: this has to be fetched so that we have a local copy
sector/industry
country
identifiers

Either we can update the real tables or we can put the data in intermediate table and then update the real tables.
If we use intermediate table, we can store the timestamp. That way, in the next parsing, we do not bother with data rows
that has not changed (the fetching of logo, creating thumb, inserting in table all takes time).
*****************************************/

$msg = "";
$ok = process_company_file($destination,$msg);
if(!$ok){
	$master->set_status_note($worker_name,"Error processing file");
	exit;
}
$master->set_status_note($worker_name,"processed file");
/***
Now some questions:
1) The real tables also has data from other sources. So how do we check if the company data is already there or not?
2) If there, do we mark the intermediate record as 'archived'?
3) Suppose the data is there but it is missing some info that we have now in co-codes. What do we do?
4) After moving a company data from our intermediate table, do we mark the row as 'archived'?
5) What if we get updated information of a company from co-codes? Do we put the new info as company suggestion by admin? (remember the new look there?)

Country names - we do not update the master list. If the country code is not found, we do not put the country name.
If found, we use our own country name

We use the company long name when transferring to the real table.
We have co-codes ID which we use as index.

Sector / industry
We do a lookup in our master table. If not found, we do not put the sector / industry. If found, we use ours. Why? We have used the ICB classification everywhere

Logo, we fetch the logo, create a thumb then delete the original. We put logo in intermediate folder and then transfer.
*****/


$master->set_status_note($worker_name,"done ".$msg);
/***********
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
/********************
sng:25/oct/2012
While parsing the csv file from co-codes, make sure that you get required number of fields in each row.
delimiter: ,
enclosure: "
escape: |
We assume that the " character will never appear in any data and hence the \ is never used for escaping.
That is why we use the garbage value for escape so that we do not have the following

"0000753762","AKTIEBOLAGET VOLVO \PUBL\","AKTIEBOLAGET VOLVO \PUBL\","/var/www/store/logos/0000753762","","Consumer Goods","Automobiles & Parts","Automobiles & Parts","","","","","",""
			
The \" is the culprit
The parser takes "AKTIEBOLAGET VOLVO \PUBL\","AKTIEBOLAGET VOLVO \PUBL\","/var/www/store/logos/0000753762" as col2 and "Consumer Goods" as col4
and then we get Consumer Goods as country name

We set the escaping character to |. We hope that there si no ' " ' character in the actual data and that use of ' " ' is safe.

The code seems to handle blank line well
*******************/
function process_company_file($source,&$msg){
	
	
	$r_handle = fopen($source,'r');
	if(!$r_handle){
		return false;
	}
	/***************************
	records_scanned: total number of rows in this csv file
	records_error: total number of rows that were skipped because of problem
	records_added: total number of company data added to intermediate database. Adding to
	the intermediate database does not mean it will be added to the master. It may well happen that
	the company record is in the master already (added by some user)
	records_skipped: total number of records not added to intermediate table (because it is already there from the last parsing)
	sng:6/nov/2012
	We also need to know how many records were updated
	****************************/
	$records_scanned = 0;
	$records_error = 0;
	$records_added = 0;
	$records_updated = 0;
	$records_skipped = 0;
	
	while (($csv_data = fgetcsv($r_handle, 10240, ',', '"','|')) !== false) {
		$records_scanned++;
		/******************
		get data
		*********************/
		$added = false;
		$updated = false;
		$skipped = false;
		$ok = process_record($csv_data,$skipped,$added,$updated);
		if(!$ok){
			/********************
			We might want to record that there was error in the current record but let's
			not bother with it now.
			**************/
			$records_error++;
		}else{
			if($added){
				$records_added++;
			}else{
				if($skipped){
					$records_skipped++;
				}else{
					$records_updated++;
				}
			}
		}
		
	}
	fclose($r_handle);
	$msg = "scanned: ".$records_scanned.", error: ".$records_error.", added: ".$records_added.", skipped: ".$records_skipped.", updated: ".$records_updated;
	return true;
}
/*****
added: true/false, whether the record is added to intermediate table
sng: 6/nov/2012
We need to know whether the record was skipped (already exists), added (new record) or updated (existing record but we have new data now)

We check if we have the required number of cells or not. If not, it is an error and we return false
*********/
function process_record($row_data,&$skipped,&$added,&$updated){
	global $conn;
	
	$num_cols_expected = 18;
	$col_cnt = count($row_data);
	if($col_cnt!==$num_cols_expected){
		return false;
	}
	/***********
	sng:10/nov/2012
	The current co code ID is so important in duplicate check that if it is not found, we raise error
	***********/
	$curr_co_codes_id = trim($row_data[0]);
	if(""===$curr_co_codes_id){
		return false;
	}
	$prev_co_codes_id = trim($row_data[1]);
	
	$cik = trim($row_data[2]);
	$company_name = trim($row_data[3]);
	$company_short_name = trim($row_data[4]);
	$logo_url = trim($row_data[5]);
	$country_code = trim($row_data[6]);
	$co_code_country_name = trim($row_data[7]);
	/************
	Since the co-codes country name is not the way we want, we will only use the country code. When transferring to our table,
	we will do a lookup and get our country name
	**********************/
	
	//level 1
	$icb_industry = trim($row_data[8]);
	//level 2
	$icb_supersector = trim($row_data[9]);
	//level 3
	$icb_sector = trim($row_data[10]);
	//level 4
	$icb_subsector = trim($row_data[11]);
	
	/******************
	we are interested in level 1 and level 3
	However, the way we have arranged our data, our sector is icb_industry and industry is icb_sector
	******************/
	$sector = $icb_industry;
	$industry = $icb_sector;
	
	$ric = trim($row_data[12]);
	$bloomberg_code = trim($row_data[13]);
	$isin = trim($row_data[14]);
	$sedol = trim($row_data[15]);
	$google_code = trim($row_data[16]);
	
	$co_code_timestamp = trim($row_data[17]);
	/**********
	since we are storing datetime, we check if this is blank. If so, we convert it
	**************/
	if($co_code_timestamp===""){
		$co_code_timestamp = "0000-00-00 00:00:00";
	}
	
	/**************
	We first check whether we already have the record or not. We use the ‘short name’ to check. (Hold it see comment of sng:6/nov/2012)
	If not found, we add and we fetch the logo (if one is specified). If found, we check the timestamp of the existing record with the timestamp in the csv.
	If same, we ignore else we update the record
	
	Remember to escape: else you will face problem in data like ZORAN CORP \DE\
	
	Problem: what is there are two records like
	"0000001800","ABBOTT LABORATORIES","ABBOTT LABORATORIES","","US","USA"
	"","ABBOTT LABS.(PAK.)","ABBOTT LABORATORIES ","","PK","PAKISTAN"
	Note the short name: they are same if we truncate. A space character is used to separate the second from the first
	
	sng:6/nov/2012
	With MNCs having branches in multiple countries, we will face problem
	There could be multiple matching records with shortname
	IBM, IN
	IBM, US
	IMB, CN
	All are IBM but the country codes may be different.
	
	sng: 10/nov/2012
	We have decided to use co-codes unique ID.
	
	sng:10/nov/2012
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
	These two will be first and second column. The CIK will start from third etc.
	My side of the parsing
	1) Read current identifier
	1.1) Found in our table. So we do not bother with the old identifier column. We check the timestamp etc
	1.2) Not found in our table. Read the old identifier column
	1.2.1) It is blank. This means we are dealing with totally new record. We insert the company data.
	1.2.2) It is non blank. We check if the old one is there in the record.
	1.2.2.1) It is there. This means the identifier changed. We update the identifier in our table.
	We also check the timestamp and update rest of the record or skip updating rest of the record.
	1.2.2.2) It is not there. This means, we somehow missed the original record. We just add the record but we store the current identifier.
	******************/
	$q = "select curr_co_codes_id,co_code_timestamp from ".TP."co_codes_company where curr_co_codes_id='".mysql_real_escape_string($curr_co_codes_id)."'";
	$res = mysql_query($q,$conn);
	if(!$res){
		return false;
	}
	$row_count = mysql_num_rows($res);
	/***************
	We already checked that the current co code Id field value is there.
	So either there is a hit orthere is a  miss
	*******************/
	if($row_count !== 0){
		/***********
		This record is there. Check the stored timestamp
		*************/
		$row = mysql_fetch_assoc($res);
		$existing_timestamp = $row['co_code_timestamp'];
		if($existing_timestamp===$co_code_timestamp){
			/*********
			in the csv, the record is same as what we have in the intermediate database (as the timestamp is same)
			so skip
			*********/
			$skipped = true;
		}else{
			/*******
			This is updated version of what we have, so update
			We update, including the logo since we do not know what has changed
			********/
			/***
			TO DO: WRITE THAT CODE
			**/
			$updated = true;
		}
	}else{
		/*********
		Not found. Now, it may happen that the ID changed. So check again, with the prev ID. The prev id may be blank in the csv
		*************/
		if(""===$prev_co_codes_id){
			/******
			There was no prev ID for this company, and the current ID did not match any record. This is a brand new record. We add
			***********/
			$logo_name = "";
			$logo_ok = download_logo($logo_url,$logo_name);
			if(!$logo_ok){
				$logo_name = "";
			}
			
			$add_q = "insert into ".TP."co_codes_company set
				curr_co_codes_id='".mysql_real_escape_string($curr_co_codes_id)."'
				,long_name='".mysql_real_escape_string($company_name)."'
				,short_name='".mysql_real_escape_string($company_short_name)."'
				,logo='".mysql_real_escape_string($logo_name)."'
				,country_code='".mysql_real_escape_string($country_code)."'
				,sector='".mysql_real_escape_string($sector)."'
				,industry='".mysql_real_escape_string($industry)."'
				,cik='".mysql_real_escape_string($cik)."'
				,ric='".mysql_real_escape_string($ric)."'
				,bloomberg_code='".mysql_real_escape_string($bloomberg_code)."'
				,isin='".mysql_real_escape_string($isin)."'
				,sedol='".mysql_real_escape_string($sedol)."'
				,google_ticker='".mysql_real_escape_string($google_code)."'
				,co_code_timestamp='".$co_code_timestamp."'";
			$res = mysql_query($add_q,$conn);
			if($res===false){
				return false;
			}
			$added = true;
		}else{
			/**********
			There is a prev ID for this company. See if we get a match. If so, we may have to update it.
			***********/
			$q = "select id,curr_co_codes_id,co_code_timestamp from ".TP."co_codes_company where curr_co_codes_id='".mysql_real_escape_string($prev_co_codes_id)."'";
			$res = mysql_query($q,$conn);
			if(!$res){
				return false;
			}
			$row_count = mysql_num_rows($res);
			if($row_count!==0){
				/*******************
				We have the company, but with the old ID. We need to update the ID.
				We also need to check the timestamp to see if anything changed or not
				****************/
				$need_update = false;
				$row = mysql_fetch_assoc($res);
				$existing_timestamp = $row['co_code_timestamp'];
				if($existing_timestamp!==$co_code_timestamp){
					$need_update = true;
				}
				
				/*********
				first thing first, update ID
				*****/
				$updt_q = "update ".TP."co_codes_company set curr_co_codes_id='".mysql_real_escape_string($curr_co_codes_id)."' where id='".$row['id']."'";
				$res = mysql_query($updt_q,$conn);
				if($res===false){
					return false;
				}
				/*****
				Now see if we need to update or skip. Updating the co codes ID is not a real update
				****/
				if($need_update){
					/***
					TO DO: WRITE THAT CODE
					**/
					$updated = true;
				}else{
					$skipped = true;
				}
			}else{
				/************
				Somehow we missed the record. We add, but with the current ID
				*************/
				$logo_name = "";
				$logo_ok = download_logo($logo_url,$logo_name);
				if(!$logo_ok){
					$logo_name = "";
				}
				
				$add_q = "insert into ".TP."co_codes_company set
					curr_co_codes_id='".mysql_real_escape_string($curr_co_codes_id)."'
					,long_name='".mysql_real_escape_string($company_name)."'
					,short_name='".mysql_real_escape_string($company_short_name)."'
					,logo='".mysql_real_escape_string($logo_name)."'
					,country_code='".mysql_real_escape_string($country_code)."'
					,sector='".mysql_real_escape_string($sector)."'
					,industry='".mysql_real_escape_string($industry)."'
					,cik='".mysql_real_escape_string($cik)."'
					,ric='".mysql_real_escape_string($ric)."'
					,bloomberg_code='".mysql_real_escape_string($bloomberg_code)."'
					,isin='".mysql_real_escape_string($isin)."'
					,sedol='".mysql_real_escape_string($sedol)."'
					,google_ticker='".mysql_real_escape_string($google_code)."'
					,co_code_timestamp='".$co_code_timestamp."'";
				$res = mysql_query($add_q,$conn);
				if($res===false){
					return false;
				}
				$added = true;
			}
		}
	}
	return true;
}
function download_logo($url,&$logo_name){
	require_once(FILE_PATH."/classes/class.image_util.php");
	$img_obj = new image_util();
	
	if($url===""){
		return false;
	}
	/**********
	We need to clean the logo name and put a timestamp, the way we do in front end. Since we only
	need the thumbnail, we do that when creating the thumb.
	
	For the original image, we can store as it is (because we will delete it after creating the thumb
	************/
	$original_name = basename($url);
	$destination = FILE_PATH."/from_co-codes/logo/".$original_name;
	
	$remote_file = fopen($url, 'rb');
	if(!$remote_file){
		return false;
	}
	$local_file = fopen($destination, 'wb');
	if(!$local_file){
		return false;
	}
	
	while(!feof($remote_file)){
		fwrite($local_file, fread($remote_file, 1024), 1024);
	}
	fclose($remote_file);
	fclose($local_file);
	
	/*********
	now create the thumb
	********/
	$noblank = clean_filename(basename($url));
	$upload_img_name = time()."_".$noblank;
	$thumb_fit_width = 200;
	$thumb_fit_height = 200;
	/********
	see classes/class.company.php
	********/
	$success = $img_obj->create_resized($destination,FILE_PATH."/from_co-codes/logo/thumbnails",$upload_img_name,$thumb_fit_width,$thumb_fit_height,false);
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