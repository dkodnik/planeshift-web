<?php
function spells(){
  if (checkaccess('rules', 'read')){
    if (checkaccess('rules', 'delete') && isset($_POST['commit']) && isset($_GET['way'])){
      $id = mysql_real_escape_string($_POST['id']);
      if ($_POST['commit'] == 'Confirm Delete'){
        $query = "DELETE FROM spells WHERE id='$id'";
        $result = mysql_query2($query);
        echo '<p class="error">Update Successful</p>';
      }else{
        echo '<p class="error">Please Confirm you want to delete spell id '.$id;
        $query = "SELECT name FROM spells WHERE id='$id'";
        $result = mysql_query2($query);
        $r = mysql_fetch_array($result, MYSQL_ASSOC);
        $name = $r['name'];
        $way = $_GET['way'];
        echo ' - '.$name.'</p><form action="./index.php?do=spells&amp;way='.$way.'" method="post"><input type="hidden" name="id" value="'.$id.'"/><input type="submit" name="commit" value="Confirm Delete"></form>';
      }
    }
    if (isset($_GET['way'])){
      $way = mysql_real_escape_string($_GET['way']);
      $query = "SELECT name, realm, spell_description, id FROM spells WHERE way_id='$way' ORDER BY realm, name";
      $result = mysql_query2($query);
      if (mysql_num_rows($result) > 0){
        echo '<table border="1"><tr><th>Name</th><th>Realm</th><th>Description</th>';
        if (checkaccess('rules', 'delete')){
          echo '<th>Actions</th>';
        }
        echo '</tr>';
        while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
          echo '<tr><td>';
          echo '<a href="./index.php?do=spell&amp;id='.$row['id'].'">'.$row['name'].'</a>';
          echo '</td><td>';
          echo $row['realm'];
          echo '</td><td>';
          echo $row['spell_description'];
          echo '</td>';
          if (checkaccess('rules', 'delete')){
            echo '<td><form action="./index.php?do=spells&amp;way='.$way.'" method="post"><input type="hidden" name="id" value="'.$row['id'].'" /><input type="submit" name="commit" value="Delete"></form></td>';
          }
          echo '</tr>';
        }
        echo '</table>';
      }else{
        echo '<p class="error">No Spells found</p>';
      }
    }else{
      $query = "SELECT w.name, w.id, COUNT(s.id) AS count FROM ways AS w LEFT JOIN spells AS s ON w.id=s.way_id GROUP BY w.id ORDER BY w.name";
      $result = mysql_query2($query);
      if (mysql_num_rows($result) > 0){
        echo '<table border="1"><tr><th>Way</th><th>Number of Spells</th></tr>';
        while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
          echo '<tr><td><a href="./index.php?do=spells&amp;way='.$row['id'].'">'.$row['name'].'</a></td>';
          echo '<td>'.$row['count'].'</td></tr>';
        }
        echo '</table>';
      }else{
        echo '<p class="error">Error: No Ways found</p>';
      }
    }
  }else{
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}

