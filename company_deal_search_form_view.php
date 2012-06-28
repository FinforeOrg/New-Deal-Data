<?php
/***
sng:29/apr/2010
Since we bow embed the company name from this form to the filter search form, we now cannot
differentiate the 2 search methods by presence of the field deal_company.
So now we need another hidden field to differentiate
********/
?>
<!--search form-->
<form method="post" action="deal_search.php">
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="searchinput">
<tr>
<td>
<input type="hidden" name="action" value="search" />
<input type="hidden" name="company_deal_search" value="" />
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="input">
<tr>
<td><input type="text" name="deal_company" id="deal_company" value="<?php echo $g_view['deal_company_form_input'];?>" title="company name" alt="company name" /></td>
</tr>
</table>
</td>
<td>&nbsp;</td>
<td><input name="submit" type="submit" class="btn_auto" id="button" value="Search" /></td>
</tr>
</table>
</form>
<!--search form-->