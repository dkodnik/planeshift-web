<?php

function statshardware()
{
  include('./graphfunctions.php');
  if(checkaccess('statistics', 'read'))
  {
		$filter = 10;
		$psversion = "psu";
		if ( isset($_POST['filter']) ) {
			$filter = escapeSqlString($_POST['filter']);
		}
		if ( isset($_POST['psversion']) ) {
			$psversion = escapeSqlString($_POST['psversion']);
			echo "<br/> PSVersion used for query: ".$psversion;
		}
		
		?>
		<form action="./index.php?do=statshardware" method="post"> Minimum number of result count required <input type="text" value="<?=$filter?>" name="filter" size="4"><br/>
        <?php
		echo "Filter by release: " . OutputPSVersionDropdown($psversion);
		echo "<br/><input type=\"submit\" name=\"Filter\" value=\"Filter\"/></form>";
		echo '<p class="header">Operating Systems</p>';
	




		$sql = "SELECT id,operating_system,count(operating_system) as result FROM accounts where operating_system <>'' AND ".generateWhereClauseByPSVersion($psversion)." group by operating_system having result>=".$filter." order by result desc";
		$query = mysql_query2($sql);

		if(sqlNumRows($query) < 1)
		{
			echo '<p class="error">No data found! Try lowering the threshold</p>';
		} else
      outputGraph ($query,0);

    echo '<p class="header">Graphics Cards</p>';

		$sql = "SELECT id,graphics_card,count(graphics_card) as result FROM accounts where graphics_card <>'' AND ".generateWhereClauseByPSVersion($psversion)." group by graphics_card having result>=".$filter." order by result desc";
		$query2 = mysql_query2($sql);

		if(sqlNumRows($query2) < 1)
		{
			echo '<p class="error">No data found! Try lowering the threshold</p>';
		} else
      outputGraph ($query2,0);

    echo '<p class="header">Graphics Version</p>';

		$sql = "SELECT id,graphics_version,count(graphics_version) as result FROM accounts where graphics_version <>'' AND ".generateWhereClauseByPSVersion($psversion)." group by graphics_version having result>=".$filter." order by result desc";
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
