<?php
/*********************
sng:5/mar/2012
We now show admin verified deals with an icon
*************************/
$g_view['has_verified_deal'] = false;
?>
<?php if($err) : ?>
<h2> <?php echo $err; exit(0)  ?></h2>
<?php endif ?>

<link rel="stylesheet" href="css/savedSearches.css" type="text/css" media="screen" />
<link rel="stylesheet" href="css/ss_style.css" type="text/css" media="screen" />
<script>
function remove_deal_from_watch(watch_id){
	document.getElementById("watch_id").value = watch_id;
	<?php
	/***********
	sng:12/sep/2011
	We now need to send watchlist_filter has hidden so that after the delete, the display show the proper
	filtered list and filter option
	*****************/
	?>
	jQuery("#frm_data_watchlist_filter").val(jQuery('#watchlist_filter :selected').val());
	document.getElementById("frm_remove_deal_from_watch").submit();
	return false;
}
function remove_deal_discussion_from_watch(discussion_watch_id){
	document.getElementById("discussion_watch_id").value = discussion_watch_id;
	<?php
	/***********
	sng:12/sep/2011
	We now need to send watchlist_filter has hidden so that after the delete, the display show the proper
	filtered list and filter option
	*****************/
	?>
	jQuery("#frm_data_discussion_watchlist_filter").val(jQuery('#watchlist_filter :selected').val());
	document.getElementById("frm_remove_deal_discussion_from_watch").submit();
	return false;
}

function filter_watchlist(){
	document.getElementById("frm_filter_watchlist").submit();
}
</script>
<script>
function deleteSearch(id, type, isAlert){
    isAlert = isAlert == undefined ? 0 : isAlert;
    $.ajax({
       type: "GET",
       url: "watchlist.php?action=deleteSearch&type=" + type  + "&id=" + id + "&alert="+isAlert,
       success: function(obj){
         alert( obj.message );
         if (obj.newLocation != undefined ) {
            window.location.reload()        
         }
       },
       dataType: 'json'
     });     
}

function shareSearch(token) {
    $('#shareSearchForm #button').attr('disabled', 'disabled');
    $.ajax({
       type: "POST",
       url: "ajax/saved_searches.php?action=shareSearch",
       data: $("#shareSearchForm").serialize(),
       success: function(msg){
         $('#shareSearchForm #button').removeAttr('disabled');
         $("#status").html(msg); 
       }
     });    
}

function openShareSearchPopup(key, type) {
    $("#shareSearchForm")[0].reset();
    $("#savedSearch").val(key);
    $("#savedSearchType").val(type);
    centerPopup();
    loadPopup();
}
</script>
<div>
<form method="post" action="" id="frm_filter_watchlist">
<p>
Show:
<select id="watchlist_filter" name="watchlist_filter" onchange="filter_watchlist()">
<option value="all" <?php if($g_view['watchlist_filter']=="all"){?>selected="selected"<?php }?>>All Watchlists</option>
<option value="h|48" <?php if($g_view['watchlist_filter']=="h|48"){?>selected="selected"<?php }?>>Watchlists altered in last 48 hours</option>
<option value="d|7" <?php if($g_view['watchlist_filter']=="d|7"){?>selected="selected"<?php }?>>Watchlists altered in last 7 days</option>
</select>
</p>
</form>
</div>
<?php
/***********
sng:12/sep/2011
We now need to send watchlist_filter has hidden so that after the delete, the display show the proper
filtered list and filter option
*****************/
?>
<form id="frm_remove_deal_from_watch" method="post" action="">
<input type="hidden" name="myaction" value="remove_deal_from_watch" />
<input type="hidden" name="watchlist_filter" id="frm_data_watchlist_filter" value="" />
<input type="hidden" name="watch_id" id="watch_id" value="" />
</form>

<form id="frm_remove_deal_discussion_from_watch" method="post" action="">
<input type="hidden" name="myaction" value="remove_deal_discussion_from_watch" />
<input type="hidden" name="watchlist_filter" id="frm_data_discussion_watchlist_filter" value="" />
<input type="hidden" name="watch_id" id="discussion_watch_id" value="" />
</form>

