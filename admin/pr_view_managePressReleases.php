<?php
/*****
sng:13/nov/2010
putting all actions to relative urls
**/
?>
<form action="pr.php?action=managePressReleases" method="post" >
<table cellspacing="0" cellpadding="5" border="1" width="100%" style="border-collapse: collapse;">
<tr style="border: none;">
    <td>
        Filter <input type="text" name="query" style="width: 100%;"/>
    </td>
    <td>
        <input type="submit" value="Filter" name="submit"/>
    </td>
</tr>
<tr bgcolor="#dec5b3" style="height: 20px;">
    <td>
        Press release text
    </td>
    <td>
        Actions
    </td>
</tr>
<?php if (sizeOf($pressReleases)) 
        foreach ($pressReleases as $pressRelease) : ?>
          <tr>
            <td width="75%"><?php echo stripslashes($pressRelease['text'])?></td>
            <td width="25%">
            <a href="pr.php?action=managePressReleases&subaction=delete&id=<?php echo $pressRelease['id']?>" >Delete </a>  |  
    <a href="pr.php?action=editPressRelease&id=<?php echo $pressRelease['id']?>" >Edit </a>
            </td>
          </tr>
        <?php endforeach ?>
</table>


</form>