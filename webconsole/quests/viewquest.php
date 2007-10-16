<?PHP
function viewquest(){

  echo "<br> <A HREF=index.php?page=listquests>Back to Quests list</A><br><br>";  

	$masterid = $_POST['id'];
	if ($masterid == ''){
		$masterid = $_GET['id'];
	}

	$query = "select id, name, task, player_lockout_time, quest_lockout_time from quests where master_quest_id=0 and id=" . $masterid;
	$result = mysql_query2($query);

	while ($line = mysql_fetch_array($result, MYSQL_NUM)){
		$masterid = $line[0];

    echo "<FORM action=index.php?page=quest_actions METHOD=POST>";
		echo "<INPUT TYPE=HIDDEN NAME=operation VALUE=updatestep>";
		echo "<INPUT TYPE=HIDDEN NAME=questid VALUE=$masterid>";
		echo "<INPUT TYPE=HIDDEN NAME=masterid VALUE=$masterid>";
		echo "<b>Quest ID:</b> $masterid<BR>";
		echo "<b>Quest name:</b> <INPUT size=30 TYPE=text NAME=name VALUE='$line[1]'> <BR>";
		echo "<b>Quest description: </b> <INPUT size=100 TYPE=text NAME=task VALUE='$line[2]'><BR>";
		echo "<b>Player Lockout Time: </b> <INPUT size=10 TYPE=text NAME=plockout VALUE='$line[3]'><BR>";
		echo "<b>Quest Lockout Time: </b> <INPUT size=10 TYPE=text NAME=qlockout VALUE='$line[4]'><BR><BR>";
		echo "<INPUT TYPE=SUBMIT NAME=submit VALUE=\"Save\">";
		echo "</FORM>";

		$query2 = "select q.id, q.name, q.task, q.minor_step_number, player_lockout_time, quest_lockout_time from quests q where master_quest_id =$masterid order by q.minor_step_number";
		$result2 = mysql_query2($query2);

		echo "  <TABLE BORDER=1>";
		echo "  <TH> ID </TH> <TH> NAME</TH> <TH> TASK</TH> <TH> STEP </TH><TH> ASSIGNED TO</TH><TH>N.Trig/N.Resp</TH><TH> FUNCTIONS</TH>";

		while ($line2 = mysql_fetch_array($result2, MYSQL_NUM)){

      echo " <FORM action=index.php?page=quest_actions METHOD=POST>";
  		echo "<INPUT TYPE=HIDDEN NAME=operation VALUE=updatestep>";

			echo "<TR><TD valign=top>$line2[0]</TD>";
			echo "<TD valign=top><INPUT size=30 TYPE=text NAME=name VALUE=\"$line2[1]\"></TD>";
			echo "<TD ALIGN=CENTER valign=top><INPUT size=100 TYPE=text NAME=task VALUE=\"$line2[2]\"><br><br>";
  		echo "<b>Player Lockout Time: </b> <INPUT size=10 TYPE=text NAME=plockout VALUE=\"$line2[4]\">";
  		echo "<b> Quest Lockout Time: </b> <INPUT size=10 TYPE=text NAME=qlockout VALUE=\"$line2[5]\"></TD>";
			echo "<TD><INPUT size=2 TYPE=text NAME=stepnum VALUE=\"$line2[3]\"></TD>";


			$query3 = "select area from npc_triggers where quest_id=$line2[0]";
			$result3 = mysql_query2($query3);
			$line3 = mysql_fetch_array($result3, MYSQL_NUM);
			$area = $line3[0];
			if ($area == '')
				$area = "Not Assigned";

			echo "<TD>$area</TD>";
			$query3 = "select count(*) from npc_triggers where quest_id=$line2[0]";
			$result3 = mysql_query2($query3);
			$line3 = mysql_fetch_array($result3, MYSQL_NUM);
			$numtrig = $line3[0];

			$query3 = "select count(*) from npc_responses where quest_id=$line2[0]";
			$result3 = mysql_query2($query3);
			$line3 = mysql_fetch_array($result3, MYSQL_NUM);
			$numresp = $line3[0];

			echo "<TD>$numtrig / $numresp</TD>";
			echo "<TD><INPUT TYPE=HIDDEN NAME=masterid VALUE=$masterid>";
			echo "<INPUT TYPE=HIDDEN NAME=questid VALUE=$line2[0]>";
			echo "<INPUT TYPE=SUBMIT NAME=status VALUE=\"SAVE\"></FORM>";
			echo "<FORM ACTION=index.php?page=assignquest METHOD=POST>";
			echo "<INPUT TYPE=HIDDEN NAME=masterid VALUE=$masterid>";
			echo "<INPUT TYPE=HIDDEN NAME=stepid VALUE=$line2[0]>";
			echo "<INPUT TYPE=SUBMIT NAME=status VALUE=\"ASSIGN\"></FORM>";
			echo "<FORM ACTION=index.php?page=quest_actions METHOD=POST>";
			echo "<INPUT TYPE=HIDDEN NAME=operation VALUE=deletestep>";
		  echo "<INPUT TYPE=HIDDEN NAME=id VALUE=$line2[0]>";
		  echo "<INPUT TYPE=HIDDEN NAME=masterid VALUE=$masterid>";
			echo "<INPUT TYPE=SUBMIT NAME=status VALUE=\"DELETE\"></FORM>";

			echo "<FORM ACTION=index.php?page=viewqueststep&id=$line2[0] METHOD=POST>";
			printf("<INPUT TYPE=SUBMIT NAME=status VALUE=\"EDITSTEP\">");
			echo "<INPUT TYPE=HIDDEN NAME=operation VALUE=editstep>";
			printf('</FORM></TD></TR>');
		}
		echo '</TABLE>';

		echo '<TABLE><TR><TD>';
		echo "<FORM action=index.php?page=quest_actions METHOD=POST>";
		echo "<INPUT TYPE=SUBMIT NAME=submit VALUE=\"Add one step\">";
		echo "<INPUT TYPE=HIDDEN NAME=operation VALUE=addstep>";
		echo "<INPUT TYPE=HIDDEN NAME=id VALUE=$masterid>";
		echo '</FORM></TD>';

		echo "<TD><FORM action=index.php?page=quest_actions METHOD=POST>";
		echo "<INPUT TYPE=SUBMIT NAME=submit VALUE=\"Delete the quest\">";
		echo "<INPUT TYPE=HIDDEN NAME=operation VALUE=delete>";
		echo "<INPUT TYPE=HIDDEN NAME=id VALUE=$masterid>";
		echo "</FORM></TD></TR></TABLE>";
	}

  echo "<br> <A HREF=index.php?page=listquests>Back to Quests list</A><br><br>";  

}

?>