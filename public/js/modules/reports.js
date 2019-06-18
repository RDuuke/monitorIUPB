
var ctx_c = $("#usuariosConsolidado");
var ctx_m = $("#usuariosMes");
var backgroundColor = [
        'rgba(255, 99, 132)',
        'rgba(54, 162, 235)',
        'rgba(255, 206, 86)',
        'rgba(75, 192, 192)',
        'rgba(153, 102, 255)',
        'rgba(255, 159, 75)',
        'rgba(255, 12, 64)',
        'rgba(21, 159, 64)',
    ];

$.get($("#url").attr('data-url')).done(function (response) {
    let labels = [];
    let dataset = [];
    $.each(response, function (key, value) {
        labels.push(value.nombre);
        dataset.push(value.cantidad);
    });

    config = {
        type: "pie",
        data: {
            labels: labels,
            datasets: [{
                data: dataset,
                backgroundColor: backgroundColor
            }],
        },
        options: {
            cutoutPercentage: 20,
            legend: {
                position: 'bottom'
            },
        },
    };
    var myPieChart  = new Chart(ctx_c, config);

});
$.get($("#url-moth").attr('data-url')).done(function (response) {
    let labels = [];
    let dataset = [];
    $.each(response, function (key, value) {
        labels.push(value.nombre);
        dataset.push(value.cantidad);
    });
    config = {
        type: "horizontalBar",
        data: {
            labels: labels,
            datasets: [{
                data: dataset,
                backgroundColor: backgroundColor,
                label : "Periodo del mes: " + $("b").html(),
            }],
        },

    };
    var myPieChart  = new Chart(ctx_m, config);
});


$("#formLimitDate").on("submit", function (e) {
    e.preventDefault();
    let fechaI = new Date($("#fechainicia").val());
    let fechaF = new Date($("#fechafinal").val());
    if ( fechaI.getTime() <= fechaF.getTime() )  {
        $.get($(this).attr('action') + "/" + $("#fechainicia").val() + "/" + $("#fechafinal").val()).done( function (response) {
            console.log(response);
            $("#response_table").html(response);
        });
    }else {
        toastr.error("La fecha inicial no puede ser mayor que la final.", "Error", {timeOut: 5000000});
    }

});
