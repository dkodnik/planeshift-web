<?PHP
/*
 * index.php - Original Author: Greg von Beck
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
 * Creation Date : 10/6/03
 * Description : This page is for the creating and activation of player accounts
 */
 
  // global define to check against people trying to call 
  // scripts directly they shouldn't be calling directly.  
  define('psregister',1);

  include_once("start.php"); 

  echo "<div id=\"content\">";
  
  // Inform the user of stuff
  if($_GET['action'] == "reg")
  {
    echo "An e-mail has been sent to your address with a link for activation<br /><br />";   
  }
  else if($_GET['action'] == "active")
  {
    echo "Your account is now active<br /><br />";      
  }
  else if($_GET['action'] == "passchange")
  {
    echo "Your password has been changed.<br /><br />";      
  }
?>

      <div class="yellowtitlebig">
        Create an account!
      </div>

      <div class="registerlinks">
        <p>
          In order to create an account use the links below.
        </p>
        <ul>
          <li>
            <a href="newaccount.php">Create New Account</a>
          </li>
          <li>
            <a href="resendemail.php">Resend Verification E-Mail</a>
          </li>
          <li>
            <a href="resendemail.php?forgot=yes">Forgot my password</a>
          </li>
        </ul>
      </div>
    
    </div>

<?
include("end.php");
?>
