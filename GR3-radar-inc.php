<?php 
/*
// GRLevel3 Radar image display script
// Version 1.00 - 07-Mar-2008 - Initial release
// Version 1.01 - 15-Mar-2008 - added logic to find web document root on IIS servers
// Version 1.02 - 21-Jul-2008 - added script to allow XHTML validation for output
// Version 1.03 - 23-Dec-2008 - added support for Digital Total Rainfall display
// Version 1.04 - 04-Jun-2012 - added support for Dual Polarization NEXRAD products
//
// the script does NOT generate a complete HTML page and is intended for use
// ONLY by being included in an existing webpage on your site by:
//   <?php include("GR3-radar-inc.php"); ?>
// */
// settings ----------------------------- 
###########################################################################
# GRLevel3 Radar image settings
$GR3radar	= 'kmux';	// set to lower-case full name of NEXRAD radar site (ICAO)
$GR3DIR		= '/GR3'; 	// set to directory for GRLevel3 images (or '/' for root directory
$GR3type	= 'cr';		// default radar image type 'cr','br','cr248','br1' etc.
$GR3img		= 'jpg';	// GR3 image type 'jpg' or 'png'
$GR3cnt		= 10;		// number of images in series 10=(_0 ... _9 in name of file)
$GR3width	= 512;		// width of GR3 images
$GR3height  = 512;		// height of GR3 images
$GR3maxAge =  1200;  // image_0 has to be less than 20 minutes old for consideration
$GR3notAvailMsg = 'GRLevel3 radar images are not available at this time.';
$GR3notCurrentMsg = 'GRLevel3 radar image is not current - more than %s seconds old.';  
###########################################################################
// end of settings
//------------------------------------------------
if (isset($_REQUEST['sce']) && strtolower($_REQUEST['sce']) == 'view' ) {
   //--self downloader --
   $filenameReal = __FILE__;
   $download_size = filesize($filenameReal);
   header('Pragma: public');
   header('Cache-Control: private');
   header('Cache-Control: no-cache, must-revalidate');
   header("Content-type: text/plain");
   header("Accept-Ranges: bytes");
   header("Content-Length: $download_size");
   header('Connection: close');
   readfile($filenameReal);
   exit;
}
?>
<!-- GR3-radar-inc.php - Version 1.04 - 04-Jun-2012 - http://saratoga-weather.org/scripts.php -->
<?php
//------------------------------------------------
// overrides from Settings.php if available
global $SITE;
if (isset($SITE['GR3radar'])) 	{$GR3radar	= $SITE['GR3radar'];}
if (isset($SITE['GR3DIR'])) 	{$GR3DIR	= $SITE['GR3DIR'];}
if (isset($SITE['GR3type'])) 	{$GR3type	= $SITE['GR3type'];}
if (isset($SITE['GR3img'])) 	{$GR3img	= $SITE['GR3img'];}
if (isset($SITE['GR3cnt'])) 	{$GR3cnt	= $SITE['GR3cnt'];}
if (isset($SITE['GR3width'])) 	{$GR3width	= $SITE['GR3width'];}
if (isset($SITE['GR3width'])) 	{$GR3height  = $SITE['GR3height'];}
if (isset($SITE['GR3maxAge'])) 	{$GR3maxAge = $SITE['GR3maxAge'];}
if (isset($SITE['GR3notAvailMsg'])) 	{$GR3notAvailMsg = $SITE['GR3notAvailMsg'];}
if (isset($SITE['$GR3notCurrentMsg'])) 	{$$GR3notCurrentMsg = $SITE['$GR3notCurrentMsg'];}
// end of overrides from Settings.php if available

$UTCtime = time(); // not random, but needed for URL fetches.
$doDebug = false;
if (isset($_REQUEST['debug']))  {$doDebug = strtolower($_REQUEST['debug']) == 'y'; }

print "<!-- part of the Carterlake/WD/PHP/AJAX template set -->\n";
print "<!-- Thanks to Tim Hanko at http://www.thanko.info/ for the concept and initial radar type selection code -->\n";
?>
<script type="text/javascript">
/*
Interactive Image slideshow with text description
By Christian Carlessi Salvadó (cocolinks@c.net.gt). Keep this notice intact.
Visit http://www.dynamicdrive.com for script
Modified by Ken True - Saratoga-Weather.org
*/

