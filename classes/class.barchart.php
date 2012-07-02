<?php
/****
sng:3/april/2010
This is a simple code that create a bar chart image.

Caption strip ht, graph ht, legend margin, legent row height is specified, since a chart can be as tall as needed
We specify left margin, the total chart image width (as width may be limited) and calculate chart width
We specify gap between the bars and specify a default value for bar width but it is adjusted if needed
****/
class barchart{

	private $chart_caption;
	private $caption_ht;
	private $graph_height;
	
	private $left_margin;
	
	private $img_width;
	
	private $legend_margin;
	private $legend_row_ht;
	
	private $background_colour_r;
	private $background_colour_g;
	private $background_colour_b;
	
	private $axis_colour_r;
	private $axis_colour_g;
	private $axis_colour_b;
	
	private $caption_colour_r;
	private $caption_colour_g;
	private $caption_colour_b;
	
	private $bar_colour_r;
	private $bar_colour_g;
	private $bar_colour_b;
	
	private $y_axis_txt_colour_r;
	private $y_axis_txt_colour_g;
	private $y_axis_txt_colour_b;
	
	private $value_line_colour_r;
	private $value_line_colour_g;
	private $value_line_colour_b;
	
	private $font;
	private $caption_font_size;
	private $legend_code_font_size;
	//used to show legend code under each bar
	
	private $footer_font_size;
	
	private $bar_gap;
	private $bar_width;
	
	private $value_line_gap;
	
	private $show_caption;
	private $show_legend_detail;
	private $show_scale;
	
	private $stat_value_label_format;
	/**
	how the stat numbers are to be displayed
	%n just show the number
	$%nbn show it like $3bn
	**/
	
	public function __construct(){
		$this->chart_caption = "Chart";
		$this->caption_ht = 70;
		$this->graph_height = 400;
		$this->left_margin = 40;
		
		$this->img_width = 600;
		
		$this->legend_margin = 50;
		$this->legend_row_ht = 30;
		
		$this->background_colour_r = 238;
		$this->background_colour_g = 238;
		$this->background_colour_b = 235;
		
		$this->bar_colour_r = 123;
		$this->bar_colour_g = 123;
		$this->bar_colour_b = 123;
		
		$this->axis_colour_r = 157;
		$this->axis_colour_g = 156;
		$this->axis_colour_b = 155;
		
		$this->caption_colour_r = 0;
		$this->caption_colour_g = 0;
		$this->caption_colour_b = 0;
		
		$this->y_axis_txt_colour_r = 107;
		$this->y_axis_txt_colour_g = 107;
		$this->y_axis_txt_colour_b = 108;
		
		$this->value_line_colour_r = 220;
		$this->value_line_colour_g = 220;
		$this->value_line_colour_b = 220;
		
		$this->font = "font/tahoma.ttf";
		$this->caption_font_size = 14;
		$this->legend_code_font_size = 8;
		$this->footer_font_size = 10;
		
		$this->bar_gap = 20;
		$this->bar_width = 20;
		
		$this->value_line_gap = 20;
		
		$this->stat_value_label_format = "%n";
		
		/***
		sng:15/apr/2010
		by default, we do not show the caption because for long text, it is getting clipped
		*********/
		$this->show_caption = false;
		$this->show_legend_detail = false;
		$this->show_scale = false;
	}
	
	public function set_caption($caption){
		$this->chart_caption = $caption;
	}
	
	public function set_dimension($img_width,$graph_ht){
		$this->img_width = $img_width;
		$this->graph_height = $graph_ht;
	}
	
	public function show_legend_detail($show){
		$this->show_legend_detail = $show;
	}
	public function show_scale($show){
		$this->show_scale = $show;
	}
	public function set_font($path){
		$this->font = $path;
	}
	
	public function set_bar_width($bar_width){
		$this->bar_width = $bar_width;
	}
	public function set_bar_gap($bar_gap){
		$this->bar_gap = $bar_gap;
	}
	
	public function set_stat_value_label_format($format){
		$this->stat_value_label_format = $format;
	}
	
