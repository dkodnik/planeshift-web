<?
function lootcategories_actions(){
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
	$cat_id = $_POST['cat_id']; 
	$item_stat_id = $_POST['item_stat_id']; 
	// delete named loot
	$query = "delete from loot_rule_details where loot_rule_id='$cat_id' and item_stat_id='$item_stat_id'";
	$result = mysql_query2($query); 
	// redirect
	?><SCRIPT language="javascript">
          document.location = "index.php?page=listlootcategories";
      </script>
    <?PHP

	/**
	 * create loot
	 */
}else if ($operation == 'create'){
	$cat_name = $_POST['cat_id'];

	// insert loot_rule
	$query = "insert into loot_rules (name) values ('$cat_name') ;";
	$result = mysql_query2($query); 

	// redirect
	?><SCRIPT language="javascript">
          document.location = "index.php?page=listlootcategories";
       </script>
    <?PHP

	/**
	 * add item to loot
	 */
}else if ($operation == 'add'){
	$cat_name = $_POST['cat_id'];
	$item_stat_id = $_POST['item_stat_id'];
	$probability = $_POST['probability']; 
	$min_money = $_POST['min_money']; 
	$max_money = $_POST['max_money']; 
	$randomize = $_POST['randomize']; 

	// insert loot_details
	$query = "insert into loot_rule_details values ('','$cat_name','$item_stat_id','$probability','$min_money','$max_money','$randomize')";
	$result = mysql_query2($query); 
	// redirect
	?><SCRIPT language="javascript">
          document.location = "index.php?page=listlootcategories";
       </script>
    <?PHP
	/**
	 * update loot
	 */
}else if ($operation == 'update'){
	$cat_id = $_POST['cat_id'];
	$item_stat_id = $_POST['item_stat_id'];
	$probability = $_POST['probability']; 
	$min_money = $_POST['min_money']; 
	$max_money = $_POST['max_money'];
	$randomize = $_POST['randomize']; 
	// insert loot
	$query = "update loot_rule_details set probability='$probability',min_money='$min_money',max_money='$max_money',randomize='$randomize' where loot_rule_id='$cat_id' and item_stat_id='$item_stat_id'";
	$result = mysql_query2($query); 
	// redirect
	?><SCRIPT language="javascript">
          document.location = "index.php?page=listlootcategories";
       </script>
    <?PHP

}else if ($operation == 'remove_npc'){
	$cat_id = $_POST['cat_id'];
	$npc_id = $_POST['npc_id'];
	// insert loot
	$query = "update characters set npc_addl_loot_category_id='-1' where id='$npc_id'";
	$result = mysql_query2($query); 
	// redirect
	?><SCRIPT language="javascript">
          document.location = "index.php?page=listlootcategories";
       </script>
    <?PHP

}else if ($operation == 'add_npc'){
	$cat_id = $_POST['cat_id'];
	$npc_id = $_POST['npc_id'];
	// insert loot
	$query = "update characters set npc_addl_loot_category_id='$cat_id' where id='$npc_id'";
	$result = mysql_query2($query); 
	// redirect
	?><SCRIPT language="javascript">
          document.location = "index.php?page=listlootcategories";
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
