<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Whitehole - Chart</title>
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-15" />
</head>
<body>
<?php
	/* Libchart - PHP chart library
	 * Copyright (C) 2005-2011 Jean-Marc Trémeaux (jm.tremeaux at gmail.com)
	 * 
	 * This program is free software: you can redistribute it and/or modify
	 * it under the terms of the GNU General Public License as published by
	 * the Free Software Foundation, either version 3 of the License, or
	 * (at your option) any later version.
	 * 
	 * This program is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 * GNU General Public License for more details.
	 *
	 * You should have received a copy of the GNU General Public License
	 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 * 
	 */
	
	/**
	 * Line chart demonstration
	 *
	 */
include "db_conn.php";
include "functions.php";

$hostname=$_GET['hostname'];

$now_timestamp=mktime();
$start_timestamp=$now_timestamp - 3600; // ago 1 hour

$metrics=array('vm_cpuUsed','vda_rd_req','vda_rd_bytes','vda_wr_req','vda_wr_bytes','vdb_rd_req','vdb_rd_bytes','vdb_wr_req','vdb_wr_bytes','vnet_rx_bytes','vnet_rx_packets','vnet_rx_errs','vnet_rx_drop','vnet_tx_bytes','vnet_tx_packets','vnet_tx_errs','vnet_tx_drop');
//print_r($metrics); exit;

foreach ($metrics as $metric) {
	if ($metric == "vm_cpuUsed") {
		$scale=1;
		$unit="nano";
	} else if ($metric == "vda_rd_req" || $metric == "vda_wr_req" || $metric == "vdb_rd_req" || $metric == "vdb_wr_req" || $metric == "vnet_rx_packets" || $metric =="vnet_rx_errs" || $metric == "vnet_rx_drop" || $metric == "vnet_tx_packets" || $metric == "vnet_tx_errs" || $metric == "vnet_tx_drop")
	{
		$scale=1/1000;
		$unit="kilo";
	} else if ($metric == "vda_rd_bytes" || $metric == "vda_wr_bytes" || $metric == "vdb_rd_bytes" || $metric == "vdb_wr_bytes" || $metric == "vnet_rx_bytes" || $metric == "vnet_tx_bytes") {
		$scale=1/(1024*1024);
		$unit="Mega";
	}
	$query="select timestamp,$metric from monitoring where hostname='$hostname' and timestamp > $start_timestamp";
	$result=@mysql_query($query);
	
	if (!$result) {
		Query_Error();
	}
	
	include "./libchart/classes/libchart.php";
	//$chart = new LineChart($width=900, $height=400);
	$chart = new VerticalBarChart($width=900, $height=400);
	$dataSet = new XYDataSet();
	
	while ($data=@mysql_fetch_row($result)) {;
	//print_r($data);
	
		$date=date("H:i",$data['0']);
	
		$dataSet->addPoint(new Point("$date", round($data['1']*$scale),0));
	}
		$chart->setDataSet($dataSet);
		$chart->getPlot()->setGraphPadding(new Padding(5, 30, 50, 140));
		//$chart->getPlot()->setGraphCaptionRatio(0.65);
		//$chart->getPlot()->setTitleHeight(50);
	
		$chart->setTitle("[$hostname] $metric ($unit)");
		$chart->render("generated/$hostname-$metric.png");
?>
	<center><img alt="Line chart" src="generated/<? echo "$hostname-$metric"; ?>.png" style="border: 1px solid gray;"/></center>
	<p>
<?
}
?>
</body>
</html>
