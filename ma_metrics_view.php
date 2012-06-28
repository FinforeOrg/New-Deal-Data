<style type="text/css">
.jqplot-yaxis {
    display:block;
	margin-left:-20px !important;
	width:20px !important;
}
.jqplot-title{
	font-size:14px;
}
table.jqplot-table-legend {
	width:auto;
    margin-top: 12px;
    margin-bottom: 12px;
    margin-left: 12px;
    margin-right: 12px;
    border: 1px solid #cccccc;
    position: absolute;
    font-size: 0.75em;
	vertical-align:middle;
}
td.jqplot-table-legend{
	text-align:left;
}
td.jqplot-table-legend > div {
    border: 1px solid #cccccc;
    padding:1px;
	display:block;
}

div.jqplot-table-legend-swatch {
    width:16px;
    height:16px;
	border:none;
}
</style>
<script>
$(function(){
	$('select').selectmenu();
	<?php
	if($g_view['has_featured']){
		?>
		fetch_featured_metrics();
		<?php
	}
	?>
});
</script>
<script>
function fetch_featured_metrics(){
	<?php
	for($j=0;$j<$g_view['type_count'];$j++){
		?>
		fetch_featured_metrics_data(<?php echo $g_view['type_list'][$j]['type_id'];?>);
		<?php
	}
	?>
}

function fetch_featured_metrics_data(metrics_type_id){
	
	//clear the msg area
	$('#msg'+metrics_type_id).html('');
	//hide the chart div area
	$('#metrics-chart'+metrics_type_id).css("display","none");
	//put loading...
	$('#metrics-loading'+metrics_type_id).html('loading...');
	//fire ajax
	$.ajax({
		url: "ajax/fetch_ma_metrics_featured_data.php?metrics_type_id="+metrics_type_id,
		data: {metrics_region_country_id:<?php echo $g_view['featured_metrics_region_country_id'];?>,metrics_sector_industry_id:<?php echo $g_view['featured_metrics_sector_industry_id'];?>},
		type: "POST",
		dataType: "json",
		success: function(data){
			//remove loading...
			$('#metrics-loading'+metrics_type_id).html('');
			if(data.has_data==0){
				$('#msg'+metrics_type_id).html(data.msg);
			}else{
				draw_chart('metrics-chart'+metrics_type_id,data.region_country_title,data.sector_industry_title,data.points,data.avg,data.labels);
			}
		}
	});
}

function fetch_metrics(){
	<?php
	for($j=0;$j<$g_view['type_count'];$j++){
		?>
		fetch_metrics_data(<?php echo $g_view['type_list'][$j]['type_id'];?>);
		<?php
	}
	?>
}

