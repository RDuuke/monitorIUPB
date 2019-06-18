
$("#userCreateForm").on( "submit", function( event ) {
  event.preventDefault();
  var userForm = $(this).serialize();
  $.post($(this).attr('action'), userForm).done( function(response){
    if (response.message == 1){
      table.row.add(response.user).draw(false);
      toastr.success('Accion completada correctamente.', 'Estupendo!!!', {timeOut: 3000});
    } else if(response.message == 2) {
      console.log(_td);
      table.row(_td.parents('tr')).data(response.user);
      toastr.success('Accion completada correctamente.', 'Estupendo!!!', {timeOut: 3000});
    }else {
      console.log('0');
    }
    $('#userCreateModal').modal('hide');

  }).
  fail(function(response){
    toastr.error('Servicio no disponible intentalo luego.' + response.responseText, 'Error!!', {timeOut: 3000});
  });

});
$("#tb_user").on('click', '.studentEliminar', function(event) {
    event.preventDefault();
    var r = confirm("¿Está seguro que desea eliminar este registro de forma permanente?");
    if (r == true) {

        _td = $(this);
        var _data = functions.getDataTable(_td);
        var url = _td.attr('href') + _data.id
        $.get(url).done( function(response){
        if (response == 1){
          toastr.success('Estudiante eliminado correctamente.', 'Estupendo!!!', {timeOut: 3000});
          table
            .row( _td.parents('tr') )
            .remove()
            .draw();    }
        }).
        fail(function(response){
            toastr.error(response.responseText, 'Error!!', {timeOut: 3000});
        });
      } else {
        return false;
      }
});
$("#tb_user").on('click', '.studentShow', function(event){
    event.preventDefault();
    _td = $(this);
    var _data = functions.getDataTable(_td);
    var url = _td.attr('href') + _data.id
    $.get(url).done(function(response){
      $.each(response, function( key, value ) {
          if (key == 'institucion_id' && value != ''){
              $('#institucion_user option[value='+value+']').attr('selected','selected');
          } else if ( key == 'genero' && value != "") {
              $("select#genero option[value="+value+"]").prop("selected", true);
          } else if (key == 'usuario') {
              $('input[name="'+key+'"]').val(value).attr('readonly');
              $('input[name="'+key+'"]').attr('readonly', 'readonly');
          }
          else {
              $('input[name="'+key+'"]').val(value);
          }
          $('#userCreateForm').attr('action', _td.attr('data-href') + _data.id);
        $('#userCreateModal').modal('show');
      });
    });
});

$("#tb_user").on('click', '.studentChangePaswword', function(event){
    var r = confirm("¿Está seguro que desea restablecer la contraseña?");
    if (r == true) {

        event.preventDefault();
        _td = $(this);
        var _data = functions.getDataTable(_td);
        var url = _td.attr('href') + _data.id
        $.get(url).done(function(response){
            functions.removeToast();
            if (response.response == 1) {
                toastr.success(response.message, 'Finalizado', {timeOut : 3000});
            } else {
                toastr.error(response.message, 'Error', {timeOut : 3000});
            }
        });
    } else {
        return false;
    }
});

$("#tb_user").on('click', '.studentArchive', function(event){
    var r = confirm("¿Está seguro que desea archivar este registro de forma permanente?");
    if (r == true) {
        event.preventDefault();
        _td = $(this);
        var _data = functions.getDataTable(_td);
        var url = _td.attr('href') + _data.id
        $.get(url).done(function(response){
            functions.removeToast();
            console.log(response);
            if (response.response == 1) {
                toastr.success(response.message, 'Finalizado', {timeOut : 3000});
                table
                    .row( _td.parents('tr') )
                    .remove()
                    .draw();
            } else if(response.response == 2) {
                toastr.info(response.message, 'Info', {timeOut : 3000});

            }
            else {
                toastr.error(response.message, 'Error', {timeOut : 3000});
            }
        });
    } else {
        return false;
    }
});
$("#tb_user").on('click', '.searchRegister', function(event){
  let n = 0;
  event.preventDefault();
  _td = $(this);
  var _data = functions.getDataTable(_td);
  $("#name_student").html(_data.nombres + ' ' + _data.apellidos);
  var url = _td.attr('href') + _data.id
  $.get(url).done(function(response){
      $("#content_student_register").html("");
      $("#content_student_register").html(response);
      $("#studentRegister").modal('show');
  });

});
$("#content_student_register").on("click", ".archive_register", function(event){
  event.preventDefault();
  var r = confirm("¿Está seguro que quiere desmatricular?");
  if (r == true) {
    _this = $(this);
    //$("#name_student").html(data.nombres + ' ' + data.apellidos);
    $.get($(this).attr('href')).done(function (r){

      if (r.message == 1 || r.message == '1') {
        _this.parent().parent().remove();
        toastr.success("Matricula archivada", "¡¡ Estupendo!!", {timeOut: 3000});
      }
    });
  } else {
    return false;
  }
});
$(".addstudent").on('click', function(event){
    event.preventDefault();
    $('#userCreateForm')[0].reset();
    $('#userCreateForm').attr('action',$(this).attr('data-href'));
    $('#userCreateForm input[name="usuario"]').removeAttr('readonly');
    $('#userCreateModal').modal('show');
});
