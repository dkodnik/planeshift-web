<?php
function listitems(){
  if(checkaccess('items', 'read')){
    $query = 'SELECT category_id, name FROM item_categories ORDER BY name';
    $result = mysql_query2($query);
    echo '<p><a href="./index.php?do=listitems';
    if (isset($_GET['category'])){
      echo '&amp;category='.$_GET['category'];
    }
    if (isset($_GET['item'])){
      echo '&amp;item='.$_GET['item'];
    }
    if (isset($_GET['override1'])){
      echo '&amp;override1';
    }
    if (!isset($_GET['override2'])){
      echo '&amp;override2';
    }
    echo '">Toggle Personalized Items ';
    if (isset($_GET['override2'])){
      echo 'OFF';
    }else{
      echo 'ON';
    }
    echo '</a></p>';
    echo '<table border="1" class="top">';
    echo '<tr><th>Category</th><th>Items</th><th>Details</th></tr>';
    echo '<tr class="top"><td>';
    while ($row=mysql_fetch_array($result, MYSQL_ASSOC)){
      if (isset($_GET['category']) && ($_GET['category']==$row['category_id']))
        echo '<b>';
      echo '<a href="./index.php?do=listitems&amp;category='.$row['category_id'];
      if (isset($_GET['override2'])){
        echo '&amp;override2';
      }
      echo '">'.$row['name'].'</a>';
      if (isset($_GET['category']) && ($_GET['category']==$row['category_id']))
        echo '</b>';
      echo '<br/>';
    }
    echo '</td><td>';
    if (isset($_GET['category'])){
      if (isset($_GET['item']) && !isset($_GET['override1'])){
        echo 'List suppressed<br/><a href="./index.php?do=listitems&amp;override1';
        if (isset($_GET['override2'])){
          echo '&amp;override2';
        }
        echo '&amp;category='.$_GET['category'].'&amp;item='.$_GET['item'].'">Override</a>';
      }else{
        $category = mysql_real_escape_string($_GET['category']);
        $query = 'SELECT id, name FROM item_stats WHERE category_id ='.$category;
        if (!isset($_GET['override2'])){
          $query = $query." AND stat_type = 'B'";
        }
        $query .= ' ORDER BY name';
        $result = mysql_query2($query);
        while ($row = mysql_fetch_array($result)){
          echo '<a href="./index.php?do=listitems&amp;category='.$_GET['category'].'&amp;item='.$row['id'];
          if (isset($_GET['override2'])){
            echo '&amp;override2';
          }
          echo '">'.$row['name'].'</a><br/>';
        }
      }
    }
    echo '</td><td>';
    if (isset($_GET['item'])){
      $id = mysql_real_escape_string($_GET['item']);
      $query = 'SELECT i.id, i.stat_type, i.name, i.weight, i.visible_distance, i.size, i.container_max_size, i.container_max_slots, i.valid_slots, i.flags, i.decay_rate, s1.name AS item_skill_id, s2.name AS item_skill_id_2, s3.name AS item_skill_id_3, i.item_bonus_1_attr, i.item_bonus_2_attr, i.item_bonus_3_attr, i.item_bonus_1_max, i.item_bonus_2_max, i.item_bonus_3_max, i.dmg_slash, i.dmg_blunt, i.dmg_pierce, i.weapon_speed, i.weapon_penetration, i.weapon_block_targeted, i.weapon_block_untargeted, i.weapon_counterblock, i.armor_hardness, i.cstr_gfx_mesh, i.cstr_gfx_icon, i.cstr_gfx_texture, i.cstr_part, i.cstr_part_mesh, i.removed_mesh, i.armorvsweapon_type, i.category_id, i.base_sale_price, i.item_type, i.requirement_1_name, i.requirement_1_value, i.requirement_2_name, i.requirement_2_value, i.requirement_3_name, i.requirement_3_value, i.item_type_id_ammo, i.spell_id_on_hit, i.spell_on_hit_prob, i.spell_id_feature, i.spell_feature_charges, i.spell_feature_timing, i.item_anim_id, i.description, i.sound, i.item_max_quality, i.equip_script, i.consume_script, i.creative_definition, i.max_charges, i.weapon_range, i.assigned_command, i.spawnable FROM item_stats i LEFT JOIN skills AS s1 ON i.item_skill_id_1=s1.skill_id LEFT JOIN skills AS s2 ON i.item_skill_id_2=s2.skill_id LEFT JOIN skills AS s3 ON i.item_skill_id_3=s3.skill_id WHERE id='.$id;
      $result = mysql_query2($query);
      $row = mysql_fetch_array($result, MYSQL_ASSOC);
      echo '<table border="1" class="top">';
      if (checkaccess('items','edit')){
        echo '<tr><td>Item Actions:</td><td><a href="./index.php?do=edititem&amp;item='.$_GET['item'].'">Edit Item</a>';
        echo ' -- <a href="./index.php?do=showitemusage&amp;id='.$id.'">Check Usage</a>';
      }
      if (checkaccess('items','delete')){
        echo ' -- <a href="./index.php?do=deleteitem&amp;item='.$_GET['item'].'">Delete Item</a>';
      }
      echo '</td></tr>';
      foreach ($row as $key=>$value){
        echo '<tr><td>'.$key.'</td><td>'.htmlspecialchars($value).'</td></tr>';
      }
      echo '</table>';
    }
    echo '</td></tr>';
    echo '</table>';
  }else{
    echo '<p class="error">You are not authorized to use these functions!</p>';
  }
}

