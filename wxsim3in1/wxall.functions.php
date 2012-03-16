<?php
// Functionsfile for 3in1 V.4
#error_reporting(E_ALL);
#ini_set('display_errors', '1');

##############################################################
# PRECIPITATION-FUNCTIONS
##############################################################
# PRECIPTYPE USING TEMPERATURE, DEWPOINT & LEVEL 1 TEMPERATURE
function raintyp($temp,$dew,$lev,$unit){
if($unit == "C"){
switch (TRUE) {
case ($temp > 2): $pr = 'rain'; break;
case ($dew > 1.2 && $dew <> 9999): $pr = 'rain'; break;
case ($lev > 2 && $lev <> 9999): $pr = 'rain'; break;
case ($temp < -0.3): $pr = 'snow'; break;
case ($dew < -0.6): $pr = 'snow'; break;
case ($lev < -1.5): $pr = 'snow'; break;
case ($lev < 0.5&&$dew < 0&&$temp < 0.6): $pr = 'snow'; break;
case ($lev < 0&&$dew < 0.5&&$temp < 0.6): $pr = 'snow'; break;
case ($lev < 2&&$dew < 0&&$temp < 0): $pr = 'sleet'; break;
default: $pr = 'sleet';
}
}
if($unit == "F"){
switch (TRUE) {
case ($temp > 35.6): $pr = 'rain'; break;
case ($dew > 34.16 && $dew <> 9999): $pr = 'rain'; break;
case ($lev > 35.6 && $lev <> 9999): $pr = 'rain'; break;
case ($temp < 31.46): $pr = 'snow'; break;
case ($dew < 30.92): $pr = 'snow'; break;
case ($lev < 29.3): $pr = 'snow'; break;
case ($lev < 32.9&&$dew < 32&&$temp < 33.1): $pr = 'snow'; break;
case ($lev < 32&&$dew < 32.9&&$temp < 33.1): $pr = 'snow'; break;
case ($lev < 35.6&&$dew < 32&&$temp < 32): $pr = 'sleet'; break;
default: $pr = 'sleet';
}
}
return $pr;
}

###############################################################
# PRECIP INTENSITY 
function preccond($cover,$btot,$type) {
global $modrain,$heavyrain,$modsnow,$heavysnow,$frzz,$slzz,$tfrzz,$tslzz;
if($btot>0){
$prcond = false;
$shower = false;
$hard = false;
if($cover <= 95) { $shower = ' showers'; } // shower if not fully cloudy
if($btot >= $heavyrain) { $hard = 'heavy'; }
if($btot < $heavyrain) { $hard = 'moderate'; }
if($btot <= $modrain) { $hard = 'light'; }
if($type == 'snow' || $type == 'sleet' ) {
if($btot >= $heavysnow) { $hard = 'heavy'; }
if($btot < $heavysnow) { $hard = 'moderate'; }
if($btot <= $modsnow) { $hard = 'light'; }
}
}else{$type=false;}
$prcond = $hard.' '.$type.$shower;
//echo $btot.'-'.$hard.' '.$type.$shower.'<br>';
return $prcond;
}

################################################################
# Detect fog #
function fogcond($oldicon) {
switch (TRUE) {
case (preg_match("|HEAVYFOG|",$oldicon)): $fogcond = 'heavy fog'; break;
case (preg_match("|MOD.FOG|",$oldicon)): $fogcond = 'moderate fog'; break;
case (preg_match("|LIGHTFOG|",$oldicon)): $fogcond = 'light fog'; break;
case (preg_match("||",$oldicon)): $fogcond = '';break;
}
return $fogcond;
}

