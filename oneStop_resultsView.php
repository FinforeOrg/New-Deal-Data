<script>
    var _firstCheckBox = null;
    var _secondCheckBox = null;
    var _firstVolumeCheckBox = null;
    var _secondVolumeCheckBox = null; 
    
    function verifyChartCheckboxes(obj)
    {
        var checkedAlready = $('input[name="download_pptx_chart[]"]:checked').length;
        
        if (1 == checkedAlready) {
            _firstCheckBox = obj;
        }
        
        if (2 == checkedAlready) {
            _secondCheckBox = obj;
        }
        
        if (2 < checkedAlready) {
            $(_secondCheckBox).removeAttr('checked');
            _secondCheckBox = obj;
        }        
    }

    function verifyVolumeChartCheckboxes(obj)
    {
        var checkedAlready = $('input[name="download_pptx_volume_chart[]"]:checked').length;
        
        if (1 == checkedAlready) {
            _firstVolumeCheckBox = obj;
        }
        
        if (2 == checkedAlready) {
            _secondVolumeCheckBox = obj;
        }
        
        if (2 < checkedAlready) {
            $(_secondVolumeCheckBox).removeAttr('checked');
            _secondVolumeCheckBox = obj;
        }        
    }
   
   function download()
   {    if ( (1 > $('input[name="download_pptx_chart[]"]:checked').length &&  $('input[name="download_pptx_chart[]"]').length > 0)
             || (1 > $('input[name="download_pptx_volume_chart[]"]:checked').length && $('input[name="download_pptx_volume_chart[]"]').length > 0)
             || (1 > $('input[name="download_pptx_credential_slide[]"]:checked').length &&  $('input[name="download_pptx_credential_slide[]"]').length > 0)
             || (1 > $('input[name="download_pptx_top_ten[]"]:checked').length && $('input[name="download_pptx_top_ten[]"]').length > 0)
             || 1 > $('input[name="download_pptx_cross[]"]').length) 
        {
            alert('Please select at least one item in each section available for the PowerPoint download.')
            return false;
        }
       
        $('input[name="download_pptx_chart[]"]:checked').each(function(idx) {
            $('#downloadForm').append($(this).clone());
        })
        
        $('input[name="download_pptx_volume_chart[]"]:checked').each(function(idx) {
            $('#downloadForm').append($(this).clone());
        })  
        
        $('input[name="download_pptx_credential_slide[]"]:checked').each(function(idx) {
            $('#downloadForm').append($(this).clone());
        })
        
        $('input[name="download_pptx_top_ten[]"]:checked').each(function(idx) {
            $('#downloadForm').append($(this).clone());
        })   
        
        $('input[name="download_pptx_cross[]"]').each(function(idx) {
            $('#downloadForm').append($(this).clone());
        }) 
        
        $('#downloadForm').submit();
   }
   
    
</script>
 
<table cellspacing="0" cellpadding="0" border="0" width="100%"  class="zebraTable">
        <tbody>
            <tr>
                <th> Country</th>
                <th >Industry</th>
                <th >Deal</th>
                <th >Download</th>
            </tr>
                <tr>
                    <td><?php echo $infos['country'] ?></td>
                    <td><?php echo $infos['industry'] ?></td>
                    <td><?php echo $infos['deal'] ?></td>
                    <td width="170px"><input type="button" class="btn_auto" id="downloadButton" value="Download to PowerPoint&trade;" onclick="return download()" style="float:right"/></td>
                </tr>
        </tbody>
    </table>
 <br /> <br />
