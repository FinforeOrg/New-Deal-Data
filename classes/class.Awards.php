<?php
require_once("classes/class.company.php");
require_once("classes/class.transaction.php");
require_once("classes/class.account.php");
require_once("classes/class.country.php");
require_once("classes/class.account.php");

class Awards 
{
    public $transaction = null;
    public $company = null;
    
    public function __construct()
    {
        $this->transaction = new transaction();
        $this->company = new company();
    }
    
    public function query($q) {
        //echo "<pre> $q </pre><br />" ;
        return mysql_query($q);
    }
    
    public function getDistinctSectors()
    {   
        $sectors = array();
        $q = "SELECT DISTINCT aws.sector_id as id, coalesce( im.sector, 'Any Sector' ) as label
                FROM `%s` aws
                LEFT JOIN %s im ON aws.sector_id = im.id
             ";
        $q = sprintf($q, TP . 'awards_sectors', TP . 'sector_industry_master');
        if (!$res = $this->query($q)) {
            return $sectors;
        }
        
        while ($row = mysql_fetch_assoc($res)) {
            $sectors[] = $row;
        }
        
        return $sectors;
    }

    public function getDistinctRegions()
    {   
        $regions = array();
        $q = " SELECT DISTINCT awr.region_id as id, coalesce( rm.name, 'Any  Region' ) as label
                FROM `%s` awr
                LEFT JOIN %s rm ON awr.region_id = rm.id
             ";
        $q = sprintf($q, TP . 'awards_regions', TP . 'region_master');
        if (!$res = $this->query($q)) {
            return $regions;
        }
        
        while ($row = mysql_fetch_assoc($res)) {
            $regions[] = $row;
        }
        
        return $regions;
    }  
    
    public function getDistinctCategories()
    {
        $categories = array();
        $q = " SELECT DISTINCT award_type FROM `tombstone_awards` aw";
        if (!$res = $this->query($q)) {
            return $categories;
        }
        
        while ($row = mysql_fetch_assoc($res)) {
            $categories[] = $row['award_type'];
        }
        
        return $categories;        
    }
    
    public function getCompanyById($id)
    {
        $q = sprintf('SELECT * FROM ' . TP . 'company WHERE company_id = %d', $id);
        if (!$res = $this->query($q)) {
            return false;
        }
        
        return mysql_fetch_assoc($res);
    }
    
    public function getAwards()
    {
        $dealType = isset($_POST['deal_cat_name']) ? $_POST['deal_cat_name'] : '';
        $region = isset($_POST['region']) ? $_POST['region'] : '';
        $sector = isset($_POST['sector']) ? $_POST['sector'] : '';
        $companyId = isset($_SESSION['company_id']) ? $_SESSION['company_id'] : '';
        
        $deals = array();
        $q = 'SELECT aw.id, aw.winner, aw.year, aw.detail, aw.award_type, aw.award, ar.region_id, aws.sector_id, rm.name as regionName
            FROM `%s` aw
            LEFT JOIN %s ar ON aw.id = ar.award_id
            LEFT JOIN %s aws ON aw.id = aws.award_id
            LEFT JOIN %s awc ON aw.id = awc.award_id
            LEFT JOIN %s rm on ar.region_id = rm.id
            WHERE awc.company_id = %d 
            %s
            %s
            %s
            GROUP BY aw.id
            ORDER BY year DESC
            LIMIT 0 , 30';
        
        $dealTypeFilter = '';
        if ($dealType != 'All Deals' && !empty($dealType)) {
            $dealTypeFilter = sprintf("AND award_type = '%s'", $dealType);
        }
        
        $sectorFilter = '';
        if ($sector != 0) {
            $sectorFilter = sprintf("AND sector_id = '%d'", $sector);
            
        }

        $regionFilter = '';
        if ($region != 0) {
            $regionFilter = sprintf("AND region_id = '%d'", $region);
            
        }        
        $q = sprintf($q, TP . 'awards', TP . 'awards_regions', TP . 'awards_sectors', TP . 'awards_companies', TP . 'region_master', $companyId, $sectorFilter, $regionFilter, $dealTypeFilter);

        if (!$res = $this->query($q)) {
            return $deals;
        }
        
        while ($row = mysql_fetch_assoc($res)) {
            switch ($row['award']) {
                case 'IFR':
                    $pic = 'images/IFRlogo.png';
                    break;
                case 'Legal Week':
                    $pic = 'images/LegalWeeklogo.png';
                    break;
                case 'The Lawyer':
                default: //intentionally ommited breal
                    $pic = 'images/TheLawyerlogo.jpg';
                    break;
            }
            $row['pic'] = $pic;
            $deals[]  = $row;
        }
         return $deals;
    }
}
?>
