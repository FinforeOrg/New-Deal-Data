<?php
    //var_dump(unserialize(base64_decode($_GET['token'])));
    //var_dump($_POST);
    //var_dump($_SESSION['company_id'], $firstTableData);
?>
<style type="text/css">
    .company td {
        height: 36px;
    }
    
    .selected {
        background-color: #CCC
    }
    
    .jqplot-yaxis {
        display: none;
    }    
</style>

<h2> <?php echo $g_view['message'] ?> </h2>
<h2> <?php if (isset($searchDetails)) echo $searchDetails; ?> </h2>
<?php if ($errors) exit(); ?>
<table width="100%" border="0">
  <tr>
    <td>
        <h3>League Table as in <?php echo $startDate ?>  </h3>
        <?php
        echo $g_view['firstCaption'];
        if (is_array($firstTableData) && sizeOf($firstTableData)) : ?>
        <table width="100%" cellspacing="0" cellpadding="0" class="company">
            <tbody>
                <tr>
                    <th>Rank</th>
                    <th>Firm</th>
                    <th>Tombstone #</th>
                    <th>Tombstone $billion</th>
                    <th>Adjusted $billion</th>
                </tr>
                <?php foreach($firstTableData as $rank => $info) : ?>
                <tr <?php if ($_SESSION['company_id'] == $info['partner_id']) echo 'class="selected"'?>>
                    <td><?php echo $rank+1?></td>
                    <td><?php echo $info['firm_name']?></td>
                    <td><?php echo $info['num_deals']?></td>
                    <td><?php echo number_format($info['total_deal_value'],2)?></td>
                    <td><?php echo number_format($info['total_adjusted_deal_value'],2)?></td>
                </tr>
                <?php endforeach ?>
            </tbody>
        </table> 

        <?php endif ?>        
    </td>
    <td width="15px">&nbsp; </td>
    <td>
        <h3>League Table as in <?php echo $endDate ?> </h3>
        <?php
        if (is_array($secondTableData) && sizeOf($secondTableData)) : ?>
        <table width="100%" cellspacing="0" cellpadding="0" class="company">
            <tbody>
                <tr>
                    <th>Rank</th>
                    <th>Firm</th>
                    <th>Tombstone #</th>
                    <th>Tombstone $billion</th>
                    <th>Adjusted $billion</th>
                </tr>
                <?php foreach($secondTableData as $rank => $info) : ?>
                <tr <?php if ($_SESSION['company_id'] == $info['partner_id']) echo 'class="selected"'?>>
                    <td><?php echo $rank+1?></td>
                    <td><?php echo $info['firm_name']?></td>
                    <td><?php echo $info['num_deals']?></td>
                    <td><?php echo number_format($info['total_deal_value'],2)?></td>
                    <td><?php echo number_format($info['total_adjusted_deal_value'],2)?></td>
                </tr>
                <?php endforeach ?>
            </tbody>
        </table> 

        <?php endif ?>        
    </td>
  </tr>
</table>

<table width="100%" border="0">
  <tr>
    <td>
        <h3> League Table Chart as in <?php echo $startDate ?> </h3>
        <form id="chart1form">
            <?php foreach ($_POST as $key => $value) :?>
                <?php if (!in_array($key, array('last_alert_date_max', 'max_date', 'last_alert_date_max', 'myaction'))) :?>
                    <input type="hidden" name="<?php echo $key?>" value="<?php echo $value?>" />
                <?php endif ?>
            <?php endforeach ?>
            <input type="hidden" name="max_date" value="<?php echo $startDate?>" />                    
        </form>
        <div id="chart1" style="margin-top:20px; margin-left:20px; width:90%; height:300px; float:left; position:relative;">
            <div class="loading" style="height: 300px;" >
            </div>       
        </div>        
    </td>
    <td>
        <h3> League Table Chart as in <?php echo $endDate ?> </h3>
        <form id="chart2form">
            <?php foreach ($_POST as $key => $value) :?>
                <?php if (!in_array($key, array('last_alert_date_max', 'max_date', 'last_alert_date_max', 'myaction'))) :?>
					<?php
					/***********
					sng:23/jul/2012
					we need to send deal size condition via ajax post, but sanitizer will remove it. So we encode it
					**************/
					if($key == 'deal_size'){
						$value = base64_encode($value);
					}
					?>
                    <input type="hidden" name="<?php echo $key?>" value="<?php echo $value?>" />
                <?php endif ?>
            <?php endforeach ?>
            <input type="hidden" name="max_date" value="<?php echo $endDate?>" />
        </form>         
        <div id="chart2" style="margin-top:20px; margin-left:20px; width:90%; height:300px; float:left; position:relative;">
            <div class="loading" style="height: 300px;" >
            </div>       
        </div>        
    </td>
  </tr>
