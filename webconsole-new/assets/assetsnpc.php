<?php

function assetsnpc()
{
    if(!checkaccess('assets', 'read'))
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
		return;
    }

	if (!isset($_GET['op'])) {
		echo "You have to specify an option.";
		return;
	}

	if ($_GET['op']=="npc") {
		echo '<p class="header">NPCs used in game by race</p>';
		echo '(excludes npcrooms, sect 3,68,69,70,71)<br><br>';

		$sql = "select count(c.id) as num, r.name as name, r.sex as sex from characters as c, race_info as r where r.id=racegender_id and c.character_type=1 and c.loc_sector_id not in (3,68,69,70,71) group by r.name,r.sex order by num DESC";
		$query = mysql_query2($sql);

		echo "<table><tr><th>Count</th><th>Race</th><th>Gender</th></tr>";
		$i=0;
		while($result = mysql_fetch_array($query, MYSQL_ASSOC))
		{
				if ($result['num']==0)
					echo '<tr class="color_a"><td><font color=red>'.$result['num'].'</font></td><td>'.$result['name'].'</td><td>'.$result['sex'].'</td></tr>';
				else {
					echo '<tr class="color_a"><td>'.$result['num'].'</td><td>'.$result['name'].'</td><td>'.$result['sex'].'</td></tr>';
					$characters[$i]=$result['name'].$result['sex'];
					$i++;
				}
		}
		// find races with no instances
		$sql = "select name, sex from race_info";
		$query = mysql_query2($sql);
		$i=0;
		while($result = mysql_fetch_array($query, MYSQL_ASSOC))
		{
			$races[$i]=$result['name'].$result['sex'];
			$i++;
		}

		$diffs = array_values(array_diff($races,$characters));

		for ($i = 0; $i <= count($diffs); $i++) {
			$diflen = strlen($diffs[$i]);
			echo '<tr class="color_a"><td><font color=red>0</font></td><td>'.substr($diffs[$i],0,$diflen-1).'</td><td>'.substr($diffs[$i], $diflen-1).'</td></tr>';
		}

		echo "</table>";
	} else if ($_GET['op']=="trait") { 
		$sql = "SELECT count(c.trait_id) as num,t.id,t.name from traits t left join ";
		$sql .= " (select * from character_traits, characters where character_traits.character_id=characters.id and character_type=1)";
		$sql .= " as c on t.id=c.trait_id group by t.id,t.name order by num desc ";

		echo '<p class="header">Traits available used by NPCs</p>';
		echo '(todo: exclude npcroom)<br><br>';

		$query = mysql_query2($sql);
		echo "<table><tr><th>Count</th><th>Trait ID</th><th>Trait Name</th></tr>";
		while($result = mysql_fetch_array($query, MYSQL_ASSOC))
		{
				if ($result['num']==0)
					echo '<tr class="color_a"><td><font color=red>'.$result['num'].'</font></td><td>'.$result['id'].'</td><td>'.$result['name'].'</td></tr>';
				else
					echo '<tr class="color_a"><td>'.$result['num'].'</td><td>'.$result['id'].'</td><td>'.$result['name'].'</td></tr>';
		}
		echo "</table>";
	}
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