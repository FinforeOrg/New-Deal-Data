<h3> <?php echo  $g_view['msg'] ?></h3>

  <table width="100%" border="0">
  <?php if (sizeOf($accounts)) : ?>
    <?php foreach ($accounts as $account) : ?>
    <tr>
      <td><?php echo $account['twitter_id'] ?></td>
      <td>
      <a href="<?php echo $g_http_path;?>/admin/pr.php?action=manageTweeterAccounts&subaction=delete&id=<?php echo $account['id']?>"> Delete </a>
      <a href="<?php echo $g_http_path;?>/admin/pr.php?action=manageTweeterAccounts&subaction=<?php echo $account['active'] ? 'disable' : 'enable' ;?>&id=<?php echo $account['id']?>"> <?php echo $account['active'] ? 'Disable' : 'Enable' ;?> </a>
      </td>
    </tr>
    <?php endforeach ?>    
  <?php endif ?>

  </table>