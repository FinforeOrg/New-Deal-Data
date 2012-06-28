<?php

/**
* feeData.php file
*
* $Id:$
*
* $Rev:  $
*
* $LastChangedBy:  $
*
* $LastChangedDate: $
*
* @author Ionut MIHAI <ionut_mihai25@yahoo.com>
* @copyright 2011 Ionut MIHAI
*/

                
@session_start();
require_once("include/global.php");
/***************
sng:10/nov/2011
Let us make this open here
$_SESSION['after_login'] = "feeData.php";
require_once("check_mem_login.php");
******************/
require_once("classes/class.country.php"); 
require_once("classes/class.company.php");
require_once("classes/class.transaction.php"); 
require_once("classes/class.feeData.php"); 
require_once("classes/class.OneStopPowerpoint.php"); 

if (isset($_GET['action']) && $_GET['action'] == 'download') {
    if (count($_POST['download_pptx_fee_chart'])) {
        $powerpoint = new OneStopPowerpoint($_POST);
        $powerpoint->downloadFeeFile();
    }
}

require_once("default_metatags.php"); 
ini_set('display_errors', 1);
error_reporting(E_ALL ^ E_NOTICE);

$g_view['page_heading'] = "Fee Data & Analysis";
$g_view['top_search_view'] = "all_search_view.php";
$g_view['content_view'] = "feeData_view.php";

$feeData = new feeData();

if (isset($_GET['getNexFromMultiPage'])) {
    if (isset($_GET['getData']) && $_GET['getData'] == 'y') {
        echo $feeData->getNextFromMultiPage();
    }
    exit(0);
} else {
    //** first visin, we should cleanup
    unset($_SESSION['remainingCharts']);
}

$categories = $feeData->getAvailableCategories();

$regions = $feeData->getAvailableRegions();
$countries = $feeData->getAvailableCountries();
$msg = '';

if (count($_POST) && $_POST['filter'] == 'POST') {
    if ( (isset($_POST['region']) && $_POST['region'] == -2) && (isset($_POST['country']) && $_POST['country'] == -2) ) {
        $msg = 'The parameter combination you chosen is not valid. Please select at least one Region/Country';
    } else {
        $charts = $feeData->getPossibleCharts();
    }
} else {
        $charts = $feeData->getRandomCharts();
}

require("content_view.php");
