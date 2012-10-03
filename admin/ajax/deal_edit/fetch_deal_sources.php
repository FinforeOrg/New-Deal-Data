<?php
/*****************
sng:3/oct/2012
****************/
require_once("../../../include/global.php");
require_once("classes/class.account.php");
if(!$g_account->is_admin_logged()){
	echo "You need to login first";
	exit;
}
require_once("classes/class.transaction_source.php");
$trans_source = new transaction_source();

$g_view['sources'] = NULL;
$g_view['sources_count'] = 0;

$g_view['deal_id'] = $_GET['deal_id'];
$ok = $trans_source->get_deal_sources($g_view['deal_id'],$g_view['sources']);
if(!$ok){
	?><div class="err_txt">error fetching sources</div><?php
}else{
	?>
	<table cellpadding="10" cellspacing="0" border="1" style="border-collapse:collapse;">
	<tr>
	<td>Source URL</td>
	<td>&nbsp;</td>
	</tr>
	<?php
	$g_view['sources_count'] = count($g_view['sources']);
	if(0 == $g_view['sources_count']){
		?>
		<tr><td colspan="3"><div class="msg_txt">None specified</div></td></tr>
		<?php
	}else{
		for($j=0;$j<$g_view['sources_count'];$j++){
			$source = $g_view['sources'][$j]['source_url'];
			?>
			<tr>
			<td><a href="<?php echo $source;?>" target="_blank"><?php echo $source;?></a></td>
			<td><input type="button" onClick="delete_deal_source(<?php echo $g_view['sources'][$j]['id'];?>)" value="Delete"></td>
			</tr>
			<?php
		}
	}
	?>
	</table>
	<?php
}
?>