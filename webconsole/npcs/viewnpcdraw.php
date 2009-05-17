<?
function viewnpcdraw(){

    include('util.php');
    checkAccess('listnpc', '', 'read');

    header("Content-type: image/png");

    $sector = $_GET['sector'];
    $live = $_GET['live'];

    draw_map($sector,$live);
}
?>
