<?php

    require_once('config.php');
    require_once('classes/Navigation.php');
    require_once('classes/PSAccount.php');

	session_start();
	
	// is the user logged in?
	if (!isset($_SESSION["__SECURITY_LEVEL"])) {
		header("Location: index.php?origin=gmlist");
		exit;
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<title>List of GMs</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <link rel="stylesheet" type="text/css" href="<?=$__CSS_RELURI?>" media="all"/>
        <link rel="stylesheet" type="text/css" href="<?=$__CSS_ADDON_RELURI?>" media="all"/>
        <script language="javascript" type="text/javascript">
            function setCharId(charId)
            {
                document.getElementById("charId").value = charId;
                document.getElementById("charListForm").submit();
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
				<td>
					<h2 class="yellowtitlebig">List of GMs and access level</h2>
					<table class="table">
						<tr>
							<th>Email address</th>
							<th>Security level</th>
							<th>Status</th>
						</tr>
<?php
						$gms = PSAccount::S_GetGMs();
						foreach ($gms as $gm) {
							echo '<tr>';
							echo '<td><a href="mailto:' . $gm->UserName . '">' . $gm->UserName . '</a></td>';
							echo '<td>' . $gm->SecurityLevel . '</td>';
							echo '<td>' . $gm->GetStatus() . '</td>';
							echo '</tr>';
						}
?>
				</td>
			</tr>
		</table>
	</body>
</html>