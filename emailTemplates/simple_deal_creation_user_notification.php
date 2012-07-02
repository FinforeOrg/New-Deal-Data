<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>New Deal Notification</title>
</head>
<body>
<p>
A new transaction, that is relevant to you, has been submitted to Data-CX. Please click on the following link to review the submission, by <?php if($email_data['use_poster_email']){echo $email_data['work_email'];}else{?>a <?php echo $email_data['member_type'];?> at <?php echo $email_data['company_name'];?><?php }?>.
</p>
<p><a href="<?php echo $email_data['deal_link'];?>"><?php echo $email_data['deal_link'];?></a></p>
<p>
Deal type: <?php echo $email_data['deal_type'];?><br />
Value: <?php echo $email_data['deal_value'];?><br />
Participants:<br />
<?php echo $email_data['companies'];?><br />
Banks:<br />
<?php echo $email_data['banks'];?><br />
Law firms:<br />
<?php echo $email_data['law_firms'];?><br />
</p>
</body>
</html>
