<?php
function listrecipes()
{
    if (!checkaccess('npcs', 'read'))
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
        return;
    }
    
    $sql = 'SELECT * FROM tribe_recipes';

    $id_url = '';
    if (isset($_GET['id']) && is_numeric($_GET['id'])) 
    {
        $id = escapeSqlString($_GET['id']);
        $sql .= " WHERE id='$id'";
        $id_url = '&amp;id='.$id;
    }
    
    if (isset($_GET['sort']))
    {
        if ($_GET['sort'] == 'id')
        {
            $sql .= ' ORDER BY id';
        }
        else if ($_GET['sort'] == 'name')
        {
            $sql .= ' ORDER BY name';
        }
    }
    
    $sql2 = "SELECT COUNT(*) FROM tribe_recipes".(isset($id) ? " WHERE id='$id'" : '');
    $item_count = fetchSqlRow(mysql_query2($sql2));
    $nav = RenderNav('do=listrecipes'.(isset($id) ? '&id='.$id : '').(isset($_GET['sort']) ? '&sort='.$_GET['sort'] : ''), $item_count[0]);
    $sql .= $nav['sql'];
    echo $nav['html'];
    unset($nav);
    
    $result = mysql_query2($sql);
    if (sqlNumRows($result) > 0)
    {
        echo '<table>'."\n";
        echo '<tr><th><a href="./index.php?do=listrecipes&amp;sort=id'.$id_url.'">ID</a></th><th><a href="./index.php?do=listrecipes&amp;sort=name'.$id_url.'">Name</a></th><th>Requirements</th><th>Algorithm</th><th>Persistent</th><th>Uniqueness</th>';
        if (checkaccess('npcs', 'edit'))
        {
            echo '<th>Actions</th>';
        }
        echo '</tr>'."\n";

        $alt = false;
        while ($row = fetchSqlAssoc($result))
        {
            echo '<tr class="color_'.(($alt = !$alt) ? 'a' : 'b').'">';
            echo '<td>'.$row['id'].'</td>';
            echo '<td>'.$row['name'].'</td>';
            // this replace allows html to display things properly. (It is only replaced in display.)
            echo '<td>'.htmlentities(str_replace(';', '; ', $row['requirements'])).'</td>';
            echo '<td>'.htmlentities(str_replace(';', '; ', $row['algorithm'])).'</td>';
            echo '<td>'.$row['persistent'].'</td>';
            echo '<td>'.$row['uniqueness'].'</td>';
            if (checkaccess('npcs', 'edit'))
            {
                echo '<td><form action="./index.php?do=editrecipes" method="post">';
                echo '<div><input type="hidden" name="id" value="'.$row['id'].'" />';
                echo '<input type="submit" name="commit" value="Edit" /></div>';
                echo '</form>';
                
                if (checkaccess('npcs', 'delete'))
                {
                    echo '<form action="./index.php?do=editrecipes" method="post">';
                    echo '<div><input type="hidden" name="id" value="'.$row['id'].'" />';
                    echo '<input type="submit" name="commit" value="Delete" /></div>';
                    echo '</form>';
                }
                echo '</td>';
            }
            echo '</tr>'."\n";
        }
        echo '</table>'."\n";
        if (checkaccess('npcs', 'create')) 
        {
            echo '<hr />'."\n";
            echo '<p>Create New Tribe Recipe: </p>'."\n";
            echo '<form action="./index.php?do=editrecipes" method="post">'."\n";
            echo '<table border="1">'."\n";
            echo '<tr><th>Field</th><th>Value</th></tr>'."\n";
            echo '<tr><td>Name</td><td><input type="text" name="name" /></td></tr>'."\n";
            echo '<tr><td>Requirements</td><td><textarea name="requirements" rows="6" cols="55"></textarea></td></tr>'."\n";
            echo '<tr><td>Algorithm</td><td><textarea name="algorithm" rows="6" cols="55"></textarea></td></tr>'."\n";
            echo '<tr><td>Persistent</td><td><input type="checkbox" name="persistent" /></td></tr>'."\n";
            echo '<tr><td>Uniqueness</td><td><input type="checkbox" name="uniqueness" /></td></tr>'."\n";
            echo '<tr><td colspan="2"><input type="submit" name="commit" value="Create Tribe Recipe" /></td></tr>'."\n";
            echo '</table>'."\n";
            echo '</form>'."\n";
        }
    }
    else
    {
        echo '<p class="error">No Tribe Recipes Found</p>';
    }
}

