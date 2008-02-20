<?php

function edititems($commit = 0) {

    checkaccess('items', '', 'edit');
    if ($commit != 0) {
// pull data from $_POST
        $item_id = $_POST['id'];
        $item_name = $_POST['name'];
        $item_description = $_POST['description'];
        $item_pretags = htmlspecialchars_decode($_POST['pretags']);
        $item_posttags = htmlspecialchars_decode($_POST['posttags']);
        $item_cd = $_POST['creative_definition'];
        $query = 'UPDATE item_stats SET name = "'. $item_name .'", description = "'. $item_description .'", creative_definition = "'. $item_pretags . $item_cd . $item_posttags .'" WHERE id = '. $item_id;
        if (strpos($item_cd, '>') || strpos($item_cd, '<')) {
            echo 'Book text contains illegal characters.  Unable to commit changes.';
            }
        else {
            $result = mysql_query2($query);
            echo 'Changes committed successfully!';
            }
        }
    $query = "SELECT * FROM item_stats WHERE id ='" . $_GET['item'] . "'";
    $result = mysql_query2($query);
    echo '<A HREF="index.php?page=listitems">Back to item statistics</A>';
    if (mysql_num_rows($result) == 0) {
        echo '<TABLE BORDER="1"><TR><TD>Invalid item.</TD></TR></TABLE>';
        }
    else {
        echo '<FORM ACTION="index.php?page=edititemcommit&item=' . $_GET['item'] . '" method="POST"><TABLE BORDER="1"><TR><TD VALIGN="top">';
        $row = mysql_fetch_assoc($result);

// Public books can be edited in-game and we should not be using this to edit any player's books or other creative_definition content in this manner.

        $unownedNonPublic = strpos(' ' . $row['creative_definition'], '<creative type="literature">');

        $creative_definition = strip_tags($row['creative_definition']);
        $parsed_cd = extract_tags($row['creative_definition']);
        echo '<TR><TD>id</TD><TD>' . $row['id'];
        echo '<INPUT TYPE="hidden" NAME="id" VALUE="' . $row['id'] . '"></TD></TR>';
        echo '<TR><TD>name</TD><TD><INPUT TYPE="text" NAME="name" VALUE="' . $row['name'] . '"></TD></TR>';
        echo '<TR><TD>description</TD><TD><TEXTAREA NAME="description" ROWS="10" COLS="40">' . $row['description'] . '</TEXTAREA></TD></TR>';
        echo '<TR><TD>creative_definition</TD><TD>';
        $pretags = '';
        $posttags = '';
        $cd_text = '';
        foreach ($parsed_cd as $value) {
           if (strpos($value, '>')) {
               echo htmlspecialchars($value);
               $posttags = $posttags . $value;
               }
           else {
               echo '<BR /><TEXTAREA NAME="creative_definition" ROWS="10" COLS="40"';
               if ($unownedNonPublic) {
                   echo '>';
                   }
               else {
                   echo 'DISABLED READONLY>';
                   }
               $cd_text = $value;
               $pretags = $posttags;
               $posttags = '';
               if ($item_cd) {
                   echo $item_cd;
                   }
               else {
                   echo $value;
                   }
               echo '</TEXTAREA><BR />';
               }
            }
        echo '<INPUT TYPE="hidden" NAME="pretags" VALUE="' . htmlspecialchars($pretags) . '"><INPUT TYPE="hidden" NAME="posttags" VALUE="' . htmlspecialchars($posttags) . '">';
        echo '</TD></TR><TR><TD><INPUT TYPE="submit" NAME="Submit"></TD></TR>';
        }
    echo '</TD></TR></TABLE>';
    }
?>