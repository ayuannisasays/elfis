var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
var current_page;

$(document).ready(function(){

    getMateri();
    getEditDetailKuis();

	$('#ubah_kuis').click(function() {

	    nama = $('#edit_nama_kuis').val();
	    materi = $('#edit_nama_materi').val();
	    mulai = $('#edit_tgl_mulai').val();
	    selesai = $('#edit_tgl_selesai').val();
	    durasi = $('#edit_durasi').val();

		if (nama && materi && mulai && selesai && durasi != "") {
            UpdateKuis();
			return false;
		}

	});

	$('#simpan_soal').click(function() {

		soal = $('#soal_kuis').val();
	    a = $('#jwb_a').val();
	    b = $('#jwb_b').val();
	    c = $('#jwb_c').val();
	    d = $('#jwb_d').val();
	    e = $('#jwb_e').val();
	    jawaban = $('#jawaban').val();

	    if (soal && a && b && c && d && e && jawaban != "" ) {
			AddSoal();
			return false;
	    }

	});

	$('#ubah_soal').click(function() {
		EditSoal();
		return false;
	});


	// --------------------------------- DateTimePicker ---------------------------------

    $('#time_durasi').datetimepicker({
        format: 'HH:mm:ss'
    });

    $('#datepicker_start').datetimepicker({
        format: 'DD-MM-YYYY'
    });

    $('#datepicker_end').datetimepicker({
        format: 'DD-MM-YYYY'
    });

    $("#datepicker_start").on("dp.change",function (e) {
        $('#datepicker_end').data("DateTimePicker").minDate(e.date);
    });

    $("#datepicker_end").on("dp.change",function (e) {
        $('#datepicker_start').data("DateTimePicker").maxDate(e.date);
    });


});


// ---------------------------------------------------------- Kuis Edit ----------------------------------------------------------


$(document).on("click", ".pg a", function(){
    soal_list_edit(this.id);
    current_page = this.id;
    return false;
});

function getMateri() {

	var form_data = {
		_token		: CSRF_TOKEN
	}

	$.ajax({
        url: '../../get_materi',
        type: 'GET',
        data: form_data,
        dataType: "JSON",
        success: function(data) {

        	option_materi = '';
            option_materi += '<option value=""> Nama Materi </option><br/>';
            $.each(data.data, function(i, item) {
            option_materi += '<option value="' + data.data[i].id_materi + '">' + data.data[i].nama_materi + '</option><br/>';
            });

            $('#edit_nama_materi').html(option_materi);
            return false;
        }
    });

}


function getEditDetailKuis() {
    
    var form_data = {
        id      : $('#id_kuis').val(),
        _token  : CSRF_TOKEN
    }

    $.ajax({
        url: '../../get_detail_kuis',
        type: 'GET',
        data: form_data,
        dataType: "JSON",
        success: function(data) {

            $('#id_group_kuis').val(data.data_kuis.id_group_kuis);
            $('#edit_nama_kuis').val(data.data_kuis.nama_group_kuis);
            $('#edit_nama_materi').val(data.data_kuis.id_materi);
            $('#edit_tgl_mulai').val(data.data_kuis.kuis_mulai);
            $('#edit_tgl_selesai').val(data.data_kuis.kuis_selesai);
            $('#edit_durasi').val(data.data_kuis.durasi);
            
            soal_list_edit();
            return false;
        }
    });

}


