<?php
function compareitems(){
  if(checkaccess('items', 'read')){
	$cat = '';
	if (isset($_POST['category_id'])){
      $cat = $_POST['category_id'];
    }
	$comparetype = '';
	$comtype = '';
	if (isset($_POST['compare_type'])){
	  $comtype = $_POST['compare_type'];
	}
	$tableheads = '<th>ID</th><th>Name</th>';
	$selvals = 'i.id, i.name';
	
	  switch($comtype)
	 {
		 
		case 'att':
			$comparetype = '<option value="att">Attack</option><option value="gen">General</option><option value="def">Defense</option><option value="gfx">Graphics</option><option value="req">Requirements</option><option value="bon">Bonuses</option><option value="misc">Misc</option>';
			$tableheads .= '<th>Blunt</th><th>Pierce</th><th>Slash</th><th>Ammo</th><th>Penetration</th><th>Range</th><th>Speed</th>';
			$selvals .= ', i.dmg_blunt, i.dmg_pierce, i.dmg_slash, i.item_type_id_ammo, i.weapon_penetration, i.weapon_range, i.weapon_speed';
			break;
		case 'def':
			$comparetype = '<option value="def">Defense</option><option value="gen">General</option><option value="att">Attack</option><option value="gfx">Graphics</option><option value="req">Requirements</option><option value="bon">Bonuses</option><option value="misc">Misc</option>';
			$tableheads .= '<th>Armor Hardness</th><th>ArmorVsWeapon_type</th><th>Block Targeted</th><th>Block Untargeted</th><th>counterblock</th>';
			$selvals .= ', i.armor_hardness, i.armorvsweapon_type, i.weapon_block_targeted, i.weapon_block_untargeted, i.weapon_counterblock';
			break;
		case 'gfx':
			$comparetype = '<option value="gfx">Graphics</option><option value="gen">General</option><option value="att">Attack</option><option value="def">Defense</option><option value="req">Requirements</option><option value="bon">Bonuses</option><option value="misc">Misc</option>';
			$tableheads .= '<th>Icon</th><th>Mesh</th><th>Texture</th><th>cstr part</th><th>cstr mesh</th><th>Removed mesh</th>';
			$selvals .= ', i.cstr_gfx_icon, i.cstr_gfx_mesh, i.cstr_gfx_texture, i.cstr_part, i.cstr_part_mesh, i.removed_mesh';
			break;
		case 'req':
			$comparetype = '<option value="req">Requirements</option><option value="gen">General</option><option value="att">Attack</option><option value="def">Defense</option><option value="gfx">Graphics</option><option value="bon">Bonuses</option><option value="misc">Misc</option>';
			$tableheads .= '<th>Requirement 1</th><th>Requirement 2</th><th>Requirement 3</th>';
			$selvals .= ', CONCAT(i.requirement_1_name,"=",i.requirement_1_value) as requirement1, CONCAT(i.requirement_2_name, "=",i.requirement_2_value) as requirement2, CONCAT(i.requirement_3_name,"=",i.requirement_3_value) as requirement3';
			break;
		case 'bon':
			$comparetype = '<option value="bon">Bonuses</option><option value="gen">General</option><option value="att">Attack</option><option value="def">Defense</option><option value="gfx">Graphics</option><option value="req">Requirements</option><option value="misc">Misc</option>';
			$tableheads .= '<th>Bonus 1</th><th>Bonus 2</th><th>Bonus 3</th>';
			$selvals .= ', CONCAT(i.item_bonus_1_attr,"=",i.item_bonus_1_max) as item_bonus_1, CONCAT(i.item_bonus_2_attr,"=", i.item_bonus_2_max) as item_bonus_2, CONCAT(i.item_bonus_3_attr,"=",i.item_bonus_3_max) as item_bonus_3';
			break;
		case 'misc':
			$comparetype = '<option value="misc">Misc</option><option value="gen">General</option><option value="att">Attack</option><option value="def">Defense</option><option value="gfx">Graphics</option><option value="req">Requirements</option><option value="bon">Bonuses</option>';
			$tableheads .= '<th>Description</th><th>Spawnable</th><th>Skill</th><th>Consume</th><th>Equip</th>';
			$selvals .= ', i.description, i.spawnable, CONCAT_WS(",",s1.name,", ",s2.name,", ",s3.name) as item_skills, i.consume_script, i.equip_script';
			break;
		default:
			$comparetype = '<option value="gen">General</option><option value="att">Attack</option><option value="def">Defense</option><option value="gfx">Graphics</option><option value="req">Requirements</option><option value="bon">Bonuses</option><option value="misc">Misc</option>';
			$tableheads .= '<th>Size</th><th>Weight</th><th>Price</th><th>Container</th><th>Quality</th><th>Slots</th><th>Flags</th>';
			$selvals .= ', i.size, i.weight, i.base_sale_price, (i.container_max_size +"-"+i.container_max_slots) as container, i.item_max_quality, i.valid_slots, i.flags';
   	 }
	 echo('<form method="post" action="index.php?do=compareitems">');
	$categories = PrepSelect('category');
	echo(DrawSelectBox('category', $categories, 'category_id', $cat));
	echo('<select name="compare_type" value="' . $comtype . '">' . $comparetype . '</select>');
    echo('<input type="submit" value="View"></form>');
	
	$tablerows = '';
	if($cat)
	{
		$query = 'SELECT ' . $selvals . ' FROM item_stats i LEFT JOIN skills AS s1 ON i.item_skill_id_1=s1.skill_id LEFT JOIN skills AS s2 ON i.item_skill_id_2=s2.skill_id LEFT JOIN skills AS s3 ON i.item_skill_id_3=s3.skill_id WHERE category_id='.$cat;
		$result = mysql_query2($query);
	    while ($row=mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$tablerows .= '<tr>';
			
			foreach($row as $r)
			{
				$tablerows .= '<td>' . $r . '</td>';
			}
			$tablerows .= '</tr>';
		}
	}
    echo '<table border="1" class="top">';
    echo '<tr>'.$tableheads.'</tr>';
	echo($tablerows . '</table>');
  }else{
    echo '<p class="error">You are not authorized to use these functions!</p>';
  }
}
?>
