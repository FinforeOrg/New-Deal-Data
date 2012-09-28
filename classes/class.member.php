<?php
/*****
sng:7/apr/2010
This class contains all the functions related to membership registration, member data, etc
********/
require_once("classes/class.magic_quote.php");

class member{
	/******************
	sng:28/sep/2012
	thumbnauls must fit within a bounding box. We define the constants here
	******************/
	private $thumb_fit_width = 121;
	private $thumb_fit_height = 121;
	///////////////////////////////designation related code start//////////////////////////////////////
	/***
	sng:23/apr/2010
	we order by the wight
	*/
	public function get_all_designation_list(&$data_arr,&$data_count){
		$q = "select * from ".TP."designation_master order by member_type asc,deal_share_weight desc";
		
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$data_count = mysql_num_rows($res);
		if($data_count == 0){
			//no recs
			return true;
		}
		//recs so
		while($row = mysql_fetch_assoc($res)){
			//we assume that designation will not have single quote in the name
			$data_arr[] = $row;
		}
		return true;
	}
	/***
	sng:1/may/2010
	this just delete from master list. since nobody refer to this by id, this should not be a problem
	***/
	public function delete_designation($designation_id){
		$q = "delete from ".TP."designation_master where id='".$designation_id."'";
		$result = mysql_query($q);
		if(!$result){
			return false;
		}
		return true;
	}
	
	public function get_all_designation_list_by_type($type,&$data_arr,&$data_count){
	
		$q = "select * from ".TP."designation_master where member_type='".$type."' order by designation";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$data_count = mysql_num_rows($res);
		if($data_count == 0){
			//no recs
			return true;
		}
		//recs so
		while($row = mysql_fetch_assoc($res)){
			/**
			sng:6/apr/2010
			I do not think designation will have single quote
			**/
			$data_arr[] = $row;
		}
		return true;
	}
	
	public function add_designation($data_arr,&$validation_passed,&$err_arr){
		//validation
		$validation_passed = true;
		
		if($data_arr['designation'] == ""){
			$err_arr['designation'] = "Please specify the designation";
			$validation_passed = false;
		}
		
		if($data_arr['type'] == ""){
			$err_arr['type'] = "Please select type";
			$validation_passed = false;
		}
		
		if($data_arr['deal_share_weight'] == ""){
			$err_arr['deal_share_weight'] = "Please specify the deal share weight";
			$validation_passed = false;
		}
		
		//check for duplication, we cannot have same designation for same type
		if(($data_arr['designation']!="")&&($data_arr['type']!="")){
			$q = "select count(designation) as cnt from ".TP."designation_master where designation='".$data_arr['designation']."' And member_type='".$data_arr['type']."'";
			$res = mysql_query($q);
			if(!$res){
				return false;
			}
			$row = mysql_fetch_assoc($res);
			if($row['cnt'] > 0){
				//this designation exists for this member type
				$err_arr['designation'] = "This designation for this member type already exists, specify another one.";
				$validation_passed = false;
			}
		}
		if(!$validation_passed){
			//no need to proceed
			return true;
		}
		////////////////////////////////////////////////////////////////////////////
		//validation passed, insert data
		$q = "insert into ".TP."designation_master set designation='".$data_arr['designation']."',
		member_type='".$data_arr['type']."', deal_share_weight='".$data_arr['deal_share_weight']."'";
		
		$result = mysql_query($q);
		if(!$result){
			return false;
		}
		/////////////////
		//data inserted
		$validation_passed = true;
		return true;
	}
	///////////////////////////////designation related code end//////////////////////////////////////
	
	/////////////////////////registration related code start////////////////////////////////////////////
	/***
	sng:5/jun/2010
	We want the user to select deals after doing the registration. So we also return the registration request code
	so that the list of deals can be associated with this request
	
	sng:28/july/2010
	We need to record when the registration is made
	*******/
	public function new_membership_request($data_arr,$security_code,&$validation_passed,&$err_arr,&$req_id){
		global $g_mc;
		//validation
		$validation_passed = true;
		
		$first_name = $g_mc->view_to_db($data_arr['first_name']);
		$last_name = $g_mc->view_to_db($data_arr['last_name']);
		$firm_name = $g_mc->view_to_db($data_arr['firm_name']);
		
		if($first_name == ""){
			$err_arr['first_name'] = "Please specify the first name";
			$validation_passed = false;
		}
		
		if($last_name == ""){
			$err_arr['last_name'] = "Please specify the last name";
			$validation_passed = false;
		}
		
		if($data_arr['password'] == ""){
			$err_arr['password'] = "Please specify the password";
			$validation_passed = false;
		}
		
		if($data_arr['re_password'] == ""){
			$err_arr['re_password'] = "Please retype the password";
			$validation_passed = false;
		}
		
		if(($data_arr['password']!="")&&($data_arr['re_password']!="")){
			if($data_arr['re_password'] != $data_arr['password']){
				$err_arr['re_password'] = "password and retyped password does not match";
				$validation_passed = false;
			}
		}
		
		if($data_arr['type'] == ""){
			$err_arr['type'] = "Please specify the membership type";
			$validation_passed = false;
		}
		
		if($data_arr['home_email'] == ""){
			/*****************
			sng:16/nov/2011
			Let home email be optional. However, if it is specified, we check for uniqueness
			***************/
		}else{
			//check if exists
			$exists = true;
			$success = $this->is_email_exists($data_arr['home_email'],$exists);
			if(!$success){
				return false;
			}
			if($exists){
				$err_arr['home_email'] = "This home email exists, specify another";
				$validation_passed = false;
			}
		}
		
		if($data_arr['work_email'] == ""){
			$err_arr['work_email'] = "Please specify the work email";
			$validation_passed = false;
		}else{
			//check if exists
			$exists = true;
			$success = $this->is_email_exists($data_arr['work_email'],$exists);
			if(!$success){
				return false;
			}
			if($exists){
				$err_arr['work_email'] = "This work email exists, specify another";
				$validation_passed = false;
			}else{
				/*************************************
				sng:14/dec/2011
				This is a new work email. Check if this is one of the 'unfavoured' emails
				************************************/
				$is_unfavoured = false;
				$success = $this->is_work_email_unfavoured($data_arr['work_email'],$is_unfavoured);
				if(!$success){
					return false;
				}
				if($is_unfavoured){
					$err_arr['work_email'] = "Free public emails are not accepted";
					$validation_passed = false;
				}
			}
		}
		
		/******************
		sng:16/nov/2011
		Company is optional
		designation is optional
		Year join is optional
		Location is optional
		
		sng:19/dec/2011
		If company is optional, division should be optional too
		
		sng:19/dec/2011
		We cannot make company optional since it breaks codes in other sections, like favoured emails
		and account activation
		*********************/
		if($firm_name == ""){
			$err_arr['firm_name'] = "Please specify the firm where you work";
			$validation_passed = false;
		}
		
		
		/************
		sng:28/dec/2011
		we are using recaptcha service. The keys are defined in recaptcha_1_11/recaptcha_conf.php
		**************/
		global $recaptcha_private_key;
		$resp = recaptcha_check_answer($recaptcha_private_key,$_SERVER["REMOTE_ADDR"],$_POST["recaptcha_challenge_field"],$_POST["recaptcha_response_field"]);
		if (!$resp->is_valid) {
			$err_arr['security_code'] = "Please enter correct security code";
			$validation_passed = false;
		}
		
		if(!$validation_passed){
			//no need to proceed
			return true;
		}
		
	
		if(!$validation_passed){
			//no need to proceed
			return true;
		}
		///////////////////////////////////////////////////////
		//insert data
	
		/***
		sng:6/apr/2010
		since we do not allow the user to select a company name from a list, we do not get the company_id
		we just enter the company name
		
		sng:9/jun/2010
		An email is sent upon registration request
		*******/
		$uid = time();
		$time_now = date("Y-m-d H:i:s");
		$q = "insert into ".TP."registration_request set 
		uid                  ='".$uid."',
		registration_datetime = '".$time_now."',
		f_name               ='".$first_name."',
		l_name               ='".$last_name."',
		password             ='".$data_arr['password']."',
		work_email           ='".$data_arr['work_email']."',
		home_email           ='".$data_arr['home_email']."',
		member_type          ='".$data_arr['type']."',
	
		company_name         ='".$firm_name."',
		year_joined          ='".$data_arr['join_date']."',
		designation          ='".$data_arr['designation']."',
		designation_other    ='".$data_arr['other_designation']."',
		division             ='".$data_arr['division']."',
		posting_country      ='".$data_arr['location']."'";
		$result = mysql_query($q);
		if(!$result){
			//echo mysql_error();
			return false;
		}
		$validation_passed = true;
		$req_id = $uid;
		/////////////////////////////////////////////////
		//dash off an email to registration_notification_email
		require_once("classes/class.sitesetup.php");
		global $g_site;
		$admin_data = array();
		$success = $g_site->get_site_emails($admin_data);
		if(!$success){
			//well, we will not hang a registration script just because we cannot send email
			return true;
		}
		//echo "admin email";
		$admin_email = $admin_data['registration_notification_email'];
		$headers = "";
		$headers .= "From: ".$admin_email."\r\n";
		$to = $admin_email;
		$subject = "New data-cx.com membership request";
		$message = "A new member wants to register. The details are as follows\r\n\r\n";
		$message.="First name ".stripslashes($first_name)."\r\n";
		$message.="Last name ".stripslashes($last_name)."\r\n";
		$message.="Member type ".$data_arr['type']."\r\n";
		$message.="Company ".stripslashes($firm_name)."\r\n";
		$message.="Work email ".$data_arr['work_email']."\r\n";
		/******
		sng:12/oct/2010
		use the mailer class
		******/
		require_once("classes/class.mailer.php");
		$mailer = new mailer();
		$success = $mailer->mail($to,$subject,$message);
		//mail($to,$subject,$message,$headers);
		////////////////////////////////////////////////////
		return true;
	
		/////////////////
		//data inserted
	
	}
	
	/***
	sng:/jun/
	Allow to add deals to registration request
	*******/
	public function add_deals_to_registration_request($req_id,$deal_id_arr){
		$deal_id_csv = implode(",",$deal_id_arr);
		$q = "update ".TP."registration_request set deals='".$deal_id_csv."' where uid='".$req_id."'";
		$result = mysql_query($q);
		if(!$result){
			return false;
		}
		return true;
	}
	/****
	sng:6/apr/2010
	We show only those data for which admin has not sent activation mail
	
	sng:27/july/2010
	Do not show the new registrations that are treated as favoured. The admin will not activate or reject. They are listed in their own menu.
	Non favoured registrations have verified of N if admin has not activated it, but so does favoured registrations, since there is not admin
	check. So we add another check is_favoured = N
	***/
	public function get_all_new_reg_list(&$data_arr,&$data_count){
		global $g_mc;
		$q = "select * from ".TP."registration_request where verified='N' and is_favoured='N'";
		$res = mysql_query($q);
		if(!$res){
			//echo mysql_error();
			return false;
		}
		$data_count = mysql_num_rows($res);
		if($data_count == 0){
			//no recs
			return true;
		}
		//recs so
		while($row = mysql_fetch_assoc($res)){
			$row['company_name'] = $g_mc->db_to_view($row['company_name']);
			$row['f_name'] = $g_mc->db_to_view($row['f_name']);
			$row['l_name'] = $g_mc->db_to_view($row['l_name']);
			$data_arr[] = $row;
		}
		return true;
	}
	
	/***
	sng:27/july/2010
	get the list of unavtivated favoured reg items. These are when 2 remainders has been sent
	
	sng:28/july/2010
	We get all the unactivated favoured registration request items, never mind how many remainders were sent
	******/
	public function get_all_unactivated_favoured_reg_list(&$data_arr,&$data_count){
		global $g_mc;
		//$q = "select * from ".TP."registration_request where is_favoured='Y' and num_remainder_email_sent>=2";
		$q = "select * from ".TP."registration_request where is_favoured='Y'";
		$res = mysql_query($q);
		if(!$res){
			//echo mysql_error();
			return false;
		}
		$data_count = mysql_num_rows($res);
		if($data_count == 0){
			//no recs
			return true;
		}
		//recs so
		while($row = mysql_fetch_assoc($res)){
			$row['company_name'] = $g_mc->db_to_view($row['company_name']);
			$row['f_name'] = $g_mc->db_to_view($row['f_name']);
			$row['l_name'] = $g_mc->db_to_view($row['l_name']);
			$data_arr[] = $row;
		}
		return true;
	}
	/****
	sng:6/oct/2010
	We also need to see the list of normal members who are yet to activate their account.
	These are the account where is_favoured is N and activastion email has been sent
	The question is, say for some reason they do not get the activation email. within 48 hrs, their
	request data will be deleted. How do you solve that?
	*********/
	public function get_all_unactivated_reg_list(&$data_arr,&$data_count){
		global $g_mc;
		$q = "select * from ".TP."registration_request where is_favoured='N' AND activation_email_sent_date!='0000-00-00 00:00:00'";
		$res = mysql_query($q);
		if(!$res){
			//echo mysql_error();
			return false;
		}
		$data_count = mysql_num_rows($res);
		if($data_count == 0){
			//no recs
			return true;
		}
		//recs so
		while($row = mysql_fetch_assoc($res)){
			$row['company_name'] = $g_mc->db_to_view($row['company_name']);
			$row['f_name'] = $g_mc->db_to_view($row['f_name']);
			$row['l_name'] = $g_mc->db_to_view($row['l_name']);
			$data_arr[] = $row;
		}
		return true;
	}
	
	public function get_new_reg_req($uid,&$data_arr){
		global $g_mc;
		
		$q = "select * from ".TP."registration_request where uid='".$uid."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$data_count = mysql_num_rows($res);
		if($data_count == 0){
		//no such req
			return false;
		}
		//recs so
		$data_arr = mysql_fetch_assoc($res);
		$data_arr['company_name'] = $g_mc->db_to_view($data_arr['company_name']);
		$data_arr['f_name'] = $g_mc->db_to_view($data_arr['f_name']);
		$data_arr['l_name'] = $g_mc->db_to_view($data_arr['l_name']);
		return true;
	}
	
	/***
	sng:6/apr/2010
	since admin is satisfied that this is a bona fide member and has sent activation link, we set verified to Y
	We do not delete the record till that is activated
	
	sng:28/apr/2010
	we get the email of the admin who is accepting the member and use that email as from email
	
	sng:3/jun/2010
	We use the registration_email of sitesetup as the from email
	*****/
	public function accept_registration_request($data_arr){
		global $g_http_path, $g_mc;
		
		//send activation email to work email only. That way we know whether the fellow
		//really work in that company or not
		$to = $data_arr['work_email'];
		$subject = "data-cx.com membership activation link";
		$message = "Hi ".$g_mc->view_to_view($data_arr['f_name'])." ".$g_mc->view_to_view($data_arr['l_name'])."\r\n\r\n";
		$message.="Your registration request has been accepted. To active you account please type the following url in your browser and visit the site to activate your account\r\n";
		$message.=$g_http_path."/active.php?uid=".$data_arr['uid'];
		$message.="\r\n\r\nHowever, please note, by clicking on the link you shall be joining Data-CX and agreeing to abide by our Terms of Service, become a legal party to our User Agreement, understand our Copyright and Privacy Policies, and all other agreements. You should review these agreements at the relevant location on our website.\r\n\r\n";
		/***
		sng:29/july/2010
		Need to add some more text
		***/
		$message.="If you have received this message in Error, or believe someone is trying to register on Data-CX under your profile, please send us a message so we can investigate this unauthorised use of your email address for registration.\r\n\r\n";
		
		$headers = "";
		//$headers.= 'MIME-Version: 1.0' . "\r\n";
		//$headers .= 'Content-type: text/plain;charset=iso-8859-1' . "\r\n";
		////////////////////////////////////////////
		require_once("classes/class.sitesetup.php");
		global $g_site;
		
		$admin_data = array();
		$success = $g_site->get_site_emails($admin_data);
		if(!$success){
			return false;
		}
		$admin_email = $admin_data['registration_email'];
		//////////////////////////////////////
		$headers .= "From: ".$admin_email."\r\n";
		/***
		sng:29/july/2010
		Set the sender same as From
		***/
		$message.="Thanks,\r\n\r\n";
		//$message.="Shane @ myTombstones";
		$message.=$admin_email;
		/**********************************************/
		/******
		sng:12/oct/2010
		use the mailer class
		******/
		require_once("classes/class.mailer.php");
		$mailer = new mailer();
		$mail_ok = $mailer->mail($to,$subject,$message);	
		//$mail_ok = mail($to,$subject,$message,$headers);
		//$mail_ok = true;
		if($mail_ok){
			//set the activation mail sending time
			$now = date("Y-m-d H:i:s");
			$q = "UPDATE ".TP."registration_request set activation_email_sent_date='".$now."', verified='Y' WHERE uid='".$data_arr['uid']."'";
			$result = mysql_query($q);
			if(!$result){
				return false;
			}else{
				return true;
			}
		}else{
			return false;
		}
	}
	