<div class="leaguechartsDiv">
	<div class="leaguechartsHeaderDiv"><p>Watched Deals</p></div>
	<table class="grey_table" width="100%" cellpadding="0" cellspacing="0">
		<tr>
			
			<th>Participant</th>
			<th>Deal Type</th>
			<th>Date of Deal</th>
			<th>Value</th>
			<th>Last Updated</th>
			<th></th>
			<th colspan="2"></th>
		</tr>
		<?php
		if(0 == $g_view['watch_count']){
			?>
			<tr><td colspan="8">None found</td></tr>
			<?php
		}else{
			for($k=0;$k<$g_view['watch_count'];$k++){
				?>
				<tr>
				
				<td>
				<?php
				/**********************************
				sng:3/feb/2012
				We no longer have single company for a deal
				<?php echo $g_view['watch_list'][$k]['name'];?>
				*************************************/
				echo Util::deal_participants_to_csv($g_view['watch_list'][$k]['participants']);
				?>
				</td>
				<td><?php echo $deal_support->deal_page_show_deal_type_data_heading($g_view['watch_list'][$k])?></td>
				<td><?php echo ymd_to_dmy($g_view['watch_list'][$k]['date_of_deal']);?></td>
				<td><?php echo convert_deal_value_for_display_round($g_view['watch_list'][$k]['value_in_billion'],$g_view['watch_list'][$k]['value_range_id'],$g_view['watch_list'][$k]['fuzzy_value']);?></td>
				<td><?php if($g_view['watch_list'][$k]['last_edited']!="0000-00-00 00:00:00") echo date("jS M Y g:i a",strtotime($g_view['watch_list'][$k]['last_edited'])); else echo "-";?></td>
				<td>
				<?php 
				if($g_view['watch_list'][$k]['admin_verified']=='y'){
					?><img src="images/tick_ok.gif" /><?php
					$g_view['has_verified_deal'] = true;
				}
				?>
				</td>
				<td>
				<a href="#" onClick="return remove_deal_from_watch(<?php echo $g_view['watch_list'][$k]['watch_id'];?>)"><img src="images/ss_delete.gif" alt="" width="19" height="18" align="left" hspace="5px" vspace="6" /><span>Delete</span></a>
				</td>
				<td>
				<a href="deal_detail.php?deal_id=<?php echo $g_view['watch_list'][$k]['deal_id'];?>"><img src="images/ss_view.gif" alt="" width="15" height="15" align="left" hspace="5px" vspace="6" /><span>View</span></a>
				</td>
				</tr>
				<?php
			}
		}
		?>
	</table>
</div>

<?php
/******************************************************************************
discussions
*********************************************************************************/
?>
<div style="height: 20px; width:100%; clear:both;"> &nbsp; </div>
<div class="leaguechartsDiv">
	<div class="leaguechartsHeaderDiv"><p>Watched Deal Discussions</p></div>
	<table class="grey_table" width="100%" cellpadding="0" cellspacing="0">
		<tr>
			
			<th>Participant</th>
			<th>Deal Type</th>
			<th>Date of Deal</th>
			<th>Value</th>
			<th>Last Posting on</th>
			<th></th>
			<th colspan="2"></th>
		</tr>
		<?php
		if(0 == $g_view['discussion_watch_count']){
			?>
			<tr><td colspan="8">None found</td></tr>
			<?php
		}else{
			for($k=0;$k<$g_view['discussion_watch_count'];$k++){
				?>
				<tr>
				
				<td>
				<?php
				/**********************************
				sng:3/feb/2012
				We no longer have single company for a deal
				<?php echo $g_view['discussion_watch_list'][$k]['name'];?>
				*************************************/
				echo Util::deal_participants_to_csv($g_view['discussion_watch_list'][$k]['participants']);
				?>
				</td>
				<td><?php echo $deal_support->deal_page_show_deal_type_data_heading($g_view['discussion_watch_list'][$k])?></td>
				<td><?php echo ymd_to_dmy($g_view['discussion_watch_list'][$k]['date_of_deal']);?></td>
				<td><?php echo convert_deal_value_for_display_round($g_view['discussion_watch_list'][$k]['value_in_billion'],$g_view['discussion_watch_list'][$k]['value_range_id'],$g_view['discussion_watch_list'][$k]['fuzzy_value']);?></td>
				<?php
				/**************
				sng:20/sep/2011
				We need the last post date for discussion on a deal
				*********************/
				?>
				<td><?php if(($g_view['discussion_watch_list'][$k]['last_post_date']!="0000-00-00 00:00:00")&&($g_view['discussion_watch_list'][$k]['last_post_date']!="")) echo date("jS M Y g:i a",strtotime($g_view['discussion_watch_list'][$k]['last_post_date'])); else echo "-";?></td>
				<td>
				<?php 
				if($g_view['discussion_watch_list'][$k]['admin_verified']=='y'){
					?><img src="images/tick_ok.gif" /><?php
					$g_view['has_verified_deal'] = true;
				}
				?>
				</td>
				<td>
				<a href="#" onClick="return remove_deal_discussion_from_watch(<?php echo $g_view['discussion_watch_list'][$k]['watch_id'];?>)"><img src="images/ss_delete.gif" alt="" width="19" height="18" align="left" hspace="5px" vspace="6" /><span>Delete</span></a>
				</td>
				<td>
				<?php
				/********************
				see deal_page_view.php for this viewtab param
				**********************/
				?>
				<a href="deal_detail.php?deal_id=<?php echo $g_view['discussion_watch_list'][$k]['deal_id'];?>&viewtab=discussion"><img src="images/ss_view.gif" alt="" width="15" height="15" align="left" hspace="5px" vspace="6" /><span>View</span></a>
				</td>
				</tr>
				<?php
			}
		}
		?>
	</table>
