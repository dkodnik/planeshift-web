<?
function listnpcs($invulnerable)
{
  include('util.php');
?>

<SCRIPT language=javascript>

function confirmDelete()
{
    return confirm("Are you sure you want to delete this NPC?");
}
</SCRIPT>

<?PHP

    checkAccess('listnpc', '', 'read');

    if ($invulnerable==true) 
    {
        echo "<H2>List of INVulnerable NPCs</H2>";
    } 
    else 
    {
        echo "<b>List of Vulnerable NPCs</b><br>";
        echo "<b><A HREF=\"index.php?page=listnpcscombat\">Click here to switch to combat view</A></b>";
    }

    $query = "select c.id, c.name, sec.name, c.loc_x, c.loc_y, c.loc_z, c.npc_spawn_rule, b.npctype,c.npc_addl_loot_category_id, b.region from characters as c LEFT JOIN sc_npc_definitions as b ON b.char_id=c.id, sectors as sec ";
    $query = $query . " where npc_master_id !=0 and character_type=1 and c.loc_sector_id=sec.id";
  
    $sector = $_GET['sector'];
    if ($sector!=null and $sector!="") 
    {
        $query = $query . " and c.loc_sector_id=".$sector;
    }

    if ($invulnerable==true)
        $query = $query . " and npc_impervious_ind='Y'";
    else
        $query = $query . " and npc_impervious_ind='N'";

    $query = $query . " order by sec.name, c.name";
    $result = mysql_query2($query);

    echo "  <TABLE BORDER=1>";
    echo "  <TH> ID </TH> <TH> NAME</TH><TH> Spawn / Loot</TH><TH> SECTOR</TH> <TH> POSITION</TH><TH>BEHAVIOR</TH><TH>Personal Triggers</TH><TH>Region</TH><TH>NA</TH><TH> FUNCTIONS</TH><TH>CHECK</TH>";

    while ($line = mysql_fetch_array($result, MYSQL_NUM))
    {
        $spawn = "(none)";
        if ($line[6] != 0)
        {
            $spawn = "(<A HREF=index.php?page=listspawnrules&selectedrule=$line[6]>".$line[6]."</A>)";
        }

        $loot = "(none)";
        if ($line[8] != 0)
        {
            $loot = "(<A HREF=index.php?page=listlootcategories&selectedloot=$line[8]>".$line[8]."</A>)";
        }

        echo "<TR><TD>$line[0] </TD><TD><A href='index.php?page=viewnpc&id=".$line[0]."'>".$line[1]."</a></TD>";
    
        echo "<TD>".$spawn." ".$loot."</TD>";
    
        echo "<TD ALIGN=CENTER>".$line[2]."</TD><TD>".$line[3].",".$line[4].",".$line[5]."</TD>";
        echo "<TD>$line[7] </TD>";

        $escaped_area = mysql_escape_string($line[1]);
        $query3 = "select count(*) from npc_triggers where area='$escaped_area'";
        $result3 = mysql_query2($query3);
        $line3 = mysql_fetch_array($result3, MYSQL_NUM);
        $numtrig = $line3[0];

        if ($numtrig==0) 
        {
            echo "<TD><font color=red>$numtrig</TD>";
        } 
        else
        {
            echo "<TD>$numtrig</TD>";
        }

        echo "<td>$line[9]</td>";

        echo "<td>-1</td>";

        echo "<TD><FORM ACTION=index.php?page=npc_actions&npcid=$line[0]&operation=deletenpc METHOD=POST onsubmit=\"return confirmDelete()\">";
        echo "<INPUT TYPE=SUBMIT NAME=submit VALUE=Delete>";
        echo "</FORM></TD>";

        $query = "select count(*) from npc_knowledge_areas where area='general' and player_id=$line[0]";
        $result3 = mysql_query2($query);
        $line3 = mysql_fetch_array($result3, MYSQL_NUM);
        if ($line3[0]==0) 
        {
            echo "<TD><font color=red>general area missing</font></TD></TR>";
        } 
        else
        {
            echo "<TD></TD></TR>";
        }
    }
  
    echo '</TABLE><br><br>';

    if ($invulnerable==true) 
    {
        echo "<FORM ACTION=index.php?page=npc_actions&operation=createnpc METHOD=POST>";
        echo "Create a New NPC with name: <INPUT TYPE=text NAME=npcname> Lastname: <INPUT TYPE=text NAME=npclastname>";
        echo " <INPUT TYPE=SUBMIT NAME=submit VALUE=Create>";
        echo '</FORM>';
    } 
    else 
    {
        echo "<b>Create a New Simple NPC</b>";
        echo "<TABLE><FORM ACTION=index.php?page=npc_actions&operation=createsimplenpc METHOD=POST>";
        echo "<TH>NAME</TH><TH>Description</TH><TH>Race</TH><TH>Stats(S,A,E,I,W,C)</TH><TH>HP</TH><TH>SECTOR</TH><TH>X,Y,Z,Rot</TH>";
        echo "<TR><TD><INPUT TYPE=text NAME=npcname></TD><TD><INPUT TYPE=text NAME=description></TD><TD>";
        SelectRace("","race");
        echo "</TD><TD><INPUT TYPE=text NAME=stats></TD><TD><INPUT TYPE=text NAME=hp size=5></TD><TD>";
        SelectSectors("","sector");
        echo "</TD><TD><INPUT TYPE=text NAME=position></TD><TD></TR></TABLE>";

        echo "<TABLE><TH>SPAWN RULE</TH><TH>WEAPON</TH><TH>Behavior</TH><TH>Region</TH><TH>SKILL RANK</TH><TH>EXP</TH><TR><TD>";
        SelectSpawnRule("","spawnrule");
        echo "</TD><TD>";
        SelectWeapon("","weapon");
        echo "</TD><TD>";
        SelectBehavior("","behavior");
        echo "</TD><TD>";
        SelectRegion("","region");
        echo "</TD><TD align=center><INPUT TYPE=text NAME=skill_value size=8></TD><TD><INPUT TYPE=text NAME=exp size=5></TD></TR></TABLE>";

        echo "<BR><INPUT TYPE=SUBMIT NAME=submit VALUE=Create>";
        echo '</FORM></TABLE>';
    }
    echo '<br><br>';
}
?>

      
      
      

