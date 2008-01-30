<?php
 
function listitems() {

    echo '<TABLE BORDER="1"><TR><TD VALIGN="top">';
    echo '<TABLE>';

// Display Item Types.
    $query = "SELECT DISTINCT item_type FROM item_stats";
    $result = mysql_query2($query);

    while ($row = mysql_fetch_array($result)) {
        echo '<TR><TD><A HREF="index.php?page=listitems&item_type=' . $row['item_type'] . '">' . $row['item_type'] . '</A></TD></TR>';
        }

    echo '</table></td><td valign="top">';

// Display Items of Type.
    if (mysql_num_rows($result) == 0) {
        echo '<TABLE BORDER="1"><TR><TD>Selet an item</TD></TR></TABLE></TD><TD VALIGN="top">';
        }
    else {
        echo '<TABLE>';
        $query = 'SELECT id, name FROM item_stats WHERE item_type = "' . $_GET['item_type'] . '"';
        $result = mysql_query2($query);

        while($row = mysql_fetch_array($result)) {
            echo '<TR><TD>' . $row['id'] . '</TD><TD><A HREF="index.php?page=listitems&item_type=' . $_GET['item_type'] . '&item=' . $row['id'] . '">' . $row['name'] . '</A></TD></TR>';
            }

        echo '</TABLE></TD<TD VALIGN="top">';
        }

//Display Item Stats for Item
    $query = "SELECT * FROM item_stats WHERE id ='" . $_GET['item'] . "'";
    $result = mysql_query2($query);
    if (mysql_num_rows($result) == 0) {
        echo '<TABLE BORDER="1"><TR><TD>Select an item</TD></TR></TABLE></TD></TR></TABLE>';
        }

    else {
        echo '<TABLE BORDER="1">';
        while ($row = mysql_fetch_assoc($result)) {
            foreach ($row as $key => $value) {
                echo '<TR><TD>' . $key . '</TD><TD>' . $value . '</TD></TR>';
                }

            echo 'Actions for item #' . $row['id'] . ':';
            echo ' <A HREF="index.php?page=edititem&item=' . $row['id'] . '" TITLE="Edit this item.">EDIT</A>';
            echo ' <A HREF="index.php?page=deleteitem&item=' . $row['id'] . '" TITLE="Delete this item.">DELETE</A>';
            echo '</TABLE></TD></TR></TABLE>';
            }
        }
    }
?>
