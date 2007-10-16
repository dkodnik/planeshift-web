<?
function itemcategory_actions()
{
?>
<HTML>
<BODY>


<?PHP


// gets operation to perform
$operation = $_GET['operation'];

/**
 * delete a script
 */
if ($operation == 'delete')
{
    $category_id = $_POST['category_id']; 
    // delete named script
    $query = "delete from item_categories where category_id='$category_id'";
    $result = mysql_query2($query);

    /**
     * create script
     */
}
else if ($operation == 'create')
{
    $name = $_POST['name']; 
    // insert script
    $query = "insert into item_categories values (NULL,'$name')";
    $result = mysql_query2($query);

    /**
     * update script
     */
}
else if ($operation == 'update')
{
    $category_id = $_POST['category_id'];
    $name = $_POST['name']; 
    // insert script
    $query = "update item_categories set name='$name' where category_id='$category_id'";
    $result = mysql_query2($query);
}
else
{ 
    // manage another operation here
    echo "Operation $operation not supported.";
} 
// redirect
?>
<SCRIPT language="javascript">
        document.location = "index.php?page=listitemcategories";
</script>
<?
}
?>