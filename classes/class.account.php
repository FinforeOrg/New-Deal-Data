<?php
/********
created by sng on 17/march/2010

This class has the methods to authenticate admin and site members
**********/
class account{
	
	/***
	authenticate the super admin user
	login_name: login name
	password: password
	is_authenticated: ref to send whether authenticated or not. True: authenticated, False, not authenticated
	msg: ref to send message
	return false on fatal error
	********/
	public function authenticate_sa($login_name,$password,&$is_authenticated,&$msg){
		if(login_name == ""){
			$msg = "Please specify login name/ password";
			$is_authenticated = false;
			return true;
		}
		$q = "SELECT * from ".TP."superadmin WHERE login_name='".$login_name."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		////////////////
		$cnt = mysql_num_rows($res);
		if(0 == $cnt){
			//no such login name
			$is_authenticated = false;
			$msg = "Incorrect login name / password";
			return true;
		}
		////////////////////
		$row = mysql_fetch_assoc($res);
		if($password == $row['password']){
			//ok
			$is_authenticated = true;
			$_SESSION['is_sa'] = true;
			return true;
		}else{
			//wrong pass
			$is_authenticated = false;
			$msg = "Incorrect login name / password";
			return true;
		}
	}
	
	public function authenticate_admin($login_name,$password,&$is_authenticated,&$msg){
		if(login_name == ""){
			$msg = "Please specify login name/ password";
			$is_authenticated = false;
			return true;
		}
		$q = "SELECT * from ".TP."admins WHERE login_name='".$login_name."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		////////////////
		$cnt = mysql_num_rows($res);
		if(0 == $cnt){
			//no such login name
			$is_authenticated = false;
			$msg = "Incorrect login name / password";
			return true;
		}
		////////////////////
		$row = mysql_fetch_assoc($res);
		if($password == $row['password']){
			//check if active or inactive
			if($row['is_active']=='N'){
				$is_authenticated = false;
				$msg = "Your account has been deactivated";
				return true;
			}else{
				//password ok, account active
				$is_authenticated = true;
				$_SESSION['is_admin'] = true;
				$_SESSION['admin_id'] = $row['id'];
				return true;
			}	
		}else{
			//wrong pass
			$is_authenticated = false;
			$msg = "Incorrect login name / password";
			return true;
		}
	}
	
