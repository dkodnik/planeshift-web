<?
function skill_actions(){
?>
<HTML>
<BODY>


<?PHP


    checkAccess('main', '', 'read');
    
// gets operation to perform
$operation = $_GET['operation'];

	/**
	 * update script
	 */
if ($operation == 'update'){
	$id = $_POST['id'];
	$description = $_POST['description']; 
	$practice_factor = $_POST['practice_factor'];
    $mental_factor = $_POST['mental_factor'];
	$price = $_POST['price']; 
	$base_rank_cost = $_POST['base_rank_cost']; 
	// insert script
	$query = "update skills set description='$description', practice_factor='$practice_factor',mental_factor='$mental_factor',price='$price',base_rank_cost='$base_rank_cost' where skill_id='$id'";
	$result = mysql_query2($query); 
	// redirect
	?><SCRIPT language="javascript">
          document.location = "index.php?page=listskills";
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
