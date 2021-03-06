<?php
function listresources(){
  if (checkaccess('natres', 'read')){
    if (isset($_POST['commit']) && (checkaccess('natres', 'edit'))){
      if ($_POST['commit'] == "Commit Edit"){
        $id = escapeSqlString($_POST['id']);
        $loc_sector_id = escapeSqlString($_POST['loc_sector_id']);
        $loc_x = escapeSqlString($_POST['loc_x']);
        $loc_y = escapeSqlString($_POST['loc_y']);
        $loc_z = escapeSqlString($_POST['loc_z']);
        $radius = escapeSqlString($_POST['radius']);
        $visible_radius = escapeSqlString($_POST['visible_radius']);
        $probability = escapeSqlString($_POST['probability']);
        $skill = escapeSqlString($_POST['skill']);
        $skill_level = escapeSqlString($_POST['skill_level']);
        $item_cat_id = escapeSqlString($_POST['item_cat_id']);
        $item_quality = escapeSqlString($_POST['item_quality']);
        $item_id_reward = escapeSqlString($_POST['item_id_reward']);
        $animation = escapeSqlString($_POST['animation']);
        $anim_duration_seconds = escapeSqlString($_POST['anim_duration_seconds']);
        $action = escapeSqlString($_POST['action']);
        $reward_nickname = escapeSqlString($_POST['reward_nickname']);
		$amount = escapeSqlString($_POST['amount']);
		if (!$amount)
			$amount = 'NULL';
		$interval = escapeSqlString($_POST['interval']);
		$max_random = escapeSqlString($_POST['max_random']);
        $query = "UPDATE natural_resources SET loc_sector_id='$loc_sector_id', loc_x='$loc_x', loc_y='$loc_y', loc_z='$loc_z', radius='$radius', visible_radius='$visible_radius', probability='$probability', skill='$skill', skill_level='$skill_level', item_cat_id='$item_cat_id', item_quality='$item_quality', item_id_reward='$item_id_reward', animation='$animation', anim_duration_seconds='$anim_duration_seconds', action='$action', reward_nickname='$reward_nickname', amount=$amount, `interval`=$interval, max_random=$max_random WHERE id='$id'";
        $result = mysql_query2($query);
        echo '<p class="error">Update Successful</p>';
        unset($_POST);
        listresources();
        return; 
      }else if($_POST['commit'] == "Commit New" && checkaccess('natres', 'create')){
        $loc_sector_id = escapeSqlString($_POST['loc_sector_id']);
        $loc_x = escapeSqlString($_POST['loc_x']);
        $loc_y = escapeSqlString($_POST['loc_y']);
        $loc_z = escapeSqlString($_POST['loc_z']);
        $radius = escapeSqlString($_POST['radius']);
        $visible_radius = escapeSqlString($_POST['visible_radius']);
        $probability = escapeSqlString($_POST['probability']);
        $skill = escapeSqlString($_POST['skill']);
        $skill_level = escapeSqlString($_POST['skill_level']);
        $item_cat_id = escapeSqlString($_POST['item_cat_id']);
        $item_quality = escapeSqlString($_POST['item_quality']);
        $item_id_reward = escapeSqlString($_POST['item_id_reward']);
        $animation = escapeSqlString($_POST['animation']);
        $anim_duration_seconds = escapeSqlString($_POST['anim_duration_seconds']);
        $action = escapeSqlString($_POST['action']);
        $reward_nickname = escapeSqlString($_POST['reward_nickname']);
        $query = "INSERT INTO natural_resources SET loc_sector_id='$loc_sector_id', loc_x='$loc_x', loc_y='$loc_y', loc_z='$loc_z', radius='$radius', visible_radius='$visible_radius', probability='$probability', skill='$skill', skill_level='$skill_level', item_cat_id='$item_cat_id', item_quality='$item_quality', item_id_reward='$item_id_reward', animation='$animation', anim_duration_seconds='$anim_duration_seconds', action='$action', reward_nickname='$reward_nickname'";
        $result = mysql_query2($query);
        echo '<p class="error">Update Successful</p>';
        unset($_POST);
        listresources();
        return;
      }else if($_POST['commit'] == "Confirm Delete" && checkaccess('natres', 'delete')){
        $id = escapeSqlString($_POST['id']);
        $query = "DELETE FROM natural_resources WHERE id='$id'";
        $result = mysql_query2($query);
        unset($_POST);
        echo '<p class="error">Update Successful</p>';
        listresources();
        return;
      }
    }else if (isset($_POST['action']) && (checkaccess('natres', 'edit'))){
      if ($_POST['action'] == 'Edit'){
        $id = escapeSqlString($_POST['id']);
        $query = "SELECT id, loc_sector_id, loc_x, loc_y, loc_z, radius, visible_radius, probability, skill, skill_level, item_cat_id, item_quality, animation, anim_duration_seconds, item_id_reward, reward_nickname, action, amount, `interval`, max_random FROM natural_resources WHERE id='$id'";
        $result = mysql_query2($query);
        $Sectors = PrepSelect('sectorid');
        $Category = PrepSelect('category');
        $Skills = PrepSelect('skill');
        $row = fetchSqlAssoc($result);
        echo '<form action="./index.php?do=resource" method="post">';
        echo '<table border="1">';
        echo '<tr><td>Sector:</td><td>'.DrawSelectBox('sectorid', $Sectors, 'loc_sector_id', $row['loc_sector_id']).'</td>';
        echo '<td>Coordinates (X/Y/Z):</td><td><input type="text" name="loc_x" value="'.$row['loc_x'].'" size="5" />/<input type="text" name="loc_y" value="'.$row['loc_y'].'" size="5" />/<input type="text" name="loc_z" value="'.$row['loc_z'].'" size="5" /></td></tr>';
        echo '<tr><td>Radius:</td><td><input type="text" name="radius" value="'.$row['radius'].'" size="10" /></td>';
        echo '<td>Visible Radius:</td><td><input type="text" name="visible_radius" value="'.$row['visible_radius'].'" size="10" /></td></tr>';
        echo '<tr><td>Probability:</td><td><input type="text" name="probability" value="'.$row['probability'].'" size="10" /></td>';
        echo '<td>Tool Category</td><td>'.DrawSelectBox('category', $Category, 'item_cat_id', $row['item_cat_id']).'</td></tr>';
        echo '<tr><td>Skill:</td><td>'.DrawSelectBox('skill', $Skills, 'skill', $row['skill']).'</td>';
        echo '<td>Skill Level:</td><td><input type="text" name="skill_level" value="'.$row['skill_level'].'" size="10" /></td></tr>';
        echo '<tr><td>Reward Item:</td><td>'.DrawItemSelectBox('item_id_reward', $row['item_id_reward'], true, true).'</td>';
        echo '<td>Item Quality:</td><td><input type="text" name="item_quality" value="'.$row['item_quality'].'" size="10" /></td></tr>';
        echo '<tr><td>Animation:</td><td><input type="text" name="animation" value="'.$row['animation'].'" /></td>';
        echo '<td>Animation Duration:</td><td><input type="text" name="anim_duration_seconds" value="'.$row['anim_duration_seconds'].'" size="5" /></td></tr>';
        echo '<tr><td>Action:</td><td><input type="text" name="action" value="'.$row['action'].'" /></td>';
        echo '<td>Reward Nickname:<br/>(Used by players after /dig)</td><td><input type="text" name="reward_nickname" value="'.$row['reward_nickname'].'" /></td></tr>';
		echo '<tr><td>Amount:<br/>(if not null it spawns items)</td><td><input type="text" name="amount" value="'.$row['amount'].'" /></td></tr>';
		echo '<tr><td>Interval:<br/>(if amount not null, <br/>msec interval for spawning item when picked up</td><td><input type="text" name="interval" value="'.$row['interval'].'" /></td></tr>';
		echo '<tr><td>Max Random:<br/>(if amount not null, <br/>maximum random interval modifier in msecs</td><td><input type="text" name="max_random" value="'.$row['max_random'].'" /></td></tr>';
        echo '</table><div><input type="hidden" name="id" value="'.$row['id'].'" /><input type="submit" name="commit" value="Commit Edit" /></div>';
        echo '</form>';
      }else if ($_POST['action'] == 'Create New' && checkaccess('natres', 'create')){
        $Sectors = PrepSelect('sectorid');
        $Category = PrepSelect('category');
        $Skills = PrepSelect('skill');
        echo '<form action="./index.php?do=resource" method="post">';
        echo '<table border="1">';
        echo '<tr><td>Sector:</td><td>'.DrawSelectBox('sectorid', $Sectors, 'loc_sector_id', '').'</td>';
        echo '<td>Coordinates (X/Y/Z):</td><td><input type="text" name="loc_x" size="5" />/<input type="text" name="loc_y" size="5" />/<input type="text" name="loc_z" size="5" /></td></tr>';
        echo '<tr><td>Radius:</td><td><input type="text" name="radius" size="10" /></td>';
        echo '<td>Visible Radius:</td><td><input type="text" name="visible_radius" size="10" /></td></tr>';
        echo '<tr><td>Probability:</td><td><input type="text" name="probability" size="10" /></td>';
        echo '<td>Tool Category</td><td>'.DrawSelectBox('category', $Category, 'item_cat_id', '').'</td></tr>';
        echo '<tr><td>Skill:</td><td>'.DrawSelectBox('skill', $Skills, 'skill', '').'</td>';
        echo '<td>Skill Level:</td><td><input type="text" name="skill_level" size="10" /></td></tr>';
        echo '<tr><td>Reward Item:</td><td>'.DrawItemSelectBox('item_id_reward', '', true, true).'</td>';
        echo '<td>Item Quality:</td><td><input type="text" name="item_quality" size="10" /></td></tr>';
        echo '<tr><td>Animation:</td><td><input type="text" name="animation"  /></td>';
        echo '<td>Animation Duration:</td><td><input type="text" name="anim_duration_seconds" size="5" /></td></tr>';
        echo '<tr><td>Action:</td><td><input type="text" name="action" /></td>';
        echo '<td>Reward Nickname:<br/>(Name used after action)</td><td><input type="text" name="reward_nickname" /></td></tr>';
        echo '</table><div><input type="submit" name="commit" value="Commit New" /></div>';
        echo '</form>';
      }else if ($_POST['action'] == 'Delete' && checkaccess('natres', 'delete')){
        $id = escapeSqlString($_POST['id']);
        $query = "SELECT r.reward_nickname, s.name FROM natural_resources AS r LEFT JOIN sectors AS s ON r.loc_sector_id=s.id WHERE r.id='$id'";
        $result = mysql_query2($query);
        $row = fetchSqlAssoc($result);
        echo '<form action="./index.php?do=resource" method="post">';
        echo '<p>Please Confirm that you wish to delete the '.$row['reward_nickname'].' resource in sector '.$row['name'].'</p>';
        echo '<input type="hidden" name="id" value="'.$id.'"/><input type="submit" name="commit" value="Confirm Delete"/>';
        echo '</form>';
      }else{
        echo '<p class="error">Unknown Action - Returning to List</p>';
      }
    }else{
      $query = "SELECT r.id, r.loc_sector_id, s.name AS sector, r.loc_x, r.loc_y, r.loc_z, r.radius, r.visible_radius, r.probability, r.skill, sk.name AS skill_name, r.skill_level, r.item_cat_id, c.name AS category, r.item_quality, r.animation, r.anim_duration_seconds, r.item_id_reward, i.name AS item, r.reward_nickname, r.action, r.amount, r.interval, r.max_random FROM natural_resources AS r LEFT JOIN sectors AS s ON r.loc_sector_id=s.id LEFT JOIN item_stats AS i on i.id=r.item_id_reward LEFT JOIN item_categories AS c ON r.item_cat_id=c.category_id LEFT JOIN skills AS sk on sk.skill_id=r.skill";
      if (isset($_GET['id']))
      {
        $id = escapeSqlString($_GET['id']);
        $query .= " WHERE r.id='$id'";
      }
      if (isset($_GET['sort'])){
        if ($_GET['sort'] == 'loc'){
          $query = $query . ' ORDER BY sector, loc_x, loc_y, loc_z, item';
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
      echo '<table border="1"><tr><th>ID</th><th><a href="./index.php?do=resource&amp;sort=loc">Location</a></th><th>Radius</th><th>Visible Radius</th><th>Probability</th><th><a href="./index.php?do=resource&amp;sort=skill">Skill</a></th><th>Skill Level</th><th><a href="./index.php?do=resource&amp;sort=tool">Tool Category</a></th><th>Item Quality</th><th>Animation</th><th>Animation Duration</th><th><a href="./index.php?do=resource&amp;sort=item">Item</a></th><th>Resource "Nickname"</th><th>Amount (1)</th><th>Interval (2)</th><th>Max Random (3)</th>';
      if (checkaccess('natres', 'edit')){
        echo '<th>Actions</th>';
      }
      echo '</tr>';
      while ($row = fetchSqlAssoc($result)){
        echo '<tr>';
		echo '<td>'.$row['id'].'</td>';
        echo '<td>'.$row['sector'].'/'.$row['loc_x'].'/'.$row['loc_y'].'/'.$row['loc_z'].'</td>';
        echo '<td>'.$row['radius'].'</td>';
        echo '<td>'.$row['visible_radius'].'</td>';
        echo '<td>'.$row['probability'].'</td>';
        echo '<td>'.$row['skill_name'].'</td>';
        echo '<td>'.$row['skill_level'].'</td>';
        echo '<td>'.$row['category'].'</td>';
        echo '<td>'.$row['item_quality'].'</td>';
        echo '<td>'.$row['animation'].'</td>';
        echo '<td>'.$row['anim_duration_seconds'].'</td>';
        echo '<td>'.$row['item'].'</td>';
        echo '<td>'.$row['reward_nickname'].'</td>';
        echo '<td>'.$row['amount'].'</td>';
        echo '<td>'.$row['interval'].'</td>';
        echo '<td>'.$row['max_random'].'</td>';
        if (checkaccess('natres', 'edit')){
          echo '<td><form action="./index.php?do=resource" method="post">';
          echo '<div><input type="hidden" name="id" value="'.$row['id'].'" />';
          echo '<input type="submit" name="action" value="Edit" />';
          if (checkaccess('natres', 'delete')){
            echo '<br/><input type="submit" name="action" value="Delete" />';
          }
          echo '</div></form></td>';
        }
        echo '</tr>';
      }
      echo '</table>(1) if not null, indicates how many items will be spawned (same as hunt_location)<br/>(2) if amount not null, msec interval for spawning item when picked up<br/>(3) if amount not null, maximum random interval modifier in msecs';
      if (checkaccess('natres', 'create')){
        echo '<form action="./index.php?do=resource" method="post">';
        echo '<div><input type="submit" name="action" value="Create New" /></div></form>';
      }
    }
  }else{
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}
?>
