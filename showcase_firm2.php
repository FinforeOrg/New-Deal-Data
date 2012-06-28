<?php
/*****
This is used to show case a bank or a law firm.
This shows 25 latest tombstones and a link to charts that make this firm look good
********/
include("include/global.php"); 
require_once("classes/class.company.php");
require_once("classes/class.transaction.php");
require_once("classes/class.account.php");
require_once("classes/class.country.php");
require_once("classes/class.account.php");
require_once("classes/class.savedSearches.php");
require_once("classes/class.oneStop.php"); 
$savedSearches = new SavedSearches();
//////////////////////////////////////
$g_view['firm_id'] = $_REQUEST['id'];
//get the firm data
$g_view['company_data'] = array();
$success = $g_company->get_company($g_view['firm_id'],$g_view['company_data']);
if(!$success){
	die("Cannot get company data");
}
////////////////////////////////////////////
//get the latest tombstones
$g_view['data'] = array();
$g_view['data_count'] = 0;
$success = $g_trans->get_showcase_deal_ids_of_firm($g_view['firm_id'],24,$g_view['data'],$g_view['data_count']);
if(!$success){
	die("Cannot get tombstones");
}
///////////////////////////////////////
require_once("default_metatags.php");

$g_view['content_view'] = "showcase_firm_view.php";

if (isset($_GET['from']) && $_GET['from'] = 'savedSearches') {
    if (isset($_GET['token'])) {
        $savedSearches->loadIntoPost($_GET['token']);
    }
    if (isset($_POST['data'])) {
       oneStop::loadIntoPost($_POST['data']);
    }
    $g_view['data'] = array();
    $g_view['data_count'] = 0;
    
    $g_view['data'] = $g_trans->getTombstonesForFirm($g_view['firm_id']);
    
    
    $g_view['data_count']  = sizeof($g_view['data']);
    $g_view['minMaxValues'] = $g_trans->getTombstonesForFirm($g_view['firm_id'], 0, 2, true);
   // echo "<div style='display:none'><pre>". print_r($g_view['minMaxValues'],1). "</pre></div>";
    //$success = $g_trans->get_showcase_deal_ids_of_firm($g_view['firm_id'],24,$g_view['data'],$g_view['data_count']);
    if(!$success){
        die("Cannot get tombstones");
    }
	$g_view['page_heading'] = "Credentials / Tombstones";
    $g_view['content_view'] = "showcase_firm_view_savedSearches2.php";
    $g_view['region_list'] = array();
    $g_view['region_count'] = 0;
    $success = $g_country->get_all_region_list($g_view['region_list'],$g_view['region_count']);
    if(!$success){
        die("Cannot get region list");
    }  
    $g_view['country_list'] = array();
    $g_view['country_count'] = 0;
    $success = $g_country->get_all_country_list($g_view['country_list'],$g_view['country_count']);
    if(!$success){
        die("Cannot get country list");
    } 
    
    $g_view['cat_list'] = array();
    $g_view['cat_count'] = 0;
    $success = $g_trans->get_all_category_type("type",$g_view['cat_list'],$g_view['cat_count']);
    if(!$success){
        die("Cannot get category list");
    }
    //////////////////////////////////////////////
    //fetch subcategories for this category
    $g_view['subcat_list'] = array();
    $g_view['subcat_count'] = 0;
    $success = $g_trans->get_all_category_subtype1_for_category_type($_POST['deal_cat_name'],$g_view['subcat_list'],$g_view['subcat_count']);
    if(!$success){
        die("Cannot get sub category list");
    }
    //////////////////////////////////////////////////////
    //fetch sub subcategories for this category
    $g_view['sub_subcat_list'] = array();
    $g_view['sub_subcat_count'] = 0;
    $success = $g_trans->get_all_category_subtype2_for_category_type($_POST['deal_cat_name'],$_POST['deal_subcat1_name'],$g_view['sub_subcat_list'],$g_view['sub_subcat_count']);
    if(!$success){
        die("Cannot get sub sub category list");
    }
    /////////////////////////////////////////////////////
    //fetch regions
    $g_view['region_list'] = array();
    $g_view['region_count'] = 0;
    $success = $g_country->get_all_region_list($g_view['region_list'],$g_view['region_count']);
    if(!$success){
        die("Cannot get region list");
    }
    //////////////////////////////////////////////////////////
    //fetch countries
    $g_view['country_list'] = array();
    $g_view['country_count'] = 0;
    $success = $g_country->get_all_country_list($g_view['country_list'],$g_view['country_count']);
    if(!$success){
        die("Cannot get country list");
    }

    //fetch sector types
    $g_view['sector_list'] = array();
    $g_view['sector_count'] = 0;
    $success = $g_company->get_all_sector_list($g_view['sector_list'],$g_view['sector_count']);
    if(!$success){
        die("Cannot get sector list");
    }
    ////////////////////////////////////
    //fetch industry types
    $g_view['industry_list'] = array();
    $g_view['industry_count'] = 0;
    $success = $g_company->get_all_industry_for_sector($_POST['sector'],$g_view['industry_list'],$g_view['industry_count']);
    if(!$success){
        die("Cannot get industry list");
    }
    $categories = $g_trans->getCategoryTree();
    
}
require("content_view.php");
?>