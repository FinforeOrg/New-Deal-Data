<?php
/**
 * Class used to generate the new type of charts
 *
 * @author Ionut Mihai
 */
//error_reporting(E_ALL);
class leagueTableChart {
    public $name = 'chart1';
    public $data = '';
    public $pointLabels = '';
    public $values = '';
    public $labels = '';
    public $statData = array();
    public $statistcs = null;
    public $title = '';
    public $properLabeledRankings = array();
    
        
    public function __construct($data = null) {
        global $g_stat;
        if (is_null($data)) {
            if (isset($_POST)) {
                $this->data = $_POST;
            } else {
                die('Please provide data for the chart. Either to the constructor or POST');
            }            
        } else {
            $this->data = $data;
        }
        
        if (!isset($g_stat)) {
            require_once(dirname(__FILE__) . "/class.statistics.php");
        }

        $this->statistics = $g_stat;
    }
    
    public function setTitle($title) {
        $this->title = $title;
    }
    
    public function setName($name) {
        $this->name = $name;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function getStatData() {
        $statData = array();
        $maxValue = 0;
        $statCount = 0;
        $this->statistics->generate_ranking($this->data, $statData, $maxValue, $statCount);
        
        $this->statData = $statData;
        return $statData;
    }
    
    public function getNoDataMarkup() {
        return '<div style="width: 302px; height: 45px; position: absolute; left: 50px; top: 30px;" class="ui-widget-shadow ui-corner-all"></div></div> <div class="ui-widget ui-widget-content ui-corner-all" style="position: absolute; width: 280px; height: 20px; left: 50px; top: 30px; padding: 10px;"> No transactions matching your request were found. </div>';
    }

    public function setData($data) {
        $this->data = $data;
    }
    
    public function getLabels() {
        return $this->labels;
    }
    
    public function getPointLabels() {
        return $this->pointLabels;
    }
    
    public function getValues() {
        return $this->values;
    }
    
    public function parseData() {
        if (!sizeOf($this->statData)) {
            return false;
        }
        
        foreach ($this->statData as $key => $values) {
            if (!strlen($values['short_name'])) {
                if (strlen($values['name']) > 6) {
                    if (substr_count($values['name'],' ') > 0) {
                        $words = explode(' ', $values['name']);
                        $index = '';
                        foreach($words as $word) {
                             $index .= strtoupper(substr($word,0,1));
                        }
                    } else {
                        $index = substr($values['name'], 0, 1);
                    } 
                } else {
                   $index =  $values['name'];
                }
            } else {
               $index = $values['short_name'];
            }
            if (isset($newData[$index])) {
                $index .= ' ';
            }
           $newData[$index] =  (float) $values['value']; 
        }
        
        $this->properLabeledRankings = $newData;
        
        $labels = array_keys($newData);
        $this->values = join(',' , array_values($newData));
        
        $pLabels =   array_values($newData);
        foreach($pLabels as $key2=>$pointLabel) {
            if ($pointLabel != 0) {
                if ($_POST['ranking_criteria'] != 'num_deals') {
                   $pointLabels[] = "'$" . $pointLabel . "bn'" ; 
                } else {
                   $pointLabels[] =  "'$pointLabel'";
                }                 
            }
        }  
        
        foreach($labels as $label) {
            $nlabels[] = "'$label'"; 
        }
        
        $this->labels = join(',', $nlabels);            
        $this->pointLabels = join(',', $pointLabels);        
    }
    
    public function getProperLabeledRankings() {
        $this->getStatData();
        $this->parseData();
        
        return $this->properLabeledRankings;
    }
    public function getHtml($return = false) {
        $this->getStatData();
        $this->parseData();
        
        $markup = <<<MARKUP
        <script class="code" type="text/javascript">
        
        $(document).ready(function() {
            $.jqplot.config.enablePlugins = true;
            line = [#VALUES#]; 
            plot = $.jqplot('#CHARTNAME#', [line], {
                title:'#NAME#',
                seriesDefaults: {
                    showMarker:false, 
                    pointLabels:{location:'n', ypadding:3, labels:[#POINTLABELS#]},
                    renderer:$.jqplot.BarRenderer,
                    color:'#7b7b7b'
                },
                grid: {
                    background: '#ffffff',
                    borderWidth: 0.0,
                    drawGridLines: false ,
                    shadow: false
                },
                markerOptions: {
                    shadow: false  
                },
                axesDefaults:{
                    tickOptions: {
                        showGridline: false,
                        showMark: false
                    }
                },
                axes:{
                    xaxis:{
                        renderer:$.jqplot.CategoryAxisRenderer,
                        ticks:[#LABELS#],
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
            try {
                plot.redraw();
                generateCaption();
            } catch (e) {
                //do nothing
            }
        });

        </script>
MARKUP;
        $markup = str_replace(array(
            '#CHARTNAME#',
            '#POINTLABELS#',
            '#VALUES#',
            '#LABELS#',
            '#NAME#'
        ), array(
            $this->name,
            $this->pointLabels,
            $this->values,
            $this->labels,
            $this->title
        ), $markup);
        
        if ($return) {
            return $markup;
        } else {
            echo $markup;
        }
    }
}

