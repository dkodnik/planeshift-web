<?php
function createquest()
{
    if(!checkaccess('quests','create'))
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
        return;
    }
    
    if(!isset($_GET['commit']))
    {
        echo '<p class="header">Creating a new Quest</p>'."\n";
        echo '<form action="index.php?do=createquest&amp;commit" method="post">'."\n";
        echo '<p class="bold">Quest ID will be generated automatically</p>'."\n";
        echo '<div><table border="0"> <tr><td>Quest Name:</td><td> <input type="text" name="name" size="30"/></td></tr>'."\n";
        echo '<tr><td>Category:</td><td> <input type="text" name="category" size="30"/></td></tr>'."\n";
        echo '<tr><td>Player Lockout:</td><td> <input type="text" name="player_lockout" size="30"/></td></tr>'."\n";
        echo '<tr><td>Quest Lockout:</td><td> <input type="text" name="quest_lockout" size="30"/></td></tr>'."\n";
        echo '<tr><td>Quest Description:</td><td> <textarea rows="2" cols="40" name="description"></textarea></td></tr>'."\n";
        echo '<tr><td><input type="submit" name="submit" value="Create Quest" /></td><td></td></tr></table></div>'."\n";
        echo '</form>'."\n";
    }
    else
    {
        //Here we create the quest
        $name = escapeSqlString($_POST['name']);
        $player_lockout = escapeSqlString($_POST['player_lockout']);
        $quest_lockout = escapeSqlString($_POST['quest_lockout']);
        $category = escapeSqlString($_POST['category']);
        $description = escapeSqlString($_POST['description']);
        $query = "INSERT INTO quests (name, task, player_lockout_time, quest_lockout_time, category) VALUES ('$name', '$description', '$player_lockout', '$quest_lockout', '$category')";
        $result = mysql_query2($query);
        $id = sqlInsertId();
        $query = "INSERT INTO quest_scripts (quest_id, script) VALUES ('$id', '#New Quest - Please Update')";
        $result = mysql_query2($query);

        unset($_POST);
        unset($_GET['commit']);
        $_GET['id'] = $id;
        include("editquest.php");
        editquest();
    }
}
?>
