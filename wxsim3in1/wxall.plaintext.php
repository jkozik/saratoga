<?php
/*
# 3in1 wxall.php V.4
Modded and combined WXSIM plaintext.txt and lastret.txt-parser by Henkka, nordicweather.net, July 2010
Orginal plaintext-parser.php script by Ken True - webmaster@saratoga-weather.org
Many thanks also to Snowi & jwwd for testing the script :)

Script released "as it is", without any warranty. No support are promised.
License http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
*/

include_once $wxallmainfolderfull.'wxall.functions.php';

// Unit-specific checks
if (preg_match("|mm|",$uoms[1])) {$traceam = 0.30;} else {$traceam = 0.019;} 
if (preg_match("|C|",$uoms[0])) {$tempuom="C";} else {$tempuom="F";} 
if (preg_match("|m/s|",$uoms[2])) {$useica = true;} else {$useica = false;} 

#########################################################
# Translations, borrowed from Ken's plaintext-parser ;)

// load the config file
$config = file($plaintextfolderfull."plaintext-parser-data.txt");  // 
// load and merge the language file (if it exists)
if ($lang <> 'en' and file_exists($plaintextfolderfull."plaintext-parser-lang-$lang.txt") ) {
  $doTranslate = true;
  $lfile = file($plaintextfolderfull."plaintext-parser-lang-$lang.txt");
  foreach ($lfile as $val) {
    array_push($config,$val);
  }
  $Status .= "<!-- translation file for '$lang' loaded -->\n";
  if (strpos($UTFLang,$lang) > 0) {$useCharSet = 'UTF-8'; $Status .= "<!-- using UTF-8 -->\n";}
} else {
  $doTranslate = false;
  if($lang <> 'en') {
    $Status .= "<!-- translation file for '$lang' not found -->\n";
    $lang = 'en';
  }
}

// Initialize -- read in control file and place in $Conditions and $Precip
//  for later use

$LanguageLookup = array();
$WindLookup = array( // initialized by wind-direction abbreviations for icons
// NOTE: don't change these .. use LANGLOOKUP entries in the plaintext-parser-LL.txt
// translation control file instead.
'north' => 'N',
'north-northeast' => 'NNE',
'northeast' => 'NE',
'east-northeast' => 'ENE',
'east' => 'E',
'east-southeast' => 'ESE',
'southeast' => 'SE',
'south-southeast' => 'SSE',
'south' => 'S',
'south-southwest' => 'SSW',
'southwest' => 'SW',
'west-southwest' => 'WSW',
'west' => 'W',
'west-northwest' => 'WNW',
'northwest' => 'NW',
'north-northwest' => 'NNW'
);

$BeaufortText = array('Calm', 'Light air', 'Light breeze','Gentle breeze', 'Moderate breeze', 'Fresh breeze', 'Strong breeze', 'Near gale', 'Gale', 'Strong gale', 'Storm', 'Violent storm', 'Hurricane' );
$BeaufortKTS = array(1,4,7,11,17,22,28,34,41,48,56,64,64);
$BeaufortMPH = array(1,4,8,13,19,25,32,39,47,55,64,73,73);
$BeaufortKPH = array(1,6,12,20,30,40,51,63,76,88,103,118,118);
$BeaufortMS  = array(0.2,1.6,3.4,5.5,8.0,10.8,13.9,17.2,20.8,24.5,28.5,32.7,32.7);

reset($config);
// $Status .= "<!-- config: \n" . print_r($config,true) . " -->\n";
foreach ($config as $key => $rec) { // load the parser condition strings
  $recin = trim($rec);
  if ($recin and substr($recin,0,1) <> '#') { // got a non comment record
    list($type,$keyword,$dayicon,$nighticon,$condition) = explode('|',$recin . '|||||');
	
	if (isset($type) and strtolower($type) == 'cond' and isset($condition)) {
	  $Conditions["$keyword"] = "$dayicon\t$nighticon\t$condition";
	}
	if (isset($type) and strtolower($type) == 'precip' and isset($nighticon)) {
	  $Precip["$keyword"] = "$dayicon\t$nighticon";
	}
	if (isset($type) and strtolower($type) == 'snow' and isset($nighticon)) {
	  $Snow["$keyword"] = "$dayicon\t$nighticon";
	}
	if (isset($type) and strtolower($type) == 'lang' and isset($dayicon)) {
	  $Language["$keyword"] = "$dayicon";
    } 
	if (isset($type) and strtolower($type) == 'langlookup' and isset($dayicon)) {
	  $LanguageLookup["$keyword"] = "$dayicon";
    } 
	if (isset($type) and strtolower($type) == 'charset' and isset($keyword)) {
	  $useCharSet = trim($keyword);
	  $Status .= "<!-- using charset '$useCharSet' -->\n";
    } 
	if (isset($type) and strtolower($type) == 'notavail' and isset($keyword)) {
	  $notAvail = trim($keyword);
    } 
  } // end if not comment or blank
} // end loading of $Conditions and $Precip

