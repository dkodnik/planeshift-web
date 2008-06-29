<?php
 
function listitemicons() {

	displayCategory("books");
	displayCategory("food");
	displayCategory("furniture");
	displayCategory("helms");
	displayCategory("items");
	displayCategory("jewelry");
	displayCategory("money");
	displayCategory("naturalres");
	displayCategory("potions");
	displayCategory("shields");
	displayCategory("tools");
	displayCategory("weapons");
	

}

function displayCategory ($category) {
	echo "<H1>".$category."</H1>";
    echo '<TABLE BORDER="1"><TR><TD VALIGN="top">';

// Display Item Types.
    $query = "SELECT * FROM common_strings where string like '%".$category."%' order by id";
    $result = mysql_query2($query);

	// /planeshift/items/rathide01_icon.dds
	// /planeshift/art/things/items.zip/rathide01_icon.dds
	
    while ($row = mysql_fetch_array($result)) {
        echo "<TR><TD>".$row['id'] . "</TD><TD> " . $row['string'];
		$str = $row['string'];
		$name = substr( $str, strrpos($str,"/"));
		echo "</TD><TD>";
		if (!strpos($str,"#"))
			echo "<A HREF=planeshift/art/things/".$category.".zip/".$name.">".$name."</A>";
		echo "</TD></TR>";
	}
	echo '</TABLE>';
	
}
?>
