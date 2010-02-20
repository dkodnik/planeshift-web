<?php

function deletenpc()
{
    if(checkaccess('npcs', 'delete'))
    {
        $id = (isset($_GET['id']) && is_numeric($_GET['id']) ? $_GET['id'] : 'nan');
        $sure = (isset($_GET['sure']) && $_GET['sure'] == 'yes');
        $password = (isset($_POST['password']) ? $_POST['password'] : '');
        
        echo '<p class="header">Delete NPC</p>';
        if($id == 'nan')
        {
            if(isset($_GET['id']))
            {
                echo '<p class="error">There is no NPC with ID '.$_GET['id'].'</p>';
                return;
            }
            else
            {
                echo '<p class="error">You have to specify the NPC\'s ID to delete it!</p>';
                return;
            }
        }
        
        $sql = 'SELECT name, character_type FROM characters WHERE id='.$id;
        $info = mysql_fetch_array(mysql_query2($sql), MYSQL_ASSOC);
        
        if($info['character_type'] != 1 && $info['character_type'] != 3)
        {
            echo '<p class="error">You can only delete NPCs and Mounts but the character you wanted to delete had the type ID "'.$info['character_type'].'".</p>';
            return;
        }
        
        if($sure)
        {
            if(empty($password))
            {
                echo '<p class="error">You have to enter the password.</p>';
            }
            else
            {
                if(CheckPassword($password))
                {
                    $sql = 'DELETE FROM characters WHERE id='.$id;
                    mysql_query2($sql);
                    
                    echo 'The NPC "'.htmlentities($info['name']).'" was successfully deleted.';
                    return;
                }
                else
                {
                    echo '<p class="error">The password you entered is wrong.</p>';
                }
            }
        }
        echo '<form action="index.php?do=deletenpc&sure=yes&id='.$id.'" method="post">';
        echo 'To delete the NPC "'.htmlentities($info['name']).'" you have to enter your password:<br/>';
        echo '<input type="password" name="password" /><br/>';
        echo '<input type="submit" value="delete NPC" /></form>';
    }
    else
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
    }
}

?>
