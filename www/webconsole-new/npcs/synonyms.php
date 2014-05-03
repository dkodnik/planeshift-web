<?php
function synonyms(){
    if (!checkaccess('npcs', 'read'))
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
        return;
    }
    if (isset($_POST['commit']))
    {
        if (!checkaccess('npcs', 'edit'))
        {
            echo '<p class="error">You are not authorized to use these functions</p>';
            return;
        }
        if ($_POST['commit'] == 'Delete')
        {
            $word = mysql_real_escape_string($_POST['word']);
            $syn = mysql_real_escape_string($_GET['syn']);
            $query = "DELETE FROM npc_synonyms WHERE word='$word' AND synonym_of='$syn'";
        }
        else if ($_POST['commit'] == 'Add Synonym')
        {
            $word = mysql_real_escape_string($_POST['new_syn']);
            $syn = mysql_real_escape_string($_GET['syn']);
            $query = "INSERT INTO npc_synonyms (synonym_of, word) VALUES ('$syn', '$word')";
        }
        else if ($_POST['commit'] == 'New Base Phrase')
        {
            $word = mysql_real_escape_string(strtolower($_POST['new_syn']));
            $syn = mysql_real_escape_string(strtolower($_POST['new_phrase']));
            $query = "INSERT INTO npc_synonyms (synonym_of, word) VALUES ('$syn', '$word')";
            $_GET['syn'] = $syn;
        }
        else
        {
            unset($_POST);
            echo '<p class="error">No Commit Specified</p>';
            synonyms();
            return;
        }
        $result = mysql_query2($query);
        unset($_POST);
        synonyms();
    }
    else
    {
        if (isset($_GET['reverse']))
        {
            echo '<p>Currently displaying the "player types" list, to show the "server parses" list, click <a href="./index.php?do=synonyms">here</a></p>'."\n";
            $query = 'SELECT word, synonym_of FROM npc_synonyms ORDER BY word';
            $result = mysql_query2($query);
            $alt = false;
            echo '<table><tr><th>Player Types</th><th>Server Parses</th></tr>'."\n";
            while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
            {
                echo '<tr class="color_'.(($alt = !$alt) ? 'a' : 'b').'">';
                echo '<td>'.htmlentities($row['word']).'</td>';
                echo '<td><a href="./index.php?do=synonyms&amp;syn='.rawurlencode($row['synonym_of']).'">'.htmlentities($row['synonym_of']).'</a></td>';
                echo '</tr>'."\n";
            }
            echo '</table>'."\n";
            return;
        }
        else
        {
            echo '<p>Currently displaying the "server parses" list, to show the "player types" list, click <a href="./index.php?do=synonyms&amp;reverse">here</a></p>';
        }
        $query = 'SELECT DISTINCT synonym_of FROM npc_synonyms ORDER BY synonym_of';
        $result = mysql_query2($query);
        if (isset($_GET['syn']))
        {
            $syn = mysql_real_escape_string($_GET['syn']);
        }
        else
        {
            $syn = "";
        }
        echo '<table border="1"><tr><th>Server Parses</th><th>Player Types</th></tr><tr class="top"><td>'."\n";
        while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
        {
            if ($row['synonym_of'] != "")
            {
                if ($row['synonym_of'] == $syn)
                {
                    echo '<b><a href="./index.php?do=synonyms&amp;syn='.rawurlencode($row['synonym_of']).'">* '.$row['synonym_of'].' *</a></b><br/>'."\n";
                }
                else
                {
                    echo '<a href="./index.php?do=synonyms&amp;syn='.rawurlencode($row['synonym_of']).'">'.$row['synonym_of'].'</a><br/>'."\n";
                }
            }
        }
        if (checkaccess('npcs', 'edit'))
        {
            echo '<hr /><form action="./index.php?do=synonyms" method="post">'."\n";
            echo '<table><tr><td>Parsed Phrase:</td><td><input type="text" name="new_phrase" /></td></tr>'."\n";
            echo '<tr><td>Typed Phrase:</td><td><input type="text" name="new_syn" /></td></tr>'."\n";
            echo '<tr><td colspan="2"><input type="submit" name="commit" value="New Base Phrase" /></td></tr></table></form>'."\n";
        }
        echo '</td><td>';
        if ($syn == "")
        {
            echo 'No Phrase Selected';
        }
        else
        {
            $query = "SELECT word FROM npc_synonyms WHERE synonym_of='$syn' ORDER BY word";
            $result = mysql_query2($query);
            while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
            {
                if (checkaccess('npcs', 'edit'))
                {
                    echo '<form action="./index.php?do=synonyms&amp;syn='.$syn.'" method="post"><p><input type="hidden" name="word" value="'.$row['word'].'" /><input type="submit" name="commit" value="Delete" /> - '.$row['word'].'</p></form>'."\n";
                }
                else
                {
                    echo '<p>'.$row['word'].'</p>'."\n";
                }
            }
            if (checkaccess('npcs', 'edit'))
            {
                echo '<form action="./index.php?do=synonyms&amp;syn='.$syn.'" method="post"><p><input type="text" name="new_syn" /><input type="submit" name="commit" value="Add Synonym" /></p></form>'."\n";
            }
        }
        echo '</td></tr>';
        echo '</table>';
    }
}
?>
