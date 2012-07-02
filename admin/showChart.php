<?php

if (!isset($_GET['chartid'])) {
    echo "<h3> INVALID REQUEST </h3>";exit();
}
require_once("../include/global.php");
require_once ("admin/checklogin.php");
require_once("classes/class.statistics.php");
require_once("classes/class.magic_quote.php");

$q = 'SELECT * FROM __TP__charts WHERE id = ' . (int) $_GET['chartid'];
if (!$res = query($q)) {
    echo "<h3>  Failed to run query </h3>";exit();
}
$result = mysql_fetch_assoc($res);

if (!$result) {
    echo "<h3>  There is no such chart </h3>";exit();
}
?>

<html>
<head>
<title>Admin Area</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<!--[if IE]><script language="javascript" type="text/javascript" src="/js/jqplot/excanvas.min.js"></script><![endif]-->
<script src="/js/jquery-1.4.4.min.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="/js/jqplot/jquery.jqplot.min.css" />
<link rel="stylesheet" type="text/css" href="/admin/style.css" />

<script language="javascript" type="text/javascript" src="/js/jqplot/jquery.jqplot.js"></script>
<script language="javascript" type="text/javascript" src="/js/jqplot/plugins/jqplot.barRenderer.min.js"></script>
<script language="javascript" type="text/javascript" src="/js/jqplot/plugins/jqplot.categoryAxisRenderer.min.js"></script>
<script language="javascript" type="text/javascript" src="/js/jqplot/plugins/jqplot.pointLabels.min.js"></script>   
</head>
<body style="background-color: #FFF">
 <div id ="<?php echo $result['containerId']?>" class="chart">
     <?php echo base64_decode($result['img'])?>
 </div>
    
</body>