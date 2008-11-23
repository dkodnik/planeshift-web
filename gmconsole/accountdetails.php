<?php

    require_once('config.php');
    require_once('classes/Navigation.php');
    require_once("classes/PSCharacter.php");

	session_start();

	// is the user logged in?
	if (!isset($_SESSION["__SECURITY_LEVEL"])) {
		header("Location: index.php?origin=charsearch");
		exit;
	}

	// get variables
    $accountId = $_POST["accountId"];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<title>Account details</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <link rel="stylesheet" type="text/css" href="<?=$__CSS_RELURI?>"/>
        <link rel="stylesheet" type="text/css" href="<?=$__CSS_ADDON_RELURI?>"/>
        <script language="javascript" type="text/javascript">
            function setCharId(charId)
            {
                document.getElementById("charId").value = charId;
                document.getElementById("charDetailsForm").submit();
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
				<td style="width:800px">
					<input type="button" onclick="javascript:window.history.back();" value="Back"/><br/>
<?php
					if(!is_numeric($accountId)) {
						die("Could not load account, no ID specified by parent page.");
					}

                    $account = new PSAccount($accountId);
                    if ($account->UserName != 'superclient') {
                        $altChars = $account->GetCharacters();
                    } else {
                        $altChars = array();
                    }
?>

					<h2 class="yellowtitlebig">General account information</h2>
					<table cellspacing="0" cellpadding="0">
						<tr>
							<td style="vertical-align:top">
								<table class="table">
									<tr>
										<th>Attribute</th>
										<th>Value</th>
									</tr>
									<tr>
										<td>Account ID</td>
										<td><?=$account->ID?></td>
									</tr>
									<tr>
										<td>Email</td>
										<td><?=$account->UserName?></td>
									</tr>
                                    <tr>
										<td>Account status</td>
										<td><?=$account->GetStatus()?></td>
									</tr>
									<tr>
										<td>Last login</td>
										<td><?=$account->LastLogin?></td>
									</tr>
									<tr>
										<td>Last login IP</td>
										<td><?=$account->LastLoginIP?></td>
									</tr>
                                    <tr>
										<td>Creation date</td>
										<td><?=$account->CreatedDate?></td>
									</tr>
                                    <tr>
										<td>Spam points</td>
										<td><?=$account->SpamPoints?></td>
									</tr>
                                    <tr>
										<td>Advisor points</td>
										<td><?=$account->AdvisorPoints?></td>
									</tr>
                                </table>
                            </td>
							<td style="width:20px">&nbsp;</td>
							<td style="vertical-align:top">
                                <table class="table">
                                    <tr>
                                        <th>#</th>
                                        <th>Character on this account</th>
                                    </tr>
<?php
                                    $i = 0;
                                    foreach ($altChars as $altChar) {
                                        if ($altChar->ID != $char->ID) {
                                            $i++;
                                            echo '<tr>';
                                            echo '<td style="text-align:right;">#' . $i . '</td>';
                                            echo '<td><a href="javascript:setCharId(\'' . $altChar->ID . '\');">' . $altChar->FirstName . ' ' . $altChar->LastName . '</a></td>';
                                            echo '</tr>';
                                        }
                                    }
?>
								</table>
                                <form id="charDetailsForm" action="chardetails.php" method="post">
                                    <input type="hidden" id="charId" name="charId"/>
                                </form>
							</td>
						</tr>
					</table>