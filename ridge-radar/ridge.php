<?php
#########################################################################
#
#                   ridge.php
#         NOAA Ridge Radar Animation Script
#
#    Author: webmaster@eldoradocountyweather.com
#
#########################################################################
#
# // See the install.txt for installation instructions included in zip file
#
# // ver 1.0 - Originally coded with AniS in 2009
# // ver 2.0 - Did a rewrite & changed over to FlAniS instead of AniS
#
#########################################################################
#
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, 
# USA
#########################################################################
if ( isset($_REQUEST['sce']) && strtolower($_REQUEST['sce']) == 'view' ) {
//--self downloader --
   $filenameReal = __FILE__;
   $download_size = filesize($filenameReal);
   header('Pragma: public');
   header('Cache-Control: private');
   header('Cache-Control: no-cache, must-revalidate');
   header("Content-type: text/plain");
   header("Accept-Ranges: bytes");
   header("Content-Length: $download_size");
   header('Connection: close');
   
   readfile($filenameReal);
   exit;
}

$SITE['self'] = htmlentities( substr($_SERVER['PHP_SELF'], 0,  
    strcspn( $_SERVER['PHP_SELF'] , "\n\r") ), ENT_QUOTES );

// User configuration files: Change the 3 line below to your radar type & station
$radartype = 'N0R';
$radarstation = 'LOT';
$radarstation2 = 'lot';



// Search patterns
$regex1 = '/' . $radarstation . '_[0-9_]+_' . $radartype . '.gif/';
$pattern = $regex1;
$pattern1 = '/"[a-zA-Z0-9_: ]*"/';
$regex2 = '/' . $radarstation . '_[0-9_]*_' . $radartype . '_Legend.gif/';
$pattern2 = $regex2;
$regex3 = '/' . $radarstation . '_[0-9_]*_Short_Warnings.gif/';
$pattern3 = $regex3;
$regex4 = '/' . $radarstation . '_[0-9_]*_*Warnings.gif/';
$pattern4 = $regex4;

// Source file

$noaasource = 'http://radar.weather.gov/' . $radarstation2 . '_' . $radartype . '_overlayfiles.txt';
$content = file_get_contents( $noaasource );

$counter = -1;

preg_match_all( $pattern, $content, $matches );

// Loop produces radar images - image0.gif through image8.gif

foreach($matches[0] as $value) 
{
        $counter ++;
        $im1 = 'http://radar.weather.gov/RadarImg/' . $radartype . '/' . $radarstation . '/' . $value;
        $image = 'image' . $counter . '.gif';

        if(@GetImageSize($im1))
        {
        $timg1=imagecreatefromgif($im1);
        imagegif($timg1, 'ridge-radar/RadarImg/' . $radartype . '/' . $radarstation . '/' . $image,100);
        imagedestroy($timg1);
        }
}

// Download 1 static radar image and rename (most current radar image)

        $im9 = 'http://radar.weather.gov/RadarImg/' . $radartype . '/' . $radarstation . '_' . $radartype . '_0.gif';

        if(@GetImageSize($im9))
        {
        $timg9=imagecreatefromgif($im9);
        imagegif($timg9, 'ridge-radar/RadarImg/' . $radartype . '/' . $radarstation . '_' . $radartype . '_0.gif',100);
        imagedestroy($timg9);
        }

// Pull the image time stamps in an array for $timestamp1 through $timestamp10

preg_match_all($pattern1, $content, $matches1);

       $time1a = $matches1[0][0];
       $time2a = $matches1[1][0];
       $time3a = $matches1[2][0];
       $time4a = $matches1[3][0];
       $time5a = $matches1[4][0];

$timestamp1 = $time1a;$time2a;$time3a;$time4a;$time5a;

       $time1b = $matches1[0][1];
       $time2b = $matches1[1][1];
       $time3b = $matches1[2][1];
       $time4b = $matches1[3][1];
       $time5b = $matches1[4][1];

$timestamp2 = $time1b;$time2b;$time3b;$time4b;$time5b;

       $time1c = $matches1[0][2];
       $time2c = $matches1[1][2];
       $time3c = $matches1[2][2];
       $time4c = $matches1[3][2];
       $time5c = $matches1[4][2];

$timestamp3 = $time1c;$time2c;$time3c;$time4c;$time5c;

       $time1d = $matches1[0][3];
       $time2d = $matches1[1][3];
       $time3d = $matches1[2][3];
       $time4d = $matches1[3][3];
       $time5d = $matches1[4][3];

