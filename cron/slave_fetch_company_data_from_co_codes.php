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
$source = "http://co-codes.com/store/dealdata.csv";
$destination = FILE_PATH."/from_co-codes/deal-data.csv";

$ok = fetch_and_store_remote_file($source,$destination);
if(!$ok){
	$master->set_status_note($worker_name,"error fetching file");
	exit;
}
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
We use the short name to do a lookup in the intermediate table (say next day when we parse again), need index on the short name

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
	****************************/
	$records_scanned = 0;
	$records_error = 0;
	$records_added = 0;
	$records_skipped = 0;
	
	while (($csv_data = fgetcsv($r_handle, 10240, ',', '"','|')) !== false) {
		$records_scanned++;
		/******************
		get data
		*********************/
		$added = false;
		$ok = process_record($csv_data,$added);
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
				$records_skipped++;
			}
		}
		
	}
	fclose($r_handle);
	$msg = "scanned: ".$records_scanned.", error: ".$records_error.", added: ".$records_added.", skipped: ".$records_skipped;
	return true;
}
/*****
added: true/false, whether the record is added to intermediate table

We check if we have the required number of cells or not. If not, it is an error and we return false
*********/
function process_record($row_data,&$added){
	global $conn;
	
	$num_cols_expected = 16;
	$col_cnt = count($row_data);
	if($col_cnt!==$num_cols_expected){
		return false;
	}
	
	$cik = trim($row_data[0]);
	$company_name = trim($row_data[1]);
	$company_short_name = trim($row_data[2]);
	$logo_url = trim($row_data[3]);
	$country_code = trim($row_data[4]);
	$co_code_country_name = trim($row_data[5]);
	/************
	Since the co-codes country name is not the way we want, we will only use the country code. When transferring to our table,
	we will do a lookup and get our country name
	**********************/
	
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
	$sector = $icb_industry;
	$industry = $icb_sector;
	
	$ric = trim($row_data[10]);
	$bloomberg_code = trim($row_data[11]);
	$isin = trim($row_data[12]);
	$sedol = trim($row_data[13]);
	$google_code = trim($row_data[14]);
	
	$co_code_timestamp = trim($row_data[15]);
	/**********
	since we are storing datetime, we check if this is blank. If so, we convert it
	**************/
	if($co_code_timestamp===""){
		$co_code_timestamp = "0000-00-00 00:00:00";
	}
	
	/**************
	We first check whether we already have the record or not. We use the ‘short name’ to check.
	If not found, we add and we fetch the logo (if one is specified). If found, we check the timestamp of the existing record with the timestamp in the csv.
	If same, we ignore else we update the record
	
	Remember to escape: else you will face problem in data like ZORAN CORP \DE\
	******************/
	$q = "select * from ".TP."co_codes_company where short_name='".mysql_real_escape_string($company_short_name)."'";
	$res = mysql_query($q,$conn);
	if(!$res){
		return false;
	}
	$row_count = mysql_num_rows($res);
	if(0===$row_count){
		/******
		fetch the logo
		if problem fetching logo, no issue. Just set the logo_name to blank so that the other codes do not try to show
		a logo that is not there
		*****/
		$logo_name = "";
		$logo_ok = download_logo($logo_url,$logo_name);
		if(!$logo_ok){
			$logo_name = "";
		}
		
		
		/****
		make sure you remove the spaces etc else problem with powerpoint presentation of tombstones
		***********/
		$add_q = "insert into ".TP."co_codes_company set
			long_name='".mysql_real_escape_string($company_name)."'
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
		/********
		there is a matching record, fetch it
		********/
		$row = mysql_fetch_assoc($res);
		$existing_timestamp = $row['co_code_timestamp'];
		if($existing_timestamp===$co_code_timestamp){
			/*********
			in the csv, the record is same as what we have in the intermediate database (as the timestamp is same)
			so ignore
			*********/
			$added = false;
		}else{
			/*********
			update, including the logo since we do not know what has changed
			*********/
			$added = true;
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