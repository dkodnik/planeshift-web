<?
/*
 * start.php - Author: Christian Svensson
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
?>         

<HTML>
<!-- #BeginTemplate "/Templates/main.dwt" --> 
<HEAD>
<!-- #BeginEditable "doctitle" --> 
<TITLE>PlaneShift - A 3D Fantasy MMORPG</TITLE>
<!-- #EndEditable --> 
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">
<link rel="stylesheet" href="http://www.planeshift.it/newstyles.css" type="text/css">
</HEAD>
<BODY BGCOLOR=#052F2E text="57B9CB" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" link="#FFFFFF" vlink="#FFFFFF" alink="#FFFFFF">
<table width="997" border="0" cellpadding="0" cellspacing="0">
  <!--DWLayoutTable-->
  <tr> 
    <td width="133" rowspan="3" valign="top"><img src="http://www.planeshift.it/pix/menu/left.jpg" width="133" height="574"></td>
    <td height="78" colspan="2" valign="top"><img src="http://www.planeshift.it/pix/menu/neshift.jpg" width="328" height="78"></td>
    <td width="536" valign="top" background="http://www.planeshift.it/pix/menu/up.jpg"> 
      <table width="75%" border="0">
        <tr> 
          <td class="yellowtitlebig"><a href="http://www.planeshift.it/about.html" class="yellowtitlebig"><b>About</b></a></td>
          <td class="yellowtitlebig"><a href="http://www.planeshift.it/" class="yellowtitlebig">News</a></td>
          <td class="yellowtitlebig"><a href="http://www.planeshift.it/screenshots.html" class="yellowtitlebig">Pics</a></td>
          <td class="yellowtitlebig"><a href="http://www.planeshift.it/download.html" class="yellowtitlebig">Download</a></td>
        </tr>
        <tr> 
          <td class="yellowtitlebig"><a href="http://www.planeshift.it/setting.html" class="yellowtitlebig">Setting</a></td>
          <td class="yellowtitlebig"><a href="http://www.planeshift.it/guide/en/index.html" class="yellowtitlebig">Player 
            Guide</a></td>
          <td class="yellowtitlebig"><a href="http://www.planeshift.it/recruitment.html" class="yellowtitlebig">Help 
            Us!</a></td>
          <td class="yellowtitlebig"><a href="http://www.planeshift.it/forums.html" class="yellowtitlebig">Community</a></td>
        </tr>
      </table>
    </td>
  </tr>
  <tr> 
    <td width="208" height="461" valign="top"> 
      <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <!--DWLayoutTable-->
        <tr> 
          <td width="208" height="152" valign="middle" nowrap background="http://www.planeshift.it/pix/menu/central.jpg"> 
            <p>&nbsp;</p>
            <p align="right"><!-- #BeginEditable "center" --><!-- #EndEditable --></p>
          </td>
        </tr>
        <tr> 
          <td height="178" valign="top"> <!-- #BeginEditable "tip" --> 
            <p>&nbsp;</p>
            <!-- #EndEditable --> </td>
        </tr>
        <tr> 
          <td height="131" valign="top"> 
            <!--DWLayoutEmptyCell-->
            &nbsp;</td>
        </tr>
      </table>
    </td>
    <td colspan="2" rowspan="3" valign="top"> 
      <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <!--DWLayoutTable-->
        <tr> 
          <td width="647" height="511" valign="top"> 
            <!--DWLayoutEmptyCell-->
            <!-- #BeginEditable "body" --> 
            
<?
if($_GET['error'] == "db")
{
    echo "<font color=red><B>A database error ocurred, please report this</B></FONT><br>";
}
?>