<?php
require_once("league_table_filter_support_js.php");
?>
<?php
/*********************************
sng:1/oct/2011
<script src="js/jquery-1.4.4.min.js" type="text/javascript"></script>
<script src="js/jquery-ui-1.8.9.custom.min.js" type="text/javascript"></script>  
<script src="js/jquery.ui.selectmenu.js" type="text/javascript"></script>
<link rel="stylesheet" href="css/custom-theme/jquery-ui-1.8.9.custom.css" />
<link rel="stylesheet" href="css/custom-theme/jquery-ui-1.8.9.custom_orange.css" />
<link rel="stylesheet" href="css/custom-theme/jquery.ui.selectmenu.css" />
*************************************/
?>

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
    $('#deal_size').selectmenu();    
    $('#sector').selectmenu().change(function(idx){
        $.post(
            '/admin/ajax/industry_list_for_sector.php?for=leagueTables',
            {'sector' : $(this).selectmenu('value')},
            function(data) {
                $('#industry').html(data).selectmenu();
            }
        )
        /*alert($(this).selectmenu('value') )*/
    });
    //$('button').button().click(function(event){event.preventDefault()});
    $('#submit').button();
    $('#chartButton').button().click(function(event){
         $("#league_table_filter").attr('action','index.php').submit();    
    });
    $('#updateButton').button().click(function(event){
         $("#league_table_filter").attr('action','league_table_detail.php').attr('target','_self').submit();    
    })
    $('#downloadButton').button().click(function(event){
         $("#league_table_filter").attr('action','download_league_table_detail.php').attr('target','_blank').submit();    
    });
    $('#saveSearch').button().click(function(event){event.preventDefault()});
    $('#updateSearch').button().click(function(event){event.preventDefault()});     
    $('#importSearch').button().click(function(event){event.preventDefault()});     
    
    $(".loading").hide(); 
    $("#filters").show(); 
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
</script>
<div id="explanation">
<p>The detail behind your league table request is displayed below. However, using the drop-down menus you can revise the request and run additional analysis.</p>
<p>If you want to see what deals are used to calculate any specific firms ranking, click on the name of the firm and this will bring you to an analysis of the relevant deals that firm has been involved in.</p>
</div>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
<td>
<!--filter area-->
<form method="post" action="" id="league_table_filter" target="_self">
<?php
/***************
sng:8/jan/2011
Client now wants that when the data is submitted to league_table, the user will see the chart
Problem is, how the code in the league_table will know that it is comming from league_table_detail?
We send a myaction here, and if that is present, the code in league_table does not fetch a random chart
and the code in the view page triggers a chart creation.
**********/
?>        
<input type="hidden" name="myaction" value="gen_chart" />
<div class="loading" style="height: 250px; width: 100%;" >
</div>
      <table width="100%" border="0" cellspacing="6" cellpadding="0" style="display:none" id="filters">
      <tr>                                                                   
        <td width="50%">
        <table width="100%" border="0" cellspacing="6" cellpadding="0">
          <tr>
            <td width="70">Type&nbsp;of&nbsp;firm:</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td colspan="2">            
              <div class="radio" style="font-size:10px;">
                <input type="radio" id="partner_type1" name="partner_type" <?php if ( (isset($_POST['partner_type']) && $_POST['partner_type'] == 'bank') || !isset($_POST['partner_type']))  echo 'checked="checked"'?> value="bank" /><label for="partner_type1">Bank</label>
                <input type="radio" id="partner_type2" name="partner_type"  <?php if (isset($_POST['partner_type']) && $_POST['partner_type'] == 'law firm') echo 'checked="checked"'?> value="law firm"/><label for="partner_type2" >Law Firm</label>
            </div></td>
          </tr>
          <tr>
            <td align="left" valign="top">Type of LT:</td>
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
                            if($subCatName == 'Pending') {
                            $subCatName = 'All';
                        }?>
                <input type="radio" id="deal_subCat_name<?php echo $j?>" name="deal_subcat1_name" value="<?php echo ($subCatName == 'All') ? ''  : $subCatName?>" onClick="subCategoryChanged(<?php echo $j?>)" <?php if($_POST['deal_subcat1_name']==$origSubcatName){?>checked<?php }?>/><label for="deal_subCat_name<?php echo $j?>"><?php echo $subCatName?></label>
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
              
              <?php $k++; endforeach;?>        </td>
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
        <td><select name="sector" id="sector" onchange="" style="width: 200px;">
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
          <option value="<?php echo $curr_year-1;?>-<?php echo $curr_year;?>" <?php if(!empty($_POST['year']) && $_POST['year']==($curr_year-1)."-".$curr_year){?>selected="selected"<?php }?>><?php echo $curr_year-1;?>-<?php echo $curr_year;?> YTD</option>
        </select></td>
        <td>and</td>
        <td><select name="deal_size" id="deal_size" style="width: 200px;">
          <option value="">Refine by Deal Size</option>
          <?php for($j=0;$j<$g_view['deal_size_filter_list_count'];$j++):?>
          <option value="<?php echo $g_view['deal_size_filter_list'][$j]['condition'];?>" <?php if($_POST['deal_size']==$g_view['deal_size_filter_list'][$j]['condition']){?>selected="selected"<?php } else { if ($g_view['deal_size_filter_list'][$j]['condition'] == '>=0.100') echo "selected='selected'"; }?> ><?php echo $g_view['deal_size_filter_list'][$j]['caption'];?></option>
          <?php  endfor; ?>
        </select></td>
      </tr>
      <tr>
        <td colspan="3">
