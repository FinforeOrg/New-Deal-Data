<!DOCTYPE html>
<html>
<head>
<title>deal Alert</title>
</head>
<body>
</body>
</html>
<?php
global $g_http_path;

?>
<table style='margin: 0 auto;'>
	<tr>
		<td align='center' valign='top'>
			<table width='100%' border='0'>
				<tr>
					<td align='center' valign='top'>
						<table width='100%' border='0'>
							<tr>
								<td><img height='65' width='236' alt='' src='<?php echo $g_http_path;?>/images/mytombstones_logo.gif'></td>
							</tr>
							<tr>
								<td>
									<div style='font:14px Arial,sans-serif;'> Hello <?php echo $email_data['l_name'];?> , <br  /><br  /> <?php echo $email_data['dealCount'];?> new deals have been added in the '<?php echo $email_data['label'];?>' section of <a href='<?php echo $g_http_path;?>/' >,<?php echo $g_http_path;?>.</a> . <br /><br />
Below you can find the list of added transactions.
									</div>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>                 
<?php
foreach ($email_data['dealArray'] as $deal) {
$link = $g_http_path.'/hitCount.php?referer=savedSearch&token='.base64_encode('deal_detail.php?deal_id=' . $deal['deal_id']);
//var_dump($deal);
?>

<table style='border: 1px solid #CCCCCC; width: 210px;  text-decoration: none; float: left; margin-left: 10px; margin-bottom:5px;'> 
	<tbody>
		<tr>
			<td style='cursor: pointer; text-align: center;color: #3B3B3B; font: 11px/18px Tahoma,Geneva,sans-serif;text-align: left; height:210px'>
			<?php
			/*************
			Now we have multiple participants and their logo. What we do it show the first logo.
			However, if none found, then we show the company names
			*****************/
			$sng_logo = "";
			if(count($deal['logo']) > 0){
				$sng_logo = $deal['logo'][0]['logo'];
			}
			if (strlen($sng_logo) && is_file($filename = dirname(dirname(__FILE__)) . "/uploaded_img/logo/thumbnails/" . $sng_logo)) {
			?>
			<a style='text-decoration: none; cursor: pointer; display:block; text-align:center' href='<?php echo $link;?>'>
			<img src='<?php echo LOGO_IMG_URL;?>/<?php echo $sng_logo;?>' style='border: 0 none;' align='center'>
			</a>
			<?php               
			} else {
				if(count($deal['companies']) > 0){
					?>
					<a style='text-decoration: none; cursor: pointer; display:block; text-align:center;color: #E86200; outline: medium none;    font-size: 14px;  font-weight: bold;' href='<?php echo $link;?>'>
					<?php
					foreach($deal['companies'] as $participant){
						echo $participant['company_name'];?><br /><?php
					}
					?>
					</a>
					<?php
				}
			}
			?>
			</td>
		</tr>
		<tr>
			<td align='center' style='width: 40px; text-align: center;center;color: #3B3B3B; font: 11px/18px Tahoma,Geneva,sans-serif;text-align: left;'>&nbsp;</td>
		</tr>
		<tr>
			<td style=' width: 210px; height: 110px;'>           
				<a href='<?php echo $link;?>' style='display: block; width: 100%;height: 100%;cursor: pointer;center;color: #3B3B3B; font: 11px/18px Tahoma,Geneva,sans-serif;text-align: left; color: #000000; font-family: Verdana,Arial,Helvetica,sans-serif; font-size: 14px; font-weight: bold; height: 110px; padding: 5px; text-align: center; vertical-align: middle; text-decoration: none;'><?php echo $deal['deal_cat_name'];?>
<?php
if ($deal['deal_subcat2_name'] != 'n/a'){
	echo " ".$deal['deal_subcat2_name'];
}else{
	echo " ".$deal['deal_subcat1_name'];
}
/*************
sng:10/jul/2010
deal value may be unknon or undisclosed. In that case, 0 is stored.
if that is the case then show 'not disclosed'

sng:24/jan/2012
Now we have deal range if deal value is not known
Of course, if both are 0 it means, value is not known, exact or otherwise.
However, if we have exact deal value, that takes priority.
***************/
$sng_deal_value = "";

if((0==$deal['value_in_billion'])&&(0==$deal['value_range_id'])){
	$sng_deal_value = "Not disclosed";
}elseif($deal['value_in_billion'] > 0){
	$sng_deal_value = "US $ ".number_format($deal['value_in_billion'] * 1000, 0 )." million";
}else{
	$sng_deal_value = $deal['fuzzy_value'];
}
?>
<br /><br />
<?php echo $sng_deal_value;?>
<br /><br />
<?php echo date('M Y', strtotime($deal['date_of_deal']));?>
				</a>
			</td>
		</tr>
	</tbody>
</table> 
<?php
}
?>