</div>
<?php
if($g_view['has_verified_deal']){
	?><div style="height: 20px; width:100%; clear:both;"> &nbsp; </div><div><img src="images/tick_ok.gif" /> Verified deal</div><?php
}
?>

<div style="height: 20px; width:100%; clear:both;"> &nbsp; </div>
<h1> My Saved Searches </h1>
<div><img width="1" height="15" alt="" src="images/spacer.gif"></div>
<div style="float: none; clear: both; margin:0 auto; width: 100%; height: auto;">

	<div class="leaguechartsDiv">
		
		
			
		
			<div class="leaguechartsHeaderDiv"><p>Credentials Slides</p></div>
		
			<?php if (count($mySavedSearches['tombstone'])) : ?>
				<?php foreach ($mySavedSearches['tombstone'] as $key=>$description) : ?>
					<div class="leaguechartsContent">
					<div class="leaguechartsContentleft"><?php echo $description ?></div>
					<div class="leaguechartsContentRight">
						<ul>
						<li><a href="#" onClick="deleteSearch(<?php echo $key ?>,'tombstone')"><img src="images/ss_delete.gif" alt="" width="19" height="18" align="left" hspace="5px" vspace="2" /><span>Delete</span></a></li>
						<li><img src="images/ss_border.gif" alt="" width="1" height="17" /></li>
						<li><a href="#" onClick="openShareSearchPopup(<?php echo $key ?>,'tombstone')"><img src="images/ss_share.gif" alt="" width="15" height="15" align="left" hspace="5px" vspace="2" /><span>Share</span></a></li>
						<li><img src="images/ss_border.gif" alt="" width="1" height="17" /></li>
						<li><a href="showcase_firm.php?id=<?php echo $_SESSION['company_id']?>&from=savedSearches&token=<?php echo base64_encode($key)?>"><img src="images/ss_view.gif" alt="" width="15" height="15" align="left" hspace="5px" vspace="2" /><span>View</span></a></li>
						</ul>
					</div>
					</div>
				<?php endforeach ?>
			<?php else : ?>
			<div class="leaguechartsContent">
				<div class="leaguechartsContentleft">You do not have any saved searches</div>
			</div>
            <?php endif ?>
		
		<div class="leaguechartsContent">
			<a class="link_as_button" href="showcase_firm.php?id=<?php echo $_SESSION['company_id']?>&from=savedSearches">Add a New Credentials Slide</a>
		</div>	
		
		<div class="leaguechartsHeaderDiv"><p>Deal Searches</p></div>
		
			<?php if (count($mySavedSearches['deal'])) : ?>
				<?php foreach ($mySavedSearches['deal'] as $key=>$description) : ?>
					<div class="leaguechartsContent">
						<div class="leaguechartsContentleft"><?php echo $description ?></div>
						<div class="leaguechartsContentRight">
							<ul>
							<li><a href="#" onClick="deleteSearch(<?php echo $key ?>, 'deal')"><img src="images/ss_delete.gif" alt="" width="19" height="18" align="left" hspace="5px" vspace="2" /><span>Delete</span></a></li>
							<li><img src="images/ss_border.gif" alt="" width="1" height="17" /></li>
							<li><a href="#" onClick="openShareSearchPopup(<?php echo $key ?>,'deal')"><img src="images/ss_share.gif" alt="" width="15" height="15" align="left" hspace="5px" vspace="2" /><span>Share</span></a></li>
							<li><img src="images/ss_border.gif" alt="" width="1" height="17" /></li>
							<li><a href="deal_search.php?token=<?php echo base64_encode($key)?>"><img src="images/ss_view.gif" alt="" width="15" height="15" align="left" hspace="5px" vspace="2" /><span>View</span></a></li>
							</ul>
						</div>
					</div>
				<?php endforeach ?>
			<?php else : ?>
			<div class="leaguechartsContent">
				<div class="leaguechartsContentleft">You do not have any saved searches</div>
			</div>
            <?php endif ?>
			<div class="leaguechartsContent">
			<a class="link_as_button" href="deal.php">Add a Deal Search</a>
			</div>
		
		<div style="height: 20px; width:100%; clear:both;"> &nbsp; </div>
  		
		
		
	</div>
