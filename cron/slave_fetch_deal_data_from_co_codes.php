<?php
/**********
sng:14/dec/2012
Now, these are called inside slave_fetch_from_co_codes.php
*************/
$master->set_status_note($worker_name,"fetching deal data file from co-codes");
/***************************************************************
fetch the csv file
****/
$data_source = "http://co-codes.com/store/dealdata_nasdaq.csv";

$data_destination = FILE_PATH."/from_co-codes/deals.csv";

$ok = fetch_and_store_remote_file($data_source,$data_destination);
if(!$ok){
	$master->set_status_note($worker_name,"error fetching co-codes deal data file");
	exit;
}
$master->set_status_note($worker_name,"fetched co-codes deal data file");

/*******
delete this csv file
********/
//unlink($data_destination);
//$master->set_status_note($worker_name,"removed co-codes company data file");
return;