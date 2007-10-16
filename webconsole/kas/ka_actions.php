<?
function ka_actions(){
?>
<HTML>
<BODY>


<?PHP

 
// gets operation to perform
$operation = $_GET['operation'];

/**
 * delete a synonym
 */
if ($operation == 'deleteka'){
	$ka = $_POST['ka']; 
	// delete all responses
	$query = "select id from npc_triggers where area='$ka'";
	$result = mysql_query2($query);
	while ($line = mysql_fetch_array($result, MYSQL_NUM)){
		$query2 = "delete from npc_responses where trigger_id=$line[0]";
		echo $query2;
		$result2 = mysql_query2($query2);
	} 
	// delete all triggers
	$query = "delete from npc_triggers where area='$ka'";
	$result = mysql_query2($query); 
	// redirect
	?><SCRIPT language="javascript">
          document.location = "index.php?page=listkas";
       </script>
    <?PHP

	/**
	 * add a ka
	 */
}else if ($operation == 'createka'){
	$ka = $_POST['ka']; 
	// get trigger id
	$newtriggerid = getNextId("npc_triggers", "id"); 
	// insert trigger
	$query = "insert into npc_triggers (id,trigger_text,prior_response_required, area) values ($newtriggerid,'newka $ka', 0, '$ka')";
	$result = mysql_query2($query); 
	// insert response
	// redirect
	?><SCRIPT language="javascript">
          document.location = "index.php?page=listkas";
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