################################################################################
# ICONPARSER plaintext.txt 
# Convert NOAA-iconnames to description used in 3in1 using also prec-amount & temperature
# "Detecing" tscd-number based on the text because its not available in plaintext.txt.
function plaintexticon($raw,$btot,$temp,$txt,$unit) {
global $modrain,$heavyrain,$modsnow,$heavysnow,$frzz,$slzz,$pltscd;
$pltscd = 0;
# thundercheck
# Determine "virtual" tscd-number based on the text in plaintext.txt
switch (TRUE) {
case (preg_match("|Severe thunderstorms likely, with possible tornados|",$txt)):$pltscd=506;break;
case (preg_match("|Thunderstorms very likely, some severe|",$txt)):$pltscd=405;break;
case (preg_match("|Thunderstorms very likely, some possibly severe|",$txt)):$pltscd=403;break;
case (preg_match("|Scattered thunderstorms likely, some possibly severe|",$txt)):$pltscd=303;break;
case (preg_match("|Scattered thunderstorms likely|",$txt)):$pltscd=303;break;
case (preg_match("|Scattered thundershowers possible|",$txt)):$pltscd=303;break;
}

# Clouds
$cover = 0;
switch (TRUE) {
case(preg_match("|ostly cloudy|",$txt)):$cover=80; break;
case(preg_match("|artly cloudy|",$txt)):$cover=40; break;
case(preg_match("|loudy|",$txt)):$cover=100; break;
case(preg_match("|vercast|",$txt)):$cover=100; break;
case(preg_match("|air|",$txt)):$cover=20; break;
case(preg_match("|ostly sunny|",$txt)):$cover=20; break;
}
switch (TRUE) {
case ($cover == 100): $clouds = "ovc"; break;
case ($cover == 80): $clouds = "bkn"; break;
case ($cover == 40): $clouds = "sct"; break;
case ($cover == 20): $clouds = "few"; break;
default: $clouds = "skc"; break;
}

# Prec-type
if($btot > 0) {
$cond = raintyp($temp,9999,9999,$unit);
if($btot >= $heavyrain) { $hard = 'heavy'; }
if($btot < $heavyrain) { $hard = 'moderate'; }
if($btot <= $modrain) { $hard = 'light'; }
if($cond == "snow") {
if($btot >= $heavysnow) { $hard = 'heavy'; }
if($btot < $heavysnow) { $hard = 'moderate'; }
if($btot <= $modsnow) { $hard = 'light'; }
}
}
if(preg_match("|fog|",$txt)){$fogcond = 'fog';}else{$fogcond = '';}

$prcond = $clouds.$hard.$cond;
$iconcheck = wanewicon($prcond,$fogcond,$pltscd,$cover,$btot);
//echo $temp.'||'.$prcond.'||'.$iconcheck.'<br/>';
return $iconcheck;
} 

