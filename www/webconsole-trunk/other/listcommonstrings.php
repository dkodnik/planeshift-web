<?

include('util.php');

function list_commonstrings(){

    checkAccess('main', '', 'read');
    
	$query = "select id, string from common_strings";
	$result = mysql_query2($query);

	echo '  <TABLE BORDER=1>';
	echo '  <TH> ID </TH> ';
	echo '  <TH> String </TH> ';
	echo '  <TH> Functions </TH> ';

	while ($line = mysql_fetch_array($result, MYSQL_NUM)){
		echo '<TR>';
		echo '<FORM ACTION=index.php?page=commonstring_actions&operation=update METHOD=POST>';
		echo "<TD><INPUT TYPE=hidden NAME=id VALUE=\"$line[0]\">$line[0]</TD>";
		echo "<TD><INPUT SIZE=60 TYPE=text NAME=string VALUE=\"$line[1]\"></TD>";
		echo '<TD><TABLE><TD><INPUT TYPE=SUBMIT NAME=submit VALUE=Update></FORM>';
		echo '<FORM ACTION=index.php?page=commonstring_actions&operation=delete METHOD=POST></TD>';
		echo "<TD><INPUT TYPE=hidden NAME=id VALUE=\"$line[0]\">";
		echo '<INPUT TYPE=SUBMIT NAME=submit VALUE=Delete></FORM></TD></TABLE>';
		echo '</TD></TR>';
	}
	echo '<TR>';
	echo '<FORM ACTION=index.php?page=commonstring_actions&operation=add METHOD=POST>';
	echo "<TD></TD>";
	echo "<TD><INPUT SIZE=60 TYPE=text NAME=string1><BR>";
	echo "<INPUT SIZE=60 TYPE=text NAME=string2><BR>";
	echo "<INPUT SIZE=60 TYPE=text NAME=string3><BR>";
	echo "<INPUT SIZE=60 TYPE=text NAME=string4><BR>";
	echo "<INPUT SIZE=60 TYPE=text NAME=string5><BR>";
	echo "<INPUT SIZE=60 TYPE=text NAME=string6><BR>";
	echo "<INPUT SIZE=60 TYPE=text NAME=string7><BR>";
	echo "<INPUT SIZE=60 TYPE=text NAME=string8><BR>";
	echo "<INPUT SIZE=60 TYPE=text NAME=string9><BR>";
	echo "<INPUT SIZE=60 TYPE=text NAME=string10></TD>";
	echo '<TD><INPUT TYPE=SUBMIT NAME=submit VALUE=Add></FORM>';

	echo '</FORM></TD></TR>';
	echo '</TABLE><br><br>';

	echo '<br><br>';
}

?>
  
