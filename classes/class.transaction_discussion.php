<?php
/********************
sng:8/apr/2011
holds methods related to discussions on a deal
**********************/
require_once("classes/class.account.php");
require_once("classes/db.php");
class transaction_discussion{

	/**************
	check who can access the discussion section of a deal
	The user has to login
	any member of type data partner, never mind the deal
	a company rep, only if the fellow works in the company which did the deal
	a banker or lawyer, if the bank / law firm is a partner in that deal. The member need not be listed in the deal team
	************/
	public function can_see($deal_id,&$allow){
		global $g_account;
		
		if(!$g_account->is_site_member_logged()){
			//not logged, no access
			$allow = false;
			return true;
		}
		//logged in, check the member type
		if($_SESSION['member_type'] == "data partner"){
			//data partner has access to discussion to any deal
			$allow = true;
			return true;
		}
		if($_SESSION['member_type'] == "company rep"){
			//get the company of this member
			$mem_company = $_SESSION['company_id'];
			//get the company that did the deal (not seller, or target or banks or law firms)
			$q = "select company_id from ".TP."transaction where id='".$deal_id."'";
			$res = mysql_query($q);
			if(!$res){
				return false;
			}
			$cnt = mysql_num_rows($res);
			if(0 == $cnt){
				//no such deal
				return false;
			}
			$row = mysql_fetch_assoc($res);
			$deal_company = $row['company_id'];
			if($mem_company == $deal_company){
				$allow = true;
				return true;
			}else{
				//the company of the member was not involved in the deal
				$allow = false;
				return true;
			}
			
		}
		if(($_SESSION['member_type'] == "banker")||($_SESSION['member_type'] == "lawyer")){
			//get the company of this member
			$mem_company = $_SESSION['company_id'];
			//is this firm a partner in the deal
			$q = "select count(*) as cnt from ".TP."transaction_partners where transaction_id='".$deal_id."' and partner_id='".$mem_company."'";
			$res = mysql_query($q);
			if(!$res){
				return false;
			}
			$row = mysql_fetch_assoc($res);
			if($row['cnt'] == 0){
				//not a partner
				$allow = false;
				return true;
			}else{
				$allow = true;
				return true;
			}
		}
		//by default do not allow
		$allow = false;
		return true;
	}
	
	public function post_comment($deal_id,$member_id,$parent_id,$data_txt,&$validation_passed,&$err_msg){
		$validation_passed = true;
		if($data_txt == ""){
			$validation_passed = false;
			$err_msg.="Please specify the comment<br />";
		}
		if(!$validation_passed){
			return true;
		}
		$posted_on = date("Y-m-d H:i:s");
		$q = "insert into ".TP."transaction_discussion set transaction_id='".$deal_id."',posting_member_id='".$member_id."',parent_posting_id='".$parent_id."',flag_count='0',posting_txt='".$data_txt."',posted_on='".$posted_on."'";
		$result = mysql_query($q);
		if(!$result){
			return false;
		}
		/********************
		the postings can be questions (parent is 0) or replies (against some question).
		We need to track these and ue 2 fields - tree and branch
		if parent is 0, tree is comment id, crench is 0
		
		else tree is the parent id, branch is comment id
		*********************/
		$posting_id = mysql_insert_id();
		$tree = 0;
		$branch = 0;
		
		if($parent_id == 0){
			//a question
			$tree = $posting_id;
			$branch = 0;
		}else{
			//a reply
			$tree = $parent_id;
			$branch = $posting_id;
		}
		//update
		$q = "update ".TP."transaction_discussion set tree='".$tree."', branch='".$branch."' where posting_id='".$posting_id."'";
		$result = mysql_query($q);
		if(!$result){
			return false;
		}
		$validation_passed = true;
		return true;
	}
	
	public function flag_comment($posting_id){
		$q = "update ".TP."transaction_discussion set flag_count=flag_count+1 where posting_id='".$posting_id."'";
		$result = mysql_query($q);
		if(!$result){
			return false;
		}
		return true;
	}
	
