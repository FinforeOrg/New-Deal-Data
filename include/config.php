<?php
/***************
sng:26/oct/2012

We put the path definitions here

Note on dirname(__FILE__)
Even if we call this from test/src/t.php, the value is D:\wamp\www\new_deal_data\include
which is location of this file (never mind from where this is included). Therefore, we can easily set the path.
******************/
$g_config = array();

$g_config['db_name'] = "mytombstones";
$g_config['db_host'] = "localhost";
$g_config['db_user'] = "root";
$g_config['db_password'] = "";

define('TP','tombstone_');
define('FILE_PATH',dirname(dirname(__FILE__)));
?>