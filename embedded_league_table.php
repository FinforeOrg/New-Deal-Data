<?php
require_once("classes/class.country.php");
require_once("classes/class.company.php");
require_once("classes/class.transaction.php");
require_once("classes/class.account.php");
require_once("classes/class.savedSearches.php");
$savedSearches = new SavedSearches();

//////////////////
//sng: 21/apr/2010
require("league_table_filter_support.php");
$categories = $g_trans->getCategoryTree();
//echo "<pre>" . print_r($categories,1) . "</pre>";
////////////////////////////////////////////
?>