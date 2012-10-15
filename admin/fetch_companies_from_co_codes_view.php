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
</table>