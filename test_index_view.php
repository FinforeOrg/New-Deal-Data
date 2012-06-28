<script src="js/jquery-1.4.4.min.js" type="text/javascript"></script>
<script src="js/jquery-ui-1.8.9.custom.min.js" type="text/javascript"></script>  
<script src="js/jquery.ui.selectmenu.js" type="text/javascript"></script>
<link rel="stylesheet" href="css/custom-theme/jquery-ui-1.8.9.custom.css" />
<link rel="stylesheet" href="css/custom-theme/jquery-ui-1.8.9.custom_orange.css" />
<link rel="stylesheet" href="css/custom-theme/jquery.ui.selectmenu.css" />
<script>
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
    $('button').button().click(function(event){event.preventDefault()});
    $('#submit').button();
    $('#generate').button().click(function(data){
        var myData = $('#leagueTableForm').serialize()
        $("#chart1").html('');
        $("#chart1").addClass('loading');
        $.post(
            '/ajax/league_table_creator.php?version=2',
            myData,
            function(returned) {
                //console.log(returned);
                 $("#chart1").removeClass('loading');
                 $('#script').html(returned);
                 //
            }
        );
    });

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

function generateCaption() {
    var caption = 'Top 5';
    partner_type = $('input[name="partner_type"]:checked').val();
    //console.log(partner_type);
    if(partner_type == "bank"){
        caption += " Banks";
    }
    if(partner_type == "law firm"){
        caption += " Law Firms";
    }
    ranking_criteria = $('#ranking_criteria').val();
    if(ranking_criteria=="num_deals"){
        caption += " based on number of deals";
    }
    if(ranking_criteria=="total_deal_value"){
        caption += " based on total deal value";
    }
    if(ranking_criteria=="total_adjusted_deal_value"){
        caption += " based on total adjusted deal value";
    }
    $('#chart_caption').html(caption);       
}

function saveLeagueSearch(){ 
    $.post(
        'saved_searches.php?action=saveSearch&type=league',
        $('#leagueTableForm').serialize(),
        function(data) {
            alert(data.message)
            if (data.newLocation != undefined) {
                if (data.newLocation.length)
                    window.location.href = data.newLocation;                
            }
        },
        'json'
    )
    return false;
}

function updateLeagueSearch(id) {
    $.post(
        'saved_searches.php?action=updateSearch&type=league&id=' + id,
        $('#leagueTableForm').serialize(),
        function(data) {
            window.location.href = window.location.href;             
        }    
    )
    return false;
}
function showCustomizeForm() {
    centerPopup();
    loadPopup();
}
function do_getCustomizedChart() {
    var url = 'chartgen/generate.php?type=leagueTables';
    var data = jQuery("#formCustomizeDownload").serialize();
    download(url,data);
    disablePopup();
} 

function download(url, data, method){
//url and data options required
    if( url && data ){ 
        //data can be string of parameters or array/object
        data = typeof data == 'string' ? data : jQuery.param(data);
        //split params into form inputs
        var inputs = '';
        jQuery.each(data.split('&'), function(){ 
            var pair = this.split('=');
            inputs+='<input type="hidden" name="'+ pair[0] +'" value="'+ pair[1] +'" />'; 
        });
        //send request
        jQuery('<form action="'+ url +'" method="'+ (method||'post') +'">'+inputs+'</form>')
        .appendTo('body').submit().remove();
    };
};
</script>
<table width="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
<td><input type="button" class="btn_auto" value="Submit Deal Information" onclick="window.location.replace('suggest_deal.php');" /></td>
<td><input type="button" class="btn_auto" value="Look Up Deal Information" onclick="window.location.replace('deal.php');" /></td>
<?php
if(!$g_account->is_site_member_logged()){
	?>
	<td><input type="button" class="btn_auto" value="Register for Access" onclick="window.location.replace('register.php?method=choose');" /></td>
	<?php
}
?>
</tr>
<tr><td style="height:20px;" colspan="3">&nbsp;</td></tr>
</table>
<table width="100%" cellspacing="0" cellpadding="0" border="0">
    <tbody>
        <tr>
            <td style="text-align: left;">
                <h1>League Table</h1>
            </td>
            <td style="text-align: right;"> 
                <a href="top_firms.php">Details of the top banks and law firms</a>
            </td>
        </tr>
    </tbody>