	/***
	sng:13/apr/2010
	We are putting company_id in session so that we may get the company detail or the deals made by the company of the user quickly
	
	sng:20/apr/2010
	If the member is marked as ghost, do not allow to login
	
	sng:4/may/2010
	if this member has been appointed as delegate, store a flag
	
	sng:9/jun/2010
	if the user wants to remenber the login, then, upon successful authentication, set the userid in a cookie
	named mytombstones_id. But if remember login is false, then, we remove the cookie(never mind whether it was set before or not)
	**********/
	public function authenticate_site_member($login_email,$password,$remember_login,&$is_authenticated,&$err_arr){
		//validation
		$is_authenticated = true;
		
		if($login_email == ""){
			$err_arr['login_email'] = "Please specify login email";
			$is_authenticated = false;
		}
		
		if($password == ""){
			$err_arr['password'] = "Please specify the password";
			$is_authenticated = false;
		}
		
		if(($login_email!="")&&($password!="")){
			//match
			/*************************
			sng:9/feb/2011
			Client said: member cannot login with home email
			$q = "SELECT mem_id, f_name, l_name, password, work_email, home_email, member_type,company_id, is_ghost,blocked from ".TP."member WHERE work_email='".$login_email."' or home_email='".$login_email."'";
			********************/
			$q = "SELECT mem_id, f_name, l_name, password, work_email, home_email, member_type,company_id, is_ghost,blocked from ".TP."member WHERE work_email='".$login_email."'";
			$res = mysql_query($q);
			if(!$res){
				return false;
			} 
			$cnt = mysql_num_rows($res);
			if(0 == $cnt){
				//no such login emails
				/***
				sng:11/jun/2010
				check if the email is in membership request table, in which case, we show another
				error message
				*******/
				$rq = "select count(*) as rcnt from ".TP."registration_request where work_email='".$login_email."' or home_email='".$login_email."'";
				$rq_res = mysql_query($rq);
				if(!$rq_res){
					return false;
				}
				$rq_res_row = mysql_fetch_assoc($rq_res);
				if($rq_res_row['rcnt'] > 0){
					$is_authenticated = false;
					$err_arr['password'] = "Please wait until you receive your welcome email, or if received, click the activation link to activate the account first.";
					return true;
				}
				//and if not found above, then
				$is_authenticated = false;
				$err_arr['password'] = "Incorrect login email / password";
				//no need to proceed further 
				return true;
			} 
			//row is there, check password
			$row = mysql_fetch_assoc($res);
			if($password != $row['password']){
				$is_authenticated = false;
				$err_arr['password'] = "Incorrect login email / password";
				//no need to proceed further
				return true;
			}      
			//password match, so authenticated, but, is it blocked
			if($row['blocked']=='Y'){
				$is_authenticated = false;
				$err_arr['password'] = "Your account is suspended currently";
				//no need to proceed further
				return true;
			}
			//not blocked
			/***
			sng:20/apr/2010
			is it ghost, then do not authenticate
			*****/
			if($row['is_ghost']=='Y'){
				$is_authenticated = false;
				$err_arr['password'] = "Your account either does not exists or has been deleted";
				//no need to proceed further
				return true;
			}
			//not ghost
			$is_authenticated = true;
			/***
			sng:4/may/2010
			We need to check whether this member is appointed as delegate or not
			if delegate, we need to set a flag and also keep the member id in another field (in case
			the delegate switch identity)
			*****/
			$q_delegate = "select count(*) as cnt from ".TP."member_delegate where mem_id='".$row['mem_id']."'";
			$q_delegate_res = mysql_query($q_delegate);
			if(!$q_delegate_res){
				return false;
			}
			$q_delegate_res_row = mysql_fetch_assoc($q_delegate_res);
			//set the session data
			$_SESSION['is_member'] = true;
			$_SESSION['mem_id'] = $row['mem_id'];
			$_SESSION['f_name'] = $row['f_name'];
			$_SESSION['l_name'] = $row['l_name'];
			$_SESSION['work_email'] = $row['work_email'];
			$_SESSION['home_email'] = $row['home_email'];
			$_SESSION['member_type'] = $row['member_type'];
			$_SESSION['company_id'] = $row['company_id'];
			if($q_delegate_res_row['cnt'] == 0){
				$_SESSION['is_delegate'] = false;
			}else{
				$_SESSION['is_delegate'] = true;
				$_SESSION['real_mem_id'] = $row['mem_id'];
			}
			//if user wants to remember login, set the user id in a cookie
			if($remember_login){
				setcookie("mytombstones_mem_id",$row['mem_id']);
			}else{
				//expire cookie
				setcookie("mytombstones_mem_id","",time()-60*60*24);
			}
			//////
			return true;
		}else{
			//either login or password is missing, we already set the error msg and flag
			return true;
		}
	}
	/***
	sng:9/jun/2010
	Now we allow user to rememnber their login. That way, when they visit login page, the email and password is
	prefilled. We need a function to get that from mem_id
	*******/
	public function get_login_credential($mem_id,&$work_email,&$password){
		$q = "select work_email,password from ".TP."member where mem_id='".$mem_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$cnt = mysql_num_rows($res);
		if(0==$cnt){
			//not found, send blank data
			$work_email = "";
			$password = "";
			return true;
		}
		$row = mysql_fetch_assoc($res);
		$work_email = $row['work_email'];
		$password = $row['password'];
		return true;
	}
	/***
	sng:4/may/2010
	This is for delegate when they assume identity of another member.
	We check whether the delegate can assume the identity and then update the session vars
	We keep the is_delegate to true;
	******/
	public function switch_identity_for_delegate($delegate_mem_id,$assume_identity_of,&$switch_accepted){
		$q = "select count(*) as cnt from ".TP."member_delegate where mem_id='".$delegate_mem_id."' and delegate_for_id='".$assume_identity_of."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$row = mysql_fetch_assoc($res);
		if($row['cnt'] == 0){
			//this member is not appointed as a delegate of the second party so
			$switch_accepted = false;
			return true;
		}
		/////////////
		//appointed as delegate so switch.
		//for that, get the relevant data of the member $assume_identity_of and put in session
		$q = "select mem_id,f_name,l_name,work_email,home_email,member_type,company_id from ".TP."member where mem_id='".$assume_identity_of."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$row = mysql_fetch_assoc($res);
		$_SESSION['is_member'] = true;
		$_SESSION['mem_id'] = $row['mem_id'];
		$_SESSION['f_name'] = $row['f_name'];
		$_SESSION['l_name'] = $row['l_name'];
		$_SESSION['work_email'] = $row['work_email'];
		$_SESSION['home_email'] = $row['home_email'];
		$_SESSION['member_type'] = $row['member_type'];
		$_SESSION['company_id'] = $row['company_id'];
		//no need to change is_delegate or real_mem_id
		$switch_accepted = true;
		return true;
	}
	