############################################################################
# MAIN ICONSELECTOR
# Names refers also to css-sprites, ex. 420.png = .d420
function wanewicon($prcond,$fogcond,$num,$cover,$newtot) {
global $txtcloud,$tsclcover;

switch (TRUE) {
case ($cover > 95): $clouds = "ovc"; $txtcloud = "Cloudy"; break;
case ($cover > 50 && $cover <= 95): $clouds = "bkn"; $txtcloud = "Mostly cloudy"; break;
case ($cover > 25 && $cover <= 50): $clouds = "sct"; $txtcloud = "Partly cloudy"; break;
case ($cover > 5 && $cover <= 25): $clouds = "few"; $txtcloud = "Mostly sunny"; break;
default: $clouds = "skc"; $txtcloud = "Sunny"; break;
}

$prcond = str_replace(' ','',$prcond);
$fogcond = str_replace(' ','',$fogcond);
$prcond = $clouds.$prcond.$fogcond;

switch (TRUE) {
    case($num==302&&$cover>85&&$newtot>0):$newicon = "33.png|33.png";break;
    case($num==303&&$cover>85&&$newtot>0):$newicon = "33.png|33.png";break;
    case($num==402&&$cover>85&&$newtot>0):$newicon = "33.png|33.png";break;
    case($num==403&&$cover>85&&$newtot>0):$newicon = "33.png|33.png";break;
    case($num==404&&$cover>85&&$newtot>0):$newicon = "36.png|36.png";break;
    case($num==501&&$cover>85&&$newtot>0):$newicon = "33.png|33.png";break;
    case($num==503&&$cover>85&&$newtot>0):$newicon = "36.png|36.png";break;
    case($num==504&&$cover>85&&$newtot>0):$newicon = "39.png|39.png";break;
    case($num==505&&$cover>85&&$newtot>0):$newicon = "39.png|39.png";break;
    case($num==506&&$cover>85&&$newtot>0):$newicon = "39.png|39.png";break;
    // If bkn
    case($num==302&&$cover>$tsclcover&&$newtot>0):$newicon = "32.png|32n.png";break;
    case($num==303&&$cover>$tsclcover&&$newtot>0):$newicon = "32.png|32n.png";break;
    case($num==402&&$cover>$tsclcover&&$newtot>0):$newicon = "32.png|32n.png";break;
    case($num==403&&$cover>$tsclcover&&$newtot>0):$newicon = "32.png|32n.png";break;
    case($num==404&&$cover>$tsclcover&&$newtot>0):$newicon = "35.png|35n.png";break;
    case($num==501&&$cover>$tsclcover&&$newtot>0):$newicon = "32.png|32n.png";break;
    case($num==503&&$cover>$tsclcover&&$newtot>0):$newicon = "35.png|35n.png";break;
    case($num==504&&$cover>$tsclcover&&$newtot>0):$newicon = "38.png|38n.png";break;
    case($num==505&&$cover>$tsclcover&&$newtot>0):$newicon = "38.png|38n.png";break;
    case($num==506&&$cover>$tsclcover&&$newtot>0):$newicon = "38.png|38n.png";break;

    # Snow
    case (preg_match("|ovcheavysnow|",$prcond)): $newicon = "59.png|59.png"; break;
    case (preg_match("|ovcmoderatesnow|",$prcond)): $newicon = "56.png|56.png"; break;
    case (preg_match("|ovclightsnow|",$prcond)): $newicon = "53.png|53.png"; break;
    case (preg_match("|bknheavysnow|is",$prcond)): $newicon = "58.png|58n.png"; break;
    case (preg_match("|bknmoderatesnow|is",$prcond)): $newicon = "55.png|55n.png"; break;
    case (preg_match("|bknlightsnow|is",$prcond)): $newicon = "52.png|52n.png"; break;
    case (preg_match("|sctheavysnow|is",$prcond)): $newicon = "57.png|57n.png"; break;
    case (preg_match("|sctmoderatesnow|is",$prcond)): $newicon = "54.png|54n.png"; break;
    case (preg_match("|sctlightsnow|is",$prcond)): $newicon = "51.png|51n.png"; break;
    case (preg_match("|fewheavysnowssnow|is",$prcond)): $newicon = "57.png|57n.png"; break;
    case (preg_match("|fewmoderatesnow|is",$prcond)): $newicon = "54.png|54n.png"; break;
    case (preg_match("|fewlightsnow|is",$prcond)): $newicon = "51.png|51n.png"; break;
    # Sleet
    case (preg_match("|ovcheavysleet|",$prcond)): $newicon = "43.png|43.png"; break;
    case (preg_match("|ovcmoderatesleet|",$prcond)): $newicon = "43.png|43.png"; break;
    case (preg_match("|ovclightsleet|",$prcond)): $newicon = "43.png|43.png"; break;
    case (preg_match("|bknheavysleet|is",$prcond)): $newicon = "42.png|42n.png"; break;
    case (preg_match("|bknmoderatesleet|is",$prcond)): $newicon = "42.png|42n.png"; break;
    case (preg_match("|bknlightsleet|is",$prcond)): $newicon = "42.png|42n.png"; break;
    case (preg_match("|sctheavysleet|is",$prcond)): $newicon = "41.png|41n.png"; break;
    case (preg_match("|sctmoderatesleet|is",$prcond)): $newicon = "41.png|41n.png"; break;
    case (preg_match("|sctlightsleet|is",$prcond)): $newicon = "41.png|41n.png"; break;
    case (preg_match("|fewheavysleet|is",$prcond)): $newicon = "41.png|41n.png"; break;
    case (preg_match("|fewmoderatesleet|is",$prcond)): $newicon = "41.png|41n.png"; break;
    case (preg_match("|fewlightsleet|is",$prcond)): $newicon = "41.png|41n.png"; break;
    # Rain
    case (preg_match("!ovcheavy[rain|showers]!is",$prcond)): $newicon = "29.png|29.png"; break;
    case (preg_match("!ovcmoderate[rain|showers]!is",$prcond)): $newicon = "26.png|26.png"; break;
    case (preg_match("!ovclight[rain|showers]!is",$prcond)): $newicon = "23.png|23.png"; break;
    case (preg_match("!bknheavy[rain|showers]!is",$prcond)): $newicon = "28.png|28n.png"; break;
    case (preg_match("!bknmoderate[rain|showers]!is",$prcond)): $newicon = "25.png|25n.png"; break;
    case (preg_match("!bknlight[rain|showers]!is",$prcond)): $newicon = "22.png|22n.png"; break;
    case (preg_match("!sctheavy[rain|showers]!is",$prcond)): $newicon = "27.png|27n.png"; break;
    case (preg_match("!sctmoderate[rain|showers]!is",$prcond)): $newicon = "24.png|24n.png"; break;
    case (preg_match("!sctlight[rain|showers]!is",$prcond)): $newicon = "21.png|21n.png"; break;
    case (preg_match("!fewheavy[rain|showers]!is",$prcond)): $newicon = "27.png|27n.png"; break;
    case (preg_match("!fewmoderate[rain|showers]!is",$prcond)): $newicon = "24.png|24n.png"; break;
    case (preg_match("!fewlight[rain|showers]!is",$prcond)): $newicon = "21.png|21n.png"; break;
    # Fog
    case (preg_match("|fog|",$prcond)): $newicon = "3.png|3n.png"; break;
    # Other
    case (preg_match("|ovc|",$prcond)): $newicon = "14.png|14.png"; break;
    case (preg_match("|bkn|",$prcond)): $newicon = "13.png|13n.png"; break;
    case (preg_match("|sct|",$prcond)): $newicon = "11.png|11n.png"; break;  
    case (preg_match("|few|",$prcond)): $newicon = "10.png|10n.png"; break;
    case (preg_match("|skc|",$prcond)): $newicon = "1.png|1n.png"; break;
}
return $newicon;
}

