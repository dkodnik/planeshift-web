<?php
function listskills(){
  if (checkaccess('rules', 'read')){
    if (isset($_POST['commit']) && checkaccess('rules', 'edit')){
      if ($_POST['commit'] == "Commit Edit"){
        $id = mysql_real_escape_string($_POST['id']);
        $description = mysql_real_escape_string($_POST['description']);
        $practice_factor = mysql_real_escape_string($_POST['practice_factor']);
        $mental_factor = mysql_real_escape_string($_POST['mental_factor']);
        $price = mysql_real_escape_string($_POST['price']);
        $base_rank_cost = mysql_real_escape_string($_POST['base_rank_cost']);
        $query = "UPDATE skills SET description='$description', practice_factor='$practice_factor', mental_factor='$mental_factor', price='$price', base_rank_cost='$base_rank_cost' WHERE skill_id='$id'";
        $result = mysql_query2($query);
        unset($_POST);
        echo '<p class="error">Update Successful</p>';
        listskills();
        return;
      }else{
        echo '<p class="error">Bad Commit, returning to listing</p>';
        unset($_POST);
        listskills();
        return;
      }
    }else if (isset($_POST['action']) && checkaccess('rules', 'edit')){
      if ($_POST['action'] == "Edit"){
        $id = mysql_real_escape_string($_POST['id']);
        $query = "SELECT * FROM skills WHERE skill_id='$id'";
        $result = mysql_query2($query);
        $row = mysql_fetch_array($result, MYSQL_ASSOC);
        echo '<form action="./index.php?do=skills" method="post">';
        echo '<input type="hidden" name="id" value="'.$id.'" />';
        echo '<table border="1"><tr><th>Field</th><th>Value</th></tr>';
        echo '<tr><td>Name:</td><td>'.$row['name'].'</td></tr>';
        echo '<tr><td>Description:</td><td><textarea name="description" row="4" cols="40">'.$row['description'].'</textarea></td></tr>';
        echo '<tr><td>Practice Factor:</td><td><input type="text" name="practice_factor" value="'.$row['practice_factor'].'" /></td></tr>';
        echo '<tr><td>Mental Factor:</td><td><input type="text" name="mental_factor" value="'.$row['mental_factor'].'" /></td></tr>';
        echo '<tr><td>Price:</td><td><input type="text" name="price" value="'.$row['price'].'" /></td></tr>';
        echo '<tr><td>Base Rank Cost:</td><td><input type="text" name="base_rank_cost" value="'.$row['base_rank_cost'].'" /></td></tr>';
        echo '<tr><td>Category</td><td>'.$row['category'].'</td></tr>';
        echo '</table>';
        echo '<input type="submit" name="commit" value="Commit Edit" />';
        echo '</form>';
      }else{
        echo '<p class="error">Bad Action, returning to listing</p>';
        unset($_POST);
        listskills();
        return;
      }
    }else{
      $query = "SELECT * FROM skills";
      $result = mysql_query2($query);
      if (mysql_numrows($result) == 0 ){
        echo 'No Skills Found!';
      }
      echo '<table border="1"><tr><th>ID</th><th>Skill</th><th>Description</th><th>Practice Factor</th><th>Mental Factor</th><th>Price</th><th>Base Rank Cost</th><th>Category</th>';
      if (checkaccess('rules', 'edit')){
        echo '<th>Actions</th>';
      }
      echo '</tr>';
      while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
        echo '<tr>';
        echo '<td>'.$row['skill_id'].'</td>';
        echo '<td>'.$row['name'].'</td>';
        echo '<td>'.$row['description'].'</td>';
        echo '<td>'.$row['practice_factor'].'</td>';
        echo '<td>'.$row['mental_factor'].'</td>';
        echo '<td>'.$row['price'].'</td>';
        echo '<td>'.$row['base_rank_cost'].'</td>';
        echo '<td>'.$row['category'].'</td>';
        if (checkaccess('rules', 'edit')){
          echo '<td>';
          echo '<form action="./index.php?do=skills" method="post">';
          echo '<input type="hidden" name="id" value="'.$row['skill_id'].'"/>';
          echo '<input type="submit" name="action" value="Edit" />';
          echo '</form></td>';
        }
        echo '</tr>';
      }
      echo '</table>';
    }
  }else{
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}
?>
