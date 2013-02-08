<?php 
###############################################################
#
#   NWS Public Alerts
#
#   This key file culls alert data
#   No user settings in this file
#
###############################################################
/*

Version 1.10 - 21-July-2012   Added 2nd chance to cull data if timed out.
Version 1.11 - 26-July-2012   Added ability to have repeated County codes.
Version 1.12 - 27-July-2012   Fixed doubled alerts for zone & county.
Version 1.13 - 29-July-2012   Fixed icon cache file writing if set to 0.
Version 1.14 - 05-Aug-2012    Fixed spacing in the alert box.

*/
$Version = "nws-alerts.php - 1.14 - 05-Aug-2012 - NWS Public Alerts"; 

// self downloader code
if (isset($_REQUEST['sce']) && ( strtolower($_REQUEST['sce']) == 'view' or
    strtolower($_REQUEST['sce']) == 'show') ) {
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
$noted = "<!-- $Version -->\n";  // display version remark

// check for adequate PHP version
if(phpversion() < 5) {
  echo 'Failure: This ATOM/CAP Advisory Script requires PHP version 5 or greater. You only have PHP version: ' . phpversion();
  exit;
}   
  
//ini_set('display_errors', 1);
//error_reporting(E_ALL);

// include the configuration file
include('nws-alerts-config.php'); 

// start total timer
$time_startTotal = load_timer();

// overrides from Settings.php if available
if(is_file("Settings.php")) {include_once("Settings.php");}
global $SITE;
if(isset($SITE['cacheFileDir']))    {$cacheFileDir = $SITE['cacheFileDir'];}
if(isset ($SITE['tz']))             {$ourTZ = $SITE['tz'];}
if(isset ($SITE['NWSalertsCodes'])) {$myZC = $SITE['NWSalertsCodes'];}

if(!function_exists('date_default_timezone_set')) {
  putenv("TZ=" . $ourTZ);
} else {
  date_default_timezone_set("$ourTZ");
}

$dataCache = $cacheFileDir.$cacheFileName;                    // path & file name for the cache data file
$aboxCache = $cacheFileDir.$aboxFileName;                     // path & file name for the alert box data file
$iconCache = $cacheFileDir.$iconFileName;                     // path & file name for the big icon data file

$priURL = 'http://alerts.weather.gov/cap/wwaatmget.php?x=';   // NWS URL

// set variables
$ad      = '';
$vCodes  = '';
$noAlrt  = '';
$cmyzc   = '';
$aBox    = '';
$box     = '';
$atData  = '';
$noData  = '';
$WA      = '1';
$ai      = array();
$bi      = array();
$noA     = array();
$codeID  = array();
$ano     = array();
$norss   = array();
$abData  = array();
$rssData = array();
$adnoBI  = array();
$uts = date("U");     // current unix time stamp
$timenow = date("D g:i a",$uts);
$totalPreCount = '';

// display notice if more than 4 codes are checked without using a cron job
foreach ($myZC as $myv) {
  $cmyzc += substr_count($myv, '|'); // total count of codes in $myZC array
}
if($cmyzc > 4 and $noCron == true) {
  echo "nws-alerts: Checking more than four warning/county codes can delay the loading of your pages. You should use a cron job to get the data.\n"; 
}   

// cache file update policy
$updateCache = true;                          // update cache file(s)
if(file_exists($dataCache) and $noCron) {     // IF the data cache file exists and using a cron job
  $cft = filemtime($dataCache);               //   get cache file last modified time
  $cfAge = $uts - $cft;                       //   get cache file age in seconds
  if($cfAge < $updateTime) {                  //   IF cache file has not expired
    $updateCache = false;                     //     don't update
  }
  if(!empty($_GET['mu'])) {                   //   IF you want to manually update the report   ?mu=1
    $updateCache = true;                      //     set update cache
    $noted .= "<!-- manually updated -->\n";  //     display notice
  }
}

// display remark about cron
($noCron) ? $noted .= "<!-- Cron job not used -->\n" :
            $noted .= "<!-- Cron job enabled -->\n";

###  IF UPDATE CACHE
if($updateCache) {
  $time_startPreliminary = load_timer();                                         // start preliminary timer
  // get preliminary links
  foreach ($myZC as $mv) {                                                       // FOR EACH zone/county code
    preg_match_all("/\w{3}\d{3}/", $mv, $cds);                                   //   grab the codes
    $locCode = $cds[0][0];                                                       //   get first code listed after location as reference
    $vCodes .= $locCode.'|';                                                     //   list valid reference codes
    $loc = preg_replace("/\|.*$/", '', $mv);                                     //   grab the locatiion
    $ccds = count($cds[0]);                                                      //   count codes
    $totalPreCount += $ccds;                                                     //   add up codes to check for preliminary alerts
    for ($i=0;$i<$ccds;$i++) {                                                   //   FOR EACH listed code
      if(!$zData = get_nwsalerts($priURL.$cds[0][$i])) {                         //     IF can't get  preliminary URL
        $noted .= "<!-- First attempt in getting preliminary URL failed -->\n";  //       create note
        sleep(.5);                                                               //       wait a half second
        if($zData = get_nwsalerts($priURL.$cds[0][$i])) {                        //       IF URL cull was successful
          $noted .= "<!-- Second attempt successful -->\n";                      //         create note
          $noted .= '<!-- '.$priURL.$cds[0][$i]." -->\n\n";                      //         create note
       }
        else{                                                                    //       OR ELSE
          $noted .= "<!-- Second attempt failed -->\n";                          //         create note
          $noted .= '<!-- '.$priURL.$cds[0][$i]." -->\n\n";                      //         create note
          $noData = true;                                                        //         set variable
        }
      }
      (isset($zData->entry)) ? $zSearch = $zData->entry : $zSearch = '';    //     set search varible
      if(isset($zSearch[0])) {                                              //     IF there is data available
        $noData = '';
        $cccds=count($zSearch);                                             //       count each alert
        for ($m=0;$m<$cccds;$m++) {                                         //       FOR EACH alert per code
          $za = trim($zSearch[$m]->id);                                     //         get the secondary URL link
          if(!preg_match('/\?x=\w{12,}/Ui',$za)) {                          //         IF the link doesn't list an alert URL
            $noA[$loc][] = $locCode;                                        //           assemble array for 'No alerts'
          }
          else {                                                            //         OR ELSE
            $codeID[$za][$loc][] = $locCode;                                //           assemble array of alert URL's & locations
          }
        }
      }
    }
  }

  $time_stopPreliminary = load_timer();                                     // stop preliminary timer

  // check for codes with 'No alerts' against similar location codes with alerts
  foreach ($codeID as $rk => $rv) {                                         // FOR EACH alert
    $noA = array_diff_ukey($noA, $rv, 'key_compare');                       //  remove location code from no Alerts is alert is found
  }

  // sort No alerts array by key
  ksort($noA);
  // trim down No alert array
  foreach ($noA as $nk => $nv) {                                                                              // FOR EACH No alert
    $noAlrt[$nk] = $nv[0];                                                                                    //   create a new array
    $norss[$nk][] = array('150',$nk,$nv[0],'No Alerts',1,'Severe weather is not expected');                   //   create array for RSS/XML
  }
  
  $countCodeID = count($codeID);                                                                              // count locations with alerts
  $time_startPrimary = load_timer();                                                                          // start primary timer
  // get main data
  foreach ($codeID as $ck => $cv) {                                                                           // FOR EACH primary alert
    if(isset($cv)) {                                                                                          //   IF there is an alert
      $clr = ''; $ico = ''; $sev = '';                                                                        //     set variables
      if(!$czR = get_nwsalerts($ck)){                                                                         //     IF can't download each alert
        $noted .= "<!-- First attempt in getting primary data failed -->\n";                                  //       create note
        sleep(.5);                                                                                            //       wait a half second
        if($czR = get_nwsalerts($ck)){                                                                        //       IF download alert was successful
          $noted .= "<!-- Second attempt successful -->\n";                                                   //         create note
          $noted .= '<!-- '.$ck." -->\n\n";                                                                   //         create note
        }
        else{                                                                                                 //       OR ELSE
          $noted .= "<!-- Second attempt failed -->\n";                                                       //         create failure note
          $noted .= '<!-- '.$ck." -->\n\n";                                                                   //         create note
          $noData = true;                                                                                     //         set variable
        }
      }
      (isset($czR->info->event)) ? $event = trim($czR->info->event) : $event = '';                            //     get event
      (isset($czR->info->urgency)) ? $urgency = trim($czR->info->urgency) : $urgency = '';                    //     get urgency
      (isset($czR->info->severity)) ? $severity = trim($czR->info->severity) : $severity = '';                //     get severity
      (isset($czR->info->certainty)) ? $certainty = trim($czR->info->certainty) : $certainty = '';            //     get certainty
      (isset($czR->info->effective)) ? $effective = strtotime(trim($czR->info->effective)) : $effective = ''; //     get effective time
      (isset($czR->info->expires)) ? $expires = strtotime(trim($czR->info->expires)) : $expires = '';         //     get expiration time
      (isset($czR->info->description)) ? $description = trim($czR->info->description) : $description = '';    //     get the full alert description
      (isset($czR->info->instruction)) ? $instruction = trim($czR->info->instruction) : $instruction = '';    //     get the full alert description
      (isset($czR->info->area->areaDesc)) ? $areaDesc = trim($czR->info->area->areaDesc) : $areaDesc = '';    //     get areas
      (isset($czR->info->area->polygon)) ? $poly = trim($czR->info->area->polygon) : $poly = '';              //     get poly areas
      (isset($event)) ? $cis = get_icon($event) : $cis = '';                                                  //     get other variables for event
      if(isset($cis)) {                                                                                       //     IF event varaibles
        $clr = $cis['color'];                                                                                 //       set event color
        $ico = $cis['icon'];                                                                                  //       set event icon
        $sev = $cis['severity'];                                                                              //       set event severity
      }
      (!empty($ico)) ? $ico = conv_icon($icons_folder,$ico,$event) : $ico = '';                               //     IF an icon name is found, convert name into icon
      if($event <> '' or $description <> '') {
        foreach($cv as $cvk => $cvv) {                                                                        //     FOR EACH listed code
          $cvv = array_unique($cvv);
          $lcount = count($cvv);                                                                              //       count location codes
				  for($i=0;$i<$lcount;$i++) {                                                                         //       FOR EACH location code
            $lCode = $cvv[$i];                                                                                //         set variable to location code
            $ad[$lCode][] = array($event,$urgency,$severity,$certainty,$effective,$expires,
                            $areaDesc,$instruction,$description,$clr,$ico,$sev,$cvk,$WA,$cvv[0],$poly);       //         create array with needed wx variables
					}
          $WA++;                                                                                              //       increment counter
        }
      }
      else {
        $noted .= "<!-- $cv did not have any data -->\n";                                   //   display remark
      }
    }
  }
  $time_stopPrimary = load_timer();                                                          // stop primary timer
	
  // TIMERS
  $total_timer1 = '';
  $total_timer1 += ($time_stopPreliminary - $time_startPreliminary);
  $time_fetch1 = sprintf("%01.4f", round($time_stopPreliminary - $time_startPreliminary, 4));
  $time_fetch1;
  $avgCull = round($time_fetch1 / $totalPreCount, 4);
  $noted .= "<!-- Preliminary codes checked: $totalPreCount -->\n";                          //   display remark
  $noted .= "<!-- Preliminary data cull: $time_fetch1 seconds -->\n";                        //   display remark
  $noted .= "<!-- Average cull per code: $avgCull seconds -->\n";                            //   display remark

  $total_timer2 = '';
  $total_timer2 += ($time_stopPrimary - $time_startPrimary);
  $time_fetch2 = sprintf("%01.4f", round($time_stopPrimary - $time_startPrimary, 4));
  $time_fetch2;
  $noted .= "<!-- Primary alerts downloaded: $countCodeID -->\n";                            //   display remark
  $noted .= "<!-- Primary data cull: $time_fetch2 seconds -->\n";                            //   display remark
  // END OF TIMERS

  // sort active alerts by severity
  if(!empty($ad)) {                                                                          // IF alert data is not empty
    foreach ($ad as $adk => $adv) {                                                          //   FOR EACH alert data
      foreach ($adv as $advk => $advkv) {                                                    //   FOR EACH alert data
        usort($adv, 'sev_sort');                                                             //     sort locations multiple alerts by severity
        $atData[$adk] = $adv;                                                                //     create sorted array
      }
    }
  }
					
  // writing alert data to cache file
  if($noData == '') {
    $dcfo = fopen($dataCache , 'w');                                                           // data cache file open
    if(!$dcfo) {                                                                               // IF unable to open cache file for writing
      $noted .= "<!-- unable to open cache file -->\n";                                        //   display remark
    } 
    else {                                                                                     // OR ELSE
      $write = fputs($dcfo, "<?php \n \n".'$atomAlerts = '. var_export($atData, 1).";\n");     //   write all of the alert data
      $write = fputs($dcfo, "\n".'$noAlerts = '. var_export($noAlrt, 1).";\n");                //   write no alert data
      $write = fputs($dcfo, "\n".'$validCodes = '. var_export($vCodes, 1).";\n\n?>");          //   write valid codes
      fclose($dcfo);                                                                           //   close the cache file
      $noted .= "<!-- Cache file updated: $timenow -->\n";                                     //   display remark
    } 
  }
  else {
    $noted .= "<!-- NO cache files updated -->\n";                                           //   display remark
  }
  ($centerText) ? $ct = 'text-align:center;' : $ct = 'text-align:left;';                     //   set text alignment

  // alert box conditions for NO alerts
  if($useAlertBox and empty($atData)) {                                                      // IF using alert box and no alerts
    get_scc('150');                                                                          //   set alert box backgound color and text color
    $box .= "\n<!-- nws-alerts box -->\n"
         .'<div style="width:'.$aBox_Width
         .'; border:solid thin #000; margin:0px auto 0px auto;">'."\n";
    if($showNone) {                                                                          //   IF showing "NONE', create alert box with No Alert
      $box .= ' <div style=" '.$bc.' '.$ct.' '.$tc.' '.'padding:4px 8px 4px 8px"><a href="'
           .$summaryURL.'" title=" &nbsp;View summary" style="text-decoration:none; '.$tc
           .'">No alerts</a></div>
</div>
';
    }
    else {                                                                                   //   OR ELSE, don't show alert box
      $box = '';
    }
  }

  // alert box conditions WITH alerts
  if($useAlertBox and !empty($atData)) {                                                       // IF use alert box & have data
    foreach ($atData as $aak => $aav) {                                                        //   FOR EACH location with data
      foreach ($aav as $avk => $avv) {                                                         //     FOR EACH alert data
        $abData[] .= "$avv[11]|$avv[12]|$avv[14]|$avv[0]|$avv[10]|$avv[9]|$avv[13]";           //       create data string
      }
    }
    // IF sort alphabetically
    if($locSort == 1) {                                                                        // IF sort alert box data
      natsort($abData);                                                                        //   perform a natural sort for array
    }

    foreach ($abData as $aBk => $aBv) {
      // list = severity code, location name, location code, title, icon, color, alert sequence
      list($sc, $ln, $lc, $ttl, $icn, $clr, $as) = explode('|', $aBv . '|||');                 // create list for each alert
      $soryByLocation[$ln][][] = array($sc, $ln, $lc, $ttl, $icn, $clr, $as);                  // 0 sort by alert
      $soryByLocation2[$ln][$sc][] = array($sc, $ln, $lc, $ttl, $icn, $clr, $as);              // 1 sort by alert
      $soryByALert[$sc][][] = array($sc, $ln, $lc, $ttl, $icn, $clr, $as);                     // 2 sort by location
      $soryByALert2[$sc][$ln][] = array($sc, $ln, $lc, $ttl, $icn, $clr, $as);                 // 3 sort by location
			$sortByAlert3[$ttl][] = array($sc, $ln, $lc, $ttl, $icn, $clr, $as); 
    }
	
    $abta = array_shift(array_keys($soryByALert));                                              // get first alert (key) severity code
    get_scc($abta);                                                                             // set alert box backgound color to most severe alert
    $setStyle = 'style="'.$tc.' text-decoration: none"';                                        //     set text color
	
    // set alert box style
    $box .= "\n<!-- nws-alerts box -->\n"
         .'<div style="width:'.$aBox_Width
         .'; border:solid thin #000; margin:0px auto 0px auto;">'."\n"
         .' <div style=" '.$bc.' '.$ct.' '.$tc.' '.'padding:4px 8px 4px 8px">
';
    // set alert box sorting conditions
    if($sortbyEvent == 0):     $sortMe = $soryByLocation;
    elseif($sortbyEvent == 1): $sortMe = $soryByLocation2;
    elseif($sortbyEvent == 2): $sortMe = $soryByALert;
    elseif($sortbyEvent == 3): $sortMe = $soryByALert2;
    endif;

    // duplicate events will be displayed
    if($sortbyEvent == 0) {
      foreach ($sortMe as $sblk => $sblv) {
        $abt = strtoupper($sblk);                                                                //    capitalize event title
        $abt = str_replace(" ", "&nbsp;", $abt);                                                 //    replace space
        ($titleNewline) ? $spc = ' ' : $spc = '';                                                //    set spacing
        $box .= '  <span style="white-space: nowrap">&nbsp;<a href="'.$alertURL.'?a='
             .$sblv[0][0][2].'" '.$setStyle.' title=" &nbsp;View details"><b>'.$abt
             .'</b></a></span>&nbsp;&nbsp;-';                                                    //    icon & event title
        $csblv = count($sblv);                                                                   //    count each string
        for($i=0;$i<$csblv;$i++) {                                                               //    FOR EACH string of data
          $sblv[$i][0][3] = str_replace(" ", "&nbsp;", $sblv[$i][0][3]);                         //       replace spaces
          $box .= '<span style="white-space: nowrap">&nbsp;'.$sblv[$i][0][4]
               .'&nbsp;<a href="'.$alertURL.'?a='.$sblv[$i][0][2].'#WA'.$sblv[$i][0][6]
               .'" '.$setStyle.' title=" &nbsp;Details for '.$sblv[$i][0][1].' - '
               .$sblv[$i][0][3].'">'.$sblv[$i][0][3]
               .'</a>&nbsp;&nbsp;</span> ';                                                      //        link & details
          }
        ($titleNewline) ? $box .= "<br />\n" : $box .= "&nbsp;&nbsp;&nbsp; " ;                   //      set line break or spaces
      }
    }

    //  duplicate events removed
    if($sortbyEvent == 1) {
      ($titleNewline) ? $spc = ' ' : $spc = '';                                                  //    set spacing
      foreach ($sortMe as $sblk => $sblv) {                                                      //    FOR EACH location with data
        $box .= '  <span style="white-space: nowrap">&nbsp;<a href="'.$alertURL
             .'?a='.$sblv[key($sblv)][0][2].'" '.$setStyle
             .' title=" &nbsp;View details"><b>'.$sblk.'</b></a></span>&nbsp;&nbsp;-';           //       icon & event title
        foreach ($sblv as $sblvk => $sblvv) {                                                    //       FOR EACH string of data
          $abt = strtoupper($sblk);                                                              //         capitalize event title
          $abt = str_replace(" ", "&nbsp;", $abt);
          $sblvv[0][3] = str_replace(" ", "&nbsp;", $sblvv[0][3]);                               //         replace spaces
          $box .= '<span style="white-space: nowrap">&nbsp;'.$sblvv[0][4]
               .'&nbsp;<a href="'.$alertURL.'?a='.$sblvv[0][2].'#WA'.$sblvv[0][6].'" '.$setStyle
               .' title=" &nbsp;Details for '.$sblvv[0][1].' - '.$sblvv[0][3].'">'.$sblvv[0][3]
               .'</a>&nbsp;&nbsp;</span> ';                                                      //         link & details
        }
        ($titleNewline) ? $box .= "<br />\n" : $box .= "&nbsp;&nbsp;&nbsp; " ;                   //         set line break or spaces
    	}
    }

    // duplicate events will be displayed
    if($sortbyEvent == 2) {
      foreach ($sortMe as $sblk => $sblv) {                                                      //      FOR EACH location with data
        $abt = strtoupper($sblv[key($sblv)][0][3]);                                              //        capitalize event title
        $abt = str_replace(" ", "&nbsp;", $abt);                                                 //         replace spaces
       ($titleNewline) ? $spc = ' ' : $spc = '';                                                 //         set spacing
        $box .= '  <span style="white-space: nowrap">'.$sblv[key($sblv)][0][4]
             .'&nbsp;<a href="'.$summaryURL.'" '.$setStyle
             .' title=" &nbsp;View summary"><b>'.$abt.'</b></a></span>';                         //         icon & event title
        $csblv = count($sblv);
        for($i=0;$i<$csblv;$i++) {
          $sblv[$i][0][1] = str_replace(" ", "&nbsp;", $sblv[$i][0][1]);                         //         replace spaces
          $box .= '&nbsp;-&nbsp;<a href="'.$alertURL.'?a='.$sblv[$i][0][2].'#WA'
               .$sblv[$i][0][6].'" '.$setStyle.' title=" &nbsp;Details for '.$sblv[$i][0][1]
               .' - '.$sblv[$i][0][3].'">'.$sblv[$i][0][1].'</a> '.$spc;                         //         create link & location
        }
        ($titleNewline) ? $box .= "<br />\n" : $box .= "&nbsp;&nbsp;&nbsp; " ;                   //         set line break or spaces
      }
    }

    //  duplicate events removed
    if($sortbyEvent == 3) {
      foreach ($sortMe as $sblk => $sblv) {                                                     //      FOR EACH location with data
        $abt = strtoupper($sblv[key($sblv)][0][3]);                                             //         capitalize event title
        $abt = str_replace(" ", "&nbsp;", $abt);                                                //         replace spaces
        ($titleNewline) ? $spc = ' ' : $spc = '';                                               //         set spacing
        $box .= '  <span style="white-space: nowrap">'.$sblv[key($sblv)][0][4]
             .'&nbsp;<a href="'.$summaryURL.'" '.$setStyle
             .' title=" &nbsp;View summary"><b>'.$abt.'</b></a></span>';                        //         icon & event title
        foreach ($sblv as $sblvk => $sblvv) {
          $sblvv[0][1] = str_replace(" ", "&nbsp;", $sblvv[0][1]);                              //         replace spaces
          $box .= '&nbsp;-&nbsp;<a href="'.$alertURL.'?a='.$sblvv[0][2].'#WA'
               .$sblvv[0][6].'" '.$setStyle.' title=" &nbsp;Details for '.$sblvv[0][1]
               .' - '.$sblvv[0][3].'">'.$sblvv[0][1].'</a> '.$spc;                              //         create link & location
        }
        ($titleNewline) ? $box .= "<br />\n" : $box .= "&nbsp;&nbsp;&nbsp; " ;                  //         set line break or spaces
      }
    }
    $box .= " </div>
</div>
";

  }// END IF NOT EMPTY $atData
	
  // writing alert box data to file	
  if($useAlertBox and $noData == '') {                                                      // IF using alert box
    $abfo = fopen($aboxCache , 'w');                                                        //   alert box file open
    if(!$abfo) {                                                                            //   IF not alert box file open cache file for writing
      $noted .= "<!-- unable to open cache file -->\n";                                     //     display remark
    } 
    else {                                                                                  // OR ELSE
      $write = fputs($abfo, "<?php \n".'$alertBox = '. var_export($box, 1).";\n\n?>");      //   write all of the alert box data
      fclose($abfo);                                                                        //   close the cache file
      $noted .= "<!-- Alert box data file updated -->\n";                                   //   display remark
    } 
	}

  // construct big icons
  if(!empty($noA)) {                                                                        // IF there are 'No alerts'
    foreach($noA as $nok => $nov) {                                                         //   FOR EACH 'No alert'
      $ano[][] = array('150'.'|'.$nok.'|'.$alertURL.'|'.$nov[0]
                       .'|'.'1'.'|'.''.'|'.$icons_folder.'|');                              //     IF $addNone, create array with no alert data
    }
    $rssno = $ano;                                                                          //     create array for rss with no alert data
  }
	
  // arrays for sorting icons
  if($useIcons !== 0 and !empty($atData)) {
    foreach ($atData as $aak => $aav) {
    $bic = 0;              // set big icon count to zero
      $caav = count($aav); // count alerts
      for($i=0;$i<$caav;$i++) {
        // by alert - no duplicates
        if($useIcons == 2) { $bi[$aav[$i][11]][$aav[$i][12]] = array($aav[$i][11].'|'.$aav[$i][12].'|'.$alertURL
                             .'|'.$aav[$i][14].'|'.$aav[$i][13].'|'.$aav[$i][0].'|'.$icons_folder.'|');}
        // by alert - with duplicates
        if($useIcons == 1) { $bi[$aav[$i][11]][] = array($aav[$i][11].'|'.$aav[$i][12].'|'.$alertURL.'|'.$aav[$i][14]
                             .'|'.$aav[$i][13].'|'.$aav[$i][0].'|'.$icons_folder.'|');}
        // by location - no duplicates
        if($useIcons == 4) { $bi[$aav[$i][12]][$aav[$i][0]] = array($aav[$i][11].'|'.$aav[$i][12].'|'.$alertURL
                             .'|'.$aav[$i][14].'|'.$aav[$i][13].'|'.$aav[$i][0].'|'.$icons_folder.'|');}
        // by location - with duplicates
        if($useIcons == 5) { $bi[$aav[$i][12]][] = array($aav[$i][11].'|'.$aav[$i][12].'|'.$alertURL.'|'.$aav[$i][14]
                             .'|'.$aav[$i][13].'|'.$aav[$i][0].'|'.$icons_folder.'|');}
        $bic++; // increment counter for each alert per location
      }
      if($useIcons == 3) { $bi[$aav[0][11]][] = array($aav[0][11].'|'.$aav[0][12].'|'.$alertURL.'|'.$aav[0][14]
                           .'||'.$aav[0][0].'|'.$icons_folder.'|'.$bic);}
    }
    // top icons
    if($useIcons == 1 or $useIcons == 2) {  // IF #1 or #2 is selected
      ksort($bi);                           //   sort by key
    }
    else {                                  // OR ELSE 
      if($locSort == 1) {                   //   IF sort icons by location alphabetcally
        ksort($bi);                         //     sort by key
      }
    }
  }

  if($addNone) {	
    $bi = array_merge($bi,$ano);                                                                         //   merge alerts with no alerts
  }
  $k=1;
  foreach($bi as $tik => $tiv) {                                                                         // FOR EACH location with alerts (and no alerts)
    foreach($tiv as $tivk => $tivv) {                                                                    //   FOR EACH alert
      // list = sev code, loc name, alert URL, loc code, alert sequence, title, icon folder, alert count
      list($scode, $lname, $aurl, $lcode, $aseq, $titl, $icnf, $alrtc) = explode('|', $tivv[0] . '|||'); //     create a list
      $ai[$k] = create_bi($lname, $aurl, $lcode, $aseq, $titl, $icnf, $scode, $alrtc);                   //     create all icons array
      $k++;
    }
  }
  // construct icon output if NONE
  $bigIcos = $ai;
  if(empty($atData) and $shoNone and !$addNone) {                                                         // IF there are no big icons (no alerts)
      $bigIcos = '';	
      $bigIcos[1] = '<a href="'.$summaryURL
                    .'" title=" &nbsp;Summary" style="width:99%"><br /><img src="'
                    .$icons_folder
                    .'/A-none.png" alt="No alerts" width="74" height="18" /></a>';
	}

  // limit menu bar icons
  $icount = count($bigIcos);                                                               // count the big icons
  if($iconLimit !== 0 and $iconLimit < $icount) {                                          // IF within icon limit
    $bigIcos = array_slice($bigIcos, 0, $iconLimit);                                       //   remove the last x number of icons
    $idiff = $icount - $iconLimit;                                                         //   count the icons removed
    ($idiff == 1) ? $diff = "other" : $diff = "others";                                    //   set plural or singular text
    $otherIcos = array(" <br />\n".' <a href="'.$summaryURL
                .'" title=" View the summary" style="width:99%; text-decoration: none;">'
                ."+$idiff $diff</a>");                                                     //   assembled remaining icons text
    $bigIcos = array_merge($bigIcos,$otherIcos);                                           //   merge icons and remaining icons
  }

  if($noData == '' and $useIcons !==0) {                                                    // IF there is data and able to write cache file
    $bifo = fopen($iconCache , 'w');                                                        //  icon file open
    if(!$bifo) {                                                                            //  IF not alert box file open cache file for writing
      $noted .= "<!-- unable to open big icon cache file -->\n";                            //   display remark
    } 
    else {                                                                                  //  OR ELSE
      $write = fputs($bifo, "<?php \n".'$bigIcons = '. var_export($bigIcos, 1).";\n\n");    //   write all of the alert box data
      fclose($bifo);                                                                        //   close the cache file
      $noted .= "<!-- Icon data file updated -->\n";                                        //   display remark
    }
	}

  // create RSS Feed file	
  if($useXML and $noData == '') {                                                         // IF using RSS feed and there is data
    if(!empty($atData)) {                                                                 //   IF there is data
      foreach ($atData as $aak => $aav) {                                                 //     FOR EACH location with data
        foreach ($aav as $avk => $avv) {                                                  //       FOR EACH alert data, creat RSS data array
          $rssData[$avv[12]][] = array($avv[11],$avv[12],$avv[14],
                                       $avv[0],$avv[13],$avv[8]);
       }
     }
   }
  $ma = array_merge($rssData,$norss);                                                   //   merge alerts with no alerts
  // construct RSS/XML
  $xml_zone = '<?xml version="1.0" encoding="iso-8859-1"?>
<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:atom="http://www.w3.org/2005/Atom">
<!-- version 1.00 - 10-Jun-2012 -->
<channel>
  <title>'.$rssTitle.'</title>
  <link>'.$_SERVER["SERVER_NAME"].'/</link>
  <description>Courtesy of the National Weather Service</description>
  <language>en-us</language>
  <generator>RSS feed version 1.00 - 10-Jun-2012</generator>
  <copyright>Curly at ricksturf.com</copyright>
  <pubDate>'.date("Y-m-d\TH:i:s T").'</pubDate>
  <ttl>15</ttl>
  <lastBuildDate>'.date("Y-m-d\TH:i:s T").'</lastBuildDate>
'; 
  foreach($ma as $xdk => $xdv) {
    $xml_zone .= '  <item>
    <title>'.$xdk.'</title>
    <link>href="http://'.$_SERVER['SERVER_NAME'].'/"</link>
    <pubDate>'.date("D, d M Y H:i:s T").'</pubDate>
    <description>';
      $cxdv = count($xdv);
      for($i=0;$i<$cxdv;$i++) {
      $desc_length = strlen($xdv[$i][5]);
      if($desc_length <= 50) { $xdv[$i][5] = $xdv[$i][5]; $dets = 'There are no details';}
        else { 
          if($desc_length >= 221) {
            $pos = strpos($xdv[$i][5], ' ', 221);
            $xdv[$i][5] = trim(substr($xdv[$i][5], 0, $pos)).' [more]...'; $dets = 'Click for complete details';
          }
          else {
            $xdv[$i][5] = $xdv[$i][5].' [more]...';
            $dets = 'Click for complete details';
          }
        }
        $xml_zone .= "&lt;a href=&quot;".$alertURL."?a=".$xdv[$i][2]."#WA".$xdv[$i][4]
				."&quot; title=&quot;".$dets."&quot;&gt;".$xdv[$i][3]
				."&lt;/a&gt; &lt;br/&gt;".$xdv[$i][5]."&lt;br/&gt;&lt;br/&gt;";
      }
      $xml_zone .= ' &lt;br/&gt;</description>
  </item>
';
    }
    $xml_zone .= '</channel>
</rss>';
    $rssfo = fopen('nws-rssfeed.xml' , 'w');                                                // icon file open
    if(!$rssfo) {                                                                           // IF not alert box file open cache file for writing
      $noted .= "<!-- unable to open rss feed cache file -->\n";                            //   display remark
    } 
    else {                                                                                  // OR ELSE
      $write = fputs($rssfo, $xml_zone);                                                    //   write all of the alert box data
      fclose($rssfo);                                                                       //   close the cache file
      $noted .= "<!-- RSS feed file updated -->\n";                                         //   display remark
    } 
  }
} // END IF UPDATE CACHE

////////////////////////////////////////////

// FUNCTION - get color, severity, icon
function get_icon($evnt) {
  $a = array();
  $alert_types = array (
    array('N'=>'Tornado Warning',                 'C'=>'#A00', 'S'=>'0', 'I'=>'TOR.gif'),
    array('N'=>'Severe Thunderstorm Warning',     'C'=>'#B11', 'S'=>'1', 'I'=>'SVR.gif'),
    array('N'=>'Blizzard Warning',                'C'=>'#D00', 'S'=>'2', 'I'=>'WSW.gif'),
    array('N'=>'Hurricane Force Wind Warning',    'C'=>'#D00', 'S'=>'3', 'I'=>'HUW.gif'),
    array('N'=>'Heavy Snow Warning',              'C'=>'#D00', 'S'=>'4', 'I'=>'WSW.gif'),
    array('N'=>'Hurricane Warning',               'C'=>'#D00', 'S'=>'5', 'I'=>'HUW.gif'),
    array('N'=>'Hurricane Wind Warning',          'C'=>'#D00', 'S'=>'6', 'I'=>'HUW.gif'),
    array('N'=>'Tsunami Warning',                 'C'=>'#D00', 'S'=>'7', 'I'=>'SMW.gif'),
    array('N'=>'Tropical Storm Warning',          'C'=>'#D00', 'S'=>'8', 'I'=>'TRW.gif'),
    array('N'=>'Winter Storm Warning',            'C'=>'#D00', 'S'=>'9', 'I'=>'WSW.gif'),
    array('N'=>'Winter Weather Warning',          'C'=>'#D00', 'S'=>'10', 'I'=>'WSW.gif'),
    array('N'=>'Ashfall Warning',                 'C'=>'#D00', 'S'=>'11', 'I'=>'EWW.gif'),
    array('N'=>'Avalanche Warning',               'C'=>'#D00', 'S'=>'12', 'I'=>'WSW.gif'),
    array('N'=>'Civil Danger Warning',            'C'=>'#D00', 'S'=>'13', 'I'=>'WSW.gif'),
    array('N'=>'Coastal Flood Warning',           'C'=>'#D00', 'S'=>'14', 'I'=>'CFW.gif'),
    array('N'=>'Dust Storm Warning',              'C'=>'#D00', 'S'=>'15', 'I'=>'EWW.gif'),
    array('N'=>'Earthquake Warning',              'C'=>'#D00', 'S'=>'16', 'I'=>'WSW.gif'),
    array('N'=>'Extreme Cold Warning',            'C'=>'#D00', 'S'=>'17', 'I'=>'HZW.gif'),
    array('N'=>'Excessive Heat Warning',          'C'=>'#D00', 'S'=>'18', 'I'=>'EHW.gif'),
    array('N'=>'Extreme Wind Warning',            'C'=>'#D00', 'S'=>'19', 'I'=>'EWW.gif'),
    array('N'=>'Fire Warning',                    'C'=>'#D00', 'S'=>'20', 'I'=>'WSW.gif'),
    array('N'=>'Flash Flood Warning',             'C'=>'#D00', 'S'=>'21', 'I'=>'FFW.gif'),
    array('N'=>'Flood Warning',                   'C'=>'#D00', 'S'=>'22', 'I'=>'FFW.gif'),
    array('N'=>'Freeze Warning',                  'C'=>'#D00', 'S'=>'23', 'I'=>'FZW.gif'),
    array('N'=>'Gale Warning',                    'C'=>'#D00', 'S'=>'24', 'I'=>'HWW.gif'),
    array('N'=>'Hard Freeze Warning',             'C'=>'#D00', 'S'=>'25', 'I'=>'HZW.gif'),
    array('N'=>'Hazardous Materials Warning',     'C'=>'#D00', 'S'=>'26', 'I'=>'WSW.gif'),
    array('N'=>'Hazardous Seas Warning',          'C'=>'#D00', 'S'=>'27', 'I'=>'SMW.gif'),
    array('N'=>'High Surf Warning',               'C'=>'#D00', 'S'=>'28', 'I'=>'SMW.gif'),
    array('N'=>'High Wind Warning',               'C'=>'#D00', 'S'=>'29', 'I'=>'HWW.gif'),
    array('N'=>'Ice Storm Warning',               'C'=>'#D00', 'S'=>'30', 'I'=>'ISW.gif'),
    array('N'=>'Lake Effect Snow Warning',        'C'=>'#D00', 'S'=>'31', 'I'=>'SMW.gif'),
    array('N'=>'Lakeshore Flood Warning',         'C'=>'#D00', 'S'=>'32', 'I'=>'SMW.gif'),
    array('N'=>'Law Enforcement Warning',         'C'=>'#D00', 'S'=>'33', 'I'=>'WSA.gif'),
    array('N'=>'Nuclear Power Plant Warning',     'C'=>'#D00', 'S'=>'34', 'I'=>'WSW.gif'),
    array('N'=>'Radiological Hazard Warning',     'C'=>'#D00', 'S'=>'35', 'I'=>'WSW.gif'),
    array('N'=>'Red Flag Warning',                'C'=>'#D00', 'S'=>'36', 'I'=>'FWW.gif'),
    array('N'=>'River Flood Warning',             'C'=>'#D00', 'S'=>'37', 'I'=>'FLW.gif'),
    array('N'=>'Shelter In Place Warning',        'C'=>'#D00', 'S'=>'38', 'I'=>'WSW.gif'),
    array('N'=>'Sleet Warning',                   'C'=>'#D00', 'S'=>'39', 'I'=>'IPW.gif'),
    array('N'=>'Special Marine Warning',          'C'=>'#D00', 'S'=>'40', 'I'=>'SMW.gif'),
    array('N'=>'Typhoon Warning',                 'C'=>'#D00', 'S'=>'41', 'I'=>'WSW.gif'),
    array('N'=>'Volcano Warning',                 'C'=>'#D00', 'S'=>'42', 'I'=>'WSW.gif'),
    array('N'=>'Wind Chill Warning',              'C'=>'#D00', 'S'=>'43', 'I'=>'WCW.gif'),
    array('N'=>'Storm Warning',                   'C'=>'#D00', 'S'=>'44', 'I'=>'SVR.gif'),

    array('N'=>'Air Stagnation Advisory',         'C'=>'#F60', 'S'=>'50', 'I'=>'SCY.gif'),
    array('N'=>'Ashfall Advisory',                'C'=>'#F60', 'S'=>'51', 'I'=>'WSW.gif'),
    array('N'=>'Blowing Dust Advisory',           'C'=>'#F60', 'S'=>'52', 'I'=>'HWW.gif'),
    array('N'=>'Blowing Snow Advisory',           'C'=>'#F60', 'S'=>'53', 'I'=>'WSA.gif'),
    array('N'=>'Coastal Flood Advisory',          'C'=>'#F60', 'S'=>'54', 'I'=>'FLS.gif'),
    array('N'=>'Small Craft Advisory',            'C'=>'#F60', 'S'=>'55', 'I'=>'SCY.gif'),
    array('N'=>'Dense Fog Advisory',              'C'=>'#F60', 'S'=>'56', 'I'=>'FGY.gif'),
    array('N'=>'Dense Smoke Advisory',            'C'=>'#F60', 'S'=>'57', 'I'=>'SMY.gif'),
    array('N'=>'Brisk Wind Advisory',             'C'=>'#F60', 'S'=>'58', 'I'=>'WIY.gif'),
    array('N'=>'Flash Flood Advisory',            'C'=>'#F60', 'S'=>'59', 'I'=>'FLS.gif'),
    array('N'=>'Flood Advisory',                  'C'=>'#F60', 'S'=>'60', 'I'=>'FLS.gif'),
    array('N'=>'Freezing Drizzle Advisory',       'C'=>'#F60', 'S'=>'61', 'I'=>'SWA.gif'),
    array('N'=>'Freezing Fog Advisory',           'C'=>'#F60', 'S'=>'62', 'I'=>'FZW.gif'),
    array('N'=>'Freezing Rain Advisory',          'C'=>'#F60', 'S'=>'63', 'I'=>'SWA.gif'),
    array('N'=>'Freezing Spray Advisory',         'C'=>'#F60', 'S'=>'64', 'I'=>'SWA.gif'),
    array('N'=>'Frost Advisory',                  'C'=>'#F60', 'S'=>'65', 'I'=>'FRY.gif'),
    array('N'=>'Heat Advisory',                   'C'=>'#F60', 'S'=>'66', 'I'=>'HTY.gif'),
    array('N'=>'Heavy Freezing Spray Warning',    'C'=>'#F60', 'S'=>'67', 'I'=>'SWA.gif'),
    array('N'=>'High Surf Advisory',              'C'=>'#F60', 'S'=>'68', 'I'=>'SUY.gif'),
    array('N'=>'Hydrologic Advisory',             'C'=>'#F60', 'S'=>'69', 'I'=>'FLS.gif'),
    array('N'=>'Lake Effect Snow Advisory',       'C'=>'#F60', 'S'=>'70', 'I'=>'WSA.gif'),
    array('N'=>'Lake Effect Snow and Blowing Snow Advisory', 'C'=>'#F60', 'S'=>'71', 'I'=>'WSA.gif'),
    array('N'=>'Lake Wind Advisory',              'C'=>'#F60', 'S'=>'72', 'I'=>'LWY.gif'),
    array('N'=>'Lakeshore Flood Advisory',        'C'=>'#F60', 'S'=>'73', 'I'=>'FLS.gif'),
    array('N'=>'Low Water Advisory',              'C'=>'#F60', 'S'=>'74', 'I'=>'FFA.gif'),
    array('N'=>'Sleet Advisory',                  'C'=>'#F60', 'S'=>'75', 'I'=>'SWA.gif'),
    array('N'=>'Snow Advisory',                   'C'=>'#F60', 'S'=>'76', 'I'=>'WSA.gif'),
    array('N'=>'Snow and Blowing Snow Advisory',  'C'=>'#F60', 'S'=>'77', 'I'=>'WSA.gif'),
    array('N'=>'Tsunami Advisory',                'C'=>'#F60', 'S'=>'78', 'I'=>'SWA.gif'),
    array('N'=>'Wind Advisory',                   'C'=>'#F60', 'S'=>'79', 'I'=>'WIY.gif'),
    array('N'=>'Wind Chill Advisory',             'C'=>'#F60', 'S'=>'80', 'I'=>'WCY.gif'),
    array('N'=>'Winter Weather Advisory',         'C'=>'#F60', 'S'=>'81', 'I'=>'WWY.gif'),

    array('N'=>'Tornado Watch',                   'C'=>'#F33', 'S'=>'90', 'I'=>'TOA.gif'),
    array('N'=>'Severe Thunderstorm Watch',       'C'=>'#F31', 'S'=>'91', 'I'=>'SVA.gif'),
    array('N'=>'High Wind Watch',                 'C'=>'#F33', 'S'=>'92', 'I'=>'WIY.gif'),
    array('N'=>'Hurricane Force Wind Watch',      'C'=>'#F33', 'S'=>'93', 'I'=>'HWW.gif'),
    array('N'=>'Hurricane Watch',                 'C'=>'#F33', 'S'=>'94', 'I'=>'HUA.gif'),
    array('N'=>'Hurricane Wind Watch',            'C'=>'#F33', 'S'=>'95', 'I'=>'HWW.gif'),
    array('N'=>'Typhoon Watch',                   'C'=>'#F33', 'S'=>'96', 'I'=>'HUA.gif'),
    array('N'=>'Avalanche Watch',                 'C'=>'#F33', 'S'=>'97', 'I'=>'WSA.gif'),
    array('N'=>'Blizzard Watch',                  'C'=>'#F33', 'S'=>'98', 'I'=>'WSA.gif'),
    array('N'=>'Coastal Flood Watch',             'C'=>'#F33', 'S'=>'99', 'I'=>'CFA.gif'),
    array('N'=>'Excessive Heat Watch',            'C'=>'#F33', 'S'=>'100', 'I'=>'EHA.gif'),
    array('N'=>'Extreme Cold Watch',              'C'=>'#F33', 'S'=>'101', 'I'=>'HZA.gif'),
    array('N'=>'Flash Flood Watch',               'C'=>'#F33', 'S'=>'102', 'I'=>'FFA.gif'),
    array('N'=>'Fire Weather Watch',              'C'=>'#F33', 'S'=>'103', 'I'=>'FWA.gif'),
    array('N'=>'Flood Watch',                     'C'=>'#F33', 'S'=>'105', 'I'=>'FFA.gif'),
    array('N'=>'Freeze Watch',                    'C'=>'#F33', 'S'=>'105', 'I'=>'FZA.gif'),
    array('N'=>'Gale Watch',                      'C'=>'#F33', 'S'=>'106', 'I'=>'GLA.gif'),
    array('N'=>'Hard Freeze Watch',               'C'=>'#F33', 'S'=>'107', 'I'=>'HZA.gif'),
    array('N'=>'Hazardous Seas Watch',            'C'=>'#F33', 'S'=>'108', 'I'=>'SUY.gif'),
    array('N'=>'Heavy Freezing Spray Watch',      'C'=>'#F33', 'S'=>'109', 'I'=>'SWA.gif'),
    array('N'=>'Lake Effect Snow Watch',          'C'=>'#F33', 'S'=>'110', 'I'=>'WSA.gif'),
    array('N'=>'Lakeshore Flood Watch',           'C'=>'#F33', 'S'=>'111', 'I'=>'FFA.gif'),
    array('N'=>'Tropical Storm Watch',            'C'=>'#F33', 'S'=>'112', 'I'=>'TRA.gif'),
    array('N'=>'Tropical Storm Wind Watch',       'C'=>'#F33', 'S'=>'113', 'I'=>'WIY.gif'),
    array('N'=>'Tsunami Watch',                   'C'=>'#F33', 'S'=>'114', 'I'=>'WSA.gif'),
    array('N'=>'Wind Chill Watch',                'C'=>'#F33', 'S'=>'115', 'I'=>'WCA.gif'),
    array('N'=>'Winter Storm Watch',              'C'=>'#F33', 'S'=>'116', 'I'=>'SRA.gif'),
    array('N'=>'Winter Weather Watch',            'C'=>'#F33', 'S'=>'117', 'I'=>'WSA.gif'),
    array('N'=>'Storm Watch',                     'C'=>'#F33', 'S'=>'118', 'I'=>'SRA.gif'),

    array('N'=>'Coastal Flood Statement',         'C'=>'#C70', 'S'=>'120', 'I'=>'FFS.gif'),
    array('N'=>'Flash Flood Statement',           'C'=>'#C70', 'S'=>'121', 'I'=>'FFS.gif'),
    array('N'=>'Rip Current Statement',           'C'=>'#C70', 'S'=>'122', 'I'=>'RVS.gif'),
    array('N'=>'Flood Statement',                 'C'=>'#C70', 'S'=>'123', 'I'=>'FFS.gif'),
    array('N'=>'Hurricane Statement',             'C'=>'#C70', 'S'=>'124', 'I'=>'HUA.gif'),
    array('N'=>'Lakeshore Flood Statement',       'C'=>'#C70', 'S'=>'125', 'I'=>'FFS.gif'),
    array('N'=>'Marine Weather Statement',        'C'=>'#C70', 'S'=>'126', 'I'=>'MWS.gif'),
    array('N'=>'Public Information Statement',    'C'=>'#C70', 'S'=>'127', 'I'=>'PNS.gif'),
    array('N'=>'River Flood Statement',           'C'=>'#C70', 'S'=>'128', 'I'=>'FLS.gif'),
    array('N'=>'River Statement',                 'C'=>'#C70', 'S'=>'129', 'I'=>'RVS.gif'),
    array('N'=>'Severe Weather Statement',        'C'=>'#F33', 'S'=>'130', 'I'=>'SVS.gif'),
    array('N'=>'Special Weather Statement',       'C'=>'#C70', 'S'=>'131', 'I'=>'SPS.gif'),
    array('N'=>'Beach Hazards Statement',         'C'=>'#C70', 'S'=>'132', 'I'=>'SPS.gif'),
    array('N'=>'Tropical Statement',              'C'=>'#C70', 'S'=>'133', 'I'=>'HLS.gif'),
    array('N'=>'Typhoon Statement',               'C'=>'#C70', 'S'=>'134', 'I'=>'TRA.gif'),

    array('N'=>'Air Quality Alert',               'C'=>'#06C', 'S'=>'140',  'I'=>'SPS.gif'),
    array('N'=>'Significant Weather Alert',       'C'=>'#F33', 'S'=>'141',  'I'=>'SWA.gif'),
    array('N'=>'Child Abduction Emergency',       'C'=>'#093', 'S'=>'142', 'I'=>'SPS.gif'),
    array('N'=>'Civil Emergency Message',         'C'=>'#093', 'S'=>'143',  'I'=>'SPS.gif'),
    array('N'=>'Local Area Emergency',            'C'=>'#093', 'S'=>'144',  'I'=>'SPS.gif'),
    array('N'=>'Extreme Fire Danger',             'C'=>'#D00', 'S'=>'82',  'I'=>'WSW.gif'),
    array('N'=>'Coastal Hazard',                  'C'=>'#C70', 'S'=>'135',  'I'=>'CFS.gif'),
    array('N'=>'Short Term',                      'C'=>'#093', 'S'=>'136',  'I'=>'NOW.gif'),
    array('N'=>'911 Telephone Outage',            'C'=>'#36C', 'S'=>'137', 'I'=>'SPS.gif'),
    array('N'=>'Evacuation Immediate',            'C'=>'EA00', 'S'=>'45',  'I'=>'SVW.gif'),
  );

  foreach ($alert_types as $a_type)  {
    if(strpos($evnt,$a_type['N']) !== false){
      $a['color']    = $a_type['C'];
      $a['severity'] = $a_type['S'];
      $a['icon']     = $a_type['I'];
      return $a;
    }
  }

   // if alert type is not in list
  if (strpos($evnt,"Warning") !== false) {
    $a['color']    = "#D11";
    $a['severity'] = 46;
    $a['icon']     = 'SVW.gif';
    return $a;
  }
  if (strpos($evnt,"Advisory") !== false) {
    $a['color']    = "#F60";
    $a['severity'] = 83;
    $a['icon']     = 'SWA.gif';
    return $a;
  }
  if (strpos($evnt,"Watch") !== false) {
    $a['color']    = "#F30";
    $a['severity'] = 119;
    $a['icon']     = 'SWA.gif';
    return $a;
  }
  if (strpos($evnt,"Statement") !== false) {
    $a['color']    = "#C70";
    $a['severity'] = 139;
    $a['icon']     = 'SWA.gif';
    return $a;
  }
  if (strpos($evnt,"Air") !== false) {
    $a['color']    = "#06C";
    $a['severity'] = 140;
    $a['icon']     = 'SPS.gif';
    return $a;
  }
  if (strpos($evnt,"Short") !== false) {
    $a['color']    = "#093";
    $a['severity'] = 136;
    $a['icon']     = 'NOW.gif';
    return $a;
  }
  if (strpos($evnt,"Emergency") !== false) {
    $a['color']    = "#093";
    $a['severity'] = 145;
    $a['icon']     = 'SPS.gif';
    return $a;
  }
  if (strpos($evnt,"Outage") !== false) {
    $a['color']    = "#36C";
    $a['severity'] = 146;
    $a['icon']     = 'SPS.gif';
    return $a;
  }
  if (strpos($evnt,"No alerts") !== false) {
    $a['color']    = "#333";
    $a['severity'] = 150;
    $a['icon']     = 'BNK.gif';
    return $a;
  }

  // if no matches yet, set default
  $a['color'] = "#333";
  $a['severity'] = 149;
  $a['icon'] = 'SPS.gif';
  return $a;
}


$fc = '';                      // fault count
// FUNCTION - get data
function get_nwsalerts($url)    {
  global $noted, $fc;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:14.0) Gecko/20100101 Firefox/14.0.1  (.NET CLR 4.0.20506)');
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);  // 3 sec timeout
  curl_setopt($ch, CURLOPT_TIMEOUT, 2);         // 2 sec timeout
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  // write the response to a variable
  $data = curl_exec($ch);
  if($data === false or preg_match('/Database Connection Issue/Ui',$data)) {  // could not connect
    $noted .= "\n<!-- cURL error: ". curl_error($ch) . " -->\n";
    $noted .= "<!-- $url  -->\n";
    $surl = preg_replace('/.*\.php/','',$url);
    $to = preg_replace('/ after.*/','',curl_error($ch));
    $fc = date("D g:i:s a").' - '.$to." - ". $surl;       // fault count
  }
  curl_close($ch);
  $data = simplexml_load_string($data);
  return $data;
}// end get_nwsalerts

