<?php

function liststats()
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

	if ($groupid==1)
		echo '<p class="header">New Accounts</p>(including also the ones without a login in game)<br><br>';
	else if ($groupid==2)
		echo '<p class="header">New Accounts</p>(including only the ones with a valid login in game)<br><br>';
	else if ($groupid==17)
		echo '<p class="header">New Characters</p>(including the ones with no connections)<br><br>';
	else if ($groupid==18)
		echo '<p class="header">New Accounts</p>(including only the ones with connection time>0)<br><br>';


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
				// run the query to get the result
				$sql = getBaseQuery($groupid,$period);
				//echo $sql;
				$query = mysql_query2($sql);
				$result = mysql_fetch_array($query, MYSQL_ASSOC);
				$counted_items = $result['result'];
				//echo "$counted_items";

				// check if period already exists, if not add it
				$sql = "SELECT * FROM wc_statistics where groupid=".$groupid." and periodname='".$period."'";
				$query = mysql_query2($sql);
				if(mysql_num_rows($query) < 1)
				{
					$sql = "INSERT INTO wc_statistics (groupid, periodname, result) VALUES ('$groupid', '".$period."', ".$counted_items.")";
					$query = mysql_query2($sql);
				} else {

					$sql = "UPDATE wc_statistics SET result = '".$counted_items."' WHERE groupid = '$groupid' and periodname = '$period'";
					mysql_query2($sql);
				}
			}
		}
		else
		{
			echo '<p class="error">You are not authorized to use these functions</p>';
		}
	}
	
	$sql = "SELECT id, periodname, result FROM wc_statistics WHERE groupid = '$groupid' ORDER BY periodname";
	$query = mysql_query2($sql);
	
	echo '<table><tr>';
	
	$line2 = '';
	$line3 = '';
	while($result = mysql_fetch_array($query, MYSQL_ASSOC))
	{
		echo '<th>'.htmlentities($result['periodname']).'</th>';
		$line2 .= '<td valign=bottom>';
		$line2 .= '<img src="img/bluebar2.gif" width="20" height="'.($result['result'] / 10).'" />';
		$line2 .= '</td>';
		$line3 .= '<td>'.(is_numeric($result['result']) ? $result['result'] : '').'</td>';
	}
	
	echo '</tr><tr class="color_a">'.$line2.'</tr><tr class="color_b">'.$line3.'</tr></table>';

	echo '<br><br><form action="./index.php?do=liststats&op=calc&groupid='.$groupid.'" METHOD=POST>(Re)Calculate for ';
	echo '<input type=text name=period size=10> <INPUT TYPE="SUBMIT" NAME="calculate" VALUE="Do it!"></form><br/>';

}


function getBaseQuery($groupid, $period) {

	$dates = getDatesFromPeriod($period);
	$to_exclude = getAccountsToExclude();

	if ($groupid==1)
		return "select count(*) as result from accounts where security_level=0 and created_date>=DATE('".$dates[1]."') and created_date<DATE('".$dates[2]."')";
	else if ($groupid==2)
		return "select count(*) as result from accounts where security_level=0 and last_login is not null and created_date>=DATE('".$dates[1]."') and created_date<DATE('".$dates[2]."')";
	else if ($groupid==17)
		return "select count(*) as result from characters where creation_time>=DATE('".$dates[1]."') and creation_time<DATE('".$dates[2]."') and account_id not in ".$to_exclude;
	else if ($groupid==18)
		return "select count(*) as result from characters where time_connected_sec>0 and creation_time>=DATE('".$dates[1]."') and creation_time<DATE('".$dates[2]."') and account_id not in ".$to_exclude;
}


?>