function spell(){
  if (checkaccess('rules', 'read')){
    if (isset($_GET['id'])){
      $id = mysql_real_escape_string($_GET['id']);
      $query = "SELECT s.name, s.way_id, s.realm, s.casting_effect, s.spell_description, s.offensive, s.outcome, s.max_power, s.npc_spell_power, s.target_type, s.cast_duration, s.range, s.aoe_radius, s.aoe_angle, s.image_name, s.cstr_npc_spell_category AS cstr FROM spells AS s WHERE s.id='$id'";
      $result = mysql_query2($query);
      $row = mysql_fetch_array($result, MYSQL_ASSOC);
      if (checkaccess('rules', 'edit')){
        if (isset($_POST['commit'])){
          $name = mysql_real_escape_string($_POST['name']);
          $way_id = mysql_real_escape_string($_POST['way_id']);
          $realm = mysql_real_escape_string($_POST['realm']);
          $casting_effect = mysql_real_escape_string($_POST['casting_effect']);
          $spell_description = mysql_real_escape_string($_POST['spell_description']);
          $offensive = mysql_real_escape_string($_POST['offensive']);
          $outcome = mysql_real_escape_string($_POST['outcome']);
          $max_power = mysql_real_escape_string($_POST['max_power']);
          $npc_spell_power = mysql_real_escape_string($_POST['npc_spell_power']);
          $target_type=0;
          foreach ($_POST['tt'] AS $key => $value){
            $target_type += $value;
          } 
          $cast_duration = mysql_real_escape_string($_POST['cast_duration']);
          $range = mysql_real_escape_string($_POST['range']);
          $aoe_radius = mysql_real_escape_string($_POST['aoe_radius']);
          $aoe_angle = mysql_real_escape_string($_POST['aoe_angle']);
          $image_name = mysql_real_escape_string($_POST['image_name']);
          $cstr_npc_spell_category = mysql_real_escape_string($_POST['cstr_npc_spell_category']);
          $pos0 = mysql_real_escape_string($_POST['position-0']);
          $pos1 = mysql_real_escape_string($_POST['position-1']);
          $pos2 = mysql_real_escape_string($_POST['position-2']);
          $pos3 = mysql_real_escape_string($_POST['position-3']);
          $query = "UPDATE spells SET name='$name', way_id='$way_id', realm='$realm', casting_effect='$casting_effect', spell_description='$spell_description', offensive='$offensive', outcome='$outcome', max_power='$max_power', npc_spell_power='$npc_spell_power', target_type='$target_type', cast_duration='$cast_duration', `range`='$range', aoe_radius='$aoe_radius', aoe_angle='$aoe_angle', image_name='$image_name', cstr_npc_spell_category='$cstr_npc_spell_category' WHERE id='$id'";
          $result = mysql_query2($query);
          if ($pos0 == ''){
            $query = "DELETE FROM spell_glyphs WHERE spell_id='$id' AND position='0'";
          }else{
            $query = "INSERT INTO spell_glyphs (spell_id, item_id, position) VALUES ('$id', '$pos0', '0') ON DUPLICATE KEY UPDATE item_id='$pos0'";
          }
          $result = mysql_query2($query);
          if ($pos1 == ''){
            $query = "DELETE FROM spell_glyphs WHERE spell_id='$id' AND position='1'";
          }else{
            $query = "INSERT INTO spell_glyphs (spell_id, item_id, position) VALUES ('$id', '$pos1', '1') ON DUPLICATE KEY UPDATE item_id='$pos1'";
          }
          $result = mysql_query2($query);
          if ($pos2 == ''){
            $query = "DELETE FROM spell_glyphs WHERE spell_id='$id' AND position='2'";
          }else{
            $query = "INSERT INTO spell_glyphs (spell_id, item_id, position) VALUES ('$id', '$pos2', '2') ON DUPLICATE KEY UPDATE item_id='$pos2'";
          }
          $result = mysql_query2($query);
          if ($pos3 == ''){
            $query = "DELETE FROM spell_glyphs WHERE spell_id='$id' AND position='3'";
          }else{
            $query = "INSERT INTO spell_glyphs (spell_id, item_id, position) VALUES ('$id', '$pos3', '3') ON DUPLICATE KEY UPDATE item_id='$pos3'";
          }
          $result = mysql_query2($query);
          unset($_POST);
          echo '<p class="error">Update Successful</p>';
          spell();
        }else{
          echo '<form action="./index.php?do=spell&amp;id='.$id.'" method="post">';
          echo '<table border="1"><tr><th>Field</th><th>Value</th></tr>';
          echo '<tr><td>Name:</td><td><input type="text" name="name" value="'.$row['name'].'" size="30" /></td></tr>';
          $ways = PrepSelect ('ways');
          echo '<tr><td>Way:</td><td>'.DrawSelectBox('ways', $ways, 'way_id', $row['way_id']).'</td></tr>';
          echo '<tr><td>Realm:</td><td><select name="realm">';
          $i = 1;
          while ($i <= 10){
            if ($row['realm'] == $i){
              echo '<option value="'.$i.'" selected="true">'.$i.'</option>';
            }else{
              echo '<option value="'.$i.'">'.$i.'</option>';
            }
            $i++;
          }
          echo '</select></td></tr>';
          echo '<tr><td>Casting Effect:</td><td><input type="text" name="casting_effect" value="'.$row['casting_effect'].'" size="30"/></td></tr>';
          echo '<tr><td>Description:</td><td><textarea name="spell_description" rows="4" cols="50">'.$row['spell_description'].'</textarea></td></tr>';
          echo '<tr><td>Offensive:</td><td><select name="offensive">';
          if ($row['offensive'] == '1'){
            echo '<option value="1" selected="true">True</option><option value="0">False</option>';
          }else{
            echo '<option value="1">True</option><option value="0" selected="true">False</option>';
          }
          echo '</select></td></tr>';
          $prog_events = PrepSelect('cast_events');
          echo '<tr><td>Outcome</td><td>'.DrawSelectBox('cast_events', $prog_events, 'outcome', $row['outcome'], true).'</td></tr>';
          echo '<tr><td>Max Power</td><td><input type="text" name="max_power" value="'.$row['max_power'].'" size="30"/></td></tr>';
          echo '<tr><td>NPC Spell Power</td><td><input type="text" name="npc_spell_power" value="'.$row['npc_spell_power'].'" size="30" /></td></tr>';
          echo '<tr><td>Target Type</td><td>';
          $tt = $row['target_type'];
          if ($tt >=64){
            echo 'TARGET_DEAD: <input type="checkbox" name="tt[]" value="64" checked="true"/><br/>';
            $tt -= 64;
          }else{
            echo 'TARGET_DEAD: <input type="checkbox" name="tt[]" value="64"/><br/>';
          }
          if ($tt >=32){
            echo 'TARGET_FOE: <input type="checkbox" name="tt[]" value="32" checked="true"/><br/>';
            $tt -= 32;
          }else{
            echo 'TARGET_FOE: <input type="checkbox" name="tt[]" value="32"/><br/>';
          }
          if ($tt >=16){
            echo 'TARGET_FRIEND: <input type="checkbox" name="tt[]" value="16" checked="true"/><br/>';
            $tt -= 16;
          }else{
            echo 'TARGET_FRIEND: <input type="checkbox" name="tt[]" value="16"/><br/>';
          }
          if ($tt >=8){
            echo 'TARGET_SELF: <input type="checkbox" name="tt[]" value="8" checked="true"/><br/>';
            $tt -= 8;
          }else{
            echo 'TARGET_SELF: <input type="checkbox" name="tt[]" value="8"/><br/>';
          }
          if ($tt >=4){
            echo 'TARGET_ITEM: <input type="checkbox" name="tt[]" value="4" checked="true"/><br/>';
            $tt -= 4;
          }else{
            echo 'TARGET_ITEM: <input type="checkbox" name="tt[]" value="4"/><br/>';
          }
          if ($tt >= 1){
            echo 'TARGET_NONE: <input type="checkbox" name="tt[]" value="1" checked="true"/><br/>';
            $tt -= 1;
          }else{
            echo 'TARGET_NONE: <input type="checkbox" name="tt[]" value="1"/><br/>';
          }
          echo '</td></tr>';
          echo '<tr><td>Cast Duration</td><td><input type="text" name="cast_duration" value="'.$row['cast_duration'].'" size="30" /></td></tr>';
          echo '<tr><td>Range</td><td><input type="text" name="range" value="'.$row['range'].'" size="30" /></td></tr>';
          echo '<tr><td>Aoe Radius</td><td><input type="text" name="aoe_radius" value="'.$row['aoe_radius'].'" size="30" /></td></tr>';
          echo '<tr><td>Aoe Angle</td><td><input type="text" name="aoe_angle" value="'.$row['aoe_angle'].'" size="30" /></td></tr>';
          echo '<tr><td>Image Name</td><td><input type="text" name="image_name" value="'.$row['image_name'].'" size="30" /></td></tr>';
          echo '<tr><td>NPC Spell Type</td><td><input type="text" name="cstr_npc_spell_category" value="'.$row['cstr'].'"/></td></tr>';
          $query = "SELECT g.item_id, g.position FROM spell_glyphs AS g WHERE g.spell_id='$id' ORDER BY g.position";
          $result2 = mysql_query2($query);
          $i = 0;
          $glyphs = PrepSelect('Glyphs');
          while ($i < 4){
            $r2 = mysql_fetch_array($result2, MYSQL_ASSOC);
            $j = $i+1;
            echo '<tr><td>Glyph Slot '. $j.':</td><td>';
            if ($i == $r2['position']){
              echo DrawSelectBox('glyphs', $glyphs, "position-$i", $r2['item_id'], true);
            }else{
              echo DrawSelectBox('glyphs', $glyphs, "position-$i", '', true);
            }
            echo '</td></tr>';
            $i++;
          }
          echo '</table>';
          echo '<input type="submit" name="commit" value="Update Spell"/>';
          echo '</form>';
        }
      }else{
        echo '<table border="1"><tr><th>Field</th><th>Value</th></tr>';
        echo '<tr><td>Name:</td><td>'.$row['name'].'</td></tr>';
        echo '<tr><td>Way:</td><td>'.$row['way'].'</td></tr>';
        echo '<tr><td>Realm:</td><td>'.$row['realm'].'</td></tr>';
        echo '<tr><td>Casting Effect:</td><td>'.$row['casting_effect'].'</td></tr>';
        echo '<tr><td>Description:</td><td>'.$row['spell_description'].'</td></tr>';
        echo '<tr><td>Offensive:</td><td>';
        if ($row['offensive'] == '1'){
          echo 'True</td></tr>';
        }else{
          echo 'False</td></tr>';
        }
        echo '<tr><td>Outcome</td><td>'.$row['outcome'].'</td></tr>';
        echo '<tr><td>Max Power</td><td>'.$row['max_power'].'</td></tr>';
        echo '<tr><td>NPC Spell Power</td><td>'.$row['npc_spell_power'].'</td></tr>';
        echo '<tr><td>Target Type</td><td>';
        $tt = $row['target_type'];
        while ($tt > 0){
          if ($tt >=64){
            echo 'TARGET_DEAD<br/>';
            $tt -= 64;
          }else if ($tt >=32){
            echo 'TARGET_FOE<br/>';
            $tt -= 32;
          }else if ($tt >=16){
            echo 'TARGET_FRIEND<br/>';
            $tt -= 16;
          }else if ($tt >=8){
            echo 'TARGET_SELF<br/>';
            $tt -= 8;
          }else if ($tt >=4){
            echo 'TARGET_ITEM<br/>';
            $tt -= 4;
          }else{
            echo 'TARGET_NONE<br/>';
            $tt -= 1;
          }
        }
        echo '</td></tr>';
        echo '<tr><td>Cast Duration</td><td>'.$row['cast_duration'].'</td></tr>';
        echo '<tr><td>Range</td><td>'.$row['range'].'</td></tr>';
        echo '<tr><td>Aoe Radius</td><td>'.$row['aoe_radius'].'</td></tr>';
        echo '<tr><td>Aoe Angle</td><td>'.$row['aoe_angle'].'</td></tr>';
        echo '<tr><td>Image Name</td><td>'.$row['image_name'].'</td></tr>';
        echo '<tr><td>NPC Spell Type</td><td>'.$row['cstr'].'</td></tr>';
        $query = "SELECT g.item_id, g.position, i.name FROM spell_glyphs AS g LEFT JOIN item_stats AS i ON i.id=g.item_id WHERE g.spell_id='$id' ORDER BY g.position";
        $result2 = mysql_query2($query);
        $i = 0;
        while ($i < 4){
          $r2 = mysql_fetch_array($result2, MYSQL_ASSOC);
          $j = $i+1;
          echo '<tr><td>Glyph Slot '. $j.':</td><td>';
          if ($i == $r2['position']){
            echo $r2['item_id'].' - '.$r2['name'];
          }else{
            echo 'Empty';
          }
          echo '</td></tr>';
          $i++;
        }
        echo '</table>';
      }
    }else{
      echo '<p class="error">Error: No spell selected - Returning to spell listing</p>';
      spells();
    }
  }else{
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}

function createspell(){
  if (checkaccess('rules', 'create')){
    if (isset($_POST['commit'])){
      $name = mysql_real_escape_string($_POST['name']);
      $way_id = mysql_real_escape_string($_POST['way_id']);
      $realm = mysql_real_escape_string($_POST['realm']);
      $casting_effect = mysql_real_escape_string($_POST['casting_effect']);
      $spell_description = mysql_real_escape_string($_POST['spell_description']);
      $offensive = mysql_real_escape_string($_POST['offensive']);
      $outcome = mysql_real_escape_string($_POST['outcome']);
      $max_power = mysql_real_escape_string($_POST['max_power']);
      $npc_spell_power = mysql_real_escape_string($_POST['npc_spell_power']);
      $target_type=0;
      if (isset($_POST['tt'])){
        foreach ($_POST['tt'] AS $key => $value){
          $target_type += $value;
        }
      }
      $cast_duration = mysql_real_escape_string($_POST['cast_duration']);
      $range = mysql_real_escape_string($_POST['range']);
      $aoe_radius = mysql_real_escape_string($_POST['aoe_radius']);
      $aoe_angle = mysql_real_escape_string($_POST['aoe_angle']);  
      $image_name = mysql_real_escape_string($_POST['image_name']);
      $cstr_npc_spell_category = mysql_real_escape_string($_POST['cstr_npc_spell_category']);
      $pos0 = mysql_real_escape_string($_POST['position-0']);
      $pos1 = mysql_real_escape_string($_POST['position-1']);
      $pos2 = mysql_real_escape_string($_POST['position-2']);
      $pos3 = mysql_real_escape_string($_POST['position-3']);
      $query = "INSERT INTO spells SET name='$name', way_id='$way_id', realm='$realm', casting_effect='$casting_effect', spell_description='$spell_description', offensive='$offensive', outcome='$outcome', max_power='$max_power', npc_spell_power='$npc_spell_power', target_type='$target_type', cast_duration='$cast_duration', `range`='$range', aoe_radius='$aoe_radius', aoe_angle='$aoe_angle', image_name='$image_name', cstr_npc_spell_category='$cstr_npc_spell_category'";
      $result = mysql_query2($query);
      $query = "SELECT id FROM spells WHERE name='$name'";
      $result = mysql_query2($query);
      $r = mysql_fetch_array($result, MYSQL_ASSOC);
      $id = $r['id'];
      if ($pos0 == ''){
        $query = "DELETE FROM spell_glyphs WHERE spell_id='$id' AND position='0'";
      }else{
        $query = "INSERT INTO spell_glyphs (spell_id, item_id, position) VALUES ('$id', '$pos0', '0') ON DUPLICATE KEY UPDATE item_id='$pos0'";
      }
      $result = mysql_query2($query);
      if ($pos1 == ''){
        $query = "DELETE FROM spell_glyphs WHERE spell_id='$id' AND position='1'";
      }else{
        $query = "INSERT INTO spell_glyphs (spell_id, item_id, position) VALUES ('$id', '$pos1', '1') ON DUPLICATE KEY UPDATE item_id='$pos1'";
      }
      $result = mysql_query2($query);
      if ($pos2 == ''){
        $query = "DELETE FROM spell_glyphs WHERE spell_id='$id' AND position='2'";
      }else{
        $query = "INSERT INTO spell_glyphs (spell_id, item_id, position) VALUES ('$id', '$pos2', '2') ON DUPLICATE KEY UPDATE item_id='$pos2'";
      }
      $result = mysql_query2($query);
      if ($pos3 == ''){
        $query = "DELETE FROM spell_glyphs WHERE spell_id='$id' AND position='3'";
      }else{
        $query = "INSERT INTO spell_glyphs (spell_id, item_id, position) VALUES ('$id', '$pos3', '3') ON DUPLICATE KEY UPDATE item_id='$pos3'";
      }
      $result = mysql_query2($query);
      unset($_POST);
      echo '<p class="error">Update Successful</p>';
      spell();
    }else{
      echo '<form action="./index.php?do=createspell" method="post">';
      echo '<table border="1"><tr><th>Field</th><th>Value</th></tr>';
      echo '<tr><td>Name:</td><td><input type="text" name="name" size="30" /></td></tr>';
      $ways = PrepSelect ('ways');
      echo '<tr><td>Way:</td><td>'.DrawSelectBox('ways', $ways, 'way_id', '').'</td></tr>';
      echo '<tr><td>Realm:</td><td><select name="realm">';
      $i = 1;
      while ($i <= 10){
        echo '<option value="'.$i.'">'.$i.'</option>';
        $i++;
      }
      echo '</select></td></tr>';
      echo '<tr><td>Casting Effect:</td><td><input type="text" name="casting_effect" size="30"/></td></tr>';
      echo '<tr><td>Description:</td><td><textarea name="spell_description" rows="4" cols="50"></textarea></td></tr>';
      echo '<tr><td>Offensive:</td><td><select name="offensive">';
      echo '<option value="1" selected="true">True</option><option value="0">False</option>';
      echo '</select></td></tr>';
      $outcome = PrepSelect('cast_events');
      echo '<tr><td>Outcome</td><td>'.DrawSelectBox('cast_events', $outcome, 'outcome', '', true).'</td></tr>';
      echo '<tr><td>Max Power</td><td><input type="text" name="max_power" size="30"/></td></tr>';
      echo '<tr><td>NPC Spell Power</td><td><input type="text" name="npc_spell_power" size="30" /></td></tr>';
      echo '<tr><td>Target Type</td><td>';
      echo 'TARGET_DEAD: <input type="checkbox" name="tt[]" value="64"/><br/>';
      echo 'TARGET_FOE: <input type="checkbox" name="tt[]" value="32"/><br/>';
      echo 'TARGET_FRIEND: <input type="checkbox" name="tt[]" value="16"/><br/>';
      echo 'TARGET_SELF: <input type="checkbox" name="tt[]" value="8"/><br/>';
      echo 'TARGET_ITEM: <input type="checkbox" name="tt[]" value="4"/><br/>';
      echo 'TARGET_NONE: <input type="checkbox" name="tt[]" value="1"/><br/>';
      echo '</td></tr>';
      echo '<tr><td>Cast Duration</td><td><input type="text" name="cast_duration" value="" size="30" /></td></tr>';
      echo '<tr><td>Range</td><td><input type="text" name="range" value="" size="30" /></td></tr>';
      echo '<tr><td>Aoe Radius</td><td><input type="text" name="aoe_radius" value="" size="30" /></td></tr>';
      echo '<tr><td>Aoe Range</td><td><input type="text" name="aoe_angle" value="" size="30" /></td></tr>';
      
      echo '<tr><td>Image Name</td><td><input type="text" name="image_name" size="30" /></td></tr>';
      echo '<tr><td>NPC Spell Type</td><td><input type="text" name="cstr_npc_spell_category" /></td></tr>';
      $i = 0;
      $glyphs = PrepSelect('Glyphs');
      while ($i < 4){
        $j = $i+1;
        echo '<tr><td>Glyph Slot '. $j.':</td><td>';
        echo DrawSelectBox('glyphs', $glyphs, "position-$i", '', true);
        echo '</td></tr>';
        $i++;
      }
      echo '</table>';
      echo '<input type="submit" name="commit" value="Update Spell"/>';
      echo '</form>';
    }
  }else{
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}
?>
 
