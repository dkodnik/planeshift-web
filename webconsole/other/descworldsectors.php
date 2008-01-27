<?
function descworldsectors($sector){
  include('util.php');

	?>
			
<?PHP

    checkAccess('main', '', 'read');

    if ($sector=="") {
    	$query = 'select s.name from sectors s';
    	$result = mysql_query2($query);
    	echo 'Choose a sector to list/edit';
    	echo "<ul>";
    	while ($line = mysql_fetch_array($result, MYSQL_NUM)){
    		echo "<li><a href='index.php?page=descworld&sector=".$line[0]."'>".$line[0]."</a></li>";
    	}
    	echo "</ul>";
    	
    	return;
    }

    echo "<B>$sector</B><br><br>";

    	$query = "select * from action_locations where sectorname='$sector'";
    	$result = mysql_query2($query);
    	
    	echo '<table><TH>ID</TH><TH>Master_id</TH><TH>Name</TH><TH>Meshname</TH><TH>polygon</TH><TH>x/y/z</TH><TH>Radius</TH><TH>triggertype</TH><TH>responsetype</TH><TH>response</TH><TH>Actions</TH>';
    	while ($line = mysql_fetch_array($result, MYSQL_NUM)){
    	    echo "<tr><FORM action=index.php?page=descworldactions&operation=update&id=$line[0]&sector=$sector METHOD=POST>";
    		echo "<td>$line[0] </td><td><INPUT type=text size=5 name=masterid value=$line[1]></td><td><INPUT type=text name=name value=\"$line[2]\"></td><td><INPUT type=text name=meshname value=\"$line[4]\"></td>";
    		$pos = $line[6]."/".$line[7]."/".$line[8];
    		echo "<td><INPUT type=text name=polygon size=5 value=\"$line[5]\"></td><td>$pos</td><td><INPUT type=text size=5 name=radius value=$line[9]></td>";
    		echo "<td>";
    		SelectActionLocation($line[10],"triggertype");
    		echo "</td><td>";
    		SelectActionLocation($line[11],"responsetype");
    		echo "</td><td><textarea cols=30 rows=2 name=response> $line[12] </textarea></td>";
    		echo "<TD><INPUT type=submit name=submit value=Update></FORM><br>";
    		echo "<FORM action=index.php?page=descworldactions&operation=delete&id=$line[0]&sector=$sector METHOD=POST><INPUT type=submit name=submit value=Delete></FORM></TD></TR>";
    	}
    	echo '</table>';
    	
	echo "<br><br><A HREF=\"index.php?page=descworld\" target=_top>Go back to sectors list</A>";

}

?>