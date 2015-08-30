<?php

    require_once("config.php");
    require_once('classes/Navigation.php');
    require_once("classes/PSCharacter.php");

	session_start();

	// is the user logged in?
	if (!isset($_SESSION["__SECURITY_LEVEL"])) {
		header("Location: index.php?origin=check_keys");
		exit;
	}

	// get variables
	$keyField = $_POST['keyField'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<title>Check Keys</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <link rel="stylesheet" type="text/css" href="<?=$__CSS_RELURI?>" media="all"/>
        <link rel="stylesheet" type="text/css" href="<?=$__CSS_ADDON_RELURI?>" media="all"/>
        <script language="javascript" type="text/javascript">
            function setKeyId(keyId)
            {
                document.getElementById("keyId").value = keyId;
                document.getElementById("checkKeyForm").submit();
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
				<td><?php echo $keyField; ?>

					<form action="check_keys.php" method="post">
						<table cellspacing="0" cellpadding="0">
							<tr>
								<td>Key ID</td>
								<td><input type="text" name="keyField" style="width:200px" value="<?=$keyField?>"/>(exact match)</td>
							</tr>
						</table>
                        <br/>
						<input type="submit" value="Search"/>
					</form>

                    <br/>
					<div>
						<form id="checkKeyForm" action="chardetail.php" method="post">
							<table class="table">
								<tr>
									<th>First Name</th>
									<th>Last Name</th>
									<th>Character type</th>
									<th>Time connected</th>
								</tr>
<?php

							if (isset($keyField))
							{
                                $characters = PSCharacter::Key_Find($keyField);

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
                            <input type="hidden" id="keyId" name="keyId"/>
                        </form>
                    </div>
                </td>
            </tr>
        </table>
    </body>
</html>