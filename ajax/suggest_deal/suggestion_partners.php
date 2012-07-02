<?php
/*************
bank names and whether sellside advisor

["banks"]=> array(4) { 
[0]=> string(4) "Citi"  (bank 1)
[1]=> string(8) "JPMorgan" (bank 2 sellside)
[2]=> string(11) "BNP Paribas" (bank 3)
[3]=> string(13) "Credit Suisse" (bank 4 sellside)
} 
["sellside_advisors_2"]=> string(2) "on" 
["sellside_advisors_4"]=> string(2) "on"

["law_firms"]=> array(4) { 
[0]=> string(20) "Mello Jones & Martin" (firm 1)
[1]=> string(0) "" (firm 2)
[2]=> string(0) "" (firm 3)
[3]=> string(11) "Bredin Prat" (firm 4 sellside)
} 
["law_sellside_advisors_4"]=> string(2) "on"

NO LONGER USED
*******************/
return;
if(isset($_POST['banks'])){
	$bank_count = count($_POST['banks']);
	for($b=0;$b<$bank_count;$b++){
		$partner_name = $_POST['banks'][$b];
		//name can be blank so
		if($partner_name != ""){
			//check if sellside advisor, default n
			//for 0th item, the key name is sellside_advisors_1 etc. It may not be there
			$key = "sellside_advisors_".($b+1);
			if(isset($_POST[$key])&&($_POST[$key]=="on")){
				$is_sellside_advisor = 'y';
			}else{
				$is_sellside_advisor = 'n';
			}
			$suggestion_partner_ins_q.=",('".$suggestion_id."','".$partner_name."','".$is_sellside_advisor."','bank')";
		}
	}
}
//law firm names and whether sellside advisor
if(isset($_POST['law_firms'])){
	$law_firm_count = count($_POST['law_firms']);
	for($b=0;$b<$law_firm_count;$b++){
		$partner_name = $_POST['law_firms'][$b];
		//name can be blank so
		if($partner_name != ""){
			//check if sellside advisor, default n
			//for 0th item, the key name is law_sellside_advisors_1 etc. It may not be there
			$key = "law_sellside_advisors_".($b+1);
			if(isset($_POST[$key])&&($_POST[$key]=="on")){
				$is_sellside_advisor = 'y';
			}else{
				$is_sellside_advisor = 'n';
			}
			$suggestion_partner_ins_q.=",('".$suggestion_id."','".$partner_name."','".$is_sellside_advisor."','law firm')";
		}
	}
}


//if data, remove the first ,
if($suggestion_partner_ins_q!=""){
	$suggestion_partner_ins_q = substr($suggestion_partner_ins_q,1);
}
$suggestion_partner_ins_q = "insert into ".TP."transaction_suggestions_partners  (suggestion_id,partner_name,is_sellside_advisor,partner_type) values ".$suggestion_partner_ins_q;
?>