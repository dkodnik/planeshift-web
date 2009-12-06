<?PHP
function checknpcchar()
{

    if(!checkAccess('npcs', 'read'))
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
        return;
    }
    $query = "SELECT s.name, c.id, c.name, c.lastname, ra.name, ra.sex, description, r.response1";
    $query = $query . " FROM characters c, sectors s, npc_triggers t, npc_responses r, race_info ra ";
    $query = $query . " WHERE TRIM(CONCAT(c.name,' ',c.lastname))=t.area AND s.id=c.loc_sector_id AND t.id=r.trigger_id AND c.racegender_id=ra.id ";
    $query = $query . " AND t.trigger_text='about you' AND c.character_type=1 AND c.npc_spawn_rule!=0 ";
    $query = $query . " ORDER BY s.name, c.name;";

    $result = mysql_query2($query);

    $num = mysql_num_rows($result);
  
    echo "<p>Note that the NPCs without the basic triggers will not be displayed here. Use the report \"Check NPC Triggers\" first.<br /><br />";
    echo "Found $num NPCs</p>";

    echo "  <TABLE BORDER=1>";
    echo "  <TH> Sector </TH> <TH> ID</TH> <TH> NAME</TH><TH> Race </TH><TH> Gender </TH><TH> description </TH><TH> about you answer </TH>";

    // for each NPC
    while ($line = mysql_fetch_array($result, MYSQL_NUM)) 
    {
        echo "<TR><TD>$line[0]</TD><TD>$line[1] </TD><TD><A href='index.php?do=npc_details&sub=kas&npc_id=".$line[1]."'>".$line[2]." ".$line[3]."</a></TD><TD>$line[4]</TD><TD>$line[5]</TD>";
        echo "<TD>$line[6]</TD><TD>$line[7]</TD><TR>";
    }
    echo '</TABLE><br><br>';

    echo '<br><br>';
}

?>