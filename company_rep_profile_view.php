<table width="100%" cellpadding="0" cellspacing="0">
<?php
/***
sng:24/apr/2010
if I am seeing my own profile, then $g_view['member_id'] will metch $_SESSION['mem_id'] and
is_logged_in will be true. In that case show the edit profile button
*********/
if($g_account->is_site_member_logged()&&($g_view['member_id']==$_SESSION['mem_id'])){
/***
sng:20/sep/2010
make the link a button. Also, put the unregister facility here
***/
?>
<script type="text/javascript">
function goto_edit_profile(){
	window.location="edit_my_profile.php";
}

function goto_unregister(){
	window.location="member_unregister.php";
}
</script>
<tr><td style="text-align:right;"><input type="button" value="EDIT PROFILE" class="btn_auto" onclick="goto_edit_profile();" />&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="UNREGISTER" class="btn_auto" onclick="goto_unregister();" /></td></tr>
<?php
}
?>
<tr>
<td>
<!--top part containing name and tombstone points-->
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
<td style="text-align:left"><h1><?php echo $g_view['data']['f_name'];?> <?php echo $g_view['data']['l_name'];?></h1></td>
<td>&nbsp;</td>
</tr>
</table>
<!--top part containing name and tombstone points-->
</td>
</tr>

<tr>
<td><img src="images/spacer.gif" width="1" height="30" alt="" /></td>
</tr>

<tr>
<td>
<!--mid part-->
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
<td style="width:45%">
<!--image, designation, company-->
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
<td style="width:170px;">
<?php
if($g_view['data']['profile_img']==""){
	?>
	<img src="images/no_profile_img.jpg" />
	<?php
}else{
	?>
	<img src="uploaded_img/profile/thumbnails/<?php echo $g_view['data']['profile_img'];?>" />
	<?php
}
?>
</td>
<td>
<?php echo $g_view['data']['designation'];?>, <?php echo $g_view['data']['company_name'];?><br />
<?php
if($g_view['data']['year_joined']!=0){
	echo $g_view['data']['year_joined']." - present";
}
?>
</td>
</tr>



</table>
<!--image, designation, company-->
</td>
<td>&nbsp;</td>
<td style="width:45%; vertical-align:top">
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
<td><h3>Previous Work</h3></td>
</tr>
<?php
if($g_view['prev_work_count'] == 0){
	//do nothing
}else{
	for($work_i=0;$work_i<$g_view['prev_work_count'];$work_i++){
		?>
		<tr>
		<td>
		<?php echo $g_view['prev_work_data'][$work_i]['company_name'];?>, <?php echo $g_view['prev_work_data'][$work_i]['designation'];?><br />
		<?php echo $g_view['prev_work_data'][$work_i]['year_from'];?> - <?php echo $g_view['prev_work_data'][$work_i]['year_to'];?>
		</td>
		</tr>
		<?php
	}
}
?>

</table>
</td>
</tr>
</table>
<!--mid part-->
</td>
</tr>




</table>