	/****
	sng:4/may/2010
	If we allow a delegate to assume identity of another member then there has to be a way to switch to self identity
	*******/
	public function switch_to_self($delegate_real_id){
		
		$q = "select mem_id,f_name,l_name,work_email,home_email,member_type,company_id from ".TP."member where mem_id='".$delegate_real_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$row = mysql_fetch_assoc($res);
		$_SESSION['is_member'] = true;
		$_SESSION['mem_id'] = $row['mem_id'];
		$_SESSION['f_name'] = $row['f_name'];
		$_SESSION['l_name'] = $row['l_name'];
		$_SESSION['work_email'] = $row['work_email'];
		$_SESSION['home_email'] = $row['home_email'];
		$_SESSION['member_type'] = $row['member_type'];
		$_SESSION['company_id'] = $row['company_id'];
		//no need to change is_delegate or real_mem_id
		$switch_accepted = true;
		return true;
	}
	
	public function check_sa(){
		if(!(isset($_SESSION['is_sa']))||($_SESSION['is_sa']!=true)){
			header("Location: login.php");
			exit;
		}
	}
	public function check_admin(){
		if(!(isset($_SESSION['is_admin']))||($_SESSION['is_admin']!=true)){
			header("Location: login.php");
			exit;
		}
	}
	
	/***
	sng:11/mar/2011
	Just check whether the admin is logged in or not, do not send anywhere
	*******/
	public function is_admin_logged(){
		if(!(isset($_SESSION['is_admin']))||($_SESSION['is_admin']!=true)){
			return false;
		}else{
			return true;
		}
	}
	
	public function check_site_member(){
		if(!(isset($_SESSION['mem_id']))||($_SESSION['mem_id']=="")){
			/****
			sng:21/mar/2011
			since we have a login page, why not redirect there
			header("Location: index.php");
			***/
			header("Location: login.php");
			exit;
		}
	}
	/***
	sng:14/apr/2010
	Just check whether the member is logged in or not, do not send anywhere
	*******/
	public function is_site_member_logged(){
		if(!(isset($_SESSION['mem_id']))||($_SESSION['mem_id']=="")){
			return false;
		}else{
			return true;
		}
	}
	
