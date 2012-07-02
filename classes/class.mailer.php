<?php
require_once("lib/mail/class.phpmailer.php");
require_once("lib/mail/class.smtp.php");
class mailer{
	private $mail_obj;
	private $from_email;
	/****
	for smtp, use the email that you use for authentication as From email
	sng:20/feb/2012
	We can now use the From header
	***/
	
	public function __construct(){
		$this->mail_obj = new PHPMailer(true);
		// the true param means it will throw exceptions on errors, which we need to catch
		$this->mail_obj->IsSMTP();
		// telling the class to use SMTP
		$this->mail_obj->Host = "mail.yourdomain.com";
		// SMTP server
  		//$this->mail_obj->SMTPDebug = 2;
		$this->mail_obj->SMTPDebug = false;
		// enables SMTP debug information (for testing), else set to false
  		$this->mail_obj->SMTPAuth = true;
		// enable SMTP authentication
		//get the smtp credentials from db
		$q = "select smtp_host,smtp_port,smtp_user,smtp_pass from ".TP."sitesetup";
		$res = mysql_query($q) or die(mysql_error());
		$row = mysql_fetch_assoc($res);
  		$this->mail_obj->Host = $row['smtp_host'];
		// sets the SMTP server
  		$this->mail_obj->Port = $row['smtp_port'];
		// set the SMTP port
  		$this->mail_obj->Username = $row['smtp_user'];
		// SMTP account username
		$this->from_email = $row['smtp_user'];
  		$this->mail_obj->Password = $row['smtp_pass'];
		// SMTP account password
	}
	public function mail($to,$subject,$msg,$from_email){
	/*return true;*/
		/**************
		sng:1/mar/2012
		if the email address is malformed, exception is thrown
		***************/
		try{
			$this->mail_obj->AddAddress($to);
		}
		catch(phpmailerException $e){
			return false;
		}
		$this->mail_obj->Subject = $subject;
		$this->mail_obj->IsHTML(false);
		$this->mail_obj->Body = $msg;
		/*************
		sng:20/feb/2012
		***************/
		if($from_email==""){
			$from_email = $this->from_email;
		}
		$this->mail_obj->From = $from_email;
		$this->mail_obj->FromName = $from_email;
		try{
			$this->mail_obj->Send();
			return true;
		}
		catch(phpmailerException $e){
			return false;
		}
		catch(Exception $e){
			return false;
		}
	}
	
	/***************
	sng:27/feb/2012
	*************/
	public function html_mail($to,$subject,$msg,$from_email){
	/*return true;*/
		/**************
		sng:1/mar/2012
		if the email address is malformed, exception is thrown
		***************/
		try{
			$this->mail_obj->AddAddress($to);
		}
		catch(phpmailerException $e){
			return false;
		}
		$this->mail_obj->Subject = $subject;
		$this->mail_obj->IsHTML(true);
		$this->mail_obj->Body = $msg;
		/*************
		sng:20/feb/2012
		***************/
		if($from_email==""){
			$from_email = $this->from_email;
		}
		$this->mail_obj->From = $from_email;
		$this->mail_obj->FromName = $from_email;
		try{
			$this->mail_obj->Send();
			return true;
		}
		catch(phpmailerException $e){
			return false;
		}
		catch(Exception $e){
			return false;
		}
	}
	
	/*********************
	sng:27/feb/2012
	Assume that there is a FILE_PATH where path of the app is specified
	The template use the variable email_data
	********************/
	public function mail_from_template($template,$email_data){
		if(file_exists(FILE_PATH."/".$template)){
			ob_start();
			require_once($template);
			$content = ob_get_contents();
			ob_end_clean();
			return $content;
		}else{
			return "";
		}
	}
	/*********************
	sng:1/mar/2012
	It may happen that we create a mailer object and set the body. Then we want to send the email to
	multiple recipients but, each will only see his/her address.
	To do this, we need to clear the TO field after sending a mail.
	************************/
	public function clear_recipients(){
		$this->mail_obj->ClearAddresses();
	}
}
?>