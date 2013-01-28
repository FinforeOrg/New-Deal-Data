    <?php
	/**************************
	1/oct/2011
	we now put these in container view
    <script src="js/jquery-ui-1.8.11.custom.min.js" type="text/javascript"></script>  
    <script src="js/jquery.ui.selectmenu.js" type="text/javascript"></script>
    <link rel="stylesheet" href="css/custom-theme/jquery-ui-1.8.9.custom.css" />
    <link rel="stylesheet" href="css/custom-theme/jquery-ui-1.8.9.custom_orange.css" />
    <link rel="stylesheet" href="css/custom-theme/jquery.ui.selectmenu.css" />
	*******************************/
	?>
    <script src="js/scripts.js" type="text/javascript" charset="utf-8"></script>
    <link rel="stylesheet" href="css/savedSearches.css" type="text/css" media="screen" />

    <script type="text/javascript">

$(function() {
    $( ".radio" ).buttonset().click(function(){$( ".radio" ).buttonset('refresh')});  ;    
    $( ".radio_subcat" ).buttonset().click(function(){
        $( ".radio_subsubcat :radio" ).each(function(idx){
            $(this).removeAttr('checked');
        })
        $( ".radio_subsubcat" ).buttonset('refresh')        
    });    
    $( ".radio_cat" ).buttonset().click(function(){
        $( ".radio_subcat :radio" ).each(function(idx){
            $(this).removeAttr('checked');
        })
        $( ".radio_subcat" ).buttonset('refresh')
    });  
    $( ".radio_subsubcat" ).buttonset();
    
    /** 
    * Hadle the case when a hidden sub sub cat is checked 
    */
    $('#cats :radio').click(function() {
        $('#cats :checked').each(function(idx){
            if (!$(this).is(':visible')) {
                $(this).removeAttr('checked');
            }
        })         
    }
   
    );      
    $('#region').selectmenu();
    $('#country').selectmenu();    
    $('#industry').selectmenu();    
    $('#ranking_criteria').selectmenu();    
    $('#year').selectmenu();    
    $('#number_of_deals').selectmenu();    
    $('#value_range_id').selectmenu();    
    $('#sector').selectmenu().change(function(idx){
        $.post(
            'admin/ajax/industry_list_for_sector.php?for=leagueTables',
            {'sector' : $(this).selectmenu('value')},
            function(data) {
                $('#industry').html(data).selectmenu();
            }
        )
        /*alert($(this).selectmenu('value') )*/
    });
    $('button').button().click(function(event){event.preventDefault()});
    $('#filter').button().click(function(event){
        $('#tombstone_search_frm').submit();
        
    });    

    $('#submit').button();
    $('#downloadToPowerpoint').button();
    $('#searchCompetitor').button();
	/**********
    $('#generate').button().click(function(data){
        var myData = $('#leagueTableForm').serialize()
        $("#chart1").html('');
        $("#chart1").addClass('loading');
        $.post(
            'ajax/league_table_creator.php?version=2',
            myData,
            function(returned) {
                //console.log(returned);
                 $("#chart1").removeClass('loading');
                 $('#script').html(returned);
                 //
            }
        );
    });
	*************/
    $(".loading").hide();
    $("div.toHide").show();
    $('#tombstone_search_frm input[type="button"]').button();
    $('#tombstone_search_frm input[type="submit"]').button();    
});

function categoryChanged(category) {
    $('div.radio_subcat:visible').hide();  
    $('#subCatsForCat' + category).show();
    $('div.radio_subsubcat:visible').hide();
}