	/***
	change the password of super admin
	***/
	public function change_sa_password($curr_password,$new_password,$retype_new_password,&$validation_passed,&$err_arr){
		//validation
		//get the current password as stored in db
		$q = "select password from ".TP."superadmin";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$row = mysql_fetch_assoc($res);
		$stored_password = $row['password'];
		///////////////////////////////////////////////
		$validation_passed = true;
		if($curr_password==""){
			$validation_passed = false;
			$err_arr['password'] = "Please specify the current password";
		}else{
			if($curr_password!=$stored_password){
				$validation_passed = false;
				$err_arr['password'] = "Current password in incorrect";
			}
		}
		if($new_password==""){
			$validation_passed = false;
			$err_arr['newpassword'] = "Please specify the new password";
		}
		if($retype_new_password==""){
			$validation_passed = false;
			$err_arr['renewpassword'] = "Please retype the new password";
		}
		if(($new_password!="")&&($retype_new_password!="")){
			if($new_password!=$retype_new_password){
				$validation_passed = false;
				$err_arr['renewpassword'] = "The new password and the retyped new password do not match";
			}
		}
		if(!$validation_passed){
			return true;
		}
		//validation passed, so update
		$q = "update ".TP."superadmin set password='".$new_password."'";
		$result = mysql_query($q);
		if($result){
			return true;
		}else{
			return false;
		}
	}
	
	public function change_admin_password($admin_id,$curr_password,$new_password,$retype_new_password,&$validation_passed,&$err_arr){
		//validation
		//get the current password as stored in db
		$q = "select password from ".TP."admins where id='".$admin_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$row = mysql_fetch_assoc($res);
		$stored_password = $row['password'];
		///////////////////////////////////////////////
		$validation_passed = true;
		if($curr_password==""){
			$validation_passed = false;
			$err_arr['password'] = "Please specify the current password";
		}else{
			if($curr_password!=$stored_password){
				$validation_passed = false;
				$err_arr['password'] = "Current password in incorrect";
			}
		}
		if($new_password==""){
			$validation_passed = false;
			$err_arr['newpassword'] = "Please specify the new password";
		}
		if($retype_new_password==""){
			$validation_passed = false;
			$err_arr['renewpassword'] = "Please retype the new password";
		}
		if(($new_password!="")&&($retype_new_password!="")){
			if($new_password!=$retype_new_password){
				$validation_passed = false;
				$err_arr['renewpassword'] = "The new password and the retyped new password do not match";
			}
		}
		if(!$validation_passed){
			return true;
		}
		//validation passed, so update
		$q = "update ".TP."admins set password='".$new_password."' where id='".$admin_id."'";
		$result = mysql_query($q);
		if($result){
			return true;
		}else{
			return false;
		}
	}
	
	public function change_site_member_password($member_id,$curr_password,$new_password,$retype_new_password,&$validation_passed,&$err_arr){
		//validation
		//get the current password as stored in db
		$q = "select password from ".TP."member where mem_id='".$member_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$row = mysql_fetch_assoc($res);
		$stored_password = $row['password'];
		///////////////////////////////////////////////
		$validation_passed = true;
		if($curr_password==""){
			$validation_passed = false;
			$err_arr['curr_password'] = "Please specify the current password";
		}else{
			if($curr_password!=$stored_password){
				$validation_passed = false;
				$err_arr['curr_password'] = "Current password in incorrect";
			}
		}
		if($new_password==""){
			$validation_passed = false;
			$err_arr['new_password'] = "Please specify the new password";
		}
		if($retype_new_password==""){
			$validation_passed = false;
			$err_arr['re_password'] = "Please retype the new password";
		}
		if(($new_password!="")&&($retype_new_password!="")){
			if($new_password!=$retype_new_password){
				$validation_passed = false;
				$err_arr['re_password'] = "The new password and the retyped new password do not match";
			}
		}
		if(!$validation_passed){
			return true;
		}
		//validation passed, so update
		$q = "update ".TP."member set password='".$new_password."' where mem_id='".$member_id."'";
		$result = mysql_query($q);
		if($result){
			return true;
		}else{
			return false;
		}
	}
	
