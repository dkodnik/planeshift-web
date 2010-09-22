<?php

/**
*  Notice that on any SVN database, this page will likely not find any trainers, since all NPCrooms are explicitly excluded from the query.
*/

// This is a support method for checktrainers().
function sum_ranges($current, $new) { 
    $finished = false;
    foreach($current as $i => $range)
    {
        // It's not in this range, if, at the end of this loop, it did not match any ranges, it will be added as a new one.
        if($range['max'] < $new['min'] || $range['min'] > $new['max'])
        { 
            continue;
        }
        if($range['max'] >= $new['max'] && $range['min'] <= $new['min']) // Included in this range.
        {
            $range['status'] .= '<a href="./index.php?do=npc_details&sub=main&npc_id='.$new['player_id'].'"><span style="color: yellow;">'.htmlentities($new['name']).' ('.$new['sector'].'): Range '.$new['min'].'-'.$new['max'].' is included in '.$range['min'].'-'.$range['max'].'!</span></a><br/>';
            $current[$i] = $range;
            $finished = true;
            break;
        }
        // Note that we do not need to check if we need to glue multiple ranges, because the data arrives sorted by min rank.
        if($range['max'] == $new['min']) // Extends this range.
        {
            if($range['max'] < $new['max'])
            {
                $range['max'] = $new['max'];
            }
            $range['npc'][] = $new['name'];
            $current[$i] = $range;
            $finished = true;
            break;
        }
        else
        {
            // overlapping entry.
            $npcs = $range['npc'];
            if($range['max'] < $new['max'])
            {
                $range['max'] = $new['max'];
            }
            $range['npc'][] = $new['name'];
            $range['status'] .= '<a href="./index.php?do=npc_details&sub=main&npc_id='.$new['player_id'].'"><span style="color: yellow;">'.htmlentities($new['name']).' ('.$new['sector'].'): Range '.$new['min'].'-'.$new['max'].' is overlapping with '.$range['min'].'-'.$range['max'].'('.implode(', ', $npcs).')!</span></a><br/>';
            $current[$i] = $range;
            $finished = true;
            break;
        }
    }
    
    if(!$finished)
    {
        $new['status'] = '';
        $new['npc'] = array($npc);
        $current[] = $new;
    }
    return $current;
}

function checktrainers()
{
    if(checkaccess('npcs', 'read'))
    {
        echo '<p class="header">Check Trainers</p>';
        
        $sql = 'SELECT ts.player_id, ts.skill_id, ts.min_rank, ts.max_rank, c.id, c.name, s.name AS sector_name FROM trainer_skills AS ts LEFT JOIN characters AS c ON ts.player_id=c.id LEFT JOIN sectors AS s ON c.loc_sector_id=s.id WHERE character_type=1  ORDER BY min_rank';//AND s.name NOT LIKE "NPCroom%"
        $query = mysql_query2($sql);
        $skills = array();
        // $skills contains as first index an ID. Then on the next dimension, we find an array filled with ranges, each range is an array itself containing min, max, 
        // array(npc), status, name, player_id, sector. The "array(npc) will later contain all npcs belonging to the combined range.
        // $skills[ID][RANGE_NR][RANGE_ELEMENT] is thus a 3d array, with the exception of the element npc, at which point it is 4d.
        
        while($row = mysql_fetch_array($query, MYSQL_ASSOC))
        {
            if($row['max_rank'] < $row['min_rank'])
            {
                $msg = '<a href="./index.php?do=npc_details&sub=main&npc_id='.$row['player_id'].'"><span style="color: red;">'.htmlentities($row['name']).': cannot define higher min ('.$row['min_rank'].') than max ('.$row['max_rank'].')!</span></a><br/>';
                if(isset($skills[$row['skill_id']]))
                {
                    $skills[$row['skill_id']][0]['status'] .= $msg;
                }
                else
                {
                    $skills[$row['skill_id']] = array(array('min' => 0, 'max' => 0, 'npc' => array($row['player_id']), 'status' => $msg));
                }
                unset($msg);
                continue;
            }
            if(isset($skills[$row['skill_id']]))
            {
                $skills[$row['skill_id']] = sum_ranges($skills[$row['skill_id']], array('min' => $row['min_rank'], 'max' => $row['max_rank'], 'name' => $row['name'], 'player_id' => $row['player_id'], 'sector' => $row['sector_name'])); 
            }
            else
            {
                $skills[$row['skill_id']] = array(array('min' => $row['min_rank'], 'max' => $row['max_rank'], 'npc' => array($row['name']), 'status' => '', 'name' => $row['name'], 'player_id' => $row['player_id'], 'sector' => $row['sector_name']));
            }
        }
        
        $sql = 'SELECT skill_id, name FROM skills ORDER BY name';
        $query = mysql_query2($sql);
        
        echo '<table><tr><th>Skill</th><th>Range</th><th>Status</th></tr>';
        $color = 'b';
        while($row = mysql_fetch_array($query, MYSQL_ASSOC))
        {
            $color = ($color == 'a' ? 'b' : 'a');
            $str = '';
            $status = '';
            if(isset($skills[$row['skill_id']]))
            {
                foreach($skills[$row['skill_id']] as $range)
                {
                    $str .= ','.$range['min'].'-'.$range['max'];
                    $status .= $range['status'];
                }
                $str = substr($str, 1);
            }
            if(!isset($skills[$row['skill_id']]) || count($skills[$row['skill_id']]) == 0)
            {
                $status .= '<span style="color: orange;">No Trainer available for this skill!</span>';
            }
            elseif(count($skills[$row['skill_id']]) > 1)
            {
                $status .= '<span style="color: red;">There are gaps between the skill levels!</span>';
            }
            $status = (empty($status) ? 'OK' : $status);
            echo '<tr class="color_'.$color.'"><td><label title="'.$row['skill_id'].'">'.htmlentities($row['name']).'</label></td><td>'.$str.'</td><td>'.$status.'</td></tr>';
        }
        echo '</table>';
    }
    else
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
    }
}
?>