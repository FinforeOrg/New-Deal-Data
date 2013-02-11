<?php
/*******************
sng:11/feb/2013
Now we use the co_codes class
******************/
require(dirname(dirname(__FILE__))."/include/minimal_bootstrap.php");
require(FILE_PATH."/classes/class.co_codes.php");
$co_code = co_codes::create();
if($co_code===false){
	print_r("cannot create co-code object\r\n");
	return;
}
$co_code->get_all_equity_deal_data();
?>