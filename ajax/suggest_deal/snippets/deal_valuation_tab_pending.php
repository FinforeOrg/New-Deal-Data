<?php
require_once(dirname(dirname(dirname(dirname(__FILE__)))) . "/include/global.php");
require_once("classes/db.php");
$db = new db();
?>
                            <table width="100%" border="0" cellspacing="0" cellpadding="4">
                              <tr>
                                <td>
                                    <table width="100%" border="0" cellspacing="0" cellpadding="4">
                                      <tr>
                                        <td width="48%">Implied Equity Value (in Local Currency Millions):</td>
                                        <td>Acquisition of What Percentage (%):</td>
                                      </tr>
                                      <tr>
                                        <td width="48%"><input name="implied_equity_value" type="text" class="std special" id="implied_equity_value"></td>
                                        <td><input name="aquisition_percentage" type="text" class="std special" id="aquisition_percentage"></td>
                                      </tr>
									  
									  <tr>
                                        <td>Any dividend payment on top of equity purchase:</td>
                                      </tr>
                                      <tr>
                                        <td><input name="divident_payment" type="text" class="std special" id="divident_payment"></td>
                                      </tr>
									  <?php
									  /*******************
									  sng:3/may/2012
									  some new fields
									  *********************/
									  ?>
									  <tr>
                                        <td width="48%">Total Debt (in Local Currency Millions):</td>
                                        <td>Cash (in Local Currency Millions):</td>
                                      </tr>
                                      <tr>
                                        <td width="48%"><input name="total_debt_million_local_currency" type="text" class="std special" id="total_debt_million_local_currency"></td>
                                        <td><input name="cash_million_local_currency" type="text" class="std special" id="cash_million_local_currency"></td>
                                      </tr>
									  <tr>
									  	<td width="48%">Adjustments (in Local Currency Millions):</td>
										<td width="48%">Net Debt (in Local Currency Millions):</td>
									  </tr>
									  <tr>
									  	<td width="48%"><input name="adjustments_million_local_currency" type="text" class="std special" id="adjustments_million_local_currency"></td>
										<td width="48%"><input name="net_debt" type="text" class="std special" id="net_debt"></td>
									  </tr>
									  
                                      
									  
                                      <tr>
                                        <td width="48%">Enterprise Value  (in Local Currency Millions):</td>
                                        <td>Implied Deal Size  (in Local Currency Millions):</td>
                                      </tr>
                                      <tr>
                                        <td width="48%"><input name="enterprise_value" type="text" class="std special" id="enterprise_value"></td>
                                        <td><input name="implied_deal_size_local" type="text" class="std special" id="implied_deal_size_local"></td>
                                      </tr>
                                      <tr>
                                        <td width="48%">
                                            Transaction type: <br />
                                            <div id="transaction_type_check">
                                              <input type="radio" name="transaction_type" value="cash" id="transaction_type_0"><label for="transaction_type_0" onclick="toggleEquityPercentage(this,false)">Cash</label>
                                              <input type="radio" name="transaction_type" value="equity" id="transaction_type_1"><label for="transaction_type_1" onclick="toggleEquityPercentage(this, false)">Equity</label>
                                              <input type="radio" name="transaction_type" value="part_cash_part_quity" id="transaction_type_2"> <label for="transaction_type_2" onclick="toggleEquityPercentage(this, true)">Part Cash/ part Equity</label>               
                                            </div>
                                            <br />
											
                                             <div id="hostile_or_friendly">
											 <?php
											 /**********
											 sng:8/jun/2011
											 We need these options from takeover_type_master will send the id
											 *************/
											 $takeover_q = "select * from ".TP."takeover_type_master where is_active='y'";
											 $success = $db->select_query($takeover_q);
											 if($success){
											 	$takeover_q_row_count = $db->row_count();
												if($takeover_q_row_count > 0){
													$takeover_q_result = $db->get_result_set_as_array();
													for($t = 0;$t<$takeover_q_row_count;$t++){
														?>
														<input type="radio" name="friendly_or_hostile" value="<?php echo $takeover_q_result[$t]['takeover_id'];?>" id="hostile_or_friendly<?php echo $t+1;?>"><label for="hostile_or_friendly<?php echo $t+1;?>" onclick="togleButton(this);"><?php echo $takeover_q_result[$t]['takeover_name'];?></label>
														<?php
													}
												}
											 }
											 ?>
											 <!--
                                              <input type="radio" name="friendly_or_hostile" value="">Friendly</label>
                                              <input type="radio" name="friendly_or_hostile" value="">Hostile</label>
											  -->
              
                                            </div>
                                        </td>
                                        <td>
                                            &nbsp;<br />
                                            <input type="text" name="equity_percentage" class="std special" id="equity_percentage" value="" /></td>
                                      </tr>
                                      <tr>
                                        <td>&nbsp;</td>
                                        <td></td>
                                      </tr>
                                    </table>
                                </td>
                              </tr>
                            </table>
                        <table width="100%" border="0" cellspacing="0" cellpadding="4">
                          <tr>
                            <td width="48%">Local Currency:</td>
                            <td>Implied Deal Size (in USD Millions):</td>
                          </tr>
                          <tr>
                            <td><input type="text" name="local_currency" id="local_currency" class="std special"></td>
                            <td><input type="text" name="implied_deal_size" id="implied_deal_size" class="std special"></td>
                          </tr>
                          <tr>
                            <td>Local Currency per 1 USD:</td>
                            <td>Implied Enterprise Value (in USD Millions)</td>
                          </tr>
                          <tr>
                            <td><input type="text" name="local_currency_rate" id="local_currency_rate" class="std special"></td>
                            <td><input type="text" name="implied_enterprise_value" id="implied_enterprise_value" class="std special"></td>
                          </tr>
                          <tr>
                            <td colspan="2">&nbsp;</td>
                          </tr>
						  <!--/////////////////////////////////////////////////
  sng:21/july/2011
  When this button is clicked, a check mark is placed. However, when it is clicked again, the check mark remains (but the
  server does not get the value, which is correct since I have unchecked it
  We have created a function to deal with this.
  Instead of togleButton(), we use toggle_single_button() and pass both the element and the id;
  /////////////////////////////////////////////////////////-->
                          <tr>
                            <td colspan="2">
                                <div style="line-height: 24px;">
                                    <input type="checkbox" class="button_checkbox" name="publicly_listed" id="publicly_listed" /> <label for="publicly_listed" style="height: 24px;" onclick="toggle_single_button(this,'publicly_listed')">Target is publicly listed on a stock exchange.</label> 
                                </div>    
                            </td>
                          </tr>
                        </table>
                        <table id="publicly_listed_details" style="display: none;">
							<tr>
                            <td>Name of the stock exchange:</td>
                            <td>&nbsp;</td>
                          </tr>
						  <tr>
                            <td><input type="text" name="target_stock_exchange_name" id="target_stock_exchange_name" class="std special" value=""></td>
                            <td>&nbsp;</td>
                          </tr>
                          <tr>
                            <td>Deal price per share:</td>
                            <td>Local Currency of Share Price (if different):</td>
                          </tr>
                          <tr>
                            <td><input type="text" name="deal_price_per_share" id="deal_price_per_share" class="std special" value=""></td>
                            <td><input type="text" name="local_currency_of_share_price" id="local_currency_of_share_price" class="std special"></td>
                          </tr>
                          <tr>
                            <td>Share price prior to announcement:</td>
                            <td>Date of share price, prior to announcement:</td>
                          </tr>
                          <tr>
                            <td><input type="text" name="share_price_prior_to_announcement" id="share_price_prior_to_announcement" class="std special"></td>
                            <td><input class="date" type="text" name="date_of_share_price_prior_to_announce" id="date_of_share_price_prior_to_announce"></td>
                          </tr>
                          <tr>
                            <td>Implied Premium:</td>
                            <td>Total shares outstanding (million)</td>
                          </tr>
                          <tr>
                            <td><input type="text" name="implied_premium" id="implied_premium" class="std special"></td>
                            <td><input type="text" name="total_shares_outstanding" id="total_shares_outstanding" class="std special"></td>
                          </tr>
                        </table>
						 <!--/////////////////////////
						  sng:12/mar/2012
						  we will only use a single 'note' textarea
                        <table>
                           <tr>
                            <td colspan="2" style="padding:10px;">
                                <div style="margin: 10px 60px;">
                                  Enter additional text deal value: <br>
                                  <textarea name="addition_text_on_deal_value" style="width: 100%; height: 150px"></textarea>
                                </div>
                            </td>
                          </tr>
                        </table>
						///////////////////////////-->

<script language="text/javascript">
	reinitialize();
	$('#transaction_type_check').buttonset();
	$('#hostile_or_friendly').buttonset();
	$('.button_checkbox').button({});
</script>

<script>
	$('#publicly_listed').button().click(function(){
		$('#publicly_listed_details').toggle();
	});
</script>

<script>
$( "#local_currency" ).autocomplete({
	source: "ajax/suggest_a_deal_search_currency.php",
	minLength: 1,
	select: function( event, ui ) {


	}
}).data( "autocomplete" )._renderItem = function( ul, item ) {
	return $( "<li></li>" )
	.data( "item.autocomplete", item )
	.append( "<a>" + item.label + " - "+item.name+" </a>" )
	.appendTo( ul );
};


$( "#target_stock_exchange_name" ).autocomplete({
	source: "ajax/sugest_a_deal_search_stock_exchange.php",
	minLength: 3,
	select: function( event, ui ) {


	}
}).data( "autocomplete" )._renderItem = function( ul, item ) {
	return $( "<li></li>" )
	.data( "item.autocomplete", item )
	.append( "<a>" + item.label + " - "+item.name+" </a>" )
	.appendTo( ul );
};
</script>
