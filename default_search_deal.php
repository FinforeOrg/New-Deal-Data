<?php
/****************
sng:21/nov/2011

sng:5/mar/2012
This is used to search for deals when the user type a firm name in top search bar and select Deals
We show deals
***************/
require_once("include/global.php");
require_once("classes/class.magic_quote.php");
require_once("classes/class.company.php");
require_once("classes/class.transaction.php");
require_once("nifty_functions.php");
/***********************************************************************************/
$g_view['search_form_input'] = $g_mc->view_to_view($_POST['top_search_term']);
$g_view['num_to_show'] = 10;
$g_view['start_offset'] = 0;
/**********************************************************************************
search for deals

sng:22/nov/2011
Let us fetch one extra. If that many are fetched, we show the 'show all'
***/
$g_view['deal_data'] = array();
$g_view['deal_data_count'] = 0;
$success = $g_trans->front_deal_search_paged($_POST,$g_view['start_offset'],$g_view['num_to_show']+1,$g_view['deal_data'],$g_view['deal_data_count']);
if(!$success){
	die("Cannot search for deal");
}
$g_view['deal_search_heading'] = "Deal search result";
/*************************************
sng:3/feb/2012
Here the user type a company name, select 'Deals' and search.
The result is a list of deals where the company is the search term AND for M&A deals, the result is a list of deals where the target or seller match the search term.
Now we no longer have this concept. Instead we have a list of participants with roles. I think we can get rid of two sections and show a single search result.
*************************************/
require_once("default_metatags.php");
$g_view['page_heading'] = "Search Result";
$g_view['content_view'] = "default_search_deal_view.php";
require("content_view.php");
?>