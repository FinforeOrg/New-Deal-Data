<table width="100%" cellpadding="5" cellspacing="0" border="0">
<tr>
<td><div class="msg_txt" id="polling_msg">Checking. Please be patient.</div></td>
</tr>
</table>

<script>

function poll_slave_status(){
	$.get("ajax/fetch_slave_status.php?slave_name=fetch_company_data_co_codes&dummy="+$.now(),function(result){
		if(result.err_flag===1){
			//nothing to do, and do not trigger another poll
			$('#polling_msg').html("Error checking status");
		}else{
			if(result.still_running==='y'){
				//be patient, check again after some time
				setTimeout(poll_slave_status,10000);
			}else{
				//done
				$('#polling_msg').html(result.msg);
			}
		}
	},"json");
}
setTimeout(poll_slave_status,10000);
</script>