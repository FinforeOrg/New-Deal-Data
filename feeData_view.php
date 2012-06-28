<?php

/**
* feeData_view.php file
*
* $Id:$
*
* $Rev:  $
*
* $LastChangedBy:  $
*
* $LastChangedDate: $
*
* @author Ionut MIHAI <ionut_mihai25@yahoo.com>
* @copyright 2011 Ionut MIHAI
*/
?>
<link rel="stylesheet" href="css/ss_style.css" type="text/css" media="screen" />
<script>
   
function displayChartPlaceholders(nbPlaceHolders, prepend) {
    if (nbPlaceHolders == 0) {return false;};
    for (var i = 1; i <= nbPlaceHolders; i++) {
        if (prepend == true) {
            $('#chartResultArea').prepend('<div class="loading" style="height: 300px; width: 700px; float:left"></div>');
        } else {
            $('#chartResultArea').append('<div class="loading" style="height: 300px; width: 700px; float:left"></div>');
        }
        
    }
}

var _requestNb = 0;
var _nbPlaceHolders = 1;
function doMySubmit()
{
    if ($('#region').val() == -2 && $('#country').val() == -2) {
        alert('The parameter combination you chosen is not valid. Please select at least one Region/Country');
        return false;
    } 
    _requestNb = 0;
    $('#featuredCharts').remove();
    $('#chartResultArea').html('');
    $('#chartResultArea').show();
    loadMoreCharts('&first=y');

    return false;
}

function selectUnselectAll()
{
     $('input[name="download_pptx_fee_chart[]"]').each(function(idx) {
            $(this).attr('checked', !this.checked);
     })
     
     $('#downloadForm').append($(this).clone());
}

function download()
{
    if ($('input[name="download_pptx_fee_chart[]"]:checked').length < 1) {
        alert('Please select at least one chart to add to your download');
        return false;
    }
    $('#downloadForm').html('');
    $('input[name="download_pptx_fee_chart[]"]:checked').each(function(idx) {
        $('#downloadForm').append($(this).clone());
    })
    $('#downloadForm').append($('input[name="type"]:checked').clone())
    $('#downloadForm').append($('select[name="region"]').clone())
    $('#downloadForm').append($('select[name="country"]').clone())
    $('#downloadForm').append($('select[name="datapoint_filter"]').clone())

    $('#filterForm input').each(function(idx) {
        $('#downloadForm').append($(this).clone());
    });    
    
    $('#downloadForm').submit();
    return false;   
}

function loadMoreCharts(extraParam, prepend)
{
    if (undefined == extraParam) {extraParam = '';}
    if (undefined == prepend) {prepend = false;}
    displayChartPlaceholders(_nbPlaceHolders, prepend);
    _requestNb++;
    $.post('feeData.php?getNexFromMultiPage=y&getData=y' + extraParam + '&page=' + (_requestNb), $('#filterForm').serialize(), function(response){
        $('.loading').remove();
        
        chartsLeft = $(response).find('#nbChartsAvailableNext').val();
        if (chartsLeft > 0) {
            $('#loadMoreBtn').show();
        } else {
            $('#loadMoreBtn').hide();
        }
        //idToScrollTo = $(response).first().attr('id');
        $('select#customChartsFilter').html('<option value="">  Load custom charts  </option>');
        

        try {
            var customChartsAvailable = unescape($(response).find('#customChartsRemaining').val());
            customChartsAvailable = eval('(' + customChartsAvailable + ')');
            if (customChartsAvailable.length) {
                $(customChartsAvailable).each(function(idx){
                   customChart =  customChartsAvailable[idx];
                    //console.log(customChart);
                    $('select#customChartsFilter').append('<option value="' + customChart + '"> ' + customChart + "</option>")
                });
            }
        } catch (e) {
            //
        }   
        
        if (chartsLeft>0) {
            $('#customChartsFilter-button').show();
            $('select#customChartsFilter').selectmenu();
        } else {
            $('#customChartsFilter-button').hide();
        }

        if (prepend == true) {
            $('#chartResultArea').prepend(response);
        } else {
            $('#chartResultArea').append(response);
        }

    })

    return false;
}

