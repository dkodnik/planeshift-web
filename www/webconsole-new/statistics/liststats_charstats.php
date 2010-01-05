<?php

function liststats_charstats()
{
    if(!checkaccess('statistics', 'read'))
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
		return;
    }

	$groupid = (isset($_GET['groupid']) && is_numeric($_GET['groupid']) ? $_GET['groupid'] : 'nan');
	$op = (isset($_GET['op']) && ($_GET['op'] == 'add' || $_GET['op'] == 'calc')  ? $_GET['op'] : 'list');
	$period = (isset($_POST['period']) ? $_POST['period'] : 'nan');
	if (validatePeriod($period)==0)
		$period = 'nan';

	if($groupid == 'nan')
	{
		echo '<p class="error">You have to specify a valid Group ID!</p>';
		return;
	}

	echo '<p class="header">Characters Stats:'.getStatFromGroup($groupid).'</p>';
	echo 'Average stats of characters, created in a given quarter, after a certain amount of playing hours';


?>

	<?php
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
				// time: 3600 (1 hour), 36000 (10 hours), 72000 (20 hours), 180000 (50 hours), 360000 (100 hours), 720000 (200 hours), 1080000 (300 hours)
				$to_exclude = getAccountsToExclude();
				$result = runBaseQuery($groupid,$period,3600,$to_exclude);
				$result2 = runBaseQuery($groupid,$period,36000,$to_exclude);
				$result3 = runBaseQuery($groupid,$period,72000,$to_exclude);
				$result4 = runBaseQuery($groupid,$period,180000,$to_exclude);
				$result5 = runBaseQuery($groupid,$period,360000,$to_exclude);
				$result6 = runBaseQuery($groupid,$period,720000,$to_exclude);
				$result7 = runBaseQuery($groupid,$period,1080000,$to_exclude);

				// check if period already exists, if not add it
				$sql = "SELECT * FROM wc_statistics where groupid=".$groupid." and periodname='".$period."'";
				$query = mysql_query2($sql);
				if(mysql_num_rows($query) < 1)
				{
					$sql = "INSERT INTO wc_statistics (groupid, periodname, result, param1) VALUES ('$groupid', '".$period."', ".$result.", 3600)";
					$query = mysql_query2($sql);
					$sql = "INSERT INTO wc_statistics (groupid, periodname, result, param1) VALUES ('$groupid', '".$period."', ".$result2.", 36000)";
					$query = mysql_query2($sql);
					$sql = "INSERT INTO wc_statistics (groupid, periodname, result, param1) VALUES ('$groupid', '".$period."', ".$result3.", 72000)";
					$query = mysql_query2($sql);
					$sql = "INSERT INTO wc_statistics (groupid, periodname, result, param1) VALUES ('$groupid', '".$period."', ".$result4.", 180000)";
					$query = mysql_query2($sql);
					$sql = "INSERT INTO wc_statistics (groupid, periodname, result, param1) VALUES ('$groupid', '".$period."', ".$result5.", 360000)";
					$query = mysql_query2($sql);
					$sql = "INSERT INTO wc_statistics (groupid, periodname, result, param1) VALUES ('$groupid', '".$period."', ".$result6.", 720000)";
					$query = mysql_query2($sql);
					$sql = "INSERT INTO wc_statistics (groupid, periodname, result, param1) VALUES ('$groupid', '".$period."', ".$result7.", 1080000)";
					$query = mysql_query2($sql);

				} else {

					$sql = "UPDATE wc_statistics SET result = '".$result."' WHERE groupid = '$groupid' and periodname = '$period' and param1=3600";
					mysql_query2($sql);
					$sql = "UPDATE wc_statistics SET result = '".$result2."' WHERE groupid = '$groupid' and periodname = '$period' and param1=36000";
					mysql_query2($sql);
					$sql = "UPDATE wc_statistics SET result = '".$result3."' WHERE groupid = '$groupid' and periodname = '$period' and param1=72000";
					mysql_query2($sql);
					$sql = "UPDATE wc_statistics SET result = '".$result4."' WHERE groupid = '$groupid' and periodname = '$period' and param1=180000";
					mysql_query2($sql);
					$sql = "UPDATE wc_statistics SET result = '".$result5."' WHERE groupid = '$groupid' and periodname = '$period' and param1=360000";
					mysql_query2($sql);
					$sql = "UPDATE wc_statistics SET result = '".$result6."' WHERE groupid = '$groupid' and periodname = '$period' and param1=720000";
					mysql_query2($sql);
					$sql = "UPDATE wc_statistics SET result = '".$result7."' WHERE groupid = '$groupid' and periodname = '$period' and param1=1080000";
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

		if ($groupid==16)
			$line2 .= '<img src="img/bluebar2.gif" width="20" height="'.($result['result'] / 1000).'" />';
		else
			$line2 .= '<img src="img/bluebar2.gif" width="20" height="'.($result['result'] / 10).'" />';

		$line2 .= '</td>';
		$line3 .= '<td>'.(is_numeric($result['result']) ? $result['result'] : '').'</td>';
			
	}
	echo '</tr><tr class="color_a">'.$line1.'</tr><tr class="color_a">'.$line2.'</tr><tr class="color_b">'.$line3.'</tr></table>';

	echo '<br><br><form action="./index.php?do=liststats_charstats&op=calc&groupid='.$groupid.'" METHOD=POST>(Re)Calculate for ';
	echo '<input type=text name=period size=10> <INPUT TYPE="SUBMIT" NAME="calculate" VALUE="Do it!"></form><br/>';

}

