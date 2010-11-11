<?php

    require_once('config.php');
    require_once('classes/Navigation.php');
    require_once('classes/PSGuild.php');

	session_start();
	
	// is the user logged in?
	if (!isset($_SESSION["__SECURITY_LEVEL"])) {
		header("Location: index.php?origin=guildsearch");
		exit;
	}

	// get variables
	$guildId = $_POST['guildId'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<title>Guild details</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <link rel="stylesheet" type="text/css" href="<?=$__CSS_RELURI?>" media="all"/>
        <link rel="stylesheet" type="text/css" href="<?=$__CSS_ADDON_RELURI?>" media="all"/>
        <script language="javascript" type="text/javascript">
            function setCharId(charId)
            {
                document.getElementById("charId").value = charId;
                document.getElementById("guildForm").submit();
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

				<td style="vertical-align:top; width:800px">
					<input type="button" onclick="javascript:window.history.back();" value="Back"/><br/>
<?php
					if(!is_numeric($guildId)) {
						die("Could not load guild, no ID specified by parent page.");
					}

					$guild = new PSGuild($guildId);
					$founder = $guild->GetFounder();
					$guildLeader = $guild->GetLeader();
                    $members = $guild->GetMembers();;

?>
					<h2 class="yellowtitlebig">General guild information</h2>
                    <form id="guildForm" action="chardetails.php" method="post">
                        <table class="table">
                            <tr>
                                <th>Attribute</th>
                                <th>Value</th>
                            </tr>
                            <tr>
                                <td>Name</td>
                                <td><?=$guild->Name?></td>
                            </tr>
                            <tr>
                                <td>Creation date</td>
                                <td><?=$guild->DateCreated?></td>
                            </tr>
                            <tr>
                                <td>Founder</td>
                                <td><a href="javascript:setCharId('<?=$founder->ID?>');"><?=$founder->FirstName . ' ' . $founder->LastName?></a></td>
                            </tr>
                            <tr>
                                <td>Leader</td>
                                <td><a href="javascript:setCharId('<?=$guildLeader->ID?>');"><?=$guildLeader->FirstName . ' ' . $guildLeader->LastName?></a></td>
                            </tr>
                            <tr>
                                <td>MOTD</td>
                                <td><?=str_replace("\n", "<br>", $guild->Motd)?></td>
                            </tr>
                        </table>
                        <input type="hidden" name="charId" id="charId"/>

                        <hr/>
                        <h2 class="yellowtitlebig">Current guild members</h2>
                        <table class="table">
                            <th>Guild level</th>
                            <th>First name</th>
                            <th>Last name</th>
                            <th>Last login</th>
<?php
                            foreach ($members as $member) {
                                echo '<tr>';
                                echo '<td>' . $member->GuildLevel . '</td>';
                                echo '<td><a href="javascript:setCharId(' . $member->ID . ');">' . $member->FirstName. '</a></td>';
                                echo '<td>' . $member->LastName . '</td>';
                                echo '<td>' . $member->LastLogin . '</td>';
                                echo '</tr>';
                            }
?>
					    </table>
                    </form>
				</td>
			</tr>
		</table>
	</body>
</html>