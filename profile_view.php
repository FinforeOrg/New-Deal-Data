<?php
/***********************
sng:29/sep/2011
we now include jquery in the container view
<script type="text/javascript" src="js/jquery-1.2.1.pack.js"></script>
********************************/
?>

<table width="100%" cellpadding="0" cellspacing="0">
<?php
/***
sng:24/apr/2010
if I am seeing my own profile, then $g_view['member_id'] will metch $_SESSION['mem_id'] and
is_logged_in will be true. In that case show the edit profile button
*********/
if($g_account->is_site_member_logged()&&($g_view['member_id']==$_SESSION['mem_id'])){
/***
sng:10/jun/2010
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
<tr><td style="text-align:right;"><input type="button" value="Edit profile" class="btn_auto" onclick="goto_edit_profile();" />&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="Unregister" class="btn_auto" onclick="goto_unregister();" /></td></tr>
<?php
}
?>
<tr>
<td>
<!--top part containing name and tombstone points-->
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
<td style="text-align:left"><h1><?php echo $g_view['data']['f_name'];?> <?php echo $g_view['data']['l_name'];?></h1></td>
<td style="text-align: right"></td>
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
<?php echo $g_view['data']['designation'];?>, <?php echo $g_view['data']['company_name'];?>
<?php
if($g_view['data']['year_joined']!=0){
	?>
	<br /><?php echo $g_view['data']['year_joined']." - present";?>
	<?php
}
?>
<?php
/***
sng:4/jun/2010
we show posting country if specified
*****/
if($g_view['data']['posting_country']!=""){
	?>
	<br /><?php echo $g_view['data']['posting_country'];?>
	<?php
}
?>
<?php
/****
sng:7/may/2010
we show recommend/admire button here based on condition

sng:12/nov/2011
No need for recommend / admire for now since in the current data-cx, no one can see profile of another
********/
?>
</td>
</tr>
<?php
/*****************************
<tr><td colspan="2" style="height:20px;">&nbsp;</td></tr>
<tr>
<td colspan="2">
<h1><?php echo $g_view['data']['f_name'];?>'s Recent Tombstones</h1>
</td>
</tr>
<tr>
<td colspan="2">
<?php
if($g_view['tombstone_data_count'] == 0){
	?>
	None yet
	<?php
}else{
	?>
	<table cellpadding="0" cellspacing="10">
	<tr>
	<?php
	$col_count=0;
	for($i=0;$i<$g_view['tombstone_data_count'];$i++){
		?>
		<td style="width:200px">
		<?php
		$g_trans->get_tombstone_from_deal_data($g_view['tombstone_data'][$i]['logo'],$g_view['tombstone_data'][$i]['deal_company_name'],$g_view['tombstone_data'][$i]['deal_cat_name'],$g_view['tombstone_data'][$i]['deal_subcat1_name'],$g_view['tombstone_data'][$i]['deal_subcat2_name'],$g_view['tombstone_data'][$i]['value_in_billion'],$g_view['tombstone_data'][$i]['date_of_deal'],false,false,$g_view['tombstone_data'][$i]['deal_id'],"");
		?>
		</td>
		<?php
		$col_count++;
		if($col_count == 2){
			$col_count = 0;
			?>
			</tr>
			<tr>
			<?php
		}
	}
	?>
	</tr>
	</table>
	<?php
}
?>
</td>
</tr>
********************************************/
?>
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
<?php
/********************************
sng:12/nov/2011
We remove the admire/recommend section for now
The roginal code in in profile_view.php-2011-11-12
*********************************/
?>
</table>
</td>
</tr>
</table>
<!--mid part-->
</td>
</tr>
<?php
/***************************************************
<tr><td style="height:10px'">&nbsp;</td></tr>
<tr><td><h1><?php echo $g_view['data']['f_name'];?>'s Recent Deals</h1></td></tr>
<tr>
<td>
<!--bottom part-->
<table width="100%" cellpadding="0" cellspacing="0" class="company">
<tr>
<th>Date</th>
<th>Client</th>
<th>Deal Type</th>
<th>Size ($m)</th>
<th>Firm</th>
<th>Designation</th>
<th>&nbsp;</th>
</tr>
<?php
if($g_view['deal_count'] == 0){
?>
<tr><td colspan="7">None yet</td></tr>
<?php
}else{
	for($i=0;$i<$g_view['deal_count'];$i++){
		?>
		<tr>
		<td><?php echo date("M, Y",strtotime($g_view['deal_data'][$i]['date_of_deal']));?></td>
		<td><a href="company.php?show_company_id=<?php echo $g_view['deal_data'][$i]['deal_company_id'];?>"><?php echo $g_view['deal_data'][$i]['deal_company_name'];?></a></td>
		<td><?php echo $g_view['deal_data'][$i]['deal_cat_name'];?></td>
		<td><?php echo convert_billion_to_million_for_display($g_view['deal_data'][$i]['value_in_billion']);?></td>
		<td><?php echo $g_view['deal_data'][$i]['firm_name'];?></td>
		<td><?php echo $g_view['deal_data'][$i]['designation'];?></td>
		<td>
		<form method="get" action="deal_detail.php">
		<input type="hidden" name="deal_id" value="<?php echo $g_view['deal_data'][$i]['deal_id'];?>" />
		<input name="submit" type="submit" class="btn_auto" id="button" value="Detail" />
		</form>
		</td>
		
		</tr>
		<?php
	}
}
?>
</table>
<!--bottom part-->
</td>
</tr>
*********************************************/
?>

</table>