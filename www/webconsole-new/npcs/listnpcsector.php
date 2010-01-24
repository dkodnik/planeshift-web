<?php
function listnpcsector(){
  if (!checkaccess('npcs', 'read')){
    echo '<p class="error">You are not authorized to use these functions</p>';
	return;
  }
  
  echo '<h1>List of invulnerable NPCs divided by sector</h1>';
  echo '<table border="1">';
  echo '<th>Count</th><th>Sector</th><th>Race</th>';
    $query = "select count(r.name) as num, s.name as sector, r.name as race from characters c, race_info r, sectors s where c.racegender_id=r.id and s.id=c.loc_sector_id and c.npc_impervious_ind='N' and c.character_type=1 group by s.name,r.name order by s.name";

    $result = mysql_query2($query);
    if (mysql_num_rows($result) > 0){
      while ($row = mysql_fetch_array($result)){
        echo '<tr>';
        echo '<td>'.$row['num'].'</td>';
        $T_id = $row['id'];
        echo '<td>'.$row['sector'].'</td>';
        echo '<td>'.$row['race'].'</td>';
        echo '</tr>';
      }
      echo '</table>';
    }else{
      echo '</table>';
      echo '<p class="error">No NPCs Found</p>';
    }
}
?>
