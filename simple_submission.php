<?php
/*****************
To be embedded in home page and simple submission page
*********************/
require_once("classes/class.transaction.php");
require_once("classes/class.deal_support.php");
$deal_support = new deal_support();
$g_db = new db();

/*******************************************************************
support for simple submission form
**/
$categories = $g_trans->getCategoryTree();

$g_view['value_range_items'] = NULL;
$g_view['value_range_items_count'] = 0;
$success = $deal_support->front_get_deal_value_range_list($g_view['value_range_items'],$g_view['value_range_items_count']);
if(!$success){
	die("cannot get deal size ranges");
}
/******************************************/

require("simple_submission_view.php");
?>