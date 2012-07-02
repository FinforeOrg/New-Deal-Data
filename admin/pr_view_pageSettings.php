
<h3> <?php echo  $g_view['msg'] ?></h3>

<form name="pageSettings" method="post" action="<?php echo $g_http_path;?>/admin/pr.php?action=pageSettings">
<table width="600" border="0">
  <tr>
    <td width="267">Number of twitts to fetch </td>
    <td width="323">
      <label>
        <input type="text" name="number_of_twitts" id="number_of_twitts" style="width:200px;" value="<?php echo isset($settings['number_of_twitts']) ? $settings['number_of_twitts'] : '10'?>" />
      </label>
</td>
  </tr>
  <tr>
    <td colspan="2" align="right"><label>
      <input type="submit" name="submit" id="submit" value="Save">
    </label></td>
    </tr>

</table>
 </form>