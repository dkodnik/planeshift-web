<?php

    require_once("config.php");
    require_once('classes/Navigation.php');
    require_once("classes/PSCharacter.php");

	session_start();

	// is the user logged in?
	if (!isset($_SESSION["__SECURITY_LEVEL"])) {
		header("Location: index.php?origin=charsearch");
		exit;
	}

	// get variables
	$email = $_POST['email'];
    $lastIP = $_POST['lastIP'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<title>Account search</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <link rel="stylesheet" type="text/css" href="<?=$__CSS_RELURI?>" media="all"/>
        <link rel="stylesheet" type="text/css" href="<?=$__CSS_ADDON_RELURI?>" media="all"/>
        <script language="javascript" type="text/javascript">
            function setAccountId(accountId)
            {
                document.getElementById("accountId").value = accountId;
                document.getElementById("accountListForm").submit();
            }
        </script>
	</head>
	<body>
		<table>
			<tr>
				<td style="vertical-align:top;">
<?php
					echo Navigation::S_GetNavigation();
?>
				</td>
				<td><?php echo $email; ?>
					<p><b>To avoid unnecessary strain for the server, do not be too vague with your search parameters!<br/>
					Also, the search is limited to 100 results.</b></p>
					<form action="accountsearch.php" method="post">
						<table cellspacing="0" cellpadding="0">
							<tr>
								<td>Email</td>
								<td><input type="text" name="email" style="width:200px" value="<?=$email?>"/>(exact match)</td>
							</tr>
                            <tr>
								<td>Last known IP</td>
								<td><input type="text" name="lastIP" style="width:200px" value="<?=$lastIP?>"/> (exact match)</td>
							</tr>
						</table>
                        <br/>
						<input type="submit" value="Search"/>
					</form>

                    <br/>
					<div>
						<form id="accountListForm" action="accountdetails.php" method="post">
							<table class="table">
								<tr>
									<th>Email</th>
									<th>Last login</th>
									<th>Creation date</th>
									<th>Last login IP</th>
									<th>Spam points</th>
									<th>Advisor points</th>
								</tr>
<?php
							if (isset($email) || isset($lastIP))
							{
                                $accounts = PSAccount::S_Find($email, $lastIP);
								foreach ($accounts as $account)
								{
									echo '<tr>';
									echo '<td><a href="javascript:setAccountId(\'' . $account->ID . '\');">' . $account->UserName . '</a></td>';
									echo '<td>' . $account->LastLogin . '</td>';
									echo '<td>' . $account->CreatedDate . '</td>';
									echo '<td>' . $account->LastLoginIP . '</td>';
									echo '<td>' . $account->SpamPoints . '</td>';
									echo '<td>' . $account->AdvisorPoints . '</td>';
									echo '</tr>';
								}
							}
?>
                            </table>
                            <input type="hidden" id="accountId" name="accountId"/>
                        </form>
                    </div>
                </td>
            </tr>
        </table>
    </body>
</html>