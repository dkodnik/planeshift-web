<?php
function compareitems(){
    if(!checkaccess('items', 'read'))
    {
        echo '<p class="error">You are not authorized to use these functions!</p>';
        return;
    }
    $category_id = (isset($_GET['category_id']) ? $_GET['category_id'] : '');
    $sort_col = (isset($_GET['sort_col']) ? $_GET['sort_col'] : 'name');
    $sort_dir = (isset($_GET['sort_dir']) ? $_GET['sort_dir'] : 'asc');
    
    $makeSortUrl = function($colName) use (&$sort_col, &$sort_dir, &$category_id, &$compare_type)
    {
        // we use htmlentities on the whole string, so not using &amp; here.
        $base = 'index.php?do=compareitems&category_id='.$category_id;
        if ($colName == $sort_col && $sort_dir == 'asc')
        {
            return htmlentities($base.'&sort_col='.$colName.'&sort_dir=desc');
        }// basically, else.
        return htmlentities($base.'&sort_col='.$colName.'&sort_dir=asc');
    };
    

    $categories = PrepSelect('category');
    echo '<form method="get" action="index.php">'."\n";
    echo '<div><input type="hidden" name="do" value="compareitems" />'."\n";
    echo DrawSelectBox('category', $categories, 'category_id', $category_id);
    echo '<input type="hidden" name="sort_col" value="'.htmlentities($sort_col).'" />'."\n";
    echo '<input type="hidden" name="sort_dir" value="'.htmlentities($sort_dir).'" />'."\n";
    echo '<input type="submit" name="View" value="View" /></div></form>'."\n";
    
    if ($category_id == '')
    {   // no category_id, we're done.
        return;
    }
    
    $category_id = escapeSqlString($category_id);
    $sort_col = escapeSqlString($sort_col);
    $sort_dir = escapeSqlString($sort_dir);
    
    $query = "SELECT i.id, i.name, i.size, i.weight, i.base_sale_price, CONCAT(i.container_max_size, '-', i.container_max_slots) AS container";
    $query .= ", i.item_max_quality, i.valid_slots, i.flags, i.description, i.spawnable, CONCAT_WS(', ', s1.name,s2.name,s3.name) AS item_skills";
    $query .= ", i.consume_script, i.equip_script, CONCAT(i.item_bonus_1_attr, '=', i.item_bonus_1_max) AS item_bonus_1";
    $query .= ", CONCAT(i.item_bonus_2_attr, '=', i.item_bonus_2_max) AS item_bonus_2, CONCAT(i.item_bonus_3_attr, '=', i.item_bonus_3_max) AS item_bonus_3";
    $query .= ", CONCAT(i.requirement_1_name,'=',i.requirement_1_value) AS requirement1, CONCAT(i.requirement_2_name, '=',i.requirement_2_value) AS requirement2";
    $query .= ", CONCAT(i.requirement_3_name,'=',i.requirement_3_value) AS requirement3, i.cstr_gfx_icon, i.cstr_gfx_mesh, i.cstr_gfx_texture";
    $query .= ", i.cstr_part, i.cstr_part_mesh, i.removed_mesh, i.armor_hardness, i.armorvsweapon_type, i.weapon_block_targeted, i.weapon_block_untargeted";
    $query .= ", i.weapon_counterblock, i.dmg_blunt, i.dmg_pierce, i.dmg_slash, i2.name as ammo, i.weapon_penetration, i.weapon_range, i.weapon_speed";
    $query .= " FROM item_stats i LEFT JOIN skills AS s1 ON i.item_skill_id_1=s1.skill_id LEFT JOIN skills AS s2 ON i.item_skill_id_2=s2.skill_id";
    $query .= " LEFT JOIN skills AS s3 ON i.item_skill_id_3=s3.skill_id LEFT JOIN item_stats AS i2 ON i.item_type_id_ammo=i2.id";
    $query .= " WHERE i.category_id='$category_id' ORDER BY $sort_col $sort_dir, name";
    
    $result = mysql_query2($query);
    if (sqlNumRows($result) == 0)
    {
        echo '<p class="error">There were no items in this category.</p>';
        return;
    }
    
    echo '<div id="scrollingtablediv" class="scrollingtablediv">'."\n";
    echo '<table id="scrollingtable">'."\n";
    echo '<thead class="scrollingthead">'."\n";
    
    $alt = false;
    $count = 0;
    echo '<tr class="color_'.(($alt = !$alt) ? 'a' : 'b').'">';
    foreach (fetchSqlAssoc($result) as $colName => $colValue)
    {
        // first 2 are "frozen" columns
        $thClass = '';
        if ($count == 0)
        {
            $thClass = ' class="firstfrozencol color_'.($alt ? 'a' : 'b').'"';
        }
        else if ($count == 1)
        {
            $thClass = ' class="secondfrozencol color_'.($alt ? 'a' : 'b').'"';
        }
        echo '<th'.$thClass.'><a href="'.$makeSortUrl($colName).'">'.htmlentities($colName).'</a></th>';
        $count++;
    }
    echo '</tr>'."\n";
    echo '</thead>'."\n";
    echo '<tbody class="scrollingtbody">'."\n";

    // reset the result after our header printing.
    sqlSeek($result, 0);
    while ($row = fetchSqlAssoc($result))
    {
        echo '<tr class="color_'.(($alt = !$alt) ? 'a' : 'b').'">';
        $count = 0;
        $itemId = 0;
        foreach ($row as $colValue)
        {
            $tdClass = '';
            // first 2 are "frozen" columns, also contain view item and edit item links. The other columns are generated.
            if ($count == 0)
            {
                $itemId = $colValue;
                echo '<td class="firstfrozencol color_'.($alt ? 'a' : 'b').'"><a href="index.php?do=listitems&amp;category='.$category_id.'&amp;item='.$itemId.'">'.htmlentities($colValue).'</a></td>';
            }
            else if ($count == 1)
            {
                echo '<td class="secondfrozencol color_'.($alt ? 'a' : 'b').'">'.htmlentities($colValue);
                if (checkaccess('items', 'edit'))
                {
                    echo '<a href="index.php?do=edititem&amp;item='.$itemId.'"> edit</a>';
                }
                echo '</td>';
            }
            else
            {
                echo '<td'.$tdClass.'>'.htmlentities($colValue).'</td>';
            }
            $count++;
        }
        echo '</tr>'."\n";
    }
    echo '</tbody>'."\n";
    echo '</table>'."\n";
    echo '</div>'."\n";
    
    // the following javascript ensures we have frozen columns, and makes all columns the proper width.
    echo '
        <script type="text/javascript">
        //<![CDATA[
        function myscroller() 
        {
            var left = document.getElementById("scrollingtablediv").scrollLeft;
            var firsts = document.getElementsByClassName("firstfrozencol");
            var seconds = document.getElementsByClassName("secondfrozencol");
            for (var i = 0; i < firsts.length; i++) 
            {
                first = firsts.item(i);
                first.style.left = left -(left > 6 ? 6 : 0) + "px"; 
                
                // only set a second frozen table if one is given.
                if (seconds.length != 0)
                { 
                    seconds.item(i).style.borderRight = "1px solid black";
                    // -2 to remove the "gap" between the 2 table elements. It is probably not technically correct, but since there is already an element 
                    // at that position, it moves just to the right of it.
                    seconds.item(i).style.left = parseFloat(first.style.left) - 2 + "px";
                }
                else
                {
                    first.style.borderRight = "1px solid black";
                }
            }
        }
        // add the above function to the table div as an event listener for scrolling (div scrolls horizontal, which is what we need).
        document.getElementById("scrollingtablediv").addEventListener("scroll", myscroller);
        
        // number of th tags and amount of columns are considered to be equal.
        var thElements = document.getElementsByClassName("scrollingthead").item(0).getElementsByTagName("tr").item(0).getElementsByTagName("th");
        var tdElements = document.getElementsByClassName("scrollingtbody").item(0).getElementsByTagName("tr").item(0).getElementsByTagName("td");
        var thIncrease = 0;
        var tdIncrease = 0;
        
        // number of th tags and amount of columns are considered to be equal.
        for (var i = 0; i < thElements.length; i++) 
        {
            var tdWidth = tdElements.item(i).getBoundingClientRect().width;
            var thWidth = thElements.item(i).getBoundingClientRect().width;
            var maxWidth = (thWidth < tdWidth ? tdWidth : thWidth);
            // keep track of which row we increased in size by how much
            thWidth < tdWidth ? thIncrease += tdWidth - thWidth : tdIncrease += thWidth - tdWidth;
            thElements.item(i).style.width = maxWidth + "px";
            tdElements.item(i).style.width = maxWidth + "px";
        } 
        
        var cs = window.getComputedStyle;
        // this script assumes matching left and right padding/margin/border width. In addition, it assumes equality between TH and TD tags in this respect.
        var tdi = tdElements.item(0);
        // we add + 1px to the table for each element. This is from unknown origin, but is required.
        var additionalWidth =  thElements.length * (parseFloat(cs(tdi).getPropertyValue("border-left-width")) * 2 + 
            parseFloat(cs(tdi).getPropertyValue("margin-left")) * 2 + parseFloat(cs(tdi).getPropertyValue("padding-left")) * 2 + 1);
        // table should be increased by the smallest of td and th increase, + additional width for padding/margin/etc + 1 for IE.
        var tableIncrease = (thIncrease < tdIncrease ? thIncrease : tdIncrease) + additionalWidth + 1; 
        
        var tableWidth = document.getElementById("scrollingtable").offsetWidth;
        // set table to the proper width to match the new element sizes.
        document.getElementById("scrollingtable").style.width = tableWidth + tableIncrease + "px";
        //]]>
        </script>'; // end of echo.
}
?>