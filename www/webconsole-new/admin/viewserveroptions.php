<?PHP
function viewserveroptions()
{
    if (checkaccess('admin', 'read'))
    {
        if(isset($_POST['submit']) && $_POST['submit'] == 'Save')
        {
            if (checkaccess('admin', 'edit'))
            {
                for ($i = 0; count($_POST['data']) > $i; $i++)
                {
                    $option = escapeSqlString($_POST['option'][$i]);
                    $data = escapeSqlString($_POST['data'][$i]);
                    if ($option == '' || $data == '')
                    {
                        echo '<p class="error">I ignored 1 empty line. You cannot leave lines empty.</p>';
                        continue;
                    }
                    $query = "UPDATE server_options SET option_value = '$data' WHERE option_name = '$option'";
                    mysql_query2($query);
                }
                echo '<p class="error">Server options successfully updated.</p>';
            }
            else
            {
                echo '<p class="error">You are not authorized to edit this page.</p>';
            }
        }
        
        $query = 'select * from server_options order by option_name';
        $result = mysql_query2($query);
        echo '<form action="./index.php?do=viewserveroptions" method="post">';
        echo '<table width="500" border="0"><tr><td>Option name</td><td>Option value</td></tr>';
        while ($row = fetchSqlAssoc($result))
        {
            echo '<tr><td width="150">'.$row['option_name'].'</td>';
            echo '<td width="350"><input type="hidden" name="option[]" value="'.$row['option_name'].'"/><input size="90" type="text" name="data[]" value="'.$row['option_value'].'"/></td></tr>';
        }
        echo '</table>';
        echo '<input type="submit" name="submit" value="Save"/></form>';
    }
}

?>
