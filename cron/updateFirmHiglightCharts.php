<?php

include(dirname(dirname(__FILE__))."/include/global.php"); 

require_once(dirname(dirname(__FILE__))."/classes/class.company.php");
require_once(dirname(dirname(__FILE__))."/classes/class.transaction.php");
require_once(dirname(dirname(__FILE__))."/classes/class.account.php");
require_once(dirname(dirname(__FILE__))."/classes/class.country.php");
require_once(dirname(dirname(__FILE__))."/classes/class.account.php");
require_once(dirname(dirname(__FILE__))."/classes/class.savedSearches.php");
require_once(dirname(dirname(__FILE__))."/classes/class.leagueTableChart.php");

$logMsg_header = str_repeat('-', 20) . ' '. date('d/m/Y H:i:s') . ' ' . str_repeat('-', 20);
header("Content-type: text/plain");
mLog($logMsg_header);
$selectQ = 'SELECT * FROM __TP__charts';

if (!$res = query($selectQ)) {
    mLog('Cannot run query.');
    mLog(str_repeat('-', strlen($logMsg_header)));
    exit();
}

while ($row = mysql_fetch_assoc($res)) {
    $updateQ = 'UPDATE __TP__charts 
                SET img = "%s",
                    containerId = "%s",
                    generated_on = "%s"
                 WHERE id = %d';

    mLog(sprintf('Now updating chart %s', $row['id']));
    $createdOn = date('Y-m-d H:i:s');
    $containerId = md5($row['name'] . $today);
    $chart = new leagueTableChart(unserialize($row['params']));
    $chart->setName($containerId);
    $markup = base64_encode($chart->getHtml(true));
    $updateQ = sprintf($updateQ, $markup, $containerId, $createdOn, $row['id']);

    if (!$res2 = query($updateQ)) {
        mLog('Cannot run update query.');
        continue;
    }
    mLog('Updated to new params.');
    mLog(str_repeat('*', strlen($logMsg_header)));
}

mLog(str_repeat('-', strlen($logMsg_header)));



function mLog($msg) {
    echo $msg . PHP_EOL;
}