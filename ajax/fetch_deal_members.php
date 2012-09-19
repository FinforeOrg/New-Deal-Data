<?php
/*************
sng:18/sep/2012
**************/
require_once("../include/global.php");
require_once("classes/class.account.php");
require_once("classes/class.transaction_member.php");

$trans_mem = new transaction_member();

$g_view['deal_id'] = (int)$_GET['deal_id'];

$g_view['deal_bankers'] = NULL;
$g_view['deal_bankers_count'] = 0;
$ok = $trans_mem->get_all_deal_members_by_type($g_view['deal_id'],'banker',$g_view['deal_bankers'],$g_view['deal_bankers_count']);
if(!$ok){
	?>
	<p>Error while fetching records</p>
	<?php
	return;
}

$g_view['deal_lawyers'] = NULL;
$g_view['deal_lawyers_count'] = 0;
$ok = $trans_mem->get_all_deal_members_by_type($g_view['deal_id'],'lawyers',$g_view['deal_lawyers'],$g_view['deal_lawyers_count']);
if(!$ok){
	?>
	<p>Error while fetching records</p>
	<?php
	return;
}
?>
<h2>Bankers involved in this deal</h2>
<table cellpadding="0" cellspacing="0" class="company" style="width:100%;">
<tr>
<th>Name</th>
<th>Title</th>
<th>Firm</th>
<th>Credit ($m)</th>
<th>Adjusted Credit for the member ($m)</th>
</tr>
<?php
if(0==$g_view['deal_bankers_count']){
?>
<tr><td colspan="5">None found</td></tr>
<?php
}else{
	for($i=0;$i<$g_view['deal_bankers_count'];$i++){
		?>
		<tr>
		<td><a href="profile.php?mem_id=<?php echo $g_view['deal_bankers'][$i]['member_id'];?>"><?php echo $g_view['deal_bankers'][$i]['f_name']." ".$g_view['deal_bankers'][$i]['l_name'];?></a></td>
		<td><?php echo $g_view['deal_bankers'][$i]['designation'];?></td>
		<td><?php echo $g_view['deal_bankers'][$i]['firm_name'];?></td>
		<td><?php echo convert_billion_to_million_for_display($g_view['deal_bankers'][$i]['value_in_billion']);?></td>
		<td><?php echo convert_billion_to_million_for_display($g_view['deal_bankers'][$i]['adjusted_value_in_billion']);?></td>
		</tr>
		<?php
	}
}
?>
</table>

<h2>Lawyers involved in this deal</h2>
<table cellpadding="0" cellspacing="0" class="company" style="width:100%;">
<tr>
<th>Name</th>
<th>Title</th>
<th>Firm</th>
<th>Credit ($m)</th>
<th>Adjusted Credit for the member ($m)</th>
</tr>
<?php
if(0==$g_view['deal_lawyers_count']){
?>
<tr><td colspan="5">None found</td></tr>
<?php
}else{
	for($i=0;$i<$g_view['deal_lawyers_count'];$i++){
		?>
		<tr>
		<td><a href="profile.php?mem_id=<?php echo $g_view['deal_lawyers'][$i]['member_id'];?>"><?php echo $g_view['deal_lawyers'][$i]['f_name']." ".$g_view['deal_lawyers'][$i]['l_name'];?></a></td>
		<td><?php echo $g_view['deal_lawyers'][$i]['designation'];?></td>
		<td><?php echo $g_view['deal_lawyers'][$i]['firm_name'];?></td>
		<td><?php echo convert_billion_to_million_for_display($g_view['deal_lawyers'][$i]['value_in_billion']);?></td>
		<td><?php echo convert_billion_to_million_for_display($g_view['deal_lawyers'][$i]['adjusted_value_in_billion']);?></td>
		</tr>
		<?php
	}
}
?>
</table>
<?php
/*******************
sng:19/sep/2012
In previous workflow, we added ourself to the team of specific bank/lawfirm.
Here we are just adding to the deal. We assume that we want to add ourself as a team member of our current firm. Of course, it can happen that
our current firm is not associated with the deal.
Also, we will not be able to add ourself as a team member of a firm where I used to work and that firm is associated with the deal.
**********************/
?>
<div style="text-align:center; margin-top:10px;">
<?php
if($g_account->is_site_member_logged()){
?>
<input onclick="return add_self_to_deal(<?php echo $g_view['deal_id'];?>,<?php echo $_SESSION['company_id'];?>);" class="btn_auto" type="button" value="ADD YOURSELF TO THIS DEAL" />
<?php
}else{
?>
<p>You need to login to add yourself to deal</p>
<?php
}
?>
<div class="msg_txt" id="add_self_to_deal_result"></div>
</div>



<script>
$('.btn_auto').button();
</script>