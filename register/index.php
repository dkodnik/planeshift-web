<?PHP
/*
 * index.php - Author: Greg von Beck
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
?>

<?include("start.php");?>
<?
// Inform the user of stuff
if($_GET['action'] == "reg")
{
    echo "An e-mail has been sent to your address with a link for activation<br><br>";   
}
else if($_GET['action'] == "active")
{
    echo "Your account is now active<br><br>";      
}
else if($_GET['action'] == "passchange")
{
    echo "Your password has been changed.<br><br>";      
}

?>
            <p class="yellowtitlebig">Create an account!</p>
            <p class="yellowtitlebig">&nbsp;</p>


            <b><font color=white>Important Notice:</font></b> If you migrated your character from MB release, please do the following: Create an account (using link below) with the same email you used
            to migrate your MB char. Download the game, and use the created account to log in. Press New Character, enter the first name exactly as it was in MB, enter any last name. If email and
            first name match the migrated char, you will get the trias and item migrated from MB. <br><br><br>

            <A href="newaccount.php">Create New Account</a><BR>
            <BR>
            <A href="resendemail.php">Resend Verification E-Mail</A><BR>
            <BR>
            <A href="resendemail.php?forgot=yes">Forgot my password</A>

            <BR><BR>
<?
include("end.php");
?>
