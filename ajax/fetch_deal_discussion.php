<?php
/******************
Now the deal discussion is in a tab of deal detail page.
although the submission is ajax, the update of the content was page refresh.

We use ajax so that after update, we can fetch the content again

We of course check whether the member is logged in or not and is authorised
*****************/
require_once("../include/global.php");
require_once("classes/class.account.php");
require_once("classes/class.transaction.php");
require_once("classes/class.transaction_discussion.php");

///////////////
if(!$g_account->is_site_member_logged()){
	echo "You need to login first";
	exit;
}

$deal_id = $_GET['deal_id'];

$show_discussion = false;
$success = $g_deal_disc->can_see($deal_id,$show_discussion);
if(!$success){
	echo "Cannot determine whether the user can access deal discussion or not";
	exit;
}
if($show_discussion){
	/***********************************************
	get the comments. Right now, just get the comments for this deal serially
	*********/
	$g_view['discussion_count'] = 0;
	$g_view['discussion'] = array();
	$success = $g_deal_disc->get_comments($deal_id,$g_view['discussion'],$g_view['discussion_count']);
	if(!$success){
		echo "Cannot get the comments";
		exit;
	}
	if($g_view['discussion_count'] == 0){
		?>
		None yet
		<?php
	}else{
		$curr_tree = $g_view['discussion'][0]['tree'];
		?>
		<div class="deal_discussion_block">
		<?php
		for($i=0;$i<$g_view['discussion_count'];$i++){
			$poster_email_tokens = explode("@",$g_view['discussion'][$i]['work_email']);
			$poster_email = "@".$poster_email_tokens[1];
			$poster_division = $g_view['discussion'][$i]['division'];
			
			$use_class = "deal_discussion_posting";
			if(0 != $g_view['discussion'][$i]['parent_posting_id']){
				$use_class = "deal_discussion_reply";
			}
			if($g_view['discussion'][$i]['tree']!=$curr_tree){
				$curr_tree = $g_view['discussion'][$i]['tree'];
				?>
				</div>
				<div class="deal_discussion_block">
				<?php
				
			}
			?>
			<div class="<?php echo $use_class;?>">
				<?php
				if($g_view['discussion'][$i]['flag_count'] > 0){
					?>
					<div style="float:left;"><img src="images/icon_red_flag.gif" /></div>
					<?php
				}
				?>
				<div><?php echo nl2br($g_view['discussion'][$i]['posting_txt']);?></div>
				<div><?php echo $poster_email;?> [<?php echo $poster_division;?>] on <?php echo ymd_to_dmy($g_view['discussion'][$i]['posted_on']);?></div>
				<div class="deal_discussion_toolbar">
					<?php
					/*****************
					if this is a top level posting, it means, it is a question. Show the reply button along with the 'flag as inappropriate' button
					**********/
					if(0 == $g_view['discussion'][$i]['parent_posting_id']){
						?>
						<input type="button" class="btn_auto" value="Post Reply" onClick="return open_discussion_posting_popup(<?php echo $g_view['discussion'][$i]['posting_id'];?>);" />&nbsp;&nbsp;&nbsp;&nbsp;
						<?php
					}
					?>
					<input type="button" class="btn_auto" value="Flag" onClick="return flag_posting(<?php echo $g_view['discussion'][$i]['posting_id'];?>);" />&nbsp;<span id="flag_result_<?php echo $g_view['discussion'][$i]['posting_id'];?>"></span>
				</div>
			</div>
			<?php
		}
		?>
		</div>
		<?php
	}
}else{
	?>
	<p>These pages are only available to the actual deal participants (banks and law firms) and data providers.</p>
	<?php
}
?>
<script>
$('.btn_auto').button();
</script>