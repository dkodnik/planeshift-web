<?PHP
function trainers_actions(){ 
	// gets operation to perform
	$operation = $_GET['operation'];

	/**
	 * remove a category
	 */
	if ($operation == 'remove'){
		$skill_id = $_POST['skill_id'];
		$player_id = $_POST['player_id']; 
		// delete named script
		$query = "delete from trainer_skills where skill_id=$skill_id AND player_id=$player_id";
		echo "$query";
		$result = mysql_query2($query);

		/**
		 * add category
		 */
	}else if ($operation == 'add'){
		$skill_id = $_POST['skill_id'];
		$player_id = $_POST['player_id'];
		$min_rank = $_POST['min_rank'];
		$max_rank = $_POST['max_rank'];
		$min_faction = $_POST['min_faction'];
		// insert script
		$query = "insert into trainer_skills values($player_id,$skill_id,$min_rank,$max_rank,$min_faction)";
		//echo "$query";
		$result = mysql_query2($query);
	}else{ 
		// manage another operation here
		echo "Operation $operation not supported.";
	} 
	// redirect
	?>
	<SCRIPT language="javascript">
	        document.location = "index.php?page=listtrainers";
	</script>
<?
}

?>
