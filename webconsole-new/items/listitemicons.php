<?php
 
function listitemicons() 
{
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

function displayCategory ($category) 
{
    if (checkaccess('items', 'read'))
    {

        echo "<H1>".$category."</H1>";
        echo '<TABLE BORDER="1">';

        $query = "SELECT DISTINCT cstr_gfx_icon FROM item_stats WHERE cstr_gfx_icon LIKE '%$category%' ORDER BY cstr_gfx_icon";
        $result = mysql_query2($query);
        if (sqlNumRows($result) != 0) {
            echo '<TR><TD>results from item_stats</TD></TR>';
        }
        while ($row = fetchSqlAssoc($result)) {
            echo "<TR><TD> " . $row['cstr_gfx_icon'];
            //$str = $row['string'];
            //$name = substr( $str, strrpos($str,"/"));
            echo "</TD></TR>";
        }
        
        $query = "SELECT DISTINCT cstr_icon FROM quests WHERE cstr_icon LIKE '%$category%' ORDER BY cstr_icon";
        $result = mysql_query2($query);
        if (sqlNumRows($result) != 0) {
            echo '<TR><TD>results from quests</TD></TR>';
        }
        while ($row = fetchSqlAssoc($result)) {
            echo "<TR><TD> " . $row['cstr_icon'];
            //$str = $row['string'];
            //$name = substr( $str, strrpos($str,"/"));
            echo "</TD></TR>";
        }
        
        $query = "SELECT DISTINCT image_name FROM spells WHERE image_name LIKE '%$category%' ORDER BY image_name";
        $result = mysql_query2($query);
        if (sqlNumRows($result) != 0) {
            echo '<TR><TD>results from spells</TD></TR>';
        }
        while ($row = fetchSqlAssoc($result)) {
            echo "<TR><TD> " . $row['image_name'];
            //$str = $row['string'];
            //$name = substr( $str, strrpos($str,"/"));
            echo "</TD></TR>";
        }
        echo '</TABLE>';
    }
    else
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
    }
}
?>
