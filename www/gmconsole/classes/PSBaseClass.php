<?php


class PSBaseClass {

    //
    // Member-Variables
    //
    var $__IsLoaded;
	var $conn;

    function PSBaseClass() {
    }

    function S_GetConnection()
    {
        require('config.php');
		//if ($conn==null) {
	        $conn = mysql_connect($__DB_HOST, $__DB_USER, $__DB_PASS);
	        mysql_select_db($__DB_NAME);
			//echo "Opened a db connection.";
		//}

        return $conn;
    }

    function S_AppendWhereCondition(&$where, $param, $op, $value) {
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