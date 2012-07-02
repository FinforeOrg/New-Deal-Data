<?php

/**
 * PHPPowerPoint_Shape_Chart_Type_Bar
 *
 * @category   PHPPowerPoint
 * @package    PHPPowerPoint_Shape_Chart_Type
 * @copyright  Copyright (c) 2011 Ionut MIHAI
 */
class PHPPowerPoint_Shape_Chart_Type_Bar extends PHPPowerPoint_Shape_Chart_Type implements PHPPowerPoint_IComparable
{
	/**
	 * Data
	 *
	 * @var array
	 */
	private $_data = array();
    
    /**
     * Create a new PHPPowerPoint_Shape_Type_Bar3D instance
     */
    public function __construct()
    {
    }

	/**
	 * Get Data
	 *
	 * @return array
	 */
	public function getData() {
	        return $this->_data;
	}
	
	/**
	 * Set Data
	 *
	 * @param array $value Array of PHPPowerPoint_Shape_Chart_Series
	 * @return PHPPowerPoint_Shape_Type_Bar3D
	 */
	public function setData($value = array()) {
	        $this->_data = $value;
	        return $this;
	}
	
	/**
	 * Add Series
	 *
	 * @param PHPPowerPoint_Shape_Chart_Series $value
	 * @return PHPPowerPoint_Shape_Type_Bar3D
	 */
	public function addSeries(PHPPowerPoint_Shape_Chart_Series $value) {
	        $this->_data[] = $value;
	        return $this;
	}
	
	/**
	 * Get hash code
	 *
	 * @return string	Hash code
	 */
	public function getHashCode() {
		$hash = '';
		foreach ($this->_data as $series) {
			$hash .= $series->getHashCode();
		}
		
    	return md5(
    		  $hash
    		. __CLASS__
    	);
    }

    /**
     * Hash index
     *
     * @var string
     */
    private $_hashIndex;

	/**
	 * Get hash index
	 *
	 * Note that this index may vary during script execution! Only reliable moment is
	 * while doing a write of a workbook and when changes are not allowed.
	 *
	 * @return string	Hash index
	 */
	public function getHashIndex() {
		return $this->_hashIndex;
	}

	/**
	 * Set hash index
	 *
	 * Note that this index may vary during script execution! Only reliable moment is
	 * while doing a write of a workbook and when changes are not allowed.
	 *
	 * @param string	$value	Hash index
	 */
	public function setHashIndex($value) {
		$this->_hashIndex = $value;
	}

	/**
	 * Implement PHP __clone to create a deep clone, not just a shallow copy.
	 */
	public function __clone() {
		$vars = get_object_vars($this);
		foreach ($vars as $key => $value) {
			if (is_object($value)) {
				$this->$key = clone $value;
			} else {
				$this->$key = $value;
			}
		}
	}
}