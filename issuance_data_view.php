<?php
require_once("league_table_filter_support_js.php");
?>
<?php
/*********************************
sng:25/nov/2010
*******/
?>
<script type="text/javascript">
function month_division_changed(){
	var month_div_obj = document.getElementById('month_division');
	var offset_selected = month_div_obj.selectedIndex;
	if(offset_selected != 0){
		var month_div_selected = month_div_obj.options[offset_selected].value;
		jQuery.post("ajax/month_division_list.php", {month_div: ""+month_div_selected+""}, function(data){
				if(data.length >0) {
					//alert(data);
					jQuery('#month_division_list').html(data);
				}
		});
	}
}
</script>
 
    <script src="js/scripts.js" type="text/javascript" charset="utf-8"></script>
    <link rel="stylesheet" href="css/savedSearches.css" type="text/css" media="screen" />


<script type="text/javascript">

function showCustomizeForm() {
    centerPopup();
    loadPopup();
}
function do_getCustomizedChart() {
    var url = 'chartgen/generate.php?type=volumes';
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

function  post_chart_data() {
    jQuery(document).ready(function() {     
            var myData = $('#issuance_table_filter').serialize()
            $.post(                  
                'ajax/issuance_table_creator.php?version=2',
                myData,
                function(returned) {
                    //console.log(returned);
                     $("#chart1").removeClass('loading');
                     $('#script').html(returned);
                     //
                }
            ); 
    });    
}
</script>
<script type="text/javascript">
function goto_login(){
	window.location.href = "index.php";
}
</script>
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
    $('#month_division_list').selectmenu();     
    $('#month_division').selectmenu().change(function(idx){
        $.post(
            'ajax/month_division_list.php',
            {'month_div' : $(this).selectmenu('value')},
            function(data) {
                $('#month_division_list').html(data).selectmenu();
            }
        )
        /*alert($(this).selectmenu('value') )*/
    });      
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
        var myData = $('#issuance_table_filter').serialize()
        $("#chart1").html('');
        $("#chart1").addClass('loading');
        $.post(                  
            'ajax/issuance_table_creator.php?version=2',
            myData,
            function(returned) {
                //console.log(returned);
                 $("#chart1").removeClass('loading');
                 $('#script').html(returned);
                 //
            }
        ); 
    });
    $('.loading').hide();
    $('#leftSide').show();
    $('#rightSide').show();

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

function generateCaption(){
	var caption = "Total ";
	var deal_cat_name = $('input:radio[name=deal_cat_name]:checked').val();
    if (deal_cat_name == undefined) {
         deal_cat_name = '';
    }
	caption = caption + deal_cat_name + " Issuance US $ billion";
	document.getElementById('chart_caption').innerHTML = caption;
    jQuery("#pptTitle").val(caption);
    //jQuery("#presentationTitle").val(caption);
}

