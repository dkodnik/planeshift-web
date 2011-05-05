<?php
function listlocationtypes(){
  if (checkaccess('rules', 'read')){
    if (isset($_POST['commit']) && checkaccess('rules', 'edit')){
      if ($_POST['commit'] == "Update Location Type"){
        $id = mysql_real_escape_string($_POST['id']);
        $name = mysql_real_escape_string($_POST['name']);
        $query = "UPDATE sc_location_type SET name='$name' WHERE id = '$id'";
        $result = mysql_query2($query);
        echo '<p class="error">Update Successful</p>';
        unset($_POST);
        listlocationtypes();
        return;
      }else if ($_POST['commit'] == "Confirm Delete" && checkaccess('rules', 'delete')){
        $id = mysql_real_escape_string($_POST['id']);
        $query = "DELETE FROM sc_location_type WHERE id = '$id'";
        $result = mysql_query2($query);
        echo '<p class="error">Update Successful</p>';
        unset($_POST);
        listlocationtypes();
        return;
      }else if ($_POST['commit'] == "Create Location Type" && checkaccess('rules', 'create')){
        $name = mysql_real_escape_string($_POST['name']);
        $query = "INSERT INTO sc_location_type SET name='$name'";
        $result = mysql_query2($query);
        echo '<p class="error">Update Successful</p>';
        unset($_POST);
        listlocationtypes();
        return;
      }else{
        echo '<p class="error">Bad Commit, return to listing</p>';
        unset($_POST);
        listlocationtypes();
        return;
      }
    }else if (isset($_POST['action']) && checkaccess('rules', 'edit')){
      if ($_POST['action'] == "Edit"){
        $id = mysql_real_escape_string($_POST['id']);
        $query = "SELECT * FROM sc_location_type WHERE id = '$id'";
        $result = mysql_query2($query);
        $row = mysql_fetch_array($result, MYSQL_ASSOC);
        echo '<form action="./index.php?do=locationtype" method="post"><input type="hidden" name="id" value="'.$id.'" /><table border="1">';
        echo '<tr><th>Field</th><th>Value</th></tr>';
        echo '<tr><td>Name:</td><td><input type="text" name="name" value="'.$row['name'].'"/></td></tr>';
        echo '</table><input type="submit" name="commit" value="Update Location Type"/>';
        echo '</form>';
      }else if ($_POST['action'] == "Delete" && checkaccess('rules', 'delete')){
        $id = mysql_real_escape_string($_POST['id']);
        $query = "SELECT name FROM sc_location_type WHERE id = '$id'";
        $result = mysql_query2($query);
        $row = mysql_fetch_array($result, MYSQL_ASSOC);
        echo '<p>You are about to delete the location type '.$row['name'].' id '.$id.' Please confirm:</p>';
        echo '<form action="./index.php?do=locationtype" method="post"><input type="hidden" name="id" value="'.$id.'" />';
        echo '<input type="submit" name="commit" value="Confirm Delete" /></form>';
      }else{
        echo '<p class="error">Bad Action - Returning to list</p>';
        unset($_POST);
        listlocationtypes();
        return;
      }
    }else{
      $query = "SELECT l.id, l.name FROM sc_location_type AS l";
      if (isset($_GET['id']))
      {
        $id = mysql_real_escape_string($_GET['id']);
        $query .= " WHERE l.id='$id'";
      }
      if (isset($_GET['sort'])){
        switch ($_GET['sort']){
          case 'id':
            $query = $query . " ORDER BY id";
            break;
          case 'name':
            $query = $query . " ORDER BY name";
            break;
          default:
            echo '<p class="error">Bad sort method - No sort used</p>';
        }
      }
      if (isset($_GET['limit'])){
        $lim = mysql_real_escape_string($_GET['limit']);
        $ll = $lim - 30;
        $query = $query . " LIMIT $ll, 30"; // limit 1, 10 uses offset 1, then 10 records.
      }else{
        $query = $query . " LIMIT 0, 30";
        $ll = 0;
        $lim=30;
      }
      $result = mysql_query2($query);
      if (mysql_numrows($result) == 0){
        echo '<p class="error">No Location types in DataBase</p>';
      }else{
        if ($lim> 30){
          echo '<a href="./index.php?do=locationtype';
          if (isset($_GET['sort'])){
            echo '&amp;sort='.$_GET['sort'];
          }
          echo '&amp;limit='.$ll.'">Previous Page</a>';
        }
        echo ' - Displaying records '.$ll.' through '.$lim.' - ';
        $result2 = mysql_query2('select count(id) AS mylimit FROM sc_location_type');
        $row2 = mysql_fetch_array($result2);
        if ($row2['mylimit'] > $lim)
        {
          echo '<a href="./index.php?do=locationtype';
          if (isset($_GET['sort'])){
            echo '&amp;sort='.$_GET['sort'];
          }
          $lu = $lim + 30;
          echo '&amp;limit='.$lu.'">Next Page</a>';;
        }
        echo '<table border="1">';
        echo '<tr>';
        echo '<th><a href="./index.php?do=locationtype&amp;sort=id';
        if (isset($_GET['limit'])){
          echo '&amp;limit='.$_GET['limit'];
        }
        echo '">id</a></th>';
        echo '<th><a href="./index.php?do=locationtype&amp;sort=name';
        if (isset($_GET['limit'])){
          echo '&amp;limit='.$_GET['limit'];
        }
        echo '">name</a></th>';
        if (checkaccess('rules', 'edit')){
          echo '<th>Actions</th>';
        }
        echo '</tr>';
        while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
          echo '<tr>';
          echo '<td>'.$row['id'].'</td>';
          echo '<td>'.$row['name'].'</td>';
          if (checkaccess('rules', 'edit')){
            echo '<td><form action="./index.php?do=locationtype" method="post"><input type="hidden" name="id" value="'.$row['id'].'"/>';
            echo '<input type="submit" name="action" value="Edit"/>';
            echo '<br/><input type="submit" name="action" value="Delete"/>';
            echo '</form></td>';
          }
          echo '</tr>';
        }
        echo '</table>';
        if (checkaccess('rules', 'create')){
          echo '<hr/>Create New Location Type:';
          echo '<form action="./index.php?do=locationtype" method="post"><table border="1">';
          echo '<tr><th>Field</th><th>Value</th></tr>';
          echo '<tr><td>Name:</td><td><input type="text" name="name" /></td></tr>';
          echo '</table><input type="submit" name="commit" value="Create Location Type"/>';
          echo '</form>';
        }
      }
    }
  }else{
    echo '<p class="error">You are not authorised to use these functions.</p>';
  }
}
?>