function runBaseQuery($groupid, $period, $time, $to_exclude) {

	$dates = getDatesFromPeriod($period);
	$startime = $time - $time * 0.1;
	$endtime = $time + $time * 0.1;

	// run query for money
	if ($groupid==16) {
		$sql = "select avg( money_trias+money_hexas*10+money_octas*50+money_circles*250+bank_money_trias+bank_money_hexas*10+bank_money_octas*50+bank_money_circles*250) as result from characters c where character_type=0";
		$sql .= " and time_connected_sec>".$startime." and time_connected_sec<".$endtime;

	// run query for stats/skills
	} else {
		$statid = getStatIDFromGroup($groupid);
		$sql = "select avg(skill_rank) as result from character_skills s, characters c where character_type=0 ";
		$sql .= " and skill_id=".$statid." and c.id=s.character_id and creation_time>=DATE('".$dates[1]."') and creation_time<DATE('".$dates[2]."')";

		$sql .= " and time_connected_sec>".$startime." and time_connected_sec<".$endtime;

		// remove 0 ranks from the average
		$sql .= " and skill_rank!=0";
	}

	$sql .= " and account_id not in ".$to_exclude;
	//echo $sql;
	$query = mysql_query2($sql);
	$result = mysql_fetch_array($query, MYSQL_ASSOC);
	$counted_items = $result['result'];
	return ($counted_items=='')?0:$counted_items;
}


function getLabelFromTime($time) {
	
	$startime = $time - $time * 0.1;
	$endtime = $time + $time * 0.1;

	return ($startime/3600)."-".($endtime/3600)." hours";
}

function getStatFromGroup($groupid) {

	if ($groupid==4)
		return "Strength";
	else if ($groupid==5)
		return "Endurance";
	else if ($groupid==6)
		return "Agility";
	else if ($groupid==7)
		return "Intelligence";
	else if ($groupid==8)
		return "Will";
	else if ($groupid==9)
		return "Charisma";

	else if ($groupid==10)
		return "Sword";
	else if ($groupid==11)
		return "Light Armor";
	else if ($groupid==12)
		return "Medium Armor";
	else if ($groupid==13)
		return "Heavy Armor";
	else if ($groupid==14)
		return "Crystal Way";
	else if ($groupid==15)
		return "Melee";

	else if ($groupid==16)
		return "Trias";

}

function getStatIDFromGroup($groupid) {

	if ($groupid==4)
		return 50;
	else if ($groupid==5)
		return 48;
	else if ($groupid==6)
		return 46;
	else if ($groupid==7)
		return 49;
	else if ($groupid==8)
		return 51;
	else if ($groupid==9)
		return 47;

	else if ($groupid==10)
		return 0;
	else if ($groupid==11)
		return 7;
	else if ($groupid==12)
		return 8;
	else if ($groupid==13)
		return 9;
	else if ($groupid==14)
		return 11;
	else if ($groupid==15)
		return 4;
}



?>