var formOk = new Array;
var formAlert = new Array;
var table = $('#tableResult').DataTable({
    lengthMenu: [25, 50, 75, 100],
    language: {
        "sProcessing": "Procesando...",
        "sLengthMenu": "Registros por pagina: _MENU_ ",
        "sZeroRecords": "No se encontraron resultados",
        "sEmptyTable": "Ningún dato disponible en esta tabla",
        "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
        "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
        "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
        "sInfoPostFix": "",
        "sSearch": "Buscar:",
        "sUrl": "",
        "sInfoThousands": ",",
        "sLoadingRecords": "Cargando...",
        "paginate": {
            "first": "Primera",
            "last": "Ultima",
            "next": "Siguiente <i class='fa fa-angle-right'></i>",
            "previous": "<i class='fa fa-angle-left'></i> Anterior"
        },
        "oAria": {
            "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
            "sSortDescending": ": Activar para ordenar la columna de manera descendente"
        }
    },
    dom: 'Blfrtip',
    buttons : [{
        extend: 'excel',
        title: 'resultado_analisis_del_anexo1.xlsx',
        text: 'Exportar información en excel <i class="fa fa-file-excel-o"></i>',
        className: 'btn btn-info'
    }],
});

$("#formFile").on('submit', function(event){
    event.preventDefault();
    var _this = $(this);
    var form = new FormData(_this[0]);
    console.log(form);
    table.clear().draw();
    $.ajax({
        type : "POST",
        url : _this.attr('action'),
        cache: false,
        processData: false,
        contentType: false,
        enctype: 'multipart/form-data',
        beforeSend: function () {
            toastr.info("Analizando el archivo... ", "Info", {timeOut: 5000000});
        },
        data: form
    }).done(response => {
        delete formOk;
        delete formAlert;
        toastr.remove();
        let i = 1;
        i = renderData(i, response.creators, 'creators', formOk);
        i = renderData(i, response.alerts, 'alerts', formAlert);
        renderData(i, response.errors, 'errors');
        $("#registros").html(response.totalr);
        $("#totalR").html(response.totalc);
        $("#totalE").html(response.totale);
        $("#totalA").html(response.totala);
        $("p.fadein").addClass('flex').addClass('align-items-center');
        $(".tools-p").fadeIn().addClass('visible');
        $(".fadein").fadeIn();
        $("#content-result").fadeIn();
        $("form")[0].reset();

    }).fail(error => {
        toastr.remove();
        toastr.error("Error analizando el archivo, vuelve a intentarlo", "Error", {timeOut: 5000000});


    });
});
/*
$(".download_archive").on('click', function(event){
    event.preventDefault();
    var tbl = document.getElementById("tableResult");
    var wb = XLSX.utils.table_to_book(tbl);
    var wopts = { bookType:'xlsx', bookSST:false, type:'array' };
    var wbout = XLSX.write(wb,wopts);
    saveAs(new Blob([wbout],{type:"application/octet-stream"}), "resultado_analisis_del_anexo1.xlsx");
});
*/
$("#cargar").on('click', function(event){
    let _this = $(this);
    event.preventDefault();
    console.log($("#alertsT").prop('checked'));
    if ($("#alertsT").prop('checked')) {
        if (formOk.length == 0 && formAlert.length == 0) {
            toastr.error('No hay registros para cargar.', 'Error', {timeOut: 500000});
            return false;
        } else {
            formOk = formOk.concat(formAlert);
            functions.proccess(formOk, _this.attr('data-action'));
        }
    }else {
        if(formOk.length == 0) {
            toastr.remove()
            toastr.error('No hay registros para cargar.', 'Error', {timeOut: 3000});
            return false;
        } else {
            functions.proccess(formOk, _this.attr('data-action'));
        }
    }
});

function renderData(i, value, classes, saveData = []) {
    let a = 0;
    $.each(value, function(key, value) {

        var trDom = table.row.add([
            i,
            value.usuario,
            value.nombres,
            value.apellidos,
            value.documento,
            value.institucion,
            value.codigo,
            value.message
        ]).draw(false).node();
        $(trDom).addClass(classes);

        saveData[a] = {"usuario": value.usuario, "clave": value.clave, "nombres": value.nombres, "apellidos": value.apellidos, "correo": value.correo, "documento": value.documento, "genero": value.genero, "institucion": value.institucion, "ciudad": value.ciudad, "departamento": value.departamento, "pais": value.pais, "telefono": value.telefono, "celular": value.celular, "direccion": value.direccion, "institucion_id": value.institucion_id};
        i++;
        a++;
    });
    return i;
}