	/**************************************************
	sng:27/july/2010
	is the registration favoured? This is used during registration, when the user has added deals to the
	registration request process. This is used to change the message shown
	**********/
	public function is_registration_favoured($req_id,&$is_favoured){
		$q = "select is_favoured from ".TP."registration_request where uid='".$req_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$count = mysql_num_rows($res);
		if(0==$count){
			//no such row, then treat it as no favourite
			$is_favoured = false;
			return true;
		}
		$row = mysql_fetch_assoc($res);
		if($row['is_favoured']=='Y'){
			$is_favoured = true;
		}else{
			$is_favoured = false;
		}
		return true;
	}
	/*********
	sng:27/jul/2010
	case for favoured emails during registration. Use this to accept registration request, also set
	is_favoured to Y
	However, since this is bypassing admin check, we set is_verified to N
	***/
	public function favour_accept_registration($req_id){
		global $g_http_path, $g_mc;
		
		//get the required data
		$req_q = "select uid,f_name,l_name,work_email from ".TP."registration_request where uid='".$req_id."'";
		$req_q_res = mysql_query($req_q);
		if(!$req_q_res){
			//echo mysql_error();
			return false;
		}
		$req_q_res_count = mysql_num_rows($req_q_res);
		if(0==$req_q_res_count){
			//serious
			//echo "not found";
			return false;
		}
		$req_q_res_row = mysql_fetch_assoc($req_q_res);
		
		//send activation email to work email only. That way we know whether the fellow
		//really work in that company or not
		$to = $req_q_res_row['work_email'];
		$subject = "data-cx.com membership activation link";
		$message = "Hi ".$g_mc->db_to_view($req_q_res_row['f_name'])." ".$g_mc->db_to_view($req_q_res_row['l_name'])."\r\n\r\n";
		$message.="Your registration request has been accepted. To active you account please type the following url in your browser and visit the site to activate your account\r\n";
		$message.=$g_http_path."/active.php?uid=".$req_q_res_row['uid'];
		$message.="\r\n\r\nHowever, please note, by clicking on the link you shall be joining Deal-data and agreeing to abide by our Terms of Service, become a legal party to our User Agreement, understand our Copyright and Privacy Policies, and all other agreements. You should review these agreements at the relevant location on our website.\r\n\r\n";
		$message.="Thanks,\r\n\r\n";
		$message.="Shane @ Deal-data";
		$headers = "";
		//$headers.= 'MIME-Version: 1.0' . "\r\n";
		//$headers .= 'Content-type: text/plain;charset=iso-8859-1' . "\r\n";
		////////////////////////////////////////////
		require_once("classes/class.sitesetup.php");
		global $g_site;
		
		$admin_data = array();
		$success = $g_site->get_site_emails($admin_data);
		if(!$success){
			//echo mysql_error();
			return false;
		}
		$admin_email = $admin_data['registration_email'];
		//////////////////////////////////////
		$headers .= "From: ".$admin_email."\r\n";
		/******
		sng:12/oct/2010
		use the mailer class
		******/
		require_once("classes/class.mailer.php");
		$mailer = new mailer();
		$mail_ok = $mailer->mail($to,$subject,$message);		
		//$mail_ok = mail($to,$subject,$message,$headers);
		//$mail_ok = true;
		if($mail_ok){
			//set the activation mail sending time
			//admin has not verified this
			//this is favoured
			$now = date("Y-m-d H:i:s");
			$q = "UPDATE ".TP."registration_request set activation_email_sent_date='".$now."', verified='N',is_favoured='Y' WHERE uid='".$req_q_res_row['uid']."'";
			$result = mysql_query($q);
			if(!$result){
				//echo mysql_error();
				return false;
			}else{
				return true;
			}
		}else{
			return false;
		}
	}
	/***
	sng:27/july/2010
	get data, resend email and then update the record
	***/
	public function resend_activation_email($req_id){
		global $g_http_path, $g_mc;
		
		//get the required data
		$req_q = "select uid,f_name,l_name,work_email from ".TP."registration_request where uid='".$req_id."'";
		$req_q_res = mysql_query($req_q);
		if(!$req_q_res){
			return false;
		}
		$req_q_res_count = mysql_num_rows($req_q_res);
		if(0==$req_q_res_count){
			//serious
			return false;
		}
		$req_q_res_row = mysql_fetch_assoc($req_q_res);
		
		//send activation email to work email only. That way we know whether the fellow
		//really work in that company or not
		$to = $req_q_res_row['work_email'];
		$subject = "data-cx.com membership activation link";
		$message = "Hi ".$g_mc->db_to_view($req_q_res_row['f_name'])." ".$g_mc->db_to_view($req_q_res_row['l_name'])."\r\n\r\n";
		$message.="This is a remainder email to activate your account. To active you account please type the following url in your browser and visit the site to activate your account\r\n";
		$message.=$g_http_path."/active.php?uid=".$req_q_res_row['uid'];
		$message.="\r\n\r\nHowever, please note, by clicking on the link you shall be joining Deal-data and agreeing to abide by our Terms of Service, become a legal party to our User Agreement, understand our Copyright and Privacy Policies, and all other agreements. You should review these agreements at the relevant location on our website.\r\n\r\n";
		$message.="Thanks,\r\n\r\n";
		$message.="Shane @ Deal-data";
		$headers = "";
		//$headers.= 'MIME-Version: 1.0' . "\r\n";
		//$headers .= 'Content-type: text/plain;charset=iso-8859-1' . "\r\n";
		////////////////////////////////////////////
		require_once("classes/class.sitesetup.php");
		global $g_site;
		
		$admin_data = array();
		$success = $g_site->get_site_emails($admin_data);
		if(!$success){
			return false;
		}
		$admin_email = $admin_data['registration_email'];
		//////////////////////////////////////
		$headers .= "From: ".$admin_email."\r\n";
		/******
		sng:12/oct/2010
		use the mailer class
		******/
		require_once("classes/class.mailer.php");
		$mailer = new mailer();
		$mail_ok = $mailer->mail($to,$subject,$message);		
		//$mail_ok = mail($to,$subject,$message,$headers);
		//$mail_ok = true;
		if($mail_ok){
			//update the date time of sending the email and update the count
			$now = date("Y-m-d H:i:s");
			$q = "UPDATE ".TP."registration_request set activation_email_sent_date='".$now."',num_remainder_email_sent=num_remainder_email_sent+1 WHERE uid='".$req_q_res_row['uid']."'";
			$result = mysql_query($q);
			if(!$result){
				return false;
			}else{
				return true;
			}
		}else{
			return false;
		}
	}
	/***********************************************************/
	
	/****
	sng:6/apr/2010
	When we reject a membership application, we should also send the reason to the email addresses specified
	This we send to both work and home email
	***********/
	public function reject_registration_request($uid,$reject_reason){
		global $g_mc;
		//get the emails
		$q = "select work_email,home_email from ".TP."registration_request where uid='".$uid."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$row = mysql_fetch_assoc($res);
		$work_email = $row['work_email'];
		$home_email = $row['home_email'];
		//send email
		$subject = "data-cx.com membership";
		$to = $work_email.",".$home_email;
		$headers = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/plain;charset=iso-8859-1' . "\r\n";
		require_once("classes/class.account.php");
		global $g_account;
		$admin_id = $_SESSION['admin_id'];
		$admin_data = array();
		$success = $g_account->get_email_of_admin($admin_id,$admin_data);
		if(!$success){
			return false;
		}
		$admin_email = $admin_data['email'];
		$headers .= "From: ".$admin_email."\r\n";
		$msg = "Your membership was rejected. The reason is\r\n";
		$msg.=$g_mc->view_to_view($reject_reason);
		/******
		sng:12/oct/2010
		use the mailer class
		******/
		require_once("classes/class.mailer.php");
		$mailer = new mailer();
		/*****
		sng:12/oct/2010
		We send this to work email address
		***/
		$to = $work_email;
		$mailer->mail($to,$subject,$msg);
		//mail($to,$subject,$msg,$headers);
		//now delete the record
		$q = "DELETE FROM ".TP."registration_request WHERE uid='".$uid."'";
		$result = mysql_query($q);
		if(!$result){
			return false;
		}
	
		return true;
	
	}
	/************************
	sng:19/feb/2011
	
	sng:24/feb/2011
	clean out all future fields as if the request never happened
	
	sng:23/sep/2011
	erase out those where the email sent count is 2 (one sent during acceptance, one during remainder)
	**********/
	public function cron_delete_unverified_company_email_change_request(){
		$q = "update ".TP."member set future_company_name='',future_work_email='',future_year_joined='',future_designation='',future_division='',future_country='',future_requested_on='0000-00-00 00:00:00',future_validation_token='',future_validation_email_sent_on='0000-00-00 00:00:00',future_validation_email_sent_count='0' where future_validation_email_sent_count>=2";
		mysql_query($q);
		
		/*****************
		sng:23/sep/2011
		so we have cleaned out the work email/ company change requests for which 2 remainders have been sent
		for the rest, we send a remainder
		We get the mem_id, future_work_email and the token and update the future_validation_email_sent_on to current datetime
		and increase the future_validation_email_sent_count
		
		future_validation_email_sent_on!='0000-00-00 00:00:00' means there is a change request
		*********************/
		global $g_http_path, $g_mc;
		
		$q = "select mem_id,f_name,l_name,future_work_email,future_validation_token from ".TP."member where future_validation_email_sent_on!='0000-00-00 00:00:00'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		require_once("classes/class.sitesetup.php");
		global $g_site;
		$admin_data = array();
		$success = $g_site->get_site_emails($admin_data);
		if(!$success){
			return false;
		}
		$admin_email = $admin_data['registration_email'];
		require_once("classes/class.mailer.php");
		
		while($row = mysql_fetch_assoc($res)){
			$to = $row['future_work_email'];
			$subject = "data-cx.com company / work email change validation email";
			$message = "Dear ".$g_mc->db_to_view($row['f_name'])." ".$g_mc->db_to_view($row['l_name'])."\r\n\r\n";
			$message.="This is a reminder. Your request to update the work email/company has been accepted. Please type the following url in your browser and visit the site. to update your member record.\r\n";
			//we do not send the mem_id because we will get that from session when member logs in and comes here
			$message.=$g_http_path."/update_company_email_change.php?token=".$row['future_validation_token']."\r\n";
			$message.="If you have received this message in Error, or believe someone is trying to change your profile on Deal-data, please send us a message so we can investigate this unauthorised behaviour.\r\n\r\n";
			
			$headers = "";
			$headers .= "From: ".$admin_email."\r\n";
			$message.="Thanks,\r\n\r\n";
			$message.=$admin_email;
			/***
			sng:29/july/2010
			Set the sender same as From
			***/
			/******
			sng:12/oct/2010
			use the mailer class
			******/
			$mailer = new mailer();
			$mail_ok = $mailer->mail($to,$subject,$message);
			if($mail_ok){
				//set the mail sending time
				$now = date("Y-m-d H:i:s");
				$updt_q = "UPDATE ".TP."member set future_validation_email_sent_on='".$now."', future_validation_email_sent_count=future_validation_email_sent_count+1 WHERE mem_id='".$row['mem_id']."'";
				mysql_query($updt_q);
				echo "sent remainder to id: ".$row['mem_id'].PHP_EOL;
				//bad luck if data not updated, continue
			}else{
				//continue
			}
		}
	}
	/***
	sng:11/jun/2010
	
	sng:27/july/2010
	if this is favoured, then do not delete
	**/
	public function cron_delete_unactivated_membership(){
		$q = "delete from ".TP."registration_request where activation_email_sent_date!='0000-00-00 00:00:00' AND datediff( curdate() , activation_email_sent_date )>2 AND is_favoured='N'";
		mysql_query($q);
	}
	
	/***
	sng:27/july/2010
	***/
	public function cron_resend_favoured_activation_emails(){
		//get the registration records that are favoured, for which activation email has been sent, which are there for more than 2 days and remainder
		//has sent less than 2 times
		$q = "select uid from ".TP."registration_request where activation_email_sent_date!='0000-00-00 00:00:00' AND datediff( curdate() , activation_email_sent_date )>2 AND is_favoured='Y' AND num_remainder_email_sent<2";
		$res = mysql_query($q);
		if(!$res){
			return;
		}
		//any records?
		$count = mysql_num_rows($res);
		if(0==$count){
			return;
		}
		//for each resend email and update
		while($row = mysql_fetch_assoc($res)){
			$req_id = $row['uid'];
			$this->resend_activation_email($req_id);
			//the updation of record is headache of the called function
		}
	}
	/***
	When user click activation link, the membership is activated. The record is moved from
	registration_request table to member table.
	In registration request table we store company name, so we need to get the company id
	
	sng:6/may/2010
	it may happen that there is a ghost account of same name, type, company id. If so
	update that record
	
	sng:5/jun/2010
	Now, user can specify deals during registration. So during activation, we add this member and this company to those deals
	
	sng:6/oct/2010
	anybody can activate the membership by guessing the code. We prevent that by checking that admin has approved it.
	and an email has been sent. For that we see the email sending date.
	We also update the message
	****/
	public function activate_membership($req_id,&$is_activated,&$msg){
		global $g_mc;
		//get the data from registration request table
		$data_arr = NULL;
		
		$q = "select * from ".TP."registration_request where uid='".$req_id."' AND activation_email_sent_date!='0000-00-00 00:00:00'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$data_count = mysql_num_rows($res);
		if($data_count == 0){
			//no such req
			$is_activated = false;
			$msg = "The registration data was not found, maybe it was deleted because 48 hr has elapsed or admin is yet to approve it.";
			return true;
		}
		//recs so
		$data_arr = mysql_fetch_assoc($res);
		$data_arr['company_name'] = $g_mc->db_to_view($data_arr['company_name']);
		$data_arr['f_name'] = $g_mc->db_to_view($data_arr['f_name']);
		$data_arr['l_name'] = $g_mc->db_to_view($data_arr['l_name']);
		//first name, last name, company name are magic quoted for view, so we add slash before we enter
		//them for db
		$data_arr['f_name'] = addslashes($data_arr['f_name']);
		$data_arr['l_name'] = addslashes($data_arr['l_name']);
		$data_arr['company_name'] = addslashes($data_arr['company_name']);
		//get the company id from name
		if($data_arr['member_type']=="banker") $company_type = "bank";
		elseif($data_arr['member_type']=="lawyer") $company_type = "law firm";
		elseif($data_arr['member_type']=="company rep") $company_type = "company";
		/**************************************************
		sng:5/apr/2011
		data partner also associate with company
		*********/
		elseif($data_arr['member_type']=="data partner") $company_type="company";
		/********************************************/
		$mem_id = 0;
		
		$company_id = 0;
		$company_found = false;
		//this function require magic quoted data
		$success = $this->company_id_from_name($data_arr['company_name'],$company_type,$company_id,$company_found);
		if(!$success){
			return false;
		}
		if(!$company_found){
			$is_activated = false;
			$msg = "Could not find the company in company database";
			return true;
		}
		////////////////////////////////////////////
		//it may happen that there is a ghost account of same name, type, company id. If sso
		//update that record
		//so find matching ghost record
		////////////////////////////////////////////////////////
		$ghost_q = "select mem_id from ".TP."member where f_name='".$data_arr['f_name']."' and l_name='".$data_arr['l_name']."' and member_type='".$data_arr['member_type']."' and company_id='".$company_id."'";
		$ghost_q_res = mysql_query($ghost_q);
		if(!$ghost_q_res){
			return false;
		}
		$ghost_q_res_cnt = mysql_num_rows($ghost_q_res);
		if($ghost_q_res_cnt == 0){
			//no ghost found so insert
			//now insert
			$q = "insert into ".TP."member set 
			f_name          ='".$data_arr['f_name']."',
			l_name          ='".$data_arr['l_name']."',
			password        ='".$data_arr['password']."',
			work_email      ='".$data_arr['work_email']."',
			home_email      ='".$data_arr['home_email']."',
			member_type     ='".$data_arr['member_type']."',
			company_id      ='".$company_id."',
			year_joined     ='".$data_arr['year_joined']."',
			designation     ='".$data_arr['designation']."',
			division        ='".$data_arr['division']."',
			posting_country ='".$data_arr['posting_country']."',
			is_ghost='N',
			blocked='N'";
		
			$result = mysql_query($q);
				if(!$result){
				//echo mysql_error();
				return false;
			}
			//data inserted so delete the record from request table
			/***
			sng:5/jun/2010
			we need the mem id so that we can add this member to deals if some deals are specified in registration data
			*****/
			$mem_id = mysql_insert_id();
			
			$is_activated = true;
			/////////////////////////////////////////////
			//if there is deals, that has to be added
			//deal partner is firm of this member
			if($data_arr['deals']!=""){
				$this->add_bulk_deals($data_arr['deals'],$company_id,$mem_id);
			}
			///////////////////////
			$msg = "Your member account has been activated.";
			$del_q = "delete from ".TP."registration_request where uid='".$req_id."'";
			mysql_query($del_q);
			return true;
		}else{
			if($ghost_q_res_cnt == 1){
				//one matching ghost, update the ghost member
				$ghost_q_res_row = mysql_fetch_assoc($ghost_q_res);
				$ghost_mem_id = $ghost_q_res_row['mem_id'];
				//update, and make it non ghost
				$q = "update ".TP."member set 
				f_name          ='".$data_arr['f_name']."',
				l_name          ='".$data_arr['l_name']."',
				password        ='".$data_arr['password']."',
				work_email      ='".$data_arr['work_email']."',
				home_email      ='".$data_arr['home_email']."',
				member_type     ='".$data_arr['member_type']."',
				company_id      ='".$company_id."',
				year_joined     ='".$data_arr['year_joined']."',
				designation     ='".$data_arr['designation']."',
				division        ='".$data_arr['division']."',
				posting_country ='".$data_arr['posting_country']."',
				is_ghost='N',
				blocked='N' where mem_id='".$ghost_mem_id."'";
			
				$result = mysql_query($q);
					if(!$result){
					//echo mysql_error();
					return false;
				}
				//data inserted so delete the record from request table
				/***
				sng:5/jun/2010
				we need the mem id so that we can add this member to deals if some deals are specified in registration data
				*****/
				$mem_id = $ghost_mem_id;
				///////////////////////////////////////////////////////////////
				$is_activated = true;
				/////////////////////////////////////////////
				//if there is deals, that has to be added
				//deal partner is firm of this member
				if($data_arr['deals']!=""){
					$this->add_bulk_deals($data_arr['deals'],$company_id,$mem_id);
				}
				////////////////////////////
				$msg = "Your member account has been activated.";
				$del_q = "delete from ".TP."registration_request where uid='".$req_id."'";
				mysql_query($del_q);
				return true;
			}else{
				//two ghosts with same name, type, compnay id? problem
				die("Two ghosts with same name, type, company");
			}
		}
		///////////////////////////////////////////////////////////
	}
	public function add_bulk_deals($deal_id_csv,$company_id,$mem_id){
		require_once("classes/class.transaction.php");
		
		$deal_id_arr = explode(",",$deal_id_csv);
		$deal_cnt = count($deal_id_arr);
		$mem_added = false;
		$msg = "";
		for($k=0;$k<$deal_cnt;$k++){
			$g_trans->add_deal_partner_team_member($deal_id_arr[$k],$company_id,$mem_id,$mem_added,$msg);
			//since this is bulk insert never mind errors
		}
	}
	
