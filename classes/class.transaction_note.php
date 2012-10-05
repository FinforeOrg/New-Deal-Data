<?php
/***********************
sng:5/oct/2012
*******/
class transaction_note{
	/****
    sng:21/may2010
    The note is in different table with same transaction id
    if the id is found, update, else insert
	
	sng:17/jun/2011
	Made this public. We will access this from other points also. We also use mysql_real_escape_string instead of magic quote
	
	sng:30/apr/2012
	we need to notify suggestion that we are adding a deal and this is the original submission for note.
	We need two extra arguments - mem id who added the deal and the date of addition
	
	When we are adding a deal data, and has specified the note, we add the note diretly and then notify to store it as suggestion
	transaction_suggestion::note_added_via_deal_submission
	Call this ONLY when you are ADDING a deal. Otherwise do not call
    **********/
    public function set_note($deal_id,$member_id,$deal_added_on,$note){
        
        $q = "select count(*) as cnt from ".TP."transaction_note where transaction_id='".$deal_id."'";
        $res = mysql_query($q);
        if(!$res){
            return false;
        }
        $row = mysql_fetch_assoc($res);
        if(0==$row['cnt']){
            //not found, insert
            $note_q = "insert into ".TP."transaction_note set transaction_id='".$deal_id."', note='".mysql_real_escape_string($note)."'";
        }else{
            $note_q = "update ".TP."transaction_note set note='".mysql_real_escape_string($note)."' where transaction_id='".$deal_id."'";
        }
        $result = mysql_query($note_q);
        if(!$result){
            return false;
        }
		
		require_once("classes/class.transaction_suggestion.php");
		$trans_suggest = new transaction_suggestion();
		$ok = $trans_suggest->note_added_via_deal_submission($deal_id,$member_id,$deal_added_on,$note);
		/*********
		never mind if error
		**********/
        return true;
    }
	/**************************
	sng:27/apr/2012
	We need a way to allow members to add to the note.
	
	This is called when a note is suggested by member via transaction_suggestion::front_submit_note. That function has already
	added to the note suggestion table so no need for notification. In fact, do not call this directly in front end.
	************************/
	public function front_append_to_note($deal_id,$note){
        $db = new db();
		
        $q = "select note from ".TP."transaction_note where transaction_id='".$deal_id."'";
        $ok = $db->select_query($q);
		if(!$ok){
			return false;
		}
		if(!$db->has_row()){
			//not found, insert
			$note_q = "insert into ".TP."transaction_note set transaction_id='".$deal_id."', note='".mysql_real_escape_string($note)."'";
		}else{
			//there is existing note, we get the note and append the suggestion to it and store
			$row = $db->get_row();
			$curr_note = $row['note'];
			$new_note = $curr_note."\r\n".$note;
			$note_q = "update ".TP."transaction_note set note='".mysql_real_escape_string($new_note)."' where transaction_id='".$deal_id."'";
		}
		
		
        $ok = $db->mod_query($note_q);
        if(!$ok){
            return false;
        }
        return true;
    }
	
	/******************
	sng:5/oct/2012
	Admin can directly append to the notes. We do not use the transaction_suggestion::front_submit_note because we are not sure
	whether the front end submissions will add to the note (currently we do that but later might change, so we use another function)
	
	We use a notification to add this submission as a suggestion
	******************/
	public function admin_update_note($deal_id,$note,$append){
        $db = new db();
		if($append){
			$q = "select note from ".TP."transaction_note where transaction_id='".$deal_id."'";
			$ok = $db->select_query($q);
			if(!$ok){
				return false;
			}
			if(!$db->has_row()){
				//not found, insert
				$note_q = "insert into ".TP."transaction_note set transaction_id='".$deal_id."', note='".mysql_real_escape_string($note)."'";
			}else{
				//there is existing note, we get the note and append the suggestion to it and store
				$row = $db->get_row();
				$curr_note = $row['note'];
				$new_note = $curr_note."\r\n".$note;
				$note_q = "update ".TP."transaction_note set note='".mysql_real_escape_string($new_note)."' where transaction_id='".$deal_id."'";
			}
		}else{
			$note_q = "insert into ".TP."transaction_note set transaction_id='".$deal_id."', note='".mysql_real_escape_string($note)."'";
		}
        $time_now = date("Y-m-d H:i:s");
        $ok = $db->mod_query($note_q);
        if(!$ok){
            return false;
        }
		/**************
		now send the notification
		****************/
		require_once("classes/class.transaction_suggestion.php");
		$trans_suggest = new transaction_suggestion();
		$ok = $trans_suggest->note_added_via_admin($deal_id,0,$time_now,$note);
		/*********
		never mind if error
		**********/
        return true;
    }
	/****
    sng:4/feb/2011
    The private note is in different table with same transaction id
    if the id is found, update, else insert
	
	sng:17/jun/2011
	Made this public, will access from other points
    **********/
    public function set_private_note($deal_id,$note){
        
        $q = "select count(*) as cnt from ".TP."transaction_private_note where transaction_id='".$deal_id."'";
        $res = mysql_query($q);
        if(!$res){
            return false;
        }
        $row = mysql_fetch_assoc($res);
        if(0==$row['cnt']){
            //not found, insert
            $note_q = "insert into ".TP."transaction_private_note set transaction_id='".$deal_id."', note='".mysql_real_escape_string($note)."'";
        }else{
            $note_q = "update ".TP."transaction_private_note set note='".mysql_real_escape_string($note)."' where transaction_id='".$deal_id."'";
        }
        $result = mysql_query($note_q);
        if(!$result){
            return false;
        }
        return true;
    }
}
?>