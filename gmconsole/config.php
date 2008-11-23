<?php

    //
    // Settings to connect to the database
    //
	$__DB_HOST = "localhost";
	$__DB_PORT = 3308;
	$__DB_USER = "planeshift";
	$__DB_PASS = "planeshift";
	$__DB_NAME = "planeshift";

    //
    // CSS related stuff
    //
    $__CSS_RELURI = "./mainpage.css";
    $__CSS_ADDON_RELURI = "./gm.css";

    //
    // Report log related settings
    //
    //  LAANX server log dir
    //$__REPORT_LOG_SUBDIR = "../../psserver/planeshift/logs";
    //$__REPORT_LOG_DELETED_SUBDIR = "../../psserver/planeshift/logs/old";

    $__REPORT_LOG_SUBDIR = "logs";
    $__REPORT_LOG_DELETED_SUBDIR = "logs/old";


    // sessions path
    $path_session = 'sessions';
    session_save_path($path_session);
	
?>