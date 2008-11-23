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
		<title>List of report logs</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <link rel="stylesheet" type="text/css" href="<?=$__CSS_RELURI?>" media="all"/>
        <link rel="stylesheet" type="text/css" href="<?=$__CSS_ADDON_RELURI?>" media="all"/>
        <script language="javascript" type="text/javascript">
            function setLogId(viewlogId)
            {
                document.getElementById("viewlogId").value = viewlogId;
                document.getElementById("reportLogsForm").submit();
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
                        <h2 class="yellowtitlebig">Report logs (active)</h2>
						<table class="table">
							<tr>
								<th>Time</th>
								<th>Reporter</th>
								<th>Offender</th>
								<th>Log</th>
							</tr>
<?php
                            $fileArray = Array();
                            $dir = dir(getcwd() . DIRECTORY_SEPARATOR . $__REPORT_LOG_SUBDIR);
                            while (($fileNameWithExt = $dir->read()) !== false) {
                                $fileNameWithPathAndExt = getcwd() . DIRECTORY_SEPARATOR . $__REPORT_LOG_SUBDIR . DIRECTORY_SEPARATOR . $fileNameWithExt;
                                if (is_file($fileNameWithPathAndExt) && strstr($fileNameWithPathAndExt,"report_chat_")) {
                                    $fileArray[filemtime($fileNameWithPathAndExt)] = $fileNameWithExt;
                                }
                            }

                            krsort($fileArray);

                            foreach ($fileArray as $key => $fileNameWithExt) {
                                $fileNameWithPathAndExt = getcwd() . DIRECTORY_SEPARATOR . $__REPORT_LOG_SUBDIR . DIRECTORY_SEPARATOR . $fileNameWithExt;

                                //
                                // File name is expected to be of format: report_chat_{offender}_by_{reporter}.log
                                //
                                $offender = substr(substr($fileNameWithExt, 12), 0, strpos(substr($fileNameWithExt, 12), "_")); // remove "head" and "tail"

                                $reporter = substr($fileNameWithExt, strrpos($fileNameWithExt, "_") + 1); // remove "head"
                                $reporter = substr($reporter, 0, strpos($reporter, ".")); // remove "tail"

                                if (is_file($fileNameWithPathAndExt)) {
                                    echo '<tr>';
                                    echo '<td>' . strftime("%Y/%m/%d - %I:%M:%S%p", $key) . '</td>';
                                    echo '<td>' . $reporter . '</td>';
                                    echo '<td>' . $offender . '</td>';
                                    echo '<td><a href="javascript:setLogId(\'' . str_replace("\\", "\\\\", $fileNameWithPathAndExt) . '\');">' . $fileNameWithExt . '</a></td>';
									//echo '<td><a href="javascript:setLogId(\'test1\');">' . $fileNameWithExt . '</a></td>';
                                    echo '</tr>';
                                }
                            }
?>
						</table>
                        <hr/>
                        <h2 class="yellowtitlebig">Report logs (marked as deleted)</h2>
						<table class="table">
							<tr>
								<th>Time</th>
								<th>Reporter</th>
								<th>Offender</th>
								<th>Log</th>
							</tr>
							<form id="reportLogsForm" action="reportlogsview.php" method="post">
<?php
                            $fileArray = Array();
                            $dir = dir(getcwd() . DIRECTORY_SEPARATOR . $__REPORT_LOG_DELETED_SUBDIR);
                            while (($fileNameWithExt = $dir->read()) !== false) {
                                $fileNameWithPathAndExt = getcwd() . DIRECTORY_SEPARATOR . $__REPORT_LOG_DELETED_SUBDIR . DIRECTORY_SEPARATOR . $fileNameWithExt;
                                if (is_file($fileNameWithPathAndExt) && strstr($fileNameWithPathAndExt,"report_chat_")) {
                                    $fileArray[filemtime($fileNameWithPathAndExt)] = $fileNameWithExt;
                                }
                            }

                            krsort($fileArray);

                            foreach ($fileArray as $key => $fileNameWithExt) {
                                $fileNameWithPathAndExt = getcwd() . DIRECTORY_SEPARATOR . $__REPORT_LOG_DELETED_SUBDIR . DIRECTORY_SEPARATOR . $fileNameWithExt;

                                //
                                // File name is expected to be of format: report_chat_{offender}_by_{reporter}.log
                                //
                                $offender = substr(substr($fileNameWithExt, 12), 0, strpos(substr($fileNameWithExt, 12), "_")); // remove "head" and "tail"

                                $reporter = substr($fileNameWithExt, strrpos($fileNameWithExt, "_") + 1); // remove "head"
                                $reporter = substr($reporter, 0, strpos($reporter, ".")); // remove "tail"

                                if (is_file($fileNameWithPathAndExt)) {
                                    echo '<tr>';
                                    echo '<td>' . strftime("%Y/%m/%d - %I:%M:%S%p", $key) . '</td>';
                                    echo '<td>' . $reporter . '</td>';
                                    echo '<td>' . $offender . '</td>';
                                    echo '<td><a href="javascript:setLogId(\'' . str_replace("\\", "\\\\", $fileNameWithPathAndExt) . '\');">' . $fileNameWithExt . '</a></td>';
                                    echo '</tr>';
                                }
                            }
?>
                        <input type="hidden" id="viewlogId" name="viewlogId" />
    				</form>
				</td>
			</tr>
		<table>
	</body>
</html>