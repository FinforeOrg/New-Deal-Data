<?php

?>

<style type="text/css">
<!--
#twitts ul {
    margin:0;
    padding:0;
}
#twitts li {
    cursor:pointer;
    display:block;
    float:left;
    list-style-type:none;
    margin:0;
    padding:0;
    width:100%;
    margin-bottom:10px;
  
}

.tweet-details {
    margin-left: 15px;
    color: #999;
    display: block;
    width: 80%;
    text-align: right;
}

.presReleaseText {
    font-size:16px;
    margin-bottom: 10px;

}

.presReleaseText a {
        color: #656565;
}
-->
</style>
<?php
/*********************
sng:29/sep/2011
we now include jquery in container view
 <script src="js/jquery-1.3.2.js" type="text/javascript" charset="utf-8"></script>
******************************/
?>
<script type="text/javascript">
$(document).ready(function(){ 
    $.get('ajax/twitter.php',{},
        function(response) {
            $("#tweets").html(response);
        }
    )
    
});
</script>
<div id="explanation">
<p>The following lists are deal announcements and related press releases from the leading banks and law firms.</p>
<p>Clicking on any of these links shall take you to the actual press release or news story, on the third party's website.</p>
</div>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td width="60%">
    <table cellspacing="2">
    <?php if (sizeOf($presReleases))
        foreach ($presReleases as $key=>$pr) : ?>
    <tr>
        <td>
            <div class="presReleaseText">
            <?php echo date('d M Y', strtotime($pr['date'])) ?>:
                <?php echo stripslashes($pr['text']) ?>
            </div>
            <div class="presReleaseTags">
             Tags : 
             <?php if (sizeOf($tags[$key])) {
                 $ntags = array();
                     foreach ($tags[$key] as $tag)
                        $ntags[] = "<a href='pr.php?tag=" . urlencode($tag) . "' title='See other press releases tagged $tag' > $tag </a>"; 
                    echo implode(", ",$ntags);                
             }?> 

            </div>
			<?php
			/*********
			sng:13/nov/2010
			If the deal_id is not blank, show the link to deal
			*********/
			if($pr['deal_id']!=0){
			?>
			<div>
			<a href="deal_detail.php?deal_id=<?php echo $pr['deal_id'];?>">More deal details</a>
			</div>
			<?php
			}
			/************************************************/
			?>

        </td>
    </tr>
    <?php endforeach ?>
    </table>
    </td>
    <td width="40%" align="right">
        <table width="100%" border="0" style="border:1px solid #1177AA;" id="twitts">
          <tr>
            <td style="background:none repeat scroll 0 0 #E86200; padding: 5px;font:11px/18px Tahoma,Geneva,sans-serif;color:#3B3B3B;font-weight: bold;">Deal News From Twitter</td>
          </tr>
          <tr>
            <td>
            <div id="tweets" style="position: absolute; width: 100%; height: 600px;"> <img src="images/ajax-loader.gif" style="margin-left: 135px;"></div>
           </td>
          </tr>
        </table>
    </td>
  </tr>
  <?php
  /*******
  sng:10/nov/2010
  pagination support
  ********/
  ?>
  <tr>
  <td style="text-align:right;">
  <?php echo $g_view['pagination'];?>
  </td>
  </tr>
</table>
