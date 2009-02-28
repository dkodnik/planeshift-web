<?
function synonyms(){
  if (checkaccess('npcs', 'read')){
    if (isset($_POST['commit'])){
      if (checkaccess('npcs', 'edit')){
        if ($_POST['commit'] == 'Delete'){
          $word = mysql_real_escape_string($_POST['word']);
          $syn = mysql_real_escape_string($_GET['syn']);
          $query = "DELETE FROM npc_synonyms WHERE word='$word' AND synonym_of='$syn'";
        }else if ($_POST['commit'] == 'Add Synonym'){
          $word = mysql_real_escape_string($_POST['new_syn']);
          $syn = mysql_real_escape_string($_GET['syn']);
          $query = "INSERT INTO npc_synonyms (synonym_of, word) VALUES ('$syn', '$word')";
        }else if ($_POST['commit'] == 'New Base Phrase'){
          $word = mysql_real_escape_string(strtolower($_POST['new_syn']));
          $syn = mysql_real_escape_string(strtolower($_POST['new_phrase']));
          $query = "INSERT INTO npc_synonyms (synonym_of, word) VALUES ('$syn', '$word')";
          $_GET['syn'] = $syn;
        }else{
          unset($_POST);
          echo '<p class="error">No Commit Specified</p>';
          synonyms();
          return;
        }
        $result = mysql_query2($query);
        unset($_POST);
        synonyms();
      }else{
        echo '<p class="error">You are not authorized to use these functions</p>';
      }
    }else{
      $query = 'SELECT DISTINCT synonym_of FROM npc_synonyms ORDER BY synonym_of';
      $result = mysql_query2($query);
      if (isset($_GET['syn'])){
        $syn = mysql_real_escape_string($_GET['syn']);
      }else{
        $syn = "";
      }
      echo '<table border="1"><tr><th>Server Parses</th><th>Player Types</th></tr><tr class="top"><td>';
      while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
        if ($row['synonym_of'] != ""){
          if ($row['synonym_of'] == $syn){
            echo '<b><a href="./index.php?do=synonyms&amp;syn='.rawurlencode($row['synonym_of']).'">'.$row['synonym_of'].'</a></b><br/>';
          }else{
            echo '<a href="./index.php?do=synonyms&amp;syn='.rawurlencode($row['synonym_of']).'">'.$row['synonym_of'].'</a><br/>';
          }
        }
      }
      if (checkaccess('npcs', 'edit')){
        echo '<hr/><form action="./index.php?do=synonyms" method="post">Parsed Phrase:<input type="text" name="new_phrase"/><br/>';
        echo 'Typed Phrase:<input type="text" name="new_syn"/><br/>';
        echo '<input type="submit" name="commit" value="New Base Phrase" /></form>';
      }
      echo '</td><td>';
      if ($syn == ""){
        echo 'No Phrase Selected';
      }else{
        $query = "SELECT word FROM npc_synonyms WHERE synonym_of='$syn' ORDER BY word";
        $result = mysql_query2($query);
        while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
          if (checkaccess('npcs', 'edit')){
            echo '<form action="./index.php?do=synonyms&amp;syn='.$syn.'" method="post"><input type="hidden" name="word" value="'.$row['word'].'" /><input type="submit" name="commit" value="Delete" /> - '.$row['word'].'</form>';
          }else{
            echo $row['word'].'<br/>';
          }
        }
        if (checkaccess('npcs', 'edit')){
          echo '<form action="./index.php?do=synonyms&amp;syn='.$syn.'" method="post"><input type="text" name="new_syn"/><input type="submit" name="commit" value="Add Synonym" /></form>';
        }
      }
      echo '</td></tr>';
      echo '</table>';
    }
  }else{
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}
?>
