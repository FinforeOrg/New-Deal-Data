<?php

/**
* 04-05-2011 
* imihai
* Updated $g_http_path (www does not seem to work. TODO: add redirection in .htaccess)
*/
/***********
global.php
created by sng on 17/march/2010
Include this file at top of any php file that is called by browser.
This contains all the common definitions.
***********/
@session_start();
/***
sng:21/oct/2010
change the session id in each request so that even if someone else get holds of my session id,
hopefully he cannot impersonate me, because by that time, I have requested another page
and session id got changed
***/
//session_regenerate_id(true);
defined('FILE_PATH') or define("FILE_PATH",dirname(dirname(__FILE__)));

defined('DD_PATH') or define('DD_PATH',"/var/www/");
ini_set("include_path",FILE_PATH. PATH_SEPARATOR . DD_PATH);
/////////////////////////////////////
$db_name ="mytombstones";       
$db_host ="localhost";
$db_user ="root";
$db_password ="SunNov27";

/**
 * This are used for logging purposes. Please set to WARNING, false when in production
 */
$logFile = dirname(dirname(__FILE__)) . '/logs/deal-data.com.log';
$logLevel = "WARNING";
$dbProfiler = false;
require_once(dirname(dirname(__FILE__)) . "/classes/class.Log.php");
Log::enable();

$conn = mysql_connect($db_host, $db_user, $db_password) or die("Cannot connect to the Host");
mysql_select_db($db_name, $conn) or die("Cannot connect to the Database !!");

define('TP','tombstone_');
//without trailing slash
$g_http_path = "http://data-cx.com";
$g_view = array();

/**********************************
sng:15/nov/2011
Let us define a url path for logos and profile images

sng:22/nov/2011
Let us define a file path for case study. It will point to the case study path for deal-data

sng:02/dec/2011
Let us define a file path for uploading logo image
*************************************/
defined('LOGO_IMG_URL') or define('LOGO_IMG_URL','http://data-cx.com/uploaded_img/logo/thumbnails');
defined('CASE_STUDY_PATH') or define('CASE_STUDY_PATH',DD_PATH."case_studies");
defined('LOGO_PATH') or define('LOGO_PATH',DD_PATH."uploaded_img/logo");

/****
sng:10/may/2010
We need to check if the site is flagged as down for maintenance or not. If so, do not proceed further
if this is being viewed by front users.

However, admin and sa should be able to work
*******/
require_once("site_under_maintenance.php");
require_once("nifty_functions.php");
/**************
sng:3/jun/2010
A quick and dirty implementation of contact us, we will use mailto
so we need the contact email
**********/
require_once("classes/class.sitesetup.php");
$g_view['site_emails'] = NULL;
$g_success = $g_site->get_site_emails($g_view['site_emails']);
if(!$g_success){
    die("Cannot get site emails");
}
////////////////////////////////////////////
/*************
sng:22/oct/2010
if this is not included by admin or sa, run the include/sanitize_input.php
That file basically removes all html content from user submitted data.
After all, in this site, all user submitted data will be plain text.
I am placing this in the global portion so that any code written will benefit. If this slow down the response,
we will have to put it page by page.
Of course, if you have better idea, you are welcome to implement that, since security can be tricky
*********/
if((basename(dirname($_SERVER['PHP_SELF']))!="admin")&&(basename(dirname($_SERVER['PHP_SELF']))!="sa")){
    require_once("include/sanitize_input.php");
}

if (!function_exists('query')) {
    /**
     * Wrapper function used to avoid using TP constant all across the code. USE __TP__ string instead and call 
     * this function. This will allow future implementation of logs and other features.
     * 
     * @param string $q
     * @return mixed 
     */
    function query($q) {
        global $dbProfiler;
        
        $query = str_replace('__TP__', TP, $q);
        
        if ($dbProfiler) {
            Log::profiler($query, __FILE__, __LINE__);
        }
        
        return mysql_query($query);

    }    
}

$utilClass = dirname(dirname(__FILE__)) . '/classes/class.Util.php';
if (file_exists($utilClass)) {
    require_once dirname(dirname(__FILE__)) . '/classes/class.Util.php';
}

require_once("classes/db.php");
require_once("include/default_metatags.php");
?>
