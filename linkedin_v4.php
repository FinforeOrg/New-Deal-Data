<?php

ini_set("display_errors",1);
error_reporting(E_ALL);
/**
 * LinkedIn PHP class to communicate with the LinkedIn API
 * Relies on Pecl OAuth: http://pecl.php.net/package/oauth
 *
 * Usage example:
 * 
 * $apiKey		= '<YourApiKey>';
 * $secretKey	= '<YourApiSecret>';
 * $callback	= '<YourCallbackUrl>';
 * $linkedin	= new Linkedin($apiKey,$secretKey,$callback);
 * $profile		= $linkedin->getData();
 *
 * @copyright     Copyright 2009, Jeroen Sentel
 * @version       0.1
 * @lastmodified  2009-11-30
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 * 
 * LinkedIn API Documentation from developer.linkedin.com
 * Profile API:					http://developer.linkedin.com/docs/DOC-1002
 * Field Selectors:				http://developer.linkedin.com/docs/DOC-1014
 * Profile Fields:				http://developer.linkedin.com/docs/DOC-1061
 * Post Network Update:			http://developer.linkedin.com/docs/DOC-1009
 * Messaging:					http://developer.linkedin.com/docs/DOC-1044
 * Comments Network Updates:	http://developer.linkedin.com/docs/DOC-1043
 * Get Network Updates:			http://developer.linkedin.com/docs/DOC-1006
 * Invitation API:				http://developer.linkedin.com/docs/DOC-1012
 * Connections API:				http://developer.linkedin.com/docs/DOC-1004
 * Status Update API:			http://developer.linkedin.com/docs/DOC-1007
 * Search API:					http://developer.linkedin.com/docs/DOC-1005
 * Industry Codes:				http://developer.linkedin.com/docs/DOC-1011
 * 
 */

class Linkedin {
		
	var $apiUrl				= 'https://api.linkedin.com';
	var $requestTokenPath	= '/uas/oauth/requestToken';
	var $authorizePath		= '/uas/oauth/authorize?oauth_token=';
	var $accessTokenPath	= '/uas/oauth/accessToken';
	var $callback			= '';
			
	/**
	 * Create $this->oauth object (pecl oauth)
	 * uncomment $this->oauth->disableSSLChecks() if you encounter CA Certificates errors
	 * @param api_key
	 * @param api_secret
	 * @todo check if connection succeeded or failed and return true or false
	 */
	function __construct($api_key, $api_secret, $callback = '') {
				
		$this->oauth = new OAuth($api_key, $api_secret);		
		$this->oauth->disableSSLChecks();
		$this->oauth->disableDebug();
				
		if(!empty($callback)) {
			$this->callback = $callback;
		}
		
		$this->generateIndustryCodes();
		
	}
	
	/**
	 * Authorize the user against LinkedIn.
	 * Get Request and Access tokens if none are present in the Session.
	 * @return true or false.
	 */
	function authorize() {
		
		// Check if not authorized already
		if(empty($_SESSION['oauth']['linkedin']['authorized'])) {
		
			// Get the request tokens if none exist
			if(empty($_SESSION['oauth']['linkedin']['request'])) {
				try {
					$request = $this->oauth->getRequestToken($this->apiUrl.$this->requestTokenPath, $this->callback);
					$_SESSION['oauth']['linkedin']['request'] = $request;
					header('location:'.$this->apiUrl.$this->authorizePath.$request['oauth_token']);
				} catch(Exception $e) {
					echo '<pre>Error: getRequestToken. Please try again.</pre>';
					return false;
				}
			}
			
			// Get the access tokens if none exist
			if(empty($_SESSION['oauth']['linkedin']['access'])) {
				$request = $_SESSION['oauth']['linkedin']['request'];
				$this->oauth->setToken($request['oauth_token'], $request['oauth_token_secret']);
				try {
					$access = $this->oauth->getAccessToken($this->apiUrl.$this->accessTokenPath);
					$_SESSION['oauth']['linkedin']['access'] = $access;
				} catch(Exception $e) {
					echo '<pre>Error: getAccessToken. Please try again.</pre>';
					return false;
				}
			}
		
		}
		
		// Set the access tokens
		$access = $_SESSION['oauth']['linkedin']['access'];
		$this->oauth->setToken($access['oauth_token'], $access['oauth_token_secret']);
		$_SESSION['oauth']['linkedin']['authorized'] = true;
		
		return true;
		
	}
	
