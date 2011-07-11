<?php

    require_once('config.php');
	require_once('classes/PSGuild.php');
    require_once('classes/Navigation.php');
	session_start();
	
	// is the user logged in?
	if (!isset($_SESSION["__SECURITY_LEVEL"])) {
		header("Location: index.php?origin=reportlogs");
		exit;
	}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<title>List of exchange logs</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <link rel="stylesheet" type="text/css" href="<?=$__CSS_RELURI?>" media="all"/>
        <link rel="stylesheet" type="text/css" href="<?=$__CSS_ADDON_RELURI?>" media="all"/>
        <script src="sorttable.js"></script>
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
                        <h2 class="yellowtitlebig">Exchange log (usage: ?date=-1 week)</h2>
                        Click columns to sort.
<table class=\"sortable\">
<?php
$date = (isset($_GET['date']) ? $_GET['date'] : '-1 week');
$file = fopen("../../psserver/planeshift/logs/exchange.csv", "r");
echo "<tr><th>";
$headerLine = fgets($file);
echo str_replace(",", "</th><th>", $headerLine);
echo "</th></tr>";
$fromTime = strtotime($date);
while(!feof($file))
{
        $line = fgetcsv($file);
        $dateLine = strtotime($line[0]);
        // This is needed in case the dateline is corrupted.
        if(!($dateLine > $fromTime))
            continue;
        echo "<tr>";
        $num = count($line);
        for ($c=0; $c < $num; $c++) {
                echo "<td>" . $line[$c]. "</td>\n";
        }
        echo "</tr>";
}

fclose($file);
?>
</table>
	</body>
</html>