if (count($LanguageLookup) < 1) {$doTranslate = false; }

// Setup an line with some words and translate them
$words = "PoP|Conditions|Temperature|Wind|Precipitation|Snow|Description and notes|Snowdepth|night|PoP|Conditions|Temperature|Wind|Precipitation|Snow|Description and notes|Detailed forecast|Hour-by-hour|Meteogram|Graphs coming soon|Dewpoint|Windchill|Heat index|Snowdepth|Thunderstorm probability|unlikely|possible|likely|Pressure|Solar radiation|ET|Grass temperature|Surface temperature|Soil|Moisture|Grass & Soil|mm/h|Calm|No precip|Windch.|Heat.|Windgust|Thunder|Show|within 80km from|Thunderstorm probability within 80km from|Max|Min";
reset ($LanguageLookup);
foreach ($LanguageLookup as $key => $replacement) {
$words = str_replace($key,$replacement,$words);
}
list($PoPtxt,$condtxt,$Temptxt,$winddtxt,$Prectxt,$Snowtxt,$Desctxt,$Snowdtxt,$night,$PoPtxt,$condtxt,$Temptxt,$Wind,$Prectxt,$Snowtxt,$Desctxt,$detailedtxt,$hourbyhourtxt,$meteogramtxt,$comingsoon,$dewptxt,$wchilltxt,$heattxt,$Snowdtxt,$Kitxt,$kida,$kidb,$kidc,$Barotxt,$Solartxt,$ettxt,$grasstxt,$surftxt,$soiltxt,$moisttxt,$grassoiltxt,$mmh,$calm,$noprectxt,$windchtxt,$heattxt,$windgstdtxt,$thundertxt,$showstxt,$kids,$thunderdesc,$maxtxt,$mintxt) = explode("|",$words);

############################################################
# plaintext.txt, snippets borrowed from Ken's plaintext-parser

$pt = file($plaintextfile);
// fix missing space at start of line if need be
foreach ($pt as $i => $line) {
	if(substr($line,0,1) != ' ') {$pt[$i] = ' ' . $line;}
}
$plaintext = implode('',$pt);   // get the plaintext file.
$plaintext = preg_replace('![\n][\r|\n]+!s',"\n \n",$plaintext);
$plaintext = preg_replace('![\r|\n]+ [\r|\n]+!s',"\t\t",$plaintext); 
$plaintext .= "\t\t";  // make sure a delimiter is at the end too.
$plaintext = preg_replace('|_|is','',$plaintext); // remove dashed line in front

// Find city and update date
if (preg_match('|WXSIM text forecast for (.*), initialized at\s+(.*)|i',$plaintext,$matches)) {
  $wxallcity = get_lang(trim($matches[1]));
  $d = filemtime($plaintextfile);
  $wxallupdated = date($timeFormat,$d);
}

// Next update by Labbs
$thishr = date('i') < $uploadupdate ? date('G') : date('G') + 1;
$nextupdate = $updatehrs[0];
for ($i = 0 ; $i < count($updatehrs) ; $i++) {
	if ($updatehrs[$i] >= $thishr) {
		$nextupdate = $updatehrs[$i];
		break;
    }
    else if ($i+1 == count($updatehrs)){
    $nextupdate = "+1 day " . $nextupdate;
    }
}
$wxallnext = date($timeFormat,strtotime($nextupdate.':'.$uploadupdate));

