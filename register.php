<?php
require_once("include/global.php");
require_once("classes/class.member.php");
require_once("classes/class.country.php");
require_once("classes/class.company.php");
require_once("classes/class.transaction.php");
require_once("classes/class.magic_quote.php");
require_once("recaptcha_1_11/recaptchalib.php");
require_once("recaptcha_1_11/recaptcha_conf.php");
////////////////////////////////////////////////////////////
require_once("default_metatags.php");

$g_view['err'] = array();
$g_view['msg'] = "";
$g_view['input'] = array();
$g_view['req_id'] = "";
/////////////////////////Member Insert/////////////////////////////////////////
/***
sng:6/apr/2010
we no longer allow user to select a company from a list. We allow the user to type the company name
and if that company name is there a hint appears which the user can select or can keep on typing the name

sng:20/apr/2010: since we send activation email to work email only, we update the msg
see member::accept_registration_request

sng:5/jun/2010: we check if user wants to add deals to his/her profile or not. For that, 
we show a checkbox and need to show the types of deals in the registration page

If the checkbox is checked we take the user to second page after adding the registration request (assuming no validation error)
However, since we are already adding the registration request, we need the registration row primary key, so we change the registration function a bit.
**********/
if(isset($_POST['action'])&&($_POST['action']=="add")){
	
	$validation_passed = false;
	$success = $g_mem->new_membership_request($_POST,$_SESSION['security_code'],$validation_passed,$g_view['err'],$g_view['req_id']);
	if(!$success){
		die("Cannot add membership request");
	}
	if($validation_passed){
		//default message
		$g_view['msg'] = "WE ARE CHECKING YOUR REGISTRATION DETAILS. PLEASE <strong>DO NOT</strong> TRY AND LOG-IN UNTIL YOU HAVE RECEIVED THE WELCOME EMAIL. THANK YOU FOR YOUR PATIENCE.";
		/****
		sng:27/july/2010
		now check whether this is a favourite or not. If favourite, we prepare the request for activation (which emails the member)
		*******/
		$g_view['is_favoured'] = false;
		$success = $g_mem->is_work_email_favoured($_POST['type'],$_POST['work_email'],$_POST['firm_name'],$g_view['is_favoured']);
		if($success){
			if($g_view['is_favoured']){
				$success = $g_mem->favour_accept_registration($g_view['req_id']);
				if(!$success){
					die("Cannot accept membership request");
				}
				//accepted, email sent, the message will be
				$g_view['msg'] = "WE HAVE SENT THE WELCOME EMAIL TO YOUR WORK EMAIL. IT CONTAINS THE ACTIVATION LINK. YOU WILL HAVE TO CLICK THAT LINK BEFORE YOU CAN LOG-IN.";
			}
		}else{
			//we treat this as if not favoured
		}
		
		//now, if add_deals is there, then show the add deals page
		/*************************************
		sng:13/jan/2011
		We just allow the user to register and do not add any deal. We add deals when the user logs in
		if(isset($_POST['add_deal'])){
			include("register_show_deal.php");
			exit;
		}
		*****************************************/
	}else{
		//the form is to be shown with data just entered
		$g_view['input']['first_name']        = $g_mc->view_to_view($_POST['first_name']);
		$g_view['input']['last_name']         = $g_mc->view_to_view($_POST['last_name']);
		$g_view['input']['password']          = $_POST['password'];
		$g_view['input']['re_password']       = $_POST['re_password'];
		$g_view['input']['type']              = $_POST['type'];
		$g_view['input']['home_email']        = $_POST['home_email'];
		$g_view['input']['work_email']        = $_POST['work_email'];
		$g_view['input']['firm_name']        = $g_mc->view_to_view($_POST['firm_name']);
		$g_view['input']['designation']       = $_POST['designation'];
		
		$g_view['input']['join_date']         = $_POST['join_date'];
		$g_view['input']['location']          = $_POST['location'];
		$g_view['input']['division']          = $_POST['division'];
	}
}
///////////////////////////////////////////////////////////////////////
/***
sng:6/apr/2010
We no longer fetch list of banks, law firms, companies. The view code fetches them
as per membership type selected and letter entered
************/

///////////////////////////////////////////////////////////
/****
sng:6/apr/2010
we check if membership type is selected or not. If selected, we fetch only those designations
**********/
$g_view['designation_list'] = array();
$g_view['designation_count'] = 0;
if(isset($_POST['type'])&&($_POST['type']!="")){
	$success = $g_mem->get_all_designation_list_by_type($_POST['type'],$g_view['designation_list'],$g_view['designation_count']);
	if(!$success){
		die("Cannot get designation list");
	}
}
///////////////////////////////////////////////////////////



//fetch headquarter_country names
$g_view['country_list'] = array();
$g_view['country_count'] = 0;
$success = $g_country->get_all_country_list($g_view['country_list'],$g_view['country_count']);
if(!$success){
	die("Cannot get country list");
}
///////////////////////////////////////////////////////////
//fetch Category names
/*************************************************
sng:13/jan/2011
We do not fetch deal categories. Even if we allow to add deals, we will put the filters
in the show deal page, not here. 
$g_view['cat_list'] = array();
$g_view['cat_count'] = 0;
$success = $g_trans->get_all_category_type("type",$g_view['cat_list'],$g_view['cat_count']);
if(!$success){
	die("Cannot get category list");
}
*********************************************/
/////////////////////////////////
/********
sng:11/apr/2011
We want to show the heading and a button in the view page
**********/
$g_view['page_heading'] = "";
////////////////////////////////////////////////////
$g_view['content_view'] = "register_view.php";

if (isset($_REQUEST['method'])) {
    if ($_REQUEST['method'] == "choose") {
		$g_view['page_heading'] = "Member Registration";
        $g_view['content_view']  = "register_choose_view.php";
    }
}
if (isset($_REQUEST['from']) && $_REQUEST['from'] == "linkedIn") {
  $data = base64_decode($_REQUEST['token']);
  $data = unserialize($data);  
        $g_view['input']['first_name']        = $data['first-name'];
        $g_view['input']['last_name']         = $data['last-name'];
        $g_view['input']['firm_name']        = $data['lastWorkplace']['company'];        
        $g_view['input']['join_date']         = $data['lastWorkplace']['start-date'];
        $g_view['input']['location']            = $data['country'];
        $g_view['err']['home_email']        = "Please specify the home email";
        $g_view['err']['work_email']        = "Please specify the work email";
        $g_view['err']['division']          = "Please specify the division"; 
        $g_view['err']['designation']          = "Please specify your designation"; 
        $g_view['err']['password']          = "Please specify the password "; 
        $g_view['err']['re_password']          = "Please specify the retype password"; 
        $g_view['err']['type']          = "Please specify the membership type"; 
        //$g_view['err']['last_name']           = "Please specify the work email";
}

require("content_view.php");
?>