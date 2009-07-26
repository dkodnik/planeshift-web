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

        $query = "SELECT * FROM common_strings WHERE string LIKE '%$category%' ORDER BY id";
        $result = mysql_query2($query);

        while ($row = mysql_fetch_array($result)) {
            echo "<TR><TD>".$row['id'] . "</TD><TD> " . $row['string'];
            $str = $row['string'];
            $name = substr( $str, strrpos($str,"/"));
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