var g_fPlayMode = 0;
var g_iimg = -1;
var g_imax = 0;
var g_ImageTable = new Array();

function ChangeImage(fFwd)
{
if (fFwd)
{
if (++g_iimg==g_imax)
g_iimg=0;
}
else
{
if (g_iimg==0)
g_iimg=g_imax;
g_iimg--;
}
Update();
}

function getobject(obj){
if (document.getElementById)
  return document.getElementById(obj)
else if (document.all)
  return document.all[obj]
}

function Update(){
getobject("_Ath_Slide").src = g_ImageTable[g_iimg][0];
//getobject("_Ath_FileName").innerHTML = g_ImageTable[g_iimg][1];
getobject("_Ath_Img_X").innerHTML = g_iimg + 1;
getobject("_Ath_Img_N").innerHTML = g_imax;
}

function Oldest() {
  g_iimg=0;
  Update();
}

function Newest() {
  g_iimg=g_imax;
  g_iimg--;
  Update();
}
function Play()
{
g_fPlayMode = !g_fPlayMode;
if (g_fPlayMode)
{
getobject("btnPrev").disabled = getobject("btnNext").disabled = true;
getobject("btnOldest").disabled = getobject("btnNewest").disabled = true;
Next();
}
else 
{
getobject("btnPrev").disabled = getobject("btnNext").disabled = false;
getobject("btnOldest").disabled = getobject("btnNewest").disabled = false;

}
}
function OnImgLoad()
{
if (g_fPlayMode)
window.setTimeout("Tick()", g_dwTimeOutSec*1000);
}
function Tick() 
{
if (g_fPlayMode)
Next();
}
function Prev()
{
ChangeImage(false);
}
function Next()
{
ChangeImage(true);
}


// configuration section 
<?php
// list of all GRLevel3 radar types that may be produced
$RadarTypes = array(
				'br1' => 'Base Reflectivity 0.5&deg;',
				'br2' => 'Base Reflectivity 0.9&deg;',
				'br3' => 'Base Reflectivity 1.5&deg;',
				'br4' => 'Base Reflectivity 1.8&deg;',
				'br5' => 'Base Reflectivity 2.5&deg;',
				'br6' => 'Base Reflectivity 3.5&deg;',
			    'br248' => 'Base Reflectivity 248nm',
				'bv1' => 'Base Velocity 0.5&deg;',
				'bv2' => 'Base Velocity 0.9&deg;',
				'bv3' => 'Base Velocity 1.5&deg;',
				'bv4' => 'Base Velocity 1.8&deg;',
				'bv5' => 'Base Velocity 2.5&deg;',
				'bv6' => 'Base Velocity 3.5&deg;',
				'bv32' => 'Base Velocity 32nm',
				'srv1' => 'Storm Relative Velocity 0.5&deg;',
				'srv2' => 'Storm Relative Velocity 1.5&deg;',
				'srv3' => 'Storm Relative Velocity 2.5&deg;',
				'srv4' => 'Storm Relative Velocity 3.5&deg;',
			    'sw' => 'Spectrum Width',
			    'sw32' => 'Spectrum Width 32nm',
			    'cr' => 'Composite Reflectivity',
			    'cr248' => 'Composite Reflectivity 248nm',
			    'et' => 'Echo Tops',
			    'vil' => 'Vertically Integrated Liquid',
			    'ohr' => 'One Hour Rain',
			    'thr' => 'Three Hour Rain',
			    'str' => 'Storm Rain',
				'dsp'  => 'Digital Total Rainfall',
				'zdr1' => 'DP Digital Differential Reflectivity 0.5&deg;',
				'zdr2' => 'DP Digital Differential Reflectivity 0.9&deg;',
				'zdr3' => 'DP Digital Differential Reflectivity 1.5&deg;',
				'zdr4' => 'DP Digital Differential Reflectivity 1.8&deg;',
				'zdr5' => 'DP Digital Differential Reflectivity 2.5&deg;',
				'zdr6' => 'DP Digital Differential Reflectivity 3.5&deg;',
				'cc1' => 'DP Correlation Coefficient 0.5&deg;',
				'cc2' => 'DP Correlation Coefficient 0.9&deg;',
				'cc3' => 'DP Correlation Coefficient 1.5&deg;',
				'cc4' => 'DP Correlation Coefficient 1.8&deg;',
				'cc5' => 'DP Correlation Coefficient 2.5&deg;',
				'cc6' => 'DP Correlation Coefficient 3.5&deg;',
				'kdp1' => 'Specific Differential Phase 0.5&deg;',
				'kdp2' => 'Specific Differential Phase 0.9&deg;',
				'kdp3' => 'Specific Differential Phase 1.5&deg;',
				'kdp4' => 'Specific Differential Phase 1.8&deg;',
				'kdp5' => 'Specific Differential Phase 2.5&deg;',
				'kdp6' => 'Specific Differential Phase 3.5&deg;',
				'hca1' => 'Hydrometeor Class 0.5&deg;',
				'hca2' => 'Hydrometeor Class 0.9&deg;',
				'hca3' => 'Hydrometeor Class 1.5&deg;',
				'hca4' => 'Hydrometeor Class 1.8&deg;',
				'hca5' => 'Hydrometeor Class 2.5&deg;',
				'hca6' => 'Hydrometeor Class 3.5&deg;',
				'hhc' => 'DP Digital Hybrid Hydrometeor Class', 
				'dod' => 'DP Digital One Hour Difference', 
				'dsd' => 'DP Digital Storm Total Rainfall', 
				'tvs' => 'Tornado Vortex Signature', 
				'ssi' => 'Storm Structure Information', 

);
$AvailTypes = array();