#################################################################
# Colorize phrases #
function fixstring($str,$num,$kiww,$cover,$newtot) { 
global $lightsnowcolorized,$tsclcover,$tsactive;
switch (TRUE) {
# Convective
case($num==202&&$cover>$tsclcover&&$newtot>0):
$str = preg_replace('/(rain showers|rain)./i',"$1. They can contain lightning.",$str);break;
case($num==301&&$cover>$tsclcover&&$newtot>0):
$str = preg_replace('/(rain showers|rain)./i',"$1 but thunder unlikely.",$str);break;
case($num==401&&$cover>$tsclcover&&$newtot>0):
$str = preg_replace('/(rain showers|rain)./i',"$1 but thunder unlikely.",$str);break;
case($num==302&&$cover>$tsclcover&&$newtot>0):
$str = preg_replace('!(light|moderate|heavy) (rain showers|rain).!is',
"<span style=\"color:#EE7621\"><b>Scattered thundershowers possible".thunderproc($kiww).". Except for higher amounts of precipitation within thunderstorms. </b></span>",$str);
break;
case($num==303&&$cover>$tsclcover&&$newtot>0):
$str = preg_replace('!(light|moderate|heavy) (rain showers|rain).!is',
"<span style=\"color:#EE7621\"><b>Thundershowers possible".thunderproc($kiww).". Except for higher amounts of precipitation within thunderstorms.</b></span>",$str);
break;
case($num==402&&$cover>$tsclcover&&$newtot>0):
$str = preg_replace('!(light|moderate|heavy) (rain showers|rain).!is',
"<span style=\"color:#EE7621\"><b>Thunder possible".thunderproc($kiww).". Except for higher amounts of precipitation within thunderstorms.</b></span>",$str);
break;
case($num==403&&$cover>$tsclcover&&$newtot>0):
$str = preg_replace('!(light|moderate|heavy) (rain showers|rain).!is',
"<span style=\"color:#EE7621\"><b>Scattered thunderstorms likely".thunderproc($kiww).". Except for higher amounts of precipitation within thunderstorms.</b></span>",$str);
break;
case($num==404&&$cover>$tsclcover&&$newtot>0):
$str = preg_replace('!(light|moderate|heavy) (rain showers|rain).!is',
"<span style=\"color:#CD3333\"><b>Scattered heavy or severe thunderstorms".thunderproc($kiww).". Except for higher amounts of precipitation within thunderstorms.</b></span>",$str);
break;
case($num==501&&$cover>$tsclcover&&$newtot>0):
$str = preg_replace('!(light|moderate|heavy) (rain showers|rain).!is',
"<span style=\"color:#EE7621\"><b>$1 $2 but few with thunder. Except for higher amounts of precipitation within thunderstorms.</b></span>",$str);
break;
case($num==503&&$cover>$tsclcover&&$newtot>0):
$str = preg_replace('!(light|moderate|heavy) (rain showers|rain).!is',
"<span style=\"color:#CD3333\"><b>Numerous thunderstorms, some possible heavy".thunderproc($kiww).". Except for higher amounts of precipitation within thunderstorms.</b></span>",$str);
break;
case($num==504&&$cover>$tsclcover&&$newtot>0):
$str = preg_replace('!(light|moderate|heavy) (rain showers|rain).!is',
"<span style=\"color:#CD3333\"><b>Numerous thunderstorms, some heavy or severe".thunderproc($kiww).". Except for higher amounts of precipitation within thunderstorms.</b></span>",$str);
break;
case($num==505&&$cover>$tsclcover&&$newtot>0):
$str = preg_replace('!(light|moderate|heavy) (rain showers|rain).!is',
"<span style=\"color:#CD3333\"><b>Numerous thunderstorms, some severe".thunderproc($kiww).". Except for higher amounts of precipitation within thunderstorms.</b></span>",$str);
break;
case($num==506&&$cover>$tsclcover&&$newtot>0):
$str = preg_replace('!(light|moderate|heavy) (rain showers|rain).!is',
"<span style=\"color:#CD3333\"><b>Nnumerous thunderstorms, many severe".thunderproc($kiww).". Except for higher amounts of precipitation within thunderstorms.</b></span>",$str);
break;
# Other
case (preg_match("|eavy sleet|",$str)): $str = '<span style="color:red"><b>'.$str.'</b></span>'; break;
case (preg_match("|oderate sleet|",$str)): $str = '<span style="color:#EE7621"><b>'.$str.'</b></span>'; break;
case (preg_match("|eavy snow|",$str)): $str = '<span style="color:#3399CC"><b>'.$str.'</b></span>'; break;
case (preg_match("|oderate snow|",$str)): $str = '<span style="color:#3399CC"><b>'.$str.'</b></span>'; break;
}
if($lightsnowcolorized == true) {
switch (TRUE) {
case (preg_match("|ight sleet|",$str)): $str = '<span style="color:#EE7621"><b>'.$str.'</b></span>'; break;
case (preg_match("|ight snow|",$str)): $str = '<span style="color:#3399CC"><b>'.$str.'</b></span>'; break;
}
}
return $str;
}