function soal_list_edit(page) {

    $(".dataTable").html('<img style="margin-top:50px; margin-bottom:50px;" src="../../../public/img/loading/loading4.gif") }}" width="50px" height="50px">');
    var form_data = {
        id_group_kuis     : $('#id_group_kuis').val(),
        paging      : page,
        _token      : CSRF_TOKEN
    }

    $.ajax({
        url: '../../soal_list_edit',
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


function refreshSoal() {

	$('#soal_kuis').val('');
    $('#jwb_a').val('');
    $('#jwb_b').val('');
    $('#jwb_c').val('');
    $('#jwb_d').val('');
    $('#jwb_e').val('');
    $('#jawaban').val('');

}


function AddSoal() {

	var form_data = {
        submit      : $('#simpan_soal').val(),
		id_soal 	: $('#id_group_kuis').val(),
    	soal_kuis 	: $('#soal_kuis').val(),
    	jwb_a 		: $('#jwb_a').val(),
    	jwb_b 		: $('#jwb_b').val(),
    	jwb_c 		: $('#jwb_c').val(),
    	jwb_d 		: $('#jwb_d').val(),
    	jwb_e 		: $('#jwb_e').val(),
    	jawaban 	: $('#jawaban').val(),
        _token      : CSRF_TOKEN
    }

    $.ajax({
        url: '../../AddSoal',
        type: 'POST',
        data: form_data,
        dataType: "JSON",
        success: function(data) {
            alert(data.pesan);
            soal_list_edit();
            refreshSoal();
        }
    });

}


function deleteSoal(id) {

	var konfirmasi = confirm($('#delete'+id).data('delete'));

	var form_data = {
		id 		: id,
        _token  : CSRF_TOKEN
    }

    if (konfirmasi == true) {

	    $.ajax({
	        url: '../../DeleteSoal',
	        type: 'POST',
	        data: form_data,
	        dataType: "JSON",
	        success: function(data) {
	            soal_list_edit();

	            return false;
	        }
	    });

    } else {
    	return false;
	}
}


function getEdit(id) {

	var form_data = {
		id 		: id,
        _token  : CSRF_TOKEN
    }

    $.ajax({
        url: '../../soal_get_edit',
        type: 'POST',
        data: form_data,
        dataType: "JSON",
        success: function(data) {

        	$('#edit_id_kuis').val(data.soal.id);
        	$('#edit_soal_kuis').val(data.soal.soal);
		    $('#edit_jwb_a').val(data.soal.pil_a);
		    $('#edit_jwb_b').val(data.soal.pil_b);
		    $('#edit_jwb_c').val(data.soal.pil_c);
		    $('#edit_jwb_d').val(data.soal.pil_d);
		    $('#edit_jwb_e').val(data.soal.pil_e);
		    $('#edit_jawaban').val(data.soal.jawaban);

            return false;
        }
    });

}


function EditSoal() {

    var form_data = {
        id 		: $('#edit_id_kuis').val(),
        soal 	: $('#edit_soal_kuis').val(),
		jwb_a 	: $('#edit_jwb_a').val(),
		jwb_b   : $('#edit_jwb_b').val(),
		jwb_c   : $('#edit_jwb_c').val(),
		jwb_d   : $('#edit_jwb_d').val(),
		jwb_e   : $('#edit_jwb_e').val(),
		jawaban : $('#edit_jawaban').val(),
        _token  : CSRF_TOKEN
    }
    
    $.ajax({
        // async: "false",
        url: '../../soal_edit',
        type: 'POST',
        data: form_data,
        dataType: "JSON",
        success: function(data) {
        	alert(data.pesan);
            soal_list_edit();
            document.getElementById('close_modal').click();
        }
    });

}


function UpdateKuis() {

	var form_data = {
        id_kuis 		        : $('#id_kuis').val(),
        edit_nama_kuis		    : $('#edit_nama_kuis').val(),
        edit_id_materi			: $('#edit_nama_materi').val(),
        edit_kuis_mulai			: $('#edit_tgl_mulai').val(),
        edit_kuis_selesai		: $('#edit_tgl_selesai').val(),
        edit_durasi 		    : $('#edit_durasi').val(),
        _token                  : CSRF_TOKEN
    }

    $.ajax({
        url: '../../UpdateKuis',
        type: 'POST',
        data: form_data,
        dataType: "JSON",
        success: function(data) {
        	alert(data.pesan);
            document.getElementById('redirect').click();
        }
    });

}


