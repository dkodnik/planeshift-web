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
	$firstName = $_POST['firstName'];
	$lastName = $_POST['lastName'];
	$charType = isset($_POST['charType']) ? $_POST['charType'] : -1;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<title>Character search</title>
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
					<p><b>To avoid unnecessary strain for the server, do not be too vague with your search parameters!<br/>
					Also, the search is limited to 100 results.</b></p>
					<form action="charsearch.php" method="post">
						<table cellspacing="0" cellpadding="0">
							<tr>
								<td>First name</td>
								<td><input type="text" name="firstName" style="width:200px" value="<?=$firstName?>"/>(exact match)</td>
							</tr>
							<tr>
								<td>Last name</td>
								<td><input type="text" name="lastName" style="width:200px" value="<?=$lastName?>"/>(exact match)</td>
							</tr>
							<tr>
								<td>Character type</td>
								<td>
									<select name="charType" style="width:206px">
										<option value="-1"<?=($charType==-1) ? ' selected="selected"':""?>>All</option>
										<option value="0"<?=($charType==0) ? ' selected="selected"':""?>>Player</option>
										<option value="1"<?=($charType==1) ? ' selected="selected"':""?>>NPC</option>
										<option value="2"<?=($charType==2) ? ' selected="selected"':""?>>Pet</option>
									</select>
								</td>
							</tr>
                            <tr>
                                <td colspan="2">
                                    Search by IP was moved to <a href="accountsearch.php">Account search</a>.
                                </td>
                            </tr>
						</table>
                        <br/>
						<input type="submit" value="Search"/>
					</form>

					<br/>
					<div>
						<form id="charListForm" action="chardetails.php" method="post">
							<table class="table">
								<tr>
									<th>First Name</th>
									<th>Last Name</th>
									<th>Character type</th>
									<th>Time connected</th>
								</tr>
<?php
							if (isset($firstName) || isset($lastName))
							{
                                $characters = PSCharacter::S_Find($firstName, $lastName, $charType);
								foreach ($characters as $char)
								{
									// make the the time connected more human readable
									$hours = floor($char->TimeConnectedInSeconds / 3600);
									$minutes = floor($char->TimeConnectedInSeconds / 60) - ($hours * 60);
									$seconds = $char->TimeConnectedInSeconds - ($minutes * 60) - ($hours * 3600);

									echo '<tr>';
									echo '<td><a href="javascript:setCharId(\'' . $char->ID . '\');">' . $char->FirstName . '</a></td>';
									echo '<td>' . $char->LastName . '</td>';
									echo '<td>' . $char->GetHumanReadableCharType() . '</td>';
									echo '<td>' . $hours . 'h ' . $minutes . 'm ' . $seconds . 's</td>';
									echo '</tr>';
								}
							}
?>
							</table>
							<input type="hidden" id="charId" name="charId"/>
						</form>
					</div>
				</td>
			</tr>
		<table>
	</body>
</html>