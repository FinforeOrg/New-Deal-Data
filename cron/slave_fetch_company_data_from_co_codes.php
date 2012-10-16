<?php
/***********
It is assumed that at any given time, there is at most one running instance
*************/
require_once(dirname(dirname(__FILE__))."/include/global.php");
require_once("classes/class.background_slave_controller.php");
$master = new background_slave_controller();

$worker_name = "fetch_company_data_co_codes";
$master->set_status_note($worker_name,"started, fetching remote file");
/***************************************************************
fetch the csv file
****/
$source = "http://co-codes.com/store/dealdata.csv";
$destination = FILE_PATH."/from_co-codes/deal-data.csv";
/***********
sng:16/oct/2012
Using fopen instead of curl as curl is not installed in the remote server
************/

$fp = fopen($source,"rb");
$local = fopen($destination,"wb");
if($fp&&$local){
	while(!feof($fp)){
		fwrite($local,fread($fp, 1024*8));
		flush();
		if(connection_status()!=0) {
			fclose($fp);
			die();
		}
	}
	fclose($fp);
}
$master->set_status_note($worker_name,"done");
?>