</div>

<div style="height: 20px; width:100%; clear:both;"> &nbsp; </div>
<h1> My Alerts </h1>
<div><img width="1" height="15" alt="" src="images/spacer.gif"></div>
<div style="float: none; clear: both; margin:0 auto; width: 100%; height: auto;">
	<div class="leaguechartsDiv">
		<div class="leaguechartsHeaderDiv"><p>Alerts</p></div>
		
			<?php if (isset($mySavedAlerts) and sizeOf($mySavedAlerts) and count($mySavedAlerts['deal']))
				foreach ($mySavedAlerts['deal'] as $key=>$savedSearch) : ?>
					<div class="leaguechartsContent">
						<div class="leaguechartsContentleft"><?php echo $savedSearch?></div>
						<div class="leaguechartsContentRight">
							<ul>
							<li><a href="#" onClick="deleteSearch(<?php echo $key ?>, 'deal',1)"><img src="images/ss_delete.gif" alt="" width="19" height="18" align="left" hspace="5px" vspace="2" /><span>Delete</span></a></li>
							
							
							<li><img src="images/ss_border.gif" alt="" width="1" height="17" /></li>
							<li><a href="deal_search.php?action=addAlert&token=<?php echo  base64_encode($key)?>"><img src="images/ss_view.gif" alt="" width="15" height="15" align="left" hspace="5px" vspace="2" /><span>View</span></a></li>
							</ul>
						</div>
					</div>
				<?php endforeach ?>
				<?php if ($savedSearches->currentUserCanStillAdd('deal',1)) : ?>
					<div class="leaguechartsContent">
					<a class="link_as_button" href="deal_search.php?action=addAlert">Add a New Email Alert</a>
				</div>
				<?php endif?>
		
	</div>
</div>

<div id="popupShare">
    <a id="popupShareClose">x</a>
    <h1>Share your search!</h1>

    <p id="contactArea">

    </p>
    <form action="#" method="POST" onsubmit="return shareSearch()" id="shareSearchForm">
        <table width="100%" border="0">
          <tr>
            <td>Enter the e-mail adresses to send your search to. (separated by comma)</td>
          </tr>
          <tr>
            <td><label>
              <textarea name="emailAdresses" id="emailAdresses" cols="45" rows="5" style="width: 400px;"></textarea>
            </label></td>
          </tr>
          <tr>
            <td>Enter any extra info to send in your e-mail</td>
          </tr>
          <tr>
            <td><label>
              <textarea name="extraInfo" id="extraInfo" cols="45" rows="5" style="width: 400px;"></textarea>
            </label>
            </td>
          </tr>
          <tr>
              <td>
              <div id="status"  > &nbsp;</div>
               <input type="button" value="Send" id="button" class="btn_auto" name="submit" style="float:right" onclick="return shareSearch()">
               <input type="hidden" name="savedSearch" value="" id="savedSearch" />
               <input type="hidden" name="savedSearchType" value="" id="savedSearchType" />
              </td> 
          </tr>
        </table>

    </form>
</div>
<div id="backgroundPopup"></div>

<script src="js/scripts.js" type="text/javascript" charset="utf-8"></script>
<script>
$('#watchlist_filter').selectmenu();
</script>