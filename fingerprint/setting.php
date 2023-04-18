<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FingerPrint Registration</title>
    <script src="../stdio.js"></script>
</head>
<body>
<?php
    require_once "../stdio.php";
    var_dump($_GET);
    if (isset($_GET["st"]) && $_GET["st"]=="update") {
        if (!array_diff_key(array_flip(["st", "uid", "fp"]), $_GET)) {
            $r = function(){
                $db = new DBSrvs();
                $mysqli = $db->mysqli();
                $query = "INSERT INTO fingerprint VALUES( 0, ?, ?, ?)";
                if ($stmt  = $mysqli->prepare($query)) {
                    $stmt->bind_param('iss', $a, $b, $c);
                    $a = $_GET["uid"]; $b = $_GET["cmnt"]; $c = $_GET["fp"];
                    $stmt->execute();
                    return $mysqli->insert_id;
                }
                else {
                    return false;
                }
            };

            if ($r() !== false) {
                echo "<p>登録完了</p>";
            } else {
                echo "<p>失敗しました</p>";
            }
            echo "<p><a href='../index.html'>戻る</a></p>";
            $_GET = array();
        }
    }
    // else if (!array_diff_key(array_flip(["uid", "fp"]), $_GET)) {
    //     $ipt_uid = (string) htmlspecialchars($_GET["uid"], ENT_QUOTES);
    //     $ipt_fp = (string) htmlspecialchars($_GET["fp"], ENT_QUOTES);
    // }
?>
<form method="get" action="./setting.php" name="fp_set_form">
    <h2>更新内容</h2>
    <input type="hidden" name="st" value="update"></input>
    <label>ユーザID<input placeholder="ユーザID" type="text" name="uid"></input><label>
    <label>ブラウザ識別キー<input placeholder="ブラウザ識別キー" type="text" name="fp"></input><label>
    <label>名称・コメント<input placeholder="名称・コメント" type="text" name="cmnt"></input><label>
    <input type="submit" value="登録">
</form>
<script>
    document.forms["fp_set_form"].uid.value = getprm("uid");
    document.forms["fp_set_form"].fp.value = getprm("fp");
</script>
</body>
</html>