// The loop
preg_match_all('!\t\s(.*):\s(.*)\t!Us',$plaintext,$matches); // split up the forecast
$howmany = count($matches[1]);
for ($i=0;$i<$howmany;$i++) {

$wxallday[$i] = trim($matches[1][$i]);
 $wxalltitle[$i] = preg_replace('! (\S+)$!',"<br />\\1",get_lang($wxallday[$i]));
 if (! preg_match('!<br />!',$wxalltitle[$i])) {
   $wxalltitle[$i] .= '<br />';  // add line break to 'short' day titles
 }

$wxalltext[$i] =  preg_replace('![\r|\n]+!is','',trim($matches[2][$i])); // remove CR and LF chars.
$rawtext = $wxalltext[$i];

#################################################################
# Tempicon
if(preg_match('!(High|Low) ([-|\d]+)(.*)[\.|,]!i',$wxalltext[$i],$mtemp)) {
$bigtemp = $mtemp[2];
if(round($bigtemp)<0.1){$bt = "#499AC7";}else{$bt = "#F33A27";}
$wxalltempbig[$i] = wxsim_tempcolor($bigtemp);
$wxallrawtemp[$i] = get_lang(ucfirst($mtemp[1])." temperature").' <b>'.$mtemp[2].$uoms[0].'</b>';
}

##################################################################
# Precip & POP
 if (preg_match('!Chance of precipitation (.*) percent!i',$wxalltext[$i],$mtemp)) {
   $wxallpop[$i] = $mtemp[1];
   $wxallpop[$i] = preg_replace('|less than |i','<',$wxallpop[$i]);
   if ($wxallpop[$i] == '<20') {$wxallpop[$i] = '10';}
   if ($wxallpop[$i] == 'near 100') { $wxallpop[$i] = '100'; }
 }

 reset($Precip);  // Do search in load order
 $wxallprec[$i] = 0;
 foreach ($Precip as $pamt => $prec) { // look for matching precipitation amounts  
   if(preg_match("!$pamt!is",$wxalltext[$i],$mtemp)) {
   list($amount,$units) = explode("\t",$prec);
	 $wxallprec[$i] = $amount;
	 break;
   }
 } // end of precipitation amount search

$wxallprec[$i] = str_replace("&lt;.1","0.05",$wxallprec[$i]);
$wxallprec[$i] = str_replace("&lt;.25","0.20",$wxallprec[$i]);
$wxallprec[$i] = str_replace("&lt;","",$wxallprec[$i]);

if($wxallprec[$i] > 0) {
$wxallprecstr[$i] = '<span style="color: #3399CC;"><b>'.fixprecip($wxallprec[$i],$uoms[1]).' '.$uoms[1].'</b></span><br/>'.$PoPtxt.': '.$wxallpop[$i].'%';
$wxallprectip[$i] = $Prectxt.' <b>'. fixprecip($wxallprec[$i],$uoms[1]).' '.$uoms[1].'</b><br/>'.$PoPtxt.' <b>'.$wxallpop[$i].'%</b>';
if(preg_match('|<br/><span style="color: blue(.*)span>|Ui',$wxallprecstr[$i],$precstr)) {
$wxallprecstr[$i] = str_replace($precstr[0],'',$wxallprecstr[$i]);
}
} else if($washowzeroprecip == true) {
$wxallprecstr[$i] = $noprectxt;
$wxallprectip[$i] = $noprectxt;
} else {
$wxallprecstr[$i] = '&nbsp;';
$wxallprectip[$i] = $noprectxt;
}

####################################################
# Icon
 // now look for harshest conditions first.. (in order in -data file
 reset($Conditions);  // Do search in load order
 foreach ($Conditions as $cond => $condrec) { // look for matching condition  
   if(preg_match("!$cond!i",$wxalltext[$i],$mtemp)) {
     list($dayicon,$nighticon,$condition) = explode("\t",$condrec);
	 if (preg_match('!chance!i',$condition) and $wxallpop[$i] < $minPoP) {
	   continue; // skip this one
	 }
	 if (preg_match("|$cond level|i",$wxalltext[$i]) ) {
	   continue; // skip 'snow level' and 'freezing level' entries
	 }
	 $wxallcond[$i] = get_lang($condition);
	 if (preg_match('|night|i',$wxallday[$i])) {
	   $wxallicon[$i] = $nighticon;
	 } else {
	   $wxallicon[$i] = $dayicon;
	 }
	 break;
   }
 } // end of conditions search
//  now fix up the full icon name and PoP if available
  $curicon = $wxallicon[$i]  . '.jpg';
  if ($wxallpop[$i] > 0) {
	$testicon = preg_replace("|\.jpg|","$wxallpop[$i].jpg",$curicon);
	if (file_exists($iconDir . $testicon)) {
      $wxallicon[$i] = $testicon;
	} else {
	  $wxallicon[$i] = $curicon;
	}
  } else {
    $wxallicon[$i] = $curicon;
  }
 
$iconcheck = plaintexticon($wxallicon[$i],$wxallprec[$i],$bigtemp,$wxalltext[$i],$tempuom);
$icons = explode('|',$iconcheck);
if (preg_match("|night|",$wxallday[$i])) {$newicon = $icons[1];$wxalltime[$i] = get_lang($wxallday[$i]).' 18-06';} 
else {$newicon = $icons[0];$wxalltime[$i] = get_lang($wxallday[$i]).' 06-18';}

//echo $wxallicon[$i].'||'.$newicon;
$wxallbic[$i] = '<span style="display:inline-block;margin-top:4px;" class="cond45 cond45-'.str_replace(".png","",$newicon).'"></span>';
preg_match('|<img src=\"(.*)/>|',$iconc ,$frestr);
$iconc = '<span style="display:inline-block;margin-top:6px;" class="cond45 cond45-'.str_replace(".png","",$newicon).'"></span><br/>';
$wxallicons[$i] = $iconc;

# Add PoP badge
preg_match('/([0-9]+)/',$wxallprec[$i],$ppval);
if (intval(end($ppval)) > 0) {
	if (intval(end($ppval)) >= $pophigh)
	        $tempclass = "percip2";
	else if (intval(end($ppval)) >= $poplow)
	        $tempclass = "percip1";
	else
        $tempclass = "percip0";
	$wxallbicPop[$i] = '<div class="popwrap"><span id="pop" class="'.$tempclass.'" title="'.$Prectxt.': '.$wxallprecip[$i].'"> '.$wxallpop[$i].'% </span><span style="color:#2779aa;font-size:11px;"><b>'.$wxalltitle[$i].'</b></span><br/>'.$wxallicons[$i].'</div>';
}
else {
	$wxallbicPop[$i] = '<span style="color:#2779aa;font-size:11px;"><b>'.$wxalltitle[$i].'</b></span><br/>'.$wxallicons[$i];
}

#############################################################
# UV
 // extract UV index value
 if (preg_match('|UV index up to (\d+)\.|i',$wxalltext[$i],$mtemp) ) {
 $wxalluv[$i] = $mtemp[1];
 }
$uvic = $wxalluv[$i];
if($uvic<10){$uvic = ''.$uvic;}
$wxalluvtip[$i] = uv_word(round($uvic));
if($uvic>0.1){
$uvic = '<small>UV<br/></small><img src="'.$wxallmainfolder.'img/uv2/'.$uvic.'.png" alt="UV Index: '.$uvic.'"/>';
} else {
$uvic = '';
}
$wxalluvimg[$i] = $uvic;

#####################################################
# Wind
$wr = array ("N" => 0,"NNE" => 22,"NE" => 45, "ENE" => 67,"E" => 90, "ESE" => 112, "SE" => 135, "SSE" => 157, "S" => 180,"SSW" => 202,"SW" => 225, "WSW" => 247, "W" => 270, "WNW" => 292, "NW" => 315, "NNW" => 337);
 $testwind = str_replace('Wind chill','Wind-chill',$rawtext);
 if (preg_match('|Wind (.*)\.|Ui',$testwind,$mtemp) ) {
 $wtemp = preg_replace('! around| near| in the| morning| evening| afternoon| midnight| tonight| to| after!Uis','',$mtemp[1]);
 $wtemp = explode(', ',$wtemp);
  $wparts = explode(' ',$wtemp[0]); // break it by spaces.
  $maxWind = 0;
  for ($k =0;$k<count($wtemp);$k++) {
    $wparts = explode(' ',$wtemp[$k]);	
	if(isset($WindLookup[$wparts[0]]) ) { // got <dir> [speed] [units] format
	  $wxallwdir = $WindLookup[$wparts[0]];  // get abbreviation for direction
      $wxallwind[$i] = $wparts[1];  // get speed
	  if ($wparts[1] > $maxWind and $wparts[1] <> 'calm') { $maxWind = $wparts[1]; }
      if ( isset($wparts[2])) {
       $wxallwindunits[$i] = $wparts[2]; // get wind units of measure
      }
    }
  $wxallwindb[$i] = $wxallwind[$i];
	if ($wparts[0] == 'gusting') {
	  //$wxallwind[$i] .= '-' . $wparts[1];
	  if ($wparts[1] > $maxWind) { $maxWind = $wparts[1]; }
	  $wxallgust[$i] = $wparts[1];
	  $wxallgustb[$i] = '</b><br/>'.get_lang("Gusting to").' <b>'.$wparts[1].' '.$uoms[2];
	}
	if ($wparts[0] == 'becoming') { // got 'becoming [dir] [speed] [units]
	  if (isset($WindLookup[$wparts[1]]) ) {
	    $wxallwinddir[$i] .= '&rarr;' . $WindLookup[$wparts[1]];
	  }
	  if (preg_match('!(\d+|calm)!',$wtemp[$k],$match)) {
	    $wxallwind[$i] .= '&rarr;' . $match[1];
  	    if ($match[1] > $maxWind and $match[1] <> 'calm') { $maxWind = $match[1]; }
	  }
	  if (! $wxallwindunits[$i] and isset($wparts[2]) ) {
        $wxallwindunits[$i] = $wparts[2]; // get wind units of measure
	  }
	}
  
  }
 }

if($useica){
if(round(crop($wxallwindb[$i]))=="calm"){$dira="E";$wxallwindb[$i]=0;}else{$dira = crop($wxallwdir);}
if(round(crop($wxallwindb[$i]))>$highwindlimit){$ah="O-";}else{$ah="";}
$wxallwindbarb[$i] = '
<div class="wind-'.checksprite(crop($wxallwindb[$i])).' '.$ah.$dira.round(crop($wxallwindb[$i])).'" style="margin:10px auto;"></div>';
$wxallwindtip[$i] = crop($wxallwdir).' '.crop($wxallwindb[$i]).' '.$uoms[2].$wxallgustb[$i];
} else {
if(crop($wxallwindb[$i]) <= $highwindlimit ){
$wxdirics[] = crop($wxallwdir);
} else {
$wxdirics[] = 'W-'.crop($wxallwdir);
}
if(crop($wxallwindb[$i]) == "calm") {
$wxallwindbarb[$i] = '<div style="position:relative;height:42px;width:100%;">
<span style="display:inline-block;background:url('.$wxallmainfolder.'img/calm.png) 50% 50% no-repeat;width: 42px; height: 42px;"></span>
<div style="position:absolute;top:15px;width:100%;text-align:center;font-size:11px;">0</div></div>';
$wxallwindtip[$i] = ucfirst($calm);
} else {
$wxallwindbarb[$i] = '<div style="position:relative;height:41px;width:100%;">
<span style="display:inline-block;" class="wxallwind wxall-'.$wxdirics[$i].'"></span>
<div style="position:absolute;top:14px;width:100%;text-align:center;font-size:11px;">'.crop($wxallwindb[$i]).'</div></div>';
$wxallwindtip[$i] = crop($wxallwdir).' '.crop($wxallwindb[$i]).' '.$uoms[2].$wxallgustb[$i];
}
}
$wxdirs[] = crop($wxallwdir);

#############################################################
# Text-section

 // extract temperature High/Low values
 $tempDegrees = "&deg;";
 if (preg_match('!(high|low) ([-|\d]+)[\.|,]!i',$wxalltext[$i],$mtemp)) {
   $wxalltemp[$i] = get_lang($mtemp[1] .':') . ' ' . $mtemp[2] . $tempDegrees;
   if ($tempDegrees) {  // fix up degrees in the text
      $wxalltext[$i] = preg_replace(
	  '|' . $mtemp[1] . ' ' . $mtemp[2] .'|',
	  $mtemp[1] . ' ' . $mtemp[2] . $tempDegrees,
	  $wxalltext[$i]);
	  $wxalltext[$i] = preg_replace('/Wind chill down to ([-|\d]+)/i',"Wind chill down to $1$tempDegrees",$wxalltext[$i]);
	  $wxalltext[$i] = preg_replace('/Heat index up to ([-|\d]+)/i',"Heat index up to $1$tempDegrees",$wxalltext[$i]);
//     $Status .= "<!-- WXSIMtext[$i]='".$wxalltext[$i]."' -->\n";
	  
   }
   }

    $fixedtxt = $wxalltext[$i];
    # Snow & ice
    // Freezinglevel
    $frzltxt = '';
    if(preg_match('|Maximum freezing level(.*)above sea level.|',$fixedtxt,$snowstr)) {
    if($snowlcolor == true) {
    $frzltxt = str_replace($snowstr[0],'.<br/><span style="color:#3399CC"><b>'.$snowstr[0].'</b></span>',$snowstr[0]);
    } else {
    $frzltxt = '<br/>'.$snowstr[0];
    }
    $fixedtxt = str_replace($snowstr[0],'',$fixedtxt);
    } else if(preg_match('|Minimum freezing level(.*)above sea level.|',$fixedtxt,$snowstr)) {
    if($snowlcolor == true) {
    $frzltxt = str_replace($snowstr[0],'.<br/><span style="color:#3399CC"><b>'.$snowstr[0].'</b></span>',$snowstr[0]);
    } else {
    $frzltxt = '<br/>'.$snowstr[0];
    }
    $fixedtxt = str_replace($snowstr[0],'',$fixedtxt);
    }
    // Snowlevel
    $snowltxt = '';
    if(preg_match('|Maximum snow level(.*)above sea level.|',$fixedtxt,$snowstr)) {
    if($snowlcolor == true) {
    $snowltxt = str_replace($snowstr[0],'.<br/><span style="color:#3399CC"><b>'.$snowstr[0].'</b></span>',$snowstr[0]);
    } else {
    $snowltxt = '<br/>'.$snowstr[0];
    }
    $fixedtxt = str_replace($snowstr[0],'',$fixedtxt);
    } else if(preg_match('|Minimum snow level(.*)above sea level.|',$fixedtxt,$snowstr)) {
    if($snowlcolor == true) {
    $snowltxt = str_replace($snowstr[0],'.<br/><span style="color:#3399CC"><b>'.$snowstr[0].'</b></span>',$snowstr[0]);
    } else {
    $snowltxt = '<br/>'.$snowstr[0];
    }
    $fixedtxt = str_replace($snowstr[0],'',$fixedtxt);
    }
    // Freezing rain
    $snowtxt = '';
    $frztxt = '';
    if(preg_match('|Little if any freezing rain accumulation(.*).|Ui',$fixedtxt,$frestr)) {
    $frztxt = str_replace($frestr[0],'.<br/><span style="color:#EE7621"><b>'.$frestr[0].'</b></span>',$frestr[0]);
    $fixedtxt = str_replace($frestr[0],'',$fixedtxt);
    } else if(preg_match('|Above-ground freezing rain accumulation up(.*).|Ui',$fixedtxt,$frestr)) {
    $fixedtxt = str_replace($frestr[0],'',$fixedtxt);
    } else if(preg_match('|Freezing rain accumulation up(.*).|Ui',$fixedtxt,$frestr)) {
    $fixedtxt = str_replace($frestr[0],'',$fixedtxt);
    } else if(preg_match('|Freezing rain accumulation(.*).|Ui',$fixedtxt,$frestr)) {
    $frztxt = str_replace($frestr[0],'.<br/><span style="color:#EE7621"><b>'.$frestr[0].'</b></span>',$frestr[0]);
    $fixedtxt = str_replace($frestr[0],'',$fixedtxt);
    }
    // Snow
    if(preg_match('|Little or no snow(.*)expected.|',$fixedtxt,$snowstr)) {
    $snowtxt = str_replace($snowstr[0],'.<br/><span style="color:#3399CC"><b>'.$snowstr[0].'</b></span>',$snowstr[0]);
    $fixedtxt = str_replace($snowstr[0],'',$fixedtxt);
    } else if(preg_match('|Little if any snow(.*)expected.|',$fixedtxt,$snowstr)) {
    $snowtxt = str_replace($snowstr[0],'.<br/><span style="color:#3399CC"><b>'.$snowstr[0].'</b></span>',$snowstr[0]);
    $fixedtxt = str_replace($snowstr[0],'',$fixedtxt);
    }else if(preg_match('|No (.*)\.|Ui',$fixedtxt,$snowstr)) {
    $snowtxt = '<br/>'.$snowstr[0];
    $fixedtxt = str_replace($snowstr[0],'',$fixedtxt);
    }else if(preg_match('!Snow or ice(.*).!i',$fixedtxt,$snowstr)) {
    $snowtxt = str_replace($snowstr[0],'.<br/><span style="color:#3399CC"><b>'.$snowstr[0].'</b></span>',$snowstr[0]);
    $fixedtxt = str_replace($snowstr[0],'',$fixedtxt);
    }else if(preg_match('!snow accumulation(.*).!i',$fixedtxt,$snowstr)) {
    $snowtxt = str_replace($snowstr[0],'.<br/><span style="color:#3399CC"><b>'.$snowstr[0].'</b></span>',$snowstr[0]);
    $fixedtxt = str_replace($snowstr[0],'',$fixedtxt);
    }
    
    $fixedtxt = str_replace('Wind chill','Wind-chill',$fixedtxt);
    preg_match('|Wind-chill (.*)\.|Ui',$fixedtxt,$wchillstr);
    $orgwc = $wchillstr[0];
    preg_match('|Wind (.*)m\/s\.|Ui',$fixedtxt,$windstr);
    $fixedtxt = str_replace($windstr[0],"",$fixedtxt);
    preg_match('|Wind (.*)\.|Ui',$fixedtxt,$windstr);
    $fixedtxt = str_replace($windstr[0],"",$fixedtxt);
    if($lang == "fi"){ // Few finnish fixes
    $wchillstr[0] = str_replace('to',"and",$wchillstr[0]);
    if(preg_match('|and|',$wchillstr[0])) {
    $wchillstr[0] = str_replace('.'," valilla.",$wchillstr[0]);
    }
    $fixedtxt = str_replace($orgwc,$wchillstr[0],$fixedtxt);
    } // Eof finnsih fixes
    $fixedtxt = str_replace('Wind-chill','Wind chill',$fixedtxt);
    //PoP
    preg_match('|Chance of precipitation(.*)percent.|Ui',$fixedtxt,$windstr);
    $fixedtxt = str_replace($windstr[0],"",$fixedtxt);
    // Rain
    preg_match('|Precipitation(.*)mm\.|Ui',$fixedtxt,$windstr);
    $fixedtxt = str_replace($windstr[0],"",$fixedtxt);
    
    $fixedtxt = str_replace("Heat index up to","<br/>Heat index:",$fixedtxt);
    $fixedtxt = str_replace(" mostly less than"," less than",$fixedtxt);
    $fixedtxt = str_replace("mix of snow and sleet","snow",$fixedtxt);

    preg_match('|UV(.*)\. |Ui',$fixedtxt,$windstr);
    $fixedtxt = str_replace($windstr[0],"",$fixedtxt);
    
    preg_match('|Low(.*)&deg;.|',$fixedtxt,$wcstr);
    $fixedtxt = str_replace($wcstr[0],"",$fixedtxt);
    preg_match('!but temperatures(.*)(night|afternoon|morning)\. !i',$fixedtxt,$wcstr);
    $fixedtxt = str_replace($wcstr[0],"",$fixedtxt);
    $fixedtxt = str_replace("High risk","Increased risk",$fixedtxt); // Rename "high risk" so the parser not remove it
    $fixedtxt = str_replace("high thin","highthin",$fixedtxt); // Rename "high thin" so the parser not remove it
    $fixedtxt = str_replace("high cloudiness","highcloudiness",$fixedtxt); // Rename "high thin" so the parser not remove it
    preg_match('|High (.*)&deg;.|Ui',$fixedtxt,$wjstr);
    $fixedtxt = str_replace($wjstr[0],"",$fixedtxt);
    if(!preg_match('|thunder|Ui',$windstr[0])) {
    $fixedtxt = str_replace($windstr[0],"",$fixedtxt);
    }
    $fixedtxt = str_replace("Increased risk","High risk",$fixedtxt); // Put "high risk" back ;)
    $fixedtxt = str_replace("highthin","high thin",$fixedtxt); // Put "high risk" back ;)
    $fixedtxt = str_replace("highcloudiness","high cloudiness",$fixedtxt); // Rename "high thin" so the parser not remove it
    
    // Thunder ;)
    $fixedtxt = fixthunder($fixedtxt);
   
    $fixedtxt = $fixedtxt.$snowtxt.$frztxt.$frzltxt.$snowltxt;
    $fixedtxt = preg_replace('/\s\s+/', ' ',$fixedtxt);
    $fixedtxt = str_replace(". .",".",$fixedtxt);
    $fixedtxt = str_replace("..",".",$fixedtxt);
    $fixedtxt = str_replace("</b></span>.","</b></span>",$fixedtxt);
    if ($doTranslate) {
    reset ($Language);
    foreach ($Language as $key => $replacement) {
      $fixedtxt = str_replace($key,$replacement,$fixedtxt);
    }
    }
    $fixedtxt = preg_replace('!\.\s+([a-z])!es',"'.  ' . strtoupper('\\1')",$fixedtxt);
    $wxalltext[$i] = $fixedtxt;

} # EOF own loop