$timeLimit = time() - 1200;  // time for 'freshness' is 20*60 = 20 minutes.

// Create the list from current files available.
// Obtain Basic Environment
// in APACHE servers it is always defined
if (! isset($_SERVER['DOCUMENT_ROOT'] ) ) {
   $path_trans = str_replace( '\\\\', '/', $_SERVER['PATH_TRANSLATED']);
   print "// path_trans = '$path_trans' \n";
   print "// self = '" . $_SERVER['PHP_SELF'] . "' \n";
   $WEBROOT = substr($path_trans, 0, strlen($path_trans)-strlen($_SERVER['PHP_SELF']) );
}
else {
   $WEBROOT        = $_SERVER['DOCUMENT_ROOT'];
}
$path = realpath($WEBROOT . $GR3DIR . '/' ) . '/';
print "// webroot = '$WEBROOT' \n";
print "// gr3dir = '$GR3DIR'\n";
print "// path = '$path' \n";
$RadarMsg = array();

foreach ($RadarTypes as $type => $legend) {
    $tname = $path . strtolower($GR3radar) . '_' . 
           $type . '_';
    $ttype = '.' . $GR3img;
	$tfile = $tname . '0' . $ttype;
    $shortname = strtolower($GR3radar) . '_' . 
           $type . '_0.' . $GR3img;
	
	if(file_exists($tfile)) {
	   $secsOld = filemtime($tfile);
	   if($secsOld >= time() - $GR3maxAge) {
	     $AvailTypes[$type] = $legend;
		 $RadarMsg[$type] = '&nbsp;';
	   } else {
	     $tage = time() - $secsOld;
	     print "// -- $shortname too old. Last updated $tage seconds ago\n";
		 $RadarMsg[$type] = sprintf($GR3notCurrentMsg,$GR3maxAge);
	   }
	} else {
	   print "// -- $shortname not found \n";
	   $RadarMsg[$type] = "$GR3notAvailMsg";
	}
}

if (!isset($AvailTypes[$GR3type])) { // oops.. our default one is not available

  $key = array_shift(array_keys($AvailTypes)); // get the first one available
  if ($key) { $GR3type = $key; } 
  
}
// $AvailTypes[$GR3type] = $RadarTypes[$GR3type]; // ensure at least the default is shown
print "/* -- avail \n " . print_r($AvailTypes,true) . " */\n";

