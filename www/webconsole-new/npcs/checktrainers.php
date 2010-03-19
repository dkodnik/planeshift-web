<?php

function sum_ranges($current, $new, $npc, $names) {
    $finished = false;
    foreach($current as $i => $range)
    {
        if($range['max'] < $new['min'] || $range['min'] > $new['max'])
        { // It's not in this range...
            continue;
        }
        if($range['max'] >= $new['max'] && $range['min'] <= $new['min'])
        {// Included in this range.
            $range['status'] .= '<a href="./index.php?do=npc_details&sub=main&npc_id='.$npc.'"><span style="color: yellow;">'.htmlentities($names[$npc]).': Range '.$new['min'].'-'.$new['max'].' is included in '.$range['min'].'-'.$range['max'].'!</span></a><br/>';
            $current[$i] = $range;
            $finished = true;
            break;
        }
        if($range['max'] == $new['min'])
        {// Extends this range.
            if($range['max'] < $new['max'])
            {
                $range['max'] = $new['max'];
            }
            $range['npc'][] = $npc;
            $current[$i] = $range;
            $finished = true;
            break;
        }
        else
        {
            $npcs = $range['npc'];
            foreach($npcs as $z => $id)
            {
                $npcs[$z] = $names[$id];
            }
            $range['status'] .= '<a href="./index.php?do=npc_details&sub=main&npc_id='.$npc.'"><span style="color: red;">'.htmlentities($names[$npc]).': Range '.$new['min'].'-'.$new['max'].' is overlapping with '.$range['min'].'-'.$range['max'].'('.implode(', ', $npcs).')!</span></a><br/>';
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
    if(checkaccess('npc', 'read'))
    {
        echo '<p class="header">Check Trainers</p>';
        
        $sql = 'SELECT id, name FROM characters WHERE character_type=1';
        $query = mysql_query2($sql);
        $names = array();
        while($row = mysql_fetch_array($query, MYSQL_ASSOC))
        {
            $names[$row['id']] = $row['name'];
        }
        
        $sql = 'SELECT player_id, skill_id, min_rank, max_rank FROM trainer_skills ORDER BY min_rank';
        $query = mysql_query2($sql);
        $skills = array();
        
        while($row = mysql_fetch_array($query, MYSQL_ASSOC))
        {
            if($row['max_rank'] < $row['min_rank'])
            {
                $msg = '<a href="./index.php?do=npc_details&sub=main&npc_id='.$row['player_id'].'"><span style="color: red;">'.htmlentities($names[$row['player_id']]).': cannot define higher min ('.$row['min_rank'].') than max ('.$row['max_rank'].')!</span></a><br/>';
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
                $skills[$row['skill_id']] = sum_ranges($skills[$row['skill_id']], array('min' => $row['min_rank'], 'max' => $row['max_rank']), $row['player_id'], $names);
            }
            else
            {
                $skills[$row['skill_id']] = array(array('min' => $row['min_rank'], 'max' => $row['max_rank'], 'npc' => array($row['player_id']), 'status' => ''));
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
                $status .= '<span style="color: yellow;">No Trainer available for this skill!</span>';
            }
            elseif(count($skills[$row['skill_id']]) > 1)
            {
                $status .= '<span style="color: yellow;">There are gaps between the skill levels!</span>';
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