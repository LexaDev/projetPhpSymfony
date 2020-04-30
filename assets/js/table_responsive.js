document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.table-responsive').forEach(function (table) {
        let labels = [];
        table.querySelectorAll('th').forEach(function (th) {
            labels.push(th.innerText);
        })
        table.querySelectorAll('td').forEach(function (td, index) {
            td.setAttribute('data-label', labels[index % labels.length]);   //calcul de math pour avoir un indice correct du tableau
        })
    })
})
