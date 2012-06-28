<?php
/*********************
sng:29/sep/2011
we now include jquery in container view
	<script src="js/jquery-1.3.2.js" type="text/javascript" charset="utf-8"></script>
***************************/
?>
    <script src="js/scripts.js" type="text/javascript" charset="utf-8"></script>
    <link rel="stylesheet" href="css/savedSearches.css" type="text/css" media="screen" />
<script type="text/javascript">
/*****************
sng:23/feb/2012
The entire preferred logo handling is changed. Now we store the logo filename instead of ordinal number.
Why? because now we have multiple companies and logos instead of logos for a deal
********************/
function updateChosenLogos() {
    jQuery.get(
        'ajax/save_chosen_logo.php?deal_id='+deal_id+'&logo_file='+filename,
        function (data) {
        }
  )  
}
 function download(url, data, method){
//url and data options required
    if( url && data ){ 
        //data can be string of parameters or array/object
        data = typeof data == 'string' ? data : jQuery.param(data);
        //split params into form inputs
        var inputs = '';
        $.each(data.split('&'), function(){ 
            var pair = this.split('=');
            inputs+='<input type="hidden" name="'+ pair[0] +'" value="'+ pair[1] +'" />'; 
        });
        //send request
        $('<form action="'+ url +'" method="'+ (method||'post') +'">'+inputs+'</form>')
        .appendTo('body').submit().remove();
    };
};


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
         /************
		 sng:23/feb/2012
		 use the ordinal number to get the name of the next logo
		 ****************/
         updateChosenLogos(id,jQuery("#logo-"+id+"-"+(currentId+1)).attr('name'));
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
         updateChosenLogos(id,jQuery("#logo-"+id+"-"+(currentId-1)).attr('name'));
    }
} 

function goto_showcase_firm_deals(firm_id){
	window.location="showcase_firm_deals.php?id="+firm_id;
}
function goto_showcase_chart(firm_id){
	window.location="showcase_firm_chart.php?id="+firm_id;
}
function goto_suggest_deal(){
	window.location="suggest_a_deal.php";
}

function goto_download_powerpoint(id) {
    $("#firmId").val(id);
    centerPopup();
    loadPopup();
};

function do_download_powerpoint() {
   id =  $("#firmId").val();
   title =  $("#pptTitle").val();
   extra =  $("#nrBlanks").val();   
   newUrl = "download_ppt.php?id=" + id  + "&title=" + escape(title)+ "&extra=" + extra;
   download(newUrl, $(".thumb-val"), 'post');
   $("#firmId").val();
   $("#pptTitle").val("");
   $("#nrBlanks").val(0); 
   disablePopup();  
}

function goto_deal_detail(deal_id){
	window.location="deal_detail.php?deal_id="+deal_id;
}
</script>


<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
<td style="text-align:left"><h1><?php echo $g_view['company_data']['name'];?></h1></td>
</tr>
<tr>

<td style="text-align:right;">
<?php
/***
sng:6/jul/2010
Only logged in members can download the tombstones
********/
if($g_account->is_site_member_logged()){
?>
<input type="button" class="btn_auto" value="download to powerpoint" onclick="goto_download_powerpoint(<?php echo $g_view['firm_id'];?>);" />&nbsp;&nbsp;
<?php
}
?>
<input type="button" class="btn_auto" value="recent deals" onclick="goto_showcase_firm_deals(<?php echo $g_view['firm_id'];?>);" />&nbsp;&nbsp;<input type="button" class="btn_auto" value="Charts showcasing this firm" onclick="goto_showcase_chart(<?php echo $g_view['firm_id'];?>);" />&nbsp;&nbsp;<input type="button" class="btn_auto" value="suggest a deal" onclick="goto_suggest_deal();" /></td>
</tr>
<tr><td colspan="2" style="height:10px;">&nbsp;</td></tr>
</table>
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
		
		<a href="deal_detail.php?deal_id=<?php echo $g_view['data'][$i]['transaction_id'];?>" style="text-decoration:none; cursor:pointer;" onclick="goto_deal_detail(<?php echo $g_view['data'][$i]['transaction_id'];?>)">-->
		<?php
		$g_trans->get_tombstone_from_deal_id($g_view['data'][$i]['transaction_id']);
		?>
		<!--</a> -->
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
    <div id="popupShare" style="height: 200px;">
        <a id="popupShareClose">x</a>
        <h1>Download to powerpoint</h1>
        <table width="600" border="0">
          <tr>
            <td>Title for your presentation<br />
            <input name="pptTitle" id="pptTitle" type="text"  style="width:100%"/></td>
          </tr>
          <tr>
            <td>Number of extra blank tombstones to add to your presentation<br />
            <input name="nrBlanks" id="nrBlanks" type="text" value="0" style="width:100%"/></td>
          </tr>
          <tr>
            <td align="right">
            <input type="button" onclick="do_download_powerpoint();" value="download" class="btn_auto">
            <input type="hidden" value="" name="firmId" id="firmId" />
            <input type="hidden" value="" name="searchId" id="searchId" />
            </td>
          </tr>
        </table>
    </div>
    <div id="backgroundPopup"></div>