<h1> Section 1: League Table Position</h1>
<?php
//dump($fistTableResults);
 if (sizeof($fistTableResults['success'])) { ?>
<table cellspacing="0" cellpadding="0" border="0" width="100%"  class="zebraTable">
    <tbody>
        <tr>
            <th >Ranking</th>
            <th>Country</th>
            <th >Industry</th>
            <th >Deal</th>
            <th >Size</th>
            <th >Date</th>
            <th >Ranking Criteria</th>
            <th >Datapoint</th>
            <th >View</th>
            <th> <img src="/images/pptx.png" style="height: 20px" /></th>
        </tr>
    <?php foreach ($fistTableResults['success'] as $res) : ?>
        <tr>
            <td>#<?php echo $res['rank'] ?></td>
            <td><?php echo $res['deal_country']?></td>
            <td><?php echo $res['deal_industry']?></td>
            <td><?php echo $res['deal_subcat2_name']?></td>
            <td><?php echo $res['sizeR']?></td>
            <td><?php echo str_replace(date('y'), date('y') .'YTD', $res['date']); ?></td>
            <td><?php echo $res['rank_label']?></td>
            <td><?php echo $res[$res['rankingCritKey']] ?></td>
            <td> <form action="/index.php?from=oneStop" method="post" target="_blank" style="padding: 0;margin:0;"><input type="hidden" name="data" value="<?php echo $res['dataForPost']?>" /> <input type="submit" class="btn_auto" value="Chart"  /> </form></td>
            <td> <input type="checkbox" name="download_pptx_chart[]" onclick="verifyChartCheckboxes(this);" value="<?php echo $res['dataForPost']; ?>" /> 
        </tr>
    <?php endforeach ?>
    </tbody>
</table>
<?php } 

 if (sizeof($fistTableResults['failed'])) 
    foreach ($fistTableResults['failed'] as $res) : ?>
       <li> <?php echo $res?></li>
 <?php endforeach ?>
 
<?php  if (sizeof($fistTableResults['success'])) : ?>
<div style="text-align: center; width: 100%; font-size: 1.1em; margin-top: 10px; font-weight: bold;" >    
Need to improve your league table position?  Use our <a href="/make_me_top.php" target="_blank"> Make Me Top </a> algoritm.
</div>

<?php endif ?>
<br />
<br />
<h1> Section 2: Credentials / Tombstones </h1>
<?php if (sizeof($secondTableResults)) { ?>  
<table cellspacing="0" cellpadding="0" border="0" width="100%"  class="zebraTable">
    <tbody>
        <tr>
            <th>Country</th>
            <th >Industry</th>
            <th >Deal</th>
            <th >Number of tombstones</th>
            <th >View</th>
            <th> <img src="/images/pptx.png" style="height: 20px" /></th>
        </tr>
    <?php foreach ($secondTableResults as $res) : ?>
        <tr>
            <td><?php echo $res['country'] ?></td>
            <td><?php echo $res['industry']?></td>
            <td><?php echo $res['deal_cat_name']?></td>
            <td><?php echo $res['nrTombstones']?></td>
            <td> <form action="/showcase_firm.php?id=<?php echo $_SESSION['company_id']?>&from=savedSearches" method="post" target="_blank" style="padding: 0;margin:0;"><input type="hidden" name="data" value="<?php echo $res['dataForPost']?>" /> <input type="submit" class="btn_auto" value="View"  /> </form> </td>
            <td> <input type="radio" name="download_pptx_credential_slide[]" value="<?php echo $res['dataForPost']?>"/> 
        </tr>
    <?php endforeach ?>
    </tbody>
</table>
<?php } //if ?>
 <br /> <br />
<h1> Section 3: Volume charts </h1>
<?php 
//var_Dump($fistTableResults)?>
    <?php 
