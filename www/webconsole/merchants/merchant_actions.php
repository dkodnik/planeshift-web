<?PHP
function merchant_actions(){ 
	// gets operation to perform
	$operation = $_GET['operation'];

	/**
	 * remove a category
	 */
	if ($operation == 'remove'){
		$category_id = $_POST['category_id'];
		$player_id = $_POST['player_id']; 
		// delete named script
		$query = "delete from merchant_item_categories where category_id='$category_id' AND player_id='$player_id'";
		$result = mysql_query2($query);

		/**
		 * add category
		 */
	}else if ($operation == 'add'){
		$category_id = $_POST['category_id'];
		$player_id = $_POST['player_id']; 
		// insert script
		$query = "insert into merchant_item_categories values('$player_id','$category_id')";
		$result = mysql_query2($query);
	}else{ 
		// manage another operation here
		echo "Operation $operation not supported.";
	} 
	// redirect
	?>
	<SCRIPT language="javascript">
	        document.location = "index.php?page=listmerchants";
	</script>
<?
}

?>