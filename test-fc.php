<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en-US">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
<title>XHTML 1.0 Strict Page for forecast-compare Testing</title>
<!--  This style info isn't necessary for those who use any of the Carterlake template sets but may be used if you want this page to look different -->
<style type="text/css">
.table-top {
  color: black;
  background-color: rgb(230,223,207);
  border: 1px solid rgb(107,107,107);
  text-align: left;
}

.column-dark {
  color: black;
  background-color: rgb(243,242,235);
  border: 1px solid #DEDEDE;
}

.column-light {
  color: black;
  background-color: white;
}
</style>
</head>
<body> 
	<div style="text-align:center;">
		<h3>WxSim &amp; NWS Forecast Comparison</h3>
		National Weather Service (NW), Actual Observed (Act), WxSim Generated (WS)
		<br />
		<a name="AM"></a>  <!-- optional to bring the page back here when the form is submitted -->
		<?php 
			$_REQUEST['config'] = 'am';                       
			include("forecast-compare-include.php");          // will show forecastAM.log
		?>
		<br />
		<a name="PM"></a>  <!-- optional to bring the page back here when the form is submitted -->
		<?php	
			$_REQUEST['config'] = 'pm';
			include("forecast-compare-include.php");          // will show forecastPM.log		
/*			
			$_REQUEST['config'] = 'pm';
			$_REQUEST['lang'] = 'xx';                         // optional language
			include("forecast-compare-include.php");           		
*/			
		?>
	</div>
</body>
</html>
