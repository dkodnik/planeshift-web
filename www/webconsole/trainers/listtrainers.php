<?
function listtrainers(){
	include('util.php');

	?>
	<SCRIPT language=javascript>
	
	function confirmDelete()
	{
	    return confirm("Are you sure you want to remove this category?");
	}
	
	</SCRIPT>
	<?PHP

    checkAccess('npc', '', 'read');

    echo "<A HREF=index.php?page=checktrainers>Check if all skills/stats can be trained</A><br><br>";

	$query = "select distinct c.id,c.name,c.id,s.name from trainer_skills t, characters c, sectors s where c.id=t.player_id and c.loc_sector_id=s.id order by s.name, c.name";
	$result = mysql_query2($query);

	while ($line = mysql_fetch_array($result, MYSQL_NUM)){
		// Find number of skills in category
		$skillquery = "select s.skill_id,s.name,t.min_rank,t.max_rank,t.min_faction from trainer_skills t, skills s where s.skill_id=t.skill_id AND t.player_id=$line[0]";
		$skill_res = mysql_query2($skillquery);

		echo "  <b><A HREF=index.php?page=viewnpc&id=$line[0]>$line[1]</A> in $line[3]</b><br>";
		echo '  <TABLE BORDER=1>';
		echo "  <TH> Skill </TH> <TH> MinRank </TH> <TH> MaxRank </TH> <TH>MinFaction</TH> <TH> Functions </TH>";
		while ($skilldata = mysql_fetch_array($skill_res, MYSQL_NUM)){
			echo "<TR><TD>$skilldata[1]</TD>";
                        echo "<TD>$skilldata[2]</TD>";
                        echo "<TD>$skilldata[3]</TD>";
                        echo "<TD>$skilldata[4]</TD>";
			echo "<TD><FORM ACTION=index.php?page=trainer_actions&operation=remove METHOD=POST>";
			echo "<INPUT TYPE=hidden NAME=skill_id VALUE=\"$skilldata[0]\">";
			echo "<INPUT TYPE=hidden NAME=player_id VALUE=\"$line[0]\">";
			echo "<INPUT TYPE=SUBMIT NAME=submit VALUE=Remove></FORM>";
		}
		echo '<TR><TD>';
		echo "<FORM ACTION=index.php?page=trainer_actions&operation=add METHOD=POST>";
		SelectSkills("","skill_id");
		echo "<TD><INPUT TYPE=text NAME=min_rank VALUE=\"\"> </TD>";
                echo "<TD><INPUT TYPE=text NAME=max_rank VALUE=\"\"></TD>";
                echo "<TD><INPUT TYPE=text NAME=min_faction VALUE=\"\"></TD>";
		echo "<INPUT TYPE=hidden NAME=player_id VALUE=\"$line[2]\">";
		echo "<TD><INPUT TYPE=SUBMIT NAME=submit VALUE=Add></TD></TR>";
		echo '</FORM>';

		echo '</TABLE><br>';
	}

	echo '<br><br>';
}

?>
