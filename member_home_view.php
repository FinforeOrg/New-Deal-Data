<?php
/**************************
sng:29/nov/2011
We now include the jquery in container view
<script type="text/javascript" src="js/jquery-1.2.1.pack.js"></script>
********************************/
?>
<script type="text/javascript">
function updateChosenLogos() {
    $.get(
        'ajax/save_chosen_logo.php?' + jQuery(".thumb-val").serialize(),
        function (data) {
        }
  )  
}

function showNext(id) {
    activeLogoId =  $("#logo-"+id+" img:visible").attr('id');
    c = activeLogoId.match(/logo-\d+-(\d+)/);
    currentId = parseInt(c[1]);
    /* Test if we have a next picture */
    next = $("#logo-"+id+"-" + (currentId + 1) );
    if (next.length == 0) {
        return false;
    } else {
         $("#logo-"+id+" img:visible").css('display','none');
         next.css('display','block');
         $("#thumb-"+id).val(currentId + 1);
         updateChosenLogos();
    }
}

function showPrevious(id) {
    activeLogoId =  $("#logo-"+id+" img:visible").attr('id');
    c = activeLogoId.match(/logo-\d+-(\d+)/);
    currentId = parseInt(c[1]);
    /* Test if we have a previous picture */
    prev = $("#logo-"+id+"-" + (currentId - 1) );
    if (prev.length == 0) {
        return false;
    } else {
         $("#logo-"+id+" img:visible").css('display','none');
         prev.css('display','block');
         $("#thumb-"+id).val(currentId - 1);
         updateChosenLogos();
    }
} 
function recommend_colleague(this_colleague_id){
	$('#recommend_result').html('sending request');
	$.post("ajax/recommend_colleague.php", {colleague_id: ""+this_colleague_id+""}, function(data){
		if(data.length >0) {
			$('#recommend_result').html(data);
		}
	});
}

function admire_competitor(this_competitor_id){
	$('#admire_result').html('sending request');
	$.post("ajax/admire_competitor.php", {competitor_id: ""+this_competitor_id+""}, function(data){
		if(data.length >0) {
			$('#admire_result').html(data);
		}
	});
}


function showNext(id) {
    activeLogoId =  $("#logo-"+id+" img:visible").attr('id');
    c = activeLogoId.match(/logo-\d+-(\d+)/);
    currentId = parseInt(c[1]);
    /* Test if we have a next picture */
    next = $("#logo-"+id+"-" + (currentId + 1) );
    if (next.length == 0) {
        return false;
    } else {
         $("#logo-"+id+" img:visible").css('display','none');
         next.css('display','block');
         $("#thumb-"+id).val(currentId + 1);
    }
}
function showPrevious(id) {
    activeLogoId =  $("#logo-"+id+" img:visible").attr('id');
    c = activeLogoId.match(/logo-\d+-(\d+)/);
    currentId = parseInt(c[1]);
    /* Test if we have a previous picture */
    prev = $("#logo-"+id+"-" + (currentId - 1) );
    if (prev.length == 0) {
        return false;
    } else {
         $("#logo-"+id+" img:visible").css('display','none');
         prev.css('display','block');
         $("#thumb-"+id).val(currentId - 1);
    }
} 

</script>
<table width="100%" cellpadding="0" cellspacing="0">
<tr>
<td>
<!--top part-->
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
<td style="text-align:left;"><span style="color:#E86200;">Hi <a href="my_profile.php"><?php echo $_SESSION['f_name']." ".substr($_SESSION['l_name'],0,1);?></a></span></td>
<td style="text-align:right;">
<?php
if( $g_view['my_total_points'] > 0){
	?>
	You have $ <?php echo convert_billion_to_million_for_display($g_view['my_total_points']);?>m tombstone points.
	<?php
}else{
	?>
	You have zero tombstone points.
	<?php
}
?>
<?php
/**********
sng:13/jan/2011
Since the member has no tombstones, ask the user to add some
We send the id of the firm so that only deals done by the member's firm are shown
********/
?>
&nbsp;<a href="deal_search.php?partner_id=<?php echo $_SESSION['company_id'];?>">Add tombstones</a>

