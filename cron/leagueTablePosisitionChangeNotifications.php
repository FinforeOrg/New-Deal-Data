<?php
echo "<pre>";
include(dirname(dirname(__FILE__))."/include/global.php"); 

require_once(dirname(dirname(__FILE__))."/classes/class.company.php");
require_once(dirname(dirname(__FILE__))."/classes/class.transaction.php");
require_once(dirname(dirname(__FILE__))."/classes/class.account.php");
require_once(dirname(dirname(__FILE__))."/classes/class.country.php");
require_once(dirname(dirname(__FILE__))."/classes/class.account.php");
require_once(dirname(dirname(__FILE__))."/classes/class.savedSearches.php");

/********
sng:5/dec/2012
Let us use the mailer class. Sending email directly create issue
**************/
require_once("classes/class.mailer.php");
$mailer = new mailer();

$savedSearches  = new SavedSearches();
$qSavedSearches = 'SELECT * 
      FROM __TP__saved_searches 
      WHERE search_type = "leagueDetail"
      AND forAlert = 1
      AND currentRank > 0
      ORDER BY id DESC';

$qUserInfo = 'SELECT company_id, f_name, l_name, work_email, home_email
    FROM __TP__member 
    WHERE mem_id = %d';

$qSavedSearchesRes = query($qSavedSearches);
if (!$qSavedSearchesRes) {
    logMsg("Please check if the query used for selecting saved searches is correct");
    return;
}

while ($row = mysql_fetch_assoc($qSavedSearchesRes)) {
    $logMsg_header = str_repeat('-', 15) . ' Now processing alert ' . $row['id'] . ' ' . str_repeat('-', 15);
    logMsg($logMsg_header);
    $userRes = query(sprintf($qUserInfo, $row['member_id']));
    if (!$userRes) {
        //this should never happen
        logMsg("Cannot get user info");
        continue;
    }
    $userInfo = mysql_fetch_assoc($userRes);
    if (!(is_array($userInfo) && sizeOf($userInfo))) {
        logMsg("Cannot get user info. User id provided is invalid.");
        continue;
    }
    $currentRank = $row['currentRank'];
    /**
     * These are here only for testing purposes. remove once the testing has been done
     */
    //$rank = 11;
    //$row['last_alert_date'] = '2011-05-19';
    
    
    $rank = $savedSearches->getCompanyRank($userInfo['company_id'], $row);
    $nrTransactions  = $savedSearches->getTransactionsAddedAfter($row);
    $nrTransactions = (is_null($nrTransactions) ? 0 : sizeOf($nrTransactions));
        
    if ($currentRank == $rank) {
        logMsg('Rank hasn`t changed. Notification is not needed.');
        logMsg(str_repeat('-', strlen($logMsg_header)));
        continue;
    }
    
    if ($nrTransactions == 0) {
        logMsg('No transactions have been added. Notification is not needed');
        logMsg(str_repeat('-', strlen($logMsg_header)));
        continue;
    }
    if (!$rank) {
        logMsg("Company has now gone beyond 10th place.");
        
        $template = file_get_contents(dirname(dirname(__FILE__)) . '/emailTemplates/leagueTablePositionChangeNotTop10.html');
        
        $infos = array(
            'receiverName' => $userInfo['f_name'] . ' ' . $userInfo['l_name']
            , 'nrDeals' => $nrTransactions
            , 'searchType' => $savedSearches->cleanAndTranslate($row['parameters'])
            , 'token' => getToken($row)
        );
    } else {
         
        $template = file_get_contents(dirname(dirname(__FILE__)) . '/emailTemplates/leagueTablePositionChange.html'); 
        $ranksMoved = $currentRank - $rank;
        $infos = array(
            'receiverName' => $userInfo['f_name'] . ' ' . $userInfo['l_name']
            , 'nrDeals' =>  $nrTransactions
            , 'searchType' => $savedSearches->cleanAndTranslate($row['parameters'])
            , 'direction' => ( $ranksMoved < 0 ) ? 'down' : 'up'
            , 'nrPlaces' => $nrPlaces = ( $ranksMoved < 0 ) ? -$ranksMoved : $ranksMoved
            , 'token' => getToken($row)
            , 'plural' => $nrPlaces > 1 ? 's' : ''
        );
        logMsg("Company went {$infos['direction']} {$infos['nrPlaces']} place {$infos['plural']}. Sending notification");
    }
    foreach ($infos as $key => $value) {
        $template = str_replace("#$key#", $value, $template);
    }
    
    $mailAddress = is_null($userInfo['work_email']) ? $userInfo['home_email'] : $userInfo['work_email'];
    logMsg("Sending e-mail to $mailAddress.");
    $mailSent = sendMail($mailAddress, $template); 
    if (!$mailSent) {
        logMsg("Cannot send e-mail to $mailAddress. Continue with next search");
    } else {
        /**
         * Will be updated to today +1 in order to allow testing by running the cron manually.
         */
		/**************************
		sng:7/dec/2012
		Storing +1 day is creating trouble.
		Let us store the current date
		***************************/
        $updated = $savedSearches->updateTable(array('last_alert_date' => date('Y-m-d'), 'currentRank' => $rank), $row['id']);
        if ($updated) {
            logMsg('Updated last_alert_date to ' . date('Y-m-d'));
            
            $insertQuery = 'INSERT INTO __TP__saved_searches_history 
                (id, mem_id, parameters, start_date, end_date, places, old_rank, new_rank )
                VALUES (NULL, %d, \'%s\', \'%s\', \'%s\', %d, %d, %d)';
            $insertQuery = sprintf($insertQuery, $row['member_id'], $row['parameters'], $row['last_alert_date'], date('Y-m-d'), $ranksMoved, $currentRank, $rank);
            
            if (!query($insertQuery)) {
                logMsg('Adding to history FAILED');
            } else {
               logMsg('Adding to history SUCCEEDED'); 
            }
        } else {
            logMsg('Failed to update last_alert_date to ' . date('Y-m-d', strtotime("+1day"))); 
        }
    }
    logMsg(str_repeat('-', strlen($logMsg_header)));
}

/**
 *  There is something wrong with the sendHTMLemail function in nifty_functions.php
 * 
 * @param string $to
 * @param string $content
 * @return bool 
 */
function sendMail($to, $content)
{
	/************
	sng:5/dec/2012
	this sending email as it is is creating issue, so we are trying the mailer class
	
	$headers = "From: no-reply@deal-data.com\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";  
    
    return mail($to, $subject, $content, $headers);
	***************/
	global $mailer;
	
    $subject = 'Company ranking change';
	$from_email = "no-reply@deal-data.com";
	return $mailer->html_mail($to,$subject,$content,$from_email);

    
}

function logMsg($msg) 
{
    echo $msg . PHP_EOL;
}

function getToken($row) {
    $token = serialize(array('id' => $row['id'], 'date' => $row['last_alert_date']));
    return base64_encode($token);
    
}
?>