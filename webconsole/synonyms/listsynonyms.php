<?
function listsynonyms(){

	?>

<SCRIPT language=javascript>

function confirmDelete()
{
    return confirm("Are you sure you want to delete all synonyms?");
}

</SCRIPT>
<?PHP

    checkAccess('main', '', 'read');

	echo "<FORM ACTION=index.php?page=syn_actions&operation=add METHOD=POST>";
	echo "Word: <INPUT TYPE=text NAME=word> Synonym: <INPUT TYPE=text NAME=syn> More general (Synonym must be empty): <INPUT TYPE=text NAME=moregen>";
	echo "<INPUT TYPE=SUBMIT NAME=submit VALUE=Add>";
	echo '</FORM>';
	echo '<br><br>';

	echo "<FORM ACTION=index.php?page=syn_actions&operation=delall METHOD=POST>";
	echo "<INPUT TYPE=SUBMIT NAME=submit VALUE=\"Delete All\" onClick=\"return confirmDelete()\">";
	echo '</FORM>';
	echo '<br><br>';

	echo "<table border=1><tr><td><FORM ACTION=index.php?page=syn_actions&operation=uploadfile ENCTYPE=\"multipart/form-data\" METHOD=POST> ";
	echo "Upload file with synonyms: <INPUT TYPE=file NAME=file><br><br>";
	echo "Format is WORD,SYNONYM  one each line ";
	echo '<INPUT TYPE=SUBMIT NAME=submit VALUE=Upload>';
	echo '</FORM></td></tr></table>';
	echo '<br><br>';

	echo'<table><tr><td valign="top">';
	$query = "select distinct distinct synonym_of from npc_synonyms order by synonym_of, word";
	$result = mysql_query2($query);
	echo'<table border="1">';
	while ($line = mysql_fetch_array($result , MYSQL_NUM)){
		$query = "select more_general from npc_synonyms where word='". $line[0] ."' and synonym_of=''";
		$moregeneral="";
		$moregeneralresult = mysql_query2($query);
		if($moregeneralresult)
			$moregeneral=$moregeneralresult[0];
		echo"<tr><td><a href='index.php?page=listsynonyms&synonym_of=" . $line[0] . "'> $line[0]</a></td><td>$moregeneral</td></tr>";
	}
	echo'</table></td><td valign="top">';

	$query = "select word, synonym_of from npc_synonyms where synonym_of ='" . $_GET['synonym_of'] . "' order by synonym_of, word";
	$result = mysql_query2($query);
	echo '  <TABLE BORDER=1>';
	if(mysql_num_rows($result) > 0)
		echo "   <TH> SYNONYMS of '" . $_GET['synonym_of'] . "' </TH><TH>Function</TH>";

	while ($line = mysql_fetch_array($result, MYSQL_NUM)){
		echo "<TR><TD>$line[0] </TD>";
		echo "<TD><FORM ACTION=index.php?page=syn_actions&operation=delete METHOD=POST >";
		echo "<INPUT TYPE=hidden NAME=word VALUE=\"$line[0]\">";
		echo "<INPUT TYPE=hidden NAME=syn VALUE=\"$line[1]\">";
		echo "<INPUT TYPE=SUBMIT NAME=submit VALUE=Delete>";
		echo "</FORM></TD></TR>";
	}
	echo '</TABLE></td></tr></table><br><br>';
}

?>