if (sizeOf($thirdTableResults)) {
    //echo "<pre> " . print_r($thirdTableResults,1 ) . "</pre>"; 
    foreach ($thirdTableResults as $key=>$result) {
        foreach ($result['data'] as $key2=>$data ) {
            $dt[$key]['data'][$data['short_name']] = $data['value'];
            $dt[$key]['info'] = $result['info'];  
            $dt[$key]['dataForPost'] = $result['dataForPost'];  
        }
    }
    //echo "<pre> " . print_r($dt,1 ) . "</pre>";
    foreach ($dt as $key=>$value) {
        $values = implode(', ', array_values($value['data']));
        $labels = array_keys($value['data']);
        $pointLabels =   $value['data'];
        foreach($pointLabels as $key2=>$pointLabel) {
                if ($pointLabel != 0)
                $pointLabels[$key2] =  "'$". $pointLabel."bn'";  
        }
        $pointLabels = implode(', ', array_values($pointLabels));
        $lbl = array();
        foreach($labels as $label) {
            $lbl[] = "'$label'";
        }
        $labels = implode(', ', $lbl );
        //$labels = implode(', ', array_keys($value['data']));  ?>
        <div id="chart<?php echo $key+1?>" style="margin-top:20px; margin-left:20px; width:47%; height:300px; float:left">
            <input type="checkbox" name="download_pptx_volume_chart[]" style="float: right; z-index: 9999; position: relative;" onclick="verifyVolumeChartCheckboxes(this);" value="<?php echo $value['dataForPost']?>"/>
        </div>
         <script class="code" type="text/javascript">
        $.jqplot.config.enablePlugins = true;
        $(document).ready(function() {
            
            line<?php echo $key+1?> = [<?php echo $values?>]; 
            plot<?php echo $key+1?> = $.jqplot('chart<?php echo $key+1?>', [line<?php echo $key+1?>], {
                title:'Volumes: <?php echo implode(', ', $value['info']) ?>',
                seriesDefaults: {
                    showMarker:false, 
                    pointLabels:{location:'n', ypadding:3, labels:[<?php echo $pointLabels?>]},
                    renderer:$.jqplot.BarRenderer,
                    color:'#7b7b7b'
                },
                grid: {
                    background: '#ffffff'
                },
                axes:{
                    xaxis:{
                        renderer:$.jqplot.CategoryAxisRenderer,
                        ticks:[<?php echo $labels?>],
                        tickOptions:{
                            showGridline:false
                        }                   
                    }, 
                    yaxis:{
                        min:0, 
                        tickOptions:{
                            showGridline:false
                        }
                    }
                },
                highlighter: {sizeAdjust: 7.5},
                cursor: {show: false}

            });  
        });
        </script>
        
<?php } ?> 
<div style="text-align: center; width: 100%; font-size: 1.1em; margin-top: 10px; font-weight: bold; clear: both;" >    
Need different variation of these Volumes' charts?  Use our <a href="/issuance_data.php" target="_blank"> Volumes </a> search function.
</div>
<?php } ?>


<div style="height: 20px;display: block; clear: both"  >&nbsp;</div>
<h1> Section 4: Top 10 Deals </h1>
<?php if (sizeof($fourthTableResults)) {?> 
<?php foreach ($fourthTableResults as $key => $table) { ?>
<h4>Top 10 deals: <?php echo $table['label'] ?> </h2> 
<?php     
    if (sizeof($table['data'])) {
    $i = 1;
    
?>                

    <table cellspacing="0" cellpadding="0" border="0" width="100%"  class="zebraTable">
        <tbody>
            <tr>
                <th>Rank</th>
                <th >Company</th>
                <th >Country</th>
                <th >Industry</th>
                <th >Deal Type</th>
                <th >Date</th>
                <th >Size $bn <input type="radio" name="download_pptx_top_ten[]" style="float:right" value="<?php echo $table['dataForPost']?>"/></th>
            </tr>
            
        <?php 
        if (sizeOf($table['data'])) {
            foreach ($table['data'] as $key2=>$res) : ?>
                <tr>
                    <td><?php echo $i ?></td>
                    <td><?php echo $res['company_name'] ?></td>
                    <td><?php echo $res['hq_country'] ?></td>
                    <td><?php echo $res['industry'] ?></td> 
                    <td><?php
                        if ($res['deal_cat_name'] == 'M&A')
                            echo $res['deal_subcat1_name'] . ' M&A';
                        else 
                           echo ($res['deal_subcat2_name'] == 'n/a') ? $res['deal_subcat1_name'] : $res['deal_subcat2_name']; 
                     
                     ?></td>
                    <td><?php echo date("M Y", strtotime($res['date_of_deal'])) ?></td>
                    <td>$<?php echo number_format($res['value_in_billion'],2); $i++?></td>

                </tr>
            <?php endforeach; } else echo "There are no deals in our database that match these criteria. ";?>
        </tbody>
    </table>
    <?php } else  echo "There are no deals in our database that match these criteria. ";
    if (sizeOf($table['data']) && sizeOf($table['data']) !=10) { ?> 
        The table above shows all the deals we have in our database that match these criteria.
    <?php }?>
<?php } ?> 
<div style="text-align: center; width: 100%; font-size: 1.1em; margin-top: 10px; font-weight: bold; clear: both;" >    
Need a different variation of these Top 10 tables?  Use our <a href="/deal.php" target="_blank"> Deals </a> search function.
</div>

<?php } else echo "There are no deals in our database that match these criteria. "; //if ?>    

