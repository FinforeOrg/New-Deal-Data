<table width="100%" cellpadding="5" cellspacing="0" border="1" style="border-collapse:collapse;">
  <?php
if($g_view['msg']!=""){
?>
  <tr>
    <td colspan="2"><span class="msg_txt"><?php echo $g_view['msg'];?></span></td>
  </tr>
  <?php
}
?>
  <tr>
    <td colspan="2"><form method="post" action="">
      <input type="hidden" name="action" value="add_country" />
      <table cellpadding="" cellspacing="10" border="0">
        <tr>
          <td>Add a country</td>
          <td><select name="country_id">
            <option value="" selected="selected">Select country</option>
            <?php
for($country_i=0;$country_i<$g_view['country_data_count'];$country_i++){
	?>
            <option value="<?php echo $g_view['country_data'][$country_i]['id'];?>"><?php echo $g_view['country_data'][$country_i]['name'];?></option>
            <?php
}
?>
          </select>
                <span class="err_txt"> *</span>
                
          <td><input type="submit" name="submit" value="Add" /></td>
        </tr>
		<tr>
		<td>&nbsp;</td>
		<td><span class="err_txt"><?php echo $g_view['err']['country'];?></span></td>
		</tr>
      </table>
    </form></td>
  </tr>
  <tr bgcolor="#dec5b3" style="height:20px;">
    <td colspan="2"><strong>Name</strong></td>
  </tr>
  <?php
if($g_view['region_country_data_count']==0){
	?>
  <tr>
    <td colspan="2">No countries found for this region</td>
  </tr>
  <?php
}else{
	for($i=0;$i<$g_view['region_country_data_count'];$i++){
		?>
  <tr>
    <td><?php echo $g_view['region_country_data'][$i]['name'];?></td>
	<td>
	<form method="post" action="">
	<input type="hidden" name="action" value="remove_country" />
	<input type="hidden" name="country_id" value="<?php echo $g_view['region_country_data'][$i]['country_id'];?>"  />
	<input type="submit" name="submit" value="Remove" />
	</form>
	</td>
  </tr>
  <?php
	}
}
?>
</table>