	/**
	 * GET the response of a url.
	 * $param $url the url to be fetched
	 * @return xml response or false if oauth->fetch fails.
	 */
	function get($url) {
		
		$this->authorize();
		$this->oauth->enableDebug();
		
		try {
			$this->oauth->fetch($url);
			return $this->XMLtoArray($this->oauth->getLastResponse());
		} catch(Exception $e) {
			echo '<pre>Error: GET '.$url.'</pre>';
			echo '<pre>';
			print_r($this->oauth->debugInfo);
			echo '</pre>';
			return false;
		}
	}
	
	/**
	 * Fetch data of a user.
	 * @param $options array of options:
	 * 'data' empty for users profile, 'connections' for users connections, 'network' for users network updates
	 * 'profile' string default: '~'. info: http://developer.linkedin.com/docs/DOC-1002
	 * 'fields' array of fields. info: http://developer.linkedin.com/docs/DOC-1061
	 * @return result of $this->fetch
	 */
	function getData($options = array('data'=>'','profile'=>'~','fields'=>array())) {
				
		if(empty($options['profile'])) $options['profile'] = '~';
		
		$query = '/v1/people/'.$options['profile'];
		
		if(!empty($options['data'])) {
			$query .= '/'.$options['data'];
		}
		
		if(is_string($options['fields']) && $options['fields'] == 'all') {
			$query .= ':full';
		} elseif(!empty($options['fields'])) {
			$query .= ':('.implode(',',$options['fields']).')';
		}
		
		return $this->get($this->apiUrl.$query);
	}
	
	/**
	 * Search people. See also: http://developer.linkedin.com/docs/DOC-1005
	 * @param $options array of options:
	 * 'keywords' [+ delimited keywords]
	 * 'name' [first name + last name]
	 * 'company' [company name]
	 * 'current-company' [true|false]
	 * 'title' [title]
	 * 'current-title' [true|false]
	 * 'industry-code' [industry code]
	 * 'search-location-type' [I|Y]										- buggy
	 * 'country-code' [country code] (search-location-type must be I)	- buggy
	 * 'postal-code' [postal code] (search-location-type must be I)		- buggy
	 * 'network' [in|out]
	 * 'start' [number]
	 * 'count' [1-10]
	 * 'sort-criteria' [ctx|endorsers|distance|relevance]				- bug: only ctx ??
	 * @return result of $this->fetch
	 */
	function search($options = array()) {
						
		$query = '/v1/people';
		
		$first = true;
		foreach($options as $key => $value) {
			if($first == true) {
				$separator = '?';
			} else {
				$separator = '&';
			}
			
			$query .= $separator.$key.'='.urlencode($value);
			
			$first = false;
		}
		
		return $this->get($this->apiUrl.$query);
	}
	
	/**
	 * Save the status update to linkedin.
	 * @param $text string to be posted as the status
	 * @return result of $this->fetch
	 */
	function statusUpdate($text) {
		
		$this->authorize();
		
		$parameters = '<?xml version="1.0" encoding="UTF-8"?><current-status>'.$text.'</current-status>';
				
		try {
			$this->oauth->fetch($this->apiUrl.'/v1/people/~/current-status',$parameters,OAUTH_HTTP_METHOD_PUT);
			return true;
		} catch(Exception $e) {
			echo '<pre>Error: PUT current-status. Please try again.</pre>';
			return false;
		}
		
	}
	
	/**
	 * Save a network activity from the user to linkedin.
	 * Follow the guidelines: http://developer.linkedin.com/docs/DOC-1009
	 * @param $text string to be posted as the status
	 * @return result of $this->fetch
	 */
	function networkUpdate($text) {
		
		$this->authorize();

		$user = $this->getData();
		$user = '<a href="'.$user['site-standard-profile-request']['url'].'">'
				.$user['first-name'].' '.$user['last-name'].'</a>';
		
		$parameters = 	'<activity locale="en_US">'
						.'<timestamp>'.time().'</timestamp>'
						.'<content-type>linkedin-html</content-type>'
						.'<body>'.htmlspecialchars($user).' '.htmlspecialchars($text).'</body>'
						.'</activity>';
						

		$headers['Content-Type'] = 'text/xml;charset=UTF-8';
		
		$this->oauth->enableDebug();
		
		try {
			$this->oauth->fetch($this->apiUrl.'/v1/people/~/person-activities',$parameters,OAUTH_HTTP_METHOD_POST,$headers);
			echo '<pre>';
			print_r($this->oauth->getLastResponse());
			print_r($this->oauth->getLastResponseInfo());
			print_r($this->oauth);
			echo '</pre>';
			return true;
		} catch(Exception $e) {
			echo '<pre>Error: POST person-activities. Please try again.</pre>';
			return false;
		}
		
	}
	
