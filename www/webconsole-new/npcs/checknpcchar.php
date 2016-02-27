<?PHP
function checknpcchar()
{

    if(!checkAccess('npcs', 'read'))
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
        return;
    }
    $query = "SELECT s.name, c.id, c.name, c.lastname, ra.name, ra.sex, description";
    $query = $query . " FROM characters c, sectors s, race_info ra ";
    $query = $query . " WHERE s.id=c.loc_sector_id AND c.racegender_id=ra.id ";
    $query = $query . " AND c.character_type=1 AND c.npc_impervious_ind='Y' ";
    $query = $query . " ORDER BY s.name, c.name;";

    $result = mysql_query2($query);

    $num = sqlNumRows($result);
  
    echo '<p>Note that the NPCs without the basic triggers will not be displayed here. Use the report "Check NPC Triggers" first.<br /><br />';
    echo "Found $num NPCs</p>";

    echo '<table border="1">';
    echo "<tr><th> Sector </th><th> ID</th><th> NAME</th><th> Race </th><th> Gender </th><th> description </th><th> greetings </th><th> about you answers </th><th> how are you answers </th></tr>";

    // for each NPC
    while ($line = fetchSqlRow($result)) 
    {
	    $fullname = $line[2] . " " . $line[3];
	    $fullname = trim($fullname);
        // full name gets used below as well, so we can't just escape that one.
        $fullNameEscaped = escapeSqlString($fullname);
	    // search his triggers
	    $query2 = "select response1,response2,response3 from npc_triggers t , npc_responses r where t.id=r.trigger_id and area='$fullNameEscaped' and trigger_text='greetings'";
	    $result2 = mysql_query2($query2);
	    $line2 = fetchSqlRow($result2);
	    $found1a = $line2[0];
	    $found1b = $line2[1];
	    $found1c = $line2[2];

	    $query2 = "select response1,response2,response3 from npc_triggers t , npc_responses r where t.id=r.trigger_id and area='$fullNameEscaped' and trigger_text='about you'";
	    $result2 = mysql_query2($query2);
	    $line2 = fetchSqlRow($result2);
	    $found2a = $line2[0];
	    $found2b = $line2[1];
	    $found2c = $line2[2];

	    $query2 = "select response1,response2,response3 from npc_triggers t , npc_responses r where t.id=r.trigger_id and area='$fullNameEscaped' and trigger_text='how you'";
	    $result2 = mysql_query2($query2);
	    $line2 = fetchSqlRow($result2);
	    $found3a = $line2[0];
	    $found3b = $line2[1];
	    $found3c = $line2[2];

        echo "<tr><td>$line[0]</td><td>$line[1] </td><td><a href=\"index.php?do=npc_details&amp;sub=kas&amp;npc_id=".$line[1].'">'.htmlentities($fullname)."</a></td><td>$line[4]</td><td>$line[5]</td>";
        echo "<td>$line[6]</td><td>$found1a / <br/>$found1b / <br/>$found1c</td><td>$found2a / <br/>$found2b / <br/>$found2c</td>";
	echo "<td>$found3a / <br/>$found3b / <br/>$found3c</td></tr>";
    }
    echo '</table><br/><br/>';

}

?>