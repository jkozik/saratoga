<?php
# 3in1 wxall.php V.4.15

// Debug-settings 
$snowdebug = false; // Hourtable

$csv = file($lastretfile);
$howmany = count($csv);
ini_set('display_errors', '0');

$csv = preg_replace( '/\s+/', ' ', $csv ); // remove white space
$csv = preg_replace( '/\,/', '.', $csv ); // replace , with . for decimal spaces

$hour3 = array('0000','0300','0600','0900','1200','1500','1800','2100');
$hour8 = array('0000','0600','1200','1800');
$months = array('','Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
$startt = '';
$prectot = 0;

# Unit-specific limits & traceamount
// rain
if (preg_match("|mm|",$uoms[1])) {$traceam3 = 0.3;$traceam1 = 0.2;$defra = 10;} 
else {$traceam3 = 0.009;$traceam1 = 0.007;$defra = 0.5;$useusprec=true;} 
// temp
if (preg_match("|C|",$uoms[0])) {$tempuom="C";$frzpoint = 0;}
else {$tempuom="F";$frzpoint = 32;$useustemp=true;} 
// wind
if (preg_match("|m/s|",$uoms[2])) {$defwind = 10;} 
else if (preg_match("|mph|",$uoms[2])) {$defwind = 20;} 
else if (preg_match("|km|",$uoms[2])) {$defwind = 35;}
else {$defwind = 20;}
// snow
if (preg_match("|cm|",$uoms[1])) {$defsno = 10;} 
else {$defsno = 0.5;$useussnow=true;} 

// Translate days for Highcharts
$days = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
$says = array('Sun','Mon','Tue','Wed','Thu','Fri','Sat');
for($i=0;$i<count($days);$i++) {
$transday = get_lang($days[$i]);
$dayarray.="'".$transday."',";
$dayarrayb.="'".substr($says[$i],0,3)."': '".$transday."',";
$daarray[]=$transday;
}
$dayarray = substr($dayarray, 0, -1);
$dayarrayb = substr($dayarrayb, 0, -1);

#######################################
## Figure out where data are located ##
$snowava = false;
$sgactive = false;
$tmaxtive = false;
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
if($namedata[$r] == "SKY"){$covvwp = $r;}
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
if($startt == '') { $startt = $tstr;$jstart = (($tstr+$utcdiff)*1000);}
$jstop = (($tstr+$utcdiff)*1000);
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

# COLLECT FOR DETECTING MAX/MIN
$graphtemps[] = $revdata[$tempwp];
$graphbaros[] = $revdata[$barowp];
$graphtemps[] = $revdata[$chillwp];
$graphdews[] = $revdata[$dewwp];
$solargraph[] = $revdata[$solarwp];
$uvgraph[] = $revdata[$uviwp];
if($snowava <> true){$revdata[$snowwp] = 0;}
$snowgraph[] = $revdata[$snowwp];
if(in_array(date('Hi',$tstr),$hour3)) {
$wgstgraphs[] = max($wgstsgraph);
$mxgst = max($wgstsgraph);
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

if(substr($stuff,3,2)=='00' && $r1<=48) {
$r1++;
$timestrs1[] = $tstr;
$temp1[]= $revdata[$tempwp];
$dew1[]= $revdata[$dewwp];
$chill1[]= $revdata[$chillwp];
$heat1[]= $revdata[$heatwp];
$dirs1[]= $revdata[$dirwp];
$windw1[]= $revdata[$windwp];
$baro1[]= $revdata[$barowp];
if($useussnow==1){$snow1[]= $revdata[$snowwp];}
else{$snow1[]= round($revdata[$snowwp]);}
$windgst1[$r1-2] = max($wgsts1);
$dn1[$r1-2] = $dn;
$uv1[$r1-2] = max($uvs1);

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
if($useusprec==1){$rtot1[$r1-2]= sprintf("%01.2f", $newtot1);}
else{$rtot1[$r1-2]= sprintf("%01.1f", $newtot1);}
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
$raitime[] = $jst;
$raitime2[] = $jst;
$ratime = "-";
$ravol = "-";
if(in_array(date('Hi',$tstr),$hour3)) {
$r3++;
$timestrs3[] = $tstr;
$temp3[]= $revdata[$tempwp];
$dew3[]= $revdata[$dewwp];
$dew3[]= $revdata[$dewwp];
$chill3[]= $revdata[$chillwp];
$heat3[]= $revdata[$heatwp];
$dir3[]= $revdata[$dirwp];
$windw3[]= $revdata[$windwp];
$baro3[]= $revdata[$barowp];

if($snowava == true){
if($useussnow==1){$snow3[]= $revdata[$snowwp];}
else{$snow3[]= round($revdata[$snowwp]);}
}
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
if($useusprec==1){$rtot3[$r3-2]= sprintf("%01.2f", $newtot3);}
else{$rtot3[$r3-2]= sprintf("%01.1f", $newtot3);}
$jsstr = $jst;
if($newtot3>0&&is_numeric($rawjsstr)){
for($t=0;$t<count($raitime);$t++){
if(count($raitime)/2==$t){
$ratime = $raitime[$t];
$ravol = $newtot3;
$graphprecs[] = $newtot3;
}
$tooltipprec.=$raitime[$t].':'.$newtot3.',';
}
$precsgraph[] = $newtot3;
}else{
for($t=0;$t<count($raitime);$t++){
if(count($raitime)/2==$t){
$ratime = $raitime[$t];
$ravol = $newtot3;
}
}
}
$conv = 0;
if(count($convs3)>0){
$conv3 = max($convs3);
}
# check some conditions
$fogw3 = fogcond($rawcond3); // fog
$raintyp = raintyp($rawtemp3,$rawdew3,$rawlevel3,$tempuom);
$raintypdebug = '<br>Test: Tem: '.$rawtemp3.', Dew: '.$rawdew3.', LWL: '.$rawlevel3.' = '.$raintyp;
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
$totcond3 = $txtcond3.$prcond3.$fogw3.' '.$frostline3.$visibline3.$tmcond;

reset ($Language);
foreach ($Language as $key => $replacement) {
$totcond3 = str_replace($key,$replacement,$totcond3);
}
// Add debuglines
if($snowdebug == true){$totcond3.=$raintypdebug;}
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
$raitime = array();
$raitime2 = array();
$tmax3 = array();
$tmin3 = array();
$rawcond3 = '';
} // EOF 3 hour

# 3 hour data done
##########################################################
# Every 8 hour-stuff for the graphs

$condic = "-";
$condictime = "-";
if(in_array(date('Hi',$tstr),$hour8)) {
# Graphicons
$icons = explode('|',$iconcond3);
if($dn <> "day") {
$condics[] = $icons[1];
$condic = $icons[1];
} else {
$condics[] = $icons[0];
$condic = $icons[0];
}
$condictime = $jst;
} # EOF 8 hr

if($useussnow==1){$snod=$revdata[$snowwp];}
else{$snod=round($revdata[$snowwp]);}

# CREATE DATAFILE FOR GRAPH
$graphlog.=$jst.','.$revdata[$tempwp].','.$revdata[$tmaxwp].','.$revdata[$tminwp].','.$revdata[$dewwp].','.$revdata[$chillwp].','.$revdata[$heatwp].','.$revdata[$barowp].','.$revdata[$uviwp].','.$revdata[$solarwp].','.$revdata[$windwp].','.$mxgst.','.fixwdir($revdata[$dirwp]).','.$snod.','.thunderproc3($revdata[$kiwp]).','.$ravol.','.$ratime.','.$condic.','.$condictime."\n";

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
$tmax3[] = $revdata[$tmaxwp];
$tmin3[] = $revdata[$tminwp];
$rawcond1.= $revdata[$condwp4].$revdata[$condwp3].$revdata[$condwp2].$revdata[$condwp1];
$rawcond3.= $revdata[$condwp4].$revdata[$condwp3].$revdata[$condwp2].$revdata[$condwp1];

}  # IF numeric
}  # EOF Loop

file_put_contents($wxallmainfolderfull.'graphlog.txt',$graphlog);
chmod($wxallmainfolderfull.'graphlog.txt',0666);

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
//if(($hr > 21 || $hr < 4) && $i == 0) {$r++;}
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
$windtip1[] = '<b>'.$Wind.'</b><br/>'.$dayline[$ra].' '.date('H:i',$timestrs1[$i]).':<br/><b>'.fixwdir($dirs1[$i]).' '.round($windw1[$i]).' '.$uoms[2].'</b><br/>'.fixgust2(round($windw1[$i]),$windgst1[$i],$uoms[2]);
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
<td style="width:9%;vertical-align:middle;" class="tooltip" title="'.$temptip1[$i].'">'.fixtempcolor($temp1[$i]).''.$chiline1[$i].'</td>
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
<td style="width:9%;vertical-align:middle;" class="tooltip" title="'.$temptip3[$i].'">'.fixtempcolor($temp3[$i]).''.$chiline3[$i].'</td>
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
$ddays.= '{ from: '.$jsdaychanges[$i].', to: '.($stopt*1000).', color: "rgba(255, 255, 255, 0.3)" },';
} else {
$ddays.= '{ from: '.$jsdaychanges[$i].', to: '.$jsdaychanges[$i+1].', color: "rgba(255, 255, 255, 0.3)" },';
}
$i++;
}

