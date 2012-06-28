<script src="js/SpryTabbedPanels.js" type="text/javascript"></script>
<link href="css/SpryTabbedPanels.css" rel="stylesheet" type="text/css" />
<div id="explanation">
<p>Please use any of the drop-down menus to search for companies that are in our database.</p>
<p>Below you can see a "Featured Company" and recent deals that company has been involved in. If you click the Details button you shall find all the underlying information about that transaction.</p>
<p>For every company, we track identifiers associated with that company, including tickers, ISINs, Sedol codes, and any additional codes or identifiers that our users would like us to track.</p>
</div>
<?php
/*************************************
sng:22/nov/2011
We do not show the search dropdowns if I am seeing a specific company
**********************************/
if($g_view['show_search']){
	?>
	<table cellpadding="0" cellspacing="5" width="100%">
	<tr>
	<td>
	<?php
	require_once("company_extended_search_form_view.php");
	?>
	</td>
	</tr>
	</table>
	<?php
}
?>
<h2><?php echo $g_view['company_heading'];?></h2>
<div style="height:15px"></div>

<div id="company_page" class="TabbedPanels">
	<ul class="TabbedPanelsTabGroup">
	<li class="TabbedPanelsTab" tabindex="0">Summary</li>
	<li class="TabbedPanelsTab" tabindex="0">Edit Detail</li>
	<li class="TabbedPanelsTab" tabindex="0">Deals</li>
	</ul>

	<div class="TabbedPanelsContentGroup">
		<div class="TabbedPanelsContent">
		<?php require("company_overview.php");?>
		</div>
		<!--<div class="TabbedPanelsContent">
		<?php //require("company_detail_view.php");?>
		</div>-->
		<div class="TabbedPanelsContent"><?php require("company_suggestion.php");?></div>
		<div class="TabbedPanelsContent">
		<?php require("company_recent_deals_view.php");?>
		</div>      
	</div>

</div>
<script type="text/javascript">
var tabbed_company_page = new Spry.Widget.TabbedPanels("company_page");
var pp = tabbed_company_page.showPanel;
tabbed_company_page.showPanel = function(elementOrIndex){
	if (typeof elementOrIndex == "number"){
		tpIndex = elementOrIndex;
	}else{
		// Must be the element for the tab or content panel.
		tpIndex = this.getTabIndex(elementOrIndex);
	}
	
	//call the original function, that is apply the function on our
	//tabbed panel object with the data
	pp.apply(tabbed_company_page,[elementOrIndex]);
	//now that the tab is loaded, check which tab is loaded and trigger
	
	switch(tpIndex){
	
		case 1:
			can_edit_alert();
			break;
		default:
			//do nothing
	}
}
</script>