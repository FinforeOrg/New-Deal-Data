<?php

@session_start();
//error_reporting(E_ALL);
//ini_set('display_errors',true);
$_SESSION['after_login'] = "leagueTableComparison.php?token=" . $_GET['token'];
include("include/global.php");
require_once("check_mem_login.php");
//////////////////////////////////////////
//support for filters
require_once("classes/class.transaction.php");
require_once("classes/class.country.php");
require_once("classes/class.company.php");
require_once("classes/class.statistics.php");
require_once("classes/class.savedSearches.php");
//////////////////////////////////////////////
$savedSearches = new SavedSearches();

$info = unserialize(base64_decode($_GET['token']));
$errors = false;
$g_view['message'] = '';
$currentSavedSearch = $savedSearches->getById($info['id']);


if (!is_array($currentSavedSearch) && sizeof($currentSavedSearch)) {
    $g_view['message'] = "Token received is invalid";
    $errors = true;
}
if (!$errors) {
    $searchDetails = $savedSearches->cleanAndTranslate($currentSavedSearch['parameters']);
    $savedSearches->loadIntoPostByParams($currentSavedSearch['parameters']);
    
    //$_POST['min_date'] = $info['date'];
    $_POST['max_date'] = $info['date'];
    $g_view['start_offset'] = 0;
    $g_view['num_to_show'] = 9;

    $startDate = $info['date'];
    $endDate = $currentSavedSearch['last_alert_date'];

    unset($g_view['data']);
    $g_stat->front_generate_league_table_for_firms_paged($_POST, $g_view['start_offset'], $g_view['num_to_show']+1, $g_view['data'], $g_view['data_count']);

    $firstTableData = $g_view['data'];
    unset($g_view['data']);
    $_POST['max_date'] = $currentSavedSearch['last_alert_date'];
    $g_stat->front_generate_league_table_for_firms_paged($_POST, $g_view['start_offset'], $g_view['num_to_show']+1, $g_view['data'], $g_view['data_count']);
    $secondTableData = $g_view['data'];

    $_POST['last_alert_date'] = $info['date'];
    $_POST['last_alert_date_max'] = $currentSavedSearch['last_alert_date'];

    unset($g_view['data']);
    $dealsWereFetched = $g_trans->front_deal_search_paged($_POST, $g_view['start_offset'], $g_view['num_to_show']+1, $g_view['data'], $g_view['data_count']); 
    if ($dealsWereFetched) {
        $dealsAdded = $g_view['data'];
    } else {
        $dealsAdded = array();
    }
}
$g_view['page_heading'] = "League table comparison";
$g_view['content_view'] = "leagueTableComparison_view.php";
require("content_view.php");