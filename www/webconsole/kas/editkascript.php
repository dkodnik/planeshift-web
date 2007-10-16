<?PHP

include('util.php');


function editkascript(){

  echo "<br> <A HREF=index.php?page=listkascripts>Back to KA Scripts list</A><br><br>";  

	$masterid = $_POST['id'];
	if ($masterid == ''){
		$masterid = $_GET['id'];
	}

	$query = "select id, script from quest_scripts where id=" . $masterid;
	$result = mysql_query2($query);

	while ($line = mysql_fetch_array($result, MYSQL_NUM)){
		$masterid = $line[0];

        echo "<FORM name=editquest action=index.php?page=questscript_actions METHOD=POST onsubmit=\"return checkFields()\" >";
		echo "<INPUT TYPE=HIDDEN NAME=operation VALUE=updatekascript>";
		echo "<INPUT TYPE=HIDDEN NAME=id VALUE=$masterid>";

        echo "<b>KA script: </b><br><textarea name=script rows=25 cols=80 wrap=virtual>{$line[1]}</textarea><br>";

		echo "<INPUT TYPE=submit NAME=submit VALUE=\"Save\">";
		echo "</FORM>";
	}

  echo "<br> <A HREF=index.php?page=listkascripts>Back to KA Scripts list</A><br><br>";  
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