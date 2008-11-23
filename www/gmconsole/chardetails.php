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
    $charId = $_POST["charId"];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<title>Character details</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <link rel="stylesheet" type="text/css" href="<?=$__CSS_RELURI?>"/>
        <link rel="stylesheet" type="text/css" href="<?=$__CSS_ADDON_RELURI?>"/>
        <script language="javascript" type="text/javascript">
            function setCharId(charId)
            {
                document.getElementById("charId").value = charId;
                document.getElementById("charDetailsForm").submit();
            }

            function setGuildId(charId)
            {
                document.getElementById("guildId").value = charId;
                document.getElementById("guildForm").submit();
            }

            function setItemId(itemId)
            {
                document.getElementById("itemId").value = itemId;
                document.getElementById("itemDetailForm").submit();
            }
            function setQuestId(playerId, questId)
            {
                document.getElementById("playerId").value = playerId;
				document.getElementById("questId").value = questId;
                document.getElementById("questDetailForm").submit();
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
					if(!is_numeric($charId)) {
						die("Could not load character, no ID specified by parent page.");
					}

					$char = new PSCharacter($charId);
                    $race = $char->GetRace();
                    $account = $char->GetAccount();
                    $guild = $char->GetGuild();
                    if ($char->CharacterType == 0)
                        $altChars = $account->GetCharacters();
                    else
                        $altChars = array();
                    $inventoryItems = $char->GetInventory();

?>
					<h2 class="yellowtitlebig">General character information</h2>
					<table cellspacing="0" cellpadding="0">
						<tr>
							<td style="vertical-align:top">
								<table class="table">
									<tr>
										<th>Attribute</th>
										<th>Value</th>
									</tr>
									<tr>
										<td>Name</td>
										<td><?=$char->FirstName?></td>
									</tr>
									<tr>
										<td>Last name</td>
										<td><?=$char->LastName?></td>
									</tr>
                                    <tr>
										<td>Race</td>
										<td><?=$race->Name?></td>
									</tr>
                                    <tr>
										<td>Gender</td>
										<td><?=$race->Sex?></td>
									</tr>
<?php
if ($_SESSION["__SECURITY_LEVEL"] >= 22) {
?>
                                    <tr>
										<td>Stamina (mental)</td>
										<td><?=$char->StaminaMental?></td>
									</tr>
                                    <tr>
										<td>Stamina (physical)</td>
										<td><?=$char->StaminaPhysical?></td>
									</tr>
                                    <tr>
										<td>STR (Strength)</td>
										<td><?=$char->STR?></td>
									</tr>
                                    <tr>
										<td>AGI (Agility)</td>
										<td><?=$char->AGI?></td>
									</tr>
                                    <tr>
										<td>END (Endurance)</td>
										<td><?=$char->END?></td>
									</tr>
                                    <tr>
										<td>INT (Intelligence)</td>
										<td><?=$char->INT?></td>
									</tr>
                                    <tr>
										<td>WIL (Willpower)</td>
										<td><?=$char->WIL?></td>
									</tr>
                                    <tr>
										<td>CHA (Charisma)</td>
										<td><?=$char->CHA?></td>
									</tr>
                                    <tr>
										<td>HP (Hit points)</td>
										<td><?=$char->HP?></td>
									</tr>
                                    <tr>
										<td>MANA (Mana points)</td>
										<td><?=$char->MANA?></td>
									</tr>
<?php
}
?>
								</table>
							</td>
                            <td style="width:20px">&nbsp;</td>
                            <td style="vertical-align:top">
								<table class="table">
									<tr>
										<th>Attribute</th>
										<th>Value</th>
									</tr>
									<tr>
										<td>Account email</td>
										<td><?=$account->UserName?></td>
									</tr>
									<tr>
										<td>Last known IP</td>
										<td><?=$account->LastLoginIP?></td>
									</tr>
									<tr>
										<td>Guild</td>
										<td><a href="javascript:setGuildId('<?=$guild->ID?>');"><?=$guild->Name?></a></td>
									</tr>
                                </table>
                                <form id="guildForm" action="guilddetails.php" method="post">
                                    <input type="hidden" id="guildId" name="guildId"/>
                                </form>
                            </td>
							<td style="width:20px">&nbsp;</td>
							<td style="vertical-align:top">
                                <table class="table">
                                    <tr>
                                        <th>#</th>
                                        <th>Alt character's name</th>
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
					<br/>
<?php
if ($_SESSION["__SECURITY_LEVEL"] >= 22) {
?>
                    <hr/>
			        <h2 class="yellowtitlebig">Inventory</h2>
					<div style="height:200px; overflow:auto;clear:both;">
						<table class="table">
							<tr>
								<th>Item</th>
                                <th>Amount</th>
                                <th>Location</th>
							</tr>
<?php
						foreach ($inventoryItems as $item) {
							echo '<tr>';
							echo '<td><a href="javascript:setItemId(\'' . $item->ID . '\');">' . $item->Name . '</a></td>';
							echo '<td>' . $item->StackCount . '</td>';
							echo '<td>' . $item->GetHumanReadableItemLocation() . '</td>';
							echo '</tr>';
						}
?>
						</table>
                        <form id="itemDetailForm" action="iteminstance.php" method="post">
                            <input type="hidden" id="itemId" name="itemId"/>
                        </form>
					</div>
<?php
}
?>
<?php
					if ($account->SecurityLevel >= 21) {
						$actions = $char->GetGMCommandLog();
?>
					<hr/>
					<h2 class="yellowtitlebig">GM actions taken by this character</h2>
					<div style="height:200px; overflow:auto;clear:both;">
						<table class="table">
							<tr>
								<th>Action</th>
                                <th>Target player</th>
								<th>Date</th>
							</tr>
<?php
						foreach ($actions as $action) {
							echo '<tr>';
							echo '<td>' . $action->Command . '</td>';
                            echo '<td><a href="javascript:setCharId(\'' . $action->TargetPlayerID . '\');">' . $action->TargetPlayerFirstName . '</a></td>';
							echo '<td>' . $action->TimeOfExecution . '</td>';
							echo '</tr>';
						}
?>
						</table>
					</div>
<?php
					}

					$actions = $char->GetQuestEntries();
?>
					<hr/>
					<h2 class="yellowtitlebig">Quests completed by this character</h2>
					<div style="height:200px; overflow:auto;clear:both;">
						<table class="table">
							<tr>
								<th>Quest</th>
                                <th>Status</th>
								<th>Lockout</th>
							</tr>
<?php
						foreach ($actions as $action) {
							echo '<tr>';
							echo '<td><a href=' . $action->Name . '</td>';
							echo '<td><a href="javascript:setQuestId(\'' . $char->ID . '\' , \'' . $action->ID . '\');">' . $action->Name . '</a></td>';
                            echo '<td>' . $action->Status . '</td>';
							echo '<td>' . $action->Lockout . '</td>';
							echo '</tr>';
						}
?>
						</table>
                        <form id="questDetailForm" action="questinstance.php" method="post">
                            <input type="hidden" id="playerId" name="playerId"/>
							<input type="hidden" id="questId" name="questId"/>
                        </form>
					</div>

				</td>
			</tr>
		</table>
    </body>
</html>