<?
include ('./util.php');

function listlocations($selected,$selectedtype,$selectedsector){

    checkAccess('main', '', 'read');

    echo "<p>Used values for FLAGS: </p>";

    if ($selectedsector != null && $selectedsector != "")
    {
	$data = getDataFromArea($selectedsector);
        $sectors = " and ".$data[0];
    } else
    {
        $sectors = "";
    }


    $query = "select l.id, type_id, l.name, id_prev_loc_in_region, loc_sector_id, x, y, z, radius, flags, t.name from sc_locations l, sc_location_type t where l.type_id=t.id ".$sectors." order by t.name, l.id";
    $result = mysql_query2($query);

    echo '  <TABLE BORDER=1>';
    echo '  <TH> ID </TH> ';
    echo '  <TH> Region Name (ID) </TH> ';
    echo '  <TH> Loc Name (ID) </TH> ';
    echo '  <TH> Prev Loc (ID) </TH> ';
    echo '  <TH> Sector </TH> ';
    echo '  <TH> X </TH> ';
    echo '  <TH> Y </TH> ';
    echo '  <TH> Z </TH> ';
    echo '  <TH> Radius </TH> ';
    echo '  <TH> Flags </TH> ';
    echo '  <TH> Functions </TH> ';

    while ($line = mysql_fetch_array($result, MYSQL_NUM)){
        if (($selected == "" || $selected==$line[0]) &&
            ($selectedtype == "" || $selectedtype==$line[1]))
        {
            echo '<TR>';
            echo "<FORM ACTION=index.php?page=locations_actions&operation=update&selectedsector=$selectedsector METHOD=POST>";
            echo "<INPUT TYPE=hidden NAME=id VALUE=\"$line[0]\"><TD>$line[0]</TD><TD>";
            SelectLocationType($line[1],"loc_type");
            echo "</TD><TD nowap><INPUT TYPE=text NAME=name VALUE=\"$line[2]\"></TD><TD>";
            SelectLocation($line[3],"prev_loc");
            echo "</TD><TD>";
            SelectSectors($line[4],"sector");
            echo "</TD><TD><INPUT TYPE=text NAME=x VALUE=\"$line[5]\"></TD>";
            echo "<TD><INPUT TYPE=text NAME=y VALUE=\"$line[6]\"></TD>";
            echo "<TD><INPUT TYPE=text NAME=z VALUE=\"$line[7]\"></TD>";
            echo "<TD><INPUT TYPE=text NAME=radius VALUE=\"$line[8]\"></TD>";
            echo "<TD><INPUT TYPE=text NAME=flags VALUE=\"$line[9]\"></TD>";
            echo '<TD><INPUT TYPE=SUBMIT NAME=submit VALUE=Update></FORM>';
            echo "<FORM ACTION=index.php?page=locations_actions&operation=delete&selectedsector=$selectedsector METHOD=POST><INPUT TYPE=hidden NAME=id VALUE=\"$line[0]\"><INPUT TYPE=SUBMIT NAME=submit VALUE=Delete>";
            echo '</FORM></TD></TR>';
        }
    }
    echo '<TR>';
    echo '<TD></TD>';
    echo "<FORM ACTION=index.php?page=locations_actions&operation=create&selectedsector=$selectedsector METHOD=POST><TD>";
    SelectLocationType("","loc_type");
    echo "<TD><INPUT TYPE=text NAME=name ></TD>";
    echo '<TD>';
    SelectLocation("-1","prev_loc");
    echo '</TD><TD>';
    SelectSectors("","sector");
    echo '</TD>';
    echo "<TD><INPUT TYPE=text NAME=x ></TD>";
    echo "<TD><INPUT TYPE=text NAME=y ></TD>";
    echo "<TD><INPUT TYPE=text NAME=z ></TD>";
    echo "<TD><INPUT TYPE=text NAME=radius ></TD>";
    echo "<TD><INPUT TYPE=text NAME=flags ></TD>";
    echo '<TD><INPUT TYPE=SUBMIT NAME=submit VALUE=Create></FORM>';
    echo '</FORM></TD></TR>';


    echo '</TABLE>';

    echo '<P>Create Location Type<P><TABLE BORDER=1>';
    echo '<TR>';
    echo '<TH>Name</TH> ';
    echo '</TR><TR>';
    echo '<FORM ACTION=index.php?page=locations_actions&operation=createtype&selectedsector=$selectedsector METHOD=POST>';
    echo "<TD><INPUT TYPE=text NAME=name ></TD>";
    echo "<TD><INPUT TYPE=SUBMIT NAME=submit VALUE=Create></FORM>";
    echo '</TD></TR>';
    echo '</TABLE>';

    echo "<P><A HREF=\"index.php?category={$_GET['category']}&page=listlocations\">List/Edit All Locations</A></P>";
    echo "<P><A HREF=\"index.php?category={$_GET['category']}&page=locations_map\">View Map of All Locations</A></P>";
    echo '<br><br>';
}

?>
  