// FUNCTION - remove 'no alerts' if similar location code has alert
function key_compare($key1, $key2){
  if ($key1 == $key2) return 0;
  else if ($key1 > $key2) return 1;
  else return -1;
}// end remove 'no alerts'

// FUNCTION - sort array by severity
function sev_sort($a, $b){
  if($a[11] == $b[11]){
    if($a[12] == $b[12]){ return 0; }
    elseif($a[12] > $b[12]){ return 1; }
    elseif($a[12] < $b[12]){ return -1; }
  }
  elseif($a[11] > $b[11]){ return 1; }
  elseif($a[11] < $b[11]){ return -1; }
} // end u-sort function

// FUNCTION - sort array by severity
function ico_sort($a, $b){
  if($a[2] == $b[2]){
    if($a[6] == $b[6]){ return 0; }
    elseif($a[6] > $b[6]){ return 1; }
    elseif($a[6] < $b[6]){ return -1; }
  }
  elseif($a[2] > $b[2]){ return 1; }
  elseif($a[2] < $b[2]){ return -1; }
} // end u-sort function

// FUNCTION - set background color (severity color code)
function get_scc($scc) {
  global $tc, $bc;                        // make colors global
  $tc = 'color: #000;';
  if($scc >= 0 and $scc <= 49) {          // warning background
    $bc = 'background-color:#CC0000;';
    $tc = 'color: white;';
  }
  if($scc >= 50 and $scc <= 89) {         // advisory background
    $bc = 'background-color:#FFCC00;';
  }
  if($scc >= 90 and $scc <= 119) {        // watch background
    $bc = 'background-color:#FF9900;';
  }
  if($scc >= 120 and $scc <= 149) {       // other background
    $bc = 'background-color:#E6E6E3;';
  }
  if($scc >= 150) {                       // none background
     $bc = 'background-color:#E6E6E3;';
  }
}// end background color function