# DETECT SOME SETTINGS FOR THE GRAPH
$gmaxtemp = ceil(max($graphtemps));
$gmintemp = floor(min($graphtemps));
$gmaxdew = ceil(max($graphdews));
$gmindew = floor(min($graphdews));
$gmaxprec = max($graphprecs)+1;

if (preg_match("|C|",$uoms[0])) {
if($gmintemp%2<>0){$gmintemp-=1;}
if($gmaxtemp%2<>0){$gmintemp+=1;}
if($gmindew%2<>0){$gmindew-=1;}
if($gmaxdew%2<>0){$gmaxdew-=1;}

$gtempdif = $gmaxtemp-$gmintemp;
if($gtempdif<6){$gtempstep=1;}
elseif($gtempdif<12){$gtempstep=2;}
elseif($gtempdif<18){$gtempstep=3;}
elseif($gtempdif<24){$gtempstep=4;}
elseif($gtempdif<30){$gtempstep=5;}
elseif($gtempdif<36){$gtempstep=6;}
else{$gtempstep=8;}

$gdewdif = $gmaxdew-$gmindew;
if($gdewdif<6){$gdewstep=1;}
elseif($gdewdif<12){$gdewstep=2;}
elseif($gdewdif<18){$gdewstep=3;}
elseif($gdewdif<24){$gdewstep=4;}
elseif($gdewdif<30){$gdewstep=5;}
else{$gdewstep=6;}

}else{// F-numbers

if($gmintemp%2<>0){$gmintemp-=1;}
if($gmaxtemp%2<>0){$gmintemp+=1;}
if($gmindew%2<>0){$gmindew-=1;}
if($gmaxdew%2<>0){$gmaxdew-=1;}

$gtempdif = $gmaxtemp-$gmintemp;
if($gtempdif<10){$gtempstep=2;}
elseif($gtempdif<20){$gtempstep=3;}
elseif($gtempdif<30){$gtempstep=4;}
elseif($gtempdif<40){$gtempstep=5;}
elseif($gtempdif<50){$gtempstep=6;}
elseif($gtempdif<60){$gtempstep=7;}
else{$gtempstep=8;}

$gdewdif = $gmaxdew-$gmindew;
if($gdewdif<16){$gdewstep=2;}
elseif($gdewdif<24){$gdewstep=3;}
elseif($gdewdif<32){$gdewstep=4;}
elseif($gdewdif<40){$gdewstep=5;}
elseif($gdewdif<48){$gdewstep=6;}
else{$gdewstep=7;}
}

