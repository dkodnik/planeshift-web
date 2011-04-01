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

    $num = mysql_num_rows($result);
  
    echo "<p>Note that the NPCs without the basic triggers will not be displayed here. Use the report \"Check NPC Triggers\" first.<br /><br />";
    echo "Found $num NPCs</p>";

    echo "  <TABLE BORDER=1>";
    echo "  <TH> Sector </TH> <TH> ID</TH> <TH> NAME</TH><TH> Race </TH><TH> Gender </TH><TH> description </TH><TH> greetings </TH><TH> about you answers </TH><TH> how are you answers </TH>";

    // for each NPC
    while ($line = mysql_fetch_array($result, MYSQL_NUM)) 
    {
	    $fullname = $line[2] . " " . $line[3];
	    $fullname = trim($fullname);
	    // search his triggers
	    $query2 = "select response1,response2,response3 from npc_triggers t , npc_responses r where t.id=r.trigger_id and area='$fullname' and trigger_text='greetings'";
	    $result2 = mysql_query2($query2);
	    $line2 = mysql_fetch_array($result2, MYSQL_NUM);
	    $found1a = $line2[0];
	    $found1b = $line2[1];
	    $found1c = $line2[2];

	    $query2 = "select response1,response2,response3 from npc_triggers t , npc_responses r where t.id=r.trigger_id and area='$fullname' and trigger_text='about you'";
	    $result2 = mysql_query2($query2);
	    $line2 = mysql_fetch_array($result2, MYSQL_NUM);
	    $found2a = $line2[0];
	    $found2b = $line2[1];
	    $found2c = $line2[2];

	    $query2 = "select response1,response2,response3 from npc_triggers t , npc_responses r where t.id=r.trigger_id and area='$fullname' and trigger_text='how you'";
	    $result2 = mysql_query2($query2);
	    $line2 = mysql_fetch_array($result2, MYSQL_NUM);
	    $found3a = $line2[0];
	    $found3b = $line2[1];
	    $found3c = $line2[2];

        echo "<TR><TD>$line[0]</TD><TD>$line[1] </TD><TD><A href='index.php?do=npc_details&sub=kas&npc_id=".$line[1]."'>".$fullname."</a></TD><TD>$line[4]</TD><TD>$line[5]</TD>";
        echo "<TD>$line[6]</TD><TD>$found1a / <br>$found1b / <br>$found1c</TD><TD>$found2a / <br>$found2b / <br>$found2c</TD>";
	echo "<TD>$found3a / <br>$found3b / <br>$found3c</TD></TR>";
    }
    echo '</TABLE><br><br>';

    echo '<br><br>';
}

?>