<?
function checknpctriggers(){
  include('util.php');

  checkAccess('listnpc', '', 'read');

  // extract all invulnerable NPC names
  $query = "select c.id, c.name, c.lastname, sec.name, c.loc_x, c.loc_y, c.loc_z, c.npc_spawn_rule from characters as c, sectors as sec ";
  $query = $query . " where npc_master_id !=0 and c.loc_sector_id=sec.id";
  $query = $query . " and npc_impervious_ind='Y'";
  $query = $query . " order by sec.name, c.name";

  $result = mysql_query2($query);

  echo "  <TABLE BORDER=1>";
  echo "  <TH> ID </TH> <TH> NAME</TH><TH> Sector</TH><TH> Position</TH><TH> Missing Triggers</TH><TH> Loaded in game?</TH>";

  // for each NPC
  while ($line = mysql_fetch_array($result, MYSQL_NUM)) {

    $fullname = $line[1] . " " . $line[2];
    $fullname = trim($fullname);
    // search his triggers
    $query2 = "select count(*) from npc_triggers where area='$fullname' and trigger='greetings'";
    $result2 = mysql_query2($query2);
    $line2 = mysql_fetch_array($result2, MYSQL_NUM);
    $found1 = $line2[0];
    if ($found1=="0") {
      $query2 = "select count(*) from npc_knowledge_areas where area like 'greetings%' and player_id=$line[0]";
      $result2 = mysql_query2($query2);
      $line2 = mysql_fetch_array($result2, MYSQL_NUM);
      $found1 = $line2[0];
    }

    $query2 = "select count(*) from npc_triggers where area='$fullname' and trigger='about you'";
    $result2 = mysql_query2($query2);
    $line2 = mysql_fetch_array($result2, MYSQL_NUM);
    $found2 = $line2[0];

    $query2 = "select count(*) from npc_triggers where area='$fullname' and trigger='how you'";
    $result2 = mysql_query2($query2);
    $line2 = mysql_fetch_array($result2, MYSQL_NUM);
    $found3 = $line2[0];
    
    $loaded = "yes";
    if ($line[7]==0)
      $loaded = "<font color=red>no</font>";

    if ($found1=="0" or $found2=="0" or $found3=="0") {
      echo "<TR><TD>$line[0] </TD><TD><A href='index.php?page=viewnpc&id=".$line[0]."'>".$fullname."</a></TD><TD>$line[3]</TD><TD>$line[4]/$line[5]/$line[6]</TD><TD>";
      if ($found1=="0")
        echo "greetings ";
      if ($found2=="0")
        echo "about you";
      if ($found3=="0")
        echo "how you";
      echo "</TD><TD>$loaded</TD></TR>";
    }
  }
  echo '</TABLE><br><br>';

  echo '<br><br>';
}

?>

      
      
      

