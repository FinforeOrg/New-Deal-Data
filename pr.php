<?php
//ini_set('display_errors',1);
//error_reporting(E_ALL);
include("include/global.php");
/*********************************
sng:23/mar/2011
This now requires login
**************/
$_SESSION['after_login'] = "pr.php";
require_once("check_mem_login.php");

require_once("classes/class.account.php");
require_once("classes/class.twitter.php");
  

$g_view['page_heading'] = "Deal Press Releases from Banks and Law Firms";
$g_view['content_view'] = "pr_view.php";

$prTable = TP.'press_releases';
$prTagsTable = TP.'press_releases_tags';
$settingsTable = TP.'pr_settings';

/****
sng:10/nov/2010
support for pagination
********/
if (isset($_GET['tag'])) {
    $tag = mysql_escape_string(urldecode($_GET['tag']));
	$g_view['target_page'] = "pr.php?tag=" . urlencode($tag)."&";
	//we need a count query
	$q = "SELECT count(*) as cnt FROM {$prTable} pr JOIN {$prTagsTable} tags ON tags.press_release_id = pr.id WHERE tags.tag = '$tag'";
    //$q = "SELECT pr.* FROM {$prTable} pr JOIN {$prTagsTable} tags ON tags.press_release_id = pr.id WHERE tags.tag = '$tag'";
} else {
	$g_view['target_page'] = "pr.php?";
	$q = "SELECT count(*) as cnt FROM $prTable WHERE TRUE ORDER BY UNIX_TIMESTAMP(`date`) DESC";
   //$q = "SELECT * FROM $prTable WHERE TRUE ORDER BY UNIX_TIMESTAMP(`date`) DESC"; 
}
//first we do pagination part
$q_res = mysql_query($q) or die(mysql_error());
$q_res_row = mysql_fetch_assoc($q_res);
$g_view['total_pages'] = $q_res_row['cnt'];
$g_view['limit'] = 40; //how many items to show
$g_view['adjacents'] = 4;// How many adjacent pages should be shown on each side?
$g_view['page'] = 1;//default
if(isset($_GET['page'])&&($_GET['page']!="")){
	$g_view['page'] = $_GET['page'];
}
$g_view['start'] = ($g_view['page'] - 1) * $g_view['limit'];
$g_view['prev'] = $g_view['page'] - 1;							//previous page is page - 1
$g_view['next'] = $g_view['page'] + 1;							//next page is page + 1
$g_view['lastpage'] = ceil($g_view['total_pages']/$g_view['limit']);		//lastpage is = total pages / items per page, rounded up.
$g_view['lpm1'] = $g_view['lastpage'] - 1;						//last page minus 1
/**
Now we apply our rules and draw the pagination object. 
We're actually saving the code to a variable in case we want to draw it more than once.
**/
require_once("pagination_support.php");

//now we set up the page limited query
if (isset($_GET['tag'])) {
    $tag = mysql_escape_string(urldecode($_GET['tag']));
	/***
	sng:10/nov/2010
	why not order this by date also?
	***/
    $q = "SELECT pr.* FROM {$prTable} pr JOIN {$prTagsTable} tags ON tags.press_release_id = pr.id WHERE tags.tag = '$tag' ORDER BY UNIX_TIMESTAMP(`date`) DESC";
} else {
   $q = "SELECT * FROM $prTable WHERE TRUE ORDER BY UNIX_TIMESTAMP(`date`) DESC"; 
}
$q.=" LIMIT ".$g_view['start'].",".$g_view['limit'];
$res = mysql_query($q);
$presReleases = array();
$i = 0;
while($row = mysql_fetch_assoc($res)) {
    $presReleases[$i] = $row;
    $id = $row['id'];
    $q2 = "SELECT tag FROM $prTagsTable WHERE press_release_id = $id ";
    $res2 = mysql_query($q2);
    while ($row2 = mysql_fetch_assoc($res2)) {
        $tags[$i][] = $row2['tag'];
    }
    $i++;
}
$g_view['show_help'] = true;
require("content_view.php");
?>
