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
				$result = runBaseQuery($groupid,$period,3600);
				$result2 = runBaseQuery($groupid,$period,36000);
				$result3 = runBaseQuery($groupid,$period,72000);
				$result4 = runBaseQuery($groupid,$period,180000);
				$result5 = runBaseQuery($groupid,$period,360000);
				$result6 = runBaseQuery($groupid,$period,720000);
				$result7 = runBaseQuery($groupid,$period,1080000);

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
		$line2 .= '<img src="img/bluebar2.gif" width="20" height="'.($result['result'] / 10).'" />';
		$line2 .= '</td>';
		$line3 .= '<td>'.(is_numeric($result['result']) ? $result['result'] : '').'</td>';
			
	}
	echo '</tr><tr class="color_a">'.$line1.'</tr><tr class="color_a">'.$line2.'</tr><tr class="color_b">'.$line3.'</tr></table>';

	echo '<br><br><form action="./index.php?do=liststats_charstats&op=calc&groupid='.$groupid.'" METHOD=POST>(Re)Calculate for ';
	echo '<input type=text name=period size=10> <INPUT TYPE="SUBMIT" NAME="calculate" VALUE="Do it!"></form><br/>';

}

function getNextQuarterPeriod($groupid) {
    $sql = "SELECT MAX(periodname) AS max FROM wc_statistics WHERE groupid = '$groupid' ORDER BY periodname";

    $result = mysql_fetch_array(mysql_query2($sql), MYSQL_ASSOC);
    $max = $result['max'];
    
    $year = substr($max, 0, 4);
    $quarter = substr($max, 5, 6);
    
    if($quarter == 'Q4')
    {
      $year = $year+1;
      $quarter = 'Q1';
    }
    else
    {
      $quarter = 'Q'. (substr($quarter, 1, 2) + 1);
    }

    return $year.' '.$quarter;
}

function runBaseQuery($groupid, $period, $time) {

	$dates = getDatesFromPeriod($period);
	$startime = $time - $time * 0.1;
	$endtime = $time + $time * 0.1;

	$statid = getStatIDFromGroup($groupid);
	$sql = "select avg(skill_rank) as result from character_skills s, characters c, accounts a where c.account_id=a.id and character_type=0 and skill_id=".$statid." and c.id=s.character_id and creation_time>=DATE('".$dates[1]."') and creation_time<DATE('".$dates[2]."')";

	$sql .= " and time_connected_sec>".$startime." and time_connected_sec<".$endtime;
	//echo $sql;
	$query = mysql_query2($sql);
	$result = mysql_fetch_array($query, MYSQL_ASSOC);
	$counted_items = $result['result'];
	return ($counted_items=='')?0:$counted_items;
}

function validatePeriod($period) {
    
    $year = substr($period, 0, 4);
    $quarter = substr($period, 5, 6);
	
	if ($year=='' || $quarter=='')
		return 0;
	
	if ($quarter!="Q1" && $quarter!="Q2" && $quarter!="Q3" && $quarter!="Q4")
		return 0;
	
	return 1;
}

function getDatesFromPeriod($period) {
    
    $year = substr($period, 0, 4);
    $quarter = substr($period, 5, 6);
    
    if($quarter == 'Q1')
    {
      $start = $year."-01-01";
      $end = $year."-03-31";
    }
    else if($quarter == 'Q2')
    {
      $start = $year."-04-01";
      $end = $year."-06-30";
    }
    else if($quarter == 'Q3')
    {
      $start = $year."-07-01";
      $end = $year."-09-30";
    }
    else if($quarter == 'Q4')
    {
      $start = $year."-10-01";
      $end = $year."-12-31";
    }

	$dates[1] = $start;
	$dates[2] = $end;
    return $dates;
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
}



?>