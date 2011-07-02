<?php

    require_once('config.php');
    require_once('classes/Navigation.php');
    require_once('classes/PSPetition.php');
	session_start();
	
	// is the user logged in?
	if (!isset($_SESSION["__SECURITY_LEVEL"])) {
		header("Location: index.php?origin=petitions");
		exit;
	}
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<title>Petitions</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <link rel="stylesheet" type="text/css" href="<?=$__CSS_RELURI?>"/>
        <link rel="stylesheet" type="text/css" href="<?=$__CSS_ADDON_RELURI?>"/>
        <script language="javascript" type="text/javascript">
            function setCharId(charId)
            {
                document.getElementById("charId").value = charId;
                document.getElementById("petitionListForm").submit();
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
    				<form id="petitionListForm" action="chardetails.php" method="post">
                        <table class="table">
                            <tr>
                                <th>Time submitted</th>
                                <th>Petitioner</th>
                                <th>Status</th>
                                <th>Petition</th>
                                <th>Assigned to</th>
                                <th>Escalation</th>
    <?php
							if ($_GET["type"]==2) {
								echo "<th>Resolution</th>";
							}
							echo "</tr>";

							if ($_GET["type"]==2) {
								$petitions = PSPetition::S_GetPetitions(2);
							} else 
								$petitions = PSPetition::S_GetPetitions(1);

                            foreach ($petitions as $petition) {
                                echo '<tr>';
                                echo '<td>' . $petition->CreatedDate . '</td>';
                                echo '<td><a href="javascript:setCharId(\'' . $petition->PetitionerID . '\');">' . $petition->PetitionerFirstName . '</a></td>';
                                echo '<td>' . $petition->Status . '</td>';
                                echo '<td>' . $petition->Petition . '</td>';
                                echo '<td><a href="javascript:setCharId(\'' . $petition->CaseworkerID . '\');">' . $petition->CaseworkerFirstName . '</a></td>';
                                echo '<td>' . $petition->EscalationLevel . '</td>';

								if ($_GET["type"]==2) {
									echo '<td>' . $petition->Resolution . '</td>';
								}

                                echo '</tr>';
                            }
    ?>
                        </table>
                        <input type="hidden" id="charId" name="charId"/>
                    </form>
				</td>
			</tr>
		</table>
    </body>
</html>