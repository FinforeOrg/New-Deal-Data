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
            'deal'=>'http://www.deal-data.com/deal_search.php?alert=1&token=',
        );
        $success = $g_trans->front_deal_search_paged(unserialize($numAlert['parameters']),0,999999,$deals,$dealCount, $numAlert['lastAlertId']);
       
        //var_dump($deals);
        if (sizeOf($deals)) {
            foreach ($deals as $deal) {
                $dealIds[] = $deal['deal_id'];
            }
            $dealArray = $g_trans->get_tombstone_from_deal_ids($dealIds); 
        }
        
        
        echo "Processing saved search with the id : " .$numAlert['id'] . PHP_EOL;         
    
        if (sizeOf($dealArray)) {
            $label = $savedSearches->cleanAndTranslate($numAlert['parameters']);
         $content = "
         
         
         <table style='margin: 0 auto;'>
            <tr>
                <td align='center' valign='top'>
                <table width='100%' border='0'>
                <tr>
                <td align='center' valign='top'>
                <table width='100%' border='0'>
                  <tr>
                    <td><img height='65' width='236' alt='' src='http://www.deal-data.com/images/mytombstones_logo.gif'></td>
                  </tr>
                  <tr>
                    <td>
                        <div style='font:14px Arial,sans-serif;'> Hello {$numAlert['l_name']}, <br  /><br  /> {$dealCount} new deals have been added in the '{$label}' section of <a href='http://www.deal-data.com/' > deal-data.com </a> . <br /><br />
                           Below you can find the list of added transactions.
                        </div>
                    </td>
                  </tr>
                </table></td>
                </tr>
                </table>                 
                
                 ";
            foreach ($dealArray as $deal) {
            //var_dump($deal); 
            if (strlen($deal['logo']) && is_file($filename = dirname(dirname(__FILE__)) . "/uploaded_img/logo/thumbnails/" . $deal['logo'])) {
            $dealLogo =  "<a style='text-decoration: none; cursor: pointer; display:block; text-align:center' href='{$link}'>
                <img src='http://deal-data.com/uploaded_img/logo/thumbnails/{$deal['logo']}' style='border: 0 none;' align='center'>                    </a>
            "; 
                //echo 'Logo found ' . $filename . "\n";               
            } else {
                $dealLogo = "<a style='text-decoration: none; cursor: pointer; display:block; text-align:center;color: #E86200; outline: medium none;    font-size: 14px;  font-weight: bold;' href='{$link}'>
                        {$deal['company_name']}            </a>
            ";
            //echo 'Logo not found ' . $filename . "\n";
            }
            
            $link = 'http://www.deal-data.com/hitCount.php?referer=savedSearch&token='.base64_encode('deal_detail.php?deal_id=' . $deal['deal_id']);
            $content .= "
<table class='tombstone_display' style='border: 1px solid #CCCCCC; width: 210px;  text-decoration: none; float: left; margin-left: 10px; margin-bottom:5px;'> 
    <tbody>
        <tr>
            <td id='logo-{$deal['deal_id']}?>' style='cursor: pointer; text-align: center;color: #3B3B3B; font: 11px/18px Tahoma,Geneva,sans-serif;text-align: left; height:210px' onclick='goto_deal_detail(17363)' class='tombstone_company'>
                $dealLogo
            </td>
        </tr>
        <tr>
            <td align='center' style='width: 40px; text-align: center;center;color: #3B3B3B; font: 11px/18px Tahoma,Geneva,sans-serif;text-align: left;'>
            &nbsp;
            </td>
        </tr>
        <tr>
            <td style=' width: 210px; height: 110px;' class='tombstone_deal'>           
             <a href='{$link}' style='display: block; width: 100%;height: 100%;cursor: pointer;center;color: #3B3B3B; font: 11px/18px Tahoma,Geneva,sans-serif;text-align: left; color: #000000; font-family: Verdana,Arial,Helvetica,sans-serif; font-size: 14px; font-weight: bold; height: 110px; padding: 5px; text-align: center; vertical-align: middle; text-decoration: none;'>  {$deal['deal_cat_name']}
            ";
            if ($deal['deal_subcat2_name'] != 'n/a') 
                $content .=  $deal['deal_subcat2_name'];
             else 
                $content .=  $deal['deal_subcat1_name'];
            if ($deal['value_in_billion'] != 0) {
                 $content .= "<br><br>
                US \$ ";
                
                $content .= number_format($deal['value_in_billion'] * 1000, 0 );
                 $content .= " million
                <br><br>
                ";               
            } else {
                $content .= "<br><br>
                Not disclosed
                <br><br>
                ";
            }
            $content .= date('M Y', strtotime($deal['date_of_deal'])) . "
            </a></td>
        </tr>
    </tbody>
</table>        
";
          } 
        }
              $content .= " </td>
            </tr>
        
        </table> ";        
        if ($dealCount) {
            $lastIdForDeal = getLatestDealId($deals);
            echo "Last alert id for deal for saved alert {$numAlert['id']} : {$numAlert['lastAlertId']} \n";
            $template = file_get_contents(dirname(dirname(__FILE__))."/emailTemplates/searchAlert.html");
            $what = $numAlert['search_type'] == 'league' ? 'chart' : 'list';
            
            if (strlen($numAlert['work_email'])) {
                echo "Current deal id for alerts :  " . $numAlert['lastAlertId'].PHP_EOL;
                $q = "UPDATE {$savedSearchesTable} SET lastAlertId = $lastIdForDeal WHERE id = {$numAlert['id']}";    
                 echo "Updating saved alert {$numAlert['id']} last id to  : $lastIdForDeal \n";                                  
                 $result = mysql_query($q) or die(mysql_error());
                // echo    $content; exit;
                $mailSent = sendHTMLemail($content,'no-reply@deal-data.com',$numAlert['work_email'], 'New deals added ('.($savedSearches->cleanAndTranslate($numAlert['parameters']). ")") );
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