$timestamp4 = $time1d;$time2d;$time3d;$time4d;$time5d;

       $time1e = $matches1[0][4];
       $time2e = $matches1[1][4];
       $time3e = $matches1[2][4];
       $time4e = $matches1[3][4];
       $time5e = $matches1[4][4];

$timestamp5 = $time1e;$time2e;$time3e;$time4e;$time5e;

       $time1f = $matches1[0][5];
       $time2f = $matches1[1][5];
       $time3f = $matches1[2][5];
       $time4f = $matches1[3][5];
       $time5f = $matches1[4][5];

$timestamp6 = $time1f;$time2f;$time3f;$time4f;$time5f;

       $time1g = $matches1[0][6];
       $time2g = $matches1[1][6];
       $time3g = $matches1[2][6];
       $time4g = $matches1[3][6];
       $time5g = $matches1[4][6];

$timestamp7 = $time1g;$time2g;$time3g;$time4g;$time5g;

       $time1h = $matches1[0][7];
       $time2h = $matches1[1][7];
       $time3h = $matches1[2][7];
       $time4h = $matches1[3][7];
       $time5h = $matches1[4][7];

$timestamp8 = $time1h;$time2h;$time3h;$time4h;$time5h;

       $time1j = $matches1[0][8];
       $time2j = $matches1[1][8];
       $time3j = $matches1[2][8];
       $time4j = $matches1[3][8];
       $time5j = $matches1[4][8];

$timestamp9 = $time1j;$time2j;$time3j;$time4j;$time5j;

       $time1k = $matches1[0][9];
       $time2k = $matches1[1][9];
       $time3k = $matches1[2][9];
       $time4k = $matches1[3][9];
       $time5k = $matches1[4][9];

$timestamp10 = $time1k;$time2k;$time3k;$time4k;$time5k;

$counter2 = -1;

preg_match_all( $pattern2, $content, $matches2 );

// Loop produces legend image0.gif through image8.gif

foreach($matches2[0] as $value2) 
{
        $counter2 ++;
        $im2= 'http://radar.weather.gov/Legend/'. $radartype . '/' . $radarstation . '/' . $value2;
        $image = 'image' . $counter2 . '.gif';

        if(@GetImageSize($im2))
        {
        $timg2=imagecreatefromgif($im2);
        imagegif($timg2, 'ridge-radar/Legend/' . $radartype . '/' . $radarstation . '/' . $image,100);
        imagedestroy($timg2);
        }
}

// Download 1 static legend image (most current legend image)

        $imj= 'http://radar.weather.gov/Legend/' . $radartype . '/' . $radarstation . '_' . $radartype . '_Legend_0.gif';

        if(@GetImageSize($imj))
        {
        $timgj=imagecreatefromgif($imj);
        imagegif($timgj,'ridge-radar/Legend/' . $radartype . '/' . $radarstation . '_' . $radartype . '_Legend_0.gif',100);
        imagedestroy($timgj);
        }

$counter3 = -1;

preg_match_all($pattern3, $content, $matches3);

// Loop produces warnings image0.gif through image4.gif

foreach($matches3[0] as $value3) 
{
        $counter3 ++;
        $im3 = 'http://radar.weather.gov/Warnings/Short/' . $radarstation . '/' . $value3;
        $image = 'image' . $counter3 . '.gif';

        if(@GetImageSize($im3))
        {
        $timg3=imagecreatefromgif($im3);
        imagegif($timg3, 'ridge-radar/Warnings/Short/' . $radarstation . '/' . $image,100);
        imagedestroy($timg3);
        }
}

$counter4 = 4;

preg_match_all($pattern4, $content, $matches4);

// Loop produces warnings image5.gif through image8.gif

foreach($matches4[0] as $value4) 
{
        $counter4 ++;
        $im4 = 'http://radar.weather.gov/Warnings/Short/' . $radarstation . '/' . $value4;
        $image = 'image' . $counter4 . '.gif';

        if(@GetImageSize($im4))
        {
        $timg4=imagecreatefromgif($im4);
        imagegif($timg4, 'ridge-radar/Warnings/Short/' . $radarstation . '/' . $image,100);
        imagedestroy($timg4);
        }
}

// Download the static warning image (most current image)

        $imw9 = 'http://radar.weather.gov/Warnings/Short/' . $radarstation . '_Short_Warnings_0.gif';
        if(@GetImageSize($imw9))
        {
        $timgw9=imagecreatefromgif($imw9);
        imagegif($timgw9,'ridge-radar/Warnings/Short/' . $radarstation . '_Short_Warnings_0.gif',100);
        imagedestroy($timgw9);
}

