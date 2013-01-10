<?php

require_once('PSBaseClass.php');

class PSBan extends PSBaseClass {

    //
    // Member variables
    //
    var $AccountID;
    var $AccountName;
    var $IPRange;
    var $DateStart;
    var $DateEnd;
    var $Reason;
    var $IPBan;

    //
    // Constructor
    //
    function PSBan() {
    }


    //
    // Functions
    //
    function S_GetBans() {
        $conn = PSBaseClass::S_GetConnection();

		// base query
        $sql = 'SELECT a.id, a.username, b.* FROM accounts a, bans b WHERE a.id = b.account';

        $res = mysql_query($sql, $conn);
        if (!$res) {
            die($sql . $where . "<br>" . mysql_error());
        } else {
            $bans = array();

            while (($row = mysql_fetch_array($res)) != null) {
                $ban = new PSBan();

                $ban->AccountID = $row['account'];
                $ban->AccountName = $row['username'];
                $ban->IPRange = $row['ip_range'];
                $ban->DateStart = $row['start'];
                $ban->DateEnd = $row['end'];
                $ban->Reason = $row['reason'];
                $ban->IPBan = $row['ban_ip'];

                $ban->__IsLoaded = true;
                array_push($bans, $ban);
            }

            return $bans;
        }
    }
}

?>