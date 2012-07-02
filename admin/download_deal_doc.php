<?php
/*******************************************
code taken from /download_deal_doc.php
*********************************************/
include("../include/global.php");
require_once("classes/class.account.php");
if(!$g_account->is_admin_logged()){
	?>
	<html>
	<head>
	<title>Deal Document</title>
	</head>
	<body>
	<h1>You need to be logged in to download a deal document</h1>
	</body>
	</html>
	<?php
	exit;
}
//logged in, so download, never mind whether this has been approved by admin or not
$q = "select file_id,caption,stored_filename from ".TP."transaction_files where file_id='".$_POST['doc_id']."'";
$res = mysql_query($q) or die("Cannot get doc detail");
$cnt = mysql_num_rows($res);
if(0 == $cnt){
	?>
	<html>
	<head>
	<title>Deal Document</title>
	</head>
	<body>
	<h1>The deal document was not found</h1>
	</body>
	</html>
	<?php
	exit;
}
$row = mysql_fetch_assoc($res);

//send the file
$file = FILE_PATH."/temp_suggestion_files/".$row['stored_filename'];
if(!is_file($file)){
	?>
	<html>
	<head>
	<title>Deal Document</title>
	</head>
	<body>
	<h1>The deal document file was not found</h1>
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
$content_disposition = "Content-Disposition: attachment; filename=\"".$row['caption']."\"";
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
?>