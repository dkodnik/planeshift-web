<?php
function rule_mscripts(){
  if (checkaccess('crafting', 'read')){
      	echo '<script>
function fixWidth () {
	$(".scriptBox").width(50);
	$("#scriptCol").width("100%");
	$(".scriptBox").width($("#scriptCol").width() - 50);
}
jQuery(function($) {
	fixWidth();
	$(window).resize(function() {
		fixWidth();
	});
});
</script>';
    if (isset($_POST['commit'])){
      if (($_POST['commit']=='Change Name') && (checkaccess('crafting', 'edit'))){
        $name = escapeSqlString($_POST['name']);
        $orig_name = escapeSqlString($_POST['orig_name']);
        $query = "UPDATE math_scripts SET name='$name' WHERE name='$orig_name'";
        $result = mysql_query2($query);
        echo '<p class="error">Update Successful</p>';
        unset($_POST);
        rule_mscripts();
        return;
      }else if (($_POST['commit']=='Update Script') && (checkaccess('crafting', 'edit'))){
        $name = escapeSqlString($_POST['name']);
        $math_script = escapeSqlString($_POST['math_script']);
        $query = "UPDATE math_scripts SET math_script='$math_script' WHERE name='$name'";
        $result = mysql_query2($query);
        echo '<p class="error">Update Successful</p>';
        unset($_POST);
        rule_mscripts();
        return;
      }else if (($_POST['commit']=='Delete') && (checkaccess('crafting', 'delete'))){
        $name = escapeSqlString($_POST['name']);
        $query = "DELETE FROM math_scripts WHERE name='$name'";
        $result = mysql_query2($query);
        echo '<p class="error">Update Successful</p>';
        unset($_POST);
        rule_mscripts();
        return;
      }else if (($_POST['commit']=='Create Script') && (checkaccess('crafting', 'create'))){
        $name = escapeSqlString($_POST['name']);
        $math_script = escapeSqlString($_POST['math_script']);
        $query = "INSERT INTO math_scripts (name, math_script) VALUES ('$name', '$math_script')";
        $result = mysql_query2($query);
        echo '<p class="error">Update Successful</p>';
        unset($_POST);
        rule_mscripts();
        return;
      }
    }else{
      $query = "SELECT name, math_script FROM math_scripts";
      if (isset($_GET['name']))
      {
        $query .= " WHERE name='".escapeSqlString($_GET['name'])."'";
      }
      $query .= " ORDER BY name";
      $result = mysql_query2($query);
      if (sqlNumRows($result) > 0){
        echo '<table border="1" width="100%">';
        echo '<tr><th>Name</th><th id="scriptCol">Script</th>';
        if (checkaccess('crafting', 'delete')){
          echo '<th>Actions</th></tr>';
        }else{
          echo '</tr>';
        }
        while ($row = fetchSqlAssoc($result)){
          echo '<tr>';
          if (checkaccess('crafting', 'edit')){
            echo '<td><form action="index.php?do=mscripts" method="post">';
            echo '<input type="hidden" name="orig_name" value="'.$row['name'].'"/><input type="text" name="name" value="'.$row['name'].'"/><br/><input type="submit" name="commit" value="Change Name"/></form></td>';
            echo '<td><form action="index.php?do=mscripts" method="post"><input type="hidden" name="name" value="'.$row['name'].'"/>';
            echo '<textarea name="math_script" rows="6" cols="55" class="scriptBox">'.htmlspecialchars($row['math_script']).'</textarea><br/><input type="submit" name="commit" value="Update Script"/></form></td>';
            if (checkaccess('crafting', 'delete')){
              echo '<td><form action="index.php?do=mscripts" method="post"><input type="hidden" name="name" value="'.$row['name'].'"/>';
              echo '<input type="submit" name="commit" value="Delete"/>';
              echo '</form></td>';
            }
          }else{
            echo '<td>'.$row['name'].'</td><td>'.htmlspecialchars($row['math_script']).'</td>';
          }
          echo '</tr>';
        }
        echo '</table>';
      }else{
        echo '<p class="error">No Scripts Found</p>';
      }
      if (checkaccess('crafting', 'create')){
        echo '<hr/><p>Create new math script</p>';
        echo '<form action="index.php?do=mscripts" method="post">Name: <input type="text" name="name" /><br/>';
        echo 'Script: <textarea name="math_script" rows="6" cols="55" class="scriptBox"></textarea><br/>';
        echo '<input type="submit" name="commit" value="Create Script" /></form>';
      }
    }
  }else{
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}
?>
