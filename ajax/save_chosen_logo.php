<?php
include("../include/global.php");
//////////////////////////////////////////////
/*******************
sng:23/feb/2012
We now store the filename of the preferred logo for a deal
We change the table name also
***********************/
$tableName  = TP.'preferred_logos';



if (!isset($_SESSION['mem_id'])) {
    echo "You need to login in order to use this feature.";
    exit(0);
}

$logos = array();
$oldLogos = array();
$q = "SELECT logos FROM {$tableName} WHERE mem_id = {$_SESSION['mem_id']} LIMIT 1";
$res = mysql_query($q);
if ($res) {
    $savedLogos = mysql_fetch_assoc($res);
}

$logos[$_GET['deal_id']] = $_GET['logo_file'];
if (is_array($savedLogos)) {
    $oldLogos = unserialize($savedLogos['logos']);
}

    foreach ($logos as $key=>$value) {
            $oldLogos[$key] = $logos[$key];
    }
    if (isset($_GET['dbg'])) {
        echo "<pre>";
        var_dump($oldLogos);
   
    }

$logos = serialize($oldLogos);   
$q = "INSERT INTO {$tableName} (mem_id, logos ) VALUES ({$_SESSION['mem_id']},'{$logos}') ON DUPLICATE KEY UPDATE logos='$logos'";
//$q = "UPDATE {$tableName} SET logos = '{$logos}' WHERE mem_id = {$_SESSION['mem_id']}";
mysql_query($q) or die(mysql_error());


?>

