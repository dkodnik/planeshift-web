<?php
function deletecategory()
{
    if (!checkaccess('items', 'delete'))
    {
        echo '<p class="error">You are not authorized to use these functions.</p>';
    }
    if (isset($_GET['id']))
    {
        if (isset($_GET['commit']) && isset($_POST['pwd']))
        {
            $id = escapeSqlString($_GET['id']);
            $pwd = escapeSqlString($_POST['pwd']);
            $query = 'SELECT COUNT(*) FROM item_stats WHERE category_id='.$id;
            $result = mysql_query2($query);
            $row = fetchSqlRow($result);
            if ($row[0] != 0)
            {
                include('./items/editcategory.php');
                echo '<p class="error">Error: category has items assigned, unable to delete, returning to category listing</p>';
                unset($_GET['commit']);
                editcategory();
                return;
            }
            $query = 'SELECT COUNT(*) FROM merchant_item_categories WHERE category_id='.$id;
            $result = mysql_query2($query);
            $row = fetchSqlRow($result);
            if ($row[0] != 0)
            {
                include('./items/editcategory.php');
                echo '<p class="error">Error: Category has merchants assigned, unable to delete, returning to category listing</p>';
                unset($_GET['commit']);
                editcategory();
                return;
            }
            $username = $_SESSION['username'];
            $query = "SELECT count(username) FROM accounts WHERE username='$username' AND password=MD5('$pwd')";
            $result = mysql_query2($query);
            $row = fetchSqlRow($result);
            if ($row[0] != 1)
            {
                include('./items/editcategory.php');
                echo '<p class="error">Error: Password check failed, unable to delete, returning to category listing</p>';
                unset($_GET['commit']);
                editcategory();
                return;
            }
            $query = 'DELETE FROM item_categories WHERE category_id='.$id;
            $result = mysql_query2($query);
?>
    <script language="javascript">
      document.location = "index.php?do=editcategory";
    </script>
<?php
            exit;
        }
        else
        {
            $id = escapeSqlString($_GET['id']);
            $query = 'SELECT name, category_id FROM item_categories WHERE category_id='.$id;
            $result = mysql_query2($query);
            $row = fetchSqlAssoc($result);
            echo '<p>You are about to delete permenently Item Category: '.$row['name'].'</p>';
            echo '<form action="./index.php?do=deletecategory&amp;id='.$id.'&amp;commit" method="post">';
            echo '<p>Enter your password to confirm: <input type="password" name="pwd" /><input type="submit" name="submit" value="confirm" /></p></form>';
        }
    }
    else
    {
        echo '<p class="error">Error: No category_id Selected, returning to category listing</p>';
        include('./items/editcategory.php');
        editcategory();
    }

}
?>
