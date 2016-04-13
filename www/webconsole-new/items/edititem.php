<?php
function edititem(){
  if (checkaccess('items','edit')){
    if (!isset($_GET['commit']) && !isset($_POST['name'])){
      if (isset($_GET['item'])){
        $id = escapeSqlString($_GET['item']);
        $query = 'SELECT * FROM item_stats WHERE id='.$id;
        $result = mysql_query2($query);
        $row = fetchSqlAssoc($result);
        echo '<p>You are editing Item '.$row['id'].'</p>';
        echo '<form action="./index.php?do=edititem&amp;item='.$_GET['item'].'&amp;commit" method="post">';
        echo '<table border="1">';
        echo '<tr><td>stat_type</td><td>';
        echo '<select name="stat_type">';
        $alpha = "BUR";
        for ($i="0";$i<strlen($alpha);$i++){
          $j = substr($alpha,$i,1);
          echo '<option value="'.$j.'"';
          if ($j==$row['stat_type']){
            echo ' selected="selected"';
          }
          echo '>'.$j.'</option>'."\n";
        }
        echo '</select></td></tr>';
        echo '<tr><td>Name</td><td><input type="text" size="50" name="name" value="'.$row['name'].'" /></td></tr>';
        if (checkaccess('items','delete'))  // This does not actually delete anything, but 'delete' is the highest level, so this effectively says that only someone who may do *everything* with items can set this.
        {
            echo '<tr><td>New ID</td><td><input type="text" size="4" name="new_id"/> <span class="error">Only do this if you really know what you are doing.</span></td></tr>';
        }
        echo '<tr><td>Description</td><td><input type="text" size="50" name="description" value="'.$row['description'].'"></td></tr>';
        echo '<tr><td>Weight</td><td><input type="text" name="weight" value="'.$row['weight'].'" /></td></tr>';
        echo '<tr><td>Visible Distance</td><td><input type="text" name="visible_distance" value="'.$row['visible_distance'].'" /></td></tr>';
        echo '<tr><td>Size</td><td><input type="text" name="size" value="'.$row['size'].'" /></td></tr>';
        echo '<tr><td>container_max_size</td><td><input type="text" name="container_max_size" value="'.$row['container_max_size'].'" /></td></tr>';
        echo '<tr><td>container_max_slots</td><td><input type="text" name="container_max_slots" value="'.$row['container_max_slots'].'" /></td></tr>';
        echo '<tr><td>valid slots</td><td><input type="text" size="50" name="valid_slots" value="'.$row['valid_slots'].'"/></td></tr>';
        echo '<tr><td>flags</td><td><input type="text" size="50" name="flags" value="'.$row['flags'].'" /></td></tr>';
        echo '<tr><td>decay rate</td><td><input type="text" name="decay_rate" value="'.$row['decay_rate'].'" /></td></tr>';
        $skill_result = PrepSelect('skill');
        echo '<tr><td>item_skill_id_1</td><td>'.DrawSelectBox('skill', $skill_result, 'item_skill_id_1', $row['item_skill_id_1'], 'true').'</td></tr>';
        echo '<tr><td>item_skill_id_2</td><td>'.DrawSelectBox('skill', $skill_result, 'item_skill_id_2', $row['item_skill_id_2'], 'true').'</td></tr>';
        echo '<tr><td>item_skill_id_3</td><td>'.DrawSelectBox('skill', $skill_result, 'item_skill_id_3', $row['item_skill_id_3'], 'true').'</td></tr>';
        echo '<tr><td>item_bonus_1_attr</td><td><input type="text" name="item_bonus_1_attr" value="'.$row['item_bonus_1_attr'].'" /></td></tr>';
        echo '<tr><td>item_bonus_2_attr</td><td><input type="text" name="item_bonus_2_attr" value="'.$row['item_bonus_2_attr'].'" /></td></tr>';
        echo '<tr><td>item_bonus_3_attr</td><td><input type="text" name="item_bonus_3_attr" value="'.$row['item_bonus_3_attr'].'" /></td></tr>';
        echo '<tr><td>item_bonus_1_max</td><td><input type="text" name="item_bonus_1_max" value="'.$row['item_bonus_1_max'].'" /></td></tr>';
        echo '<tr><td>item_bonus_2_max</td><td><input type="text" name="item_bonus_2_max" value="'.$row['item_bonus_2_max'].'" /></td></tr>';
        echo '<tr><td>item_bonus_3_max</td><td><input type="text" name="item_bonus_3_max" value="'.$row['item_bonus_3_max'].'" /></td></tr>';
        echo '<tr><td>dmg_slash</td><td><input type="text" name="dmg_slash" value="'.$row['dmg_slash'].'"/></td></tr>';
        echo '<tr><td>dmg_blunt</td><td><input type="text" name="dmg_blunt" value="'.$row['dmg_blunt'].'"/></td></tr>';
        echo '<tr><td>dmg_pierce</td><td><input type="text" name="dmg_pierce" value="'.$row['dmg_pierce'].'"/></td></tr>';
        echo '<tr><td>weapon_speed</td><td><input type="text" name="weapon_speed" value="'.$row['weapon_speed'].'"/></td></tr>';
        echo '<tr><td>weapon_penetration</td><td><input type="text" name="weapon_penetration" value="'.$row['weapon_penetration'].'"/></td></tr>';
        echo '<tr><td>weapon_block_targeted</td><td><input type="text" name="weapon_block_targeted" value="'.$row['weapon_block_targeted'].'"/></td></tr>';
        echo '<tr><td>weapon_block_untargeted</td><td><input type="text" name="weapon_block_untargeted" value="'.$row['weapon_block_untargeted'].'"/></td></tr>';
        echo '<tr><td>weapon_counterblock</td><td><input type="text" name="weapon_counterblock" value="'.$row['weapon_counterblock'].'"/></td></tr>';
        echo '<tr><td>armor_hardness</td><td><input type="text" name="armor_hardness" value="'.$row['armor_hardness'].'"/></td></tr>';
        echo '<tr><td>cstr_gfx_mesh</td><td><input type="text" name="cstr_gfx_mesh" value="'.$row['cstr_gfx_mesh'].'"/></td></tr>';
        echo '<tr><td>cstr_gfx_icon</td><td><input type="text" name="cstr_gfx_icon" value="'.$row['cstr_gfx_icon'].'"/></td></tr>';
        echo '<tr><td>cstr_gfx_texture</td><td><input type="text" name="cstr_gfx_texture" value="'.$row['cstr_gfx_texture'].'"/></td></tr>';
        echo '<tr><td>cstr_part</td><td><input type="text" name="cstr_part" value="'.$row['cstr_part'].'"/></td></tr>';
        echo '<tr><td>cstr_part_mesh</td><td><input type="text" name="cstr_part_mesh" value="'.$row['cstr_part_mesh'].'"/></td></tr>';
        echo '<tr><td>removed_mesh</td><td><input type="text" name="removed_mesh" value="'.$row['removed_mesh'].'"/></td></tr>';
        echo '<tr><td>armorvsweapon_type</td><td><input type="text" name="armorvsweapon_type" value="'.$row['armorvsweapon_type'].'" /></td></tr>';
        $category_result = PrepSelect('category');
        $skillname_result = PrepSelect('skillnames');
        echo '<tr><td>category_id</td><td>'.DrawSelectBox('category', $category_result, 'category_id', $row['category_id']).'</td></tr>';
        echo '<tr><td>base_sale_price</td><td><input type="text" name="base_sale_price" value="'.$row['base_sale_price'].'"/></td></tr>';
        echo '<tr><td>item_type</td><td><input type="text" name="item_type" value="'.$row['item_type'].'" /></td></tr>';
        echo '<tr><td>requirement_1_name</td><td>'.DrawSelectBox('skillnames', $skillname_result, 'requirement_1_name', $row['requirement_1_name'], 'true').'</td></tr>';
        echo '<tr><td>requirement_1_value</td><td><input type="text" name="requirement_1_value" value="'.$row['requirement_1_value'].'"/></td></tr>';
        echo '<tr><td>requirement_2_name</td><td>'.DrawSelectBox('skillnames', $skillname_result, 'requirement_2_name', $row['requirement_2_name'], 'true').'</td></tr>';
        echo '<tr><td>requirement_2_value</td><td><input type="text" name="requirement_2_value" value="'.$row['requirement_2_value'].'"/></td></tr>';
        echo '<tr><td>requirement_3_name</td><td>'.DrawSelectBox('skillnames', $skillname_result, 'requirement_3_name', $row['requirement_3_name'], 'true').'</td></tr>';
        echo '<tr><td>requirement_3_value</td><td><input type="text" name="requirement_3_value" value="'.$row['requirement_3_value'].'"/></td></tr>';
        echo '<tr><td>item_type_id_ammo</td><td><input type="text" name="item_type_id_ammo" value="'.$row['item_type_id_ammo'].'"/></td></tr>';
        echo '<tr><td>spell_id_on_hit</td><td><input type="text" name="spell_id_on_hit" value="'.$row['spell_id_on_hit'].'"/></td></tr>';
        echo '<tr><td>spell_on_hit_prob</td><td><input type="text" name="spell_on_hit_prob" value="'.$row['spell_on_hit_prob'].'"/></td></tr>';
        echo '<tr><td>spell_id_feature</td><td><input type="text" name="spell_id_feature" value="'.$row['spell_id_feature'].'"/></td></tr>';
        echo '<tr><td>spell_feature_charges</td><td><input type="text" name="spell_feature_charges" value="'.$row['spell_feature_charges'].'"/></td></tr>';
        echo '<tr><td>spell_feature_timing</td><td><input type="text" name="spell_feature_timing" value="'.$row['spell_feature_timing'].'" /></td></tr>';
        echo '<tr><td>item_anim_id</td><td><input type="text" name="item_anim_id" value="'.$row['item_anim_id'].'"/></td></tr>';
        echo '<tr><td>item_max_quality</td><td><input type="text" name="item_max_quality" value="'.$row['item_max_quality'].'"/></td></tr>';
        echo '<tr><td>equip_script</td><td><textarea name="equip_script" rows="6" cols="55">'.htmlspecialchars($row['equip_script']).'</textarea></td></tr>';
        echo '<tr><td>consume_script</td><td><textarea name="consume_script" rows="6" cols="55">'.htmlspecialchars($row['consume_script']).'</textarea></td></tr>';
        echo '<tr><td>creative_definition</td><td>';
        if (strpos($row['flags'], "CREATIVE") !== FALSE){
        echo '<textarea name="creative_definition" rows="6" cols="50">'.$row['creative_definition'].'</textarea>';
        }else{
          echo 'This item is not a "CREATIVE" item<input type="hidden" name="creative_definition" value="" />';
        }
        echo '</td></tr>';
        echo '<tr><td>max_charges</td><td><input type="text" name="max_charges" value="'.$row['max_charges'].'" /></td></tr>';
        echo '<tr><td>weapon_range</td><td><input type="text" name="weapon_range" value="'.$row['weapon_range'].'" /></td></tr>';
        echo '<tr><td>assigned_command</td><td><input type="text" name="assigned_command" value="'.$row['assigned_command'].'" /></td></tr>';
        echo '<tr><td>spawnable</td><td>';
        if ($row['spawnable'] == "Y")
        {
            echo '<select name="spawnable"><option value="N">False</option><option value="Y" selected="true">True</option></select>';
        }
        else
        {
            echo '<select name="spawnable"><option value="N" selected="true">False</option><option value="Y">True</option></select>';
        }
        echo '</td></tr>';
        echo '</table>';
        echo '<input type="submit" name="submit" value="Submit Changes" />';
        echo '</form>';
      }else{
        echo '<p class="error">Error: No item selected, returnting to item selection.</p>';
        include('./items/listitems.php');
        listitems();
      }
    }else{
 //here we do the "magic"
      $id = escapeSqlString($_GET['item']);
      $query = 'UPDATE item_stats SET ';
      if (isset($_POST['new_id']) && $_POST['new_id'] != '' && is_numeric($_POST['new_id']))  // only change the ID if new ID is set and has a valid value.
      {
        $new_id = escapeSqlString($_POST['new_id']);
        $query = $query . "id='$new_id', ";
      }
      $stat_type = escapeSqlString($_POST['stat_type']);
      $query = $query . "stat_type='$stat_type', ";
      $name = escapeSqlString($_POST['name']);
      $query = $query . "name='$name', ";
      $description = escapeSqlString($_POST['description']);
      $query = $query . "description='$description', ";
      $weight = escapeSqlString($_POST['weight']);
      $query = $query . "weight='$weight', ";
      $visible_distance = escapeSqlString($_POST['visible_distance']);
      $query = $query . "visible_distance='$visible_distance', ";
      $size = escapeSqlString($_POST['size']);
      $query = $query . "size='$size', ";
      $container_max_size = escapeSqlString($_POST['container_max_size']); 
      $query = $query . "container_max_size='$container_max_size', ";
      $container_max_slots = escapeSqlString($_POST['container_max_slots']);
      $query = $query . "container_max_slots='$container_max_slots', ";
      $valid_slots = escapeSqlString($_POST['valid_slots']);
      $query = $query . "valid_slots='$valid_slots', ";
      $flags = escapeSqlString($_POST['flags']);
      $query = $query . "flags='$flags', ";
      $decay_rate = escapeSqlString($_POST['decay_rate']);
      $query = $query . "decay_rate='$decay_rate', ";
      $item_skill_id_1 = escapeSqlString($_POST['item_skill_id_1']);
      $query = $query . "item_skill_id_1='$item_skill_id_1', ";
      $item_skill_id_2 = escapeSqlString($_POST['item_skill_id_2']);
      $query = $query . "item_skill_id_2='$item_skill_id_2', ";
      $item_skill_id_3 = escapeSqlString($_POST['item_skill_id_3']);
      $query = $query . "item_skill_id_3='$item_skill_id_3', ";
      $item_bonus_1_attr = escapeSqlString($_POST['item_bonus_1_attr']);
      $query = $query . "item_bonus_1_attr='$item_bonus_1_attr', ";
      $item_bonus_2_attr = escapeSqlString($_POST['item_bonus_2_attr']);
      $query = $query . "item_bonus_2_attr='$item_bonus_2_attr', ";
      $item_bonus_3_attr = escapeSqlString($_POST['item_bonus_3_attr']);
      $query = $query . "item_bonus_3_attr='$item_bonus_3_attr', ";
      $item_bonus_1_max = escapeSqlString($_POST['item_bonus_1_max']);
      $query = $query . "item_bonus_1_max='$item_bonus_1_max', ";
      $item_bonus_2_max = escapeSqlString($_POST['item_bonus_2_max']);
      $query = $query . "item_bonus_2_max='$item_bonus_2_max', ";
      $item_bonus_3_max = escapeSqlString($_POST['item_bonus_3_max']);
      $query = $query . "item_bonus_3_max='$item_bonus_3_max', ";
      $dmg_slash = escapeSqlString($_POST['dmg_slash']);
      $query = $query . "dmg_slash='$dmg_slash', ";
      $dmg_blunt = escapeSqlString($_POST['dmg_blunt']);
      $query = $query . "dmg_blunt='$dmg_blunt', ";
      $dmg_pierce = escapeSqlString($_POST['dmg_pierce']);
      $query = $query . "dmg_pierce='$dmg_pierce', ";
      $weapon_speed = escapeSqlString($_POST['weapon_speed']);
      $query = $query . "weapon_speed='$weapon_speed', ";
      $weapon_penetration = escapeSqlString($_POST['weapon_penetration']);
      $query = $query . "weapon_penetration='$weapon_penetration', ";
      $weapon_block_targeted = escapeSqlString($_POST['weapon_block_targeted']);
      $query = $query . "weapon_block_targeted='$weapon_block_targeted', ";
      $weapon_block_untargeted = escapeSqlString($_POST['weapon_block_untargeted']);
      $query = $query . "weapon_block_untargeted='$weapon_block_untargeted', ";
      $weapon_counterblock = escapeSqlString($_POST['weapon_counterblock']);
      $query = $query . "weapon_counterblock='$weapon_counterblock', ";
      $armor_hardness = escapeSqlString($_POST['armor_hardness']);
      $query = $query . "armor_hardness='$armor_hardness', ";
      $cstr_gfx_mesh = escapeSqlString($_POST['cstr_gfx_mesh']);
      $query = $query . "cstr_gfx_mesh='$cstr_gfx_mesh', ";
      $cstr_gfx_icon = escapeSqlString($_POST['cstr_gfx_icon']);
      $query = $query . "cstr_gfx_icon='$cstr_gfx_icon', ";
      $cstr_gfx_texture = escapeSqlString($_POST['cstr_gfx_texture']);
      $query = $query . "cstr_gfx_texture='$cstr_gfx_texture', ";
      $cstr_part = escapeSqlString($_POST['cstr_part']);
      $query = $query . "cstr_part='$cstr_part', ";
      $cstr_part_mesh = escapeSqlString($_POST['cstr_part_mesh']);
      $query = $query . "cstr_part_mesh='$cstr_part_mesh', ";  
      $removed_mesh = escapeSqlString($_POST['removed_mesh']);
      $query = $query . "removed_mesh='$removed_mesh', ";
      $armorvsweapon_type = escapeSqlString($_POST['armorvsweapon_type']);
      $query = $query . "armorvsweapon_type='$armorvsweapon_type', ";
      $category_id = escapeSqlString($_POST['category_id']);
      $query = $query . "category_id='$category_id', ";
      $base_sale_price = escapeSqlString($_POST['base_sale_price']);
      $query = $query . "base_sale_price='$base_sale_price', ";
      $item_type = escapeSqlString($_POST['item_type']);
      $query = $query . "item_type='$item_type', ";
      $requirement_1_name = escapeSqlString($_POST['requirement_1_name']);
      $query = $query . "requirement_1_name='$requirement_1_name', ";
      $requirement_1_value = escapeSqlString($_POST['requirement_1_value']);
      $query = $query . "requirement_1_value='$requirement_1_value', ";
      $requirement_2_name = escapeSqlString($_POST['requirement_2_name']);
      $query = $query . "requirement_2_name='$requirement_2_name', ";
      $requirement_2_value = escapeSqlString($_POST['requirement_2_value']);
      $query = $query . "requirement_2_value='$requirement_2_value', ";
      $requirement_3_name = escapeSqlString($_POST['requirement_3_name']);
      $query = $query . "requirement_3_name='$requirement_3_name', ";
      $requirement_3_value = escapeSqlString($_POST['requirement_3_value']);
      $query = $query . "requirement_3_value='$requirement_3_value', ";
      $item_type_id_ammo = escapeSqlString($_POST['item_type_id_ammo']);
      $query = $query . "item_type_id_ammo='$item_type_id_ammo', ";
      $spell_id_on_hit = escapeSqlString($_POST['spell_id_on_hit']);
      $query = $query . "spell_id_on_hit='$spell_id_on_hit', ";
      $spell_on_hit_prob = escapeSqlString($_POST['spell_on_hit_prob']);
      $query = $query . "spell_on_hit_prob='$spell_on_hit_prob', ";
      $spell_id_feature = escapeSqlString($_POST['spell_id_feature']);
      $query = $query . "spell_id_feature='$spell_id_feature', ";
      $spell_feature_charges = escapeSqlString($_POST['spell_feature_charges']);
      $query = $query . "spell_feature_charges='$spell_feature_charges', ";
      $spell_feature_timing = escapeSqlString($_POST['spell_feature_timing']);
      $query = $query . "spell_feature_timing='$spell_feature_timing', ";
      $item_anim_id = escapeSqlString($_POST['item_anim_id']);
      $query = $query . "item_anim_id='$item_anim_id', ";
      $item_max_quality = escapeSqlString($_POST['item_max_quality']);
      $query = $query . "item_max_quality='$item_max_quality', ";
      $equip_script = escapeSqlString($_POST['equip_script']);
      $query = $query . "equip_script='$equip_script', ";
      $consume_script = escapeSqlString($_POST['consume_script']);
      $query = $query . "consume_script='$consume_script', ";
      $creative_definition = escapeSqlString($_POST['creative_definition']);
      $query = $query . "creative_definition='$creative_definition', ";
      $max_charges = escapeSqlString($_POST['max_charges']);
      $query = $query . "max_charges='$max_charges', ";
      $weapon_range = escapeSqlString($_POST['weapon_range']);
      $query = $query . "weapon_range='$weapon_range', ";
      $assigned_command = escapeSqlString($_POST['assigned_command']);
      $query = $query . "assigned_command='$assigned_command', ";
      $spawnable = escapeSqlString($_POST['spawnable']);
      $query = $query . "spawnable='$spawnable' ";
      $query = $query . "WHERE id=$id";
      $result = mysql_query2($query);
      if (isset($new_id))
      {
        $id = $new_id; // change id to new_id if it was set, so redirect works properly.
      }
?>
    <SCRIPT language="javascript">
      document.location = "index.php?do=listitems&category=<?php echo $category_id?>&item=<?php echo $id?>";
    </script>
<?php
      exit;
    }
  }else{
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}
?>
