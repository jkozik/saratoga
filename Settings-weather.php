<?php
############################################################################
# A Project of TNET Services, Inc. and Saratoga-Weather.org (WD-World-ML template set)
############################################################################
#
#	Project:	Sample Included Website Design
#	Module:		Settings-weather.php
#	Purpose:	Provides the Site Settings Used Throughout the Site
# 	Authors:	Kevin W. Reed <kreed@tnet.com>
#				TNET Services, Inc.
#               Ken True <webmaster@saratoga-weather.org>
#               Saratoga-Weather.org
#
# 	Copyright:	(c) 1992-2007 Copyright TNET Services, Inc.
############################################################################
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
# Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA
############################################################################
#	This document uses Tab 4 Settings
############################################################################
global $SITE;
#---------------------------------------------------------------------------
#  required settings for Virtual Weather Station (VWS) software
#---------------------------------------------------------------------------
$SITE['WXtags']		= 'VWStags.php';  // for weather variables
$SITE['ajaxScript']     = 'ajaxVWSwx.js'; // for AJAX enabled display
#$SITE['wflashdir']      = './wflash/Data/';  // relative directory for WeatherFlash wflash.txt and wflash2.txt files
$SITE['wflashdir']      = '../WxFlash/Data/';  // relative directory for WeatherFlash wflash.txt and wflash2.txt files
$SITE['trendsPage']     = 'VWS-trends-inc.php'; // VWS-specific trends page
$SITE['graphImageDir']  = './vwsimages/';  // directory location for graph images with trailing /
$SITE['NOAAdir']        = './noaa/';   // relative location of the NOAA report files
#
# Weather Station sensors and options for dashboard
#
# $SITE['conditionsMETAR'] = 'KSJC';  // set to nearby METAR for current conditions icon/text
$SITE['conditionsMETAR'] = 'KDPA'; // dist=11 mi (18 km), dir=NW, Chicago/Dupage, Illinois, USA elev=231, dated=09-NOV-04
# comment out conditionsMETAR to use built-in VWS icon/text conditions setting for ^climate_ccond1^ and ^climate_icon1^

$SITE['DavisVP']		= true;  // set to false if not a Davis VP weather station
$SITE['UV']				= true;  // set to false if no UV sensor
$SITE['SOLAR']			= true;  // set to false if no Solar sensor
#
##########################################################################
# end of configurable settings
#
// default settings needed for various pages when the weather software plugin is not installed.
// do NOT change these
$SITE['WXsoftware']     = 'VWS';
$SITE['WXsoftwareURL']  = 'http://www.ambientweather.com/virtualstation.html';
$SITE['WXsoftwareLongName'] = 'Virtual Weather Station';
$SITE['ajaxDashboard']  = 'ajax-dashboard.php';
$SITE['showSnow']	= false;   // snow input not recorded by VWS
$SITE['showSnowTemp'] 	= -60;	   // disabled setting
?>
