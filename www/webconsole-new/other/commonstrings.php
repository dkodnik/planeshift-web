<?php

//pending a decision on what to do with this, this code is unused since there is no more common_strings table, but the information is still out there.
function listcommonstrings()
{
    if (checkaccess('other', 'read'))
    {
        $page = (isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 0);
        $items_per_page = (isset($_GET['items_per_page']) && is_numeric($_GET['items_per_page']) ? $_GET['items_per_page'] : 30);
        
        $sql = 'SELECT COUNT(*) FROM common_strings';
        $page_count = fetchSqlRow(mysql_query2($sql));
        $page_count = ceil($page_count[0] / $items_per_page);
        
        if($page > $page_count)
        {
            $page = ($page_count - 1);
        }
        if($page < 0)
        {
            $page = 0;
        }
        
        $sql = 'SELECT id, string FROM common_strings ORDER BY id LIMIT '.($page * $items_per_page).', '.$items_per_page;
        $query = mysql_query2($sql);
        
        
        echo '<p class="header">List Common Strings</p>';
        
        echo '<form action="./index.php" method="get">';
        echo '<input type="hidden" name="do" value="listcommonstrings" />';
        echo '<input type="hidden" name="page" value="'.$page.'" />';
        echo 'Items per page: <input type="text" name="items_per_page" value="'.$items_per_page.'" size="5" />';
        echo '</form>';
        
        echo 'Page: ';
        for($i = 0; $i < $page_count; $i++) {
            if($i == $page)
            {
                echo ($i+1).' ';
            }
            else
            {
                echo '<a href="./index.php?do=listcommonstrings&items_per_page='.$items_per_page.'&page='.$i.'">'.($i+1).'</a> ';
            }
            echo ($i == ($page_count -1) ? '' : ' | ');
        }
        echo '<br/><br/>';
        
        echo '<a href="./index.php?do=addcommonstrings">Add a common string</a>';
        echo '<table>';
        echo '<tr><th>ID</th><th>String</th><th>Actions</th></tr>';
        $color = 'b';
        while($row = fetchSqlAssoc($query)) {
            $color = ($color == 'a' ? 'b' : 'a');
            echo '<tr class="color_'.$color.'">';
            echo '<td>'.$row['id'].'</td>';
            echo '<td>'.htmlentities($row['string']).'</td>';
            echo '<td><a href="./index.php?do=editcommonstrings&id='.intval($row['id']).'">Edit</a> ';
            echo '<a href="./index.php?do=deletecommonstrings&id='.intval($row['id']).'">Delete</a> ';
            echo '</tr>';
        }
        echo '</table>';
    }
    else
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
    }
}

function addcommonstrings()
{
    if(!checkaccess('other', 'create'))
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
        return false;
    }
    $form_sent = (isset($_GET['form_sent']) && $_GET['form_sent'] == 'yes');
    $string = (isset($_POST['string']) ? $_POST['string'] : '');
    $message = '';
    
    echo '<p class="header">Add A Common String</p>';
    if($form_sent)
    {
        if(empty($string))
        {
            $message = '<p class="error">You have to enter a string!</p>';
        }
        else
        {
            $_string = escapeSqlString($string);
            $sql = 'SELECT id FROM common_strings WHERE string = \''.$_string.'\' LIMIT 1';
            $query = mysql_query2($sql);
            if(sqlNumRows($query) > 0)
            {
                $message = '<p class="error">You have to enter an unique string!</p>';
            }
            else
            {
                $sql = 'INSERT INTO common_strings (string) VALUES (\''.$_string.'\')';
                $query = mysql_query2($sql);
                $message = '<p style="color: green;">The string was added!</p>'; // FIXME: Why isn't there a "success" class?
            }
        }
    }
    echo '<a href="./index.php?do=listcommonstrings">Back</a>';
    echo $message;
    echo '<form action="index.php?do=addcommonstrings&form_sent=yes" method="post">';
    echo 'String: <input type="text" name="string" value="'.htmlentities($string).'" />';
    echo '<input type="submit" value="Add" /></form>';
}

function editcommonstrings()
{
    if(!checkaccess('other', 'edit'))
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
        return false;
    }
    $form_sent = (isset($_GET['form_sent']) && $_GET['form_sent'] == 'yes');
    $id = (isset($_GET['id']) && is_numeric($_GET['id']) ? $_GET['id'] : 'nan');
    $string = (isset($_POST['string']) ? $_POST['string'] : '');
    
    echo '<p class="header">Edit Common Strings</p>';
    if(empty($id) || $id == 'nan')
    {
        echo '<p class="error">You have to specify a valid ID to edit!</p>';
        echo '<a href="./index.php?do=listcommonstrings">Back</a>';
    }
    else
    {
        $message = '';
        if($form_sent)
        {
            if(empty($string))
            {
                $message = '<p class="error">You can\'t enter an empty string!</p>';
            }
            else
            {
                $string = escapeSqlString($string);
                $sql = 'UPDATE common_strings SET string = \''.$string.'\' WHERE id = '.$id;
                $query = mysql_query2($sql);
                // FIXME: Why isn't there a "success" class?
                $message = '<p style="color: green;">Your changes have been submitted to the database.</p>';
            }
        }
        $sql = 'SELECT string FROM common_strings WHERE id = '.$id.' LIMIT 1';
        $row = fetchSqlAssoc(mysql_query2($sql));
        
        echo '<a href="./index.php?do=listcommonstrings">Back</a>';
        echo $message;
        echo '<form action="./index.php?do=editcommonstrings&id='.$id.'&form_sent=yes" method="post">';
        echo '<table>';
        echo '<tr class="color_a"><td>ID: </td><td>'.$id.'</td></tr>';
        echo '<tr class="color_b"><td>String: </td><td><input type="text" name="string" value="'.htmlentities($row['string']).'"/></td></tr>';
        echo '</table>';
        echo '<input type="submit" value="Save" /></form>';
    }
}

function deletecommonstrings()
{
    if(!checkaccess('other', 'delete'))
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
        return false;
    }
    $id = (isset($_GET['id']) && is_numeric($_GET['id']) ? $_GET['id'] : 'nan');
    $sure = (isset($_GET['answer']) && $_GET['answer'] == 'yes');
    
    echo '<p class="header">Delete A Common String</p>';
    if(empty($id) || $id == 'nan')
    {
        echo '<p class="error">You have to specify an ID to delete!</p>';
        echo '<a href="./index.php?do=listcommonstrings">Back</a>';
    }
    elseif($sure)
    {
        $sql = 'DELETE FROM common_strings WHERE id = '.$id.' LIMIT 1';
        $query = mysql_query2($sql);
        echo '<p style="color: green;">The string was successfully deleted.</p>';
        echo '<a href="./index.php?do=listcommonstrings">Back</a>';
    }
    else
    {
        echo '<form action="./index.php?do=deletecommonstrings&id='.$id.'&answer=yes" method="post">';
        echo 'Are you sure you want to delete the string with ID '.$id.'?<br/>';
        echo '<input type="submit" value="Yes" /><a href="./index.php?do=listcommonstrings">No</a>';
        echo '</form>';
    }
}
?>