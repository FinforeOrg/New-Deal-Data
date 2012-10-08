<style type="text/css">
.hr_div
{
	height:10px;
	margin-top:20px;
	border-top:1px solid #CCCCCC;
}
</style>
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
<table width="100%">
<tr>
<td>
<div id="edit_deal_detailed" class="TabbedPanels">
	<ul class="TabbedPanelsTabGroup">
		<li class="TabbedPanelsTab" tabindex="0">Participants</li>
		<li class="TabbedPanelsTab" tabindex="1">Details</li>
		<li class="TabbedPanelsTab" tabindex="2">Advisors</li>
		<li class="TabbedPanelsTab" tabindex="3">People</li>
		<li class="TabbedPanelsTab" tabindex="4">Notes</li>
		<li class="TabbedPanelsTab" tabindex="5">Sources</li>
		<li class="TabbedPanelsTab" tabindex="6">Case Studies</li>
	</ul>
	<div class="TabbedPanelsContentGroup">
		<div class="TabbedPanelsContent">
		<?php //require("admin/deal_edit_snippets/deal_notes.php");?>Participants
		</div>
		<div class="TabbedPanelsContent">
		<?php //require("admin/deal_edit_snippets/deal_notes.php");?>Details
		</div>
		<div class="TabbedPanelsContent">
		<?php //require("admin/deal_edit_snippets/deal_sources.php");?>Advisors
		</div>
		<div class="TabbedPanelsContent">
		<?php require("admin/deal_edit_snippets/deal_members.php");?>
		</div>
		<div class="TabbedPanelsContent">
		<?php require("admin/deal_edit_snippets/deal_notes.php");?>
		</div>
		<div class="TabbedPanelsContent">
		<?php require("admin/deal_edit_snippets/deal_sources.php");?>
		</div>
		<div class="TabbedPanelsContent">
		<?php //require("admin/deal_edit_snippets/deal_sources.php");?>Case Studies
		</div>
	</div>
</div>
</td>
</tr>
</table>
<script>
var tabbed_deal_page = new Spry.Widget.TabbedPanels("edit_deal_detailed");
//get the original definition
var pp = tabbed_deal_page.showPanel;
//now write our own definition which is invoked
tabbed_deal_page.showPanel = function(elementOrIndex){
	if (typeof elementOrIndex == "number")
		tpIndex = elementOrIndex;
	else // Must be the element for the tab or content panel.
		tpIndex = this.getTabIndex(elementOrIndex);

	//call the original function, that is apply the function on our
	//tabbed panel object with the data
	pp.apply(tabbed_deal_page,[elementOrIndex]);
	//now that the tab is loaded, check which tab is loaded and trigger
	
	switch(tpIndex){
		case 0:
			//participants
			break;
		case 1:
			//details
			break;
		case 2:
			//advisors
			break;
		case 3:
			//people
			fetch_deal_members_for_admin();
			break;
		case 4:
			//notes
			fetch_deal_notes_for_admin();
			break;
		case 5:
			//sources
			fetch_deal_sources_for_admin();
			break;
		case 6:
			//case studies
			break;
		default:
			//do nothing
	}
}
</script>
<script>
$(function(){
	//by default, the first tab is highlighted, so call the initialization ajax call for first tab
});
</script>