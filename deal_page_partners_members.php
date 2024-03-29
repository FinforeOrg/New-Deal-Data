<?php
/***********
sng:18/sep/2012
We fetch this using ajax
*************/
?>
<script>
function update_members(){
	$.blockUI({ message: '<h1>updating...</h1><img src="images/loader.gif" />' });
	$.get(
		'ajax/fetch_deal_members.php?deal_id=<?php echo $g_view['deal_id'];?>',
		function(data) {
			$.unblockUI();
			$('#team_member_content').html(data);
		}
	)
}
</script>
<script type="text/javascript">
function add_self_to_deal(the_deal_id,the_partner_id){
	$('#add_self_to_deal_result').html('');
	$.blockUI({ message: '<h1>adding...</h1><img src="images/loader.gif" />' });
	$.post("ajax/add_self_to_deal.php", {deal_id: ""+the_deal_id+"",partner_id: ""+the_partner_id+""}, function(data){
		$.unblockUI();
		if(data.mem_added===0){
			//failed
			$('#add_self_to_deal_result').html(data.msg);
		}else{
			update_members();
		}
	},"json");
}
</script>
<script>
function add_colleague_to_deal(the_deal_id,the_partner_id){
	var colleague_id = $('#team_member_id').val();
	$('#add_colleague_to_deal_result').html('');
	$.blockUI({ message: '<h1>adding...</h1><img src="images/loader.gif" />' });
	$.post("ajax/add_colleague_to_deal.php", {deal_id: ""+the_deal_id+"",partner_id: ""+the_partner_id+"",member_id:""+colleague_id+""}, function(data){
		$.unblockUI();
		if(data.mem_added===0){
			//failed
			$('#add_colleague_to_deal_result').html(data.msg);
		}else{
			update_members();
		}
	},"json");
}
</script>
<script>
/*******
sng:14/jan/2013
We just take the mem_id. On the server side, we get the current firm of the member and decide whether to recommend as colleague or admire as competitor
**********/
function recommend_admire_member(mem_id){
	$('#admire_recommend_result').html("");
	$.blockUI({ message: '<h1>adding to recommend / admire list...</h1><img src="images/loader.gif" />' });
	$.post("ajax/admire_recommend_member.php", {member_id:""+mem_id+""}, function(data){
		$.unblockUI();
		if(data.mem_added===0){
			//failed
			$('#admire_recommend_result').html(data.msg);
		}else{
			update_members();
		}
	},"json");
}
</script>

<div id="team_member_content"></div>