<?
function listfactions(){

    checkAccess('main', '', 'read');
    
	$query = "select id, faction_name, faction_weight from factions";
	$result = mysql_query2($query);

	echo '  <TABLE BORDER=1>';
	echo '  <TH> ID </TH> ';
	echo '  <TH> Faction </TH> ';
	echo '  <TH> Weight </TH> ';

	while ($line = mysql_fetch_array($result, MYSQL_NUM)){
		echo '<TR>';
		echo '<FORM ACTION=index.php?page=faction_actions&operation=update METHOD=POST>';
		echo "<TD><INPUT TYPE=hidden NAME=id VALUE=\"$line[0]\">$line[0]</TD>";
		echo "<TD>$line[1]</TD>";
		echo "<TD><textarea name=weight rows=1 cols=5>$line[2]</textarea></TD>";
		echo '<TD><INPUT TYPE=SUBMIT NAME=submit VALUE=Update></FORM>';
		echo '</FORM></TD></TR>';
	}
	echo '</TABLE><br><br>';

	echo '<br><br>';
}

?>
  
