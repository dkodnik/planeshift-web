<?php

function gameboards()
{
    if (!checkaccess('als', 'read'))
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
    }
    echo '<table border="1">';
    $query2 = "SELECT * FROM gameboards";
    $result2 = mysql_query2($query2);
    while ($row2 = fetchSqlAssoc($result2))
    {
        echo '<tr><td>'.$row2['name'].'</td><td>';

        // edit
        if (checkaccess('als', 'edit')) 
        {
            echo '<form action="./index.php?do=editgameboard" method="post"><div><input type="hidden" name="name" value="'.$row2['name'].'" />';
            $layout = $row2['layout'];
            $layoutnew = "";
            for ($i = 0; $i < $row2['numColumns']; $i++) 
            {
                $layoutnew .= substr($layout, $i * $row2['numColumns'], $row2['numColumns'])."<br/>";
            }
            echo '<br/>Columns: <input type="text" name="columns" size="4" value="'.$row2['numColumns'].'" /> Rows: <input type="text" name="rows" size="4" value="'.$row2['numRows'].'" /><br/>';
            echo 'Layout string: <input type="text" name="layout" size="50" value="'.$row2['layout'].'" /><br/><br/>Layout Visual:<br/>'.$layoutnew.'<br/> Pieces: <input type="text" name="pieces" size="20" value="'.$row2['pieces'].'" /><br/>';
            echo 'numPlayers: <input type="text" name="numplayers" size="4" value="'.$row2['numPlayers'].'" /> <br/>gameboardOptions: <input type="text" name="options" size="20" value="'.$row2['gameboardOptions'].'" /><br/>';
            echo 'gameRules: <textarea cols="50" rows="4" name="gameRules">'.htmlentities($row2['gameRules']).'</textarea> <br/>endgames: <textarea cols="50" rows="4" name="endgames">'.htmlentities($row2['endgames']).'</textarea><br/><br/>';
            echo '<input type="submit" name="submit" value="Update" />';
            echo '</div></form>';
            if (checkaccess('als', 'delete'))
            {
                echo ' -- <form action="./index.php?do=deletegameboard" method="post"><div><input type="hidden" name="name" value="'.$row2['name'].'" /><input type="submit" name="delete" value="Delete" /></div></form>';
            }
            echo "\n";
        }
        else
        {    // read only
            $layout = $row2['layout'];
            $layoutnew = "";
            for ($i = 0; $i < $row2['numColumns']; $i++) {
                $layoutnew .= substr($layout, $i * $row2['numColumns'], $row2['numColumns'])."<br/>";
            }
            echo '<br/>Columns: '.$row2['numColumns'].' Rows: '.$row2['numRows'].'<br/>';
            echo 'Layout string: '.$row2['layout'].'<br/><br/>Layout Visual:<br/>'.$layoutnew.'<br/> Pieces: '.$row2['pieces'].'<br/>';
            echo 'numPlayers: '.$row2['numPlayers'].' gameboardOptions: '.$row2['gameboardOptions'].'<br/>';
            echo 'gameRules: '.htmlentities($row2['gameRules']).' <br/>endgames: '.htmlentities($row2['endgames']).'<br/><br/>';
        }
        echo '</td></tr>';
    }
    echo '</table>';
}

function editgameboard(){
echo $_POST['name'];
  if (checkaccess('als', 'edit')){
    if (isset($_POST['name'])){
      $name = escapeSqlString($_POST['name']);
      $columns = escapeSqlString($_POST['columns']);
      $rows = escapeSqlString($_POST['rows']);
      $layout = escapeSqlString($_POST['layout']);
      $pieces = escapeSqlString($_POST['pieces']);
      $numplayers = escapeSqlString($_POST['numplayers']);
      $options = escapeSqlString($_POST['options']);
      $gameRules = escapeSqlString($_POST['gameRules']);
      $endgames = escapeSqlString($_POST['endgames']);
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
    if (isset($_POST['commit']) && isset($_POST['pass']) && isset($_POST['name'])){
      $name = escapeSqlString($_POST['name']);
      $password = escapeSqlString($_POST['pass']);
      $username = escapeSqlString($_SESSION['username']);
      $query = "SELECT COUNT(username) FROM accounts WHERE username='$username' AND password=MD5('$password')";
      $result = mysql_query2($query);
      $row = fetchSqlRow($result);
      if ($row[0] == 1){
        $query = "DELETE FROM gameboards WHERE name='$name'";
        $result = mysql_query2($query);
        echo '<p class="error">Delete Successful</p>';
        gameboards();
      }else{
        echo '<p class="error">Error: Password check failed, Delete aborted</p>';
        gameboards();
      }
    }else{
      if (isset($_POST['name'])){
        $name = escapeSqlString($_POST['name']);
        $query = "SELECT name, sectorname FROM gameboards WHERE name='$name'";
        $result = mysql_query2($query);
        $row = fetchSqlAssoc($result);
        echo '<p>You are about to permanently delete Game Board '.$name.'<br/>Name: '.$row['name'].'<br/>sector: '.$row['sectorname'].'</p>';
        echo '<form action="./index.php?do=deleteal" method="post"><p>Enter your password to confirm: <input type="hidden" name="name" value="'.$name.'" /><input type="password" name="pass" /><input type="submit" name="commit" value="Confirm Delete" /></p></form>';
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

