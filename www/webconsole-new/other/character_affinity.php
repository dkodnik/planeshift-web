<?php

function characteraffinity()
{
    // block unauthorized access
    if (!checkaccess('other', 'read')) 
    {
        echo '<p class="error">You are not authorized to view Character affinities</p>';
        return;
    }
    if (isset($_POST['commit']) && !checkaccess('other', 'edit')) 
    {
        echo '<p class="error">You are not authorized to edit Character affinities</p>';
        return;
    }
    
    // after the handling of commit, the script will resume with the listing of all affinities.
    if (isset($_POST['commit']) && $_POST['commit'] == 'Create Affinity')
    {
        $attribute = escapeSqlString($_POST['attribute']);
        $category = escapeSqlString($_POST['category']);
        $sql = "INSERT INTO char_create_affinity (attribute, category) VALUES ('$attribute', '$category')";
        mysql_query2($sql);
        echo '<p class="error">Affinity added.</p>';
    }
    elseif (isset($_POST['commit']) && $_POST['commit'] == 'Confirm Delete')
    {
        $category = escapeSqlString($_POST['category']);
        $sql = "DELETE FROM char_create_affinity WHERE category='$category'";
        mysql_query2($sql);
        echo '<p class="error">Delete succesfull</p>';
    }
    // no edit, it doesn't make sense with these fields.
    
    // if we print something for any of these actions, nothing else gets printed (no affinity list).
    if (isset($_GET['action']) && $_GET['action'] == 'delete')
    {
        // confirm delete
        $category = escapeSqlString($_GET['category']);
        echo '<p class="error">You are about to delete Affinity category "'.$category.'" </p>';
        echo '<form action="./index.php?do=characteraffinity" method="post">';
        echo '<div><input type="hidden" name="category" value="'.$category.'" /><input type="submit" name="commit" value="Confirm Delete" /></div>';
        echo '</form>';
        return;
    }
    
    // Display the main list
    $sql = "SELECT attribute, category FROM char_create_affinity ORDER BY attribute, category";
    $result = mysql_query2($sql);
    
    if (sqlNumRows($result) == 0)
    {
        echo '<p class="error">No Character Affinity data found.</p>';
    }
    else
    {
        // main list
        echo '<table border="1">'."\n";
        echo '<tr><th>Attribute</th><th>Category</th><th>Actions</th></tr>'."\n";
        
        while ($row = fetchSqlAssoc($result))
        {
            echo '<tr>';
            echo '<td>'.htmlentities($row['attribute']).'</td>';
            echo '<td>'.htmlentities($row['category']).'</td>';
            echo '<td>';
            if (checkAccess('other', 'edit'))
            {
                $url = './index.php?do=characteraffinity&amp;category='.$row['category'];
                echo '<a href="'.$url.'&amp;action=delete">Delete</a>';
            }
            echo '</td>';
            echo '</tr>'."\n";
        }
        echo '</table>'."\n";
    }
    if (checkAccess('other', 'edit'))
    {
        // create form
        echo '<hr/><p>Create new Affinity: </p>';
        echo '<form action="./index.php?do=characteraffinity" method="post">';
        echo '<table border="1">';
        echo '<tr><th>Field</th><th>Value</th></tr>';
        echo '<tr><td>Attribute</td><td><input type="text" name="attribute" /></td></tr>';
        echo '<tr><td>Category</td><td><input type="text" name="category" /></td></tr>';
        echo '<tr><td colspan="2"><input type="submit" name="commit" value="Create Affinity" /></td></tr>';
        echo '</table>';
        echo '</form>';
    }
}

?>