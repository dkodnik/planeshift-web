<?
function checknpcchar(){
  include('util.php');

  checkAccess('listnpc', '', 'read');

  // extract all invulnerable NPC names
  $query = "select s.name, c.id, c.name, c.lastname, ra.name, ra.sex, description, r.response1";
  $query = $query . " from characters c, sectors s, npc_triggers t, npc_responses r, race_info ra ";
  $query = $query . " where TRIM(CONCAT(c.name,' ',c.lastname))=t.area and s.id=c.loc_sector_id and t.response_id=r.id and c.racegender_id=ra.race_id ";
  $query = $query . " and trigger=\"about you\" and character_type=1 and npc_impervious_ind='Y' and npc_spawn_rule!=0 ";
  $query = $query . " order by s.name, c.name;";

  $result = mysql_query2($query);
  $num = mysql_num_rows($result);
  
  echo "<BR><b>Please use this page with care, everytime you refresh it, it does a lot of work on the database.</b><br><br>";
  echo "<b>Found $num NPCs</b><br><br>";

  echo "  <TABLE BORDER=1>";
  echo "  <TH> Sector </TH> <TH> ID</TH> <TH> NAME</TH><TH> Race </TH><TH> Gender </TH><TH> description </TH><TH> about you answer </TH>";

  // for each NPC
  while ($line = mysql_fetch_array($result, MYSQL_NUM)) {
      echo "<TR><TD>$line[0]</TD><TD>$line[1] </TD><TD><A href='index.php?page=viewnpc&id=".$line[1]."'>".$line[2]." ".$line[3]."</a></TD><TD>$line[4]</TD><TD>$line[5]</TD>";
      echo "<TD>$line[6]</TD><TD>$line[7]</TD><TR>";
  }
  echo '</TABLE><br><br>';

  echo '<br><br>';
}

?>

      
      
      

