<?php
function listspawnrules(){
  if (checkaccess('npcs', 'read')){
    $query = 'SELECT * FROM npc_spawn_rules';
    if (isset($_GET['id'])){
      $id = mysql_real_escape_string($_GET['id']);
      $query = $query . ' WHERE id='.$id;
    }
    $result = mysql_query2($query);
    echo '<table border="1">';
    $query = "SELECT id, CONCAT_WS(' ', name, lastname) AS name, npc_spawn_rule FROM characters WHERE npc_spawn_rule !=0";
    $c_result = mysql_query2($query);
    while ($c_row = mysql_fetch_array($c_result, MYSQL_ASSOC)){
      $rule = $c_row['npc_spawn_rule'];
      $id = $c_row['id'];
      $chars[$rule][$id] = $c_row['name'];
    }
    $query = "SELECT r.id, r.npc_spawn_rule_id, r.x1, r.y1, r.z1, r.x2, r.y2, r.z2, r.sector_id, r.range_type_code, r.radius, s.name FROM npc_spawn_ranges AS r LEFT JOIN sectors AS s ON r.sector_id=s.id";
    $r_result = mysql_query2($query);
    while ($r_row = mysql_fetch_array($r_result, MYSQL_ASSOC)){
      $rule = $r_row['npc_spawn_rule_id'];
      $id = $r_row['id'];
      $Rules[$rule][$id]['id'] = $id;
      $Rules[$rule][$id]['x1'] = $r_row['x1'];
      $Rules[$rule][$id]['y1'] = $r_row['y1'];
      $Rules[$rule][$id]['z1'] = $r_row['z1'];
      $Rules[$rule][$id]['x2'] = $r_row['x2'];
      $Rules[$rule][$id]['y2'] = $r_row['y2'];
      $Rules[$rule][$id]['z2'] = $r_row['z2'];
      $Rules[$rule][$id]['sector'] = $r_row['sector_id'];
      $Rules[$rule][$id]['sname'] = $r_row['name'];
      $Rules[$rule][$id]['code'] = $r_row['range_type_code'];
      $Rules[$rule][$id]['radius'] = $r_row['radius'];
    }
    $Sectors = PrepSelect('sectorid');
    echo '<tr><th>Rule</th><th>Details</th><th>NPCs</th></tr>';
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
      echo '<tr>';
      echo '<td>'.$row['id'].'<br/>';
      if (checkaccess('npcs', 'edit')){
        echo '<form action="./index.php?do=editspawnrule" method="post"><input type="hidden" name="id" value="'.$row['id'].'"/><input type="text" name="name" value="'.$row['name'].'" size="35"/><br/><input type="submit" name="commit" value="Change Name" /></form></td>';
      }else{
        echo $row['name'].'</td>';
      }
      echo '<td>';
      if (checkaccess('npcs', 'edit')){
        echo '<form action="./index.php?do=editspawnrule" method="post"><input type="hidden" name="id" value="'.$row['id'].'"/><table>';
        echo '<tr><td>Min Spawn Time:</td><td><input type="text" name="min_spawn_time" value="'.$row['min_spawn_time'].'"/></td><td>Max Spawn Time:</td><td><input type="text" name="max_spawn_time" value="'.$row['max_spawn_time'].'"/></td></tr>';
        echo '<tr><td>Substitute Spawn Odds:</td><td><input type="text" name="substitute_spawn_odds" value="'.$row['substitute_spawn_odds'].'"/></td><td>Substitute NPC:</td><td><input type="text" name="substitute_player" value="'.$row['substitute_player'].'"/></td></tr>';
        echo '<tr><td>Fixed X:</td><td><input type="text" name="fixed_spawn_x" value="'.$row['fixed_spawn_x'].'"/></td><td>Fixed Y:</td><td><input type="text" name="fixed_spawn_y" value="'.$row['fixed_spawn_y'].'"/></td></tr>';
        echo '<tr><td>Fixed Z:</td><td><input type="text" name="fixed_spawn_z" value="'.$row['fixed_spawn_z'].'"/></td><td>Fixed Rot:</td><td><input type="text" name="fixed_spawn_rot" value="'.$row['fixed_spawn_rot'].'"/></td></tr>';
        echo '<tr><td>Fixed sector:</td><td><input type="text" name="fixed_spawn_sector" value="'.$row['fixed_spawn_sector'].'"/></td><td>Fixed instance:</td><td><input type="text" name="fixed_spawn_instance" value="'.$row['fixed_spawn_instance'].'"/></td></tr>';
        if (!isset($loot_result)){
          $loot_result = PrepSelect('loot');
        }
        echo '<tr><td>Loot Category ID:</td><td>'.DrawSelectbox('loot', $loot_result, 'loot_category_id', $row['loot_category_id'], TRUE).'</td><td>Dead Time</td><td><input type="text" name="dead_remain_time" value="'.$row['dead_remain_time'].'"/></td></tr>';
        echo '<tr><td>Minimal Spawn Spacing Disatance: </td><td><input type="text" name="min_spawn_spacing_dist" value="'.$row['min_spawn_spacing_dist'].'" /></td><td></td><td></td></tr>';
        echo '</table><input type="submit" name="commit" value="Update Spawn Rule"/></form>';
      }else{
        echo '<table>';
        echo '<tr><td>Min Spawn Time:</td><td>'.$row['min_spawn_time'].'</td><td>Max Spawn Time:</td><td>'.$row['max_spawn_time'].'</td></tr>';
        echo '<tr><td>Substitute Spawn Odds:</td><td>'.$row['substitute_spawn_odds'].'</td><td>Substitute NPC:</td><td>'.$row['substitute_player'].'</td></tr>';
        echo '<tr><td>Fixed X:</td><td>'.$row['fixed_spawn_x'].'</td><td>Fixed Y:</td><td>'.$row['fixed_spawn_y'].'</td></tr>';
        echo '<tr><td>Fixed Z:</td><td>'.$row['fixed_spawn_z'].'</td><td>Fixed Rot:</td><td>'.$row['fixed_spawn_rot'].'</td></tr>';
        echo '<tr><td>Fixed sector:</td><td>'.$row['fixed_spawn_sector'].'</td><td>Fixed instance:</td><td>'.$row['fixed_spawn_instance'].'</td></tr>';
        echo '<tr><td>Loot Category ID:</td><td>'.$row['loot_category_id'].'</td><td>Dead Time</td><td>'.$row['dead_remain_time'].'</td></tr>';
        echo '<tr><td>Minimal Spawn Spacing Disatance: </td><td>'.$row['min_spawn_spacing_dist'].'</td><td></td><td></td></tr>';
        echo '</table>';
      }
      echo '<hr/>';
      $id = $row['id'];
      if (checkaccess('npcs', 'edit')){
        if (isset($Rules["$id"])){
          echo '<table border="1"><tr><th>X1</th><th>Y1</th><th>Z1</th><th>X2</th><th>Y2</th><th>Z2</th><th>Sector</th><th>Type</th><th>Radius</th><th>Actions</th></tr>';
          foreach ($Rules["$id"] AS $r_id){
            echo '<tr>';
            echo '<td>'.$r_id['x1'].'</td>';
            echo '<td>'.$r_id['y1'].'</td>';
            echo '<td>'.$r_id['z1'].'</td>';
            echo '<td>'.$r_id['x2'].'</td>';
            echo '<td>'.$r_id['y2'].'</td>';
            echo '<td>'.$r_id['z2'].'</td>';
            echo '<td>'.$r_id['sname'].'</td>';
            echo '<td>'.$r_id['code'].'</td>';
            echo '<td>'.$r_id['radius'].'</td>';
            echo '<td><form action="./index.php?do=editspawnrule" method="post"><input type="hidden" name="id" value="'.$id.'" />';
            echo '<input type="hidden" name="range_id" value="'.$r_id['id'].'"/><input type="submit" name="commit" value="Remove Range" />';
            echo '</form></td>';
          }
          echo '</table>';
        }
        echo '<form action="./index.php?do=editspawnrule" method="post"><input type="hidden" name="id" value="'.$id.'" /><table border="1">';
        echo '<tr><th>X1</th><th>Y1</th><th>Z1</th><th>X2</th><th>Y2</th><th>Z2</th><th>Sector</th><th>Type</th><th>Radius</th></tr>';
        echo '<tr>';
        echo '<td><input type="text" name="x1" size="4" /></td>';
        echo '<td><input type="text" name="y1" size="4" /></td>';
        echo '<td><input type="text" name="z1" size="4" /></td>';
        echo '<td><input type="text" name="x2" size="4" /></td>';
        echo '<td><input type="text" name="y2" size="4" /></td>';
        echo '<td><input type="text" name="z2" size="4" /></td>';
        echo '<td>'.DrawSelectBox('sectorid', $Sectors, 'sector_id', '').'</td>';
        echo '<td><select name="code"><option value="A">Area</option><option value="L">Line</option></select></td>';
        echo '<td><input type="text" name="radius" size="4" /></td>';
        echo '</tr>';
        echo '</table><input type="submit" name="commit" value="New Range" /></form>';
      }else{
        echo '<table border="1"><tr><th>X1</th><th>Y1</th><th>Z1</th><th>X2</th><th>Y2</th><th>Z2</th><th>Sector</th><th>Type</th><th>Radius</th></tr>';
        if (isset($Rules["$id"])){
          foreach ($Rules["$id"] AS $r_id){
            echo '<tr>';
            echo '<td>'.$r_id['x1'].'</td>';
            echo '<td>'.$r_id['y1'].'</td>';
            echo '<td>'.$r_id['z1'].'</td>';
            echo '<td>'.$r_id['x2'].'</td>';
            echo '<td>'.$r_id['y2'].'</td>';
            echo '<td>'.$r_id['z2'].'</td>';
            echo '<td>'.$r_id['sname'].'</td>';
            echo '<td>'.$r_id['code'].'</td>';
            echo '<td>'.$r_id['radius'].'</td>';
          }
        }
        echo '</table>';
      }
      echo '</td><td>';
      if (checkaccess('npcs', 'edit')){
        echo '<form action="./index.php?do=editspawnrule" method="post"><input type="hidden" name="id" value="'.$row['id'].'"/>';
        echo '<select name="npc_id">';
        if (isset($chars[$id])){
          foreach ($chars[$id] AS $c_id => $c_name){
            echo '<option value="'.$c_id.'">';
            echo $c_id. ' - '. $c_name.'</option>';
          }
        }
        echo '</select><input type="submit" name="commit" value="Remove NPC" /></form>';
      }else{ 
        if (isset($chars[$id])){
          foreach ($chars[$id] AS $c_id => $c_name){
            echo $c_id. ' - '. $c_name.'<br/>';
          }
        }
      }
      echo '</td></tr>'."\n";
    }
    if (checkaccess('npcs', 'create')){
      echo '<tr><td><form action="./index.php?do=editspawnrule" method="post"><input type="hidden" name="id" value="0" />';
      echo '<input type="text" name="name" size="30" /><input type="submit" name="commit" value="Create New Rule"/></form></td>';
      echo '<td></td><td></td></tr>';
    }
    echo '</table>';
  }else{
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}

