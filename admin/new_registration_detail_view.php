<script type="text/javascript">
function check(){
	if(document.getElementById('company_id').value==0){
		alert("The company is not set. Either create the company or reject this request");
		return false;
	}
	return true;
}
</script>
<table width="100%" cellpadding="5" cellspacing="0" border="0">
<tr>
<td><span class="msg_txt"><?php echo $g_view['msg'];?></span></td>
</tr>
<tr>
<td>
<form method="post" action="" name="frm_opts" onsubmit="return check();">
<input type="hidden" name="action" value="accept" />
<input type="hidden" name="uid" value="<?php echo $_POST['uid'];?>" />
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
<?php echo $g_view['data']['company_name'];?> [<?php echo $g_view['company_status'];?>]


<input type="hidden" name="company_id" id="company_id" value="<?php echo $g_view['company_id'];?>" />
</td>
</tr>

<tr>
<td>Designation :</td>
<td>
<?php 
if($g_view['data']['designation']!='others')
{
 $designation=$g_view['data']['designation'];
 echo $designation;
}
else
{
  $designation=$g_view['data']['designation_other'];
  echo $designation;
}
?>
<input type="hidden" name="designation" value="<?php echo $designation;?>" />
</td>
</tr>

<tr>
<td>Year Joined :</td>
<td>
<?php echo $g_view['data']['year_joined'];?>
<input type="hidden" name="year_joined" value="<?php echo $g_view['data']['year_joined'];?>" />
</td>
</tr>

<tr>
<td>Location :</td>
<td>
<?php echo $g_view['data']['posting_country'];?>
<input type="hidden" name="posting_country" value="<?php echo $g_view['data']['posting_country'];?>" />
</td>
</tr>
<tr>
<td>Division :</td>
<td>
<?php echo $g_view['data']['division'];?>
<input type="hidden" name="division" value="<?php echo $g_view['data']['division'];?>" />
</td>
</tr>
<tr>
<td></td>
<td><input type="submit" name="submit" value="Accept" /></td>
</tr>
</table>
</form>
</td>
</tr>
<?php
/****
sng:6/apr/2010
If admin is rejecting the request, admin should give a reason
**********/
?>
<tr>
<td>
<form method="post" action="">
<input type="hidden" name="action" value="reject" />
<input type="hidden" name="uid" value="<?php echo $_POST['uid'];?>" />
<table width="100%">
<tr>
<td colspan="2">If this data is questionable, you can reject the application.</td>
</tr>
<tr>
<td style="vertical-align: top;">Reason</td>
<td>
<textarea name="reject_reason" style="width:400px; height:40px;"></textarea>
</td>
</tr>
<tr>
<td>&nbsp;</td>
<td><input type="submit" name="submit" value="Reject" />
</tr>
</table>
</form>
</td>
</tr>
</table>
