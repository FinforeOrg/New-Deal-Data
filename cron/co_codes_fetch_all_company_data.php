<?php
/*******************
sng:22/jan/2013

We use this to fetch all the company records from co-codes.com
***************/
require(dirname(dirname(__FILE__))."/include/minimal_bootstrap.php");
require(FILE_PATH."/classes/class.co_codes.php");
$co_code = co_codes::create();
if($co_code===false){
	print_r("cannot create co-code object\r\n");
	return;
}
$co_code->get_all_company_data();
?>