	public function unregister_membership($member_id){
		//set this member as ghost but do not delete any deal data
		/***
		sng:10/jun/2010
		When we make a registered account a ghost, we delete the emails and passwords (because ghost account do not have emails an dpassword
		That way, when that person try to register again, the person do not get stuck on "This email already exists"
		***/
		$q = "update ".TP."member set work_email='',home_email='',is_ghost='Y' where mem_id='".$member_id."'";
		$result = mysql_query($q);
		if(!$result){
			return false;
		}else{
			return true;
		}
	}
	
	
	
	/////////////////////////registration related code end////////////////////////////////////////////
	/*************************change company or work email admin part**************************************/
	
	/**********
	sng:24/jan/2011
	get the list of requests for company change or work email change
	We get only those for which the validation email has not been sent
	***/
	public function admin_member_company_email_change_list_paged($start,$num_to_fetch,&$data_arr,&$data_count){
		global $g_mc;
		
		$q = "select mem_id,f_name,l_name,work_email,curr_c.name as curr_company_name,future_company_name,future_work_email,future_requested_on from ".TP."member as m left join ".TP."company as curr_c on(m.company_id= curr_c.company_id) where future_company_name!='' and future_validation_email_sent_on='0000-00-00 00:00:00' order by future_requested_on limit ".$start.",".$num_to_fetch;
		
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$data_count = mysql_num_rows($res);
		if(0==$data_count){
			return true;
		}
		//////////////////////////////////////
		for($i=0;$i<$data_count;$i++){
			$data_arr[$i] = mysql_fetch_assoc($res);
			$data_arr[$i]['f_name'] = $g_mc->db_to_view($data_arr[$i]['f_name']);
			$data_arr[$i]['l_name'] = $g_mc->db_to_view($data_arr[$i]['l_name']);
			$data_arr[$i]['future_company_name'] = $g_mc->db_to_view($data_arr[$i]['future_company_name']);
			$data_arr[$i]['curr_company_name'] = $g_mc->db_to_view($data_arr[$i]['curr_company_name']);
		}
		return true;
	}
	/**********
	sng:14/feb/2011
	get the list of requests for company change or work email change
	We get only those for which the validation email has been sent
	***/
	public function admin_member_unactivated_company_email_change_list_paged($start,$num_to_fetch,&$data_arr,&$data_count){
		global $g_mc;
		
		$q = "select mem_id,f_name,l_name,work_email,curr_c.name as curr_company_name,future_company_name,future_work_email,future_requested_on from ".TP."member as m left join ".TP."company as curr_c on(m.company_id= curr_c.company_id) where future_company_name!='' and future_validation_email_sent_on!='0000-00-00 00:00:00' order by future_requested_on limit ".$start.",".$num_to_fetch;
		//echo $q;
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$data_count = mysql_num_rows($res);
		if(0==$data_count){
			return true;
		}
		//////////////////////////////////////
		for($i=0;$i<$data_count;$i++){
			$data_arr[$i] = mysql_fetch_assoc($res);
			$data_arr[$i]['f_name'] = $g_mc->db_to_view($data_arr[$i]['f_name']);
			$data_arr[$i]['l_name'] = $g_mc->db_to_view($data_arr[$i]['l_name']);
			$data_arr[$i]['future_company_name'] = $g_mc->db_to_view($data_arr[$i]['future_company_name']);
			$data_arr[$i]['curr_company_name'] = $g_mc->db_to_view($data_arr[$i]['curr_company_name']);
		}
		return true;
	}
	public function admin_reject_company_email_change_request($mem_id){
		/***************
		sng:28/jan/2011
		We need to send an email to the existing work email of the member. But the thing is, the content will change depending on
		whether the member changed the work email only or company only or both
		****************************/
		global $g_mc;
		
		$q = "select f_name,l_name,work_email as curr_work_email,future_company_name,future_work_email,c.name as curr_company_name from ".TP."member as m left join ".TP."company as c on(m.company_id=c.company_id) where mem_id='".$mem_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$row = mysql_fetch_assoc($res);
		$f_name = $row['f_name'];
		$l_name = $row['l_name'];
		$curr_company = $row['curr_company_name'];
		$curr_work_email = $row['curr_work_email'];
		$future_company = $row['future_company_name'];
		$future_work_email = $row['future_work_email'];
		/*******************
		sng:24/feb/2011
		clean out all the future fields, as if the request never happened
		***********/
		$q = "update ".TP."member set future_company_name='',future_work_email='',future_year_joined='',future_designation='',future_division='',future_country='',future_requested_on='0000-00-00 00:00:00',future_validation_token='',future_validation_email_sent_on='0000-00-00 00:00:00',future_validation_email_sent_count='0' where mem_id='".$mem_id."'";
		$result = mysql_query($q);
		if(!$result){
			return false;
		}
		
		//now email, to current work email. We have not accepted any change
		$to = $curr_work_email;
		
		
		require_once("classes/class.sitesetup.php");
		global $g_site;
		
		$admin_data = array();
		$success = $g_site->get_site_emails($admin_data);
		if(!$success){
			return false;
		}
		$admin_email = $admin_data['registration_email'];
		////////////////////////////////////////////////////////////////////////////////////////////
		$headers = "";
		$headers .= "From: ".$admin_email."\r\n";
		$subject = "data-cx.com company / work email change request";
		
		$message = "Dear ".$g_mc->db_to_view($f_name)." ".$g_mc->db_to_view($l_name).",\r\n\r\n";
		
		if(($curr_company!=$future_company)&&($curr_work_email==$future_work_email)){
			$message.="Unfortunately we cannot change your firm to the one selected, since the work email address you have given does not match the firm. Please check both the firm name and work email. You shall receive an authentication email at the new work email address, which you need to click, before your profile can be updated.";
		}
		
		if(($curr_company==$future_company)&&($curr_work_email!=$future_work_email)){
			$message.="Unfortunately we cannot change your work email to the one selected, since the firm you have given does not match the work email. Please check both the firm name and work email. You shall receive an authentication email at your work email address, which you need to click, before your profile can be updated.";
		}
		
		if(($curr_company!=$future_company)&&($curr_work_email!=$future_work_email)){
			$message.="Unfortunately we cannot change your work email and firm to the one selected, since the firm you have given does not match the work email. Please check both the firm name and work email. You shall receive an authentication email at your work email address, which you need to click, before your profile can be updated.";
		}
		
		
		$message.="\r\n\r\n";
		
		
		
		$message.="If we have misunderstood, please drop us at email at ".$admin_data['mem_related_email']."\r\n\r\n";
		$message.="Many thanks,\r\n\r\n";
		$message.="The Admin team at data-cx.com";
		/**********************************************/
		/******
		sng:12/oct/2010
		use the mailer class
		******/
		require_once("classes/class.mailer.php");
		$mailer = new mailer();
		$mail_ok = $mailer->mail($to,$subject,$message);
		//never mind if error
		
		return true;
	}
	
	public function admin_accept_company_email_change_request($mem_id){
		/*******
		generate a token and send that token to the future_work_email (even if the member is just changing company)
		It is assumed that admin has checked the change request
		We use the registration email address for the from part
		*********/
		global $g_http_path, $g_mc;
		$token = rand(11111,99999);
		//get the future work email
		$q = "select f_name,l_name,future_work_email from ".TP."member where mem_id='".$mem_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$row = mysql_fetch_assoc($res);
		
		$to = $row['future_work_email'];
		$subject = "data-cx.com company / work email change validation email";
		$message = "Dear ".$g_mc->db_to_view($row['f_name'])." ".$g_mc->db_to_view($row['l_name'])."\r\n\r\n";
		$message.="Your request to update the work email/company has been accepted. Please type the following url in your browser and visit the site. to update your member record.\r\n";
		//we do not send the mem_id because we will get that from session when member logs in and comes here
		$message.=$g_http_path."/update_company_email_change.php?token=".$token."\r\n";
		/***
		sng:29/july/2010
		Need to add some more text
		***/
		$message.="If you have received this message in Error, or believe someone is trying to change your profile on Deal-data, please send us a message so we can investigate this unauthorised behaviour.\r\n\r\n";
		
		$headers = "";
		
		////////////////////////////////////////////
		require_once("classes/class.sitesetup.php");
		global $g_site;
		
		$admin_data = array();
		$success = $g_site->get_site_emails($admin_data);
		if(!$success){
			return false;
		}
		$admin_email = $admin_data['registration_email'];
		//////////////////////////////////////
		$headers .= "From: ".$admin_email."\r\n";
		/***
		sng:29/july/2010
		Set the sender same as From
		***/
		$message.="Thanks,\r\n\r\n";
		//$message.="Shane @ myTombstones";
		$message.=$admin_email;
		/**********************************************/
		/******
		sng:12/oct/2010
		use the mailer class
		******/
		require_once("classes/class.mailer.php");
		$mailer = new mailer();
		$mail_ok = $mailer->mail($to,$subject,$message);	
		if($mail_ok){
			//set the mail sending time
			$now = date("Y-m-d H:i:s");
			$q = "UPDATE ".TP."member set future_validation_token='".$token."',future_validation_email_sent_on='".$now."',future_validation_email_sent_count='1' WHERE mem_id='".$mem_id."'";
			$result = mysql_query($q);
			if(!$result){
				return false;
			}else{
				return true;
			}
		}else{
			return false;
		}
	}
	
	public function admin_accept_and_update_company_email_change_request($mem_id,&$is_updated){
		/*******
		Accept the change and update the profile.No confirmation is needed. No emails are sent.
		We can use the update_company_work_email_change function to do this All we need is the token
		*********/
		$q = "select future_validation_token from ".TP."member where mem_id='".$mem_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$row = mysql_fetch_assoc($res);
		$token = $row['future_validation_token'];
		$is_updated = false;
		$message = "";
		//since it is admin who is doing this, we will not show the message. For admin, either updated
		//or not updated
		return $this->update_company_work_email_change($mem_id,$token,$is_updated,$message);
	}
	/*************************change company or work email admin part end**************************************/
	/***
	sng:3/jun/2010
	We now have ghost member management so we get only non ghost members
	
	sng:12/jun/2010
	We now pass search params. If a search param is found, we set the AND clause
	We do like search on first name, last name, company name. The like search is search item followed by anything.
	*****/
	public function search_all_member_list_paged($search_param_arr,$start_offset,$num_to_fetch,&$data_arr,&$data_count){
		global $g_mc;
		
		$q = "select m.*,c.name as company_name from ".TP."member as m left join ".TP."company as c on(m.company_id=c.company_id) where is_ghost='N'";
		if(isset($search_param_arr['f_name'])&&($search_param_arr['f_name']!="")){
			$q.=" and f_name LIKE '".$g_mc->view_to_db($search_param_arr['f_name'])."%'";
		}
		if(isset($search_param_arr['l_name'])&&($search_param_arr['l_name']!="")){
			$q.=" and l_name LIKE '".$g_mc->view_to_db($search_param_arr['l_name'])."%'";
		}
		if(isset($search_param_arr['company'])&&($search_param_arr['company']!="")){
			$q.=" and name LIKE '".$g_mc->view_to_db($search_param_arr['company'])."%'";
		}
		$q.=" limit ".$start_offset.",".$num_to_fetch;
		$res = mysql_query($q);
		if(!$res){
			//echo mysql_error();
			return false;
		}
		$data_count = mysql_num_rows($res);
			if($data_count == 0){
			//no recs
			return true;
		}
		//recs so
		while($row = mysql_fetch_assoc($res)){
			$row['f_name'] = $g_mc->db_to_view($row['f_name']);
			$row['l_name'] = $g_mc->db_to_view($row['l_name']);
			$row['company_name'] = $g_mc->db_to_view($row['company_name']);
			$data_arr[] = $row;
		}
		return true;
	}
	
	/****
	sng:4/jun/2010
	Get data for ghost member edit
	********/
	public function get_ghost_member_profile($mem_id,&$data_arr){
		global $g_mc;
		
		$q = "select mem_id,f_name,l_name,member_type,m.company_id,designation,posting_country,c.name as company_name from ".TP."member as m left join ".TP."company as c on (m.company_id=c.company_id) where mem_id='".$mem_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$data_arr = mysql_fetch_assoc($res);
		$data_arr['f_name'] = $g_mc->db_to_view($data_arr['f_name']);
		$data_arr['l_name'] = $g_mc->db_to_view($data_arr['l_name']);
		$data_arr['company_name'] = $g_mc->db_to_view($data_arr['company_name']);
		return true;
	}
	
