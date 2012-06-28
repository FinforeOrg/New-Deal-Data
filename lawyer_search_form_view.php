<!--search form-->
<form method="post" action="lawyer_search.php">
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="searchinput">
<tr>
<td>
<input type="hidden" name="action" value="search" />
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="input">
<tr>
<td><input type="text" name="search" id="search" value="<?php echo $g_view['search_form_input'];?>" title="lawyer name" alt="lawyer name" /></td>
</tr>
</table>
</td>
<td>&nbsp;</td>
<td><input name="submit" type="submit" class="btn_auto" id="button" value="Search" /></td>
</tr>
</table>
</form>
<!--search form-->