# Other small fixes to string
function thunderproc($raw){
global $showtsprob,$ts,$tsactive;
$tsactive = false;
if($showtsprob == true){
if($raw < 1){$raw = ", probability &lt;20%*";$tsactive = true;}
else if($raw >= 1 && $raw < 2){$raw = ", probability 20-40%*";$tsactive = true;}
else if($raw >= 2 && $raw < 3){$raw = ", probability 40-60%*";$tsactive = true;}
else if($raw >= 3 && $raw < 4){$raw = ", probability 60-80%*";$tsactive = true;}
else if($raw >= 4 && $raw < 5){$raw = ", probability &ht;80%*";$tsactive = true;}
else if($raw >= 5){$raw = ", probability ~100%*";$tsactive = true;}
return $raw;
} 
else {return '';}
}

function thunderproc2($raw){
if($raw <= 0){$raw = "0%";}
else if($raw > 0 && $raw < 1){$raw = "&lt;20%";}
else if($raw >= 1 && $raw < 2){$raw = "20-40%";}
else if($raw >= 2 && $raw < 3){$raw = "40-60%";}
else if($raw >= 3 && $raw < 4){$raw = "60-80%";}
else if($raw >= 4 && $raw < 5){$raw = "&ht;80%";}
else if($raw >= 5){$raw = "~100%";}
return $raw;
}

