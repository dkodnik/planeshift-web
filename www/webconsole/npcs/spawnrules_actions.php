<?
function spawnrules_actions(){
?>
<HTML>
<BODY>


<?PHP


// gets operation to perform
$operation = $_GET['operation'];

/**
 * remove a loot
 */
if ($operation == 'remove'){
	$rule_id = $_POST['rule_id']; 
	$range_id = $_POST['range_id']; 
	// delete named loot
	$query = "delete from npc_spawn_ranges where npc_spawn_rule_id='$rule_id' and id='$range_id'";
	$result = mysql_query2($query); 
	// redirect
	?><SCRIPT language="javascript">
          document.location = "index.php?page=listspawnrules";
      </script>
    <?PHP

	/**
	 * create loot
	 */
}else if ($operation == 'create'){
	// insert loot_rule
	error_reporting(E_ALL);
	$query = "insert into npc_spawn_rules (id, name) values ('','{$_POST['rule_name']}')";
	$result = mysql_query2($query); 
	$id = mysql_insert_id();
	// insert loot_details
	$query = "insert into npc_spawn_ranges values ('','{$id}','{$_POST['x1']}','{$_POST['y1']}','{$_POST['z1']}','{$_POST['x2']}','{$_POST['y2']}','{$_POST['z2']}','{$_POST['cstr_id_spawn_sector']}', '{$_POST['range_type_code']}')";
	$result = mysql_query2($query); 
	// redirect
	?><SCRIPT language="javascript">
          document.location = "index.php?page=listspawnrules";
       </script>
    <?PHP

	/**
	 * add item to loot
	 */
}else if ($operation == 'add'){
	// insert loot_details
	$query = "insert into npc_spawn_ranges values ('','{$_POST['rule_id']}','{$_POST['x1']}','{$_POST['y1']}','{$_POST['z1']}','{$_POST['x2']}','{$_POST['y2']}','{$_POST['z2']}','{$_POST['cstr_id_spawn_sector']}', '{$_POST['range_type_code']}')";
	$result = mysql_query2($query); 
	// redirect
	?><SCRIPT language="javascript">
          document.location = "index.php?page=listspawnrules";
       </script>
    <?PHP
	/**
	 * update range
	 */
}else if ($operation == 'update'){
	$rule_id = $_POST['rule_id'];
	$range_id = $_POST['range_id'];
	// insert loot
	$query = "update npc_spawn_ranges set x1='{$_POST['x1']}',y1='{$_POST['y1']}',z1='{$_POST['z1']}',x2='{$_POST['x2']}',y2='{$_POST['y2']}',z2='{$_POST['z2']}',z2='{$_POST['z2']}', range_type_code='{$_POST['range_type_code']}' where npc_spawn_rule_id='$rule_id' and id='$range_id'";
	$result = mysql_query2($query); 
	// redirect
	?><SCRIPT language="javascript">
          document.location = "index.php?page=listspawnrules";
       </script>
    <?PHP

}else if ($operation == 'updaterule'){
	// update rule
	$query = "update npc_spawn_rules set 
					min_spawn_time='{$_POST['min_spawn_time']}',
					max_spawn_time='{$_POST['max_spawn_time']}',
					substitute_spawn_odds='{$_POST['substitute_spawn_odds']}',
					substitute_player='{$_POST['substitute_player']}',
					fixed_spawn_x='{$_POST['fixed_spawn_x']}',
					fixed_spawn_y='{$_POST['fixed_spawn_y']}',
					fixed_spawn_z='{$_POST['fixed_spawn_z']}', 
					fixed_spawn_rot='{$_POST['fixed_spawn_rot']}', 
					fixed_spawn_sector='{$_POST['fixed_spawn_sector']}', 
					fixed_spawn_instance='{$_POST['fixed_spawn_instance']}',
					loot_category_id='{$_POST['loot_category_id']}', 
					dead_remain_time='{$_POST['dead_remain_time']}'
				where id='{$_POST['rule_id']}'";
	$result = mysql_query2($query); 
	// redirect
	?><SCRIPT language="javascript">
          document.location = "index.php?page=listspawnrules";
       </script>
    <?PHP

}else if ($operation == 'remove_npc'){
	$rule_id = $_POST['rule_id'];
	$npc_id = $_POST['npc_id'];
	// insert loot
	$query = "update characters set npc_spawn_rule='-1' where id='$npc_id'";
	$result = mysql_query2($query); 
	// redirect
	?><SCRIPT language="javascript">
          document.location = "index.php?page=listspawnrules";
       </script>
    <?PHP

}else if ($operation == 'add_npc'){
	$rule_id = $_POST['rule_id'];
	$npc_id = $_POST['npc_id'];
	// insert loot
	$query = "update characters set npc_spawn_rule='$rule_id' where id='$npc_id'";
	$result = mysql_query2($query); 
	// redirect
	?><SCRIPT language="javascript">
          document.location = "index.php?page=listspawnrules";
       </script>
    <?PHP

}else{ 
	// manage another operation here
	echo "Operation $operation not supported.";
}

?>



</body>
</html>

<?
}
?>
