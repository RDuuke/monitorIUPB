

$("form").keypress(function(e) {
  if (e.which == 13) {
      return false;
  }
});
$(".addcourse").on('click', function (event) {
    event.preventDefault();
    $('#programCreateForm')[0].reset();
    $('#programCreateForm').attr('action', $(this).attr('data-href'));
    $("#input_codigo").prop('readonly', false);
    $('#programCreateModal').modal('show');
  });

  $("#programCreateForm").on("submit", function (event) {
    event.preventDefault();
    var userForm = $(this).serialize();
    $.post($(this).attr('action'), userForm).done(function (response) {
      if (response.message == 1) {
        table.row.add(response.course).draw(false);
        toastr.success('Accion completada correctamente.', 'Estupendo!!!', { timeOut: 3000 });
        $('#programCreateModal').modal('hide');
      } else if (response.message == 2) {
        console.log(_td);
        table.row(_td.parents('tr')).data(response.course);
        toastr.success('Accion completada correctamente.', 'Estupendo!!!', { timeOut: 3000 });
        $('#programCreateModal').modal('hide');
      } else {
        toastr.error('No sé pudo registrar correctamente, ' + response.alerta, 'Error!!', { timeOut: 3000 });
      }

    }).
      fail(function (response) {
        toastr.error(response.responseText, 'Error!!', { timeOut: 3000 });
      });

  });

  $("#tb_courses").on('click', '.coursesEliminar', function(event) {
    event.preventDefault();
      var r = confirm("¿Está seguro que desea eliminar este registro de forma permanente?");
      if (r == true) {
        _td = $(this);
        var _data = functions.getDataTable(_td);
        var url = _td.attr('href');
        $.get(url).done( function(response){
          if (response == 1){
            toastr.success('Curso eliminado correctamente.', 'Estupendo!!!', {timeOut: 3000});
            table
              .row( _td.parents('tr') )
              .remove()
              .draw();
          }
        }).
        fail(function(response){
          toastr.error('Servicio no disponible intentalo luego. ' + response.responseText, 'Error!!', {timeOut: 3000});
        });
      } else {
        return false;
      }
  });

  $("#tb_courses").on('click', '.courseshow', function (event) {
    event.preventDefault();
    _td = $(this);
    var codigo = null;
    var _data = functions.getDataTable(_td);
    var url = _td.attr('href');
    $.get(url).done(function (r) {
      $('#programCreateForm')[0].reset();
      $.each(r.courses, function (key, value) {
        if (key == 'codigo') {
          codigo = value;
          $('select#id_programa option[value='+value.toString().substr(0, 5)+']').attr('selected','selected');
          console.log(value.toString().substr(5));
          $('input[name="' + key + '"]').val(value.toString().substr(5));
        } else {
          $('input[name="' + key + '"]').val(value);
        }
        $('input[name="codigo_forma"]').val(codigo);
      });
      if (r.flag > 0) {
          console.log(r.flag);
          $("#input_codigo").prop('readonly', true);
      }
      $('#programCreateForm').attr('action', _td.attr('data-href') + _data.id);
      $('#programCreateForm').removeClass();
      $('#programCreateForm').addClass('update');
      $('#programCreateModal').modal('show');
    });
  });
  $("#id_programa").change(function(e){
    console.log('S');
    let codigo = $(this).val() + $('input[name="codigo"]').val();
    $('input[name="codigo_forma"]').val(codigo);
  });

  $('input[name="codigo"]').blur(function(e){
    let codigo =  $('#id_programa').val() + $(this).val();
    $('input[name="codigo_forma"]').val(codigo);
  });