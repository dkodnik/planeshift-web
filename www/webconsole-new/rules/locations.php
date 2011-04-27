<?php
function listlocations(){
  if (checkaccess('rules', 'read')){
    if (isset($_POST['commit']) && checkaccess('rules', 'edit')){
      if ($_POST['commit'] == "Update Location"){
        $id = mysql_real_escape_string($_POST['id']);
        $name = mysql_real_escape_string($_POST['name']);
        $loc_sector_id = mysql_real_escape_string($_POST['loc_sector_id']);
        $x = mysql_real_escape_string($_POST['x']);
        $y = mysql_real_escape_string($_POST['y']);
        $z = mysql_real_escape_string($_POST['z']);
        $radius = mysql_real_escape_string($_POST['radius']);
        $angle = mysql_real_escape_string($_POST['angle']);
        $id_prev_loc_in_region = mysql_real_escape_string($_POST['previous']);
        $type_id = mysql_real_escape_string($_POST['type']);
        $query = "UPDATE sc_locations SET name='$name', loc_sector_id='$loc_sector_id', x='$x', y='$y', z='$z', radius='$radius', angle='$angle', id_prev_loc_in_region='$id_prev_loc_in_region', type_id='$type_id' WHERE id = '$id'";
        $result = mysql_query2($query);
        echo '<p class="error">Update Successful</p>';
        unset($_POST);
        listlocations();
        return;
      }else if ($_POST['commit'] == "Confirm Delete" && checkaccess('rules', 'delete')){
        $id = mysql_real_escape_string($_POST['id']);
        $query = "DELETE FROM sc_locations WHERE id = '$id'";
        $result = mysql_query2($query);
        echo '<p class="error">Update Successful</p>';
        unset($_POST);
        listlocations();
        return;
      }else if ($_POST['commit'] == "Create Location" && checkaccess('rules', 'create')){
        $name = mysql_real_escape_string($_POST['name']);
        $loc_sector_id = mysql_real_escape_string($_POST['loc_sector_id']);
        $x = mysql_real_escape_string($_POST['x']);
        $y = mysql_real_escape_string($_POST['y']);
        $z = mysql_real_escape_string($_POST['z']);
        $radius = mysql_real_escape_string($_POST['radius']);
        $angle = mysql_real_escape_string($_POST['angle']);
        $id_prev_loc_in_region = mysql_real_escape_string($_POST['previous']);
        $type_id = mysql_real_escape_string($_POST['type']);
        $query = "INSERT INTO sc_locations SET name='$name', loc_sector_id='$loc_sector_id', x='$x', y='$y', z='$z', radius='$radius', angle='$angle', id_prev_loc_in_region='$id_prev_loc_in_region', type_id='$type_id'";
        $result = mysql_query2($query);
        echo '<p class="error">Update Successful</p>';
        unset($_POST);
        listlocations();
        return;
      }else{
        echo '<p class="error">Bad Commit, return to listing</p>';
        unset($_POST);
        listlocations();
        return;
      }
    }else if (isset($_POST['action']) && checkaccess('rules', 'edit')){
      if ($_POST['action'] == "Edit"){
        $id = mysql_real_escape_string($_POST['id']);
        $query = "SELECT * FROM sc_locations WHERE id = '$id'";
        $result = mysql_query2($query);
        $row = mysql_fetch_array($result, MYSQL_ASSOC);
        echo '<form action="./index.php?do=location" method="post"><input type="hidden" name="id" value="'.$id.'" /><table border="1">';
        echo '<tr><th>Field</th><th>Value</th></tr>';
        echo '<tr><td>Name:</td><td><input type="text" name="name" value="'.$row['name'].'"/></td></tr>';
        $Sectors = PrepSelect('sectorid');
        echo '<tr><td>Sector:</td><td>'.DrawSelectBox('sectorid', $Sectors, 'loc_sector_id', $row['loc_sector_id']).'</td></tr>';
        echo '<tr><td>X:</td><td><input type="text" name="x" value="'.$row['x'].'"/></td></tr>';
        echo '<tr><td>Y:</td><td><input type="text" name="y" value="'.$row['y'].'" /></td></tr>';
        echo '<tr><td>Z:</td><td><input type="text" name="z" value="'.$row['z'].'" /></td></tr>';
        echo '<tr><td>Radius:</td><td><input type="text" name="radius" value="'.$row['radius'].'"/></td></tr>';
        echo '<tr><td>Angle:</td><td><input type="text" name="angle" value="'.$row['angle'].'" /></td></tr>';
        echo '<tr><td>Flags:</td><td>Not Supported</td></tr>';
        $Locations = PrepSelect('locations');
        echo '<tr><td>Previous Location</td><td>'.DrawSelectBox('locations', $Locations, 'previous', $row['id_prev_loc_in_region'], true).'</td></tr>';
        $location_type = PrepSelect('location_type');
        echo '<tr><td>Type</td><td>'.DrawSelectBox('location_type', $location_type, 'type', '', false).'</td></tr>';
        echo '</table><input type="submit" name="commit" value="Update Location"/>';
        echo '</form>';
      }else if ($_POST['action'] == "Delete" && checkaccess('rules', 'delete')){
        $id = mysql_real_escape_string($_POST['id']);
        $query = "SELECT name FROM sc_locations WHERE id = '$id'";
        $result = mysql_query2($query);
        $row = mysql_fetch_array($result, MYSQL_ASSOC);
        echo '<p>You are about to delete the location '.$row['name'].' id '.$id.' Please confirm:</p>';
        echo '<form action="./index.php?do=location" method="post"><input type="hidden" name="id" value="'.$id.'" />';
        echo '<input type="submit" name="commit" value="Confirm Delete" /></form>';
      }else{
        echo '<p class="error">Bad Action - Returning to list</p>';
        unset($_POST);
        listlocations();
        return;
      }
    }else{
      $query = "SELECT l.id, l.type_id, lt.name AS type_name, l.id_prev_loc_in_region, l.name, l.x, l.y, l.z, l.radius, l.angle, l.flags, l.loc_sector_id, s.name AS sector FROM sc_locations AS l LEFT JOIN sectors AS s ON l.loc_sector_id = s.id LEFT JOIN sc_location_type AS lt ON l.type_id = lt.id";
      if (isset($_GET['id']))
      {
        $id = mysql_real_escape_string($_GET['id']);
        $query .= " WHERE l.id='$id'";
      }
      elseif (isset($_GET['type']))
      {
        $type = mysql_real_escape_string($_GET['type']);
        $query .= " WHERE l.type_id='$type'";
      }
      if (isset($_GET['sort'])){
        switch ($_GET['sort']){
          case 'name':
            $query = $query . " ORDER BY name";
            break;
          case 'sector':
            $query = $query . " ORDER BY sector";
            break;
          case 'type':
            $query = $query . " ORDER BY type_id";
            break;
          default:
            echo '<p class="error">Bad sort method - No sort used</p>';
        }
      }
      if (isset($_GET['limit'])){
        $lim = mysql_real_escape_string($_GET['limit']);
        $ll = $lim - 30;
        $query = $query . " LIMIT $ll, 30"; // limit 1, 10 uses offset 1, then 10 records.
      }else{
        $query = $query . " LIMIT 0, 30";
        $ll = 0;
        $lim=30;
      }
      $result = mysql_query2($query);
      if (mysql_numrows($result) == 0){
        echo '<p class="error">No Locations in DataBase</p>';
      }else{
        if ($lim> 30){
          echo '<a href="./index.php?do=location';
          if (isset($_GET['sort'])){
            echo '&amp;sort='.$_GET['sort'];
          }
          echo '&amp;limit='.$ll.'">Previous Page</a>';
        }
        echo ' - Displaying records '.$ll.' through '.$lim.' - ';
        $result2 = mysql_query2('select count(id) AS mylimit FROM sc_locations');
        $row2 = mysql_fetch_array($result2);
        if ($row2['mylimit'] > $lim)
        {
          echo '<a href="./index.php?do=location';
          if (isset($_GET['sort'])){
            echo '&amp;sort='.$_GET['sort'];
          }
          $lu = $lim + 30;
          echo '&amp;limit='.$lu.'">Next Page</a>';;
        }
        echo '<table border="1">';
        echo '<tr><th><a href="./index.php?do=location&amp;sort=name';
        if (isset($_GET['limit'])){
          echo '&amp;limit='.$_GET['limit'];
        }
        echo '">name</a></th>';
        echo '<th><a href="./index.php?do=location&amp;sort=sector';
        if (isset($_GET['limit'])){
          echo '&amp;limit='.$_GET['limit'];
        }
        echo '">Sector</a></th>';
        echo '<th>X</th>';
        echo '<th>Y</th>';
        echo '<th>Z</th>';
        echo '<th>Radius</th>';
        echo '<th>Angle</th>';
        echo '<th>Flags<br/>Not Supported</th>';
        echo '<th>Previous Location</th>';
        echo '<th><a href="./index.php?do=location&amp;sort=type';
        if (isset($_GET['limit'])){
          echo '&amp;limit='.$_GET['limit'];
        }
        echo '">Type</a></th>';
        if (checkaccess('rules', 'edit')){
          echo '<th>Actions</th>';
        }
        echo '</tr>';
        while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
          echo '<tr>';
          echo '<td>'.$row['name'].'</td>';
          echo '<td>'.$row['sector'].'</td>';
          echo '<td>'.$row['x'].'</td>';
          echo '<td>'.$row['y'].'</td>';
          echo '<td>'.$row['z'].'</td>';
          echo '<td>'.$row['radius'].'</td>';
          echo '<td>'.$row['angle'].'</td>';
          echo '<td>'.$row['flags'].'</td>';
          echo '<td>'.$row['id_prev_loc_in_region'].'</td>';
          echo '<td>'.$row['type_name'].'</td>';
          if (checkaccess('rules', 'edit')){
            echo '<td><form action="./index.php?do=location" method="post"><input type="hidden" name="id" value="'.$row['id'].'"/>';
            echo '<input type="submit" name="action" value="Edit"/>';
            echo '<br/><input type="submit" name="action" value="Delete"/>';
            echo '</form></td>';
          }
          echo '</tr>';
        }
        echo '</table>';
        if (checkaccess('rules', 'create')){
          echo '<hr/>Create New Location:';
          echo '<form action="./index.php?do=location" method="post"><table border="1">';
          echo '<tr><th>Field</th><th>Value</th></tr>';
          echo '<tr><td>Name:</td><td><input type="text" name="name" /></td></tr>';
          $Sectors = PrepSelect('sectorid');
          echo '<tr><td>Sector:</td><td>'.DrawSelectBox('sectorid', $Sectors, 'loc_sector_id', '').'</td></tr>';
          echo '<tr><td>X:</td><td><input type="text" name="x" /></td></tr>';
          echo '<tr><td>Y:</td><td><input type="text" name="y" /></td></tr>';
          echo '<tr><td>Z:</td><td><input type="text" name="z" /></td></tr>';
          echo '<tr><td>Radius:</td><td><input type="text" name="radius" /></td></tr>';
          echo '<tr><td>Angle:</td><td><input type="text" name="angle" /></td></tr>';
          echo '<tr><td>Flags:</td><td>Not Supported</td></tr>';
          $Locations = PrepSelect('locations');
          echo '<tr><td>Previous Location</td><td>'.DrawSelectBox('locations', $Locations, 'previous', '', true).'</td></tr>';
          $location_type = PrepSelect('location_type');
          echo '<tr><td>Type</td><td>'.DrawSelectBox('location_type', $location_type, 'type', '', false).'</td></tr>';
          echo '</table><input type="submit" name="commit" value="Create Location"/>';
          echo '</form>';
        }
      }
    }
  }else{
    echo '<p class="error">You are not authorised to use these functions.</p>';
  }
}
?>
