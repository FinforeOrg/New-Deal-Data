<?php
require_once("include/global.php");
require_once("classes/class.savedSearches.php");
require_once("classes/class.account.php");

$_SESSION['after_login'] = "watchlist.php";
require_once("check_mem_login.php");

$savedSearches = new SavedSearches();
if ($_SERVER['HTTP_X_REQUESTED_WITH'] == "XMLHttpRequest") {
    //header("Content-type: application/x-javascript;\n");
    if(!$g_account->is_site_member_logged()){
       echo json_encode(array('message'=>"You need to login !"));
       exit(0);
    }
    $action = @$_REQUEST['action'];
    $label = (isset($_REQUEST['alert']) && $_REQUEST['alert'] == "1") ? "alert" : "search";
    $extra = (isset($_REQUEST['alert']) && $_REQUEST['alert'] == "1") ? "&action=addAlert" : "";
    switch ($action) {
       
        
         case "deleteSearch" :
         $searches = array('tombstone', 'deal', 'league','leagueDetail','volumes', 'volumesDetail');
         if (!in_array($_REQUEST['type'], $searches)) {
            echo json_encode(array('message'=>"Your request is invalid", "newLocation"=>"saved_searches.php#".$_REQUEST['type']));
            break;
         }

            if  ($savedSearches->deleteSearch($_REQUEST['id'],$_REQUEST['type']))
                echo json_encode(array('message'=>"The $label has been deleted", "newLocation"=>"saved_searches.php#".$_REQUEST['type']));
            else
                echo json_encode(array('message'=>"The $label could not be deleted. Please try again in a couple of minutes"));
        break;
       
        
    }
    exit(0);    
} 

require_once("classes/class.deal_support.php");
$deal_support = new deal_support();

require_once("classes/class.transaction_discussion.php");

if(isset($_POST['myaction'])&&($_POST['myaction']=="remove_deal_from_watch")){
	$success = $deal_support->remove_deal_from_watch($_POST['watch_id']);
	if(!$success){
		die("Cannot remove deal from watch list");
	}
}

if(isset($_POST['myaction'])&&($_POST['myaction']=="remove_deal_discussion_from_watch")){
	$success = $g_deal_disc->remove_deal_discussion_from_watch($_POST['watch_id']);
	if(!$success){
		die("Cannot remove deal discussion from watch list");
	}
}
/*********************************************************************
get the watched deals

sng:12/sep/2011
we check what filter is set, default is last 7 days. That option requires the value of d|7
(see the watchlist_view.php
************/
if(!isset($_POST['watchlist_filter'])||($_POST['watchlist_filter']=="")){
	$g_view['watchlist_filter'] = "d|7";
}else{
	$g_view['watchlist_filter'] = $_POST['watchlist_filter'];
}

$g_view['watch_list'] = NULL;
$g_view['watch_count'] = 0;
$success = $deal_support->get_watched_deals_for_members($_SESSION['mem_id'],$g_view['watchlist_filter'],$g_view['watch_list'],$g_view['watch_count']);
if(!$success){
	die("Cannot get the deal watchlist");
}

//get the watched deal discussion
$g_view['discussion_watch_list'] = NULL;
$g_view['discussion_watch_count'] = 0;
$success = $g_deal_disc->get_watched_deal_discussion_for_members($_SESSION['mem_id'],$g_view['watchlist_filter'],$g_view['discussion_watch_list'],$g_view['discussion_watch_count']);
if(!$success){
	die("Cannot get the deal discussion watchlist");
}
/***********************************************************************/
$mySavedSearches = array('tombstone'=>false,'deal' => false);
if ($_SESSION['mem_id']) {
	$mySavedSearches = $savedSearches->getForUser($_SESSION['mem_id']);
    $mySavedAlerts = $savedSearches->getAlertForUser($_SESSION['mem_id']);
}

require_once("default_metatags.php");
$g_view['page_heading'] = "My Watchlist";
$g_view['content_view'] = "watchlist_view.php";
require("content_view.php");
?>