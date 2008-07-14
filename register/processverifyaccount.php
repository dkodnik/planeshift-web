<?php
/*
 * processverifyaccount.php - Author: Greg von Beck
 *                     Redesigned by: John Sennesael
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
 * Description : This page writes new password into database if username and
 *               validation id are correct
 */

  // script can be called directly by user
  define('psregister',1);

  // includes
  include_once("usermsg.php");
  include_once("db_setup.php");
  include_once("randString.php");

  // make sure the password != verification id
  $verificationid = $_POST['verificationid'];
  if($verificationid == $_POST['password1'])
  {
    UserMsg("
    <div class=\"error\">Do NOT enter the verification code as the password.</div>
    <p>
    - <a href=\"verifyaccount.php?verificationid={$verificationid}\"> Back </a> -
    "); 
    exit();
  }

  // connect to database
  $db_link = mysql_pconnect($db_hostname,
                            $db_username,
	                          $db_password);
  // error handling
  if (!$db_link)
  {
    UserMsg("
      <div class=\"error\">
        Oops! There was a problem connecting to the database.
        <p>Hopefully this will be fixed soon<sup>(tm)</sup>.</p>
      </div>");
  }
  mysql_select_db($db_name);

  // find account in db
  $query = "Select * from accounts where" .
           " username='" . mysql_real_escape_string(strtolower($_POST['email'])) . "'" .
           " and verificationid = '" . mysql_real_escape_string($_POST['verificationid']) . "'";

  // forgot password?
  $strstatus = "U";
  if(isset($_GET['forgot'])) {
    $strstatus = "A";
  }
  $query = $query . " and status = '$strstatus'";
  print $query; 
  // run db query
  $result = ExecQuery($query);

  // did we find an account?
  if (mysql_num_rows($result) > 0)
  {
    // this line generates a new verificcation ID string
    $newverify = mysql_real_escape_string(randString());
    // this sets the password, and commits the above generated new verification string to the DB
    $query = "Update accounts set password = md5('" . mysql_real_escape_string($_POST['password1']) . "') " .
             ",status = 'A', verificationid = '$newverify' " .
             "where username = '" . mysql_real_escape_string(strtolower($_POST['email'])) . "' " .
      	     "and verificationid = '" . mysql_real_escape_string($_POST['verificationid']) . "' " .
	           "and status = '$strstatus'";
    ExecQuery($query);
    // Show message to user
    UserMsg("
      <div class=\"bigyellowtext\">
      Thank you. Your account has been updated.
      </div>
      <p>
      - <a href=\"index.php\"> back </a> -
      </p>
    ");
  }
  else
  {
    // was the account already validated maybe?
    $query = "Select * from accounts where" .
             " username='" . mysql_real_escape_string(strtolower($_POST['email'])) . "'" .
             " and status = 'A'";
    $result = ExecQuery($query);
    if(mysql_num_rows($result) > 0)
    {
      UserMsg("
        <div class=\"error\">
          This account validation or password change link was already used!
        </div>
        <p>
        - <a href=\"index.php\"> back </a> -
        </p>
      ");
      exit();
    }
    else
    {
      // The account was not already validated
      // and we did not find the verification id at all.
      // Either it was deleted from the database, or some
      // user has been tampering, or some other mystery has occured.
      UserMsg("
        <div class=\"error\">
          Your account was <strong>NOT</strong> validated!
        </div>
      ");
    }
  }
?>
