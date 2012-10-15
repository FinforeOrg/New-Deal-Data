<?php
require_once(dirname(dirname(__FILE__))."/include/global.php");
$source = "http://co-codes.com/store/dealdata.csv";
$destination = FILE_PATH."/from_co-codes/deal-data.csv";

$fp = fopen($destination, 'w');
$ch = curl_init($source);
if($ch){
	curl_setopt($ch, CURLOPT_FILE, $fp);
	$data = curl_exec($ch);
	curl_close($ch);
	fclose($fp);
}
?>