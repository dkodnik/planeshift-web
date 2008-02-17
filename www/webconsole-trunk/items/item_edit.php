<?php

function edititems() {

    checkaccess('items', '', 'edit');
    $query = "SELECT * FROM item_stats WHERE id ='" . $_GET['item'] . "'";
    $result = mysql_query2($query);
    if (mysql_num_rows($result) == 0) {
        echo '<TABLE BORDER="1"><TR><TD>Invalid item.</TD></TR></TABLE>';
        }
    else {
        echo '<FORM ACTION="index.php?item_edit.php" method="POST"><TABLE BORDER="1"><TR><TD VALIGN="top">';
        $row = mysql_fetch_assoc($result);
        $book = strpos(' ' . $row['creative_definition'], '<creative type="literature">');
        $creative_definition = strip_tags($row['creative_definition']);
        $parsed_cd = extract_tags($row['creative_definition']);
        echo '<TR><TD>id</TD><TD>' . $row['id'] . '</TD></TR>';
        echo '<TR><TD>name</TD><TD><INPUT TYPE="text" NAME="name" VALUE="' . $row['name'] . '"></TD></TR>';
        echo '<TR><TD>description</TD><TD><TEXTAREA NAME="description" ROWS="10" COLS="40">' . $row['description'] . '</TEXTAREA></TD></TR>';
        echo '<TR><TD>creative_definition</TD><TD>';
        foreach ($parsed_cd as $value) {
           if (strpos($value, '>')) {
               echo htmlspecialchars($value);
               }
           else {
               echo '<BR /><TEXTAREA NAME="creative_definition" ROWS="10" COLS="40"';
               if ($book) {
                   echo '>';
                   }
               else {
                   echo 'DISABLED READONLY>';
                   }
               echo $value . '</TEXTAREA><BR />';
               }
            }

        echo '</TD></TR><TR><TD><INPUT TYPE="submit" NAME="Submit"></TD></TR>';
        }
    echo '</TD></TR></TABLE>';
    }
?>