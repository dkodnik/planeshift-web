<?
function listitemcategories()
{


    checkAccess('main', '', 'read');
?>

<P>Item categories can only be deleted if there are no
items in that category. Items in the category list
show how many items using that category.
</P>
<P>Item categories can only be deleted if there are no
merchants seling/buying that category. Merchants in the category list
show how merchants using that category.
</P>

<SCRIPT language=javascript>

function confirmDelete()
{
    return confirm("Are you sure you want to delete this category?");
}

</SCRIPT>

<?PHP

    include "config.php";

    $link = mysql_connect($db_hostname,
                          $db_username,
                          $db_password);

    mysql_select_db($db_name);

    $query = "select category_id, name from item_categories order by name";
    $result = mysql_query2($query);

    echo '  <TABLE BORDER=1>';
    echo "  <TH> Id </TH> <TH> Items </TH><TH> Merchants </TH><TH> Name </TH> <TH> Functions </TH>";

    while ($line = mysql_fetch_array($result, MYSQL_NUM))
    { 
        // Find number of items in category
        $used = "select COUNT(category_id) from item_stats where category_id='$line[0]'";
        $num_items = mysql_result(mysql_query2($used), 0); 
        // Find number of merchants selling/buying category
        $used = "select COUNT(category_id) from merchant_item_categories where category_id='$line[0]'";
        $num_merchants = mysql_result(mysql_query2($used), 0);

        echo '<TR>';
        echo "<TD>$line[0]</TD><TD>$num_items</TD><TD>$num_merchants</TD>";
        echo "<FORM ACTION=index.php?page=itemcategory_actions&operation=update METHOD=POST>";
        echo "<INPUT TYPE=hidden NAME=category_id VALUE=\"$line[0]\">";
        echo "<TD><INPUT TYPE=TEXT NAME=name VALUE=\"$line[1]\"></TD>";
        echo "<TD VALIGN=LEFT><INPUT TYPE=SUBMIT NAME=submit VALUE=Update></FORM>"; 
        // Delete
        // Check if this category is used
        if ($num_items == 0 && $num_merchants == 0)
        {
            echo "<FORM ACTION=index.php?page=itemcategory_actions&operation=delete METHOD=POST onsubmit=\"return confirmDelete()\">";
            echo "<INPUT TYPE=hidden NAME=category_id VALUE=\"$line[0]\">";
            echo "<INPUT TYPE=SUBMIT NAME=submit VALUE=Delete>";
            echo "</FORM>";
        }
          echo '</TD></TR>';
    }
    echo "<TR><TD></TD><TD></TD><TD></TD><TD>";
    echo "<FORM ACTION=index.php?page=itemcategory_actions&operation=create METHOD=POST>";
    echo "<INPUT TYPE=text NAME=name></TD>";
    echo "<TD><INPUT TYPE=SUBMIT NAME=submit VALUE=Create>";
    echo '</FORM></TD></TR>';

    echo '</TABLE><br><br>';

    echo '<br><br>';
}

?>
