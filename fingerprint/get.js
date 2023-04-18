const fp2Src = 'https://cdnjs.cloudflare.com/ajax/libs/fingerprintjs2/2.1.0/fingerprint2.js';

let po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
po.src = 'https://cdnjs.cloudflare.com/ajax/libs/fingerprintjs2/2.1.0/fingerprint2.js';
document.head.appendChild(po);

import { fp2Src } from "module";

async function getme() {
    const components = await Fingerprint2.getPromise();
    const values = components.map(component => component.value);
    const murmur = Fingerprint2.x64hash128(values.join(""), 31);
    console.log("murmur=" + murmur);
    return murmur;
}

function identify (ipt_fp , ipt_uid = "") {
    const srvrurl = "http://"+location.host+"/";
    ipt_uid = (ipt_uid=="") ? (()=>{ return document.forms["form-registration"].user_id.value; }) : ipt_uid;

    const xhr = new XMLHttpRequest();
    xhr.open("get", srvrurl + "fingerprint/user_identify.php"
        + "?fp=" + ipt_fp
        + "&uid=" + ipt_uid
    );
    xhr.send();
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            console.log("responseText=" + xhr.responseText);
            if (xhr.responseText == "ok") {
                return true;
            } else {
                return false;
            }
        }
    }
}