<?php
include("../include/global.php");  

$q = sprintf("select code,name from ".TP."currency_master where code like '%%%s%%' LIMIT 5", mysql_real_escape_string($_GET['term']));

$res  = mysql_query($q);
while ($row = mysql_fetch_assoc($res)) {
    $results[] = $row;
}
$newResult = array();
if (sizeOf($results)) {
    foreach($results as $result) {
       $newResult[] = array('label' => $result['code'],'name' => $result['name']);
    }    
}

echo json_encode($newResult);
?>
