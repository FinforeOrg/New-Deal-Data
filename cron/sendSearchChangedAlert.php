<?php
include(dirname(dirname(__FILE__))."/include/global.php"); 

require_once(dirname(dirname(__FILE__))."/classes/class.company.php");
require_once(dirname(dirname(__FILE__))."/classes/class.transaction.php");
require_once(dirname(dirname(__FILE__))."/classes/class.account.php");
require_once(dirname(dirname(__FILE__))."/classes/class.country.php");
require_once(dirname(dirname(__FILE__))."/classes/class.account.php");
require_once(dirname(dirname(__FILE__))."/classes/class.savedSearches.php");
require_once(dirname(dirname(__FILE__))."/classes/class.mailer.php");

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
	/**************
	sng:8/sep/2012
	*****************/
	$email_data = array();
	
	
        $links = array(
            'deal'=>$g_http_path.'/deal_search.php?alert=1&token=',
        );
        $success = $g_trans->front_deal_search_paged(unserialize($numAlert['parameters']),0,999999,$deals,$dealCount, $numAlert['lastAlertId']);
		
		$dealArray = array();
        //var_dump($deals);
        if (sizeOf($deals)) {
            foreach ($deals as $deal) {
                $dealIds[] = $deal['deal_id'];
            }
            //$dealArray = $g_trans->get_tombstone_from_deal_ids($dealIds);
			$dealArray = $g_trans->get_tombstone_from_deal_ids_for_alert($dealIds); 
        }
        
        
        echo "Processing saved search with the id : " .$numAlert['id'] . PHP_EOL;         
    
        
           
			
			/**********
			sng:8/sep/2012
			*************/
			if (sizeOf($dealArray)) {
				 $label = $savedSearches->cleanAndTranslate($numAlert['parameters']);
			}else{
				$label = "";
			}
			$email_data['l_name'] = $numAlert['l_name'];
			$email_data['dealCount'] = $dealCount;
			$email_data['label'] = $label;
			$email_data['dealArray'] = $dealArray;
			
			$mailer = new mailer();
			$content = $mailer->mail_from_template("emailTemplates/saved_deal_alert_notification.php",$email_data);
			/****echo $content; exit;*****/
		
                 
        if ($dealCount) {
            $lastIdForDeal = getLatestDealId($deals);
            echo "Last alert id for deal for saved alert {$numAlert['id']} : {$numAlert['lastAlertId']} \n";
            /***********
			sng:6/dec/2012
			We are not using the emailTemplates/searchalert.html here
			************/
            $what = $numAlert['search_type'] == 'league' ? 'chart' : 'list';
            
            if (strlen($numAlert['work_email'])) {
                echo "Current deal id for alerts :  " . $numAlert['lastAlertId'].PHP_EOL;
                $q = "UPDATE {$savedSearchesTable} SET lastAlertId = $lastIdForDeal WHERE id = {$numAlert['id']}";    
                 echo "Updating saved alert {$numAlert['id']} last id to  : $lastIdForDeal \n";                                  
                 $result = mysql_query($q) or die(mysql_error());
                 /*******
				 sng:7/dec/2012
				 Let us use the mailer class. Sending email directly create issue
				 *********/
				 $from_email = "no-reply@deal-data.com";
				$mailSent = $mailer->html_mail($numAlert['work_email'],'New deals added ('.($savedSearches->cleanAndTranslate($numAlert['parameters'])). ")",$content,$from_email);
                 
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

