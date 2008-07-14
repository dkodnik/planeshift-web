<?php
/*
 * verifyaccount.php - Author: Greg von Beck
 *             Redesigned by: John Sennesael
 *
 * Copyright (C) 2001 PlaneShift Team (info@planeshift.it,
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
 * Creation Date : 10/10/03
 * Description : This page is the verification emails link to.  It passes the 
 *               username and verificationid to the next page.  It also accepts
 *               and verifies a password.
 */

  // let start.php know which page we're rendering.
  global $page;
  $page = "verifyaccount";

  // users are allowed to call this page directly.
  define('psregister',1);

  // site, menu, etc...
  include_once("start.php");
  
?>

<div id="content">

<?php

  if(isset($_GET['forgot']))
  {
    echo "<p class=\"yellowtitlebig\">Password Change!</p>";
  } else {
    echo "<p class=\"yellowtitlebig\">Register a new account!</p>";
  }

?>

  <script language="javascript" type="text/javascript">
    <!--
    function validate()
    {
      if(document.password.password1.value.length < 6)
      {
        alert("Your Password must be at least 6 characters");
      	document.password.password1.focus();
        return false;
      }
      if(document.password.password1.value.length > 32)
      {
        alert("Your Password must be no more than 32 charaters");
      	document.password.password1.focus();
        return false;
      }
      if(document.password.password2.value.length <= 0)
      {
        alert("You must retype the password");
      	document.password.password2.focus();
        return false;
       }
      if(document.password.password1.value != document.password.password2.value)
      {
        alert("The passwords must match exactly");
	      document.password.password2.focus();
      	return false;
      }
      return true;
    }
  -->
  </script>

<?php

  if(isset($_GET['forgot']))
  {
    echo "<form name=\"password\" action=\"processverifyaccount.php?forgot=yes\" method=\"post\" onsubmit=\"return validate()\">";
  } else {
    echo "<form name=\"password\" action=\"processverifyaccount.php\" method=\"post\" onsubmit=\"return validate()\">";
  } 

?>

<p>

<?
  
  if(isset($_GET['forgot']))
  {
    echo "Enter the new password you want to use.";
  } else {
    echo "Please choose a password to use with this account<br/>";
    echo "<span style=\"color: red;\">Please note: Do not enter the verification ID here.</span>";
  }

?>

</p>

  <div class="password">
  <table>
    <tr>
      <th>Password: </th>
      <td>
        <input type="password" name="password1" />
      </td>
    </tr>
    <tr>
      <th>Retype Password: </th>
      <td>
        <input type="password" name="password2" />
      </td>
    </tr>
  </table>
  </div>
  
  <input type="submit" value="Set Password" />

<?php
  
  // Strip html characters to prevent xss attacks.
  $user_name = htmlspecialchars( $_GET['username'] );
  $verification_id = htmlspecialchars ( $_GET['verificationid'] );
 
  echo "<input type=\"hidden\" name=\"email\" value=\"$user_name\" />";
  echo "<input type=\"hidden\" name=\"verificationid\" value=\"$verification_id\" />";

?>  
  </form>
</div>

<?php
  include("end.php");
?>