if (preg_match("|mm|",$uoms[1])) {
if($gmaxprec<14){$gprecstep=2;$gmaxprec = 12;}
elseif($gmaxprec<21){$gprecstep=3;$gmaxprec = 24;}
else{$gprecstep=4;$gmaxprec = 36;}
}else{
if($gmaxprec<2){$gprecstep=.1;$gmaxprec = 1;}
elseif($gmaxprec<4){$gprecstep=.2;$gmaxprec = 2;}
else{$gprecstep=1;$gmaxprec = 3;}
}

if (preg_match("|hPa|",$uoms[3])) {
if(max($graphbaros)<1049){$graphbaromax = 1050;}
else{$graphbaromax = 1065;}
$graphbaromin = 960;
$graphbarostep = 15;
$tsgraphstep=16.5;
}else{
$tsgraphstep=10;
$graphbaromax = 31.0;
$graphbaromin = 28.5;
$graphbarostep = .25;
}
########################################################
# Put together the graph

$icstart = $jstart-(round(($jstop-$jstart)/($mainwidth-20)*15));
$graph='
<script>
<!--
var wxsimconf = {
days: {'.$dayarrayb.'},
windiconurl: "'.$wxallwindicondir.'",
iconurl: "'.$wxallicondir.'",
tooltipprec: {'.substr($tooltipprec, 0, -1).'}
}

var chart;
var series;
var thunderoptions;
var rgr = 1;

