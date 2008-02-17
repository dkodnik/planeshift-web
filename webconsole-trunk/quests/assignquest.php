<?PHP
function assignquest(){
	$masterid = $_POST['masterid'];
	if ($masterid == ''){
		$masterid = $_GET['masterid'];
	}
	$stepid = $_POST['stepid'];

	$query2 = "select q.id, q.name, q.task, q.minor_step_number from quests q where id=$stepid";
	//echo "$query2<br>";
	$result2 = mysql_query2($query2);
	$line2 = mysql_fetch_array($result2, MYSQL_NUM);

	echo "Selected quest step: $line2[1] <br><br>";

  // check if there are triggers defined for this step
	$query3 = "select count(*) from npc_triggers where quest_id=$line2[0]";
	//echo "$query3<br>";
	$result3 = mysql_query2($query3);
	$line3 = mysql_fetch_array($result3, MYSQL_NUM);
	$numtrig = $line3[0];
	if ($numtrig==0) {
	  echo "This step does not contain any triggers. You must first add triggers before you can assign it. Use the 'EDITSTEP' button in the edit quest page.<br><br>";
	  echo "<A HREF='index.php?page=viewquest&id=".$masterid."'>Back to this quest</A><br><br>";
	  return;
	}


	echo "Assign to NPC: <br>"; 
	// search available NPCs
	$query = "select id, name from characters where npc_master_id !=0";
	$result = mysql_query2($query);

	echo "<form action=index.php?page=quest_actions METHOD=POST>";

	while ($line = mysql_fetch_array($result, MYSQL_NUM)){
		$npcid = $line[0];
		$npcname = $line[1];

		echo "<INPUT NAME=npcid TYPE=radio VALUE=$npcid><b>$npcname [$npcid]</b> <BR>";
	}
	echo "<INPUT TYPE=hidden NAME=masterid VALUE=$masterid>";
	echo "<INPUT TYPE=hidden NAME=operation VALUE=assign>";
	echo "<INPUT TYPE=hidden NAME=questid VALUE=$stepid>";
	echo "<br><INPUT TYPE=SUBMIT NAME=status VALUE=\"ASSIGN\"></form>";

	echo "<br><br><A HREF='index.php?page=viewquest&id=".$masterid."'>Back to quests</A>";
}

?>