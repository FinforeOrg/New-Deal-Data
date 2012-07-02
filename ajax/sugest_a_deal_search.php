<?php
/*********************
sng:5/mar/2012
We now show admin verified deals with an icon
*************************/
require_once("../include/global.php");  
require_once("../classes/class.transaction.php");

$g_view['has_verified_deal'] = false;

$_POST['top_search_term'] = $_POST['search_term'];  
 
$start = 0;
if(isset($_GET['start'])){
	$start = $_GET['start'];
}
$max_to_show = 5;  
//we fetch one extra                                 
$g_trans->front_deal_search_paged($_POST,$start,$max_to_show+1,$g_view['data'],$g_view['data_count']);
if (sizeOf($g_view['data'])) {
?>
<table width="100%" cellspacing="0" cellpadding="0" class="company">
<tbody>
    <tr>
		
        <th style="width:110px;">Participant</th>
		<th style="width:60px;">Date</th>
		<th style="width:120px;">Type</th>
		<th><!--Value US$m-->Size</th>
		<th style="width:170px;">Bank(s)</th>
		<th style="width:170px;">Law Firm(s)</th>
		<th></th>
		<th>&nbsp;</th>
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
		/****************
		sng:3/feb/2012
		We now have multiple companies per deal
		<a href="company.php?show_company_id=<?php echo $g_view['data'][$j]['company_id'];?>"><?php echo $g_view['data'][$j]['company_name'];?></a>
		*****************/
		echo Util::deal_participants_to_csv_with_links($g_view['data'][$j]['participants']);
		?>
		</td>
        <td><?php echo $g_view['data'][$j]['date_of_deal']?></td>
        <td>
            <?php echo Util::get_deal_type_for_home_listing($g_view['data'][$j]);?>      
        </td>
        <td>
            <?php
			/**********************************************
			sng:23/jan/2012
			We now show the value if we have it else value in range
			
                if($result['value_in_billion']==0){
                    ?>
                    not disclosed
                    <?php
                }else{
                    echo convert_billion_to_million_for_display_round($result['value_in_billion']);
                }
			************************************************/
			echo convert_deal_value_for_display_round($g_view['data'][$j]['value_in_billion'],$g_view['data'][$j]['value_range_id'],$g_view['data'][$j]['fuzzy_value']) ;
            ?>        
        </td>
        <td>
            <?php
                $banks_csv = "";
                $bank_cnt = count($g_view['data'][$j]['banks']);
                for($banks_i=0;$banks_i<$bank_cnt;$banks_i++){
                    $banks_csv.=", ".$g_view['data'][$j]['banks'][$banks_i]['name'];
                }
                $banks_csv = substr($banks_csv,1);
                echo $banks_csv;
            ?>        
        </td>
        <td>
            <?php
                $law_csv = "";
                $law_cnt = count($g_view['data'][$j]['law_firms']);
                for($law_i=0;$law_i<$law_cnt;$law_i++){
                    $law_csv.=", ".$g_view['data'][$j]['law_firms'][$law_i]['name'];
                }
                $law_csv = substr($law_csv,1);
                echo $law_csv;
            ?>        
        </td>
		<td>
		<?php 
		if($g_view['data'][$j]['admin_verified']=='y'){
			?><img src="images/tick_ok.gif" /><?php
			$g_view['has_verified_deal'] = true;
		}
		?>
		</td>
        <td><a class='link_as_button' target="_blank" href="deal_detail.php?deal_id=<?php echo $g_view['data'][$j]['deal_id']?>"> Detail </a></td>
    </tr>
   
   
    

<?php
    }
}
?>
</tbody>
</table> 
<?php
	if($g_view['data_count'] > $max_to_show){
		$next_start = $start+$max_to_show;
		?>
		<?php
		/*******************
		sng:30/jan/2012
		We need to show deal search page in a new tab. So we hide this
		<div style="float:right;padding-top:20px;">
		<input type="button" value="Show more" id="search_more" onclick="detailed_deal_search(<?php echo $next_start;?>)" />
		</div>
		*******************/
		?>
		<div style="float:right;padding-top:20px;">
		<form id="deal_search_helper" method="post" action="deal_search.php" target="_blank">
			<input type="hidden" name="myaction" value="search" />
			<input type="hidden" name="top_search_area" value="deal" />
			<input type="hidden" name="top_search_term" value="<?php echo $_POST['search_term'];?>" />
			<input type="hidden" name="start" id="pagination_helper_start" value="0" />
			<input type="submit" id="search_all" value="Show more"  />
		</form>
		</div>
		<div style="clear:both;"></div>
		<script>
		$(function(){
			$('#search_more').button();
			$('#search_all').button();
		});
		</script>
		<?php
	}
	if($g_view['has_verified_deal']){
		?><div><img src="images/tick_ok.gif" /> Verified deal</div><?php
	}
	?>
<script>
$(function(){
	$('.link_as_button').button();
});
</script>