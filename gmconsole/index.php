<?php
	require_once('config.php');
	require_once('classes/PSAccount.php');

	session_start();

	// get variables
	$email = $_POST['email'];
	$pass = $_POST['pass'];
	$origin = $_GET['origin'];

	// try to log on user
	$err = '';
	if ($_POST["login"]) {
		if (!$email) {
			$err .= "You must specify a login name<br>";
		}
		if (!$pass) {
			$err .= "You must specify a password<br>";
		}

		if ($email && $pass) {
			$account = PSAccount::S_FindOne($email, $pass);
			if (!$account) {
				$err .= "You supplied an invalid password, or the user does not exist<br>";
			} else {
				// check security level
				if ($account->SecurityLevel <= 20) {
                    $err .= "You need a security level of at least 21 (GM1) to enter.<br>";
				} else {
					$_SESSION["__SECURITY_LEVEL"] = $account->SecurityLevel;
					// redircect the user
					if ($origin) {
 						header("Location: " . $origin . ".php");
					} else {
						header("Location: charsearch.php");
					}
				}
			}
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<title>Login</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<link rel="stylesheet" type="text/css" href="<?=$__CSS_RELURI?>"/>
        <link rel="stylesheet" type="text/css" href="<?=$__CSS_ADDON_RELURI?>"/>
	</head>
	<body>
<?php
	// evaluate possible logon errors
	if ($err) {
		echo '<p>' . $err . '</p>';
	}
?>
		<form action="index.php?origin=<?=$origin?>" method="post">
			<table>
				<tr>
					<td>Email address</td>
					<td><input type="text" name="email" style="width:200px" value="<?=$email?>"/></td>
				</tr>
				<tr>
					<td>Password</td>
					<td><input type="password" style="width:200px" name="pass"/>
				</tr>
			</table>
			<input type="submit" name="login" value="Login"/>
		</form>
	</body>
</html>