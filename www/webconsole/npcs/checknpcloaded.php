<?
function checknpcloaded(){
  include('util.php');

  checkAccess('listnpc', '', 'read');

  // extract all NPCs names that are not loaded in game
  $query = "select c.id, c.name, c.lastname, sec.name, c.loc_x, c.loc_y, c.loc_z, c.npc_spawn_rule from characters as c, sectors as sec ";
  $query = $query . " where npc_master_id !=0 and c.loc_sector_id=sec.id";
  $query = $query . " and character_type!=2 and npc_spawn_rule<1";
  $query = $query . " order by sec.name, c.name";

  $result = mysql_query2($query);

  echo "<h3>All NPCs that are not loaded in game (Pets excluded)</h3>";
  echo "  <TABLE BORDER=1>";
  echo "  <TH> ID </TH> <TH> NAME</TH><TH> Sector</TH><TH> Position</TH><TH> Loaded in game?</TH>";

  // for each NPC
  while ($line = mysql_fetch_array($result, MYSQL_NUM)) {

    $loaded = "yes";
    if ($line[7]==0)
      $loaded = "<font color=red>no</font>";

    $fullname = $line[1] . " " . $line[2];
    $fullname = trim($fullname);

    echo "<TR><TD>$line[0] </TD><TD><A href='index.php?page=viewnpc&id=".$line[0]."'>".$fullname."</a></TD><TD>$line[3]</TD><TD>$line[4]/$line[5]/$line[6]</TD>";
    echo "<TD>$loaded</TD></TR>";
  }
  echo '</TABLE><br><br>';

  echo '<br><br>';

  echo "<h3>All NPC Loaded in NPCRoom or in wrong sector</h3>";
  // NPC Loaded in NPCRoom or in wrong sector
  $query = "select c.id, c.name, c.lastname, sec.name, c.loc_x, c.loc_y, c.loc_z, c.npc_spawn_rule from characters as c, sectors as sec ";
  $query = $query . " where npc_master_id !=0 and c.loc_sector_id=sec.id";
  $query = $query . " and npc_spawn_rule>0 and (loc_sector_id<1 or loc_sector_id=3)";
  $query = $query . " order by sec.name, c.name";

  $result = mysql_query2($query);

  echo "  <TABLE BORDER=1>";
  echo "  <TH> ID </TH> <TH> NAME</TH><TH> Sector</TH><TH> Position</TH><TH> Loaded in game?</TH>";

  // for each NPC
  while ($line = mysql_fetch_array($result, MYSQL_NUM)) {
    
    $loaded = "yes";
    if ($line[6]==0)
      $loaded = "<font color=red>no</font>";

    $fullname = $line[1] . " " . $line[2];
    $fullname = trim($fullname);

    echo "<TR><TD>$line[0] </TD><TD><A href='index.php?page=viewnpc&id=".$line[0]."'>".$fullname."</a></TD><TD>$line[3]</TD><TD>$line[4]/$line[5]/$line[6]</TD>";
    echo "<TD>$loaded</TD></TR>";
  }
  echo '</TABLE><br><br>';

  echo '<br><br>';




}

?>

      
      
      

