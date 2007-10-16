<?PHP
/*
 * processnewaccount.php - Author: Greg von Beck
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
?>

<?PHP include "db_setup.php" ?>
<?PHP include "randString.php" ?>

<?PHP
$db_link = mysql_pconnect($db_hostname,
                          $db_username,
                          $db_password);

mysql_select_db($db_name);

//verify username is unique

$query = "select username from accounts where username = '" .
         addslashes(strtolower($_POST['email'])) . "'";

$result = ExecQuery($query);

if (mysql_num_rows($result) > 0)
{
    ?>
         <script language=javascript>
         document.location='newaccount.php?error=username';
         </script>
    <?PHP
    exit();
}
					 

//create verification id

$verificationid = randString();

//save to database

$age = $_POST['age'];
if ($age=="N")
	$age="''";
	
$query = "Insert into accounts (realname, username, verificationid, " .
                               "created_date, status, country, gender, birth)" .
	 "values('" . addslashes(strtolower($_POST['realname'])) . "', ".
	        "'" . addslashes(strtolower($_POST['email'])) . "', " .
		"'" . $verificationid . "', " .
		"'" . date('Y/m/d', mktime()) . "', 'U'," .
		"'" . addslashes($_POST['country']) . 
		"','" . $_POST['gender'] . "'," . $age. ")";

		echo $query;

ExecQuery($query);
						    


// send email

include 'sendverificationemail.php';
sendVerificationEmail( $_POST['email'], $verificationid);

?>

<HTML>
<SCRIPT language=javascript>
document.location="index.php?action=reg";
</SCRIPT>
</HTML>
