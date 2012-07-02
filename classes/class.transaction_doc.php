<?php
/*****************************
sng:22/feb/2012
handles everthing regarding docs associated with a deal
********************************/
class transaction_doc{
	/******************************
	sng:1/sep/2011
	Users can upload files as part of deal suggestion. what admin can do is associate the files with the new deal
	admin is creating. Admin use a simple deal add form which create the deal and show admin the edit view along with the deal id
	In the suggestion popup, admin type the deal id in a text box beside the filename and ajax submit
	
	sng:6/sep/2011
	We also need to set is_approved to y or else the file is not listed for the deal in the front end
	************************************/
	public function ajax_accept_deal_suggestion_file($file_id,$transaction_id,&$msg){
		$q = "update ".TP."transaction_files set transaction_id='".$transaction_id."',is_approved='y' where file_id='".$file_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$count = mysql_affected_rows();
		if($count == 0){
			$msg = "error";
		}else{
			$msg = "accepted";
		}
		return true;
	}
	
	public function get_all_documents($transaction_id,&$data_arr,&$data_count){
		
		$q = "select cf.*,m.f_name,m.l_name from ".TP."transaction_files as cf left join ".TP."member as m on(cf.mem_id=m.mem_id) where cf.transaction_id='".$transaction_id."' order by date_uploaded";
		
		$res = mysql_query($q);
        if(!$res){
            return false;
        }
		$data_count = mysql_num_rows($res);
        if(0 == $data_count){
            //no data to return so
            return true;
        }
		for($i=0;$i<$data_count;$i++){
            $data_arr[$i] = mysql_fetch_assoc($res);
        }
        return true;
	}
	
	public function delete_document($doc_id){
	
		//get the document file
		$q = "select stored_filename from ".TP."transaction_files where file_id='".$doc_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$cnt = mysql_num_rows($res);
		if(0 == $cnt){
			//no such document, this should not happen
			return false;
		}
		$row = mysql_fetch_assoc($res);
		$filename = $row['stored_filename'];
		$file_path = FILE_PATH."temp_suggestion_files/".$filename;
		if(file_exists($file_path)){
			unlink($file_path);
		}
		//now delete the record
		$q = "delete from ".TP."transaction_files where file_id='".$doc_id."'";
		$result = mysql_query($q);
		if(!$result){
			return false;
		}
		return true;
	}
	
	public function add_document($deal_id,$mem_id,$is_approved,&$validation_passed,&$err_arr){
		require_once("classes/fileuploader.php");
		require_once("classes/db.php");
		
		$validation_passed = true;
		//validation
		if($_FILES['qqfile']['name']==""){
			$validation_passed = false;
			$err_arr['filename'] = "Please specify the file";
		}
		if(!$validation_passed){
			return true;
		}
		
		//see /ajax/fileuploader.php
		// list of valid extensions, ex. array("jpeg", "xml", "bmp")
		$allowedExtensions = array('pdf','doc','docx','txt','xls','xlsx','ppt','pptx','jpg','gif','png');
		// max file size in bytes
		$sizeLimit = 10 * 1024 * 1024;
		
		$uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
		$result = $uploader->handleUpload(FILE_PATH."/temp_suggestion_files/");
		//further validation done by fileuploader class
		if(isset($result['error'])&&($result['error']!="")){
			$validation_passed = false;
			$err_arr['filename'] = $result['error'];
		}
		if(!$validation_passed){
			return true;
		}
		//no error ,so
		$db = new db();
		$q = "insert into ".TP."transaction_files set caption='".$result['original_filename']."',stored_filename='".$result['stored_filename']."',date_uploaded='".date("Y-m-d")."',mem_id='".$mem_id."',suggestion_id='',transaction_id='".$deal_id."',is_approved='".$is_approved."'";
		$success = $db->mod_query($q);
		if(!$success){
			return false;
		}
		//data inserted
		return true;
	}
	/******************
	sng:22/feb/2012
	code to get the documents of a deal.
	Since we are implementing peer review, we get all the docs, even if it is marked is_approved=n
	*******************/
	public function front_get_all_documents_for_deal($transaction_id,&$data_arr,&$data_count){
		$db = new db();
		$q = "select file_id,caption,is_approved,flag_count from ".TP."transaction_files where transaction_id='".$transaction_id."' order by date_uploaded";
		
		$ok = $db->select_query($q);
        if(!$ok){
            return false;
        }
		$data_count = $db->row_count();
        if(0 == $data_count){
            //no data to return so
            return true;
        }
		$data_arr = $db->get_result_set_as_array();
        return true;
	}
	
	/******************
	sng:22/feb/2012
	There might be one or more files along with this new deal suggestion.
	code to get the documents for a deal suggestion.
	NOTE: we only fetch those that are yet to be associated with a deal
	*******************/
	public function front_get_all_documents_for_deal_suggestion($suggestion_id,&$data_arr,&$data_count){
		$db = new db();
		$q = "select * from ".TP."transaction_files where suggestion_id='".$suggestion_id."' AND transaction_id=''";
		
		$ok = $db->select_query($q);
        if(!$ok){
            return false;
        }
		$data_count = $db->row_count();
        if(0 == $data_count){
            //no data to return so
            return true;
        }
		$data_arr = $db->get_result_set_as_array();
        return true;
	}
	
	/******************************************
	sng:22/feb/2012
	Members can flag a deal doc. They also specify the reason but we do not
	make the reason mandatory.
	We store the entry and update the flag counter for the deal doc
	Then we fire a mail to admin.
	Also, a member can flag a deal doc more than once.
	********************************/
	public function front_flag_deal_doc($file_id,$member_id,$reason){
		$db = new db();
		$q = "insert into ".TP."transaction_files_disputes set file_id='".$file_id."',
		mem_id='".$member_id."',
		date_flagged='".date("Y-m-d")."',
		flag_reason='".mysql_real_escape_string($reason)."'";
		
		$ok = $db->mod_query($q);
		if(!$ok){
			return false;
		}
		//inserted, now update flag count
		$q = "update ".TP."transaction_files set flag_count=flag_count+1 where file_id='".$file_id."'";
		$ok = $db->mod_query($q);
		//never mind if this fails
		/*******************************
		Notify admin that a deal document has been flagged. Problem is, we have many admins.
		So let us send the email to a site email. This code is triggered by front end.
		*******************************/
		
		require_once("classes/class.sitesetup.php");
		global $g_site;
		$site_emails = NULL;
		$success = $g_site->get_site_emails($site_emails);
		if(!$success){
			//do not bother
			return true;
		}
		$to = $site_emails['contact_email'];
		$from = $site_emails['mem_related_email'];
		
		$headers = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/plain;charset=iso-8859-1' . "\r\n";
		$headers .= "From: ".$from."\r\n";
		
		$subject = "data-cx.com deal document flagged";
		
		
		$msg = "A deal document has been flagged. Please login to admin panel to review it.\r\n";
		
		require_once("classes/class.mailer.php");
		$mailer = new mailer();
		
		$to = $work_email;
		/**********
		sng:18/nov/2011
		Ignore mailer exception
		**************/
		try{
			$mailer->mail($to,$subject,$msg);
		}catch(Exception $e){}
		
		return true;
	}
}
?>