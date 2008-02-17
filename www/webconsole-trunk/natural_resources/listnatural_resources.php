<?
include ('./util.php');

function listnatural_resources($selected){

    checkAccess('main', '', 'read');

    echo "Resources used for foundryman : copper, iron, coal, zinc <br>";
    echo "Precious resources: silver, gold, platinum <br>";
    echo "Gems resources: ruby, emerald, diamond<br>";
    echo "Other resources: tin, mercury<br>";
    echo "<br>";

    $query = "select id, loc_sector_id, loc_x, loc_y, loc_z, radius, visible_radius, probability, skill, skill_level, item_cat_id, item_quality, animation, anim_duration_seconds, item_id_reward, reward_nickname from natural_resources";
    $result = mysql_query2($query);

    echo '  <TABLE BORDER=1>';
    echo '  <TH> ID </TH> ';
    echo '  <TH> Sector </TH> ';
    echo '  <TH> X </TH> ';
    echo '  <TH> Y </TH> ';
    echo '  <TH> Z </TH> ';
    echo '  <TH> Radius </TH> ';
    echo '  <TH> Visible Radius </TH> ';
    echo '  <TH> Probability </TH> ';
    echo '  <TH> Skill </TH> ';
    echo '  <TH> Skill Level </TH> ';
    echo '  <TH> Item Cat Id </TH> ';
    echo '  <TH> Item Quality </TH> ';
    echo '  <TH> Animation </TH> ';
    echo '  <TH> Anim Duration Seconds </TH> ';
    echo '  <TH> Item Id Reward </TH> ';
    echo '  <TH> Reward Nickname </TH> ';
    echo '  <TH> Functions </TH> ';

    while ($line = mysql_fetch_array($result, MYSQL_NUM)){
        if ($selected == "" || $selected==$line[0])
        {

            echo '<TR>';
            echo '<FORM ACTION=index.php?page=natural_resources_actions&operation=update METHOD=POST>';
            echo "<TD><INPUT TYPE=hidden NAME=id VALUE=\"$line[0]\">$line[0]</TD><TD>";
            SelectSectors($line[1],"sector");
            echo "</TD><TD><INPUT TYPE=text NAME=loc_x VALUE=\"$line[2]\"></TD>";
            echo "<TD><INPUT TYPE=text NAME=loc_y VALUE=\"$line[3]\"></TD>";
            echo "<TD><INPUT TYPE=text NAME=loc_z VALUE=\"$line[4]\"></TD>";
            echo "<TD><INPUT TYPE=text NAME=radius VALUE=\"$line[5]\"></TD>";
            echo "<TD><INPUT TYPE=text NAME=visible_radius VALUE=\"$line[6]\"></TD>";
            echo "<TD><INPUT TYPE=text NAME=probability VALUE=\"$line[7]\"></TD>";
            echo '<TD>';
            SelectSkills($line[8],"skill");
            echo '</TD>';
            echo "<TD><INPUT TYPE=text NAME=skill_level VALUE=\"$line[9]\"></TD>";
            echo '<TD>';
            SelectItemCateogory($line[10],"item_cat_id");
            echo '</TD>';
            echo "<TD><INPUT TYPE=text NAME=item_quality VALUE=\"$line[11]\"></TD>";
            echo "<TD><INPUT TYPE=text NAME=animation VALUE=\"$line[12]\"></TD>";
            echo "<TD><INPUT TYPE=text NAME=anim_duration_seconds VALUE=\"$line[13]\"></TD>";
            echo '<TD>';
            SelectBaseItem($line[14],"item_id_reward");
            echo '</TD>';
            echo "<TD><INPUT TYPE=text NAME=reward_nickname VALUE=\"$line[15]\"></TD>";
            echo '<TD><INPUT TYPE=SUBMIT NAME=submit VALUE=Update></FORM>';
            echo "<FORM ACTION=index.php?page=natural_resources_actions&operation=delete METHOD=POST><INPUT TYPE=hidden NAME=id VALUE=\"$line[0]\"><INPUT TYPE=SUBMIT NAME=submit VALUE=Delete>";
            echo '</FORM></TD></TR>';
        }
    }
    echo '<TR>';
    echo '<TD></TD>';
    echo '<FORM ACTION=index.php?page=natural_resources_actions&operation=create METHOD=POST>';
    echo '<TD>';
    SelectSectors("","sector");
    echo '</TD>';
    echo "<TD><INPUT TYPE=text NAME=loc_x ></TD>";
    echo "<TD><INPUT TYPE=text NAME=loc_y ></TD>";
    echo "<TD><INPUT TYPE=text NAME=loc_z ></TD>";
    echo "<TD><INPUT TYPE=text NAME=radius ></TD>";
    echo "<TD><INPUT TYPE=text NAME=visible_radius ></TD>";
    echo "<TD><INPUT TYPE=text NAME=probability ></TD>";
    echo '<TD>';
    SelectSkills(-1,"skill");
    echo '</TD>';
    echo "<TD><INPUT TYPE=text NAME=skill_level ></TD>";
    echo '<TD>';
    SelectItemCateogory(-1,"item_cat_id");
    echo '</TD>';
    echo "<TD><INPUT TYPE=text NAME=item_quality ></TD>";
    echo "<TD><INPUT TYPE=text NAME=animation ></TD>";
    echo "<TD><INPUT TYPE=text NAME=anim_duration_seconds ></TD>";
    echo '<TD>';
    SelectBaseItem(-1,"item_id_reward");
    echo '</TD>';
    echo "<TD><INPUT TYPE=text NAME=reward_nickname ></TD>";
    echo '<TD><INPUT TYPE=SUBMIT NAME=submit VALUE=Create></FORM>';
    echo '</FORM></TD></TR>';


    echo '</TABLE>';

    echo '<P><A HREF="index.php?page=listnatural_resources">List/Edit All Natrual Resources</A></P>';
    echo '<P><A HREF="index.php?page=natural_resources_map">View Map of All Natrual Resources</A></P>';

    echo '<br><br>';
}

?>
  
