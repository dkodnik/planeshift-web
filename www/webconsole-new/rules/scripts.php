<?php
function rule_scripts(){
  if (checkaccess('natres', 'read')){
    if (isset($_POST['commit'])){
      if (($_POST['commit']=='Change Name') && (checkaccess('natres', 'edit'))){
        $name = escapeSqlString($_POST['name']);
        $orig_name = escapeSqlString($_POST['orig_name']);
        $query = "UPDATE progression_events SET name='$name' WHERE name='$orig_name'";
        $result = mysql_query2($query);
        echo '<p class="error">Update Successful</p>';
        $_GET['type']=$_POST['type'];
        unset($_POST);
        rule_scripts();
        return;
      }else if (($_POST['commit']=='Update Script') && (checkaccess('natres', 'edit'))){
        $name = escapeSqlString($_POST['name']);
        $event_script = escapeSqlString($_POST['event_script']);
        $query = "UPDATE progression_events SET event_script='$event_script' WHERE name='$name'";
        $result = mysql_query2($query);
        echo '<p class="error">Update Successful</p>';
        $_GET['type']=$_POST['type'];
        unset($_POST);
        rule_scripts();
        return;
      }else if (($_POST['commit']=='Delete') && (checkaccess('natres', 'delete'))){
        $name = escapeSqlString($_POST['name']);
        $query = "DELETE FROM progression_events WHERE name='$name'";
        $result = mysql_query2($query);
        echo '<p class="error">Update Successful</p>';
        $_GET['type']=$_POST['type'];
        unset($_POST);
        rule_scripts();
        return;
      }else if (($_POST['commit']=='Create Script') && (checkaccess('natres', 'create'))){
        $name = escapeSqlString($_POST['name']);
        $event_script = escapeSqlString($_POST['event_script']);
        $query = "INSERT INTO progression_events (name, event_script) VALUES ('$name', '$event_script')";
        $result = mysql_query2($query);
        echo '<p class="error">Update Successful</p>';
        $_GET['type']=$_POST['type'];
        unset($_POST);
        rule_scripts();
        return;
      }
    }else{
      $query = "SELECT name, event_script FROM progression_events";
      if (isset($_GET['type'])){
        switch ($_GET['type']){
          /*case 'RL':
            echo '<p class="error">Listing only RandomItem scripts</p>';
            $query = $query. " WHERE name LIKE 'randomitem%'";
            break;*/
          case 'SI':
            echo '<p class="error">Listing only SimpleItem scripts</p>';
            $query = $query. " WHERE name LIKE 'simpleitem%' or name LIKE 'consumeitem%' ";
            break;
          case 'CG':
            echo '<p class="error">Listing only CharGen scripts</p>';
            $query = $query. " WHERE name LIKE 'charcreate%' OR name LIKE 'PATH%'";
            break;
          case 'S':
            echo '<p class="error">Listing only Spell scripts</p>';
            $query = $query. " WHERE name LIKE 'cast%' OR name LIKE 'apply%'";
            break;
          case 'A':
            echo '<p class="error">Listing only Special Attacks scripts</p>';
            $query = $query. " WHERE name LIKE 'attack%' ";
            break;
          case 'O':
            echo '<p class="error">Listing only "Other" scripts</p>';
            $query = $query. " WHERE name NOT LIKE 'simpleitem%' AND name NOT LIKE 'charcreate%' AND name NOT LIKE 'PATH%' AND name NOT LIKE 'cast%' AND name NOT LIKE 'apply%' AND name NOT LIKE 'consumeitem%' AND name NOT LIKE 'attack%'";
            break;
          default:
            echo '<p class="error">Unsupported type specified, not limiting listing</p>';
        }
      }else{
        echo '<p class="error">No Type Specified, not limiting listing</p>';
      }
      $query = $query. " ORDER BY name";
      $result = mysql_query2($query);
      if (sqlNumRows($result) > 0)
      {
        echo '<table border="1">';
        echo '<tr><th>Name</th><th>Script</th>';
        if (checkaccess('natres', 'delete'))
        {
            echo '<th>Actions</th></tr>';
        }
        else
        {
            echo '</tr>';
        }
        while ($row = fetchSqlAssoc($result))
        {
            echo '<tr>';
            if (checkaccess('natres', 'edit'))
            {
                echo '<td><form action="index.php?do=scripts" method="post"><div>';
                if (isset($_GET['type']))
                {
                    echo '<input type="hidden" name="type" value="'.$_GET['type'].'"/>';
                }
                echo '<input type="hidden" name="orig_name" value="'.$row['name'].'"/><input type="text" name="name" value="'.$row['name'].'"/><br/><input type="submit" name="commit" value="Change Name"/></div></form></td>';
                echo '<td><form action="index.php?do=scripts" method="post"><div><input type="hidden" name="name" value="'.$row['name'].'"/>';
                if (isset($_GET['type']))
                {
                    echo '<input type="hidden" name="type" value="'.$_GET['type'].'"/>';
                }
                echo '<textarea name="event_script" rows="6" cols="55">'.htmlentities($row['event_script']).'</textarea><br/><input type="submit" name="commit" value="Update Script"/></div></form></td>';
                if (checkaccess('natres', 'delete'))
                {
                    echo '<td>';
                    echo '<p><a href="./index.php?do=xmlscriptvalidator&amp;type=progression_events&amp;name='.$row['name'].'">Validate</a></p>';
                    echo '<form action="index.php?do=scripts" method="post"><div><input type="hidden" name="name" value="'.$row['name'].'"/>';
                    if (isset($_GET['type']))
                    {
                        echo '<input type="hidden" name="type" value="'.$_GET['type'].'"/>';
                    }
                    echo '<input type="submit" name="commit" value="Delete"/>';
                    echo '</div></form></td>';
                }
            }
            else
            {
                echo '<td>'.$row['name'].'</td><td>'.htmlspecialchars($row['event_script']).'</td>';
            }
            echo '</tr>';
        }
        echo '</table>';
      }
      else
      {
        echo '<p class="error">No Scripts Found</p>';
      }
      if (checkaccess('natres', 'create'))
      {
        echo '<hr/><p>Create new progression script</p>';
        $prefix='Script names of this type should start with: ';
        echo '<form action="index.php?do=scripts" method="post"><div>';
        if (isset($_GET['type']))
        {
            echo '<input type="hidden" name="type" value="'.$_GET['type'].'"/>';
            switch ($_GET['type'])
            {
                /*case 'RL':
                    $prefix .= '"randomitem"';
                    break;*/
                case 'SI':
                    $prefix .= '"simpleitem"';
                    break;
                case 'CG':
                    $prefix .= '"charcreate" or "PATH"';
                    break;
                case 'S':
                    $prefix .= '"cast" or "apply"';
                    break;
                case 'O':
                    $prefix = '';
                    break;
            }
        }
        echo 'Name: <input type="text" name="name" />  '.$prefix.' <br/>';
        echo 'Script: <textarea name="event_script" rows="6" cols="55"></textarea><br/>';
        if (isset($_GET['type']))
        {
            echo '<input type="hidden" name="type" value="'.$_GET['type'].'"/>';
        }
        echo '<input type="submit" name="commit" value="Create Script" /></div></form>';
      }
    }
  }else{
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}
?>
