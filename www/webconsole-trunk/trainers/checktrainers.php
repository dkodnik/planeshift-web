<?
function checktrainers(){
	include('util.php');


    function SumRange( $current, $min, $max ) {
      if ($current==null)
        return $min . "-".$max;

      // build an array with all ranges
      $tok = strtok($current, ',');
      $i=0;
      while ($tok !== false) {
       $ranges[$i++] = $tok;
       $tok = strtok(',');
      }
      //print_r($ranges);

      // check current ranges and expand it
      
      for ($i = 0; $i < sizeof($ranges); $i++) {
          $elem = $ranges[$i];
          $curmin = strtok($elem, '-');
          $curmax = strtok('-');
          //echo "checking: $curmin-$curmax<br>";
          // separate ranges
          if ($curmax < $min || $curmin > $max) {
            $newrange = true;
            break;
          } else {
            $newrange = false;
          }
          // included into the other
          if ($curmax >= $max && $curmin <= $min) {
            break;
          } else if ($max >= $curmax && $min <= $curmin) {
            $ranges[$i] = $min . "-" . $max;
          // merge
          } else if ($curmin < $min && $curmax < $max) {
            $ranges[$i] = $curmin . "-" . $max;
          } else if ($curmin > $min && $curmax > $max) {
            $ranges[$i] = $min . "-" . $curmax;
          }
      }
      
      //print_r($ranges);

      if ($newrange) {
        //echo "it's a new range! $min-$max<br>";
        $ranges[sizeof($ranges)] = $min . "-".$max;
      }
        for ($i = 0; $i < sizeof($ranges); $i++) {
          if ($i==0)
            $final = $ranges[$i];
          else
            $final = $final . ',' . $ranges[$i];
        }
        return $final;
      
      
    }

	?>
	<SCRIPT language=javascript>
	
	function confirmDelete()
	{
	    return confirm("Are you sure you want to remove this category?");
	}
	
	</SCRIPT>
	<?PHP

    checkAccess('npc', '', 'read');

    echo "<A HREF=index.php?page=listtrainers>Back to trainers list</A><br><br>";

	$query = "select skill_id, min_rank, max_rank from trainer_skills where player_id>8 order by min_rank ";
	$result = mysql_query2($query);
    
	while ($line = mysql_fetch_array($result, MYSQL_NUM)){

        $value = $present[$line[0]];
	    // check if present and sum range
	    if ($value!=null) {
	        //echo "START $line[0]: $value,$line[1]-$line[2] <br>";
	        $total = SumRange($value, $line[1],$line[2]);
	        //echo "GOT $line[0]: $total<br>";
	        $present[$line[0]] = $total;
	    } else {
	      $present[$line[0]] = $line[1].'-'.$line[2];
	      //echo "ADDING $line[0]: $line[1]-$line[2]<br>";
	    }

	}
	    //print_r($present);

  // cycle on all skills
	$query = "select skill_id, name from skills order by name";
	$result = mysql_query2($query);

	while ($line = mysql_fetch_array($result, MYSQL_NUM)) {
	  $values = $present[$line[0]];
	  echo "$line[1]: $values<br>";
    }

	echo '<br><br>';
}

?>
