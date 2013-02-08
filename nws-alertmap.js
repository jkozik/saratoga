// <![CDATA[
// ----------------------------------------------
//
//   NWS Public Alerts
//
//   Set a cookie to see if the user has 
//   javascript enabled
//
// ----------------------------------------------
 
var expdate = new Date ();
expdate.setTime (expdate.getTime() + (24 * 60 * 60 * 15)); 
function setCookie(name, value, expires, path, domain, secure) {
 var thisCookie = name + "=" + escape(value) +
 ((expires) ? "; expires=" + expires.toGMTString() : "") +
 ((path) ? "; path=" + path : "") +
 ((domain) ? "; domain=" + domain : "") +
 ((secure) ? "; secure" : "");
 document.cookie = thisCookie;
}
setCookie ("NWSalerts", 'true', expdate)
// ]]>
