    <?php
	/*******************************
	sng:1/oct/2011
	we put these in container view
    <script src="js/jquery-ui-1.8.11.custom.min.js" type="text/javascript"></script>  
    <script src="js/jquery.ui.selectmenu.js" type="text/javascript"></script>
    <link rel="stylesheet" href="css/custom-theme/jquery-ui-1.8.9.custom.css" />
    <link rel="stylesheet" href="css/custom-theme/jquery-ui-1.8.9.custom_orange.css" />
    <link rel="stylesheet" href="css/custom-theme/jquery.ui.selectmenu.css" />
	*****************************************/
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
    
    $('#year').selectmenu();    
    $('#number_of_deals').selectmenu();    
    $('#deal_size').selectmenu();    
    $('#sector').selectmenu();
    $('button').button().click(function(event){event.preventDefault()});
    $('#filter').button().click(function(event){
        $('#tombstone_search_frm').submit();
        
    });    
    $('#download').button().click(function(event){
        $('#download_form').submit();
        
    });    

    $('#submit').button();
    $(".loading").hide();
    $("div.toHide").show();
    $('#tombstone_search_frm input[type="button"]').button();
    $('#tombstone_search_frm input[type="submit"]').button();    
});


</script>
<div class="loading" style="height: 200px; width: 100%;" >
</div>
<div class="toHide" style="display: none;">
    <form id="tombstone_search_frm" method="post" action="awards.php">
         <input type="hidden" name="myaction" value="filter" />
         <table width="100%" border="0" cellspacing="6" cellpadding="0" style="display:block" id="filters">
          <tr>                                                                   
            <td width="50%" >
                <table width="100%" border="0" cellspacing="6" cellpadding="0">
                  <tr>
                    <td width="70" align="left" valign="top">Deal Type:</td>
                    <td align="left" valign="top">&nbsp;</td>
                  </tr>
                  <tr>
                    <td colspan="2" align="left" valign="top">
                      <div class="radio_cat" style="font-size:10px;">
                        <input type="radio" id="deal_cat_name0" name="deal_cat_name" value="" <?php if($_POST['deal_cat_name']==''){?>checked<?php }?>/><label for="deal_cat_name0"> All Deals </label>

                        <?php
                            $i = 1;
                            foreach($categories as $categoryName) :?>   
                        <input type="radio" id="deal_cat_name<?php echo $i?>" name="deal_cat_name" value="<?php echo $categoryName?>" <?php if($_POST['deal_cat_name']==$categoryName){?>checked<?php }?>/><label for="deal_cat_name<?php echo $i?>"><?php echo $categoryName?></label>
                        <?php $i++;endforeach?>
                      </div>
                    </td>
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
                      <?php  foreach($regions as $region) :?>
                      <option value="<?php echo $region['id'];?>" <?php if($_POST['region']==$region['id'] || (!isset($_POST['region']) && $region['id'] == 0)){?>selected="selected"<?php }?>><?php echo $region['label'];?></option>
                      <?php endforeach; ?>
                    </select></td>
                    <td>and</td>
                    <td><select name="sector" id="sector" onChange="" style="width: 200px;">
                      <?php foreach($sectors as $sector):?>
                      <option value="<?php echo $sector['id'];?>" <?php if($_POST['sector'] === $sector['id']){?>selected="selected"<?php }?> ><?php echo $sector['label'];?></option>
                      <?php endforeach; ?>
                    </select></td>
                  </tr>
                </table>
            </td>
          </tr>
          <tr>
              <td colspan="2" style="text-align: right" >
                  <button id="filter"> Filter </button>
                  <?php if (sizeOf($awards)) :?>
                  <button id="download"> Download to PowerPoint </button>
                  <?php endif ?>
              </td>
          </tr>
         </table>
     </form>
<!---                     EndForm                  --->
</div>
<form style="display:none" action="awards.php?download=pptx" id="download_form" method="post">
    <input type="hidden" value="pptx" name="format" />
    <input type="hidden" value="<?php echo $g_view['page_heading']?>" name="title" />
</form>
<div id="awards">
    <?php if (sizeOf($awards)) :?>
    <?php foreach ($awards as $award) : ?>
    <div class="tombstone_display" style="height: 280px; float:left; margin-right: 10px; margin-bottom:10px;"> 
        <table width="100%" border="0">
          <tr>
            <td valign="middle" height="150px" style="vertical-align: middle; text-align:center">
                <img src="<?php echo $award['pic']?>" />
            </td>
          </tr>
          <tr>
            <td class="tombstone_deal"><?php echo $award['winner'] ?> <br /><?php echo $award['year'] ?> <br /></td>
          </tr>
        </table>
        
    </div>    
    <?php endforeach ?>
    <?php else :?>
    We are sorry. There are no awards for this company that match your criteria.
    <?php endif ?>
</div>


