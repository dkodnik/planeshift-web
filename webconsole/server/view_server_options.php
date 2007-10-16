<?PHP
function view_server_options()
{
	if(isset($_POST['button']))
	{
        foreach ($_POST as $key => $value)
		{
			if($key == "button")
				continue;
			$sql = "UPDATE server_options SET option_value = '$value' WHERE option_name = '$key'";
			mysql_query2($sql);
		}
	}
	
	$sql = 'select * from server_options order by option_name';
	$query = mysql_query2($sql);
	echo "<form method='POST'>";
	echo"<table width='500' border='0'><tr><td>Option name</td><td>Option value</td></tr>";
	while ($result = mysql_fetch_array($query))
    {
	    echo '<tr><td width="150">' . $result['option_name'] . '</td>';
		echo '<td width="350"><INPUT size=90 type=text name="'.$result['option_name'].'" value="'.$result['option_value'].'"</td></tr>';
	}
	echo"</table>";
	echo "<input type=submit name=button value=Save></form>";
}

?>