// Write the new xxx_XXX_overlayfiles.txt file that FLAniS reads
$overlayfiles = "ridge-radar/graphics/black_bgrnd.gif". $timestamp1 ." overlay=ridge-radar/Overlays/Topo/Short/". $radarstation . "_Topo_Short.jpg,"." ridge-radar/RadarImg/". $radartype ."/". $radarstation ."/image0.gif,"." ridge-radar/Overlays/County/Short/". $radarstation ."_County_Short.gif,"." ridge-radar/Overlays/Rivers/Short/". $radarstation ."_Rivers_Short.gif,"." ridge-radar/Overlays/Highways/Short/". $radarstation ."_Highways_Short.gif,"." ridge-radar/Overlays/Cities/Short/". $radarstation ."_City_Short.gif,"." ridge-radar/Warnings/Short/". $radarstation ."/image0.gif,"." ridge-radar/Legend/". $radartype ."/". $radarstation ."/image0.gif"."\n".

"ridge-radar/graphics/black_bgrnd.gif". $timestamp2." overlay=ridge-radar/Overlays/Topo/Short/". $radarstation ."_Topo_Short.jpg,"." ridge-radar/RadarImg/". $radartype ."/". $radarstation ."/image1.gif,"." ridge-radar/Overlays/County/Short/". $radarstation ."_County_Short.gif,"." ridge-radar/Overlays/Rivers/Short/". $radarstation ."_Rivers_Short.gif,"." ridge-radar/Overlays/Highways/Short/". $radarstation ."_Highways_Short.gif,"." ridge-radar/Overlays/Cities/Short/". $radarstation ."_City_Short.gif,"." ridge-radar/Warnings/Short/". $radarstation ."/image5.gif,"." ridge-radar/Legend/". $radartype ."/". $radarstation ."/image1.gif"."\n".

"ridge-radar/graphics/black_bgrnd.gif". $timestamp3." overlay=ridge-radar/Overlays/Topo/Short/". $radarstation ."_Topo_Short.jpg,"." ridge-radar/RadarImg/". $radartype ."/". $radarstation ."/image2.gif,"." ridge-radar/Overlays/County/Short/". $radarstation ."_County_Short.gif,"." ridge-radar/Overlays/Rivers/Short/". $radarstation ."_Rivers_Short.gif,"." ridge-radar/Overlays/Highways/Short/". $radarstation ."_Highways_Short.gif,"." ridge-radar/Overlays/Cities/Short/". $radarstation ."_City_Short.gif,"." ridge-radar/Warnings/Short/". $radarstation ."/image1.gif,"." ridge-radar/Legend/". $radartype ."/". $radarstation ."/image2.gif"."\n".

"ridge-radar/graphics/black_bgrnd.gif". $timestamp4." overlay=ridge-radar/Overlays/Topo/Short/". $radarstation ."_Topo_Short.jpg,"." ridge-radar/RadarImg/". $radartype ."/". $radarstation ."/image3.gif,"." ridge-radar/Overlays/County/Short/". $radarstation ."_County_Short.gif,"." ridge-radar/Overlays/Rivers/Short/". $radarstation ."_Rivers_Short.gif,"." ridge-radar/Overlays/Highways/Short/". $radarstation ."_Highways_Short.gif,"." ridge-radar/Overlays/Cities/Short/". $radarstation ."_City_Short.gif,"." ridge-radar/Warnings/Short/". $radarstation ."/image6.gif,"." ridge-radar/Legend/". $radartype ."/". $radarstation ."/image3.gif"."\n".

"ridge-radar/graphics/black_bgrnd.gif". $timestamp5." overlay=ridge-radar/Overlays/Topo/Short/". $radarstation ."_Topo_Short.jpg,"." ridge-radar/RadarImg/". $radartype ."/". $radarstation ."/image4.gif,"." ridge-radar/Overlays/County/Short/". $radarstation ."_County_Short.gif,"." ridge-radar/Overlays/Rivers/Short/". $radarstation ."_Rivers_Short.gif,"." ridge-radar/Overlays/Highways/Short/". $radarstation ."_Highways_Short.gif,"." ridge-radar/Overlays/Cities/Short/". $radarstation ."_City_Short.gif,"." ridge-radar/Warnings/Short/". $radarstation ."/image2.gif,"." ridge-radar/Legend/". $radartype ."/". $radarstation ."/image4.gif"."\n".

