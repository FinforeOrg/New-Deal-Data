<?php

/**
 * feeData class
 *
 * $Id:$
 *
 * $Rev:  $
 *
 * $LastChangedBy:  $
 *
 * $LastChangedDate: $
 *
 * @author Ionut MIHAI <ionut_mihai25@yahoo.com>
 * @copyright 2011 Ionut MIHAI
 */

class feeData {

    public $tableName = '';
    public $chart = null;
    
    public function __construct()
    {
        /*if (!defined(TP)) {
            require_once(dirname(dirname(__FILE__)) . '/include/global.php');
        }*/
        
        //require_once(dirname(__FILE__) . '/class.chart.php');
		require_once('classes/class.chart.php');
        $this->tableName = '__TP__fee_data';
    }
    
    /**
     * Get regions available regions for user display
     */
    public function getAvailableRegions($regionId = -2)
    {
        $where = '';
        /** were letting out Global and unkown regions **/
        if ($regionId != -2) {
            $where = sprintf(' AND fd.region_id = %d', $regionId);
        }
        
        $q = '
        SELECT * FROM __TP__region_master rm WHERE EXISTS (SELECT id FROM __TP__fee_data fd WHERE fd.region_id = rm.id' . $where .' );  
        ';

        $res = query($q);
        if (!$res) {
            return array();
        }

        while($row = mysql_fetch_assoc($res)) {
            $regions[$row['id']] = $row['name'];
        }
        
        return $regions;
    }
    
    /**
     * Get regions available regions for user display
     */
    public function getAvailableCountries($countryId = -2)
    {
        $where = '';
        if ($countryId != -2) {
            $where = sprintf(' AND fd.country_id = %d', $countryId);
        }

        $countries = array();  
        $q = '
        SELECT * FROM __TP__country_master cm WHERE EXISTS (SELECT id FROM __TP__fee_data fd WHERE fd.country_id = cm.id ' . $where . ');  
        ';
        $res = query($q);
        
        if (!$res) {
            return array();
        }

        while($row = mysql_fetch_assoc($res)) {
            $countries[$row['id']] = $row['name'];
        }

        return $countries;
    }
    
    public function getAvailableCategories($type = '')
    {
        if ('' != $type) {
            return array($type);
        }
        
        $categories = array(
            'M&A',
            'Equity',
            'Loans',
            'Debt',
        );
        
        return $categories;
    }
    
    public function getRandomCharts()
    {

        
        /**
         * Let`s only show random M&A info (there is more M&A  in the database than anything else)
         */
        $_POST['type'] = 'M&A';
        $_POST['region'] = 25; 
        // let`s send an invalid country id... as we don`t really need the random country graphs
        $_POST['country'] = -3;
        /*
         * TODO: find a better way to handle this
         */
        $_POST['datapoint_filter'] = 3;
        
        $graphs = $this->getPossibleCharts(true);

        unset($_POST);
        if (count($graphs)) {
            $key = array_rand($graphs);
            $section = $graphs[$key];
            if (count($section)) {
                return $this->getMarkupForCharts(array($key => array_slice($section, 0,2)), true);
            }
        }
        
        return '';
    }

