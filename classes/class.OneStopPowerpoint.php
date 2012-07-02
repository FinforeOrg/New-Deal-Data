<?php

/**
 * OneStopPowerpoint class
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
class OneStopPowerpoint {
    
    /**
     * The data coming in $_POST
     * 
     * @var array 
     */
    private $_post;
    
    /**
     * The PHPPowerPoint object
     * 
     * @var PHPPowerPoint 
     */
    private $_ppObj;
    
    /**
     * The statistics object
     * 
     * @var statistics 
     */
    private $_statistics;
    
    /**
     * The saved searches object
     * 
     * @var SavedSearches 
     */
    private $_savedSearches;
    /**
     * Constructor
     * 
     * @param array $postData 
     */
    public function __construct($postData)
    {
        global $g_stat, $g_trans;
        
        ini_set('display_errors', 0);
        error_reporting(E_ALL);
        
        set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__) . '/PHPPowerpoint/');
        set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__));
        require_once 'PHPPowerPoint.php';
        require_once 'class.statistics.php';
        require_once 'class.transaction.php';
        require_once 'class.savedSearches.php';
        
        $this->_statistics = $g_stat;
        $this->_transaction = $g_trans;
        $this->_ppObj = new PHPPowerPoint();
        $this->setProperties();

        /**
         * remove the first slide as we don`t need it
         */
        $this->_ppObj->removeSlideByIndex(0);
        $this->_post = $postData;
        $this->_savedSearches = new SavedSearches();
                
    }
    
    /**
     * Shorthand for slide creation
     * 
     * @return PHPPowerPoint_Slide 
     */
    public function createSlide()
    {
        return $this->_ppObj->createSlide();
    }
    
    public function setProperties()
    {
        $this->_ppObj->getProperties()->setCreator("deal-data.com")
                                  ->setLastModifiedBy("Ionut MIHAI")
                                  ->setTitle("SpeedyCreds Office 2007 PPTX")
                                  ->setSubject("SpeedyCreds")
                                  ->setDescription("SpeedyCreds Office 2007 PPTX")
                                  ->setKeywords("deal-data.com")
                                  ->setCategory("Finance");
    }
    
    public function addCharts()
    {
        require_once 'class.leagueTableChart.php';
        
        $chartsPosted = $this->_post['download_pptx_chart'];
        if (count($chartsPosted)) {
           $slide = $this->createSlide();
        } else {
            return null;
        }
        
        switch (count($chartsPosted)):
            case 2:
                $xOffset = 50;
                foreach ($chartsPosted as $idx => $post) {
                    $post = unserialize(base64_decode($post));
                    $leagueTableChart = new leagueTableChart($post);
                    $series = @$leagueTableChart->getProperLabeledRankings();
                    if (!count($series)) {
                        continue;
                    }
                    $this->writeText(Util::cleanAndTranslate($post), $slide, $xOffset, 550, '00000000', 400);
                    $this->createChart($series, $this->getChartName($post), $slide, $xOffset, 150, 400, 400);
                    $xOffset += 400;
                }                
                break;
            case 1:
                    $post = $chartsPosted[0];
                    $post = unserialize(base64_decode($post));
                    $leagueTableChart = new leagueTableChart($post);
                    $series = @$leagueTableChart->getProperLabeledRankings();
                    $this->createChart($series, $this->getChartName($post), $slide, 50, 150, 800, 550);                    
                break;
        endswitch;
        
        $this->setSlideTitle($slide, 'League Table Position');
    }
    
    public function addVolumeCharts()
    {
        $volumeCharts = $this->_post['download_pptx_volume_chart'];
        if (count($volumeCharts)) {
           $slide = $this->createSlide();
        } else {
            return null;
        }
        
        switch (count($volumeCharts)):
            case 2:
                $xOffset = 50;
                foreach ($volumeCharts as $idx => $post) {
                    $data_arr = array();
                    $post = unserialize(base64_decode($post));
                    unset($_SESSION['lastGeneratedGraphData']);
                    @$this->_statistics->generate_issuance_data($post, $data_arr, $max_value, $num_values);   
                    $series = $this->createSeriesForVolumes($data_arr);
                    $this->createChart($series, $this->getVolumeChartName($post) . ' US$ bn', $slide, $xOffset, 150, 400, 550);
                    $xOffset += 400;
                }                
                break;
            case 1:
                    $post = $chartsPosted[0];
                    $post = unserialize(base64_decode($post));
                    unset($_SESSION['lastGeneratedGraphData']);
                    @$this->_statistics->generate_issuance_data($post, $data_arr, $max_value, $num_values);   
                    $series = $this->createSeriesForVolumes($data_arr);                    
                    $this->createChart($series, $this->getVolumeChartName($post) . ' US$ bn', $slide, 50, 150, 800, 550); 
                break;
        endswitch;
        
        $this->setSlideTitle($slide, 'Recent Transaction Volumes');
    }
    
    public function getVolumeChartName($post)
    {
        if (!@strlen($post['deal_subcat2_name']) && @strlen($post['deal_subcat1_name'])) {
            $post['deal_cat_name'] =  'All ' . $post['deal_subcat1_name']; 
            unset($post['deal_subcat2_name']);
            unset($post['deal_subcat1_name']);
        }    
        
        if ($post['deal_cat_name'] == 'M&A') {
            if (isset($post['deal_subcat1_name'])) {
                $post['deal_subcat2_name'] = $post['deal_subcat1_name'] . ' M&A';
            } else {
                $post['deal_cat_name'] = 'All M&A';
            }            
        }
        //var_dump($post);
        unset($post['month_division']);
        unset($post['month_division_list']);
        
        if (@$post['ranking_criteria'] == 'total_deal_value' || @$post['ranking_criteria'] == 'total_adjusted_value') {
            $post[] = 'US$';
        }
        //if ($post[0])
        return (join(', ', $post));
    }
    
    public function createSeriesForVolumes($volumeInfo)
    {
        if (!count($volumeInfo)) {
            return $volumeInfo;
        }
        $newData = array();
        foreach ($volumeInfo as $volume) {
            $newData[$volume['short_name']] = $volume['value'];
        }
        
        return $newData;
    }
    public function addCredentialSlide()
    { 
        define('IMAGESDIR',"uploaded_img/logo/thumbnails/");
        
        $this->_savedSearches->loadIntoPostByParams(base64_decode($this->_post['download_pptx_credential_slide'][0]));
        $companyId = $_SESSION['company_id'];
        $tombstones = @$this->_transaction->getTombstonesForFirm($companyId);
        
        $images = $this->getImagesForTombstones($tombstones);
        
        $extra = sizeOf($images) % 18 != 0 ? 1 : 0;
        $nrSlides = floor(sizeOf($images)/18)+$extra;
        $start = 0;
        $length = 18;
        
        //var_dump($nrSlides);
        for ($i=1;$i<=$nrSlides;$i++) {
            $currentSlide = $this->_ppObj->createSlide();
            $currImagesArray = array_slice($images, $start, $length, true);
            
            $start += $length;
            $offsetX = 40;
            $offsetY = 150;
            foreach ($currImagesArray as $key=>$image) {
                if ($key == 0) {
                    continue;
                }
                $shape = $currentSlide->createDrawingShape();

                $shape->setPath(IMAGESDIR . "itemBk2.png");
                $shape->setWidth(145);
                $shape->setOffsetX($offsetX);
                $shape->setOffsetY($offsetY);
                if (preg_match("/\s+/",$image['logo']))  {
                    $newName =  preg_replace("/\s+/","","$image[logo]");
                    if (!@copy(IMAGESDIR . "$image[logo]", IMAGESDIR.$newName )){
                        $image['logo'] = "Placeholder1.jpg";
                    } else {
                      $image['logo'] =  $newName;  
                    }  
                }

                if ($image['logo'] == "" || !file_exists(IMAGESDIR . "$image[logo]")) {
                   $image['logo'] = "Placeholder2.jpg"; 
                }
                if (file_exists(IMAGESDIR . "$image[logo]" )) {
                    $shape = $currentSlide->createDrawingShape();
                    $shape->setPath(IMAGESDIR . "$image[logo]" );
                    $shape->setHeight(95);
                    $shape->setWidth(70);
                    $shape->setOffsetX($offsetX+35);
                    $shape->setOffsetY($offsetY+25); 
                    $offsetX += 145;               
                } 

                if ($key % 6 == 0 and $key != 0) {
                   $offsetY += 170;
                   $offsetX = 40;
                }
            }

            $offsetX = 50;
            $offsetY = 240;

            foreach ($currImagesArray as $key=>$image) {
                $shape = $currentSlide->createRichTextShape();
                $shape->setWidth(125);
                $shape->setHeight(50);
                $shape->setOffsetX($offsetX);
                $shape->setOffsetY($offsetY);
                $shape->getActiveParagraph()->getAlignment()->setHorizontal( PHPPowerPoint_Style_Alignment::HORIZONTAL_CENTER );
                $textRun = $shape->createTextRun($image['text']);

                $textRun->getFont()->setSize(8);
                $textRun->getFont()->setName('Calibri');
                $textRun->getFont()->setColor( new PHPPowerPoint_Style_Color( '00000000' ) );
                   $offsetX += 145;
                if ($key % 6 == 0 and $key != 0) {
                   $offsetY += 170;
                   $offsetX = 50;
                }
            }

            $this->setSlideTitle($currentSlide, "Our Credentials in the Sector");
        }        
    }
    
    
    public function addRecentDealsSlide()
    {
        $slide = $this->createSlide();
        
        $headers = array(
            'Rank', 'Company', 'Country', 'Industry', 'Deal Type', 'Date', 'Size $bn'
        );

        
        $post = unserialize(base64_decode($this->_post['download_pptx_top_ten'][0]));
        $data = array();
        @$this->_transaction->front_deal_search_paged($post, 0, 10, $data, $count);
        
        if (!count($data)) {
            return null;
        }
        $label = 'Top 10 deals: ';
        if (isset($post['country']) && strlen($post['country'])) {
            $label .= sprintf("%s, %s, %s, %s", $post['country'], $post['industry'], $post['deal_cat_name'], $post['year'] );
        } else {
            $label .= sprintf("%s, %s, %s, %s", $post['region'], $post['sector'], $post['deal_cat_name'], $post['year'] );
        }
        $this->writeText($label,$slide, 60, 170 );
        
        $data = $this->parseRecentDealsValues($data);
        $this->createTable($headers, $data, $slide, 60, 200, 800, 400);
        $this->setSlideTitle($slide, 'Top 10 Recent Deals in The Sector');
    }
    
    
    public function createTable($headers, $data, &$slide, $posX, $posY, $w, $h) {
        $shape = $slide->createTableShape(sizeOf($headers));
        $shape->setHeight($h);
        $shape->setWidth($w);
        $shape->setOffsetX($posX);
        $shape->setOffsetY($posY);   
        $row = $shape->createRow()->setHeight(16);
        foreach ($headers as $header) {
            $row->getFill()->setFillType(PHPPowerPoint_Style_Fill::FILL_SOLID)
                           ->setStartColor(new PHPPowerPoint_Style_Color('FFCCCCCC'))
                           ->setEndColor(new PHPPowerPoint_Style_Color('FFCCCCCC'));
            $cell = $row->nextCell();
            $cell->createTextRun($header)->getFont()->setBold(true)
                                                    ->setSize(12);
            switch ($header) {
                case 'Rank':
                    $cell->setWidth(70);
                    break;
                case 'Industry':
                    $cell->setWidth(200);
                    break;
                case 'Target':
                    $cell->setWidth(250);
                    break;
            }
            
            $cell->getBorders()->getBottom()->setColor(new PHPPowerPoint_Style_Color('FFFFFFFF'))->setLineWidth(3);
            $cell->getBorders()->getTop()->setColor(new PHPPowerPoint_Style_Color('FFFFFFFF'));    
            $cell->getBorders()->getLeft()->setColor(new PHPPowerPoint_Style_Color('FFCCCCCC'));    
            $cell->getBorders()->getRight()->setColor(new PHPPowerPoint_Style_Color('FFCCCCCC'));    
        }

        
        foreach ($data as $key => $row) {
                $tableRow = $shape->createRow();
                $tableRow->setHeight(20);
                $tableRow->getFill()->setFillType(PHPPowerPoint_Style_Fill::FILL_SOLID)
                               ->setStartColor(new PHPPowerPoint_Style_Color('FFE4E4E4'))
                               ->setEndColor(new PHPPowerPoint_Style_Color('FFE4E4E4'));

                foreach ($row as $column) {
                    $cell = $tableRow->nextCell();
                    $cell->createTextRun($column)->getFont()->setBold(false);

                    $cell->getBorders()->getBottom()->setColor(new PHPPowerPoint_Style_Color('FF999999'))->setLineWidth(1);
                    $cell->getBorders()->getTop()->setColor(new PHPPowerPoint_Style_Color('FF999999'));    
                    $cell->getBorders()->getLeft()->setColor(new PHPPowerPoint_Style_Color('FF999999'));    
                    $cell->getBorders()->getRight()->setColor(new PHPPowerPoint_Style_Color('FF999999'));                     
                }
        } 
        
        return $shape;
    }
    
    public function addCrossSellingSlide()
    {
        //var_dump($this->_post);
        
        $w = 800;
        $h = 400;
        $posX = 50;
        $posY = 160;
        $slide = $this->createSlide();
        foreach ($this->_post['download_pptx_cross'] as $crossTablePost) {
            $transactions = array();
            
            $post = unserialize(base64_decode($crossTablePost));
            ;
            unset($_POST);
            $_POST = $post;
            $_POST['myaction'] = 'filter';
            
            //var_dump($_POST);
            $label = sprintf('Top 5 %s deals at your firm: %s', $post['deal_cat_name'], join(', ', $post) );
            $this->writeText($label, $slide, 50, ($posY == 160) ? 130 : 430);            
            $c = @$this->_transaction->getTombstonesForFirm(@$_SESSION['company_id'], 0,5);
            //var_dump($c);
            if (empty($c)) {
                continue;
            }

            foreach ($c as $deal) {
                $details = array();
                @$this->_transaction->front_get_deal_detail($deal['transaction_id'], $details, $qqq);
                $transactions[] = $details;
            }

            if ('M&A' == $_POST['deal_cat_name']) {
                $headers = array(
                    'Buyer', 'Target', 'Date', 'Size $bn'
                );

                $data = $this->getCrossSellingDataFromTransactions($transactions, true);
            } else {
                $headers = array(
                    'Company', 'Date', 'Size $bn'
                );

                $data = $this->getCrossSellingDataFromTransactions($transactions);                
            }
            
            $this->createTable($headers, $data, $slide, $posX, $posY, $w, $h);
            $posY += 300; 
        }
        
        $this->setSlideTitle($slide, 'Related Transactions of Our Firm');
    }
    
    public function getCrossSellingDataFromTransactions($transactions, $isMA = false)
    {
        $newData = array();
        if (!count($transactions)) {
            return $newData;
        }
        
        foreach ($transactions as $transaction) {
            if ($isMA) {
                $newData[] = array(
                    $transaction['company_name'],
                    $transaction['target_company_name'],
                    date('M Y', strtotime($transaction['date_of_deal'])),
                    number_format((double) $transaction['value_in_billion'], 2)
                );                
            } else {
                $newData[] = array(
                    $transaction['company_name'],
                    date('M Y', strtotime($transaction['date_of_deal'])),
                    number_format((double) $transaction['value_in_billion'], 2)
                );                  
            }
        }
        
        return $newData;
    }
    
    public function parseRecentDealsValues($vals)
    {
        $newData = array();
        
        if (!count($vals)) {
            return $newData;
        }
        
        foreach ($vals as $idx => $val) {
            if (@$val['deal_cat_name'] == 'M&A')
                $cat = $val['deal_subcat1_name'] . ' M&A';
            else 
                $cat = (@$val['deal_subcat2_name'] == 'n/a') ? @$val['deal_subcat1_name'] : @$val['deal_subcat2_name']; 

            $newData[] = array(
                ($idx + 1),
                $val['company_name'],
                $val['hq_country'],
                $val['industry'],
                $cat,
                date("M Y", strtotime($val['date_of_deal'])),
                number_format((double) $val['value_in_billion'], 2)
            );            
        }
        return $newData;
    }
    
    public function setSlideTitle(&$slide, $title)
    {
            $offsetX = 40;
            $offsetY = 40;
            $shape = $slide->createRichTextShape();
            $shape->setWidth(800);
            $shape->setHeight(60);
            $shape->setOffsetX($offsetX);
            $shape->setOffsetY($offsetY);
            $shape->getActiveParagraph()->getAlignment()->setHorizontal( PHPPowerPoint_Style_Alignment::HORIZONTAL_CENTER );
            $textRun = $shape->createTextRun($title);
            $textRun->getFont()->setSize(44);
            $textRun->getFont()->setName('Calibri');
            $textRun->getFont()->setColor( new PHPPowerPoint_Style_Color( '00000000' ) );        
    }
    
    public function writeText($text, &$slide, $posX, $posY, $color = '00000000', $width = 800, $textSize = 13, $font = 'Calibri', $alignment = PHPPowerPoint_Style_Alignment::HORIZONTAL_LEFT)
    {
            $shape = $slide->createRichTextShape();
            $shape->setWidth($width);
            $shape->setHeight($textSize + 5);
            $shape->setOffsetX($posX);
            $shape->setOffsetY($posY);
            $shape->getActiveParagraph()->getAlignment()->setHorizontal( $alignment );
            $textRun = $shape->createTextRun($text);
            $textRun->getFont()->setSize($textSize);
            $textRun->getFont()->setName($font);
            $textRun->getFont()->setColor( new PHPPowerPoint_Style_Color( $color ) );         
    }
    
    public function getImagesForTombstones($tombstones)
    {
        $nrTombstones = count($tombstones); 
        for($i=0; $i<$nrTombstones; $i++){
            $deal_cat_name = '';
            $firm = @$this->_transaction->get_tombstone_from_deal_id($tombstones[$i]['transaction_id'], true);
            $deal_type = $firm['deal_cat_name'];
            $deal_subcat2_name = $firm['deal_subcat2_name'];
            $deal_subcat1_name = $firm['deal_subcat1_name'];
            //if sub cat 2 name is there then we do not show sub cat 1 name
            if(($deal_subcat2_name!="")&&($deal_subcat2_name!="n/a")){
                $deal_type.=" : ".$deal_subcat2_name;
            }else{
                //check sub cat 1 name
                if(($deal_subcat1_name!="")&&($deal_subcat1_name!="n/a")){
                    //it must not be same as deal cat name
                    if($deal_subcat1_name!=$deal_cat_name){
                        $deal_type.=" : ".$deal_subcat1_name;
                    }
                }
            }
            $date = date("F Y",strtotime($firm['date_of_deal']));
            $deal_value = round($firm['value_in_billion']*1000,2);

            $logos = unserialize($firm['logos']);
            if (is_array($logos) && sizeOf($logos)) {
                if (isset($logos[$_POST[$firm['deal_id']]])) {
                    $logo = $logos[$_POST[$firm['deal_id']]]['fileName'];
                } else {
                   $logo = $firm['logo']; 
                }
            } else {
                $logo = $firm['logo'];
            }

            if($firm['value_in_billion'] == 0){
                $images[$i+1] = array('logo'=>$logo,'text'=>$deal_type ."\n". "Not disclosed" ."\n" . $date, "name"=>$firm['company_name']) ;
            }else{
                $images[$i+1] = array('logo'=>$logo,'text'=>$deal_type ."\n". 'US $ '.$deal_value . " million" ."\n" . $date, "name"=>$firm['company_name']) ;
            }
        } 
        
        return $images;
    }
    public function getChartName($post) {
        $chartName = 'Top 5';
        
        if ('bank' == $post['partner_type']) {
            $chartName .= ' Banks ';
        }

        if ('law firm' == $post['partner_type']) {
            $chartName .= ' Law Firms ';
        }   
        
        if ('num_deals' == $post['ranking_criteria']) {
            $chartName .= ' based on number of deals.';
        }
        
        if ('total_deal_value' == $post['ranking_criteria']) {
            $chartName .= ' based on total deal value.';
        }
        
        if ('total_adjusted_deal_value' == $post['ranking_criteria']) {
            $chartName .= ' based on total adjusted deal value.';
        }
        
        if ('num_deals' != $post['ranking_criteria']) {
            $chartName .= ' (bn $)';
        }
        
        return $chartName;
    }
    
    public function createChart($series, $title, $slide, $posX, $posY, $width, $heigth)
    {
        $barChart = new PHPPowerPoint_Shape_Chart_Type_Bar();
        $series = new PHPPowerPoint_Shape_Chart_Series('', $series) ;
        $series->getFill()->setFillType(PHPPowerPoint_Style_Fill::FILL_SOLID)
                   ->setStartColor( new PHPPowerPoint_Style_Color('FFCCCCCC'))
                   ->setEndColor(new PHPPowerPoint_Style_Color('FFCCCCCC'));

        $barChart->addSeries($series);

        $shape = $slide->createChartShape();
        $shape->setName($title)
              ->setResizeProportional(false)
              ->setHeight($heigth)
              ->setWidth($width)
              ->setOffsetX($posX)
              ->setOffsetY($posY)
              ->getLegend()->setVisible(false);

        $shape->getTitle()->setWidth($width)->setText($title);
        $shape->getPlotArea()->setType($barChart);
        $shape->getView3D()->setRightAngleAxes(true);
        $shape->getView3D()->setRotationX(0);
        $shape->getView3D()->setRotationY(0);        
    }
    
    public function download()
    {
        //echo "<pre>";
        //var_dump($_POST);
        $this->addCharts();
        $this->addCredentialSlide();
        $this->addVolumeCharts();
        $this->addRecentDealsSlide();
        $this->addCrossSellingSlide();
        
        
        $this->serveFile();
        exit();
    }
    
    public function setHeaders($fileName)
    {
        header("Pragma: public"); 
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private", false); 
        header("Content-Type: application/vnd.openxmlformats-officedocument.presentationml.presentation");
        header("Content-Disposition: attachment; filename=\"".basename($fileName)."\";" );
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: ".filesize($fileName));
    }
    
    public function addFeePageCharts()
    {
        $chartsPosted = $this->_post['download_pptx_fee_chart'];

        if (!count($chartsPosted)) {
            return false;
        }

        foreach ($chartsPosted as $post) {
            
            $slide = $this->createSlide();

            $post = unserialize(base64_decode($post));
            
            $series = $post['data'];

            foreach($series as $lbl => $val) {
                $series[(string) $lbl] = (float) $val;
            }
            
            $this->createChart($series, $post['name'], $slide, 100, 150, 700, 500);
            $this->writeText($post['legend'], $slide, 350, 650);
            $this->setSlideTitle($slide, 'Fee data');
            
        }
        
        
    }

    public function downloadFeeFile()
    {
        $this->addFeePageCharts();
        $this->serveFile();
        exit();
    }
    
    public function serveFile() 
    {
        $fileName = date('YmdHis') . rand(1, 999) . '.pptx';
        $objWriter = PHPPowerPoint_IOFactory::createWriter($this->_ppObj, 'PowerPoint2007');
        $objWriter->save($fileName);
        $this->setHeaders($fileName);

        readfile("$fileName");
        @unlink($fileName);  
    }
    
}

