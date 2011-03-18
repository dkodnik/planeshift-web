<?php
function ka_scripts(){
  if (checkaccess('npcs', 'read')){
    if (!isset($_GET['sub'])){
      $query = "SELECT id, script FROM quest_scripts WHERE quest_id='-1'";
      $result = mysql_query2($query);
      echo '<table border="1">';
      echo '<tr><th>Name</th><th>Action</th></tr>';
      while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
        $pos1 = strpos($row['script'], "\n")+1;
        $string = substr($row['script'], $pos1);
        $pos2 = strpos($string, ":");
        $name = substr($string, 0, $pos2);   
        echo '<tr><td>'.$name.'</td>';
        echo '<td><a href="./index.php?do=ka_scripts&amp;sub=Read&amp;areaid='.$row['id'].'">Read</a>';
        if (checkaccess('npcs', 'edit')){
          echo '<br/><a href="./index.php?do=ka_scripts&amp;sub=Edit&amp;areaid='.$row['id'].'">Edit</a>';
          echo '<br/><a href="./index.php?do=ka_scripts&amp;sub=Delete&amp;areaid='.$row['id'].'">Delete</a>';
        }
        echo '</td></tr>';
      }
      if (checkaccess('npcs', 'edit')){
        echo '<tr><td><form action="./index.php?do=ka_scripts&amp;sub=New" method="post">';
        echo '<input type="text" name="name" /><br/><input type="submit" name="commit" value="Create New KA Script" />';
        echo '</form></td><td>&nbsp;</td></tr>';
      }
      echo '</table>';
    }else{
      if (!checkaccess('npcs', 'edit')) {
        echo '<p class="error">Error: You are not authorized to use this function.</p>';
        return;
      }
      if (isset($_GET['areaid'])){
        $areaid = mysql_real_escape_string($_GET['areaid']);
      }
      if ($_GET['sub'] == 'Read'){
        $query = "SELECT script FROM quest_scripts WHERE id='$areaid'";
        $result = mysql_query2($query);
        $row = mysql_fetch_array($result, MYSQL_ASSOC);
        $pos1 = strpos($row['script'], "\n")+1;
        $string = substr($row['script'], $pos1);
        $pos2 = strpos($string, ":");
        $name = substr($string, 0, $pos2);
        echo 'Reading KA Script: '.$name.'<hr/>';
        $script = str_replace("\n", "<br/>\n", htmlspecialchars($row['script']));
        echo $script.'<br/>';
      }else if ($_GET['sub'] == 'Edit'){
        if (isset($_POST['commit'])){
          $script = mysql_real_escape_string($_POST['script']);
          $query = "UPDATE quest_scripts SET script='$script' WHERE id='$areaid'";
          $result = mysql_query2($query);
          unset($_POST);
          $_GET['sub']='Read';
          ka_scripts();
          return;
        }else{
          $query = "SELECT script FROM quest_scripts WHERE id='$areaid'";
          $result = mysql_query2($query);
          $row = mysql_fetch_array($result, MYSQL_ASSOC);
          $pos1 = strpos($row['script'], "\n")+1;
          $string = substr($row['script'], $pos1);
          $pos2 = strpos($string, ":");
          $name = substr($string, 0, $pos2);
          echo 'Editing KA Script: '.$name.'<hr/>';
          echo '<form action="./index.php?do=ka_scripts&amp;sub=Edit&amp;areaid='.$areaid.'" method="post">';
          echo '<textarea name="script" rows="20" cols="70">'.$row['script'].'</textarea><br/>';
          echo '<input type="submit" name="commit" value="Update Script"/>';
          echo '</form>';
        }
      }else if ($_GET['sub'] == 'Delete'){
        if (isset($_POST['commit'])){
          $query = "DELETE FROM quest_scripts WHERE id='$areaid'";
          $result = mysql_query2($query);
          unset($_POST);
          unset($_GET);
          ka_scripts();
          return;
        }else{
          $query = "SELECT script FROM quest_scripts WHERE id='$areaid'";
          $result = mysql_query2($query);
          $row = mysql_fetch_array($result, MYSQL_ASSOC);
          $pos1 = strpos($row['script'], "\n")+1;
          $string = substr($row['script'], $pos1);
          $pos2 = strpos($string, ":");
          $name = substr($string, 0, $pos2);
          echo 'You are about to delete the following KA Script: '.$name.'<br/>';
          echo '<form action="./index.php?do=ka_scripts&amp;sub=Delete&amp;areaid='.$areaid.'" method="post">';
          echo '<input type="submit" name="commit" value="Confirm Delete"/></form>';
        }
      }else if ($_GET['sub'] == 'New'){
        $name = mysql_real_escape_string($_POST['name']);
        $script = " \n"."$name:\n#This is a temporary Entry -Needs to be changed";
        $query = "INSERT INTO quest_scripts (quest_id, script) VALUES ('-1', '$script')";
        $result = mysql_query2($query);
        unset($_GET);
        unset($_POST);
        ka_scripts();
        return;
      }else{
        echo '<p class="error">Error: No action specified</p>';
      }
    }
  }else{
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}
?>
