<?php
 require_once(dirname(__FILE__) . "/class.magic_quote.php"); 
 require_once(dirname(dirname(__FILE__)) . "/include/global.php");
 require_once(dirname(__FILE__) . "/class.country.php"); 
 require_once(dirname(__FILE__) . "/class.company.php");
 require_once(dirname(__FILE__) . "/class.transaction.php"); 
 require_once(dirname(__FILE__) . "/class.oneStop.php"); 
 require_once(dirname(__FILE__) . "/class.transaction.php");
 require_once(dirname(__FILE__) . '/class.MobileDetection.php');
 
 ini_set('display_errors',0);
 error_reporting(E_ALL);
  class MobileApp {
      
      public $account = null;
      public $transaction = null;
      public $platform = null;

      public function __construct() 
      {
         require_once(dirname(__FILE__) . "/class.account.php");
         
         $this->account = new account();
         $this->transaction = new transaction();
         $this->platform = new MobileDetection();
      }

      public function authenticate()
      {
           
         $this->account->authenticate_site_member($_POST['username'], $_POST['password'], true, $isAuthenticated, $errors); 
         if ($isAuthenticated) {
            return $this->getResponseForCode();    
         } else {
             if (@$errors['login_email']) {
                return $this->createJson($errors['login_email']) ;   
             }
             if (@$errors['password']) {
                return $this->createJson($errors['password']) ;   
             }
             
             return $this->createJson('Unknown error occured.');            
         }       
      }
      
      public function getResponseForCode($errorCode = null)
      {
        if (is_null($errorCode)) {
            return $this->createJson(false);
        } 
         
        $errorMessages = array(
            0 => 'Invalid request.'
            , 1 => 'The username/password you have entered is incorrect.'
            , 2 => ''
        );
        
        return $this->createJson($errorMessages[$errorCode]);
               
      }
      
      public function createJson($error)
      {
        return json_encode(    
        array(
            'success'=> ($error === false) ? true : false
            , 'error' => ($error === false) ? '' : $error
        ));
      }
      
      public function getAllCountries()
      {
         country::get_all_country_list($countries,$countriesNr);
         return $countries;
      }      
      
      public function getAllIndustries()
      {
         company::get_all_sector_industry_list($industries, $industriesNr);
         return json_encode($industries);
      }
            
      public function getAllMeetingTypes()
      {
        transaction::get_all_category_type('*', $categories, $categoriesNr);
        $sortedCategories = array();
        //dump($categories);
        $prohibitedCategories = array(11,17);

        $alreadyAddedTypes = array();


        foreach($categories as $category) {
            $text = $category['subtype1']; 
            if ($category['subtype2'] == 'n/a') {
                if ($category['type'] == 'M&A') {
                    $category['subtype1'] = 'M&A ' . $category['subtype1'];   
                }
                $sortedCategories[$category['type']][$category['subtype1']][] = array('text' => 'All ' . $category['subtype1'] . 's', 'id' => $category['id'], 'leaf' => true);  
            } else {
                $sortedCategories[$category['type']][$category['subtype1']][] = array('text' => $category['subtype2'] , 'id' => $category['id'], 'leaf' => true); 
            }
        } 
        
        $arrayForJson = array();
        //var_dump($sortedCategories);
        foreach ($sortedCategories as $key=>$sCat) {
            $ssCatArray = array();
            foreach($sCat as $ssCatName => $ssCat) {
                $pushData =  array('text' => $ssCatName, 'id' => $ssCat[0]['id']);
                if (sizeOf($ssCat) == 1) $pushData['leaf'] = true;
                if (sizeOf($ssCat) > 1)  $pushData['items'] = $ssCat; 
                $ssCatArray[] = $pushData;
            }
            $arrayForJson[] = array('text' => $key, 'items' => $ssCatArray);
        }
        
        return json_encode(array('items' => $arrayForJson));
        return  preg_replace('/"(leaf|items|text|id)"/', '\1', json_encode($arrayForJson));
  
      }
      
        function getTypeId($types, $type) {
            foreach ($types as $currType) {
                   if($currType['subtype1'] == $type && $currType['subtype2'] == 'n/a')
                    return $currType['id'];
            }
        }
        
        function getResults($returnResults = false) 
        {
            if (isset($_GET['requestId'])) {
                $this->getRequest($_GET['requestId']);
            }
            
            $industry = $_POST['industry'];
            $dealType = $_POST['dealType'];
            $country = $_POST['country'];
            $currentDate = date('Y-m-d'); 
            if (0 == $dealType) {
                $dealArr = array(
                    'type' => 'M&A'
                    , 'subtype1' => ''
                    , 'subtype2' => 'n/a'
                );
            } else {
                $dealArr = $this->getDealById($dealType);
            }
            
            $countryArr = $this->getCountryById($country);
            $industryArr = $this->getIndustryById($industry);

            $firstTableData = array(
                'country' => $countryArr['countryName']
                //, 'region' => $countryArr['regionName']
                , 'industry' => $industryArr['industry']
                , 'sector' => $industryArr['sector']
                , 'deal_cat_name' => $dealArr['type']
                , 'deal_subcat1_name' => $dealArr['subtype1']
                /**
                 * TODO: Check whether this is needed in case 
                 * of n/a value
                 */
                , 'deal_subcat2_name' => $dealArr['subtype2']
                , 'myaction' => 'search'
                , 'miniumDateForDeals' => '2010-06-28'
            );

            $secondTableData = array(
                'region' => $countryArr['regionName']
                , 'sector' => $industryArr['sector']
                , 'deal_cat_name' => $dealArr['type']
                , 'myaction' => 'search'
                , 'miniumDateForDeals' => '2010-06-28'
            );
            
            if ("M&A" != $dealArr['type']) {
                $secondTableData['deal_subcat1_name'] = $dealArr['subtype1'];
            }
            
            if ($dealArr['subtype2'] == 'n/a') {
                unset($dealArr['subtype2']);
                unset($firstTableData['deal_subcat2_name']);
            }
            
            unset($dealArr['id']);
            $dealLabel = join(' > ', $dealArr);
            //$dealLabel 
            $this->transaction->front_deal_search_paged($firstTableData, 0, 10, $firstTableResult, $nrFound);
            $this->transaction->front_deal_search_paged($secondTableData, 0, 10, $secondTableResult, $nrFound);
            
            $data1 = array(
                'label1' => '1. exact deals in the last 2 weeks'
                , 'label2' => implode(', ', array($countryArr['countryName'], $industryArr['industry'], $dealLabel))
                , 'entries' => $firstTableResult
            );   
            
            $data2 = array(
                'label1' => '2. top 10 similar deals in the last 2 weeks'
                , 'label2' => implode(', ', array($countryArr['regionName'], $industryArr['sector'], $dealArr['type']))
                , 'entries' => $secondTableResult
            );  
            $news = $this->getNewsFeed($secondTableData);
            if (!isset($_GET['requestId']))
                $this->saveRequest();
            
            if ($returnResults) {
                $data = array(
                    'data1' => $data1,
                    'data2' => $data2,
                    'news' => $news,

                );
                
                return $data;
            }
            
            echo $this->getTableMarkup($data1);
            echo $this->getTableMarkup($data2);
            echo $this->getNewsMarkup($news);
            /**
             * Footer
             * TODO: Find a better way to do this in the app
             */
            echo '<div style="text-align:center; font-size: 12px;" class="x-panel-body">Copyright &copy; 2011 deal-data.com <br /> <a href="#"> Privacy policy </a> | <a href="#"> Legal Notices </a></div>';
            
        }
        
        function saveRequest() {
            $q = sprintf('INSERT INTO %s (userid, post_data) VALUES (%d, \'%s\')', TP . '2weeksnow_requests', $_SESSION['mem_id'], serialize($_POST));
            mysql_query($q);
        }
        
        function getRequest($requestId) {
            $q = sprintf('SELECT * FROM %s WHERE userid = %d AND id = %d', TP . '2weeksnow_requests',  $_SESSION['mem_id'], $requestId);
            $stmt = mysql_query($q);
            
            if ($stmt) {
                $data = mysql_fetch_assoc($stmt);
                if (!sizeOf($data) && !$this->platform->isMobile()) {
                    header('Location: 2WeeksNow.php');
                    exit();
                }
                $_POST = unserialize($data['post_data']);
            }
                
        }
        
        function getRequestsForCurrentUser($numberOfRequests = 5) {
            $q = sprintf("SELECT * FROM %s WHERE userid = %d ORDER BY id DESC LIMIT %d", TP . '2weeksnow_requests', $_SESSION['mem_id'], $numberOfRequests);
            $stmt = mysql_query($q);
            $requests = array();
            if ($stmt) {
                while($row = mysql_fetch_assoc($stmt)) {
                    $data = unserialize($row['post_data']);
                    $requests[] = array(
                        'country' => $this->getCountryById($data['country']),
                        'industry' => $this->getIndustryById($data['industry']),
                        'dealType' => $this->getDealById($data['dealType']),
                        'id' => $row['id']
                    );
                }
            }
            
            return $requests;
        }
        
        function getNewsFeed($data) {
            $url = 'https://ajax.googleapis.com/ajax/services/search/news?v=1.0&q=%s&key=ABQIAAAABgcz5mhsLIAHbwxpv0nV1xRY7-VUBxxk6GBFcajrGXM59eGelhQPH-voRL1AmR7ClA1yJakD7dYqIQ&userip=%s&rsz=7';
            //$url = 'https://ajax.googleapis.com/ajax/services/search/news?v=1.0&q=%s&key=ABQIAAAABgcz5mhsLIAHbwxpv0nV1xTsxxi_eoC4piWA70bP1Y0_vvuDcRRKyV5yEE6PvsA2GFGO4QUpBzvp1Q&userip=%s';
            $query = urlencode($data['region'] . ' ' . $data['sector'] . ' ' . $data['deal_cat_name']);
            $url = sprintf($url, $query, $this->getUserIp());
            $stories = $this->getByCurl($url);
            
            $news = array();
            if (@$stories->responseData->results) {
                foreach ($stories->responseData->results as $story) {
                    $news[] = array(
                        'title' => $story->titleNoFormatting, 
                        'content' =>  $story->content, 
                        'date' => $story->publishedDate, 
                        'link' => $story->unescapedUrl
                    ); 
                }
            }
            
            return $news;
          }
        
        function getNewsMarkup($news){
            $markup = "<div style='padding:10px'>";
            $markup .= "<div style='color: #E86200; font-size:14px; margin-bottom:10px;'> 3. News feeds </div> <hr style='width:90%' />";
            if (!sizeOf($news)) {
                $markup .= "<div style='font-size:13px; margin-bottom:10px;'> No news maching your critera were found.</div></div>";
                return $markup;
            }
            foreach ($news as $story) {
                $markup .= "<div>";
                $markup .= sprintf('<a href="%s" style="font-size:13px; margin-bottom:10px;" target="_blank"> %s </a>', $story['link'], $story['title']);
                $markup .= sprintf("<div style='font-size:12px'> %s </div>" , $story['content']);
                $markup .= sprintf("<br /><div style='font-size:11px;'> %s </div>" , $story['date']);
                $markup .= "<hr style='width:40%' />"; 
                $markup .= "</div>";
            }
            
            $markup .= "<div>";
            return $markup;
        }
        function getByCurl($url) {

            // create curl resource
            $ch = curl_init();

            // set url
            curl_setopt($ch, CURLOPT_URL, $url);

            //return the transfer as a string
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $output = curl_exec($ch);

            // close curl resource to free up system resources
            curl_close($ch);
            //$output = '{"responseData": {"results":[{"GsearchResultClass":"GnewsSearch","clusterUrl":"","content":"Compared with year-end 2010, total \u003cb\u003ecustomer\u003c/b\u003e loan balances increased by 8% or US$79.5bn to US$1.0 trillion, rising in all regions except \u003cb\u003eNorth America\u003c/b\u003e, where we managed down balances in the \u003cb\u003eConsumer\u003c/b\u003e Finance portfolios. • The core tier 1 ratio increased \u003cb\u003e...\u003c/b\u003e","unescapedUrl":"http://hereisthecity.com/2011/08/01/hsbc-holdings-plc-2011-interim-results-highlights/","url":"http%3A%2F%2Fhereisthecity.com%2F2011%2F08%2F01%2Fhsbc-holdings-plc-2011-interim-results-highlights%2F","title":"HSBC - Interim Results Highlights","titleNoFormatting":"HSBC - Interim Results Highlights","location":"","publisher":"Here Is The City","publishedDate":"Mon, 01 Aug 2011 03:26:33 -0700","signedRedirectUrl":"http://news.google.com/news/url?sa\u003dT\u0026ct\u003dus/0-0-0\u0026fd\u003dS\u0026url\u003dhttp://hereisthecity.com/2011/08/01/hsbc-holdings-plc-2011-interim-results-highlights/\u0026cid\u003d0\u0026ei\u003dv-g2TpieHYur6AbVmYsY\u0026usg\u003dAFQjCNHAkcF5ah-qm13PxoaXj1w2Z2tx2g","language":"en"},{"GsearchResultClass":"GnewsSearch","clusterUrl":"","content":"The executive management and I are confident that the combination of our two companies is a strong strategic fit that will not only deliver expanded service offerings for our clients in \u003cb\u003eNorth America\u003c/b\u003e, but also creates opportunities to service them on a \u003cb\u003e...\u003c/b\u003e","unescapedUrl":"http://www.marketwatch.com/story/sfn-group-to-be-acquired-by-randstad-2011-07-20?reflink\u003dMW_news_stmp","url":"http%3A%2F%2Fwww.marketwatch.com%2Fstory%2Fsfn-group-to-be-acquired-by-randstad-2011-07-20%3Freflink%3DMW_news_stmp","title":"SFN Group to Be Acquired By Randstad","titleNoFormatting":"SFN Group to Be Acquired By Randstad","location":"","publisher":"MarketWatch (press release)","publishedDate":"Wed, 20 Jul 2011 14:06:33 -0700","signedRedirectUrl":"http://news.google.com/news/url?sa\u003dT\u0026ct\u003dus/0-1-0\u0026fd\u003dS\u0026url\u003dhttp://www.marketwatch.com/story/sfn-group-to-be-acquired-by-randstad-2011-07-20%3Freflink%3DMW_news_stmp\u0026cid\u003d8797728773875\u0026ei\u003dv-g2TpieHYur6AbVmYsY\u0026usg\u003dAFQjCNECAqCZ7NrmtaWuUes8voXmLsCuKg","language":"en"},{"GsearchResultClass":"GnewsSearch","clusterUrl":"","content":"The geographical distribution of revenue for the period was 42% \u003cb\u003eNorth America\u003c/b\u003e, 35% EAME \u0026amp; CIS, 15% Latin America, and 8% APAC markets. Year to date capital expenditures totaled $126 million, a 40% increase from the comparable prior period largely as a \u003cb\u003e...\u003c/b\u003e","unescapedUrl":"http://www.marketwatch.com/story/cnh-second-quarter-2011-revenue-increases-24-operating-profit-up-58-2011-07-25?reflink\u003dMW_news_stmp","url":"http%3A%2F%2Fwww.marketwatch.com%2Fstory%2Fcnh-second-quarter-2011-revenue-increases-24-operating-profit-up-58-2011-07-25%3Freflink%3DMW_news_stmp","title":"CNH Second Quarter 2011 Revenue Increases 24%; Operating Profit up 58%","titleNoFormatting":"CNH Second Quarter 2011 Revenue Increases 24%; Operating Profit up 58%","location":"","publisher":"MarketWatch (press release)","publishedDate":"Mon, 25 Jul 2011 05:15:21 -0700","signedRedirectUrl":"http://news.google.com/news/url?sa\u003dT\u0026ct\u003dus/0-2-0\u0026fd\u003dS\u0026url\u003dhttp://www.marketwatch.com/story/cnh-second-quarter-2011-revenue-increases-24-operating-profit-up-58-2011-07-25%3Freflink%3DMW_news_stmp\u0026cid\u003d8797730996471\u0026ei\u003dv-g2TpieHYur6AbVmYsY\u0026usg\u003dAFQjCNFqVbQxrnVpIIZeEfj51xSVfisPZw","language":"en"},{"GsearchResultClass":"GnewsSearch","clusterUrl":"","content":"We have been taking our focus away from \u003cb\u003ecyclical\u003c/b\u003e stocks.” Tom Walker, manager of the £677m Martin Currie \u003cb\u003eNorth American\u003c/b\u003e fund says valuations are very attractive, given that interest rates are low and will remain so because of the economy. \u003cb\u003e...\u003c/b\u003e","unescapedUrl":"http://www.moneymarketing.co.uk/investments/american-strategies/1034878.article","url":"http%3A%2F%2Fwww.moneymarketing.co.uk%2Finvestments%2Famerican-strategies%2F1034878.article","title":"\u003cb\u003eAmerican\u003c/b\u003e strategies","titleNoFormatting":"American strategies","location":"","publisher":"Money Marketing","publishedDate":"Mon, 25 Jul 2011 00:13:25 -0700","signedRedirectUrl":"http://news.google.com/news/url?sa\u003dT\u0026ct\u003dus/0-3-0\u0026fd\u003dS\u0026url\u003dhttp://www.moneymarketing.co.uk/investments/american-strategies/1034878.article\u0026cid\u003d8797730877824\u0026ei\u003dv-g2TpieHYur6AbVmYsY\u0026usg\u003dAFQjCNEgwtXVEcWjH2eP_gF8z69v60JmVg","language":"en"}],"cursor":{"pages":[{"start":"0","label":1},{"start":"4","label":2},{"start":"8","label":3},{"start":"12","label":4},{"start":"16","label":5},{"start":"20","label":6},{"start":"24","label":7}],"estimatedResultCount":"27","currentPageIndex":0,"moreResultsUrl":"http://news.google.com/nwshp?oe\u003dutf8\u0026ie\u003dutf8\u0026source\u003duds\u0026q\u003dNorth+America+Consumer,+Cyclical+Equity\u0026hl\u003den\u0026start\u003d0"}}, "responseDetails": null, "responseStatus": 200}';
            return json_decode($output);
        }
        function getUserIp() {
            if (isset($_SERVER['HTTP_X_FORWARD_FOR'])) {
                return $_SERVER['HTTP_X_FORWARD_FOR'];
            } else {
                return $_SERVER['REMOTE_ADDR'];
            }   
        }
        
        function getDealById($id)
        {
            $q = "SELECT * FROM %s WHERE id = %d";
            $q = sprintf($q, TP . 'transaction_type_master', $id);
            
            $stmt = mysql_query($q);
            if (!$stmt) {
                return false;
            }
            return mysql_fetch_assoc($stmt);
        }
        
        function getCountryById($id)
        {
            $q = "SELECT cm.name as countryName, rm.name as regionName, cm.id as countryId, rm.id as regionId FROM %s rcl LEFT JOIN %s cm on rcl.country_id = cm.id LEFT JOIN %s rm ON rcl.region_id = rm.id WHERE rcl.country_id = %d";
            $q = sprintf($q, TP . 'region_country_list', TP . 'country_master', TP . 'region_master', $id);
            //echo $q;
            $stmt = mysql_query($q);
            if (!$stmt) {
                return false;
            }
            return mysql_fetch_assoc($stmt);            
        }
        
        public function getIndustryById($id)
        {
            $q = "SELECT * FROM %s WHERE id = %d";
            $q = sprintf($q, TP . 'sector_industry_master', $id);
            //echo $q;
            $stmt = mysql_query($q);
            if (!$stmt) {
                return false;
            }
            return mysql_fetch_assoc($stmt);              
        }
        
        public function getTableMarkup($data)
        {
            $markup = "<div style='padding:10px'>";
            $markup .= sprintf("<div style='color: #E86200; font-size:14px; margin-bottom:10px;'> %s </div>", $data['label1']);
            if (!sizeOf($data['entries'])) {
                $markup .= "<div style='font-size:13px; margin-bottom:10px;'> There are no entries that match the criteria in the last 2 weeks.</div></div>";
                return $markup;
            }
            $markup .= sprintf("<div style='font-size:13px; margin-bottom:5px;'> %s </div>", $data['label2']);
            $markup .= 
           "<table class='company'>
            <tr>
                <td> Company </td>
                <td> Size </td>
                <td> Date </>
                <td> Link </td>
            </tr>";
            foreach ($data['entries'] as $entry) {
                $link = sprintf('<a href="#" class="link" id="%s"> Details </a>', $entry['deal_id']);
                $tmpMarkup = sprintf("
                <tr>
                    <td> %s </td>
                    <td> %s </td>
                    <td> %s </>
                    <td> %s </td>
                </tr>                    
                ", $entry['company_name'], 
                   $this->formatSize($entry['value_in_billion']),
                   $this->formatDate($entry['date_of_deal']),
                   $link
                );
                $markup .= $tmpMarkup;
                
            }
            $markup .= '</table></div>';
            return $markup;
            
        }
        
        public function formatSize($value)
        {
            $size = $value * 1000;
            $size = number_format($size, 0);
            $size .= 'm';
            return '$' . $size;
        }
        
        public function formatDate($date, $format = 'd M') 
        {
            return str_replace(' ', '&nbsp;', date($format, strtotime($date)));
        }
        
        public function getDetails() 
        {
            $sections = array(array(), array(), array(), array(), array('<div style="text-align:center; font-size: 12px;" class="x-panel-body">Copyright &copy; 2011 deal-data.com <br /> <a href="#"> Privacy policy </a> | <a href="#"> Legal Notices </a></div>'));
            
            $this->transaction->front_get_deal_detail_extra($_GET['dealId'], $transactionInfo, $foundTransaction);
            if (!$foundTransaction) {
                return 'We are sorry, the transaction you requested does not have any info associated';
            }
            
            //if ($transactionInfo['deal_cat_name'])
            switch($transactionInfo['deal_cat_name']){
                case 'M&A':
                    $sections[0][] = $transactionInfo['deal_cat_name'] . ' Deal: ' . $transactionInfo['deal_subcat1_name'];
                    $sections[0][] = $transactionInfo['company_name'] . ' aquiring ' . $transactionInfo['target_company_name'];
                    $sections[0][] = $this->formatSize($transactionInfo['value_in_billion']);
                    $sections[0][] = $this->formatDate($transactionInfo['date_of_deal'], 'jS M Y');
                    $sections[1][] = '<span style="font-style: italic;"> Buyer: </span>';
                    $sections[1][] = $transactionInfo['company_name'];
                    $sections[1][] = $transactionInfo['sector'] . ', ' . $transactionInfo['industry'];
                    $sections[1][] = $transactionInfo['hq_country'] . '<br />';
                    $sections[1][] = '<span style="font-style: italic;"> Target: </span>';
                    $sections[1][] = $transactionInfo['target_company_name'];
                    $sections[1][] = $transactionInfo['target_sector'] . ', ' . $transactionInfo['target_industry'];
                    $sections[1][] = $transactionInfo['target_country']. '<br />';
                    if ($transactionInfo['seller_company_name'] != '') {
                        $sections[1][] = '<span style="font-style: italic;"> Seller: </span>';
                        $sections[1][] = $transactionInfo['seller_company_name'];
                        $sections[1][] = $transactionInfo['seller_sector'] . ', ' . $transactionInfo['seller_industry'];
                        $sections[1][] = $transactionInfo['seller_country']. '<br />';                        
                    }
                    $sections[2][] = '<span style="font-style: italic;"> Sources: </span>';
                    $sourcesMarkup = '';
                    if ('' != $transactionInfo['sources']) {
                        $sources = explode(',', $transactionInfo['sources']);
                        foreach ($sources as $source) {
                            $urlArray = parse_url($source);
                            $text = $urlArray['scheme'] . '://' . $urlArray['host'];
                            $sourcesMarkup .= sprintf('<a href="%s" target="_blank"> %s </a><br />', $source, $text);
                        }
                        $sections[2][] = $sourcesMarkup;
                    } else {
                         $sections[2][] = 'n/a';
                    }
                   
                    $sections[3][] = '<span style="font-style: italic;"> Banks: </span>';
                    $banksMarkup = '';
                    foreach ($transactionInfo['banks'] as $bank) {
                        $banksMarkup .= $bank['name'] . '<br />';
                    }
                    $sections[3][] = $banksMarkup . '<br />';
                    
                    $sections[3][] = '<span style="font-style: italic;"> Law Firms: </span>';
                    $lawFirmsMarkup = '';
                    foreach ($transactionInfo['law_firms'] as $lawFirm) {
                        $lawFirmsMarkup .= $lawFirm['name'] . '<br />';
                    }
                    $sections[3][] = $lawFirmsMarkup . '<br />';                    
                    break;
                default:
                    $sections[0][] = $transactionInfo['deal_cat_name'] . ' Deal: ' . $transactionInfo['deal_subcat2_name'];
                    $sections[0][] = $this->formatSize($transactionInfo['value_in_billion']);
                    $sections[0][] = $this->formatDate($transactionInfo['date_of_deal'], 'jS M Y');
                    $sections[1][] = '<span style="font-style: italic;"> Company: </span>';
                    $sections[1][] = $transactionInfo['company_name'];
                    $sections[1][] = $transactionInfo['sector'] . ', ' . $transactionInfo['industry'];
                    $sections[1][] = $transactionInfo['hq_country'] . '<br />';
                    $sections[2][] = '<span style="font-style: italic;"> Sources: </span>';
                    $sourcesMarkup = '';
                    if ('' != $transactionInfo['sources']) {
                        $sources = explode(',', $transactionInfo['sources']);
                        foreach ($sources as $source) {
                            $urlArray = parse_url($source);
                            $text = $urlArray['scheme'] . '://' . $urlArray['host'];                                    
                            $sourcesMarkup .= sprintf('<a href="%s" target="_blank"> %s </a><br />', $source, $text);
                        }
                        $sections[2][] = $sourcesMarkup;
                    } else {
                         $sections[2][] = 'n/a';
                    }
                   
                    $sections[3][] = '<span style="font-style: italic;"> Banks: </span>';
                    $banksMarkup = '';
                    foreach ($transactionInfo['banks'] as $bank) {
                        $banksMarkup .= $bank['name'] . '<br />';
                    }
                    $sections[3][] = $banksMarkup . '<br />';
                    
                    $sections[3][] = '<span style="font-style: italic;"> Law Firms: </span>';
                    $lawFirmsMarkup = '';
                    foreach ($transactionInfo['law_firms'] as $lawFirm) {
                        $lawFirmsMarkup .= $lawFirm['name'] . '<br />';
                    }
                    $sections[3][] = $lawFirmsMarkup . '<br />';                      
                    break;
            }
            //echo "<pre>" . print_r($transactionInfo,1) . "</pre>";
            //var_dump($sections);
            $markup = '<div style="font-size: 12px; padding: 10px">';
            foreach($sections as $key=>$section) {
                if (sizeOf($section)) {
                    $sections[$key] = join("<br />", $section);
                } else {
                    $sections[$key] = '';
                }
            }
           
            $markup .= join("<hr style='width:80%' />", $sections);
            //echo $markup;
            $markup .= '</div>';
            return $markup;//'<pre>' . print_r($transactionInfo,1) . '</pre>';
        }
        
        public function logout() {
            $_SESSION = array();
        }
  }
?>
