$(function(){
    let data = null;
    functions = {
        getDataTable : function (tr) {
            return table.row(tr.parents('tr')).data();
        },
        getCourses : function() {

            $.get(getUri + "/panel/register/courses").done(function(response){
                return response;
            });
        },
        searchRender : function (param, element) {
            $.get(getUri + param ).done( function(response){
                element.append(response);
            });
        },
        render : function(tag, data, route="", type="") {
            let html = "";
            switch(type){
                case 'list':
                    $.each(data, function(key, value){
                        html = html + "<tr><td>"+value.codigo+"</td><td><a href='"+getUri+ route + value.codigo + "'>"+ value.nombre +"</a></td></tr>";
                    });
                    break;
                    case 'table':
                    break;
                    case 'students':
                    $.each(JSON.parse(data), function(key, value){
                        html =  html + "<tr><td>"+value.documento+"</td><td>"+ value.nombres + "</td><td>" + value.apellidos +"</td><td>"+ value.telefono +"</td><td><a href='"+getUri+ route + value.id +"'>"+value.usuario+"</a></td></tr>";
                    });
                    break;
            }
            return html;
        },
        lowercase : function (str) {
            return str.toLowerCase();
        },

        proccess : function (data, url, type = 1) {
            toastr.info('Cargando usuarios.', 'Cargando...', {timeOut: 500000});
            $.ajax({
                method : "POST",
                url : url,
                enctype: 'multipart/form-data',
                data: {data : data},
                cache: false
            }).done(function(response){
                console.log(response);
                functions.removeToast();
                if (type === 1) {
                    toastr.success(response.message, 'Generando archivo', {timeOut: 3000});
                    functions.dowloadStudent(response);
                } else {
                    toastr.info("Proceso terminado", "Info", {timeOut : 3000});
                }
            }).fail( function (response) {
                console.log(response);
                toastr.remove();
                toastr.error("Error cargando los datos, vuelve ha intentarlo", "Error", {timeOut: 5000000});
            });
        },
        sleep : function (ms) {
            return new Promise(resolve => setTimeout(resolve, ms));
        },

        removeToast : function () {
            toastr.remove();
        },

        getReportStudent :  function (url) {
            let labels = [];
            let dataset = [];
            $.get(url).done(function (response) {
                $.each(response, function (key, value) {
                    labels.push(value.nombre);
                    dataset.push(value.cantidad);
                });
            });
            return {labels, dataset};
        },  
        
        dowloadStudent : function (xlsRows) {
            console.log(xlsRows);
            var createXLSLFormatObj = [];
            var xlsHeader = ["USUARIO", "CLAVE", "NOMBRES",	"APELLIDOS", "DOCUMENTO", "INSTITUCION", "GENERO", "CIUDAD",
                "DEPARTAMENTO",	"PAIS",	"TELEFONO",	"CELULAR", "DIRECCIÓN", "CODIGO", "MENSAJE"];
            createXLSLFormatObj.push(xlsHeader);
            if (xlsRows.creators.total > 0) {

                $.each(xlsRows.creators.data, function (index, value) {
                    createXLSLFormatObj.push([
                        value.usuario, value.clave, value.nombres, value.apellidos,
                        value.documento, value.institucion, value.genero, value.ciudad, value.departamento, value.pais,
                        value.telefono, value.celular, value.direccion, value.codigo, value.message
                    ]);
                });
            }
            if (xlsRows.errors.total > 0) {

                $.each(xlsRows.errors.data, function (index, value) {
                    createXLSLFormatObj.push([
                        value.usuario, value.clave, value.nombres, value.apellidos,
                        value.documento, value.institucion, value.genero, value.ciudad, value.departamento, value.pais,
                        value.telefono, value.celular, value.direccion, value.codigo, value.message
                    ]);
                });
            }

            var filename = "usuarios_procesados.xlsx";

            /* Sheet Name */
            var ws_name = "usuariosProcesados";

            if (typeof console !== 'undefined') console.log(new Date());
            var wb = XLSX.utils.book_new(),
                ws = XLSX.utils.aoa_to_sheet(createXLSLFormatObj);

            /* Add worksheet to workbook */
            XLSX.utils.book_append_sheet(wb, ws, ws_name);

            /* Write workbook and Download */
            if (typeof console !== 'undefined') console.log(new Date());
            XLSX.writeFile(wb, filename);
            if (typeof console !== 'undefined') console.log(new Date());
        }
    }
});