$(document).ready(function(){
  Highcharts.setOptions({
      chart: {
        renderTo: "placeholder",
        defaultSeriesType: "spline",
        backgroundColor: "#f7f7f7",
        plotBackgroundColor: {linearGradient: [0, 0, 0, 150],stops: [[0, "#ddd"],[1, "#f2f2f2"]]},
        plotBorderColor: "#88BCCE",
        plotBorderWidth: 0.5,
        marginRight: 40,
        marginLeft: 55,
        style: {fontFamily: \'"UbuntuM","Lucida Grande",Verdana,Helvetica,sans-serif\',fontSize:\'11px\'}
      },
      title: {text: ""},
      lang: {thousandsSep: ""},
      credits: {enabled: false},
      plotOptions: {
        series: {marker: { radius: 0,states: {hover: {enabled: true}}}},
        spline: {lineWidth: 1.5, shadow: false, cursor: "pointer",states:{hover:{enabled: false}}},
        column: {groupPadding: 0.001,pointPadding: 0.025,lineWidth:0,pointWidth:'.$barwidth.'},
        areaspline: {lineWidth: 1.5, shadow: false,states:{hover:{enabled: false}}}
      },
      tooltip: {
         borderColor: "#222",
         borderRadius: 1,
         borderWidth: 0.5,  
         shared: true,
         crosshairs: { width: 0.5,color: "#666"},
         style: {lineHeight: "1.3em",fontSize: "11px"},
         formatter: function() {
            var s = "" + wxsimconf.days[Highcharts.dateFormat(\'%a\', this.x)]+" "+Highcharts.dateFormat(\'%H:%M\', this.x) +"";
            $.each(this.points, function(i, point) {
            var unit = {
               "'.$Prectxt.'": " '.$uoms[1].'",
               "'.$winddtxt.'": " '.$uoms[2].'",
               "'.$windgstdtxt.'": " '.$uoms[2].'",
               "'.$Kitxt.'": "%",
               "'.$Solartxt.'": " w/m²",
               "UV Index": "",
               "'.$Temptxt.'": "'.$uoms[0].'",
               "'.$maxtxt.' '.strtolower($Temptxt).'": "'.$uoms[0].'",
               "'.$mintxt.' '.strtolower($Temptxt).'": "'.$uoms[0].'",
               "'.$wchilltxt.'": "'.$uoms[0].'",
               "'.$dewptxt.'": "'.$uoms[0].'",
               "'.$Barotxt.'": " '.$uoms[3].'",
               "'.$Snowdtxt.'": " '.$uoms[4].'"
            }[point.series.name];
            var uh = point.series.color;
            if(point.series.name=="'.$Prectxt.'"){da=point.series.color;}
            if(point.series.name!="'.$Prectxt.'"){
            s += "<br/><span style=\"color:"+uh+"\">"+point.series.name+":</span> <b>"+point.y+unit+"</b>";
            }
            });
            if(wxsimconf.tooltipprec[this.x]!=null&&rgr==1){
            s += "<br/><span style=\"color:#4572A7\">'.$Prectxt.':</span> <b>"+wxsimconf.tooltipprec[this.x]+" '.$uoms[1].'</b>";
            }else if (rgr==1){
            s += "<br/><span style=\"color:#4572A7\">'.$Prectxt.':</span> <b>'.$noprectxt.'</b>";
            }
            return s;
         }
      },
      legend: {  borderWidth: 0},
      exporting: {enabled:false}
    //  navigation: {buttonOptions: {verticalAlign: "bottom",y: -10}}     
 });
 
var globalX = [{
         gridLineWidth: 0.4,      
         plotBands: ['.substr($ddays, 0, -1).'],
         lineWidth: 0,
         type: "datetime",
         min: '.$jstart.',
         max: '.$jstop.',
         title: {text: null},
         dateTimeLabelFormats: {day: "%H",hour: "%H"},
         tickInterval: 6 * 3600 * 1000,
         labels: {y: 16,style:{fontWeight: \'normal\',fontSize:\'10px\'}}
       }];
      
 var iconX = [{
         type: "datetime",
         min: '.$icstart.',
         max: '.$jstop.',
         title: {text: null},
         dateTimeLabelFormats: {day: "%A",hour: "%a %H:%M"},
         tickLength: 0,
         opposite:true,
         gridLineWidth: 0,
         lineWidth: 0,
         tickInterval: 6 * 3600 * 1000,
         labels: {y: -6,
                  formatter: function() {
                    var uh = Highcharts.dateFormat("%H", this.value);
                    if(uh=="12"){return wxsimconf.days[Highcharts.dateFormat("%a", this.value)];}
                    else{return "";}
                  }
         }
      }];
      
var yTitles = {color: "#666666", fontWeight: "bold",fontSize:"10px"};
var yLabels = {fontWeight: "normal",fontSize:"10px"};
 
  $.get("'.$wxallmainfolder.'graphlog.txt", null, function(tsv, state, xhr) {
      var lines = [], date, rada, ts = "",
      temps = [],
      chills = [],
      gsts = [],
      wsps = [],
      baros = [],
      sols = [],
      uvs = [],
      precs = [],
      snows = [],
      kiws = [],
      dews = [],
      icos = [],
      maxs = [],
      mins = [];
      
      if (typeof tsv !== "string") {tsv = xhr.responseText;}
      
      tsv = tsv.split(/\n/g);
      co = 0;
      $.each(tsv, function(i, line) {
            line = line.split(/,/);
            if(line[0].length > 0 && parseInt(line[0]) != "undefined"){
            date = parseInt(line[0]);
            if(line[16] != "-" && parseFloat(line[15]) > 0){
            precs.push([parseInt(line[16]), parseFloat(line[15])]);
            }         
            temps.push([date, parseFloat(line[1])]);
            chills.push([date, parseFloat(line[5])]);
            dews.push([date, parseFloat(line[4])]);
            baros.push([date, parseFloat(line[7])]);
            kiws.push([date, parseFloat(line[14])]);
            sols.push([date, parseFloat(line[9])]);
            uvs.push([date, parseFloat(line[8])]);
            snows.push([date, parseFloat(line[13])]);
            var arr = ["00","03","06","09","12","15","18","21"];
            var uh = Highcharts.dateFormat("%H", date);
            var ah = Highcharts.dateFormat("%M", date);
            if($.inArray(uh, arr) > -1 && ah == "00"){
            gsts.push([date, parseFloat(line[11])]);
            mkr = wxsimconf.windiconurl+line[12]+".png";
            str = {x:date,y:parseFloat(line[10]), marker:{symbol:\'url(\'+mkr+\')\'}};
            wsps.push(str);			
            }
            if(line[18] != "-" && line[17] != "undefined"){
            mkr = wxsimconf.iconurl+line[17];
            str = {x:date,y:12, marker:{symbol:\'url(\'+mkr+\')\'}};
            icos.push(str);			
            } else {
            str = {x:date,y:12};
            icos.push(str);	
            }
            maxs.push([date, parseFloat(line[2])]);
            mins.push([date, parseFloat(line[3])]);          
            } // If numeric
            
      });
     // alert(wsps[4]);//Debug
      
   var tempoptions = {
      chart:{events: {load: applyGraphGradient},spacingTop:4},
      xAxis: globalX,
      yAxis: [{
         gridLineWidth: 0,tickInterval:'.$gtempstep.',
         title: {text: "'.$uoms[0].'",y:-7,margin:-18,style:yTitles,rotation:0,align:"high"},
         labels: {x: -4,formatter: function() {return this.value;},style:yLabels}       
      },{
         lineWidth: 1,gridLineWidth: 0,max:'.$gmaxprec.',tickInterval:'.$gprecstep.', offset: 28,
         title: {text: "'.$uoms[1].'",y:-7,margin:-22,style:yTitles,rotation:0,align:"high"},
         labels: {x: -4,formatter: function() {return this.value;},style:yLabels}
      },{
         gridLineWidth: 0.4,max: '.$graphbaromax.',min: '.$graphbaromin.',opposite: true,tickInterval: '.$graphbarostep.',
         title: {text:"'.$uoms[3].'",y:-7,margin:-2,style:yTitles,rotation:0,align:"high"},        
         labels: {align: "left",x: 4,formatter: function() {return this.value;},style:yLabels}
      }],
      series: [
      {name: "'.$Temptxt.'",data: temps,events:{legendItemClick:false}},
      {name: "'.$Prectxt.'",color:"#4572A7",type:"column",yAxis:1,events:{legendItemClick:false},data:precs},
      {name: "'.$Barotxt.'",data: baros,color: "#9ACD32",yAxis: 2,events:{legendItemClick:false}},
      {name: "'.$wchilltxt.'",color:"rgba(24,116,205,0.6)",dashStyle:"Dot",events:{legendItemClick:false},data:chills}
      ]
   };

   
   thunderoptions = {
      chart:{spacingTop:4},
      xAxis: globalX,
      yAxis: [{
         gridLineWidth: 0,min: 0,max: 100,tickInterval: '.$tsgraphstep.',allowDecimals:false,endOnTick: false,
         title: {text: "%",y:-7,margin:-18,style:yTitles,rotation:0,align:"high"},
         labels: {x: -4,formatter: function() {return this.value;},style:yLabels}       
      },{
         lineWidth: 1,gridLineWidth: 0,offset: 31,tickInterval:'.$gdewstep.',
         title: {text: "'.$uoms[0].'",y:-7,margin:-21,style:yTitles,rotation:0,align:"high"},
         labels: {x: -4,formatter: function() {return this.value;},style:yLabels}
      },{
         gridLineWidth: 0.4,opposite: true,max: '.$graphbaromax.',min: '.$graphbaromin.',opposite: true,tickInterval: '.$graphbarostep.',
         title: {text: "'.$uoms[3].'",y:-7,margin:-2,style:yTitles,rotation:0,align:"high"},
         labels: {align: "left",x: 4,formatter: function() {return this.value;},style:yLabels}
      }],
      series: [
      {name: "'.$Kitxt.'",color:"#EE7621",type:"areaspline",events:{legendItemClick:false},data:kiws},
      {name: "'.$dewptxt.'",color:"#6CA6CD",yAxis:1,events:{legendItemClick:false},data:dews},
      {name: "'.$Barotxt.'",color:"#9ACD32",yAxis:2,events:{legendItemClick:false},data:baros}
      ]
   };
   
   var soloptions = {
      chart:{spacingTop:4,marginLeft: 56},
      xAxis: globalX,
      yAxis: [{gridLineWidth: 0.4,min: 0,max: 10,opposite: true,
         title: {text: "uvi",y:-7,margin:0,style:yTitles,rotation:0,align:"high"},      
         labels: {x: 2,formatter: function() {return this.value;},style:yLabels}       
      },{
         lineWidth: 1,gridLineWidth: 0.4,min: 0,max: 1000,
         title: {text: "w/m²",y:-7,margin:-26,style:yTitles,rotation:0,align:"high"},
         labels: {x: -4,formatter: function() {return this.value;},style:yLabels}
      }],
      series: [
      {name: "'.$Solartxt.'",data: sols,color:"#EEEE00",type: "areaspline",yAxis:1},
      {name: "UV Index",data: uvs,color:"#FFB90F",type: "areaspline",zIndex: 2}
      ]
   };
   
   var snowoptions = {
      chart:{spacingTop:4},
      xAxis: globalX,
      yAxis: [{
         gridLineWidth: 0.4,
         title: {text: "'.$uoms[4].'",y:-7,margin: -5,style:yTitles,rotation:0,align:"high"},
         labels: {x: -4,formatter: function() {return this.value;},style:yLabels}       
      },{
      linkedTo:0,gridLineWidth:0,opposite:true,
      title: {text: "'.$uoms[4].'",y:-7,margin: -5,style:yTitles,rotation:0,align:"high"},
      labels: {x:4,formatter: function() {return this.value;},style:yLabels}
      }],
      series: [{name:"'.$Snowdtxt.'",color:"#87CEFA",type:"areaspline",events:{legendItemClick:false},data:snows}]
   };
   
   var windoptions = {
      chart:{spacingTop:4},
      xAxis: globalX,
      yAxis: [{
         gridLineWidth: 0.4,min: 0,max: '.$windlimit.',
         title: {text: "'.$uoms[2].'",y:-7,margin: -11,style:yTitles,rotation:0,align:"high"},
         labels: {x: -4,formatter: function() {return this.value;},style:yLabels}       
      },{
         linkedTo:0,gridLineWidth:0,opposite:true,
         title: {text: "'.$uoms[2].'",y:-7,margin:6,style:yTitles,rotation:0,align:"high"},
         labels: {x: 4,formatter: function() {return this.value;},style:yLabels}
      }],
      series: [
      {name:"'.$windgstdtxt.'",color:"#CD3333",type:"scatter",events:{legendItemClick:false},data:gsts,marker:{radius:2,symbol:"circle"}},
      {name:"'.$winddtxt.'",color:"#006400",type:"scatter",events:{legendItemClick:false},data:wsps}
      ]
   };

   var maxoptions = {
      chart:{spacingTop:4},
      xAxis: globalX,
      yAxis: [{
         gridLineWidth: 0.4,
         title: {text: "'.$uoms[0].'",y:-7,margin:-21,style:yTitles,rotation:0,align:"high"},
         labels: {x: -7,formatter: function() {return this.value;},style:yLabels}       
      },{
         lineWidth: 1,gridLineWidth: 0.4,opposite:true,linkedTo: 0,
         title: {text: "'.$uoms[0].'",y:-7,margin: -12,style:yTitles,rotation:0,align:"high"},
         labels: {x: 5,formatter: function() {return this.value;},style:yLabels}
      }],
      series: [
         {name: "'.$maxtxt.' '.strtolower($Temptxt).'",color: "#EE3B3B",events: {legendItemClick:false},data:maxs},
         {name: "'.$mintxt.' '.strtolower($Temptxt).'",color: "#6495ED",events: {legendItemClick:false},data:mins}
         ]
   };
   
   var iconoptions = {
      chart:{renderTo:"iconholder",backgroundColor:"#f7f7f7",plotBackgroundColor:"#f7f7f7",
             plotBorderWidth:0,borderWidth:0,spacingBottom:0,marginLeft:43},
      legend: {enabled:false},
      tooltip: {enabled:false},
      xAxis: iconX,
      yAxis: [{
         gridLineWidth: 0,min: 0,max:10,title:{text:""},
         labels: {enable: false,style: {color: "#f7f7f7"}}       
      }],
      series: [
      {name:"",color:"#006400",type:"scatter",events:{legendItemClick:false},data:icos}
      ]
   };
   
      chart = new Highcharts.Chart($.extend({},tempoptions));
      chart2 = new Highcharts.Chart(iconoptions);
        $("#wxsimuom1").html("'.$uoms[1].'");
        $("#wxsimuom2").html("'.$uoms[0].'").css({left:"10px"});
        $("#wxsimuom3").html("'.$uoms[3].'").css({right:"20px"});
      
        $("#tempbtn").click(function () {
        if(chart){chart.destroy();chart = null;}
        rgr = 1;
        chart = new Highcharts.Chart($.extend({},tempoptions));
        $("#wxsimuom1").html("'.$uoms[1].'");
        $("#wxsimuom2").html("'.$uoms[0].'").css({left:"10px"});
        $("#wxsimuom3").html("'.$uoms[3].'").css({right:"20px"});
        $("#tempbtn").addClass("active");
        $("#solarbtn").removeClass("active");
        $("#windbtn").removeClass("active");
        $("#barobtn").removeClass("active");
        $("#snowbtn").removeClass("active"); 
        $("#maxbtn").removeClass("active");
        });
        $("#windbtn").click(function () {
        chart.destroy();
        rgr = 0;
        chart = new Highcharts.Chart($.extend({},windoptions));
        $("#wxsimuom1").html("");
        $("#wxsimuom2").html("'.$uoms[2].'").css({left:"25px"});
        $("#wxsimuom3").html("'.$uoms[2].'").css({right:"25px"});
        $("#windbtn").addClass("active");
        $("#solarbtn").removeClass("active");
        $("#tempbtn").removeClass("active");
        $("#barobtn").removeClass("active");
        $("#snowbtn").removeClass("active"); 
        $("#maxbtn").removeClass("active");
        });
        $("#barobtn").click(function () {
        if(chart){chart.destroy();chart = null;}
        rgr = 0;
        var chart = new Highcharts.Chart($.extend({},thunderoptions));
        $("#wxsimuom1").html("'.$uoms[0].'");
        $("#wxsimuom2").html("%").css({left:"15px"});
        $("#wxsimuom3").html("'.$uoms[3].'").css({right:"20px"});
        $("#barobtn").addClass("active");
        $("#solarbtn").removeClass("active");
        $("#tempbtn").removeClass("active");
        $("#windbtn").removeClass("active");
        $("#snowbtn").removeClass("active"); 
        $("#maxbtn").removeClass("active");
        });
        $("#snowbtn").click(function () {
        chart.destroy();
        rgr = 0;
        chart = new Highcharts.Chart($.extend({},snowoptions));
        $("#wxsimuom1").html("");
        $("#wxsimuom2").html("'.$uoms[4].'").css({left:"30px"});
        $("#wxsimuom3").html("'.$uoms[4].'").css({right:"30px"});
        $("#snowbtn").addClass("active");
        $("#solarbtn").removeClass("active");
        $("#tempbtn").removeClass("active");
        $("#windbtn").removeClass("active");
        $("#barobtn").removeClass("active"); 
        $("#maxbtn").removeClass("active");
        });
        $("#solarbtn").click(function () {
        chart.destroy();
        rgr = 0;
        chart = new Highcharts.Chart($.extend({},soloptions));
        $("#wxsimuom1").html("");
        $("#wxsimuom2").html("w/m&sup2;").css({left:"22px"});
        $("#wxsimuom3").html("UV").css({right:"30px"});
        $("#solarbtn").addClass("active");
        $("#snowbtn").removeClass("active");
        $("#tempbtn").removeClass("active");
        $("#windbtn").removeClass("active");
        $("#barobtn").removeClass("active"); 
        $("#precbtn").removeClass("active");
        });
        $("#maxbtn").click(function () {
        chart.destroy();
        rgr = 0;
        chart = new Highcharts.Chart($.extend({},maxoptions));
        $("#wxsimuom1").html("");
        $("#wxsimuom2").html("'.$uoms[0].'").css({left:"28px"});
        $("#wxsimuom3").html("'.$uoms[0].'").css({right:"30px"});
        $("#maxbtn").addClass("active");
        $("#humbtn").removeClass("active");
        $("#tempbtn").removeClass("active");
        $("#windbtn").removeClass("active");
        $("#barobtn").removeClass("active"); 
        $("#solarbtn").removeClass("active");
        });
      
   });
function applyGraphGradient() {
    // Options
    var threshold = '.$frzpoint.',colorAbove = "#EE3B3B",colorBelow = "#6495ED";
    // internal
    var series = this.series[0],i,point;      
    if (this.renderer.box.tagName === "svg") {      
        var translatedThreshold = series.yAxis.translate(threshold),
            y1 = Math.round(this.plotHeight - translatedThreshold),
            y2 = y1 + 2; // 0.01 would be fine, but IE9 requires 2
        // Apply gradient to the path
        series.graph.attr({stroke: {linearGradient: [0, y1, 0, y2],stops: [[0, colorAbove],[1, colorBelow]]}});    
    }   
    // Apply colors to the markers
    for (i = 0; i < series.data.length; i++) {
        point = series.data[i];
        point.color = point.y < threshold ? colorBelow : colorAbove;
        if (point.graphic) {point.graphic.attr({fill: point.color});}
    }   
    // prevent the old color from coming back after hover
    delete series.pointAttr.hover.fill;
    delete series.pointAttr[""].fill;  
}
});
//-->
</script>
<div style="padding-top:10px;margin-bottom:20px;position:relative;min-height:400px" class="flotwrap" >
<div id="wxsimuom3" style="position:absolute;top:46px;font-size:10px;color:#424242;z-index:2;font-weight:bold;"></div>
<div style="position:absolute;left:16px;top:46px;font-size:10px;color:#424242;z-index:2;font-weight:bold;"><span id="wxsimuom1"></span>
<span id="wxsimuom2" style="display:inline-block;position:relative;"></span></div>
<div id="iconholder" style="width:'.($mainwidth-20).'px;height:55px;margin:0 auto;position:relative;"></div>
<div id="placeholder" style="width:'.($mainwidth-20).'px;height:340px;margin:0 auto;position:relative;"></div>
<br/>
<div id="meteogrambuttons" style="width:'.($mainwidth-20).'px;">
<span style="margin:3px;" class="wxall-button" id="tempbtn">'.$Temptxt.'/'.$Prectxt.'/'.$Barotxt.'</span>';
if(max($snowgraph)>0) {$graph.='<span class="wxall-button" style="margin:3px;" id="snowbtn">'.$Snowdtxt.'</span>';}
$graph.='<span class="wxall-button" style="margin:3px;" id="barobtn">'.$thundertxt.'/'.$dewptxt.'/'.$Barotxt.'</span>
<span class="wxall-button" style="margin:3px;" id="solarbtn">UV/'.$Solartxt.'</span>
<span class="wxall-button" style="margin:3px;" id="windbtn">'.$winddtxt.'</span>
<span class="wxall-button" style="margin:3px;" id="maxbtn">'.$maxtxt.'/'.strtolower($mintxt).' '.strtolower($Temptxt).'</span>
</div>
</div>
';

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
';
if($useustemp==1){$wxallhead.= '<link rel="stylesheet" type="text/css" media="screen" href="'.$wxallmainfolder.'css/US/wxall.css" />';}
$wxallhead.= '
<script type="text/javascript" src="'.$wxallmainfolder.'js/modernizr.js"></script>
<script type="text/javascript" src="'.$wxallmainfolder.'js/jquery.1.6.4.min.js"></script>
<script type="text/javascript" src="'.$wxallmainfolder.'js/jquery.tooltip.js"></script>
<script type="text/javascript" src="'.$wxallmainfolder.'js/jquery.ui.min.js"></script>
<script type="text/javascript" src="'.$wxallmainfolder.'js/highcharts.2.2.0.js"></script>
<script>
<!--
$(document).ready(function(){
tooltip();
});
//-->
</script>
';
?>