function clear_error_msgs(){
	document.getElementById("err_deal_cat_name").innerHTML = "";
	/***********
	sng:25/nov/2010
	we now have month division adn month division starting. We need to check
	*********/
	document.getElementById("err_month_division").innerHTML = "";
	document.getElementById("err_month_division_list").innerHTML = "";
}
</script>
<?php
/****
sng:1/jun/2010
the links to top 5 banks and law firms will be on this page instead of league table
for bankers and lawyers. This is because the lawyers and banker
league tables page will be restricted to logged in member only

sng:23/mar/2011
Top firms now require login, so let us remove this

sng:10/oct/2011
Now we have separate page to show top 5 firms, so we can use the heading via content_view
which also allow to show the help button
*******/
?>
 <form method="post" id="issuance_table_filter" action="issuance_data_detail.php" onsubmit="">
 <table width="100%" border="0" cellspacing="5" cellpadding="5" class="registercontent">
    <tr>
        <th width="49%">Customize the issuance data</th>
        <th id="chart_caption"> Total Issuance US $ billion</th>
    </tr>
  <tr>
    <td>
        <div class="loading" style="height: 300px;" >
        </div>
        <table width="100%" border="0" cellspacing="6" cellpadding="0" id="leftSide" style="display: none;">
          <tr>
            <td width="100px;">Deal Category </td>
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
                <select name="month_division" id="month_division" onchange="return month_division_changed();" style="width: 200px;">
                    <option value="" <?php if($_POST['month_division']==""){?>selected="selected"<?php }?>>Select</option>
                    <option value="q" <?php if($_POST['month_division']=="q"){?>selected="selected"<?php }?>>Quarterly</option>
                    <option value="h" <?php if($_POST['month_division']=="h"){?>selected="selected"<?php }?>>Semi-Annual</option>
                    <option value="y" <?php if($_POST['month_division']=="y"){?>selected="selected"<?php }?>>Annual</option>
                </select>             
            </td>
            <td>start with</td>
            <td>  
                <select name="month_division_list" id="month_division_list" style="width: 200px;" >
                <?php
                for($j=0;$j<$g_view['month_div_cnt'];$j++){
                    ?>
                    <option value="<?php echo $g_view['month_div']['value_arr'][$j];?>" <?php if($_POST['month_division_list']==$g_view['month_div']['value_arr'][$j]){?>selected="selected"<?php }?>><?php echo $g_view['month_div']['label_arr'][$j];?></option>
                    <?php
                }
                ?>
                </select>          
            </td>
          </tr>
		  
          <tr>
		  <?php
		  /*******************************
		  sng:22/sep/2011
		  we do not use the year parameter for issuance data
			****************************************/
			?>
            <td colspan="3" style="text-align:center">  
                <select name="deal_size" id="deal_size" style="width: 200px;">
                <option value="">Refine by Deal Size</option>
                <?php for($j=0;$j<$g_view['deal_size_filter_list_count'];$j++):?>
                    <option value="<?php echo $g_view['deal_size_filter_list'][$j]['condition'];?>" <?php if($_POST['deal_size']==$g_view['deal_size_filter_list'][$j]['condition']){?>selected="selected"<?php } else { if ($g_view['deal_size_filter_list'][$j]['condition'] == '>=0.100') echo "selected='selected'"; }?> ><?php echo $g_view['deal_size_filter_list'][$j]['caption'];?></option>
                <?php  endfor; ?>
                </select>            
            </td>
          </tr>
		  <tr><td colspan="3" style="height:2px;"></td></tr>
           
          <tr>
            <td colspan="3" align="center" valign="top">
              <button onclick="return false;" id="generate"> Generate </button>
              <?php
                if(!$g_account->is_site_member_logged()):?>
                <button onclick="window.location.href = 'login.php';return false;"> Login to view details </button> <button onclick="window.location.href = 'login.php';return false;"> Login to download to PowerPoint </button>
              <?php else :?>
                <input type="submit" id="submit" value="Show details" />
                <?php if ($savedSearches->searchBelongsToTheCurrentUser(base64_decode($_GET['token']))) : ?>
                  <button id="updateSearch" onclick="return updateVolumesSearch(<?php echo base64_decode($_GET['token'])?>);"> Update search </button>
                <?php elseif($savedSearches->searchCanBeImported(base64_decode($_GET['token']))) : ?>
                  <button id="importSearch" onclick="return saveVolumesSearch();" > Import search </button> 
                <?php else : ?>                                                                                           
                  <button id="saveSearchButton" onclick="return saveVolumesSearch();" >  Save search </button>  
                <?php endif ?> 
                <button class='orange' onclick="return showCustomizeForm(); "> Download to powerpoint </button>              
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
                'ajax/issuance_table_creator.php?version=2',
                $('#issuance_table_filter').serialize(),
                function(returned) {
                    //console.log(returned);
                     $("#chart1").removeClass('loading');
                     $('#script').html(returned);
                     //
                }
            );
        });
        </script>     
    </div>
	<?php
	/***********
	sng:18/oct/2011
	if the charts have lots of data, they are getting squeezed. so, we need to adjust the width
	of the chart and scroll it horizontally if needed.
	That is why I created an outer div with fixed width
	*****************/
	?>
   <div style="margin-top:20px; margin-left:20px; width:500px; height:350px; float:left; position:relative;overflow:auto;">
        <div id="chart1" style="width:90%; height:320px;">
            <div class="loading" style="height: 300px;" >
            </div>       
        </div>
	</div>
    </td>
  </tr>
</table>
</form> 
<div id="explanation">
<p>Using the choices on the lefthand side of the page, you can choose the specific details for any type of volumes/ issuance chart. Then hit the "Generate" button to see the results on the righthand side.</p>
<p>If you click on the "View Details" button you can analyse the underlying figures for the volumes/ issuance analysis you request.</p>
</div>
<script type="text/javascript">
function saveVolumesSearch() {
	jQuery.ajax({
		url: "saved_searches.php?action=saveSearch&type=volumes",
		type: "POST",
		data: jQuery('#issuance_table_filter').serialize(),
		success: function(data){
			alert(data.message);
			if(data.newLocation != undefined ) {
                window.location.href = data.newLocation;
            }
		},
		dataType: "json"
	});
    return false;  
}

function updateVolumesSearch(id) {
	
	jQuery.ajax({
		url: "saved_searches.php?action=updateSearch&type=volumes&id="+id,
		type: "POST",
		data: jQuery('#issuance_table_filter').serialize(),
		success: function(data){
			alert(data.message);
			post_chart_data();
		},
		dataType: "json"
	});
    return false;
}
</script>


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
<?php
/**************
sng:10/oct/2011
We now have placed the jqplot js files in content_view
***************/
?>
<script src="js/scripts.js" type="text/javascript" charset="utf-8"></script>