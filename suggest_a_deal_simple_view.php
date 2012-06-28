<table style="width:100%;" cellpadding="0" cellspacing="0">
<tr>
<td class="deal_page_h2" style="text-align:center; width:50%">Simple Submission Template</td>
<td class="deal_page_h2" style="text-align:center; width:50%">Search</td>
</tr>
<td style="padding-right:20px;">
<?php
require("simple_submission.php");
?>
</td>
<td style="border-left:1px solid #CCCCCC;padding:0px 10px 0px 10px;">
<!--////////////////////////////////deal search and result/////////////////////////////////-->
<script>
_defaultInputs['search_term'] = 'e.g. Vodafone';

<?php
/******************************
sng:23/jan/2012
we now want to enter the company name and press ENTER to trigger the search.
What we do is put a form element and trap onsubmit()
********************************/
?>
$(function(){
	$('#search').button();/*.click(function(event){
        simple_deal_search(0);
        
    });*/
});
function simple_deal_search(start){
	$('#results').addClass('loading');
	$.post('ajax/suggest_a_deal_simple_search.php?start='+start,
    	{'search_term' : $('#search_term').val()},
        function(data) {
        	$('#resultsSeparator').show();
            $('#results').html(data).removeClass('loading');
       	}
    );
}
</script>
<table cellpadding="0" cellspacing="0" style="width:100%">
<tr>
<td><strong>Check if a deal is already in database:</strong></td>
</tr>
<tr><td style="height:15px;"></td></tr>
<tr>
<td>
<form method="post" action="dummy.php" onsubmit="simple_deal_search(0);return false;">
<input name="search_term" id="search_term" type="text" style="border:1px solid #CCC; width: 280px; height:20px; background:url(images/search-bk.png) top left no-repeat; padding-left:20px; line-height:20px;" class="special"><input type="submit" name="search" id="search" value="Search" style="float:right">
</form>
</td>
</tr>
<tr><td style="height:15px;"></td></tr>
<tr>
<td><strong>Search results:</strong></td>
</tr>
<tr><td style="height:15px;"></td></tr>
</table>
<hr class="gray" style="display: none;" id="resultsSeparator"/>
<div style="display: block; width:100%;" id="results" ></div>

<!--////////////////////////////////deal search and result/////////////////////////////////-->
</td>
<tr>
</tr>
</table>