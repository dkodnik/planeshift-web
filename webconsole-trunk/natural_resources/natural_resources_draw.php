<?
function natural_resources_draw(){

    include('util.php');
    checkAccess('listnpc', '', 'read');

    header("Content-type: image/png");

    $sector = $_GET['sector'];

    draw_map($sector);
}
?>
