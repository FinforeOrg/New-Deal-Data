<?php
  class chart {
      public $data;
      public $labelRegex = '%s';
      public $name = 'chart1';
      public $title = '';
      public $legend = '';
      
      function __construct($data) {
          if (is_array($data)) {
            $this->data = $data;             
          }
      }
      
      public function setData($data) {
          if (is_array($data) && sizeOf($data)) {
            $this->data = $data;             
          }          
      }
      public function setLegend($legend) 
      {
          $this->legend = $legend;
      }
      
      public function getLegend()
      {
          return $this->legend;
      }
      public function setName($name)
      {
          $this->name = $name;
      }
      
      public function setTitle($title)
      {
          $this->title = $title;
      }
      
      public function getTitle()
      {
          return $this->title;
      }      
      public function getName(){
          return $this->name;
      }
      
      public function getLabels() {
          $labels =  array_keys($this->data);
          $nLabels = $this->escapeLabels($labels);
          return join(',', $nLabels);
      }
      
      public function getPointLabels() {
        $labels = array_values($this->data); 
        $nLabels = $this->escapeLabels($labels, true);
        return join(',', $nLabels); 
      }
      
      public function getValues() {
          return join(',' , array_values($this->data)); 
      }
      
      public function escapeLabels($labels, $point = false) {
          foreach($labels as $label) {
            if ($point) {
               $nLabels[] = "'"  . sprintf($this->labelRegex, $label)  . "'"; 
            } else {
               $nLabels[] = "'"  . $label . "'";  
            } 
                
          }
          return  $nLabels;         
      }
      
      public function getHtml($return = false) {?>
            <?php 
            if ($return) {
                ob_start();
            }
            $noDataHtml = '<div style="width: 302px; height: 45px; position: absolute; left: 50px; top: 30px;" class="ui-widget-shadow ui-corner-all"></div></div> <div class="ui-widget ui-widget-content ui-corner-all" style="position: absolute; width: 280px; height: 20px; left: 50px; top: 30px; padding: 10px;"> No transactions matching your request were found. </div>';
            if (!count($this->data)) { ?>
            <script type="text/javascript">
                $(document).ready(function() { 
                    $('#<?php echo $this->getName()?>').html(
                    '<?php echo $noDataHtml?>'
                    );
                });
            </script>
            <?php } else {?>      
              <script class="code" type="text/javascript">
                
                $(document).ready(function() {
				<?php
				/*************
				sng:18/oct/2011
				adjust the width of the chart, based on number of data points and some extra padding
				****************/
				?>
				var chart_div_width = '<?php echo count($this->data)*50+10;?>';
				$('#<?php echo $this->getName()?>').width(chart_div_width);
				
				
                    $.jqplot.config.enablePlugins = true;   
                    line = [<?php echo $this->getValues()?>]; 
                    plot = $.jqplot('<?php echo $this->getName()?>', [line], {
                        title:'<?php echo $this->getTitle()?>',
                        seriesDefaults: {
                            showMarker:false, 
                            pointLabels:{location:'n', ypadding:3, labels:[<?php echo $this->getPointLabels()?>]},
                            renderer:$.jqplot.BarRenderer,
                            color:'#7b7b7b',
                            rendererOptions: {
                                barWidth: 30,
                                barPadding: 8
                            }                            
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
                                rendererOptions: {
                                    barWidth: 30
                                },
                                ticks:[<?php echo $this->getLabels()?>],
                                tickOptions:{
                                    showGridline:false,
                                    mark: 'cross'
                                },
                                pad: 1.8
                                                  
                            }, 
                            yaxis:{
                                min:0, 
                                tickOptions:{
                                    showGridline:false
                                },
                            }
                        },
                        highlighter: {sizeAdjust: 7.5},
                        cursor: {show: false}
                        <?php if ('' != $this->getLegend()) :?>
                        ,
                        legend: {
                            show: true,
                            location: 's',
                            placement: 'outsideGrid',
                            labels: ['<?php echo $this->getLegend()?>']
                        }
                        <?php endif?>
                    });  
                });
                try {
                    plot.redraw();
                    generateCaption();                
                } catch (e) {
                    //nothing to do
                }

                </script>          
              
              <?php 
                }
                if ($return) {
                    $ret  = ob_get_contents();
                    ob_end_clean();
                    return $ret;
                }              
              }
      }
?>
