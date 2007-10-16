<?PHP
function adjustraces()
{
	$query = 'SELECT * FROM race_info WHERE race_id < 12';
	$result = mysql_query2($query);

	while ($line = mysql_fetch_array($result, MYSQL_BOTH))
    {
		$cp = $line[name] . '_initialcp';
		$cpQuery = "update race_info set initial_cp=$_POST[$cp] where name='$line[name]'";
		mysql_query2($cpQuery) or die ("Bad Query");

		$start_x = $line[name] . '_start_x';
		$start_y = $line[name] . '_start_y';
		$start_z = $line[name] . '_start_z';
		$regen1 = $line[name] . '_regen1';
		$regen2 = $line[name] . '_regen2';
		$regen3 = $line[name] . '_regen3';
		$regen4 = $line[name] . '_regen4';

		$positionQuery = "UPDATE race_info SET start_x=$_POST[$start_x],
	                                           start_y=$_POST[$start_y],
	                                           start_z=$_POST[$start_z],
											   base_physical_regen_still=$_POST[$regen1],
											   base_physical_regen_walk=$_POST[$regen2],
											   base_mental_regen_still=$_POST[$regen3],
											   base_mental_regen_walk=$_POST[$regen4]
	                                       WHERE name='$line[name]'";
		mysql_query2($positionQuery);

		$sector = $line[name] . '_sectorName';
		$sectorIDQuery = "SELECT id FROM sectors WHERE name='$_POST[$sector]'";
		$sectorIDResult = mysql_query2($sectorIDQuery);
		$IDline = mysql_fetch_row($sectorIDResult);
		$sectorQuery = "UPDATE race_info SET start_sector_id=$IDline[0] WHERE name='$line[name]'";
		mysql_query2($sectorQuery);

		Header('Location: index.php?page=maincharcreate');
	}
}
?>
