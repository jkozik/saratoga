// <![CDATA[
// ----------------------------------------------------------------------
// Rotate content display -- Ken True -- saratoga-weather.org
// This script is for use with the ajaxwx.js to create a rotating gizmo
// display of AJAX conditions marked with <span class="contentN" style="display: none"></span>
// where N=0..8
//
//  Version 1.00 - initial release
//  Version 1.01 - changed to look for class="ajaxcontentN" to prevent interference with buoy-data.php
//  Version 1.02 - fixed rotation for Internet Explorer 8
// 
//
// --------- begom settomgs ---------------------------------------------------------------
var ajaxrotatedelay=4000; // Rotate display every 4 secs (= 4000 ms)
var showUV = true ;       // set to false if you don't have a Davis VP UV sensor
// --------- emd settomgs -----------------------------------------------------------------
//
// you shouldn't need to change things below this line
//
var ie4=document.all;
var browser = navigator.appName;
var ie8 = false;
if (ie4 && /MSIE (\d+\.\d+);/.test(navigator.userAgent)){ //test for MSIE x.x;
 var ieversion=new Number(RegExp.$1) // capture x.x portion and store as a number
 if (ieversion>=8) {
   ie4=false;
   ie8=true;
 }
}
var ajaxcurindex = 0;
var ajaxtotalcontent = 0;
var ajaxrunrotation = 1;
var browser = navigator.appName;

function get_content_tags ( tag ) {
// search all the span tags and return the list with class=tag 
//
  if (ie4 && browser != "Opera" && ! ie8) {
    var elem = document.getElementsByTagName('span');
	var lookfor = 'className';
  } else {
    var elem = document.getElementsByTagName('span');
	var lookfor = 'class';
  }
     var arr = new Array();
	 var i = 0;
	 var iarr = 0;
	 
     for(i = 0; i < elem.length; i++) {
          var att = elem[i].getAttribute(lookfor);
          if(att == tag) {
               arr[iarr] = elem[i];
               iarr++;
          }
     }

     return arr;
}


function ajax_get_total() {
	ajaxtotalcontent = 8; // content0 .. content7 
	if (showUV) { ajaxtotalcontent++ ; } // UV display is in last content area
}

function ajax_contract_all() {
  for (var y=0;y<ajaxtotalcontent;y++) {
      var elements = get_content_tags("ajaxcontent"+y);
	  var numelements = elements.length;
//	  alert("ajax_contract_all: content"+y+" numelements="+numelements);
	  for (var index=0;index!=numelements;index++) {
         var element = elements[index];
		 element.style.display="none";
      }
  }
}

function ajax_expand_one(which) {
  ajax_contract_all();
  var elements = get_content_tags("ajaxcontent"+which);
  var numelements = elements.length;
  for (var index=0;index!=numelements;index++) {
     var element = elements[index];
	 element.style.display="inline";
  }
}
function ajax_step_content() {
  ajax_get_total();
  ajax_contract_all();
  ajaxcurindex=(ajaxcurindex<ajaxtotalcontent-1)? ajaxcurindex+1: 0;
  ajax_expand_one(ajaxcurindex);
}
function ajax_set_run(val) {
  ajaxrunrotation = val;
  ajax_rotate_content();
}
function ajax_rotate_content() {
  if (ajaxrunrotation) {
    ajax_get_total();
    ajax_contract_all();
    ajax_expand_one(ajaxcurindex);
    ajaxcurindex=(ajaxcurindex<ajaxtotalcontent-1)? ajaxcurindex+1: 0;
    setTimeout("ajax_rotate_content()",ajaxrotatedelay);
  }
}

ajax_rotate_content();

// ]]>
