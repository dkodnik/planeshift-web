<?php
function listlootrules(){
  if (checkaccess('npcs', 'read')){
    $query = 'SELECT id, name FROM loot_rules';
    if (isset($_GET['id'])){
      $id = mysql_real_escape_string($_GET['id']);
      $query = $query . ' WHERE id='.$id;
    }
    $result = mysql_query2($query);
    $query = "SELECT id, CONCAT_WS(' ', name, lastname) AS name, npc_addl_loot_category_id FROM characters WHERE npc_addl_loot_category_id !=0";
    $c_result = mysql_query2($query);
    while ($c_row = mysql_fetch_array($c_result, MYSQL_ASSOC)){
      $rule = $c_row['npc_addl_loot_category_id'];
      $id = $c_row['id'];
      $chars[$rule][$id] = $c_row['name'];
    }

    $query = 'SELECT l.id, l.loot_rule_id, l.item_stat_id, i.name, l.probability, l.min_money, l.max_money, l.randomize FROM loot_rule_details AS l LEFT JOIN item_stats AS i ON i.id=l.item_stat_id';
    $rules_result = mysql_query2($query);
    while ($r_row = mysql_fetch_array($rules_result, MYSQL_ASSOC)){
      $R_id = $r_row['loot_rule_id'];
      $l_id = $r_row['id'];
      $Rules["$R_id"]["$l_id"]['id'] = $l_id;
      $Rules["$R_id"]["$l_id"]['item_stat_id'] = $r_row['item_stat_id'];
      $Rules["$R_id"]["$l_id"]['name'] = $r_row['name'];
      $Rules["$R_id"]["$l_id"]['probability'] = $r_row['probability'];
      $Rules["$R_id"]["$l_id"]['min_money'] = $r_row['min_money'];
      $Rules["$R_id"]["$l_id"]['max_money'] = $r_row['max_money'];
      $Rules["$R_id"]["$l_id"]['randomize'] = $r_row['randomize'];
    }

    echo '<table border="1">';
    echo '<tr><th>Rule</th><th>Details</th><th>NPCs</th></tr>';
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
      echo '<tr><td>'.$row['id'].'<br/>';
      if (checkaccess('npcs', 'edit')){
        echo '<form action="./index.php?do=editlootrule" method="post"><input type="hidden" name="id" value="'.$row['id'].'"/><input type="text" name="name" value="'.$row['name'].'" size="30"/><br/><input type="submit" name="commit" value="Change Name" /></form></td>';
      }else{
        echo $row['name'].'</td>';
      }
      echo '<td>';
      if (checkaccess('npcs', 'edit')){
        if (!isset($item_result)){
          $item_result = PrepSelect('items');
        }
        $R_id = $row['id'];
        if (isset($Rules["$R_id"])){
          foreach ($Rules["$R_id"] as $R){
            echo '<form action="./index.php?do=editlootrule" method="post"><input type="hidden" name="id" value="'.$R['id'].'"/><table border="1"><tr><th>Item</th><th>Probability</th><th>Minimum Money</th><th>Maxiumum Money</th><th>Randomize</th></tr>';
            echo '<tr><td>'.DrawSelectBox('items', $item_result, 'item_stat_id', $R['item_stat_id']).'</td>';
            echo '<td><input type="text" name="probability" value="'.$R['probability'].'" size="8"/></td>';
            echo '<td><input type="text" name="min_money" value="'.$R['min_money'].'" size="11"/></td>';
            echo '<td><input type="text" name="max_money" value="'.$R['max_money'].'" size="11"/></td>';
            echo '<td><select name="randomize">';
            if ($R['randomize'] == 1){
              echo '<option value="0">No</option><option value="1" selected="true">Yes</option>';
            }else{
              echo '<option value="0" selected="true">No</option><option value="1">Yes</option>';
            }
            echo '</select></td></tr>';
            echo '</table><input type="submit" name="commit" value="Update Rule"/></form>';
          }
        }
      }else{
        if (isset($Rules["$R_id"])){
          foreach ($Rules["$R_id"] as $R){
            echo '<table border="1"><tr><th>Item</th><th>Probability</th><th>Minimum Money</th><th>Maxiumum Money</th><th>Randomize</th></tr>';
            echo '<tr><td>'.$R['item_stat_id'].' - '.$R['name'].'</td><td>'.$R['probability'].'</td><td>'.$R['min_money'].'</td><td>'.$R['max_money'].'</td><td>'.$R['randomize'].'</td></tr>';
            echo '</table>';
          }
        }
      }
      if (checkaccess('npcs', 'edit')){
        echo '<hr/>';
        echo '<form action="./index.php?do=editlootrule" method="post"><input type="hidden" name="id" value="'.$row['id'].'"/><table border="1"><tr><th>Item</th><th>Probability</th><th>Minimum Money</th><th>Maxiumum Money</th><th>Randomize</th></tr>';
        echo '<tr><td>'.DrawSelectBox('items', $item_result, 'item_stat_id', '', true).'</td>';
        echo '<td><input type="text" name="probability" value="0" size="8"/></td>';
        echo '<td><input type="text" name="min_money" value="0" size="11"/></td>';
        echo '<td><input type="text" name="max_money" value="0" size="11"/></td>';
        echo '<td><select name="randomize">';
        echo '<option value="0" selected="true">No</option><option value="1">Yes</option>';
        echo '</select></td></tr>';
        echo '</table><input type="submit" name="commit" value="Create Rule"/></form>';
      }
      echo '<br/></td><td>';
      if (checkaccess('npcs', 'edit')){
        echo '<form action="./index.php?do=editlootrule" method="post"><input type="hidden" name="id" value="'.$row['id'].'"/>';
        echo '<select name="npc_id">';
        $id = $row['id'];
        if (isset($chars["$id"])){
          foreach ($chars["$id"] AS $c_id => $c_name){
            echo '<option value="'.$c_id.'">';
            echo $c_id. ' - '. $c_name.'</option>';
          }
        }
        echo '</select><input type="submit" name="commit" value="Remove NPC" /></form>';
      }else{
        $id = $row['id'];
        if (isset($chars["$id"])){
          foreach ($chars["$id"] AS $c_id => $c_name){
            echo $c_id. ' - '. $c_name.'<br/>';
          }
        }
      }
      echo '</td></tr>'."\n";
    }
    if (checkaccess('npcs', 'create')){
      echo '<tr><td><form action="./index.php?do=editlootrule" method="post"><input type="hidden" name="id" value="0" />';
      echo '<input type="text" name="name" size="30" /><input type="submit" name="commit" value="Create New Rule"/></form></td>';
      echo '<td></td><td></td></tr>';
    }
    echo '</table>';
  }else{
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}

