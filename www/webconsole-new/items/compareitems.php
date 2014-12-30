<script language="javascript" type="text/javascript">
function comparesortby(val)
{
	if(document.getElementById('comparesort').value == val)
	{
		document.getElementById('comparesort').value=val + ' desc';
		document.getElementById('compareform').submit();
	}
	else
	{
		document.getElementById('comparesort').value=val;
		document.getElementById('compareform').submit();
	}
}
</script>
<?php
function compareitems(){
  if(checkaccess('items', 'read')){
	$cat = '';
	if (isset($_POST['category_id'])){
      $cat = $_POST['category_id'];
    }
	$comparetype = '';
	$comtype = '';
	if (isset($_POST['compare_type']))
	{
	  $comtype = $_POST['compare_type'];
	}
	else
	{
		$comtype = 'gen';
	}
	$comsort = '';
	if (isset($_POST['comparesort']))
	{
	  $comsort = $_POST['comparesort'];
	}
	else
	{
		$comsort = 'name';
	}
	$tableheads = '<th><a href="#" onclick="comparesortby(\'id\');">ID</a></th>
	<th><a href="#" onclick="comparesortby(\'name\');">Name</a></th>';
	$selvals = 'i.id, i.name';
	
	  switch($comtype)
	 {
		 
		case 'att':
			$comparetype = '<option value="att">Attack</option><option value="gen">General</option><option value="def">Defense</option><option value="gfx">Graphics</option><option value="req">Requirements</option><option value="bon">Bonuses</option><option value="misc">Misc</option>';
			$tableheads .= '<th><a href="#" onclick="comparesortby(\'dmg_blunt\');">Blunt</a></th><th><a href="#" onclick="comparesortby(\'dmg_pierce\');">Pierce</a></th><th><a href="#" onclick="comparesortby(\'dmg_slash\');">Slash</a></th><th><a href="#" onclick="comparesortby(\'ammo\');">Ammo</a></th><th><a href="#" onclick="comparesortby(\'weapon_penetration\');">Penetration</a></th><th><a href="#" onclick="comparesortby(\'weapon_range\');">Range</a></th><th><a href="#" onclick="comparesortby(\'weapon_speed\');">Speed</a></th>';
			$selvals .= ', i.dmg_blunt, i.dmg_pierce, i.dmg_slash, i2.name as ammo, i.weapon_penetration, i.weapon_range, i.weapon_speed';
			break;
		case 'def':
			$comparetype = '<option value="def">Defense</option><option value="gen">General</option><option value="att">Attack</option><option value="gfx">Graphics</option><option value="req">Requirements</option><option value="bon">Bonuses</option><option value="misc">Misc</option>';
			$tableheads .= '<th><a href="#" onclick="comparesortby(\'armor_hardness\');">Armor Hardness</a></th><th><a href="#" onclick="comparesortby(\'armorvsweapon_type\');">Armor Vs Weapon</a></th><th><a href="#" onclick="comparesortby(\'weapon_block_targeted\');">Block Targeted</a></th><th><a href="#" onclick="comparesortby(\'weapon_block_untargeted\');">Block Untargeted</a></th><th><a href="#" onclick="comparesortby(\'weapon_counterblock\');">Counter Block</a></th>';
			$selvals .= ', i.armor_hardness, i.armorvsweapon_type, i.weapon_block_targeted, i.weapon_block_untargeted, i.weapon_counterblock';
			break;
		case 'gfx':
			$comparetype = '<option value="gfx">Graphics</option><option value="gen">General</option><option value="att">Attack</option><option value="def">Defense</option><option value="req">Requirements</option><option value="bon">Bonuses</option><option value="misc">Misc</option>';
			$tableheads .= '<th><a href="#" onclick="comparesortby(\'cstr_gfx_icon\');">Icon</a></th><th><a href="#" onclick="comparesortby(\'cstr_gfx_mesh\');">Mesh</a></th><th><a href="#" onclick="comparesortby(\'cstr_gfx_texture\');">Texture</a></th><th><a href="#" onclick="comparesortby(\'cstr_part\');">CSTR Part</a></th><th><a href="#" onclick="comparesortby(\'cstr_part_mesh\');">CSTR Mesh</a></th><th><a href="#" onclick="comparesortby(\'removed_mesh\');">Removed Mesh</a></th>';
			$selvals .= ', i.cstr_gfx_icon, i.cstr_gfx_mesh, i.cstr_gfx_texture, i.cstr_part, i.cstr_part_mesh, i.removed_mesh';
			break;
		case 'req':
			$comparetype = '<option value="req">Requirements</option><option value="gen">General</option><option value="att">Attack</option><option value="def">Defense</option><option value="gfx">Graphics</option><option value="bon">Bonuses</option><option value="misc">Misc</option>';
			$tableheads .= '<th><a href="#" onclick="comparesortby(\'requirement1\');">Requirement 1</a></th><th><a href="#" onclick="comparesortby(\'requirement2\');">Requirement 2</a></th><th><a href="#" onclick="comparesortby(\'requirement3\');">Requirement 3</a></th>';
			$selvals .= ', CONCAT(i.requirement_1_name,"=",i.requirement_1_value) as requirement1, CONCAT(i.requirement_2_name, "=",i.requirement_2_value) as requirement2, CONCAT(i.requirement_3_name,"=",i.requirement_3_value) as requirement3';
			break;
		case 'bon':
			$comparetype = '<option value="bon">Bonuses</option><option value="gen">General</option><option value="att">Attack</option><option value="def">Defense</option><option value="gfx">Graphics</option><option value="req">Requirements</option><option value="misc">Misc</option>';
			$tableheads .= '<th><a href="#" onclick="comparesortby(\'item_bonus_1\');">Bonus 1</a></th><th><a href="#" onclick="comparesortby(\'item_bonus_2\');">Bonus 2</a></th><th><a href="#" onclick="comparesortby(\'item_bonus_3\');">Bonus 3</a></th>';
			$selvals .= ', CONCAT(i.item_bonus_1_attr,"=",i.item_bonus_1_max) as item_bonus_1, CONCAT(i.item_bonus_2_attr,"=", i.item_bonus_2_max) as item_bonus_2, CONCAT(i.item_bonus_3_attr,"=",i.item_bonus_3_max) as item_bonus_3';
			break;
		case 'misc':
			$comparetype = '<option value="misc">Misc</option><option value="gen">General</option><option value="att">Attack</option><option value="def">Defense</option><option value="gfx">Graphics</option><option value="req">Requirements</option><option value="bon">Bonuses</option>';
			$tableheads .= '<th><a href="#" onclick="comparesortby(\'description\');">Description</a></th><th><a href="#" onclick="comparesortby(\'spawnable\');">Spawnable</a></th><th><a href="#" onclick="comparesortby(\'item_skills\');">Skills</a></th><th><a href="#" onclick="comparesortby(\'consume_script\');">Consume</a></th><th><a href="#" onclick="comparesortby(\'equip_script\');">Equip</a></th>';
			$selvals .= ', i.description, i.spawnable, CONCAT_WS(",",s1.name,", ",s2.name,", ",s3.name) as item_skills, i.consume_script, i.equip_script';
			break;
		default:
			$comparetype = '<option value="gen">General</option><option value="att">Attack</option><option value="def">Defense</option><option value="gfx">Graphics</option><option value="req">Requirements</option><option value="bon">Bonuses</option><option value="misc">Misc</option>';
			$tableheads .= '<th><a href="#" onclick="comparesortby(\'size\');">Size</a></th><th><a href="#" onclick="comparesortby(\'weight\');">Weight</a></th><th><a href="#" onclick="comparesortby(\'base_sale_price\');">Price</a></th><th><a href="#" onclick="comparesortby(\'container\');">Container</a></th><th><a href="#" onclick="comparesortby(\'item_max_quality\');">Quality</a></th><th><a href="#" onclick="comparesortby(\'valid_slots\');">Slot</a></th><th><a href="#" onclick="comparesortby(\'flags\');">Flags</a></th>';
			$selvals .= ', i.size, i.weight, i.base_sale_price, (i.container_max_size +"-"+i.container_max_slots) as container, i.item_max_quality, i.valid_slots, i.flags';
   	 }
	 
	 echo('<form method="post" action="index.php?do=compareitems" id="compareform">');
	$categories = PrepSelect('category');
	echo(DrawSelectBox('category', $categories, 'category_id', $cat));
	echo('<select id="comparetype" name="compare_type" value="' . $comtype . '" onchange="document.getElementById(\'comparesort\').value=\'name\'">' . $comparetype . '</select>');
    echo('<input type="hidden" id="comparesort" name="comparesort" value="' . $comsort . '"><input type="submit" value="View"></form>');
	
	$tablerows = '';
	if($cat)
	{
		$query = 'SELECT ' . $selvals . ' FROM item_stats i LEFT JOIN skills AS s1 ON i.item_skill_id_1=s1.skill_id LEFT JOIN skills AS s2 ON i.item_skill_id_2=s2.skill_id LEFT JOIN skills AS s3 ON i.item_skill_id_3=s3.skill_id LEFT JOIN item_stats AS i2 ON i.item_type_id_ammo=i2.id WHERE i.category_id="'.$cat.'" ORDER by ' . $comsort . ', name';
		$result = mysql_query2($query);
	    while ($row=mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$tablerows .= '<tr>';
			$i = 0;
			$id = 0;
			foreach($row as $r)
			{				
				if($i ==0)
				{
					$tablerows .= '<td><a href="index.php?do=listitems&category=' . $cat . '&item=' . $r .'">' . $r . '</a>' . '</td>';
					$id = $r;
				}
				else if($i == 1 && checkaccess('items', 'edit'))
				{
					$tablerows .= '<td>' . $r . ' (<a href="index.php?do=edititem&item=' . $id .'">edit</a>)' . '</td>';
				}
				else
				{
					$tablerows .= '<td>' . $r . '</td>';
				}
				$i++;
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