	public function search_all_ghost_member_list_paged($search_param_arr,$start_offset,$num_to_fetch,&$data_arr,&$data_count){
		global $g_mc;
		
		$q = "select m.mem_id,m.f_name,m.l_name,m.member_type,m.designation,c.name as company_name from ".TP."member as m left join ".TP."company as c on(m.company_id=c.company_id) where is_ghost='Y'";
		if(isset($search_param_arr['f_name'])&&($search_param_arr['f_name']!="")){
			$q.=" and f_name LIKE '".$g_mc->view_to_db($search_param_arr['f_name'])."%'";
		}
		if(isset($search_param_arr['l_name'])&&($search_param_arr['l_name']!="")){
			$q.=" and l_name LIKE '".$g_mc->view_to_db($search_param_arr['l_name'])."%'";
		}
		$q.=" limit ".$start_offset.",".$num_to_fetch;
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$data_count = mysql_num_rows($res);
			if($data_count == 0){
			//no recs
			return true;
		}
		//recs so
		while($row = mysql_fetch_assoc($res)){
			$row['f_name'] = $g_mc->db_to_view($row['f_name']);
			$row['l_name'] = $g_mc->db_to_view($row['l_name']);
			$row['company_name'] = $g_mc->db_to_view($row['company_name']);
			$data_arr[] = $row;
		}
		return true;
	}
	
	public function set_member_status($mem_id,$status){
		$q = "update ".TP."member set blocked='".$status."' where mem_id='".$mem_id."'";
		$result = mysql_query($q);
		return true;
	}
	
	public function get_member_profile($mem_id,&$data_arr){
		global $g_mc;
	
		$q = "select * from ".TP."member where mem_id='".$mem_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$data_count = mysql_num_rows($res);
		if($data_count == 0){
			//no such member
			return false;
		}
		//recs so
		$data_arr = mysql_fetch_assoc($res);
	
	
		return true;
	}
	
	/***
	sng:7/apr/2010
	check whether the given email exists or not. We check the member table and membership table.
	we check request table because the home email and work email will get transferred to member table. So we do not want
	duplicates in future.
	we check both the home email and work email field since we allow to login with both
	
	sng:9/jun/2010
	We check all records, never mind inactive or ghost
	********/
	private function is_email_exists($email,&$exist){
		$q = "select count(uid) as cnt from ".TP."registration_request where work_email='".$email."' or home_email='".$email."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$res_row = mysql_fetch_assoc($res);
		if($res_row['cnt'] > 0){
			//found, so
			$exist = true;
			return true;
		}
		//not found in request, check member
		$q = "select count(mem_id) as cnt from ".TP."member where work_email='".$email."' or home_email='".$email."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$res_row = mysql_fetch_assoc($res);
		if($res_row['cnt'] > 0){
			//found, so
			$exist = true;
			return true;
		}
		//not found anywhere
		$exist = false;
		return true;
	}
	/***
	same as the above function, but it exclude the given member when checking for email in member table. This is used
	in update code where member may keep his/her existing email id. In that case there should not be a message, email exists just
	because the user himself/herself has that email
	********/
	private function is_email_exists_for_other($member_id,$email,&$exist){
		$q = "select count(uid) as cnt from ".TP."registration_request where work_email='".$email."' or home_email='".$email."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$res_row = mysql_fetch_assoc($res);
		if($res_row['cnt'] > 0){
			//found, so
			$exist = true;
			return true;
		}
		//not found in request, check member, excluding this member
		$q = "select count(mem_id) as cnt from ".TP."member where mem_id!='".$member_id."' and( work_email='".$email."' or home_email='".$email."')";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$res_row = mysql_fetch_assoc($res);
		if($res_row['cnt'] > 0){
			//found, so
			$exist = true;
			return true;
		}
		//not found anywhere
		$exist = false;
		return true;
	}
	///////////////////////FRONT CODE START/////////////////////////////////
	/***
	search for members of given type
	the name can be full name, with space between first name and last name
	if only the first name is given then a like clause is used to match
	if first name and last name is given then equality is used on first name and like on last name
	********/
	public function front_search_for_member_of_type_paged($search_data,$member_type,$start_offset,$num_to_fetch,&$data_arr,&$data_count,&$total_count){
		global $g_mc;
		
		$condition = "member_type='".$member_type."'";
		
		$tokens = explode(" ",$search_data);
		//how many tokens?
		$token_count = count($tokens);
		//we only consider the first two tokens, as first name and last name
		if($token_count > 1){
			$condition.=" and f_name='".$g_mc->view_to_db($tokens[0])."' and l_name like '".$g_mc->view_to_db($tokens[1])."%'";
		}else{
			if($token_count > 0){
				$condition.=" and f_name like '".$g_mc->view_to_db($tokens[0])."%'";
			}
		}
		
		
		
		////////////////////////////////////////
		//first we get the count
		$q = "select count(*) as cnt from ".TP."member where ".$condition;
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$row = mysql_fetch_assoc($res);
		$total_count = $row['cnt'];
		if(0==$total_count){
			return true;
		}
		////////////////////////////////////////
		//now we get the fields with paging
		$q = "select m.mem_id,m.f_name,m.l_name,m.designation,c.name as company_name from ".TP."member as m left join ".TP."company as c on(m.company_id=c.company_id) where ".$condition." order by f_name,l_name limit ".$start_offset.",".$num_to_fetch;
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$data_count = mysql_num_rows($res);
		if(0==$data_count){
			return true;
		}
		for($i=0;$i<$data_count;$i++){
			$data_arr[$i] = mysql_fetch_assoc($res);
			$data_arr[$i]['f_name'] = $g_mc->db_to_view($data_arr[$i]['f_name']);
			$data_arr[$i]['l_name'] = $g_mc->db_to_view($data_arr[$i]['l_name']);
			$data_arr[$i]['company_name'] = $g_mc->db_to_view($data_arr[$i]['company_name']);
		}
		return true;
	}
	/***
	get a random collegue of the given member. A collegue is a member of same type, work in same company,
	and is not a ghost
	**********/
	public function front_get_random_collegue($collegue_of_mem_id,&$data_arr,&$data_count){
		global $g_mc;
		//get the company and type of the member
		$q = "select company_id,member_type from ".TP."member where mem_id='".$collegue_of_mem_id."'";
		$res = mysql_query($q);
		if(!$res){
			
			return false;
		}
		$row = mysql_fetch_assoc($res);
		$this_company_id = $row['company_id'];
		$this_member_type = $row['member_type'];
		////////////////////////////////////////////////////////
		$q = "select m.mem_id,m.f_name,m.l_name,m.profile_img,m.designation,c.name as company_name from ".TP."member as m left join ".TP."company as c on(m.company_id=c.company_id) where mem_id!='".$collegue_of_mem_id."' and is_ghost='N' and m.company_id='".$this_company_id."' and member_type='".$this_member_type."' order by rand() limit 0,1";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$data_count = mysql_num_rows($res);
		if($data_count == 0){
			//no collegue
			return true;
		}
		///////////////////////
		$data_arr = mysql_fetch_assoc($res);
		$data_arr['f_name'] = $g_mc->db_to_view($data_arr['f_name']);
		$data_arr['l_name'] = $g_mc->db_to_view($data_arr['l_name']);
		$data_arr['company_name'] = $g_mc->db_to_view($data_arr['company_name']);
		return true;
	}
	/***
	get a random competitor of the given member. A competitor is a member of same type, does not
	work in the company of this user and is not a ghost
	*****/
	public function front_get_random_competitor($competitor_of_mem_id,&$data_arr,&$data_count){
		global $g_mc;
		//get the company id, and member type of the member
		$q = "select m.company_id,member_type from ".TP."member as m left join ".TP."company as c on(m.company_id=c.company_id) where mem_id='".$competitor_of_mem_id."'";
		$res = mysql_query($q);
		if(!$res){
			
			return false;
		}
		$row = mysql_fetch_assoc($res);
		$this_company_id = $row['company_id'];
		
		$this_member_type = $row['member_type'];
		////////////////////////////////////////////////////////
		$q = "select m.mem_id,m.f_name,m.l_name,m.profile_img,m.designation,c.name as company_name from ".TP."member as m left join ".TP."company as c on(m.company_id=c.company_id) where mem_id!='".$collegue_of_mem_id."' and is_ghost='N' and m.company_id!='".$this_company_id."' and member_type='".$this_member_type."' order by rand() limit 0,1";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$data_count = mysql_num_rows($res);
		if($data_count == 0){
			//no collegue
			return true;
		}
		///////////////////////
		$data_arr = mysql_fetch_assoc($res);
		$data_arr['f_name'] = $g_mc->db_to_view($data_arr['f_name']);
		$data_arr['l_name'] = $g_mc->db_to_view($data_arr['l_name']);
		$data_arr['company_name'] = $g_mc->db_to_view($data_arr['company_name']);
		return true;
	}
	
	/***
	sng:7/may/2010
	We also get the company id where this member works currently, in case we need to mark his member as colleague or competitor
	of the visiting member
	
	sng:4/jun/2010
	We also show posting country if it is there, so we need this field also
	*************/
	public function front_get_profile_data($member_id,&$data_arr){
		global $g_mc;
		$q = "select m.mem_id,m.member_type,m.f_name,m.l_name,m.profile_img,m.designation,m.company_id,m.year_joined,m.posting_country,c.name as company_name from ".TP."member as m left join ".TP."company as c on(m.company_id=c.company_id) where mem_id='".$member_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$data_count = mysql_num_rows($res);
		if($data_count == 0){
			//no such user
			return false;
		}
		///////////////////////
		$data_arr = mysql_fetch_assoc($res);
		$data_arr['f_name'] = $g_mc->db_to_view($data_arr['f_name']);
		$data_arr['l_name'] = $g_mc->db_to_view($data_arr['l_name']);
		$data_arr['company_name'] = $g_mc->db_to_view($data_arr['company_name']);
		///////////////////////////////////////
		return true;
	}
	
	public function front_recommend_colleague($mem_id,$colleague_id,&$msg){
		//check if this member has already added this colleague or not
		$q = "select count(*) as cnt from ".TP."recommend where mem_id='".$mem_id."' and recommended_mem_id='".$colleague_id."'";
		$res = mysql_query($q);
		if(!$res){
			return true;
		}
		$row = mysql_fetch_assoc($res);
		if($row['cnt']!=0){
			//already added
			$msg = "This colleague is there in your recommend list";
			return true;
		}
		////////////////////////
		//insert
		$q = "insert into ".TP."recommend set mem_id='".$mem_id."', recommended_mem_id='".$colleague_id."'";
		$result = mysql_query($q);
		if(!$result){
			return false;
		}
		//////////
		$msg = "Added to your recommend list";
		return true;
	}
	/******
	get the list of colleagues recommended by this member
	******/
	public function front_recommended_colleague_list($mem_id,&$data_arr,&$data_count){
		global $g_mc;
		
		$q = "SELECT r. * , m.f_name, m.l_name, m.designation, c.company_id, c.name AS company_name FROM ".TP."recommend AS r LEFT JOIN ".TP."member AS m ON ( r.recommended_mem_id = m.mem_id ) LEFT JOIN ".TP."company AS c ON ( m.company_id = c.company_id ) WHERE r.mem_id = '".$mem_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		/////////////////////////////////
		$data_count = mysql_num_rows($res);
		if(0==$data_count){
			return true;
		}
		//////////////////////////////////
		for($i=0;$i<$data_count;$i++){
			$data_arr[$i] = mysql_fetch_assoc($res);
			$data_arr[$i]['f_name'] = $g_mc->db_to_view($data_arr[$i]['f_name']);
			$data_arr[$i]['l_name'] = $g_mc->db_to_view($data_arr[$i]['l_name']);
			$data_arr[$i]['company_name'] = $g_mc->db_to_view($data_arr[$i]['company_name']);
		}
		return true;
	}
	
	/***
	delete the collegue recommended by this member
	***/
	public function delete_recommended($mem_id,$recommended_mem_id){
		$q = "delete from ".TP."recommend where mem_id='".$mem_id."' and recommended_mem_id='".$recommended_mem_id."'";
		$result = mysql_query($q);
		if(!$result){
			return false;
		}else{
			return true;
		}
	}
	
	/******
	get the list of members who has recommended this member
	******/
	public function front_recommended_by_list($mem_id,&$data_arr,&$data_count){
		global $g_mc;
		
		$q = "SELECT r.mem_id , m.f_name, m.l_name, m.designation, c.company_id, c.name AS company_name FROM ".TP."recommend AS r LEFT JOIN ".TP."member AS m ON ( r.mem_id = m.mem_id ) LEFT JOIN ".TP."company AS c ON ( m.company_id = c.company_id ) WHERE r.recommended_mem_id = '".$mem_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		/////////////////////////////////
		$data_count = mysql_num_rows($res);
		if(0==$data_count){
			return true;
		}
		//////////////////////////////////
		for($i=0;$i<$data_count;$i++){
			$data_arr[$i] = mysql_fetch_assoc($res);
			$data_arr[$i]['f_name'] = $g_mc->db_to_view($data_arr[$i]['f_name']);
			$data_arr[$i]['l_name'] = $g_mc->db_to_view($data_arr[$i]['l_name']);
			$data_arr[$i]['company_name'] = $g_mc->db_to_view($data_arr[$i]['company_name']);
		}
		return true;
	}
	
	/******
	get the list of competitors admired by this member
	******/
	public function front_admired_competitor_list($mem_id,&$data_arr,&$data_count){
		global $g_mc;
		
		$q = "SELECT a. * , m.f_name, m.l_name, m.designation, c.company_id, c.name AS company_name FROM ".TP."admire AS a LEFT JOIN ".TP."member AS m ON ( a.admired_mem_id = m.mem_id ) LEFT JOIN ".TP."company AS c ON ( m.company_id = c.company_id ) WHERE a.mem_id = '".$mem_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		/////////////////////////////////
		$data_count = mysql_num_rows($res);
		if(0==$data_count){
			return true;
		}
		//////////////////////////////////
		for($i=0;$i<$data_count;$i++){
			$data_arr[$i] = mysql_fetch_assoc($res);
			$data_arr[$i]['f_name'] = $g_mc->db_to_view($data_arr[$i]['f_name']);
			$data_arr[$i]['l_name'] = $g_mc->db_to_view($data_arr[$i]['l_name']);
			$data_arr[$i]['company_name'] = $g_mc->db_to_view($data_arr[$i]['company_name']);
		}
		return true;
	}
	
	/***
	delete the competitor admired by this member
	***/
	public function delete_admired($mem_id,$admired_mem_id){
		$q = "delete from ".TP."admire where mem_id='".$mem_id."' and admired_mem_id='".$admired_mem_id."'";
		$result = mysql_query($q);
		if(!$result){
			return false;
		}else{
			return true;
		}
	}
	
	/******
	get the list of competitors who admire this member
	******/
	public function front_admired_by_list($mem_id,&$data_arr,&$data_count){
		global $g_mc;
		
		$q = "SELECT a.mem_id , m.f_name, m.l_name, m.designation, c.company_id, c.name AS company_name FROM ".TP."admire AS a LEFT JOIN ".TP."member AS m ON ( a.mem_id = m.mem_id ) LEFT JOIN ".TP."company AS c ON ( m.company_id = c.company_id ) WHERE a.admired_mem_id = '".$mem_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		/////////////////////////////////
		$data_count = mysql_num_rows($res);
		if(0==$data_count){
			return true;
		}
		//////////////////////////////////
		for($i=0;$i<$data_count;$i++){
			$data_arr[$i] = mysql_fetch_assoc($res);
			$data_arr[$i]['f_name'] = $g_mc->db_to_view($data_arr[$i]['f_name']);
			$data_arr[$i]['l_name'] = $g_mc->db_to_view($data_arr[$i]['l_name']);
			$data_arr[$i]['company_name'] = $g_mc->db_to_view($data_arr[$i]['company_name']);
		}
		return true;
	}
	
