<script type="text/javascript" src="js/datepicker.js"></script>
<link href="css/datepicker.css" rel="stylesheet" type="text/css" />
<table width="100%" cellpadding="5" cellspacing="0" border="1" style="border-collapse:collapse;">
<?php
if($g_view['msg']!=""){
?>
<tr>
<td colspan="6"><span class="msg_txt"><?php echo $g_view['msg'];?></span></td>
</tr>
<?php
}
?>
<tr>
<td colspan="6">
<form method="post" action="" >
<input name="action" type="hidden" value="edit" />
<table cellpadding="0" cellspacing="5" border="0" >
<tr>
<td colspan="7">
Admin set a range like<br />
2008: 2008-01-01 to 2008-12-31<br />
2Q 2010: 2010-04-01 to 2010-06-30<br />
2008-2009: 2008-01-01 to 2009-12-31<br />
2010YTD: 2010-01-01 to (blank)<br />
</td>
</tr>
<tr>
<td>Preset Name</td>
<td><input type="text" name="name" value="<?php echo $g_view['data']['name'];?>" style="width:100px;" /><span class="err_txt"> *</span></td>
</tr>
<tr>
<td colspan="2"><span class="err_txt"><?php echo $g_view['err']['name'];?></span></td>
</tr>
<tr>
<td>Date From</td>
<td>
<input name="date_from" id="date_from" type="text" style="width:100px;" value="<?php echo $g_view['data']['date_from'];?>" /><span class="err_txt"> *</span>
<script type="text/javascript">
      // <![CDATA[       
        var opts = {                            
                formElements:{"date_from":"Y-ds-m-ds-d"},
                showWeeks:false                    
        };      
        datePickerController.createDatePicker(opts);
      // ]]>
      </script>
</td>
<td>To</td>
<td>
<input name="date_to" id="date_to" type="text" style="width:100px;" value="<?php echo $g_view['data']['date_to'];?>" /><span class="err_txt"> *</span>
<script type="text/javascript">
      // <![CDATA[       
        var opts = {                            
                formElements:{"date_to":"Y-ds-m-ds-d"},
                showWeeks:false                    
        };      
        datePickerController.createDatePicker(opts);
      // ]]>
      </script>
</td>
</tr>
<tr>
<td colspan="2"><span class="err_txt"><?php echo $g_view['err']['date_from'];?></span></td>
<td colspan="2"><span class="err_txt"><?php echo $g_view['err']['date_to'];?></span></td>
<td>&nbsp;</td>
</tr>
<tr>
<td><input type="submit" name="submit" value="Update" />
</tr>

</table>
</form>
</td>
</tr>
</table>