function fetch_metrics_data(metrics_type_id){
	//clear the msg area
	$('#msg'+metrics_type_id).html('');
	//hide the chart div area
	$('#metrics-chart'+metrics_type_id).css("display","none");
	//put loading...
	$('#metrics-loading'+metrics_type_id).html('loading...');
	//fire ajax
	$.ajax({
		url: "ajax/fetch_ma_metrics_data.php?metrics_type_id="+metrics_type_id,
		data: $('#frm_metrics_filter').serialize(),
		type: "POST",
		dataType: "json",
		success: function(data){
			//remove loading...
			$('#metrics-loading'+metrics_type_id).html('');
			if(data.has_data==0){
				$('#msg'+metrics_type_id).html(data.msg);
			}else{
				draw_chart('metrics-chart'+metrics_type_id,data.region_country_title,data.sector_industry_title,data.points,data.avg,data.labels);
			}
		}
	});
}
function draw_chart(plot_area,region_country_title,sector_industry_title,points,avg,x_axis_labels){
	<?php
	/**********************
	sng:18/oct/2011
	If some data point is n/a, we stored 0.0 in the table and the class returns NULL
	We send the option breakOnNull so that there is a break in the line chart
	***********************/
	?>
	var chart_title = region_country_title+" "+sector_industry_title+" Vs Average";
	$('#'+plot_area).css("display","block");
	var plot = $.jqplot(plot_area, [points,avg],{
		title: chart_title,
		seriesDefaults:{
			pointLabels: { show: true },
			shadow: false
		},
		markerOptions: {
			shadow: false  
		},
		series:[
			{
				breakOnNull: true,
				label:sector_industry_title,
				color:'#CCCCCC'
			},
			{
				breakOnNull: true,
				label:region_country_title+' Avg',
				color:'#999999'
			}
		],
		axes: {
			xaxis: {
				renderer: $.jqplot.CategoryAxisRenderer,
				ticks: x_axis_labels,
				tickOptions:{
					show:true,
					mark:'outside',
					showGridline: false,
				}
			},
			yaxis:{
				tickRenderer: $.jqplot.AxisTickRenderer,
				tickOptions:{
					show:true,
					mark:'cross',
					showGridline: false
				}
	
			}
		},
		highlighter: { show: false },
		legend: {
        	show: true,
        	location: 's',
			placement: 'outsideGrid'
    	},
		grid: {
			background: '#ffffff',
			borderWidth: 0.2,
			shadow: false
		}
	});
	plot.redraw(true);
}
</script>
<form id="frm_metrics_filter">
<table border="0" cellspacing="6" cellpadding="0" style="width:auto">
<tr><td colspan="3">Refine Analysis</td></tr>
<tr>
<td>
<select name="region_id" id="region_id" style="font-size: 10px; width: 200px;">
<option value="">Select Region</option>
<?php
for($i=0;$i<$g_view['region_count'];$i++){
?>
<option value="<?php echo $g_view['region_list'][$i]['id'];?>" <?php if($_POST['region_id']==$g_view['region_list'][$i]['id']){?>selected="selected"<?php }?>><?php echo $g_view['region_list'][$i]['name'];?></option>
<?php
}
?>
</select>
</td>
<td>OR</td>
<td>
<select name="country_id" id="country_id" style="font-size: 10px; width: 200px;">
<option value="">Select Country</option>
<?php for($i=0;$i<$g_view['country_count'];$i++){
?>
<option value="<?php echo $g_view['country_list'][$i]['id'];?>" <?php if($_POST['country_id']==$g_view['country_list'][$i]['id']){?>selected="selected"<?php }?>><?php echo $g_view['country_list'][$i]['name'];?></option>
<?php
}
?>
</select>
</td>
</tr>
<tr><td colspan="3">AND</td></tr>
<tr>
<td>
<select name="sector_id" id="sector_id" style="font-size: 10px; width: 200px;">
<option value="">Select Sector</option>
<?php
for($i=0;$i<$g_view['sector_count'];$i++){
?>
<option value="<?php echo $g_view['sector_list'][$i]['id'];?>" <?php if($_POST['sector_id']==$g_view['sector_list'][$i]['id']){?>selected="selected"<?php }?>><?php echo $g_view['sector_list'][$i]['name'];?></option>
<?php
}
?>
</select>
</td>
<td>OR</td>
<td>
<select name="industry_id" id="industry_id" style="font-size: 10px; width: 200px;">
<option value="">Select Industry</option>
<?php for($i=0;$i<$g_view['industry_count'];$i++){
?>
<option value="<?php echo $g_view['industry_list'][$i]['id'];?>" <?php if($_POST['industry_id']==$g_view['industry_list'][$i]['id']){?>selected="selected"<?php }?>><?php echo $g_view['industry_list'][$i]['name'];?></option>
<?php
}
?>
</select>
</td>
</tr>
<tr><td colspan="3"><input type="button" class="btn_auto" value="generate" onclick="fetch_metrics();" /></td></tr>
</table>
</form>
<?php
for($j=0;$j<$g_view['type_count'];$j++){
	?>
	<table border="0" cellspacing="6" cellpadding="0" style="width:auto">
	<tr>
	<td><h3><?php echo $g_view['type_list'][$j]['type_name'];?></h3></td>
	<td id="metrics-loading<?php echo $g_view['type_list'][$j]['type_id'];?>" style="vertical-align:middle;"></td>
	<td id="msg<?php echo $g_view['type_list'][$j]['type_id'];?>" style="vertical-align:middle;"></td>
	</tr>
	</table>
	<div id="metrics-chart<?php echo $g_view['type_list'][$j]['type_id'];?>" style="height:600px;width:800px;display:none; "></div>
	<?php
}
?>
<div>Note: A broken line indicated that there are no relevant data points for a particular quarter.</div>