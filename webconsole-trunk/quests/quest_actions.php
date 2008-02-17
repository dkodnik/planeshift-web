<?php
function quest_actions(){ 
	// gets operation to perform
	$operation = $_POST['operation'];

	/**
	 * creates a new Quest
	 */
	if ($operation == 'createquest'){
		$name = $_POST['name'];
		$description = $_POST['description'];
		$steps = $_POST['steps'];
	    $type = $_POST['type'];

		// is there another way to get a new ID?
		$query_string = "select max(id) from quests";
		$result = mysql_query2($query_string);
		$line = mysql_fetch_array($result, MYSQL_NUM);
		$newid = $line[0] + 1;
		$masterid = $newid;
		echo "new ID: $newid";

        if ($type=="old") {
    		$query_string = "insert into quests values($newid, '$name', '$description',1,0,0,0,0,0)";
    		$result = mysql_query2($query_string); 
    		// insert steps
    		for ($i = 0; $i < $steps; $i++){
    		
    			$newid++;
    			$query_string = "insert into quests values($newid, 'stepname$i', 'stepdescription$i',0,0,$masterid,$i,0,0)";
    			$result = mysql_query2($query_string);
    			if(mysql_errno() != 0){
    				echo $query_string . "\n<BR><BR>";
    				echo mysql_errno() . ": " . mysql_error();
    				exit();
    			}
    		} 

		// redirect to view quest page
		?>

    <SCRIPT language="javascript">
      document.location = "index.php?page=viewquest&id=<?=$masterid?>";
    </script>

<?PHP

    	} else {
    		$query_string = "insert into quests values($newid, '$name', '$description',1,0,0,0,0,0)";
    		$result = mysql_query2($query_string); 

    		$query_string = "insert into quest_scripts values($newid, 'Newbie','$name', '')";
    		$result = mysql_query2($query_string); 

		// redirect to view quest page
		?>

    <SCRIPT language="javascript">
      document.location = "index.php?page=viewquestscript&id=<?=$masterid?>";
    </script>

<?PHP

        }

	}
	/**
	 * Adds one step to a quest
	 */
	if ($operation == 'addstep'){
		$masterid = $_POST['id'];

		$query_string = "select max(id) from quests";
		$result = mysql_query2($query_string);
		$line = mysql_fetch_array($result, MYSQL_NUM);
		$newid = $line[0] + 1;

		$query_string = "select max(minor_step_number) from quests where master_quest_id=" . $masterid;
		$result = mysql_query2($query_string);
		$line = mysql_fetch_array($result, MYSQL_NUM);
		$minorstep = $line[0] + 1;

		$query_string = "insert into quests values($newid, 'stepname$minorstep', 'stepdescription$minorstep',0,0,$masterid,$minorstep,0,0)";
		$result = mysql_query2($query_string);

		?>
    <SCRIPT language="javascript">
      document.location = "index.php?page=viewquest&id=<?=$masterid?>";
    </script>

<?PHP

		/**
		 * Assigns one quest step to an NPC
		 */
	}else if ($operation == 'assign'){
		$masterid = $_POST['masterid'];
		$questid = $_POST['questid'];
		$npcid = $_POST['npcid'];

		echo "assigning quest $questid to npc $npcid ...<br>"; 
		// search npc name to assign KA
		$query = "select name from characters where npc_master_id !=0 and id=$npcid";
		$result = mysql_query2($query);
		$line = mysql_fetch_array($result, MYSQL_NUM);
		$area = $line[0];

		$query_string = "update npc_triggers set area='$area' where quest_id=$questid";
		//echo "$query_string<br>";
		$result = mysql_query2($query_string);

		?>
    <SCRIPT language="javascript">
      document.location = "index.php?page=viewquest&id=<?=$masterid?>";
    </script>

<?PHP

		/**
		 * Update one quest step
		 */
	}else if ($operation == 'updatestep'){

		$masterid = $_POST['masterid'];
		$questid = $_POST['questid'];
		$name = $_POST['name'];
		$task = $_POST['task'];
    $plockout = $_POST['plockout'];
    $qlockout = $_POST['qlockout'];
    $stepnum = $_POST['stepnum'];
    
		// update quest
		$query = "update quests set name='$name', task='$task', minor_step_number='$stepnum', player_lockout_time='$plockout', quest_lockout_time='$qlockout' where id='$questid'";
		$result = mysql_query2($query);

		?>
    <SCRIPT language="javascript">
      document.location = "index.php?page=viewquest&id=<?=$masterid?>";
    </script>

<?PHP

		/**
		 * Delete one quest step
		 */
	}else if ($operation == 'deletestep'){

    $masterid = $_POST['masterid'];
		$questid = $_POST['id'];

		// delete responses
		$query = "delete from npc_responses where quest_id=$questid";
		$result = mysql_query2($query);

    // delete triggers
		$query = "delete from npc_triggers where quest_id=$questid";
		$result = mysql_query2($query);

    // delete queststep
		$query = "delete from quests where id=$questid";
		$result = mysql_query2($query);

		?>
    <SCRIPT language="javascript">
      document.location = "index.php?page=viewquest&id=<?=$masterid?>";
    </script>

<?PHP

	} 
	// manage another operation here
	echo "Operation $operation not supported.";
}

?>



