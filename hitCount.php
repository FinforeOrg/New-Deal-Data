<?php
include(dirname(__FILE__)."/include/global.php"); 

require_once(dirname(__FILE__)."/classes/class.company.php");
require_once(dirname(__FILE__)."/classes/class.transaction.php");
require_once(dirname(__FILE__)."/classes/class.account.php");
require_once(dirname(__FILE__)."/classes/class.country.php");
require_once(dirname(__FILE__)."/classes/class.account.php");
require_once(dirname(__FILE__)."/classes/class.savedSearches.php");

$tableName = TP . 'hit_count';

$referer = $_REQUEST['referer'];
$redirectLink = base64_decode($_REQUEST['token']);

$q = "INSERT INTO $tableName (hits,referer, spare) VALUES (0 , '$referer', '')
  ON DUPLICATE KEY UPDATE hits=hits+1;
";
//echo $q;
mysql_query($q);
header("Location: $redirectLink");
?>
