<?php
require_once "../stdio.php";
$ipt_fp = $_GET["fp"];
$ipt_uid = $_GET["uid"];

$db = new DBSrvs();
$mysqli = $db->mysqli();
$query = "SELECT fingerprint_id, fingerprint FROM fingerprint WHERE user_id = ? ORDER BY fingerprint_id DESC";
if ($stmt = $mysqli->prepare($query)) {
    $stmt->bind_param("i", $a);
    $a = (int) $ipt_uid;
    $stmt->execute();
    $stmt->bind_result($fp_id, $fp);
    while ($stmt->fetch()) {
        if ($fp == $ipt_fp) {
            $db->close();
            echo "ok";
            exit;
        }
    }
}
$db->close();
echo "";
exit;