<?php
include("../include/global.php");  
require_once("../classes/class.company.php");
if (isset($_GET['for'])) {
    switch ($_GET['for']) {
        case 'advisors' :
            if (!isset($_GET['type'])) {
                $_GET['type'] = '1';
            }            
            
            switch ($_GET['type']) {
                case '1':
                    $firmType = 'bank'; 
                break;
                case '2':
                    $firmType = 'law firm';
                break;
				/*******************
				sng:18/jan/2012
				DIRTY HACK
				to support getting the company name only in simple deal submission
				******************/
				case '3':
                    $firmType = 'company';
                break;
                default:
                   $firmType = 'bank'; 
                break;
            }
            $q = "SELECT name FROM %s WHERE type ='$firmType' AND name LIKE '%s' LIMIT 5" ;
            $q = sprintf($q, TP . 'company', '%' . mysql_escape_string($_GET['term']) . '%');
            $results = array();
            if (!$rez = mysql_query($q)) {
                echo '';//json_encode(array(array('label' => 'No suggestions were found.')));
                return;
            }
            while ($row = mysql_fetch_assoc($rez)) {
                $results[] = array('label' => $row['name'], 'searchedFor' => urldecode( $_GET['term']));
            }
            if (!sizeof($results)) {
                echo '';//json_encode(array(array('label' => 'No suggestions were found.')));
                return; 
            }            
            echo json_encode($results);
        break;
    }
    exit(0);
}
/*****
sng:10/jun/2011
we are also getting the country of the hq

sng:20/oct/2011
We are calling this part only ion suggest_a_deal_view.php and that too to get names for
seller company
target company
buyer company
parent company

We can filter the search to get entries for 'company' type only
******/
$q = sprintf("select company_id,name,type,sector,industry,hq_country from ".TP."company where type='company' and name like '%%%s%%' LIMIT 5", mysql_escape_string($_GET['term']));

$res  = mysql_query($q);
while ($row = mysql_fetch_assoc($res)) {
    $results[] = $row;
}
$newResult = array();
if (sizeOf($results)) {
    foreach($results as $result) {
       $newResult[] = array('id' => $result['company_id'],'label' => $result['name'], 'sector'=> $result['sector'], 'industry' => $result['industry'],'hq_country' =>$result['hq_country']);
    }    
}

echo json_encode($newResult);
//var_dump($g_company->filter_company_name_list_by_type_name('name','Vodafone',false);
?>
