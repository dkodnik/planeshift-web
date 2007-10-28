<?PHP
function view_stats(){

	$groupid=$_GET['groupid'];

// accounts global
if ($groupid==1) {

	$statquery = "select count(*) from accounts where created_date>=DATE('param1') and created_date<DATE('param2');";

	$sql = "select * from statistics where groupid = ".$groupid." order by periodname";
	$query = mysql_query2($sql);
	echo'<table border="1">';
	$i = 0;
	while ($result = mysql_fetch_array($query)){
		$array[$i] = $result['periodname'];
		$arrayres[$i] = $result['result'];
		$name = $result['name'];
		if ($result['query']!="")
			$statquery = $result['query'];
		$i++;
	}

	echo "<h2>".$name."</h2>";
	
	echo "<table border=1>";
	foreach ($array as $element) {
		echo "<th>$element</th>";
	}

	echo "<tr>";
    for ($i = 0; $i < sizeof($array); $i++){
		$element = $arrayres[$i];
		echo "element: ".$i." - ".$element;
		if ($element !=null && $element!="") {
			$scaledvalue = $element / 200;
			echo "<td valign=bottom align=center><img src=images/bluebar2.gif height=".$scaledvalue." width=20></td>";
		} else {
			echo "xxxxxxxxxxxxxxxxxxxxxxxx<td><input type=submit value=Calculate></td>";
		}
	}
	echo "</tr>";

	echo "<tr>";
	foreach ($arrayres as $element) {
		echo "<td>$element</td>";
	}
	echo "</tr>";
	echo "</table>";

	echo "<br>Add one statistic element, with period name <input type=text name=periodname>,";
	echo " query: <input type=text name=query size=50 value=\"".$statquery." \">";
}
	
}	
?>