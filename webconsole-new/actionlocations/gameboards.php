<?php

function gameboards(){
  if (checkaccess('als', 'read')){
      echo "<table border=1>";
      $query2 = "SELECT * FROM gameboards";
      $result2 = mysql_query2($query2);
      while ($row2 = mysql_fetch_array($result2, MYSQL_ASSOC)){
        echo '<tr><td>'.$row2['name'].'</td><td>';

        // edit
        if (checkaccess('als', 'edit')) {
          echo '<form action="./index.php?do=editgameboard" method="post"><input type="hidden" name="name" value="'.$row2['name'].'" />';
          $layout = $row2['layout'];
          $layoutnew = "";
          for ($i=0;$i<$row2['numColumns'];$i++) {
            $layoutnew .= substr($layout,$i*$row2['numColumns'],$row2['numColumns'])."<br/>";
          }
          echo '<br/>Columns: <input type=text name=columns size=4 value='.$row2['numColumns'].' /> Rows: <input type=text name=rows size=4 value='.$row2['numRows'].' /><br/>';
          echo 'Layout string: <input type=text name=layout size=50 value='.$row2['layout'].' /><br/><br/>Layout Visual:<br/>'.$layoutnew.'<br/> Pieces: <input type=text name=pieces size=20 value='.$row2['pieces'].' /><br/>';
          echo 'numPlayers: <input type=text name=numplayers size=4 value='.$row2['numPlayers'].' /> <br/>gameboardOptions: <input type=text name=options size=20 value='.$row2['gameboardOptions'].' /><br/>';
          echo 'gameRules: <textarea cols="50" rows="4" name="gameRules">'.$row2['gameRules'].'</textarea> <br/>endgames: <textarea cols="50" rows="4" name="endgames">'.$row2['endgames'].'</textarea><br/><br/>';
          echo '<input type="submit" name="submit" value="Update" />';
          if (checkaccess('als', 'delete')){
            echo '</form> -- <form action="./index.php?do=deletegameboard" method="post"><input type="hidden" name="id" value="'.$row['id'].'" /><input type="submit" name="delete" value="Delete" />';
          }
          echo "</form>";
        // read only
        }else{
          $layout = $row2['layout'];
          $layoutnew = "";
          for ($i=0;$i<$row2['numColumns'];$i++) {
            $layoutnew .= substr($layout,$i*$row2['numColumns'],$row2['numColumns'])."<br/>";
          }
          echo '<br/>Columns: '.$row2['numColumns'].' Rows: '.$row2['numRows'].'<br/>';
          echo 'Layout string: '.$row2['layout'].'<br/><br/>Layout Visual:<br/>'.$layoutnew.'<br/> Pieces: '.$row2['pieces'].'<br/>';
          echo 'numPlayers: '.$row2['numPlayers'].' gameboardOptions: '.$row2['gameboardOptions'].'<br/>';
          echo 'gameRules: '.htmlentities($row2['gameRules']).' <br/>endgames: '.htmlentities($row2['endgames']).'<br/><br/>';
        }
        echo '</td></tr>';
      }
      echo '</table>';
  }else{
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}

function editgameboard(){
echo $_POST['name'];
  if (checkaccess('als', 'edit')){
    if (isset($_POST['name'])){
      $name = mysql_real_escape_string($_POST['name']);
      $columns = mysql_real_escape_string($_POST['columns']);
      $rows = mysql_real_escape_string($_POST['rows']);
      $layout = mysql_real_escape_string($_POST['layout']);
      $pieces = mysql_real_escape_string($_POST['pieces']);
      $numplayers = mysql_real_escape_string($_POST['numplayers']);
      $options = mysql_real_escape_string($_POST['options']);
      $gameRules = mysql_real_escape_string($_POST['gameRules']);
      $endgames = mysql_real_escape_string($_POST['endgames']);
      $query = "UPDATE gameboards SET numColumns='$columns', numRows='$rows', layout='$layout', pieces='$pieces', numPlayers='$numplayers', gameboardOptions='$options', gameRules='$gameRules', endgames='$endgames' WHERE name='$name'";
      $result = mysql_query2($query);
      //echo $query;
      echo '<p class="error">Update Successful</p>';
      gameboards();
    }else{
      echo '<p class="error">Error: No gameboard name specified</p>';
      gameboards();
    }
  }else{
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}

function deletegameboard(){
  if (checkaccess('als', 'delete')){
    if (isset($_POST['commit']) && isset($_POST['pass']) && isset($_POST['id'])){
      $id = mysql_real_escape_string($_POST['id']);
      $password = mysql_real_escape_string($_POST['pass']);
      $username = mysql_real_escape_string($_SESSION['username']);
      $query = "SELECT COUNT(username) FROM accounts WHERE username='$username' AND password=MD5('$password')";
      $result = mysql_query2($query);
      $row = mysql_fetch_row($result);
      if ($row[0] == 1){
        $query = "DELETE FROM gameboards WHERE name='$id'";
        $result = mysql_query2($query);
        echo '<p class="error">Delete Successful</p>';
        gameboards();
      }else{
        echo '<p class="error">Error: Password check failed, Delete aborted</p>';
        gameboards();
      }
    }else{
      if (isset($_POST['id'])){
        $id = mysql_real_escape_string($_POST['id']);
        $query = "SELECT name, sectorname FROM gameboards WHERE id='$id'";
        $result = mysql_query2($query);
        $row = mysql_fetch_array($result, MYSQL_ASSOC);
        echo '<p>You are about to permanently delete Game Board '.$id.'<br/>Name: '.$row['name'].'<br/>sector: '.$row['sectorname'].'</p>';
        echo '<form action="./index.php?do=deleteal" method="post"><p>Enter your password to confirm: <input type="hidden" name="id" value="'.$id.'" /><input type="password" name="pass" /><input type="submit" name="commit" value="Confirm Delete" /></p></form>';
      }else{
        echo '<p class="error">Error: No Gameboard name specified</p>';
        gameboards();
      }
    }
  }else{
    echo '<p class="error">You are not authorized to yse these functions</p>';
  }
}

?>

