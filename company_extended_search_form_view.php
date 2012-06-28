<?php
/**************************
sng:1/oct/2011
we now put these in container view
<script src="js/jquery-ui-1.8.11.custom.min.js" type="text/javascript"></script>  
<script src="js/jquery.ui.selectmenu.js" type="text/javascript"></script>
<link rel="stylesheet" href="css/custom-theme/jquery-ui-1.8.9.custom.css" />
<link rel="stylesheet" href="css/custom-theme/jquery.ui.selectmenu.css" /> 
*************************************/
?>
<script type="text/javascript">
$(function() {
    $('#sector').selectmenu().change(function(idx){
        $.post(
            'admin/ajax/industry_list_for_sector.php?for=leagueTables',
            {'sector' : $(this).selectmenu('value')},
            function(data) {
                $('#industry').html(data).selectmenu();
            }
        )
        /*alert($(this).selectmenu('value') )*/
    });
    $('input[type="button"]').button();
    $('input[type="submit"]').button();         
    $('select').selectmenu();         
});
</script>
<form method="post" action="company_extended_search.php">
<input type="hidden" name="myaction" value="extended_search" />
<table cellpadding="0" cellspacing="5" width="100%">
<tr>
<td width="20%">
<select name="region">
<option value="" style="width: 200px;">Any Region</option>
<?php
for($i=0;$i<$g_view['region_count'];$i++){
	?>
	<option value="<?php echo $g_view['region_list'][$i]['id'];?>" <?php if($_POST['region']==$g_view['region_list'][$i]['id']){?>selected="selected"<?php }?>><?php echo $g_view['region_list'][$i]['name'];?></option>
	<?php
}
?>
</select>
</td>
<td width="20%">
<select name="country">
<option value="" style="width: 200px;">Any Country</option>
<?php
for($i=0;$i<$g_view['country_count'];$i++){
	?>
	<option value="<?php echo $g_view['country_list'][$i]['name'];?>" <?php if($_POST['country']==$g_view['country_list'][$i]['name']){?>selected="selected"<?php }?>><?php echo $g_view['country_list'][$i]['name'];?></option>
	<?php
}
?>
</select>
</td>
<td width="20%">
<select name="sector" id="sector" onchange="" style="width: 200px;">
<option value="">Any Sector</option>
<?php
for($i=0;$i<$g_view['sector_count'];$i++){
	?>
	<option value="<?php echo $g_view['sector_list'][$i]['sector'];?>" <?php if($_POST['sector']==$g_view['sector_list'][$i]['sector']){?>selected="selected"<?php }?>><?php echo $g_view['sector_list'][$i]['sector'];?></option>
	<?php
}
?>
</select>
</td>
<td width="20%">
<select name="industry" id="industry" style="width: 200px;">
<option value="">Any Industry</option>
<?php
for($i=0;$i<$g_view['industry_count'];$i++){
	?>
	<option value="<?php echo $g_view['industry_list'][$i]['industry'];?>" <?php if($_POST['industry']==$g_view['industry_list'][$i]['industry']){?>selected="selected"<?php }?>><?php echo $g_view['industry_list'][$i]['industry'];?></option>
	<?php
}
?>
</select>
</td>
<td><input type="submit" name="submit" value="SEARCH" class="btn_auto" /></td>
</tr>
</table>
</form>