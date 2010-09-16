<?php
function raceinfo(){
  if (checkaccess('rules','read')){
    if (checkaccess('rules', 'edit') && isset($_POST['commit'])){
      if ($_POST['commit'] == "Confirm Spawn Point Delete"){
        $x = mysql_real_escape_string($_POST['x']);
        $y = mysql_real_escape_string($_POST['y']);
        $z = mysql_real_escape_string($_POST['z']);
        $raceid = mysql_real_escape_string($_POST['id']);
        $yrot = mysql_real_escape_string($_POST['yrot']);
        $range = mysql_real_escape_string($_POST['range']);
        $sec = mysql_real_escape_string($_POST['sec']);
        $query = "DELETE FROM race_spawns WHERE x='$x' AND y='$y' AND z='$z' AND yrot='$yrot' AND `range`='$range' AND sector_id='$sec' AND raceid='$raceid' LIMIT 1";
        $result = mysql_query2($query);
        echo '<p class="error">Update Successful</p>';
        unset($_POST);
        raceinfo();
      }else if ($_POST['commit'] == 'Confirm Update'){
        $id = mysql_real_escape_string($_POST['id']);
        $name = mysql_real_escape_string($_POST['name']);
        $sex = mysql_real_escape_string($_POST['sex']);
        $size_x = mysql_real_escape_string($_POST['size_x']);
        $size_y = mysql_real_escape_string($_POST['size_y']);
        $size_z = mysql_real_escape_string($_POST['size_z']);
        $initial_cp = mysql_real_escape_string($_POST['initial_cp']);
        $start_str = mysql_real_escape_string($_POST['start_str']);
        $start_end = mysql_real_escape_string($_POST['start_end']);
        $start_agi = mysql_real_escape_string($_POST['start_agi']);
        $start_int = mysql_real_escape_string($_POST['start_int']);
        $start_will = mysql_real_escape_string($_POST['start_will']);
        $start_cha = mysql_real_escape_string($_POST['start_cha']);
        $base_physical_regen_still = mysql_real_escape_string($_POST['base_physical_regen_still']);
        $base_physical_regen_walk = mysql_real_escape_string($_POST['base_physical_regen_walk']);
        $base_mental_regen_still = mysql_real_escape_string($_POST['base_mental_regen_still']);
        $base_mental_regen_walk = mysql_real_escape_string($_POST['base_mental_regen_walk']);
        $armor_id = mysql_real_escape_string($_POST['armor_id']);
        $weapon_id = mysql_real_escape_string($_POST['weapon_id']);
        $helm = mysql_real_escape_string($_POST['helm']);
        $bracer = mysql_real_escape_string($_POST['bracer']);
        $belt = mysql_real_escape_string($_POST['belt']);
        $cloak = mysql_real_escape_string($_POST['cloak']);
        $speed_modifier = mysql_real_escape_string($_POST['speed_modifier']);
        $scale = mysql_real_escape_string($_POST['scale']);
        $query = "UPDATE race_info SET name='$name', sex='$sex', size_x='$size_x', size_y='$size_y', size_z='$size_z', initial_cp='$initial_cp', start_str='$start_str', start_end='$start_end', start_agi='$start_agi', start_int='$start_int', start_will='$start_will', start_cha='$start_cha', base_physical_regen_still='$base_physical_regen_still', base_physical_regen_walk='$base_physical_regen_walk', base_mental_regen_still='$base_mental_regen_still', base_mental_regen_walk='$base_mental_regen_walk', armor_id='$armor_id', weapon_id='$weapon_id', helm='$helm', bracer='$bracer', belt='$belt', cloak='$cloak', speed_modifier='$speed_modifier', scale='$scale' WHERE id='$id'";
        $result = mysql_query2($query);
        echo '<p class="error">Update Successful</p>';
        unset($_POST);
        raceinfo();
      }else if ($_POST['commit'] == 'Add Spawn Point'){
        $id = mysql_real_escape_string($_POST['id']);
        $sector_id = mysql_real_escape_string($_POST['sector_id']);
        $x = mysql_real_escape_string($_POST['x']);
        $y = mysql_real_escape_string($_POST['y']);
        $z = mysql_real_escape_string($_POST['z']);
        $yrot = mysql_real_escape_string($_POST['yrot']);
        $range = mysql_real_escape_string($_POST['range']);
        $query = "INSERT INTO race_spawns (raceid, sector_id, x, y, z, yrot, `range`) VALUES ('$id', '$sector_id', '$x', '$y', '$z', '$yrot', '$range')";
        $result = mysql_query2($query);
        echo '<p class="error">Update Successful</p>';
        unset($_POST);
        raceinfo();
      }
    }else if (checkaccess('rules', 'edit') && isset($_POST['action'])){
      if ($_POST['action'] == "Delete"){
        $x = mysql_real_escape_string($_POST['x']);
        $y = mysql_real_escape_string($_POST['y']);
        $z = mysql_real_escape_string($_POST['z']);
        $yrot = mysql_real_escape_string($_POST['yrot']);
        $range = mysql_real_escape_string($_POST['range']);
        $raceid = mysql_real_escape_string($_POST['id']);
        $sec = mysql_real_escape_string($_POST['sec']);
        $query = "SELECT r.name, r.sex, p.x, p.y, p.z, s.name AS sector FROM race_spawns AS p LEFT JOIN race_info AS r ON r.id=p.raceid LEFT JOIN sectors AS s ON s.id=p.sector_id WHERE p.sector_id='$sec' AND p.raceid='$raceid' AND p.x='$x' AND p.z='$z' AND p.y='$y' AND p.yrot='$yrot' AND p.range='$range'";
        $result = mysql_query2($query);
        if (mysql_numrows($result) > 0){
          $row = mysql_fetch_array($result, MYSQL_ASSOC);
          echo 'You are about to delete the following Spawn-Point:<br/>'.$row['name'].' '.$row['sex'].': '.$row['x'].' / '.$row['y'].' / '.$row['z'].' / '.$row['sector'].' yrot: '.$yrot.' range: '.$range.'<br/>';
          echo '<form action="./index.php?do=raceinfo" method="post">';
          echo '<input type="hidden" name="x" value="'.$x.'" />';
          echo '<input type="hidden" name="y" value="'.$y.'" />';
          echo '<input type="hidden" name="z" value="'.$z.'" />';
          echo '<input type="hidden" name="yrot" value="'.$yrot.'" />';
          echo '<input type="hidden" name="range" value="'.$range.'" />';
          echo '<input type="hidden" name="id" value="'.$raceid.'" />';
          echo '<input type="hidden" name="sec" value="'.$sec.'" />';
          echo '<input type="submit" name="commit" value="Confirm Spawn Point Delete" />';
          echo '</form>';
        }
      }else if ($_POST['action'] == "Edit Values"){
        $id = mysql_real_escape_string($_POST['id']);
        $query = "SELECT * FROM race_info WHERE id='$id'";
        $result = mysql_query2($query);
        $row = mysql_fetch_array($result, MYSQL_ASSOC);
        echo '<form action="./index.php?do=raceinfo" method="post"><input type="hidden" name="id" value="'.$id.'" />';
        echo '<table border="1"><tr><th>Field</th><th>Value</th></tr>';
        echo '<tr><td>Race:</td><td><input type="text" name="name" value="'.$row['name'].'"/></td></tr>';
        echo '<tr><td>Sex:</td><td><select name="sex">';
        if ($row['sex'] == 'M'){
          echo '<option value="M" selected="true">Male</option><option value="F">Female</option><option value="N">Neuter</option>';
        }else if ($row['sex'] == 'F'){
          echo '<option value="M">Male</option><option value="F" selected="true">Female</option><option value="N">Neuter</option>';
        }else if ($row['sex'] == 'N'){
          echo '<option value="M">Male</option><option value="F">Female</option><option value="N" selected="true">Neuter</option>';
        }
        echo '</select></td></tr>';
        echo '<tr><td>Size X:</td><td><input type="text" name="size_x" value="'.$row['size_x'].'"/></td></tr>';
        echo '<tr><td>Size Y:</td><td><input type="text" name="size_y" value="'.$row['size_y'].'"/></td></tr>';
        echo '<tr><td>Size Z:</td><td><input type="text" name="size_z" value="'.$row['size_z'].'"/></td></tr>';
        echo '<tr><td>Starting Character Points:</td><td><input type="text" name="initial_cp" value="'.$row['initial_cp'].'"/></td></tr>';
        echo '<tr><td>Base Strength:</td><td><input type="text" name="start_str" value="'.$row['start_str'].'"/></td></tr>';
        echo '<tr><td>Base Endurance:</td><td><input type="text" name="start_end" value="'.$row['start_end'].'"/></td></tr>';
        echo '<tr><td>Base Agility:</td><td><input type="text" name="start_agi" value="'.$row['start_agi'].'"/></td></tr>';
        echo '<tr><td>Base Intelligence:</td><td><input type="text" name="start_int" value="'.$row['start_int'].'"/></td></tr>';
        echo '<tr><td>Base Willpower:</td><td><input type="text" name="start_will" value="'.$row['start_will'].'"/></td></tr>';
        echo '<tr><td>Base Charisma:</td><td><input type="text" name="start_cha" value="'.$row['start_cha'].'"/></td></tr>';
        echo '<tr><td>Physical Rengeneration (Standing/Walking):</td><td><input type="text" name="base_physical_regen_still" value="'.$row['base_physical_regen_still'].'"/> / <input type="text" name="base_physical_regen_walk" value="'.$row['base_physical_regen_walk'].'" /></td></tr>';
        echo '<tr><td>Mental Regeneration (Standing/Walking):</td><td><input type="text" name="base_mental_regen_still" value="'.$row['base_mental_regen_still'].'"/> / <input type="text" name="base_mental_regen_walk" value="'.$row['base_mental_regen_walk'].'" /></td></tr>';
        $armors = PrepSelect('armor');
        $weapons = PrepSelect('weapon');
        echo '<tr><td>Armor ID:</td><td>'.DrawSelectBox('armor', $armors, 'armor_id', $row['armor_id'], true).'</td></tr>';
        echo '<tr><td>Weapon ID:</td><td>'.DrawSelectBox('weapon', $weapons, 'weapon_id', $row['weapon_id'], true).'</td></tr>';
        echo '<tr><td>Helm:</td><td><input type="text" name="helm" value="'.$row['helm'].'"/></td></tr>';
        echo '<tr><td>Bracer:</td><td><input type="text" name="bracer" value="'.$row['bracer'].'"/></td></tr>';
        echo '<tr><td>Belt:</td><td><input type="text" name="belt" value="'.$row['belt'].'"/></td></tr>';  
        echo '<tr><td>Cloak:</td><td><input type="text" name="cloak" value="'.$row['cloak'].'"/></td></tr>';
        echo '<tr><td>Speed modifier:</td><td><input type="text" name="speed_modifier" value="'.$row['speed_modifier'].'"/></td></tr>';
        echo '<tr><td>Scale:</td><td><input type="text" name="scale" value="'.$row['scale'].'"/></td></tr>';
        echo '</table><input type="submit" name="commit" value="Confirm Update" />';
        echo '</form>';
      }else if ($_POST['action'] == "Add Spawn Point"){
        $id = mysql_real_escape_string($_POST['id']);
        $Sectors = PrepSelect('sectorid');
        echo '<form action="./index.php?do=raceinfo" method="post"><input type="hidden" name="id" value="'.$id.'" />';
        echo '<table border="1"><tr><th>Field</th><th>Value</th></tr>';
        echo '<tr><td>Sector:</td><td>'.DrawSelectBox('sectorid', $Sectors, 'sector_id', '').'</td></tr>';
        echo '<tr><td>X:</td><td><input type="text" name="x"/></td></tr>';
        echo '<tr><td>Y:</td><td><input type="text" name="y"/></td></tr>';
        echo '<tr><td>Z:</td><td><input type="text" name="z"/></td></tr>';
        echo '<tr><td>yrot:</td><td><input type="text" name="yrot"/></td></tr>';
        echo '<tr><td>Range:</td><td><input type="text" name="range"/></td></tr>';
        echo '</table><input type="submit" name="commit" value="Add Spawn Point"/>';
        echo '</form>';
      }else{
      }
    }else{    
      $query = "SELECT r.*, s.name FROM race_spawns AS r LEFT JOIN sectors AS s ON r.sector_id=s.id";
      $result = mysql_query2($query);
      while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
        $raceid = $row['raceid'];
        if (isset($Spawns[$raceid])){
          $i = count($Spawns[$raceid]);
          $i++;
        }else{
          $i = 0;
        }
        $Spawns[$raceid][$i]['x'] = $row['x'];
        $Spawns[$raceid][$i]['y'] = $row['y'];
        $Spawns[$raceid][$i]['z'] = $row['z'];
        $Spawns[$raceid][$i]['yrot'] = $row['yrot'];
        $Spawns[$raceid][$i]['range'] = $row['range'];
        $Spawns[$raceid][$i]['sector_id'] = $row['sector_id'];
        $Spawns[$raceid][$i]['sector_name'] = $row['name'];
      }
      $query = "SELECT ri.*, ist.name AS armor_name, ist.category_id AS armor_cat, iss.name AS weapon_name, iss.category_id AS weapon_cat FROM race_info AS ri LEFT JOIN item_stats AS ist ON ist.id=ri.armor_id LEFT JOIN item_stats AS iss ON iss.id=ri.weapon_id ORDER BY name, sex";
      $result = mysql_query2($query);
      echo '<table>';
      echo '<tr><th>ID</th><th>Race</th><th>Sex</th><th>Size</th><th>CP\'s</th><th>Base STR</th><th>Base END</th><th>Base AGI</th><th>Base INT</th><th>Base WILL</th><th>Base CHA</th><th>Physical Regen (Standing/Walking)</th><th>Mental Regen (Standing/Walking)</th><th>armor_id</th><th>weapon_id</th><th>helm</th><th>bracer</th><th>belt</th><th>cloak</th><th>Speed Modifier</th><th>Scale</th><th>Spawn Points</th>';
      if (checkaccess('rules', 'edit')){
        echo '<th>Actions</th>';
      }
      echo '</tr>';
      $Alt = FALSE;
      while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
        $Alt = !$Alt;
        $raceid = $row['id'];
        if ($Alt){
          echo '<tr class="color_a">';
        }else{
          echo '<tr class="color_b">';
        }
        echo '<td>'.$row['id'].'</td>';
        echo '<td>'.$row['name'].'</td>';
        echo '<td>'.$row['sex'].'</td>';
        echo '<td>'.$row['size_x'].'/'.$row['size_y'].'/'.$row['size_z'].'</td>';
        echo '<td>'.$row['initial_cp'].'</td>';
        echo '<td>'.$row['start_str'].'</td>';
        echo '<td>'.$row['start_end'].'</td>';
        echo '<td>'.$row['start_agi'].'</td>';
        echo '<td>'.$row['start_int'].'</td>';
        echo '<td>'.$row['start_will'].'</td>';
        echo '<td>'.$row['start_cha'].'</td>';
        echo '<td>'.$row['base_physical_regen_still'].' / '.$row['base_physical_regen_walk'].'</td>';
        echo '<td>'.$row['base_mental_regen_still'].' / '.$row['base_mental_regen_walk'].'</td>';
        echo '<td><a href="./index.php?do=listitems&amp;category='.$row['armor_cat'].'&amp;item='.$row['armor_id'].'">'.$row['armor_name'].'</a></td>';
        echo '<td><a href="./index.php?do=listitems&amp;category='.$row['weapon_cat'].'&amp;item='.$row['weapon_id'].'">'.$row['weapon_name'].'</a></td>';
        echo '<td>'.$row['helm'].'</td>';
        echo '<td>'.$row['bracer'].'</td>';
        echo '<td>'.$row['belt'].'</td>'; 
        echo '<td>'.$row['cloak'].'</td>';
        echo '<td>'.$row['speed_modifier'].'</td>';
        echo '<td>'.$row['scale'].'</td>';
        echo '<td>';
        if (isset($Spawns[$raceid])){
          echo '<table border="1"><tr><th>X</th><th>Y</th><th>Z</th><th>Angle</th><th>range</th><th>Sector</th>';
          if (checkaccess('rules', 'edit')){
            echo '<th>Actions</th>';
          }
          echo '</tr>';
          foreach ($Spawns[$raceid] as $spawn){
            echo '<tr>';
            echo '<td>'.$spawn['x'].'</td>';
            echo '<td>'.$spawn['y'].'</td>';
            echo '<td>'.$spawn['z'].'</td>';
            $angle = ($spawn['yrot']*180)/3.14159;
            echo '<td>'.$angle.'</td>';
            echo '<td>'.$spawn['range'].'</td>';
            echo '<td>'.$spawn['sector_name'].'</td>';
            if (checkaccess('rules','edit')){
              echo '<td>';
              echo '<form action="./index.php?do=raceinfo" method="post">';
              echo '<input type="hidden" name="id" value="'.$row['id'].'" />';
              echo '<input type="hidden" name="x" value="'.$spawn['x'].'" />';
              echo '<input type="hidden" name="y" value="'.$spawn['y'].'" />';
              echo '<input type="hidden" name="z" value="'.$spawn['z'].'" />';
              echo '<input type="hidden" name="yrot" value="'.$spawn['yrot'].'" />';
              echo '<input type="hidden" name="range" value="'.$spawn['range'].'" />';
              echo '<input type="hidden" name="sec" value="'.$spawn['sector_id'].'" />';
              echo '<input type="submit" name="action" value="Delete" />';
              echo '</form></td>';
            }
            echo '</tr>';
          }
          echo '</table>';
        }else{
          echo 'No Spawn Points';
        }
        echo '</td>';
        if (checkaccess('rules', 'edit')){
          echo '<td>';
          echo '<form action="./index.php?do=raceinfo" method="post">';
          echo '<input type="hidden" name="id" value="'.$row['id'].'" />';
          echo '<input type="submit" name="action" value="Edit Values" />';
          echo '<input type="submit" name="action" value="Add Spawn Point" />';
          echo '</form></td>';
        }
        echo '</tr>';
      }
      echo '</table>';
    }
  }else{
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}
