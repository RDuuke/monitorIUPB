
$("#box-search").on("click", async function(e){
    e.preventDefault();
    let param = $(".box-search").val();
    let element = $(".list-group");
    let route = $(this).attr('route-get');
    let route_item = $(this).attr('base-route-item');
    let type = $(this).attr("tipo");
    if (param == '') {
        element.html('');
        return true;
    }
    //console.log(result);
    element.html("");
    functions.searchRender(route + param, element);
    //console.log(result);
    $("#load").removeClass('none').addClass('block');
    //let html = functions.render("", result, route_item, type);
    // console.log(html);
    //$("#table_result tbody").append(html);
    $("#load").removeClass('block').addClass('none');
    $("#table_result").removeClass("none");
});
