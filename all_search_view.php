<?php
die("not used");
/***
20/aug/2010
We allow user to enter search term. We get rid of the dropdown.
Now the code search all areas and shows the result in multiple sections.
Since there will be many items in each search area, we only show top few options
**********/
require_once("classes/class.magic_quote.php");
?>
<script type="text/javascript">
function validate_search(){
	var search_term = document.getElementById('top_search_term').value;
	if(search_term==""){
		document.getElementById('top_search_term').value = "Please specify search term";
		return false;
	}
}
</script>
<!--search form-->
<form method="post" action="search_all.php" onsubmit="return validate_search();">
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="searchinput">
<tr>
<td style="width:30px;">&nbsp;</td>
<td>
<input type="hidden" name="myaction" value="search" />
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="input">
<tr>
<td><input type="text" name="top_search_term" id="top_search_term" value="<?php echo $g_mc->view_to_view($_POST['top_search_term']);?>" title="search" alt="search" /></td>
</tr>
</table>
</td>
<td>&nbsp;</td>
<td><input name="submit" type="submit" class="btn_auto" id="button" value="Search" /></td>
</tr>
</table>
</form>
<!--search form-->