function thunderproc3($raw){
if($raw <= 0){$raw = 0;}
else if($raw > 0 && $raw < 5){$raw = round($raw*20);}
else if($raw >= 5){$raw = 100;}
return $raw;
}

###########################################################################
# Colorize thunder also in plaintext.txt
function fixthunder($str) { 
switch (TRUE) {
    case (preg_match('!Severe thunderstorms likely, with possible tornados.!',$str)):
    $str = str_replace("Severe thunderstorms likely, with possible tornados.",
    '<br/><span style="color:red"><b>Severe thunderstorms likely, with possible tornados.</b></span>',$str);
    break;
    case (preg_match('!Thunderstorms very likely, some severe.!',$str)):
    $str = str_replace("Thunderstorms very likely, some severe.",
    '<br/><span style="color:red"><b>Thunderstorms very likely, some severe.</b></span>',$str);
    break;
    case (preg_match('!Thunderstorms very likely, some possibly severe.!',$str)):
    $str = str_replace("Thunderstorms very likely, some possibly severe.",
    '<br/><span style="color:red"><b></b></span>',$str);
    break;
    case (preg_match('!Scattered thunderstorms likely, some possibly severe.!',$str)):
    $str = str_replace("Scattered thunderstorms likely, some possibly severe.",
    '<br/><span style="color:red"><b>Scattered thunderstorms likely, some possibly severe.</b></span>',$str);
    break;
    case (preg_match('!Scattered thunderstorms likely.!',$str)):
    $str = str_replace("Scattered thunderstorms likely.",
    '<br/><span style="color:#EE7621"><b>Scattered thunderstorms likely.</b></span>',$str);
    break;
    case (preg_match('!Scattered thundershowers possible.!',$str)):
    $str = str_replace("Scattered thundershowers possible.",
    '<br/><span style="color:#EE7621"><b>Scattered thundershowers possible.</b></span>',$str);
    break;
}
return $str;
}

########################################################################
# LOST AND FOUND-SECTION ;)
# Misc small "good to have"-functions but also misc small fixes/checks

function crop($name) {
if(preg_match('|rr;|Ui',$name)) { 
$clen = strpos($name,";");
$name = substr($name, $clen+1, (strlen($name)-$clen));
}
return $name;
}

function dayornight($time) {
global $lat,$long;
$utcdiff = date('Z')/3600;
$zenith=90+40/60;
$sunrise_epoch = date_sunrise($time, SUNFUNCS_RET_TIMESTAMP, $lat, $long, $zenith, $utcdiff);
$sunset_epoch  = date_sunset($time, SUNFUNCS_RET_TIMESTAMP, $lat, $long, $zenith, $utcdiff);
$time_epoch = $time; // time now
if ($time_epoch >= $sunset_epoch or $time_epoch <= $sunrise_epoch) {
$dayornight = 'night';
} else {
$dayornight = 'day';
}
return $dayornight;
}

