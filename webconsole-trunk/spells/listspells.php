<?

include('util.php');

function listspells(){

	?>
			
<?PHP


    checkAccess('main', '', 'read');

	$query = 'select * from ways';
	$result = mysql_query2($query);
	echo'<table><tr><td valign="top"><table>';
	while ($line = mysql_fetch_array($result, MYSQL_NUM)){
		echo "<tr><td><a href='index.php?page=listspells&way=".$line[0]."'>".$line[1]."</a></td></tr>";
	}
	echo'</table>';

	$query = "select s.id, s.name ,s.spell_description, s.realm, s.saving_throw, s.saving_throw_value, s.max_power from spells as s, ways as w  where s.way_id=w.id and w.id ='" . $_GET['way'] . "' order by realm";
	$result = mysql_query2($query);
	echo'</td><td valign="top"><table>';

	$found = false;
        $currentrealm = 0;
	while ($line = mysql_fetch_array($result, MYSQL_NUM)){
                if ($currentrealm!=$line[3]) {
                  if ($currentrealm!=0) echo"<TR><TD ><br></TD></TR>";
                  echo"<TR><TD ><b>Realm: $line[3]</b></TD></TR>";
                  $currentrealm=$line[3];
                }
		$found = true;
                $save = "$line[4]v$line[5]";
                if ($line[4]=="0") $save = "None";
		echo"<TR><TD ><P><a href='index.php?page=spell_actions&id=$line[0]'>$line[1]</a> Time:$line[4] Save:$save PCap:$line[6]  (ID:$line[0])</P></TD></TR>";
		echo"<TR><TD ><P>$line[2] </P></TD></TR>";
	}
	
	if(!$found && isset($_GET['way']))
	echo "<TR><TD><P>No spells found in this category</P></TD></TR>";

	echo '</TABLE></td></tr></table>';
}

function whereusedglyph() {

	$query_events = "SELECT id,name FROM item_stats WHERE flags LIKE '%GLYPH%' order by name";
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
