<?PHP
/*
 * processresendemail.php - Author: Greg von Beck
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
?>

<?PHP include "db_setup.php" ?>

<?PHP
// Establish a db connection
$db_link = mysql_pconnect($db_hostname,
                          $db_username,
			  $db_password);

mysql_select_db($db_name);

// Get the users db entry
$query = "Select * from accounts where status = 'U' and username = '" . addslashes($_POST['email']) . "'";

if(isset($_GET['forgot']))
{
  $query = "Select * from accounts where status = 'A' and username = '" . addslashes($_POST['email']) . "'";
}

$result = ExecQuery($query);

// Get the verification ID and send a new email
if(mysql_num_rows($result) > 0)
{
    $line = mysql_fetch_array($result, MYSQL_ASSOC);

    include 'sendverificationemail.php';

  if(isset($_GET['forgot']))
  {
    sendVerificationEmail($_POST['email'],  $line['verificationid'],"yes");
  } else {
    sendVerificationEmail($_POST['email'],  $line['verificationid'],"");
  }
}
else
{
    ?>
    <Script>
    document.location = "resendemail.php?error=email";
    </script>
    <?PHP
}
?>

<Script>
document.location = "index.php";
</script>

