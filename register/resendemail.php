<?PHP
/*
 * resendemail.php - Author: Greg von Beck
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
?>


<?include("start.php");

if(isset($_GET['forgot']))
{
 echo "Since your password is stored in our DB in encrypted format, the only option is to generate a new password.<br>";
 echo "Using the form below you will receive a verification email to reset your password.<br>";

} else {
  ?>
 <p class=yellowtitlebig>Register a new account!</p>
 
         <BR>Please Enter the E-Mail you would like to have an<BR>
        account verification email resent to.<BR>
<?
}
?>
    <p class="yellowtitlebig">&nbsp;</p>

<script language=javascript src=validationLibrary.js>
</script>

<script language=javascript>
function validate()
{
    return isEmail(document.resend.email, "E-Mail", true);
}
</script>

<?
if(isset($_GET['forgot']))
{
?>
<FORM name=resend action="processresendemail.php?forgot=yes" method="post" onsubmit="return validate()">
<?
} else {
?>
<FORM name=resend action="processresendemail.php" method="post" onsubmit="return validate()">
<? } ?>

<Table>
    <TR><TD colspan=2>

    </TD></TR>
    <TR><TD colspan=2><HR></TD></TR>
    <?PHP
    if($_GET['error'] == 'email')
    {
    	echo "<TR><TD colspan=2><font color=red><B>No account with that e-mail found or account has already been validated. </B></FONT><BR></TD></TR>";
    }
    ?>
    <TR>
        <TH align=right>E-Mail</TH>
	<TD><input name="email"></TD>
    </TR>
    <TR><TD colspan=2 align=center><BR><input type=submit value="Submit"></TD></TR>
</TABLE>
</FORM>

<?include("end.php");?>
