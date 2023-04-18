addEventListener("load",function () {
    {
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
    }
});

// フォーム入力ショートカットボタン
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
    const xhr = new XMLHttpRequest();
    const params = "?uid=" + f.uid.value + "&yy=" + f.year.value + "&mm=" + f.month.value;
    xhr.open("get", ADDR + "/dataview/main.php" + params);
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

function formcheck(formid) {
    const f = document.getElementById(formid)
    let err_ignore = ""; let err_exit = "";
    switch (formid) {
        case "form_chargemng_reg":
            if(f.paymethod_number.selectedIndex==0) { err_exit = "[!] 支払方法が未選択"; break; }
            if(!f.charge_v.value) { err_exit = "[!] 請求金額が未入力"; break; }
            if(!f.date) { err_ignore = "[ ] 請求日に変更なし"; break; }
            break;
    
        case "form_chargemng_stupdate":
            
            break;
        default:
            break;
    }
}