	public function front_admire_competitor($mem_id,$competitor_id,&$msg){
		//check if this member has already added this competitor or not
		$q = "select count(*) as cnt from ".TP."admire where mem_id='".$mem_id."' and admired_mem_id='".$competitor_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$row = mysql_fetch_assoc($res);
		if($row['cnt']!=0){
			//already added
			$msg = "This competitor is there in your admire list";
			return true;
		}
		////////////////////////
		//insert
		$q = "insert into ".TP."admire set mem_id='".$mem_id."', admired_mem_id='".$competitor_id."'";
		$result = mysql_query($q);
		if(!$result){
			return false;
		}
		//////////
		$msg = "Added to your admire list";
		return true;
	}
	/***
	3/june/2010
	ordering by year from ascending
	also getting if of the member_work_history row since we now allow the member to delete
	entry from work history
	*******/
	public function front_prev_work_list($mem_id,&$data_arr,&$data_count){
		global $g_mc;
		$q = "select mem_id,h.company_id,h.id as work_id,designation,year_from,year_to,c.name as company_name from ".TP."member_work_history as h left join ".TP."company as c on(h.company_id=c.company_id) where h.mem_id='".$mem_id."' order by year_from";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		//////////////////////////////////
		$data_count = mysql_num_rows($res);
		if(0==$data_count){
			//no data, so no need to proceed
			return true;
		}
		//////////////////////////////////
		for($i=0;$i<$data_count;$i++){
			$data_arr[$i] = mysql_fetch_assoc($res);
			$data_arr[$i]['company_name'] = $g_mc->db_to_view($data_arr[$i]['company_name']);
		}
		return true;
		
	}
	/***
	3/june/2010
	delete a record from work history table. We use both id of member and the id of the row to be
	sure that we are deleting the record for that member and not of someone else's.
	We do not use company id since a member may work for the same company time to time and there will be multiple entries
	********/
	public function delete_prev_work_via_edit($mem_id,$work_id){
		$q = "delete from ".TP."member_work_history where id='".$work_id."' and mem_id='".$mem_id."'";
		$result = mysql_query($q);
		if(!$result){
			return false;
		}
		return true;
	}
	/***********************************************************************************
	sng:19/feb/2011
	We now change the forms a bit. We put more info in a form
	***********************/
	public function get_my_profile_for_edit($member_id,&$data_arr){
		global $g_mc;
		$q = "select f_name,l_name,home_email,profile_img from ".TP."member where mem_id='".$member_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		///////////////
		$cnt = mysql_num_rows($res);
		if(0==$cnt){
			//no such member
			return false;
		}
		////////////////
		$data_arr = mysql_fetch_assoc($res);
		$data_arr['f_name'] = $g_mc->db_to_view($data_arr['f_name']);
		$data_arr['l_name'] = $g_mc->db_to_view($data_arr['l_name']);
		
		return true;
	}
	/************
	sng:21/feb/2011
	This now does not include home email and photo
	****/
	public function update_my_profile_via_edit($member_id,$data_arr,&$validation_passed,&$err_arr){
		global $g_mc;
		require_once("classes/class.image_util.php");
		$g_img = new image_util();
		
		$validation_passed = true;
		
		if($data_arr['f_name']==""){
			$validation_passed = false;
			$err_arr['f_name'] = "Please specify your first name";
		}
		if($data_arr['l_name']==""){
			$validation_passed = false;
			$err_arr['l_name'] = "Please specify your last name";
		}
		
		if(!$validation_passed){
			return true;
		}
		///////////////////////////////
		
		/*************************
		now update the record
		/************************************/
		$q = "update ".TP."member set f_name='".$g_mc->view_to_db($data_arr['f_name'])."', l_name='".$g_mc->view_to_db($data_arr['l_name'])."' where mem_id='".$member_id."'";
		$result = mysql_query($q);
		if(!$result){
			return false;
		}
		$validation_passed = true;
		return true;
	}
	/*********
	sng:21/feb/2011
	This is for home email and photo
	******/
	public function update_my_profile_2_via_edit($member_id,$data_arr,$img_field_name,$image_destination_path,&$validation_passed,&$err_arr){
		
		require_once("classes/class.image_util.php");
		$g_img = new image_util();
		
		$validation_passed = true;
		
		
		
		if($data_arr['home_email']==""){
			$validation_passed = false;
			$err_arr['home_email'] = "Please specify your home email";
		}else{
			//check for uniqueness
			$exists = false;
			$success = $this->is_email_exists_for_other($member_id,$data_arr['home_email'],$exists);
			if(!$success){
				return false;
			}
			//////////
			if($exists){
				$validation_passed = false;
				$err_arr['home_email'] = "In use, please specify another";
			}
		}
		//photo may or may not be specified
		if(!$validation_passed){
			return true;
		}
		///////////////////////////////
		//check if image is specified
		if($_FILES[$img_field_name]['name']!=""){
			//get the current photo name if any
			$curr_img = "";
			$q = "select profile_img from ".TP."member where mem_id='".$member_id."'";
			$res = mysql_query($q);
			if(!$res){
				return false;
			}
			$row = mysql_fetch_assoc($res);
			$curr_img = $row['profile_img'];
			//now try to upload the image
			$upload_img_name = time()."_".clean_filename(basename($_FILES[$img_field_name]['name']));
			
			$upload_src = $_FILES[$img_field_name]['tmp_name'];
			/******************
			sng:28/sep/2012
			We now directly create the logo thumb. The function checks whether the uploaded img is image file or not
			****************/
			$success = $g_img->create_resized($upload_src,$image_destination_path."/thumbnails",$upload_img_name,$this->thumb_fit_width,$this->thumb_fit_height,false);
			
			if(!$success){
				return false;
			}
			//try to update the database for the image
			$q = "update ".TP."member set profile_img='".$upload_img_name."' where mem_id='".$member_id."'";
			$result = mysql_query($q);
			if(!$result){
				return false;
			}
			///////////////////
			//delete prev image if any
			if($curr_img != ""){
				/*************
				sng:28/sep/2012
				since we no longer create the larger image, no need to delete that. Just
				delete the thumb
				***********/
				unlink($image_destination_path."/thumbnails/".$curr_img);
			}
			//image taken care of
		}
		/*************************
		now update the record
		/************************************/
		$q = "update ".TP."member set home_email='".mysql_real_escape_string($data_arr['home_email'])."' where mem_id='".$member_id."'";
		$result = mysql_query($q);
		if(!$result){
			return false;
		}
		$validation_passed = true;
		return true;
	}
	/*********************************************************************************/
	/****
	used to edit profile of member. This is used to get profile data like first name, last name etc
	
	sng:22/jan/2011
	Now we change the work email from company edit, since work email is linked to my current firm. So here, no need for sending it.
	Instead, we fetch the work_email in get_company_for_edit()
	********/
	public function get_profile_for_edit($member_id,&$data_arr){
		global $g_mc;
		$q = "select f_name,l_name,home_email from ".TP."member where mem_id='".$member_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		///////////////
		$cnt = mysql_num_rows($res);
		if(0==$cnt){
			//no such member
			return false;
		}
		////////////////
		$data_arr = mysql_fetch_assoc($res);
		$data_arr['f_name'] = $g_mc->db_to_view($data_arr['f_name']);
		$data_arr['l_name'] = $g_mc->db_to_view($data_arr['l_name']);
		return true;
	}
	public function get_profile_photo_for_edit($member_id,&$data_arr){
		
		$q = "select profile_img from ".TP."member where mem_id='".$member_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		///////////////
		$cnt = mysql_num_rows($res);
		if(0==$cnt){
			//no such member
			return false;
		}
		////////////////
		$data_arr = mysql_fetch_assoc($res);
		
		return true;
	}
	public function update_profile_via_edit($member_id,$data_arr,&$validation_passed,&$err_arr){
		global $g_mc;
		$validation_passed = true;
		if($data_arr['f_name']==""){
			$validation_passed = false;
			$err_arr['f_name'] = "Please specify your first name";
		}
		if($data_arr['l_name']==""){
			$validation_passed = false;
			$err_arr['l_name'] = "Please specify your last name";
		}
		if($data_arr['home_email']==""){
			$validation_passed = false;
			$err_arr['home_email'] = "Please specify your home email";
		}else{
			//check for uniqueness
			$exists = false;
			$success = $this->is_email_exists_for_other($member_id,$data_arr['home_email'],$exists);
			if(!$success){
				return false;
			}
			//////////
			if($exists){
				$validation_passed = false;
				$err_arr['home_email'] = "In use, please specify another";
			}
		}
		/********************************************
		sng:22/jan/2011
		We no longer update work email in edit account. We do it in company edit
		*******************************************/
		if(!$validation_passed){
			return true;
		}
		///////////////////////////////
		//update
		/************************************************
		sng:22/jan/2011
		We do not chnage work email here, so no need to update member_company_change_log to log the change in work email
		***************************************************/
		/*************************
		now update the record
		sng:22/jan/2011
		We do not update work email here
		/************************************/
		$q = "update ".TP."member set f_name='".$g_mc->view_to_db($data_arr['f_name'])."', l_name='".$g_mc->view_to_db($data_arr['l_name'])."', home_email='".$data_arr['home_email']."' where mem_id='".$member_id."'";
		$result = mysql_query($q);
		if(!$result){
			return false;
		}
		$validation_passed = true;
		return true;
	}
	/**********************
	sng:28/sep/2012
	No longer used. The thing that we have in front end use update_my_profile_2_via_edit
	*******************/
	public function update_profile_photo_via_edit($member_id,$img_field_name,$image_destination_path,&$validation_passed,&$err_arr){
		return false;
		require_once("classes/class.image_util.php");
		$g_img = new image_util();
		
		$validation_passed = true;
		if($_FILES[$img_field_name]['name']==""){
			$validation_passed = false;
			$err_arr['profile_img'] = "Please specify an image";
		}
		if(!$validation_passed){
			return true;
		}
		//////////////////////////////////
		//get the current photo name if any
		$curr_img = "";
		$q = "select profile_img from ".TP."member where mem_id='".$member_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		///////////////
		$row = mysql_fetch_assoc($res);
		$curr_img = $row['profile_img'];
		///////////////////////////////////
		//now try to upload the image
		$upload_img_name = time()."_".clean_filename(basename($_FILES[$img_field_name]['name']));
		$upload_path = $image_destination_path."/".$upload_img_name;
		$upload_src = $_FILES[$img_field_name]['tmp_name'];
		/***********************************
		sng:18/may/2012
		***/
		$ok = $g_img->is_valid_image_file($upload_img_name);
		if(!$ok){
			return false;
		}
		/****************************/
		$success = move_uploaded_file($upload_src,$upload_path);
		if(!$success){
			return false;
		}
		////////////////////////////////
		//create thumbnail
		$success = $g_img->create_thumbnail($image_destination_path,$upload_img_name,$this->thumb_fit_width,$this->thumb_fit_height,$image_destination_path."/thumbnails",false);
		if(!$success){
			return false;
		}
		//////////////////////////////////////
		//update
		$q = "update ".TP."member set profile_img='".$upload_img_name."' where mem_id='".$member_id."'";
		$result = mysql_query($q);
		if(!$result){
			return false;
		}
		///////////////////
		//delete prev image if any
		if($curr_img != ""){
			unlink($image_destination_path."/".$curr_img);
			unlink($image_destination_path."/thumbnails".$curr_img);
		}
		return true;
	}
	
	/****
	used to edit company of member. This is used to get profile data like company, designation etc.
	We do not allow to change the date of join
	
	sng:22/jan/2011
	It may happen that the member has requested to change the company. The new company data, designation are hold in future_ fields
	till it is verified. In the company edit page, we show the requested data also.
	
	also, now when we allow member to edit the company data, we allow to specify the work email since validation link is sent in
	work email. Admin also checks the company name and work email to see if the request should be rejected or not.
	********/
	public function get_company_for_edit($member_id,&$data_arr){
		global $g_mc;
		$q = "select member_type,m.company_id,work_email,year_joined,designation,division,posting_country,future_company_name,future_work_email,future_year_joined,future_designation,future_division,future_country,c.name as firm_name from ".TP."member as m left join ".TP."company as c on(m.company_id=c.company_id) where mem_id='".$member_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		///////////////
		$cnt = mysql_num_rows($res);
		if(0==$cnt){
			//no such member
			return false;
		}
		////////////////
		$data_arr = mysql_fetch_assoc($res);
		$data_arr['firm_name'] = $g_mc->db_to_view($data_arr['firm_name']);
		$data_arr['future_company_name'] = $g_mc->db_to_view($data_arr['future_company_name']);
		return true;
	}
	public function update_company_via_edit($member_id,$data_arr,&$validation_passed,&$is_pending,&$is_favoured,&$err_arr){
		global $g_mc;
		$validation_passed = true;
		/******************************
		sng:24/jan/2011
		change to company or work email are not done till verified
		*************/
		$is_pending = false;
		$is_favoured = false;
		
		if($data_arr['firm_name']==""){
			$validation_passed = false;
			$err_arr['firm_name'] = "Please specify company name";
		}
		/****************************************************************************************
		sng:22/jan/2011
		We now update work email here, so we need validation
		********/
		if($data_arr['work_email']==""){
			$validation_passed = false;
			$err_arr['work_email'] = "Please specify your work email";
		}else{
			//check for uniqueness
			$exists = false;
			$success = $this->is_email_exists_for_other($member_id,$data_arr['work_email'],$exists);
			if(!$success){
				return false;
			}
			//////////
			if($exists){
				$validation_passed = false;
				$err_arr['work_email'] = "In use, please specify another";
			}
		}
		/**************************************************************************************/
		if($data_arr['designation']==""){
			$validation_passed = false;
			$err_arr['designation'] = "Please specify your designation";
		}
		if($data_arr['location']==""){
			$validation_passed = false;
			$err_arr['location'] = "Please specify your posting";
		}
		if($data_arr['division']==""){
			$validation_passed = false;
			$err_arr['division'] = "Please specify your division";
		}
		if(!$validation_passed){
			return true;
		}
		/********************************************************************************************
		sng:22/jan/2011
		We need to check whether the company and/or work email has changed or not. For that, we get
		the old company_id, work email
		**********/
		$curr_firm_id = 0;
		$future_firm_id = 0;
		$curr_work_email = "";
		$future_work_email = $data_arr['work_email'];
		
		$old_q = "select work_email,company_id from ".TP."member where mem_id='".$member_id."'";
		$old_q_res = mysql_query($old_q);
		if(!$old_q_res){
			return false;
		}
		$old_q_res_count = mysql_num_rows($old_q_res);
		if(0==$old_q_res_count){
			return false;
		}
		$old_q_res_row = mysql_fetch_assoc($old_q_res);
		
		$curr_firm_id = $old_q_res_row['company_id'];
		$curr_work_email = $old_q_res_row['work_email'];
		
		//the future company name is send as text. Get the id. It may happen that the company is not in the db.
		//In that case, do not create the company now, let the curr_firm_id remain 0
		if($data_arr['member_type']=="banker") $company_type = "bank";
		elseif($data_arr['member_type']=="lawyer") $company_type = "law firm";
		elseif($data_arr['member_type']=="company rep") $company_type = "company";
		/**************************************************
		sng:5/apr/2011
		data partner also associate with company
		*********/
		elseif($data_arr['member_type']=="data partner") $company_type="company";
		/********************************************/
		$company_found = false;
		$success = $this->company_id_from_name($data_arr['firm_name'],$company_type,$future_firm_id,$company_found);
		if(!$success){
			return false;
		}
		/***************************************************
		sng:24/jan/2011
		if company is not found, that means I am switching to a new company. As the future_firm_id is 0, the test will
		catch it
		
		We hold update of member data if firm is changed or work email is changed
		*****************************************/
		if(($future_firm_id != $curr_firm_id)||($future_work_email != $curr_work_email)){
			/************
			switching to new company, or work email, hold
			store the data in future fields
			future_validation_email_sent_on to default, to simulate a new request for which validation email has not been sent
			******/
			$is_pending = true;
			
			$q = "update ".TP."member set future_company_name='".$g_mc->view_to_db($data_arr['firm_name'])."',future_work_email='".$data_arr['work_email']."',future_year_joined='".date('Y')."',future_designation='".$data_arr['designation']."',future_division='".$data_arr['division']."',future_country='".$data_arr['location']."' ,future_requested_on='".date("Y-m-d H:i:s")."',future_validation_email_sent_on='0000-00-00 00:00:00',future_validation_email_sent_count='0' where mem_id='".$member_id."'";
			$res = mysql_query($q);
			if(!$res){
				return false;
			}
			//dash off an email to admin, use the one to notify of registration
			require_once("classes/class.sitesetup.php");
			global $g_site;
			$admin_data = array();
			$success = $g_site->get_site_emails($admin_data);
			if(!$success){
				//well, we will not hang a script just because we cannot send email
				$validation_passed = true;
				return true;
			}
			//echo "admin email";
			$admin_email = $admin_data['registration_notification_email'];
			$headers = "";
			$headers .= "From: ".$admin_email."\r\n";
			$to = $admin_email;
			$subject = "A data-cx.com member wishes to change the company or work email";
			/*************************************************************************
			sng:18/feb/2011
			We have to show admin the proposed change
			****************/
			$message = "A member wants to change the company or work email. The proposed changes are\r\n\r\n";
			$message.="New company: ".$g_mc->view_to_view($data_arr['firm_name'])."\r\n";
			$message.="New work email: ".$data_arr['work_email']."\r\n\r\n";
			$message.="Please login to admin panel to review.\r\n\r\n";
			/*************************************************************************/
			
			/******
			sng:12/oct/2010
			use the mailer class
			******/
			require_once("classes/class.mailer.php");
			$mailer = new mailer();
			$success = $mailer->mail($to,$subject,$message);
			/*****************************
			sng:28/jan/2011
			ok, we have held the update, the requested change are in future_ fields, we have notified admin.
			Now we check if the future_work_email is in favoured list, and that, the corresponding company name (given the member type)
			matches the future company or not, If so, act as if admin has accepted this change and fired an email.
			Note: The account is NOT updated till activation code is sent by the member.
			************************************/
			$q = "select member_type,future_company_name,future_work_email from ".TP."member where mem_id='".$member_id."'";
			$res = mysql_query($q);
			if(!$res){
				return false;
			}
			$row = mysql_fetch_assoc($res);
			$success = $this->is_work_email_favoured($row['member_type'],$row['future_work_email'],$row['future_company_name'],$is_favoured);
			if(!$success){
				return false;
			}
			if($is_favoured){
				//trigger sending of activation email
				$success = $this->admin_accept_company_email_change_request($member_id);
				if(!$success){
					return false;
				}
			}
			
			$validation_passed = true;
			return true;
		}else{
			//same company, same work email. So nothing to validate. go ahead with the change
			//update
			$q = "update ".TP."member set designation='".$data_arr['designation']."', posting_country='".$data_arr['location']."', division='".$data_arr['division']."' where mem_id='".$member_id."'";
			$result = mysql_query($q);
			if(!$result){
				return false;
			}
		}
		/**********************************************************************************************************/
		return true;
	}
	
