var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
var current_page;

$(document).ready(function(){

    getList('');

    $("#search_button").click(function() {
        getList('');
        return false;
    });

});


$(document).on("click", ".pg a", function(){
    getList(this.id);
    current_page = this.id;
    return false;
});


function getList(page) {

    $(".dataTable").html('<img style="margin-top:180px;" src="../public/img/loading/loading4.gif") }}" width="50px" height="50px">');
    var form_data = {
        search_by       : $('#search_by').val(),
        search_input    : $('#search_input').val(),
        paging          : page,
        _token          : CSRF_TOKEN
    }

    $.ajax({
        // async: "false",
        url: 'kuis_list',
        type: 'GET',
        data: form_data,
        dataType: "JSON",
        success: function(data) {
            $(".dataTable").html(data.result);
            $(".pg ul").html(data.paging);
            return false;
        }
    });

}

