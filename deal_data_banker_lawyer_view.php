<!--deal data, bankers lawyers-->

			<tr><td><?php echo $g_view['deal_data']['company_name'];?> (<?php echo $g_view['deal_data']['hq_country'];?>, <?php echo $g_view['deal_data']['industry'];?>)</td></tr>
			<tr><td><?php echo show_deal_type_data($g_view['deal_data'])?></td></tr>
			
			<tr><td>
			<?php
			/**
			sng:10/jul/2010
			if deal value is 0, it is not disclosed
			***/
			if($g_view['deal_data']['value_in_billion']==0){
				?>
				value not disclosed
				<?php
			}else{
				?>
				$<?php echo convert_billion_to_million_for_display($g_view['deal_data']['value_in_billion']);?>m
				<?php
			}
			?>
			<?php
			/****
			sng:08/oct/2010
			if M&A deal and is pending, then show announced on
			****/
			$closing_txt = "closed on";
			if(($g_view['deal_data']['deal_cat_name']=="M&A")&&($g_view['deal_data']['deal_subcat1_name']!="Completed")){
				$closing_txt = "announced on";
			}
			?>
			, <?php echo $closing_txt;?> <?php echo date("jS M Y",strtotime($g_view['deal_data']['date_of_deal']));?></td></tr>
			<tr><td>Base fee: <?php if($g_view['deal_data']['base_fee']!=0) echo $g_view['deal_data']['base_fee']."%"; else echo "n/a";?></td></tr>
			<tr><td>Incentive fee: <?php if($g_view['deal_data']['incentive_fee']!=0) echo $g_view['deal_data']['incentive_fee']."%"; else echo "n/a";?></td></tr>
			<?php
			if(strtolower($g_view['deal_data']['deal_cat_name'])=="debt"){
				?>
				<tr><td><?php echo show_coupon_data($g_view['deal_data'])?></td></tr>
				<?php
			}
			?>
			<?php
			if(strtolower($g_view['deal_data']['deal_cat_name'])=="equity"){
				if(strtolower($g_view['deal_data']['deal_subcat1_name'])=="equity"){
					if(strtolower($g_view['deal_data']['deal_subcat2_name'])=="ipo"){
						?>
						<tr><td>1 day price change: <?php if($g_view['deal_data']['1_day_price_change']!="") echo $g_view['deal_data']['1_day_price_change']."%";else echo "n/a";?></td></tr>
						<?php
					}
					if(strtolower($g_view['deal_data']['deal_subcat2_name'])=="additional"){
						?>
						<tr><td>Discount to last: <?php if($g_view['deal_data']['discount_to_last']!="") echo $g_view['deal_data']['discount_to_last']."%";else echo "n/a";?></td></tr>
						<?php
					}
					if(strtolower($g_view['deal_data']['deal_subcat2_name'])=="rights issue"){
						?>
						<tr><td>Discount to TERP: <?php if($g_view['deal_data']['discount_to_terp']!="") echo $g_view['deal_data']['discount_to_terp']."%";else echo "n/a";?></td></tr>
						<?php
					}
				}
				/******************************************************
				sng:11/nov/2010
				for equity convertible, equity preferred, show coupon
				***/
				if(strtolower($g_view['deal_data']['deal_subcat1_name'])=="convertible"){
					?>
					<tr><td><?php echo show_coupon_data($g_view['deal_data'])?></td></tr>
					<?php
				}
				if(strtolower($g_view['deal_data']['deal_subcat1_name'])=="preferred"){
					?>
					<tr><td><?php echo show_coupon_data($g_view['deal_data'])?></td></tr>
					<?php
				}
				/********************************************/
			}
			if(strtolower($g_view['deal_data']['deal_cat_name'])=="m&a"){
				?>
				<tr><td>EV/EBITDA LTM: <?php if($g_view['deal_data']['ev_ebitda_ltm']!=0) echo $g_view['deal_data']['ev_ebitda_ltm'];else echo "n/a";?></td>
				<tr><td>EV/EBITDA +1yr: <?php if($g_view['deal_data']['ev_ebitda_1yr']!=0) echo $g_view['deal_data']['ev_ebitda_1yr'];else echo "n/a";?></td>
				<tr><td>30 days premia: <?php if($g_view['deal_data']['30_days_premia']!=0) echo $g_view['deal_data']['30_days_premia']."%";else echo "n/a";?></td>
				<?php
			}
			?>
			<tr>
			<td>
			<?php
			$bank_count = count($g_view['deal_data']['banks']);
			if($bank_count == 0){
				?>
				No banks listed for this transaction
				<?php
			}else{
				?>
				<?php echo $bank_count;?> bank(s) involved. Deal credit of $<?php echo $g_view['deal_data']['banks'][0]['adjusted_value_in_billion']*1000;?>m each.
				<?php
			}
			?>
			</td>
			</tr>
			
			<?php ////////////////////////////////////////////////// ?>
			<tr>
			<td>
			<?php
			$law_count = count($g_view['deal_data']['law_firms']);
			if($law_count == 0){
				?>
				No law firms listed for this transaction
				<?php
			}else{
				?>
				<?php echo $law_count;?> law firm(s) involved. Deal credit of $<?php echo $g_view['deal_data']['law_firms'][0]['adjusted_value_in_billion']*1000;?>m each.
				<?php
			}
			?>
			</td>
			</tr>
			
			
			
<!--deal data, bankers lawyers-->