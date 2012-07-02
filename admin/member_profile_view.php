<table width="100%" cellpadding="5" cellspacing="0" border="0">
<tr>
<td><span class="msg_txt"><?php echo $g_view['msg'];?></span></td>
</tr>
<tr>
<td>
<form method="post" action="" name="frm_opts">

<input type="hidden" name="mem_id" value="<?php echo $_POST['mem_id'];?>" />
<table width="100%" cellpadding="7" cellspacing="0" border="0">
<tr>
<td width="22%">First Name :</td>
<td width="78%">
<?php echo $g_view['data']['f_name'];?>
<input type="hidden" name="f_name" value="<?php echo $g_view['data']['f_name'];?>" />
</td>
</tr>


<tr>
<td>Last Name :</td>
<td>
<?php echo $g_view['data']['l_name'];?>
<input type="hidden" name="l_name" value="<?php echo $g_view['data']['l_name'];?>" />
</td>
</tr>

<tr>
<td>Type :</td>
<td>
<?php echo $g_view['data']['member_type'];?>
<input type="hidden" name="member_type" value="<?php echo $g_view['data']['member_type'];?>" />
</td>
</tr>

<tr>
<td>Home e-mail :</td>
<td>
<?php echo $g_view['data']['home_email'];?>
<input type="hidden" name="home_email" value="<?php echo $g_view['data']['home_email'];?>" />
</td>
</tr>

<tr>
<td>Work e-mail :</td>
<td>
<?php echo $g_view['data']['work_email'];?>
<input type="hidden" name="work_email" value="<?php echo $g_view['data']['work_email'];?>" />
</td>
</tr>

<tr>
<td>Firm :</td>
<td>
<?php
$sql="SELECT name,company_id FROM ".TP."company WHERE company_id='". $g_view['data']['company_id']."'";
$qry=mysql_query($sql);
$row=mysql_fetch_array($qry);
echo $row['name'];
$com_id = $row['company_id'];
			  
?>

</td>
</tr>

<tr>
<td>Designation :</td>
<td>
<?php echo $g_view['data']['designation'];?>
</td>
</tr>

<tr>
<td>Year Joined :</td>
<td>
<?php echo $g_view['data']['year_joined'];?>
</td>
</tr>

<tr>
<td>Location :</td>
<td>
<?php echo $g_view['data']['posting_country'];?>
</td>
</tr>
<tr>
<td>Division :</td>
<td>
<?php echo $g_view['data']['division'];?>
</td>
</tr>


</table>
</form>
</td>
</tr>
</table>
