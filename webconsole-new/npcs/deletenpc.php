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
        $info = fetchSqlAssoc(mysql_query2($sql));
        
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
                    // Remove all character related entries from the DB
                    // Trick to find tables:
                    // SELECT * FROM information_schema.`COLUMNS` C WHERE TABLE_SCHEMA = 'planeshift' AND COLUMN_NAME='player_id';
                    // SELECT * FROM information_schema.`COLUMNS` C WHERE TABLE_SCHEMA = 'planeshift' AND COLUMN_NAME='character_id';
                    // SELECT * FROM information_schema.`COLUMNS` C WHERE TABLE_SCHEMA = 'planeshift' AND COLUMN_NAME='char_id';

                    // Start with the stuff that is editable from the NPC details view

                    // Remove skills
                    $sql_del_skills = 'DELETE FROM character_skills WHERE character_id='.$id;
                    mysql_query2($sql_del_skills);

                    // Remove traits
                    $sql_del_character_traits = 'DELETE FROM character_traits WHERE character_id='.$id;
                    mysql_query2($sql_del_character_traits);

                    // Remove factions
                    $sql_del_character_factions = 'DELETE FROM character_factions WHERE character_id='.$id;
                    mysql_query2($sql_del_character_factions);

                    // Remove knowledge areas
                    $sql_del_knowledge_areas = 'DELETE FROM npc_knowledge_areas WHERE player_id='.$id;
                    mysql_query2($sql_del_knowledge_areas);

                    // Remove item instances
                    $sql_del_item_instances = 'DELETE FROM item_instances WHERE char_id_owner='.$id;
                    mysql_query2($sql_del_item_instances);

                    // Remove trainer skills
                    $sql_del_trainer_skills = 'DELETE FROM trainer_skills WHERE player_id='.$id;
                    mysql_query2($sql_del_trainer_skills);

                    // Remove merchant item categories
                    $sql_del_merchant_item_categories = 'DELETE FROM merchant_item_categories WHERE player_id='.$id;
                    mysql_query2($sql_del_merchant_item_categories);

                    // Other stuff to delete, most will contain any data but we would not like to leave any thing

                    // Remove from npc definitions
                    $sql_del_npc_definitions = 'DELETE FROM sc_npc_definitions WHERE char_id='.$id;
                    mysql_query2($sql_del_npc_definitions);

                    // Remove from tribe members
                    $sql_del_tribe_members = 'DELETE FROM tribe_members WHERE member_id='.$id;
                    mysql_query2($sql_del_tribe_members);

                    // Remove from character relationships
                    $sql_del_character_relationships = 'DELETE FROM character_relationships WHERE character_id='.$id;
                    mysql_query2($sql_del_character_relationships);

                    // Remove from character variables
                    $sql_del_character_variables = 'DELETE FROM character_variables WHERE character_id='.$id;
                    mysql_query2($sql_del_character_variables);

                    // Remove from character events
                    $sql_del_character_events = 'DELETE FROM character_events WHERE player_id='.$id;
                    mysql_query2($sql_del_character_events);

                    // Remove from character quests
                    $sql_del_character_quests = 'DELETE FROM character_quests WHERE player_id='.$id;
                    mysql_query2($sql_del_character_quests);

                    // Remove from player spells
                    $sql_del_player_spells = 'DELETE FROM player_spells WHERE player_id='.$id;
                    mysql_query2($sql_del_player_spells);

                    // Final stage

                    // Remove the character
                    $sql_del_characters = 'DELETE FROM characters WHERE id='.$id;
                    mysql_query2($sql_del_characters);
                    
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
