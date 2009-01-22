<?
function listnpcquest(){
  include('util.php');

  checkAccess('listnpc', '', 'read');

  // extract all invulnerable NPC names
  $query = "select c.id, c.name, c.lastname, sec.name from characters as c, sectors as sec ";
  $query = $query . " where npc_master_id !=0 and c.loc_sector_id=sec.id";
  $query = $query . " and npc_impervious_ind='Y'";
  $query = $query . " order by sec.name, c.name";

  $result = mysql_query2($query);

  echo "  <TABLE BORDER=1>";
  echo "  <TH> ID </TH> <TH> NAME</TH><TH> SECTOR</TH> <TH>Quests</TH><TH>Starting Quests</TH>";

  $distribution = array();
  while ($line = mysql_fetch_array($result, MYSQL_NUM)){

    $npcfullname = $line[1]." ".$line[2];
    $npcfullname = trim($npcfullname);

    echo "<TR><TD>$line[0] </TD><TD><A href='index.php?page=viewnpc&id=".$line[0]."'>".$npcfullname."</a></TD>";

    echo "<TD ALIGN=CENTER>".$line[3]."</TD>";

    $query3 = "select quests.id,name from quest_scripts, quests where quests.id=quest_scripts.quest_id and script like '%$npcfullname:%'";
    $result3 = mysql_query2($query3);
    echo "<TD ALIGN=CENTER>";
    while ($line3 = mysql_fetch_array($result3, MYSQL_NUM)){
      echo " <a href=index.php?page=viewquestscript&id=".$line3[0].">".$line3[1]."<br>";
      $distribution[$line[3]] = $distribution[$line[3]]+1;
    }
    echo "</TD><TD ALIGN=CENTER>";
    $query4 = "select quests.id,name from quest_scripts, quests where quests.id=quest_scripts.quest_id and script REGEXP '".$npcfullname.":.*[Aa]ssign\ [Qq]uest\.'";
    $result4 = mysql_query2($query4);
    while ($line4 = mysql_fetch_array($result4, MYSQL_NUM)){
      echo " <a href=index.php?page=viewquestscript&id=".$line4[0].">".$line4[1]."<br>";
      $distribution[$line[4]] = $distribution[$line[4]]+1;
    }
    echo "</TD></TR>";
  }

  echo '</TABLE><br><br>';
  $keys=array_keys($distribution);
  foreach ($keys as $i) {
      echo "$i : $distribution[$i]<br>";
  }
}

?>

      
      
      

