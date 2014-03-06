<?php
function huntlocations(){
  if (checkaccess('natres', 'read')){
    if (isset($_POST['commit']) && (checkaccess('natres', 'edit'))){
      if ($_POST['commit'] == "Commit Edit"){
        $id = mysql_real_escape_string($_POST['id']);
        $sector = mysql_real_escape_string($_POST['sector']);
        $x = mysql_real_escape_string($_POST['x']);
        $y = mysql_real_escape_string($_POST['y']);
        $z = mysql_real_escape_string($_POST['z']);
        $range = mysql_real_escape_string($_POST['range']);
        $itemid = mysql_real_escape_string($_POST['itemid']);
        $interval = mysql_real_escape_string($_POST['interval']);
        $max_random = mysql_real_escape_string($_POST['max_random']);
        $amount = mysql_real_escape_string($_POST['amount']);
        $query = "UPDATE hunt_locations SET sector='$sector', x='$x', y='$y', z='$z', `range`='$range', itemid='$itemid', `interval`='$interval', max_random='$max_random', amount='$amount' WHERE id='$id'";
        $result = mysql_query2($query);
        echo '<p class="error">Update Successful</p>';
        unset($_POST);
        huntlocations();
        return; 
      }else if($_POST['commit'] == "Commit New" && checkaccess('natres', 'create')){
        $sector = mysql_real_escape_string($_POST['sector']);
        $x = mysql_real_escape_string($_POST['x']);
        $y = mysql_real_escape_string($_POST['y']);
        $z = mysql_real_escape_string($_POST['z']);
        $range = mysql_real_escape_string($_POST['range']);
        $itemid = mysql_real_escape_string($_POST['itemid']);
        $interval = mysql_real_escape_string($_POST['interval']);
        $max_random = mysql_real_escape_string($_POST['max_random']);
        $amount = mysql_real_escape_string($_POST['amount']);
        $query = "INSERT INTO hunt_locations (sector,x,y,z,`range`,itemid,`interval`,max_random,amount) VALUES ('$sector','$x','$y','$z','$range','$itemid','$interval', '$max_random','$amount')";
        $result = mysql_query2($query);
        echo '<p class="error">Insert Successful</p>';
        unset($_POST);
        huntlocations();
        return;
      }else if($_POST['commit'] == "Confirm Delete" && checkaccess('natres', 'delete')){
        $id = mysql_real_escape_string($_POST['id']);
        $query = "DELETE FROM hunt_locations WHERE id='$id'";
        $result = mysql_query2($query);
        unset($_POST);
        echo '<p class="error">Update Successful</p>';
        huntlocations();
        return;
      }
    }else if (isset($_POST['action']) && (checkaccess('natres', 'edit'))){
      if ($_POST['action'] == 'Edit'){
        $id = mysql_real_escape_string($_POST['id']);
        $query = "SELECT id, sector, x, y, z, itemid, `interval`, max_random, amount, `range` FROM hunt_locations WHERE id='$id'";
        $result = mysql_query2($query);
        $Sectors = PrepSelect('sectorid');
        $Items = PrepSelect('items_resource');
        $row = mysql_fetch_array($result, MYSQL_ASSOC);
        echo '<form action="./index.php?do=huntlocations" method="post">';
        echo '<table border="1">';
        echo '<tr><td>Sector:</td><td>'.DrawSelectBox('sectorid', $Sectors, 'sector', $row['sector']).'</td>';
        echo '<td>Coordinates (X/Y/Z):</td><td><input type="text" name="x" value="'.$row['x'].'" size="5"/>/<input type="text" name="y" value="'.$row['y'].'" size="5"/>/<input type="text" name="z" value="'.$row['z'].'" size="5"/></td></tr>';
        echo '<tr><td>Range:</td><td><input type="text" name="range" value="'.$row['range'].'" size="10" /></td>';
        echo '<tr><td>Reward Item:</td><td>'.DrawSelectBox('items', $Items, 'itemid', $row['itemid']).'</td>';
        echo '<td>Interval:</td><td><input type="text" name="interval" value="'.$row['interval'].'" size="10"/></td></tr>';
        echo '<tr><td>Max Random:</td><td><input type="text" name="max_random" value="'.$row['max_random'].'" /></td>';
        echo '<td>Amount:</td><td><input type="text" name="amount" value="'.$row['amount'].'" size="5"/></td></tr>';
        echo '</table><input type="hidden" name="id" value="'.$row['id'].'"><input type="submit" name="commit" value="Commit Edit" />';
        echo '</form>';
      }
	  else if ($_POST['action'] == 'Create New' && checkaccess('natres', 'create')){
        $Sectors = PrepSelect('sectorid');
        $Items = PrepSelect('items_resource');
        echo '<form action="./index.php?do=huntlocations" method="post">';
        echo '<table border="1">';
        echo '<tr><td>Sector:</td><td>'.DrawSelectBox('sectorid', $Sectors, 'sector', '').'</td>';
        echo '<td>Coordinates (X/Y/Z):</td><td><input type="text" name="x" size="5"/>/<input type="text" name="y" size="5"/>/<input type="text" name="z" size="5"/></td></tr>';
        echo '<tr><td>Range:</td><td><input type="text" name="range" size="10" /></td>';
        echo '<tr><td>Reward Item:</td><td>'.DrawSelectBox('items', $Items, 'itemid', '').'</td>';
        echo '<td>Interval:</td><td><input type="text" name="interval" size="10"/></td></tr>';
        echo '<tr><td>Max Random:</td><td><input type="text" name="max_random"  /></td>';
        echo '<td>Amount:</td><td><input type="text" name="amount" /></td></tr>';
        echo '</table><input type="submit" name="commit" value="Commit New" />';
        echo '</form>';
      }
	  else if ($_POST['action'] == 'Delete' && checkaccess('natres', 'delete')){
        $id = mysql_real_escape_string($_POST['id']);
        $query = "SELECT i.name as itemname, s.name FROM hunt_locations AS h, sectors AS s, item_stats AS i WHERE h.sector=s.id AND h.itemid=i.id AND h.id='$id'";
        $result = mysql_query2($query);
        $row = mysql_fetch_array($result, MYSQL_ASSOC);
        echo '<form action="./index.php?do=huntlocations" method="post">';
        echo '<p>Please Confirm that you wish to delete the '.$row['itemname'].' hunt location in sector '.$row['name'].'</p>';
        echo '<input type="hidden" name="id" value="'.$id.'"/><input type="submit" name="commit" value="Confirm Delete"/>';
        echo '</form>';
      }else{
        echo '<p class="error">Unknown Action - Returning to List</p>';
      }
    }else{
      $query = "SELECT r.id, r.sector, s.name AS sector, r.x, r.y, r.z, r.interval, r.max_random, r.range,r.amount, i.name AS item FROM hunt_locations AS r LEFT JOIN sectors AS s ON r.sector=s.id LEFT JOIN item_stats AS i on i.id=r.itemid";
      if (isset($_GET['id']))
      {
        $id = mysql_real_escape_string($_GET['id']);
        $query .= " WHERE r.id='$id'";
      }
      if (isset($_GET['sort'])){
        if ($_GET['sort'] == 'loc'){
          $query = $query . ' ORDER BY sector, x, y, z, item';
        }else if($_GET['sort'] == 'item'){
          $query = $query . ' ORDER BY item';
        }else if($_GET['sort'] == 'tool'){
          $query = $query . ' ORDER BY category, sector, item';
        }else if($_GET['sort'] == 'skill'){
          $query = $query . ' ORDER BY skill_name, sector, item';
        }else{
          $query = $query . ' ORDER BY sector, item';
        }
      }
      $result = mysql_query2($query);
      echo '<table border="1"><tr><th><a href="./index.php?do=resource&amp;sort=loc">Sector</a></th><th>Coordinates</th><th>Range </th><th>Interval</th><th>Max Random</th><th>Amount</th><th><a href="./index.php?do=huntlocations&amp;sort=item">Item</a></th>';
      if (checkaccess('natres', 'edit')){
        echo '<th>Actions</th>';
      }
      echo '</tr>';
      while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
        echo '<tr>';
        echo '<td>'.$row['sector'].'</td><td>'.$row['x'].'/'.$row['y'].'/'.$row['z'].'</td>';
        echo '<td>'.$row['range'].'</td>';
        echo '<td>'.$row['interval'].'</td>';
        echo '<td>'.$row['max_random'].'</td>';
        echo '<td>'.$row['amount'].'</td>';
        echo '<td>'.$row['item'].'</td>';
        if (checkaccess('natres', 'edit')){
          echo '<td><form action="./index.php?do=huntlocations" method="post">';
          echo '<input type="hidden" name="id" value="'.$row['id'].'" />';
          echo '<input type="submit" name="action" value="Edit" />';
          if (checkaccess('natres', 'delete')){
            echo '<br/><input type="submit" name="action" value="Delete" />';
          }
          echo '</form></td>';
        }
        echo '</tr>';
      }
      echo '</table>';
      if (checkaccess('natres', 'create')){
        echo '<form action="./index.php?do=huntlocations" method="post">';
        echo '<input type="submit" name="action" value="Create New" /></form>';
      }
    }
  }else{
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}
?>
