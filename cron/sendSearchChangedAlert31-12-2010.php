<?php
include(dirname(dirname(__FILE__))."/include/global.php"); 

require_once(dirname(dirname(__FILE__))."/classes/class.company.php");
require_once(dirname(dirname(__FILE__))."/classes/class.transaction.php");
require_once(dirname(dirname(__FILE__))."/classes/class.account.php");
require_once(dirname(dirname(__FILE__))."/classes/class.country.php");
require_once(dirname(dirname(__FILE__))."/classes/class.account.php");
require_once(dirname(dirname(__FILE__))."/classes/class.savedSearches.php");
$savedSearches = new SavedSearches();
$memberTable = TP . 'member';

$savedSearchesTable = TP . 'saved_searches';
$q = "SELECT ss.*, mt.l_name, mt.work_email, mt.company_id FROM {$savedSearchesTable} ss LEFT JOIN {$memberTable} mt ON ss.member_id = mt.mem_id WHERE receiveAlerts =  1";

$res = mysql_query($q) or die(mysql_error());
$ret =  array();
while ($row = mysql_fetch_assoc($res)) {
    $ret[] = $row;
}
echo "<pre>";
echo "Found " . sizeOf($ret) ." saved searches that require alerts." . PHP_EOL;
foreach($ret as $numAlert) {
        $links = array(
            'deal'=>'http://www.mytombstones.com/deal_search.php?alert=1&token=',
        );
        $success = $g_trans->front_deal_search_paged(unserialize($numAlert['parameters']),0,999999,$deals,$dealCount, $numAlert['lastAlertId']);
        
        echo "Processing saved search with the id : " .$numAlert['id'] . PHP_EOL;
        if ($dealCount) {
            $lastIdForDeal = getLatestDealId($deals);
            echo "Last alert id for deal for saved alert {$numAlert['id']} : {$numAlert['lastAlertId']} \n";
            $template = file_get_contents(dirname(dirname(__FILE__))."/emailTemplates/searchAlert.html");
            $what = $numAlert['search_type'] == 'league' ? 'chart' : 'list';
            $template = str_replace(
                array('#receiverName#','#nrDeals#', '#searchType#', '#link#', '#what#'),
                array($numAlert['l_name'], $dealCount, $savedSearches->cleanAndTranslate($numAlert['parameters']),$links[$numAlert['search_type']] . base64_encode($numAlert['id'])."&lid=".base64_encode($numAlert['lastAlertId']), $what),
                $template
            );
            
            if (strlen($numAlert['work_email'])) {
                echo "Current deal id for alerts :  " . $numAlert['lastAlertId'].PHP_EOL;
                $q = "UPDATE {$savedSearchesTable} SET lastAlertId = $lastIdForDeal WHERE id = {$numAlert['id']}";    
                 echo "Updating saved alert {$numAlert['id']} last id to  : $lastIdForDeal \n";                                  $result = mysql_query($q) or die(mysql_error());
                $mailSent = sendHTMLemail($template,'no-reply@mytombstones.com',$numAlert['work_email'], 'New deals added ('.($savedSearches->cleanAndTranslate($numAlert['parameters']). ")") );
                if ($mailSent)
                    echo "Email sent to " .$numAlert['work_email'] . PHP_EOL ;
            } else {
                echo "Email not sent (no home_email specified for user) " . PHP_EOL;
            } 
            
        } else {
            echo "No more deals found for current saved search since last alert  " . PHP_EOL;
        }
        
        echo "FINISHED SEARCH WITH ID  " .$numAlert['id'] . PHP_EOL;
        echo "======================================================" .PHP_EOL.PHP_EOL.PHP_EOL;
       
}

function getLatestDealId($deals) {
    $max = 0;
    if (is_array($deals) && sizeOf($deals)) {
        $max = $deals[0]['deal_id'];
        foreach ($deals as $deal ) {
            if ($deal['deal_id'] > $max) {
                $max = $deal['deal_id'];
            }
        }
        
    }
    return $max;
}
?>
