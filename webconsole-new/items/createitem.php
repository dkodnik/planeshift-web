<?php
function createitem(){
  if (checkaccess('items', 'create')){
    if (isset($_GET['commit']) && isset($_POST['name'])){
   //do the magic
      $query = 'INSERT INTO item_stats ';
      $cols = '(';
      $name = mysql_real_escape_string($_POST['name']);
      $values = 'VALUES (';
      $cols = $cols.'name';
      $values = $values . "'$name'";
      if (isset($_POST['id']) && $_POST['id'] != '' && is_numeric($_POST['id'])) { // only set the ID if it is set and has a valid value.){
        $id = mysql_real_escape_string($_POST['id']);
        $cols = $cols . ', id';
        $values = $values . ", '$id'";
      }
      if (isset($_POST['stat_type'])){
        $stat_type = mysql_real_escape_string($_POST['stat_type']);
        $cols = $cols . ', stat_type';
        $values = $values . ", '$stat_type'";
      }
      if (isset($_POST['description'])){
        $description = mysql_real_escape_string($_POST['description']);
        $cols = $cols . ', description';
        $values = $values . ", '$description'";
      }
      if (isset($_POST['weight'])){
        $weight = mysql_real_escape_string($_POST['weight']);
        $cols = $cols . ', weight';
        $values = $values . ", '$weight'";
      }
      if (isset($_POST['visible_distance'])){
        $visible_distance = mysql_real_escape_string($_POST['visible_distance']);
        $cols = $cols . ', visible_distance';
        $values = $values . ", '$visible_distance'";
      }
      if (isset($_POST['size'])){
        $size = mysql_real_escape_string($_POST['size']);
        $cols = $cols . ', size';
        $values = $values . ", '$size'";
      }
      if (isset($_POST['container_max_size'])){
        $container_max_size = mysql_real_escape_string($_POST['container_max_size']);
        $cols = $cols . ', container_max_size';
        $values = $values . ", '$container_max_size'";
      }
      if (isset($_POST['container_max_slot'])){
        $container_max_slot = mysql_real_escape_string($_POST['container_max_slot']);
        $cols = $cols . ', container_max_slot';
        $values = $values . ", '$container_max_slot'";
      }
      if (isset($_POST['valid_slots'])){
        $valid_slots = mysql_real_escape_string($_POST['valid_slots']);
        $cols = $cols . ', valid_slots';
        $values = $values . ", '$valid_slots'";
      }
      if (isset($_POST['flags'])){
        $flags = mysql_real_escape_string($_POST['flags']);
        $cols = $cols . ', flags';
        $values = $values . ", '$flags'";
      }
      if (isset($_POST['decay_rate'])){
        $decay_rate = mysql_real_escape_string($_POST['decay_rate']);
        $cols = $cols . ', decay_rate';
        $values = $values . ", '$decay_rate'";
      }
      if (isset($_POST['item_skill_id_1']) && trim($_POST['item_skill_id_1']) != ""){
        $item_skill_id_1 = mysql_real_escape_string($_POST['item_skill_id_1']);
        $cols = $cols . ', item_skill_id_1';
        $values = $values . ", '$item_skill_id_1'";
      }
      if (isset($_POST['item_skill_id_2']) && trim($_POST['item_skill_id_2']) != ""){
        $item_skill_id_2 = mysql_real_escape_string($_POST['item_skill_id_2']);
        $cols = $cols . ', item_skill_id_2';
        $values = $values . ", '$item_skill_id_2'";
      }
      if (isset($_POST['item_skill_id_3']) && trim($_POST['item_skill_id_3']) != ""){
        $item_skill_id_3 = mysql_real_escape_string($_POST['item_skill_id_3']);
        $cols = $cols . ', item_skill_id_3';
        $values = $values . ", '$item_skill_id_3'";
      }
      if (isset($_POST['item_bonus_1_attr'])){
        $item_bonus_1_attr = mysql_real_escape_string($_POST['item_bonus_1_attr']);
        $cols = $cols . ', item_bonus_1_attr';
        $values = $values . ", '$item_bonus_1_attr'";
      }
      if (isset($_POST['item_bonus_2_attr'])){
        $item_bonus_2_attr = mysql_real_escape_string($_POST['item_bonus_2_attr']);
        $cols = $cols . ', item_bonus_2_attr';
        $values = $values . ", '$item_bonus_2_attr'";
      }
      if (isset($_POST['item_bonus_3_attr'])){
        $item_bonus_3_attr = mysql_real_escape_string($_POST['item_bonus_3_attr']);
        $cols = $cols . ', item_bonus_3_attr';
        $values = $values . ", '$item_bonus_3_attr'";
      }
      if (isset($_POST['item_bonus_1_max']) && trim($_POST['item_bonus_1_max']) != ""){
        $item_bonus_1_max = mysql_real_escape_string($_POST['item_bonus_1_max']);
        $cols = $cols . ', item_bonus_1_max';
        $values = $values . ", '$item_bonus_1_max'";
      }
      if (isset($_POST['item_bonus_2_max']) && trim($_POST['item_bonus_2_max']) != ""){
        $item_bonus_2_max = mysql_real_escape_string($_POST['item_bonus_2_max']);
        $cols = $cols . ', item_bonus_2_max';
        $values = $values . ", '$item_bonus_2_max'";
      }
      if (isset($_POST['item_bonus_3_max']) && trim($_POST['item_bonus_3_max']) != ""){
        $item_bonus_3_max = mysql_real_escape_string($_POST['item_bonus_3_max']);
        $cols = $cols . ', item_bonus_3_max';
        $values = $values . ", '$item_bonus_3_max'";
      }
      if (isset($_POST['dmg_slash']) && trim($_POST['dmg_slash']) != ""){
        $dmg_slash = mysql_real_escape_string($_POST['dmg_slash']);
        $cols = $cols . ', dmg_slash';
        $values = $values . ", '$dmg_slash'";
      }
      if (isset($_POST['dmg_blunt']) && trim($_POST['dmg_blunt']) != ""){
        $dmg_blunt = mysql_real_escape_string($_POST['dmg_blunt']);
        $cols = $cols . ', dmg_blunt';
        $values = $values . ", '$dmg_blunt'";
      }
      if (isset($_POST['dmg_pierce']) && trim($_POST['dmg_pierce']) != ""){
        $dmg_pierce = mysql_real_escape_string($_POST['dmg_pierce']);
        $cols = $cols . ', dmg_pierce';
        $values = $values . ", '$dmg_pierce'";
      }
      if (isset($_POST['weapon_speed']) && trim($_POST['weapon_speed']) != ""){
        $weapon_speed = mysql_real_escape_string($_POST['weapon_speed']);
        $cols = $cols . ', weapon_speed';
        $values = $values . ", '$weapon_speed'";
      }
      if (isset($_POST['weapon_penetration']) && trim($_POST['weapon_penetration']) != ""){
        $weapon_penetration = mysql_real_escape_string($_POST['weapon_penetration']);
        $cols = $cols . ', weapon_penetration';
        $values = $values . ", '$weapon_penetration'";
      }
      if (isset($_POST['weapon_block_targeted'])  && trim($_POST['weapon_block_targeted']) != ""){
        $weapon_block_targeted = mysql_real_escape_string($_POST['weapon_block_targeted']);
        $cols = $cols . ', weapon_block_targeted';
        $values = $values . ", '$weapon_block_targeted'";
      }
      if (isset($_POST['weapon_block_untargeted']) && trim($_POST['weapon_block_untargeted']) != ""){
        $weapon_block_untargeted = mysql_real_escape_string($_POST['weapon_block_untargeted']);
        $cols = $cols . ', weapon_block_untargeted';
        $values = $values . ", '$weapon_block_untargeted'";
      }
      if (isset($_POST['weapon_counterblock']) && trim($_POST['weapon_counterblock']) != ""){
        $weapon_counterblock = mysql_real_escape_string($_POST['weapon_counterblock']);
        $cols = $cols . ', weapon_counterblock';
        $values = $values . ", '$weapon_counterblock'";
      }
      if (isset($_POST['armor_hardness']) && trim($_POST['armor_hardness']) != ""){
        $armor_hardness = mysql_real_escape_string($_POST['armor_hardness']);
        $cols = $cols . ', armor_hardness';
        $values = $values . ", '$armor_hardness'";
      }
      if (isset($_POST['cstr_gfx_mesh'])){
        $cstr_gfx_mesh = mysql_real_escape_string($_POST['cstr_gfx_mesh']);
        $cols = $cols . ', cstr_gfx_mesh';
        $values = $values . ", '$cstr_gfx_mesh'";
      }
      if (isset($_POST['cstr_gfx_icon'])){
        $cstr_gfx_icon = mysql_real_escape_string($_POST['cstr_gfx_icon']);
        $cols = $cols . ', cstr_gfx_icon';
        $values = $values . ", '$cstr_gfx_icon'";
      }
      if (isset($_POST['cstr_gfx_texture'])){
        $cstr_gfx_texture = mysql_real_escape_string($_POST['cstr_gfx_texture']);
        $cols = $cols . ', cstr_gfx_texture';
        $values = $values . ", '$cstr_gfx_texture'";
      }
      if (isset($_POST['cstr_part'])){
        $cstr_part = mysql_real_escape_string($_POST['cstr_part']);
        $cols = $cols . ', cstr_part';
        $values = $values . ", '$cstr_part'";
      }
      if (isset($_POST['cstr_part_mesh'])){
        $cstr_part_mesh = mysql_real_escape_string($_POST['cstr_part_mesh']);
        $cols = $cols . ', cstr_part_mesh';
        $values = $values . ", '$cstr_part_mesh'";
      } 
      if (isset($_POST['removed_mesh'])){
        $removed_mesh = mysql_real_escape_string($_POST['removed_mesh']);
        $cols = $cols . ', removed_mesh';
        $values = $values . ", '$removed_mesh'";
      }
      if (isset($_POST['armorvsweapon_type'])){
        $armorvsweapon_type = mysql_real_escape_string($_POST['armorvsweapon_type']);
        $cols = $cols . ', armorvsweapon_type';
        $values = $values . ", '$armorvsweapon_type'";
      }
      if (isset($_POST['category_id'])){
        $category_id = mysql_real_escape_string($_POST['category_id']);
        $cols = $cols . ', category_id';
        $values = $values . ", '$category_id'";
      }
      if (isset($_POST['base_sale_price'])){
        $base_sale_price = mysql_real_escape_string($_POST['base_sale_price']);
        $cols = $cols . ', base_sale_price';
        $values = $values . ", '$base_sale_price'";
      }
      if (isset($_POST['item_type'])){
        $item_type = mysql_real_escape_string($_POST['item_type']);
        $cols = $cols . ', item_type';
        $values = $values . ", '$item_type'";
      }
      if (isset($_POST['requirement_1_name'])){
        $requirement_1_name = mysql_real_escape_string($_POST['requirement_1_name']);
        $cols = $cols . ', requirement_1_name';
        $values = $values . ", '$requirement_1_name'";
      }
      if (isset($_POST['requirement_1_value']) && trim($_POST['requirement_1_value']) != ""){
        $requirement_1_value = mysql_real_escape_string($_POST['requirement_1_value']);
        $cols = $cols . ', requirement_1_value';
        $values = $values . ", '$requirement_1_value'";
      }
      if (isset($_POST['requirement_2_name'])){
        $requirement_2_name = mysql_real_escape_string($_POST['requirement_2_name']);
        $cols = $cols . ', requirement_2_name';
        $values = $values . ", '$requirement_2_name'";
      }
      if (isset($_POST['requirement_2_value']) && trim($_POST['requirement_2_value']) != ""){
        $requirement_2_value = mysql_real_escape_string($_POST['requirement_2_value']);
        $cols = $cols . ', requirement_2_value';
        $values = $values . ", '$requirement_2_value'";
      }
      if (isset($_POST['requirement_3_name'])){
        $requirement_3_name = mysql_real_escape_string($_POST['requirement_3_name']);
        $cols = $cols . ', requirement_3_name';
        $values = $values . ", '$requirement_3_name'";
      }
      if (isset($_POST['requirement_3_value']) && trim($_POST['requirement_3_value']) != ""){
        $requirement_3_value = mysql_real_escape_string($_POST['requirement_3_value']);
        $cols = $cols . ', requirement_3_value';
        $values = $values . ", '$requirement_3_value'";
      }
      if (isset($_POST['item_type_id_ammo'])){
        $item_type_id_ammo = mysql_real_escape_string($_POST['item_type_id_ammo']);
        $cols = $cols . ', item_type_id_ammo';
        $values = $values . ", '$item_type_id_ammo'";
      }
      if (isset($_POST['spell_id_on_hit'])){
        $spell_id_on_hit = mysql_real_escape_string($_POST['spell_id_on_hit']);
        $cols = $cols . ', spell_id_on_hit';
        $values = $values . ", '$spell_id_on_hit'";
      }
      if (isset($_POST['spell_on_hit_prob']) && trim($_POST['spell_on_hit_prob']) != ""){
        $spell_on_hit_prob = mysql_real_escape_string($_POST['spell_on_hit_prob']);
        $cols = $cols . ', spell_on_hit_prob';
        $values = $values . ", '$spell_on_hit_prob'";
      }
      if (isset($_POST['spell_id_feature'])){
        $spell_id_feature = mysql_real_escape_string($_POST['spell_id_feature']);
        $cols = $cols . ', spell_id_feature';
        $values = $values . ", '$spell_id_feature'";
      }
      if (isset($_POST['spell_feature_charges'])){
        $spell_feature_charges = mysql_real_escape_string($_POST['spell_feature_charges']);
        $cols = $cols . ', spell_feature_charges';
        $values = $values . ", '$spell_feature_charges'";
      }
      if (isset($_POST['spell_feature_timing'])){
        $spell_feature_timing = mysql_real_escape_string($_POST['spell_feature_timing']);
        $cols = $cols . ', spell_feature_timing';
        $values = $values . ", '$spell_feature_timing'";
      }
      if (isset($_POST['item_anim_id'])){
        $item_anim_id = mysql_real_escape_string($_POST['item_anim_id']);
        $cols = $cols . ', item_anim_id';
        $values = $values . ", '$item_anim_id'";
      }
      if (isset($_POST['equip_script'])){
        $equip_script = mysql_real_escape_string($_POST['equip_script']);
        $cols = $cols . ', equip_script';
        $values = $values . ", '$equip_script'";
      }
      if (isset($_POST['consume_script'])){
        $consume_script = mysql_real_escape_string($_POST['consume_script']);
        $cols = $cols . ', consume_script';
        $values = $values . ", '$consume_script'";
      }
      if (isset($_POST['creative_definition'])){
        $creative_definition = mysql_real_escape_string($_POST['creative_definition']);
        $cols = $cols . ', creative_definition';
        $values = $values . ", '$creative_definition'";
      }
      if (isset($_POST['max_charges'])){
        $max_charges = mysql_real_escape_string($_POST['max_charges']);
        $cols = $cols . ', max_charges';
        $values = $values . ", '$max_charges'";
      }
      if (isset($_POST['weapon_range']) && trim($_POST['weapon_range']) != ""){
        $weapon_range = mysql_real_escape_string($_POST['weapon_range']);
        $cols = $cols . ', weapon_range';
        $values = $values . ", '$weapon_range'";
      }
      if (isset($_POST['assigned_command']) && trim($_POST['assigned_command']) != ""){
        $assigned_command = mysql_real_escape_string($_POST['assigned_command']);
        $cols = $cols . ', assigned_command';
        $values = $values . ", '$assigned_command'";
      }
      if (isset($_POST['spawnable']) && trim($_POST['spawnable']) != ""){
        $spawnable = mysql_real_escape_string($_POST['spawnable']);
        $cols = $cols . ', spawnable';
        $values = $values . ", '$spawnable'";
      }
   // Finish preparing the query
      $query = $query . $cols . ')' . $values . ')';
      $result = mysql_query2($query);
    }else{
   //display entry form
      echo '<form action="./index.php?do=createitem&amp;commit" method="post">';
      echo '<table border="1">';
      echo '<tr><td>stat_type</td><td>';
      echo '<select name="stat_type">';
      $alpha = "BUR";
      for ($i="0";$i<strlen($alpha);$i++){
        $j = substr($alpha,$i,1);
        echo '<option value="'.$j.'"';
        echo '>'.$j.'</option>'."\n";
      }
      echo '</select></td></tr>';
      echo '<tr><td>ID</td><td><input type="text" size="4" name="id"/> <span class="error">Only do this if you really know what you are doing.</span></td></tr>';
      echo '<tr><td>Name</td><td><input type="text" size="50" name="name" /></td></tr>';
      echo '<tr><td>Description</td><td><input type="text" size="50" name="description" /></td></tr>';
      echo '<tr><td>Weight</td><td><input type="text" name="weight" /></td></tr>';
      echo '<tr><td>Visible Distance</td><td><input type="text" name="visible_distance" /></td></tr>';
      echo '<tr><td>Size</td><td><input type="text" name="size" /></td></tr>';
      echo '<tr><td>container_max_size</td><td><input type="text" name="container_max_size" /></td></tr>';
      echo '<tr><td>container_max_slots</td><td><input type="text" name="container_max_slots" /></td></tr>';
      echo '<tr><td>valid slots</td><td><input type="text" size="50" name="valid_slots" /></td></tr>';
      echo '<tr><td>flags</td><td><input type="text" size="50" name="flags" /></td></tr>';
      echo '<tr><td>decay rate</td><td><input type="text" name="decay_rate" /></td></tr>';
      $skill_result = PrepSelect('skill');
      echo '<tr><td>item_skill_id_1</td><td>'.DrawSelectBox('skill', $skill_result, 'item_skill_id_1', '', 'true').'</td></tr>';
      echo '<tr><td>item_skill_id_2</td><td>'.DrawSelectBox('skill', $skill_result, 'item_skill_id_2', '', 'true').'</td></tr>';
      echo '<tr><td>item_skill_id_3</td><td>'.DrawSelectBox('skill', $skill_result, 'item_skill_id_3', '', 'true').'</td></tr>';
      echo '<tr><td>item_bonus_1_attr</td><td><input type="text" name="item_bonus_1_attr" /></td></tr>';
      echo '<tr><td>item_bonus_2_attr</td><td><input type="text" name="item_bonus_2_attr" /></td></tr>';
      echo '<tr><td>item_bonus_3_attr</td><td><input type="text" name="item_bonus_3_attr" /></td></tr>';
      echo '<tr><td>item_bonus_1_max</td><td><input type="text" name="item_bonus_1_max" /></td></tr>';
      echo '<tr><td>item_bonus_2_max</td><td><input type="text" name="item_bonus_2_max" /></td></tr>';
      echo '<tr><td>item_bonus_3_max</td><td><input type="text" name="item_bonus_3_max" /></td></tr>';
      echo '<tr><td>dmg_slash</td><td><input type="text" name="dmg_slash" /></td></tr>';
      echo '<tr><td>dmg_blunt</td><td><input type="text" name="dmg_blunt" /></td></tr>';
      echo '<tr><td>dmg_pierce</td><td><input type="text" name="dmg_pierce" /></td></tr>';
      echo '<tr><td>weapon_speed</td><td><input type="text" name="weapon_speed" /></td></tr>';
      echo '<tr><td>weapon_penetration</td><td><input type="text" name="weapon_penetration" /></td></tr>';
      echo '<tr><td>weapon_block_targeted</td><td><input type="text" name="weapon_block_targeted" /></td></tr>';
      echo '<tr><td>weapon_block_untargeted</td><td><input type="text" name="weapon_block_untargeted" /></td></tr>';
      echo '<tr><td>weapon_counterblock</td><td><input type="text" name="weapon_counterblock" /></td></tr>';
      echo '<tr><td>armor_hardness</td><td><input type="text" name="armor_hardness" /></td></tr>';
      echo '<tr><td>cstr_gfx_mesh</td><td><input type="text" name="cstr_gfx_mesh"/></td></tr>';
      echo '<tr><td>cstr_gfx_icon</td><td><input type="text" name="cstr_gfx_icon"/></td></tr>';
      echo '<tr><td>cstr_gfx_texture</td><td><input type="text" name="cstr_gfx_texture"/></td></tr>';
      echo '<tr><td>cstr_part</td><td><input type="text" name="cstr_part"/></td></tr>';
      echo '<tr><td>cstr_part_mesh</td><td><input type="text" name="cstr_part_mesh"/></td></tr>';
      echo '<tr><td>removed_mesh</td><td><input type="text" name="removed_mesh"/></td></tr>';
      echo '<tr><td>armorvsweapon_type</td><td><input type="text" name="armorvsweapon_type" /></td></tr>';
      $category_result = PrepSelect('category');
      $skillname_result = PrepSelect('skillnames');
      echo '<tr><td>category_id</td><td>'.DrawSelectBox('category', $category_result, 'category_id', '').'</td></tr>';
      echo '<tr><td>base_sale_price</td><td><input type="text" name="base_sale_price" /></td></tr>';
      echo '<tr><td>item_type</td><td><input type="text" name="item_type" /></td></tr>';
      echo '<tr><td>requirement_1_name</td><td>'.DrawSelectBox('skillnames', $skillname_result, 'requirement_1_name', '', 'true').'</td></tr>';
      echo '<tr><td>requirement_1_value</td><td><input type="text" name="requirement_1_value" /></td></tr>';
      echo '<tr><td>requirement_2_name</td><td>'.DrawSelectBox('skillnames', $skillname_result, 'requirement_2_name', '', 'true').'</td></tr>';
      echo '<tr><td>requirement_2_value</td><td><input type="text" name="requirement_2_value" /></td></tr>';
      echo '<tr><td>requirement_3_name</td><td>'.DrawSelectBox('skillnames', $skillname_result, 'requirement_3_name', '', 'true').'</td></tr>';
      echo '<tr><td>requirement_3_value</td><td><input type="text" name="requirement_3_value" /></td></tr>';
      echo '<tr><td>item_type_id_ammo</td><td><input type="text" name="item_type_id_ammo" /></td></tr>';
      echo '<tr><td>spell_id_on_hit</td><td><input type="text" name="spell_id_on_hit" /></td></tr>';
      echo '<tr><td>spell_on_hit_prob</td><td><input type="text" name="spell_on_hit_prob" /></td></tr>';
      echo '<tr><td>spell_id_feature</td><td><input type="text" name="spell_id_feature" /></td></tr>';
      echo '<tr><td>spell_feature_charges</td><td><input type="text" name="spell_feature_charges" /></td></tr>';
      echo '<tr><td>spell_feature_timing</td><td><input type="text" name="spell_feature_timing" /></td></tr>';
      echo '<tr><td>item_anim_id</td><td><input type="text" name="item_anim_id" /></td></tr>';
      $script_result = PrepSelect('scripts');
      echo '<tr><td>equip_script</td><td>'.DrawSelectBox('scripts', $script_result, 'equip_script' , '', 'true').'</td></tr>';
      echo '<tr><td>consume_script</td><td>'.DrawSelectBox('scripts', $script_result, 'consume_script' , '', 'true').'</td></tr>';
      echo '<tr><td>creative_definition</td><td>';
      echo '<textarea name="creative_definition" rows="6" cols="50"></textarea>';
      echo '</td></tr>';
      echo '<tr><td>max_charges</td><td><input type="text" name="max_charges" /></td></tr>';
      echo '<tr><td>weapon_range</td><td><input type="text" name="weapon_range" /></td></tr>';
      echo '<tr><td>assigned_command</td><td><input type="text" name="assigned_command" /></td></tr>';
      echo '<tr><td>spawnable</td><td><select name="spawnable"><option value="N">False</option><option value="Y" selected="true">True</option></select></td></tr>';
      echo '</table>';
      echo '<input type="submit" name="submit" value="Submit Changes" />';
      echo '</form>';
    }
  }else{
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}
?>
