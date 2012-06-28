<?php

@session_start();
//error_reporting(E_ALL);
//ini_set('display_errors',true);
include("include/global.php");

//////////////////////////////////////////
//support for filters
require_once("classes/class.transaction.php");
require_once("classes/class.country.php");
require_once("classes/class.company.php");
require_once("classes/class.statistics.php");
require_once("classes/class.savedSearches.php");
//////////////////////////////////////////////

$savedSearches = new SavedSearches();
if (!isset($_GET['token'])) {
    $_SESSION['after_login'] = "leagueTableComparisonHistory.php";
    $g_view['page_heading'] = "League Table Forensics";
    $g_view['content_view'] = "leagueTableComparisonHistory_view.php";     
    $notifications = $savedSearches->getNotificationsForUser();
} else {
    $_SESSION['after_login'] = "leagueTableComparisonHistory.php?token=" . $_GET['token'];
    $g_view['page_heading'] = "League Table Forensics Details";
    $g_view['content_view'] = "leagueTableComparisonHistory_details_view.php"; 
    
    $currentSavedSearch = $savedSearches->getNotificationFromHistory(base64_decode($_GET['token']));
    
    $savedSearches->loadIntoPostByParams($currentSavedSearch['parameters']);
    $searchDetails = $savedSearches->cleanAndTranslate($currentSavedSearch['parameters']);
    //$_POST['min_date'] = $info['date'];
    $_POST['max_date'] = $currentSavedSearch['start_date'];
    $g_view['start_offset'] = 0;
    $g_view['num_to_show'] = 9;

    $startDate = $currentSavedSearch['start_date'];
    $endDate = $currentSavedSearch['end_date'];

    unset($g_view['data']);
    $g_stat->front_generate_league_table_for_firms_paged($_POST, $g_view['start_offset'], $g_view['num_to_show']+1, $g_view['data'], $g_view['data_count']);

    $firstTableData = $g_view['data'];
    unset($g_view['data']);
    $_POST['max_date'] = $currentSavedSearch['end_date'];
    $g_stat->front_generate_league_table_for_firms_paged($_POST, $g_view['start_offset'], $g_view['num_to_show']+1, $g_view['data'], $g_view['data_count']);
    $secondTableData = $g_view['data'];

    $_POST['last_alert_date'] = $currentSavedSearch['start_date'];
    $_POST['last_alert_date_max'] = $currentSavedSearch['end_date'];

    unset($g_view['data']);
    $dealsWereFetched = $g_trans->front_deal_search_paged($_POST, $g_view['start_offset'], $g_view['num_to_show']+1, $g_view['data'], $g_view['data_count']); 
    if ($dealsWereFetched) {
        $dealsAdded = $g_view['data'];
    } else {
        $dealsAdded = array();
    }    
}


require_once("check_mem_login.php");
require("content_view.php");