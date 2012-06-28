<div id="explanation">
<p>Any visitor can run a search for deal information by selecting a "Type of Transaction" and using our drop-down menus to refine their search.</p>
<p>You can download the details to excel, or if you have registered with us, you can save any search.</p>
<p>Registered users are able to create email alerts, so they receive notifications daily, of any interesting transactions added to the database.</p>
</div>
<div>
<?php
require_once("deal_search_filter_form_view.php");
?>
</div>
<table cellpadding="0" cellspacing="0" class="company" style="width:auto">
<tr><td colspan="7" style="border:0"><h1>M&amp;A Deals: 5 Most Recent</h1></td></tr>
<tr><td colspan="7" style="height:15px;border-left:0px;border-right:0px;border-top:0px;"></td></tr>
<?php
$g_view['data_count'] = $g_view['ma_data_count'];
$g_view['data'] = $g_view['ma_data'];
require("index_snippet.php");
?>
<tr><td colspan="7" style="height:15px;border-left:0px;border-right:0px;border-bottom:0px;"></td></tr>
<tr><td colspan="7" style="border:0"><h1>Equity Deals: 5 Most Recent</h1></td></tr>
<tr><td colspan="7" style="height:15px;height:15px;border-left:0px;border-right:0px;border-top:0px;"></td></tr>
<?php
$g_view['data_count'] = $g_view['eq_data_count'];
$g_view['data'] = $g_view['eq_data'];
require("index_snippet.php");
?>
<tr><td colspan="7" style="height:15px;border-left:0px;border-right:0px;border-bottom:0px;"></td></tr>
<tr><td colspan="7" style="border:0"><h1>Debt Deals: 5 Most Recent</h1></td></tr>
<tr><td colspan="7" style="height:15px;height:15px;border-left:0px;border-right:0px;border-top:0px;"></td></tr>
<?php
$g_view['data_count'] = $g_view['dbt_data_count'];
$g_view['data'] = $g_view['dbt_data'];
require("index_snippet.php");
?>
</table>