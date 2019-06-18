
$("#userCreateForm").on( "submit", function( event ) {
    event.preventDefault();
    var userForm = $(this).serialize();
    $.post($(this).attr('action'), userForm).done( function(response){
      if (response.message == 1){
        table.row.add(response.user).draw(false);
        toastr.success('Accion completada correctamente.', 'Estupendo!!!', {timeOut: 3000});
        $('#userCreateModal').modal('hide');
      } else if(response.message == 2) {
        console.log(_td);
        table.row(_td.parents('tr')).data(response.user);
        toastr.success('Accion completada correctamente.', 'Estupendo!!!', {timeOut: 3000});
        $('#userCreateModal').modal('hide');
      }else {
        console.log('0');
        toastr.error('No sé pudo registrar correctamente. '+ response.message, 'Error!!', {timeOut: 3000});
      }
        $('#userCreateModal').modal('hide');
    }).
    fail(function(response){
      toastr.error('Servicio no disponible intentalo luego. '+ response.message, 'Error!!', {timeOut: 3000});
    });

});
$("#tb_user").on('click', '.userShow', function(event){
    event.preventDefault();
    _td = $(this);
    var _data = functions.getDataTable(_td);
    var url = _td.attr('href');
    $.get(url).done(function(response){
      $('#userCreateForm')[0].reset();
      $.each(response, function( key, value ) {
        if ( key == 'id_institucion') {
            $('#institucion option[value='+value+']').attr('selected','selected');
        } else if ( key == 'id_rol') {
            $('select#id_rol option[value='+value+']').attr('selected','selected');
        } else {
            $('input[name="'+key+'"]').val(value);
        }
        $('#userCreateForm').attr('action', _td.attr('data-href') + _data.id);
        $('#userCreateForm').removeClass();
        $('#userCreateForm').addClass('update');
        $('#userCreateModal').modal('show');
      });
    });
});
$("#tb_user").on('click', '.userChangePaswword', function (event) {
    event.preventDefault();
    var r = confirm("¿Esta seguro que deseas restablecer la contraseña de este usuario?");
    if (r == true) {
      $.get($(this).attr('href'))
          .done(function (response) {
          if (response.message == 1) {
            toastr.success("Contraseña restablecida correctamente", "Info", {timeOut : 3000});
          }
          }).fail( function (response) {

      });
    } else {
      return false;
    }
});
$("#tb_user").on('click', '.userEliminar', function(event) {
  event.preventDefault();
    var r = confirm("¿Está seguro que desea eliminar este registro de forma permanente?");
    if (r == true) {
      _td = $(this);
      var _data = functions.getDataTable(_td);
      var url = _td.attr('href');
      $.get(url).done( function(response){
      if (response == 1){
        toastr.success('Usuario eliminado correctamente.', 'Estupendo!!!', {timeOut: 3000});
        table
          .row( _td.parents('tr') )
          .remove()
          .draw();    }
      }).
      fail(function(response){
        toastr.error('Servicio no disponible intentalo luego.', 'Error!!', {timeOut: 3000});
      });
    } else {
      return false;
    }
});

$(".addstudent").on('click', function(event){
   event.preventDefault();
    $('#userCreateForm')[0].reset();
    $('#userCreateForm').attr('action',$(this).attr('data-href'));
    $('#userCreateModal').modal('show');
  });

$("#tb_user").on("click", ".userPermission", function(event){
  event.preventDefault();
  let _this = $(this);
  var _data = functions.getDataTable(_this);
    $("#tablePermission tbody").html("");
  $("#permission").attr("data-id", _data.id);
  $.get(_this.attr('href')).done(function(response){
    $("#tablePermission tbody").append(response);
    $('#addPermissionModal').modal('show');
  });
});

$("#writing").on('change', function (event) {
  $("#reading").prop('checked', true);
});

$("#permission").on('click', function(event){
  event.preventDefault();
  var data = $("#addPermissionForm").serialize();
  let _this = $(this);
  $.ajax({
    url : _this.attr('href') + "/" +  _this.attr('data-id'),
    data : data,
    method : "POST"
  }).done( function(jsonResponse){
      if (jsonResponse.status == 1) {
          if(jsonResponse.writing == 2){ w = "Sí"}else{w = "No"};
          if(jsonResponse.reading == 1){ r = "Sí"}else{r = "No"};
          if ($("#tablePermission tbody tr[data-id="+jsonResponse.id+"]").length == 1){
              $("#tablePermission tbody tr[data-id="+jsonResponse.id+"]").html("<td class='text-left text-capitalize'>"+jsonResponse.module+"</td><td class='text-center text-capitalize'>"+r+"</td><td class='text-center text-capitalize'>"+w+"</td><td class='text-right'><a class='permissionRemove icon_a text-danger' href='"+getUri+"/panel/users/permission/remove/"+jsonResponse.id+"'><i class='fa fa-remove'></i></a></td>");
          } else {
              $("#tablePermission tbody").append("<tr data-id='"+jsonResponse.id+"'><td class='text-left text-capitalize'>"+jsonResponse.module+"</td><td class='text-center text-capitalize'>"+r+"</td><td class='text-center'>"+w+"</td><td class='text-right'><a class='permissionRemove icon_a text-danger' href='"+getUri+"/panel/users/permission/remove/"+jsonResponse.id+"'><i class='fa fa-remove'></i></a></td><tr>");
          }
          toastr.success('Permisos asignados correctamente.', 'Estupendo!!!', {timeOut: 3000});
      } else {
          toastr.error('No se pudo agregar los permisos.', 'Estupendo!!!', {timeOut: 3000});
      }
  }).fail( function (response) {
        toastr.error('No se pudo agregar los permisos.', 'Error!', {timeOut: 3000});
  });
});

$("#addPermissionModal").on('click', '.permissionRemove', function(event){

    event.preventDefault();
    var r = confirm("¿Esta seguro que deseas eliminar los permisos?");
    if (r == true) {
        let _this = $(this);
        $.get(_this.attr('href')).done(function (r){
            if (r.status == 1) {
                _this.parents('tr').remove();
                toastr.success('Permisos elimados correctamente.', 'Estupendo!!!', {timeOut: 3000});
            } else {
                toastr.error('No se pudo elimadar los permisos .', 'Error!', {timeOut: 3000});
            }
        })
    } else {
        return false;
    }
});
