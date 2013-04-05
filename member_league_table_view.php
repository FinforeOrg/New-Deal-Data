<script>
$(function() {
    $( ".radio" ).buttonset().click(function(){$( ".radio" ).buttonset('refresh')}); ;

    $( ".LT_radio_subcat" ).buttonset().click(function(){
        $( ".LT_radio_subsubcat :radio" ).each(function(idx){
            $(this).removeAttr('checked');
        })
        $( ".LT_radio_subsubcat" ).buttonset('refresh')
    });

    $( ".LT_radio_cat" ).buttonset().click(function(){
        $( ".LT_radio_subcat :radio" ).each(function(idx){
            $(this).removeAttr('checked');
        })
        $( ".LT_radio_subcat" ).buttonset('refresh')
    });

    $( ".LT_radio_subsubcat" ).buttonset();
    
    /**
* Handle the case when a hidden sub sub cat is checked
*/
    $('#LT_cats :radio').click(function() {
        $('#LT_cats :checked').each(function(idx){
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
            'admin/ajax/industry_list_for_sector.php?for=leagueTables',
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
		alert("generate");return;
        var myData = $('#leagueTableForm').serialize();
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

    $('input[type="button"]').button();
    $('input[type="submit"]').button();
});

function LT_categoryChanged(category) {
    $('div.LT_radio_subcat:visible').hide();
    $('#LT_subCatsForCat' + category).show();
    $('div.LT_radio_subsubcat:visible').hide();
}

function LT_subCategoryChanged(subCategory) {
    $('div.LT_radio_subsubcat:visible').hide();
    $('#LT_subSubCatsForCat' + subCategory).show();
}

function generateCaption() {
    var caption = 'Top 10';
    member_type = $('#member_type').val();
    if(member_type == "banker"){
        caption += " Bankers";
    }
    if(member_type == "lawyer"){
        caption += " Lawyers";
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
	alert("save");return false;
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
	alert("update");return false;
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
	alert("customize");return false;
    var url = 'chartgen/generate.php?type=leagueTables';
    var data = jQuery("#formCustomizeDownload").serialize();
    download(url,data);
    disablePopup();
}

function download(url, data, method){
	alert("download");return false;
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


<div style="display: none;"> <pre> <?php var_dump($_POST)?></pre></div>

<form method="post" id="leagueTableForm" action='league_table_detail.php'>
<table width="100%" border="0" cellspacing="5" cellpadding="5" class="registercontent">
<tr>
<th width="49%">Run a league table analysis</th>
<th id="chart_caption">Top 10 <?php if("banker"==$g_view['for']){?>Bankers<?php }elseif("lawyer"==$g_view['for']){?>Lawyers<?php }?> based on number of deals</th>
</tr>
<tr>
<td>
<div class="loading" style="height: 300px;" >
</div>
<table width="100%" border="0" cellspacing="6" cellpadding="0" id="leftSide" style="display: none;">
<tr>
<td>For:</td>
<td colspan="2">
<div>
<?php
if("banker"==$g_view['for']){
	?><input type="hidden" name="member_type" id="member_type" value="banker" />Banker<?php
}elseif("lawyer"==$g_view['for']){
	?><input type="hidden" name="member_type" id="member_type" value="lawyer" />Lawyer<?php
}
?>
</div>

</td>
</tr>
<tr>
<td width="100px;">Type of LT:</td>

<td colspan="2" align="left" style="font-size: 10px;" id='LT_cats'>
<div class="LT_radio_cat" style="font-size:10px;">
<?php
                $i = 1;
                foreach($categories as $categoryName=>$subCats) :?>
				<?php
				/*************
				sng:26/jan/2013
				HACK
				We want to show only Equities, but only the subtype and sub sub type. We just need to hide the type part. However, we do send the data via hidden element
				***************
                <input type="radio" id="deal_cat_name<?php echo $i?>" name="deal_cat_name" value="<?php echo $categoryName?>" onClick="LT_categoryChanged(<?php echo $i?>)" <?php if($_POST['deal_cat_name']==$categoryName){?>checked<?php }?>/><label for="deal_cat_name<?php echo $i?>"><?php echo $categoryName?></label>
				********/?>
				<input type="hidden" name="deal_cat_name" value="<?php echo $categoryName?>" />
<?php $i++;endforeach?>
</div>
<?php
                $i = 1; $j = 1;
                foreach($categories as $subCategoryName=>$subCats) :?>
                <div class="LT_radio_subcat" style="font-size:10px;margin-top:5px;display: <?php if($_POST['deal_cat_name']==$subCategoryName){?>block<?php } else {?> none <?php }?>;" id="LT_subCatsForCat<?php echo $i?>">
                 <?php foreach ($subCats as $subCatName => $subSubCats) : ?>
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
<input type="radio" id="deal_subCat_name<?php echo $j?>" name="deal_subcat1_name" value="<?php echo $value?>" onClick="LT_subCategoryChanged(<?php echo $j?>)" <?php if($_POST['deal_subcat1_name']==$origSubcatName){?>checked<?php }?>/><label for="deal_subCat_name<?php echo $j?>"><?php echo $subCatName?></label>
<?php $j++;endforeach;?>
</div>
<?php $i++; endforeach;?>
<?php
                $i = 1;$j = 1;$k = 1;
                foreach($categories as $subCategoryName=>$subCats) :?>
                 <?php foreach ($subCats as $subCatName => $subSubCats) : ?>
				 <?php
				 /*************
				 sng:26/jan/2013
				 HACK
				 We also need to show the 'Common Equity' sub sub types to be visible
				 ******************
<div class="LT_radio_subsubcat <?php echo "parent_$k"?>" style="font-size:10px;margin-top:5px; display:<?php if($_POST['deal_cat_name']==$subCategoryName && $_POST['deal_subcat1_name']==$subCatName){?>block<?php } else {?> none <?php }?>;" id="LT_subSubCatsForCat<?php echo $j?>">
******************************/
?>
<div class="LT_radio_subsubcat <?php echo "parent_$k"?>" style="font-size:10px;margin-top:5px; display:block;" id="LT_subSubCatsForCat<?php echo $j?>">
<?php /******************************************************************/?>
<?php foreach ($subSubCats as $key=>$name) : ?>
<?php if ($name == 'n/a') continue ?>
<input type="radio" id="deal_subSubCat_name<?php echo $i?>" name="deal_subcat2_name" value="<?php echo $name?>" <?php if($_POST['deal_subcat2_name']==$name){?>checked<?php }?>/><label for="deal_subSubCat_name<?php echo $i?>"><?php echo $name?></label>
<?php $i++;endforeach; ?>
</div>
<?php $j++;endforeach;?>
<?php $k++; endforeach;?>
<?php
/*************
sng:26/jan/2013
HACK
We have hidden the type, but problem is, how do we show the subtype? We call the JS function here
*******************/
?>
<script>LT_categoryChanged(1);</script>
<?php
/**********************************/
?>
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
<select name="sector" id="sector" onChange="" style="width: 200px;">
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
                        <option value="<?php echo $curr_year-$predate;?>" <?php if(!empty($_POST['year']) && $_POST['year']==$curr_year-$predate){?>selected="selected"<?php }?>><?php echo $curr_year-$predate;?> A</option>
<?php endfor;?>
<option value="<?php echo $curr_year;?>" <?php if( !empty($_POST['year']) && $_POST['year']==$curr_year){?>selected="selected"<?php } ?>><?php echo $curr_year;?> YTD</option>
<option value="<?php echo $curr_year-1;?>-<?php echo $curr_year;?>" <?php if( !empty($_POST['year']) && $_POST['year']==($curr_year-1) . "-$curr_year" ){?>selected="selected"<?php }else {if (!isset($_POST['year'])) echo 'selected="selected"';} ?> ><?php echo $curr_year-1;?>-<?php echo $curr_year;?> YTD</option>
</select>
</td>
<td>and</td>
<td>
<?php
/************
sng:23/jul/2012
we cannot send condition like >=2 so we encode it
***************/
?>
<select name="deal_size" id="deal_size" style="width: 200px;">
<?php
/******************
sng:5/sep/2012
If I have specifically set the deal size to 'no filter' option, we should pre select the 'no filter' option and not the 'deals over 100m'
**************/
$selected = false;
if(isset($_POST['deal_size'])&&($_POST['deal_size']=="")){
$selected = true;
}
?>
<option value="" <?php if($selected){?>selected="selected"<?php }?>>Refine by Deal Size</option>
<?php for($j=0;$j<$g_view['deal_size_filter_list_count'];$j++):?>
<?php
/***************
sng:5/sep/2012
However, if I just visiting this page, there is no preselected deal size. In that case we preselect the 'more than 100m' option
******************/
$selected = false;
if(isset($_POST['deal_size'])&&($_POST['deal_size']==$g_view['deal_size_filter_list'][$j]['condition'])){
$selected = true;
}else{
if(!isset($_POST['deal_size'])&&($g_view['deal_size_filter_list'][$j]['condition'] == '>=0.100')){
$selected = true;
}
}
?>
<option value="<?php echo base64_encode($g_view['deal_size_filter_list'][$j]['condition']);?>" <?php if($selected){?>selected="selected"<?php }?> ><?php echo $g_view['deal_size_filter_list'][$j]['caption'];?></option>
<?php endfor; ?>
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
<td colspan="3" align="center" valign="top" style="text-align:right;">
<button onClick="return false;" id="generate"> Generate </button>
<?php
                if(!$g_account->is_site_member_logged()):?>
               <button onClick="window.location.href = 'login.php';return false;"> Login to view details </button> <?php /**** sng:5/apr/2013 we are hiding this feature <button onClick="window.location.href = 'login.php';return false;"> Login to download to PowerPoint </button>****/?>
              <?php else :?>
                <input type="submit" id="submit" value="Show details" /></span>
				
                <?php /*** sng:5/apr/2013 we are hiding this feature if ($savedSearches->searchBelongsToTheCurrentUser(base64_decode($_GET['token']))) : ?>
<button id="updateSearch" onClick="return updateLeagueSearch(<?php echo base64_decode($_GET['token'])?>);"> Update search </button>
<?php elseif($savedSearches->searchCanBeImported(base64_decode($_GET['token']))) : ?>
<button id="importSearch" onClick="return saveLeagueSearch();" > Import search </button>
<?php else : ?>
<button id="saveSearchButton" onClick="return saveLeagueSearch();" > Save search </button>
<?php endif ****/ ?>

<?php
/******************
sng:5/apr/2013
Let us hide download for now
<button class='orange' onClick="return showCustomizeForm(); "> <?php /**** sng:20/oct/2011 this is causing alignment issue in safari <span class="ui-icon ui-icon-newwin" style="float:left"></span>**** ?> Download to powerpoint </button>
*********************/
?>
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
	alert("initial display");return;
	$.post('ajax/league_table_creator.php?version=2',
		$('#leagueTableForm').serialize(),
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



<script language="javascript" type="text/javascript" src="js/excanvas.compiled.js"></script>


<script src="js/scripts.js" type="text/javascript" charset="utf-8"></script>
<div id="popupShare" style="height: 220px;">
<form action="#" method="get" id="formCustomizeDownload">
<a id="popupShareClose">x</a>
<h1>Download to powerpoint</h1>
<table width="600" border="0">
<tr>
<td>Title for your chart<br />
<input name="pptTitle" id="pptTitle" type="text" style="width:100%"/></td>
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
<input type="button" onClick="return do_getCustomizedChart();" value="download" class="btn_auto"> <br />
Note: you can double click on the chart and alter it in PowerPoint, after you have downloaded it.
</td>
</tr>
</table>
<br />
</form>
</div>

<div id="backgroundPopup"></div> 