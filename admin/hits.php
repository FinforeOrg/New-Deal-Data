<?php
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.member.php");
require_once("classes/class.country.php");
require_once("classes/class.company.php");
require_once("classes/class.magic_quote.php");  

$tableName = TP.'hit_count';  
if (isset($_GET['action']) && $_GET['action'] == 'resetCounter') {
    $referer = $_GET['referer'];
    $q = "UPDATE $tableName SET hits = 0 WHERE referer  = '$referer' LIMIT 1";
    mysql_query($q);
}


$qSavedSearch = "SELECT hits from $tableName WHERE referer = 'savedSearch' LIMIT 1";
$savedSearchHits = '0';
if ($res = mysql_query($qSavedSearch)) {
    $result = mysql_fetch_assoc($res);
    $savedSearchHits = $result['hits'];    
}

$g_view['heading'] = "View hits";
$g_view['content_view'] = "admin/hits_view.php";
include("admin/content_view.php");
?>
