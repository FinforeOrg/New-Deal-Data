<?php
require_once("classes/class.country.php");
require_once("classes/class.company.php");
/*********
sng:18/jan/2013
embedded_league_table.php is included in
index_new.php [that include transaction]
league_table.php [that does not use transaction obj but use oneStop and oneStop include transaction]

Problem is, the league_table_filter_support.php still uses functions from transaction (and those functions are yet to be transported to transaction_support).
Once we fix that, we can use only the transaction_support here
*************************/
require_once("classes/class.transaction.php");

require_once("classes/class.transaction_support.php");
$g_transaction_support = new transaction_support();

require_once("classes/class.account.php");
require_once("classes/class.savedSearches.php");
$savedSearches = new SavedSearches();

//////////////////
//sng: 21/apr/2010
require("league_table_filter_support.php");
/*****************
sng:26/jan/2013
HACK
We need only the Equities, so we get a restricted set for now
***************/
$categories = $g_transaction_support->hack_get_category_tree();
////////////////////////////////////////////
?>