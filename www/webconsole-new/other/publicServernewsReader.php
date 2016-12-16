<?php
/*
 * This script is designed to operate outside the regular context of the WC, the path mentioned below can be used to point it to its files.
 */
$pathToWC = './../'; // this *must* end in a /, use ./ if the script is in the WC root, otherwise use an additional ../ for every directory it is below that.
include($pathToWC.'commonfunctions.php');
include($pathToWC.'../secure/db_config.php');

SetUpDB($db_hostname, $db_username, $db_password, $db_name);

$sql = "SELECT * FROM wc_servernews WHERE id='1'";
$row = fetchSqlAssoc(mysql_query2($sql));

echo htmlentities($row['news']);
    
?>