function loadCustomCharts(cat) {
    loadMoreCharts('&cat=' + escape(cat), true);
}
</script>
<form action="" method="POST" id="filterForm">
    <table>
        <tr>
            <td>
                <div class="radio" style="font-size:10px;">
                    <?php foreach ($categories as $j => $cat) : ?>
                        <input type="radio" id="type<?php echo $j?>" name="type" value="<?php echo $cat?>" <?php if($_POST['type']==$cat){?>checked<?php }; if (!isset($_POST['type']) && 'M&A' == $cat) echo 'checked'; ?>/><label for="type<?php echo $j?>"><?php echo $cat?></label>
                    <?php endforeach;?>

                </div>            
            </td>
            <td>
                <table style="float:right; width: 460px">
                    <tr>
                        <td style="width: 210px">
                            <select name="region" id="region" style="font-size: 10px; width: 200px;" class="selectmenu">
                                <option value="-2" <?php if (!isset($_POST['region'])) echo 'selected="selected"'?>> Any Region </option>
                                <?php foreach($regions as $value=>$label) :?>
                                <option value="<?php echo $value?>" <?php if (isset($_POST['region']) && $_POST['region'] ==  $value) echo 'selected="selected"'?>> <?php echo $label?> </option>
                                <?php endforeach ?>
                            </select>                        
                        </td>
                        <td style="width: 15px;">
                            or
                        </td>
                        <td style="width: 210px">
                            <select name="country" id="country" style="font-size: 10px; width: 200px;" class="selectmenu">
                                <option value="-2"> Any Country </option>
                                <?php foreach($countries as $value=>$label) :?>
                                <option value="<?php echo $value?>" <?php if (isset($_POST['country']) && $_POST['country'] ==  $value) echo 'selected="selected"'?>> <?php echo $label?> </option>
                                    <?php endforeach ?>
                            </select>  
                            <br />
                            <br />
                            <input type="hidden" name="datapoint_filter" value="3" />
                            <!--
                            <select name="datapoint_filter" style="font-size: 10px; width: 200px; margin-top: 10px;" class="selectmenu">
                                <option value=""> Show only charts with: </option>
                                <option value="3" <?php if (isset($_POST['datapoint_filter']) && $_POST['datapoint_filter'] == '3' || !isset($_POST['datapoint_filter'])) echo 'selected="selected"' ?>> more than 3 data-points </option>
                                <option value="4" <?php if (isset($_POST['datapoint_filter']) && $_POST['datapoint_filter'] == '4') echo 'selected="selected"' ?>> more than 4 data-points </option>
                                <option value="5" <?php if (isset($_POST['datapoint_filter']) && $_POST['datapoint_filter'] == '5') echo 'selected="selected"' ?>> more than 5 data-points </option>
                                <option value="6" <?php if (isset($_POST['datapoint_filter']) && $_POST['datapoint_filter'] == '6') echo 'selected="selected"' ?>> more than 6 data-points </option>
                            </select>
                            -->
                            <input type="hidden" name="multipage" value="y" />
            
                        </td>
                        
                    </tr>
                    <tr>
                        <td   colspan='3'>
                            <select name="loadCustomCharts" id="customChartsFilter" style="display: none; float: left; width: 435px;">
                                <option value="" > Load custom charts </option>
                            </select>                             
                        </td>
                      </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <div style="float:right">
                    <button class="btn_auto" value="filter" onclick='doMySubmit(); return false;' > Filter </button>
                    <button class="btn_auto" value="download" onclick="return download();"> Download to PowerPoint&trade;</button> 
                    <input type="checkbox" onclick="selectUnselectAll()" title="Select all/Inverse selection"/>
                </div>
            </td>
        </tr>
    </table>
</form>

<div id="chartResultArea" style="display: none"> 
    <h1> Chart Search Results </h1>
</div>
<br style="clear:both"/>

<button class="btn_auto" style="float: left; width: 800px; display:none;" onclick="return loadMoreCharts();" id="loadMoreBtn"> Load More Charts </button>
<script>
$(function() {
    $(".radio" ).buttonset();
    $('select.selectmenu').selectmenu();
    $('#customChartsFilter').change(function() {
        loadCustomCharts($(this).val()); 
        return false;
    });    
});
</script>

<?php if (!strlen($charts)) : ?>
    <div style="position: relative" id="nocharts"> 
    <script type="text/javascript">
        $(document).ready(function() { 
            $('#nocharts').html(
            '<div style="width: 302px; height: 45px; position: absolute; left: 50px; top: 30px;" class="ui-widget-shadow ui-corner-all"></div></div> <div class="ui-widget ui-widget-content ui-corner-all" style="position: absolute; width: 280px; height: 20px; left: 50px; top: 30px; padding: 10px;"> No transactions matching your request were found. </div>'
            );
        });
    </script>
    </div>
<?php else : ?>
    <div id="featuredCharts">
        <h1> Featured charts </h1>
        <?php if ('' != $message) : ?>
        <h3> <?php echo $message ?> </h3>
        <?php endif?>
        <?php echo $charts ?>
    </div>
<?php endif?>
<form action="feeData.php?action=download" method="POST" id="downloadForm" style="display:none" target="download_pptx">
</form>
