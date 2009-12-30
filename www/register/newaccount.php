<?PHP
/*
 * newaccount.php - Original Author: Greg von Beck
 *                  Redesign: John Sennesael
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
 * Creation Date : 10/6/03
 * Description : This page accepts info for a new account.
 */

 // this script is to be called directly.
 define('psregister',1);

 // tell start.php which page we're rendering 
 global $page;
 $page = "newaccount";

 // includes
 include_once("start.php");
 include_once("db_setup.php");

 require_once('recaptchalib.php');

?>

<script language="javascript" src="validationLibrary.js" type="text/javascript"></script>

<script language="javascript" type="text/javascript">
  <!--
  function validate()
  {
    // realname should not be empty
    if(!isString(document.createaccount.realname, "Real Name", true))
    {
        return false;
    }
    //verify email is in the correct format and is not empty
    else if(!isEmail(document.createaccount.email, "E-Mail", true))
    {
        return false;
    }
    //verify that email and email2 are the same so that we know the 
    //e-mail is correct
    else if(document.createaccount.email.value != document.createaccount.email2.value)
    {
        alert("E-Mail fields do not match!");
	document.createaccount.email2.focus();
        return false;
    }
    return true;
  }
  -->
</script>

<div id="content">
  <form name="createaccount" class="newaccount" action="processnewaccount.php" method="post" onsubmit="return validate()">

<?PHP

  if(isset($_GET['error']))
  {
    if ($_GET['error'] == "email") 
    {
      echo "<p class=\"error\">The email you selected is already in use.  Please select another.</p>";
    }
    elseif($_GET['error'] == "username")
    {
      echo "<p class=\"error\">The E-Mail you entered is already associated with an account.</p>";
    }
}

?>
  <p>
    Accounts in PlaneShift are free of charge. No one will ask you money for the creation of an account or for ANY other service related to PlaneShift.<br />
    PlaneShift staff will never ask your password via email.
  </p>

  <table>

    <tr>
      <th>
        Real Name: 
      </th>
	    <td>
        <input name="realname" maxlength="30" />
      </td>
    </tr>

    <tr>
      <th>
        E-Mail: 
      </th>
      <td>
        <input name="email" maxlength="255" />
      </td>
    </tr>

    <tr class="endsection">
      <th>
        Verify E-Mail: 
      </th>
	    <td>
        <input name="email2" maxlength="255" />
      </td>
    </tr>

    <tr class="startsection">
      <td colspan="2">
  	    The following data is optional and 
  	    will be used for statistical purposes.
      </td>
    </tr>

    <tr>
      <th>
        Country
      </th>
      <td>
        <select name="country">
<?

  // Print the countries
	include("settings.php");
  PrintCountries();

?>
        </select>
	    </td>
    </tr>

    <tr>
      <th>
        Gender
      </th>
      <td>
        <select name="gender">
          <option value="N" selected="selected">-- Pick a gender --</option>
          <option value="M">Male</option>
          <option value="F">Female</option>
        </select>
    	</td>
    </tr>
    
    <tr class="endsection">
      <th>
        Year of birth
      </th>
      <td>
        <select name="age" >
          <option value="N" selected="selected">-- Select year --</option>

<?php

  // list year options
  for ($y = date('Y') ; $y >= 1900 ; $y -= 1)
    echo "<option value=\"$y\">$y</option>";

?>

        </select> 
	    </td>
    </tr>
  </table>

  <p>
	  When you click "Create Account" an e-mail will be<br />
	  sent to your address with a link to verify your account.<br />
	  Once you do this you will be able to set your password<br />
	  and access the PlaneShift server.<br />
  </p>

    <p>
	  To avoid bots and spammers, we ask you to type the two words in the picture in the box below.<br />
	  If you cannot read the words clearly, then press the button with two cycling arrows below and you will get two new words. <br>
  </p>
  
  <?php
  echo recaptcha_get_html($publickey, $error);
  ?>

  <p>
    <input type="submit" value="Create Account" />
  </p>
  <p>
  - <a href="index.php">back</a> -
  </p>
</form>
</div>

<?include("end.php");?>
