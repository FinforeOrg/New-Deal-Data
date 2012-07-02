<?php
include("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.company.php");
require_once("classes/class.magic_quote.php");

///////////////////////////////////////////////////////
$g_view['msg'] = "";
$g_view['content_view'] = "admin/pr_view.php";

////////////////////////////////////////////

$g_view['heading'] = "PR Section Administration";
$prTable = TP.'press_releases';
$prTagsTable = TP.'press_releases_tags';
$settingsTable = TP.'pr_settings';

switch ($_REQUEST['action']) {
    case "pageSettings" :
        $g_view['content_view'] = "admin/pr_view_pageSettings.php";
        if (isset($_POST['submit'])) {
            $q = "UPDATE {$settingsTable} SET number_of_twitts = %d";
            $q  = sprintf($q,mysql_escape_string($_POST['number_of_twitts']));
            if (mysql_query($q)) {
                $g_view['msg'] = "Settings saved.";
            } else {
                $g_view['msg'] = "Settings could not be saved.";
            }
        }
        $settingsQuery = "SELECT * FROM {$settingsTable} WHERE TRUE LIMIT 1";
        $settingsRes = mysql_query($settingsQuery) or die(mysql_error());
        $settings = mysql_fetch_assoc($settingsRes);
    break;
    case "addPressReleases" :
        $g_view['content_view'] = "admin/pr_view_addPressReleases.php";
        $g_view['heading'] = "Add new press releases";
        if (isset($_POST['submit'])) {
            $text = strip_tags($_POST['presReleaseText'], "<a><b><strong><i><em><span>");
			/****************************
			sng:13/nov/2010
			allow admin to enter a deal number, if this press release talks about a deal and the db has that deal data
  			admin search from the front end and go to the deal detail page to get the deal number
			***/
			$deal_id = strip_tags($_POST['deal_id']);
            $q = "INSERT INTO {$prTable} (text,date,deal_id) VALUES ('%s', '%s','%s')";
            $res = mysql_query($fullQ = sprintf($q,mysql_escape_string($text), date('Y-m-d H:i:s',strtotime($_POST['date'])),$deal_id));
			/***********************/
            if ($res) {
                $id  = mysql_insert_id();
                $tags = explode(",", $_POST['tags']);
                foreach ($tags as $tag) {
                    $q = "INSERT INTO {$prTagsTable} (press_release_id, tag) VALUES (%d, '%s')";
                    $q = sprintf($q, $id, trim($tag));
                    mysql_query($q);
                }  
                $g_view['msg'] = "Press release added.";         
            } else {
                $g_view['msg'] = "The press release could not be added. Plese try again later";
            }
        }
    break;
   case "managePressReleases" : 
       if (isset($_GET['subaction'])) {
            switch ($_GET['subaction']) {
               case "delete":
                $q = "DELETE FROM {$prTable} WHERE id = %d";
                $q = sprintf($q, $_GET['id']);
                if (mysql_query($q)) {
                    $q = "DELETE FROM {$prTagsTable} WHERE press_release_id = %d";
                    $q = sprintf($q, $_GET['id']);
                    mysql_query($q);
                    $g_view['msg'] = "Press release deleted.";
                } else {
                    $g_view['msg'] = "Press release could not be deleted.";
                }
               break; 
            }
        }
        $g_view['content_view'] = "admin/pr_view_managePressReleases.php";
        $g_view['heading'] = "Add new press releases";
        $where = '';
        if (isset($_POST['query'])) {
            $where = " AND text like '%" . mysql_escape_string($_POST['query']) . "%'";
        }

        $q = "SELECT * FROM {$prTable} WHERE TRUE $where";

        $pressReleases  = array();
        $res = mysql_query($q);
        while($row = mysql_fetch_assoc($res)) {
          $pressReleases[] = $row;
        }  
   break;
   case "editPressRelease" :
    $g_view['content_view'] = "admin/pr_view_editPressRelease.php";
    $g_view['heading'] = "Edit Press Release";
    $id = (int) $_GET['id'];
    if (isset($_POST['submit'])) {
        $text = strip_tags($_POST['presReleaseText'], "<a><b><strong><i><em><span>");
        $tags = $_POST['tags'];
		/****************************
			sng:13/nov/2010
			allow admin to enter a deal number, if this press release talks about a deal and the db has that deal data
  			admin search from the front end and go to the deal detail page to get the deal number
			***/
		$deal_id = strip_tags($_POST['deal_id']);
        $q = "UPDATE {$prTable} SET text = '%s', date = '%s', deal_id='%s' WHERE id=$id";
        $res = mysql_query($fullQ = sprintf($q,mysql_escape_string($text), date('Y-m-d H:i:s', strtotime($_POST['date'])),$deal_id));
        if ($res) {
            mysql_query("DELETE FROM {$prTagsTable} WHERE press_release_id = $id");
            $tags = explode(",", $tags);
            foreach ($tags as $tag) {
                $q = "INSERT INTO {$prTagsTable} (press_release_id, tag) VALUES (%d, '%s')";
                $q = sprintf($q, $id, trim($tag));
                mysql_query($q);
            }  
            $g_view['msg'] = "Press release updated.";         
        } else {
            $g_view['msg'] = "The press release could not be updated. Plese try again later";
        }
    }
    $q = "SELECT * FROM {$prTable} WHERE id = %d";
    $pressRelease = mysql_fetch_assoc(mysql_query(sprintf($q,$id))) or die(mysql_error());
    $tags = array();
        $tagQ = "SELECT tag FROM {$prTagsTable} WHERE press_release_id = %d";
        $res = mysql_query(sprintf($tagQ,$id));
        while($row = mysql_fetch_assoc($res)) {
           $tags[] =  $row['tag'];
        }
        $tags = implode(",",$tags);
   break;
   
}
////////////////////////////////////////////////////////
include("admin/content_view.php"); 
?>
