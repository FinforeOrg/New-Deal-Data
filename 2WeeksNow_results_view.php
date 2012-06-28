<?php
//var_dump($results, $infos, $_POST);
?>
<table cellspacing="0" cellpadding="0" border="0" width="100%"  class="zebraTable">
    <tbody>
        <tr>
            <th> Country</th>
            <th> Industry</th>
            <th> Deal</th>
        </tr>
            <tr>
                <td><?php echo $countryArr['countryName'] ?></td>
                <td><?php echo $industryArr['industry'] ?></td>
                <td><?php 
                    unset($dealArr['id']);
                    if ($dealArr['subtype2'] == 'n/a') unset($dealArr['subtype2']);
                    echo join(' > ', $dealArr);
                ?></td>
            </tr>
    </tbody>
</table>
<br /> <br />

<h3> <?php echo $results['data1']['label1']?> (<?php echo $results['data1']['label2']?>)</h3>
<?php
    if (sizeof($results['data1']['entries'])) { ?>
    <table cellspacing="0" cellpadding="0" border="0" width="100%"  class="zebraTable">
        <tbody>
            <tr>
                <th> Company</th>
                <th> Size </th>
                <th> Date </th>
                <th> Link </th>
            </tr>
        <?php 
            foreach ($results['data1']['entries'] as $entry) { ?>
            <tr>
                <td><?php echo $entry['company_name'] ?></td>
                <td><?php echo $mobileApp->formatSize($entry['value_in_billion']) ?></td>
                <td><?php echo $mobileApp->formatDate($entry['date_of_deal']) ?></td>
                <td><a href='deal_detail.php?deal_id=<?php echo $entry['deal_id']?>&submit=Detail' target='_blank'> Details </a></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
     <br /> <br /> 
<?php } else { ?>
     We are sorry. There are no records that match your criteria.
<?php }?>
<h3> <?php echo $results['data2']['label1']?> (<?php echo $results['data2']['label2']?>)</h3>
<?php
    if (sizeof($results['data2']['entries'])) { ?>
    <table cellspacing="0" cellpadding="0" border="0" width="100%"  class="zebraTable">
        <tbody>
            <tr>
                <th> Company</th>
                <th> Size </th>
                <th> Date </th>
                <th> Link </th>
            </tr>
        <?php 
            foreach ($results['data2']['entries'] as $entry) { ?>
            <tr>
                <td><?php echo $entry['company_name'] ?></td>
                <td><?php echo $mobileApp->formatSize($entry['value_in_billion']) ?></td>
                <td><?php echo $mobileApp->formatDate($entry['date_of_deal']) ?></td>
                <td><a href='deal_detail.php?deal_id=<?php echo $entry['deal_id']?>&submit=Detail' target='_blank'> Details </a></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
     <br /> <br /> 
<?php } else { ?>
     We are sorry. There are no records that match your criteria.
<?php }?>
     <h3> 3. Newsfeed </h3>
     <?php if (sizeOf($results['news'])) 
         foreach ($results['news'] as $story) {?>
        <h4> <a href="<?php echo $story['link']?>" target="_blank"> <?php echo $story['title']?></a> </h4>
        <span> <?php echo $story['content'] ?> </span>
     <?php } ?>