function subCategoryChanged(subCategory) {
    $('div.radio_subsubcat:visible').hide();
    $('#subSubCatsForCat' + subCategory).show();    
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

function goto_download_powerpoint_savedSearch(id, token) {
    $("#firmId").val(id);
    $("#searchId").val(token);
	/****************
	sng:15/nov/2011
	We do not open the popup. Rather we submit the form
	********************
    centerPopup();
    loadPopup();
	****************************/
	do_download_powerpoint_savedSearch();
};

function do_download_powerpoint_savedSearch() {
   id =  $("#firmId").val();
   token = $("#searchId").val();
   title =  $("#pptTitle").val();
   extra =  $("#nrBlanks").val();
   newUrl = "download_ppt.php?from=savedSearch&id=" + id + "&token=" + token + "&title=" + escape(title)+ "&extra=" + extra;
   /*************
   sng:24/feb/2012
   see transaction::get_tombstone_from_deal_data
   to understand the support for download to ppt and why this hidden inputs
   with class - thumb-val
   *****************/
   download(newUrl, $(".thumb-val"), 'post');
   title =  $("#pptTitle").val("");
   extra =  $("#nrBlanks").val(0);
   disablePopup();
   //window.location.href = newUrl;
}
 

                   

function saveTombstoneSearch() {
    $.post("saved_searches.php?action=saveSearch&type=tombstone", 
    $("#tombstone_search_frm").serialize(), 
    function(data){
        alert(data.message);
        if (data.newLocation != undefined)
        window.location.href = data.newLocation;
    },
    'json'
    ); 
}


function updateTombstoneSearch(search) {
    $.post("saved_searches.php?action=updateSearch&type=tombstone&id=" + search + "&company=<?php echo $_GET['id']?>", 
    $("#tombstone_search_frm").serialize(), 
    function(data){
       // alert(data.message);
        window.location.href = window.location.href;
    },
    'json'
    ); 
}


function updateFavoriteStatus(id) { 
    $.post("saved_searches.php?action=updateFavoriteStatus", 
     {'currentStatus': $("#favStat" + id).attr('src'),'id': id}, 
    function(data){
        $("#favStat" + id).attr('src',data);
    }
    ); 
    return false;
}

function goto_deal_detail(deal_id){
    window.location="deal_detail.php?deal_id="+deal_id;
}
/************
sng:21/oct/2011

sng:27/nov/2012
This function is not called in this file so we remove this
function goto_showcase_chart(firm_id)
**************/
</script>
<script src="js/logo_preference.js"></script>
<div id="explanation">
<p>Transaction details for up to 60 of the most recent deals are displayed below.</p>
<p>A user can filter the display by selecting a "Type of Transaction" and then refining the analysis using the drop-down menus.</p>
<p>You can also select specific Credentials by clicking the "Star" to the top right of each deal and then ticking the "Only show favourites" box.</p>
<p>Results can be downloaded to PowerPoint. Credential Searches can be saved to the "My Watchlist" page.</p>
<p>And if you don't like the logo that is displayed, either suggest a new logo at the "Lookup a Company" page or where we have already added more than 1 logo, use the arrows under the current logo to find a different logo you prefer.</p>
</div>
<div class="loading" style="height: 200px; width: 100%;" >
</div>
<div class="toHide" style="display: none;">
<form id="tombstone_search_frm" method="post" action="showcase_firm.php?id=<?php echo $g_view['firm_id'] ?>&from=savedSearches">
<input type="hidden" name="myaction" value="filter" />
     <table width="100%" border="0" cellspacing="6" cellpadding="0" style="display:block" id="filters">
      <tr>                                                                   
        <td width="50%" >
        <table width="100%" border="0" cellspacing="6" cellpadding="0">
          <tr>
            <td align="left" valign="top" style="width:120px;">Type of Transaction:</td>

            <td align="left" valign="top">&nbsp;</td>
          </tr>
          <tr>
            <td colspan="2" align="left" valign="top">
              <div class="radio_cat" style="font-size:10px;">
                <?php
                    $i = 1;
                    foreach($categories as $categoryName=>$subCats) :?>   
                <input type="radio" id="deal_cat_name<?php echo $i?>" name="deal_cat_name" value="<?php echo $categoryName?>" onClick="categoryChanged(<?php echo $i?>)" <?php if($_POST['deal_cat_name']==$categoryName){?>checked<?php }?>/><label for="deal_cat_name<?php echo $i?>"><?php echo $categoryName?></label>
                <?php $i++;endforeach?>
                </div>
              <?php 
                    $i = 1; $j = 1;
                    foreach($categories as $subCategoryName=>$subCats) :?>
              <div class="radio_subcat" style="font-size:10px;margin-top:5px;display: <?php if($_POST['deal_cat_name']==$subCategoryName){?>block<?php } else {?> none <?php }?>;" id="subCatsForCat<?php echo $i?>">   
                <?php foreach  ($subCats as $subCatName => $subSubCats) : ?>
                    <?php 
                        $origSubcatName = $subCatName;
                        $value = $subCatName;
                        if($subCatName == 'Pending') {
                            $subCatName = 'Pending & Completed';
                            $value = '';
                        }
                        if($subCatName == 'Completed') {
                            $subCatName = 'Completed only';
                            $value = 'Completed';
                        }
                        ?>
                    <input type="radio" id="deal_subCat_name<?php echo $j?>" name="deal_subcat1_name" value="<?php echo $value?>" onclick="subCategoryChanged(<?php echo $j?>)" <?php if($_POST['deal_subcat1_name']==$origSubcatName){?>checked<?php }?>/><label for="deal_subCat_name<?php echo $j?>"><?php echo $subCatName?></label>
                <?php $j++;endforeach;?> 
                </div>
              <?php $i++; endforeach;?>
              
              <?php 
                    $i = 1;$j = 1;$k = 1;
                    foreach($categories as $subCategoryName=>$subCats) :?>
              <?php foreach  ($subCats as $subCatName => $subSubCats) : ?>
              <div class="radio_subsubcat <?php echo "parent_$k"?>" style="font-size:10px;margin-top:5px; display:<?php if($_POST['deal_cat_name']==$subCategoryName && $_POST['deal_subcat1_name']==$subCatName){?>block<?php } else {?> none <?php }?>;" id="subSubCatsForCat<?php echo $j?>">   
                <?php foreach ($subSubCats as $key=>$name) : ?>
                <?php if ($name == 'n/a') continue ?>
                <input type="radio" id="deal_subSubCat_name<?php echo $i?>" name="deal_subcat2_name" value="<?php echo $name?>" <?php if($_POST['deal_subcat2_name']==$name){?>checked<?php }?>/><label for="deal_subSubCat_name<?php echo $i?>"><?php echo $name?></label>
                <?php $i++;endforeach; ?> 
                </div>
              <?php $j++;endforeach;?> 
              
              <?php $k++; endforeach;?>            </td>
          </tr>
        </table>
        </td>
        <td width="51%">
        <table width="100%" border="0" cellspacing="6" cellpadding="0">
          <tr>
            <td colspan="3">Refine Analysis</td>
          </tr>
          
          <tr>
            <td><select name="region" id="region" style="font-size: 10px; width: 200px;">
              <option value="">Any Region</option>
              <?php for($i=0;$i<$g_view['region_count'];$i++) :?>
              <option value="<?php echo $g_view['region_list'][$i]['name'];?>" <?php if($_POST['region']==$g_view['region_list'][$i]['name']){?>selected="selected"<?php }?>><?php echo $g_view['region_list'][$i]['name'];?></option>
              <?php endfor; ?>
            </select></td>
            <td>or</td>
            <td><select name="country" id="country" style="font-size: 10px; width: 200px;">
              <option value="">Any Country</option>
              <?php for($i=0;$i<$g_view['country_count'];$i++):?>
              <option value="<?php echo $g_view['country_list'][$i]['name'];?>" <?php if($_POST['country']==$g_view['country_list'][$i]['name']){?>selected="selected"<?php }?>><?php echo $g_view['country_list'][$i]['name'];?></option>
              <?php endfor?>
            </select></td>
          </tr>
          <tr>
            <td><select name="sector" id="sector" onChange="" style="width: 200px;">
              <option value="">Any Sector</option>
              <?php for($j=0;$j<$g_view['sector_count'];$j++):?>
              <option value="<?php echo $g_view['sector_list'][$j]['sector'];?>" <?php if($_POST['sector']==$g_view['sector_list'][$j]['sector']){?>selected="selected"<?php }?> ><?php echo $g_view['sector_list'][$j]['sector'];?></option>
              <?php endfor; ?>
            </select></td>
            <td>and</td>
            <td><select name="industry" id="industry" style="width: 200px;">
              <option value="">Any Industry</option>
              <?php for($j=0;$j<$g_view['industry_count'];$j++):?>
              <option value="<?php echo $g_view['industry_list'][$j]['industry'];?>" <?php if($_POST['industry']==$g_view['industry_list'][$j]['industry']){?>selected="selected"<?php }?> ><?php echo $g_view['industry_list'][$j]['industry'];?></option>
              <?php endfor?>
            </select></td>
          </tr>
          <tr>
            <td><select name="year" id="year" style="width: 200px;">
              <option value="" <?php if(empty($_POST['year'])) echo 'selected="selected"'?>>Refine by Year</option>
              <?php $curr_year = date("Y");
                        for($predate=2;$predate>0;$predate--):?>
              <option value="<?php echo $curr_year-$predate;?>" <?php if( !empty($_POST['year']) && $_POST['year']==$curr_year-$predate){?>selected="selected"<?php }?>><?php echo $curr_year-$predate;?> A</option>
              <?php endfor;?>
              <option value="<?php echo $curr_year;?>" <?php if(!empty($_POST['year']) && $_POST['year']==$curr_year){?>selected="selected"<?php }?>><?php echo $curr_year;?> YTD</option>
			  <?php
			  /**********
			  sng:23/sep/2011
			  need to preselect this if this was selected previously
			  ***********/
			  ?>
              <option value="<?php echo $curr_year-1;?>-<?php echo $curr_year;?>" <?php if(!empty($_POST['year']) && $_POST['year']==($curr_year-1)."-".$curr_year){?>selected="selected"<?php }?>><?php echo $curr_year-1;?>-<?php echo $curr_year;?> YTD</option>
            </select></td>
            <td>show</td>
            <td>
                <?php
				/**************************************
				sng:12/nov/2011
				we get the size dropdown here
				
				Never send the >= directly. It is treated as html and sanitizer will remove it.
				If you must sent, base64 encode it
				
				sng:20/jan/2012
				Now we send id of deal size range
				************************************/
				?>
				<select name="value_range_id" id="value_range_id" style="width: 200px;">
                <option value="">All deals</option>
                <?php for($j=0;$j<$g_view['deal_size_filter_list_count'];$j++):?>
                    <option value="<?php echo $g_view['deal_size_filter_list'][$j]['value_range_id'];?>" <?php if($_POST['value_range_id']==$g_view['deal_size_filter_list'][$j]['value_range_id']){?>selected="selected"<?php } ?> ><?php echo $g_view['deal_size_filter_list'][$j]['display_text'];?></option>
                <?php  endfor; ?>
				<option value="0" <?php if($_POST['value_range_id']=="0"){?>selected="selected"<?php } ?>>undisclosed value</option>
                </select>
            </td>
          </tr>
		  <?php
		  /****************************
		  sng:14/nov/2011
		  We show favourites only when I am seeing my firm
		  ***************************/
		  if($g_view['my_firm']){
		  		?>
			  <tr>
			  <td colspan="3" style="text-align:center;">
			  <input name="show_favourites" type="checkbox" value="y" <?php if(isset($_POST['show_favourites'])&&$_POST['show_favourites']=='y'){?>checked="checked"<?php }?> /> Only show favourites
			  </td>
			  </tr>
		  		<?php
		  }
		  ?>
        </table></td>
  </tr>
      <tr>
        <td colspan="2" align="center" style="text-align: right">
        </td>
      </tr>
      <tr>
        <td colspan="2" style="text-align: right"> 
        
                    
    
	<input id="downloadToPowerpoint" type="button" class="btn_auto" value="Download to PowerPoint" onclick="goto_download_powerpoint_savedSearch(<?php echo $g_view['firm_id'] . ",' " ; if (isset($_GET['token'])) echo $_GET['token'];echo "'";?>);" />       
                <?php
                if(!$g_account->is_site_member_logged()):?>
                 
                <?php else :?>
				&nbsp;&nbsp;
					<?php
					/*********************
					sng:10/nov/2011
					logged in, so check if viewing creds of his/her own firm. If so, only then show the buttons
					************************/
					if($g_view['firm_id']==$_SESSION['company_id']){
						?>
						<?php if ($savedSearches->searchBelongsToTheCurrentUser(base64_decode($_GET['token']))) : ?>
						<button id="updateSearch" onclick="return updateTombstoneSearch(<?php echo base64_decode($_GET['token'])?>);"> Update the Credential Search </button>
						<?php elseif($savedSearches->searchCanBeImported(base64_decode($_GET['token']))) : ?>
						<button id="importSearch" onclick="return saveTombstoneSearch();" > Import Search </button>
						<?php else : ?>
						<button id="saveSearch" onclick="return saveTombstoneSearch();" >Save the Credential Search</button>
						<?php endif ?>
						<?php
					}
					?> 
              <?php endif?>         
              &nbsp;&nbsp;<button id="filter">SEARCH</button>
			   
        </td>
      </tr>
    </table>
    </form>
<!---                     EndForm                  --->
<?php
/**********************************************************
sng:5/jan/2011
We put a form here that will allow the member to type a bank / law firm name and view the list of matching firms. Then click a link to see
the Credential page like this

sng:5/apr/2011
Since this is relevant only to bankers or lawyers, we put a check

sng:3/aug/2011
We now remove the search for competitor's tombstone in competitor_credentials.php which is accessible from its own menu
/**********************************************************/
?>
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

sng:21/oct/2011
We now allow the member to see charts that showcase this firm

sng:9/nov/2011
We make this open to all
********/
//if($g_account->is_site_member_logged()){
?>
<!--<input id="downloadToPowerpoint" type="button" class="btn_auto" value="download to powerpoint" onclick="goto_download_powerpoint_savedSearch(<?php echo $g_view['firm_id'] . ",' " ; if (isset($_GET['token'])) echo $_GET['token'];echo "'";?>);" />-->
<?php
//}
?>
</td>
</tr>
<tr><td colspan="2" style="height:10px;">&nbsp;</td></tr>
</table>
</div>
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
                $g_trans->get_tombstone_from_deal_id($g_view['data'][$i]['transaction_id'], false, $g_account->is_site_member_logged() ? true : false);
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
<div>NB: displaying only the most recent 60 transactions.</div>
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
            <input type="button" onclick="do_download_powerpoint_savedSearch();" value="download" class="btn_auto">
            <input type="hidden" value="" name="firmId" id="firmId" />
            <input type="hidden" value="" name="searchId" id="searchId" />
            </td>
          </tr>
        </table>
    </div>
    <div id="backgroundPopup"></div>