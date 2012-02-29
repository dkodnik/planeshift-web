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
		<title>Server status</title>
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
                        <h2 class="yellowtitlebig">Server Status</h2>
                        Click the columns to sort.
<?php
$report = simplexml_load_file("/home/planeshift/psserver/planeshift/report.xml");
echo "<table class=\"sortable\">";
echo "<tr><th>name</th><th>characterID</th><th>guild</th><th>title</th><th>security</th><th>kills</th><th>deaths</th><th>suicides</th><th>X</th><th>Y</th><th>Z</th><th>sector</th></tr>";
foreach($report->player as $player) {
    echo "<tr><td>", $player['name'], "</td><td>", $player['characterID'], "</td><td>", $player['guild'], "</td><td>", $player['title'], "</td><td>", $player['security'], "</td><td>", $player['kills'], "</td><td>", $player['deaths'], "</td><td>", $player['suicides'], "</td><td>", $player['pos_x'], "</td><td>", $player['pos_y'], "</td><td>", $player['pos_z'], "</td><td>", $player['sector'], "</td></tr>";
}
echo "</table>";
echo "<table class=\"sortable\">";
echo "<tr><th>name</th><th>characterID</th><th>kills</th><th>deaths</th><th>suicides</th><th>X</th><th>Y</th><th>Z</th><th>sector</th></tr>";
foreach($report->npc as $player) {
    echo "<tr><td>", $player['name'], "</td><td>", $player['characterID'], "</td><td>", $player['kills'], "</td><td>", $player['deaths'], "</td><td>", $player['suicides'], "</td><td>", $player['pos_x'], "</td><td>", $player['pos_y'], "</td><td>", $player['pos_z'], "</td><td>", $player['sector'], "</td></tr>";
}
echo "</table>"

?>
	</body>
</html>
