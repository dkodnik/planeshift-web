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

        echo "  <TABLE BORDER=1>";
        echo "  <TH> ID </TH> <TH> NAME</TH><TH> Sector</TH><TH> Position</TH><TH> Missing Triggers</TH><TH> Loaded in game?</TH>";

        // for each NPC
        while ($line = fetchSqlRow($result)) 
        {

            $fullname = $line[1] . " " . $line[2];
            $fullname = trim($fullname);
            // search his triggers
            $query2 = "select count(*) from npc_triggers where area='$fullname' and trigger_text='greetings'";
            $result2 = mysql_query2($query2);
            $line2 = fetchSqlRow($result2);
            $found1 = $line2[0];
            if ($found1=="0") {
                $query2 = "select count(*) from npc_knowledge_areas where area like 'greetings%' and player_id=$line[0]";
                $result2 = mysql_query2($query2);
                $line2 = fetchSqlRow($result2);
                $found1 = $line2[0];
            }

            $query2 = "select count(*) from npc_triggers where area='$fullname' and trigger_text='about you'";
            $result2 = mysql_query2($query2);
            $line2 = fetchSqlRow($result2);
            $found2 = $line2[0];

            $query2 = "select count(*) from npc_triggers where area='$fullname' and trigger_text='how you'";
            $result2 = mysql_query2($query2);
            $line2 = fetchSqlRow($result2);
            $found3 = $line2[0];
            
            $loaded = "yes";
            if ($line[7]==0)
            {
                $loaded = "<font color=red>no</font>";
            }

            if ($found1=="0" or $found2=="0" or $found3=="0") {
                echo "<TR><TD>$line[0] </TD><TD><A href='index.php?do=npc_details&sub=main&npc_id=".$line[0]."'>".$fullname."</a></TD><TD>$line[3]</TD><TD>$line[4]/$line[5]/$line[6]</TD><TD>";
                if ($found1=="0")
                {
                    echo "greetings <br>";
                }
                if ($found2=="0")
                {
                    echo "about you <br>";
                }
                if ($found3=="0")
                {
                    echo "how you <br>";
                }
                echo "</TD><TD>$loaded</TD></TR>";
            }
        }
        echo '</TABLE><br><br>';

        echo '<br><br>';
    }
    else
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
    }
}

?>
