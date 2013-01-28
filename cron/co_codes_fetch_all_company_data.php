<?php
/*******************
sng:22/jan/2013

We use this to fetch all the company records from co-codes.com
***************/
require(dirname(dirname(__FILE__))."/include/minimal_bootstrap.php");
require(FILE_PATH."/classes/class.co_codes.php");
$co_code = new co_codes();

$co_code->get();
?>