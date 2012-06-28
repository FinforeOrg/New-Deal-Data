<?php
/**********************
sng:11/nov/2011
Let us allow any logged in member to download a case study
********************/
include("include/global.php");
require_once("classes/class.account.php");
require_once("classes/class.transaction.php");
if(!$g_account->is_site_member_logged()){
	?>
	<html>
	<head>
	<title>Case Study</title>
	</head>
	<body>
	<h1>You need to be logged in to download a case study</h1>
	</body>
	</html>
	<?php
	exit;
}
//logged in, so get the data and check that the user indeed work in the firm who uploaded the case study
$q = "select partner_id,filename from ".TP."transaction_case_studies where case_study_id='".$_POST['case_study_id']."'";
$res = mysql_query($q) or die("Cannot get case study detail");
$cnt = mysql_num_rows($res);
if(0 == $cnt){
	?>
	<html>
	<head>
	<title>Case Study</title>
	</head>
	<body>
	<h1>The case study was not found</h1>
	</body>
	</html>
	<?php
	exit;
}
$row = mysql_fetch_assoc($res);
/***************************************************
if($_SESSION['company_id']!=$row['partner_id']){
	?>
	<html>
	<head>
	<title>Case Study</title>
	</head>
	<body>
	<h1>You can only download the case study uploaded by your firm</h1>
	</body>
	</html>
	<?php
	exit;
}
**************************************************/
//send the file
$file = CASE_STUDY_PATH."/".$row['filename'];
if(!is_file($file)){
	?>
	<html>
	<head>
	<title>Case Study</title>
	</head>
	<body>
	<h1>The case study file was not found</h1>
	</body>
	</html>
	<?php
	exit;
}
// set headers
header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: public");
header("Content-Description: File Transfer");
header("Content-Type: application/octet-stream");
$content_disposition = "Content-Disposition: attachment; filename=\"".$row['filename']."\"";
header($content_disposition);
header("Content-Transfer-Encoding: binary");
header("Content-Length: " . filesize($file));
$f = @fopen($file,"rb");
if($f){
	while(!feof($f)){
		print(fread($f, 1024*8));
		flush();
		if(connection_status()!=0) {
			@fclose($f);
			die();
		}
	}
	@fclose($f);
}
/*************************
sng:17/nov/2011
after a download, update the download count for this case study
do not bother if this does not work
**************************/
$success = $g_trans->front_case_study_downloaded($_POST['case_study_id']);
?>