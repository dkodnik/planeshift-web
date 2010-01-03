<?php

function liststats_charstats()
{
    if(!checkaccess('statistics', 'read'))
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
		return;
    }

	echo '<p class="header">Characters Stats</p>';
	echo 'Overall time in seconds logged in by characters';

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
?>
		<form action="./index.php?do=liststats_charstats" METHOD="POST"><input type=text name=filter size=10> Start date filter <INPUT TYPE="SUBMIT" NAME="Filter" VALUE="Filter"></form>

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
				// time: 180 (3 minutes), 1800 (30 minutes), 7200 (2 hours), 36000 (10 hours), 115200 (32 hours), 230400 (64 hours), 460800 (128 hours), 921600 (256 hours)
				$result = runBaseQuery($groupid,$period,180);
				$result2 = runBaseQuery($groupid,$period,1800);
				$result3 = runBaseQuery($groupid,$period,7200);
				$result4 = runBaseQuery($groupid,$period,36000);
				$result5 = runBaseQuery($groupid,$period,115200);
				$result6 = runBaseQuery($groupid,$period,230400);
				$result7 = runBaseQuery($groupid,$period,460800);
				$result8 = runBaseQuery($groupid,$period,921600);

				// check if period already exists, if not add it
				$sql = "SELECT * FROM wc_statistics where groupid=".$groupid." and periodname='".$period."'";
				$query = mysql_query2($sql);
				if(mysql_num_rows($query) < 1)
				{
					$sql = "INSERT INTO wc_statistics (groupid, periodname, result, param1) VALUES ('$groupid', '".$period."', ".$result.", 180)";
					$query = mysql_query2($sql);
					$sql = "INSERT INTO wc_statistics (groupid, periodname, result, param1) VALUES ('$groupid', '".$period."', ".$result2.", 1800)";
					$query = mysql_query2($sql);
					$sql = "INSERT INTO wc_statistics (groupid, periodname, result, param1) VALUES ('$groupid', '".$period."', ".$result2.", 7200)";
					$query = mysql_query2($sql);
					$sql = "INSERT INTO wc_statistics (groupid, periodname, result, param1) VALUES ('$groupid', '".$period."', ".$result2.", 36000)";
					$query = mysql_query2($sql);
					$sql = "INSERT INTO wc_statistics (groupid, periodname, result, param1) VALUES ('$groupid', '".$period."', ".$result2.", 115200)";
					$query = mysql_query2($sql);
					$sql = "INSERT INTO wc_statistics (groupid, periodname, result, param1) VALUES ('$groupid', '".$period."', ".$result2.", 230400)";
					$query = mysql_query2($sql);
					$sql = "INSERT INTO wc_statistics (groupid, periodname, result, param1) VALUES ('$groupid', '".$period."', ".$result2.", 460800)";
					$query = mysql_query2($sql);
					$sql = "INSERT INTO wc_statistics (groupid, periodname, result, param1) VALUES ('$groupid', '".$period."', ".$result2.", 921600)";
					$query = mysql_query2($sql);

				} else {

					$sql = "UPDATE wc_statistics SET result = '".$result."' WHERE groupid = '$groupid' and periodname = '$period' and param1=180";
					mysql_query2($sql);
					$sql = "UPDATE wc_statistics SET result = '".$result."' WHERE groupid = '$groupid' and periodname = '$period' and param1=1800";
					mysql_query2($sql);
					$sql = "UPDATE wc_statistics SET result = '".$result."' WHERE groupid = '$groupid' and periodname = '$period' and param1=7200";
					mysql_query2($sql);
					$sql = "UPDATE wc_statistics SET result = '".$result."' WHERE groupid = '$groupid' and periodname = '$period' and param1=36000";
					mysql_query2($sql);
					$sql = "UPDATE wc_statistics SET result = '".$result."' WHERE groupid = '$groupid' and periodname = '$period' and param1=115200";
					mysql_query2($sql);
					$sql = "UPDATE wc_statistics SET result = '".$result."' WHERE groupid = '$groupid' and periodname = '$period' and param1=230400";
					mysql_query2($sql);
					$sql = "UPDATE wc_statistics SET result = '".$result."' WHERE groupid = '$groupid' and periodname = '$period' and param1=460800";
					mysql_query2($sql);
					$sql = "UPDATE wc_statistics SET result = '".$result."' WHERE groupid = '$groupid' and periodname = '$period' and param1=921600";
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
		$line2 .= '<img src="img/bluebar2.gif" width="20" height="'.($result['result'] / 1).'" />';
		$line2 .= '</td>';
		$line3 .= '<td>'.(is_numeric($result['result']) ? $result['result'] : '').'</td>';
			
	}
	echo '</tr><tr class="color_a">'.$line1.'</tr><tr class="color_a">'.$line2.'</tr><tr class="color_b">'.$line3.'</tr></table>';

	echo '<br><br><form action="./index.php?do=liststats_retention&op=calc&groupid='.$groupid.'" METHOD=POST>(Re)Calculate for ';
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
	
	$sql = "select count(*) as result from characters where last_login is not null and creation_time>=DATE('".$dates[1]."') and creation_time<DATE('".$dates[2]."')";

	$sql .= " and time_connected_sec<".$time;
	echo $sql;
	$query = mysql_query2($sql);
	$result = mysql_fetch_array($query, MYSQL_ASSOC);
	$counted_items = $result['result'];
	return $counted_items;
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
	if ($time<3600)
		return "<".($time/60)." minutes";
	else
		return "<".($time/3600)." hours";
}

?>