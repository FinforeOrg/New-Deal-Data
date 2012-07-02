<?php
require_once("../include/global.php");  
require_once("classes/class.transaction.php");
/*********************
sng:5/mar/2012
We now show admin verified deals with an icon
*********************/
$g_view['has_verified_deal'] = false;
 
$start = 0;
if(isset($_GET['start'])){
	$start = $_GET['start'];
}
$max_to_show = 15;  
//we fetch one extra                               
$g_trans->front_deal_search_paged($_POST,$start,$max_to_show+1,$g_view['data'],$g_view['data_count']);

if($g_view['data_count'] > 0){
	?>
	<table width="100%" cellspacing="0" cellpadding="0" class="company">
	<tr>
		
		<th style="width:110px;">Participant</th>
		<th style="width:100px;">Size</th>
		<th style="width:120px;">Type</th>
		<th style="width:60px;">Date</th>
		<th></th>
	</tr>
	<?php
	if($g_view['data_count'] > $max_to_show){
		$total = $max_to_show;
	}else{
		$total = $g_view['data_count'];
	}
	for($j=0;$j<$total;$j++){
		?>
		<tr>
		
		<td>
		<?php
		/**************
		sng:2/feb/2012
		We now have multiple companies per deal
        <a href="company.php?show_company_id=<?php echo $g_view['data'][$j]['company_id'];?>"><?php echo $g_view['data'][$j]['company_name'];?></a>
		*****************/
		echo Util::deal_participants_to_csv_with_links($g_view['data'][$j]['participants']);
		?>
		</td>
		<td><a href="deal_detail.php?deal_id=<?php echo $g_view['data'][$j]['deal_id']?>"><?php echo $g_view['data'][$j]['fuzzy_value_short_caption'];?></a></td>
		<td><?php echo Util::get_deal_type_for_home_listing($g_view['data'][$j]);?></td>
        <td><?php echo ymd_to_my($g_view['data'][$j]['date_of_deal']);?></td> 
		<td>
		<?php 
		if($g_view['data'][$j]['admin_verified']=='y'){
			?><img src="images/tick_ok.gif" /><?php
			$g_view['has_verified_deal'] = true;
		}
		?>
		</td>
    	</tr>
		<?php
	}
	?>
	</table>
	<?php
	if($g_view['data_count'] > $max_to_show){
		$next_start = $start+$max_to_show;
		?>
		<div style="float:right;padding-top:20px;">
		<input type="button" value="Show more" id="simple_search_more" onclick="simple_deal_search_by_deal_type('<?php echo $_POST['deal_cat_name'];?>','<?php echo $_POST['deal_subcat1_name'];?>','<?php echo $_POST['deal_subcat2_name'];?>',<?php echo $next_start;?>)" />
		</div>
		<script>
		$(function(){
			$('#simple_search_more').button();
		});
		</script>
		<?php
	}
	if($g_view['has_verified_deal']){
		?><div><img src="images/tick_ok.gif" /> Verified deal</div><?php
	}
	/*********************************************************/
}else{
	?>None found<?php
}
?>