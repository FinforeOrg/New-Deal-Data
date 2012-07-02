<?php
include(dirname(dirname(__FILE__)) . '/include/global.php');
$hasSync = false;
if (isset($_POST['submit'])) {
    switch ($_POST['submit']) {
        case 'Syncronize now' :               
            $cmd = "rsync -rav --exclude-from='/var/www/home-checkout/exclude-files.txt' /var/www/home-checkout/trunk/ /var/www/home/ -O --delete";
            $cmdOutput = 'Syncronizing deal-data.com @ '  . date('d/m/Y H:i:s') . PHP_EOL;
            $cmdOutput .= shell_exec($cmd);
            
            file_put_contents('/var/www/home-checkout/export2main_sync.log', $cmdOutput, FILE_APPEND);
            file_put_contents('/var/www/home-checkout/home_revision', $_POST['rev']);
            $hasSync = true;
        break;
        default:
        break;
    }
}
  ////////////////////////////////////////////////
$g_view['heading'] = "Codebase Syncronization";
$g_view['content_view'] = dirname(__FILE__) . "/code_syncronization_view.php";
include(dirname(__FILE__) . "/content_view.php");
