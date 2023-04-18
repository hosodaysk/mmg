<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <script src="stdio.js"></script>
    <title>金銭管理 > 表示</title>
    <script>
        const nsdata = "";
        let trdata = sessionStorage.getItem("tradedata");
        let insertdata = "";
        if (trdata != "" && nsdata != "") {
            insertdata = trdata + "," + nsdata;
        } else {
            if (trdata == "" && nsdata != "") {
                insertdata = nsdata;
            } else {
                insertdata = trdata;
            }
        }
        sessionStorage.setItem("tradedata", insertdata);
    </script>
</head>

<body>
    <header>
        <p class="hd-title"><a href="index.html">LINE金銭管理</a></p>
        <nav class="hd-memu">
            <ul><label>決済情報</label>
                <li><a href="registration.php">登録</a></li>
                <li id="hd-memu-active"><a href="dataview.php">表示</a></li>
            </ul>
        </nav>
    </header>
    <main id="data_tables">
        <section>
            <form id="form_dataview">
                <ul>
                    <label>検索 - 年 ・ 月</label>
                    <li><!-- year -->
                        <label class="default-chbx">
                            <input type="button" name="year_today" id="btn1" value="今年" style="width: 60px;"
                                onclick="filldate(this)">
                        </label>
                        <label class="default-chbx">
                            <input type="button" name="year_bf" id="btn2" value="去年" style="width: 60px;"
                                onclick="filldate(this)">
                        </label>
                        <input type="number" name="year" placeholder="検索 - 年" style="width: 100px;">
                    </li>
                    <li><!-- month -->
                        <label class="default-chbx">
                            <input type="button" name="month_today" id="btn3" value="今月" style="width: 60px;"
                                onclick="filldate(this)">
                        </label>
                        <label class="default-chbx">
                            <input type="button" name="month_bf" id="btn4" value="先月" style="width: 60px;"
                                onclick="filldate(this)">
                        </label>
                        <input type="number" name="month" placeholder="検索 - 月" style="width: 100px;">
                    </li>
                    <li><input type="button" value="検索" name="send_btn" onclick="GoSearch()"></li>
                </ul>
                <input type="hidden" name="datatype" value="dataview">
                <input type="hidden" name="uid" value="1">
            </form>
        </section>
        <!-- <section class="result-dataview">
            <hr class="sub-section">
            <header class="sub-section">
                <p>20YY年MM月リスト</p>
            </header>
            <main>
                <table>
                    <thead>
                        <tr>
                            <th colspan="3">当月合計</th>
                        </tr>
                        <tr>
                            <th width="100px"></th>
                            <th width="200px"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>支払 (PAY)</th>
                            <td class="cell-y" id="dw_psum"></td>
                        </tr>
                        <tr>
                            <th>受取 (GET)</th>
                            <td class="cell-y" id="dw_gsum"></td>
                        </tr>
                        <tr>
                            <th>収支</th>
                            <td class="cell-y" id="dw_sum"></td>
                        </tr>
                    </tbody>
                </table>
                <table>
                    <thead>
                        <tr>
                            <th colspan="3">当月合計 (支払方法別)</th>
                        </tr>
                        <tr>
                            <th width="200px"></th>
                            <th width="100px"></th>
                            <th width="100px"></th>
                        </tr>
                        <tr>
                            <th>支払方法(PMH)</th>
                            <th>PAY</th>
                            <th>GET</th>
                        </tr>
                    </thead>
                    <tbody id="dw_pmh">
                        <tr>
                            <td>PAYWAY1</td>
                            <td>PSUM</td>
                            <td>GSUM</td>
                        </tr>
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
                            <th width="40px"></th>
                        </tr>
                        <tr>
                            <th data-id="sn" sortable>SN</th>
                            <th data-id="date" sortable>日付</th>
                            <th data-id="pr" sortable>P/R</th>
                            <th data-id="value" sortable>金額</th>
                            <th data-id="pmh" sortable>PMH</th>
                            <th data-id="cmnt" sortable>ｺﾒﾝﾄ</th>
                            <th data-id="rid" sortable>RcID</th>
                        </tr>
                    </thead>
                    <tbody id="dwrite">
                    </tbody>
                </table>
            </main>
        </section> -->
    </main>
    <script>
        const f = document.forms["form_dataview"];
        function filldate(obj) {
            // form-registration : input[type=checkbox] - Exclusive check function
            let that = obj;
            let today = new Date();
            switch (that.value) {
                case "今年":
                    f.year.value = today.getFullYear(); break;
                case "去年":
                    f.year.value = today.getFullYear() - 1; break;
                case "今月":
                    f.month.value = today.getMonth() + 1; break;
                case "先月":
                    let m = today.getMonth();
                    f.month.value = (m == 0) ? 12 : m; break;
                    break;
                default:
                    break;
            }
        }

        function GoSearch() {
            const url = "http://"+location.host+"/";
            const xhr = new XMLHttpRequest();
            const params = "?uid=" + f.uid.value + "&yy=" + f.year.value + "&mm=" + f.month.value;
            xhr.open("get", "./dataview/main.php" + params);
            xhr.send();
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 & xhr.status === 200) {
                    if (xhr.responseText != "") {
                        let section = document.createElement('section');
                        section.className = "result-dataview";
                        section.innerHTML = xhr.responseText;
                        document.getElementById("data_tables").appendChild(section);
                    }
                }
            }
            
        }
    </script>
</body>

</html>