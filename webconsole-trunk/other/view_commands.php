<?

include('util.php');

function view_commands(){

	?>
			
<?PHP


    checkAccess('npc', '', 'edit');

	$query = 'select * from command_groups';
	$result = mysql_query2($query);
	echo'<table><tr><td valign="top"><table>';
	while ($line = mysql_fetch_array($result, MYSQL_NUM)){
		echo "<tr><td><a href='index.php?page=view_commands&group=".$line[0]."'>".$line[1]."</a></td></tr>";
	}
	echo'</table>';

    // get group name
	$query = "select * from command_groups where id =" . $_GET['group'];
	$result = mysql_query2($query);
	$line = mysql_fetch_array($result, MYSQL_NUM);
	$groupname=$line[1];


	$query = "select * from command_group_assignment where group_member ='" . $_GET['group'] . "' order by command_name";
	$result = mysql_query2($query);
	echo'</td><td valign="top"><table>';

	$found = false;
      echo"<TR><TD ><br></TD></TR>";
        echo"<TR><TD ><b>Group: $groupname</b></TD></TR>";
	while ($line = mysql_fetch_array($result, MYSQL_NUM)){
		$found = true;
		echo"<TR><TD ><P><a href='index.php?page=cmd_actions&group=$line[1]&cmd=$line[0]'>$line[0]</a> </P></TD></TR>";
		echo"<TR><TD ><P>$line[2] </P></TD></TR>";
	}
	
	if(!$found && isset($_GET['group']))
	echo "<TR><TD><P>No commands found in this group</P></TD></TR>";

	echo '</TABLE></td></tr></table>';
}

function whereusedglyph() {

	$query_events = "SELECT id,name FROM item_stats WHERE flags LIKE 'GLYPH%' order by name";
	$result = mysql_query2($query_events);
	while ($list = mysql_fetch_array($result, MYSQL_NUM)) {

              echo " <h2>$list[1]</h2>";
          	$query = "select spells.id, spells.name, ways.name, spells.realm from spells,spell_glyphs,ways where spells.id=spell_glyphs.spell_id and ways.id=spells.way_id and item_id=".$list[0]." order by realm";
          	$result2 = mysql_query2($query);
          	while ($line2 = mysql_fetch_array($result2, MYSQL_NUM)){
                  echo "o <b>Spell:</b> <a href=index.php?page=spell_actions&id=$line2[0]>$line2[1]</A> <b>Realm:</b> $line2[3] <b>Way:</b> $line2[2]<br>";
              }
	}

}

?>
