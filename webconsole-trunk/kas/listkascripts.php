
<SCRIPT language=javascript>

function confirmDelete()
{
    return confirm("Are you sure you want to delete this KA?");
}

</SCRIPT>

<?

function listkascripts(){

    checkAccess('quest', '', 'read');

    $mode = $_GET['mode'];

	$query = "select id, script from quest_scripts where quest_id=-1 order by id";
	$result = mysql_query2($query);
	echo '<table border="1"><tr><td><b>ID:</b></td><td><b>Name:</b></td><td><b>Action</b></td></tr>';
	while ($line = mysql_fetch_array($result, MYSQL_NUM)){
        // search name
		$script = $line[1];
		$cutat = strpos($script, "\n");
                $script = substr($script,$cutat+1);
		$cutat = strpos($script, ":");
		$area = substr($script,0,$cutat);

		echo "<tr><td> $line[0] </td><td> $area </td>";

		echo "<td><FORM ACTION=index.php?page=editkascript METHOD=POST>";
		echo "<INPUT TYPE=HIDDEN NAME=id VALUE=$line[0]>";
		echo "<INPUT TYPE=SUBMIT NAME=Submit VALUE=Edit>";
		echo '</FORM>';
		echo "<FORM ACTION=index.php?page=questscript_actions METHOD=POST onsubmit=\"return confirmDelete()\">";
		echo "<INPUT TYPE=HIDDEN NAME=operation VALUE=deletekascript>";
		echo "<INPUT TYPE=HIDDEN NAME=id VALUE=$line[0]>";
		echo "<INPUT TYPE=SUBMIT NAME=Submit VALUE=Delete>";
		echo '</FORM></td></tr>';
	}
	echo "</table>";

	echo "<br><br><b>Add a new KA script</b>";
	echo "<FORM ACTION=index.php?page=questscript_actions METHOD=POST>";
	echo "<INPUT TYPE=HIDDEN NAME=operation VALUE=newka>";
	echo "<textarea name=script rows=25 cols=80 wrap=virtual></textarea><br>";
	echo "<INPUT TYPE=SUBMIT NAME=Submit VALUE=Add>";

}
  
?>
