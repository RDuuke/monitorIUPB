$(".addmonitor").on('click', function (event) {
    event.preventDefault();
    /*$('#programCreateForm')[0].reset();
    $('#programCreateForm').attr('action', $(this).attr('data-href'));
    $("#input_codigo").prop('readonly', false);*/
    $("#formMonitor")[0].reset();
    $("input[name=url]").removeAttr("readonly");
    $("#formMonitor").attr("action", $(this).attr("data-href"));
    $('#monitroCreateModal').modal('show');
});

$("#monitor").on("click", ".deleteMonitor", function (e) {
   e.preventDefault();
   let _this = $(this);
   var url = _this.attr("href");
   $.get(url).done( function (response) {
      if (response.codigo == 1) {
          toastr.success(response.message, 'Estupendo!!!', {timeOut: 3000});
          table
              .row( _this.parents('tr') )
              .remove()
              .draw();
      } else {
          toastr.error(response.message, 'Error!!', {timeOut: 3000});
      }
   });
});

$("#monitor").on("click", ".showMonitor", function (e) {
    e.preventDefault();
    let _this = $(this);
    var url = _this.attr("href");
    $("#formMonitor")[0].reset();
    $("#formMonitor").attr("action", _this.attr("data-href"));
    $.get(url).done( function (response) {
        $.each(response.data, function (key, value) {
            if (key == "type") {
                $("select#type option[value='"+ value +"']").attr("selected", "selected");
            } else if (key == "url") {
                $("input[name="+key+"]").val(value)
                $("input[name="+key+"]").attr("readonly", true);
            } else {
                $("input[name="+key+"]").val(value);
            }
        });
        $('#monitroCreateModal').modal('show');
    });
});

$("#monitor").on("click", ".pauseMonitor", function (e) {
    e.preventDefault();
    let _this = $(this);
    var url = _this.attr("href");
    var i = _this.children();
    $.get(url).done( function (response) {
        if (response.codigo == 1) {
            toastr.success(response.message, 'Estupendo!!!', {timeOut: 3000});
            i.toggleClass("fa-pause-circle").toggleClass("fa-play-circle");
        } else {
            toastr.error(response.message, 'Error!!', {timeOut: 3000});
        }
    });
});

$("#monitor").on("click", ".resetMonitor", function (e) {
    e.preventDefault();
    let _this = $(this);
    var url = _this.attr("href");
    $.get(url).done( function (response) {
        if (response.codigo == 1) {
            toastr.success(response.message, 'Estupendo!!!', {timeOut: 3000});
        } else {
            toastr.error(response.message, 'Error!!', {timeOut: 3000});
        }
    });
});