<?
/*
 * start.php - Original Author: Christian Svensson
 *             Redesigned by: John Sennesael
 *
 * Copyright (C) 2004 PlaneShift Team (info@planeshift.it,
 * http://www.planeshift.it)
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation (version 2 of the License)
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * Description : This page contains the starting stuff needed for the site
 *                  to look nice
 */

  // simple security check
  if (!defined('psregister')) die ('You are not allowed to run this script directly.');

?>         
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

  <head>
    <title>PlaneShift - A 3D Fantasy MMORPG</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <link rel="stylesheet" href="css/master.css" type="text/css" />

<?

  global $page;
  if ($page == "newaccount")
  {
    echo "    <link rel=\"stylesheet\" href=\"css/newaccount.css\" type=\"text/css\" />\n";
  }
  else if ($page == "resendemail")
  {
    echo "    <link rel=\"stylesheet\" href=\"css/resendemail.css\" type=\"text/css\" />\n";
  }
  else if ($page == "verifyaccount")
  {
    echo "    <link rel=\"stylesheet\" href=\"css/verifyaccount.css\" type=\"text/css\" />\n";
  }

?>
    <script type="text/javascript" src="http://www.planeshift.it/treemenu.js"></script>
  </head>

  <body>

    <div id="page">
      <div id="centerbox">      
      <div id="header">
        <a href="http://www.planeshift.it">
          <img src="http://www.planeshift.it/graphics/pslogo2.gif" alt="" />
        </a>
      </div>
            
<?

if($_GET['error'] == "db")
{
    echo "<div id=\"toperror\">A database error ocurred, please report this</div>";
}

include_once("menu.php");

?>
