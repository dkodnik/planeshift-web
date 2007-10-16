<?
function trait_actions(){
?>
<HTML>
<BODY>


<?PHP


// gets operation to perform
$operation = $_GET['operation'];

	/**
	 * update script
	 */
if ($operation == 'update'){
	$id = $_POST['id'];
	$next_trait = $_POST['next_trait'];
	$race_id = $_POST['race_id'];
	$location = $_POST['location'];
	$name = $_POST['name'];
	$only_npc = $_POST['only_npc'];
	$cstr_id_mesh = $_POST['cstr_id_mesh'];
	$cstr_id_material = $_POST['cstr_id_material'];
	$cstr_id_texture = $_POST['cstr_id_texture'];

	// insert script
	$query = "update traits set next_trait='$next_trait', race_id='$race_id',only_npc='$only_npc',location='$location',name='$name' ,cstr_id_mesh='$cstr_id_mesh' ,cstr_id_material='$cstr_id_material' ,cstr_id_texture='$cstr_id_texture' where id='$id'";
	$result = mysql_query2($query); 
	// redirect
	?><SCRIPT language="javascript">
          document.location = "index.php?page=list_traits";
       </script>
    <?PHP

}else if ($operation == 'add'){
	$id = $_POST['id'];
	$next_trait = $_POST['next_trait'];
	$race_id = $_POST['race_id'];
	$only_npc = $_POST['only_npc'];
	$location = $_POST['location'];
	$name = $_POST['name'];
	$cstr_id_mesh = $_POST['cstr_id_mesh'];
	$cstr_id_material = $_POST['cstr_id_material'];
	$cstr_id_texture = $_POST['cstr_id_texture'];

	// insert script
	$query = "insert into traits (next_trait,race_id,only_npc,location,name,cstr_id_mesh,cstr_id_material,cstr_id_texture) values ('$next_trait','$race_id','$only_npc','$location','$name','$cstr_id_mesh','$cstr_id_material','$cstr_id_texture')";
	$result = mysql_query2($query); 
	// redirect
	?><SCRIPT language="javascript">
          document.location = "index.php?page=list_traits";
       </script>
    <?PHP

}else if ($operation == 'delete'){
	$id = $_POST['id'];
	// insert script
	$query = "delete from traits where id='$id'";
	$result = mysql_query2($query); 
	// redirect
	?><SCRIPT language="javascript">
          document.location = "index.php?page=list_traits";
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
