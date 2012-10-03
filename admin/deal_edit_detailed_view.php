<script src="../js/SpryTabbedPanels.js" type="text/javascript"></script>
<link href="../css/SpryTabbedPanels.css" rel="stylesheet" type="text/css" />
<?php
/**********************************
the top header part
****/
?>
<table width="100%" cellpadding="0" cellspacing="0">
<tr>
<td class="deal_page_h1"><?php echo $deal_support->deal_page_show_deal_type_data_heading($g_view['min_deal_data'])?></td>
<td class="deal_page_h1" style="text-align:right"><?php echo convert_deal_value_for_display_round($g_view['min_deal_data']['value_in_billion'],$g_view['min_deal_data']['value_range_id'],$g_view['min_deal_data']['fuzzy_value']);?></td>
</tr>
<tr>
<td class="deal_page_h2"><?php echo $deal_support->deal_page_show_participants_heading($g_view['min_deal_data']['participants']);?></td>
<td colspan="2" class="deal_page_h2" style="text-align:right"><?php echo ymd_to_dmy($g_view['min_deal_data']['date_of_deal']);?></td>
</tr>
<tr><td colspan="2" style="height:20px;">&nbsp;</td></tr>
</table>
<?php
/****************************************
the tabbed panes
****************/
?>
<div id="edit_deal_detailed" class="TabbedPanels">
	<ul class="TabbedPanelsTabGroup">
		<li class="TabbedPanelsTab" tabindex="0">Tab 1</li>
		<li class="TabbedPanelsTab" tabindex="0">Sources</li>
	</ul>
	<div class="TabbedPanelsContentGroup">
		<div class="TabbedPanelsContent">
		Tab 1 hello
		</div>
		<div class="TabbedPanelsContent">
		Tab 2 hello
		</div>
	</div>
</div>
<script>
var tabbed_deal_page = new Spry.Widget.TabbedPanels("edit_deal_detailed");
</script>