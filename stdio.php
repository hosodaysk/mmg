<?php
//
$SrvRoot = "http://sharkbrown.php.xdomain.jp/";

// DB Setting
class DBSrvs {
    public $host, $user, $userpwd, $dbname;
    private $mysqli;

    function __construct(){
        $this->host = "mysql1.php.xdomain.ne.jp";
        $this->user = "sharkbrown_xxxxxx";
        $this->userpwd = "xxxxxxx";
        $this->dbname = "sharkbrown_mnymnger";
        $this->mysqli = null;
    }

    function mysqli(){
        $this->mysqli = new mysqli($this->host, $this->user, $this->userpwd, $this->dbname);
        if (mysqli_connect_error()) { die("データベースの接続に失敗しました"); }
        return $this->mysqli;
    }

    function close($mysqli_obj = null){
        if ($mysqli_obj==null) { $mysqli_obj = $this->mysqli; }
        $mysqli_obj->close();
    }

    function insert_record($usr_id, $datetime, $pay_type, $value, $paymethod_number, $cmnt, $check_type, $mysqli_obj = null){
        if ($mysqli_obj==null) { $mysqli_obj = $this->mysqli; }
        $query = "INSERT INTO record VALUES( 0, ?, ?, ?, ?, ?, ?, ?)";
        if ($stmt  = $mysqli_obj->prepare($query)) {
            $stmt->bind_param('issiiss', $a, $b, $c, $d, $e, $f, $g);
            $a = $usr_id; $b = $datetime; $c = $pay_type; $d = $value; $e = $paymethod_number; $f = $cmnt; $g = $check_type;
            $stmt->execute();
            return $mysqli_obj->insert_id;
        } else {
            return false;
        }
    }

    function get_paymethod($usr_id, $mysqli_obj = null){
        if ($mysqli_obj==null) { $mysqli_obj = $this->mysqli; }
        $query = "SELECT * FROM paymethod WHERE user_id = ? ORDER BY paymethod_number ASC";
        $stmt  = $mysqli_obj->prepare($query);
        $stmt->bind_param('i', $a);
        $a = $usr_id;
        $stmt->execute();
        $stmt->bind_result($usr_id, $paymethod_number, $name, $subname, $charge_date);
        $result = array();
        while ($stmt->fetch()) {
            $r = array(
                "user_id"=> $usr_id,
                "paymethod_number"=> $paymethod_number,
                "name"=> $name,
                "subname"=> $subname,
                "charge_date" => $charge_date
            );
            array_push($result, $r);
        }
        return $result;
    }

    function get_userinfo($id = "", $mysqli_obj = null){ // $id -> 指定 "数値" でuser_id, 指定 "G+数値" でuser_group 検索
        if ($mysqli_obj==null) { $mysqli_obj = $this->mysqli; }
        $query = (function() use ($id){
            if (gettype($id)=="string") {
                if (substr($id, 0, 1)=="G") { return "SELECT * FROM userlist WHERE user_group = ?"; }
                else { return "Err, invalid argment"; }
            } else if (gettype($id)=="integer") {
                return "SELECT * FROM userlist WHERE user_id = ?";
            }
        })();
        if ($stmt  = $mysqli_obj->prepare($query)) {
            $stmt->bind_param('i', $a);
            $a = (gettype($id)=="string") ? (int) preg_replace('/[^0-9]/', '', $id) : $id;
            $stmt->execute();
            $stmt->bind_result($usr_id, $ln_usr_id, $name, $usr_group, $usr_type);
            $result = array();
            while ($stmt->fetch()) {
                $r = array(
                    "user_id"=> $usr_id,
                    "line_user_id"=> $ln_usr_id,
                    "name"=> $name,
                    "user_group"=> $usr_group,
                    "user_type"=> $usr_type
                );
                array_push($result, $r);
            }
            return $result;
        } else {
            return false;
        }
        
    }
}


//
//
//
//

function predump($c) {
    echo "<pre>";
    var_dump($c);
    echo "</pre>";
}

function sendurl($document, $srv_root = "default") {
    if ($srv_root == "default") { $srv_root = $SrvRoot; }
    header("Location: {$srv_root}{$document}");
}