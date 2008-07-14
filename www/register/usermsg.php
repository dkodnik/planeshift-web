<?php

if (!defined('psregister')) die('You are not allowed to run this script directly.');

function UserMsg($msg="")
{
  include_once("start.php");
  echo "
    <div id=\"content\">
      $msg
    </div>
  ";
  include_once("end.php");
}

?>
