<?php


class PSBaseClass {

    //
    // Member-Variables
    //
    var $__IsLoaded;
	var $conn;

    function __construct() {
    }

    static function S_GetConnection()
    {
        require('config.php');
		$mysqli;
		$mysqli = new mysqli($__DB_HOST, $__DB_USER, $__DB_PASS, $__DB_NAME);
		
		/* check connection */
		if ($mysqli->connect_errno) //error code 0 means success, causing us to fail this if.
		{
			printf("Connect failed: %s\n", $mysqli->connect_error);
			exit();
		}
		return $mysqli;
		
		/*require('config.php');
				
		//if ($conn==null) {
	        $conn = mysqli_connect($__DB_HOST, $__DB_USER, $__DB_PASS);
	        mysqli_select_db($__DB_NAME);
			//echo "Opened a db connection.";
		//}

        return $conn;*/
    }

    static function S_AppendWhereCondition(&$where, $param, $op, $value) {
        if (isset($value)) {
            if ($where == '') {
                $where .= ' WHERE ';
            } else {
                $where .= ' AND ';
            }

            $where .= $param . ' ' . $op . ' ';
            $value = str_replace("'", "''", $value); // prevent sql-injection

            if (!strcasecmp($op, 'LIKE')) {
                $where .= '\'%' . str_replace('?', '_', str_replace('*', '%', $value)) . '%\'';
            } else {
                if (!is_numeric($value)) {
                    $where .= '\'' . $value . '\'';
                } else {
                    $where .= $value;
                }
            }
        }
    }

}
?>