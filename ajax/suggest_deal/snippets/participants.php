<?php
/*****************
sng: 8/feb/2012
Used to fetch text boxes to add companies, with proper role preselected.
In fact, for the deal type, we see how many roles are marked as preselected and fetch that many boxes
********************/
require_once("../../../include/global.php");


if(isset($_GET['deal_type'])&&(init_participants!="")){
	$g_view['deal_type'] = $_GET['deal_type'];
}else{
	$g_view['deal_type'] = "M&A";
}

require_once("ajax/suggest_deal/snippets/participants_any.php");
?>