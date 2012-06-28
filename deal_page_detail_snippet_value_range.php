<?php
/************************
sng:10/feb/2012
We can have exact value or range.
*************************/
$g_view['value_range_items'] = NULL;
$g_view['value_range_items_count'] = 0;
$success = $deal_support->front_get_deal_value_range_list($g_view['value_range_items'],$g_view['value_range_items_count']);
if(!$success){
	die("cannot get deal size ranges");
}
/*******************************************************************/
for($j=0;$j<$g_view['value_range_items_count'];$j++){
	?>
	<input type="radio" name="value_range_id" value="<?php echo $g_view['value_range_items'][$j]['value_range_id'];?>">&nbsp;<?php echo $g_view['value_range_items'][$j]['display_text'];?><br />
	<?php
}
//the special case of undisclosed which is treated as 0
?>
<input type="radio" name="value_range_id" value="0"  >&nbsp;Undisclosed<br />Or specify value: <input type="text" name="value_in_million" class="shorttxtinput" /> (in million, USD)