<table>
                    <tr>
                        <td>Type of Chart:
                        </td>
                        <td>
                            <select name="ranking_criteria" id="ranking_criteria" style="width:200px;">
                                <option value="num_deals" <?php if(!isset($_POST['ranking_criteria'])||($_POST['ranking_criteria']=="num_deals")){?>selected="selected"<?php }?>>Total number of deals</option>
                                <option value="total_deal_value" <?php if($_POST['ranking_criteria']=="total_deal_value"){?>selected="selected"<?php }?>>Total deal value</option>
                                <option value="total_adjusted_deal_value" <?php if($_POST['ranking_criteria']=="total_adjusted_deal_value"){?>selected="selected"<?php }?>>Total adjusted deal value</option>
                            </select>                         
                        </td>
                    </tr>
                </table>        
        </td>
        </tr>
    </table></td>
  </tr>
  <tr>
    <td colspan="2" style="text-align: right">
            <button class="btn_auto" value="Chart" id="chartButton" > Chart </button>
            <button name="submit" class="btn_auto" value="Update" id="updateButton"> Update </button>                   
            <button  class="btn_auto" value="Download to Excel" id="downloadButton">   Download to Excel </button>
        <?php 
        if($g_account->is_site_member_logged()){
            if ($savedSearches->searchBelongsToTheCurrentUser(base64_decode($_GET['token']))) : ?>
            <button class="btn_auto" id="updateSearch" value="Update search" onclick="return updateLeagueDetailSearch(<?php echo base64_decode($_GET['token'])?>);"> Update search </button> 
            <?php elseif($savedSearches->searchCanBeImported(base64_decode($_GET['token']))) : ?>
             <button class="btn_auto" id="importSearch" value="Import search" > Import search </button> 
            <?php else : ?>
             
                <button class="btn_auto" value="Save search" id="saveSearch" onclick="return saveLeagueDetailSearch();" > Save search </button>
            
            <?php endif; 
        } ?>
        <input type="submit" value="Submit" style="display: none;" name="submit" />     
    
    </td>
  </tr>
