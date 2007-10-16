<?
include ('./util.php');

function listwaypoints($selected){

    checkAccess('main', '', 'read');

    echo "<p>Used values for FLAGS: ALLOW_RETURN </p>";

    $query = "select id, loc_sector_id, x, y, z, radius, flags, name from sc_waypoints";
    $result = mysql_query2($query);

    echo '  <TABLE BORDER=1>';
    echo '  <TH> ID </TH> ';
    echo '  <TH> Sector </TH> ';
    echo '  <TH> Name </TH> ';
    echo '  <TH> X </TH> ';
    echo '  <TH> Y </TH> ';
    echo '  <TH> Z </TH> ';
    echo '  <TH> Radius </TH> ';
    echo '  <TH> Flags </TH> ';
    echo '  <TH> Functions </TH> ';

    while ($line = mysql_fetch_array($result, MYSQL_NUM)){
        if ($selected == "" || $selected==$line[0])
        {
            echo '<TR>';
            echo '<FORM ACTION=index.php?page=waypoints_actions&operation=update METHOD=POST>';
            echo "<TD><INPUT TYPE=hidden NAME=id VALUE=\"$line[0]\">$line[0]</TD><TD>";
            SelectSectors($line[1],"sector");
            echo "</TD><TD><INPUT TYPE=text NAME=name VALUE=\"$line[7]\"></TD>";
            echo "</TD><TD><INPUT TYPE=text NAME=x VALUE=\"$line[2]\"></TD>";
            echo "<TD><INPUT TYPE=text NAME=y VALUE=\"$line[3]\"></TD>";
            echo "<TD><INPUT TYPE=text NAME=z VALUE=\"$line[4]\"></TD>";
            echo "<TD><INPUT TYPE=text NAME=radius VALUE=\"$line[5]\"></TD>";
            echo "<TD><INPUT TYPE=text NAME=flags VALUE=\"$line[6]\"></TD>";
            echo '<TD><INPUT TYPE=SUBMIT NAME=submit VALUE=Update></FORM>';
            echo "<FORM ACTION=index.php?page=waypoints_actions&operation=delete METHOD=POST><INPUT TYPE=hidden NAME=id VALUE=\"$line[0]\"><INPUT TYPE=SUBMIT NAME=submit VALUE=Delete>";
            echo '</FORM></TD></TR>';
        }
    }
    echo '<TR>';
    echo '<TD></TD>';
    echo '<FORM ACTION=index.php?page=waypoints_actions&operation=create METHOD=POST>';
    echo '<TD>';
    SelectSectors("","sector");
    echo '</TD>';
    echo "<TD><INPUT TYPE=text NAME=name ></TD>";
    echo "<TD><INPUT TYPE=text NAME=x ></TD>";
    echo "<TD><INPUT TYPE=text NAME=y ></TD>";
    echo "<TD><INPUT TYPE=text NAME=z ></TD>";
    echo "<TD><INPUT TYPE=text NAME=radius ></TD>";
    echo "<TD><INPUT TYPE=text NAME=flags ></TD>";
    echo '<TD><INPUT TYPE=SUBMIT NAME=submit VALUE=Create></FORM>';
    echo '</FORM></TD></TR>';


    echo '</TABLE>';
    echo '<P><A HREF="index.php?page=listwaypoints">List/Edit All Waypoints</A></P>';
    echo '<P><A HREF="index.php?page=waypoints_map">View Map of All Waypoints</A></P>';
    echo '<br><br>';
}

?>
  
