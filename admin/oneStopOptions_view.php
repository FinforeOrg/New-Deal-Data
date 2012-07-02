<form action="oneStopOptions.php" method="post" enctype="application/x-www-form-urlencoded" name="options" target="_self">
    <table width="100%" border="0" cellspacing="3" cellpadding="3">
      <tr>
        <td colspan="2"><h2>Table 1 : League table position</h1></td>
      </tr>
      <tr>
        <td>Size</td>
        <td>
            <select name="deal_size" id="deal_size">
                <option value="">All deal sizes</option>
                <?php
                    for($j=0;$j<$g_view['deal_size_filter_list_count'];$j++){
                    ?>
                    <option value="<?php echo $g_view['deal_size_filter_list'][$j]['condition'];?>" <?php if($_POST['deal_size']==$g_view['deal_size_filter_list'][$j]['condition']){?>selected="selected"<?php }?> ><?php echo $g_view['deal_size_filter_list'][$j]['caption'];?></option>
                    <?php
                }
                ?>
            </select>    
        </td>
      </tr>
      <tr>
        <td>Date 1</td>
        <td>
            <select name="year1" id="year1">
            <option value="" selected="selected">Any</option>
            <?php
                $curr_year = date("Y");
                for($predate=2;$predate>0;$predate--){
                    ?>
                    <option value="<?php echo $curr_year-$predate;?>" <?php if($_POST['year1']==$curr_year-$predate){?>selected="selected"<?php }?>><?php echo $curr_year-$predate;?> A</option>
                    <?php
                }
                ?>
                <option value="<?php echo $curr_year;?>" <?php if($_POST['year1']==$curr_year){?>selected="selected"<?php }?>><?php echo $curr_year;?> YTD</option>
                <option value="<?php echo $curr_year-1;?>-<?php echo $curr_year;?>" <?php if($_POST['year1']==($curr_year-1)."-".$curr_year){?>selected="selected"<?php }?>><?php echo $curr_year-1;?>-<?php echo $curr_year;?> YTD</option>
            </select>
        </td>
      </tr>
      <tr>
        <td>Date 2</td>
        <td>
            <select name="year2" id="year2">
            <option value="" selected="selected">Any</option>
            <?php
                $curr_year = date("Y");
                for($predate=2;$predate>0;$predate--){
                    ?>
                    <option value="<?php echo $curr_year-$predate;?>" <?php if($_POST['year2']==$curr_year-$predate){?>selected="selected"<?php }?>><?php echo $curr_year-$predate;?> A</option>
                    <?php
                }
                ?>
                <option value="<?php echo $curr_year;?>" <?php if($_POST['year2']==$curr_year){?>selected="selected"<?php }?>><?php echo $curr_year;?> YTD</option>
                <option value="<?php echo $curr_year-1;?>-<?php echo $curr_year;?>" <?php if($_POST['year2']==($curr_year-1)."-".$curr_year){?>selected="selected"<?php }?>><?php echo $curr_year-1;?>-<?php echo $curr_year;?> YTD</option>
            </select>    
        </td>
      </tr>
      <tr>
        <td colspan="2"><h2>Table 4 : Top 10 deals</h1></td>
      </tr>      
      <tr>
        <td>Date</td>
        <td>
            <select name="year3" id="year3">
            <option value="" selected="selected">Any</option>
            <?php
                $curr_year = date("Y");
                for($predate=2;$predate>0;$predate--){
                    ?>
                    <option value="<?php echo $curr_year-$predate;?>" <?php if($_POST['year3']==$curr_year-$predate){?>selected="selected"<?php }?>><?php echo $curr_year-$predate;?> A</option>
                    <?php
                }
                ?>
                <option value="<?php echo $curr_year;?>" <?php if($_POST['year3']==$curr_year){?>selected="selected"<?php }?>><?php echo $curr_year;?> YTD</option>
                <option value="<?php echo $curr_year-1;?>-<?php echo $curr_year;?>" <?php if($_POST['year3']==($curr_year-1)."-".$curr_year){?>selected="selected"<?php }?>><?php echo $curr_year-1;?>-<?php echo $curr_year;?> YTD</option>
            </select>
        </td>
      </tr>
       <tr>
        <td colspan="2"><h2>Table 5 : Deals by Competitors</h1></td>
      </tr>      
      <tr>
        <td>Date</td>
        <td>
            <select name="year4" id="year4">
            <option value="" selected="selected">Any</option>
            <?php
                $curr_year = date("Y");
                for($predate=2;$predate>0;$predate--){
                    ?>
                    <option value="<?php echo $curr_year-$predate;?>" <?php if($_POST['year4']==$curr_year-$predate){?>selected="selected"<?php }?>><?php echo $curr_year-$predate;?> A</option>
                    <?php
                }
                ?>
                <option value="<?php echo $curr_year;?>" <?php if($_POST['year4']==$curr_year){?>selected="selected"<?php }?>><?php echo $curr_year;?> YTD</option>
                <option value="<?php echo $curr_year-1;?>-<?php echo $curr_year;?>" <?php if($_POST['year4']==($curr_year-1)."-".$curr_year){?>selected="selected"<?php }?>><?php echo $curr_year-1;?>-<?php echo $curr_year;?> YTD</option>
            </select>
        </td>
      </tr>
       <tr>
        <td colspan="2"><h2>Table 6 : Cross Selling Your Firm</h1></td>
      </tr>      
      <tr>
        <td>Date</td>
        <td>
            <select name="year5" id="year5">
            <option value="" selected="selected">Any</option>
            <?php
                $curr_year = date("Y");
                for($predate=2;$predate>0;$predate--){
                    ?>
                    <option value="<?php echo $curr_year-$predate;?>" <?php if($_POST['year5']==$curr_year-$predate){?>selected="selected"<?php }?>><?php echo $curr_year-$predate;?> A</option>
                    <?php
                }
                ?>
                <option value="<?php echo $curr_year;?>" <?php if($_POST['year5']==$curr_year){?>selected="selected"<?php }?>><?php echo $curr_year;?> YTD</option>
                <option value="<?php echo $curr_year-1;?>-<?php echo $curr_year;?>" <?php if($_POST['year5']==($curr_year-1)."-".$curr_year){?>selected="selected"<?php }?>><?php echo $curr_year-1;?>-<?php echo $curr_year;?> YTD</option>
            </select>
        </td>
      </tr>
      <tr>
        <td colspan="2"  align="right"> 
            <span style="font-size: 15px; text-align: left;">
            <?php
                echo $g_view['msg'];
            ?>
            </span>
            <input type="submit" value="Save" name="submit" /> 
        </td>
      </tr>
    </table>
</form>
