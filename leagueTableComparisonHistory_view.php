<style type="text/css">
    .positive {
        background-color: #0C0;
    }
    .negative {
        background-color: #FF8080;
    }
</style>
<?php if (isset($notifications) && sizeOf($notifications)) : ?>
    <table width="100%" cellspacing="0" cellpadding="0" class="company">
        <tbody>
            <tr>
                <th>Date sent</th>
                <th>Search parameters</th>
                <th>Ranks</th>
                <th>Details</th>
            </tr>
            <?php foreach($notifications as $notificationId => $notification) : ?>
            <tr>
                <td><?php echo $notification['end_date']?></td>
                <td><?php echo $notification['parameters']?></td>
                <td class="<?php echo $notification['class']?>"><?php echo sprintf('%+d (From %s to %s)', $notification['places'], $notification['old_rank'], $notification['new_rank'])?></td>
                <td><a class="link_as_button" href="leagueTableComparisonHistory.php?token=<?php echo base64_encode($notificationId)?>" target="_self"> View </a></td>
            </tr>
            <?php endforeach ?>
        </tbody>
    </table> 
<?php else: ?>
<h3> There are no notifications sent. You either have not enabled "Rank Change" alerts or your ranking has not changed. </h3>. 
<?php endif; ?>