	/****
	data arr: each element is assoc array with keys - name (item name) and value (item value)
	sng: 9/jul/2010
	Added another field, short_name. If this is there, and not blank then this value is to be used for
	legend code.
	***********/
	public function render($data_arr,$max_value,$data_count,$create_img=false,$store_image_path_name=""){
		////////////////////////////////////////////////////////////////////////////////////////////
		/***************************************************************
		sng:23/july/2010
		If the data count is 0, create a blank chart
		**********/
		if(0==$data_count){
			$blank_img = imagecreate(200,200);
			$background_color=imagecolorallocate($blank_img,$this->background_colour_r,$this->background_colour_g,$this->background_colour_b);
			imagefilledrectangle($blank_img,0,0,$this->img_width,$img_height,$background_color);
			$caption_color = imagecolorallocate($blank_img,$this->caption_colour_r,$this->caption_colour_g,$this->caption_colour_b);
			$caption_x = 5;
			$caption_y = 20;
			imagefttext($blank_img,$this->caption_font_size,0,$caption_x,$caption_y,$caption_color,$this->font,"No data found");
			if($create_img){
				imagepng($blank_img,$store_image_path_name);
				imagedestroy($blank_img);
			}else{
				header("Content-type:image/png");
				imagepng($blank_img);
				imagedestroy($blank_img);
			}
			return;
		}
		/**********************************************************/
		/////////////////////////////////////////
		//calculated values
		$chart_width = $this->img_width - $this->left_margin;
		$footer_ht = $this->legend_row_ht*$data_count;
		
		if($this->show_legend_detail){
			$img_height = $this->caption_ht + $this->graph_height + $this->legend_margin + $footer_ht;
		}else{
			$img_height = $this->caption_ht + $this->graph_height + $this->legend_margin;
			//legend margin is required to show the legend codes under the bars
		}
		//will all the bars fit within chart boundary?
		$temp_w = ($data_count*$this->bar_width) + ($this->bar_gap*($data_count + 1));
		if($temp_w > $chart_width){
			//we need to reduce the bar width
			$this->bar_width = ($chart_width - ($this->bar_gap*($data_count + 1)))/$data_count;
		}
		////////////////////////////////////////////////////////////////
		//create the chart image with background colour
		$chart_img = imagecreate($this->img_width,$img_height);
		$background_color=imagecolorallocate($chart_img,$this->background_colour_r,$this->background_colour_g,$this->background_colour_b);
		imagefilledrectangle($chart_img,0,0,$this->img_width,$img_height,$background_color);
		////////////////////////////////////////////////////////////////////////
		//draw X and Y axis
		$axis_color = imagecolorallocate($chart_img,$this->axis_colour_r,$this->axis_colour_g,$this->axis_colour_b);
		//x
		imageline($chart_img,$this->left_margin,$this->caption_ht+$this->graph_height,$this->img_width,$this->caption_ht+$this->graph_height,$axis_color);
		//y
		imageline($chart_img,$this->left_margin,$this->caption_ht,$this->left_margin,$this->caption_ht+$this->graph_height,$axis_color);
		/////////////////////////////////////////////////////////////////////
		if($this->show_caption){
			//draw caption
			$caption_color = imagecolorallocate($chart_img,$this->caption_colour_r,$this->caption_colour_g,$this->caption_colour_b);
			$caption_box = imageftbbox($this->caption_font_size,0,$this->font,$this->chart_caption);
			$caption_box_width = $caption_box[2] - $caption_box[0];
			$caption_x = ($this->img_width - $caption_box_width)/2;
			$caption_box_ht = $caption_box[7] - $caption_box[1];
			$caption_y = ($this->caption_ht - $caption_box_ht)/2;
			imagefttext($chart_img,$this->caption_font_size,0,$caption_x,$caption_y,$caption_color,$this->font,$this->chart_caption);
		}
		//////////////////////////////////////////
		$unit_value_bar_ht = $this->graph_height/$max_value;
		
		//draw the horizontal value lines with values in left margin
		$txt_color = imagecolorallocate($chart_img,$this->y_axis_txt_colour_r,$this->y_axis_txt_colour_g,$this->y_axis_txt_colour_b);
		$horizontal_line_color=imagecolorallocate($chart_img,$this->value_line_colour_r,$this->value_line_colour_g,$this->value_line_colour_b);
		if($this->show_scale){
			$horizontal_lines = $this->graph_height/$this->value_line_gap;
			for($hori_i=1;$hori_i<=$horizontal_lines;$hori_i++){
				$hori_y = $this->caption_ht + $this->graph_height - $this->value_line_gap * $hori_i ;
				imageline($chart_img,$this->left_margin,$hori_y,$this->img_width,$hori_y,$horizontal_line_color);
				$horizontal_value=intval($this->value_line_gap * $hori_i /$unit_value_bar_ht);
				imagestring($chart_img,0,5,$hori_y-5,$horizontal_value,$txt_color);
			}
		}
		////////////////////////////////////////////////
		//draw the bars
		$bar_color = imagecolorallocate($chart_img,$this->bar_colour_r,$this->bar_colour_g,$this->bar_colour_b);
		
		$bar_x = $this->left_margin + $this->bar_gap;
		for($bar_i=0;$bar_i<$data_count;$bar_i++){
			$bar_y = $this->caption_ht + $this->graph_height - ($data_arr[$bar_i]['value']*$unit_value_bar_ht);
			$bar_x2 = $bar_x + $this->bar_width;
			$bar_y2 = $this->caption_ht + $this->graph_height;
			imagefilledrectangle($chart_img,$bar_x,$bar_y,$bar_x2,$bar_y2,$bar_color);
			/////////////////////////////////////////////////////
			//draw short name
			/**
			sng:9/jul/2010
			check for short_name. If present, and is not blank, then use that
			**/
			if(isset($data_arr[$bar_i]['short_name'])&&($data_arr[$bar_i]['short_name']!="")){
				$legend_code = strtoupper($data_arr[$bar_i]['short_name']);
			}else{
				$legend_code = $this->short_legend_name($data_arr[$bar_i]['name']);
			}
			
			$font_size = 8;
			$legend_code_box = imageftbbox($font_size,0,$this->font,$legend_code);
			$legend_code_box_ht = $legend_code_box[1] - $legend_code_box[7];
			$legend_code_box_wt = $legend_code_box[6] - $legend_code_box[4];
			imagettftext ( $chart_img , $font_size , 0 , $bar_x+($this->bar_width+$legend_code_box_wt)/2 , $bar_y2+$legend_code_box_ht+3 , $txt_color , $this->font , $legend_code );
			/////////////////////////////////////////////////////////////
			//draw stat value on top of bar
			//use the stat value label format
			$stat_value = $data_arr[$bar_i]['value'];
			$stat_value_label = str_replace("%n",$stat_value,$this->stat_value_label_format);
			
			$stat_value_box = imageftbbox($this->legend_code_font_size,0,$this->font,$stat_value_label);
			$stat_value_box_ht = $stat_value_box[1] - $stat_value_box[7];
			$stat_value_box_wt = $stat_value_box[6] - $stat_value_box[4];
			imagettftext ( $chart_img , $this->legend_code_font_size , 0 , $bar_x+($this->bar_width+$stat_value_box_wt)/2 , $bar_y-$stat_value_box_ht , $txt_color , $this->font , $stat_value_label );
			/////////////////////////////////////////////////
			$bar_x+= $this->bar_width + $this->bar_gap;
		}
		/////////////////////////////////////////////////////////////////////////////
		if($this->show_legend_detail){
			//show legend codes with data name in footer
			$legend_color=imagecolorallocate($chart_img,0,0,0);
			
			$legend_x = $this->left_margin;
			$legend_y = $this->caption_ht + $this->graph_height + $this->legend_margin;
			
			for($bar_i=0;$bar_i<$data_count;$bar_i++){
				$legend_code = $this->short_legend_name($data_arr[$bar_i]['name']);
				$legend = $legend_code.": ".$data_arr[$bar_i]['name']." (".$data_arr[$bar_i]['value'].")";
				$legend_box = imageftbbox($this->footer_font_size,0,$this->font,$legend);
				$legend_box_ht = $legend_box[7] - $legend_box[1];
				imagefttext($chart_img,$this->footer_font_size,0,$legend_x,$legend_y+$legend_box_ht,$legend_color,$this->font,$legend);
				$legend_y+=$this->legend_row_ht;
			}
		}
		//////////////////////////////////////////////////////////////
		if($create_img){
			imagepng($chart_img,$store_image_path_name);
			imagedestroy($chart_img);
		}else{
			header("Content-type:image/png");
			imagepng($chart_img);
			imagedestroy($chart_img);
		}
		
	}
	/****
	Internal helper function to create short name from the name passed to it
	This is useful when the names of the data points are big
	The short name is formed by taking first letter from each word in the name.
	NOTE: this means, if there are data points with names Abro and Alkata, both the
	short name will be A. In that case, it might be useful to use number
	*********/
	private function short_legend_name($name){
		$short_name = "";
		$name_tokens = explode(" ",$name);
		$num_tokens = count($name_tokens);
		for($i=0;$i<$num_tokens;$i++){
			if($name_tokens[$i]!=""){
				$short_name.=strtoupper(substr($name_tokens[$i],0,1));
			}
		}
		return $short_name;
	}
}
$g_barchart = new barchart();
?>