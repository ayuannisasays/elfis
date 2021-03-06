var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
var base_url = $('#base_url').val();
var current_page;

$(document).ready(function(){

    jawaban_getList();

    $("#search_button").click(function() {
        jawaban_getList('');

        return false;
    });

});

$(document).on("click", ".pg a", function(){
    jawaban_getList(this.id);
    current_page = this.id;
    return false;
});

function jawaban_getList(page) {

    $(".dataTable").html('<img style="margin-top:180px;" src="../public/img/loading/loading4.gif") }}" width="50px" height="50px">');
    var form_data = {
        search_by       : $('#search_by').val(),
        search_input    : $('#search_input').val(),
        paging          : page,
        _token          : CSRF_TOKEN
    }

    $.ajax({
        // async: "false",
        url: 'jawaban_list',
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

function deleteData(id_jawaban_tugas) {

    var konfirmasi = confirm($('#btn_delete'+id_jawaban_tugas).data('delete'));
    var form_data = {
            id_jawaban_tugas : id_jawaban_tugas,
            _token   : CSRF_TOKEN
        }

    if(konfirmasi == true) {

        $.ajax({
            //async: "false",
            type: 'POST',
            data: form_data,
            url: 'jawaban_delete',
            dataType: "JSON",
            success: function(data) {
                alert(data.sukses);
                jawaban_getList();
                return false;
            }
        });

    } else {
        return false;
    }

}
