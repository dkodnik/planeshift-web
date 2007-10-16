<?PHP
/**
 * login.php
 * 
 * Copyright (C) 2001 PlaneShift Team (info@planeshift.it, http://www.planeshift.it)
 * 
 * Credits : Greg von Beck
 *                 ...
 * 
 * This source code is property of the PlaneShift Team; you can use it
 * and/or redistribute it and/or modify it under the terms of the
 * PlaneShift License.
 * 
 * You should have received a copy of the PlaneShift License along with
 * this file; if not, you can get a copy of it at http://www.planeshift.it
 * or writing at info@planeshift.it
 * 
 * Creation Date: 6/15/03
 * Description : this page processes logins, either through the auto login
 *       feature, or the username password pair.  After a good login, user
 *       will be requested to change their password, sent to overview->news,
 *       or sent to the link the used.
 *                   ...
 */
function display_login($error){
	session_unset();
	session_destroy();

	?>       
<SCRIPT language=javascript>
function validate()
{
    if(document.login.username.value.length == 0 || document.login.password.value.length == 0)
    {
        alert("Username and Password are required fields");
        return false;
    }
    return true;
}
</SCRIPT>
<FORM name=login action=index.php method=post name=login onsubmit="return validate()"><BR><BR>
<center><h1>PlaneShift Administration console</h1>
Unauthorized use of this service is strictly forbidden
</center><br>
    <Table align=center>
    <?
	if ($error == 'yes')
    {
		echo"<tr><td><div align='center'><font color='red'>Login failed</font></div></td><tr>";
	}

	?>
        <TR>
            <TD align=right>Username: </TD>
            <TD align=left><input name="username"></TD>
        </TR>
        <TR>
            <TD align=right>Password: </TD>
            <TD align=left><input type=password name=password></TD>
        </TR>
        <TR>
            <TD colspan=2 align=center>
            <input type=Submit value="Login">
        </TD>
        <TR>
            <TD align=right>Remember Me: </TD>
            <TD align=left><input type=checkbox name="autologin"></TD>
        </TD>
        </TR>
    </Table>
    </form>
    <?PHP
}

function login()
{ 
	// set all the used variables to their defaults
	$_SESSION['loggedin'] = '';
	$_SESSION['isAdmin'] = '';

	if (isset($_SESSION['username']) || isset($_COOKIE['username']) || isset($_POST['username']))
    { // try to login	
		if (isset($_COOKIE['username']))
        { // login by cookie
			$_SESSION['username'] = $_COOKIE['username'];
			$_SESSION['password'] = $_COOKIE['password'];
		}
        elseif (isset($_POST['username']))
        {
			$_SESSION['username'] = $_POST['username'];
			$_SESSION['password'] = md5($_POST['password']);
		} 
		// verify the inputted username and password, and setup the variables for that user
		$query = "Select * from accounts where Username = '" . $_SESSION['username'] . "' and password = '".$_SESSION['password']."'";
		$result = mysql_query2($query);

		if (mysql_num_rows($result) > 0)
        {
			$line = mysql_fetch_array($result, MYSQL_ASSOC);

			$_SESSION['loggedin'] = 'yes';
			$_SESSION['uid'] = $line['id'];
			$_SESSION['sec_level'] = $line['security_level'];

			/**
			 * update the lastlogin info
			 * $lastlogtime = strftime("%Y-%m-%d %H:%M:%S");
			 * $query = "UPDATE users SET lasthost='". $_SERVER["REMOTE_ADDR"] . "', lastlog='$lastlogtime'
			 * WHERE username='$username'";
			 * mysql_query2( $query );
			 */
		}
        else
        {
			display_login('yes'); //  error
		}
	}
    else 
    {
        display_login('no'); // no error
    }
}

?>
