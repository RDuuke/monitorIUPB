$("#stats-register").on("submit", function (event) {

    event.preventDefault();
    var _this = $(this);

    var posting = $.post(_this.attr("method"), _this.serialize());
    posting.done(function (data) {
        $("#content-filter").html(data);
    })

});