function editspawnrule(){
  if (checkaccess('npcs', 'edit')){
    if (isset($_POST['id'])){
      $id = mysql_real_escape_string($_POST['id']);
      if ($_POST['commit'] == "Update Spawn Rule"){
        $min_spawn_time = mysql_real_escape_string($_POST['min_spawn_time']);
        $max_spawn_time = mysql_real_escape_string($_POST['max_spawn_time']);
        $substitute_spawn_odds = mysql_real_escape_string($_POST['substitute_spawn_odds']);
        $substitute_player = mysql_real_escape_string($_POST['substitute_player']);
        $fixed_spawn_x = mysql_real_escape_string($_POST['fixed_spawn_x']);
        $fixed_spawn_y = mysql_real_escape_string($_POST['fixed_spawn_y']);
        $fixed_spawn_z = mysql_real_escape_string($_POST['fixed_spawn_z']);
        $fixed_spawn_rot = mysql_real_escape_string($_POST['fixed_spawn_rot']);
        $fixed_spawn_sector = mysql_real_escape_string($_POST['fixed_spawn_sector']);
        $fixed_spawn_instance = mysql_real_escape_string($_POST['fixed_spawn_instance']);
        $loot_category_id = mysql_real_escape_string($_POST['loot_category_id']);
        $dead_remain_time = mysql_real_escape_string($_POST['dead_remain_time']);
        $min_spawn_spacing_dist = mysql_real_escape_string($_POST['min_spawn_spacing_dist']);
        $query = "UPDATE npc_spawn_rules SET min_spawn_time='$min_spawn_time', max_spawn_time='$max_spawn_time', substitute_spawn_odds='$substitute_spawn_odds', substitute_player='$substitute_player', fixed_spawn_x='$fixed_spawn_x', fixed_spawn_y='$fixed_spawn_y', fixed_spawn_z='$fixed_spawn_z', fixed_spawn_rot='$fixed_spawn_rot', fixed_spawn_sector='$fixed_spawn_sector', fixed_spawn_instance='$fixed_spawn_instance', loot_category_id='$loot_category_id', dead_remain_time='$dead_remain_time', min_spawn_spacing_dist='$min_spawn_spacing_dist' WHERE id='$id'";
        $result = mysql_query2($query);
        echo '<p class="error">Update Successful</p>';
        listspawnrules();
      }else if ($_POST['commit'] == "Change Name"){
        $name = mysql_real_escape_string($_POST['name']);
        $query = "UPDATE npc_spawn_rules SET name='$name' WHERE id='$id'";
        $result = mysql_query2($query);
        echo '<p class="error">Update Successful</p>';
        listspawnrules();
      }else if ($_POST['commit'] == "Remove NPC"){
        $npc_id = mysql_real_escape_string($_POST['npc_id']);
        $query = "UPDATE characters SET npc_spawn_rule='0' WHERE id='$npc_id'";
        $result = mysql_query2($query);
        echo '<p class="error">Update Successful</p>';
        listspawnrules();
      }else if ($_POST['commit'] == "Create New Rule"){
        $name = mysql_real_escape_string($_POST['name']);
        $query = "INSERT INTO npc_spawn_rules (name) VALUES ('$name')";
        $result = mysql_query2($query);
        echo '<p class="error">Update Successful</p>';
        listspawnrules();
      }else if ($_POST['commit'] == "Remove Range"){
        $id = mysql_real_escape_string($_POST['range_id']);
        $query = "DELETE FROM npc_spawn_ranges WHERE id='$id'";
        $result = mysql_query2($query);
        echo '<p class="error">Update Successful</p>';
        listspawnrules();
      }else if ($_POST['commit'] == "New Range"){
        $x1 = mysql_real_escape_string($_POST['x1']);
        $y1 = mysql_real_escape_string($_POST['y1']);
        $z1 = mysql_real_escape_string($_POST['z1']);
        $x2 = mysql_real_escape_string($_POST['x2']);
        $y2 = mysql_real_escape_string($_POST['y2']);
        $z2 = mysql_real_escape_string($_POST['z2']);
        $sector_id = mysql_real_escape_string($_POST['sector_id']);
        $range_type_code = mysql_real_escape_string($_POST['code']);
        $radius = mysql_real_escape_string($_POST['radius']);
        $query = "INSERT INTO npc_spawn_ranges (npc_spawn_rule_id, x1, y1, z1, x2, y2, z2, sector_id, range_type_code, radius) VALUES ('$id', '$x1', '$y1', '$z1', '$x2', '$y2', '$z2', '$sector_id', '$range_type_code', '$radius')";
        $result = mysql_query2($query);
        echo '<p class="error">Update Successful</p>';
        listspawnrules();
      }else{
        echo '<p class="error">Error: No commit value found; Edit aborted</p>';
        listspawnrules();
      }
    }else{
      echo '<p class="error">Error: No id specified, edit aborted</p>';
      listspawnrules();
    }
  }else{
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}
?>
