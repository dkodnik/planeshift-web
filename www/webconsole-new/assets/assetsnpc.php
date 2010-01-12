<?php

function assetsnpc()
{
    if(!checkaccess('assets', 'read'))
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
		return;
    }

	echo '<p class="header">NPCs used in game by race</p>';
	echo '(todo: exclude npcroom)<br><br>';

	$sql = "SELECT count(c.id) as num,r.name as name, r.sex as sex from race_info r left join characters c on r.id=c.racegender_id and c.character_type=1 group by r.name,r.sex order by num desc";
	$query = mysql_query2($sql);

	echo "<table><tr><th>Count</th><th>Race</th><th>Gender</th></tr>";
	while($result = mysql_fetch_array($query, MYSQL_ASSOC))
	{
			if ($result['num']==0)
				echo '<tr class="color_a"><td><font color=red>'.$result['num'].'</font></td><td>'.$result['name'].'</td><td>'.$result['sex'].'</td></tr>';
			else
				echo '<tr class="color_a"><td>'.$result['num'].'</td><td>'.$result['name'].'</td><td>'.$result['sex'].'</td></tr>';
	}
	echo "</table>";

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