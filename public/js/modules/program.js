$("form").keypress(function(e) {
  if (e.which == 13) {
      return false;
  }
});
$(".addprogram").on('click', function (event) {
    $(".content_visible").show();
    event.preventDefault();
  $('#programCreateForm')[0].reset();
  $('#programCreateForm').attr('action', $(this).attr('data-href'));
  $('#programCreateModal').modal('show');
});

$("#programCreateForm").on("submit", function (event) {
  event.preventDefault();
  var userForm = $(this).serialize();
  $.post($(this).attr('action'), userForm).done(function (response) {
    if (response.message == 1) {
      table.row.add(response.program).draw(false);
      toastr.success('Accion completada correctamente.', 'Estupendo!!!', { timeOut: 3000 });
    } else if (response.message == 2) {
      console.log(_td);
      table.row(_td.parents('tr')).data(response.program);
      toastr.success('Accion completada correctamente.', 'Estupendo!!!', { timeOut: 3000 });
      $('#programCreateModal').modal('hide');
    } else {
      console.log('0');
      toastr.error('No sé pudo registrar correctamente.', 'Error!!', { timeOut: 3000 });
    }

  }).
    fail(function (response) {
      toastr.error('Servicio no disponible intentalo luego.', 'Error!!', { timeOut: 3000 });
    });

});

$("#tb_programs").on('click', '.programEliminar', function(event) {
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

$("#tb_programs").on('click', '.programshow', function (event) {
  event.preventDefault();
  _td = $(this);
  $(".content_visible").hide();
  var _data = functions.getDataTable(_td);
  var url = _td.attr('href');
  $.get(url).done(function (response) {
    $('#programCreateForm')[0].reset();
    $.each(response, function (key, value) {
      if ( key == 'codigo_institucion') {
        $('#institucion_fake').val(value);
      } else if ( key == 'codigo') {
          $('input[name="codigo"]').val(value);
          $('select#instance option[value='+value.substr(0, 1)+']').attr('selected','selected');
          $('input[name="codigo_program"]').val(value.substr(3));
      } else {
          $('input[name="'+key+'"]').val(value);
      }
    });
    $('#programCreateForm').attr('action', _td.attr('data-href') + _data.id);
    $('#programCreateForm').removeClass();
    $('#programCreateForm').addClass('update');
    $('#programCreateModal').modal('show');
  });
});
$("#codigo_institucion").change(function(e){
  console.log('S');
  $('input[name="codigo_forma"]').html("");
  let codigo = $("#instance").val() + $(this).val() + $('input[name="codigo_program"]').val();
  $('input[name="codigo_forma"]').val(codigo);
});

$("#instance").change(function(e){
    console.log('S');
    $('input[name="codigo_forma"]').html("");
    let codigo = $(this).val() + $("#codigo_institucion").val() + $('input[name="codigo_program"]').val();
    $('input[name="codigo_forma"]').val(codigo);
});

$('input[name="codigo_program"]').blur(function(e){
  $('input[name="codigo_forma"]').html("");
  let codigo =  $("#instance").val() + $('#codigo_institucion').val() + $(this).val();
  $('input[name="codigo_forma"]').val(codigo);
});

