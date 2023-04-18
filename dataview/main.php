<?php
require_once "../stdio.php";

$inputs = array(
    "uid" => $_GET["uid"],
    "yy" => $_GET["yy"],
    "mm" => $_GET["mm"]
);

$results = array(
    "record_id" => [],
    "datetime" => [],
    "paytype" => [],
    "value" => [],
    "check_type" => [],
    "cmnt" => [],
    "name" => [],
    "subname" => [],
    "psum" => 0,
    "gsum" => 0,
    "sum" => 0,
    "pmh_subname" => [],
    "pmh_psum" => [],
    "pmh_gsum" => [],
    "pmh_sum" => []
);

//
//

//
//

$db = new DBSrvs();
$mysqli = $db->mysqli();
$query = '
SELECT record_id, datetime, pay_type, value, cmnt, check_type, name, subname FROM record AS R
INNER JOIN paymethod AS P
    ON P.user_id = ? AND P.paymethod_number = R.paymethod_number
WHERE R.user_id = ?
AND R.datetime BETWEEN ? AND ?
ORDER BY R.cmnt';
/*
SELECT record_id, datetime, pay_type, value, cmnt, name, subname FROM record AS R
INNER JOIN paymethod AS P
    ON P.user_id = 1 AND P.paymethod_number = R.paymethod_number
WHERE R.user_id = 1
AND R.datetime BETWEEN '2023-01-01 00:00:00' AND '2023-01-31 23:59:59'
ORDER BY R.cmnt
*/

if ($stmt = $mysqli->prepare($query)) {
    $stmt->bind_param("iiss", $a, $b, $c, $d);
    $a = (int) $inputs["uid"];
    $b = $a;
    $s = $inputs["yy"] . "-" . str_pad($inputs["mm"], 2, "0", STR_PAD_LEFT) . "-01";
    $c = date("Y-m-01 H:i:s", strtotime($s));
    $d = date("Y-m-01 H:i:s", strtotime($s . " 1 month"));
    $stmt->execute();
    $stmt->bind_result($rid, $datetime, $paytype, $value, $cmnt, $check_type, $name, $subname);
    while ($stmt->fetch()) {
        $rp = array_push($results["record_id"], $rid);
        $results["datetime"][] = $datetime;
        $results["paytype"][] = $paytype;
        $results["value"][] = $value;
        $results["check_type"][] = $check_type;
        $results["cmnt"][] = $cmnt;
        $results["name"][] = $name;
        $results["subname"][] = $subname;

        $pmhidx = array_search($subname, $results["pmh_subname"]);
        if ($pmhidx === false) {
            $pmhidx = array_push($results["pmh_subname"], $subname) - 1;
            $results["pmh_psum"][] = 0;
            $results["pmh_gsum"][] = 0;
        }
        if ($paytype=="PAY") {
            $results["psum"] += (int) $value;
            $results["pmh_psum"][$pmhidx] += (int) $value;
        }
        else {
            $results["gsum"] += (int) $value;
            $results["pmh_gsum"][$pmhidx] += (int) $value;
        }
    }
    $results["sum"] = $results["gsum"] - $results["psum"];
    foreach ($results["pmh_gsum"] as $i => $gsum) {
        $results["pmh_sum"][] = $gsum - $results["psum"][$i];
    }

    $html_template = "
        <hr class='sub-section'>
        <header class='sub-section'>
            <p>{$inputs["yy"]}年{$inputs["mm"]}月リスト</p>
        </header>
        <main>
            <table>
                <thead>
                    <tr>
                        <th colspan='3'>当月合計</th>
                    </tr>
                    <tr>
                        <th width='100px'></th>
                        <th width='200px'></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th>支払 (PAY)</th>
                        <td class='cell-y' id='dw_psum'>{$results["psum"]}</td>
                    </tr>
                    <tr>
                        <th>受取 (GET)</th>
                        <td class='cell-y' id='dw_gsum'>{$results["gsum"]}</td>
                    </tr>
                    <tr>
                        <th>収支</th>
                        <td class='cell-y' id='dw_sum'>{$results["sum"]}</td>
                    </tr>
                </tbody>
            </table>
            <table>
                <thead>
                    <tr>
                        <th colspan='3'>当月合計 (支払方法別)</th>
                    </tr>
                    <tr>
                        <th width='200px'></th>
                        <th width='100px'></th>
                        <th width='100px'></th>
                    </tr>
                    <tr>
                        <th>支払方法(PMH)</th>
                        <th>PAY</th>
                        <th>GET</th>
                    </tr>
                </thead>
                <tbody id='dw_pmh'>
                ";

                foreach($results["pmh_subname"] as $i => $subname) {
                    $html_template .= "<tr><td>{$subname}</td><td>{$results["pmh_psum"][$i]}</td><td>{$results["pmh_gsum"][$i]}</td></tr>";
                }

                $html_template .= '
                </tbody>
            </table>
            <table>
                <thead>
                    <tr>
                        <th colspan="7">当月記録</th>
                    </tr>
                    <tr>
                        <th width="30px"></th>
                        <th width="100px"></th>
                        <th width="40px"></th>
                        <th width="75px"></th>
                        <th width="70px"></th>
                        <th width="120px"></th>
                        <th width="120px"></th>
                        <th width="40px"></th>
                    </tr>
                    <tr>
                        <th data-id="sn">SN</th>
                        <th data-id="date">日付</th>
                        <th data-id="pr">P/R</th>
                        <th data-id="value">金額</th>
                        <th data-id="pmh">PMH</th>
                        <th data-id="check_type">支払種別</th>
                        <th data-id="cmnt">ｺﾒﾝﾄ</th>
                        <th data-id="rid">RcID</th>
                    </tr>
                </thead>
                <tbody id="dwrite">
                ';

                $sn = 0;
                foreach($results["record_id"] as $i => $rcrd_id) {
                    if ($results["value"][$i]==0) { continue; }
                    $sn++;
                    $dt = substr($results["datetime"][$i], 0, 10);
                    $html_template .= "
                    <tr>
                        <td class='cell-n'>{$sn}</td>
                        <td>{$dt}</td>
                        <td>{$results["paytype"][$i]}</td>
                        <td class='cell-y'>{$results["value"][$i]}</td>
                        <td>{$results["subname"][$i]}</td>
                        <td>{$results["check_type"][$i]}</td>
                        <td>{$results["cmnt"][$i]}</td>
                        <td class='cell-n'>{$rcrd_id}</td>
                    </tr>
                    ";
                }

                $html_template .= '
                </tbody>
            </table>
        </main>';
    echo $html_template;
} else {
    echo "nohtml";
}