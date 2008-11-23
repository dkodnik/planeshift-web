<?php

    require_once('config.php');
    require_once('classes/Navigation.php');
    require_once('classes/PSNameChange.php');
	session_start();
	
	// is the user logged in?
	if (!isset($_SESSION["__SECURITY_LEVEL"])) {
		header("Location: index.php?origin=guildsearch");
		exit;
	}

    $page = $_POST['pageId'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<title>List of name changes</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <link rel="stylesheet" type="text/css" href="<?=$__CSS_RELURI?>" media="all"/>
        <link rel="stylesheet" type="text/css" href="<?=$__CSS_ADDON_RELURI?>" media="all"/>
        <script language="javascript" type="text/javascript">
            function setPage(pageId)
            {
                document.getElementById("pageId").value = pageId;
                document.getElementById("nameChangeForm").submit();
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

					<h2 class="yellowtitlebig">Name changes</h2>
                    <form id="nameChangeForm" action="namechanges.php" method="post">
                        <table class="table">
                            <tr>
                                <th>Time</th>
                                <th>Executing GM</th>
                                <th>Old first name</th>
                                <th>New first name</th>
                                <th>New last name</th>
                            </tr>
<?php
                                $nameChanges = PSNameChange::S_GetLatest($page);
                                foreach ($nameChanges as $nc) {
                                    echo '<tr>';
                                    echo '<td>' . $nc->TimeOfExecution .  '</td>';
                                    echo '<td>' . $nc->ExecutingGM .  '</td>';
                                    echo '<td>' . $nc->OldFirstName .  '</td>';
                                    echo '<td>' . $nc->NewFirstName .  '</td>';
                                    echo '<td>' . (($nc->NewLastName == '') ? '[unchanged]' : $nc->NewLastName) . '</td>';
                                    echo '</tr>';
                                }
?>
                        </table>
                        <input type="hidden" name="pageId" value="pageId" />
                        <br/>
                        <input type="button" value="<< Later" <?php echo ($page <= 0) ? 'style="visibility:hidden;"' : '' ?> onclick="setPage(<?php echo $page-1; ?>)"/>
                        <input type="button" value="Earlier >>" onclick="setPage(<?php echo $page+1; ?>)"/>
                    </form>
				</td>
			</tr>
		</table>
	</body>
</html>