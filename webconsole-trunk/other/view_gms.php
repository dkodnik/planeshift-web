<?PHP
function view_gms(){

	checkAccess('main', '', 'delete');

	include("effects.php");
	$accp = ($_GET['page'] == "view_accounts");


    // display commands used
    $id = $_GET['id'];
    if ($id != 0) {
      $sql="select * from gm_command_log where gm=".$id;
	  $result = mysql_query2($sql);
      echo "<TABLE CELLPADDING=5><TH>GM</TH><TH>Action</TH><TH>Date</TH>";

      while ( $rowGM = mysql_fetch_array( $result, MYSQL_BOTH ) )
      {
          $gmID = $rowGM["gm"];
          $gmNameQuery = "SELECT * FROM characters where id=$gmID";
          $result2 = mysql_query($gmNameQuery);
          $gmAcc = mysql_fetch_array($result2, MYSQL_BOTH );
          $gmName = $gmAcc["name"] . " " . $gmAcc["lastname"];

          echo "<TR><TD>$gmName</TD><TD>" . $rowGM["command"] . "</TD><TD>" . $rowGM["ex_time"] . "</TD></TR>";
      }
      echo "</TABLE>";
      return;
    }

    // display list of GMs
	$supercln = mysql_fetch_array(
					mysql_query2("SELECT id FROM accounts WHERE username = 'superclient'"),
					MYSQL_NUM);
	$supercln = $supercln[0];

	
	if ($_GET['character_id']!=''){
		$sql="select * from characters where id='".$_GET['character_id']."'";	
	}
	else if ($accp){	
		$sql="select * from characters where account_id='".$_GET['account_id']."'";	
	}
	else {$sql="select id,security_level,username from accounts where security_level>0 and id!=$supercln order by security_level desc";}
	$result = mysql_query2($sql);

	if($accp)
		echo "<table border='0'><tr><td width='50'><td>";
		
	echo "<table border='0' cellspacing='0'  cellpadding='0' width=\"1100\">";
	echo "<tr><td colspan='37'><b><h3>Accounts with GM powers</h3>";
	if($accp)
		echo " belonging to account ".$_GET['account_id'];
		
	echo "</b></td></tr>";
	echo "<tr height='1'>";
	echo "<td width='150'><b>AccID/CharID</b></td>";
	echo "<td width='150'><b>Security Level</b></td>";
	echo "<td width='200'><b> Username</b></td>";
	echo "<td width='200'><b> Firstname</b></td>";
	echo "<td width='200'><b> Lastname</b></td>";
	echo "<td width='200'><b> Guild</b></td>";
	echo "<td width='200'><b> Total time connected</b></td>";
	echo "<td width='200'><b> Reports</b></td>";
	echo "</tr>";

    // cycle on accounts
	while ( $line= mysql_fetch_array($result)) {

		$sql="select id,name,lastname,guild_member_of,time_connected_sec from characters where account_id=".$line['id']."";
		//echo "$sql<br>";
		$result2 = mysql_query2($sql);

        // cycle on characters
      	while ( $temp3= mysql_fetch_array($result2,MYSQL_ASSOC)) {

    		$sql="select name  from guilds where id='".$temp3['guild_member_of']."'";
    		$guild= mysql_fetch_array(mysql_query2($sql),MYSQL_ASSOC);

    		// Mouse over effect
    		echo "<tr $mouse_over>";
    		// ID
    		echo "<td><a href='index.php?page=view_accounts&account_id=".$line['id']."'>".$line['id']."</a> / ".$temp3['id']."</td>";
    		// Security level
    		echo "<td>".$line['security_level']."</td>";
    		// Username
    		echo "<td>".$line['username']."</td>";
    		// Firstname
    		echo "<td><a href='index.php?page=viewnpc&id=".$temp3['id']."'>".$temp3['name']."</a></td>";
    		// Lastname
    		echo "<td>".$temp3['lastname']."</td>";
    			
    		// Guild
    		echo "<td><a href='index.php?page=list_guilds&operation=properties&guild=".$temp3['guild_member_of']."'>".$guild['name']."</a></td>";

    		// Time connected
    		$tt = $temp3['time_connected_sec'];
    
            $days = (int)($tt / (60*60*24));
            $tt -= $days*60*60*24;
    
            $hours = (int)($tt / (60*60));
            $tt -= $hours*60*60;
    
            $mins = (int)($tt / 60);		
    		
    		echo "<td>$days days, $hours h, $mins m "."</td>";

    		$sql2="select count(*) as c from gm_command_log where gm=".$temp3['id'];
    		$result3 = mysql_query2($sql2);
    		$count= mysql_fetch_array($result3,MYSQL_ASSOC);

    		echo "<td><a href=index.php?page=view_gms&id=".$temp3['id'].">commands (".$count['c'].")</td>";

    		echo "</tr>";
	    }
	}		
	echo"</table>";
	if($accp)
		echo "</td></tr></table>";
}
?>