function checkday($val,$dtline){
$curr = date('z', time());
$va  = date('z', $val);
$vv  = date('w', $val);
$day = '';
if($curr == $va) {
$day = get_lang("Today");
} else if(($va-$curr) == 1 || $va == 0) {
$day = get_lang("Tomorrow");
} else {
$day = $dtline;
}
return $day;
}

function checksprite($raw){
global $highwindlimit;
if($raw <= $highwindlimit){return "b";}
elseif($raw <23){return "o1";}
else{return "o2";}
}

function fixgust($w,$g){
global $showwgust;
$diff = $g-$w;
if($diff>3 && $showwgust){return '-'.$g;}
else{return '';}
}

function fixgust2($w,$g,$uom){
$diff = $g-$w;
if($diff>3){return get_lang("Gusting to").' <b>'.$g.' '.$uom.'</b><br/>';}
else{return '';}
}

function fixtime($time) {
if(is_substr($time,'a')) {
$time = str_replace('a','',$time);
$time = str_replace('12','0',$time);
}
if(is_substr($time,'p')) {
$time = str_replace('p','',$time);
if(substr($time,0,2) < 12) {
$time = ((substr($time,0,2))+12).substr($time,2,3);
}
}
return $time;
}

function fixwinter($str) {
return '<span style="color: #3399CC;"><b>'.$str.'</b></span>';
}

function fixSnowTS($maxsnow,$snow,$uom,$ts) {
global $Snowdtxt;
$snow = str_replace(',','.',$snow);
if($maxsnow > 0) {
if($snow > 0) {
$snowrow = '<span style="color: #3399CC;"><b>'.round($snow).' '.$uom.'</b></span>';
} else {
$snowrow = '&nbsp;';
}
} else { $snowrow = $ts; }
return $snowrow;
}

function fixsnow($maxsnow,$snow,$uom,$snowtip) {
global $Snowdtxt,$useussnow;
$snow = str_replace(',','.',$snow);
if($maxsnow > 0) {
if($snow > 0) {
if($useussnow==1){$snod = $snow;}
else{$snod=round($snow);}
return '
<td style="width:9%;vertical-align:middle;" class="tooltip" title="'.$snowtip.'"><span style="color: #3399CC;"><b>'.$snod.' '.$uom.'</b></span></td>';
} else {
return '
<td style="width:9%;vertical-align:middle;">&nbsp;</td>';
}
} else { return; }
}

function fixuvimg($uv,$dn,$minuv) {
global $wxallmainfolder;
$uv = round($uv);
if($uv >= $minuv) {
if($uv<10){$uv=''.$uv;}
$imgstr = '<small>UV<br/></small><img src="'.$wxallmainfolder.'img/uv2/'.$uv.'.png" alt="UV Index: '.$uv.'"/>';
} else {
$imgstr = '&nbsp;';
}
return $imgstr;
}

function fixwdir($winddir) {
  if (!isset($winddir)) {
    return "---";
  }
$windlabel = array ("N","NNE", "NE", "ENE", "E", "ESE", "SE", "SSE", "S",
   "SSW","SW", "WSW", "W", "WNW", "NW", "NNW","N");
$dir = $windlabel[ (int) ($winddir / 22.5) ];
return "$dir";
}

function showprecip($prec,$unit,$show,$noshowtxt) {
global $traceam;
if($prec > $traceam) {
$precrow = '<span style="color: #3399CC;"><b>'.fixprecip($prec).' '.$unit.'</b></span>';} 
else if ($show == true) {$precrow = $noshowtxt;} 
else { $precrow = '&nbsp;'; }
return $precrow;
}