# Data done!
####################################################
# Put together the outputs

// Credits
$discl = '<br/>
<small>Script by <a rel="external" href="http://www.nordicweather.net">nordicweather.net</a>, snippets by Ken True from <a rel="external" href="http://saratoga-weather.org/">Saratoga Weather</a>, Labbs from <a rel="external" href="http://www.lokaltvader.se/">Lokaltväder.se</a> and others.<br/>
Valid HTML5. Weathericons by <a rel="external" href="http://www.dotvoid.se">Dotvoid</a><br/>
Powered by <a rel="external" href="http://www.wxsim.com">WXSIM</a> &amp; <a rel="external" href="http://www.highcharts.com">Highcharts</a>
</small>';

// Updated
$wxallupdated ='<span style="float:right;text-align:right;">'.get_lang("Updated:").' '.$wxallupdated.'<br/>
'.get_lang("Next update").': '.$wxallnext.'</span>';

// Put together the table
$shorttable = '<table class="wxsimtbl" style="width:100%;">
<tr class="wxsimtbl_header">
<td>&nbsp;</td>
<td style="padding:6px;text-align: center"><b>'.$condtxt.'</b></td>
<td style="padding:6px;text-align: center"><b>'.$Temptxt.'</b></td>
<td style="padding:6px;text-align: center"><b>'.$Wind.'</b></td>
<td style="padding:6px;text-align: center"><b>'.$Prectxt.'</b></td>
<td style="padding:6px;text-align: center"><b>'.get_lang("UV Index").'</b></td>
<td style="padding:6px;"><b>'.$Desctxt.'</b></td>
</tr>';
for($i=0;$i<$howmany;$i++) {
if($i%2==0) {$row = 'wxsimdata'; }else{$row='wxsimdataodd';}
// tooltips
$condtip = '<b>'.$condtxt.'</b><br/>'.get_lang($wxallday[$i]).':<br/><b>'.$wxallcond[$i].'</b>';
$temptip = '<b>'.$Temptxt.'</b><br/>'.get_lang($wxallday[$i]).':<br/>'.$wxallrawtemp[$i];
$windtip = '<b>'.$Wind.'</b><br/>'.get_lang($wxallday[$i]).':<br/><b>'.$wxallwindtip[$i].'</b>';
$prectip = '<b>'.$Prectxt.'</b><br/>'.get_lang($wxallday[$i]).':<br/>'.$wxallprectip[$i];
$uvtip = '<b>'.get_lang("UV Index").'</b><br/>'.get_lang($wxallday[$i]).':<br/>'.$wxalluvtip[$i];
// row
$shorttable.='
<tr style="vertical-align:top;text-align:center;" class="'.$row.'">
<td style="text-align:left;width:100px;padding:3px 3px 3px 5px;vertical-align:middle;color:#2779aa;" class="tooltip" title="'.$wxalltime[$i].'"><b>'.get_lang($wxallday[$i]).'</b></td>
<td style="width:70px;vertical-align:middle;padding:0;text-align:center" class="tooltip" title="'.$condtip.'">'.$wxallbic[$i].'</td>
<td style="width:70px;padding:3px;text-align:center;vertical-align:middle;" class="tooltip" title="'.$temptip.'">'.$wxalltempbig[$i].'</td>
<td style="width:70px;vertical-align:middle;padding:3px;text-align:center" class="tooltip" title="'.$windtip.'">'.$wxallwindbarb[$i].'</td>
<td style="width:100px;vertical-align:middle;padding:3px;text-align:center" class="tooltip" title="'.$prectip.'">'.$wxallprecstr[$i].'</td>
<td style="width:70px;vertical-align:middle;padding:3px;text-align:center" class="tooltip" title="'.$uvtip.'">'.$wxalluvimg[$i].'</td>
<td style="text-align:left;vertical-align:middle;padding:3px 5px 3px 3px;" class="tooltip" title="'.$wxalltime[$i].'">'.$wxalltext[$i].'</td></tr>
';
}
$shorttable.='<tr><td colspan="7" class="wxsiminfo-box">';
$shorttable.= get_lang("Move the mouse over the values for info.").'</td></tr></table>
';

