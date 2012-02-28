<?php

    require_once('config.php');
	require_once('classes/PSGuild.php');
    require_once('classes/Navigation.php');
	session_start();
	
	// is the user logged in?
	if (!isset($_SESSION["__SECURITY_LEVEL"])) {
		header("Location: index.php?origin=logs");
		exit;
	}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<title>Private server log (refreshed every few minutes)</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <link rel="stylesheet" type="text/css" href="<?=$__CSS_RELURI?>" media="all"/>
        <link rel="stylesheet" type="text/css" href="<?=$__CSS_ADDON_RELURI?>" media="all"/>
	</head>
	<body>
		<table>
			<tr>
				<td style="vertical-align:top;">
<?php
					echo Navigation::S_GetNavigation();
?>
				</td>
				<td style="vertical-align:top;">
                        <h2 class="yellowtitlebig">Log</h2>

<?php
							$file = fopen("/home/planeshift/psserver/planeshift/report.xml", "r");
							while(!feof($file))
							{
								echo htmlentities(fgets($file))."<br>";
							}
							fclose($file);
?>
				</td>
			</tr>
		<table>
	</body>
</html>
