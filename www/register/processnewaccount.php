<?php
/*
 * processnewaccount.php - Author: Greg von Beck
 *                  Redesigned by: John Sennesael
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
 * Description : This page enters account info into the database and sends out
 *               an email with a validation link.
 */

  // This script is to be run directly by the user.
  define('psregister',1);

  // includes
  include_once("db_setup.php");
  include_once("randString.php");

  require_once('recaptchalib.php');
  
  
  // attempt to connect to database
  $db_link = mysql_pconnect($db_hostname,
                            $db_username,
                            $db_password);

  // show error if captcha failed
  $resp = recaptcha_check_answer ($privatekey,
								$_SERVER["REMOTE_ADDR"],
								$_POST["recaptcha_challenge_field"],
								$_POST["recaptcha_response_field"]);

  if (!$resp->is_valid) {
    include_once('start.php');
    echo "<div id=\"content\">";
    echo "<div class=\"error\">";
    echo "The human verification check didn't pass, the two words entered are wrong. Please <a href=javascript:history.back()>re-try</a>.</p>";
    echo "</div>";
    echo "</div>";
    include_once('end.php');
	exit;
	// $error = $resp->error;
  }
  
  // show error if connection failed
  if (!$db_link)
  {
    include_once('start.php');
    echo "<div id=\"content\">";
    echo "<div class=\"error\">";
    echo "Oops! There was a problem connecting to the database.<p>Hopefully this will be resolved soon<sup>(tm)</sup>.</p>";
    echo "</div>";
    echo "</div>";
    include_once('end.php');
    die('db error');
  }
  mysql_select_db($db_name);

  // verify username is unique.
  $query = "select username from accounts where username = '" . mysql_real_escape_string(strtolower($_POST['email'])) . "'";
  $result = ExecQuery($query);
  if (mysql_num_rows($result) > 0)
  {
    Header("Location:newaccount.php?error=username");
    exit();
  }
           
  // create verification id.
  $verificationid = randString();

  // save to database.
  $age = mysql_real_escape_string($_POST['age']);
  if ($age=="N")
  {
    $age="''";
  }
  $query = "Insert into accounts (realname, username, verificationid, " .
           "created_date, status, country, gender, birth)" .
           "values('" . mysql_real_escape_string(strtolower($_POST['realname'])) . "', ".
           "'" . mysql_real_escape_string(strtolower($_POST['email'])) . "', " .
           "'" . mysql_real_escape_string($verificationid) . "', " .
           "'" . date('Y/m/d', mktime()) . "', 'U'," .
           "'" . mysql_real_escape_string($_POST['country']) . 
           "','" . mysql_real_escape_string($_POST['gender']) . "'," . $age. ")";
  ExecQuery($query);
                
  // send email.
  include 'sendverificationemail.php';
  sendVerificationEmail(addslashes($_POST['email']), $verificationid);

  // redirect user.
  Header("Location:index.php?action=reg");

?>