</table>

<div id="script"> 
    <script type="text/javascript" class="code">
        $(document).ready(function() {
            $.post(
                'ajax/league_table_creator.php?version=2&chartName=chart1',
                $('#chart1form').serialize(),
                function(returned) {
                    $("#chart1 .loading").hide();
                    $('#script').html(returned);
                }
            );
            $.post(
                'ajax/league_table_creator.php?version=2&chartName=chart2',
                $('#chart2form').serialize(),
                function(returned) {
                    $("#chart2 div.loading").hide();
                    $('#script').html(returned);
                }
            )                
        });
    </script>     
</div>


<h3> Deals that have been added between <?php echo $startDate . '-' . $endDate ?></h3>
<?php 
if (is_array($dealsAdded) && sizeof($dealsAdded)) :?>
<table width="100%" cellspacing="0" cellpadding="0" class="company">
    <tbody>
        <tr>
            <th style="width:150px;">Participant</th>
            <th style="width:60px;">Date</th>
            <th>Type</th>
            <th><!--Value (in million USD)-->Size</th>
            <th style="width:170px;">Bank(s)</th>
            <th style="width:170px;">Law Firm(s)</th>
            <th style="width:170px;">&nbsp;</th>
        </tr>
        <?php foreach ($dealsAdded as $deal) : ?>
        <tr>
            <td style="width:150px;">
			<?php
			/***************************
			sng:5/dec/2012
			We now have multiple companies per deal
			<a href="company.php?show_company_id=<?php echo $deal['company_id'];?>" target="_blank"><?php echo $deal['company_name'];?></a>
			******************************/
			echo Util::deal_participants_to_csv_with_links($deal['participants']);
			?>
			</td>
            <td style="width:60px;"><?php echo $deal['date_of_deal'];?></td>
            <td>
            <?php
            echo $deal['deal_cat_name'];
			/************
			sng:5/dec/2012
			Now we have concept of participants. We no longer use target company field so we have removed 'Acquisition of' for M&A deals
			*****************/
            
            ?>            
            </td>
            <td style="width:60px;">
            <?php
			/************
			sng: 5/dec/2012
			We now have fuzzy value for deal, so we use utility function
			echo (0 == $deal['value_in_billion']) ? 'not disclosed' : convert_billion_to_million_for_display_round($deal['value_in_billion']);
			***************/
            echo convert_deal_value_for_display_round($deal['value_in_billion'],$deal['value_range_id'],$deal['fuzzy_value']);
            ?>                
            </td>
            <td style="width:170px;">
            <?php
                $banks = array();
                foreach ($deal['banks'] as $bank) {
                    $banks[] = $bank['name'];
                }
                echo join(', ', $banks);
            ?>                
            </td>
            <td style="width:170px;">
                <?php
                    $lawFirms = array();
                    foreach ($deal['law_firms'] as $lawFirm) {
                        $lawFirms[] = $lawFirm['name'];
                    }
                    echo join(', ', $lawFirms);
                ?>                   
            </td>
            <td>
                <a class="link_as_button" href="deal_detail.php?deal_id=<?php echo $deal['deal_id']?>&submit=Detail" target="_blank"> Details </a>
            </td>
        </tr>  
        <?php endforeach ?>
    </tbody>
</table>
<?php endif; ?>    