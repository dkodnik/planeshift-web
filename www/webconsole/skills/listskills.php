<?
function listskills(){

    checkAccess('main', '', 'read');
    
	$query = "select skill_id, name, description, practice_factor, mental_factor, price, base_rank_cost from skills";
	$result = mysql_query2($query);

	echo '  <TABLE BORDER=1>';
	echo '  <TH> ID </TH> ';
	echo '  <TH> Skill </TH> ';
	echo '  <TH> Description </TH> ';
	echo '  <TH> Practice factor </TH> ';
	echo '  <TH> Mental factor </TH> ';
	echo '  <TH> Price </TH> ';
	echo '  <TH> Base rank cost </TH> ';

	while ($line = mysql_fetch_array($result, MYSQL_NUM)){
		echo '<TR>';
		echo '<FORM ACTION=index.php?page=skill_actions&operation=update METHOD=POST>';
		echo "<TD><INPUT TYPE=hidden NAME=id VALUE=\"$line[0]\">$line[0]</TD>";
		echo "<TD>$line[1]</TD>";
		echo "<TD><textarea name=description rows=3 cols=50>$line[2]</textarea></TD>";
		echo "<TD><textarea name=practice_factor rows=1 cols=5>$line[3]</textarea></TD>";
		echo "<TD><textarea name=mental_factor rows=1 cols=5>$line[4]</textarea></TD>";
		echo "<TD><textarea name=price rows=1 cols=5>$line[5]</textarea></TD>";
		echo "<TD><textarea name=base_rank_cost rows=1 cols=5>$line[6]</textarea></TD>";
		echo '<TD><INPUT TYPE=SUBMIT NAME=submit VALUE=Update></FORM>';
		echo '</FORM></TD></TR>';
	}
	echo '</TABLE><br><br>';

	echo '<br><br>';
}

?>
  
