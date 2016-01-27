<?PHP
function outputGraph ($result, $translate) {

	$max = 0;
	$total = 0;
	while ($line = fetchSqlAssoc($result))
  {
        $num = $line[2];
        $name = $line[1];
		
		$values[$name] = $num;
		if ($max <$num)
			$max=$num;
		$total = $total + $num;
	}

	echo "<table>";

	$i=1;
	foreach ($values as $key => $value) {
		$heights[$i] = ceil(200*$value/$max);
		$keys[$i] = $key;
		$values2[$i] = $value;
		$i++;
	}

	echo "<tr>";
	for ($i = 1; $i <= sizeof($values2); $i++)
		echo "<td>".$values2[$i]."</td>";

	echo "</tr><tr class=\"color_a\">";
	for ($i = 1; $i <= sizeof($heights); $i++)
		echo "<td valign=bottom halign=center><img align=center width=30 height=".$heights[$i]." src='img/bluebar2.gif'></td>";

	echo "</tr><tr class=\"color_b\">";
	for ($i = 1; $i <= sizeof($keys); $i++)
		echo "<td>".$keys[$i]."</td>";

    echo "</tr></table>";

}
?>