<br />  
<h1> Section 5: Deals by competitors </h1>
<?php if (sizeof($fifthTableResults)) { ?> 

<table>
    <tr>
<?php foreach ($fifthTableResults as $key => $table) {

     $i = 1;
    
?> 
<td>                
   <h4>Top 3 dealmakers: <?php echo $table['label'] ?>  </h4>
    <table cellspacing="0" cellpadding="0" border="0" width="100%"  class="zebraTable">
        <tbody>
        <?php if (sizeOf($table['data'])) { ?> 
                <tr>
                    <th>Rank</th>
                    <th >Firm</th>
                    <th >Adjusted $bn</th>
                    <th >no of deals</th>
                </tr>
            <?php foreach ($table['data'] as $key=>$res) : ?>
                <tr>
                    <td><?php echo $i ?></td>
                    <td><?php echo $res['firm_name'] ?></td>
                    <td>$<?php echo number_format($res['total_adjusted_deal_value'],2) ?></td>
                    <td><?php 
                     $lbl = $res['num_deals'] .= ' deal';
                     $lbl .= $res['num_deals'] > 1 ? 's' : '';
                     echo $lbl  ?></td>
                </tr>
            <?php $i++; endforeach ?>
        <?php } else {   ?> 
                <tr>
                    <td align="center" valign="center" style="text-align: center;"> 
                       No deals match the following criteria : <?php echo $table['label'] ?>.
                    </td>
                </tr>
        <?php } ?> 
        </tbody>
    </table>
    <?php if (sizeOf($table['data']) && sizeOf($table['data']) < 3) { ?> 
        The table above shows all the deals we have in our database that match these criteria.
    <?php } ?>
   </td> 
<?php } ?> 
  </tr>
</table>
<div style="text-align: center; width: 100%; font-size: 1.1em; margin-top: 10px; font-weight: bold; clear: both;" >    
To see the detail of the deals that your competitors may talk about, use our <a href="/league_table_detail.php" target="_blank"> League Table Details </a> search function.
</div>
<?php } //if ?> 
<br />
<h1> Section 6: Cross Selling Your Firm </h1>    
<?php if (sizeOF($sixthTableResults)) : ?>
<table>
<tr>
<?php foreach ($sixthTableResults as $key=>$table) : ?>
    <td>
    <h4>Top 5 <?php echo $key ?> deals at your firm: <?php echo join(', ',$table['label']) ?> </h4>
    </td> 
<?php endforeach ?>
</tr>
<tr>
    <?php foreach ($sixthTableResults as $key=>$table) : ?>
        <td width="50%" valign="top">
            <?php if (sizeOf($table['data'])) : ?>
                <input type="hidden" name="download_pptx_cross[]" value="<?php echo $table['dataForPost']?>" />
                <table cellspacing="0" cellpadding="0" border="0" width="100%"  class="zebraTable">
                    <tr>
                        <?php if ($key !== 'M&A') : ?><th>Company</th><?php else :?> <th>Buyer</th><th>Target</th> <?php endif?>
                        <th >Date </th>
                        <th >Size $bn</th>
                        <th >Details</th>
                    </tr>
                    <?php foreach ($table['data'] as $data) : ?>
                    <tr>
                        <?php if ($key !== 'M&A') : ?><td><?php echo $data['company_name']?></td><?php else :?> <td><?php echo $data['company_name']?></td><td><?php echo $data['target_company_name']?></td> <?php endif?>
                        
                        <td><?php echo date('M Y', strtotime($data['date_of_deal']))?></td>
                        <td>$<?php echo number_format($data['value_in_billion'],2)?></td>
                        <td><a href="/deal_detail.php?deal_id=<?php echo $data['deal_id']?>" target="_blank"> View </a></td>
                        
                    </tr>                    
                    <?php endforeach ?>
                </table>
            <?php if (sizeOf($table['data']) && sizeOf($table['data']) < 5) { ?> 
                The table above shows all the deals we have in our database that match these criteria.
            <?php } ?>                
            <?php else : ?>
                  There are no deals in our database that match these criteria.
            <?php endif?>
        </td>
    <?php endforeach; ?>
</tr>
</table> 
<div style="text-align: center; width: 100%; font-size: 1.1em; margin-top: 10px; font-weight: bold; clear: both;" >    
To see more extensive details on your firms related deals, Use our <a href="/showcase_firm.php?id=<?php echo @$_SESSION['company_id']?>&from=savedSearches" target="_blank"> League Table Details </a> search function.
</div>   
<?php else : ?>
   There are no deals in our database that match these criteria. 
<?php endif ?> 

<form style="display:none" action="oneStop.php?action=download" id="downloadForm" method="POST">
    
</form>