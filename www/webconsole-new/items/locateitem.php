<?php
function locateitem()
{
    if (checkaccess('items', 'read'))
    {
        $display_item = false;
        if (isset($_POST['search']))
        {
            $query = 'SELECT i.id, s.name, i.char_id_owner, c_owner.name AS owner_name, i.char_id_guardian, c_guardian.name AS guardian_name, i.parent_item_id, i.location_in_parent, i.stack_count, sec.name as sector, i.loc_x, i.loc_y, i.loc_z, i.loc_xrot, i.loc_zrot, i.loc_instance, i.flags, lrp.name AS prefix, lrs.name AS suffix, lra.name AS adjective FROM item_instances AS i LEFT JOIN loot_modifiers AS lrp ON i.prefix=lrp.id LEFT JOIN loot_modifiers AS lrs ON i.suffix=lrs.id LEFT JOIN loot_modifiers AS lra ON i.adjective=lra.id LEFT JOIN sectors AS sec ON i.loc_sector_id=sec.id LEFT JOIN item_stats AS s ON i.item_stats_id_standard=s.id LEFT JOIN characters AS c_owner ON i.char_id_owner=c_owner.id LEFT JOIN characters AS c_guardian ON i.char_id_guardian=c_guardian.id WHERE ';
            if ($_POST['search'] == "Find Items")  // these first 3 all give the same results with another "where", so we print them all at the end in the same code.
            {
                echo 'Finding item';
                $itemstat = escapeSqlString($_POST['itemid']);
                $query .= "item_stats_id_standard = $itemstat";
                if (isset($_POST['private'])) // The brackets in the SQL are intentional, and required for it's proper working.  The sector names for guild houses are static/fixed for now (juli, 2009), but this may change in the future.
                {
                    $query .= " AND (isnull(sec.name) OR i.loc_sector_id = '0' OR (sec.name != 'guildsimple' AND sec.name != 'guildlaw' AND sec.name != 'NPCroom'))";
                }
                $display_item = true;
            }
            else if ($_POST['search'] == "Dropped Items")
            {
                echo 'Dropped Items';
                $query .= "i.char_id_owner = 0";
                if ($_POST['sectorid'] != '')
                {
                  $sectorid = escapeSqlString($_POST['sectorid']);
                  $query = $query . " AND i.loc_sector_id='$sectorid'";
                }
                if (isset($_POST['private']))
                {
                    $query .= " AND sec.name != 'guildsimple' AND sec.name != 'guildlaw' AND sec.name != 'NPCroom'";
                }
                $display_item = true;
            }
            else if ($_POST['search'] == "Find Instance")
            {
                echo 'Locate Instance';
                $iid = escapeSqlString($_POST['iid']);
                $query .= "i.id='$iid'"; // "private" is not relevant when finding a specific instance.
                $display_item = true;
            }
            else if ($_POST['search'] == "Find Merchants") // This one is different, so it gets it's own print.
            {
                echo 'Find Merchant';
                $itemid = escapeSqlString($_POST['vendoritemid']); // Don't make "iss" "is" (like it should logically be) since "is" is a reserved keyword in mysql.
                $query = "SELECT DISTINCT c.id, c.name, c.lastname, iss.name AS item_name FROM merchant_item_categories AS m LEFT JOIN characters AS c ON c.id=m.player_id LEFT JOIN item_instances AS i ON i.char_id_owner=m.player_id LEFT JOIN item_stats AS iss ON iss.id=i.item_stats_id_standard WHERE i.location_in_parent > '15' AND i.item_stats_id_standard='$itemid' AND iss.category_id=m.category_id ORDER BY iss.name";
                $result = mysql_query2($query);
                if (sqlNumRows($result) == 0)
                {
                    echo '<p class="error">No vendors found for this item.</p>';
                    return;
                }
                $row = fetchSqlAssoc($result);
                echo '<p class="bold">Displaying vendors for '.$row['item_name'].'  </p>';
                echo '<table>';
                do {
                    echo '<tr><td><a href="./index.php?do=npc_details&sub=main&npc_id='.$row['id'].'">'.$row['name'].' '.$row['lastname'].'</a></td></tr>';
                } while ($row = fetchSqlAssoc($result));
                echo '</table>';
            }
        }
        else  // if there was no search, print the form
        {
            echo '<input type="checkbox" name="private_master" id="private" onchange="privateChanged()" /> Exclude private zones (guild houses/NPC room) from the search?';
            echo '<div class="table_no_border">';
            echo '<form action="./index.php?do=finditem" method="post" class="tr"><div class="td_no_border">Find all instances of item: </div><div class="td_no_border">';
            echo DrawItemSelectBox('itemid', false, false). '<input type="checkbox" name="private" style="display:none;" /></div><div class="td_no_border"><input type="submit" name="search" value="Find Items"/></div></form>';
            echo '<form action="./index.php?do=finditem" method="post" class="tr"><div class="td_no_border">Locate Instance ID: </div><div class="td_no_border"><input type="text" name="iid" />';
            echo '</div><div class="td_no_border"><input type="submit" name="search" value="Find Instance" /></div></form>';
            $Sectors = PrepSelect('sectorid');
            echo '<form action="./index.php?do=finditem" method="post" class="tr"><div class="td_no_border">Locate All Items on floor (Limit to Sector: </div>';
            echo '<div class="td_no_border">'.DrawSelectBox('sectorid', $Sectors, 'sectorid', '', true).') <input type="checkbox" name="private" style="display:none;" /></div><div class="td_no_border"><input type="submit" name="search" value="Dropped Items" /></div></form>';
            echo '<form action="./index.php?do=finditem" method="post" class="tr"><div class="td_no_border">Find all vendors of item: </div>';
            echo '<div class="td_no_border">'.DrawItemSelectBox('vendoritemid', false, true). '</div><div class="td_no_border"><input type="submit" name="search" value="Find Merchants"/></div></form>';
            echo '</div>';
            echo '<script type="text/javascript">//<![CDATA[
    function privateChanged()
    {
        if (document.getElementById("private").checked)
        {
            document.getElementsByName("private")[0].checked = true;
            document.getElementsByName("private")[1].checked = true;
        }
        else
        {
            document.getElementsByName("private")[0].checked = false;
            document.getElementsByName("private")[1].checked = false;
        }
    }
//]]></script>';
        }
        if ($display_item)  // if there was an item search, print it here.
        {
            $result = mysql_query2($query);
            echo '<table border="1"><tr><th>Instance ID</th><th>Name</th><th>Owner ID</th><th>Guardian ID</th><th>Parent Item</th><th>Location in Parent</th><th>Stack Count</th><th>Sector</th><th>X</th><th>Y</th><th>Z</th><th>X rot</th><th>Z rot</th><th>Instance</th><th>Flags</th><th>Prefix</th><th>Suffix</th><th>Adjective</th></tr>'."\n";
            while ($row = fetchSqlAssoc($result)){
                echo '<tr><td>'.$row['id'].'</td>';
                echo '<td>'.$row['name'].'</td>';
                if ($row['char_id_owner'] == 0) 
                {
                    echo '<td>'.$row['char_id_owner'].'</td>';
                }
                else
                {
                    echo '<td><a href="./index.php?do=npc_details&amp;sub=main&amp;npc_id='.$row['char_id_owner'].'">'.$row['owner_name'].'</a></td>';
                }
                if ($row['char_id_guardian'] == 0) 
                {
                    echo '<td>'.$row['char_id_guardian'].'</td>';
                }
                else
                {
                    echo '<td><a href="./index.php?do=npc_details&amp;sub=main&amp;npc_id='.$row['char_id_guardian'].'">'.$row['guardian_name'].'</a></td>';
                }
                echo '<td>'.$row['parent_item_id'].'</td>';
                echo '<td>'.LocationToString($row['location_in_parent']).'</td>';
                echo '<td>'.$row['stack_count'].'</td>';
                echo '<td>'.$row['sector'].'</td>';
                echo '<td>'.$row['loc_x'].'</td><td>'.$row['loc_y'].'</td><td>'.$row['loc_z'].'</td>';
                echo '<td>'.$row['loc_xrot'].'</td><td>'.$row['loc_zrot'].'</td>';
                echo '<td>'.$row['loc_instance'].'</td>';
                echo '<td>'.$row['flags'].'</td>';
                echo '<td>'.$row['prefix'].'</td>';
                echo '<td>'.$row['suffix'].'</td>';
                echo '<td>'.$row['adjective'].'</td></tr>'."\n";
            }
            echo '</table>';
        }
    }
    else
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
    }
}
?>
