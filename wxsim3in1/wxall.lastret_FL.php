<?php
# 3in1 wxall.php V.4

$csv = file($lastretfile);
$howmany = count($csv);
ini_set('display_errors', '0');


$csv = preg_replace( '/\s+/', ' ', $csv ); // remove white space
$csv = preg_replace( '/\,/', '.', $csv ); // replace , with . for decimal spaces

$hour3 = array('00:00','03:00','06:00','09:00','12:00','15:00','18:00','21:00');
$hour8 = array('03:00','12:00','18:00');
$months = array('','Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
$startt = '';
$prectot = 0;

# Unit-specific limits & traceamount
// rain
if (preg_match("|mm|",$uoms[1])) {$traceam3 = 0.3;$traceam1 = 0.2;$defra = 10;} 
else {$traceam3 = 0.011;$traceam1 = 0.007;$defra = 0.5;} 
// temp
if (preg_match("|C|",$uoms[0])) {$frzz = 0;$slzz = 0.7;} 
else {$frzz = 32;$slzz = 35;} 
// wind
if (preg_match("|m/s|",$uoms[2])) {$defwind = 10;} 
else if (preg_match("|mph|",$uoms[2])) {$defwind = 20;} 
else if (preg_match("|km|",$uoms[2])) {$defwind = 35;}
else {$defwind = 20;}
// snow
if (preg_match("|cm|",$uoms[1])) {$defsno = 10;} 
else {$defsno = 0.5;} 

// days for Flot
$days = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
for($i=0;$i<count($days);$i++) {
$transday = get_lang($days[$i]);
$dayarray.="'".$transday."',";
$daarray[]=$transday;
}
$dayarray = substr($dayarray, 0, -1);

#######################################
## Figure out where data are located ##
$snowava = false;
$sgactive = false;
$baroava = false;
$solarava = false;
$stuff = $csv[6];
$line = explode(' ', $stuff);
$namedata = array_reverse($line);

for($r=0;$r<count($namedata);$r++){
if($namedata[$r] == "AIR"){$tempwp = $r;}
if($namedata[$r] == "DEW"){$dewwp = $r;}
if($namedata[$r] == "WCF"){$chillwp = $r;}
if($namedata[$r] == "HT.I"){$heatwp = $r;}
if($namedata[$r] == "W.DIR"){$dirwp = $r;}
if($namedata[$r] == "W.SP"){$windwp = $r;}
if($namedata[$r] == "SLP"){$barowp = $r;}
if($namedata[$r] == "PTOT"){$precwp = $r;}
if($namedata[$r] == "VIS"){$visibwp = $r; $visibava = true;}
if($namedata[$r] == "SWXO"){$kiwp = $r;}
if($namedata[$r] == "UVI"){$uviwp = $r;}
if($namedata[$r] == "S.IR"){$solarwp = $r;$solarava = true;}
if($namedata[$r] == "G10M"){$windgstwp = $r;}
if($namedata[$r] == "G1HR"){$windgstwp = $r;}
if($namedata[$r] == "15M"){$temp15mwp = $r;}
if($namedata[$r] == "SN.C"){$snowwp = $r; $snowava = true;}
if($namedata[$r] == "GRS"){$grasstwp = $r; $sgactive = true;}
if($namedata[$r] == "SURF"){$surftwp = $r;}
if($namedata[$r] == "TSO1"){$soil1twp = $r;}
if($namedata[$r] == "TSO2"){$soil2twp = $r;}
if($namedata[$r] == "TSO3"){$soil2twp = $r;}
if($namedata[$r] == "MSO1"){$soil1mwp = $r;}
if($namedata[$r] == "MSO2"){$soil2mwp = $r;}
if($namedata[$r] == "MSO3"){$soil2mwp = $r;}
if($namedata[$r] == "TSCD"){$convwp = $r;}
if($namedata[$r] == "L.CD"){$covvwp = $r;}
if($namedata[$r] == "TMAX"){$tmaxwp = $r;$tmaxtive = true;}
if($namedata[$r] == "TMIN"){$tminwp = $r;$tmaxtive = true;}
if($namedata[$r] == "LVL1"){$levelwp = $r;}
}
$condwp1 = $tempwp+1;
$condwp2 = $tempwp+2;
$condwp3 = $tempwp+3;
$condwp4 = $tempwp+4;

##################################################
# Loop the data

$prectot = 0;
$r3 = 0;
$r1 = 0;
$sctfrost3 = false;
$hwyfrost3 = false;
$ts3 = false;
for($i=3;$i<$howmany;$i++) {
$stuff = $csv[$i];
if(is_substr($stuff,'Nighttime lows and daytime highs')) { break;}
# Lets fix also :xx minutes
$rawmin = substr($stuff,3,2);
if($rawmin > 40) {
$bmin = substr($stuff,2,3);
$stuff = str_replace($bmin,":00",$stuff);
}
$lines = explode(' ', $stuff);
$revdata = array_reverse($lines);

# Date
if(is_numeric($lines[0])&&preg_match('|-------|',$stuff)) {
$month = date('m');
$daline = $csv[$i+1];
$dtline = $daline.' '.$lines[0]. ' '.$lines[1];
$rawdate = $lines[0]. ' '.$lines[1];
if($month == "12" && $lines[1] == "Jan") {
$rawdate = $rawdate .' '.(date('Y')+1);
}
$rdate = strtotime('00:00:00'. $dtline);
$dtline = checkday($rdate,$dtline);
$utcdiff = date('Z');
reset ($LanguageLookup);
foreach ($LanguageLookup as $key => $replacement) {
$dtline = str_replace($key,$replacement,$dtline);
$daline = str_replace($key,$replacement,$daline);
}
$dateline[] = $dtline;
$dayline[] = $daline;
$sunrises[] = date_sunrise($rdate , SUNFUNCS_RET_STRING, $lat, $long, $zenith, ($utcdiff/3600));
$sunsets[] = date_sunset($rdate , SUNFUNCS_RET_STRING, $lat, $long, $zenith, ($utcdiff/3600));
$sunrisesstr[] = date_sunrise($rdate , SUNFUNCS_RET_TIMESTAMP, $lat, $long, $zenith, ($utcdiff/3600));
$sunsetsstr[] = date_sunset($rdate , SUNFUNCS_RET_TIMESTAMP, $lat, $long, $zenith, ($utcdiff/3600));
}
# Time
if(is_numeric($revdata[2])){
$rtime = substr($stuff,0,6);
$datestr = fixtime($rtime).':00 '.$rawdate;
$tstr = strtotime($datestr);
if(is_numeric($tstr)){
$dn = dayornight($tstr);
if($startt == '') { $startt = $tstr; }
$jst = (($tstr+$utcdiff)*1000);
$stopt = $tstr+$utcdiff;
if($i==4){$tstr=strtotime($wxallupdated);}
if((date('Hi',$tstr) == '0000') || $i==4) {
$jsdaychanges[] = $jst;
$daychanges[] = $tstr;
}
}
}

if(is_numeric($revdata[2]) && $tstr > time()) {
$wgstsgraph[]= $revdata[$windgstwp];
$wgstgraphs[]= $revdata[$windgstwp];
$temps1[]= $revdata[$tempwp];
$temps3[]= $revdata[$tempwp];
$chills1[]= $revdata[$chillwp];
$chills3[]= $revdata[$chillwp];
$heats1[]= $revdata[$heatwp];
$heats3[]= $revdata[$heatwp];
$wgsts3[]= $revdata[$windgstwp];
$visib1[] = $revdata[$visibwp];
$tmax3[] = $revdata[$tmaxwp];
$tmin3[] = $revdata[$tminwp];

# JSONS
$tempjson.= '['.$jst.','.$revdata[$tempwp].'],';
$tmaxjson.= '['.$jst.','.$revdata[$tmaxwp].'],';
$tminjson.= '['.$jst.','.$revdata[$tminwp].'],';
$dewjson.= '['.$jst.','.$revdata[$dewwp].'],';
$chilljson.= '['.$jst.','.$revdata[$chillwp].'],';
$heatjson.= '['.$jst.','.$revdata[$heatwp].'],';
$barojson.= '['.$jst.','.$revdata[$barowp].'],';
$uvijson.= '['.$jst.','.$revdata[$uviwp].'],';
$solajson.= '['.$jst.','.$revdata[$solarwp].'],';
$solargraph[] = $revdata[$solarwp];
$uvgraph[] = $revdata[$uviwp];
if($snowava <> true){$revdata[$snowwp] = 0;}
$snowdjson.= '['.$jst.','.round($revdata[$snowwp]).'],';
$snowgraph[] = $revdata[$snowwp];
$kijson.= '['.$jst.','.thunderproc3($revdata[$kiwp]).'],';
if(in_array(date('H:i',$tstr),$hour3)) {
if($revdata[$dirwp] >= 180) {$dirp = ($revdata[$dirwp]-180);}
else{$dirp = ($revdata[$dirwp]+180);}
$windjson.= '['.$jst.','.$revdata[$windwp].','.$dirp.'],';
$windgstjson.= '['.$jst.','.max($wgstsgraph).'],';
$wgstgraphs[] = max($wgstsgraph);
$wgstsgraph = array();
}

# Frost
$frostline3 = '';
if($month >=$froststart && $month <=$frostend) { 
if(preg_match('|SCTD.FRO|Ui',$stuff) || preg_match('|LT. FROST|Ui',$stuff)) {$sctfrost3 = true;}
else if(preg_match('|FRO|Ui',$stuff)) { $hwyfrost3 = true;}
}

##############################################################
# Every 1 hour-stuff for 48 hrs datailed table

if(substr($stuff,3,2)=="00" && $r1<=48) {
$r1++;
$timestrs1[] = $tstr;
$temp1[]= $revdata[$tempwp];
$dew1[]= $revdata[$dewwp];
$chill1[]= $revdata[$chillwp];
$heat1[]= $revdata[$heatwp];
$dir1[]= $revdata[$dirwp];
$dirs1[]= $revdata[$dirwp];
$windw1[]= $revdata[$windwp];
$baro1[]= $revdata[$barowp];
$snow1[]= round($revdata[$snowwp]);
$windgst1[$r1-2] = max($wgsts1);
$dn1[$r1-2] = $dn;
$uv1[$r1-2] = max($uvs1);
if($sgactive == true) {
$grast1[] = $revdata[$grasstwp];
$surft1[] = $revdata[$surftwp];
$soil1t1[] = $revdata[$soil1twp];
$soil2t1[] = $revdata[$soil2twp]; 
$soil1m1[] = $revdata[$soil1mwp];
}
# Windchill & Heat
$mahe = max($heats1);
$mate = max($temps1);
$hdif = ($mahe-$mate);
$michi = min($chills1);
$mite = min($temps1);
$mdif = ($michi-$mite);
if($mdif < $windchilllimitb && $revdata[$tempwp] < $windchilllimit) {$chiline1[] = '<br/><span><small>'.$windchtxt.':<b><span style="color:#3399CC;">'.round($michi).$uoms[0].'</span></b></small></span>'; }
else if($hdif > $heatlimitb && $revdata[$tempwp] > $heatlimit) {$chiline1[] = '<br/><span><small>'.$heattxt.':<b><span style="color:#CD3333;">'.round($mahe).$uoms[0].'</span></b></small></span>'; }
else {$chiline1[] = ''; }
# rain
$cover1 = max($covers1);
$newtot1 = $revdata[$precwp];
$newtot1 -= $prectot1;
$prectot1 = $revdata[$precwp];
if($cover1<$precclcover || $newtot1<$traceam1){$newtot1 = 0;}
$rtot1[$r1-2]= sprintf("%01.1f", $newtot1);
$conv = 0;
if(count($convs1)>0){
$conv1 = max($convs1);
}
$kiw1= max($kiws1);
# check some conditions
$fogw1 = fogcond($rawcond1); // fog
$raintyp = raintyp($rawtemp1,$rawdew1,$rawlevel1,$tempuom);
$prcond1 = preccond($cover1,$newtot1,$raintyp);
# Icon
$iconcond1 = wanewicon($prcond1,$fogw1,$conv1,$cover1,$newtot1); // Initial icon
$icons1 = explode('|',$iconcond1);
if($rawdn1 <> "day") {
$wicon1[$r1-2] = $icons1[1];
} else {
$wicon1[$r1-2] = $icons1[0];
}
$cov1[$r1-2] = max($covers1);
$con1[$r1-2] = max($kiws1);
$vis1[$r1-2] = min($visib1);
# for next loop1
$rawtemp1 = $revdata[$tempwp];
$rawdew1 = $revdata[$dewwp];
$rawlevel1 = $revdata[$levelwp];
$rawdn1 = $dn;
$rawjsstr = $jsstr;
# resets
$heats1 = array();
$chills1 = array();
$wgsts1 = array();
$uvs1 = array();
$visib1 = array();
$covers1 = array();
$convs1 = array();
$kiws1 = array();
$rawcond1 = '';
}

# 1 hour data done
########################################################
# Every 3 hour-stuff

if(in_array(date('H:i',$tstr),$hour3)) {
$r3++;
$timestrs3[] = $tstr;
$temp3[]= $revdata[$tempwp];
$dew3[]= $revdata[$dewwp];
$chill3[]= $revdata[$chillwp];
$heat3[]= $revdata[$heatwp];
$dir3[]= $revdata[$dirwp];
$windw3[]= $revdata[$windwp];
$baro3[]= $revdata[$barowp];
if($snowava == true){$snow3[]= round($revdata[$snowwp]);}
$windgst3[$r3-2] = max($wgsts3);
$dn3[$r3-2] = $dn;
$uv3[$r3-2] = max($uvs3);
if($sgactive == true) {
$grast3[] = $revdata[$grasstwp];
$surft3[] = $revdata[$surftwp];
$soil1t3[] = $revdata[$soil1twp];
$soil2t3[] = $revdata[$soil2twp]; 
$soil1m3[] = $revdata[$soil1mwp];
$soil2m3[] = $revdata[$soil2mwp];
}
# Windchill & Heat
$mahe = max($heats3);
$mate = max($temps3);
$hdif = ($mahe-$mate);
$michi = min($chills3);
$mite = min($temps3);
$mdif = ($michi-$mite);
if($mdif < $windchilllimitb && $revdata[$tempwp] < $windchilllimit) {$chiline3[] = '<br/><span><small>'.$windchtxt.':<b><span style="color:#3399CC;">'.round($michi).$uoms[0].'</span></b></small></span>'; }
else if($hdif > $heatlimitb && $revdata[$tempwp] > $heatlimit) {$chiline3[] = '<br/><span><small>'.$heattxt.':<b><span style="color:#CD3333;">'.round($mahe).$uoms[0].'</span></b></small></span>'; }
else {$chiline3[] = ''; }
# rain
$cover3 = max($covers3);
$covt3[$r3-2] = max($covers3);
$newtot3 = $revdata[$precwp];
$newtot3 -= $prectot3;
$prectot3 = $revdata[$precwp];
if($cover3<$precclcover || $newtot3<$traceam3){$newtot3 = 0;}
$rtot3[$r3-2]= sprintf("%01.1f", $newtot3);
$jsstr = $jst;
if($newtot3>0){
$totprecipjson.= '['.$rawjsstr.','.$newtot3.'],';
$precsgraph[] = $newtot3;
}
$conv = 0;
if(count($convs3)>0){
$conv3 = max($convs3);
}
# check some conditions
$fogw3 = fogcond($rawcond3); // fog
$raintyp = raintyp($rawtemp3,$rawdew3,$rawlevel3,$tempuom);
$prcond3 = preccond($cover3,$newtot3,$raintyp);
# Icon
$iconcond3 = wanewicon($prcond3,$fogw3,$conv3,$cover3,$newtot3); // Initial icon
$icons3 = explode('|',$iconcond3);
if($rawdn3 <> "day") {
$wicon3[$r3-2] = $icons3[1];
} else {
$wicon3[$r3-2] = $icons3[0];
}
$covvs3[$r3-2] = $cover3;
$kiw3= max($kiws3);
$kiwws3[$r3-2] = $kiw3;
# Text
$txtcond3 = $txtcloud;
$txtcond3.= ". "; 
if($rawdn3 <> "day") {
$txtcond3 = str_replace('Mostly sunny','Mostly clear',$txtcond3);
$txtcond3 = str_replace('Sunny','Clear',$txtcond3);
}
if(strlen($prcond3) > 4) {$prcond3 = '<br/>'.ucfirst($prcond3).'.';}
if($fogw3 <> '') {$fogw3 = '<br/>'.ucfirst($fogw3).'.';}
if($sctfrost3 == true) {$frostline3 = '<br/><span style="color: #3399CC;"><b>Scattered frost.</b></span>'; }
if($hwyfrost3 == true) {$frostline3 = '<br/><span style="color: #3399CC;"><b>Moderate or heavy frost.</b></span>';}
$sctfrost3 = false;
$hwyfrost3 = false;
$tsactive = false;
$prcond3 = fixstring($prcond3,$conv3,$kiw3,$cover3,$newtot3,$traceam3);
$totcond3 = $txtcond3.$prcond3.$fogw3.' '.$frostline3.$visibline3;
reset ($Language);
foreach ($Language as $key => $replacement) {
$totcond3 = str_replace($key,$replacement,$totcond3);
}
$conds3[$r3-2] = $totcond3; // Add condition to array
$tsact[$r3-2] = $tsactive;
# for next loop2
$rawtemp3 = $revdata[$tempwp];
$rawdew3 = $revdata[$dewwp];
$rawlevel3 = $revdata[$levelwp];
$rawdn3 = $dn;
$rawjsstr = $jsstr;
# resets
$heats3 = array();
$chills3 = array();
$wgsts3 = array();
$uvs3 = array();
$covers3 = array();
$convs3 = array();
$kiws3 = array();
$rawcond3 = '';
} // EOF 3 hour

# 3 hour data done
##########################################################
# Every 8 hour-stuff for the graphs

if(in_array(date('H:i',$tstr),$hour8)) {
# Graphicons
$icons = explode('|',$iconcond3);
if($dn <> "day") {
$condics[] = $icons[1];
} else {
$condics[] = $icons[0]; 
}
$iconpoints[] = $i-3;
} # EOF 8 hr

# for next loop 3
$wgsts1[]= $revdata[$windgstwp];
$wgsts3[]= $revdata[$windgstwp];
$uvs1[]= $revdata[$uviwp];
$uvs3[]= $revdata[$uviwp];
$covers1[] = $revdata[$covvwp];
$covers3[] = $revdata[$covvwp];
$kiws1[]= $revdata[$kiwp];
$kiws3[]= $revdata[$kiwp];
$convs1[] = $revdata[$convwp];
$convs3[] = $revdata[$convwp];
$rawcond1.= $revdata[$condwp4].$revdata[$condwp3].$revdata[$condwp2].$revdata[$condwp1];
$rawcond3.= $revdata[$condwp4].$revdata[$condwp3].$revdata[$condwp2].$revdata[$condwp1];

}  # IF numeric
}  # EOF Loop

# Data done!
##############################################################
# Create 1 hour-table

$r=0;
$hourtable1 = '<table class="wxsimtbl" style="width:100%;">
<tr class="wxsimtbl_header">
<td>&nbsp;</td>
<td style="padding:6px;text-align: center"><b>'.$condtxt.'</b></td>
<td style="padding:6px;text-align: center"><b>'.$Temptxt.'</b></td>
<td style="padding:6px;text-align: center"><b>'.$Wind.'</b></td>
<td style="padding:6px;text-align: center"><b>'.$Prectxt.'</b></td>
';
if(max($snow1) > 0) { $hourtable1.= '<td style="padding:6px;text-align: center"><b>'.$Snowdtxt.'</b></td>
'; } else {
$hourtable1.= '<td style="padding:6px;"><b>'.$Kitxt.'</b></td>';
}
$hourtable1.= '
<td style="padding:6px;text-align: center"><b>'.get_lang("Dewpoint").'</b></td>
<td style="padding:6px;text-align: center"><b>'.$Barotxt.'</b></td>
<td style="padding:6px;text-align: center"><b>'.get_lang("UV Index").'</b></td>
<td style="padding:6px;"><b>'.get_lang("Cloud cover").'</b></td>
<td style="padding:6px;"><b>'.get_lang("Visibility").'</b></td>
</tr>';

$tot = count($temp1);
for($i=0;$i<$tot-1;$i++) {
if($i==0 || in_array($timestrs1[$i],$daychanges)) {
$hr = date('H',$timestrs1[$i]);
if(($hr > 21 || $hr < 4) && $i == 0) {$r++;}
$daylength = sec2hms($sunsetsstr[$r]-$sunrisesstr[$r]);
$suntip = get_lang("Daylength").': '.$daylength;
$hourtable1.='<tr class="wxsimdateline"><td colspan="11" class="wxsimdateline-td"><span style="float:right;position:relative;top:-3px;font-size:10px;"><span style="display:inline-block;position:relative;top:4px;" class="misc sun_up"></span>&nbsp;'.$sunrises[$r].'&nbsp;&nbsp;&nbsp;
<span style="display:inline-block;position:relative;top:4px;" class="misc sun_down"></span>&nbsp;'.$sunsets[$r].'
&nbsp;&nbsp;&nbsp;'.$suntip.'</span>
<span style="position:relative;top:2px"><b>'.$dateline[$r].'</b></span></td></tr>';
$ra = $r;
$r++;
}

if($i%2==0) {$row = 'wxsimdata'; }else{$row = 'wxsimdataodd';}
if($useica){
if(round($windw1[$i])==0){$wxdiric="E";}else{$wxdiric = fixwdir($dirs1[$i]);}
if(round($windw1[$i])>$highwindlimit){$ah="O-";}else{$ah="";}
$wawstr = '
<div class="wind-'.checksprite(round($windw1[$i])).' '.$ah.$wxdiric.round($windw1[$i]).'" style="margin:10px auto;"></div>';
} else {
if (round($windw1[$i]) == "0") { $wawstr = '<div style="position:relative;height:42px;width:100%;">
<span style="display:inline-block;background:url('.$wxallmainfolder.'img/calm.png) 50% 50% no-repeat;width: 42px; height: 42px;"></span>
<div style="position:absolute;top:15px;width:100%;text-align:center;font-size:11px;">0</div></div>'; }
else { 
if($windw1[$i] < $highwindlimit ){$wxdiric = fixwdir($dirs1[$i]);
} else {$wxdiric = 'W-'.fixwdir($dirs1[$i]);}
$wawstr = '<div style="position:relative;height:41px;width:100%;">
<span style="display:inline-block;" class="wxallwind wxall-'.$wxdiric.'"></span>
<div style="position:absolute;top:14px;width:100%;text-align:center;font-size:11px;">'.round($windw1[$i]).'</div></div>';
}
}
# tooltips
$icontip1[] = '<b>'.$condtxt.'</b><br/>'.$dayline[$ra].' '.date('H:i',$timestrs1[$i]).'-'.date('H:i',$timestrs1[$i+1]);
$temptip1[] = '<b>'.$Temptxt.'</b><br/>'.$dayline[$ra].' '.date('H:i',$timestrs1[$i]).':<br/><b>'.$temp1[$i].$uoms[0].'</b>';
$windtip1[] = '<b>'.$Wind.'</b><br/>'.$dayline[$ra].' '.date('H:i',$timestrs1[$i]).':<br/><b>'.fixwdir($dir1[$i]).' '.round($windw1[$i]).' '.$uoms[2].'</b><br/>'.fixgust2(round($windw1[$i]),$windgst1[$i],$uoms[2]);
$prectip1[] = '<b>'.$Prectxt.'</b><br/>'.$dayline[$ra].' '.date('H:i',$timestrs1[$i]).'-'.date('H:i',$timestrs1[$i+1]).':<br/><b>'.showprecipt2($rtot1[$i],$uoms[1],$noprectxt);
$dewtip1[] = '<b>'.get_lang("Dewpoint").'</b><br/>'.$dayline[$ra].' '.date('H:i',$timestrs1[$i]).':<br/><b>'.$dew1[$i].$uoms[0].'</b>';
$barotip1[] = '<b>'.$Barotxt.'</b><br/>'.$dayline[$ra].' '.date('H:i',$timestrs1[$i]).':<br/><b>'.$baro1[$i].' '.$uoms[3].'</b>';
$uvtip1[] = '<b>'.get_lang("UV Index").'</b><br/>'.$dayline[$ra].' '.date('H:i',$timestrs1[$i]).':<br/>'.uv_word(round($uv1[$i]));
$covetip1[] = '<b>'.get_lang("Cloud cover").'</b><br/>'.$dayline[$ra].' '.date('H:i',$timestrs1[$i]).':<br/><b>'.$cov1[$i].'%</b>';
$visitip1[] = '<b>'.get_lang("Visibility").'</b><br/>'.$dayline[$ra].' '.date('H:i',$timestrs1[$i]).':<br/><b>'.$vis1[$i].' '.$uoms[5].'</b>';
if(max($snow1) > 0) { 
$tstip1[] = '<b>'.$Snowdtxt.'</b><br/>'.$dayline[$ra].' '.date('H:i',$timestrs1[$i]).':<br/><b>'.$snow1[$i].' '.$uoms[4].'</b>';
} else {
$tstip1[] = '<b>'.$Kitxt.'</b><br/>'.$dayline[$ra].' '.date('H:i',$timestrs1[$i]).':<br/><b>'.thunderproc2($con1[$i]).'</b><br/>('.$kids.' '.$wxallcity.')';
}
# row
$hourtable1.='
<tr style="text-align:center;" class="'.$row.'">
<td style="width:9%;vertical-align:middle;text-align:left;color:#2779aa;padding-left:6px;"><b>'.date('H:i',$timestrs1[$i]).'</b></td>
<td style="width:9%;padding:2px;vertical-align:middle;" class="tooltip" title="'.$icontip1[$i].'"><span style="display:inline-block;" class="cond45 cond45-'.str_replace(".png","",$wicon1[$i]).'"></span></td>
<td style="width:9%;vertical-align:middle;" class="tooltip" title="'.$temptip1[$i].'">'.fixtempcolor($temp1[$i]).$chiline1[$i].'</td>
<td style="width:9%;vertical-align:middle;" class="tooltip" title="'.$windtip1[$i].'">'.$wawstr.'</td>
<td style="width:9%;vertical-align:middle;" class="tooltip" title="'.$prectip1[$i].'">'.showprecipt($rtot1[$i],$uoms[1],$washowzeroprecip,$noprectxt).'</td>
<td style="width:9%;vertical-align:middle;" class="tooltip" title="'.$tstip1[$i].'">'.fixSnowTS(max($snow1),$snow1[$i],$uoms[4],thunderproc2($con1[$i])).'</td>
<td style="width:9%;vertical-align:middle;" class="tooltip" title="'.$dewtip1[$i].'">'.$dew1[$i].'&deg;</td>
<td style="width:9%;vertical-align:middle;" class="tooltip" title="'.$barotip1[$i].'">'.$baro1[$i].' '.$uoms[3].'</td>
<td style="width:9%;vertical-align:middle;" class="tooltip" title="'.$uvtip1[$i].'">'.fixuvimg($uv1[$i],$dn1[$i],$minuvtoshow).'</td>
<td style="width:9%;vertical-align:middle;" class="tooltip" title="'.$covetip1[$i].'">'.$cov1[$i].'%</td>
<td style="width:9%;vertical-align:middle;" class="tooltip" title="'.$visitip1[$i].'">'.$vis1[$i].' '.$uoms[5].'</td>
</tr>';
} # EOF loop
$hourtable1.='<tr><td colspan="11" class="wxsiminfo-box">';
$hourtable1.= get_lang("Move the mouse over the values for info.").'</td></tr></table>
';

##############################################################
# Create 3 hour-table

$r=0;
$hourtable3 = '<table class="wxsimtbl" style="width:100%;">
<tr class="wxsimtbl_header">
<td>&nbsp;</td>
<td style="padding:6px;text-align: center"><b>'.$condtxt.'</b></td>
<td style="padding:6px;text-align: center"><b>'.$Temptxt.'</b></td>
<td style="padding:6px;text-align: center"><b>'.$Wind.'</b></td>
<td style="padding:6px;text-align: center"><b>'.$Prectxt.'</b></td>
';
if(max($snow3) > 0) { $hourtable3.= '<td style="padding:6px;text-align: center"><b>'.$Snowdtxt.'</b></td>
'; }
$hourtable3.= '<td style="padding:6px;text-align: center"><b>'.$Barotxt.'</b></td>
<td style="padding:6px;text-align: center"><b>'.get_lang("UV Index").'</b></td>
<td style="padding:6px;"><b>'.$Desctxt.'</b></td>
</tr>';

$tot = count($temp3);
for($i=0;$i<$tot-1;$i++) {
if($i==0 || in_array($timestrs3[$i],$daychanges)) {
$hr = date('G',$timestrs3[$i]);
if(($hr > 21 || $hr < 4) && $r == 0) {$r++;}
$daylength = sec2hms($sunsetsstr[$r]-$sunrisesstr[$r]);
$suntip = get_lang("Daylength").': '.$daylength;
$hourtable3.='<tr class="wxsimdateline"><td colspan="9" class="wxsimdateline-td"><span style="float:right;position:relative;top:-3px;font-size:10px;"><span style="display:inline-block;position:relative;top:4px;" class="misc sun_up"></span>&nbsp;'.$sunrises[$r].'&nbsp;&nbsp;&nbsp;
<span style="display:inline-block;position:relative;top:4px;" class="misc sun_down"></span>&nbsp;'.$sunsets[$r].'
&nbsp;&nbsp;&nbsp;'.$suntip.'</span>
<span style="position:relative;top:2px"><b>'.$dateline[$r].'</b></span></td></tr>';
$ra = $r;
$r++;
}

if($i%2==0) {$row = 'wxsimdata'; }else{$row = 'wxsimdataodd';}
if($useica){
if(round($windw3[$i])==0){$wxdiric="E";}else{$wxdiric = fixwdir($dir3[$i]);}
if(round($windw3[$i])>$highwindlimit){$ah="O-";}else{$ah="";}
$wawstr = '
<div class="wind-'.checksprite(round($windw3[$i])).' '.$ah.$wxdiric.round($windw3[$i]).'" style="margin:10px auto;"></div>';
} else {
if (round($windw3[$i]) == "0") { $wawstr = '<div style="position:relative;height:42px;width:100%;">
<span style="display:inline-block;background:url('.$wxallmainfolder.'img/calm.png) 50% 50% no-repeat;width: 42px; height: 42px;"></span>
<div style="position:absolute;top:15px;width:100%;text-align:center;font-size:11px;">0</div></div>'; }
else{
if($windw3[$i] < $highwindlimit ){$wxdiric = fixwdir($dir3[$i]);
} else {$wxdiric = 'W-'.fixwdir($dir3[$i]);}
$wawstr = '<div style="position:relative;height:41px;width:100%;">
<span style="display:inline-block;" class="wxallwind wxall-'.$wxdiric.'"></span>
<div style="position:absolute;top:14px;width:100%;text-align:center;font-size:11px;">'.round($windw3[$i]).'</div></div>';
}
}

if($tsact[$i] == true){$tsproc='<br/>* '.$thunderdesc.' '.$wxallcity;}else{$tsproc="";}
# tooltips
$condtip3[] = '<b>'.$condtxt.'</b><br/>'.$dayline[$ra].' '.date('H:i',$timestrs3[$i]).'-'.date('H:i',$timestrs3[$i+1]).$tsproc;
$icontip3[]  = '<b>'.$condtxt.'</b><br/>'.$dayline[$ra].' '.date('H:i',$timestrs3[$i]).'-'.date('H:i',$timestrs3[$i+1]);
$temptip3[]  = '<b>'.$Temptxt.'</b><br/>'.$dayline[$ra].' '.date('H:i',$timestrs3[$i]).':<br/><b>'.$temp3[$i].$uoms[0].'</b>';
$windtip3[]  = '<b>'.$Wind.'</b><br/>'.$dayline[$ra].' '.date('H:i',$timestrs3[$i]).':<br/><b>'.fixwdir($dir3[$i]).' '.round($windw3[$i]).' '.$uoms[2].'</b><br/>'.fixgust2(round($windw3[$i]),$windgst3[$i],$uoms[2]);
$prectip3[]  = '<b>'.$Prectxt.'</b><br/>'.$dayline[$ra].' '.date('H:i',$timestrs3[$i]).'-'.date('H:i',$timestrs3[$i+1]).':<br/><b>'.showprecipt2($rtot3[$i],$uoms[1],$noprectxt);
$barotip3[]  = '<b>'.$Barotxt.'</b><br/>'.$dayline[$ra].' '.date('H:i',$timestrs3[$i]).':<br/><b>'.$baro3[$i].' '.$uoms[3].'</b>';
$uvtip3[]  = '<b>'.get_lang("UV Index").'</b><br/>'.$dayline[$ra].' '.date('H:i',$timestrs3[$i]).':<br/>'.uv_word(round($uv3[$i]));
if(max($snow3) > 0) { 
$snowtip3[]  = '<b>'.$Snowdtxt.'</b><br/>'.$dayline[$ra].' '.date('H:i',$timestrs3[$i]).':<br/><b>'.$snow3[$i].' '.$uoms[4].'</b>';
}
# row
$hourtable3.='
<tr style="vertical-align:top;text-align:center;" class="'.$row.'">
<td style="width:9%;vertical-align:middle;text-align:left;color:#2779aa;padding-left:6px;"><b>'.date('H:i',$timestrs3[$i]).'</b></td>
<td style="width:9%;padding:2px;vertical-align:middle;" class="tooltip" title="'.$icontip3[$i].'"><span style="display:inline-block;" class="cond45 cond45-'.str_replace(".png","",$wicon3[$i]).'"></span></td>
<td style="width:9%;vertical-align:middle;" class="tooltip" title="'.$temptip3[$i].'">'.fixtempcolor($temp3[$i]).$chiline3[$i].'</td>
<td style="width:9%;vertical-align:middle;" class="tooltip" title="'.$windtip3[$i].'">'.$wawstr.'</td>
<td style="width:9%;vertical-align:middle;" class="tooltip" title="'.$prectip3[$i].'">'.showprecipt($rtot3[$i],$uoms[1],$washowzeroprecip,$noprectxt).'</td>'.fixsnow(max($snow3),$snow3[$i],$uoms[4],$snowtip3[$i]).'
<td style="width:9%;vertical-align:middle;" class="tooltip" title="'.$barotip3[$i].'">'.$baro3[$i].' '.$uoms[3].'</td>
<td style="width:9%;vertical-align:middle;" class="tooltip" title="'.$uvtip3[$i].'">'.fixuvimg($uv3[$i],$dn3[$i],$minuvtoshow).'</td>
<td style="vertical-align:middle;text-align:left;" class="tooltip" title="'.$condtip3[$i].'">'.$conds3[$i].'<br/></td>
</tr>';
} # EOF loop
$hourtable3.='<tr><td colspan="9" class="wxsiminfo-box">';
$hourtable3.= get_lang("Move the mouse over the values for info.").'</td></tr></table>
';

##############################################################
# Create Soil & Grass-table

if($sgactive == true){
$r = 0;
$grasstable = '<table class="wxsimtbl" style="width:100%;">
<tr class="wxsimtbl_header">
<td>&nbsp;</td>
<td style="padding:6px;text-align: center"><b>'.$Temptxt.'</b></td>
<td style="padding:6px;text-align: center"><b>'.$Prectxt.'</b></td>
<td style="padding:6px;text-align: center"><b>'.$grasstxt.'</b></td>
<td style="padding:6px;text-align: center"><b>'.$surftxt.'</b></td>
<td style="padding:6px;text-align: center"><b>'.$soiltxt.' '.$sgdepth1.'</b></td>
<td style="padding:6px;text-align: center"><b>'.$soiltxt.' '.$sgdepth2.'</b></td>
</tr>';

$tot = count($temp3);
for($i=0;$i<$tot-1;$i++) {

if($i==0 || in_array($timestrs3[$i],$daychanges)) {
$hr = date('H',$timestrs3[$i]);
if(($hr > 21 || $hr < 4) && $i == 0) {$r++;}
$daylength = sec2hms($sunsetsstr[$r]-$sunrisesstr[$r]);
$suntip = get_lang("Daylength").': '.$daylength;
$grasstable .='<tr class="wxsimdateline"><td colspan="7" class="wxsimdateline-td"><span style="float:right;position:relative;top:-3px;font-size:10px;"><span style="display:inline-block;position:relative;top:4px;" class="misc sun_up"></span>&nbsp;'.$sunrises[$r].'&nbsp;&nbsp;&nbsp;
<span style="display:inline-block;position:relative;top:4px;" class="misc sun_down"></span>&nbsp;'.$sunsets[$r].'
&nbsp;&nbsp;&nbsp;'.$suntip.'</span>
<span style="position:relative;top:2px"><b>'.$dateline[$r].'</b></span></td></tr>';
$ra = $r;
$r++;
}

if($i%2==0) {$row = 'wxsimdata'; }else{$row = 'wxsimdataodd';}
if(round($temp3[$i])<0.1){$bt = "#499AC7";}else{$bt = "#F33A27";}
if(round($grast3[$i])<0.1){$bt1 = "#499AC7";}else{$bt1 = "#F33A27";}
if(round($surft3[$i])<0.1){$bt2 = "#499AC7";}else{$bt2 = "#F33A27";}
if(round($soil1t3[$i])<0.1){$bt3 = "#499AC7";}else{$bt3 = "#F33A27";}
if(round($soil2t3[$i])<0.1){$bt4 = "#499AC7";}else{$bt4 = "#F33A27";}
// tooltips
$temptip = '<b>'.$Temptxt.'</b><br/>'.$dayline[$ra].' '.date('H:i',$timestrs3[$i]).':<br/><b>'.$temp3[$i].$uoms[0].'</b>';
$prectip = '<b>'.$Prectxt.'</b><br/>'.$dayline[$ra].' '.date('H:i',$timestrs3[$i]).'-'.date('H:i',$timestrs3[$i+1]).':<br/><b>'.showprecipt2($rtot3[$i],$uoms[1],$noprectxt);
$grastip = '<b>'.$grasstxt.'</b><br/>'.$dayline[$ra].' '.date('H:i',$timestrs3[$i]).':<br/><b>'.$grast3[$i].$uoms[0].'</b>';
$surftip = '<b>'.$surftxt.'</b><br/>'.$dayline[$ra].' '.date('H:i',$timestrs3[$i]).':<br/><b>'.$surft3[$i].$uoms[0].'</b>';
$soil1tip = '<b>'.$soiltxt.' '.$sgdepth1.'</b><br/>'.$dayline[$ra].' '.date('H:i',$timestrs3[$i]).':<br/><b>'.$soil1t3[$i].$uoms[0].'</b><br/>'.$moisttxt.' <b>'.round($soil1m3[$i]).'%</b>';
$soil2tip = '<b>'.$soiltxt.' '.$sgdepth2.'</b><br/>'.$dayline[$ra].' '.date('H:i',$timestrs3[$i]).':<br/><b>'.$soil2t3[$i].$uoms[0].'</b>';
// row
$grasstable.='
<tr style="vertical-align:top;text-align:center;" class="'.$row.'">
<td style="width:9%;padding: 6px;vertical-align:middle;text-align:left;color:#2779aa;"><b>'.date('H:i',$timestrs3[$i]).'</b></td>
<td style="width:13%;padding:6px;vertical-align:middle;" class="tooltip" title="'.$temptip.'">'.fixtempcolor($temp3[$i]).'</td>
<td style="width:13%;padding:6px;vertical-align:middle;" class="tooltip" title="'.$prectip.'">'.showprecipt($rtot3[$i],$uoms[1],$washowzeroprecip,$noprectxt).'</td>
<td style="width:13%;padding:6px;vertical-align:middle;" class="tooltip" title="'.$grastip.'">'.fixtempcolor($grast3[$i]).'</td>
<td style="width:13%;padding:6px;vertical-align:middle;" class="tooltip" title="'.$surftip.'">'.fixtempcolor($surft3[$i]).'</td>
<td style="width:13%;padding:6px;vertical-align:middle;" class="tooltip" title="'.$soil1tip.'">'.fixtempcolor($soil1t3[$i]).'<br/><span><small>'.$moisttxt.':<b><span style="color:#3399CC;">'.round($soil1m3[$i]).'%</span></b></small></span></td>
<td style="width:13%;padding:6px;vertical-align:middle;" class="tooltip" title="'.$soil2tip.'">'.fixtempcolor($soil2t3[$i]).'<br/><span><small>'.$moisttxt.':<b><span style="color:#3399CC;">'.round($soil2m3[$i]).'%</span></b></small></span></td>
</tr>';
} # Loop
} # IF soil TRUE
$grasstable .='</table>
';

# EOF Tables
##################################################################
# Graph

if(max($precsgraph) < $defra) {$mrainlimit = $defra;} else { $mrainlimit = round(max($precsgraph)*1.1); }
if(max($wgstgraphs) < $defwind) {$windlimit = $defwind;} else { $windlimit = round(max($wgstgraphs)*1.1); }
if(max($snowgraph)< $defsno) {$snowlimit = $defsno;} else { $snowlimit = round(max($snowgraph)*1.1); }
if(max($uvgraph) < 5) {$uvlimit = 5;} else { $uvlimit = max($uvgraph)+1; }
if(max($solargraph) < 500) {$solarlimit = 500;} else { $solarlimit = round(max($solargraph)*1.1); }

// UV-colors
$uvarr = array('170,246,130','190,241,88','220,236,46','244,230,11','244,189,11','244,149,11','244,109,11','244,68,11','220,39,8','186,19,15','165,6,30','165,6,30','165,6,30');
$uvmax = ceil($uvlimit);
$uvchunk = round(1/$uvmax,2);
for($t=1;$t<$uvmax+1;$t++) {
$stp = ($t-1)*$uvchunk;
$uvcl .= '"rgb('.$uvarr[($t)].')",';
}

for($i=0;$i<count($jsdaychanges);$i++) {
if($i == count($jsdaychanges)-1) {
$ddays.= '{xaxis: { from: '.$jsdaychanges[$i].', to: '.($stopt*1000).' }, color: "rgba(255, 255, 255, 0.3)" },';
} else {
$ddays.= '{xaxis: { from: '.$jsdaychanges[$i].', to: '.$jsdaychanges[$i+1].' }, color: "rgba(255, 255, 255, 0.3)" },';
}
$i++;
}

########################################################
# Put together the graph

$graph='
<script>
<!--
var wxsimdata = {
tempdata: ['.substr($tempjson, 0, -1).'],
precdata: ['.substr($totprecipjson, 0, -1).'],
dewdata: ['.substr($dewjson, 0, -1).'],
chilldata: ['.substr($chilljson, 0, -1).'],
heatdata: ['.substr($heatjson, 0, -1).'],
winddata: ['.substr($windjson, 0, -1).'],
windgustdata: ['.substr($windgstjson, 0, -1).'],
barodata: ['.substr($barojson, 0, -1).'],
uvdata: ['.substr($uvijson, 0, -1).'],
soladata: ['.substr($solajson, 0, -1).'],
snowddata: ['.substr($snowdjson, 0, -1).'],
kidata: ['.substr($kijson, 0, -1).'],
tmaxdata: ['.substr($tmaxjson, 0, -1).'],
tmindata: ['.substr($tminjson, 0, -1).']
}
var wxsimconf = {
days: ['.$dayarray.']
}

var grids = {tickColor: "#ddd", color:"#424242",  borderWidth: 1, borderColor: "#88BCCE",
        backgroundColor: { colors: ["#ddd", "#f2f2f2"] },hoverable: true, autoHighlight: true,
        markings: ['.substr($ddays, 0, -1).'],mouseActiveRadius: 10};
var legends = {container: "#legenddiv", noColumns: 4};

var dataset1 =[{
        data:wxsimdata.barodata, lines: { show: true, fill: false, lineWidth: 1.5 },legend: {show: true}, 
        color: "#9ACD32",shadowSize: 0,yaxis:3,xaxis:2,label:"'.$Barotxt.'"
        },{
        data:wxsimdata.tempdata, lines: { show: true, fill: false, lineWidth: 1.5 },legend: {show: true}, 
        color: "#EE3B3B",threshold: { below: '.$frzz.', color: "#6495ED" },shadowSize: 0,label:"'.$Temptxt .'"
        },{
        data:wxsimdata.precdata, lines: { show: false,fill: true}, points:{ show: false },legend: {show: true}, 
        bars: { show: true, fill: true, align: "right",barWidth: 7500000,lineWidth:0 },
        color: "#4572A7",yaxis:2,label:"'.$Prectxt.'"
        }];
var setting1 = {
        yaxes: [{minTickSize:1,tickDecimals:0,autoscaleMargin: 0.15,labelWidth: 14,reserveSpace: true},
        {min:0,max:'.$mrainlimit.',alignTicksWithAxis: 1,labelWidth: 14,reserveSpace: true},
        {position:"right",min:'.$graphbaromin.',max:'.$graphbaromax.',alignTicksWithAxis:1,tickLength:0,labelWidth:20,reserveSpace:true}], 
        xaxes: [{ mode: "time",timeformat: "%H",  tickSize: [6, "hour"] },
        { mode: "time",timeformat: "%H:%M",  tickSize: [1, "day"],tickFormatter: ownlabels,position:"top",tickLength: 0}],
        grid: grids,legend: legends};
        
var dataset2 = [{
        data:wxsimdata.barodata, lines: { show: true,opacity: 0.25, fill: false, lineWidth: 1.5 },legend: {show: true}, 
        color: "#9ACD32",shadowSize: 0,yaxis:3,xaxis:2,label:"'.$Barotxt.'"
        },{
        data:wxsimdata.kidata, lines: { show: true, fill: true, lineWidth: 1.5},legend: {show: true}, 
        color: "#EE7621",shadowSize: 0,label:"'.$Kitxt.'"
        },{
        data:wxsimdata.dewdata, lines: { show: true,fill: false,lineWidth: 1.5}, points:{ show: false },legend: {show: true}, 
        color: "#6CA6CD",shadowSize: 0,yaxis:2,label:"'.$dewptxt.'"
        }];
var setting2 = {yaxes: [{min:0,max:100,minTickSize:1,tickDecimals:0,autoscaleMargin: 0.15,labelWidth: 14,reserveSpace: true},
        {alignTicksWithAxis: 1,labelWidth: 14,reserveSpace: true},
        {position:"right",min:'.$graphbaromin.',max:'.$graphbaromax.',alignTicksWithAxis:1,tickLength:0,labelWidth:20,reserveSpace:true}], 
        xaxes: [{ mode: "time",timeformat: "%H",  tickSize: [6, "hour"] },
        { mode: "time",timeformat: "%H:%M",  tickSize: [1, "day"],tickFormatter: ownlabels,position:"top",tickLength: 0}],
        grid: grids,legend: legends};
        
var dataset3 = [{
        data:wxsimdata.soladata, lines: { show: true, fill: true,opacity:0.15, lineWidth: 1.5 },legend: {show: true}, 
        color: "#EEEE00",shadowSize: 0,yaxis:2,xaxis:2,label:"'.$Solartxt.'"
        },{
        data:wxsimdata.uvdata, lines: { show: true, fill: true, lineWidth: 1.5, fillColor: { colors: ['.substr($uvcl, 0, -1).'] }},
        legend: {show: true}, color: "#FFB90F",shadowSize: 0,label:"UV Index"
        }];
var setting3 = {yaxes: [{min:0,max:'.$uvlimit.',minTickSize:1,tickDecimals:0,autoscaleMargin: 0.15,labelWidth: 45,reserveSpace: true},
        {position:"right",min:0,max:'.$solarlimit.',alignTicksWithAxis:1,tickLength:0,labelWidth:20,reserveSpace:true}], 
        xaxes: [{ mode: "time",timeformat: "%H",  tickSize: [6, "hour"] },
        { mode: "time",timeformat: "%H:%M",  tickSize: [1, "day"],tickFormatter: ownlabels,position:"top",tickLength: 0}],
        grid: grids,legend: legends};
        
var dataset4 = [{
        data:wxsimdata.winddata, points: { show: true, radius: 1,type: "circle" },legend: {show: true}, 
        color: "#006400",shadowSize: 0,yaxis:2,xaxis:2,label:"'.$winddtxt.'"
        },{
        data:wxsimdata.windgustdata, points: { show: true, radius: 1.5,type: "circle" },
        legend: {show: true}, color: "#CD3333",shadowSize: 0,label:"'.$windgstdtxt.'"
        }];
var setting4 = {diricon: {show: true,disablePoints: true,flwdirpath:"http://static.nordicweather.net/nordic/images/barbs2/"},
        yaxes: [{min:0,max:'.$windlimit.',minTickSize:1,tickDecimals:0,autoscaleMargin: 0.15,labelWidth: 45,reserveSpace: true},
        {position:"right",min:0,max:'.$windlimit.',tickDecimals:0,alignTicksWithAxis:1,tickLength:0,labelWidth:20,reserveSpace:true}], 
        xaxes: [{ mode: "time",timeformat: "%H",  tickSize: [6, "hour"] },
        { mode: "time",timeformat: "%H:%M",  tickSize: [1, "day"],tickFormatter: ownlabels,position:"top",tickLength: 0}],
        grid: grids,legend: legends};
        
var dataset5 = [{
        data:wxsimdata.snowddata, lines: { show: true, fill: false, lineWidth: 0 },legend: {show: false}, 
        color: "#FF6D6D",yaxis:2,shadowSize: 0
        },{
        data:wxsimdata.snowddata, lines: { show: true,fill: true, lineWidth: 1.5}, points:{ show: false },legend: {show: true}, 
        color: "#87CEFA",shadowSize: 0,xaxis:2,label:"'.$Snowdtxt.'"
        }];
var setting5 = {yaxes: [{alignTicksWithAxis: 1,labelWidth: 45,reserveSpace: true},
        {position:"right",alignTicksWithAxis:1,tickLength:0,labelWidth:20,reserveSpace:true}], 
        xaxes: [{ mode: "time",timeformat: "%H",  tickSize: [6, "hour"] },
        { mode: "time",timeformat: "%H:%M",  tickSize: [1, "day"],tickFormatter: ownlabels,position:"top",tickLength: 0}],
        grid: grids,legend: legends};
        
var dataset6 = [{
        data:wxsimdata.tempdata, lines: { show: true, fill: false, lineWidth: 1.5 },legend: {show: true}, 
        color: "#EE3B3B",threshold: { below: '.$frzz.', color: "#6495ED" },shadowSize: 0,label:"'.$Temptxt .'"
        },{
        data:wxsimdata.chilldata, lines: { show: true, fill: false, lineWidth: 0.9 },legend: {show: true}, 
        color: "#104E8B",shadowSize: 0,xaxis:2,label:"'.$wchilltxt.'"
        },{
        data:wxsimdata.precdata, lines: { show: false,fill: true}, points:{ show: false },legend: {show: true}, 
        bars: { show: true, fill: true, align: "right",barWidth: 7500000,lineWidth:0 },
        color: "#4572A7",yaxis:2,label:"'.$Prectxt.'"
        }];
var setting6 = {yaxes: [{minTickSize:1,tickDecimals:0,autoscaleMargin: 0.15,labelWidth: 14,reserveSpace: true},
        {min:0,max:'.$mrainlimit.',alignTicksWithAxis: 1,labelWidth: 14,reserveSpace: true},
        {position:"right",min:'.$graphbaromin.',max:'.$graphbaromax.',alignTicksWithAxis:1,tickLength:0,labelWidth:20,reserveSpace:true}], 
        xaxes: [{ mode: "time",timeformat: "%H",  tickSize: [6, "hour"] },
        { mode: "time",timeformat: "%H:%M",  tickSize: [1, "day"],tickFormatter: ownlabels,position:"top",tickLength: 0}],
        grid: grids,legend: legends};
        
var dataset7 = [{
        data:wxsimdata.tmaxdata, lines: { show: true, fill: false, lineWidth: 1.25 },legend: {show: true}, 
        color: "#EE3B3B",shadowSize: 0,label:"'.$maxtxt.' '.strtolower($Temptxt).'"
        },{
        data:wxsimdata.tmindata, lines: { show: true, fill: false, lineWidth: 1.25 },legend: {show: true}, 
        color: "#6495ED",shadowSize: 0,xaxis:2,label:"'.$mintxt.' '.strtolower($Temptxt).'"
        }];
var setting7 = {yaxes: [{alignTicksWithAxis: 1,labelWidth: 45,reserveSpace: true},
        {position:"right",alignTicksWithAxis:1,tickLength:0,labelWidth:20,reserveSpace:true}], 
        xaxes: [{ mode: "time",timeformat: "%H",  tickSize: [6, "hour"] },
        { mode: "time",timeformat: "%H:%M",  tickSize: [1, "day"],tickFormatter: ownlabels,position:"top",tickLength: 0}],
        grid: grids,legend: legends};

function ownlabels(v, axis, topoff) { 
    var dz= new Date();
    dz.setTime(v);
    var label = \'<div style="font-size:11px;width:100%;text-align:center;margin-left: 60px; position:relative; top: -30px;font-weight: normal;">\'+wxsimconf.days[dz.getUTCDay()]+\'</div>\';
    return label;
} 

function owntool(event, pos, item) {
        $("#x").text(pos.x.toFixed(2));
        $("#y").text(pos.y.toFixed(2));
        if (item) {
            var unit = \'\';           
            if (previousPoint != item.datapoint) {
                previousPoint = item.datapoint;        
                $("#tooltip").remove();
                var x = item.datapoint[0].toFixed(3);
                var y = item.datapoint[1].toFixed(1);
                var hy = new Date(x*1);
                var ho = hy.getUTCHours();
                var mi = hy.getUTCMinutes();
                if(item.series.label == "'.$Temptxt.'") { var unit = "'.$uoms[0].'"; }
                if(item.series.label == "'.$maxtxt.' '.strtolower($Temptxt).'") { var unit = "'.$uoms[0].'"; }
                if(item.series.label == "'.$mintxt.' '.strtolower($Temptxt).'") { var unit = "'.$uoms[0].'"; }
                if(item.series.label == "'.$wchilltxt.'") { var unit = "'.$uoms[0].'"; }
                if(item.series.label == null) { item.series.label = "'.$Temptxt.'";var unit = "'.$uoms[0].'"; }
                if(item.series.label == "'.$dewptxt.'") { var unit = "'.$uoms[0].'"; }
                if(item.series.label == "'.$Prectxt.'") { var unit = "'.$uoms[1].'"; }
                if(item.series.label == "'.$Solartxt.'") { var unit = " w/m&sup2;"; y = item.datapoint[1].toFixed(0);}
                if(item.series.label == "'.$Kitxt.'") { var unit = "%"; y = item.datapoint[1].toFixed(0);}
                if(item.series.label == "'.$Snowdtxt.'") { var unit = " '.$uoms[4].'"; y = item.datapoint[1].toFixed(0);}
                if(item.series.label == "'.$winddtxt.'") { var unit = " '.$uoms[2].'"; y = item.datapoint[1].toFixed(0);}
                if(item.series.label == "'.$windgstdtxt.'") { var unit = " '.$uoms[2].'"; y = item.datapoint[1].toFixed(0);}
                if(item.series.label == "'.$Barotxt.'") { var unit = " '.$uoms[3].'"; y = item.datapoint[1].toFixed(0);}                
                showTooltip(item.pageX, item.pageY,
                "<b>"+item.series.label+"</b><br/>"+wxsimconf.days[hy.getUTCDay()]+" "+ ho + ":00:<br/><b>"+y+unit+"</b>");
            }
        }else{
            $("#tooltip").remove();
            previousPoint = null;            
            }
}

function showTooltip(x, y, contents) {
        $(\'<div id="tooltip">\' + contents + \'</div>\').css({position: \'absolute\',display: \'none\',
        top: y + 12,left: x + 12,opacity: 0.90}).appendTo("body").fadeIn(200);
}
var previousPoint = null;

$(function () {
$("#placeholder").bind("plothover", owntool); 
 
        $.plot($("#placeholder"), dataset1,setting1);
        $("#temprainbtn").addClass("active");
        $("#wxsimuom1").html("'.$uoms[1].'");
        $("#wxsimuom2").html("'.$uoms[0].'").css({left:"3px"});
        $("#wxsimuom3").html("'.$uoms[3].'");

        $("#temprainbtn").click(function () {
        $.plot($("#placeholder"), dataset1,setting1);
        $("#wxsimuom1").html("'.$uoms[1].'");
        $("#wxsimuom2").html("'.$uoms[0].'").css({left:"3px"});
        $("#wxsimuom3").html("'.$uoms[3].'").css({left:"3px"});
        $("#temprainbtn").addClass("active");
        $("#solarbtn").removeClass("active");
        $("#windbtn").removeClass("active");
        $("#thunderbtn").removeClass("active");
        $("#snowbtn").removeClass("active"); 
        $("#wchillbtn").removeClass("active");
        $("#maxminbtn").removeClass("active");
        });
        
        $("#wchillbtn").click(function () {
        $.plot($("#placeholder"), dataset6,setting6);
        $("#wxsimuom1").html("'.$uoms[1].'");
        $("#wxsimuom2").html("'.$uoms[0].'").css({left:"3px"});
        $("#wxsimuom3").html("");
        $("#wchillbtn").addClass("active");
        $("#temprainbtn").removeClass("active");
        $("#solarbtn").removeClass("active");
        $("#windbtn").removeClass("active");
        $("#thunderbtn").removeClass("active");
        $("#snowbtn").removeClass("active");
        $("#maxminbtn").removeClass("active");
        });
        
        $("#solarbtn").click(function () {
        $.plot($("#placeholder"), dataset3,setting3);
        $("#wxsimuom1").html("");
        $("#wxsimuom2").html("UV").css({left:"25px"});
        $("#wxsimuom3").html("w/m&sup2;").css({left:"3px"});
        $("#solarbtn").addClass("active");
        $("#temprainbtn").removeClass("active");
        $("#windbtn").removeClass("active");
        $("#thunderbtn").removeClass("active");
        $("#snowbtn").removeClass("active"); 
        $("#wchillbtn").removeClass("active");
        $("#maxminbtn").removeClass("active");
        });

        $("#windbtn").click(function () {
        $.plot($("#placeholder"), dataset4,setting4);
        $("#wxsimuom1").html("");
        $("#wxsimuom2").html("'.$uoms[2].'").css({left:"25px"});
        $("#wxsimuom3").html("'.$uoms[2].'").css({left:"0px"});
        $("#windbtn").addClass("active");
        $("#temprainbtn").removeClass("active");
        $("#solarbtn").removeClass("active");
        $("#windbtn").removeClass("active");
        $("#thunderbtn").removeClass("active");
        $("#snowbtn").removeClass("active");
        $("#wchillbtn").removeClass("active");
        $("#maxminbtn").removeClass("active");
        });
        
        $("#thunderbtn").click(function () {
        $.plot($("#placeholder"), dataset2,setting2);
        $("#wxsimuom1").html("'.$uoms[0].'");
        $("#wxsimuom2").html("%").css({left:"9px"});
        $("#wxsimuom3").html("'.$uoms[3].'").css({left:"3px"});
        $("#thunderbtn").addClass("active");
        $("#solarbtn").removeClass("active");
        $("#temprainbtn").removeClass("active");
        $("#windbtn").removeClass("active");
        $("#snowbtn").removeClass("active");
        $("#wchillbtn").removeClass("active");
        $("#maxminbtn").removeClass("active");
        });
        
        $("#snowbtn").click(function () {
        $.plot($("#placeholder"), dataset5,setting5);
        $("#wxsimuom1").html("");
        $("#wxsimuom2").html("'.$uoms[4].'").css({left:"25px"});
        $("#wxsimuom3").html("'.$uoms[4].'").css({left:"-5px"});
        $("#snowbtn").addClass("active");
        $("#solarbtn").removeClass("active");
        $("#temprainbtn").removeClass("active");
        $("#windbtn").removeClass("active");
        $("#thunderbtn").removeClass("active");
        $("#wchillbtn").removeClass("active");
        $("#maxminbtn").removeClass("active");
        });
        
        $("#maxminbtn").click(function () {
        $.plot($("#placeholder"), dataset7,setting7);
        $("#wxsimuom1").html("");
        $("#wxsimuom2").html("'.$uoms[0].'").css({left:"25px"});
        $("#wxsimuom3").html("");
        $("#maxminbtn").addClass("active");
        $("#solarbtn").removeClass("active");
        $("#temprainbtn").removeClass("active");
        $("#windbtn").removeClass("active");
        $("#thunderbtn").removeClass("active");
        $("#wchillbtn").removeClass("active");
        $("#snowbtn").removeClass("active");
        });
});
//-->
</script>
<div style="padding-top:15px;margin-bottom:20px;position:relative;" class="flotwrap" >
<table style="width: '.($mainwidth-90).'px;margin:0 0 0 '.$iconlineleft.'px;position:absolute;top:'.$iconlinetop.'px; left: 0px;"><tr>';
$many = count($condics);
$piece = $iconlinewidth;
$piece/= $many;
for($i=0;$i<$many;$i++) {
$graph .='<td style="width: '.$piece.'px;text-align:center;margin:0;padding:0;"><span style="display:inline-block;" class="cond30 cond30-'.str_replace(".png","",$condics[$i]).'"></span></td>
';
}
$graph.='</tr></table>
<br/>
<div id="wxsimuom3" style="float:right;position:relative;top:15px;left:3px;font-size:10px;padding:0 5px 8px 0;color:#424242;"></div>
<div style="position:relative;left:-3px;top:15px;font-size:10px;padding:0 0 8px 5px;color:#424242;"><span id="wxsimuom1"></span>
<span id="wxsimuom2" style="position:relative;left:5px;font-size:10px;padding:0 0 8px 5px;color:#424242;"></span></div>
<div id="placeholder" style="width:'.($mainwidth-20).'px;height:250px;margin:0 auto;"></div>
<div style="position:relative;height:30px;width:350px;top:'.$legendtop.'px;" id="legenddiv"></div>
<div id="meteogrambuttons"  style="width:'.($mainwidth-20).'px;">
<span style="margin:3px;" class="wxall-button" id="temprainbtn">'.$Temptxt.'/'.$Prectxt.'/'.$Barotxt.'</span>';
if(max($snow1)>0) {$graph.='<span style="margin:3px;" class="wxall-button" id="snowbtn">'.$Snowdtxt.'</span>';}
$graph.='<span style="margin:3px;" class="wxall-button" id="thunderbtn">'.$thundertxt.'/'.$dewptxt.'/'.$Barotxt.'</span>
<span style="margin:3px;" class="wxall-button" id="solarbtn">UV/'.$Solartxt.'</span>
<span style="margin:3px;" class="wxall-button" id="windbtn">'.$winddtxt.'</span>
<span style="margin:3px;" class="wxall-button" id="wchillbtn">'.$wchilltxt.'</span>
<span class="wxall-button" style="margin:3px;" id="maxminbtn">'.$maxtxt.'/'.strtolower($mintxt).' '.strtolower($Temptxt).'</span>
</div>
</div>';

##############################################################
# Put together the output-variables
$wxallmain='
<div id="wxsimtabs" style="width:'.$mainwidth.'px;">
<ul>
<li><a href="#fragment-1"><span>'.get_lang("Overview").'</span></a></li>
<li><a href="#fragment-2"><span>'.$hourbyhourtxt.'</span></a></li>
<li><a href="#fragment-3"><span>'.get_lang("Detailed").' 48 '.get_lang("hrs").'</span></a></li>';
if($showgraph == true){
$wxallmain.='<li><a id="meteogram" href="#fragment-5"><span>'.$meteogramtxt.'</span></a></li>';
}
if($sgactive == true) {
$wxallmain.='<li><a href="#fragment-4"><span>'.$grassoiltxt.'</span></a></li>';
}
$wxallmain.='</ul>
<div id="fragment-1" style="padding:0">'.$shorttable.'</div>
<div id="fragment-2" style="padding:0">'.$hourtable3.'</div>
<div id="fragment-3" style="padding:0">'.$hourtable1.'</div>';
if($showgraph == true){
$wxallmain.='<div id="fragment-5" style="padding:0">'.$graph.'</div>';
}
if($sgactive == true) {
$wxallmain.='<div id="fragment-4" style="padding:0">'.$grasstable.'</div>';
}
$wxallmain.='</div>'.$discl.'
<script>
<!--
$(function () {
$("#wxsimtabs").tabs({\'selected\': 0,
select: function(event, ui) {
if(ui.panel.id=="fragment-5"){
setTimeout(function() {
$.plot($("#placeholder"), dataset1,setting1);
$("#temprainbtn").addClass("active");
$("#wxsimuom1").html("'.$uoms[1].'");
$("#wxsimuom2").html("'.$uoms[0].'").css({left:"-2px"});
$("#wxsimuom3").html("'.$uoms[3].'");
},200);
}
}
});
});
//-->
</script>
';

$wxallgraph=$graph;

$wxallhead = '
<link rel="stylesheet" type="text/css" media="screen" href="'.$wxallmainfolder.'css/wxall.css" />
<script type="text/javascript">
if(!!!document.createElement(\'canvas\').getContext) {
document.write(\'<script type="text/javascript" src="'.$wxallmainfolder.'js/excanvas.min.js"></scr\' + \'ipt>\');
}			 
</script>
<script type="text/javascript" src="'.$wxallmainfolder.'js/jquery.1.5.min.js"></script>
<script type="text/javascript" src="'.$wxallmainfolder.'js/jquery.tooltip.js"></script>
<script type="text/javascript" src="'.$wxallmainfolder.'js/jquery.flot.js"></script>
<script type="text/javascript" src="'.$wxallmainfolder.'js/jquery.ui.min.js"></script>
<script>
<!--
$(document).ready(function(){
tooltip();
});
//-->
</script>
';
?>