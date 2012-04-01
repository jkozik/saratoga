<?php
// Version 2.0 - 09-21-09
// Add your various language translations here, then modify the variable in fc-config.php
// This parameter may be passed by your script with either $_REQUEST['lang'] = 'en'; or by adding "?lang=xx" to the end of the URL.

if (isset($_REQUEST['lang'])) {  
	$lang = $_REQUEST['lang'];
}


// Language Configuration

if($lang == "it") {

} else if($lang == "fi") {


} else if($lang == "se") {


} else if($lang == "xx") {               // An example that you can modify

$LangTop1    = "Temps";                  // Top Left corner text
$LangTop2    = "&deg; F";                // Line just below
$LangTop3    = "Goal";                   // Replaces LangTop1 when in "goal oriented" mode (new in ver 1.5)
$Langupdated = "Updated: ";              // Change to your language here
$Langat      = " at ";
$LangPday    = "Forecast ";              // Allows you to have a different heading in the first column
$Langday     = "Day ";                   // e.g. Forecast Day ... Day +1 ... Day +2 etc.
$Langhigh    = "High";
$Langlow     = "Low";
$Langdate    = "Date";
$LangTime    = "Time";                   // Added in version 1.6
$LangNW      = "NW";
$LangDiff    = "Diff";
$LangAct     = "Act";
$LangWS      = "WS";
$Langresults = "Results";
$Langtie	 = "Tie";
$Langwins	 = "# Wins";
$Langpwins	 = "% Wins";                  // Two added in ver 1.5
$Langnsucc	 = "# Succ";
$Langpsucc	 = "% Succ";
$LangMean	 = "Ave Error";
$LangNet	 = "Net Error";               // Added in ver 2.0
$LangSD      = "Std Dev";
$LangStart   = "Start:";
$LangRows    = "Days:";
$LangCols    = "Cols:";
$LangStats   = "Ext Stats:";
$LangDsel    = "Diff Cols:";
$LangHlite   = "Hilite #s:";
$LangGoals   = "Goal:";                  // new in ver 1.5
$LangNA      = "n/a";                    // Use &nbsp; if you want it blank when a forecast temp is not available (added for ec-forecast)
$LangSNWS    = "Show";                   // Added in version 1.8

} else {
// Defaults to English

$LangTop1    = "Temps";                  // Top Left corner text
$LangTop2    = "&deg; F";                // Line just below
$LangTop3    = "Goal";                   // Replaces LangTop1 when in "goal oriented" mode (new in ver 1.5)
$Langupdated = "Updated: ";              // Change to your language here
$Langat      = " at ";
$LangPday    = "Forecast ";              // Allows you to have a different heading in the first column
$Langday     = "Day ";                   // e.g. Forecast Day ... Day +1 ... Day +2 etc.
$Langhigh    = "High";
$Langlow     = "Low";
$Langdate    = "Date";
$LangTime    = "Time:";                   // Added in version 1.6
$LangNW      = "NW";
$LangDiff    = "Diff";
$LangAct     = "Act";
$LangWS      = "WS";
$Langresults = "Results";
$Langtie	 = "Tie";
$Langwins	 = "# Wins";
$Langpwins	 = "% Wins";                  // Two added in ver 1.5
$Langnsucc	 = "# Succ";
$Langpsucc	 = "% Succ";
$LangMean	 = "Ave Error";
$LangNet	 = "Net Error";               // Added in ver 2.0
$LangSD      = "Std Dev";
$LangStart   = "Start:";
$LangRows    = "Days:";
$LangCols    = "Cols:";
$LangStats   = "Ext Stats:";
$LangDsel    = "Diff Cols:";
$LangHlite   = "Hilite #s:";
$LangGoals   = "Goal:";                  // new in ver 1.5
$LangNA      = "n/a";                    // Use &nbsp; if you want it blank when a forecast temp is not available (added for ec-forecast)
$LangSNWS    = "Show";                   // Added in version 1.8

}
?>