	/**
	 * Recursive function to transform xml to a workable array
	 * @param $input simplexml_load_string('xml_string')
	 * @return array
	 */
	function XMLtoArray($obj) {
		$obj = is_string($obj) ? simplexml_load_string($obj) : $obj;
		$_arr = is_object($obj) ? get_object_vars($obj) : $obj;
		
		if(!empty($_arr)) {
			foreach ($_arr as $key => $val) {
				$val = (is_array($val) || is_object($val)) ? $this->XMLtoArray($val) : $val;
				$arr[$key] = $val;
			}
		
			return $arr;
		}
	}
	
	/**
	 * Generates $this->industryGroups and $this->industryCodes
	 * industryGroups is the same as industryCodes but ordered by group
	 */
	function generateIndustryCodes() {
		
		$raw_industry_codes = array(
			array('47','corp fin','Accounting'),
			array('94','man tech tran','Airlines/Aviation'),
			array('120','leg org','Alternative Dispute Resolution'),
			array('125','hlth','Alternative Medicine'),
			array('127','art med','Animation'),
			array('19','good','Apparel & Fashion'),
			array('50','cons','Architecture & Planning'),
			array('111','art med rec','Arts and Crafts'),
			array('53','man','Automotive'),
			array('52','gov man','Aviation & Aerospace'),
			array('41','fin','Banking'),
			array('12','gov hlth tech','Biotechnology'),
			array('36','med rec','Broadcast Media'),
			array('49','cons','Building Materials'),
			array('138','corp man','Business Supplies and Equipment'),
			array('129','fin','Capital Markets'),
			array('54','man','Chemicals'),
			array('90','org serv','Civic & Social Organization'),
			array('51','cons gov','Civil Engineering'),
			array('128','cons corp fin','Commercial Real Estate'),
			array('118','tech','Computer & Network Security'),
			array('109','med rec','Computer Games'),
			array('3','tech','Computer Hardware'),
			array('5','tech','Computer Networking'),
			array('4','tech','Computer Software'),
			array('48','cons','Construction'),
			array('24','good man','Consumer Electronics'),
			array('25','good man','Consumer Goods'),
			array('91','org serv','Consumer Services'),
			array('18','good','Cosmetics'),
			array('65','agr','Dairy'),
			array('1','gov tech','Defense & Space'),
			array('99','art med','Design'),
			array('69','edu','Education Management'),
			array('132','edu org','E-Learning'),
			array('112','good man','Electrical/Electronic Manufacturing'),
			array('28','med rec','Entertainment'),
			array('86','org serv','Environmental Services'),
			array('110','corp rec serv','Events Services'),
			array('76','gov','Executive Office'),
			array('122','corp serv','Facilities Services'),
			array('63','agr','Farming'),
			array('43','fin','Financial Services'),
			array('38','art med rec','Fine Art'),
			array('66','agr','Fishery'),
			array('34','rec serv','Food & Beverages'),
			array('23','good man serv','Food Production'),
			array('101','org','Fund-Raising'),
			array('26','good man','Furniture'),
			array('29','rec','Gambling & Casinos'),
			array('145','cons man','Glass, Ceramics & Concrete'),
			array('75','gov','Government Administration'),
			array('148','gov','Government Relations'),
			array('140','art med','Graphic Design'),
			array('124','hlth rec','Health, Wellness and Fitness'),
			array('68','edu','Higher Education'),
			array('14','hlth','Hospital & Health Care'),
			array('31','rec serv tran','Hospitality'),
			array('137','corp','Human Resources'),
			array('134','corp good tran','Import and Export'),
			array('88','org serv','Individual & Family Services'),
			array('147','cons man','Industrial Automation'),
			array('84','med serv','Information Services'),
			array('96','tech','Information Technology and Services'),
			array('42','fin','Insurance'),
			array('74','gov','International Affairs'),
			array('141','gov org tran','International Trade and Development'),
			array('6','tech','Internet'),
			array('45','fin','Investment Banking'),
			array('46','fin','Investment Management'),
			array('73','gov leg','Judiciary'),
			array('77','gov leg','Law Enforcement'),
			array('9','leg','Law Practice'),
			array('10','leg','Legal Services'),
			array('72','gov leg','Legislative Office'),
			array('30','rec serv tran','Leisure, Travel & Tourism'),
			array('85','med rec serv','Libraries'),
			array('116','corp tran','Logistics and Supply Chain'),
			array('143','good','Luxury Goods & Jewelry'),
			array('55','man','Machinery'),
			array('11','corp','Management Consulting'),
			array('95','tran','Maritime'),
			array('97','corp','Market Research'),
			array('80','corp med','Marketing and Advertising'),
			array('135','cons gov man','Mechanical or Industrial Engineering'),
			array('126','med rec','Media Production'),
			array('17','hlth','Medical Devices'),
			array('13','hlth','Medical Practice'),
			array('139','hlth','Mental Health Care'),
			array('71','gov','Military'),
			array('56','man','Mining & Metals'),
			array('35','art med rec','Motion Pictures and Film'),
			array('37','art med rec','Museums and Institutions'),
			array('115','art rec','Music'),
			array('114','gov man tech','Nanotechnology'),
			array('81','med rec','Newspapers'),
			array('100','org','Non-Profit Organization Management'),
			array('57','man','Oil & Energy'),
			array('113','med','Online Media'),
			array('123','corp','Outsourcing/Offshoring'),
			array('87','serv tran','Package/Freight Delivery'),
			array('146','good man','Packaging and Containers'),
			array('61','man','Paper & Forest Products'),
			array('39','art med rec','Performing Arts'),
			array('15','hlth tech','Pharmaceuticals'),
			array('131','org','Philanthropy'),
			array('136','art med rec','Photography'),
			array('117','man','Plastics'),
			array('107','gov org','Political Organization'),
			array('67','edu','Primary/Secondary Education'),
			array('83','med rec','Printing'),
			array('105','corp','Professional Training & Coaching'),
			array('102','corp org','Program Development'),
			array('79','gov','Public Policy'),
			array('98','corp','Public Relations and Communications'),
			array('78','gov','Public Safety'),
			array('82','med rec','Publishing'),
			array('62','man','Railroad Manufacture'),
			array('64','agr','Ranching'),
			array('44','cons fin good','Real Estate'),
			array('40','rec serv','Recreational Facilities and Services'),
			array('89','org serv','Religious Institutions'),
			array('144','gov man org','Renewables & Environment'),
			array('70','edu gov','Research'),
			array('32','rec serv','Restaurants'),
			array('27','good man','Retail'),
			array('121','corp org serv','Security and Investigations'),
			array('7','tech','Semiconductors'),
			array('58','man','Shipbuilding'),
			array('20','good rec','Sporting Goods'),
			array('33','rec','Sports'),
			array('104','corp','Staffing and Recruiting'),
			array('22','good','Supermarkets'),
			array('8','gov tech','Telecommunications'),
			array('60','man','Textiles'),
			array('130','gov org','Think Tanks'),
			array('21','good','Tobacco'),
			array('108','corp gov serv','Translation and Localization'),
			array('92','tran','Transportation/Trucking/Railroad'),
			array('59','man','Utilities'),
			array('106','fin tech','Venture Capital & Private Equity'),
			array('16','hlth','Veterinary'),
			array('93','tran','Warehousing'),
			array('133','good','Wholesale'),
			array('142','good man rec','Wine and Spirits'),
			array('119','tech','Wireless'),
			array('103','art med rec','Writing and Editing'),
		);
		
		foreach($raw_industry_codes as $code) {			
			foreach(explode(' ',$code[1]) as $group) {
				$this->industryGroups[$group][$code[0]] = $code[2];
			}
			$this->industryCodes[$code[0]] = $code[2];
		}
	}
	
}

$apiKey        = 'Lmw8w_R-fyPxj7TZ6oUk8x3JVziS_890h0aee1RTpZLqS4pslFfE-yk10NLcDXTU';
$secretKey    = 'kLcMrbZDrsBPMxIDtb-ZtygP_snSB3GwhpcWlxYwvJLr_k3tL7iM5HjomlihJkAs';
$callback    = 'http://google.com/';
$linkedin    = new Linkedin($apiKey,$secretKey,$callback);
$profile = $linkedin->getData();
var_dump($profile);
?>