<?php
############################################################################
#
#   Module:     wxreportcommon.php
#   Purpose:    Set values common to all of the reports
#   Author:     Murry Conarroe <murry@murry.com>
#              
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
# Settings common to the detail and summary reports
############################################################################
$showGizmo = true;  // set to false to exclude the gizmo 
$path_dailynoaa = $SITE['NOAAdir'];           # Location of dailynoaareport*.htm files
$path_climatedata = "./";           # WD only -- Location of climatedataout*.html files
$first_year_of_noaadata = "2010";  # First year of dailynoaareport data that is available
$first_year_of_climatedata = "2002";  # WD only -- First year of climatedataout data that is available
$show_trends = true;                # Set to true if want trend arrows displayed on summary reports
$show_no_links = false;            # If set to true, this overrides all other link settings
$show_detail_links = true;                # Set to true to show detail links
$show_summary_links = true;               # Set to true to show monthly summary links
$show_season_links = true;                # Set to true to show season summary links

$show_temp_links = true;           # Set to true to include link to the Temperature reports
$show_rain_links = true;           # Set to true to include link to the Rain reports
$show_wind_links = true;           # Set to true to include link to the Wind reports
$show_windrun_links = true;        # Set to true to include link to the windrun reports
$show_degree_links = true;         # Set to true to include link to the Degree Days reports

$show_snow_links = true;           # WD only -- Set to true to include link to the Snow reports
$show_snowdepth_links = true;      # WD only -- Set to true to include link to the Snow Depth reports
$show_baro_links = true;           # WD only -- Set to true to include link to the Barometric Pressure reports
$show_sunhours_links = true;       # WD only -- Set to true to include link to the Sun Hours reports
$show_solar_links = true;          # WD only -- Set to true to include link to the Max Solar energy reports
$show_solarkwh_links = true;       # WD only -- Set to true to include link to the Solar kwh energy reports
$show_uv_links = true;             # WD only -- Set to true to include link to the UV reports
$show_dewpoint_links = true;       # WD only -- Set to true to include link to the dewpoint reports
$show_wetbulb_links = true;        # WD only -- Set to true to include link to the wet bulb reports
$show_soiltemp_links = false;      # WD only -- Set to true to include link to the soil temp reports 

$show_today = false; # WD only --- Set to true if you want info to be displayed for today. Info for today will come from custom tags in your testtags file.

# The settings below need to be changed only if you change the name of any of the detail, summary, or season files
$tempsummaryfile_name = "wxtempsummary.php"; # Name of temp summary page
$rainsummaryfile_name = "wxrainsummary.php"; # Name of rain summary page
$windsummaryfile_name = "wxwindsummary.php"; # Name of wind summary page
$snowsummaryfile_name = "wxsnowsummary.php"; # Name of snow summary page
$snowdepthsummaryfile_name = "wxsnowdepthsummary.php"; # Name of snow depth summary page
$barosummaryfile_name = "wxbarosummary.php"; # Name of barometric pressure summary page
$degreesummaryfile_name = "wxdegreesummary.php"; # Name of degree days summary page
$sunhourssummaryfile_name = "wxsunhourssummary.php"; # Name of sunshine hours summary page
$solarsummaryfile_name = "wxsolarsummary.php"; # Name of max solar energy summary page
$solarkwhsummaryfile_name = "wxsolarkwhsummary.php"; # Name of total solar energy summary page
$uvsummaryfile_name = "wxuvsummary.php"; # Name of uv summary page
$dewpointsummaryfile_name = "wxdewpointsummary.php"; # Name of dewpoint summary page
$wetbulbsummaryfile_name = "wxwetbulbsummary.php"; # Name of wetbulb summary page
$windrunsummaryfile_name = "wxwindrunsummary.php"; # Name of windrun summary page
$soiltempsummaryfile_name = "wxsoiltempsummary.php"; # Name of soil temp summary page

$tempdetailfile_name = "wxtempdetail.php"; # Name of temp detail page
$raindetailfile_name = "wxraindetail.php"; # Name of rain detail page
$winddetailfile_name = "wxwinddetail.php"; # Name of wind detail page
$snowdetailfile_name = "wxsnowdetail.php"; # Name of snow detail page
$snowdepthdetailfile_name = "wxsnowdepthdetail.php"; # Name of snow depth detail page
$barodetailfile_name = "wxbarodetail.php"; # Name of barometric pressure detail page
$degreedetailfile_name = "wxdegreedetail.php"; # Name of degree days detail page
$sunhoursdetailfile_name = "wxsunhoursdetail.php"; # Name of sunshine hours detail page
$solardetailfile_name = "wxsolardetail.php"; # Name of max solar energy detail page
$solarkwhdetailfile_name = "wxsolarkwhdetail.php"; # Name of solar kwh detail page
$uvdetailfile_name = "wxuvdetail.php"; # Name of uv detail page
$dewpointdetailfile_name = "wxdewpointdetail.php"; # Name of dewpoint detail page
$wetbulbdetailfile_name = "wxwetbulbdetail.php"; # Name of wetbulb detail page
$windrundetailfile_name = "wxwindrundetail.php"; # Name of windrun detail page
$soiltempdetailfile_name = "wxsoiltempdetail.php"; # Name of soil temp detail page

$tempseasonfile_name = "wxtempseason.php"; # Name of temp season page
$rainseasonfile_name = "wxrainseason.php"; # Name of rain season page
$windseasonfile_name = "wxwindseason.php"; # Name of wind season page
$snowseasonfile_name = "wxsnowseason.php"; # Name of snow season page
$snowdepthseasonfile_name = "wxsnowdepthseason.php"; # Name of snow depth season page
$baroseasonfile_name = "wxbaroseason.php"; # Name of barometric pressure season page
$degreeseasonfile_name = "wxdegreeseason.php"; # Name of degree days season page
$sunhoursseasonfile_name = "wxsunhoursseason.php"; # Name of sunshine hours season page
$solarseasonfile_name = "wxsolarseason.php"; # Name of max solar energy season page
$solarkwhseasonfile_name = "wxsolarkwhseason.php"; # Name of solar kWh  season page
$uvseasonfile_name = "wxuvseason.php"; # Name of uv season page
$dewpointseasonfile_name = "wxdewpointseason.php"; # Name of dewpoint season page
$wetbulbseasonfile_name = "wxwetbulbseason.php"; # Name of wetbulb season page
$windrunseasonfile_name = "wxwindrunseason.php"; # Name of windrun season page    
$soiltempseasonfile_name = "wxsoiltempseason.php"; # Name of soil temp season page  
            
@include("wxreport-include-V2lang.php");        
?>
