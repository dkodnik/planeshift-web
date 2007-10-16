<?PHP
/*
 * processverifyaccount.php - Author: Greg von Beck
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
?>

<?PHP include "db_setup.php" ?>

<?PHP
if($_POST['verificationid'] == $_POST['password1'])
{
    exit ("<font color='red'>Do NOT enter the verfication code as the password</font>");
}

$db_link = mysql_pconnect($db_hostname,
                          $db_username,
			  $db_password);

mysql_select_db($db_name);


$query = "Select * from accounts where" .
         " username='" . addslashes(strtolower($_POST['email'])) . "'" .
         " and verificationid = '" . addslashes($_POST['verificationid']) . "'";


$strstatus = "U";
if(isset($_GET['forgot'])) {
    $strstatus = "A";
}

$query = $query . " and status = '$strstatus'";

$result = ExecQuery($query);
	 
if (mysql_num_rows($result) > 0)
{
    $query = "Update accounts set password = md5('" . $_POST['password1'] . "') " .
             ",status = 'A' " .
             "where username = '" . addslashes(strtolower($_POST['email'])) . "' " .
	     "and verificationid = '" . addslashes($_POST['verificationid']) . "' " .
	     "and status = '$strstatus'";

    ExecQuery($query);

    ?>
    <Script>
    document.location = "index.php?action=passchange";
    </script>
    <?PHP
}
else
{
    $query = "Select * from accounts where" .
             " username='" . addslashes(strtolower($_POST['email'])) . "'" .
             " and status = 'A'";

    $result = ExecQuery($query);

    if(mysql_num_rows($result) > 0)
    {
        ?>
	<B>Your Account Has Already Been Validated</B>
	<?PHP
    }
    else
    {
        ?>
        <FONT COLOR=red><B>Your Account Has NOT Been Validated!!</B></FONT>
        <?PHP
    }
}
?>
<BR><BR>
<a href="index.php">Return to Account Registration</a>
