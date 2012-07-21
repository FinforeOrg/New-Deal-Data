<?php
@session_start();
//error_reporting(E_ALL);
//ini_set('display_errors',true);


include("include/global.php");
require_once("classes/class.savedSearches.php");
require_once("classes/class.account.php");
//////////////////////////////////////////////
$savedSearches = new SavedSearches();
/************
sng:21/jul/2012
if accessing via ajax, we check for login an if not, send json message.
if accessing directly, we redirect to login page.
Since redirection will break ajax, we do not put redirection code here, but we put it after the ajax condition block
******************/
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
        case "saveSearch" : 
            if (!$savedSearches->currentUserCanStillAdd($_REQUEST['type'], $_REQUEST['alert'])) {
                echo json_encode(array('message'=>"You have reached the maximum number of searches you can save"));
                return;
            }
            if (!$insertedId = $savedSearches->addNew($_SESSION['mem_id'],$_POST, $_REQUEST['type'],$_REQUEST['alert']))
                echo json_encode(array('message'=>"You have reached the maximum number of searches you can save"));
            else {
               switch ($_REQUEST['type']) {
                   case 'tombstone' :
                    $link = "showcase_firm.php?id=".$_SESSION['company_id']."&from=savedSearches&token=" . base64_encode($insertedId);
                   break;
                   case 'deal' :
                    $link = "deal_search.php?token=".base64_encode($insertedId).$extra;
                   break;
                   case 'league' :
				   /**********
				   sng:5/aug/2011
				   since the index.php is showing the league table chart, let us send there
				   
				   sng:21/jul/2012
				   now we have our own league table page so let us send there
				   ****/
                    $link = "league_table.php?token=" . base64_encode($insertedId).$extra;
                   break;
                   case 'leagueDetail' :
                    $link = "league_table_detail.php?token=" . base64_encode($insertedId).$extra;
                   break;
                   case 'volumes' :
                    $link = "issuance_data.php?token=" . base64_encode($insertedId).$extra;
                   break;                   
                   case 'volumesDetail' :
                    $link = "issuance_data_detail.php?token=" . base64_encode($insertedId).$extra;
                   break;
               }
            }
                echo json_encode(array('message'=>"Your $label has been saved", "newLocation"=>"$link"));
        break;
        case "updateSearch" :
			/********************
			sng:10/nov/2011
			If updating alert, strip out the year part
			*****************/
			if(isset($_REQUEST['alert'])&&($_REQUEST['alert']==1)){
				$_POST['year'] = "";
			}
            if  ($savedSearches->updateSearch($_REQUEST['id'],serialize($_POST),$_REQUEST['alert']))
                echo json_encode(array('message'=>"Your $label has been updated"));
            else
                echo json_encode(array('message'=>"Your $label could not be updated. Please try again in a couple of minutes"));
        break;
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
        case "updateFavoriteStatus" :
        $fav = strpos($_POST['currentStatus'],"images/favorite.png") !== false ? "fav" : "not-fav";
        switch ($fav) {
            case 'fav' :
                $savedSearches->deleteFavoriteTombstone($_POST['id']);
                echo "images/not-favorite.png";
            break;
            case 'not-fav' : 
                $c = $savedSearches->addFavoriteTombstone($_POST['id']);
                echo "images/favorite.png";
            break;
        } 
        break;
        
    }
    exit(0);    
} 

/*******************
sng:21/jul/2012
If it comes here, it means direct access, so we check and redirect
*******************/
$_SESSION['after_login'] = "saved_searches.php";
require_once("check_mem_login.php");
    
$mySavedSearches = array('tombstone'=>false,"league"=>false, 'deal' => false, 'leagueDetail'=>array(), 'volumesDetail');
if(!$g_account->is_site_member_logged()){
   $err  = "You need to <a href='index.php' >Sign in</a> order to view this page";
}
    
if ($_SESSION['mem_id']) {
    $mySavedSearches = $savedSearches->getForUser($_SESSION['mem_id']);
    $mySavedAlerts = $savedSearches->getAlertForUser($_SESSION['mem_id']);
}

/////////////////////////////////////////////////////////////////////////////////
$g_view['page_heading'] = "My saved searches";
$g_view['show_help'] = true;
$g_view['content_view'] = "saved_searches_view.php";
require("content_view.php");


?>
