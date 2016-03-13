<?php

function lifeeventrelations()
{
        // block unauthorized access
    if (isset($_POST['commit']) && !checkaccess('other', 'edit')) 
    {
        echo '<p class="error">You are not authorized to edit Life Event Relations</p>';
        return;
    }
    
    // after the handling of commit, the script will resume with the listing of all life relations.
    if (isset($_POST['commit']) && $_POST['commit'] == 'Create Relation')
    {
        $choice = escapeSqlString($_POST['choice']);
        $adds_choice = escapeSqlString($_POST['adds_choice']);
        $removes_choice = escapeSqlString($_POST['removes_choice']);
        if ($choice == '' || $choice == $adds_choice || $choice == $removes_choice || $adds_choice == $removes_choice)
        {
            echo '<p class="error">Invalid combination, relation was *not* added.</p>';
        }
        else
        {
            $sql = "INSERT INTO char_create_life_relations (choice, adds_choice, removes_choice) VALUES ('$choice', ";
            $sql .= ($adds_choice == '' ? 'NULL' : "'$adds_choice'").", ";
            $sql .= ($removes_choice == '' ? 'NULL' : "'$removes_choice'").")";
            mysql_query2($sql);
            echo '<p class="error">Relation added.</p>';
        }
    }
    elseif (isset($_POST['commit']) && $_POST['commit'] == 'Confirm Delete')
    {
        $choice = escapeSqlString($_POST['choice']);
        $adds_choice = escapeSqlString($_POST['adds_choice']);
        $removes_choice = escapeSqlString($_POST['removes_choice']);
        $sql = "DELETE FROM char_create_life_relations WHERE choice='$choice' ";
        $sql .= "AND adds_choice".($adds_choice == '' ? ' IS NULL' : "='$adds_choice'");
        $sql .= " AND removes_choice".($removes_choice == '' ? ' IS NULL' : "='$removes_choice'");
        mysql_query2($sql);
        echo '<p class="error">Delete succesfull</p>';
    }
    // No edit, since this table has no unique KEY (all fields together are the key).
    
    // if we print something for any of these actions, nothing else gets printed (no life event list).
    if (isset($_GET['action']) && $_GET['action'] == 'delete')
    {
        // confirm delete
        $choice = escapeSqlString($_GET['choice']);
        $adds_choice = escapeSqlString($_GET['adds_choice']);
        $removes_choice = escapeSqlString($_GET['removes_choice']);
        echo '<p class="error">You are about to delete Life relation choice: "'.$choice.'" adds_choice: "'.$adds_choice.'" removes_choice: "'.$removes_choice.'" </p>';
        echo '<form action="./index.php?do=lifeeventrelations" method="post">';
        echo '<div><input type="hidden" name="choice" value="'.$choice.'" /><input type="hidden" name="adds_choice" value="'.$adds_choice.'" />';
        echo '<input type="hidden" name="removes_choice" value="'.$removes_choice.'" /><input type="submit" name="commit" value="Confirm Delete" /></div>';
        echo '</form>';
        return;
    }
    
    // Display the main list
    $sql = "SELECT choice, ccl1.name AS choice_name, adds_choice, ccl2.name AS adds_choice_name, removes_choice, ccl3.name AS removes_choice_name FROM char_create_life_relations AS cclr ";
    $sql .= "LEFT JOIN char_create_life AS ccl1 ON ccl1.id=cclr.choice LEFT JOIN char_create_life AS ccl2 ON ccl2.id=cclr.adds_choice ";
    $sql .= "LEFT JOIN char_create_life AS ccl3 ON ccl3.id=cclr.removes_choice ORDER BY choice";
    $result = mysql_query2($sql);
    
    if (sqlNumRows($result) == 0)
    {
        echo '<p class="error">No Character Life Relation data found.</p>';
    }
    else
    {
        // main list
        echo '<p> Multiple entries per choice are allowed. Editing is not available due to the nature of the table.</p>';
        echo '<table border="1">'."\n";
        echo '<tr><th>Choice</th><th>Adds Choice</th><th>Removes Choice</th><th>Actions</th></tr>'."\n";
        
        while ($row = fetchSqlAssoc($result))
        {
            echo '<tr>';
            if (checkAccess('other', 'edit'))
            {
                echo '<td><a href="./index.php?characterlifeevents&amp;action=edit&amp;id='.$row['choice'].'">'.htmlentities($row['choice_name']).'</a></td>';
                echo '<td><a href="./index.php?characterlifeevents&amp;action=edit&amp;id='.$row['adds_choice'].'">'.htmlentities($row['adds_choice_name']).'</a></td>';
                echo '<td><a href="./index.php?characterlifeevents&amp;action=edit&amp;id='.$row['removes_choice'].'">'.htmlentities($row['removes_choice_name']).'</a></td>';
            }
            else
            {
                echo '<td>'.htmlentities($row['choice_name']).'</td>';
                echo '<td>'.htmlentities($row['adds_choice_name']).'</td>';
                echo '<td>'.htmlentities($row['removes_choice_name']).'</td>';
            }
            echo '<td>';
            if (checkAccess('other', 'edit'))
            {
                $url = './index.php?do=lifeeventrelations&amp;choice='.$row['choice'].'&amp;adds_choice='.$row['adds_choice'].'&amp;removes_choice='.$row['removes_choice'];
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
        echo '<hr/><p>Create new Event Relation: </p>';
        echo '<form action="./index.php?do=lifeeventrelations" method="post">';
        echo '<table border="1">';
        echo '<tr><th>Field</th><th>Value</th></tr>';
        $liferelations = PrepSelect('liferelations');
        echo '<tr><td>Choice</td><td>'.DrawSelectBox('liferelations', $liferelations, 'choice', '', true).'</td></tr>';
        echo '<tr><td>Adds Choice</td><td>'.DrawSelectBox('liferelations', $liferelations, 'adds_choice', '', true).'</td></tr>';
        echo '<tr><td>Removes Choice</td><td>'.DrawSelectBox('liferelations', $liferelations, 'removes_choice', '', true).'</td></tr>';
        echo '<tr><td colspan="2"><input type="submit" name="commit" value="Create Relation" /></td></tr>';
        echo '</table>';
        echo '</form>';
    }
}

?>