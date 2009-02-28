<?
function editprocess(){
  if (checkaccess('crafting', 'read')){
    $id = mysql_real_escape_string($_GET['id']);
    echo '<p class="header">Process Information';
    $result = mysql_query2("SELECT id, name FROM item_stats");
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
      $i = $row['id'];
      $Items["$i"] = $row['name'];
    }
    $result = mysql_query2("SELECT skill_id, name FROM skills");
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
      $i = $row['skill_id'];
      $Skills["$i"] = $row['name'];
    }
    $query = "SELECT * FROM trade_processes WHERE process_id = '$id'";
    $result = mysql_query2($query);
    $row = mysql_fetch_array($result, MYSQL_ASSOC);
    echo '- '.$row['name'].'</p>';
    mysql_data_seek($result, 0);
    echo '<table><tr><th>Sub-Process</th><th>Animation</th><th>Item Used</th><th>Equipment Used</th><th>Constraints</th><th>Garbage Item</th><th>Primary Skill / Min / Max / Practice / Quality</th><th>Secondary Skill / Min / Max / Practice / Quality</th><th>Description</th>';
    if (checkaccess('crafting', 'edit')){
      echo '<th>Actions</th>';
    }
    echo '</tr>';
    $Alt= FALSE;
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
      $Alt = !$Alt;
      if ($Alt){
        echo '<tr class="color_a">';
      }else{
        echo '<tr class="color_b">';
      }
      echo '<td>'.$row['subprocess_number'].'</td>';
      echo '<td>'.$row['animation'].'</td>';
      $i = $row['workitem_id'];
      echo '<td>'.$Items["$i"].'</td>';
      $i = $row['equipment_id'];
      echo '<td>'.$Items["$i"].'</td>';
      echo '<td>'.$row['constraints'].'</td>';
      $i = $row['garbage_id'];
      echo '<td>'.$row['garbage_qty'].' '.$Items["$i"].'</td>';
      $i = $row['primary_skill_id'];
      echo '<td>'.$Skills["$i"].' / '.$row['primary_min_skill'].' / '.$row['primary_max_skill'].' / '.$row['primary_practice_points'].' / '.$row['primary_quality_factor'].'</td>';
      $i = $row['secondary_skill_id'];
      echo '<td>'.$Skills["$i"].' / '.$row['secondary_min_skill'].' / '.$row['secondary_max_skill'].' / '.$row['secondary_practice_points'].' / '.$row['secondary_quality_factor'].'</td>';
      echo '<td>'.$row['description'].'</td>';
      if (checkaccess('crafting','edit')){
        echo '<td><a href="./index.php?do=editsubprocess&amp;id='.$id.'&amp;sub='.$row['subprocess_number'].'">Edit</a></td>';
      }
      echo '</tr>';
    }
    echo '</table>';
  }else{
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}

function editsubprocess(){
  if (checkaccess('crafting','edit')){
    if (isset($_POST['commit']) && ($_POST['commit'] == "Update Process")){
   //do Magic
    }else{
      $id = mysql_real_escape_string($_GET['id']);
      $sub = mysql_real_escape_string($_GET['sub']);
      $query = "SELECT * FROM trade_processes WHERE process_id = '$id' AND subprocess_number='$sub'";
      $result = mysql_query2($query);
      $row = mysql_fetch_array($result, MYSQL_ASSOC);
      echo '<p class="header">Edit Sub-Proccess</p>';
      echo '<form action="./index.php?do=editsubproccess&amp;id='.$id.'&amp;sub='.$sub.'" method="post">';
      echo '<table><tr><th>Field</th><th>Value</th></tr>';
      echo '<tr><td>Process ID:</td><td>'.$row['process_id'].'</tr>';
      echo '<tr><td>SubProcess ID:</td><td>'.$row['subprocess_number'].'</td></tr>';
      echo '<tr><td>Description:</td><td><input type="text" name="description" value="'.$row['description'].'" /></td></tr>';
      echo '<tr><td>Animation:</td><td><input type="text" name="animation" value="'.$row['animation'].'" /></td></tr>';
      $Items = PrepSelect('items');
      echo '<tr><td>Work Item:</td><td>'.DrawSelectBox('items', $Items, 'workitem_id', $row['workitem_id'], 'true').'</td></tr>';
      echo '<tr><td>Equipment:</td><td>'.DrawSelectBox('items', $Items, 'equipment_id', $row['equipment_id'], 'true').'</td></tr>';
      echo '<tr><td>Garbage Item:</td><td>'.DrawSelectBox('items', $Items, 'garbage_id', $row['garbage_id']).'</td></tr>';
      echo '<tr><td>Garbage Quantity:</td><td><input type="text" name="garbage_qty" value="'.$row['garbage_qty'].'" /></td></tr>';
      $Skills = PrepSelect('skill');
      echo '<tr><td>Primary Skill:</td><td>'.DrawSelectBox('skill', $Skills, 'primary_skill_id', $row['primary_skill_id']).'</td></tr>';
      echo '<tr><td>Primary Minimum Skill Level:</td><td><input type="text" name="primary_min_skill" value="'.$row['primary_min_skill'].'"/></td></tr>';
      echo '<tr><td>Primary Maximum Skill Level:</td><td><input type="text" name="primary_max_skill" value="'.$row['primary_max_skill'].'"/></td></tr>';
      echo '<tr><td>Primary Practice Points:</td><td><input type="text" name="primary_practice_points" value="'.$row['primary_practice_points'].'"/></td></tr>';
      echo '<tr><td>Primary Quality Factor:</td><td><input type="text" name="primary_quality_factor" value="'.$row['primary_quality_factor'].'"/></td></tr>';
      echo '<tr><td>Secondary Skill:</td><td>'.DrawSelectBox('skill', $Skills, 'secondary_skill_id', $row['secondary_skill_id'], true).'</td></tr>';
      echo '<tr><td>Secondary Minimum Skill Level:</td><td><input type="text" name="secondary_min_skill" value="'.$row['secondary_min_skill'].'"/></td></tr>';
      echo '<tr><td>Secondary Maximum Skill Level:</td><td><input type="text" name="secondary_max_skill" value="'.$row['secondary_max_skill'].'"/></td></tr>';
      echo '<tr><td>Secondary Practice Points:</td><td><input type="text" name="secondary_practice_points" value="'.$row['secondary_practice_points'].'"/></td></tr>';
      echo '<tr><td>Secondary Quality Factor:</td><td><input type="text" name="secondary_quality_factor" value="'.$row['secondary_quality_factor'].'"/></td></tr>';
       echo '</table></form>';
    }
  }else{
    echo '<p class="error">You are not authroized to use these functions</p>';
  }
}


?>
