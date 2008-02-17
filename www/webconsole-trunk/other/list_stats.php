<?PHP
function show_stats(){

	$groupid=$_GET['groupid'];
	$op=$_GET['op'];
	$periodname=$_GET['periodname'];

// accounts global
if ($groupid==1) {

	if ($op=="add") {
		// get max period name in this group, and create next
		$nextquarter = getNextQuarterPeriod($groupid);
		
		$sql = "insert into statistics (id,groupid,periodname) values ('',".$groupid.",'".$nextquarter."')";
		//echo "<h1>".$sql."</h1>";
		$query = mysql_query2($sql);
	}

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
	for ($i = 0; $i < sizeof($array); $i++){
		$element = $array[$i];
		echo "<th>$element</th>";
	}

	echo "<tr>";
    for ($i = 0; $i < sizeof($array); $i++){
		$element = $arrayres[$i];
		if ($element !=null && $element!="") {
			$scaledvalue = $element / 200;
			echo "<td valign=bottom align=center><img src=images/bluebar2.gif height=".$scaledvalue." width=20></td>";
		} else {
			echo "<td><a href=\"index.php?page=list_stat_group&groupid=".$groupid;
			echo "&op=calc&periodname=".$array[$i]."\">Calculate</A></td>";
		}
	}
	echo "</tr>";

	echo "<tr>";
	for ($i = 0; $i < sizeof($arrayres); $i++){
		$element = $arrayres[$i];
		echo "<td>$element</td>";
	}
	echo "</tr>";
	echo "</table>";

	echo "<a href=\"index.php?page=list_stat_group&groupid=".$groupid;
	echo "&op=add\">Add next period</A><br><br>";
}
	
}	
?>