</table>
<div style="display: none;">   <pre>  <?php var_dump($_POST)?></pre></div>
<form method="post" id="leagueTableForm" action='league_table_detail.php'>
<table width="100%" border="0" cellspacing="5" cellpadding="5" class="registercontent">
    <tr>
        <th width="49%">Customize your league table</th>
        <th id="chart_caption"> Top 5 Banks based on number of deals</th>
    </tr>
  <tr>
    <td>
        <div class="loading" style="height: 300px;" >
        </div>
        <table width="100%" border="0" cellspacing="6" cellpadding="0" id="leftSide" style="display: none;">
          <tr>
            <td>Type of firm:</td>
            <td colspan="2">            
            <div class="radio" style="font-size:10px;">
                <input type="radio" id="partner_type1" name="partner_type" checked="checked" value="bank" /><label for="partner_type1">Bank</label>
                <input type="radio" id="partner_type2" name="partner_type"  value="law firm"/><label for="partner_type2" >Law Firm</label>
            </div>

            </td>
          </tr>
          <tr>
            <td width="100px;">Type of LT:</td>
            <td colspan="2" align="left" style="font-size: 10px;" id='cats'> 
              <div class="radio_cat" style="font-size:10px;">
            <?php
                $i = 1;
                foreach($categories as $categoryName=>$subCats) :?>   
                <input type="radio" id="deal_cat_name<?php echo $i?>" name="deal_cat_name" value="<?php echo $categoryName?>" onclick="categoryChanged(<?php echo $i?>)" <?php if($_POST['deal_cat_name']==$categoryName){?>checked<?php }?>/><label for="deal_cat_name<?php echo $i?>"><?php echo $categoryName?></label>
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
                    <input type="radio" id="deal_subCat_name<?php echo $j?>" name="deal_subcat1_name" value="<?php echo ($subCatName == 'All') ? ''  : $subCatName?>" onclick="subCategoryChanged(<?php echo $j?>)" <?php if($_POST['deal_subcat1_name']==$origSubcatName){?>checked<?php }?>/><label for="deal_subCat_name<?php echo $j?>"><?php echo $subCatName?></label>
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
                
            <?php $k++; endforeach;?>
            </td>
          </tr>
            <tr>
                <td colspan="3">Refine Analysis</td>
            </tr>
          <tr>
            <td>
                <select name="region" id="region" style="font-size: 10px; width: 200px;">
                    <option value="">Any Region</option>
                    <?php for($i=0;$i<$g_view['region_count'];$i++) :?>
                    <option value="<?php echo $g_view['region_list'][$i]['name'];?>" <?php if($_POST['region']==$g_view['region_list'][$i]['name']){?>selected="selected"<?php }?>><?php echo $g_view['region_list'][$i]['name'];?></option>
                    <?php endfor; ?>                   
                </select>            
            </td>
            <td>or</td>
            <td>
                <select name="country" id="country" style="font-size: 10px; width: 200px;">
                    <option value="">Any Country</option>
                    <?php for($i=0;$i<$g_view['country_count'];$i++):?>
                    <option value="<?php echo $g_view['country_list'][$i]['name'];?>" <?php if($_POST['country']==$g_view['country_list'][$i]['name']){?>selected="selected"<?php }?>><?php echo $g_view['country_list'][$i]['name'];?></option>
                    <?php endfor?>                 
                </select>             
            </td>
          </tr>          
          <tr>
            <td>
                <select name="sector" id="sector" onchange="" style="width: 200px;">
                    <option value="">Any Sector</option>
                    <?php for($j=0;$j<$g_view['sector_count'];$j++):?>
                    <option value="<?php echo $g_view['sector_list'][$j]['sector'];?>" <?php if($_POST['sector']==$g_view['sector_list'][$j]['sector']){?>selected="selected"<?php }?> ><?php echo $g_view['sector_list'][$j]['sector'];?></option>
                    <?php endfor; ?>
                </select>            
            </td>
            <td>and</td>
            <td>
                <select name="industry" id="industry" style="width: 200px;">
                    <option value="">Any Industry</option>
                    <?php for($j=0;$j<$g_view['industry_count'];$j++):?>
                    <option value="<?php echo $g_view['industry_list'][$j]['industry'];?>" <?php if($_POST['industry']==$g_view['industry_list'][$j]['industry']){?>selected="selected"<?php }?> ><?php echo $g_view['industry_list'][$j]['industry'];?></option>
                    <?php endfor?>
                </select>            
            </td>
          </tr>
          <tr>
            <td>
                <select name="year" id="year" style="width: 200px;">
                    <option value="">Refine by Year</option>
                    <?php $curr_year = date("Y");
                    for($predate=2;$predate>0;$predate--):?>
                        <option value="<?php echo $curr_year-$predate;?>" <?php if($_POST['year']==$curr_year-$predate){?>selected="selected"<?php }?>><?php echo $curr_year-$predate;?> A</option>
                        <?php endfor;?>
                    <option value="<?php echo $curr_year;?>" <?php if($_POST['year']==$curr_year){?>selected="selected"<?php }?>><?php echo $curr_year;?> YTD</option>
                    <option value="<?php echo $curr_year-1;?>-<?php echo $curr_year;?>" selected='selected'><?php echo $curr_year-1;?>-<?php echo $curr_year;?> YTD</option>
                </select>             
            </td>
            <td>and</td>
            <td>  
                <select name="deal_size" id="deal_size" style="width: 200px;">
                <option value="">Refine by Deal Size</option>
                <?php for($j=0;$j<$g_view['deal_size_filter_list_count'];$j++):?>
                    <option value="<?php echo $g_view['deal_size_filter_list'][$j]['condition'];?>" <?php if($_POST['deal_size']==$g_view['deal_size_filter_list'][$j]['condition']){?>selected="selected"<?php } else { if ($g_view['deal_size_filter_list'][$j]['condition'] == '>=0.100') echo "selected='selected'"; }?> ><?php echo $g_view['deal_size_filter_list'][$j]['caption'];?></option>
                <?php  endfor; ?>
                </select>            
            </td>
          </tr>
        <tr>
            <td colspan="3" align="center" valign="top">
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
          <tr>
            <td colspan="3" align="center" valign="top">
              <span class="orange"><button onclick="return false;" id="generate"> Generate </button> </span>
              <?php
                if(!$g_account->is_site_member_logged()):?>
               <span class="orange"> <button onclick="window.location.href = '/index.php';return false;"> Login to view details </button> <button onclick="window.location.href = '/index.php';return false;"> Login to download to PowerPoint </button> </span>
              <?php else :?>
                <span class="orange"><input type="submit" id="submit" value="Show details" /></span>
                <?php if ($savedSearches->searchBelongsToTheCurrentUser(base64_decode($_GET['token']))) : ?>
                  <span class="orange"> <button id="updateSearch" onclick="return updateLeagueSearch(<?php echo base64_decode($_GET['token'])?>);"> Update search </button></span>
                <?php elseif($savedSearches->searchCanBeImported(base64_decode($_GET['token']))) : ?>
                  <span class="orange"><button id="importSearch" onclick="return saveLeagueSearch();" > Import search </button> </span>
                <?php else : ?>
                  <span class="orange" ><button id="saveSearchButton" onclick="return saveLeagueSearch();" >  Save search </button> </span> 
                <?php endif ?> 
                <span class="orange" ><button class='orange' onclick="return showCustomizeForm(); "> Download to powerpoint </button></span>               
              <?php endif?>
              
            </td>
          </tr>          
          
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
        </table>    
    
    
    </td>
    <td> 

    <div id="script"> 
    <script type="text/javascript" class="code">
        $(document).ready(function() {
            $(".loading").hide();
            $("#leftSide").show();
            $.post(
            '/ajax/league_table_creator.php?version=2',
            $('#leagueTableForm').serialize(),//{'partner_type' : 'bank', 'ranking_criteria' : 'num_deals'},
            function(returned) {
                $('#script').html(returned);
            }
            )
        });
        </script>     
    </div>
   
        <div id="chart1" style="margin-top:20px; margin-left:20px; width:90%; height:300px; float:left; position:relative;">
            <div class="loading" style="height: 300px;" >
            </div>       
        </div>
    </td>
  </tr>
