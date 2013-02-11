<?php
/**********************
sng:27/oct/2012

Minimum files needed to run a script.
This is used by cron codes who does not require session and run in background mode

NOT FOR NORMAL FILES

sng:22/jan/2013
We now use the improved classes/class.db.php which use mysqli and use the link identifier

Also, we will now call this file to load everything, including the config file

It is a good practise to set the file path and use absolute path to include another file. It is quick and explicit, no need for hunting

Note on dirname(__FILE__)
Even if we call this from test/src/t.php, the value is D:\wamp\www\new_deal_data\include
which is location of this file (never mind from where this is included). Therefore, we can easily set the path.
**************************/
define('FILE_PATH',dirname(dirname(__FILE__)));

require_once(FILE_PATH."/include/config.php");
require_once(FILE_PATH."/classes/class.db.php");
require_once(FILE_PATH."/classes/class.debug.php");
/********
We do not create any db object here. Everybody should crate their own object
**********/
?>