    public function getPossibleCharts($fromRandom = false)
    {
       
        @session_start();
        $type = '';
        $countryId = -2;
        $regionId = -2;
        
        if (isset($_POST['type'])) {
            $type = $_POST['type'];
        }
        
        if (isset($_POST['country'])) {
            $countryId = $_POST['country'];
        }
        /** were letting out Global and unkown regions **/
        if (isset($_POST['region'])) {
            $regionId = $_POST['region'];
        }

        $availableCategories = $this->getAvailableCategories($type);   
        $availableCountries = $this->getAvailableCountries($countryId);
        $availableRegions = $this->getAvailableRegions($regionId);
        
        if ($countryId == -2) {
            $availableCountries = array();
        }
        
        if ($regionId == -2) {
            //we always return global
            $availableRegions = array(25 => 'Global');
        }
        
        //$availableRegions[25] = 'Global';
        Log::debug($availableRegions, __FILE__, __LINE__);
        Log::debug($availableCountries, __FILE__, __LINE__);
        
        $possibleCharts = array();
                //<pre>';
        
        /**
         * For each available categories we have to build up the charts accordingly
         */
        foreach ($availableCategories as  $category) {
         
            // we should now fetch the country charts
            foreach ($availableCountries as $countryId => $country) {
                $chartName = '';
                $year = '';

                // Lets fetch `em all at once. No need for separate querries
                $q = 'SELECT *, avg(avg_deal_size) as avg_deal_size_calc,  avg(fees_per_deal) as fees_per_deal_calc, avg(percent_of_deal_size) as percent_of_deal_size_calc FROM __TP__fee_data WHERE (type = "%s" OR subtype1= "%s" OR subtype2= "%s") AND country_id = %s GROUP BY description, year ORDER BY description, year ASC';
                $q = sprintf($q, $category, $category, $category, $countryId);
                if ($res = query($q)) {
                    $dt = array();
                    $data = array();
                    while($row = mysql_fetch_assoc($res)) {
                        $data[$row['description']]['data']['deal_size'][$row['year']] = number_format($row['avg_deal_size_calc'], 2, '.' ,'');
                        $data[$row['description']]['data']['fees_per_deal'][$row['year']] = number_format($row['fees_per_deal'], 2, '.' ,'');
                        $data[$row['description']]['data']['percent_of_deal'][$row['year']] = number_format($row['percent_of_deal_size'], 2, '.' ,'');
                        //if ($row['avg_deal_size_calc'] == 0) continue;
                        $dt[$row['year']] = number_format($row['avg_deal_size_calc'], 2, '.' ,''); 
                    }
                    foreach($data as $description => $chartInfo) {
                        $chartName = sprintf('%s %s (%s)', $category, $country, $description);
                        
                        if (Util::idxExists($_POST, 'datapoint_filter')) {
                            $lowerLimit = $_POST['datapoint_filter'];
                        }
                        
                        $col = 1;
                        foreach ($chartInfo['data'] as $type => $cData) {
                            foreach ($cData as $idx => $value) {
                                if($value == 0) {
                                    unset($cData[$idx]);
                                }
                            }
                            if (count($cData) < $lowerLimit) {
                                Log::debug('Ignoring chart ' . $chartName . '. (' . $type . '). Reason: Too few datapoints: ' . count($cData), __FILE__, __LINE__ );
                                continue;
                            } else {
                                $possibleCharts[] = array('name' => $chartName, 'data'=>$cData, 'legend' =>  $this->getChartLabel($type) , 'column' => $col, 'container' => $description);                
                                $col++;
                                if ($col == 3) {
                                    $col = 1;
                                }
                            }                                 
                        }
                    }
                }
            }            

            //regions last - this is because it`s more general data and we should always present Global at the end
            foreach ($availableRegions as $regionId => $region) {
                $chartName = '';
                $year = '';
                
                
                // First chart is the one with average deal size
                $q = 'SELECT *, avg(avg_deal_size) as avg_deal_size_calc,  avg(fees_per_deal) as fees_per_deal_calc, avg(percent_of_deal_size) as percent_of_deal_size_calc FROM __TP__fee_data WHERE (type = "%s" OR subtype1= "%s" OR subtype2= "%s") AND region_id = %s GROUP BY description, year ORDER BY description, year ASC';
                $q = sprintf($q, $category, $category, $category, $regionId);
                if ($res = query($q)) {
                    $dt = array();
					$data = array();
                    while($row = mysql_fetch_assoc($res)) {
                        $data[$row['description']]['data']['deal_size'][$row['year']] = number_format($row['avg_deal_size_calc'], 2, '.' ,'');
                        $data[$row['description']]['data']['fees_per_deal'][$row['year']] = number_format($row['fees_per_deal'], 2, '.' ,'');
                        $data[$row['description']]['data']['percent_of_deal'][$row['year']] = number_format($row['percent_of_deal_size'], 2, '.' ,'');
                        //if ($row['avg_deal_size_calc'] == 0) continue;
                        $dt[$row['year']] = number_format($row['avg_deal_size_calc'], 2, '.' ,''); 
                    }
                    foreach($data as $description => $chartInfo) {
                        $chartName = sprintf('%s %s (%s)', $category, $region, $description);
                        
                        if (Util::idxExists($_POST, 'datapoint_filter')) {
                            $lowerLimit = $_POST['datapoint_filter'];
                        }
                        
                        $col = 1;
                        foreach ($chartInfo['data'] as $type => $cData) {
                            foreach ($cData as $idx => $value) {
                                if($value == 0) {
                                    unset($cData[$idx]);
                                }
                            }
                            if (count($cData) < $lowerLimit) {
                                Log::debug('Ignoring chart ' . $chartName . '. (' . $type . '). Reason: Too few datapoints: ' . count($cData), __FILE__, __LINE__ );
                                continue;
                            } else {
                                $possibleCharts[] = array('name' => $chartName, 'data'=>$cData, 'legend' =>  $this->getChartLabel($type) , 'column' => $col, 'container' => $description);                
                                $col++;
                                if ($col == 3) {
                                    $col = 1;
                                }
                            }                                 
                        }
                    }
                }
            }
        }
        if (!count($possibleCharts)) {
            return '';
        }

        $remainingCharts = array();
        foreach ($possibleCharts as $chart) {
            $lowerLimit = 1;
            //echo 'Found container :' . $chart['container'] . PHP_EOL;
            $mChart = new chart($chart['data']);
            $name = 'c_' . md5($chart['name'] . rand(1,200));
            $mChart->setName($name);
            //$mChart->setTitle($chart['name']);
            $mChart->setLegend($chart['legend']);

            $remainingCharts[$chart['name']][$name] = array('dataForPost' => base64_encode(serialize($chart)), 'html' => $mChart->getHtml(true));                
        }
        
        if (isset($_GET['getNexFromMultiPage']) && !$fromRandom) {
            Log::debug('We should now add the charts into session because we`ll need to fetch then later', __FILE__, __LINE__);
            $_SESSION['remainingCharts'] = $remainingCharts;
        }
        
        return $remainingCharts;
    }
    