"ridge-radar/graphics/black_bgrnd.gif". $timestamp6." overlay=ridge-radar/Overlays/Topo/Short/". $radarstation ."_Topo_Short.jpg,"." ridge-radar/RadarImg/". $radartype ."/". $radarstation ."/image5.gif,"." ridge-radar/Overlays/County/Short/". $radarstation ."_County_Short.gif,"." ridge-radar/Overlays/Rivers/Short/". $radarstation ."_Rivers_Short.gif,"." ridge-radar/Overlays/Highways/Short/". $radarstation ."_Highways_Short.gif,"." ridge-radar/Overlays/Cities/Short/". $radarstation ."_City_Short.gif,"." ridge-radar/Warnings/Short/". $radarstation ."/image7.gif,"." ridge-radar/Legend/". $radartype ."/". $radarstation ."/image5.gif"."\n".

"ridge-radar/graphics/black_bgrnd.gif". $timestamp7." overlay=ridge-radar/Overlays/Topo/Short/". $radarstation ."_Topo_Short.jpg,"." ridge-radar/RadarImg/". $radartype ."/". $radarstation ."/image6.gif,"." ridge-radar/Overlays/County/Short/". $radarstation ."_County_Short.gif,"." ridge-radar/Overlays/Rivers/Short/". $radarstation ."_Rivers_Short.gif,"." ridge-radar/Overlays/Highways/Short/". $radarstation ."_Highways_Short.gif,"." ridge-radar/Overlays/Cities/Short/". $radarstation ."_City_Short.gif,"." ridge-radar/Warnings/Short/". $radarstation ."/image3.gif,"." ridge-radar/Legend/". $radartype ."/". $radarstation ."/image6.gif"."\n".

"ridge-radar/graphics/black_bgrnd.gif". $timestamp8." overlay=ridge-radar/Overlays/Topo/Short/". $radarstation ."_Topo_Short.jpg,"." ridge-radar/RadarImg/". $radartype ."/". $radarstation ."/image7.gif,"." ridge-radar/Overlays/County/Short/". $radarstation ."_County_Short.gif,"." ridge-radar/Overlays/Rivers/Short/". $radarstation ."_Rivers_Short.gif,"." ridge-radar/Overlays/Highways/Short/". $radarstation ."_Highways_Short.gif,"." ridge-radar/Overlays/Cities/Short/". $radarstation ."_City_Short.gif,"." ridge-radar/Warnings/Short/". $radarstation ."/image8.gif,"." ridge-radar/Legend/". $radartype ."/". $radarstation ."/image7.gif"."\n".

"ridge-radar/graphics/black_bgrnd.gif". $timestamp9." overlay=ridge-radar/Overlays/Topo/Short/". $radarstation ."_Topo_Short.jpg,"." ridge-radar/RadarImg/". $radartype ."/". $radarstation ."/image8.gif,"." ridge-radar/Overlays/County/Short/". $radarstation ."_County_Short.gif,"." ridge-radar/Overlays/Rivers/Short/". $radarstation ."_Rivers_Short.gif,"." ridge-radar/Overlays/Highways/Short/". $radarstation ."_Highways_Short.gif,"." ridge-radar/Overlays/Cities/Short/". $radarstation ."_City_Short.gif,"." ridge-radar/Warnings/Short/". $radarstation ."/image4.gif,"." ridge-radar/Legend/". $radartype ."/". $radarstation ."/image8.gif"."\n".

"ridge-radar/graphics/black_bgrnd.gif". $timestamp10." overlay=ridge-radar/Overlays/Topo/Short/". $radarstation ."_Topo_Short.jpg,"." ridge-radar/RadarImg/". $radartype ."/". $radarstation ."_NCR_0.gif,"." ridge-radar/Overlays/County/Short/". $radarstation ."_County_Short.gif,"." ridge-radar/Overlays/Rivers/Short/". $radarstation ."_Rivers_Short.gif,"." ridge-radar/Overlays/Highways/Short/". $radarstation ."_Highways_Short.gif,"." ridge-radar/Overlays/Cities/Short/". $radarstation ."_City_Short.gif,"." ridge-radar/Warnings/Short/". $radarstation ."_Short_Warnings_0.gif,"." ridge-radar/Legend/". $radartype ."/". $radarstation ."_". $radartype ."_Legend_0.gif";

// Initialize the xxx_XXX_overlayfiles.txt file

        $fh = fopen('ridge-radar/' . $radarstation2 . '_' . $radartype . '_overlayfiles.txt', "w");
        fwrite($fh," ");
        fclose($fh);

// Write a new xxx_XXX_overlayfiles.txt file to the server

        $fh = fopen('ridge-radar/' . $radarstation2 . '_' . $radartype . '_overlayfiles.txt', "w");
        fwrite($fh,$overlayfiles);
        fclose($fh);
?>
