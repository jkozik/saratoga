flyout-menu-README.txt  

This is the installation information to add the flyout-menu system to your current copy of the
Weather-Display/AJAX/PHP template set (Version 1.13 or earlier).  Version 1.14 of the template 
distributions includes this capability built-in, so no need to install this plugin.

Installation:

1) unzip the distribution .zip into the directory containing your current template set.

   Files added:
   
	   flyout-menu.php
	   flyout-menu.xml
	   flyout-menu-test.xml
	   wxsitemap.php
	   ajax-images/flyout-*.gif
   
   Files replaced:
   
	   weather-screen-*-*.css  (replacement CSS files from Mike Challis)
	   include-theme-switcher.php  (V1.08 from Mike Challis)
   
   Upload all the above files to your website using ASCII mode for everything 
   except the ajax-images/*.gif which should be FTP BINARY uploaded.
   
2) edit your current Settings.php file and add under the "Mike Challis' Theme Switch configuration" a new entry

   $SITE['flyoutmenu'] = false; // set to false to use classic menubar.php instead
   
   Save the file, and upload in ASCII mode to website.
   NOTE: we are deliberately NOT enabling the menu system so you can finish configuring and testing your
    new menu below in steps (5) and (6).
   
3) edit your top.php file

   after:
   
		if ( isset ($TITLE) ) {
			echo "    <title>" . $TITLE . "</title>\n";
		} else {
			echo "    <title>" . $SITE['organ'] . "</title>\n";
		}
		
   insert:
   
		if (isset($SITE['flyoutmenu']) and $SITE['flyoutmenu'] or
		    isset($_REQUEST['menu']) and strtolower($_REQUEST['menu']) == 'test' ) {
		  $SITE['flyoutmenu'] = true;
		  $PrintFlyoutMenu = false;
		  $genDiv =false;
		  global $FlyoutCSS, $FlyoutMenuText;
		  include_once('flyout-menu.php');
		  print $FlyoutCSS;
		}

   Save the file, and upload in ASCII mode to website.
   
4) edit your menubar.php file

   replace:
   
       gen_navlinks($html); // generate the links set with highlight for the current page


   with:
   
   
	 if (isset($SITE['flyoutmenu']) and $SITE['flyoutmenu']) {
	   global $FlyoutMenuText;
	   print "<div class=\"flyoutmenu\">\n";
	   print $FlyoutMenuText;
	   print "</div>\n";
	 } else {
		gen_navlinks($html); // generate the links set with highlight for the current page
	 }

   Save the file, and upload to website.
   
   
 5) Edit the flyout-menu-test.xml and add/remove entries in it until it matches how you'd like
    your flyout menu to appear.   

	If you've implemented your home page as index.php, change:
	
		<item caption="Home" link="wxindex.php" title="Home Page"/>
	to:
	
		<item caption="Home" link="index.php" title="Home Page"/>


    Upload the file in ASCII mode.
	
 6) Open your wxtrends.php on your website using your browser.  Use:  wxtrends.php?menu=test
    That will load the flyout-menu using the flyout-menu-test.xml file you worked on in step (5)
	If you have errors, correct them in flyout-menu-test.xml (step 5) and try again.
	
	Repeat steps 5 and 6 until you are satisfied with the organization of your new menu.
	
 7) copy flyout-menu-test.xml over flyout-menu.xml and upload flyout-menu.xml in ASCII mode to your webserver.
 
 8) edit wxlive.php (if you have Weather-Display LIVE installed). This change needed to allow flyout-menu to
    popup OVER the Flash display.
 
    change:
	
	// so.addParam("wmode", "transparent"); // mchallis disabled for compatibility with multiple css style themes

	to:
	
	so.addParam("wmode", "opaque"); // mchallis disabled for compatibility with multiple css style themes

    Save and upload wxlive.php in ASCII mode to your website
	
 9) edit wxmesomap.php (if you have Weather-Display MesoMap Live installed). This change needed to allow
    flyout-menu to popup OVER the Flash display.
 
     change:
	
	// so.addParam("wmode", "transparent"); // mchallis disabled for compatibility with multiple css style themes

	to:
	
	so.addParam("wmode", "opaque"); // mchallis disabled for compatibility with multiple css style themes

    Save and upload wxmesomap.php in ASCII mode to your website

 
10) edit Settings.php again and change the $SITE['flyoutmenu'] statement to read:
 
    $SITE['flyoutmenu'] = true; // set to false to use classic menubar.php instead
	
	
	
The new flyout-menu will now be displayed on all pages of your website.  To turn it off, just 
set $SITE['flyoutmenu'] = false;  and the menu will revert to what was in the $html =' ... '; section
of menubar.php.

It is STRONGLY recommended that you never edit the flyout-menu.xml file directly .. a simple XML error will 
STOP YOUR ENTIRE SITE FROM APPEARING UNTIL YOU CORRECT THE XML ERROR.

Always edit the flyout-menu-test.xml, upload it, try it using ?menu=test on any page, and when it works to
your satisfaction, perform step (7) to implement the changed file into 'production'.

Ken True & Mike Challis
