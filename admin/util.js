var js_site_url = "";
//so that the urls become /admin
//var js_site_url = "https://localhost/data-cx";

/***********
sng:15/july/2011
Giving same name to all popups means you cannot open two popups at any time.
I have put different names so this will not be the case.
This is useful if admin is viewing suggestion detail in one popup and adding banks in another popup
****************/
function deal_bank_popup(transaction_id){
	
	popup = window.open(js_site_url+"/admin/deal_bank_popup.php?transaction_id="+transaction_id, "deal_bank_popup",
		"height=500,width=850,toolbar=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes");
	return false;
}

function deal_participant_popup(transaction_id){
	
	popup = window.open(js_site_url+"/admin/deal_participant_popup.php?transaction_id="+transaction_id, "deal_participant_popup",
		"height=500,width=900,toolbar=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes");
	return false;
}

function deal_lawfirm_popup(transaction_id){
	
	popup = window.open(js_site_url+"/admin/deal_lawfirm_popup.php?transaction_id="+transaction_id, "deal_lawfirm_popup",
		"height=500,width=850,toolbar=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes");
	return false;
}



function top_firm_popup(top_firm_cat_id,firm_type){
	
	popup = window.open(js_site_url+"/admin/top_firm_popup.php?cat_id="+top_firm_cat_id+"&firm_type="+firm_type, "top_firm_popup",
		"height=500,width=650,toolbar=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes");
	return false;
}

function deal_duplicate_bank_popup(transaction_id){
	
	popup = window.open(js_site_url+"/admin/deal_duplicate_bank_popup.php?transaction_id="+transaction_id, "deal_duplicate_bank_popup",
		"height=500,width=650,toolbar=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes");
	return false;
}

function deal_duplicate_lawfirm_popup(transaction_id){
	
	popup = window.open(js_site_url+"/admin/deal_duplicate_lawfirm_popup.php?transaction_id="+transaction_id, "deal_duplicate_lawfirm_popup",
		"height=500,width=650,toolbar=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes");
	return false;
}

function deal_suggestion_popup(suggestion_id){
	
	popup = window.open(js_site_url+"/admin/deal_suggestion_detail_popup.php?id="+suggestion_id, "deal_suggestion_popup"+suggestion_id,
		"height=600,width=650,toolbar=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes");
	return false;
}
/***************
sng:2/aug/2011
to get the notes for a deal from multiple suggestions, we open another popup
*******************/
function deal_suggestion_note_popup(suggestion_id){
	
	popup = window.open(js_site_url+"/admin/deal_suggestion_note_detail_popup.php?id="+suggestion_id, "deal_suggestion_note_popup",
		"height=600,width=650,toolbar=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes");
	return false;
}
/*******************
sng:19/nov/2011
********************/
function case_study_flags_popup(case_study_id){
	popup = window.open(js_site_url+"/admin/case_study_flags_detail_popup.php?id="+case_study_id, "case_study_flags_popup",
		"height=600,width=650,toolbar=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes");
	return false;
}