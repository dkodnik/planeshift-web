<?php
function checknpctriggers()
{

    if (!checkaccess('npcs', 'read'))
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
        return;
    }
    // extract all invulnerable NPC names
    $sql = "SELECT c.id, c.name, c.lastname, s.name AS sector_name, c.loc_x, c.loc_y, c.loc_z, c.npc_spawn_rule FROM characters AS c LEFT JOIN sectors AS s ";
    $sql .= " ON c.loc_sector_id = s.id WHERE c.npc_master_id !=0 AND c.npc_impervious_ind = 'Y' ORDER BY s.name, c.name";

    $result = mysql_query2($sql);

    echo '<table border="1">';
    echo '<tr><th> ID </th><th>Name</th><th>Sector</th><th>Position</th><th> Missing Triggers</th><th> Loaded in game?</th></tr>'."\n";

    // for each NPC
    while ($row = fetchSqlAssoc($result)) 
    {
        $fullname = $row['name'].' '.$row['lastname'];
        $fullname = trim($fullname);
        // full name gets used below as well, so we can't just escape that one.
        $fullNameEscaped = escapeSqlString($fullname);
        
        // search all triggers for this npc.
        $foundGreetings = false;
        $foundAboutYou = false;
        $foundHowYou = false;
        $sql2 = "SELECT trigger_text FROM npc_triggers WHERE area = '$fullNameEscaped'";
        $result2 = mysql_query2($sql2);
        while ($row2 = fetchSqlAssoc($result2))
        {
            // multiple triggers may be stacked seperated by dots.
            $triggers = explode('.', $row2['trigger_text']);
            foreach ($triggers as $trigger)
            {
                if (strtolower(trim($trigger)) == 'greetings')
                {
                    $foundGreetings = true;
                }
                if (strtolower(trim($trigger)) == 'about you')
                {
                    $foundAboutYou = true;
                }
                if (strtolower(trim($trigger)) == 'how you')
                {
                    $foundHowYou = true;
                }
            }
        }
        if (!$foundGreetings)
        {
            $sql2 = "select count(*) from npc_knowledge_areas where area like 'greetings%' and player_id = {$row['id']} ";
            $result2 = mysql_query2($sql2);
            $row2 = fetchSqlRow($result2);
            if ($row2[0] > 0)
            {
                $foundGreetings = true;
            }
        }

        if (!$foundGreetings || !$foundAboutYou || !$foundHowYou) 
        {
            echo '<tr><td>'.$row['id'].'</td><td><a href="index.php?do=npc_details&amp;sub=main&amp;npc_id='.$row['id'] .'">'.htmlentities($fullname).'</a></td>';
            echo '<td>'.$row['sector_name'].'</td><td>'.$row['loc_x'].'/'.$row['loc_y'].'/'.$row['loc_z'].'</td><td>';
            if (!$foundGreetings)
            {
                echo 'greetings <br/>';
            }
            if (!$foundAboutYou)
            {
                echo 'about you <br/>';
            }
            if (!$foundHowYou)
            {
                echo 'how you <br/>';
            }
            echo '</td><td>'.($row['npc_spawn_rule'] == 0 ? '<span class="error">no</span>' : 'yes').'</td></tr>'."\n";
        }
    }
    echo '</table><br/><br/>';
}

?>
