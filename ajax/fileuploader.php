<?php
require_once("../include/global.php");
require_once("classes/class.account.php");
require_once("classes/fileuploader.php");
require_once("classes/db.php");

if(!$g_account->is_site_member_logged()){
	echo htmlspecialchars(json_encode(array('error' => "You need to be logged in to upload files.")),ENT_NOQUOTES);
	exit;
}

// list of valid extensions, ex. array("jpeg", "xml", "bmp")
$allowedExtensions = array('pdf','doc','docx','txt','xls','xlsx','ppt','pptx','jpg','gif','png');
// max file size in bytes
$sizeLimit = 1024 * 1024;

$uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
$result = $uploader->handleUpload('../temp_suggestion_files/');

if(isset($result['success'])&&($result['success']==true)){
	$db = new db();
	$q = "insert into ".TP."transaction_files set caption='".$result['original_filename']."',stored_filename='".$result['stored_filename']."',date_uploaded='".date("Y-m-d")."',mem_id='".$_SESSION['mem_id']."',suggestion_id='',transaction_id='0',is_approved='n'";
	/********
	We are uploading the files in 'suggest a deal' step 1. We are yet to complete step 2 and 3.
	At this stage, we do not have the suggestion id and admin is yet to create a transaction from our suggestion (if at all)
	However, since we are storing the files, we keep a record in the database along with the time when we received the file.
	This way, we can run a cron job that can delete the files if it is not associated with a transaction, even after a month
	
	sng:6/sep/2011
	we also set is_approved to n till admin explicitly set it to y and since this is suggested by a member, we store the mem id also
	**************************/
	$success = $db->mod_query($q);
	if($success){
		/***************
		get the unique record id and store in session so that if we do store the suggestion, we can update the records and set the suggestion ids
		and then remove the file ids (otherwise another suggest a deal session will mark the existing records with another suggestion ids)
		Note: we can upload more than one file, that is why we appen to the array
		We do this in ajax/suggest_deal/request.php, if all ok.
		**********************/
		if(!isset($_SESSION['suggestion_files_id'])){
			$_SESSION['suggestion_files_id'] = array();
		}
		$_SESSION['suggestion_files_id'][] = $db->last_insert_id();
	}
	//if not success, we cannot do a thing here
}
// to pass data through iframe you will need to encode all html tags
echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
?>