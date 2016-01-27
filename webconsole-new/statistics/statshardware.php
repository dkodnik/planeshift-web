<?php

function statshardware()
{
  include('./graphfunctions.php');
  if(checkaccess('statistics', 'read'))
  {
		?>
		<form action="./index.php?do=statshardware" method="post"><input type="text" value="10" name="filter" size="4"> Threshold for filtering results <input type="submit" name="Filter" value="Filter"/></form>
        <?php
		echo '<p class="header">Operating Systems</p>';

		$filter = 10;
		if ( isset($_POST['filter']) ) {
			$filter = escapeSqlString($_POST['filter']);
		}

		$sql = "SELECT id,operating_system,count(operating_system) as result FROM accounts where operating_system <>'' group by operating_system having result>=".$filter." order by result desc";
		$query = mysql_query2($sql);

		if(sqlNumRows($query) < 1)
		{
			echo '<p class="error">No data found! Try lowering the threshold</p>';
		} else
      outputGraph ($query,0);

    echo '<p class="header">Graphics Cards</p>';

		$sql = "SELECT id,graphics_card,count(graphics_card) as result FROM accounts where graphics_card <>'' group by graphics_card having result>=".$filter." order by result desc";
		$query2 = mysql_query2($sql);

		if(sqlNumRows($query2) < 1)
		{
			echo '<p class="error">No data found! Try lowering the threshold</p>';
		} else
      outputGraph ($query2,0);

    echo '<p class="header">Graphics Version</p>';

		$sql = "SELECT id,graphics_version,count(graphics_version) as result FROM accounts where graphics_version <>'' group by graphics_version having result>=".$filter." order by result desc";
		$query2 = mysql_query2($sql);

		if(sqlNumRows($query2) < 1)
		{
			echo '<p class="error">No data found! Try lowering the threshold</p>';
		} else
      outputGraph ($query2,0);

  }
  else
  {
      echo '<p class="error">You are not authorized to use these functions</p>';
  }
}

?>
