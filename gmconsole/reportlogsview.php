<?php

    require_once('config.php');
    require_once('classes/Navigation.php');
    require_once("classes/PSCharacter.php");
	session_start();
	
	// is the user logged in?
	if (!isset($_SESSION["__SECURITY_LEVEL"])) {
		header("Location: index.php?origin=reportlogs");
		exit;
	}

    if ($_POST["deleteButton"]) {
        $fileName = $_POST["fileToDelete"];
        rename($fileName, str_replace(DIRECTORY_SEPARATOR . $__REPORT_LOG_SUBDIR . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR . $__REPORT_LOG_DELETED_SUBDIR . DIRECTORY_SEPARATOR, $fileName));
        header("Location: reportlogs.php");
        exit;
    }

	// get variables
    $logFileNameWithPathAndExt = str_replace("\\\\", "\\", $_POST["viewlogId"]);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<title>View a report log</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <link rel="stylesheet" type="text/css" href="<?=$__CSS_RELURI?>"/>
        <link rel="stylesheet" type="text/css" href="<?=$__CSS_ADDON_RELURI?>"/>
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
                    <form id="deleteForm" action="reportlogsview.php" method="post">
                        <input type="hidden" id="fileToDelete" name="fileToDelete" value="<?=$logFileNameWithPathAndExt?>" />
    					<input type="button" onclick="javascript:window.history.back();" value="Back"/>
<?php
                        if (strpos($logFileNameWithPathAndExt, DIRECTORY_SEPARATOR . $__REPORT_LOG_SUBDIR . DIRECTORY_SEPARATOR)) {
?>
                            <input type="submit" id="deleteButton" name="deleteButton" value="Delete" />
<?php
                        }
?>
                    </form>

                    <h2 class="yellowtitlebig">Viewing report log: <?=substr($logFileNameWithPathAndExt, strrpos($logFileNameWithPathAndExt, DIRECTORY_SEPARATOR) + 1)?></h2>
<?php
                    $myFile = file($logFileNameWithPathAndExt);

                    foreach ($myFile as $line) {
                        echo $line . '<br>';
                    }
?>
				</td>
			</tr>
		</table>
    </body>
</html>