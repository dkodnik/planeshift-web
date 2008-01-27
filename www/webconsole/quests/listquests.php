<?
function listquests(){

    checkAccess('quest', '', 'read');

    // selects only quests that do not have a quest_script
	$query = "select quests.id from quests where not exists (select id from quest_scripts where quests.id=quest_scripts.id and master_quest_id=0)";
	$result = mysql_query2($query);
	echo '<table border="1"><tr><td><b>ID:</b></td><td><b>Quest name:</b></td><td><b>Quest description: </b></td><td> </td></tr>';
	while ($line = mysql_fetch_array($result, MYSQL_NUM)){
		echo "<tr><td> $line[0] </td><td> $line[1] </td>";
		echo "<td> $line[2]</td>";

		echo "<td><FORM ACTION=index.php?page=viewquest METHOD=POST>";
		echo "<INPUT TYPE=HIDDEN NAME=id VALUE=$line[0]>";
		echo "<INPUT TYPE=SUBMIT NAME=Submit VALUE=Edit>";
		echo '</FORM></td></tr>';
	}
	echo "</table>";

}
  
?>
