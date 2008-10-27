<?php

define('PSQUEST_DISABLED_QUEST', 0x00000001);

function questscript_actions(){ 
	// gets operation to perform
	$operation = $_POST['operation'];

	/**
	 * creates a new Quest
	 */
	if ($operation == 'createquest'){
		$name = $_POST['name'];
		$description = $_POST['description'];

		printf("You want to create a new quest named %s<br>", $name); 
		// is there another way to get a new ID?
		$query_string = "select max(id) from quest_scripts";
		$result = mysql_query2($query_string);
		$line = mysql_fetch_array($result, MYSQL_NUM);
		$newid = $line[0] + 1;
		$masterid = $newid;
		echo "new ID: $newid";

		$query_string = "insert into quests values($newid, '$name', '$description',0,1,0,0,0,0,'','')";
		$result = mysql_query2($query_string); 

		$query_string = "insert into quest_scripts values($newid, $newid, '')";
		$result = mysql_query2($query_string); 

		// redirect to view quest page
		?>

    <SCRIPT language="javascript">
      document.location = "index.php?page=viewquestscript&id=<?=$masterid?>";
    </script>

<?PHP
		/**
		 * Update one quest step
		 */
	}else if ($operation == 'updatequestscript'){
		// update quest


         if (($_POST['questprereq']!=-1) && ($_POST['prereq']=="")) {
          $prereq = "<pre><completed quest=\"". $_POST['questprereq'] . "\"/></pre>";
        } else 
          $prereq = $_POST['prereq'];

        $flags = $_POST['qdisableflag']? PSQUEST_DISABLED_QUEST : 0;
        
		$query = "update quests set name='{$_POST['name']}', task='{$_POST['description']}', category='{$_POST['category']}', player_lockout_time='{$_POST['plock']}', quest_lockout_time='{$_POST['qlock']}', prerequisite='{$prereq}', flags='{$flags}' where id='{$_POST['id']}'";
		$result = mysql_query2($query);

		$query = "update quest_scripts set script='{$_POST['script']}' where id='{$_POST['id']}'";
		$result = mysql_query2($query);
		?>
    <SCRIPT language="javascript">
      document.location = "index.php?page=listquestscripts";
    </script>

<?PHP

}	

	// manage another operation here
	echo "Operation $operation not supported.";
}

?>