	/******************
	sng:25/jan/2011
	The company / work email change request has been accepted and the member has clicked the validation link
	*******************/
	public function update_company_work_email_change($mem_id,$token,&$is_updated,&$msg){
		//see if the data is there, with a change request
		//we also require member type and curr firm id curr work email so that we can check if this is update of company or not or is it for email
		//we also need year joined, designation for the existing company so that we can update history if this is change in company
		$q = "select company_id,year_joined,designation,member_type,future_company_name,work_email,future_work_email,future_year_joined,future_designation,future_division,future_country,future_validation_token from ".TP."member where mem_id='".$mem_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$row = mysql_fetch_assoc($res);
		if($row['future_company_name'] == ""){
			//change was not requested
			$is_updated = false;
			$msg = "You have not requested any change or maybe, the change request has been deleted since you have not clicked the email link sent to you.";
			return true;
		}
		if($row['future_validation_token']!=$token){
			//maybe trying to use an old acceptance token
			$is_updated = false;
			$msg = "The validation link seems to be malformed. Try submitting the change request again.";
			return true;
		}
		
		$curr_firm_id = $row['company_id'];
		$mem_type = $row['member_type'];
		$future_firm_name = $row['future_company_name'];
		$future_firm_id = 0;
		$future_company_found = false;
		
		$curr_work_email = $row['work_email'];
		$future_work_email = $row['future_work_email'];
		
		$curr_firm_join_year = $row['year_joined'];
		$future_firm_join_year = date('Y');
		
		$curr_designation = $row['designation'];
		
		//all ok, update
		$q = "update ".TP."member set work_email='".$row['future_work_email']."',designation='".$row['future_designation']."', posting_country='".$row['future_country']."', division='".$row['future_division']."'";
		//the future company name is send as text. Get the id. It may happen that the company is not in the db.
		//In that case, do not create the company now, let the curr_firm_id remain 0
		if($mem_type=="banker") $company_type = "bank";
		elseif($mem_type=="lawyer") $company_type = "law firm";
		elseif($mem_type=="company rep") $company_type = "company";
		/**************************************************
		sng:5/apr/2011
		data partner also associate with company
		*********/
		elseif($mem_type=="data partner") $company_type="company";
		/********************************************/
		
		$success = $this->company_id_from_name($future_firm_name,$company_type,$future_firm_id,$future_company_found);
		if(!$success){
			return false;
		}
		//if company is not found, that means I am switching to a new company. As the future_firm_id is 0, the test will
		//catch it
		if($future_firm_id != $curr_firm_id){
			//we are changing company
			//if the future company is not found, create it
			if(!$future_company_found){
				$success = $this->create_company_for_member($future_firm_name,$company_type,$future_firm_id);
				if(!$success){
					return false;
				}
				//future firm created
			}
			$q.=",company_id='".$future_firm_id."',year_joined='".$future_firm_join_year."'";
		}
		$q.=" where mem_id='".$mem_id."'";
		$result = mysql_query($q);
		if(!$result){
			return false;
		}
		//remove the future data since the change has been done
		$remove_q = "update ".TP."member set future_company_name='',future_work_email='',future_year_joined='0000',future_designation='',future_division='',future_country='',future_requested_on='0000-00-00 00:00:00',future_validation_token='',future_validation_email_sent_on='0000-00-00 00:00:00',future_validation_email_sent_count='0' where mem_id='".$mem_id."'";
		mysql_query($remove_q);
		//if this create error, what can be done?
		
		//if this is change in company then update member's work history
		if($future_firm_id != $curr_firm_id){
			$q = "insert into ".TP."member_work_history set mem_id='".$mem_id."',member_type='".$mem_type."',company_id='".$curr_firm_id."',designation='".$curr_designation."',year_from='".$curr_firm_join_year."',year_to='".date("Y")."'";
			mysql_query($q);
			//even if this fails, no issue, since the member can change this from history
		}
		
		//this is either change in work email or company or both, we need to update the change log
		$log_q = "insert into ".TP."member_company_change_log set mem_id='".$mem_id."',prev_company_id='".$curr_firm_id."',company_id='".$future_firm_id."',prev_work_email='".$curr_work_email."',work_email='".$future_work_email."',changed_on='".date("Y-m-d")."'";
		mysql_query($log_q);
		//never mind if this fails
		$is_updated = true;
		$msg = "Your company / work email has been updated";
		return true;
	}
	
	public function add_prev_work_via_edit($mem_id,$data_arr,&$validation_passed,&$err_arr){
		global $g_mc;
		$validation_passed = true;
		if($data_arr['firm_name']==""){
			$validation_passed = false;
			$err_arr['firm_name'] = "Please specify company name";
		}
		if($data_arr['designation']==""){
			$validation_passed = false;
			$err_arr['designation'] = "Please specify your designation";
		}
		if($data_arr['year_from']==""){
			$validation_passed = false;
			$err_arr['year_from'] = "Please specify year from";
		}
		if($data_arr['year_to']==""){
			$validation_passed = false;
			$err_arr['year_to'] = "Please specify year to";
		}
		if(($data_arr['year_from']!="")&&($data_arr['year_to']!="")){
			if($data_arr['year_to'] < $data_arr['year_from']){
				$validation_passed = false;
				$err_arr['year_to'] = "Cannot be less than year from";
			}
		}
		if(!$validation_passed){
			return true;
		}
		///////////////////////////////
		//the company may be there or may not be there. If not there, create it
		$company_id = 0;
		$company_found = false;
		if($data_arr['member_type']=="banker") $company_type = "bank";
		elseif($data_arr['member_type']=="lawyer") $company_type = "law firm";
		elseif($data_arr['member_type']=="company rep") $company_type = "company";
		/**************************************************
		sng:5/apr/2011
		data partner also associate with company
		*********/
		elseif($data_arr['member_type']=="data partner") $company_type="company";
		/********************************************/
		
		$success = $this->company_id_from_name($data_arr['firm_name'],$company_type,$company_id,$company_found);
		if(!$success){
			return false;
		}
		////////////////////////
		if($company_found){
			
		}else{
			$success = $this->create_company_for_member($data_arr['firm_name'],$company_type,$company_id);
			if(!$success){
				return false;
			}
			///////////////////////
			//company created
		}
		///////////////////////////////////////
		$q = "insert into ".TP."member_work_history set mem_id='".$mem_id."', member_type='".$data_arr['member_type']."', company_id='".$company_id."', designation='".$data_arr['designation']."', year_from='".$data_arr['year_from']."', year_to='".$data_arr['year_to']."'";
		$result = mysql_query($q);
		if(!$result){
			return false;
		}else{
			return true;
		}
	}
	
	public function get_company_description_for_edit($company_id,&$data_arr){
		global $g_mc;
		$q = "select name as company_name, brief_desc from ".TP."company where company_id='".$company_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$cnt = mysql_num_rows($res);
		if($cnt == 0){
			//no such company
			return false;
		}
		///////////////////////
		$row = mysql_fetch_assoc($res);
		$data_arr['company_name'] = $g_mc->db_to_view($row['company_name']);
		$data_arr['brief_desc'] = $g_mc->db_to_view($row['brief_desc']);
		return true;
	}
	public function update_company_description_via_edit($company_id,$data_arr,$updated_by,&$validation_passed,&$err_arr){
		global $g_mc;
		$validation_passed = true;
		if($data_arr['brief_desc']==""){
			$validation_passed = false;
			$err_arr['brief_desc'] = "Please specify the description";
		}
		if(!$validation_passed){
			return true;
		}
		////////////////////////////
		$curr_time = date("Y-m-d H:i:s");
		$q = "update ".TP."company set brief_desc='".$g_mc->view_to_db($data_arr['brief_desc'])."',brief_desc_updated_by='".$updated_by."',brief_desc_updated_on='".$curr_time."' where company_id='".$company_id."'";
		$result = mysql_query($q);
		if(!$result){
			return false;
		}
		$validation_passed = true;
		return true;
	}
	///////////////////////FRONT CODE END/////////////////////////////////
	private function company_id_from_name($company_name,$company_type,&$company_id,&$found){
		//since this is internal, we assume that caller has magic quoted the name
		$q = "select company_id from ".TP."company where type='".$company_type."' and name='".$company_name."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$cnt = mysql_num_rows($res);
		if(0 == $cnt){
			$found = false;
			return true;
		}
		//found
		$row = mysql_fetch_assoc($res);
		$found = true;
		$company_id = $row['company_id'];
		return true;
	}
	
	private function create_company_for_member($company_name,$company_type,&$company_id){
		global $g_mc;
		$q = "insert into ".TP."company set name='".$g_mc->view_to_db($company_name)."', type='".$company_type."'";
		$result = mysql_query($q);
		if(!$result){
			return false;
		}
		////////////////////
		$company_id = mysql_insert_id();
		return true;
	}
	//////////////////////////////////////////////////////////////////////////
	public function ajax_get_members_for_delegates($name,$mem_type,$company_id,$num_to_fetch,&$data_arr,&$data_cnt){
		global $g_mc;
		$q = "select mem_id,f_name,l_name,work_email,designation from ".TP."member where member_type='".$mem_type."' and is_ghost='N' and company_id='".$company_id."' and concat( f_name, ' ', l_name ) LIKE '".$name."%'
 limit 0,".$num_to_fetch;
		
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		///////////////////////
		$data_cnt = mysql_num_rows($res);
		if(0==$data_cnt){
			return true;
		}
		///////////////////
		for($i=0;$i<$data_cnt;$i++){
			$data_arr[$i] = mysql_fetch_assoc($res);
			$data_arr[$i]['f_name'] = $g_mc->db_to_view($data_arr[$i]['f_name']);
			$data_arr[$i]['l_name'] = $g_mc->db_to_view($data_arr[$i]['l_name']);
		}
		return true;
	}
	
	public function ajax_get_members_for_deal_team($name,$mem_type,$company_id,$num_to_fetch,&$data_arr,&$data_cnt){
		global $g_mc;
		$q = "select mem_id,f_name,l_name,work_email,designation from ".TP."member where member_type='".$mem_type."' and company_id='".$company_id."' and concat( f_name, ' ', l_name ) LIKE '".$name."%'
 limit 0,".$num_to_fetch;
		
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		///////////////////////
		$data_cnt = mysql_num_rows($res);
		if(0==$data_cnt){
			return true;
		}
		///////////////////
		for($i=0;$i<$data_cnt;$i++){
			$data_arr[$i] = mysql_fetch_assoc($res);
			$data_arr[$i]['f_name'] = $g_mc->db_to_view($data_arr[$i]['f_name']);
			$data_arr[$i]['l_name'] = $g_mc->db_to_view($data_arr[$i]['l_name']);
		}
		return true;
	}
	
	/***
	appoint a delegate
	delegate_for_id: the id of the member who is appointing a delegate, for whom colleague is acting as delegate
	colleague_id: the member who will act as delegate
	It may happen that delegate_mem_id is empty
	******/
	public function add_delegate_via_edit($delegate_for_id,$colleague_id,&$validation_passed,&$err_arr){
		$validation_passed = true;
		if($colleague_id == ""){
			$validation_passed = false;
			$err_arr['colleague_name'] = "Colleague not found";
		}
		if(!$validation_passed){
			return true;
		}
		/////////////////////
		//check if this delegate is already added for this member or not
		$q = "select count(*) as cnt from ".TP."member_delegate where mem_id='".$colleague_id."' and delegate_for_id='".$delegate_for_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		/////////////
		$row = mysql_fetch_assoc($res);
		if($row['cnt'] > 0){
			$validation_passed = false;
			$err_arr['colleague_name'] = "already acting as delegate";
		}
		if(!$validation_passed){
			return true;
		}
		////////////////////////////////////////
		//now insert
		$q = "insert into ".TP."member_delegate set mem_id='".$colleague_id."',delegate_for_id='".$delegate_for_id."'";
		$result = mysql_query($q);
		if(!$result){
			return false;
		}
		$validation_passed = true;
		return true;
	}
	/***
	delete a delegate
	delegate_for_id: the id of the member who has appointed the delegate, for whom colleague is acting as delegate
	colleague_id: the member who is acting as delegate
	***/
	public function delete_delegate_via_edit($delegate_for_id,$colleague_id){
		$q = "delete from ".TP."member_delegate where mem_id='".$colleague_id."' and delegate_for_id ='".$delegate_for_id."'";
		$result = mysql_query($q);
		if(!$result){
			return false;
		}
		return true;
	}
	
	/****
	get the list of delegates for a member
	for_mem_id: member id for whom the delegate list is fetched
	****/
	public function front_get_delegate_list($for_mem_id,&$data_arr,&$data_count){
		global $g_mc;
		$q = "select d.mem_id,f_name,l_name,work_email,designation from ".TP."member_delegate as d left join ".TP."member as m on(d.mem_id=m.mem_id) where delegate_for_id='".$for_mem_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$data_count = mysql_num_rows($res);
		if(0==$data_count){
			return true;
		}
		////////////////
		for($i=0;$i<$data_count;$i++){
			$data_arr[$i] = mysql_fetch_assoc($res);
			$data_arr[$i]['f_name'] = $g_mc->db_to_view($data_arr[$i]['f_name']);
			$data_arr[$i]['l_name'] = $g_mc->db_to_view($data_arr[$i]['l_name']);
		}
		return true;
	}
	/****
	get the list of members for which this member is acting as delegate
	*********/
	public function front_get_delegate_for_list($mem_id,&$data_arr,&$data_count){
		global $g_mc;
		$q = "select d.delegate_for_id,f_name,l_name,work_email,designation from ".TP."member_delegate as d left join ".TP."member as m on(d.delegate_for_id=m.mem_id) where d.mem_id='".$mem_id."'";
		$res = mysql_query($q);
		if(!$res){
			
			return false;
		}
		$data_count = mysql_num_rows($res);
		if(0==$data_count){
			return true;
		}
		////////////////
		for($i=0;$i<$data_count;$i++){
			$data_arr[$i] = mysql_fetch_assoc($res);
			$data_arr[$i]['f_name'] = $g_mc->db_to_view($data_arr[$i]['f_name']);
			$data_arr[$i]['l_name'] = $g_mc->db_to_view($data_arr[$i]['l_name']);
		}
		return true;
	}
	
