<?php

    require_once('config.php');
    require_once('classes/Navigation.php');
    require_once('classes/PSBan.php');
	session_start();
	
	// is the user logged in?
	if (!isset($_SESSION["__SECURITY_LEVEL"])) {
		header("Location: index.php?origin=bans");
		exit;
	}
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<title>Bans</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <link rel="stylesheet" type="text/css" href="<?=$__CSS_RELURI?>"/>
        <link rel="stylesheet" type="text/css" href="<?=$__CSS_ADDON_RELURI?>"/>
        <script language="javascript" type="text/javascript">
            function setAccountId(accountId)
            {
                document.getElementById("accountId").value = accountId;
                document.getElementById("bansListForm").submit();
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
    				<form id="bansListForm" action="accountdetails.php" method="post">
                        <table class="table">
                            <tr>
                                <th>Account</th>
                                <th>IP Range</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Reason</th>
                                <th>IP Ban</th>
    <?php
							echo "</tr>";

								$bans = PSBan::S_GetBans();

                            foreach ($bans as $ban) {
                                echo '<tr>';
                                echo '<td><a href="javascript:setAccountId(\'' . $ban->AccountID . '\');">' . $ban->AccountName . '</a></td>';
                                echo '<td>' . $ban->IPRange . '</td>';
                                echo '<td>' . $ban->DateStart . '</td>';
                                echo '<td>' . $ban->DateEnd . '</td>';
                                echo '<td>' . $ban->Reason . '</td>';
                                echo '<td>' . $ban->IPBan . '</td>';
                                echo '</tr>';
                            }
    ?>
                        </table>
                        <input type="hidden" id="accountId" name="accountId"/>
                    </form>
				</td>
			</tr>
		</table>
    </body>
</html>