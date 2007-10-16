<?PHP
function view_characters(){

	checkAccess('characters', '', 'read');

	include("effects.php");
	$accp = ($_GET['page'] == "view_accounts");
	
	if ($_GET['character_id']!=''){
		$sql="select * from characters where id='".$_GET['character_id']."'";	
	}
	else if ($accp){	
		$sql="select * from characters where account_id='".$_GET['account_id']."'";	
	}
	else {$sql="select * from characters";}
	$query = mysql_query2($sql);
	
	$supercln = mysql_fetch_array(
					mysql_query2("SELECT id FROM accounts WHERE username = 'superclient'"),
					MYSQL_NUM);
	$supercln = $supercln[0];
	

	if($accp)
		echo "<table border='0'><tr><td width='50'><td>";
		
	echo "<table border='0' cellspacing='0'  cellpadding='0' width=\"1100\">";
	echo "<tr><td colspan='37'><b>Characters";
	if($accp)
		echo " belonging to account ".$_GET['account_id'];
		
	echo "</b></td></tr>";
	echo "<tr height='1'>";
	echo "<td width='50'><b>ID</b></td>";
	echo "<td width='200'><b>Firstname</b></td>";
	echo "<td width='200'><b>Lastname</b></td>";
	echo "<td width='50'><b>NPC</b></td>";
	echo "<td width='200'><b>Guild</b></td>";
	echo "<td width='200'><b>Account</b></td>";
	echo "<td width='200'><b>Total time connected</b></td>";
	echo "</tr>";	
		
	while ( $temp= mysql_fetch_array($query)){
		
		
		$sql="select username,id from accounts where id='".$temp['account_id']."'";
		$temp2= mysql_fetch_array(mysql_query2($sql),MYSQL_ASSOC);
		
		$sql="select name  from guilds where id='".$temp['guild_member_of']."'";
		$temp3= mysql_fetch_array(mysql_query2($sql),MYSQL_ASSOC);
		
		//echo"<tr><td>".$temp['id']."</td><td><a href='index.php?page=viewnpc&id=".$temp['id']."'>".$temp['name']."</a></td><td>".$temp['lastname']."</td><td>".$temp['racegender_id']."</td><td>".$temp['base_strength']."</td><td>".$temp['base_agility']."</td><td>".$temp['base_endurance']."</td><td>".$temp['base_intelligence']."</td><td>".$temp['base_will']."</td><td>".$temp['base_charisma']."</td><td>".$temp['mod_hitpoints']."/".$temp['base_hitpoints_max']."</td><td>".$temp['mod_mana']."/".$temp['base_mana_max']."</td><td>".$temp['mod_fatigue']."/".$temp['base_fatigue_max']."</td><td>".$temp['money_circles']." : ".$temp['money_octas']." : ".$temp['money_hexas']." : ".$temp['money_trias']."</td><td>".$temp['bank_money_circles']." : ".$temp['bank_money_octas']." : ".$temp['bank_money_hexas']." : ".$temp['bank_money_trias']."</td><td>".$temp['loc_sector_id']."</td><td>".$temp['loc_x']."</td><td>".$temp['loc_y']."</td><td>".$temp['loc_z']."</td><td>".$temp['loc_yrot']."</td><td><a href='index.php?page=list_guilds&operation=properties&guild=".$temp['guild_member_of']."'>".$temp3['name']."</a></td><td>".$temp['guild_level']."</td><td>".$temp['guild_points']."</td><td>".$temp['guild_public_notes']."</td><td>".$temp['guild_private_notes']."</td><td>".$temp['faction_standings']."</td><td>".$temp['progression_script']."</td><td>".$temp['npc_spawn_rule']."</td><td>".$temp['npc_master_id']."</td><td>".$temp['npc_impervious_ind']."</td><td><a href='index.php?page=view_accounts&account_id=".$temp['account_id']."'>".$temp2['username']."</a></td><td>".$temp['time_connected_sec']."</td><td>".$temp['npc_addl_loot_category_id']."</td><td>".$temp['experience_points']."</td><td>".$temp['progression_points']."</td><td>".$temp['duel_points']."</td><td>".$temp['description']."</td></tr>";
		// Mouse over effect
		echo "<tr $mouse_over>";
		// ID
		echo "<td>".$temp['id']."</td>";
		// Firstname
		echo "<td><a href='index.php?page=viewnpc&id=".$temp['id']."'>".$temp['name']."</a></td>";
		// Lastname
		echo "<td>".$temp['lastname']."</td>";
			
		// NPC?
		if($temp['account_id'] == $supercln)
			echo "<td>Yes</td>";
		else
			echo "<td>No</td>";
			
		// Guild
		echo "<td><a href='index.php?page=list_guilds&operation=properties&guild=".$temp['guild_member_of']."'>".$temp3['name']."</a></td>";
		
		// Account
		echo "<td><a href='index.php?page=view_accounts&account_id=".$temp2['id']."'>".$temp2['username']."</a></td>";
		// Time connected
		$tt = $temp['time_connected_sec'];

        $days = (int)($tt / (60*60*24));
        $tt -= $days*60*60*24;

        $hours = (int)($tt / (60*60));
        $tt -= $hours*60*60;

        $mins = (int)($tt / 60);		
		
		echo "<td>$mins m, $hours h, $days days"."</td>";
		echo "</tr>";
	}		
	echo"</table>";
	if($accp)
		echo "</td></tr></table>";
}
?>