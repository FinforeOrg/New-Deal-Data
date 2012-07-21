<div id="explanation">
<p>At Saved Searches, you can save the charts and tables that you need to access on a regular basis.</p>
<p>Data that many of our clients need to use, over and over again includes:</p>
<ul>
  <li>League Tables that show your firm's competitive position</li>
  <li>Credentials/ Tombstones of your firm's recent deals</li>
  <li>Volumes charts that indicate how many transactions are taking place</li>
  <li>Deal Lists with the names of the most important recent deals</li>
</ul>
<p>When you click on any Saved Search, on the left of the table, you are automatically brought to the latest version of the data.</p>
<p>You can share, via email, any of these searches with a colleague using the &quot;Share&quot; link on the left.</p>
<p>At the bottom of the page is a &quot;My Alerts&quot; function where you can create email alerts. This way, you can stay up to date on any recent transaction that your colleagues or competitors have submitted to myTombstones.</p>
</div>
<?php if($err) : ?>
<h2> <?php echo $err; exit(0)  ?></h2>
<?php endif ?>
<?php
/****
sng:3/aug/2010
We put a link to Make Me Top page

sng:29/sep/2011
Shane said to remove this link
<div style="text-align:right"><a href="make_me_top.php">Try our Make Me Top Function</a></div>
***/
?>
<link rel="stylesheet" href="css/savedSearches.css" type="text/css" media="screen" />
<?php
/**************
sng:18/mar/2011
**************/
?>
<link rel="stylesheet" href="css/ss_style.css" type="text/css" media="screen" />  
<script type="text/javascript">
function deleteSearch(id, type, isAlert){
    isAlert = isAlert == undefined ? 0 : isAlert;
    $.ajax({
       type: "GET",
       url: "saved_searches.php?action=deleteSearch&type=" + type  + "&id=" + id + "&alert="+isAlert,
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

function saveLeagueTableNotification(notificationElement) {
    var state = $(notificationElement).attr('checked') ? 1 : 0
    $.get(
         'ajax/saved_searches.php?action=setLeagueTableNotificationAlertState',
         {'state' : state,  'notificationId' : $(notificationElement).attr('value')},
         function (response) {
             alert(response);
         }
    )
}
</script>

                     
<div style="float: none; clear: both; margin:0 auto; width: 100%; height: auto;">
	<div class="leaguechartsDiv">
		<div class="leaguechartsHeaderDiv"><p>League Table Charts</p></div>
		
			<?php if (count($mySavedSearches['league'])) : ?>
				<?php foreach ($mySavedSearches['league'] as $key=>$description) : ?>
					<div class="leaguechartsContent">
					<div class="leaguechartsContentleft"><?php echo $description ?><br />
                                        </div>
					<div class="leaguechartsContentRight">
						<ul>
						<li><a href="#" onClick="deleteSearch(<?php echo $key ?>,'league')"><img src="images/ss_delete.gif" alt="" width="19" height="18" align="left" hspace="5px" vspace="2" /><span>Delete</span></a></li>
						<li><img src="images/ss_border.gif" alt="" width="1" height="17" /></li>
						<li><a href="#" onClick="openShareSearchPopup(<?php echo $key ?>,'league')"><img src="images/ss_share.gif" alt="" width="15" height="15" align="left" hspace="5px" vspace="2" /><span>Share</span></a></li>
						<li><img src="images/ss_border.gif" alt="" width="1" height="17" /></li>
						<?php
						/**********
				   sng:5/aug/2011
				   since the index.php is showing the league table chart, let us send there
				   
				   sng:21/jul/2012
				   now we have our own league table page
				   ****/
				   ?>
						<li><a href="league_table.php?token=<?php echo base64_encode($key)?>"><img src="images/ss_view.gif" alt="" width="15" height="15" align="left" hspace="5px" vspace="2" /><span>View</span></a></li>
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
			<?php
			/**********
			sng:21/jul/2012
			Now we have our own league table page
			***********/
			?>
			<a class="link_as_button" href="league_table.php">Add league table chart</a>
			</div>
		
		<div class="leaguechartsHeaderDiv"><p>League Tables Details</p></div>
		
			<?php if (count($mySavedSearches['leagueDetail'])) : ?>
			<?php
			/*******************
			sng:10/oct/2011
			we will show the 'Notify Me...' text for the first checkbox. For the rest, only Notify Me
			*******************/
			$is_first = true;
			?>
				<?php foreach ($mySavedSearches['leagueDetail'] as $key=>$description) : ?>
                                        <div class="leaguechartsContent">
                                            <div class="leaguechartsContentleft">
                                                <?php echo $description ?> 
                                            </div>
                                            <div class="leaguechartsContentRight">
                                                    <ul>
                                                    <li><a href="#" onClick="deleteSearch(<?php echo $key ?>,'leagueDetail')"><img src="images/ss_delete.gif" alt="" width="19" height="18" align="left" hspace="5px" vspace="2" /><span>Delete</span></a></li>
                                                    <li><img src="images/ss_border.gif" alt="" width="1" height="17" /></li>
                                                    <li><a href="#" onClick="openShareSearchPopup(<?php echo $key ?>,'leagueDetail')"><img src="images/ss_share.gif" alt="" width="15" height="15" align="left" hspace="5px" vspace="2" /><span>Share</span></a></li>
                                                    <li><img src="images/ss_border.gif" alt="" width="1" height="17" /></li>
                                                    <li><a href="league_table_detail.php?token=<?php echo base64_encode($key)?>"><img src="images/ss_view.gif" alt="" width="15" height="15" align="left" hspace="5px" vspace="2" /><span>View</span></a></li>
                                                    </ul>
                                            </div>
                                            <?php if (isset($mySavedSearches['currentRanks'][$key])) : ?>
                                            <div id="leaguechartsContentOptions" style="display:block; clear: all; padding-left: 15px;">
                                                <input type="checkbox" id="notifyme_<?php echo $key ?>" value="<?php echo $key ?>" onclick="return saveLeagueTableNotification(this)" <?php if (isset($mySavedSearches['enabledNotifications'][$key]) && $mySavedSearches['enabledNotifications'][$key]) echo 'checked="checked"'?>/> <?php
if($is_first){
?>
Notify Me, when my firm's Ranking changes (available for a top 10 ranking only) <?php echo sprintf("(Current Rank: %d)", $mySavedSearches['currentRanks'][$key])?>
<?php
$is_first = false;
}else{
?>
Notify Me <?php echo sprintf("(Current Rank: %d)", $mySavedSearches['currentRanks'][$key])?>
<?php
}
?>
                                            </div>
                                            <?php else : ?>
                                            <div id="leaguechartsContentOptions" style="display:block; clear: all; padding-left: 15px;">
                                                <input disabled="disabled" type="checkbox" id="notifyme_<?php echo $key ?>" value="<?php echo $key ?>" /> <?php if($is_first){?>Notify Me, when my firm's Ranking changes (available for a top 10 ranking only)<?php $is_first = false;}else{?>Notify Me<?php }?>
                                            </div>                                            
                                            <?php endif ?>
					</div>
				<?php endforeach ?>
			<?php else : ?>
			<div class="leaguechartsContent">
				<div class="leaguechartsContentleft">You do not have any saved searches</div>
			</div>
            <?php endif ?>
			<div class="leaguechartsContent">
			<a class="link_as_button" href="league_table_detail.php">Add league table analysis</a>
			</div>
		<?php
		/********************
		sng:7/apr/2011
		this is only for bankers and lawyers
		*************/
		if(($_SESSION['member_type']=="banker")||($_SESSION['member_type']=="lawyer")){
			?>
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
			<a class="link_as_button" href="showcase_firm.php?id=<?php echo $_SESSION['company_id']?>&from=savedSearches">Add a new Credentials Slide</a>
		</div>
		<?php
		}
		?>
		<div class="leaguechartsHeaderDiv"><p>Volumes Charts</p></div>
		
			<?php if (count($mySavedSearches['volumes'])) : ?>
				<?php foreach ($mySavedSearches['volumes'] as $key=>$description) : ?>
					<div class="leaguechartsContent">
					<div class="leaguechartsContentleft"><?php echo $description ?></div>
					<div class="leaguechartsContentRight">
						<ul>
						<li><a href="#" onClick="deleteSearch(<?php echo $key ?>,'volumes')"><img src="images/ss_delete.gif" alt="" width="19" height="18" align="left" hspace="5px" vspace="2" /><span>Delete</span></a></li>
						<li><img src="images/ss_border.gif" alt="" width="1" height="17" /></li>
						<li><a href="#" onClick="openShareSearchPopup(<?php echo $key ?>,'volumes')"><img src="images/ss_share.gif" alt="" width="15" height="15" align="left" hspace="5px" vspace="2" /><span>Share</span></a></li>
						<li><img src="images/ss_border.gif" alt="" width="1" height="17" /></li>
						<li><a href="issuance_data.php?token=<?php echo base64_encode($key)?>"><img src="images/ss_view.gif" alt="" width="15" height="15" align="left" hspace="5px" vspace="2" /><span>View</span></a></li>
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
			<a class="link_as_button" href="issuance_data.php">Add volume chart</a>
			</div>
		
		<div class="leaguechartsHeaderDiv"><p>Volumes Detail</p></div>
		
			<?php if (count($mySavedSearches['volumesDetail'])) : ?>
				<?php foreach ($mySavedSearches['volumesDetail'] as $key=>$description) : ?>
				<div class="leaguechartsContent">
					<div class="leaguechartsContentleft"><?php echo $description ?></div>
						<div class="leaguechartsContentRight">
							<ul>
							<li><a href="#" onClick="deleteSearch(<?php echo $key ?>,'volumesDetail')"><img src="images/ss_delete.gif" alt="" width="19" height="18" align="left" hspace="5px" vspace="2" /><span>Delete</span></a></li>
							<li><img src="images/ss_border.gif" alt="" width="1" height="17" /></li>
							<li><a href="#" onClick="openShareSearchPopup(<?php echo $key ?>,'volumesDetail')"><img src="images/ss_share.gif" alt="" width="15" height="15" align="left" hspace="5px" vspace="2" /><span>Share</span></a></li>
							<li><img src="images/ss_border.gif" alt="" width="1" height="17" /></li>
							<li><a href="issuance_data_detail.php?token=<?php echo base64_encode($key)?>"><img src="images/ss_view.gif" alt="" width="15" height="15" align="left" hspace="5px" vspace="2" /><span>View</span></a></li>
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
			<a class="link_as_button" href="issuance_data_detail.php">Add volume analysis</a>
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
			<?php
			/***************
			sng:21/jul/2012
			Now we have our own league table page
			********/
			?>
			<a class="link_as_button" href="league_table.php">Add deal search</a>
			</div>
		
		<div style="height: 20px; width:100%; clear:both;"> &nbsp; </div>
  		
		
		
	</div>
</div>
<div style="height: 20px; width:100%; clear:both;"> &nbsp; </div>
<h1> My alerts </h1>
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
					<a class="link_as_button" href="deal_search.php?action=addAlert">Add a new email alert</a>
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


<?php
/*********************
sng:29/sep/2011
we now include jquery in container view
<script src="js/jquery-1.3.2.js" type="text/javascript" charset="utf-8"></script>
**************************/
?>
<script src="js/scripts.js" type="text/javascript" charset="utf-8"></script>



