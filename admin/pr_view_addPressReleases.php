<h3> <?php echo  $g_view['msg'] ?></h3>
<script src="../ckeditor/ckeditor.js" type="text/javascript"></script>
<script src="../js/jquery-1.3.2.js" type="text/javascript"></script>
<script src="../js/jquery-ui-1.8.4.custom.datepicker.min.js" type="text/javascript"></script>
    <script type="text/javascript">
    $(function() {
        $("#date").datepicker({ dateFormat: 'dd MM yy' });
    });
    </script>
<link rel="stylesheet" href="../css/ui-lightness/jquery-ui-1.8.4.custom.datepicker.css" type="text/css" media="all" />
<script type="text/javascript">
window.onload = function(){  
    CKEDITOR.editorConfig = function( config )
    {
        config.toolbar = 'CustomToolbar';

        config.toolbar_CustomToolbar =
        [
            ['NewPage','Preview'],
            ['Cut','Copy','Paste','PasteText','PasteFromWord','-','Scayt'],
            ['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
            ['Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak'],
            '/',
            ['Styles','Format'],
            ['Bold','Italic','Strike'],
            ['NumberedList','BulletedList','-','Outdent','Indent','Blockquote'],
            ['Link','Unlink','Anchor'],
            ['Maximize','-','About']
        ];
    };
  
    CKEDITOR.basepath="../ckeditor/";
    CKEDITOR.width="400";
    CKEDITOR.height="400";
    CKEDITOR.replace('presReleaseText',
       {
        toolbar :
        [
            ['Styles', 'Format'],
            ['Bold', 'Italic', '-', 'NumberedList', 'BulletedList', '-', 'Link','Unlink', '-', 'Image']
        ]
    } 
    );
    
}
</script>
<?php
/****
sng:13/nov/2010
put relative url here
****/
?>
<form id="addPressReleases" name="addPressReleases" method="post" action="pr.php?action=addPressReleases">
<table width="600" border="0">
  <tr>
    <td>Press release text<br />
        <textarea name="presReleaseText" cols="25" rows="9" style="width:100%"></textarea>
    </td>
  </tr>
  <tr>
    <td>Tags (separated by comma)
    <br />    
      <input type="text" name="tags" id="tags" style="width:100%"/>
    </td>
  </tr>
    <tr>
    <td>Date 
    <br />    
      <input type="text" name="date" id="date"/>
    </td>
  </tr>
  <?php
  /*****
  sng:13/nov/2010
  allow admin to enter a deal number, if this press release talks about a deal and the db has that deal data
  admin search from the front end and go to the deal detail page to get the deal number
  ***/
  ?>
  <tr>
    <td>Deal number 
    <br />    
      <input type="text" name="deal_id" id="deal_id"/>
    </td>
  </tr>
  <tr>
    <td align="right">
    
      <label>
        <input type="submit" name="submit" id="submit" value="Save press release" />
      </label>
    </td>
  </tr>
</table>
</form>
