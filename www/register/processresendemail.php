<?php
/*
 * processresendemail.php - Author: Greg von Beck
 *                   Redesigned by: John Sennesael
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
 * Description : This page verifies e-mail is for a unverified account and
 *               then sends out the verification email.
 */

  // allow this script to be directly run by users
  define ('psregister',1);
 
  // includes
  include_once("db_setup.php");
  include_once("usermsg.php");

  require_once('recaptchalib.php');

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

  // establish a db connection.
  $db_link = mysql_pconnect($db_hostname,
                            $db_username,
	                          $db_password);

  // show an error if failed.
  if (!$db_link)
  {
    include_once('start.php');
    echo "
    <div id=\"content\">
      <div class=\"error\">
        Oops! There was a problem connecting to the database.
        <p>Hopefully this will be resolved soon<sup>(tm)</sup>.
      </div>
    </div>
    ";
    include_once('end.php');
    die('db error');
  }

  // select database.
  mysql_select_db($db_name);

  // get user email 
  $email = mysql_real_escape_string($_POST['email']);

  // Get the users db entry
  $query = "Select * from accounts where status = 'U' and username = '" . $email . "'";
  if(isset($_GET['forgot']))
  {
    $query = "Select * from accounts where status = 'A' and username = '" . $email . "'";
  }
  $result = ExecQuery($query);

  // Get the verification ID and send a new email
  if(mysql_num_rows($result) > 0)
  {
    $line = mysql_fetch_array($result, MYSQL_ASSOC);
    include_once('sendverificationemail.php');
    if(isset($_GET['forgot']))
    {
      sendVerificationEmail($email,  $line['verificationid'],"yes");
    } else {
      sendVerificationEmail($email,  $line['verificationid'],"");
    }
  }
  else
  {
    if(isset($_GET['forgot']))
    {
      header('Location: resendemail.php?error=email&forgot=yes');
    }
    else
    {
      header('Location: resendemail.php?error=email');
    }
    exit();
  }

  // redirect user
  UserMsg("
  <div class=\"yellowtitlebig\">You should receive an email at: {$email} with the account details.</div>
  <p>
    - <a href=\"index.php\">Back</a> -
  </p>
  ");
?>

</script>

