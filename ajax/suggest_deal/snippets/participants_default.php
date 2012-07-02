<?php
/*****************
sng: 8/feb/2012
Used to fetch text boxes to add companies, with proper role preselected.
In fact, for the deal type, we see how many roles are marked as preselected and fetch that many boxes

This is embedded in php code, when the suggest_a_deal_view.php is run, so we do not need the global.php
********************/



$g_view['deal_type'] = "M&A";

require_once("ajax/suggest_deal/snippets/participants_any.php");
?>