$("form").keypress(function(e) {
  if (e.which == 13) {
      return false;
  }
});
$(".addinstitution").on('click', function (event) {
    event.preventDefault();
    $('#institutionCreateForm')[0].reset();
    $('#institutionCreateForm').attr('action', $(this).attr('data-href'));
    $('input[name="codigo"]').attr('readonly', false);
    $('#institutionCreateModal').modal('show');
  });

  $("#institutionCreateForm").on("submit", function (event) {
    event.preventDefault();
    var userForm = $(this).serialize();
    $.post($(this).attr('action'), userForm).done(function (response) {
      if (response.message == 1) {
        table.row.add(response.institution).draw(false);
        toastr.success('Accion completada correctamente.', 'Estupendo!!!', { timeOut: 3000 });
        $('#institutionCreateModal').modal('hide');
      } else if (response.message == 2) {
        console.log(_td);
        table.row(_td.parents('tr')).data(response.institution);
        toastr.success('Accion completada correctamente.', 'Estupendo!!!', { timeOut: 3000 });
        $('#institutionCreateModal').modal('hide');
      } else {
        console.log('0');
        toastr.error('No sé pudo registrar correctamente.', 'Error!!', { timeOut: 3000 });
      }

    }).
      fail(function (response) {
        toastr.error('Servicio no disponible intentalo luego.', 'Error!!', { timeOut: 3000 });
      });

  });

  $("#tb_institution").on('click', '.institutionEliminar', function(event) {
    event.preventDefault();
      var r = confirm("¿Está seguro que desea eliminar este registro de forma permanente?");
      if (r == true) {
        _td = $(this);
        var _data = functions.getDataTable(_td);
        var url = _td.attr('href');
        $.get(url).done( function(response){
        if (response == 1){
          toastr.success('Instancia eliminado correctamente.', 'Estupendo!!!', {timeOut: 3000});
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

  $("#tb_institution").on('click', '.institutionshow', function (event) {
    event.preventDefault();
    _td = $(this);
    var _data = functions.getDataTable(_td);
    var url = _td.attr('href');
    $.get(url).done(function (response) {
      $('#institutionCreateForm')[0].reset();
      $.each(response, function (key, value) {
          if (key == 'codigo') {
              $('input[name="' + key + '"]').val(value);
              $('input[name="' + key + '"]').attr('readonly', true);
          } else {
              $('input[name="' + key + '"]').val(value);
          }
      });
      $('#institutionCreateForm').attr('action', _td.attr('data-href') + _data.id);
      $('#institutionCreateForm').removeClass();
      $('#institutionCreateForm').addClass('update');
      $('#institutionCreateModal').modal('show');
    });
  });