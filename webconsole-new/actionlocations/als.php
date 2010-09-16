<?php
function alsector(){
  if (checkaccess('als', 'read')){
    $query = "SELECT name FROM sectors ORDER BY name";
    $result = mysql_query2($query);
    echo '<p> Select Sector:<br/>';
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){ 
      echo '<a href="./index.php?do=listals&amp;sector='.$row['name'].'">'.$row['name'].'</a><br/>'."\n";
    }
    echo '</p>';
  }else{
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}

function listals(){
  if (checkaccess('als', 'read')){
    if (isset($_GET['sector'])){
      $sector = mysql_real_escape_string($_GET['sector']);
      $query = "SELECT * FROM action_locations WHERE sectorname='$sector'";
      $result = mysql_query2($query);
      echo '<table border="1">';
      while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
        if (checkaccess('als', 'edit')){
          echo '<tr><td>';
          echo 'id: '.$row['id'].'</td><td><form action="./index.php?do=edital" method="post"><input type="hidden" name="id" value="'.$row['id'].'" /><input type="hidden" name="sector" value="'.$_GET['sector'].'" />';
          echo 'Master Id: <input type="text" name="master_id" value="'.$row['master_id'].'" size="2"/> -- Name: <input type="text" name="name" value="'.$row['name'].'" size="40" /><br/>';
          echo 'Mesh Name: <input type="text" name="meshname" value="'.$row['meshname'].'" size="40" /> -- Polygon: <input type="text" name="polygon" value="'.$row['polygon'].'" size="2" /> -- Radius: <input type="text" name="radius" value="'.$row['radius'].'" size="4" /><br/>';
          echo 'Position: '.$row['pos_x'].'/'.$row['pos_y'].'/'.$row['pos_z'].'<br/>';
          echo 'Trigger Type: <select name="triggertype">';
          if ($row['triggertype'] == "SELECT"){
            echo '<option value="SELECT" selected="true">SELECT</option><option value="PROXIMITY">PROXIMITY</option>';
          }else{
            echo '<option value="SELECT">SELECT</option><option value="PROXIMITY" selected="true">PROXIMITY</option>';}
          echo '</select> -- Response Type: <select name="responsetype">';
          if ($row['responsetype'] == "EXAMINE"){
            echo '<option value="EXAMINE" selected="true">EXAMINE</option><option value="SCRIPT">SCRIPT</option>';
          }else{
            echo '<option value="EXAMINE">EXAMINE</option><option value="SCRIPT" selected="true">SCRIPT</option>';
          }
          echo '</select><br/>';
          echo 'Script: <textarea name="response" rows="4" cols="50">'.$row['response'].'</textarea><br/>';
          echo 'Active: '.$row['active_ind'] .' -- (Can Only be changed In-Game)<br/>';
          echo 'Instance: <input type="text" name="pos_instance" value="'.$row['pos_instance'] .'" /> -- (Indicates from which istance this AL will be accessible. Default: 4294967295 means "all instances".)<br/>';
          echo '<input type="submit" name="submit" value="Update" />';
          if (checkaccess('als', 'delete')){
            echo '</form> -- <form action="./index.php?do=deleteal" method="post"><input type="hidden" name="id" value="'.$row['id'].'" /><input type="submit" name="delete" value="Delete AL" />';
          }
          echo "</form></td></tr>\n";
        }else{
          echo '<tr><td>id: '.$row['id'].'</td><td>';
          echo 'Master Id: '.$row['master_id'].' -- Name: '.$row['name'].'<br/>';
          echo 'Mesh Name: '.$row['meshname'].' -- Polygon: '.$row['polygon'].' -- Radius: '.$row['radius'].'<br/>';
          echo 'Position: '.$row['pos_x'].'/'.$row['pos_y'].'/'.$row['pos_z'].'<br/>';
          echo 'Trigger Type: '.$row['triggertype'].' -- Response Type: '.$row['responsetype'].'<br/>';
          echo 'Script: '.htmlspecialchars($row['response']).'<br/>';
          echo 'Active: '.$row['active_ind'] .' -- (Can Only be changed In-Game)';
          echo "</td></tr>\n";
        }
      }
      echo '</table>';
    }else{
      echo '<p class="error">Error: No sector Selected</p>';
      alsector();
    }
  }else{
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}

function edital(){
  if (checkaccess('als', 'edit')){
    if (isset($_POST['id'])){
      $id = mysql_real_escape_string($_POST['id']);
      $master_id = mysql_real_escape_string($_POST['master_id']);
      $name = mysql_real_escape_string($_POST['name']);
      $meshname = mysql_real_escape_string($_POST['meshname']);
      $polygon = mysql_real_escape_string($_POST['polygon']);
      $radius = mysql_real_escape_string($_POST['radius']);
      $triggertype = mysql_real_escape_string($_POST['radius']);
      $responsetype = mysql_real_escape_string($_POST['responsetype']);
      $response = mysql_real_escape_string($_POST['response']);
      $pos_instance = mysql_real_escape_string($_POST['pos_instance']);
      $query = "UPDATE action_locations SET master_id='$master_id', name='$name', meshname='$meshname', polygon='$polygon', radius='$radius', triggertype='$triggertype', responsetype='$responsetype', response='$response', pos_instance='$pos_instance' WHERE id='$id'";
      $result = mysql_query2($query);
      $_GET['sector'] = $_POST['sector'];
      echo '<p class="error">Update Successful</p>';
      listals();
    }else{
      echo '<p class="error">Error: No AL ID specified</p>';
      alsector();
    }
  }else{
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}

function deleteal(){
  if (checkaccess('als', 'delete')){
    if (isset($_POST['commit']) && isset($_POST['pass']) && isset($_POST['id'])){
      $id = mysql_real_escape_string($_POST['id']);
      $password = mysql_real_escape_string($_POST['pass']);
      $username = mysql_real_escape_string($_SESSION['username']);
      $query = "SELECT COUNT(username) FROM accounts WHERE username='$username' AND password=MD5('$password')";
      $result = mysql_query2($query);
      $row = mysql_fetch_row($result);
      if ($row[0] == 1){
        $query = "DELETE FROM action_locations WHERE id='$id'";
        $result = mysql_query2($query);
        echo '<p class="error">Delete Successful</p>';
        alsector();
      }else{
        echo '<p class="error">Error: Password check failed, Delete aborted</p>';
        alsector();
      }
    }else{
      if (isset($_POST['id'])){
        $id = mysql_real_escape_string($_POST['id']);
        $query = "SELECT name, sectorname FROM action_locations WHERE id='$id'";
        $result = mysql_query2($query);
        $row = mysql_fetch_array($result, MYSQL_ASSOC);
        echo '<p>You are about to permanently delete Action Location '.$id.'<br/>Name: '.$row['name'].'<br/>sector: '.$row['sectorname'].'</p>';
        echo '<form action="./index.php?do=deleteal" method="post"><p>Enter your password to confirm: <input type="hidden" name="id" value="'.$id.'" /><input type="password" name="pass" /><input type="submit" name="commit" value="Confirm Delete" /></p></form>';
      }else{
        echo '<p class="error">Error: No AL ID specified</p>';
        alsector();
      }
    }
  }else{
    echo '<p class="error">You are not authorized to yse these functions</p>';
  }
}

?>

