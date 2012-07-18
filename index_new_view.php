<?php
/*********************
sng:5/mar/2012
We now show admin verified deals with an icon
*********************/
$g_view['has_verified_deal'] = false;
?>
<div id="explanation">
<p>Welcome to Data Central Exchange, where we improve communication between banks and data vendors.</p>
<p>To the left, you can see a simple template, where any registered user, from a leading bank or advisory firm, may submit a notification of a new transaction. We capture the basic elements of the transaction and notify other deal participants of the submission. This allows cross-verification and avoids duplication of effort.</p>
<p>To the right, you can see high level information of the latest deals submitted to Data CX. We provide this snapshot, to help an advisor avoid duplication of effort. It also allows the advisor check the details provided, in case there are corrections or additional information that merits inclusion.</p>
</div>
<?php
/********************
sng:16/july/2012
Now we are embedding the league table view here
**********************/
?>
<div>
<?php
require("embedded_league_table_view.php");
?>
</div>
<table style="width:100%;" cellpadding="0" cellspacing="0">
<tr>
<td class="th_orange" style="width:49%">Simple Submission Template</td>
<td class="th_orange">Recent Submissions</td>
</tr>
<td style="padding-right:20px;">
<?php
require("simple_submission.php");
?>
</td>
<td style="border-left:1px solid #CCCCCC;padding:0px 10px 0px 10px;">
<!--//////////////////////////////////5 deals from each deal type////////////////////////////////-->
<div style="display: block; width:100%;" id="results" >
<table cellpadding="0" cellspacing="0" class="company" style="width:auto;">
<tr><td colspan="7" style="border:0"><strong>M&amp;A Deals: 5 Most Recent</strong></td></tr>
<tr><td colspan="7" style="height:15px;border-left:0px;border-right:0px;border-top:0px;"></td></tr>
<?php
$g_view['data_count'] = $g_view['ma_data_count'];
$g_view['data'] = $g_view['ma_data'];
require("index_new_snippet.php");
?>
<tr><td colspan="7" style="height:15px;border-left:0px;border-right:0px;border-bottom:0px;"></td></tr>
<tr><td colspan="7" style="border:0"><strong>Equity Deals: 5 Most Recent</strong></td></tr>
<tr><td colspan="7" style="height:15px;height:15px;border-left:0px;border-right:0px;border-top:0px;"></td></tr>
<?php
$g_view['data_count'] = $g_view['eq_data_count'];
$g_view['data'] = $g_view['eq_data'];
require("index_new_snippet.php");
?>
<tr><td colspan="7" style="height:15px;border-left:0px;border-right:0px;border-bottom:0px;"></td></tr>
<tr><td colspan="7" style="border:0"><strong>Debt Deals: 5 Most Recent</strong></td></tr>
<tr><td colspan="7" style="height:15px;height:15px;border-left:0px;border-right:0px;border-top:0px;"></td></tr>
<?php
$g_view['data_count'] = $g_view['dbt_data_count'];
$g_view['data'] = $g_view['dbt_data'];
require("index_new_snippet.php");
?>
</table>
<?php
if($g_view['has_verified_deal']){
	?><div><img src="images/tick_ok.gif" /> Verified deal</div><?php
}
?>
</div>

<!--//////////////////////////////////5 deals from each deal type////////////////////////////////-->
</td>
<tr>
</tr>
</table>
<script>
/*******************
sng:25/jan/2012
The embedded simple_submission_view has a facility so that other codes can
register a function to listen when cat/sub cat/sub sub cat change

We register our function to receive notification and call the ajax search function.
The result set has also onclick function that calls the ajax search function
*********************/
var index_category_change_listener = function(cat,sub_cat,sub_sub_cat){
	simple_deal_search_by_deal_type(cat,sub_cat,sub_sub_cat,0);
}
function simple_deal_search_by_deal_type(cat,sub_cat,sub_sub_cat,start){
	$('#results').addClass('loading');
	$.post('ajax/suggest_a_deal_simple_search_by_deal_type.php?start='+start,
    	{
		deal_cat_name:cat,
		deal_subcat1_name:sub_cat,
		deal_subcat2_name:sub_sub_cat
		},
        function(data) {
            $('#results').html(data).removeClass('loading');
       	}
    );
}
$(function(){
	set_category_change_listener(index_category_change_listener);
});

</script>