if (isset($_REQUEST['map']))
	{
	 	$selectedType = preg_replace('|[^\w]|is','',strtolower($_REQUEST['map'])); // no bad juju in input string
		if (!isset($AvailTypes[$selectedType]) ) { 
		   print "// type '$selectedType' not valid .. using '" . $GR3type . "' instead.\n";
		   $selectedType = $GR3type;
		}
	}
else
	{
		$selectedType = $GR3type;
	}

  $tname = $GR3DIR . '/' . strtolower($GR3radar) . '_' . 
           $selectedType . '_';
  $ttype = '.' . $GR3img;
  $noCache = '?t=' . time();
  for ($i=$GR3cnt-1;$i >= 0;$i--) {
    print 'g_ImageTable[g_imax++] = new Array ("' . $tname . $i . $ttype . $noCache . '", "");' . "\n";
  }
?>
//end generated function
var g_dwTimeOutSec=1;
////End configuration/////////////////////////////

//if (document.getElementById||document.all)
//window.onload=Play
</script>
<?php
if($doDebug) {
  foreach ($RadarMsg as $type => $msg) {
    print "<!-- type='$type' radar msg='$msg' -->\n";
  }
}
$haveOneFile = isset($AvailTypes[$selectedType]);

if ($haveOneFile) { // got at least one.. do the page normally
?>  

<form action="" method="get">
	<fieldset style="width: 620px">
		<legend>Radar Type</legend>
		<select name="map" onchange="this.form.submit();">
<?php
foreach ($AvailTypes as $type => $legend) {
  
  if ($type == $selectedType) {
     print "			<option selected=\"selected\" value=\"$type\">$legend - ($type)</option>\n";
  } else {
     print "			<option value=\"$type\">$legend - ($type)</option>\n";
  
  }
  
}
?>
		</select>
		<noscript><input type="submit" value="Display" name="display" /> [Enable JavaScript for animated display]</noscript>
<script type="text/javascript">
// <![CDATA[
document.write('   <input type="button" id="btnOldest" value="|&lt;&lt;-" onclick="Oldest();" />' +
'   <input type="button" id="btnPrev" value="&lt;-" onclick="Prev();" />' +
'   <input type="button" id="bntPlay" value="Play - Stop" onclick="Play()" />' +
'   <input type="button" id="btnNext" value="-&gt;" onclick="Next();" />' +
'   <input type="button" id="btnNewest" value="-&gt;&gt;|" onclick="Newest();" />'
);
// ]]>
</script>
	</fieldset>
	</form>

<?php 
  } // end of got one file at least
  
  
  if ($haveOneFile) { // hope for the best 
    echo '<p>' . $RadarMsg[$selectedType] . '&nbsp;[<b><span id="_Ath_Img_X">' . $GR3cnt . 
    '</span>/<span id="_Ath_Img_N">' . $GR3cnt . '</span></b>]&nbsp;' .
    "<br/>\n"; ?>
    <img src="<?php echo $tname . '0' . $ttype; ?>" 
    alt="GRLevel3 radar from NWS station <?php echo strtoupper($GR3radar); ?>"
    title="GRLevel3 radar from NWS station <?php echo strtoupper($GR3radar); ?>"  
    width="<?php echo $GR3width; ?>" height="<?php echo $GR3height; ?>"
    id="_Ath_Slide"/> </p>
<script type="text/javascript">
if(document.getElementById) {
 var img = document.getElementById('_Ath_Slide');
 if (img) img.onload = OnImgLoad;
}
</script>

   <p>The above image was produced by <a href="http://www.grlevelx.com/grlevel3/">GRLevel3</a> software using   NEXRAD Radar data from <a href="http://radar.weather.gov/radar.php?rid=<?php echo substr($GR3radar,1,3); ?>&amp;product=N0R&amp;overlay=11101111&amp;loop=no">station <?php echo strtoupper($GR3radar); ?></a>.</p>
<?php } else { // not one file found ?>
  <p><?php echo $GR3notAvailMsg; ?></p>
<?php } // end not one file found ?>
<!-- end of GR3-radar-inc.php -->