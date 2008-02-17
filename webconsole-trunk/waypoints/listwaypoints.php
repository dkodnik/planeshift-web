<?
include ('./util.php');

function listwaypoints($selected,$sector){

    checkAccess('main', '', 'read');

    echo "<FORM action=\"index.php?page=listwaypoints&category={$_REQUEST['category']}\" METHOD=POST>";
    echo "<b>Select one area:</b> <br><br> Area: ";
    SelectAreas($sector,"sector");
    echo " <br><br><INPUT type=submit value=view><br><br>";
    echo "</FORM>";

    echo "<p>Used values for FLAGS: ALLOW_RETURN </p>";

    $query = "select id, loc_sector_id, x, y, z, radius, flags, name, wp_group from sc_waypoints";
    if ($selected != "")
    {
       $query = $query." where id=".$selected;
    } else if ($sector != "")
    {
       $data = getDataFromArea($sector);
       $sectors = $data[0];
       $query = $query." where " .$sectors;
    } else
    {
       return;
    }
    $result = mysql_query2($query);

    echo '  <TABLE BORDER=1>';
    echo '  <TH> ID </TH> ';
    echo '  <TH> Sector </TH> ';
    echo '  <TH> Name </TH> ';
    echo '  <TH> Group </TH> ';
    echo '  <TH> X </TH> ';
    echo '  <TH> Y </TH> ';
    echo '  <TH> Z </TH> ';
    echo '  <TH> Radius </TH> ';
    echo '  <TH> Flags </TH> ';
    echo '  <TH> Functions </TH> ';

    while ($line = mysql_fetch_array($result, MYSQL_NUM)){
       echo '<TR>';
       echo '<FORM ACTION=index.php?page=waypoints_actions&operation=update&area='.$sector.' METHOD=POST>';
       echo "<TD><INPUT TYPE=hidden NAME=id VALUE=\"$line[0]\">$line[0]</TD><TD>";
       SelectSectors($line[1],"sector");
       echo "</TD><TD><INPUT TYPE=text NAME=name VALUE=\"$line[7]\"></TD>";
       echo "</TD><TD><INPUT TYPE=text NAME=wp_group VALUE=\"$line[8]\"></TD>";
       echo "</TD><TD><INPUT TYPE=text NAME=x SIZE=10 VALUE=\"$line[2]\"></TD>";
       echo "<TD><INPUT TYPE=text NAME=y SIZE=10  VALUE=\"$line[3]\"></TD>";
       echo "<TD><INPUT TYPE=text NAME=z SIZE=10  VALUE=\"$line[4]\"></TD>";
       echo "<TD><INPUT TYPE=text NAME=radius  SIZE=10 VALUE=\"$line[5]\"></TD>";
       echo "<TD><INPUT TYPE=text NAME=flags VALUE=\"$line[6]\"></TD>";
       echo '<TD><INPUT TYPE=SUBMIT NAME=submit VALUE=Update></FORM>';
       echo '<FORM ACTION=index.php?page=waypoints_actions&operation=delete&area='.$sector.' METHOD=POST><INPUT TYPE=hidden NAME=id VALUE=\"$line[0]\"><INPUT TYPE=SUBMIT NAME=submit VALUE=Delete>';
       echo '</FORM></TD></TR>';
    }
    echo '<TR>';
    echo '<TD></TD>';
    echo '<FORM ACTION=index.php?page=waypoints_actions&operation=create&area='.$sector.' METHOD=POST>';
    echo '<TD>';
    SelectSectors("","sector");
    echo '</TD>';
    echo "<TD><INPUT TYPE=text NAME=name ></TD>";
    echo "<TD><INPUT TYPE=text NAME=wp_group ></TD>";
    echo "<TD><INPUT TYPE=text NAME=x SIZE=10 ></TD>";
    echo "<TD><INPUT TYPE=text NAME=y SIZE=10 ></TD>";
    echo "<TD><INPUT TYPE=text NAME=z SIZE=10 ></TD>";
    echo "<TD><INPUT TYPE=text NAME=radius  SIZE=10></TD>";
    echo "<TD><INPUT TYPE=text NAME=flags ></TD>";
    echo '<TD><INPUT TYPE=SUBMIT NAME=submit VALUE=Create></FORM>';
    echo '</FORM></TD></TR>';


    echo '</TABLE>';
    echo '<P><A HREF="index.php?page=listwaypoints&sector='.$sector.'">List/Edit All Waypoints</A></P>';
    echo '<P><A HREF="index.php?page=waypoints_map&sector='.$sector.'">View Map of All Waypoints</A></P>';
    echo '<br><br>';
}

?>
  