    public function getChartLabel($for)
    {
        $labels = array(
            'deal_size' => 'Average deal size US$m'
            , 'fees_per_deal' => 'Fees per deal, US$m'
            , 'percent_of_deal' => 'Fees as % of deal size'
        );
        
        if (!isset($labels[$for])) {
            return '';
        }
        
        return $labels[$for];
    }
    public function getNextFromMultiPage()
    { 
        Log::debug($_POST, __FILE__, __LINE__);
        if (isset($_GET['first'])) {
            //The first request should write data into session
            $this->getPossibleCharts();                
        }
        
        $remainingCharts = $_SESSION['remainingCharts'];
        $isLast = false;
        
        if (!count($remainingCharts)) {
            Log::debug('No charts available, Serving placeholder', __FILE__, __LINE__);
            return 'No relevant transactions matching your request were found.';
        }
        
        if (isset($_GET['cat'])) {
            $thisCharts = array(urldecode($_GET['cat']) => $_SESSION['remainingCharts'][urldecode($_GET['cat'])]);
            unset($_SESSION['remainingCharts'][urldecode($_GET['cat'])]);
        } else {
            $thisCharts = array_slice($remainingCharts, 0, 1);
            $_SESSION['remainingCharts'] = @array_slice($remainingCharts, 1);            
        }

        Log::debug(count($_SESSION['remainingCharts']), __FILE__, __LINE__);
        
        // if the c
        if (!count($_SESSION['remainingCharts'])) {
            $isLast = true;
            unset($_SESSION['remainingCharts']);
        }
        
        //var_dump($thisCharts);
        return $this->getMarkupForCharts($thisCharts, $isLast);
    }
    
    public function getDataIntoSession()
    {
        $this->getPossibleCharts();
    }
    
    public function getNumberOfRequests()
    {
        if (!isset($_SESSION['remainingCharts'])) {
            //The first request should write data into session
            $this->getPossibleCharts();
        }
        
        return count($_SESSION['remainingCharts'][1]);
    }
    
    public function getMarkupForCharts($charts, $isTheLastOne = false)
    {
        ob_start();
        $nbNext = isset($_SESSION['remainingCharts']) ? count($_SESSION['remainingCharts']) : 0;
        Log::debug('Getting markups for this group. Remaining groups: ' . count($_SESSION['remainingCharts']), __FILE__, __LINE__);
        if (isset($_GET['cat'])) {
            Log::debug(sprintf('Category "%s" is present in request. Serving', $_GET['cat']), __FILE__, __LINE__);
        }
        $status = 'more';
        $customChartsRemaining = '[]';
        if($isTheLastOne) {
            $status = 'done';
            
        } else {
            $customChartsRemaining = $customChartsRemaining = htmlentities(json_encode(array_keys($_SESSION['remainingCharts'])));
        }
            
        
        echo sprintf('<div id="cachedReqestNb_%d" class="requestNb">', $_GET['page']); 
        echo '<input type="hidden" id="nbChartsAvailableNext" value="' . $nbNext . '" />';
        echo '<input type="hidden" id="status" value="' . $status . '" />';
        echo '<input type="hidden" id="customChartsRemaining" value="' . $customChartsRemaining . '" />';
        //var_dump($charts);return '';
        //Log::debug(array_keys($_SESSION['remainingCharts']), __FILE__, __LINE__);
        foreach ($charts as $contentHolderId => $chartGroup) : ?>
        <div style="width: 100%; clear: both;" class="leaguechartsHeaderDiv">
            <p> <?php echo $contentHolderId?> </p>
            <?php foreach ($chartGroup as $divName => $chart) :?>
            <div id="<?php echo str_replace('c_', '', $divName)?>" style="width: 350px; margin-left: 30px; float:left; height: 300px;" class="chart">
                <input type="checkbox" name="download_pptx_fee_chart[]" style="float: right; z-index: 9999; position: relative;" value="<?php echo $chart['dataForPost']?>"/>
                <div id="<?php echo $divName?>">
                </div>
            </div>
            <?php echo $chart['html']?>
            <?php endforeach;?>    
        </div>
        <?php endforeach;  
        echo '</div>';
        
        $markup = ob_get_contents();
        ob_end_clean();
        return $markup;
    }
    
    
}

