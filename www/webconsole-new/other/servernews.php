<?php

function serverNews()
{
    if (!checkaccess('other', 'edit'))
    {
        echo '<p class="error">You are not authorized to view Tribe details</p>';
        return;
    }
    if (isset($_POST['submit']) && $_POST['submit'] == 'Update News')
    {
        $news = escapeSqlString($_POST['news']);
        $query = "UPDATE wc_servernews SET news='$news' WHERE id='1'";
        mysql_query2($query);
        echo '<p class="error">News Updated.</p>';
    }
    $sql = "SELECT * FROM wc_servernews WHERE id='1'";
    $row = fetchSqlAssoc(mysql_query2($sql));
    echo '<form action="./index.php?do=servernews" method="post">'."\n";
    echo '<div><textarea name="news" cols="50" rows="30">'.htmlentities($row['news']).'</textarea><br/><input type="submit" name="submit" value="Update News" /></div>'."\n";
    echo '</form>'."\n";
}

?>