This directory contains the tag file to upload through VWS.

Select either the VWStags-comments.htx file (larger, but with comments) or
the VWStags.htx (smaller, without comments) as the template file source.

Then place the chosen file in your c:\vws\templates directory
 
 Use the VWS, Internet, HTML Settings panel and place a new entry to process as:

 c:\vws\template\VWStags.htx               c:\vws\root\VWStags.php

 Set a schedule for processing the VWStags.htx file to 5 minutes.

 then use the VWS, Internet, FTP Send(upload) File panel to  have

 c:\vws\root\VWStags.php                   /

 so that the processed file VWStags.php is uploaded to your website.

 Set a schedule for uploading the VWStags.php file to 5 minutes.


 Then you can view the uploaded tags on your website as:

   VWStags.php?sce=view  (see the raw source after processing by VWS)
 
   VWStags.php?sce=dump  (see the contents of the resulting $WX[] array with VWS tag names and data values


Skip the following section if you already have WeatherFlash installed
------------------------------------------------------------------------------
WeatherFlash upload configuration (if you don't already have WeatherFlash installed)

1) upload the entire directory structure of the wflash/* directory to /wflash in your weather
  website.   When successful, you should have on your website (at the document root):

  /wflash
  /wflash/Config
  /wflash/Data
  /wflash/scripts

  directories and their contents

2) use VWS, Internet, WeatherFlash dialog

   Set the UserID to 'DEMO'  (all caps)
   Set the timer to 5 seconds

   In the Active Server Page section:
   put the full url in the text box for the wxf-submit.php script like

   http://www.yourwebsite.com/wflash/scripts/wxf-submit.php

   Tick: Activate

   close the dialog

3) you should now be able to see the updates happening at

   http://www.yourwebsite.com/wflash/Data/wflash.txt  and
   http://www.yourwebsite.com/wflash/Data/wflash2.txt

   you can also run the configuration test script provided in the wflash directory:

   http://www.yourwebsite.com/wflash/wflash-filetest.php

   which should show

   "Test concluded .. no errors found. WeatherFlash data uploads should work correctly based on permissions."

4) if you used the wflash/ name on your website, then no further changes are needed in the Settings-weather.php or
   the ajaxVWSwx.js AJAX script and you should have AJAX updates available on your website.   If you changed the
   name or location on your website, make sure you update both Settings-weather.php and ajaxVWSwx.js with
   the new directory name.
