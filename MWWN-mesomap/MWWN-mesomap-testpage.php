<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Centering MWWN map</title>
<?php
error_reporting(E_ALL);

 $doPrintMWWN = false;
include("MWWN-mesomap.php");

print $MWWN_CSS; ?>
</head>

<body style="font-family:Arial, Helvetica, sans-serif;">
<h1 style="text-align:center">Centered MWWN Mesomap using two tables</h1>
<table width="99%">
<tr><td align="center">
  <table width="620">
  <tr><td style="text-align:left">
     <?php print $MWWN_MAP; ?>
  </td></tr>
   <tr><td>
     <?php print $MWWN_TABLE; ?>
   </td></tr>
   </table>
</td></tr>
</table>
</body>
</html>