function editrecipes() 
{
    if (!checkaccess('npcs', 'edit'))
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
        return;
    }
    if (!isset($_POST['commit'])) 
    {
        echo '<p class="error">Invalid Instruction</p>';
        return;
    }
    
    if ($_POST['commit'] == 'Create Tribe Recipe')
    {
        if (!checkaccess('npcs', 'create'))
        {
            echo '<p class="error">You are not authorized to use these functions</p>';
            return;
        }
        $name = escapeSqlString($_POST['name']);
        $requirements = escapeSqlString($_POST['requirements']);
        $algorithm = escapeSqlString($_POST['algorithm']);
        $persistent = (isset($_POST['persistent']) ? 1 : 0);
        $uniqueness = (isset($_POST['uniqueness']) ? 1 : 0);
        $query = "INSERT INTO tribe_recipes SET name='$name', requirements='$requirements', algorithm='$algorithm', persistent='$persistent', uniqueness='$uniqueness'";
        mysql_query2($query);
        echo '<p class="error">Tribe Recipe Successfully Created</p>';
        unset($_POST);
        listrecipes();
        return;
    }
    else if ($_POST['commit'] == 'Delete')
    {
        if (!checkaccess('npcs', 'delete'))
        {
            echo '<p class="error">You are not authorized to use these functions</p>';
            return;
        }
        if (!isset($_POST['id']) || !is_numeric($_POST['id']))
        {
            echo '<p class="error">Invalid ID, cannot delete.</p>';
            return;
        }
        $id = escapeSqlString($_POST['id']);
        $query = "DELETE FROM tribe_recipes WHERE id='$id' LIMIT 1";
        mysql_query2($query);
        echo '<p class="error">Tribe Recipe With ID '.$id.' Successfully Deleted</p>';
        unset($_POST);
        listrecipes();
        return;
    }
    else if ($_POST['commit'] == 'Edit')
    {
        if (!isset($_POST['id']) || !is_numeric($_POST['id']))
        {
            echo '<p class="error">Invalid ID, cannot edit.</p>';
            return;
        }
        $id = escapeSqlString($_POST['id']);
        $query = "SELECT * FROM tribe_recipes WHERE id='$id'";
        $result = mysql_query2($query);
        if (sqlNumRows($result) == 0)
        {
            echo '<p class="error">No database entry with ID '.$id.', cannot edit</p>';
            return;
        }
        $row = fetchSqlAssoc($result);
        echo '<p>Edit Tribe Recipe With ID '.$id.' </p>';
        echo '<form action="./index.php?do=editrecipes" method="post">';
        echo '<table border="1">';
        echo '<tr><th>Field</th><th>Value</th></tr>';
        echo '<tr><td>Name</td><td><input type="text" name="name" value="'.$row['name'].'" /></td></tr>';
        echo '<tr><td>Requirements</td><td><textarea name="requirements" rows="6" cols="55">'.htmlentities($row['requirements']).'</textarea></td></tr>';
        echo '<tr><td>Algorithm</td><td><textarea name="algorithm" rows="6" cols="55">'.htmlentities($row['algorithm']).'</textarea></td></tr>';
        $persistent = ($row['persistent'] == 1 ? 'checked="checked"' : '');
        echo '<tr><td>Persistent</td><td><input type="checkbox" name="persistent" '.$persistent.' /></td></tr>';
        $uniqueness = ($row['uniqueness'] == 1 ? 'checked="checked"' : '');
        echo '<tr><td>Uniqueness</td><td><input type="checkbox" name="uniqueness" '.$uniqueness.' /></td></tr>';
        echo '<tr><td colspan="2"><input type="hidden" name="id" value="'.$id.'" /><input type="submit" name="commit" value="Update" /></td></tr>';
        echo '</table>';
        echo '</form>';
        return;
    }
    else if ($_POST['commit'] == 'Update')
    {
        if (!isset($_POST['id']) || !is_numeric($_POST['id']))
        {
            echo '<p class="error">Invalid ID, cannot commit edit.</p>';
            return;
        }
        $id = escapeSqlString($_POST['id']);
        $name = escapeSqlString($_POST['name']);
        $requirements = escapeSqlString($_POST['requirements']);
        $algorithm = escapeSqlString($_POST['algorithm']);
        $persistent = (isset($_POST['persistent']) ? 1 : 0);
        $uniqueness = (isset($_POST['uniqueness']) ? 1 : 0);
        $query = "UPDATE tribe_recipes SET name='$name', requirements='$requirements', algorithm='$algorithm', persistent='$persistent', uniqueness='$uniqueness' WHERE id='$id'";
        mysql_query2($query);
        echo '<p class="error">Tribe Recipe Successfully Updated</p>';
        unset($_POST);
        listrecipes();
        return;
    }
}
?>
