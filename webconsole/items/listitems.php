<?
function listitems(){

      ?>
<SCRIPT language=javascript>

function confirmDelete()
{
    return confirm("Are you sure you want to delete this Item?");
}

</SCRIPT>

<?PHP

    checkAccess('main', '', 'read');
    
    echo'<table border="1"><tr><td valign="top">';
    echo'<table>';
    $query = "select distinct item_type from item_stats";
    $result = mysql_query2($query);
    while($line = mysql_fetch_array($result))
    {
        echo '<tr><td><a href="index.php?page=listitems&item_type=' . $line['item_type'] . '">' . $line['item_type'] . '</a></td></tr>';
    }
    echo'</table></td><td valign="top">';
    if (mysql_num_rows($result) == 0)
    {
        echo "<TABLE BORDER='1'><tr><td>Select an item</td></tr></table></td><td valign='top'>";
    }
    else
    {
        echo'<table>';
        $query = "select  id ,name from item_stats where item_type ='" . $_GET['item_type'] . "'";
        $result = mysql_query2($query);
        while($line = mysql_fetch_array($result))
        {
            echo '<tr><td>' . $line['id'] . '</td><td><a href="index.php?page=listitems&item_type=' . $_GET['item_type'] . '&item=' . $line['id'] . '">' . $line['name'] . '</a></td></tr>';
        }
        echo'</table></td><td valign="top">';
    }
    
    $query = "SHOW COLUMNS FROM item_stats";
    $result = mysql_query2($query);
    $count = 0;

    while ($line = mysql_fetch_array($result, MYSQL_NUM))
    {
        $columns[$count++] = $line[0];
    }
    
    /**
     * // output headers
     * echo "  <TABLE BORDER=1>";
     * printf( "<TH> Functions</TH>");
     * for($i = 0; $i < sizeof($columns); $i++) { 
     * printf( "<TH> %s</TH>", $columns[$i]); 
     * }
     */ 
     
    // output item data
    $query = "select * from item_stats where id ='" . $_GET['item'] . "'";
    $result = mysql_query2($query);

    if (mysql_num_rows($result) == 0)
    {
        echo "<TABLE BORDER=1><tr><td>Select an item</td></tr></TABLE></td></tr></table>";
    }
    else
    {
        echo '<TABLE BORDER=1>';
        while ($line = mysql_fetch_array($result, MYSQL_NUM))
        {
            for($i = 0; $i < sizeof($line); $i++)
            {
                printf("<tr><td>$columns[$i]</td><td> %s</td></tr>", $line[$i]);
            }
            printf("<FORM ACTION=processcommand.php METHOD=POST>");
            printf("<INPUT TYPE=HIDDEN NAME=id VALUE=%d \">", $line[0]);
            printf("<INPUT TYPE=SUBMIT NAME=submit VALUE=\"EDIT\">");
            printf("<INPUT TYPE=SUBMIT NAME=%s VALUE=\"DELETE\" onclick=\"return confirmDelete()\">", $line[0]);
            printf("</FORM>");
            echo '</TABLE></td></tr></table>';
        }
        /**
         * printf("<TR>");
         * printf("<TD><FORM ACTION=processcommand.php METHOD=POST>");
         * printf("<INPUT TYPE=HIDDEN NAME=id VALUE=%d \">", $line[0]);
         * printf("<INPUT TYPE=SUBMIT NAME=submit VALUE=\"EDIT\">");
         * printf("<INPUT TYPE=SUBMIT NAME=%s VALUE=\"DELETE\" onclick=\"return confirmDelete()\">", $line[0]);
         * for($i = 0; $i < sizeof($line); $i++) {
         * printf( "<TD> %s</TD>", $line[$i]);
         * }
         * printf("</FORM></TR>");
         */
    }

    echo '<br><br>';
}

?>

                  