$prc = 100/$topmany;
$wxalltop = '<table style="width:'.$mainwidth.'px;margin:0 auto;font-family:Verdana, Tahoma, Arial, sans-serif;">
<tr style="vertical-align:top;text-align:center;">';
for ($i=0;$i<$topmany;$i++){
$condtip = get_lang($wxallday[$i]).':<br/>'.$wxallcond[$i];
$wxalltop.= '<td style="width:'.$prc.'%;text-align:center;" class="tooltip" title="'.$condtip.'">'.$wxallbicPop[$i].'</td>'."\n";
}
$wxalltop.= '
</tr>
<tr style="vertical-align:top;text-align:center;">';
for ($i=0;$i<$topmany;$i++){
$combtip = get_lang($wxallday[$i]).':<br/>'.$wxallrawtemp[$i];
$wxalltop.= '<td style="width:'.$prc.'%;text-align:center;position:relative;" class="tooltip" title="'.$combtip.'"><b>'.$wxalltempbig[$i].'</b><br/>
</td>'."\n"; 
}
$wxalltop.= '
</tr>
<tr style="text-align:center;">';
for ($i=0;$i<$topmany;$i++){
$windtip = '<b>'.$Wind.'</b><br/>'.get_lang($wxallday[$i]).':<br/><b>'.$wxallwindtip[$i].'</b>';
$wxalltop.= '<td style="width:'.$prc.'%;text-align:center;" class="tooltip" title="'.$windtip.'"><div style="width:100%;text-align:center;font-size:11px;">'.$wxallwindbarb[$i].'</div></td>'."\n";
}
$wxalltop.= '
</tr>
</table>
';

# EOF plaintext.txt-part
######################################################################
# Continue with lastret.txt

include_once $wxallmainfolder.'wxall.lastret.php';

function get_lang( $text ) {
  global $LanguageLookup, $doTranslate;
  
  if ($doTranslate && isset($LanguageLookup[$text])) {
    $newtext = $LanguageLookup[$text];
  } else {
    $newtext = $text;
  }
 return($newtext);
}
?>