<?
function listscripts($type){

	?>


<SCRIPT language=javascript>

function confirmDelete()
{
    return confirm("Are you sure you want to delete this script?");
}

</SCRIPT>

<?PHP

    checkAccess('main', '', 'read');
    
	$query = "select name, event_script from progression_events";
    if ($type=="base") {
        $query = $query . " where name not like 'randomitem%' and name not like 'simpleitem_%' and ";
        $query = $query . "name not like 'charcreate_%' and name not like 'PATH_%' and name not like 'cast %' ";
        $query = $query . "and name not like 'apply %' ";
    } else if ($type=="loot") {
        $query = $query . " where name like 'randomitem%' ";
    } else if ($type=="items") {
        $query = $query . " where name like 'simpleitem_%' ";
    } else if ($type=="charcreate") {
        $query = $query . " where name like 'charcreate_%' and name like 'PATH_%'";
    } else if ($type=="spells") {
        $query = $query . " where name like 'cast %' or name like 'apply %'";
    }
    $result = mysql_query2($query);

	echo '  <TABLE BORDER=1>';
	echo '  <TH> Name </TH> <TH> Script </TH> <TH> Functions </TH>';

	while ($line = mysql_fetch_array($result, MYSQL_NUM)){
		echo '<TR>';
		echo '<FORM ACTION=index.php?page=script_actions&operation=update METHOD=POST>';
		echo "<TD>$line[0]</TD>";
		echo "<TD><INPUT TYPE=hidden NAME=name VALUE=\"$line[0]\"><textarea name=event_script rows=3 cols=50>$line[1]</textarea></TD>";
		echo '<TD><INPUT TYPE=SUBMIT NAME=submit VALUE=Update></FORM>';
		echo "<FORM ACTION=index.php?page=script_actions&operation=delete METHOD=POST onsubmit=\"return confirmDelete()\">";
		echo "<INPUT TYPE=hidden NAME=name VALUE=\"$line[0]\">";
		echo '<INPUT TYPE=SUBMIT NAME=submit VALUE=Delete>';
		echo '</FORM></TD></TR>';
	}
	echo '<TR><TD>';
	echo '<FORM ACTION=index.php?page=script_actions&operation=create METHOD=POST>';
	echo '<INPUT TYPE=text NAME=name></TD>';
	echo '<TD><textarea name=event_script rows=3 cols=50></textarea></TD>';
	echo '<TD><INPUT TYPE=SUBMIT NAME=submit VALUE=Create>';
	echo '</FORM></TD></TR>';

	echo '</TABLE><br><br>';

	echo '<br><br>';
}

?>
  