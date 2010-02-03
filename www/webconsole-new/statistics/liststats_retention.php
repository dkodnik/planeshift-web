<?php

function liststats_retention()
{
    if(!checkaccess('statistics', 'read'))
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
		return;
    }

	echo '<p class="header">Player Retention</p>';
	echo 'Overall time in seconds logged in by characters created in the given quarter.';

	$groupid = (isset($_GET['groupid']) && is_numeric($_GET['groupid']) ? $_GET['groupid'] : 'nan');
	$op = (isset($_GET['op']) && ($_GET['op'] == 'add' || $_GET['op'] == 'calc')  ? $_GET['op'] : 'list');
	$period = (isset($_POST['period']) ? mysql_real_escape_string($_POST['period']) : 'nan');
	if (validatePeriod($period)==0)
		$period = 'nan';

	if($groupid == 'nan')
	{
		echo '<p class="error">You have to specify a valid Group ID!</p>';
		return;
	}

	if($op == 'calc')
	{
		if(checkaccess('statistics', 'edit'))
		{
			if($period == 'nan')
			{
				echo '<p class="error">You have to specify a valid Period, example: 2009 Q3!</p>';
			}
			else
			{
				// run the queries to get the results
				// time: 180 (3 minutes), 1800 (30 minutes), 7200 (2 hours), 36000 (10 hours), 115200 (32 hours), 230400 (64 hours), 460800 (128 hours), 921600 (256 hours)
				$to_exclude = getAccountsToExclude();
				$result = runBaseQuery($groupid,$period,180,$to_exclude);
				$result2 = runBaseQuery($groupid,$period,1800,$to_exclude);
				$result3 = runBaseQuery($groupid,$period,7200,$to_exclude);
				$result4 = runBaseQuery($groupid,$period,36000,$to_exclude);
				$result5 = runBaseQuery($groupid,$period,115200,$to_exclude);
				$result6 = runBaseQuery($groupid,$period,230400,$to_exclude);
				$result7 = runBaseQuery($groupid,$period,460800,$to_exclude);
				$result8 = runBaseQuery($groupid,$period,921600,$to_exclude);

				// check if period already exists, if not add it
				$sql = "SELECT * FROM wc_statistics where groupid=".$groupid." and periodname='".$period."'";
				$query = mysql_query2($sql);
				if(mysql_num_rows($query) < 1)
				{
					$sql = "INSERT INTO wc_statistics (groupid, periodname, result, param1) VALUES ('$groupid', '".$period."', ".$result.", 180)";
					$query = mysql_query2($sql);
					$sql = "INSERT INTO wc_statistics (groupid, periodname, result, param1) VALUES ('$groupid', '".$period."', ".$result2.", 1800)";
					$query = mysql_query2($sql);
					$sql = "INSERT INTO wc_statistics (groupid, periodname, result, param1) VALUES ('$groupid', '".$period."', ".$result3.", 7200)";
					$query = mysql_query2($sql);
					$sql = "INSERT INTO wc_statistics (groupid, periodname, result, param1) VALUES ('$groupid', '".$period."', ".$result4.", 36000)";
					$query = mysql_query2($sql);
					$sql = "INSERT INTO wc_statistics (groupid, periodname, result, param1) VALUES ('$groupid', '".$period."', ".$result5.", 115200)";
					$query = mysql_query2($sql);
					$sql = "INSERT INTO wc_statistics (groupid, periodname, result, param1) VALUES ('$groupid', '".$period."', ".$result6.", 230400)";
					$query = mysql_query2($sql);
					$sql = "INSERT INTO wc_statistics (groupid, periodname, result, param1) VALUES ('$groupid', '".$period."', ".$result7.", 460800)";
					$query = mysql_query2($sql);
					$sql = "INSERT INTO wc_statistics (groupid, periodname, result, param1) VALUES ('$groupid', '".$period."', ".$result8.", 921600)";
					$query = mysql_query2($sql);

				} else {

					$sql = "UPDATE wc_statistics SET result = '".$result."' WHERE groupid = '$groupid' and periodname = '$period' and param1=180";
					mysql_query2($sql);
					$sql = "UPDATE wc_statistics SET result = '".$result2."' WHERE groupid = '$groupid' and periodname = '$period' and param1=1800";
					mysql_query2($sql);
					$sql = "UPDATE wc_statistics SET result = '".$result3."' WHERE groupid = '$groupid' and periodname = '$period' and param1=7200";
					mysql_query2($sql);
					$sql = "UPDATE wc_statistics SET result = '".$result4."' WHERE groupid = '$groupid' and periodname = '$period' and param1=36000";
					mysql_query2($sql);
					$sql = "UPDATE wc_statistics SET result = '".$result5."' WHERE groupid = '$groupid' and periodname = '$period' and param1=115200";
					mysql_query2($sql);
					$sql = "UPDATE wc_statistics SET result = '".$result6."' WHERE groupid = '$groupid' and periodname = '$period' and param1=230400";
					mysql_query2($sql);
					$sql = "UPDATE wc_statistics SET result = '".$result7."' WHERE groupid = '$groupid' and periodname = '$period' and param1=460800";
					mysql_query2($sql);
					$sql = "UPDATE wc_statistics SET result = '".$result8."' WHERE groupid = '$groupid' and periodname = '$period' and param1=921600";
					mysql_query2($sql);
				}
			}
		}
		else
		{
			echo '<p class="error">You are not authorized to use these functions</p>';
		}
	}
	
	$sql = "SELECT id, periodname, param1, result FROM wc_statistics WHERE groupid = '$groupid' ORDER BY periodname,param1";
	$query = mysql_query2($sql);
	
	$line1 = '';
	$line2 = '';
	$line3 = '';
	$current_period = '';
	while($result = mysql_fetch_array($query, MYSQL_ASSOC))
	{
		if ($result['periodname']!=$current_period) {
			$current_period = $result['periodname'];
			echo '</tr><tr class="color_a">'.$line1.'</tr><tr class="color_a">'.$line2.'</tr><tr class="color_b">'.$line3.'</tr></table>';
			echo "<h2>$current_period</h2><table><tr>";
			$line1 = '';
			$line2 = '';
			$line3 = '';
		}

		$line1 .= '<td>'.getLabelFromTime($result['param1']).'</td>';
		$line2 .= '<td valign=bottom>';
		$line2 .= '<img src="img/bluebar2.gif" width="20" height="'.($result['result'] / 10).'" />';
		$line2 .= '</td>';
		$line3 .= '<td>'.(is_numeric($result['result']) ? $result['result'] : '').'</td>';
			
	}
	echo '</tr><tr class="color_a">'.$line1.'</tr><tr class="color_a">'.$line2.'</tr><tr class="color_b">'.$line3.'</tr></table>';

	echo '<br><br><form action="./index.php?do=liststats_retention&op=calc&groupid='.$groupid.'" METHOD=POST>(Re)Calculate for ';
	echo '<input type=text name=period size=10> <INPUT TYPE="SUBMIT" NAME="calculate" VALUE="Do it!"></form><br/>';

}


function runBaseQuery($groupid, $period, $time, $to_exclude) {

	$dates = getDatesFromPeriod($period);
	
	$sql = "select count(*) as result from characters c where character_type=0 and last_login is not null and creation_time>=DATE('".$dates[1]."') and creation_time<DATE('".$dates[2]."') and account_id not in ".$to_exclude;

	$sql .= " and time_connected_sec>".$time;
	//echo $sql;
	$query = mysql_query2($sql);
	$result = mysql_fetch_array($query, MYSQL_ASSOC);
	$counted_items = $result['result'];
	return $counted_items;
}

function getLabelFromTime($time) {
	if ($time<3600)
		return ">".($time/60)." minutes";
	else
		return ">".($time/3600)." hours";
}

?>