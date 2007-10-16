<?PHP
include('view_characters.php');

function view_accounts(){

	checkAccess('accounts', '', 'read');

   $limit = 100; // number of accounts displayed per page
   $pageNo = $_POST["gotopage"] ? $_POST["gotopage"] :($_GET["pageNo"] ? $_GET["pageNo"] : 0);
   $accountOffset = $pageNo * $limit;

	if ( $_GET['account_id']!=''){
		$sql="select * from accounts where id ='".$_GET['account_id']."'";
	}
	else {
      $sql="select * from accounts ";
      if ($_POST["findaccount"] != "") {
         $sql .= "WHERE username LIKE '%" . $_POST["findaccount"] . "%' ";
      }
      else
      {
         $sql .= "LIMIT " . $accountOffset . "," . $limit;
      }
   }
	$query = mysql_query2($sql);

   echo "<table border='0' cellspacing='0'  cellpadding='0' width=\"100%\">";
	echo "<tr><td colspan='12'><b>Accounts</b></td></tr>";
	echo "<tr><td width='50'><b>ID</b></td>";
	echo "<td width='300'><b>Account name</b></td>";
	echo "<td width='100'><b>Account status</b></td>";
	echo "<td width='400'><b>Verifying code</b></td>";
	echo "</tr>";
		 
	while ( $temp= mysql_fetch_array($query))
	{
		$banned = ($temp['status'] == "B");
		if($banned)
			$ban_until = date("i:H - d/m/Y",$temp['banned_until']);
		
		echo "<tr $mouse_over><td>".$temp['id']."</td>";
		echo "<td><a href='index.php?page=view_accounts";
		
		// If we clicked on an account, show the chars
		// then if we click again, we should move back to the list
		if($_GET['account_id'] == '')
			echo "&account_id=".$temp['id'];
		echo "'>".$temp['username']."</a></td>";
		
		echo "<td>";
		if($banned)
			echo "Banned until $ban_until";
		else if($temp['status'] == "U")
			echo "Unactive";	
		else if($temp['status'] == "A")
			echo "Active";
		else
			echo "Other";
		echo "</td>";
		
		echo "<td>".$temp['verificationid']."</td>";
		
		echo "</tr>";
	}
	echo "</table>";
	
	if ( $_GET['account_id']!=''){
		view_characters();
	}

   // display scroll buttons if necessary
   if ($_GET['account_id'] == '')
   {
      if (!$_POST["findaccount"]) {
         $sql="select count(*) as cnt from accounts";
         if ($_POST["findaccount"] != "") {
            $sql .= " WHERE username LIKE '%" . $_POST["findaccount"] . "%' ";
         }
         $query = mysql_query2($sql);
         $temp=mysql_fetch_array($query);

         echo "<br><br>";
         echo "<table cellspacing='0' cellpadding='0' border='0'><tr>";

         // scroll back button
         echo "<td style='width:50px'>";
         if ($accountOffset != 0) {
            echo "<a href='index.php?page=view_accounts&pageNo=" . ($pageNo-1) . "'>&lt;&lt;</a>";
         } else {
            echo "&lt;&lt;";
         }
         echo "</td>";

         echo "<td style='width:180px'>";
         echo "<form method='post' action='index.php?page=view_accounts'>";
         echo "<input type='text' style='width:40px' name='gotopage'> ";
         echo "<input type='submit' value='go to page'>";
         echo "</form>";
         echo "</td>";

         // scroll forward button
         echo "<td style='width:50px'>";
         if ($temp["cnt"] > $accountOffset+$limit)
         {
            echo "<a href='index.php?page=view_accounts&pageNo=" . ($pageNo+1) . "'>&gt;&gt;</a>";
         } else {
            echo "&gt;&gt;";
         }
         echo "</td>";

         echo "</tr></table>";
      }

      echo "<form method='post' action='index.php?page=view_accounts'>";
      echo "Find account: ";
      echo "<input type='text' style='width:200px' name='findaccount'> ";
      echo "<input type='submit' value='search'>";
      echo "</form>";

   }
   
}
?>