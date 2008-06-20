<?PHP

include('util.php');


function isMultiPrereqScript($prereq){
    $pos1 = stristr($prereq, "<and>");
    $pos2 = stristr($prereq, "<or>");
    $pos3 = stristr($prereq, "<not>");
    
    if ($pos1 != false || $pos2 != false || $pos3 != false)
      return true;
    else
      return false;
}

function parsePrereqScript($prereq){
	$pos = stristr($prereq, "<pre>");
	$istrigger = 0;
	$result[0] = 0;
	if ($pos != false) {
		$istrigger = 1;
		$result[0] = 1;
	}

  // parse trigger
  if ($istrigger==1)
  {
    $pos = strpos($prereq, "<completed");
	if ( $pos != 0){
	  $pos = strpos($prereq, "\"");
	  $endname = substr($prereq,$pos+1);
	  $pos = strpos($endname, "\"");
	  $endname = substr($endname,0,$pos);
      $result[1] = "Completed Quest: ".$endname;
	  $result[2] = $endname;

	}else{
		$result[1] = htmlspecialchars($prereq);
		$result[2] = "";
	}
  }
  return $result;
}

function viewquestscript(){

  echo "<br> <A HREF=index.php?page=listquestscripts>Back to Quest Scripts list</A><br><br>";  

	$masterid = $_POST['id'];
	if ($masterid == ''){
		$masterid = $_GET['id'];
	}

	$query = "select quests.id, category, name, player_lockout_time, quest_lockout_time, prerequisite, script, task from quests, quest_scripts where quests.id=quest_scripts.quest_id and quests.id=" . $masterid;
	//echo "$query";
	$result = mysql_query2($query);

	while ($line = mysql_fetch_array($result, MYSQL_NUM)){
		$masterid = $line[0];

        echo "<FORM name=editquest action=index.php?page=questscript_actions METHOD=POST onsubmit=\"return checkFields()\" >";
	echo "<INPUT TYPE=HIDDEN NAME=operation VALUE=updatequestscript>";
	echo "<INPUT TYPE=HIDDEN NAME=id VALUE=$masterid>";
	echo "<b>Quest script ID:</b> $masterid<BR>";
	echo "<b>Category: </b> <INPUT size=30 TYPE=text NAME=category VALUE=\"$line[1]\"><BR>";
	echo "<b>Name:</b> <INPUT size=50 TYPE=text NAME=name VALUE=\"$line[2]\"> <BR>";
	echo "<b>Description:</b> <INPUT size=50 TYPE=text NAME=description VALUE=\"$line[7]\"> <BR>";
	echo "<b>Player lockout:</b> <INPUT size=50 TYPE=text NAME=plock VALUE=\"$line[3]\"> <BR>";
	echo "<b>Quest lockout:</b> <INPUT size=50 TYPE=text NAME=qlock VALUE=\"$line[4]\"> <BR>";
	echo "<b>Simple Prerequisite quest:</b> ";
	$prereqname = parsePrereqScript($line[5]);
	SelectQuestScriptByName("xxx","questprereq");
	echo "<br>OR";
       	echo "<br><table border=0><tr><td valign=top><b>Multiple Prerequisite quest (*):</b></td><td><textarea cols=50 NAME=prereq>$line[5]</textarea></td></tr></table>";
	echo "<b>&lt;pre&gt; syntax Prerequisites override Simple prerequisites</b><br><br>";
        echo "<b>Quest script: </b><br><textarea name=script rows=25 cols=80 wrap=virtual>{$line[6]}</textarea><br>";

		echo "<INPUT TYPE=submit NAME=submit VALUE=\"Save\">";
		echo "</FORM>";
	}

    echo " <br>(Example muti prereq: &lt;pre&gt;&lt;and&gt;&lt;completed quest=\"Dark Circle hunt\"/&gt;&lt;completed quest=\"Tarmeen Alecheech missing skulls\"/&gt;&lt;not&gt;&lt;completed quest=\"Another Quest\"/&gt;&lt;/not&gt;&lt;/and&gt;&lt;/pre&gt;)<BR><br>";

  echo "<br> <A HREF=index.php?page=listquestscripts>Back to Quest Scripts list</A><br><br>";  

}

?>


<script language=javascript>
  
  function checkFields() {
      if (editquest.questprereq.value!=-1 && editquest.prereq.value!="") {
        alert('Please select Simple Prerequisite OR Multiple Prerequisite');
        return false;
      } else {
        return true;
      }
  }

 </script>
