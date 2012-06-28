<h2><?php echo $g_view['data']['name'];?></h2>
<div style="height:15px"></div>

<?php
/********************************************
sng:8/may/2012
We need to put tabbed panes here
****************/
?>
<script src="js/SpryTabbedPanels.js" type="text/javascript"></script>
<link href="css/SpryTabbedPanels.css" rel="stylesheet" type="text/css" />

<div id="firm_detail" class="TabbedPanels">
	<ul class="TabbedPanelsTabGroup">
		<li class="TabbedPanelsTab" tabindex="0">Detail</li>
		<li class="TabbedPanelsTab" tabindex="0">Edit Detail</li>
	</ul>
	<div class="TabbedPanelsContentGroup">
		<div class="TabbedPanelsContent"><?php require("firm_overview.php");?></div>
		<div class="TabbedPanelsContent"><?php require("firm_edit.php");?></div>
	</div>
</div>
<script type="text/javascript">
var tabbed_deal_page = new Spry.Widget.TabbedPanels("firm_detail");
</script>