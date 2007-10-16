<?PHP
function list_guilds(){

	checkAccess('guilds', '', 'read');
	$operation=$_GET['operation'];
	$guild=$_GET['guild'];
	include("effects.php");
	
	//basic properties , and guild listing
	if ($operation==''){
		
		$sql = 'select * from guilds order by name';
		$query = mysql_query2($sql);
		echo'<table border="0" cellspacing="0"  cellpadding="0" width="600">';
		echo '<tr>';
		echo '<td width="250">Guild name</td>';
		echo '<td width="30">KP</td>';
		echo '<td width="150">Created</td>';
		echo '<td>Founder</td>';
		echo '</tr>';
		while ($result = mysql_fetch_array($query)){
			
			$sql="select name from characters where id ='".$result['char_id_founder']."'";
			$result2 = mysql_fetch_array( mysql_query2($sql));
			echo "<tr $mouse_over>";
			echo "<td><a href='index.php?page=list_guilds&operation=properties&guild=".$result['id']."'>".$result['name']."</a></td>";
			echo "<td>".$result['karma_points']."</td>";
			echo "<td>".$result['date_created']."</td><td>".$result2['name']."</td>";
			echo "</tr>";
		}
		echo'</table>';
		
	//properties of  single guild
	}elseif( $operation=='properties'){
		
		$sql = "select * from guilds where id = '$guild'";
		$query = mysql_query2($sql);
		
		echo'<table border="1" cellspacing="0"  cellpadding="0">';
		$result = mysql_fetch_array($query);		
		$sql="select name from characters where id ='".$result['char_id_founder']."'";
		$result2 = mysql_fetch_array( mysql_query2($sql));
		echo"
		<tr><td width=150>Name</td><td>".$result['name']."</td></tr>
		<tr><td>Karma points (KP)</td><td>".$result['karma_points']."</td></tr>
		<tr><td>Date created</td><td>".$result['date_created']."</td></tr>
		<tr><td>Founder name</td><td>".$result2['name']."</td></tr>
		<tr><td>Eeb page</td><td><a href='http://".$result['web_page']."'>".$result['web_page']."<a></td></tr>
		<tr><td>MOTD</td><td>".$result['motd']."</td></tr>
		";
		echo'</table>';
		
		//display alliances
		$sql = "select guilds.name as g , alliances.name as a  from alliances  , guilds  where  guilds.alliance =  '".$result['alliance']."' and alliances.id='".$result['alliance']."' and guilds.alliance != '0' limit 1";
		$result3 = mysql_fetch_array(mysql_query2($sql));
		if($result3)
		{
			echo'<br>Alliances:';
    		echo'<br>Leading guild :'.$result3['g'];
    		echo'<br>Alliance name :'.$result3['a'];
    		
    		$sql = "select * from guilds where  alliance =  '".$result['alliance']."' and alliance != '0' order by name";
    		$query = mysql_query2($sql);
    		echo'<table border="1"><tr><td>Name</td><td>KP</td><td>Created</td><td>Founder</td></tr>';
    		while ($result = mysql_fetch_array($query)){
    			
    			$sql="select name from characters where id ='".$result['char_id_founder']."'";
    			$result2 = mysql_fetch_array( mysql_query2($sql));
    			echo"<tr><td><a href='index.php?page=list_guilds&operation=properties&guild=".$result['id']."'>".$result['name']."</a></td><td>".$result['karma_points']."</td><td>".$result['date_created']."</td><td>".$result2['name']."</td></tr>";
    		}
    		echo'</table>';
		}
		
		//display guild members
		echo'<br><b>Members</b>';
		echo'<table border="0" cellspacing="0"  cellpadding="0" width="600">';
		echo'<tr>';
		echo '<td width=200>Member</td>';
		echo '<td>Level</td>';
		echo '<td width=100>Guild points</td>';
		echo '<td>Public notes</td>';
		echo '<td>Private notes</td>';
		echo '</tr>';
		$sql = "select * from characters where guild_member_of  ='$guild' order by name";
		$query = mysql_query2($sql);
		while ($result=mysql_fetch_array($query)){
			echo "<tr $mouse_over>";
			echo '<td><a href="index.php?page=view_characters&character_id='.$result['id'].'">'.$result['name'].'</a></td>';
			echo '<td>'.$result['guild_level'].'</td>';
			echo '<td>'.$result['guild_points'].'</td>';
			echo '<td>'.$result['guild_public_notes'].'</td>';
			echo '<td>'.$result['guild_private_notes'].'</td>';
			echo '</tr>';
		}
		echo'</table>';
	}
}

?>