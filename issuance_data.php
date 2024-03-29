<?php
/**********
This is used to create stat chart on the fly, based on conditions

sng: 3/jun/2010
we show a home page chart by default
********/
include("include/global.php");
/**************************************************************
sng:23/nov/2010
This now require login

sng:19/jul/2012
This is now open to all
*******
$_SESSION['after_login'] = "issuance_data.php";
require_once("check_mem_login.php");
***********************************************************/
/**********
sng:26/jan/2013
require_once("classes/class.transaction.php");
It seems, we are using the big transaction class just to get the category tree.
We can now get it from the transaction_support class.
If we face any problem we will again include the transaction class
**************/
require_once("classes/class.transaction_support.php");
$trans_support = new transaction_support();

require_once("classes/class.country.php");
require_once("classes/class.company.php");
require_once("classes/class.account.php");
require_once("classes/class.statistics.php");
require_once("classes/class.savedSearches.php");
require_once("classes/class.stat_help.php");
$savedSearches = new SavedSearches();
if (isset($_REQUEST['token'])) {
    $savedSearches->loadIntoPost($_REQUEST['token']);
}
/**********************
sng:3/aug/2012
We cannot send data like >= in POST. The sanitiser will erase it.
So we base64 encoded the view file
and we decode it here again

sng:4/sep/2012
We need to check whether $_POST['deal_size'] is set or not. Only then we call base64_decode
Why this is needed? We can come to this page 3 ways
1) From saved search option
2) From menu option
3) From the detail page

(1) If we come here from saved search option and if we did not specified the size then we should not pre select the 100m in the view
We see that for (1) $_POST['deal_size'] is set and is blank if no option was selected.

For (2), $_POST['deal_size'] is not set
We preselect the 100m by default
(In the other cases this was deliberately set to 'no size' filter value)

For (3) $_POST['deal_size'] is set and is blank if no option was selected.

Thus we do not set $_POST['deal_size'] = base64_decode($_POST['deal_size']) blindly. we will not be able to distinguish case (2)
************************/
if(isset($_POST['deal_size'])){
	$_POST['deal_size'] = base64_decode($_POST['deal_size']);
}else{
}

//////////////////
//sng: 21/apr/2010
require("league_table_filter_support.php");
////////////////////////////////////////////
require_once("default_metatags.php");

/********************************
sng:7/jan/2011
If the page comes from issuance_data_detail_view, $_POST['myaction'] is set to gen_chart. In that case, we do not
get the random chart data and the count is 0 and no default chart is displayed

sng:19/july/2012
We no longer show any pre generated chart
*************/


/***************************************
sng:5/jan/2011
By default, client wants default groupings to be half year
*********/
if(!isset($_POST['month_division'])||($_POST['month_division']=="")){
	$_POST['month_division'] = "h";
}
/*****************************************/
/*********
sng:27/nov/2010
get the month div list based on the month div selected
*******************/
$g_view['month_div'] = array();
$g_view['month_div']['value_arr'] = NULL;
$g_view['month_div']['label_arr'] = NULL;
$g_view['month_div_cnt'] = 0;
$g_stat_h->volume_get_month_div_entries($_POST['month_division'],$g_view['month_div']['value_arr'],$g_view['month_div']['label_arr']);
$g_view['month_div_cnt'] = count($g_view['month_div']['value_arr']);
/********************************
sng:5/jan/2011
By default, the first value of the list is to be selected
******/
if(!isset($_POST['month_division_list'])||($_POST['month_division_list']=="")){
	$_POST['month_division_list'] = $g_view['month_div']['value_arr'][0];
}
/*********************************************/
$g_view['page_heading'] = "Issuance Data";
$g_view['content_view'] = "issuance_data_view.php";
/**********
sng:26/jan/2013
using transaction_support instead of transaction
$categories = $g_trans->getCategoryTree();

sng:26/jan/2013
HACK
We just want a restricted set, so we use the hack version
***************/
$categories = $trans_support->hack_get_category_tree(); 
$g_view['show_help'] = true;
require("content_view.php");
?>