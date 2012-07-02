<?php
include("../include/global.php");  

$q = sprintf("select name from ".TP."stock_exchange_master where name like '%%%s%%' LIMIT 5", mysql_real_escape_string($_GET['term']));

$res  = mysql_query($q);
while ($row = mysql_fetch_assoc($res)) {
    $results[] = $row;
}
$newResult = array();
if (sizeOf($results)) {
    foreach($results as $result) {
       $newResult[] = array('label' => $result['name']);
    }    
}

echo json_encode($newResult);
?>
