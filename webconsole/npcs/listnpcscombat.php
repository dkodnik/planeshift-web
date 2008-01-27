<?
function listnpcscombat($sorting){
  include('util.php');

  ?>
<SCRIPT language=javascript>

function submit()
{
    //confirm("Are you sure you want to delete this NPC?");
    window.open('index.php?page=npc_actions&npcid=$line[0]&operation=savecombatnpc',200,200);

    exit;
}
</SCRIPT>

<?PHP

  checkAccess('listnpc', '', 'read');

  $invulnerable=false;

  if ($invulnerable==true) {
    echo "<b>List of INVulnerable NPCs</b><br>";
  } else {
    echo "<b>List of Vulnerable NPCs</b><br>";
    echo "<b>Combat View.<b><br>";
    echo "<A HREF=\"index.php?page=listnpcs\">Click here to switch to normal view</A></b>";
  }

  $query = "select c.id, c.name, sec.name, c.loc_x, c.loc_y, c.loc_z, c.npc_spawn_rule, b.npctype,c.kill_exp from characters as c LEFT JOIN sc_npc_definitions as b ON b.char_id=c.id, sectors as sec ";
  $query = $query . " where npc_master_id !=0 and character_type=1 and c.loc_sector_id=sec.id";

  if ($invulnerable==true)
    $query = $query . " and npc_impervious_ind='Y'";
  else
    $query = $query . " and npc_impervious_ind='N'";

  if ($sorting=="id") {
    $query = $query . " order by c.id, c.name";
  } else if ($sorting=="name") {
    $query = $query . " order by c.name, sec.name";
  } else if ($sorting=="sector") {
    $query = $query . " order by sec.name, c.name";
  }
  $result = mysql_query2($query);

  echo "  <TABLE BORDER=1>";
  echo "  <TH> <A HREF=\"index.php?page=listnpcscombat&sorting=id\">ID</A> </TH> <TH> <A HREF=\"index.php?page=listnpcscombat&sorting=name\">NAME</A></TH><TH> IN GAME?</TH><TH> <A HREF=\"index.php?page=listnpcscombat&sorting=sector\">SECTOR</A></TH> <TH> POSITION</TH><TH> BEHAVIOUR</TH><TH>RHand</TH><TH>LHand</TH><TH>_____Skills/Ranks_____</TH><TH>Exp</TH><TH> FUNCTIONS</TH><TH>CHECK</TH>";

  while ($line = mysql_fetch_array($result, MYSQL_NUM)){
    $spawn = "no";
    if ($line[6] != 0){
      $spawn = "yes (<A HREF=index.php?page=listspawnrules&id=$line[6]>".$line[6]."</A>)";
    }
    echo "<TR><TD>$line[0] </TD><TD><A href='index.php?page=viewnpc&id=".$line[0]."'>".$line[1]."</a></TD>";
    
    echo "<TD>".$spawn."</TD>";
    
    echo "<TD ALIGN=CENTER>".$line[2]."</TD><TD>".$line[3].",".$line[4].",".$line[5]."</TD>";
    echo "<TD>$line[7] </TD>";

    $escaped_area = mysql_escape_string($line[1]);

    echo "<FORM METHOD=POST action=\"index.php?page=npc_actions&npcid=$line[0]&operation=savecombatnpc\" target=_new>";

    $bad = 0;

    // extract weapons
    $query = "select s.id,s.name from item_instances i,item_stats s where i.item_stats_id_standard=s.id and i.char_id_owner=$line[0] and equipped_slot='righthand'";
    $result2 = mysql_query2($query);
    $line2 = mysql_fetch_array($result2, MYSQL_NUM);
    if (mysql_num_rows($result2)==0)
      $bad++;
    echo "<td>";
    SelectWeapon($line2[0],"righthand");
    echo "</td>";

    // extract skills
    $query = "select s.id,s.name from item_instances i,item_stats s where i.item_stats_id_standard=s.id and i.char_id_owner=$line[0] and equipped_slot='lefthand'";
    $result2 = mysql_query2($query);
    $line2 = mysql_fetch_array($result2, MYSQL_NUM);
    if (mysql_num_rows($result2)==0)
      $bad++;
    echo "<td>";
    SelectWeapon($line2[0],"lefthand");
    echo "</td>";

    if ($bad==2)
      $bad=1;
    else
      $bad=0;

    $query = "select s.skill_id,s.name,c.skill_rank from skills s,character_skills c where s.skill_id=c.skill_id and c.character_id=$line[0]";
    $result2 = mysql_query2($query);
    echo "<td>";
    $skillcount=1;
    while ($line2 = mysql_fetch_array($result2, MYSQL_NUM)){
      echo "$line2[1]:<INPUT TYPE=TEXT name=skill$skillcount VALUE=$line2[2] size=4><BR>";
      $skillname = "skill".$skillcount."id";
      echo "<INPUT TYPE=HIDDEN NAME=$skillname value=$line2[0]>";
      $skillcount++;
    }
    if (mysql_num_rows($result2)==0)
      $bad++;

    echo "</td>";

    echo "<td><INPUT TYPE=TEXT name=exp VALUE=$line[8] size=4></td>";

    echo "<TD><INPUT TYPE=SUBMIT NAME=submit VALUE=Save>";
    echo "</FORM></TD>";
    if ($bad>=1)
      echo "<TD><font color=red>WARNING!</font></TD></TR>";
    else
      echo "<TD></TD></TR>";
  }
  echo '</TABLE><br><br>';

  if ($invulnerable==true) {
    echo "<FORM ACTION=index.php?page=npc_actions&operation=createnpc METHOD=POST>";
    echo "Create a New NPC with name: <INPUT TYPE=text NAME=npcname>";
    echo " <INPUT TYPE=SUBMIT NAME=submit VALUE=Create>";
    echo '</FORM>';
    
  } else {
    
    echo "<b>Create a New Simple NPC</b>";
    echo "<TABLE><FORM ACTION=index.php?page=npc_actions&operation=createsimplenpc METHOD=POST>";
    echo "<TH>NAME</TH><TH>Description</TH><TH>Race</TH><TH>Stats</TH><TH>HP</TH><TH>SECTOR</TH><TH>X,Y,Z,Rot</TH>";
    echo "<TR><TD><INPUT TYPE=text NAME=npcname></TD><TD><INPUT TYPE=text NAME=description></TD><TD>";
    SelectRace("","race");
    echo "</TD><TD><INPUT TYPE=text NAME=stats></TD><TD><INPUT TYPE=text NAME=hp size=5></TD><TD>";
    SelectSectors("","sector");
    echo "</TD><TD><INPUT TYPE=text NAME=position></TD><TD></TR></TABLE>";

    echo "<TABLE><TH>SPAWN RULE</TH><TH>WEAPON</TH><TH>SKILL RANK</TH><TH>EXP</TH><TR><TD>";
    SelectSpawnRule("","spawnrule");
    echo "</TD><TD>";
    SelectWeapon("","weapon");
    echo "</TD><TD align=center><INPUT TYPE=text NAME=skill_value size=8></TD><TD><INPUT TYPE=text NAME=exp size=5></TD></TR></TABLE>";

    echo "<BR><INPUT TYPE=SUBMIT NAME=submit VALUE=Create>";
    echo '</FORM></TABLE>';
  }
  echo '<br><br>';
}

?>

      
      
      

