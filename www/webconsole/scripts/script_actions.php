<?
function script_actions(){
?>
<HTML>
<BODY>


<?PHP


// gets operation to perform
$operation = $_GET['operation'];

/**
 * delete a script
 */
if ($operation == 'delete'){
	$name = $_POST['name']; 
	// delete named script
	$query = "delete from progression_events where name='$name'";
	$result = mysql_query2($query); 
	// redirect
	?><SCRIPT language="javascript">
          document.location = "index.php?page=listscripts";
      </script>
    <?PHP

	/**
	 * create script
	 */
}else if ($operation == 'create'){
	$name = $_POST['name'];
	$script = $_POST['event_script']; 
	// insert script
	$query = "insert into progression_events values ('$name','$script')";
	$result = mysql_query2($query); 
	// redirect
	?><SCRIPT language="javascript">
          document.location = "index.php?page=listscripts";
       </script>
    <?PHP

	/**
	 * update script
	 */
}else if ($operation == 'update'){
	$name = $_POST['name'];
	$script = $_POST['event_script']; 
	// insert script
	$query = "update progression_events set event_script='$script' where name='$name'";
	$result = mysql_query2($query); 
	// redirect
	?><SCRIPT language="javascript">
          document.location = "index.php?page=listscripts";
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