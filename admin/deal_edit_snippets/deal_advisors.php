<?php
/****************
sng:9/oct/2012
Common code for the deal_financial_advisors and deal_legal_advisors
*****************/
?>
<script>
/*******
partner_type: bank, law firm

Since this is the 'common', this code knows the container ids used.
*****/
function fetch_deal_partners_for_admin(partner_type){
	$.blockUI({ message: '<p><strong>Fetching...</strong></p><img src="../images/loader.gif" />' });
	$.get(
		'ajax/deal_edit/fetch_deal_advisors.php?deal_id=<?php echo $g_view['deal_id'];?>&partner_type='+partner_type+'&dummy='+$.now(),
		function(data) {
			$.unblockUI();
			if(partner_type=="bank"){
				$('#financial_advisor_content').html(data);
			}else{
				$('#legal_advisor_content').html(data);
			}
		}
	)
}
/***************
we have tabular data. Each series of inputs have same id but different suffix. We use that to get
the inputs for a particular update
************/
function update_partner(id_suffix,partner_type){
	var msg_div;
	if(partner_type=="bank"){
		msg_div = $('#financial_advisor_edit_msg');
	}else{
		msg_div = $('#legal_advisor_edit_msg');
	}
	msg_div.html("Adding...");
	
	var rec_id = $('#trans_partner_record_id'+id_suffix).val();
	var role_id = $('#trans_partner_role_id'+id_suffix).val();
	
	$.post('ajax/deal_edit/update_deal_partner.php',{record_id: rec_id,role_id: role_id},function(data){
		msg_div.html(data.msg);
		if(data.updated=='y'){
			fetch_deal_partners_for_admin(partner_type);
		}
	},"json");
}
</script>