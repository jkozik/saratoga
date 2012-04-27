<?php
  #####################################################################
///  Add language functions if using V2 USA template
if(!function_exists('langtransstr')) {    
function langtransstr ( $item ) {
  global $LANGLOOKUP,$missingTrans;
  
  if(isset($LANGLOOKUP[$item])) {
     return $LANGLOOKUP[$item];
  } else {
      if(isset($item) and $item <> '') {$missingTrans[$item] = true; }
     return $item;
  }
}
}
#####################################################################
if(!function_exists('langtrans')) {    
function langtrans ( $item ) {
  global $LANGLOOKUP,$missingTrans;
  
  if(isset($LANGLOOKUP[$item])) {
     echo $LANGLOOKUP[$item];
  } else {
     if(isset($item) and $item <> '') {$missingTrans[$item] = true; }
     echo $item;
  }

}
}

if (($SITE['WXsoftware']=='') OR ($SITE['WXsoftware']=='WD')){
    $show_today = $show_today;
    } else {
    $show_today = false;
    }  
?>
