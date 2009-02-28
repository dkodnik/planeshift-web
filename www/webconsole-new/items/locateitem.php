<?
function locateitem(){
  if (checkaccess('items', 'read')){
    if (isset($_POST['search'])){
      if ($_POST['search'] == "Find Items"){
        echo 'Finding item';
        $itemstat = mysql_real_escape_string($_POST['itemid']);
        $query = "SELECT i.id, s.name, i.char_id_owner, i.char_id_guardian, i.parent_item_id, i.location_in_parent, i.stack_count, sec.name as sector, i.loc_x, i.loc_y, i.loc_z, i.loc_instance, i.flags FROM item_instances AS i LEFT JOIN sectors AS sec ON i.loc_sector_id=sec.id LEFT JOIN item_stats AS s ON i.item_stats_id_standard=s.id WHERE item_stats_id_standard=$itemstat";
        $result = mysql_query2($query);
        echo '<table border="1"><tr><th>Instance ID</th><th>Name</th><th>Owner ID</th><th>Guardian ID</th><th>Parent Item</th><th>Location in Parent</th><th>Stack Count</th><th>Sector</th><th>X</th><th>Y</th><th>Z</th><th>Instance</th><th>Flags</th></tr>'."\n";
        while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
          echo '<tr><td>'.$row['id'].'</td>';
          echo '<td>'.$row['name'].'</td>';
          echo '<td>'.$row['char_id_owner'].'</td>';
          echo '<td>'.$row['char_id_guardian'].'</td>';
          echo '<td>'.$row['parent_item_id'].'</td>';
          echo '<td>'.LocationToString($row['location_in_parent']).'</td>';
          echo '<td>'.$row['stack_count'].'</td>';
          echo '<td>'.$row['sector'].'</td>';
          echo '<td>'.$row['loc_x'].'</td><td>'.$row['loc_y'].'</td><td>'.$row['loc_z'].'</td><td>'.$row['loc_instance'].'</td>';
          echo '<td>'.$row['flags'].'</td></tr>'."\n";
        }
        echo '</table>';
      }else if ($_POST['search'] == "Droped Items"){
        echo 'Droped Items';
        $query = "SELECT i.id, s.name, i.char_id_owner, i.char_id_guardian, i.parent_item_id, i.location_in_parent, i.stack_count, sec.name as sector, i.loc_x, i.loc_y, i.loc_z, i.loc_instance, i.flags FROM item_instances AS i LEFT JOIN sectors AS sec ON i.loc_sector_id=sec.id LEFT JOIN item_stats AS s ON i.item_stats_id_standard=s.id WHERE i.char_id_owner=0";
        if ($_POST['sectorid'] != ''){
          $sectorid = mysql_real_escape_string($_POST['sectorid']);
          $query = $query . " AND i.loc_sector_id='$sectorid'";
        }
        $result = mysql_query2($query);
        echo '<table border="1"><tr><th>Instance ID</th><th>Name</th><th>Owner ID</th><th>Guardian ID</th><th>Parent Item</th><th>Location in Parent</th><th>Stack Count</th><th>Sector</th><th>X</th><th>Y</th><th>Z</th><th>Instance</th><th>Flags</th></tr>'."\n";
        while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
          echo '<tr><td>'.$row['id'].'</td>';
          echo '<td>'.$row['name'].'</td>';
          echo '<td>'.$row['char_id_owner'].'</td>';
          echo '<td>'.$row['char_id_guardian'].'</td>';
          echo '<td>'.$row['parent_item_id'].'</td>';
          echo '<td>'.LocationToString($row['location_in_parent']).'</td>';
          echo '<td>'.$row['stack_count'].'</td>';
          echo '<td>'.$row['sector'].'</td>';
          echo '<td>'.$row['loc_x'].'</td><td>'.$row['loc_y'].'</td><td>'.$row['loc_z'].'</td><td>'.$row['loc_instance'].'</td>';
          echo '<td>'.$row['flags'].'</td></tr>'."\n";
        }
        echo '</table>';
      }else if ($_POST['search'] == "Find Instance"){
        $iid = mysql_real_escape_string($_POST['iid']);
        $query = "SELECT i.id, s.name, i.char_id_owner, i.char_id_guardian, i.parent_item_id, i.location_in_parent, i.stack_count, sec.name as sector, i.loc_x, i.loc_y, i.loc_z, i.loc_instance, i.flags FROM item_instances AS i LEFT JOIN sectors AS sec ON i.loc_sector_id=sec.id LEFT JOIN item_stats AS s ON i.item_stats_id_standard=s.id WHERE i.id=$iid";
        $result = mysql_query2($query);
        echo '<table border="1"><tr><th>Instance ID</th><th>Name</th><th>Owner ID</th><th>Guardian ID</th><th>Parent Item</th><th>Location in Parent</th><th>Stack Count</th><th>Sector</th><th>X</th><th>Y</th><th>Z</th><th>Instance</th><th>Flags</th></tr>'."\n";
        while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
          echo '<tr><td>'.$row['id'].'</td>';
          echo '<td>'.$row['name'].'</td>';
          echo '<td>'.$row['char_id_owner'].'</td>';
          echo '<td>'.$row['char_id_guardian'].'</td>';
          echo '<td>'.$row['parent_item_id'].'</td>';
          echo '<td>'.LocationToString($row['location_in_parent']).'</td>';
          echo '<td>'.$row['stack_count'].'</td>';
          echo '<td>'.$row['sector'].'</td>';
          echo '<td>'.$row['loc_x'].'</td><td>'.$row['loc_y'].'</td><td>'.$row['loc_z'].'</td><td>'.$row['loc_instance'].'</td>';
          echo '<td>'.$row['flags'].'</td></tr>'."\n";
        }
       echo '</table>';
      }
    }else{
      echo '<form action="./index.php?do=finditem" method="post">';
      echo 'Find all instances of item: ';
      $itemresult = PrepSelect('items');
      echo DrawSelectBox('items', $itemresult, 'itemid', ''). '<input type="submit" name="search" value="Find Items"/><br/>';
      echo 'Locate Instance ID: <input type="text" name="iid" /><input type="submit" name="search" value="Find Instance" /><br/>';
      $Sectors = PrepSelect('sectorid');
      echo 'Locate All Items on floor (Limit to Sector: '.DrawSelectBox('sectorid', $Sectors, 'sectorid', '', true).') <input type="submit" name="search" value="Droped Items" />';
      echo '</form>';
    }
  }else{
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}
?>
