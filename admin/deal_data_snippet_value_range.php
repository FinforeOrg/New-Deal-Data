<?php
/*********************************************************************
sng:23/jan/2012
we need the deal value range items also, in case admin is not sure about the exact value
******/
$g_view['value_range_items'] = NULL;
$g_view['value_range_items_count'] = 0;
$success = $g_deal_support->front_get_deal_value_range_list($g_view['value_range_items'],$g_view['value_range_items_count']);
if(!$success){
	die("cannot get deal size ranges");
}
/*******************************************************************/
for($j=0;$j<$g_view['value_range_items_count'];$j++){
	?>
	<input type="radio" name="value_range_id" value="<?php echo $g_view['value_range_items'][$j]['value_range_id'];?>" <?php if($g_view['value_range_items'][$j]['value_range_id']==$g_view['data']['value_range_id']){?>checked="checked"<?php }?>>&nbsp;<?php echo $g_view['value_range_items'][$j]['display_text'];?><br />
	<?php
}
//the special case of undisclosed which is treated as 0
?>
<input type="radio" name="value_range_id" value="0" <?php if(($g_view['data']['value_in_billion']==0.0)&&($g_view['data']['value_range_id']==0)){?>checked="checked"<?php }?> >&nbsp;Undisclosed<br />Or specify value:
<input name="value_in_billion" type="text" style="width:200px;" value="<?php if($g_view['data']['value_in_billion']!="0.0") echo $g_view['data']['value_in_billion'];?>" /> (in <strong>billion</strong>, USD)&nbsp;<a href="#" onclick="return fetch_correction_suggestion('value_in_million');"><img src="has_deal_suggestion.php?deal_id=<?php echo $deal_id;?>&data_name=value_in_million" /></a>&nbsp;<span class="err_txt">*</span><br />
<span class="err_txt"><?php display_flash("value_in_billion");?></span>