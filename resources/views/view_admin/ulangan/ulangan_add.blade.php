@extends('templates/admin_layouts')

@section('add_bread_admin')
<li><a href="{{ URL::to('admin/ulangan') }}">Ulangan</a></li>
@stop

@section('bread_admin')
Tambah Ulangan
@stop
	
@section('content')

<link rel="stylesheet" type="text/css" href="{{ asset('public/css/apps/kuis.css') }}">

<div class="main-content">

	<!-- ///////////////////////////////////////////////////////////// Detail Ulangan ///////////////////////////////////////////////////////////// -->
 
	<div class="row row-judul">
		<div class="col-md-12">
			<legend> <h4 class="judul-soal"> Detail Ulangan </h4> </legend>
		</div>
	</div>

	<div class="row">
		<div class="col-md-12">
			<form id="add_form" class="form form-horizontal" role="form" data-toggle="validator">

				<div class="form-group">
					<label class="col-sm-2 label-modal-soal">Nama Ulangan</label>
					<div class="col-sm-5">
						<input type="text" class="form-control inform-height" id="add_nama_ulangan" placeholder="Nama Ulangan" required>
					</div>
					<div class="col-sm-5 help-block with-errors"></div>
				</div>

				<div class="form-group">
					<label class="col-sm-2 label-modal-soal">Nama Materi</label>
					<div class="col-sm-5">
						<select class="form-control" id="nama_materi" required>
							
						</select>
					</div>
					<div class="col-sm-5 help-block with-errors"></div>
				</div>

				<div class="form-group">
					<label class="col-sm-2 label-modal-soal">Mulai</label>
					<div class="col-sm-5">
						<div class="input-group date" id="datepicker_start">
							<input type="text" id="tgl_mulai" class="form-control inform-height" placeholder="Tanggal Mulai" required>
							<span class="input-group-addon">
								<i class="glyphicon glyphicon-calendar"></i>
							</span>
						</div>
					</div>
					<div class="col-sm-5 help-block with-errors"></div>
				</div>

				<div class="form-group">
					<label class="col-sm-2 label-modal-soal">Selesai</label>
					<div class="col-sm-5">
						<div class="input-group date" id="datepicker_end">
							<input type="text" id="tgl_selesai" class="form-control inform-height" placeholder="Tanggal Selesai" required>
							<span class="input-group-addon">
								<i class="glyphicon glyphicon-calendar"></i>
							</span>
						</div>
					</div>
					<div class="col-sm-5 help-block with-errors"></div>
				</div>

				<div class="form-group">
					<label class="col-sm-2 label-modal-soal">Durasi Ulangan</label>
					<div class="col-sm-5">
						<div class="input-group date" id="time_durasi">
							<input type="text" id="durasi" class="form-control inform-height" placeholder="Durasi Ulangan" required>
							<span class="input-group-addon">
								<i class="glyphicon glyphicon-time"></i>
							</span>
						</div>
					</div>
					<div class="col-sm-5 help-block with-errors"></div>
				</div>

				<input class="form-control" type="hidden" id="id_ulangan">
				<input class="form-control" type="hidden" id="id_after">
				<input class="form-control" type="hidden" id="id_before">

		</div>		
	</div>


	<!-- ///////////////////////////////////////////////////////////// Daftar Add ///////////////////////////////////////////////////////////// -->


	<div class="row row-judul">
		<div class="col-md-12">
			<legend><h4 class="judul-soal"> Daftar Soal </h4></legend>
		</div>
	</div>

	<div class="row">
		<div class="col-md-12">
			<a href="#" id="add_button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modal-soal-add"> <span class="glyphicon glyphicon-plus-sign"></span> Tambah </a>
		</div>
		<div class="col-md-12 table-responsive dataTable">
			
		</div>
	</div>

	<div class="row row-paging-table-soal">
		<div class="pg num-page">
	        <ul class="pagination pagination-sm">

	        </ul>
	    </div>
	</div>

    <div class="row">
	    <div class="form-group" style="float:right;">
	        <button type="submit" id="simpan_ulangan" class="btn btn-primary btn-sm">Submit</button>
	        <button type="reset" id="reset_add_form" class="btn btn-primary btn-sm">Reset</button>
	        <button type="submit" id="hapus_ulangan" class="btn btn-primary btn-sm">Batal</button>
	    </div>
	</div>
	</form>

	<!-- ///////////////////////////////////////////////////////////// Modal Add ///////////////////////////////////////////////////////////// -->

	<div class="modal fade" id="modal-soal-add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	    @include('view_admin.ulangan.modal_soal_add')
	</div>

	<!-- ///////////////////////////////////////////////////////////// Modal Edit ///////////////////////////////////////////////////////////// -->

	<div class="modal fade" id="modal-soal-edit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	    @include('view_admin.ulangan.modal_soal_edit')
	</div>

</div>


<script type="text/javascript" src="{{asset('public/js/apps/ulangan_add.js')}}"></script>

@stop