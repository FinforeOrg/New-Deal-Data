<?php
/****
sng:19/may/2010
The free text search box does not allow the user to specify what is to be searched.
So we put a dropdown. User select whether the search term is a company name or deal or what

sng:21/nov/2011
We will use only the company, deal, bank, law firm. Also
we merge the bank search and law firm search
*******/
require_once("classes/class.magic_quote.php");
?>
<script type="text/javascript">
function validate_top_search(){
	var obj = document.getElementById('top_search_area');
	var area_selected = obj.options[obj.selectedIndex].value;
	var search_term = document.getElementById('top_search_term').value;
	
	if(search_term==""){
		alert("Please specify search term");
		return false;
	}
	if(area_selected==""){
		alert("Please select the search area");
		return false;
	}
	if(area_selected=="company"){
		document.getElementById('top_search_frm').action="default_search_company.php";
		return true;
	}
	if(area_selected=="deal"){
		document.getElementById('top_search_frm').action="default_search_deal.php";
		return true;
	}
	if(area_selected=="bank"){
		document.getElementById('top_search_frm').action="default_search_bank.php";
		return true;
	}
	if(area_selected=="law_firm"){
		document.getElementById('top_search_frm').action="default_search_law_firm.php";
		return true;
	}
	
	
	return false;
}
</script>
<!--search form-->
<form id="top_search_frm" method="post" action="dummy.php" onsubmit="return validate_top_search();">
<?php
/***
otherwise, IE is giving trouble with action of the form
***/
?>
<input type="hidden" name="myaction" value="search" />
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="searchinput">
<tr>
<td>
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="input" style="width:390px;">
		<tr>
			<td><input type="text" name="top_search_term" id="top_search_term" value="<?php echo $g_mc->view_to_view($_POST['top_search_term']);?>" style="width:360px;" /></td>

		</tr>
	</table>
</td>
<td>&nbsp;</td>
<td>
<!--///////////////////////
default is set to Deals
////////////////////////////-->
<select name="top_search_area" id="top_search_area">
<option value="">select</option>
<option value="company" <?php if($_POST['top_search_area']=="company"){?> selected="selected"<?php }?>>Companies</option>
<option value="deal" <?php if((!isset($_POST['top_search_area']))||($_POST['top_search_area']=="deal")){?> selected="selected"<?php }?>>Deals</option>
<option value="bank" <?php if($_POST['top_search_area']=="bank"){?> selected="selected"<?php }?>>Banks</option>
<option value="law_firm" <?php if($_POST['top_search_area']=="law_firm"){?> selected="selected"<?php }?>>Law Firms</option>
</select>
</td>
<td>&nbsp;</td>
<td><input name="submit" type="submit" class="btn" id="button" value="Search" /></td>
</tr>
</table>
</form>
<!--search form-->
<script>
$(function(){
	$('#top_search_area').selectmenu();
});
</script>