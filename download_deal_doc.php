<?php
require_once("include/global.php");
require_once("classes/class.account.php");
/********************
sng:11/nov/2011
we make this open to all
***********/

/***************************
logged in, so get the id and see if the file is there. If so, send it
sng: 6/sep/2011
We also check whether this is approved by admin or not

sng:22/feb/2012
Now deal record can be created from simple suggeston. Admin need not check the suggestion and create a deal manually.
A deal submission can have files.
Now that we have peer review, admin need not approve the file to show it in the deal detail page.
In fact, for peer review to work, we must allow users to download un-approved files.
*****************************/
$q = "select file_id,caption,stored_filename from ".TP."transaction_files where file_id='".$_GET['doc_id']."'";
$res = mysql_query($q) or die("Cannot get doc detail");
$cnt = mysql_num_rows($res);
if(0 == $cnt){
	?>
	<html>
	<head>
	<title>Deal Detail</title>
	</head>
	<body>
	<h1>The doc was not found</h1>
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
	<title>Deal Detail</title>
	</head>
	<body>
	<h1>The file was not found</h1>
	</body>
	</html>
	<?php
	exit;
}
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