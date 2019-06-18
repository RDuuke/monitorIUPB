
var ctx = document.getElementById("grafica");
var id = window.location.href.split("/").pop();

$.get(getUri + "/panel/monitoreo/data/" + id)
    .done( function (response) {
        console.log(response);
        let labels = [], values = [];
        for (let label of response.labels) {
            labels.push(label.labels);
        }
        for (let data of response.dataset) {
            values.push(Number.parseFloat(data.time.replace(",", "."))*1000);
        }
        console.log(labels);
        console.log(values);
        var chart = new Chart(ctx, {
            // The type of chart we want to create
            type: 'line',

            // The data for our dataset
            data: {
                labels : labels,
                datasets: [{
                    data: values,
                    label: 'Milisegundos'
                }],

            },

            // Configuration options go here
            options: {
                title: {
                    display: true,
                    text: 'Tiempo de respuesta de las últimas 24 horas.'
                },
                scales: {
                    yAxes: [{
                        title : "Horas",
                        stacked: true
                    }]
                }
            },
        })
    });
