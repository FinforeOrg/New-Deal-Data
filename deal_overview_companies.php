<?php
/*******************
sng:31/jan/2012
We now show all the companies involved in the deal. We no longer have the concept of a deal company.

Now there can be multiple companies associated with a deal, each having a role

sng:14/feb/2012
There can also be footnotes for each participant. We show it in the order under the participant list.
*********************/
?>
<fieldset>
<legend>Participants</legend>
<?php
$role_footnote_i = 0;
$deal_company_count = count($g_view['deal_data']['participants']);
for($deal_company_i=0;$deal_company_i<$deal_company_count;$deal_company_i++){
	$buyer = "";
	if($g_view['deal_data']['participants'][$deal_company_i]['sector']!=""){
		$buyer.=", ".$g_view['deal_data']['participants'][$deal_company_i]['sector'];
	}
	if($g_view['deal_data']['participants'][$deal_company_i]['industry']!=""){
		$buyer.=", ".$g_view['deal_data']['participants'][$deal_company_i]['industry'];
	}
	if($buyer!=""){
		$buyer = substr($buyer,2);
	}
	?>
	<p>
	<a href="company.php?show_company_id=<?php echo $g_view['deal_data']['participants'][$deal_company_i]['company_id'];?>"><strong><?php echo $g_view['deal_data']['participants'][$deal_company_i]['company_name'];?></strong></a> <?php if($g_view['deal_data']['participants'][$deal_company_i]['role_name']!=""){?><br /><span style="font-style:italic"><?php echo $g_view['deal_data']['participants'][$deal_company_i]['role_name'];?></span><?php }?>
	<?php
	if($g_view['deal_data']['participants'][$deal_company_i]['footnote']!=""){
		?><sup><?php echo $role_footnote_i+1;?></sup><?php
		$role_footnote_i++;
	}
	?>
	<?php
	if($buyer!=""){
		?><br /><?php echo $buyer;
	}
	
	if($g_view['deal_data']['participants'][$deal_company_i]['hq_country']!=""){
		?><br /><?php echo $g_view['deal_data']['participants'][$deal_company_i]['hq_country'];
	}
	?>
	</p>
	<?php
}
?>
<ol>
<?php
for($deal_company_i=0;$deal_company_i<$deal_company_count;$deal_company_i++){
	if($g_view['deal_data']['participants'][$deal_company_i]['footnote']!=""){
		?><li><?php echo $g_view['deal_data']['participants'][$deal_company_i]['footnote'];?></li><?php
	}
}
?>
</ol>
</fieldset>