	public function email_password_of_sa(&$msg){
		/******
		sng:12/oct/2010
		use the mailer class
		******/
		require_once("classes/class.mailer.php");
		$mailer = new mailer();
		//get the password and email it to email specified
		$q = "select * from ".TP."superadmin";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$row = mysql_fetch_assoc($res);
		$subject = "super admin password of deal-data.com";
		$to = $row['email'];
		$headers = "From: ".$row['email']."\r\n";
		/*$headers = "From: bugsng@gmail.com\r\n";*/
		$body = "You deal-data.com superadmin login data\r\n";
		$body.="Login name: ".$row['login_name']."\r\n";
		$body.="Password: ".$row['password']."\r\n";
		//$success = mail($to,$subject,$body,$headers);
		$success = $mailer->mail($to,$subject,$body);
		if($success){
			$msg = "The password has been emailed to ".$to;
			return true;
		}else{
			return false;
		}
	}
	
	public function get_email_of_sa(&$data_arr){
		$q = "select email from ".TP."superadmin";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$row = mysql_fetch_assoc($res);
		$data_arr['email'] = $row['email'];
		return true;
	}
	public function get_email_of_admin($admin_id,&$data_arr){
		$q = "select email from ".TP."admins where id='".$admin_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$row = mysql_fetch_assoc($res);
		$data_arr['email'] = $row['email'];
		return true;
	}
	
	public function change_email_of_sa($data_arr,&$validation_passed,&$err_arr){
		$validation_passed = true;
		if($data_arr['email']==""){
			$validation_passed = false;
			$err_arr['email'] = "Please specify the email";
		}
		if(!$validation_passed){
			return true;
		}
		///////////////////////
		//passed so
		$q = "update ".TP."superadmin set email='".$data_arr['email']."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$validation_passed = true;
		return true;
	}
	
	public function change_email_of_admin($admin_id,$data_arr,&$validation_passed,&$err_arr){
		$validation_passed = true;
		if($data_arr['email']==""){
			$validation_passed = false;
			$err_arr['email'] = "Please specify the email";
		}
		if(!$validation_passed){
			return true;
		}
		///////////////////////
		//passed so
		$q = "update ".TP."admins set email='".$data_arr['email']."' where id='".$admin_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$validation_passed = true;
		return true;
	}
	
	public function admin_logout(){
		$_SESSION = array();
		// If it's desired to kill the session, also delete the session cookie.
		// Note: This will destroy the session, and not just the session data!
		if (isset($_COOKIE[session_name()])) {
			setcookie(session_name(), '', time()-42000, '/');
		}
		session_destroy();
		header("Location: login.php");
		exit;
	}
	
	public function site_member_logout(){
		$_SESSION = array();
		// If it's desired to kill the session, also delete the session cookie.
		// Note: This will destroy the session, and not just the session data!
		if (isset($_COOKIE[session_name()])) {
			setcookie(session_name(), '', time()-42000, '/');
		}
		session_destroy();
		header("Location: index.php");
		exit;
	}
	