	/***
	sng:3/jun/2010
	support for ghost member creation by admin. Here we take company name and member type and posting country (unlike ghost account creation
	by a colleague)
	
	sng:4/jun/2010
	We take the duplication check from create_ghost_account
	***/
	public function create_ghost_member($data_arr,&$validation_passed,&$err_arr){
		global $g_mc;
		//validation
		$validation_passed = true;
		$first_name = $g_mc->view_to_db($data_arr['first_name']);
		$last_name = $g_mc->view_to_db($data_arr['last_name']);
		$firm_name = $g_mc->view_to_db($data_arr['firm_name']);
		if($first_name == ""){
			$err_arr['first_name'] = "Please specify the first name";
			$validation_passed = false;
		}
		if($last_name == ""){
			$err_arr['last_name'] = "Please specify the last name";
			$validation_passed = false;
		}
		
		if($data_arr['type'] == ""){
			$err_arr['type'] = "Please specify the membership type";
			$validation_passed = false;
		}
		if($firm_name == ""){
			$err_arr['firm_name'] = "Please specify the firm where you work";
			$validation_passed = false;
		}else{
			//get the firm id if exists
			if($data_arr['type']=="banker") $company_type = "bank";
			elseif($data_arr['type']=="lawyer") $company_type = "law firm";
			elseif($data_arr['type']=="company rep") $company_type = "company";
			/**************************************************
			sng:5/apr/2011
			data partner also associate with company
			*********/
			elseif($data_arr['type']=="data partner") $company_type="company";
			/********************************************/
			$company_id = 0;
			$company_found = false;
			//this function require magic quoted data
			$success = $this->company_id_from_name($firm_name,$company_type,$company_id,$company_found);
			if(!$success){
				return false;
			}
			if(!$company_found){
				$validation_passed = false;
				$err_arr['firm_name'] = "Could not find the company in company database";
			}
		}
		if($data_arr['designation'] == ""){
			$err_arr['designation'] = "Please specify your designation";
			$validation_passed = false;
		}
		if($data_arr['location'] == ""){
			$err_arr['location'] = "Please specify the location";
			$validation_passed = false;
		}
		if(!$validation_passed){
			return true;
		}
		//////////////////////////////////////////////////////////////
		//we have the data, so do a duplicate check
		//same name, type, company
		$q = "select count(*) as cnt from ".TP."member where f_name='".$first_name."' and l_name='".$last_name."' and member_type='".$data_arr['type']."' and company_id='".$company_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$row = mysql_fetch_assoc($res);
		if($row['cnt'] > 0){
			//duplicate found
			$validation_passed = false;
			$err_arr['last_name'] = "This member exists";
			//no need to proceed here
			return true;
		}
		//not found in member table, check in membershhip request table
		$q = "select count(*) as cnt from ".TP."registration_request where f_name='".$first_name."' and l_name='".$last_name."' and member_type='".$data_arr['type']."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$row = mysql_fetch_assoc($res);
		if($row['cnt'] > 0){
			//duplicate found
			$validation_passed = false;
			$err_arr['last_name'] = "This member might be waiting for membership acceptance";
			//no need to proceed here
			return true;
		}
		///////////////////////////////////////////
		//insert the member data
		$ins_q = "insert into ".TP."member set f_name='".$first_name."',l_name='".$last_name."',member_type='".$data_arr['type']."',company_id='".$company_id."',designation='".$data_arr['designation']."',posting_country='".$data_arr['location']."',is_ghost='Y',blocked='N'";
		$result = mysql_query($ins_q);
		if(!$result){
			//echo mysql_error();
			return false;
		}
		return true;
	}
	
	public function create_ghost_member_from_registration($data_arr,&$msg){
		global $g_mc;
		
		$first_name = $g_mc->view_to_db($data_arr['f_name']);
		$last_name = $g_mc->view_to_db($data_arr['l_name']);
		
		//////////////////////////////////////////////////////////////
		//we have the data, so do a duplicate check
		//same name, type, company
		$q = "select count(*) as cnt from ".TP."member where f_name='".$first_name."' and l_name='".$last_name."' and member_type='".$data_arr['member_type']."' and company_id='".$data_arr['company_id']."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$row = mysql_fetch_assoc($res);
		if($row['cnt'] > 0){
			//duplicate found
			
			$msg = "This member exists";
			//no need to proceed here
			return true;
		}
		
		///////////////////////////////////////////
		//insert the member data
		$ins_q = "insert into ".TP."member set f_name='".$first_name."',l_name='".$last_name."',member_type='".$data_arr['member_type']."',company_id='".$data_arr['company_id']."',designation='".$data_arr['designation']."',posting_country='".$data_arr['posting_country']."',is_ghost='Y',blocked='N'";
		$result = mysql_query($ins_q);
		if(!$result){
			//echo mysql_error();
			return false;
		}
		//delete the registration data
		$del_q = "delete from ".TP."registration_request where uid='".$data_arr['uid']."'";
		mysql_query($del_q);
		$msg = "Ghost created from registration request.";
		return true;
	}
	
	public function update_ghost_member($data_arr,&$validation_passed,&$err_arr){
		global $g_mc;
		//validation
		$validation_passed = true;
		$f_name = $g_mc->view_to_db($data_arr['f_name']);
		$l_name = $g_mc->view_to_db($data_arr['l_name']);
		$company_name = $g_mc->view_to_db($data_arr['company_name']);
		if($f_name == ""){
			$err_arr['f_name'] = "Please specify the first name";
			$validation_passed = false;
		}
		if($l_name == ""){
			$err_arr['l_name'] = "Please specify the last name";
			$validation_passed = false;
		}
		
		if($data_arr['member_type'] == ""){
			$err_arr['member_type'] = "Please specify the membership type";
			$validation_passed = false;
		}
		if($company_name == ""){
			$err_arr['company_name'] = "Please specify the firm where you work";
			$validation_passed = false;
		}else{
			//get the firm id if exists
			if($data_arr['member_type']=="banker") $company_type = "bank";
			elseif($data_arr['member_type']=="lawyer") $company_type = "law firm";
			elseif($data_arr['member_type']=="company rep") $company_type = "company";
			/**************************************************
			sng:5/apr/2011
			data partner also associate with company
			*********/
			elseif($data_arr['member_type']=="data partner") $company_type="company";
			/********************************************/
			$company_id = 0;
			$company_found = false;
			//this function require magic quoted data
			$success = $this->company_id_from_name($company_name,$company_type,$company_id,$company_found);
			if(!$success){
				return false;
			}
			if(!$company_found){
				$validation_passed = false;
				$err_arr['company_name'] = "Could not find the company in company database";
			}
		}
		if($data_arr['designation'] == ""){
			$err_arr['designation'] = "Please specify your designation";
			$validation_passed = false;
		}
		if($data_arr['posting_country'] == ""){
			$err_arr['posting_country'] = "Please specify the location";
			$validation_passed = false;
		}
		if(!$validation_passed){
			return true;
		}
		//////////////////////////////////////////////////////////////
		//we have the data, so do a duplicate check, but excluding this member
		//same name, type, company
		$q = "select count(*) as cnt from ".TP."member where mem_id!='".$data_arr['mem_id']."' and f_name='".$f_name."' and l_name='".$l_name."' and member_type='".$data_arr['member_type']."' and company_id='".$company_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$row = mysql_fetch_assoc($res);
		if($row['cnt'] > 0){
			//duplicate found
			$validation_passed = false;
			$err_arr['l_name'] = "This member exists";
			//no need to proceed here
			return true;
		}
		//not found in member table, check in membershhip request table
		$q = "select count(*) as cnt from ".TP."registration_request where f_name='".$f_name."' and l_name='".$l_name."' and member_type='".$data_arr['member_type']."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$row = mysql_fetch_assoc($res);
		if($row['cnt'] > 0){
			//duplicate found
			$validation_passed = false;
			$err_arr['l_name'] = "This member might be waiting for membership acceptance";
			//no need to proceed here
			return true;
		}
		///////////////////////////////////////////
		//update
		$ins_q = "update ".TP."member set f_name='".$f_name."',l_name='".$l_name."',member_type='".$data_arr['member_type']."',company_id='".$company_id."',designation='".$data_arr['designation']."',posting_country='".$data_arr['posting_country']."' where mem_id='".$data_arr['mem_id']."'";
		$result = mysql_query($ins_q);
		if(!$result){
			//echo mysql_error();
			return false;
		}
		return true;
	}
	/////////////////////////////////////////////////////////////////////////////////////////////////////
	/***
	function to create a ghost member. This is used when a member is adding a colleague
	to a deal team but that colleague is not a member yet.
	At the minimum, we need the first name, last name, designation, member type, company id.
	
	We are not taking any work email or home email so duplicate check will be bit complicated.
	We check on all the fields, first name, last name, member type, company id on both member table and
	membership request table
	
	sng:7/jul/2010: Client requested that we take the email address of the colleague. We take the work email address.
	This is mandatory, and use that in the validation also.
	********/
	public function create_ghost_account($data_arr,&$validation_passed,&$ghost_mem_id,&$err_arr){
		global $g_mc;
		$f_name = $g_mc->view_to_db($data_arr['f_name']);
		$l_name = $g_mc->view_to_db($data_arr['l_name']);
		
		$validation_passed = true;
		//validation
		if($f_name==""){
			$validation_passed = false;
			$err_arr['f_name'] = "Please specify first name";
		}
		if($l_name==""){
			$validation_passed = false;
			$err_arr['l_name'] = "Please specify last name";
		}
		if($data_arr['work_email']==""){
			$validation_passed = false;
			$err_arr['work_email'] = "Please specify work email";
		}
		if($data_arr['designation']==""){
			$validation_passed = false;
			$err_arr['designation'] = "Please specify designation";
		}
		//since it is members who create ghost account, we do not check on company id and member type
		if(!$validation_passed){
			return true;
		}
		//the data are there, so check for duplicate
		$q = "select count(*) as cnt from ".TP."member where f_name='".$f_name."' and l_name='".$l_name."' and member_type='".$data_arr['member_type']."' and company_id='".$data_arr['company_id']."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$row = mysql_fetch_assoc($res);
		if($row['cnt'] > 0){
			//duplicate found
			$validation_passed = false;
			$err_arr['designation'] = "This seems to be a duplicate entry";
			//no need to proceed here
			return true;
		}
		/***
		sng:7/jul/2010
		check by email
		**/
		$q = "select count(*) as cnt from ".TP."member where work_email='".$data_arr['work_email']."' OR home_email='".$data_arr['work_email']."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$row = mysql_fetch_assoc($res);
		if($row['cnt'] > 0){
			//duplicate found
			$validation_passed = false;
			$err_arr['work_email'] = "A member having this email exists";
			//no need to proceed here
			return true;
		}
		//not found in member table, check in membershhip request table
		$q = "select count(*) as cnt from ".TP."registration_request where f_name='".$f_name."' and l_name='".$l_name."' and member_type='".$data_arr['member_type']."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$row = mysql_fetch_assoc($res);
		if($row['cnt'] > 0){
			//duplicate found
			$validation_passed = false;
			$err_arr['designation'] = "This colleague might be waiting for membership acceptance";
			//no need to proceed here
			return true;
		}
		/***
		sng:7/jul/2010
		check by email
		**/
		$q = "select count(*) as cnt from ".TP."registration_request where work_email='".$data_arr['work_email']."' OR home_email='".$data_arr['work_email']."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$row = mysql_fetch_assoc($res);
		if($row['cnt'] > 0){
			//duplicate found
			$validation_passed = false;
			$err_arr['work_email'] = "A member having this email exists";
			//no need to proceed here
			return true;
		}
		//no duplicate, so insert
		/***
		sng:7/jul/2010
		Now we accept work email so insert that also
		***/
		$q = "insert into ".TP."member set f_name='".$f_name."', l_name='".$l_name."',work_email='".$data_arr['work_email']."',member_type='".$data_arr['member_type']."',company_id='".$data_arr['company_id']."',designation='".$data_arr['designation']."',is_ghost='Y',blocked='N'";
		$result = mysql_query($q);
		if(!$res){
			return false;
		}
		////////////////
		$validation_passed = true;
		$ghost_mem_id = mysql_insert_id();
		return true;
	}
	
	public function admin_get_all_member_by_type($f_name,$l_name,$member_type,&$data_arr,&$data_count){
		global $g_mc;
		
		$q = "select mem_id,f_name,l_name,work_email,member_type,m.company_id,designation,is_ghost,c.name as company_name from ".TP."member as m left join ".TP."company as c on(m.company_id=c.company_id) where member_type='".$member_type."'";
		if($f_name!=""){
			$q.=" and f_name like '".$f_name."%'";
		}
		if($l_name!=""){
			$q.=" and l_name like '".$l_name."%'";
		}
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$data_count = mysql_num_rows($res);
		if(0==$data_count){
			return true;
		}
		//////////////////////////////////////
		for($i=0;$i<$data_count;$i++){
			$data_arr[$i] = mysql_fetch_assoc($res);
			$data_arr[$i]['f_name'] = $g_mc->db_to_view($data_arr[$i]['f_name']);
			$data_arr[$i]['l_name'] = $g_mc->db_to_view($data_arr[$i]['l_name']);
			$data_arr[$i]['company_name'] = $g_mc->db_to_view($data_arr[$i]['company_name']);
		}
		return true;
	}
	/**********************************************************************************************
	sng:14/dec/2011
	During registration, some emails cannot be used as work email (ex @gmail.com). We allow admin to manage those
	*********/
	public function add_unfavoured_email($data_arr,&$validation_passed,&$err_arr){
		//validation
		$validation_passed = true;
		
		
		if($data_arr['email_suffix']==""){
			$err_arr['email_suffix'] = "Please specify the email suffix";
			$validation_passed = false;
		}
		if(!$validation_passed){
			//no need to proceed
			return true;
		}
		//basic validation passed
		//check if already present or not
		$q = "select count(*) as cnt from ".TP."registration_unfavoured_emails where email_suffix='".$data_arr['email_suffix']."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$row = mysql_fetch_assoc($res);
		if($row['cnt'] > 0){
			//suffix present
			$err_arr['email_suffix'] = "This email suffix is present, specify another";
			$validation_passed = false;
		}
		if(!$validation_passed){
			//no need to proceed
			return true;
		}
		//insert
		$q = "insert into ".TP."registration_unfavoured_emails set email_suffix='".$data_arr['email_suffix']."'";
		$result = mysql_query($q);
		if(!$result){
			return false;
		}
		//inserted
		return true;
	}
	
	public function list_unfavoured_email_paged($start_offset,$num_to_fetch,&$data_arr,&$data_count){
		global $g_mc;
		
		$q = "select * from ".TP."registration_unfavoured_emails limit ".$start_offset.",".$num_to_fetch;
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$data_count = mysql_num_rows($res);
			if($data_count == 0){
			//no recs
			return true;
		}
		//recs so
		while($row = mysql_fetch_assoc($res)){
			$data_arr[] = $row;
		}
		return true;
	}
	
	public function delete_unfavoured_email($id,&$msg){
		$q = "delete from ".TP."registration_unfavoured_emails where id='".$id."'";
		$result = mysql_query($q);
		if(!$result){
			
			return false;
		}
		//deleted
		$msg = "Email suffix deleted";
		return true;
	}
	/*************************************************************************************************/
	
	/*********************************************************************************************************
	sng:17/jul/2010
	During registration, some emails are given special treatment. We allow admin to manage those.
	We also note the type of the company and the company id for verification purpose.
	
	There can be more than one firm sharing the email suffix and a firm may have more than one emails.
	
	We send both the company_id and firm_name. If firm_name is blank then the field has not been filled.
	Otherwise, if company_id is not there then no company is selected from list.
	***********************/
	public function add_registration_special_email($data_arr,&$validation_passed,&$err_arr){
		//validation
		$validation_passed = true;
		if($data_arr['company_type']==""){
			$err_arr['company_type'] = "Please specify company type";
			$validation_passed = false;
		}
		
		if($data_arr['firm_name']==""){
			$err_arr['company_id'] = "Please specify the company";
			$validation_passed = false;
		}else{
			if($data_arr['company_id']==""){
				$err_arr['company_id'] = "Please select a company";
				$validation_passed = false;
			}
		}
		
		if($data_arr['email_suffix']==""){
			$err_arr['email_suffix'] = "Please specify the email suffix";
			$validation_passed = false;
		}
		if(!$validation_passed){
			//no need to proceed
			return true;
		}
		//basic validation passed
		//check if already present or not
		$q = "select count(*) as cnt from ".TP."registration_favoured where email_suffix='".$data_arr['email_suffix']."' and company_id='".$data_arr['company_id']."' and company_type='".$data_arr['company_type']."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$row = mysql_fetch_assoc($res);
		if($row['cnt'] > 0){
			//suffix present
			$err_arr['email_suffix'] = "This email suffix is present for the company of the selected type, specify another";
			$validation_passed = false;
		}
		if(!$validation_passed){
			//no need to proceed
			return true;
		}
		//insert
		$q = "insert into ".TP."registration_favoured set email_suffix='".$data_arr['email_suffix']."', company_id='".$data_arr['company_id']."', company_type='".$data_arr['company_type']."'";
		$result = mysql_query($q);
		if(!$result){
			return false;
		}
		//inserted
		return true;
	}
	
