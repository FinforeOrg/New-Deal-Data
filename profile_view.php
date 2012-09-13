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
<td style="text-align: right">
<?php
if($g_view['total_points'] == 0){
?>
No tombstone points yet
<?php
}else{
?>
$<?php echo convert_billion_to_million_for_display($g_view['total_points']);?>m total points, $<?php echo convert_billion_to_million_for_display($g_view['last_3_months_total_points']);?>m points in the last 3 months
<?php
}
?>
</td>
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
********/
?>
</td>
</tr>
<!--///////////////////////////////////Recent tombstones///////////////////////////////-->
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
<!--///////////////////////////////////Recent tombstones///////////////////////////////-->
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
<tr><td style="height:10px;">&nbsp;</td></tr>
<tr>
<td><h3><?php echo $g_view['data']['f_name'];?> recommends</h3></td>
</tr>
<?php
if($g_view['recommended_count'] == 0){
//do nothing
}else{
	for($recom=0;$recom<$g_view['recommended_count'];$recom++){
		?>
		<tr>
		<td>
		<a href="profile.php?mem_id=<?php echo $g_view['recommended_data'][$recom]['recommended_mem_id'];?>"><?php echo $g_view['recommended_data'][$recom]['f_name'];?> <?php echo $g_view['recommended_data'][$recom]['l_name'];?></a>, <?php echo $g_view['recommended_data'][$recom]['designation'];?> at <?php echo $g_view['recommended_data'][$recom]['company_name'];?>
		</td>
		</tr>
		<?php
	}
}
?>
<tr><td style="height:10px'">&nbsp;</td></tr>
<tr>
<td><h3><?php echo $g_view['data']['f_name'];?> admires</h3></td>
</tr>
<?php
if($g_view['admired_count'] == 0){
//do nothing
}else{
	for($adm=0;$adm<$g_view['admired_count'];$adm++){
		?>
		<tr>
		<td>
		<a href="profile.php?mem_id=<?php echo $g_view['admired_data'][$adm]['admired_mem_id'];?>"><?php echo $g_view['admired_data'][$adm]['f_name'];?> <?php echo $g_view['admired_data'][$adm]['l_name'];?></a>, <?php echo $g_view['admired_data'][$adm]['designation'];?> at <?php echo $g_view['admired_data'][$adm]['company_name'];?>
		</td>
		</tr>
		<?php
	}
}
?>
<tr><td style="height:10px;">&nbsp;</td></tr>
<tr>
<td><h3>Members who recommend <?php echo $g_view['data']['f_name'];?></h3></td>
</tr>
<?php
if($g_view['recommended_by_count'] == 0){
//do nothing
}else{
	for($recom=0;$recom<$g_view['recommended_by_count'];$recom++){
		?>
		<tr>
		<td>
		<a href="profile.php?mem_id=<?php echo $g_view['recommended_by_data'][$recom]['mem_id'];?>"><?php echo $g_view['recommended_by_data'][$recom]['f_name'];?> <?php echo $g_view['recommended_by_data'][$recom]['l_name'];?></a>, <?php echo $g_view['recommended_by_data'][$recom]['designation'];?> at <?php echo $g_view['recommended_by_data'][$recom]['company_name'];?>
		</td>
		</tr>
		<?php
	}
}
?>
<tr><td style="height:10px'">&nbsp;</td></tr>
<tr>
<td><h3>Members who admire <?php echo $g_view['data']['f_name'];?></h3></td>
</tr>
<?php
if($g_view['admired_by_count'] == 0){
//do nothing
}else{
	for($adm=0;$adm<$g_view['admired_by_count'];$adm++){
		?>
		<tr>
		<td>
		<a href="profile.php?mem_id=<?php echo $g_view['admired_by_data'][$adm]['mem_id'];?>"><?php echo $g_view['admired_by_data'][$adm]['f_name'];?> <?php echo $g_view['admired_by_data'][$adm]['l_name'];?></a>, <?php echo $g_view['admired_by_data'][$adm]['designation'];?> at <?php echo $g_view['admired_by_data'][$adm]['company_name'];?>
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

</table>