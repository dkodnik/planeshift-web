<?php

require_once('PSBaseClass.php');
require_once('PSCharacter.php');

class PSAccount extends PSBaseClass {

    // Member-Variables
    var $ID;
    var $UserName;
    var $LastLogin;
    var $CreatedDate;
    var $LastLoginIP;
    var $LastLoginHostName;
    var $SecurityLevel;
    var $Country;
    var $SpamPoints;
    var $AdvisorPoints;


    //
    // Constructor
    //
    function PSAccount($pID = 0) {
        if ($pID > 0) {
            $this->ID = $pID;
            $this->Load();
        }
    }


    //
    // Functions
    //
    function Load() {
        if (!$this->ID) {
            die('Cannot load an account if the object trying to load it has an uninitialized property "ID"');
        }

        $conn = PSBaseClass::S_GetConnection();

        $sql = 'SELECT * FROM accounts';
        $where = '';

        PSBaseClass::S_AppendWhereCondition($where, 'id', '=', $this->ID);

        $res = mysql_query($sql . $where, $conn);
        if (!$res) {
            die($sql . $where . '<br>' . mysql_error());
        } else {
            // since it's the ID, there's only one character
            $row = mysql_fetch_array($res);
            
            $this->UserName = $row['username'];
            $this->LastLogin = $row['last_login'];
            $this->CreatedDate = $row['created_date'];
            $this->LastLoginIP = $row['last_login_ip'];
            $this->LastLoginHostName = $row['last_login_hostname'];
            $this->SecurityLevel = $row['security_level'];
            $this->Country = $row['country'];
            $this->SpamPoints = $row['spam_points'];
            $this->AdvisorPoints = $row['advisor_points'];

            // Load completed successfully
            $this->__IsLoaded = true;
        }
    }


    //
    // Returns the status of the this account as human-readable string
    //
    function GetStatus() {
        if (!$this->__IsLoaded) {
            die('Cannot tell the status of a character uninitialized character-object');
        }

        $status = "Player";

        if ($this->SecurityLevel >= 21 && $this->SecurityLevel <= 27) {
            $status = "GM" . ($this->SecurityLevel - 20);
        } else if ($this->SecurityLevel >= 30 && $this->SecurityLevel <= 50) {
            $status = "Developer";
        } else if ($this->SecurityLevel == 99) {
            $status = "NPCClient";
        }

        return $status;
    }


    //
    // Returns an array of PSCharacter-objects that represent the characters registered under this account
    // The array is empty when the account contains no characters.
    //
    function GetCharacters() {
        if (!$this->__IsLoaded) {
            die('Cannot load the characters of an uninitialized account-object');
        }
        
        return PSCharacter::S_GetCharactersOfAccount($this->ID);
    }


    //
    // Searches for an account by user name and password, used only at login from main page
    //
    function S_FindOne($userName, $password) {
        $conn = PSBaseClass::S_GetConnection();

        $sql = 'SELECT * FROM accounts';
        $where = '';
		PSBaseClass::S_AppendWhereCondition($where, 'username', '=', $userName);
		PSBaseClass::S_AppendWhereCondition($where, 'password', '=', md5($password));

        $res = mysql_query($sql . $where, $conn);
        if (!$res) {
            die($sql . $where . "<br>" . mysql_error());
        } else {
            $row = mysql_fetch_array($res);
            if (!$row) {
                // Authentication failed!
                return null;
            }

            $account = new PSAccount();

            $account->ID = $row['id'];
            $account->UserName = $row['username'];
            $account->LastLogin = $row['last_login'];
            $account->CreatedDate = $row['created_date'];
            $account->LastLoginIP = $row['last_login_ip'];
            $account->LastLoginHostName = $row['last_login_hostname'];
            $account->SecurityLevel = $row['security_level'];
            $account->Country = $row['country'];
            $account->SpamPoints = $row['spam_points'];
            $account->AdvisorPoints = $row['advisor_points'];

            $account->__IsLoaded = true;
            return $account;
        }
    }


    //
    // Searches for all accounts that match the given the criteria
    //
    function S_Find($email, $lastIP) {
        $conn = PSBaseClass::S_GetConnection();

        $sql = 'SELECT * FROM accounts';
        $where = '';
		if ($email!=null && $email!="")
			PSBaseClass::S_AppendWhereCondition($where, 'username', '=', $email);
		if ($lastIP!=null && $lastIP!="")
			PSBaseClass::S_AppendWhereCondition($where, 'last_login_ip', '=', $lastIP);

		//echo "before query ".$sql . $where . ' ORDER BY username ASC LIMIT 100';
		//echo time();
        $res = mysql_query($sql . $where . ' ORDER BY username ASC LIMIT 100', $conn);
		//echo "after query";
		//echo time();
        if (!$res) {
            die($sql . $where . "<br>" . mysql_error());
        } else {
            $accounts = array();

            while (($row = mysql_fetch_array($res)) != null) {
                $account = new PSAccount();

                $account->ID = $row['id'];
                $account->UserName = $row['username'];
                $account->LastLogin = $row['last_login'];
                $account->CreatedDate = $row['created_date'];
                $account->LastLoginIP = $row['last_login_ip'];
                $account->LastLoginHostName = $row['last_login_hostname'];
                $account->SecurityLevel = $row['security_level'];
                $account->Country = $row['country'];
                $account->SpamPoints = $row['spam_points'];
                $account->AdvisorPoints = $row['advisor_points'];

                $account->__IsLoaded = true;
                array_push($accounts, $account);
            }

            return $accounts;
        }
    }
    

    //
    // Returns an array of character objects that are GMs, ordered by security leven and name in ascending order.
    // This function can be called statically.
    //
    function S_GetGMs() {
        $conn = PSBaseClass::S_GetConnection();

        $sql = 'SELECT * FROM accounts';
        $where = '';
        PSBaseClass::S_AppendWhereCondition($where, 'security_level', '>=', 1);

        $res = mysql_query($sql . $where . ' ORDER BY security_level DESC, username ASC', $conn);
        if (!$res) {
            die($sql . $where . '<br>' . mysql_error());
        } else {
            $gmAccounts = array();

            while (($row = mysql_fetch_array($res)) != null) {            
                $gmAccount = new PSAccount();

                $gmAccount->ID = $row['id'];
                $gmAccount->UserName = $row['username'];
                $gmAccount->LastLogin = $row['last_login'];
                $gmAccount->CreatedDate = $row['created_date'];
                $gmAccount->LastLoginIP = $row['last_login_ip'];
                $gmAccount->LastLoginHostName = $row['last_login_hostname'];
                $gmAccount->SecurityLevel = $row['security_level'];
                $gmAccount->Country = $row['country'];
                $gmAccount->SpamPoints = $row['spam_points'];
                $gmAccount->AdvisorPoints = $row['advisor_points'];

                $gmAccount->__IsLoaded = true;
                array_push($gmAccounts, $gmAccount);
            }

            return $gmAccounts;
        }
    }
}
?>