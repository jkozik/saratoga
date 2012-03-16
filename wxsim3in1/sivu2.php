<!DOCTYPE HTML>
<html lang="<?php echo $_GET[lang] ?>">
<head>
<title>WXSIM 3in1 testpage</title>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />

<!-- HEAD START -->
<!-- Put this part inside <head> and </head>-tags, needed on ALL pages using 3in1 -->
<?php
if(isset($_GET[lang])) {$lang = $_GET[lang]; }
else if (isset($SITE['lang'])) {$lang = $SITE['lang']; }
else {$lang = 'fi';}           // not nessessary in Saratoga Templates where $lang defined 
include 'wxsim3in1/wxall.settings.php';
echo $wxallhead;
?>
<!-- HEAD END -->

</head>
<body>

<!-- BODY START -->
<!-- Put this part where you want it to show up -->
<!-- You may want wrap it inside a div like this -->
<div style="width:<?php echo $mainwidth ?>px;font: 72% Tahoma;">
<?php
# Header with name & update-times (optional)
echo $wxallupdated;
echo '<h3>'.get_lang("WXSIM Forecast for:").' '.$wxallcity.'</h3>';

# Theese can be used stand alone or together
echo $wxalltop;     # "Top-forecast"
echo $wxallgraph;   # Graph
echo $wxallmain;    # Tabs
?>
</div>
<!-- BODY END -->

</body>
</html>