</table>
</form>
<!--filter area-->
</td>
</tr>
<tr>
<td>
<!--listing data-->
<table width="100%" cellpadding="0" cellspacing="0" class="company">
<tr>
<th>Rank</th>
<th>Firm</th>
<th>Tombstone #</th>
<th>Tombstone $billion</th>
<th>Adjusted $billion</th>
</tr>
<?php
if(0==$g_view['data_count']){
    ?>
    <tr>
    <td colspan="5">
    None found.
    </td>
    </tr>
    <?php
}else{
    //we fetched one extra
    if($g_view['data_count'] > $g_view['num_to_show']){
        $total = $g_view['num_to_show'];
    }else{
        $total = $g_view['data_count'];
    }
    ////////////////////////////////////////////////////////////////////
    /***
    sng:10/jul/2010
    As we want to show the deals filtered by the condition, we need to sent the filter clauses via
    hidden form fields. That is why, simple href is not used.
    ****/
    for($j=0;$j<$total;$j++){
        ?>
        <tr>
        <td><?php echo $g_view['start_offset']+$j+1;?></td>
        <td><a href="#" onclick="return go_firms_deals(<?php echo $g_view['data'][$j]['partner_id'];?>);"><?php echo $g_view['data'][$j]['firm_name'];?></a></td>
        <td><?php echo $g_view['data'][$j]['num_deals'];?></td>
        <td><?php echo number_format($g_view['data'][$j]['total_deal_value'],2);?></td>
        <td><?php echo number_format($g_view['data'][$j]['total_adjusted_deal_value'],2);?></td>
        </tr>
        <?php
    }
    //////////////////////////////////////
    ?>
    <!--
    sng:10/jul/2010
    Sector was missing
    
    sng:17/jul/2010
    Now logged in user can filter via industry also. We put hidden field to help in pagination, protected
    by if clause
    
    sng:23/july/2010
    Support for the field deal_size
    -->
    <form id="pagination_helper" method="post" action="league_table_detail.php">
    <input type="hidden" name="partner_type" value="<?php echo $_POST['partner_type'];?>" />
    <input type="hidden" name="region" value="<?php echo $_POST['region'];?>" />
    <input type="hidden" name="country" value="<?php echo $_POST['country'];?>" />
    <input type="hidden" name="deal_cat_name" value="<?php echo $_POST['deal_cat_name'];?>" />
    <input type="hidden" name="deal_subcat1_name" value="<?php echo $_POST['deal_subcat1_name'];?>" />
    <input type="hidden" name="deal_subcat2_name" value="<?php echo $_POST['deal_subcat2_name'];?>" />
    <input type="hidden" name="sector" value="<?php echo $_POST['sector'];?>" />
    <?php
    if($g_account->is_site_member_logged()){
        ?>
        <input type="hidden" name="industry" value="<?php echo $_POST['industry'];?>" />
        <?php
    }
    ?>
    <input type="hidden" name="year" value="<?php echo $_POST['year'];?>" />
    <input type="hidden" name="deal_size" value="<?php echo $_POST['deal_size'];?>" />
    <input type="hidden" name="ranking_criteria" value="<?php echo $_POST['ranking_criteria'];?>" />
    <input type="hidden" name="start" id="pagination_helper_start" value="0" />
    
    </form>
    
    <script type="text/javascript">
    function go_page(offset){
        document.getElementById('pagination_helper_start').value = offset;
        document.getElementById('pagination_helper').submit();
        return false;
    }
    </script>
    
    
    <?php
    /***
    sng:10/jul/2010
    When seeing the list of deals by clicking on the firm name, we want to show only the deals that satisfy the
    filters. So we send the filters via hidden field
    
    sng:17/jul/2010
    Now logged in user can filter via industry also. We put hidden field to help in pagination, protected
    by if clause
    
    sng:23/july/2010
    support for field deal_size
    ******/
    ?>
    <form id="firm_deals_helper" method="post" action="dummy.php">
    <!--
    The firm id is sent via query string
    -->
    <input type="hidden" name="region" value="<?php echo $_POST['region'];?>" />
    <input type="hidden" name="country" value="<?php echo $_POST['country'];?>" />
    <input type="hidden" name="deal_cat_name" value="<?php echo $_POST['deal_cat_name'];?>" />
    <input type="hidden" name="deal_subcat1_name" value="<?php echo $_POST['deal_subcat1_name'];?>" />
    <input type="hidden" name="deal_subcat2_name" value="<?php echo $_POST['deal_subcat2_name'];?>" />
    <input type="hidden" name="sector" value="<?php echo $_POST['sector'];?>" />
    <?php
    if($g_account->is_site_member_logged()){
        ?>
        <input type="hidden" name="industry" value="<?php echo $_POST['industry'];?>" />
        <?php
    }
    ?>
    <input type="hidden" name="year" value="<?php echo $_POST['year'];?>" />
    <input type="hidden" name="deal_size" value="<?php echo $_POST['deal_size'];?>" />
    <!--
    pagination offset is also sent by query string
    -->
    </form>
    
    <script type="text/javascript">
    function go_firms_deals(firm_id){
        document.getElementById('firm_deals_helper').action = "firm_deals.php?id="+firm_id;
        document.getElementById('firm_deals_helper').submit();
        return false;
    }
    </script>
    <tr>
    <td colspan="5" style="text-align:right;">
    <?php
    if($g_view['start_offset'] > 0){
        ?>
        <a class="link_as_button" href="#" onclick="return go_page(<?php echo $g_view['start_offset']-$g_view['num_to_show'];?>);">Prev</a>
        <?php
    }
    if($g_view['data_count'] > $g_view['num_to_show']){
        ?>
        &nbsp;&nbsp;&nbsp;<a class="link_as_button" href="#" onclick="return go_page(<?php echo $g_view['start_offset']+$g_view['num_to_show'];?>);">Next</a>
        <?php
    }
    ?>
    </td>
    </tr>
    <?php
}
?>
</table>
<!--listing data-->
</td>
</tr>
</table>
<script type="text/javascript">
function updateLeagueDetailSearch(id){
    
    jQuery.post(
        'saved_searches.php?action=updateSearch&type=leagueDetail&id=' + id,
         $('#league_table_filter').serialize(true),
         function(response, status) {
             window.location.href = window.location.href;    
         },
         'json'
    )    
/*    new Ajax.Request('saved_searches.php?action=updateSearch&type=leagueDetail&id=' + id, {
        method: 'post',
        parameters: $('league_table_filter').serialize(true),
        onSuccess: function(transport){
            
        },
        onFailure: function(){
             alert("There was an error in your request. Please try again later");
        }
    });    */
}

function saveLeagueDetailSearch(){
    jQuery.post(
        'saved_searches.php?action=saveSearch&type=leagueDetail',
         $('#league_table_filter').serialize(true),
         function(response, status) {
            alert(response.message);
            if (response.newLocation != undefined) {
               window.location.href = response.newLocation; 
            }
            //    
         },
         'json'
    )
/*    new Ajax.Request('saved_searches.php?action=saveSearch&type=leagueDetail', {
        method: 'post',
        parameters: $('league_table_filter').serialize(true),
        onSuccess: function(transport){
            json = transport.responseText.evalJSON(true)
            alert(json.message)
            if (json.newLocation.length)
            window.location.href = json.newLocation;
        },
        onFailure: function(){
             alert("There was an error in your request. Please try again later");
        }
    });*/
}

</script>