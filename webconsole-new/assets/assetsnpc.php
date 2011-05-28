<?php

function assetsnpc()
{
    if(!checkaccess('assets', 'read'))
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
		return;
    }

	if (!isset($_GET['op'])) 
    {
		echo 'You have to specify an option.';
		return;
	}

	if ($_GET['op'] == 'npc') 
    {
		echo '<p class="header">NPCs used in game by race</p>';
		echo '(excludes npcrooms, sect 3,68,69,70,71)<br/><br/>';

		$sql = "SELECT count(c.id) AS num, r.name AS name, r.sex AS sex FROM characters AS c, race_info AS r WHERE r.id=racegender_id AND c.character_type=1 AND c.loc_sector_id NOT IN (3,68,69,70,71) GROUP BY r.name, r.sex ORDER BY num DESC";
		$query = mysql_query2($sql);

		echo '<table><tr><th>Count</th><th>Race</th><th>Gender</th></tr>';
		$i=0;
        $characters = array();
		while($result = mysql_fetch_array($query, MYSQL_ASSOC))
		{
				if ($result['num'] == 0)
                {
					echo '<tr class="color_a"><td><font color=red>'.$result['num'].'</font></td><td>'.$result['name'].'</td><td>'.$result['sex'].'</td></tr>';
				}
                else 
                {
					echo '<tr class="color_a"><td>'.$result['num'].'</td><td>'.$result['name'].'</td><td>'.$result['sex'].'</td></tr>';
					$characters[$i] = $result['name'].$result['sex'];
					$i++;
				}
		}
		// find races with no instances
		$sql = 'SELECT name, sex FROM race_info';
		$query = mysql_query2($sql);
		$i=0;
		while($result = mysql_fetch_array($query, MYSQL_ASSOC))
		{
			$races[$i] = $result['name'].$result['sex'];
			$i++;
		}

		$diffs = array_values(array_diff($races, $characters));

		for ($i = 0; $i < count($diffs); $i++) {
			$diflen = strlen($diffs[$i]);
			echo '<tr class="color_a"><td><font color=red>0</font></td><td>'.substr($diffs[$i], 0, $diflen-1).'</td><td>'.substr($diffs[$i], $diflen-1).'</td></tr>';
		}

		echo '</table>';
	} 
    else if ($_GET['op']=='trait') 
    {
		//$sql = "SELECT count(c.trait_id) as num,t.id,t.name from traits t left join ";
		//$sql .= " (select * from character_traits, characters where character_traits.character_id=characters.id and character_type=1)";
		//$sql .= " as c on t.id=c.trait_id group by t.id,t.name order by num desc ";

		$sql = "SELECT count(ct.trait_id) AS num,ct.trait_id, t.name, ri.sex, ri.name AS race FROM character_traits AS ct, characters AS c, traits AS t, race_info AS ri WHERE t.id=ct.trait_id AND ct.character_id=c.id AND t.race_id = ri.id AND character_type=1 AND c.loc_sector_id NOT IN (3,68,69,70,71) GROUP BY ct.trait_id ORDER BY num DESC";

		echo '<p class="header">Traits available used by NPCs</p>';
		echo '(excludes npcrooms, sect 3,68,69,70,71)<br><br>';
    
		$query = mysql_query2($sql);
		echo '<table><tr><th>Count</th><th>Trait ID</th><th>Trait Name</th><th>Race</th><th>Gender</th></tr>';
		$i=0;
        $traits_char_id = array();
		while($result = mysql_fetch_array($query, MYSQL_ASSOC))
		{
				if ($result['num']==0)
                {
					echo '<tr class="color_a"><td><font color=red>'.$result['num'].'</font></td><td>'.$result['id'].'</td><td>'.$result['name'].'</td><td>' . $result['race'] . '</td><td>' . getGenderFromAbbreviation($result['sex']) . '</td></tr>';
				}
                else 
                {
					echo '<tr class="color_a"><td>'.$result['num'].'</td><td>'.$result['trait_id'].'</td><td>'.$result['name'].'</td><td>' . $result['race'] . '</td><td>' . getGenderFromAbbreviation($result['sex']) . '</td></tr>';
					$traits_char_count[$i]=$result['num'];
					$traits_char_id[$i]=$result['trait_id'];
					$traits_char_name[$i]=$result['name'];
					$i++;
				}
		}
		// find traits with no instances
		$sql = 'SELECT t.id, t.name, ri.sex, ri.name AS race FROM traits t, race_info ri WHERE t.race_id = ri.id';
		$query = mysql_query2($sql);
		$i=0;
		
		$traits_id = array();
		$traits_name = array();
		$traits_sex = array();
		$traits_race = array();
		
		while($result = mysql_fetch_array($query, MYSQL_ASSOC))
		{
			$traits_id[$i]=$result['id'];
			$traits_name[$i]=$result['name'];
			$traits_sex[$i]=$result['sex'];
			$traits_race[$i]=$result['race'];
			$i++;
		}

		// compare results
		for ($i = 0; $i < count($traits_id); $i++) 
        {
			if(is_array($traits_char_id) && in_array($traits_id[$i], $traits_char_id))
            {
				continue;
			}
            echo '<tr class="color_a"><td><font color=red>0</font></td><td>'.$traits_id[$i].'</td><td>'.$traits_name[$i].'</td><td>' . $traits_race[$i] . '</td><td>' . getGenderFromAbbreviation($traits_sex[$i]) . '</td></tr>';
		}
        
		echo '</table>';
	}
}


function runBaseQuery($groupid, $period, $time, $to_exclude) {

	$dates = getDatesFromPeriod($period);
	
    $startDate = mysql_real_escape_string($dates[1]);
    $endDate = mysql_real_escape_string($dates[2]);
    $myExclude = mysql_real_escape_string($to_exclude);
    $myTime = mysql_real_escape_string($time);
	$sql = "SELECT count(*) AS result FROM characters AS c WHERE character_type=0 AND last_login IS NOT NULL AND creation_time>=DATE('$startDate') AND creation_time<DATE('$endDate') AND account_id NOT IN $myExclude";
	$sql .= " and time_connected_sec>$myTime";
	//echo $sql;
	$query = mysql_query2($sql);
	$result = mysql_fetch_array($query, MYSQL_ASSOC);
	$counted_items = $result['result'];
	return $counted_items;
}

function getLabelFromTime($time) {
	if ($time<3600)
    {
		return '>'.($time/60).' minutes';
	}
    else
    {
        return '>'.($time/3600).' hours';
    }
}
function getGenderFromAbbreviation($abbr)
{
	switch($abbr)
	{
		case 'M': return 'Male';
		case 'F': return 'Female';
		case 'N': return 'Neuter';
		default: return 'unknown';
	}
}
?>
