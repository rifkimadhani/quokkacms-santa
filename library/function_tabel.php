<?php
function buka_tabel($judul){
	echo '<div class="table-responsive">
	<table class="table-data table table-striped" width="100%">
	<thead>
		<tr >
			<th style="width:10px">No</th>';
	foreach ($judul as $jdl) {
		# code...
		echo '<th style="text-align: center">'.$jdl.'</th>';
	}

	echo '<th style="width: 60px">Aksi</th>
		</tr>
	</thead>
		<tbody>';
}

function buka_tabel_detail_comment($judul){
	echo '<div class="table-responsive">
	<table class="table-data table table-striped" width="100%">
	<thead>
		<tr >
			<th style="width:10px">No</th>';
	foreach ($judul as $jdl) {
		# code...
		echo '<th style="text-align: center">'.$jdl.'</th>';
	}

	echo '
		</tr>
	</thead>
		<tbody>';
}

function isi_tabel_comment_detail($no, $data, $link, $id, $lihat=false , $block=false){
    echo '<tr style="text-align: center"><td valign="top"> '.$no.'</td>';
    foreach($data as $dt){
        echo '<td valign="top">'.$dt.'</td>';

    }
    echo '<td valign="top">';
    if($lihat){
        echo'<a href="'.$link.'&show=lihat&id='.$id.'" class="tb btn-primary btn-sm" title="LIHAT">
		<i class="glyphicon glyphicon-zoom-in"></i></a>';
    }
    if($block){
        echo'&nbsp <a href="'.$link.'&show=block&id='.$id.'" class="tb btn-danger btn-sm" title="BLOCk">
		<i class="glyphicon glyphicon-remove"></i></a>';
    }
    echo '</td></tr>';
}

function isi_tabel_comment($no, $data, $link, $id, $lihat=true , $block=true){
    echo '<tr style="text-align: center"><td valign="top"> '.$no.'</td>';
    foreach($data as $dt){
        echo '<td valign="top">'.$dt.'</td>';

    }
    echo '<td valign="top">';
    if($lihat){
        echo'<a href="'.$link.'&show=lihat&id='.$id.'" class="tb btn-primary btn-sm">
		<i class="glyphicon glyphicon-zoom-in"></i></a>';
    }
    if($block){
        echo'&nbsp <a href="'.$link.'&show=block&id='.$id.'" class="tb btn-danger btn-sm">
		<i class="glyphicon glyphicon-remove"></i></a>';
    }
    echo '</td></tr>';
}

function isi_tabel($no, $data, $link, $id, $edit=true , $hapus=true){
	echo '<tr style="text-align: center"><td valign="top"> '.$no.'</td>';
	foreach($data as $dt){
		echo '<td valign="top">'.$dt.'</td>';

	}
	echo '<td valign="top">';
	if($edit){
		echo'<a href="'.$link.'&show=form&id='.$id.'" class="tb btn-primary btn-sm">
		<i class="glyphicon glyphicon-pencil"></i></a>';
	}
	if($hapus){
		echo'&nbsp <a href="'.$link.'&show=delete&id='.$id.'" class="tb btn-danger btn-sm">
		<i class="glyphicon glyphicon-trash"></i></a>';
	}
	echo '</td></tr>';
}
function isi_tabel_epg($no, $data, $link, $idkey, $id, $date, $edit=true , $hapus=true){
	echo '<tr><td valign="top"> '.$no.'</td>';
	foreach($data as $dt){
		echo '<td valign="top">'.$dt.'</td>';

	}
	echo '<td valign="top">';
	if($edit){
		echo'<a href="'.$link.'&show=form&ide='.$idkey.'&id='.$id.'&date='.$date.'" class="tb btn-primary btn-sm" ">
		<i class="glyphicon glyphicon-pencil"></i></a>';
	}
	if($hapus){
		echo'&nbsp <a href="'.$link.'&show=delete&ide='.$idkey.'&id='.$id.'&date='.$date.'" class="tb btn-danger btn-sm">
		<i class="glyphicon glyphicon-trash"></i></a>';
	}
	echo '</td></tr>';
}
function isi_tabel_epg_tambah($no, $data, $link, $idkey, $id, $date, $edit=true , $hapus=true){
	echo '<tr><td valign="top"> '.$no.'</td>';
	foreach($data as $dt){
		echo '<td valign="top">'.$dt.'</td>';

	}
	echo '<td valign="top">';
	if($edit){
		echo'<a href="'.$link.'&show=formtambah&ide='.$idkey.'&id='.$id.'&date='.$date.'" class="tb btn-primary btn-sm" ">
		<i class="glyphicon glyphicon-pencil"></i></a>';
	}
	if($hapus){
		echo'&nbsp <a href="'.$link.'&show=delete&ide='.$idkey.'&id='.$id.'&date='.$date.'" class="tb btn-danger btn-sm">
		<i class="glyphicon glyphicon-trash"></i></a>';
	}
	echo '</td></tr>';
}

function isi_tabel_ubah_epg($no, $data, $link, $id, $date, $edit=true , $hapus=true){
	echo '<tr><td valign="top"> '.$no.'</td>';
	foreach($data as $dt){
		echo '<td valign="top">'.$dt.'</td>';

	}
	echo '<td valign="top">';
	if($edit){
		echo'<a href="'.$link.'&show=formtambah&id='.$id.'&date='.$date.'" class="tb btn-primary btn-sm">
		<i class="glyphicon glyphicon-pencil"></i></a>';
	}
	if($hapus){
		echo'&nbsp <a href="'.$link.'&show=deletedata&id='.$id.'&date='.$date.'" class="tb btn-danger btn-sm">
		<i class="glyphicon glyphicon-trash"></i></a>';
	}
	echo '</td></tr>';
}
function tutup_tabel(){
	echo '</tbody>
	</table>
	</div>';
}
?>