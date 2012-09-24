<?php
function glyphs_used(){
  if (checkaccess('spells', 'read')){
    $query = "SELECT i.name, g.spell_id, s.name AS spell FROM item_stats AS i LEFT JOIN spell_glyphs AS g ON i.id=g.item_id LEFT JOIN spells AS s ON s.id=g.spell_id WHERE i.category_id='5' ORDER BY name";
    $result = mysql_query2($query);
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
      $g = $row['name'];
      $Glyphs["$g"]['name'] = $row['name'];
      if ($row['spell'] != ''){
        $Glyphs["$g"]['spells'][] = $row['spell'];
        $Glyphs["$g"]['spell_id'][] = $row['spell_id'];
      }
    }
    echo '<table border="1"><tr><th>Glyph</th><th>Used In</th></tr>';
    foreach ($Glyphs as $G){
      echo '<tr><td>'.$G['name'].'</td>';
      if (isset($G['spells'])){
        echo '<td>';
        $i = 1;
        while ($i !== NULL){
          if ($i != 1){
            echo '<br/>';
          }
          $i = array_shift($G['spells']);
          $j = array_shift($G['spell_id']);
          echo '<a href="./index.php?do=spell&amp;id='.$j.'">'.$i.'</a>';
        }
        echo '</td>';
      }else{
        echo '<td>Not Used</td>';
      }
      echo '</tr>';
    }
    echo '</table>';
  }else{
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}
?>