</table>
</form> 
<link rel="stylesheet" href="css/savedSearches.css" type="text/css" media="screen" />



<script language="javascript" type="text/javascript" src="/js/excanvas.compiled.js"></script>
<script language="javascript" type="text/javascript" src="/js/jqplot/jquery.jqplot.js"></script>
<!--<script language="javascript" type="text/javascript" src="/js/jqplot/plugins/jqplot.canvasAxisTickRenderer.min.js"></script>   
<script language="javascript" type="text/javascript" src="/js/jqplot/plugins/jqplot.canvasAxisLabelRenderer.min.js"></script>  
--><script language="javascript" type="text/javascript" src="/js/jqplot/plugins/jqplot.barRenderer.min.js"></script>
<script language="javascript" type="text/javascript" src="/js/jqplot/plugins/jqplot.categoryAxisRenderer.min.js"></script>
<script src="/js/scripts.js" type="text/javascript" charset="utf-8"></script>
<script language="javascript" type="text/javascript" src="/js/jqplot/plugins/jqplot.pointLabels.min.js"></script>   
    <div id="popupShare" style="height: 220px;">
    <form action="#" method="get" id="formCustomizeDownload">
        <a id="popupShareClose">x</a>
        <h1>Download to powerpoint</h1>
        <table width="600" border="0">
          <tr>
            <td>Title for your chart<br />
            <input name="pptTitle" id="pptTitle" type="text"  style="width:100%"/></td>
          </tr>
          <tr>
            <td>Color for your chart<br />
            
              <p>
                <label>
                  <input type="radio" name="color" value="cccccc" id="color_0" checked="checked" />
                  Light Gray</label>
                <label>
                  <input type="radio" name="color" value="928d8d" id="color_1" />
                  Dark Gray</label>
                <label>
                  <input type="radio" name="color" value="52a3dc" id="color_2" />
                  Light Blue</label>
                <label>
                  <input type="radio" name="color" value="000000" id="color_3" />
                  Black</label>

              </p>
              
            </td>
          </tr>
          <tr>
            <td align="right">
            <input type="button" onclick="return do_getCustomizedChart();" value="download" class="btn_auto"> <br />
            Note: you can double click on the chart and alter it in PowerPoint, after you have downloaded it.
            </td>
          </tr>
        </table>
        <br />
        </form>
    </div>

    <div id="backgroundPopup"></div> 