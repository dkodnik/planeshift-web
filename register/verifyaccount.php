<?PHP
/*
 * verifyaccount.php - Author: Greg von Beck
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
 * Description : This page is the verification emails link to.  It passes the 
 *               username and verificationid to the next page.  It also accepts
 *               and verifies a password.
 */
?>


<?include("start.php");

if(isset($_GET['forgot']))
{
    echo "<p class=yellowtitlebig>Password Change!</p>";
} else {
    echo "<p class=yellowtitlebig>Register a new account!</p>";
}
?>

    <p class="yellowtitlebig">&nbsp;</p>



<script language=javascript>
function validate()
{
    if(document.password.password1.value.length < 6)
    {
        alert("Your Password must be at least 6 characters");
	document.password.password1.focus();
        return false;
    }
    if(document.password.password1.value.length > 32)
    {
        alert("Your Password must be no more than 32 charaters");
	document.password.password1.focus();
        return false;
    }
    if(document.password.password2.value.length <= 0)
    {
        alert("You must retype the password");
	document.password.password2.focus();
        return false;
    }
    if(document.password.password1.value != document.password.password2.value)
    {
        alert("The passwords must match exactly");
	document.password.password2.focus();
	return false;
    }
    return true;
}
</script>

<?
if(isset($_GET['forgot']))
{
?>
<FORM name=password action="processverifyaccount.php?forgot=yes" method=post onsubmit="return validate()">
<?
} else {
?>
<FORM name=password action=processverifyaccount.php method=post onsubmit="return validate()">
<? } ?>


<Table>
    <TR>
        <TD colspan=2>
<?
if(isset($_GET['forgot']))
{
    echo "Enter the new password you want to use.<BR>";
} else {
    echo "Please choose a password to use with this account<BR><font color='red'>Please note: Do not enter the verification ID here</font>";
}
?>
		      <BR>
	</TD>
    </TR>
    <TR>
        <TH align=right>Password: </TH>
	<TD><input type=password name=password1></TD>
    </TR>
    <TR>
        <TH align=right>Retype Password: </TH>
	<TD><input type=password name=password2></TD>
    </TR>
    <TR>
    	<TD colspan=2 align=center>
	    <BR>
	    <input type=submit value="Set Password">
	</TD>
    </TR>
</TABLE>
<input type=hidden name="email" value="<?=$_GET['username']?>">
<input type=hidden name="verificationid" value="<?=$_GET['verificationid']?>">


</FORM>


<?include("end.php");?>