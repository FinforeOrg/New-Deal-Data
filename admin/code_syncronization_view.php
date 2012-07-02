<style>
pre {
    font-size:12px;
}

</style>
<?php 
    if ($hasSync) {
    echo "<pre>$cmdOutput</pre>";   
        
    }
   $dealDataRevision = shell_exec("cat /var/www/home-checkout/home_revision");
   $svnTrunkInfo = shell_exec('svn info /var/www/home-checkout/trunk | grep "Revision:" | cut -d":" -f2');
   $changesInfoCmd =  sprintf('/usr/bin/svn log /var/www/home-checkout/trunk -r %d:%d --username mihai --password mihai --verbose', trim($dealDataRevision), trim($svnTrunkInfo));
   $changesInfo = shell_exec($changesInfoCmd);
?>
deal-data.com revision: <b><?php echo $dealDataRevision ?></b>  <br />
export.deal-data.com revision: <b><?php echo $svnTrunkInfo ?></b>  <br />
<?php 
    if (trim($dealDataRevision) != trim($svnTrunkInfo) ) { ?>
Changes that have not been ported to deal-data.com: <br />
<pre>
    <?php echo $changesInfo ?>
</pre>
<form method="post">
    <input type="hidden" name="action" value="sync" />
    <input type="hidden" name="rev" value="<?php echo trim($svnTrunkInfo)?>"/>
    <input type="submit" value="Syncronize now" name="submit">
</form>
<?php } else { ?>
    deal-data.com and export.deal-data.com are syncronized @r[<?php echo $svnTrunkInfo?>]
<?php } ?>