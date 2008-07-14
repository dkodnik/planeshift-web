<?PHP
/*
 * resendemail.php - Original Author: Greg von Beck
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
 * Description : This page accepts a e-mail to resend a account verification
 *               E-Mail to.
 */

  // allow this script to be run directly by the user
  define('psregister',1);

  // tell start.php which page we are rendering
  global $page;
  $page = "resendemail";

  // include first half of webpage
  include_once("start.php");
  
?>

    <div id="content">

<?

if(isset($_GET['forgot']))
  {

    echo "

      <div class=\"forgot\">
        Your password is stored in our DB in an encrypted format, so you will have to generate a new password.<br />
        Use the form below to receive an email which will give you instructions on how to proceed.<br />
      </div>
    
    ";
  }
  else
  {
  
    echo"

      <div class=\"register\">
        <p class=\"yellowtitlebig\">
          Register a new account!
        </p>
        <p>
          Please enter the email address you registered your account <br />
          with to have your verification email resent.
        </p>
      </div>
    ";

  }

?>

      <script language="javascript" src="validationLibrary.js" type="text/javascript">
      </script>

      <script language="javascript" type="text/javascript">
        function validate()
        {
          return isEmail(document.resend.email, "E-Mail", true);
        }
      </script>

<?

if(isset($_GET['forgot']))
{
  echo "      <form name=\"resend\" action=\"processresendemail.php?forgot=yes\" method=\"post\" onsubmit=\"return validate()\">";
} else {
  echo "      <form name=\"resend\" action=\"processresendemail.php\" method=\"post\" onsubmit=\"return validate()\">";
} 

?>
      <hr class="ruler" />

<?PHP

if($_GET['error'] == 'email')
{
  if(isset($_GET['forgot']))
  {
    echo "<div class=\"error\">No validated account with that e-mail found. </div>";
  }
  else
  {
    echo "<div class=\"error\">No account with that e-mail found, or the account has already been validated. </div>";
  }
}

?>
      <div class="email">
        E-Mail
        <input name="email" />
      </div>
        
      <div class="submit">
        <input type="submit" value="Submit" />
      </div>
    </form>
    <p>
    - <a href="index.php">back</a> -
    </p>
  </div>
  
<?

include_once("end.php");

?>