function showprecipt($prec,$unit,$show,$noshowtxt) {
if($prec > 0) {
$precrow = '<span style="color: #3399CC;"><b>'.$prec.' '.$unit.'</b></span>';} 
else if ($show == true) {$precrow = $noshowtxt;} 
else { $precrow = '&nbsp;'; }
return $precrow;
}
function showprecipt2($prec,$unit,$noshowtxt) {
if($prec > 0) {return $name.' <b>'.$prec.' '.$unit.'</b>';} 
else  {return $noshowtxt;} 
}

function fixprecip($st,$unit) {
if($unit=="mm"){
if($st>50) { $pout = "50+"; }
if($st==50) { $pout = "50"; }
if($st<50) { $pout = "40-50"; }
if($st==40) { $pout = "40"; }
if($st<40) { $pout = "30-40"; }
if($st==30) { $pout = "30"; }
if($st<30) { $pout = "20-30"; }
if($st==20) { $pout = "20"; }
if($st<20) { $pout = "10-20"; }
if($st==10) { $pout = "10"; }
if($st<10) { $pout = "5-10"; }
if($st==5) { $pout = "5"; }
if($st<5) { $pout = "&lt;5"; }
if($st<=2) { $pout = "&lt;2"; }
}else{
if($st>5) { $pout = "5+"; }
if($st==5) { $pout = "5"; }
if($st<5) { $pout = "4-5"; }
if($st==4) { $pout = "4"; }
if($st<4) { $pout = "3-4"; }
if($st==3) { $pout = "3"; }
if($st<3) { $pout = "2-3"; }
if($st==2) { $pout = "2"; }
if($st<2) { $pout = "1-2"; }
if($st==1) { $pout = "1"; }
if($st<1) { $pout = "0.5-1"; }
if($st==0.5) { $pout = "0.5"; }
if($st==0.35) { $pout = "0.25-0.5"; }
if($st==0.25) { $pout = "0.25"; }
if($st<0.25) { $pout = "&lt;0.25"; }
if($st==0.1) { $pout = "0.1"; }
if($st<0.1) { $pout = "&lt;0.1"; }
}
#if($st==0) { $pout = "0"; }
return $pout;
}

// Returns whether needle was found in haystack
function is_substr($haystack, $needle){
$pos = strpos($haystack, $needle);
   if ($pos === false) {
   return false;
   } else {
   return true;
   }
}

function uv_word($val) {
if($val==0) { return '<b>'.get_lang("No UV Index").'</b>'; }
if($val>=1&&$val<=2) { return '<b>'.get_lang("Low").'</b>'; }
if($val>=3&&$val<=5) { return '<b>'.get_lang("Moderate").'</b>'; }
if($val>=6&&$val<=7) { return '<b>'.get_lang("High").'</b>'; }
if($val>=8&&$val<=10) { return '<b>'.get_lang("Very high").'</b>'; }
if($val>=11) { return '<b>'.get_lang("Extreme").'</b>'; }
}

function fixtempcolor($temp) {
global $bignumbdir;
$temp = round($temp);
if($temp==0) { $temp = str_replace("-", "", $temp); }
if($temp<0) { $temp = str_replace("-", "n", $temp); }
return '<span style="display:inline-block;" class="temps t'.$temp.'_png"></span>';
}

function wxsim_tempcolor($temp) {
global $bignumbdir;
if($temp<0) { $temp = str_replace("-", "n", $temp); }
return '<span style="display:inline-block;margin-top:4px;" class="temps t'.$temp.'_png"></span>';
}

function sec2hms($sec, $padHours = false) {
    $hms = "";
    $hours = intval(intval($sec) / 3600); 
    $hms .= ($padHours) 
          ? str_pad($hours, 2, "0", STR_PAD_LEFT). ":"
          : $hours. ":";
    $minutes = intval(($sec / 60) % 60); 
    $hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT);
    $seconds = intval($sec % 60); 
    //$hms .= str_pad($seconds, 2, "0", STR_PAD_LEFT);
    return $hms;
}

?>