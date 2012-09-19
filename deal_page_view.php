<?php
if(!$g_view['deal_found']){
	?>
	<h1>Deal data not found</h1>
	<?php
	return;
}
?>
<script src="js/SpryTabbedPanels.js" type="text/javascript"></script>
<link href="css/SpryTabbedPanels.css" rel="stylesheet" type="text/css" />
<script src="js/jquery.form.js" type="text/javascript"></script>

<script type="text/javascript" src="admin/js/datepicker.js"></script>
<link href="admin/css/datepicker.css" rel="stylesheet" type="text/css" />

<?php
/*******************
sng:21/mar/2012
Unfortunately, the jqury ui autocomplete and the devbridge autocomplete just clash with each other

I have made a copy of devbridge autocomplete and renamed the method to devbridge_autocomplete
*********************/
?>
<script src="js/jquery.devbridge.autocomplete.js"></script>
<link type="text/css" rel="stylesheet" href="css/devbridge-autocomplete.css" />

<table width="100%" cellpadding="0" cellspacing="0">
<tr>
<td class="deal_page_h1"><?php echo $deal_support->deal_page_show_deal_type_data_heading($g_view['deal_data'])?></td>
<td class="deal_page_h1" style="text-align:right"><?php echo convert_deal_value_for_display_round($g_view['deal_data']['value_in_billion'],$g_view['deal_data']['value_range_id'],$g_view['deal_data']['fuzzy_value']);?></td>
</tr>
<tr>
<?php
/******************************
sng:1/feb/2012
We need to show the name of the participants, just their names, not sectors or countries.
Since we have more than one companies associated with a deal, we use another function. This just show the company name as csv

<td class="deal_page_h2"><?php echo $deal_support->deal_page_show_company_heading($g_view['deal_data']);?></td>
********************/
?>
<td class="deal_page_h2"><?php echo $deal_support->deal_page_show_participants_heading($g_view['deal_data']['participants']);?></td>
<td colspan="2" class="deal_page_h2" style="text-align:right"><?php echo ymd_to_dmy($g_view['deal_data']['date_of_deal']);?></td>
</tr>
<tr>
<td></td>
<td style="text-align:right"><span id="stat_add_deal_to_watch_list" class="msg_txt"></span> <input type="button" class="btn_auto" value="Add to watchlist" id="btn_add_to_watchlist" onclick="return add_deal_to_watch_list(<?php echo $g_view['deal_data']['deal_id'];?>);" /></td>
</tr>
<tr><td colspan="2" style="height:20px;">&nbsp;</td></tr>
</table>
<div id="deal_page" class="TabbedPanels">
	<ul class="TabbedPanelsTabGroup">
	<li class="TabbedPanelsTab" tabindex="0">Detail</li>
	<li class="TabbedPanelsTab" tabindex="0">Advisors</li>
	<li class="TabbedPanelsTab" tabindex="0">People</li>
	<li class="TabbedPanelsTab" tabindex="0">Edit Detail</li>
	
	<li class="TabbedPanelsTab" tabindex="0">Discussion Page</li>
	<li class="TabbedPanelsTab" tabindex="0">Case Studies</li>
	</ul>

	<div class="TabbedPanelsContentGroup">
		<?php
		/******************
		sng:14/sep/2012
		Vorsicht: If you add new tabs here, be sure to change the codes below for spiry
		******************/
		?>
		<div class="TabbedPanelsContent">
		<?php require("deal_page_overview.php");?>
		</div>
		<div class="TabbedPanelsContent">
		<?php require("deal_page_partners.php");?>
		</div>
		<div class="TabbedPanelsContent">
		<?php require("deal_page_partners_members.php");?>
		</div>      
		<div class="TabbedPanelsContent">
		<?php 
		/*
		sng:19/mar/2012
		For now, testing
		require("deal_page_detail.php")*/
		require("deal_page_detail_suggestion.php");?>
		</div>
		
		<div class="TabbedPanelsContent">
		<?php require("deal_page_deal_discussion.php");?>
		</div>
		<div class="TabbedPanelsContent">
		<?php require("deal_page_case_study.php");?>
		</div>
	</div>

</div>
<script type="text/javascript">
/**********************************************
sng:18/july/2011
This is now loaded with the detail page. That means, the script is fired when the overview tab is shown.
But we need to fire the update_discussion only when the discussion tab has focus. So we trap the spry showPanel
to check if discussion tab is about to be shown and only then we trigger the script.
**********************************/
	<!--


   var tabbed_deal_page = new Spry.Widget.TabbedPanels("deal_page");
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
		/************************
		sng:17/nov/2011
		More contents to be fetched via ajax in just in time fashion
		if(tpIndex == 3){
			update_discussion();
		}
		**********************/
		switch(tpIndex){
			case 0:
				//overview
				break;
			case 1:
				//banks and law firms
				break;
			case 2:
				//team members
				update_members();
				break;
			case 3:
				//edit
				can_edit_alert();
				break;
			case 4:
				//discussion
				update_discussion();
				break;
			case 5:
				//case study
				update_case_study();
				break;
			default:
				//do nothing
		}
   }
   <?php
   /***********************************************************
   sng:9/sep/2011
	We now check which tab we need to open.
	Directly opening the discussion tab does not fire the update code, so we call showPanel using from our code
	******************************************/
if(isset($_GET['viewtab'])&&($_GET['viewtab']!="")){
	if($_GET['viewtab'] == "discussion"){
		?>
		tabbed_deal_page.showPanel(4);
		<?php
	}
}
?>
	//-->
</script>
<script>
function add_deal_to_watch_list(this_deal_id){
	jQuery("#stat_add_deal_to_watch_list").html("adding...");
	jQuery.get(
		'ajax/add_deal_to_watchlist.php?deal_id='+this_deal_id,
		function(data) {
			if(data.status==0){
				jQuery('#stat_add_deal_to_watch_list').html(data.reason);
			}else{
				jQuery('#stat_add_deal_to_watch_list').html('Added to watchlist');
				jQuery('#btn_add_to_watchlist').remove();
			}
		},
		"json"
	)
}
</script>
<?php
/*******************
sng:6/mar/2012
We have put a button in the overview tab. Clicking that takes the user to 'edit Details' tab
*******************/
?>
<script>
function goto_edit_tab(){
	tabbed_deal_page.showPanel(3);
}
</script>