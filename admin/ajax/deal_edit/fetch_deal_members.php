<?php
/*************
sng:8/oct/2012
Just a rehash of what we have in front
**************/
require_once("../../../include/global.php");
require_once("classes/class.account.php");
if(!$g_account->is_admin_logged()){
	echo "You need to login first";
	exit;
}

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
<div class="deal_page_h2">Bankers involved in this deal</div>
<table cellpadding="0" cellspacing="0" class="grey_table" style="width:100%;">
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
		<td><?php echo $g_view['deal_bankers'][$i]['f_name']." ".$g_view['deal_bankers'][$i]['l_name'];?></td>
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

<div class="deal_page_h2">Lawyers involved in this deal</div>
<table cellpadding="0" cellspacing="0" class="grey_table" style="width:100%;">
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
		<td><?php echo $g_view['deal_lawyers'][$i]['f_name']." ".$g_view['deal_lawyers'][$i]['l_name'];?></td>
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