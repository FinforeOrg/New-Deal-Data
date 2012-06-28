<?php
include("include/global.php"); 
require_once("default_metatags.php");  
require_once('classes/class.MobileDetection.php');
require_once('classes/mobileApp.php');

$platform = new MobileDetection();

if ($platform->isMobile() && !isset($_GET['forced'])) {
    $g_view['content_view'] = "2WeeksNow_view_mobile.php";
    require("content_view_mobile.php");    
    exit;
} 
                 
@session_start();
/*********
sng:7/apr/2011
This requires login, so we send to login page but remember where we were so that
aftr login, we can come here
******/
$_SESSION['after_login'] = "2WeeksNow.php?" . rand(1,222);
/****************************************
if (!@$_SESSION['is_member']) {
    header('Location: index.php');
}
******************/
require_once("check_mem_login.php");
/************************************************
sng:5/apr/2011
only banker and lawyers can access this section
***************/
//echo "<div style='display:none'> <pre>" . print_r($_SESSION,1) . "</pre></div>";
//$_SESSION['member_type'] = 'banker';
if(($_SESSION['member_type']!="banker")&&($_SESSION['member_type']!="lawyer")){
	$g_view['page_content'] = "This section is only for bankers and lawyers";
	require("not_authorised.php");
	exit;
}
/*******************************************************/
require_once("include/global.php"); 
require_once("classes/class.country.php"); 
require_once("classes/class.company.php");
require_once("classes/class.transaction.php"); 
require_once("classes/class.oneStop.php"); 

//dump($_SESSION);
require_once("default_metatags.php"); 
ini_set('display_errors',0);
error_reporting(E_ALL);
//dump($_SESSION);
if (!isset($_SESSION['is_member']))  $_SESSION['is_member'] = false; 
$methodCalled = false;

//if (isset($_REQUEST['action']) && strlen($_REQUEST['action'])) {
//    switch($_REQUEST['action']) {
//        case 'viewRequest' :
//            if (oneStop::loadRequestById($_GET['requestID'])) $_POST['submit'] = true;
//        break; 
//    }
//} 

function getTypeId($types, $type) {
    foreach ($types as $currType) {
           if($currType['subtype1'] == $type && $currType['subtype2'] == 'n/a')
            return $currType['id'];
    }
}

$mobileApp = new MobileApp();
    
if (isset($_GET['requestId'])) $_POST['submit'] = true;

if (!isset($_POST['submit'])) {
    country::get_all_country_list($countries,$countriesNr);
    company::get_all_sector_industry_list($industries, $industriesNr);
    transaction::get_all_category_type('*', $categories, $categoriesNr);
    $sortedCategories = array();
    //dump($categories);
    $prohibitedCategories = array(11,17);
    
    $alreadyAddedTypes = array();

    foreach ($categories as $category) {
        if (in_array($category['id'], $prohibitedCategories))
            continue;
       if (!in_array($category['subtype1'], $alreadyAddedTypes)) {
            $lbl = $category['subtype1'];
            if ($lbl == 'Completed') {
                $sortedCategories[$category['type']][] = array('name'=> "$lbl", 'id' => getTypeId($categories, $category['subtype1']), 'class' => 'subtype');    
            } else {
                if ($category['subtype1'] == 'Loan' || $category['subtype1'] == 'Bond') {
                    $lbl .= 's';    
                }
                $sortedCategories[$category['type']][] = array('name'=> "All $lbl", 'id' => getTypeId($categories, $category['subtype1']), 'class' => 'subtype'); 
                $alreadyAddedTypes[] = $category['subtype1'];               
            }

        } 
        if ($category['subtype2'] != 'n/a') {
            $sortedCategories[$category['type']][] = array('name'=>$category['subtype2'], 'id' => $category['id'], 'class' => 'sub-subtype'); 
        }
    };
    
    if (isset($sortedCategories['M&A']) && sizeOf($sortedCategories['M&A'])) {
        foreach ($sortedCategories['M&A'] as $maDeal) {
            $tmp[$maDeal['id']] = $maDeal['name'];
        }
    } 
    //$sortedCategories['M&A'][] = array('name'=>'Completed', 'id' => 18); 
    $sortedCategories['M&A'][] = array('name'=>'Completed & Pending', 'id' => '0', 'class' => 'subtype'); 
    //dump($sortedCategories);
    $sortedIndustries = array();
    foreach ($industries as $industry) {
       $sortedIndustries[$industry['sector']][] =  $industry;
    }

    $g_view['page_heading'] = "2 Weeks Now";
    $g_view['top_search_view'] = "all_search_view.php";
    $g_view['content_view'] = "2WeeksNow_view.php";

    $currentUserRequests = $mobileApp->getRequestsForCurrentUser();

}

if (isset($_POST['submit'])) {

    $g_view['page_heading'] = "2 Weeks Now :: Results";
    $g_view['top_search_view'] = "all_search_view.php";
    $g_view['content_view'] = "2WeeksNow_results_view.php";

    $results = $mobileApp->getResults(true);
    
    $industry = $_POST['industry'];
    $dealType = $_POST['dealType'];
    $country = $_POST['country'];
    $currentDate = date('Y-m-d'); 

    $dealArr = $mobileApp->getDealById($dealType);
    $countryArr = $mobileApp->getCountryById($country);
    $industryArr = $mobileApp->getIndustryById($industry);
    
    
}
require("content_view.php"); 
