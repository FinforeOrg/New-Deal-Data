<?php   
/******************
sng:16/july/2012
Now we again need the league table page. We also show the league table form in the home page.
We use the embedded_league_table.php file to keep a single copy of the code
************************/
require_once("include/global.php");
/************
support for embedded league table
**************/
require("embedded_league_table.php");


require_once("classes/class.oneStop.php");  


//ini_set('display_errors',1);
//error_reporting(E_ALL);
if (isset($_REQUEST['token'])) {
    $savedSearches->loadIntoPost($_REQUEST['token']);
}

if (isset($_REQUEST['from']) && $_REQUEST['from'] == 'oneStop') {
    oneStop::loadIntoPost($_POST['data']);
}

require_once("default_metatags.php");
$g_view['content_view'] = "league_table_view.php";
require("content_view.php");
?>