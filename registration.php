<?php
    require_once "stdio.php";
    $USER_ID = 1;
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style.css">
    <script src="./stdio.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fingerprintjs2/2.1.0/fingerprint2.js"></script>
    <title>金銭管理 > 登録</title>
</head>
<body>
<header>
    <p class="hd-title"><a href="index.html">LINE金銭管理</a></p>
    <nav class="hd-memu">
        <ul><label>決済情報</label><li id="hd-memu-active"><a href="registration.html">登録</a></li><li><a href="dataview.php">表示</a></li></ul>
    </nav>
</header>
<main>
    <p class="status">
        <?php if (isset($_GET["st"])) {
            if ($_GET["st"] == "ok") { echo "登録完了"; }
            else if ($_GET["st"] == "err") { echo "登録失敗"; }
        } ?>
    </p>
    <form id="form-registration" method="post" action="./registration/main.php" onsubmit="return fillcheck()">
        <ul>
            <label>登録日付</label><!-- date -->
            <li>
                <label class="default-chbx"><input type="checkbox" name="date_today" id="chbx1" checked onclick="chbx(this)">今日</label>
                <label class="default-chbx"><input type="checkbox" name="date_yest" id="chbx2" onclick="chbx(this)">昨日</label>
                <label class="default-chbx"><input type="checkbox" name="date_manual" id="chbx3" onclick="chbx(this)">指定</label>
                <input type="date" name="date" min="" max=""></li>
            <label>支払方法</label><!-- payway -->
            <li>
                <select name="paymethod_number">
                    <option disabled selected>支払方法</option>
                    <?php
                        $db = new DBSrvs();
                        $mysqli = $db->mysqli();
                        $paymethod = $db->get_paymethod($USER_ID);
                        foreach ($paymethod as $key => $m) {
                            echo "<option value='{$m["paymethod_number"]}'>{$m["subname"]}</option>";
                        }
                        $db->close();
                    ?>
                </select>
            </li>
            <label>支払金額<span class="values_check_holder" autocomplete="off" id="vpay_holder">0</span></label><!-- v-pay --><li><input id="vpay" type="number" name="v_pay" placeholder="支払金額" onblur="numFormatChng(this)"></li>
            <label>受取金額<span class="values_check_holder" autocomplete="off" id="vget_holder">0</span></label><!-- v-get --><li><input id="vget" type="number" name="v_get" placeholder="受取金額" onblur="numFormatChng(this)"></li>
            <label>会計種別</label><!-- cmnt --><li>
                <input type="text" name="check_type" autocomplete="off" style="width: 90%; max-width: 400px;">
                <p class="qi-list">
                    <span class="qi-item">食費</span>
                    <span class="qi-item">交際費</span>
                    <span class="qi-item">娯楽費</span>
                    <span class="qi-item">雑費</span>
                    <span class="qi-item">特別費</span>
                    <span class="qi-item">その他_医療費等</span>
                    <span class="qi-item">その他_固定費各種</span>
                </p>
            </li>
            <li><input type="submit" value="登録" name="send_btn"></li>
            <label>コメント</label><!-- cmnt --><li>
                <input type="text" name="cmnt" autocomplete="off" style="width: 90%; max-width: 400px;">
                <p class="qi-list">
                    <span class="qi-item">昼食</span>
                    <span class="qi-item">夕食</span>
                    <span class="qi-item">自販機</span>
                    <span class="qi-item">PASMOチャージ</span>
                    <span class="qi-item">コンビニ</span>
                    <span class="qi-item">交通</span>
                    <span class="qi-item">その他</span>
                </p>
            </li>
            <li><input type="submit" value="登録" name="send_btn"></li>
        </ul>
        <input type="hidden" name="datatype" value="registration">
        <input type="hidden" name="user_id" value="<?php echo $USER_ID ?>">
    </form>
</main>
<footer>
</footer>
<script>
    const form = document.forms["form-registration"];
    {
        {
            // form-registration : input[name=date] . min & max - values set
            let today = new Date();
            let dateMin = new Date(); dateMin.setMonth(today.getMonth() - 3);
            let dateMax = new Date(); dateMax.setMonth(today.getMonth() + 4);
            form.date.min = `${dateMin.getFullYear()}-${zeroPad(dateMin.getMonth()+1)}-${zeroPad(dateMax.getDate())}`;
            form.date.max = `${dateMax.getFullYear()}-${zeroPad(dateMax.getMonth()+1)}-${zeroPad(dateMax.getDate())}`;
        }
        {
            const check_type = document.forms["form-registration"].check_type;
            const cmnt = document.forms["form-registration"].cmnt;
            const qiItems = document.getElementsByClassName("qi-item");
            console.log(qiItems);
            for (let i = 0; i < qiItems.length; i++) {
                if (i<7) {
                    qiItems[i].addEventListener("click", function(){
                        check_type.value += qiItems[i].innerText;
                    });
                } else {
                    qiItems[i].addEventListener("click", function(){
                        cmnt.value += qiItems[i].innerText;
                    });
                }
            }
        }
    }

    function chbx(obj) {
        // form-registration : input[type=checkbox] - Exclusive check function
        let that = obj;
        if (document.getElementById(that.id).checked == true) {
            let boxes = document.querySelectorAll('input[type="checkbox"]');

            for (let i = 0; i < boxes.length; i++) {
            boxes[i].checked = false;
            }
            document.getElementById(that.id).checked = true;
        } else {
            document.getElementById("chbx1").checked = true;
        }
    }

    function numFormatChng(obj) {
        let that = obj;
        let thatValue = Number(that.value);
        let thatId = that.id;
        let targetId = "";
        switch (thatId) {
            case "vpay":
                targetId = "vpay_holder";
                break;
            case "vget":
                targetId = "vget_holder";
                break;
        }
        let target = document.getElementById(targetId);
        target.innerText = thatValue.toLocaleString();
    }
    if (getprm("st")) {
        let elem = document.getElementsByClassName("status");
        elem[0].style.display = "block";
    }

    (async function(){
        const components = await Fingerprint2.getPromise();
        const values = components.map(component => component.value);
        const fp = Fingerprint2.x64hash128(values.join(""), 31);
        const uid = <?php echo $USER_ID ?>;
        const srvrurl = "http://"+location.host+"/";
        const xhr = new XMLHttpRequest();
        xhr.open("get", srvrurl + "fingerprint/user_identify.php"
            + "?fp=" + fp
            + "&uid=" + uid
        );
        xhr.send();
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                console.log("responseText=" + xhr.responseText);
                if (xhr.responseText == "ok") {
                    return true;
                } else {
                    console.log("not allow");
                    window.location.href = "./fingerprint/setting.php"
                    + "?uid=" + uid
                    + "&fp=" + fp;
                }
            }
        }
    })();

    function fillcheck() {
        let consents = [];
        if (form.date_manual.checked == true && !form.date.value) {
            window.confirm("[ERR] 不足: 日付(date)");
            return false;
        }
        if (form.paymethod_number.selectedIndex==0) {
            consents[consents.length] = "支払方法(pmh) が 選択されていません";
        }
        if (!form.v_pay.value) {
            window.confirm("[ERR] 不足: 支払金額(pay)");
            return false;
        }
        if (!form.v_get.value) {
            consents[consents.length] = "受取金額(get) が \xA50 です";
        }
        if (!form.check_type.value) {
            consents[consents.length] = "会計種別(check_type) が 選択されていません";
        }

        let text = "[確認]\n";
        for (let c of consents) {
            text += "- " + c;
        }
        return window.confirm(text);
    }
</script>
</body>
</html>