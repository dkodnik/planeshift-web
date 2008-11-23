<?php

    require_once('config.php');
	require_once('classes/PSGuild.php');
    require_once('classes/Navigation.php');

	session_start();
	
	// is the user logged in?
	if (!isset($_SESSION["__SECURITY_LEVEL"])) {
		header("Location: index.php?origin=guildsearch");
		exit;
	}

	// get variables
	$guildName = $_POST['guildName'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<title>Guild search</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <link rel="stylesheet" type="text/css" href="<?=$__CSS_RELURI?>" media="all"/>
        <link rel="stylesheet" type="text/css" href="<?=$__CSS_ADDON_RELURI?>" media="all"/>
        <script language="javascript" type="text/javascript">
            function setGuildId(guildId)
            {
                document.getElementById("guildId").value = guildId;
                document.getElementById("guildListForm").submit();
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
				<td style="vertical-align:top;">
					<p><b>To avoid unnecessary strain for the server, do not be too vague with your search parameters!<br/>
					Also, the search is limited to 100 results.</b></p>
					<form action="guildsearch.php" method="post">
						<table border="0">
							<tr>
								<td>Guild name</td>
								<td><input type="text" name="guildName" style="width:200px" value="<?=$guildName?>"/>
							</tr>
						</table>
                        <br/>
						<input type="submit" value="Search"/>
					</form>
					<br/>
					<form id="guildListForm" action="guilddetails.php" method="post">
						<table class="table">
							<tr>
								<th>Name</th>
								<th>Web page</th>
								<th>Creation date</th>
								<th>Secret</th>
							</tr>
<?php
							if (isset($guildName)) {
								$guilds = PSGuild::S_Find($guildName);
								foreach ($guilds as $guild) {
									echo '<tr>';
									echo '<td><a href="javascript:setGuildId(\'' . $guild->ID . '\')">' . $guild->Name . '</a></td>';
									echo '<td>' . $guild->WebPage . '</td>';
									echo '<td>' . $guild->DateCreated . '</td>';
									echo '<td>' . $guild->SecrecyIndicator . '</td>';
									echo '</tr>';
								}
							}
?>
						</table>
						<input type="hidden" name="guildId" id="guildId"/>
					</form>
				</td>
			</tr>
		<table>
	</body>
</html>