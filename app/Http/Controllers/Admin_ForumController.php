<?php

namespace App\Http\Controllers;

use Input;
use DB;
use Redirect;
use URL;
use Session;
use Illuminate\Http\Response;
// use Response;
use Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Admin_ForumController extends Controller {

	public $status = false;

// -------------------------------------------------------- FORUM --------------------------------------------------------

	public function forum() {
		if(session('id_group') == 3) {
				
			return view('view_admin/forum/index');

		} else {
			return redirect('login');
		}
	}


	public function forum_get_list() {
		
		if(session('id_group') == 3) {

			$search_by = trim(Input::get('search_by'));
			$search_input = trim(Input::get('search_input'));

			if(Input::get('paging') == null) {
				$nopage = 1;
	        }
	        else{
	            $nopage = Input::get('paging');
	        }


			if ($search_by != null) {
				$sql_ext = "where ".$search_by." like '%".$search_input."%'";
			} else {
				$sql_ext = "";
			}

			$data_rows = DB::select('select a.*, b.nama_jurusan from forum a join jurusan b where a.id_jurusan=b.id_jurusan '.$sql_ext);
			$total_rows = count($data_rows);

			if($total_rows < 1) {
	            $total_rows = 1;
	        }
	        $per_page = '10';
	        $total_page = ceil($total_rows / $per_page);

	        if($nopage > $total_page) {
	            $nopage = $total_page;
	        }

	        $offset = ($nopage - 1) * $per_page;

			$data_forum = DB::select('select a.*, b.nama_jurusan from forum a join jurusan b where a.id_jurusan=b.id_jurusan '.$sql_ext.' ORDER BY id_forum DESC LIMIT '.$per_page.' OFFSET '.$offset);

			$limit_start = $offset + 1;

	        $prev = $nopage - 1;
	        $next = $nopage + 1;

	        $paging = '';

	        if ($nopage > 1) $paging .= '<li><a href="#" aria-label="Previous" id="'.$prev.'"> <span aria-hidden="true">&laquo;</span> </a></li>';

	        // memunculkan nomor halaman dan linknya

	        for($page = 1; $page <= $total_page; $page++){
	            if ((($page >= $nopage - 3) && ($page <= $nopage + 3)) || ($page == 1) || ($page == $total_page)){
	                if ($page == $nopage) $paging .= '<li class="active"><a href="#">'.$page.'</a></li>';
	                else $paging .= '<li><a href="#" id="'.$page.'">'.$page.'</a></li>';
	            }
	        }

	        if ($nopage < $total_page) $paging .= '<li><a href="#" aria-label="Next" id="'.$next.'"> <span aria-hidden="true">&raquo;</span> </a></li>';

			$result = '';

			$result .= '<table class="table table-hover table-bordered table-striped">';
			$result .= '<thead class="index">';
			$result .= '<tr>';
			$result .= '<th>No</th>';
			$result .= '<th>Nama Forum</th>';
			$result .= '<th>Hak Akses</th>';
			$result .= '<th>Jurusan</th>';
			$result .= '<th>Subyek</th>';
			$result .= '<th>Keterangan</th>';
			$result .= '<th>Rating</th>';
			$result .= '<th>Diposting Oleh</th>';
			$result .= '<th><span class="glyphicon glyphicon-wrench"></span></th>';
			$result .= '<th><span class="glyphicon glyphicon-folder-open"></span></th>';
			$result .= '</tr>';
			$result .= '</thead>';
			$result .= '<tbody class="index">';

			if ($data_forum != true) {

				$result .= '<tr>';
				$result .= '<td colspan="9">No Data In Database</td>';
				$result .= '</tr>';
				$result .= '</tbody>';
				$result .= '</table>';

			} else {

				$i = $limit_start;
				foreach ($data_forum as $row => $list) {
					$list = get_object_vars($list);
					$result .= '<tr>';
					$result .= '<td class="kolom-tengah">'.$i.'</td>';
					$result .= '<td class="kolom-kiri">'.$list['nama_forum'].'</td>';
					$result .= '<td class="kolom-kiri">'.$list['role_access'].'</td>';
					$result .= '<td class="kolom-kiri">'.$list['subyek'].'</td>';
					$result .= '<td class="kolom-kiri">'.$list['keterangan'].'</td>';
					$result .= '<td class="kolom-kanan">'.$list['rate'].' <span class="glyphicon glyphicon-star"></span> </td>';
					$result .= '<td class="kolom-kiri">'.$list['forum_create_by'].'</td>';
					$result .= '<td class="kolom-tengah">
									<a class="btn btn-success btn-xs" onClick="getEdit('.$list['id_forum'].')" data-toggle="modal" data-target="#edit_forum"> <span class="glyphicon glyphicon-edit"></span> </a> 
			                		<a id="btn_delete'.$list['id_forum'].'" class="btn btn-danger btn-xs" onClick="deleteData('.$list['id_forum'].')" data-delete="Apakah anda yakin ingin menghapus forum '.$list['nama_forum'].'?"><span class="glyphicon glyphicon-trash"></span></a>
			                	</td>';
			     	$result .= '<td class="kolom-tengah"><a class="btn btn-xs btn-warning" href="/elfis/admin/forum/'.$list['nama_forum'].'/'.$list['id_forum'].'">
									<span class="glyphicon glyphicon-new-window"></span></a>
								</td>';
					$result .= '</tr>';
					$i++;
				}

				$result .= '</tbody';
				$result .= '</table>';

			}

			//return Response()->json(array('result' => $result));
			$response = array (
	            'result' => $result,
	            'paging' => $paging
	        );

	        echo json_encode($response);

		} else {
			return redirect('login');
		}
	}


	public function forum_add() {

		if(session('id_group') == 3) {

			$nama_forum = Input::get('add_nama_forum');
			$role_access = Input::get('add_role_access');
			$subyek = Input::get('add_subyek');
			$keterangan = Input::get('add_keterangan');
			$isi = Input::get('add_isi');

			$data_add = DB::insert('insert into forum values ("", "'.$nama_forum.'", "'.$role_access.'", "'.$subyek.'", "'.$keterangan.'", "'.$isi.'", 0, "'.date('Y-m-d H:i:s').'", "'.session('username').'")');

			$this->json['sukses'] = 'Forum berhasil dibuat';
			echo json_encode($this->json);

			// $message = "Data Telah Ditambah";
			// $response = array (
	  		//           'pesan' => $message
	  		// );
	  		// echo json_encode($response);

			// return ['key' => 'Data berhasil masuk ke database'];

		} else {
			return redirect('login');
		}

	}


	public function forum_delete() {

		if(session('id_group') == 3) {

			$id_forum = trim(Input::get('id_forum'));
				
			$data_delete = DB::delete('delete from forum where id_forum = "'.$id_forum.'"');

			$this->json['sukses'] = 'Forum berhasil dihapus';
			echo json_encode($this->json);

		} else {
			return redirect('login');
		}
	
	}


	public function forum_get_edit() {

		if(session('id_group') == 3) {

			$id_forum = trim(Input::get('id_forum'));
				
			$data_edit = DB::select('select * from forum where id_forum = '.$id_forum.'');

			foreach ($data_edit as $list => $row) {
				$row = get_object_vars($row);
				$data_row = [

					'id_forum'		=>	$row['id_forum'],
					'nama_forum'	=>	$row['nama_forum'],
					'role_access'	=>	$row['role_access'],
					'subyek'		=>	$row['subyek'],
					'keterangan'	=>	$row['keterangan'],
					'isi'			=>	$row['isi']

					];
			}

			$response = array (
	            'data' => $data_row
	        );

			echo json_encode($response);

		} else {
			return redirect('login');
		}

	}


	function forum_edit() {

		if(session('id_group') == 3) {

			$id_forum = Input::get('id_forum');
			$nama_forum = Input::get('edit_nama_forum');
			$role_access = Input::get('edit_role_access');
			$subyek = Input::get('edit_subyek');
			$keterangan = Input::get('edit_keterangan');
			$isi = Input::get('edit_isi');

			$data_update =  DB::update('update forum set nama_forum = "'.$nama_forum.'", role_access = "'.$role_access.'", subyek = "'.$subyek.'", keterangan = "'.$keterangan.'", isi = "'.$isi.'", forum_create = "'.date('Y-m-d H:i:s').'", forum_create_by = "'.session('username').'" where id_forum = '.$id_forum);

			$this->json['sukses'] = 'Forum berhasil diubah';
			echo json_encode($this->json);

		} else {
			return redirect('login');
		}

	}


// -------------------------------------------------------- ISI FORUM --------------------------------------------------------

	public function forum_isi($nama_forum, $id_forum) {
		
		if(session('id_group') == 3) {
			return view('view_admin/forum/forum')->with('id_forum', $id_forum)->with('nama_forum', $nama_forum);
		}
		else {
			return redirect('login');
		}

	}


	public function forum_add_star() {

		if(session('id_group') == 3) {
			
			$id_forum = Input::get('id_forum');

			$rate_forum = DB::select('select rate from forum where id_forum = '.$id_forum);

			foreach ($rate_forum as $key => $value) {
				$value = get_object_vars($value);
				$rate = $value['rate'];
			}

			$add_star = DB::update('update forum set rate = '.$rate.'+1 where id_forum = '.$id_forum);
			$add_star_param = DB::insert('insert into star_forum_komentar values("", "'.$id_forum.'", "", "'.session('id_user').'")');

			$this->json['sukses'] = 'Berhasil menambahkan rating forum';
			echo json_encode($this->json);

		}
		else {
			return redirect('login');
		}

	}


	public function forum_remove_star() {

		if(session('id_group') == 3) {
			
			$id_forum = Input::get('id_forum');
			$id_user = Input::get('id_user');

			$rate_forum = DB::select('select rate from forum where id_forum = '.$id_forum);

			foreach ($rate_forum as $key => $value) {
				$value = get_object_vars($value);
				$rate = $value['rate'];
			}

			$min_star = DB::update('update forum set rate = '.$rate.'-1 where id_forum = '.$id_forum);
			$min_star_param = DB::delete('delete from star_forum_komentar where id_forum="'.$id_forum.'" and id_user="'.$id_user.'"');

			$this->json['sukses'] = 'Berhasil mengurangi rating forum';
			echo json_encode($this->json);

		}
		else {
			return redirect('login');
		}

	}


	public function komentar_add_star() {

		if(session('id_group') == 3) {
			
			$id_komentar = Input::get('id_komentar');

			$rate_komentar = DB::select('select rate_komentar from komentar where id_komentar = '.$id_komentar);

			foreach ($rate_komentar as $key => $value) {
				$value = get_object_vars($value);
				$rate = $value['rate_komentar'];
			}

			$add_star = DB::update('update komentar set rate_komentar = '.$rate.'+1 where id_komentar = '.$id_komentar);
			$add_star_param = DB::insert('insert into star_forum_komentar values("", "", "'.$id_komentar.'", "'.session('id_user').'")');

			$this->json['data'] = $id_komentar;
			echo json_encode($this->json);

		}
		else {
			return redirect('login');
		}

	}


	public function komentar_remove_star() {

		if(session('id_group') == 3) {
			
			$id_komentar = Input::get('id_komentar');
			$id_user = Input::get('id_user');

			$rate_komentar = DB::select('select rate_komentar from komentar where id_komentar = '.$id_komentar);

			foreach ($rate_komentar as $key => $value) {
				$value = get_object_vars($value);
				$rate = $value['rate_komentar'];
			}

			$min_star = DB::update('update komentar set rate_komentar = '.$rate.'-1 where id_komentar = '.$id_komentar);
			$min_star_param = DB::delete('delete from star_forum_komentar where id_komentar="'.$id_komentar.'" and id_user="'.$id_user.'"');

			$this->json['sukses'] = 'Berhasil mengurangi rating komentar';
			echo json_encode($this->json);

		}
		else {
			return redirect('login');
		}

	}


	public function komentar_add() {

		if(session('id_group') == 3) {
			
			$id_forum = Input::get('id_forum');
			$isi_komentar = Input::get('add_isi');

			$add_komentar = DB::insert('insert into komentar values ("", "'.$id_forum.'", "'.$isi_komentar.'", 0, "'.date('Y-m-d H:i:s').'", "'.session('username').'")');

			$this->json['sukses'] = 'Berhasil Menambahkan Komentar';
			echo json_encode($this->json);

		}
		else {
			return redirect('login');
		}

	}


	public function komentar_delete() {

		if(session('id_group') == 3) {
			
			$id_komentar = Input::get('id_komentar');

			$delete_komentar = DB::delete('delete from komentar where id_komentar = '.$id_komentar);

			$this->json['sukses'] = 'Berhasil Menghapus Komentar';
			echo json_encode($this->json);

		}
		else {
			return redirect('login');
		}

	}


	public function komentar_get_edit() {

		if(session('id_group') == 3) {

			$id_komentar = trim(Input::get('id_komentar'));
				
			$data_edit = DB::select('select * from komentar where id_komentar = '.$id_komentar.'');

			foreach ($data_edit as $list => $row) {
				$row = get_object_vars($row);
				$data_row = [

					'id_komentar'	=>	$row['id_komentar'],
					'isi_komentar'	=>	$row['isi_komentar']

					];
			}

			$response = array (
	            'data' => $data_row
	        );

			echo json_encode($response);

		} else {
			return redirect('login');
		}

	}


	public function komentar_edit() {

		if(session('id_group') == 3) {

			$id_komentar = trim(Input::get('id_komentar'));
			$isi_komentar = Input::get('isi_komentar');
				
			$komentar_update = DB::update('update komentar set isi_komentar = "'.$isi_komentar.'" where id_komentar = '.$id_komentar.'');

			$this->json['sukses'] = 'Berhasil Mengubah Komentar';
			echo json_encode($this->json);

		} else {
			return redirect('login');
		}

	}


	public function forum_isi_get_edit() {

		if(session('id_group') == 3) {

			$id_forum = trim(Input::get('id_forum'));
				
			$data_edit = DB::select('select * from forum where id_forum = '.$id_forum.'');

			foreach ($data_edit as $list => $row) {
				$row = get_object_vars($row);
				$data_row = [

					'id_forum'	=>	$row['id_forum'],
					'isi'		=>	$row['isi']

					];
			}

			$response = array (
	            'data' => $data_row
	        );

			echo json_encode($response);

		} else {
			return redirect('login');
		}

	}


	public function forum_isi_edit() {

		if(session('id_group') == 3) {

			$id_forum = trim(Input::get('id_forum'));
			$isi_forum = Input::get('isi_forum');
				
			$forum_update = DB::update('update forum set isi = "'.$isi_forum.'" where id_forum = '.$id_forum.'');

			$this->json['sukses'] = 'Berhasil Mengubah Isi Forum';
			echo json_encode($this->json);

		} else {
			return redirect('login');
		}

	}


	public function forum_isi_set_data() {

		if(session('id_group') == 3) {

			$id_forum = Input::get('id_forum');
			$value_tab = Input::get('value_tab');

			if(Input::get('paging') == null) {
				$nopage = 1;
	        }
	        else{
	            $nopage = Input::get('paging');
	        }

	        $set_data_head = DB::select('select * from forum where id_forum = '.$id_forum);

			$data_rows = DB::select('select * from komentar where id_forum = '.$id_forum);
			$total_komentar = count($data_rows);
			$total_rows = count($data_rows);

			if($total_rows < 1) {
	            $total_rows = 1;
	        }
	        $per_page = '10';
	        $total_page = ceil($total_rows / $per_page);

	        if($nopage > $total_page) {
	            $nopage = $total_page;
	        }

	        $offset = ($nopage - 1) * $per_page;

	        if ($value_tab == "tab_newer") {
	        	$data_komentar = DB::select('select * from komentar where id_forum = '.$id_forum.' ORDER BY id_komentar DESC LIMIT '.$per_page.' OFFSET '.$offset);
	        } elseif ($value_tab == "tab_votes") {
	        	$data_komentar = DB::select('select * from komentar where id_forum = '.$id_forum.' ORDER BY rate_komentar DESC LIMIT '.$per_page.' OFFSET '.$offset);
	        } else {
	        	$data_komentar = DB::select('select * from komentar where id_forum = '.$id_forum.' ORDER BY id_komentar ASC LIMIT '.$per_page.' OFFSET '.$offset);
	        }

			$limit_start = $offset + 1;

	        $prev = $nopage - 1;
	        $next = $nopage + 1;

	        $paging = '';

	        if ($nopage > 1) $paging .= '<li><a href="#" aria-label="Previous" id="'.$prev.'"> <span aria-hidden="true">&laquo;</span> </a></li>';

	        // memunculkan nomor halaman dan linknya

	        for($page = 1; $page <= $total_page; $page++){
	            if ((($page >= $nopage - 3) && ($page <= $nopage + 3)) || ($page == 1) || ($page == $total_page)){
	                if ($page == $nopage) $paging .= '<li class="active"><a href="#">'.$page.'</a></li>';
	                else $paging .= '<li><a href="#" id="'.$page.'">'.$page.'</a></li>';
	            }
	        }

	        if ($nopage < $total_page) $paging .= '<li><a href="#" aria-label="Next" id="'.$next.'"> <span aria-hidden="true">&raquo;</span> </a></li>';

			$result_head = '';
			$result_head .= '<table class="table table-header-forum">';
			$result_head .= '<tr class="forum-kepala">';

			foreach ($set_data_head as $row => $list) {
				$list = get_object_vars($list);

				$cek_star_forum = DB::select('select * from star_forum_komentar where id_forum='.$list['id_forum'].' and id_user='.session('id_user'));

				$tanggal 	= date('l, j F Y', strtotime($list['forum_create']));
				$pukul 		= date('g:i A', strtotime($list['forum_create']));

				$result_head .= '<td>';
				$result_head .= '<p class="header-pembuat">'.$list['forum_create_by'].'</p>';
				$result_head .= '<p class="sub-header-buat">'.$tanggal.' | '.$pukul.'</p>';
				$result_head .= '</td>';
				$result_head .= '<td class="header-right">';

				if ($cek_star_forum != null) {
					$result_head .= '<p class="sub-header-buat">Rating '.$list['rate'].' <span class="glyphicon glyphicon-star"></span> &nbsp|&nbsp <a href="#" onClick="RemoveStarForum('.$list['id_forum'].', '.session('id_user').')" class="rating"> <span class="glyphicon glyphicon-thumbs-down"></span> </a></p>';
				} else {
					$result_head .= '<p class="sub-header-buat">Rating '.$list['rate'].' <span class="glyphicon glyphicon-star"></span> &nbsp|&nbsp <a href="#" onClick="AddStarForum('.$list['id_forum'].')" class="rating"> <span class="glyphicon glyphicon-thumbs-up"></span> </a></p>';	
				}

				$result_head .= '<a href="#" class="rating" onClick="getEditForum('.$list['id_forum'].')" data-toggle="modal" data-target="#edit_forum">Edit</a>';
				$result_head .= '</td>';
				$result_head .= '</tr>';
				$result_head .= '<tr class="forum">';
				$result_head .= '<td colspan="2"> '.$list['isi'].' </td>';
			}

			$result_head .= '</tr>';
			$result_head .= '</table>';


			$result = '';
			$result .= '<table class="table table-komentar-forum">';

			if ($data_komentar != true) {

				$result .= '<h3 class="kosong"> Belum Ada Komentar </h3>';
				$result .= '</table>';

			} else {

				$i = $limit_start;
				foreach ($data_komentar as $row => $data) {
					$data = get_object_vars($data);

					$cek_star_komentar = DB::select('select * from star_forum_komentar where id_komentar='.$data['id_komentar'].' and id_user='.session('id_user'));
					
					$tanggal_komentar 	= date('l, j F Y', strtotime($data['komentar_create']));
					$pukul_komentar		= date('g:i A', strtotime($data['komentar_create']));

					$result .= '<tr class="forum-komentar">';
					$result .= '<td>';
					$result .= '<p class="header-pembuat">#'.$i.' | '.$data['komentar_create_by'].'</p>';
					$result .= '<p class="sub-header-buat">'.$tanggal_komentar.' | '.$pukul_komentar.'</p>';
					$result .= '</td>';
					$result .= '<td class="header-right">';

					if ($cek_star_komentar != null) {
						$result .= '<p class="sub-header-buat">Rating '.$data['rate_komentar'].' <span class="glyphicon glyphicon-star"></span> &nbsp|&nbsp <a href="#" onClick="RemoveStarKomentar('.$data['id_komentar'].', '.session('id_user').')" class="rating"> <span class="glyphicon glyphicon-thumbs-down"></span> </a></p>';
					} else {
						$result .= '<p class="sub-header-buat">Rating '.$data['rate_komentar'].' <span class="glyphicon glyphicon-star"></span> &nbsp|&nbsp <a href="#" id="star_komentar'.$data['id_komentar'].'" onClick="AddStarKomentar('.$data['id_komentar'].')" class="rating"> <span class="glyphicon glyphicon-thumbs-up"></span> </a></p>';	
					}
					
					$result .= '<a href="#" id="btn_delete'.$data['id_komentar'].'" onClick="deleteKomentar('.$data['id_komentar'].')" data-delete="Apakah anda yakin ingin menghapus komentar ini?" class="rating">Delete</a> | <a href="#" class="rating" onClick="getEditKomentar('.$data['id_komentar'].')" data-toggle="modal" data-target="#edit_comment">Edit</a>';
					$result .= '</td>';
					$result .= '</tr>';

					$result .= '<tr class="forum">';
					$result .= '<td colspan="2">'.$data['isi_komentar'].'</td>';
					$result .= '</tr>';
					$i++;

				}

				$result .= '</table>';

			}

			//return Response()->json(array('result' => $result));
			$response = array (
	            'result_head' => $result_head,
	            'result' => $result,
	            'paging' => $paging,
	            'jml' => $total_komentar
	        );

	        echo json_encode($response);


		}
		else {
			return redirect('login');
		}

	}


}