	/*********
	get the list of add admin users
	data_arr: ref array to send the admin user list
	data_count: number of recs found
	return false on fatal error
	***************/
	public function get_all_admin_user(&$data_arr,&$data_count){
		$q = "select * from ".TP."admins";
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
	/****
	toggle active status of admin user
	****/
	public function set_admin_user_active_state($admin_id,$state){
		$q = "update ".TP."admins set is_active='".$state."' where id='".$admin_id."'";
		$result = mysql_query($q);
		return true;
	}
	/******
	Create admin user
	data_arr: array containing user details
	validation_passed: ref to send whether validation has passed and admin user created or not. True means all ok
	err_arr: ref to send error messages
	return false on fatal error
	********/
	public function create_admin_user($data_arr,&$validation_passed,&$err_arr){
		//validation
		$validation_passed = true;
		if($data_arr['name'] == ""){
			$err_arr['name'] = "Please specify the name";
			$validation_passed = false;
		}
		if($data_arr['login_name'] == ""){
			$err_arr['login_name'] = "Please specify the login name";
			$validation_passed = false;
		}else{
			//check for duplicate login name
			$q = "select count(login_name) as cnt from ".TP."admins where login_name='".$data_arr['login_name']."'";
			$res = mysql_query($q);
			if(!$res){
				return false;
			}
			$row = mysql_fetch_assoc($res);
			if($row['cnt'] > 0){
				//this login name exists
				$err_arr['login_name'] = "This login name exists, specify another one.";
				$validation_passed = false;
			}
		}
		if($data_arr['password'] == ""){
			$err_arr['password'] = "Please specify the password";
			$validation_passed = false;
		}
		if($data_arr['repassword'] == ""){
			$err_arr['repassword'] = "Please retype the password";
			$validation_passed = false;
		}
		if(($data_arr['password']!="")&&($data_arr['repassword']!="")){
			if($data_arr['password']!=$data_arr['repassword']){
				$err_arr['repassword'] = "Passwords do not match";
				$validation_passed = false;
			}
		}
		if($data_arr['email'] == ""){
			$err_arr['email'] = "Please specify the email";
			$validation_passed = false;
		}else{
			//check for duplicate email
			$q = "select count(email) as cnt from ".TP."admins where email='".$data_arr['email']."'";
			$res = mysql_query($q);
			if(!$res){
				return false;
			}
			$row = mysql_fetch_assoc($res);
			if($row['cnt'] > 0){
				//this email exists
				$err_arr['email'] = "This email exists, specify another one.";
				$validation_passed = false;
			}
		}
		/////////////////////////////////////
		if(!$validation_passed){
			//no need to proceed
			return true;
		}
		///////////////////////////////////////////////////////
		//insert data
		$q = "insert into ".TP."admins set name='".$data_arr['name']."', login_name='".$data_arr['login_name']."', password='".$data_arr['password']."', email='".$data_arr['email']."', is_active='Y'";
		$result = mysql_query($q);
		if(!$result){
			return false;
		}
		/////////////////
		//data inserted
		$validation_passed = true;
		return true;
	}
	//////////////////////////////////////////////////////////////////////////
	/****
	sng:4/oct/2010
	If a member forgets the password, the member enters the work email. If there is a match, we get the password and email it to that address
	***********/
	public function email_password_of_site_member($work_email,&$msg){
		global $g_mc;
		/******
		sng:12/oct/2010
		use the mailer class
		******/
		require_once("classes/class.mailer.php");
		$mailer = new mailer();
		//validation
		if($work_email==""){
			$msg = "Please specify the work email";
			return true;
		}
		$q = "select f_name,l_name,password from ".TP."member where work_email='".$work_email."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$cnt = mysql_num_rows($res);
		if(0==$cnt){
			//no such member
			$msg = "This email was not found";
			//no need to proceed
			return true;
		}
		//a hit
		$row = mysql_fetch_assoc($res);
		$subject = "Your deal-data.com password";
		$to = $work_email;
		////////////////////////////////////////////////////////////
		require_once("classes/class.sitesetup.php");
		global $g_site;
		$admin_data = array();
		$success = $g_site->get_site_emails($admin_data);
		if(!$success){
			return false;
		}
		$admin_email = $admin_data['contact_email'];
		////////////////////////////////////////////////////////////
		$headers = "From: ".$admin_email."\r\n";
		/////////////////////////////////////////////////////////////
		$message = "Hi ".$g_mc->view_to_view($row['f_name'])." ".$g_mc->view_to_view($row['l_name'])."\r\n\r\n";
		$message.= "Your deal-data.com member password is: ".$row['password']."\r\n\r\n";
		$message.="Regards,\r\n\r\n";
		$message.=$admin_email;
		
		//$success = mail($to,$subject,$message,$headers);
		//$success = $mailer->mail($to,$subject,$message,$admin_email);
		$success = $mailer->mail($to,$subject,$message);
		if($success){
			$msg = "The password has been emailed to ".$to;
		}else{
			$msg = "Cannot email the password. Please try again.";
		}
		return true;
	}
}
$g_account = new account();
?>