</td>
</tr>
<?php
if($g_view['my_last_3_months_total_points']){
?>
<tr>
<td></td>
<td style="text-align:right;">You have earned $<?php echo convert_billion_to_million_for_display($g_view['my_last_3_months_total_points']);?>m points in the last 3 months</td>
</tr>
<?php
}
?>
<?php
/***
sng:24/july/2010
need a link to th etombstones of my firm. This is a quick and dirty approach. Send to page showing the featured tombstones
by giving my firm's id
***/
?>
<tr>
<td></td>
<td style="text-align:right;"><a href="showcase_firm.php?id=<?php echo $_SESSION['company_id'];?>">Tombstones of my firm</a></td>
</tr>
</table>
<!--top part-->
</td>
</tr>
<tr>
<td><img src="images/spacer.gif" width="1" height="30" alt="" /></td>
</tr>
<?php
/**************************************
sng:22/jul/2010
need to show most recent 8 tombstone of the firm where this member works
*******/
?>
<tr><td><h1>Recent Tombstones of the Firm</h1></td></tr>
<tr>
<td>
<!--/////////////////////////////firm tombstones///////////////////////-->
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<?php
if($g_view['data_count'] == 0){
	?>
	<tr>
	<td>None found</td>
	</tr>
	<?php
}else{
	$col_count = 0;
	?>
	<tr>
	<?php
	for($i=0;$i<$g_view['data_count'];$i++){
		?>
		<td>
		<!--
		sng:7/jul/2010
		client want the tombstone to be a link. Used href link because that works in FF, and used onclick because that works in IE
		-->
		<?php
		$g_trans->get_tombstone_from_deal_id($g_view['data'][$i]['transaction_id']);
		?>
		</td>
		<?php
		$col_count++;
		if($col_count == 4){
			$col_count = 0;
			?>
			</tr>
			<tr><td colspan="4" style="height:10px;">&nbsp;</td></tr>
			<tr>
			<?php
		}
	}
	?>
	</tr>
	<?php
}
?>
</table>
<!--/////////////////////////////firm tombstones///////////////////////-->
</td>
</tr>
<tr><td style="height:20px;">&nbsp;</td></tr>
<?php
/*******************************************/
?>
<tr>
<td>
<!--mid part-->
<table cellpadding="0" cellspacing="0" border="0">
<tr>
<td>
<h1>Featured Colleague</h1>
</td>
<td style="width:10px;">&nbsp;</td>
<td>
<h1>Featured Competitor</h1>
</td>
</tr>
<tr>
<td colspan="2" style="height:5px;">&nbsp;</td>
</tr>
<tr>
<td style="width:45%">
<!--featured collegue-->
<?php
if($g_view['collegue_count']!=0){
	?>
	<table cellpadding="0" cellspacing="0" border="0">
	<tr>
	<td>
		<!--name section-->
		<table cellpadding="0" cellspacing="0" border="0">
		<tr>
		<td style="width:170px;">
		<?php
		if($g_view['collegue_data']['profile_img']==""){
			?>
			<img src="images/no_profile_img.jpg" />
			<?php
		}else{
			?>
			<img src="uploaded_img/profile/thumbnails/<?php echo $g_view['collegue_data']['profile_img'];?>" />
			<?php
		}
		?>
		
		</td>
		<td style="vertical-align:top; text-align:left;">
		<a href="profile.php?mem_id=<?php echo $g_view['collegue_data']['mem_id'];?>"><?php echo $g_view['collegue_data']['f_name'];?> <?php echo $g_view['collegue_data']['l_name'];?></a><br />
		<?php echo $g_view['collegue_data']['company_name'];?><br />
		<input type="button" value="Recommend" class="btn_auto" onclick="recommend_colleague(<?php echo $g_view['collegue_data']['mem_id'];?>)" />
		<br />
		<span id="recommend_result"></span>
		</td>
		
		</tr>
		</table>
		<!--name section-->
	</td>
	</tr>
	<tr>
	<td>
	<!--points part-->
	<?php
	if($g_view['collegue_total_points'] > 0){
		?>
		$<?php echo convert_billion_to_million_for_display($g_view['collegue_total_points']);?>m total points, $<?php echo convert_billion_to_million_for_display($g_view['collegue_last_3_months_total_points']);?>m points in the last 3 months
		<?php
	}else{
		?>
		No tombstone points yet
		<?php
	}
	?>
	<!--points part-->
	</td>
	</tr>
	<tr>
	<td>
	<!--detail part-->
	<?php echo $g_view['collegue_data']['f_name'];?> <?php echo $g_view['collegue_data']['l_name'];?> is <?php echo $g_view['collegue_data']['designation'];?>, with <?php echo $g_view['collegue_data']['company_name'];?>
	<!--detail part-->
	</td>
	</tr>
	<?php
	/******
	sng:19/oct/2010
	client do not want to show the last 3 deals of collegue or competitor
	so we are removing this. the code can be found on member_home_view.php-2010-10-19
	**********/
	?>
	
	</table>
	<?php
}else{
	?>
	No collegue found
	<?php
}
?>
<!--featured collegue-->
</td>
<td style="width:1px;">&nbsp;</td>
<td>
<!--featured competitor-->
<?php
if($g_view['competitor_count']!=0){
	?>
	<table cellpadding="0" cellspacing="0" border="0">
	<tr>
	<td>
		<!--name section-->
		<table cellpadding="0" cellspacing="0" border="0">
		<tr>
		<td style="width:170px;">
		<?php
		if($g_view['competitor_data']['profile_img']==""){
			?>
			<img src="images/no_profile_img.jpg" />
			<?php
		}else{
			?>
			<img src="uploaded_img/profile/thumbnails/<?php echo $g_view['competitor_data']['profile_img'];?>" />
			<?php
		}
		?>
		
		</td>
		<td style="vertical-align:top; text-align:left;">
		<a href="profile.php?mem_id=<?php echo $g_view['competitor_data']['mem_id'];?>"><?php echo $g_view['competitor_data']['f_name'];?> <?php echo $g_view['competitor_data']['l_name'];?></a><br />
		<?php echo $g_view['competitor_data']['company_name'];?><br />
		
		<input type="button" value="I admire" class="btn_auto" onclick="admire_competitor(<?php echo $g_view['competitor_data']['mem_id'];?>)" />
		<br />
		<span id="admire_result"></span>
		</td>
		
		</tr>
		</table>
		<!--name section-->
	</td>
	</tr>
	<tr>
	<td>
	<!--points part-->
	<?php
	if($g_view['competitor_total_points'] > 0){
		?>
		$<?php echo convert_billion_to_million_for_display($g_view['competitor_total_points']);?>m total points, $<?php echo convert_billion_to_million_for_display($g_view['competitor_last_3_months_total_points']);?>m points in the last 3 months
		<?php
	}else{
		?>
		No tombstone points yet
		<?php
	}
	?>
	<!--points part-->
	</td>
	</tr>
	<tr>
	<td>
	<!--detail part-->
	<?php echo $g_view['competitor_data']['f_name'];?> <?php echo $g_view['competitor_data']['l_name'];?> is <?php echo $g_view['competitor_data']['designation'];?>, with <?php echo $g_view['competitor_data']['company_name'];?>
	<!--detail part-->
	</td>
	</tr>
	<?php
	/******
	sng:19/oct/2010
	client do not want to show the last 3 deals of collegue or competitor
	so we are removing this. the code can be found on member_home_view.php-2010-10-19
	**********/
	?>
	
	</table>
	<?php
}else{
	?>
	No competitor found
	<?php
}
?>
<!--featured competitor-->
</td>
</tr>
</table>
<!--mid part-->
</td>
</tr>
</table>