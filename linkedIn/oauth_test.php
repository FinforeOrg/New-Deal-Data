<?php
session_start();
error_reporting(E_ALL);
ini_set('error_reporting',1);
require_once("OAuth.php");  

 

$domain = "https://api.linkedin.com/uas/oauth";
$sig_method = new OAuthSignatureMethod_HMAC_SHA1();

 
$callback = "http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']."?action=getaccesstoken";
$test_consumer = new OAuthConsumer("pinUcPjmkFrKivGp4bJBmlMu6DbDT1J_S3551lptxPGcsoWhw_-rDJMUgpTd-y9M", "4jsPV_cRlCJECNIsLncRGl_Hi7bR1Qmewmn8F4hHiZTnNYLMmPS_IL1LpVw2wVoI", $callback);



# First time through, get a request token from LinkedIn.
if (!isset($_GET['action'])) {

        $req_req = OAuthRequest::from_consumer_and_token($test_consumer, NULL, "POST", $domain . "/requestToken");
        $req_req->set_parameter("oauth_callback", $callback); # part of OAuth 1.0a - callback now in requestToken
        $req_req->sign_request($sig_method, $test_consumer, NULL);
		
        $ch = curl_init();
		// make sure we submit this as a post
		curl_setopt($ch, CURLOPT_POSTFIELDS, ''); //New Line
		
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER,array (
                $req_req->to_header()
        ));
        curl_setopt($ch, CURLOPT_URL, $domain . "/requestToken");
        curl_setopt($ch, CURLOPT_POST, 1);
        $output = curl_exec($ch);
        curl_close($ch);

		//print_r($req_req);  //<---- add this line

     	//print("$output\n");  //<---- add this line
        //die();
        
        parse_str($output, $oauth);

        # pop these in the session for now - there's probably a more secure way of doing this! We'll need them when the callback is called.

        $_SESSION['oauth_token'] = $oauth['oauth_token'];
        $_SESSION['oauth_token_secret'] = $oauth['oauth_token_secret'];

 

        # Redirect the user to the authentication/authorisation page. This will authorise the token in LinkedIn
        Header('Location: ' . $domain . '/authorize?oauth_token=' . $oauth['oauth_token']);
		#print 'Location: ' . $domain . '/authorize?oauth_token=' . $oauth['oauth_token']; // <---- add this line
 

} else {
        # this is called when the callback is invoked. At this stage, the user has authorised the token.
        # Now use this token to get a real session token!

 		//print "oauth_token = [[".$_REQUEST['oauth_token']."]]\n";echo "<br/><br/>";
		
        $req_token = new OAuthConsumer($_REQUEST['oauth_token'], $_SESSION['oauth_token_secret'], 1);
        $acc_req = OAuthRequest::from_consumer_and_token($test_consumer, $req_token, "POST", $domain . '/accessToken');
        $acc_req->set_parameter("oauth_verifier", $_REQUEST['oauth_verifier']);  # need the verifier too!
        $acc_req->sign_request($sig_method, $test_consumer, $req_token);

        $ch = curl_init();
		curl_setopt($ch, CURLOPT_POSTFIELDS, ''); //New Line
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER,array (
                $acc_req->to_header()
        ));
        curl_setopt($ch, CURLOPT_URL, $domain . "/accessToken");
        curl_setopt($ch, CURLOPT_POST, 1);
        $output = curl_exec($ch);
		if(curl_errno($ch)){
			echo 'Curl error 1: ' . curl_error($ch);
		}
        curl_close($ch);
        parse_str($output, $oauth);
		
		
        $_SESSION['oauth_token'] = $oauth['oauth_token'];
        $_SESSION['oauth_token_secret'] = $oauth['oauth_token_secret'];
        # Now you have a session token and secret. Store these for future use. When the token fails, repeat the above process.
        $endpoint = "http://api.linkedin.com/v1/people/~:(id,first-name,last-name,headline,educations,industry,location,three-current-positions)";

		$req_token = new OAuthConsumer($oauth['oauth_token'],$oauth['oauth_token_secret'], 1);
    	$profile_req = OAuthRequest::from_consumer_and_token($test_consumer,$req_token, "GET", $endpoint, array());
        $profile_req->sign_request($sig_method, $test_consumer, $req_token);

        $ch = curl_init();
		
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER,array (
                $profile_req->to_header()
        ));
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        $output = curl_exec($ch);
		
        
		if(curl_errno($ch)){
			echo 'Curl error 2: ' . curl_error($ch);
		}
		curl_close($ch);
        ;
        
        
        require_once(dirname(__FILE__)."/Xml2Array.php");
        $converter = new Xml2Array();
        $converter->setXml($output);
        $xml_array = $converter->get_array();
        $person = $xml_array['person'];
        //print_r($person);
        $userDetails = array (
            "first-name" => $person['first-name']['#text'],
            "last-name" => $person['last-name']['#text'],
            "country" => $person['location']['name']['#text'] 
        );
        
        if (is_array($person['three-current-positions'])) {
            if ($person['three-current-positions']['@total'] > 0)  {
                $userDetails['lastWorkplace'] = array("title" => $person['three-current-positions']['position']['title']['#text'],
                                                      "company" => $person['three-current-positions']['position']['company']['name']['#text'],
                                                      "start-date" =>  $person['three-current-positions']['position']['start-date']['year']['#text']
                ); 
            }
        }
        
        $data = base64_encode(serialize($userDetails));
        header("Location: http://deal-data.com/register.php?from=linkedIn&token=$data");
   		

        
}