/*
This method prints all places where this item is used. It returns false if the item is not used, and true if it was used.
*/
function showitemusage()
{   
    if(!checkaccess('items', 'read'))
    {
        echo '<p class="error">You are not authorized to use these functions!</p>';
        return;
    }
    $id = '';
    if (isset($_GET['id']))
    {
        $id = mysql_real_escape_string($_GET['id']);
    }
    elseif (isset($_GET['item'])) // some scripts call it this, should be refactored someday.
    {
        $id = mysql_real_escape_string($_GET['item']);
    }
    else
    {
        echo '<p class="error">No valid ID was set.</p>';
        return;

    }
    $query = "SELECT name FROM item_stats WHERE id=$id";
    $result = mysql_query2($query);
    $row = mysql_fetch_array($result);
    $my_item_name = $row['name'];
    $item_is_used = false;
    
    echo '<p>This is the usage report for item #'.$id.' named: '.$my_item_name.'</p>';
    
    // pattern
    $query = "SELECT t.id, t.pattern_name, t.description, i.name FROM trade_patterns AS t LEFT JOIN item_stats AS i ON t.designitem_id=i.id WHERE t.designitem_id='$id'";
    $result = mysql_query2($query);
    if (mysql_num_rows($result) > 0)
    {
        $item_is_used = true;
        if (checkaccess('crafting', 'read'))
        {
            echo '<p class="header">Crafting Patterns using this item:</p>';
            $alt = FALSE;
            echo '<table><tr><th>ID</th><th>Pattern Name</th><th>Description</th><th>Design Item</th>';
            echo '<th>Actions</th>';
            echo '</tr>'; 
            while ($row = mysql_fetch_array($result)){
                $alt = !$alt;
                if ($alt)
                {
                    echo '<tr class="color_a">';
                }
                else
                {
                    echo '<tr class="color_b">';
                }
              echo '<td>'.$row['id'].'</td>';
              echo '<td>'.$row['pattern_name'].'</td>';
              echo '<td>'.$row['description'].'</td>';
              echo '<td>'.$row['name'].'</td>';
              echo '<td><a href="./index.php?do=editpattern&amp;id='.$row['id'].'">Details</a></td>';
              echo '</tr>';
            }
            echo '</table>';
        }
        else 
        {
            echo '<p>You do not have permission to view Crafting patterns, but they do use this item.</p>';
        }     
    }
    else 
    {
        echo '<p>No Crafting Patterns are using this item.</p>';
    }
    
    // transforms
    $query = "SELECT pat.pattern_name, t.pattern_id, t.id, t.process_id, p.name, t.result_id, i.name AS result_name, c.name AS result_cat, c.category_id AS result_cat_id, t.result_qty, t.item_id, ii.name AS item_name, cc.name AS item_cat, cc.category_id AS item_cat_id, t.item_qty, t.trans_points, t.penalty_pct, t.description FROM trade_transformations AS t LEFT JOIN item_stats AS i ON i.id=t.result_id LEFT JOIN item_stats AS ii ON ii.id=t.item_id LEFT JOIN trade_processes AS p ON t.process_id=p.process_id LEFT JOIN item_categories AS c ON i.category_id=c.category_id LEFT JOIN item_categories AS cc ON ii.category_id=cc.category_id LEFT JOIN trade_patterns AS pat ON pat.id=t.pattern_id WHERE t.result_id='$id' OR t.item_id='$id' GROUP BY id ORDER BY p.name, i.name";
    $result = mysql_query2($query);
    if (mysql_num_rows($result) > 0)
    {
        $item_is_used = true;
        if (checkaccess('crafting', 'read'))
        {
            echo '<p class="bold">Transforms using this item:</p>';
            echo '<table><tr><th>Pattern</th><th colspan="2">Source Item</th><th>Category</th><th>Process</th><th colspan="2">Result Item</th><th>Category</th><th>Time</th><th>Result Q</th><th>Actions</th></tr>';
            $alt = FALSE;
            while ($row=mysql_fetch_array($result))
            {
                $alt = !$alt;
                if ($alt)
                {
                    echo '<tr class="color_a">';
                }
                else
                {
                    echo '<tr class="color_b">';
                }
                $pattern_name = ($row['pattern_id'] != 0 ? $row['pattern_name'] : "patternless");
                echo '<td><a href="./index.php?do=editpattern&amp;id='.$row['pattern_id'].'">'.$pattern_name.'</a></td>';
                $item_name = ($row['item_name'] == "NULL" ? ($row['item_id'] != 0 ? "BROKEN" : "") :$row['item_name']); // Item name is broken if NULL was returned and ID is not 0, if ID was 0, name is "", else name the name found in the database.
                if (checkaccess('items','edit'))
                {
                    echo '<td>'.$row['item_qty'].' </td><td> <a href="./index.php?do=listitems&amp;override1&amp;category='.$row['item_cat_id'].'&amp;item='.$row['item_id'].'">'.$item_name.'</a> </td>';
                }
                else
                {
                    echo '<td>'.$row['item_qty'].' </td><td> '.$item_name.' </td>';
                }
                echo '<td>'.$row['item_cat'].'</td>';
                echo '<td><a href="./index.php?do=process&amp;id='.$row['process_id'].'">'.$row['name'].'</a></td>';
                $result_name = ($row['result_name'] == "NULL" ? ($row['result_id'] != 0 ? "BROKEN" : "") :$row['result_name']); // Item name is broken if NULL was returned and ID is not 0, if ID was 0, name is "", else name the name found in the database.
                if (checkaccess('items','edit'))
                {
                    echo '<td>'.$row['result_qty'].' </td><td> <a href="./index.php?do=listitems&amp;override1&amp;category='.$row['result_cat_id'].'&amp;item='.$row['result_id'].'">'.$result_name.'</a> </td>';
                }
                else
                {
                    echo '<td>'.$row['result_qty'].' </td><td> '.$result_name.'</td>';
                }
                echo '<td>'.$row['result_cat'].'</td>';
                echo '<td>'.$row['trans_points'].'</td>';
                echo '<td>'.$row['penalty_pct'].'</td>';
                echo '<td><a href="./index.php?do=transform&amp;id='.$row['id'].'">Edit</a></td>';
                echo '</tr>';
            }
            echo '</table>';
        }
        else 
        {
            echo '<p>You do not have permission to view Transforms, but they do use this item.</p>';
        }     
    }
    else 
    {
        echo '<p>No Transforms are using this item.</p>';
    }
    
    // Combinations
    $query = "SELECT DISTINCT pat.pattern_name, t.result_id, t.pattern_id, c.name AS result_cat, c.category_id AS result_cat_id, i.name AS result_name, t.result_qty, t.item_id, ii.name AS item_name, cc.name AS item_cat, cc.category_id AS item_cat_id, t.min_qty, t.max_qty, t.description FROM trade_combinations AS t LEFT JOIN item_stats AS i ON i.id=t.result_id LEFT JOIN item_stats AS ii ON ii.id=t.item_id LEFT JOIN item_categories AS c ON i.category_id=c.category_id LEFT JOIN item_categories AS cc ON ii.category_id=cc.category_id LEFT JOIN trade_patterns AS pat ON pat.id=t.pattern_id WHERE result_id='$id' OR item_id='$id' ORDER BY t.pattern_id, result_name";
    $result = mysql_query2($query);
    if (mysql_num_rows($result) > 0)
    {
        $item_is_used = true;
        if (checkaccess('crafting', 'read'))
        {
            echo '<p class="bold">Combinations using this item:</p>';
            $alt = false;
            $item = -1;
            echo '<table><tr><th>Pattern</th><th colspan="2">Result Item</th><th>Category</th><th>Source Items</th><th>Actions</th></tr>';
            while ($row = mysql_fetch_array($result))
            {
                if ($item != $row['result_id'])
                {
                    if ($item != '-1')
                    {
                        echo '</td><td><a href="./index.php?do=editcombine&amp;id='.$item.'&amp;pattern_id='.$row['pattern_id'].'">Edit</a></td></tr>'."\n";
                    }
                    $item = $row['result_id'];
                    $alt = !$alt;
                    if ($alt)
                    {
                      echo '<tr class="color_a">';
                    }else{
                      echo '<tr class="color_b">';
                    }
                    $result_id = $row['result_id'];
                    $result_name = ($row['result_name'] == '' ? ($row['result_id'] != 0 ? "BROKEN" : "") :$row['result_name']); // Item name is broken if NULL was returned and ID is not 0, if ID was 0, name is "", else name the name found in the database.
                    $pattern_name = ($row['pattern_id'] != 0 ? $row['pattern_name'] : "patternless");
                    echo '<td><a href="./index.php?do=editpattern&amp;id='.$row['pattern_id'].'">'.$pattern_name.'</a></td>';
                    if (checkaccess('items','edit'))
                    {
                        echo '<td>'.$row['result_qty'].' </td><td> <a href="./index.php?do=listitems&amp;override1&amp;category='.$row['result_cat_id'].'&amp;item='.$row['result_id'].'">'.$result_name.'</a> </td>';
                    }
                    else
                    {
                        echo '<td>'.$row['result_qty'].' </td><td> '.$result_name.'</td>';
                    }
                    echo '<td>'.$row['result_cat'].'</td>';
                    $item_name = ($row['item_name'] == "NULL" ? ($row['item_id'] != 0 ? "BROKEN" : "") :$row['item_name']); // Item name is broken if NULL was returned and ID is not 0, if ID was 0, name is "", else name the name found in the database.
                    if (checkaccess('items','edit'))
                    {
                        //echo '<td>'.$row['min_qty'].' to '.$row['max_qty'].' <a href="./index.php?do=listitems&amp;override1&amp;category='.$row['item_cat_id'].'&amp;item='.$row['item_id'].'">'.$item_name.'</a> ('.$row['item_cat'].')';
                    }
                    else
                    {
                        //echo '<td>'.$row['min_qty'].' to '.$row['max_qty'].' '.$item_name.' ('.$row['item_cat'].')';
                    }
                }
                else
                {
                    echo '<br/>';
                    $item_name = ($row['item_name'] == "NULL" ? ($row['item_id'] != 0 ? "BROKEN" : "") :$row['item_name']); // Item name is broken if NULL was returned and ID is not 0, if ID was 0, name is "", else name the name found in the database.
                    if (checkaccess('items','edit'))
                    {
                        //echo $row['min_qty'].' to '.$row['max_qty'].' <a href="./index.php?do=listitems&amp;override1&amp;category='.$row['item_cat_id'].'&amp;item='.$row['item_id'].'">'.$item_name.'</a> ('.$row['item_cat'].')';
                    }
                    else
                    {
                       // echo $row['min_qty'].' to '.$row['max_qty'].' '.$item_name.' ('.$row['item_cat'].')';
                    }
                }
            }
            echo '<td><a href="./index.php?do=editcombine&amp;id='.$item.'&amp;pattern_id='.$row['pattern_id'].'">Edit</a></td></tr></table>';
        }
        else 
        {
            echo '<p>You do not have permission to view Combinations, but they do use this item.</p>';
        }     
    }
    else 
    {
        echo '<p>No Combinations are using this item.</p>';
    }

    // Processes
    $result = mysql_query2("SELECT id, name FROM item_stats");
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
        $i = $row['id'];
        $items["$i"] = $row['name'];
    }
    $items[0] = "";
    $result = mysql_query2("SELECT skill_id, name FROM skills");
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
        $i = $row['skill_id'];
        $skills["$i"] = $row['name'];
    }
    $skills[0] = "";
    $query = "SELECT DISTINCT t.process_id, t.subprocess_number, t.name, t.animation, t.render_effect, t.workitem_id, t.equipment_id, t.constraints, t.garbage_id, t.garbage_qty, t.primary_skill_id, t.primary_min_skill, t.primary_max_skill, t.primary_practice_points, t.primary_quality_factor, t.secondary_skill_id, t.secondary_min_skill, t.secondary_max_skill, t.secondary_practice_points, t.secondary_quality_factor, t.description FROM trade_processes as t LEFT JOIN skills AS s ON t.primary_skill_id=s.skill_id LEFT JOIN skills AS ss ON t.secondary_skill_id=ss.skill_id WHERE t.workitem_id='$id' OR t.equipment_id='$id' OR t.garbage_id='$id' ORDER BY s.name, t.primary_min_skill, ss.name, t.secondary_min_skill, t.name";
    $result = mysql_query2($query);
    if (mysql_num_rows($result) > 0)
    {
        $item_is_used = true;
        if (checkaccess('crafting', 'read'))
        {
            echo '<p class="bold">Processes using this item:</p>';
            echo '<table><tr><th>Name</th><th>Sub-<br>Process</th><th>Animation</th><th>Work Item</th><th>Equipment Used</th><th>Constraints</th><th colspan="2">Garbage Item</th><th>Primary Skill / Min / Max / Practice / Quality</th><th>Secondary Skill / Min / Max / Practice / Quality</th><th>Description</th>';
            if (checkaccess('crafting', 'edit')){
                echo '<th>Actions</th>';
            }
            echo '</tr>';
            $alt= FALSE;
            while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
                $alt = !$alt;
                if ($alt)
                {
                    echo '<tr class="color_a">';
                }
                else
                {
                    echo '<tr class="color_b">';
                }
                echo '<td><a href="./index.php?do=process&amp;id='.$row['process_id'].'">'.$row['name'].'</a></td>';
                echo '<td>'.$row['subprocess_number'].'</td>';
                echo '<td>'.$row['animation'].'</td>';
                $i = $row['workitem_id'];
                echo '<td>'.$items["$i"].'</td>';
                $i = $row['equipment_id'];
                echo '<td>'.$items["$i"].'</td>';
                echo '<td>'.$row['constraints'].'</td>';
                $i = $row['garbage_id'];
                echo '<td>'.$row['garbage_qty'].' </td><td> '.$items["$i"].'</td>';
                $i = $row['primary_skill_id'];
                echo '<td>'.$skills["$i"].' / '.$row['primary_min_skill'].' / '.$row['primary_max_skill'].' / '.$row['primary_practice_points'].' / '.$row['primary_quality_factor'].'</td>';
                $i = $row['secondary_skill_id'];
                echo '<td>'.$skills["$i"].' / '.$row['secondary_min_skill'].' / '.$row['secondary_max_skill'].' / '.$row['secondary_practice_points'].' / '.$row['secondary_quality_factor'].'</td>';
                echo '<td>'.$row['description'].'</td>';
                if (checkaccess('crafting','edit'))
                {
                    echo '<td><a href="./index.php?do=editsubprocess&amp;id='.$row['process_id'].'&amp;sub='.$row['subprocess_number'].'">Edit</a></td>';
                }
                echo '</tr>';
            }
            echo '</table>';

        }
        else 
        {
            echo '<p>You do not have permission to view Processes, but they do use this item.</p>';
        }     
    }
    else 
    {
        echo '<p>No Processes are using this item.</p>';
    }

    // Spells
    $query = "SELECT DISTINCT name, realm, spell_description, id FROM spells LEFT JOIN spell_glyphs ON spell_id=id WHERE item_id='$id' ORDER BY realm, name";
    $result = mysql_query2($query);
    if (mysql_num_rows($result) > 0)
    {
        $item_is_used = true;
        if (checkaccess('rules', 'read'))
        {
            echo '<p class="bold">Spells using this item:</p>';
            echo '<table border="1"><tr><th>Name</th><th>Realm</th><th>Description</th>';
            echo '</tr>';
            while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
                echo '<tr><td>';
                echo '<a href="./index.php?do=spell&amp;id='.$row['id'].'">'.$row['name'].'</a>';
                echo '</td><td>';
                echo $row['realm'];
                echo '</td><td>';
                echo $row['spell_description'];
                echo '</td>';
                echo '</tr>';
            }
            echo '</table>';
        }
        else 
        {
            echo '<p>You do not have permission to view Spells, but they do use this item.</p>';
        }     
    }
    else 
    {
        echo '<p>No Spells are using this item.</p>';
    }

    // Resources
    
    $query = "SELECT r.id, r.loc_sector_id, s.name AS sector, r.loc_x, r.loc_y, r.loc_z, r.radius, r.visible_radius, r.probability, r.skill, sk.name AS skill_name, r.skill_level, r.item_cat_id, c.name AS category, r.item_quality, r.animation, r.anim_duration_seconds, r.item_id_reward, i.name AS item, r.reward_nickname, r.action FROM natural_resources AS r LEFT JOIN sectors AS s ON r.loc_sector_id=s.id LEFT JOIN item_stats AS i on i.id=r.item_id_reward LEFT JOIN item_categories AS c ON r.item_cat_id=c.category_id LEFT JOIN skills AS sk on sk.skill_id=r.skill WHERE i.id='$id' ORDER BY sector";
    $result = mysql_query2($query);
    if (mysql_num_rows($result) > 0)
    {
        $item_is_used = true;
        if (checkaccess('rules', 'read'))
        {
            echo '<p class="bold">Resources using this item:</p>';
            echo '<table border="1"><tr><th>Location</th><th>Radius</th><th>Visible Radius</th><th>Probability</th><th>Skill</th><th>Skill Level</th><th>Tool Category</th><th>Item Quality</th><th>Animation</th><th>Animation Duration</th><th>Item</th><th>Resource "Nickname"</th>';
            if (checkaccess('rules', 'edit')){
                echo '<th>Actions</th>';
            }
            echo '</tr>';
            while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
            {
                echo '<tr>';
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
                if (checkaccess('rules', 'edit'))
                {
                  echo '<td><form action="./index.php?do=resource" method="post">';
                  echo '<input type="hidden" name="id" value="'.$row['id'].'" />';
                  echo '<input type="submit" name="action" value="Edit" />';
                  echo '</form>';
                }
                echo '</tr>';
            }
            echo '</table>';
        }
        else 
        {
            echo '<p>You do not have permission to view Resources, but they do use this item.</p>';
        }     
    }
    else 
    {
        echo '<p>No Resources are using this item.</p>';
    }
    
    // Item instances
    $query = "SELECT COUNT(id) FROM item_instances WHERE item_stats_id_standard=$id";
    $result = mysql_query2($query);
    $row = mysql_fetch_row($result);
    if (($num = $row[0]) > 0)
    {
        $item_is_used = true;
        $button = (checkaccess('items', 'read') ? '<br>Please click the button below if you wish to see a full overview of all instances of this item. <form action="./index.php?do=finditem" method="post"><input type="hidden" name="itemid" value="'.$id.'"><input type="hidden" name="vendoritemid" value="'.$id.'"><input type="submit" name="search" value="Find Items"/> <input type="submit" name="search" value="Find Merchants"/> </form>' : '');
        echo '<p> There are '.$num.' instances of this item in the database. '.$button.'</p>';
    }
    else 
    {
        echo '<p>There are no instances of this item.</p>';
    }
   
    // Loot tables
    $query = "SELECT DISTINCT lr.name, lrd.loot_rule_id FROM loot_rule_details AS lrd LEFT JOIN loot_rules AS lr ON lr.id=lrd.loot_rule_id WHERE lrd.item_stat_id=$id";
    $result = mysql_query2($query);
    if (mysql_num_rows($result) > 0) {
        $item_is_used = true;
        if (checkaccess('npcs', 'read')) {
            echo '<p>The following loot rules use this item: </p>';
            echo '<table border="1">';
            while($row = mysql_fetch_array($result))
            {
                echo '<tr><td><a href="./index.php?do=listloot&amp;id='.$row['loot_rule_id'].'">'.$row['name'].'</a></tr></td>';
            }
            echo '</table>';
        }
        else
        {
            echo '<p>You do not have permission to view Loot Rules, but they do use this item.</p>';
        }
    }
    else 
    {
        echo '<p>This item is not in any loot rule.</p>';
    }
    
    // quests
    // REGEXP queries match case-insensitive. To do this on a "blob" field, we first need to convert the data to a charset. (SQL supports REGEXP on binary data, but it'll become case sensitive, so we don't want that.)
    // in a regexp, you can make a character group (in our case \n (with an additional \ to escape it in the PHP string)) by placing something between [].
    // so we get '[\n]text[^\n]* item' In this case our second character group is 'NOT newline' where \n is the newline, and ^ means not. Finally, the * after the group means zero or more of this character.
    // In other words, we are looking for a character sequence that contains a newline, followed by "Player Gives" (so it must be at the start of the line) and "item_name" with any amount of characters between them 
    // that are not newlines. This effectively means they have to be on the same line, in the form of "give **** <item> ****" where *** can be anything or nothing at all.
    $escaped_item_name = mysql_real_escape_string($my_item_name);
    $query = "SELECT q.id, q.name, q.category FROM quests AS q LEFT JOIN quest_scripts AS qs ON q.id=qs.quest_id WHERE CONVERT(qs.script USING latin1) REGEXP '[\\n](Player Gives|Give|Require Equipped|Require not Equipped|Require no Equipped|Require Possessed|Require not Possessed|Require no Possessed)[^\\n]*$escaped_item_name' ORDER BY q.name ASC";
    $result = mysql_query2($query);
    if (mysql_num_rows($result) > 0)
    {
        if (checkaccess('quests', 'read'))
        {
            echo '<p class="bold">Quests using this item:</p>';
            echo '<p>Please notice that if the item name is contained in another item name, it may report that item too. (If you search for an item named 
                  "ring", it will also match "golden ring". Additionally, this script can match any "Give/Player Gives/Require (not) Equipped/Require (not) 
                  Possessed Item" block anywhere in the text (like "P: Give Golden Ring". Use this quest data as a pointer, not as an absolute truth. </p>';
            echo '<table border="1">'."\n";
            echo '<tr><th>ID</th><th>Category</th><th>Name</th><th>Actions</th></tr>';
            while ($row = mysql_fetch_array($result))
            {
                echo '<tr><td>'.$row['id'].'</td><td>'.$row['category'].'</td><td>'.$row['name'].'</td>';
                echo '<td><a href="./index.php?do=readquest&amp;id='.$row['id'].'">Read</a>';
                if (checkaccess('quests', 'edit'))
                {
                    echo ' || <a href="./index.php?do=editquest&amp;id='.$row['id'].'">Edit</a>';
                }
                echo '</td></tr>';
            }
            echo '</table>'."\n";
        }
        else 
        {
            echo '<p class="error">You do not have permission to view Quests, but they do use this item.</p>';
        }     
    }
    else 
    {
        echo '<p>No Quests are using this item.</p>';
    } 
   
    return $item_is_used;
}
?>
