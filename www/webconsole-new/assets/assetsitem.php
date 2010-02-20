<?php

function assetsitem()
{
    if(!checkaccess('assets', 'read'))
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
		return;
    }

	echo '<p class="header">Meshfacts available used/not used in game </p>';

	$assets_dir=getAssetsDir();
	echo "Analyzing ".$assets_dir."<br><br>";

	$dir = dir($assets_dir . DIRECTORY_SEPARATOR . "things");

	// cycle in all dirs
	$i=0;
	$k=0;
	while (($fileNameWithExt = $dir->read()) !== false) {

		if ($fileNameWithExt==".svn" || $fileNameWithExt=="." || $fileNameWithExt=="..")
			continue;

		//echo "DIR: $fileNameWithExt<br>";

		$subdir = $assets_dir . DIRECTORY_SEPARATOR . "things" . DIRECTORY_SEPARATOR . $fileNameWithExt;
		$dir2 = dir($subdir);

		// cycle on all files in the dir
		while (($fileNameWithExt = $dir2->read()) !== false) {
			if ($fileNameWithExt==".svn" || $fileNameWithExt=="." || $fileNameWithExt=="..")
				continue;
			$fileNameWithPathAndExt = $subdir . DIRECTORY_SEPARATOR . $fileNameWithExt;

			// retrieves all meshfacts
			if (is_file($fileNameWithPathAndExt) && strstr($fileNameWithPathAndExt,"meshfact")) {
				//echo "FILE: $fileNameWithPathAndExt<br>";

				$myFile = file($fileNameWithPathAndExt);

				foreach ($myFile as $line) {
					if (strstr($line,"<meshfact name=")) {
						$pos =  strpos($line,"name=");
						$meshname = substr($line,$pos+6);
						$pos = strpos($meshname,"\"");
						$meshname = substr($meshname,0,$pos);
						$meshfacts[$i] = $meshname;
						$i++;
					}
				}
			}

			// retrieves all icons
			if (is_file($fileNameWithPathAndExt) && strstr($fileNameWithPathAndExt,"_icon")) {
				//echo "ICON: $fileNameWithPathAndExt<br>";
				$icons[$k] = $fileNameWithExt;
				$k++;
			}
		}
	}

	$sql = "SELECT cstr_gfx_mesh from item_stats";
	$query = mysql_query2($sql);

	$i=0;
	while($result = mysql_fetch_array($query, MYSQL_ASSOC))
	{
		$db_meshfacts[$i] = $result['cstr_gfx_mesh'];
		$i++;
	}

	$int = array_values(array_intersect($meshfacts, $db_meshfacts)); //C = A ^ B
	$first = array_values(array_diff($meshfacts, $int)); //A' = A - C
	$second= array_values(array_diff($db_meshfacts, $int)); //B' = B - C

	echo "<br><h2>Elements present in the file definitions but not in the db:</h2>";
	for ($i = 0; $i < count($first); $i++) {
		echo "$first[$i] <br>";
	}

	echo "<br><h2>Elements present in the database but not in the files:</h2>";
	for ($i = 0; $i < count($second); $i++) {
		echo "$second[$i] <br>";
	}

	echo '<p class="header">Icons available used/not used in game </p>';

	$sql = "SELECT cstr_gfx_icon from item_stats";
	$query = mysql_query2($sql);

	$i=0;
	while($result = mysql_fetch_array($query, MYSQL_ASSOC))
	{
		// strip path
		if (strrpos($result['cstr_gfx_icon'],'/')) {
			$slash_pos = strrchr ($result['cstr_gfx_icon'],'//');
			$slash_pos = substr ($slash_pos,1);
		}else
			$slash_pos = $result['cstr_gfx_icon'];
		$db_icons[$i] = $slash_pos;
		$i++;
	}

	$int = array_values(array_intersect($icons, $db_icons)); //C = A ^ B
	$first = array_values(array_diff($icons, $int)); //A' = A - C
	$second= array_values(array_diff($db_icons, $int)); //B' = B - C

	echo "<br><h2>Icons present in the art directory but not in the db:</h2>";
	for ($i = 0; $i < count($first); $i++) {
		echo "$first[$i] <br>";
	}

	echo "<br><h2>Icons present in the database but not in the art directory:</h2>";
	for ($i = 0; $i < count($second); $i++) {
		echo "$second[$i] <br>";
	}

}


?>