<?
function listlootcategories($selectedloot){
        include('util.php');


	?>


<SCRIPT language=javascript>

function confirmDelete()
{
    return confirm("Are you sure you want to delete this loot?");
}

</SCRIPT>

<?PHP

    checkAccess('npc', '', 'read');

    echo '  <TABLE BORDER=1>';
    echo '  <TR><TH> Category </TH> ';
    echo '  <TH> Items </TH> <TH>NPCs</TH></TR>';

    // check if one loot has been selected
    if ($selectedloot) {
	  $query = "select id, name from loot_rules where id=".$selectedloot ;
	  $editmode = 1;
	} else {
	  $query = "select id, name from loot_rules order by id";
	  $editmode = 0;
	}

	$category_result = mysql_query2($query);
	while ($category = mysql_fetch_array($category_result, MYSQL_NUM)){
	   echo "<TR><TD>(<A HREF=index.php?page=listlootcategories&selectedloot=$category[0]>$category[0]</A>) <br>$category[1]</TD>";
	   echo '<TD>';

	   $query = "select item_stat_id,name,probability,min_money,max_money,randomize from loot_rule_details as b ,item_stats as c where b.loot_rule_id='$category[0]' and c.id=item_stat_id";
	   $result = mysql_query2($query);

       // prints items table
	   echo '  <TABLE BORDER=4>';
           echo '  <TH> Id </TH> ';
           echo '  <TH> Name </TH> ';
           echo '  <TH> Probability </TH>';
           echo '  <TH> Min money </TH>';
           echo '  <TH> Max money </TH>';
           echo '  <TH> Randomize </TH>';

	   while ($line = mysql_fetch_array($result, MYSQL_NUM)){

            if ($editmode) {
			echo '<TH></TH><TH></TH></TR>';        
        		echo '<TR>';
        		echo '<FORM ACTION=index.php?page=lootcategories_actions&operation=update METHOD=POST>';
        		echo "<TD><INPUT TYPE=hidden NAME=cat_id VALUE=\"$category[0]\"><INPUT TYPE=hidden NAME=item_stat_id VALUE=\"$line[0]\">$line[0]</TD>";
        		echo "<TD>$line[1]</TD>";
        		echo "<TD><INPUT TYPE=text size=5 name=probability VAlUE=\"$line[2]\"></TD>";
        		echo "<TD><INPUT TYPE=text size=5 name=min_money VAlUE=\"$line[3]\"></TD>";
        		echo "<TD><INPUT TYPE=text size=5 name=max_money VAlUE=\"$line[4]\"></TD>";
        		echo "<TD><INPUT TYPE=text size=5 name=randomize VAlUE=\"$line[5]\"></TD>";
        		echo '<TD><INPUT TYPE=SUBMIT NAME=submit VALUE=Update></FORM>';
        		echo '</TD><TD>';
        		echo "<FORM ACTION=index.php?page=lootcategories_actions&operation=remove METHOD=POST onsubmit=\"return confirmDelete()\">";
        		echo "<INPUT TYPE=hidden NAME=cat_id VALUE=\"$category[0]\"><INPUT TYPE=hidden NAME=item_stat_id VALUE=\"$line[0]\">";
        		echo '<INPUT TYPE=SUBMIT NAME=submit VALUE=Remove>';
        		echo '</FORM></TD></TR>';
        	} else {
        		echo '<TR>';
        		echo "<TD>$line[0]</TD>";
        		echo "<TD>$line[1]</TD>";
        		echo "<TD>$line[2]</TD>";
        		echo "<TD>$line[3]</TD>";
        		echo "<TD>$line[4]</TD>";
        		echo "<TD>$line[5]</TD>";
        		echo '</TR>';

        	}
	   }

        if ($editmode) {
          // add new item row
          echo '<TR>';
          echo '<FORM ACTION=index.php?page=lootcategories_actions&operation=add METHOD=POST>';
          echo "<TD><INPUT TYPE=hidden NAME=cat_id VALUE=\"$category[0]\"></TD>";
          echo '<TD>';
          SelectBaseItem(-1,item_stat_id);
          echo '</TD>';
          echo '<TD><INPUT TYPE=text size=5 name=probability></TD>';
          echo '<TD><INPUT TYPE=text size=5 name=min_money></TD>';
          echo '<TD><INPUT TYPE=text size=5 name=max_money></TD>';
          echo '<TD><INPUT TYPE=text size=5 name=randomize></TD>';
          echo '<TD><INPUT TYPE=SUBMIT NAME=submit VALUE=Add>';
          echo '</FORM></TD></TR>';
        }
        echo '</TABLE>';

        // npc table
        echo '<TD>';
        
        echo '<TABLE>';
        $npc_query = "select c.id,c.name,s.name from characters c, sectors s where c.loc_sector_id=s.id and npc_addl_loot_category_id=\"$category[0]\"";
        $npc_result = mysql_query2($npc_query);
        while ($npc = mysql_fetch_array($npc_result, MYSQL_NUM)) {
            echo "<TR><TD>";
            echo "$npc[1] ($npc[0] - $npc[2])";
	    if ($editmode){
              echo '<FORM ACTION=index.php?page=lootcategories_actions&operation=remove_npc METHOD=POST>';
              echo "<INPUT TYPE=hidden NAME=cat_id VALUE=\"$category[0]\">";
              echo "<INPUT TYPE=hidden NAME=npc_id VALUE=\"$npc[0]\">";
              echo '</TD><TD><INPUT TYPE=SUBMIT NAME=submit VALUE=Remove>';
              echo "</FORM>";
	    }
            echo "</TD></TR>";
         }
        if ($editmode) {
          echo "<TR><TD>";
          echo '<FORM ACTION=index.php?page=lootcategories_actions&operation=add_npc METHOD=POST>';
          echo "<INPUT TYPE=hidden NAME=cat_id VALUE=\"$category[0]\">";
          SelectNPCs(-1,npc_id,"vulnerable");
          echo '</TD><TD><INPUT TYPE=SUBMIT NAME=submit VALUE=Add>';
          echo "</FORM>";
          echo "</TD></TR>";
	}
        echo '</TABLE>';
        echo '</TD>';
        echo '</TR>';
        echo '<TR><TD colspan=3 bgcolor=darkblue></TD><TR>';
      } // end while for each loot rule

    echo '</TABLE>';

    // add new full loot rule
    echo '<BR><H2>Create a new loot rule</H2>';
    echo '<FORM ACTION=index.php?page=lootcategories_actions&operation=create METHOD=POST>';
    echo '<TABLE border=1>';
    echo '  <TR><TH> Name </TH>';
    echo '<TD><INPUT TYPE=text name=cat_id size=15></TD>';
    echo '<TD><INPUT TYPE=SUBMIT NAME=submit VALUE=create></TD></TR>';
    echo '</FORM>';
    echo '</TABLE></TD>';

	echo '<TD></TD>';

	echo '</TR></TABLE>';

	echo '<br><br>';
}

?>
  