function editlootrule(){
  if (checkaccess('npcs', 'edit')){
    if (isset($_POST['id'])){
      $id = mysql_real_escape_string($_POST['id']);
      if ($_POST['commit'] == "Change Name"){
        $name = mysql_real_escape_string($_POST['name']);
        $query = "UPDATE loot_rules SET name='$name' WHERE id='$id'";
        $result = mysql_query2($query);
        echo '<p class="error">Update Successful</p>';
        listlootrules();
      }else if ($_POST['commit'] == "Update Rule"){
        $item_stat_id = mysql_real_escape_string($_POST['item_stat_id']);
        $probability = mysql_real_escape_string($_POST['probability']);
        $min_money = mysql_real_escape_string($_POST['min_money']);
        $max_money = mysql_real_escape_string($_POST['max_money']);
        $randomize = mysql_real_escape_string($_POST['randomize']);
        $query = "UPDATE loot_rule_details SET item_stat_id='$item_stat_id', probability='$probability', min_money='$min_money', max_money='$max_money', randomize='$randomize' WHERE id='$id'";
        $result = mysql_query2($query);
        echo '<p class="error">Update Successful</p>';
        listlootrules();
      }else if ($_POST['commit'] == "Remove NPC"){
        $npc_id = mysql_real_escape_string($_POST['npc_id']);
        $query = "UPDATE characters SET npc_addl_loot_category_id='0' WHERE id='$npc_id'";
        $result = mysql_query2($query);
        echo '<p class="error">Update Successful</p>';
        listlootrules();
      }else if ($_POST['commit'] == "Create Rule"){
        $item_stat_id = mysql_real_escape_string($_POST['item_stat_id']);
        $probability = mysql_real_escape_string($_POST['probability']);
        $min_money = mysql_real_escape_string($_POST['min_money']);
        $max_money = mysql_real_escape_string($_POST['max_money']);
        $randomize = mysql_real_escape_string($_POST['randomize']);
        $query = "INSERT INTO loot_rule_details (loot_rule_id, item_stat_id, probability, min_money, max_money, randomize) VALUES ('$id', '$item_stat_id', '$probability', '$min_money', '$max_money', '$randomize')";
        $result = mysql_query2($query);
        echo '<p class="error">Update Successful</p>';
        listlootrules();
      }else if ($_POST['commit'] == "Create New Rule"){
        $name = mysql_real_escape_string($_POST['name']);
        $query = "INSERT INTO loot_rules (name) VALUES ('$name')";
        $result = mysql_query2($query);
        echo '<p class="error">Update Successful</p>';
        listlootrules();
      }
    }else{
      echo '<p class="error">Error: No ID specified</p>';
      listlootrules();
    }
  }else{
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}
     
?>