	public function get_comments($deal_id,&$data_arr,&$data_count){
		$data_count = 0;
		/***********************
		sng:16/nov/2011
		We show the division also
		***************************/
		$q = "select d.*,m.work_email,m.division from ".TP."transaction_discussion as d left join ".TP."member as m on(d.posting_member_id=m.mem_id) where d.transaction_id='".$deal_id."' order by tree,branch";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$data_count = mysql_num_rows($res);
		if($data_count == 0){
			return true;
		}
		for($i=0;$i<$data_count;$i++){
			$data_arr[$i] = mysql_fetch_assoc($res);
		}
		return true;
	}
	/********************************************deal discussion watch section*******************************************/
	/****************
	sng:9/sep/2011
	get the deal discussions that are being watched by the member
	
	sng:12/sep/2011
	We now use a filter
	show all all
	show all changed in lst 7 days d|7
	show all changed in last 48 hours h|48
	*****************/
	public function get_watched_deal_discussion_for_members($mem_id,$filter,&$data_arr,&$data_count){
		$db = new db();
		
		$time_now = date("Y-m-d H:i:s");
		/*******************
		sng:20/sep/2011
		For discussion, we have one or more postings. We need the last posting for this and we order by that, and show it in the watchlist
		
		sng:25/jan/2012
		We now have value range id for each deal that show the fuzzy deal value. These are predefined.
		Sometime, we only have value range id and deal value is 0
		If both deal value and value range id is 0, the deal value is undisclosed.
		
		sng:3/feb/2012
		We no longer have a single company associated with a deal. Now we have multiple companies
		
		sng:5/mar/2012
		We need the admin_verified flag because based on that, we show a tick mark for the deals
		***************/
		$q = "select w.*,value_in_billion,date_of_deal,deal_cat_name,deal_subcat1_name,deal_subcat2_name,last_post_date,t.value_range_id,t.admin_verified,vrm.short_caption as fuzzy_value_short_caption,vrm.display_text as fuzzy_value from ".TP."transaction_discussion_watchlist as w left join ".TP."transaction as t on(w.deal_id=t.id) left join ".TP."company as c on(t.company_id=c.company_id) left join (select transaction_id,max(posted_on) as last_post_date from ".TP."transaction_discussion group by transaction_id) as d on(w.deal_id=d.transaction_id) LEFT JOIN ".TP."transaction_value_range_master as vrm ON (t.value_range_id=vrm.value_range_id) where mem_id='".$mem_id."'";
		
		if(($filter!="")&&($filter!="all")){
			//split
			$filter_tokens = explode("|",$filter);
			switch($filter_tokens[0]){
				case 'd':
				$q.=" and TIMESTAMPDIFF(DAY,last_post_date,'".$time_now."')<=".$filter_tokens[1];
				break;
				case 'h':
				$q.=" and TIMESTAMPDIFF(HOUR,last_post_date,'".$time_now."')<=".$filter_tokens[1];
				break;
			}
		}
		
		$q.=" order by last_post_date desc";
		$success = $db->select_query($q);
		if(!$success){
			return false;
		}
		$data_count = $db->row_count();
		if(0 == $data_count){
			//no deals are being watched by this member
			return true;
		}
		$data_arr = $db->get_result_set_as_array();
		/**************************
		sng:3/feb/2012
		get the deal participants, just the names
		*************************/
		require_once("classes/class.transaction_company.php");
		$g_trans_comp = new transaction_company();
		
		for($k=0;$k<$data_count;$k++){
			$data_arr[$k]['participants'] = NULL;
			$success = $g_trans_comp->get_deal_participants($data_arr[$k]['deal_id'],$data_arr[$k]['participants']);
			if(!$success){
				return false;
			}
		}
		return true;
	}
	
	public function remove_deal_discussion_from_watch($watch_id){
		$db = new db();
		$q = "delete from ".TP."transaction_discussion_watchlist where watch_id='".$watch_id."'";
		$success = $db->mod_query($q);
		return $success;
	}
	
	public function ajax_add_deal_discussion_to_watch_list($mem_id,$deal_id,&$validation_passed,&$err_msg){
		$db = new db();
		$validation_passed = true;
		
		//check if the deal exists or not
		$q = "select count(*) as cnt from ".TP."transaction where id='".$deal_id."'";
		$success = $db->select_query($q);
		if(!$success){
			return false;
		}
		$row = $db->get_row();
		if($row['cnt'] == 0){
			//the deal does not exists
			$validation_passed = false;
			$err_msg = "The deal does not exists";
			return true;
		}
		//deal exists, check if watching
		$watching = false;
		$success = $this->is_member_watching_deal_discussion($mem_id,$deal_id,$watching);
		if(!$success){
			return false;
		}
		if($watching){
			$validation_passed = false;
			$err_msg = "Already watching";
			return true;
		}
		
		//insert
		$q = "insert into ".TP."transaction_discussion_watchlist set mem_id='".$mem_id."', deal_id='".$deal_id."'";
		$success = $db->mod_query($q);
		if(!$success){
			return false;
		}
		$validation_passed = true;
		return true;
	}
	
	public function is_member_watching_deal_discussion($mem_id,$deal_id,&$watching){
		$db = new db();
		$q = "select count(*) as cnt from ".TP."transaction_discussion_watchlist where mem_id='".$mem_id."' and deal_id='".$deal_id."'";
		$success = $db->select_query($q);
		if(!$success){
			return false;
		}
		$row = $db->get_row();
		if($row['cnt'] == 0){
			$watching = false;
		}else{
			$watching = true;
		}
		return true;
	}
	/********************************************discussion watch section*******************************************/
}
$g_deal_disc = new transaction_discussion();
?>