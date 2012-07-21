<?php
ini_set('display_errors', 0);

include("../include/global.php");
require_once("../classes/class.savedSearches.php");
require_once("../classes/class.account.php");
//////////////////////////////////////////////

if (!isset($_SESSION['mem_id'])) {
    echo "You need to login in order to use this feature.";
    exit(0);
}

switch ($_GET['action']) {
    case 'setLeagueTableNotificationAlertState' :
        setLeagueTableNotificationAlertState();
        break;
    case 'shareSearch':
        shareSearch();
        break;
    
}

function shareSearch()
{
    $savedSearches = new SavedSearches();
    $emails = explode(",",$_POST['emailAdresses']);
    $savedSearchToken = base64_encode($_POST['savedSearch']);
    $searchDetails = $savedSearches->getById($_POST['savedSearch']);
    $searchDescription = $savedSearches->cleanAndTranslate($searchDetails['parameters']);
    $searchType = $_POST['savedSearchType'];
    $extra = $_POST['extraInfo'];
    /********
    sng:5/aug/2011
    index.php now perform the job of league_table.com
	
	sng:21/jul/2012
	Now we have our own league table page
    **************/
    $links = array(
        'deal'=>'http://www.deal-data.com/deal_search.php?token='.$savedSearchToken,
        'tombstone'=>'http://www.deal-data.com/showcase_firm.php?id='. $_SESSION['company_id'] . '&from=savedSearches&token='.$savedSearchToken,
        'league'=>'http://www.deal-data.com/league_table.php?token='.$savedSearchToken,
        'leagueDetail'=>'http://www.deal-data.com/league_table_detail.php?token='.$savedSearchToken,
        'volumes'=>'http://www.deal-data.com/issuance_data.php?token='.$savedSearchToken,
        'volumesDetail'=>'http://www.deal-data.com/issuance_data_detail.php?token='.$savedSearchToken,
    );
    $template = file_get_contents('./shareSearchEmailTemplate.html') or print("Cannot open template");
    $template = str_replace(
                    array(  '#senderName#',
                            '#searchDescription#',
                            '#link#',
                            '#message#'
                    ),
                    array($_SESSION['f_name'] ." " . strtoupper($_SESSION['l_name']),
                        "<a href={$links[$searchType]} target='_blank' title='$searchDescription' > $searchDescription</a>",
                        $links[$searchType],
                        $extra
                    ),$template
    );

    $err = array();
    foreach ($emails as $email) {
        if (!preg_match('#\b[\w\.-]+@[\w\.-]+\.\w{2,4}\b#',trim($email),$matches)) {
            $err[] = "$email is not a valid e-mail address.";
            continue;
        }
        sendHTMLemail($template, 'no-reply@deal-data.com', $email, $searchDescription . " search from " . $_SESSION['f_name'] ." " . strtoupper($_SESSION['l_name']));
    }

    if (sizeOf($err)) {
        echo implode("<br />",$err);
        exit(0);
    }
    echo "You have successfuly shared your search";    
}

function setLeagueTableNotificationAlertState()
{
    $savedSearches = new SavedSearches();
    $state = $_GET['state'];
    $notificationId = $_GET['notificationId'];
    
    if ($savedSearches->setLeagueTableNotificationAlertState($notificationId, $state, $_SESSION['mem_id'])) {
        echo "Saved.";
    } else {
        echo "Failed to save.";
    }
}

?>
