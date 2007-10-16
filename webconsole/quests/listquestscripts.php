<?

include('viewquestscript.php');

function printChildQuests($questname) {
  
}

// $parent is the parent of the children we want to see
// $level is increased when we go deeper into the tree,
//        used to display a nice indented tree
function display_children($questarray, $current, $level) {

    for ($i = 1; $i <= $level; $i++) {
       $space = $space. "--- ";
    }

   // retrieve all children of $current
   foreach ($questarray as $key => $data2) {
       if ($data2[1]==$current) {
          $data = $data2[0];
          echo "$space --- <A href=index.php?page=viewquestscript&id=$data[0]>$key </A><br>";
	     display_children($questarray,$key, $level+1);
	   }
	}

   // display each child
   for ($i ; $i<sizeof($result);$i++) {
        echo $result;
       // indent and display the title of this child
       //echo str_repeat('  ',$level).$row['title']."\n";

       // call this function again to display this
       // child's children
       //display_children($row['title'], $level+1);
   }
} 


function listquestscripts(){

    checkAccess('quest', '', 'read');

    $mode = $_GET['mode'];

    if ($mode=="hier") {
    echo "<A HREF=index.php?page=listquestscripts>Show quest scripts as simple list</A><br>";

    // build an array with parentname | childname | child data
	$query = "select id, category, name, player_lockout_time, quest_lockout_time, prerequisite from quests order by 2,3";
	$result = mysql_query2($query);
	echo "<ul>";
	while ($line = mysql_fetch_array($result, MYSQL_NUM)){
	 	 $data =  array($line[0],  $line[1],  $line[2],  $line[3], $line[4]);
          $prereq = parsePrereqScript($line[5]);
          //echo "$line[2] - $prereq[1]<br>";
          $data2 = array($data, $prereq[1]);
          $questarray[$line[2]] = $data2;
	}

   // recurse on nodes without parent
   foreach ($questarray as $key => $data2) {
       if ($data2[1]==null)  {
        $data = $data2[0];
         echo "o <A href=index.php?page=viewquestscript&id=$data[0]>$key </A><br>";
	     display_children($questarray,$key, 0);
	   }
	}
	echo "</ul>";

  } else {
    echo "<A HREF=index.php?page=listquestscripts&mode=hier>Show quest scripts in hierarchical view</A><br><br>";

	$query = "select id, category, name, player_lockout_time, quest_lockout_time, prerequisite from quests order by 2,3";
	$result = mysql_query2($query);
	echo '<table border="1"><tr><td><b>ID:</b></td><td><b>Category:</b></td><td><b>Name: </b></td><td><b>Player Lockout: </b></td><td><b>Quest Lockout: </b></td><td><b>Prerequisite </b></td><td> </td></tr>';
	while ($line = mysql_fetch_array($result, MYSQL_NUM)){
		echo "<tr><td> $line[0] </td><td> $line[1] </td><td> $line[2] </td><td> $line[3] </td>";
		echo "<td> $line[4]</td>";

          $prereq = parsePrereqScript($line[5]);
          echo "<td> $prereq[1]</td>";
		echo "<td><FORM ACTION=index.php?page=viewquestscript METHOD=POST>";
		echo "<INPUT TYPE=HIDDEN NAME=id VALUE=$line[0]>";
		echo "<INPUT TYPE=SUBMIT NAME=Submit VALUE=Edit>";
		echo '</FORM></td></tr>';
	}
	echo "</table>";

  }

}
  
?>
