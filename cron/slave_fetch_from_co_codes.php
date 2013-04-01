<?php
/*******************
sng:19/feb/2013

Now we use the co-code obj to fetch the company data first, then fetch the nasdaq eq deal data
nasdaq equity deal data refer to companies in company data. That is why we update the company first

This will run in background
It will be triggered by some request from co-codes. They will call our callback code, that callback code
will run this in the background

For now, we run this manually

After a run, we just email the log file
***************/
require(dirname(dirname(__FILE__))."/include/minimal_bootstrap.php");
require(FILE_PATH."/classes/class.co_codes.php");
$co_code = co_codes::create();
if($co_code===false){
	print_r("cannot create co-code object\r\n");
	return;
}
$co_code->get_all_company_data();

$co_code->get_all_equity_deal_data();

/************
sng:1/apr/2013
************/
$co_code->fetch_errored_logos();
/**********************************************
include the proper classes and trigger email
****/
require(FILE_PATH."/lib/mail/class.phpmailer.php");
require(FILE_PATH."/lib/mail/class.smtp.php");

// the true param means it will throw exceptions on errors, which we need to catch
$mailer = new PHPMailer(true);

// telling the class to use SMTP
$mailer->IsSMTP();

// enables SMTP debug information (for testing), else set to false
$mailer->SMTPDebug = false;

// enable SMTP authentication
$mailer->SMTPAuth = true;

//get the smtp credentials from db
$db = db::create($g_config['db_host'],$g_config['db_user'],$g_config['db_password'],$g_config['db_name']);
if($db!==false){
	$q = "select smtp_host,smtp_port,smtp_user,smtp_pass from ".TP."sitesetup";
	$ok = $db->select_query($q);
	if($ok){
		$row = $db->get_row();
		// sets the SMTP server
		$mailer->Host = $row['smtp_host'];
		
		// set the SMTP port
		$mailer->Port = $row['smtp_port'];
		
		// SMTP account username
		$mailer->Username = $row['smtp_user'];
		
		// SMTP account password
		$mailer->Password = $row['smtp_pass'];
		
		try{
			$mailer->SetFrom('no-reply@deal-data.com','deal-data.com');
			$mailer->AddAddress("stefan@fractalexperience.com");
			$mailer->AddAddress("unified.sng@gmail.com");
			$mailer->Subject = "data import from co-code report";
			$mailer->IsHTML(false);
			$mailer->Body = "See attachment";
			$mailer->AddAttachment(co_codes::$debug->get_output_file());
			$mailer->Send();
		}catch(Exception $e){
			print_r($e);
		}
	}
}
/****************************************************/
?>