// FUNCTION - convert icon name into icon
function conv_icon($if,$ic,$ti) {
  (!empty($ti)) ? $ti = 'alt="'.$ti.'" title=" '.$ti.'"' : $ti = 'alt=" " title=" "';
  $ico = ' <img src="'.$if.'/'.$ic.'" width="12" height="12" '.$ti.' />';
  return $ico;
}// end convert icon function

// FUNCTION - convert big icons
function create_bi($a,$b,$c,$d,$e,$g,$h,$i) {
  if($h >= 0 and $h <= 49) { $bi = "A-warn.png"; }
  if($h >= 50 and $h <= 89) { $bi = "A-advisory.png";	}
  if($h >= 90 and $h <= 119) { $bi = "A-watch.png"; }	
  if($h >= 120 and $h <= 139) { $bi = "A-statement.png"; }	
  if($h == 140) { $bi = "A-air.png"; }	
  if($h >= 141 and $h <= 149) { $bi = "A-alert.png"; }
  if($h == 150) { $bi = "A-none.png"; }
  if($i < 2) {$i = '';}
  if($i >= 2) {$i = $i - 1;}
  ($i == 1) ? $alrts = 'alert' : $alrts = 'alerts';
  if($i >= 1) {$i = '&nbsp; +'.$i.' additional '.$alrts;}
  $bico =  ' <a href="'.$b.'?a='.$c.'#WA'.$d.'" title=" &nbsp;Details for '.$a
           .'&nbsp;'.$e.$i.'" style="width:99%; text-decoration:none; padding-top:3px">'.$a.'<br /><img src="'.$g
           .'/'.$bi.'" alt=" &nbsp;Details for '.$a.'&nbsp;'.$e.$i
           .'" width="74" height="18" style="border:none; padding-bottom:3px"/><br /></a>
';
  return $bico;
}// end convert big icons function

function load_timer() { // mchallis added function
  list($usec, $sec) = explode(" ", microtime());
  return ((float) $usec + (float) $sec);
} // end function load_timeR

$time_stopTotal = load_timer();
$total_times = '';
$total_times += ($time_stopTotal - $time_startTotal);
$total_time = sprintf("%01.4f", round($time_stopTotal - $time_startTotal, 4));
$total_time;
$noted .= "<!-- Total script process time: $total_time seconds -->\n";                 //   display remark

echo $noted;

// create status file
$notes = $noted;
$notes = preg_replace('/<!-- /','',$notes);
$notes = preg_replace('/ -->\n/',"\n",$notes);
$notes = "Script characteristics on last page load:\n\nLast ran: ".$timenow."\n\n".$notes;

$notesfo = fopen($cacheFileDir.'nws-notes.txt' , 'w');                                  // open note file
$write = fputs($notesfo, $notes);                                                       // write notes to file
fclose($notesfo);                                                                       // close the file

?>
