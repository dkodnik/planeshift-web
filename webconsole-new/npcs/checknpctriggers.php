<?PHP
function checknpctriggers(){

    if (checkaccess('npcs', 'read'))
    {
        // extract all invulnerable NPC names
        $query = "select c.id, c.name, c.lastname, sec.name, c.loc_x, c.loc_y, c.loc_z, c.npc_spawn_rule from characters as c, sectors as sec ";
        $query = $query . " where c.npc_master_id !=0 and c.loc_sector_id=sec.id";
        $query = $query . " and c.npc_impervious_ind='Y'";
        $query = $query . " order by sec.name, c.name";

        $result = mysql_query2($query);

        echo '<table border="1">';
        echo '<tr><th> ID </th><th> Name</th><th> Sector</th><th> Position</th><th> Missing Triggers</th><th> Loaded in game?</th></tr>';

        // for each NPC
        while ($line = fetchSqlRow($result)) 
        {
            $fullname = $line[1] . " " . $line[2];
            $fullname = trim($fullname);
            // full name gets used below as well, so we can't just escape that one.
            $fullNameEscaped = escapeSqlString($fullname);
            // search his triggers
            $query2 = "select count(*) from npc_triggers where area='$fullNameEscaped' and trigger_text='greetings'";
            $result2 = mysql_query2($query2);
            $line2 = fetchSqlRow($result2);
            $found1 = $line2[0];
            if ($found1=="0") {
                $query2 = "select count(*) from npc_knowledge_areas where area like 'greetings%' and player_id=$line[0]";
                $result2 = mysql_query2($query2);
                $line2 = fetchSqlRow($result2);
                $found1 = $line2[0];
            }

            $query2 = "select count(*) from npc_triggers where area='$fullNameEscaped' and trigger_text='about you'";
            $result2 = mysql_query2($query2);
            $line2 = fetchSqlRow($result2);
            $found2 = $line2[0];

            $query2 = "select count(*) from npc_triggers where area='$fullNameEscaped' and trigger_text='how you'";
            $result2 = mysql_query2($query2);
            $line2 = fetchSqlRow($result2);
            $found3 = $line2[0];
            
            $loaded = "yes";
            if ($line[7]==0)
            {
                $loaded = "<font color=red>no</font>";
            }

            if ($found1=="0" or $found2=="0" or $found3=="0") {
                echo "<tr><td>$line[0] </td><td><a href=\"index.php?do=npc_details&amp;sub=main&amp;npc_id=".$line[0].'">'.$fullname."</a></td><td>$line[3]</td><td>$line[4]/$line[5]/$line[6]</td><td>";
                if ($found1=="0")
                {
                    echo "greetings <br/>";
                }
                if ($found2=="0")
                {
                    echo "about you <br/>";
                }
                if ($found3=="0")
                {
                    echo "how you <br/>";
                }
                echo "</td><td>$loaded</td></tr>";
            }
        }
        echo '</table><br/><br/>';
    }
    else
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
    }
}

?>
