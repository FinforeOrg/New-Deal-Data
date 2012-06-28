<script type="text/javascript">
function goto_home(){
	window.location="index.php";
}
</script>
<table width="100%">
<tr><td><span class="msg_txt"><?php echo $msg;?></span></td></tr>
</table>
<form method="post" action="register_add_deal.php">
<input type="hidden" name="action" value="add_deal" />
<input type="hidden" name="registration_req_id" value="<?php echo $g_view['req_id'];?>" />
<table width="100%" cellpadding="0" cellspacing="0" class="company">
<tr>
<th>&nbsp;</th>
<th>Company</th>
<th>Date</th>
<th>Type</th>
<th>Value (in million USD)</th>
</tr>
<?php
if($deal_count == 0){
	?>
	<tr>
	<td colspan="5">
	<?php
	/****
	sng:30/july/2010
	There are no deals to add. We show a note, show the message that admin is checking the registration,or if favoured,
	activation email has been sent, and a button
	We also need to get the email address where suggestion is sent
	***/
	require_once("classes/class.sitesetup.php");
	$mail_data = array();
	$success = $g_site->get_site_emails($mail_data);
	if(!$success){
		//well, we will not hang a registration script just because we cannot send email
		$suggestion_email = "suggestions@deal-data.com";
	}else{
		$suggestion_email = $mail_data['suggestion_email'];
	}
	?>
	<p>
	Currently our database has no deals listed for your firm. It is possible that we have incorrectly allocated you firm's transctions to a different variation of your firm's name. Please send a message to our team at <a href="mailto:<?php echo $suggestion_email;?>"><?php echo $suggestion_email;?></a> and we shall investigate for you. If you have a list of transactions for your firm that have been omitted, we would be grateful if you could attach details. Many thanks for your help.</p>
	<?php
	if($g_view['is_favoured']){
		?>
		<p>
		We have sent the welcome email to your work email. It contains the activation link. You will have to click that link before you can log-in.
		</p>
		<?php
	}else{
		?>
		<p>
		We are checking your registration details. Please <strong>DO NOT</strong> try and log-in until you have received the welcome email. Thank you for your patience.
		</p>
		<?php
	}
	?>
	<br />
	<input type="button" value="Continue" class="btn_auto" onclick="return goto_home();" />
	<br /><br />
	</td>
	</tr>
	
	<?php
}else{
	for($t=0;$t<$deal_count;$t++){
		?>
		<tr>
		<td><input type="checkbox" name="transaction_id[]" value="<?php echo $deal_data[$t]['id'];?>" /></td>
		<td><?php echo $deal_data[$t]['name'];?></td>
		<td><?php echo $deal_data[$t]['date_of_deal'];?></td>
		<td><?php echo $deal_data[$t]['deal_cat_name'];?></td>
		<td>
		<?php
		/****
		sng:10/jul/2010
		if the deal value is 0, then deal value is not disclosed
		***/
		if($deal_data[$t]['value_in_billion']==0){
			?>
			not disclosed
			<?php
		}else{
			echo convert_billion_to_million_for_display($deal_data[$t]['value_in_billion']);
		}
		?>
		
		</td>
		</tr>
		<?php
	}
	?>
	<tr><td style="text-align:right" colspan="5"><input type="submit" value="Add" class="btn_auto" /></td></tr>
	<?php
}
?>
</table>
