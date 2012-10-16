<table width="100%" cellpadding="5" cellspacing="0" border="0">
<tr>
<td><span class="msg_txt">
<?php
if($g_view['started']){
	echo "Started processing...Please be patient";
}else{
	echo $g_view['msg'];
}
?>
</span></td>
</tr>
<tr>
<td><div class="msg_txt" id="polling_msg"></div></td>
</tr>
</table>
<?php
if($g_view['started']){
?>
<script>

function poll_slave_status(){
	$.get("ajax/fetch_slave_status.php?slave_name=fetch_company_data_co_codes&dummy="+$.now(),function(result){
		if(result.err_flag===1){
			//nothing to do, and do not trigger another poll
		}else{
			if(result.still_running==='y'){
				//be patient, check again after some time
				setTimeout(poll_slave_status,10000);
			}else{
				//done
				$('#polling_msg').html("done");
			}
		}
	},"json");
}
setTimeout(poll_slave_status,10000);
</script>
<?php
}
?>