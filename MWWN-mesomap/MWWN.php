<?php
//------------------------------------------------
header("Cache-Control: no-cache,no-store,  must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
$NOWdate = gmdate("D, d M Y H:i:s", time());
header("Expires: $NOWdate GMT");
header("Last-Modified: $NOWdate GMT");
//------------------------------------------------
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="Refresh" content="300" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Cache-Control" content="no-cache" />
<meta name="author" content="Ken True" />
<meta name="copyright" content="&copy; 2008 MidwesternWeather.net" />
<meta name="Keywords" content="weather, Weather, temperature, dew point, humidity, forecast, weather conditions, live weather, live weather conditions, weather data, weather history, Lightning, WebCam, NetCam" />
<meta name="Description" content="Weather conditions for Midwestern USA" />
<title>Midwestern Weather Network - Current Conditions</title>
<?php
$doPrintMWWN = false;
include("MWWN-mesomap.php");
print $MWWN_CSS; ?>
</head>
<body style="font-family:Verdana, Arial, Helvetica, sans-serif; 
font-size:12px; width: 620px;">
<?php print $MWWN_MAP; print $MWWN_TABLE; ?>
</body>
</html>
