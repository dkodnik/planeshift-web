<?php

function edititems($commit = 0) {

    checkaccess('items', '', 'edit');
    if ($commit != 0) {
// pull data from $_POST
        $item_id = $_POST['id'];
        $item_name = $_POST['name'];
        $item_weight = $_POST['weight'];
	$item_size = $_POST['size'];
        $item_cstr_id_gfx_icon = $_POST['cstr_id_gfx_icon'];
        $item_cstr_id_gfx_mesh = $_POST['cstr_id_gfx_mesh'];
        $item_category_id = $_POST['category_id'];
        $item_base_sale_price = $_POST['base_sale_price'];
        $item_description = $_POST['description'];
        $item_pretags = htmlspecialchars_decode($_POST['pretags']);
        $item_posttags = htmlspecialchars_decode($_POST['posttags']);
        $item_cd = $_POST['creative_definition'];
        $query = sprintf('UPDATE item_stats SET name = "%s", weight = "%s", size = "%s", cstr_id_gfx_icon = "%s", cstr_id_gfx_mesh = "%s", category_id = "%s", base_sale_price = "%s", description = "%s", creative_definition = "%s" WHERE id = %s', $item_name, $item_weight, $item_size, $item_cstr_id_gfx_icon, $item_cstr_id_gfx_mesh, $item_category_id, $item_base_sale_price, $item_description, $item_pretags . $item_cd . $item_posttags, $item_id);
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

        $unownedNonPublic = strpos(' ' . $row['creative_definition'], '<creative type="literature">');

        $creative_definition = strip_tags($row['creative_definition']);
        $parsed_cd = extract_tags($row['creative_definition']);
        echo '<TR><TD>id</TD><TD>' . $row['id'];
        echo '<INPUT TYPE="hidden" NAME="id" VALUE="' . $row['id'] . '"></TD></TR>';
        echo '<TR><TD>name</TD><TD><INPUT TYPE="text" NAME="name" VALUE="' . $row['name'] . '"></TD></TR>';
        echo '<TR><TD>weight</TD><TD><INPUT TYPE="text" NAME="weight" VALUE="' . $row['weight'] . '"></TD></TR>';
	echo '<TR><TD>size</TD><TD><INPUT TYPE="text" NAME="size" VALUE="' .$row['size'] . '"></TD></TR>';
        echo '<TR><TD>cstr_id_gfx_icon</TD><TD>'; echo 'Due to the effort required, icon and mesh previews will not be available.<br />'; echo DrawSelectBox('icon', 'cstr_id_gfx_icon', $row['cstr_id_gfx_icon']);  echo '</TD></TR>';
        echo '<TR><TD>cstr_id_gfx_mesh</TD><TD>'; echo DrawSelectBox('mesh', 'cstr_id_gfx_mesh', $row['cstr_id_gfx_mesh']); echo '</TD></TR>';
        echo '<TR><TD>category_id</TD><TD>'; echo DrawSelectBox('itemcat', 'category_id', $row['category_id']); echo '</TD></TR>';
        echo '<TR><TD>base_sale_price</TD><TD><INPUT TYPE="text" NAME="base_sale_price" VALUE="' . $row['base_sale_price'] . '"></TD></TR>';
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
               echo '<BR /><TEXTAREA NAME="creative_definition" ROWS="10" COLS="40">';
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
