<?php
    // Import
    require_once "../stdio.php";

    // 
    // 

    $post = $_POST;
    $rd = new RegData($post);
    if (strpos($rd->const_result, "Ok")===false) {
        echo "<strong>Some error has occurred!!<strong>";
        echo $rd->const_result;
        exit();
    }
    $db = new DBSrvs();
    $mysqli = $db->mysqli();
    if (!$r1 = $db->insert_record($rd->user_id, $rd->date, "PAY", $rd->v_pay, $rd->paymethod_number, $rd->cmnt, $rd->check_type)) {
        sendurl("../regisration.php?st=err");
    } else if (!$r2 = $db->insert_record($rd->user_id, $rd->date, "GET", $rd->v_get, $rd->paymethod_number, $rd->cmnt, $rd->check_type)) {
        sendurl("../regisration.php?st=err");
    }

    //
    //

    class RegData {
        public $date, $paymethod_number, $v_pay, $v_get, $cmnt, $check_type, $user_id, $const_result;
        private $datatype;
    
        function __construct($post) {
            $this->datatype = $post["datatype"];
            if ($this->datatype != "registration") { $this->const_result = "Err, inappropriate datatype"; return; }
            $this->date = (function () use ($post) {
                if (array_key_exists("date_today", $post)) {
                    return date("Y-m-d H:i:s");
                } else if (array_key_exists("date_yest", $post)) {
                    return date("Y-m-d H:i:s", strtotime("yesterday"));
                } else if (array_key_exists("date_manual", $post)) {
                    return "{$post["date"]} 00:00:00";
                }
                return "";
            })();
            if ($this->date=="") { $this->const_result = "Err, date set"; return; }
            $this->paymethod_number = (int) $post["paymethod_number"];
            $this->v_pay = (int) $post["v_pay"];
            $this->v_get = (int) $post["v_get"];
            $this->cmnt = $post["cmnt"];
            $this->check_type = $post["check_type"];
            $this->user_id = (int) $post["user_id"];
            $this->const_result = "Ok"; return;
        }
    }

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>金銭管理 | LINK with LINE</title>
    <script>
        window.onload = function(){
            document.forms["LinkLineMsg"].submit();
        }
    </script>
</head>
<body>
    <form method="post" action="https://script.google.com/macros/s/AKfycbzAmykdo_BaAaDpWwkJ624bY_A6QdfUfhRupyPgSMSwm00dXJr1ojBy_eY55MnpbYc/exec" name="LinkLineMsg">
        <input type="hidden" name="datatype" value="registration">
        <input type="hidden" name="user_id" value="<?php echo $rd->user_id ?>">
        <input type="hidden" name="line_user_id" value="<?php
            $usrinfo = $db->get_userinfo($rd->user_id);
            echo $usrinfo[0]["line_user_id"];
        ?>">
        <input type="hidden" name="date" value="<?php echo $rd->date ?>">
        <input type="hidden" name="v_pay" value="<?php echo $rd->v_pay ?>">
        <input type="hidden" name="v_get" value="<?php echo $rd->v_get ?>">
        <input type="hidden" name="payway" value="<?php
            $paymethod = $db->get_paymethod($rd->user_id);
            foreach ($paymethod as $key => $m) {
                if ($m["paymethod_number"]==$rd->paymethod_number) { echo $m["subname"]; break; }
            }
        ?>">
        <input type="hidden" name="check_type" value="<?php echo $rd->check_type ?>">
        <input type="hidden" name="cmnt" value="<?php echo $rd->cmnt ?>">
        <input type="hidden" name="record_id1" value="<?php echo $r1 ?>">
        <input type="hidden" name="record_id2" value="<?php echo $r2 ?>">
        <input type="submit" value="LINE通知">
    </form>
</body>
</html>

<?php

$db->close();

?>