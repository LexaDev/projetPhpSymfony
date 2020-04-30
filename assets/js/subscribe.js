function inscrire() {

    let outin= event.target.id;
    let xhr = new XMLHttpRequest();
    let method = "GET";
    let url ="/subscribe/"+outin;
    xhr.onreadystatechange=function () {
        if (xhr.readyState === 4 && xhr.status === 200)
        {
            let span = document.getElementById("inscrit"+outin);
            span.innerText="";
            let btn = document.createElement('button');
            btn.setAttribute('type','submit');
            btn.setAttribute('id',outin);
            btn.setAttribute('class','btn btn-sm btn-link');
            btn.addEventListener('click',desister);
            btn.innerText='Se désister';
            span.appendChild(btn);

            document.getElementById('isInscrit'+outin).innerText="X";
        }
        else if (xhr.readyState === 4 ){
            let rep = xhr.response;
            console.log(rep);
            alert(xhr.response);
        }
    }

    xhr.open(method,url);
    xhr.send();

}

function desister() {

    let outin= event.target.id;
    let xhr = new XMLHttpRequest();
    let method = "GET";

    let url ="/unsubscribe/"+outin;
    xhr.onreadystatechange=function () {
        if (xhr.readyState === 4 && xhr.status === 200)
        {
            let span = document.getElementById("inscrit"+outin);
            span.innerText="";
            span.setAttribute('class',outin);
            let btn = document.createElement('button');
            btn.setAttribute('type','submit');
            btn.setAttribute('id',outin);
            btn.setAttribute('class','btn btn-sm btn-link');
            btn.addEventListener('click',inscrire);
            btn.innerText='S\'inscrire';
            span.appendChild(btn);

            document.getElementById('isInscrit'+outin).innerText="";
        }
        else if (xhr.readyState === 4 ){
            alert(xhr.response);
        }
    }

    xhr.open(method,url);
    xhr.send();
}