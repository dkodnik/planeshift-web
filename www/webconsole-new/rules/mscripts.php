<?php
function rule_mscripts(){
  if (checkaccess('rules', 'read')){
    if (isset($_POST['commit'])){
      if (($_POST['commit']=='Change Name') && (checkaccess('rules', 'edit'))){
        $name = mysql_real_escape_string($_POST['name']);
        $orig_name = mysql_real_escape_string($_POST['orig_name']);
        $query = "UPDATE math_scripts SET name='$name' WHERE name='$orig_name'";
        $result = mysql_query2($query);
        echo '<p class="error">Update Successful</p>';
        unset($_POST);
        rule_mscripts();
        return;
      }else if (($_POST['commit']=='Update Script') && (checkaccess('rules', 'edit'))){
        $name = mysql_real_escape_string($_POST['name']);
        $math_script = mysql_real_escape_string($_POST['math_script']);
        $query = "UPDATE math_scripts SET math_script='$math_script' WHERE name='$name'";
        $result = mysql_query2($query);
        echo '<p class="error">Update Successful</p>';
        unset($_POST);
        rule_mscripts();
        return;
      }else if (($_POST['commit']=='Delete') && (checkaccess('rules', 'delete'))){
        $name = mysql_real_escape_string($_POST['name']);
        $query = "DELETE FROM math_scripts WHERE name='$name'";
        $result = mysql_query2($query);
        echo '<p class="error">Update Successful</p>';
        unset($_POST);
        rule_mscripts();
        return;
      }else if (($_POST['commit']=='Create Script') && (checkaccess('rules', 'create'))){
        $name = mysql_real_escape_string($_POST['name']);
        $math_script = mysql_real_escape_string($_POST['math_script']);
        $query = "INSERT INTO math_scripts (name, math_script) VALUES ('$name', '$math_script')";
        $result = mysql_query2($query);
        echo '<p class="error">Update Successful</p>';
        unset($_POST);
        rule_mscripts();
        return;
      }
    }else{
      $query = "SELECT name, math_script FROM math_scripts ORDER BY name";
      $result = mysql_query2($query);
      if (mysql_num_rows($result) > 0){
      	echo '<script>
function init() {
	if (!document.getElementsByClassName) {
		document.getElementsByClassName = function(cn) {
			var allT = document.getElementsByTagName("*"), allCN=[], i=0, a;
			while (a = allT[i++]) {
				a.className==cn?allCN[allCN.length]=a:null;
			}
		return allCN
		}
	}
	
	scriptColFix = document.getElementsByClassName("scriptCol");
	scriptCol = scriptColFix[0];
	scriptBoxesWidth = scriptCol.offsetWidth - 50;
	scriptBoxes = document.getElementsByClassName("scriptBox");
	for (var box in scriptBoxes) {
		scriptBoxes[box].style.width = scriptBoxesWidth + "px";
	}
}
window.onload = init;
</script>';
        echo '<table border="1" width="100%">';
        echo '<tr><th>Name</th><th width="100%" class="scriptCol">Script</th>';
        if (checkaccess('rules', 'delete')){
          echo '<th>Actions</th></tr>';
        }else{
          echo '</tr>';
        }
        while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
          echo '<tr>';
          if (checkaccess('rules', 'edit')){
            echo '<td><form action="index.php?do=mscripts" method="post">';
            echo '<input type="hidden" name="orig_name" value="'.$row['name'].'"/><input type="text" name="name" value="'.$row['name'].'"/><br/><input type="submit" name="commit" value="Change Name"/></form></td>';
            echo '<td><form action="index.php?do=mscripts" method="post"><input type="hidden" name="name" value="'.$row['name'].'"/>';
            echo '<textarea name="math_script" rows="6" cols="55" class="scriptBox">'.htmlspecialchars($row['math_script']).'</textarea><br/><input type="submit" name="commit" value="Update Script"/></form></td>';
            if (checkaccess('rules', 'delete')){
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
      if (checkaccess('rules', 'create')){
        echo '<hr/><p>Create new math script</p>';
        echo '<form action="index.php?do=mscripts" method="post">Name: <input type="text" name="name" /><br/>';
        echo 'Script: <textarea name="math_script" rows="6" cols="55"></textarea><br/>';
        echo '<input type="submit" name="commit" value="Create Script" /></form>';
      }
    }
  }else{
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}
?>