	public function edit_registration_special_email($id,$data_arr,&$validation_passed,&$err_arr){
		//validation
		$validation_passed = true;
		if($data_arr['company_type']==""){
			$err_arr['company_type'] = "Please specify company type";
			$validation_passed = false;
		}
		
		if($data_arr['firm_name']==""){
			$err_arr['company_id'] = "Please specify the company";
			$validation_passed = false;
		}else{
			if($data_arr['company_id']==""){
				$err_arr['company_id'] = "Please select a company";
				$validation_passed = false;
			}
		}
		
		if($data_arr['email_suffix']==""){
			$err_arr['email_suffix'] = "Please specify the email suffix";
			$validation_passed = false;
		}
		if(!$validation_passed){
			//no need to proceed
			return true;
		}
		//basic validation passed
		//check if already present or not, excluding this one
		$q = "select count(*) as cnt from ".TP."registration_favoured where email_suffix='".$data_arr['email_suffix']."' and company_id='".$data_arr['company_id']."' and company_type='".$data_arr['company_type']."' and id!='".$id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$row = mysql_fetch_assoc($res);
		if($row['cnt'] > 0){
			//suffix present
			$err_arr['email_suffix'] = "This email suffix is present for the company of the selected type, specify another";
			$validation_passed = false;
		}
		if(!$validation_passed){
			//no need to proceed
			return true;
		}
		//update
		$q = "update ".TP."registration_favoured set email_suffix='".$data_arr['email_suffix']."', company_id='".$data_arr['company_id']."', company_type='".$data_arr['company_type']."' where id='".$id."'";
		$result = mysql_query($q);
		if(!$result){
			return false;
		}
		//updated
		return true;
	}
	
	public function list_registration_special_email_paged($start_offset,$num_to_fetch,&$data_arr,&$data_count){
		global $g_mc;
		
		$q = "select f.*,c.name from ".TP."registration_favoured as f left join ".TP."company as c on(f.company_id=c.company_id) limit ".$start_offset.",".$num_to_fetch;
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$data_count = mysql_num_rows($res);
			if($data_count == 0){
			//no recs
			return true;
		}
		//recs so
		while($row = mysql_fetch_assoc($res)){
			$row['name'] = $g_mc->db_to_view($row['name']);
			$data_arr[] = $row;
		}
		return true;
	}
	
	public function get_registration_special_email($id,&$data){
		global $g_mc;
		$q = "select f.*,c.name from ".TP."registration_favoured as f left join ".TP."company as c on(f.company_id=c.company_id) where id='".$id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$data_count = mysql_num_rows($res);
			if($data_count == 0){
			//no recs, this cannot be
			return false;
		}
		$data = mysql_fetch_assoc($res);
		$data['name'] = $g_mc->db_to_view($data['name']);
		return true;
	}
	
	public function delete_registration_special_email($id,&$msg){
		$q = "delete from ".TP."registration_favoured where id='".$id."'";
		$result = mysql_query($q);
		if(!$result){
			
			return false;
		}
		//deleted
		$msg = "Email suffix deleted";
		return true;
	}
	
	/*****************************
	sng:7/mar/2012
	We really need a way to check whether the member should be considered 'privileged' or 'favoured' or not.
	Basically we check the current company and the work email suffix
	*****************************/
	public function is_member_favoured($mem_id,&$is_favoured){
		$db = new db();
		$q = "select work_email,member_type,company_id from ".TP."member where mem_id='".$mem_id."'";
		$ok = $db->select_query($q);
		if(!$ok){
			return false;
		}
		if(!$db->has_row()){
			//no such member, no question of favourite
			$is_favoured = false;
			return true;
		}
		$row = $db->get_row();
		$work_email = $row['work_email'];
		//explode by @
		$tokens = explode("@",$work_email);
		//we need the second token. If that is not there, then is_favoured is treated as false
		if(count($tokens)<2){
			$is_favoured = false;
			return true;
		}
		$work_email_suffix = "@".$tokens[1];
		$company_id = $row['company_id'];
		$company_type = company_type_from_membership_type($row['member_type']);
		//now search, match on all the fields
		$q = "select count(*) as cnt from ".TP."registration_favoured where email_suffix='".$work_email_suffix."' and company_id='".$company_id."' and company_type='".$company_type."'";
		
		$ok = $db->select_query($q);
		if(!$ok){
			return false;
		}
		$row = $db->get_row();
		if($row['cnt']==0){
			//not present
			$is_favoured = false;
		}else{
			$is_favoured = true;
		}
		return true;
	}
	/****
	sng:27/july/2010
	**********/
	public function is_work_email_favoured($member_type,$work_email,$company_name,&$is_favoured){
		global $g_mc;
		//explode by @
		$tokens = explode("@",$work_email);
		//we need the second token. If that is not there, then is_favoured is treated as false
		if(count($tokens)<2){
			$is_favoured = false;
			return true;
		}
		$work_email_suffix = "@".$tokens[1];
		
		//now search for this, exact match
		if($member_type=="banker") $company_type="bank";
		if($member_type=="lawyer") $company_type="law firm";
		if($member_type=="company rep") $company_type="company";
		/**************************************************
		sng:5/apr/2011
		data partner also associate with company
		*********/
		if($member_type=="data partner") $company_type="company";
		/********************************************/
		
		//get the company id from the company name and type
		$q = "select company_id from ".TP."company where name='".$g_mc->view_to_db($company_name)."' and type='".$company_type."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		//it may happen that the company is not present, in which case no point in searching for favourite
		$res_count = mysql_num_rows($res);
		if(0==$res_count){
			$is_favoured = false;
			return true;
		}
		$row = mysql_fetch_assoc($res);
		$company_id = $row['company_id'];
		
		//now search, match on all the fields
		$q = "select count(*) as cnt from ".TP."registration_favoured where email_suffix='".$work_email_suffix."' and company_id='".$company_id."' and company_type='".$company_type."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$row = mysql_fetch_assoc($res);
		if($row['cnt']==0){
			//not present
			$is_favoured = false;
		}else{
			$is_favoured = true;
		}
		return true;
	}
	
	
	
	/********************
	sng:14/dec/2011
	We need to prevent registration using public emails like gmail.com
	********/
	public function is_work_email_unfavoured($work_email,&$is_unfavoured){
		
		//explode by @
		$tokens = explode("@",$work_email);
		//we need the second token. If that is not there, then is_unfavoured is treated as true
		if(count($tokens)<2){
			$is_unfavoured = true;
			return true;
		}
		$work_email_suffix = "@".$tokens[1];
		
		//now search for this, exact match
	
		$q = "select count(*) as cnt from ".TP."registration_unfavoured_emails where email_suffix='".mysql_real_escape_string($work_email_suffix)."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$row = mysql_fetch_assoc($res);
		if($row['cnt']==0){
			//not present
			$is_unfavoured = false;
		}else{
			$is_unfavoured = true;
		}
		return true;
	}
	/****************************************************************************/
	public function admin_member_profile_change_history_paged($start,$num_to_fetch,&$data_arr,&$data_count){
		global $g_mc;
		
		$q = "select h.*,m.f_name,m.l_name,prev_c.name as prev_company_name,curr_c.name as curr_company_name from ".TP."member_company_change_log as h left join ".TP."member as m on(h.mem_id=m.mem_id) left join ".TP."company as  prev_c on(h.prev_company_id=prev_c.company_id) left join ".TP."company as curr_c on(h.company_id=curr_c.company_id) order by h.id desc limit ".$start.",".$num_to_fetch;
		
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$data_count = mysql_num_rows($res);
		if(0==$data_count){
			return true;
		}
		//////////////////////////////////////
		for($i=0;$i<$data_count;$i++){
			$data_arr[$i] = mysql_fetch_assoc($res);
			$data_arr[$i]['f_name'] = $g_mc->db_to_view($data_arr[$i]['f_name']);
			$data_arr[$i]['l_name'] = $g_mc->db_to_view($data_arr[$i]['l_name']);
			$data_arr[$i]['prev_company_name'] = $g_mc->db_to_view($data_arr[$i]['prev_company_name']);
			$data_arr[$i]['curr_company_name'] = $g_mc->db_to_view($data_arr[$i]['curr_company_name']);
		}
		return true;
	}
	
	/***************
	sng: 23/nov/2010
	remove all trace of a member
	**********/
	public function delete_member($mem_id,&$mem_deleted,&$message){
		$mem_deleted = false;
		/***********************************
		it may happen that mmt jobs are running. so we see if any jobs by this member are pending or 'in progress'
		If so, we refrain from deletion and send a message
		*********/
		$q = "select count(*) as cnt from ".TP."top_search_request where mem_id='".$mem_id."' AND (status='pending' OR status='in progress')";
		$res = mysql_query($q);
		$row = mysql_fetch_assoc($res);
		if($row['cnt']>0){
			$message = "Cannot delete member since this member has unfinished mmt jobs";
			return true;
		}
		/***************
		no unfinished jobs, so delete top search requests, hits, error, helper
		********/
		require_once("classes/class.preset.php");
		$g_preset->delete_finished_top_search_requests_of_member($mem_id);
		/***********************************
		delete from admire list my records (I admire someone or some one admires me)
		**********************/
		$q = "delete from ".TP."admire where mem_id='".$mem_id."'";
		mysql_query($q);
		$q = "delete from ".TP."admire where admired_mem_id='".$mem_id."'";
		mysql_query($q);
		/****************************
		delete from chosen logos but do not delete the logos itself (others might be using those logos)
		*********/
		$q = "delete from ".TP."chosen_logos where mem_id='".$mem_id."'";
		mysql_query($q);
		/****************************
		delete from favourite tombstone but not the tombstone
		*******/
		$q = "delete from ".TP."favorite_tombstones where member_id='".$mem_id."'";
		mysql_query($q);
		/**********************************
		delete from company change log
		*******/
		$q = "delete from ".TP."member_company_change_log where mem_id='".$mem_id."'";
		mysql_query($q);
		/***********************************
		delete from work history
		*********/
		$q = "delete from ".TP."member_work_history where mem_id='".$mem_id."'";
		mysql_query($q);
		/***********************************
		delete from recomment list all my records (I recommend someone or someone recommend me)
		*************/
		$q = "delete from ".TP."recommend where mem_id='".$mem_id."'";
		mysql_query($q);
		$q = "delete from ".TP."recommend where recommended_mem_id='".$mem_id."'";
		mysql_query($q);
		/***********************************
		delete from delegate (where I am acting as delegate or I have appointed a delegate)
		************/
		$q = "delete from ".TP."member_delegate where mem_id='".$mem_id."'";
		mysql_query($q);
		$q = "delete from ".TP."member_delegate where delegate_for_id='".$mem_id."'";
		mysql_query($q);
		/***********************************
		delete from saved searches.
		Mihai: Does anything else needs to be deleted associated with this?
		*********/
		$q = "delete from ".TP."saved_searches where member_id='".$mem_id."'";
		mysql_query($q);
		/*************************************
		for transaction suggested, we do not delete the suggestion, just erase the member id (so now it become annonymous suggestion
		**********/
		$q = "update ".TP."transaction_suggested set suggested_by='0' where suggested_by='".$mem_id."'";
		mysql_query($q);
		/***************************************
		for transaction note suggestions, we do not delete the note but erase the member id
		sng:20/mar/2012
		Now we do not have transaction_note_suggestions. we now have tombstone_transaction_edit_suggestion_note
		
		sng:30/apr/2012
		Now we have transaction_note_suggestions
		setting suggested_by to 0 make is a suggestion by admin
		*************/
		$q = "update ".TP."transaction_note_suggestions set suggested_by='0' where suggested_by='".$mem_id."'";
		mysql_query($q);
		/**************************************
		for transaction error report, we do not delete the report but erase the member id
		*************/
		$q = "update ".TP."transaction_error_reports set reported_by='0' where reported_by='".$mem_id."'";
		mysql_query($q);
		/*************************************
		for transaction partner members, there may be records where I have flagged. For those, erase the member id
		******************/
		$q = "update ".TP."transaction_partner_members set flagged_by='0' where flagged_by='".$mem_id."'";
		mysql_query($q);
		/************************************
		now find the deals in which this member was a partner member and remove
		Here we require transaction class
		********/
		$q = "select transaction_id,partner_id,member_id from ".TP."transaction_partner_members where member_id='".$mem_id."'";
		$res = mysql_query($q);
		$mem_removed = "false";
		$msg = "";
		require_once("classes/class.transaction.php");
		while($row = mysql_fetch_assoc($res)){
			$deal_id = $row['transaction_id'];
			$deal_partner_id = $row['partner_id'];
			$g_trans->remove_deal_partner_team_member($deal_id,$deal_partner_id,$mem_id,$mem_removed,$msg);
		}
		/**********
		delete profile images
		*******/
		$q = "select profile_img from ".TP."member where mem_id='".$mem_id."'";
		$res = mysql_query($q);
		$row = mysql_fetch_assoc($res);
		$curr_img = $row['profile_img'];
		if($curr_img != ""){
			unlink(FILE_PATH."/uploaded_img/profile/".$curr_img);
			unlink(FILE_PATH."/uploaded_img/profile/thumbnails/".$curr_img);
		}
		/***************
		sng:1/mar/2012
		delete from watchlist
		update suggestion, set mem to 0
		update transaction_files_disputes, set mem to 0
		update transaction_files set mem to 0
		
		delete from transaction_discussion_watchlist
		update transaction_discussion, set mem to 0 since it is a hassle to delete it
		
		update transaction_case_studies_disputes set mem to 0
		update transaction_case_studies set mem to 0
		
		update company_suggestions set mem to 0
		
		delete from preferred_logos
		
		delete from onestop and 2weeks from now requests
		
		update transaction set mem to 0. That makes the deal uploaded by admin but what can we do?
		do not change the admin_verified and is_active flag
		******************/
		$q = "update ".TP."transaction set added_by_mem_id='0'";
		mysql_query($q);
		
		$q = "delete from ".TP."onestop_requests where userID='".$mem_id."'";
		mysql_query($q);
		
		$q = "delete from ".TP."2weeksnow_requests where userid='".$mem_id."'";
		mysql_query($q);
		
		$q = "delete from ".TP."preferred_logos where mem_id='".$mem_id."'";
		mysql_query($q);
		
		$q = "update ".TP."company_suggestions set suggested_by='0'";
		mysql_query($q);
		
		$q = "update ".TP."transaction_case_studies set mem_id='0'";
		mysql_query($q);
		
		$q = "update ".TP."transaction_case_studies_disputes set mem_id='0'";
		mysql_query($q);
		
		$q = "update ".TP."transaction_discussion set posting_member_id='0'";
		mysql_query($q);
		
		$q = "update ".TP."transaction_files set mem_id='0'";
		mysql_query($q);
		
		$q = "update ".TP."transaction_files_disputes set mem_id='0'";
		mysql_query($q);
		
		$q = "update ".TP."transaction_suggestions set suggested_by='0'";
		mysql_query($q);
		
		$q = "delete from ".TP."transaction_watchlist where mem_id='".$mem_id."'";
		mysql_query($q);
		
		$q = "delete from ".TP."transaction_discussion_watchlist where mem_id='".$mem_id."'";
		mysql_query($q);
		/***********************************
		delete from member
		********/
		$q = "delete from ".TP."member where mem_id='".$mem_id."'";
		mysql_query($q);
		$mem_deleted = true;
		return true;
	} 
}
$g_mem = new member();
?>