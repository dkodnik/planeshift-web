<?php

    require_once('config.php');
    require_once('classes/Navigation.php');
    require_once('classes/PSQuests.php');
    session_start();
    
    // is the user logged in?
    if (!isset($_SESSION["__SECURITY_LEVEL"])) {
        header("Location: index.php?origin=questinstance");
        exit;
    }

    // get variables
    $playerId = $_POST['playerId'];
	$questId = $_POST['questId'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <title>Quest substeps</title>
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
                    <input type="button" onclick="javascript:window.history.back();" value="Back"/><br/>
                    <h2 class="yellowtitlebig">Quest substeps</h2>
                    <table class="table">
                        <tr>
                            <th>Substep</th>
                            <th>Status</th>
							<th>Lockout</th>
                        </tr>
<?php

						$actions = PSQuests::S_GetQuestStepEntries($playerId,$questId);
						foreach ($actions as $action) {
							echo '<tr>';
							$stepnumber = $action->ID-10000-($questId*100);
							echo '<td>' . $stepnumber . '</td>';
                            echo '<td>' . $action->Status . '</td>';
							echo '<td>' . $action->Lockout . '</td>';
							echo '</tr>';
						}
?>

                    </table>

                </td>
            </tr>
        </table>
    </body>
</html>