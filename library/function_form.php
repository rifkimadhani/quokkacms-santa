<?php
function buka_form($link, $id, $aksi){
	echo'<form method="post" action="'.$link.'&show=action" class="form-horizontal" enctype="multipart/form-data">
			<input type="hidden" name="id" value="'.$id.'">
				<input type="hidden" name="aksi" value="'.$aksi.'">';
}
function tutup_form($link){
	echo '<div class="form-group">
			<div class="col-sm-offset-2 col-sm-10">
				<button type="submit" class="btn btn-primary"><i class="glyphicon glyphicon-floppy-disk"></i> Simpan</button>
					<a class="btn btn-warning" href="'.$link.'">
						<i class="glyphicon glyphicon-arrow-left"></i> Batal</a>
					</div>
				</div>
		</form>' ;
}
function buat_textbox($label, $nama, $nilai, $lebar='4', $type='text'){
echo '<div class="form-group" id="'.$nama.'"> 
		<label for="'.$nama.'" class="col-sm-2 control-label">'.$label.'</label>
			<div class="col-sm-'.$lebar.'">
				<input type="'.$type.'" class="form-control" name="'.$nama.'" value="'.$nilai.'" required>
			</div>
		</div>';
}
function buat_textarea($label, $nama, $nilai, $class=''){
echo '<div class="form-group" id="'.$nama.'"> 
		<label for="'.$nama.'" class="col-sm-2 control-label">'.$label.'</label>
			<div class="col-sm-9">
				<textarea  class="form-control '.$class.'" name="'.$nama.'" rows="8"> '.$nilai.'</textarea>
			</div>
		</div>';
}
function buat_combobox($label, $nama, $list, $nilai, $lebar='4'){
echo '<div class="form-group" id="'.$nama.'"> 
		<label for="'.$nama.'" class="col-sm-2 control-label">'.$label.'</label>
			<div class="col-sm-'.$lebar.'">
				<select class="form-control" name="'.$nama.'">';
					foreach ($list as $ls ) {
						# code...
						$select = $ls['val'] == $nilai ? 'selected' : '';
						echo '<option value='.$ls['val'].' '.$select.'>'.$ls['cap'].'</option>';

					}

				echo '</select>
			</div>
		</div>';
	}

function buat_checkbox($label, $nama, $list){
echo '<div class="form-group" id="'.$nama.'"> 
		<label for="'.$nama.'" class="col-sm-2 control-label">'.$label.'</label>
			<div class="col-sm-10">';
					foreach ($list as $ls ) {
						# code...
						echo '<input type="checkbox" name="'.$nama.'[]" value="'.$ls['val'].'" '.$ls['check'].'>'.$ls['cap'].'&nbsp';

					}

	 echo '</div>
		</div>';
	}
function buat_radio($label, $nama, $list){
echo '<div class="form-group" id="'.$nama.'"> 
		<label for="'.$nama.'" class="col-sm-2 control-label">'.$label.'</label>
			<div class="col-sm-10">';
					foreach ($list as $ls ) {
						# code...
						echo '<label for="'.$nama.$ls['val'].'" id="label_'.$nama.$ls['val'].'"><input type="radio" name="'.$nama.'" id="'.$nama.$ls['val'].'" value="'.$ls['val'].'"'.$ls['check'].'>'.$ls['cap'].'</label>';
						

					}

	 echo '</div>
		</div>';
	}

function buat_tombol($name, $table, $idtable,$icon, $link, $warna){
		global $mysqli;
		$query = $mysqli->prepare("SELECT * FROM $table");
		$query->execute();
		$query->store_result();
		$jml_data = $query->num_rows();
		$query->close();
		echo '<div class="col-md-6 col-xs-12"><a href ="'.$link.'">
				<div class="panel panel-'.$warna.' dashboard-panel" style="border-width: 5px; border-color: black;">
					<div class="panel-heading" style="background-color: Ghostwhite">
						<h3><i class="glyphicon glyphicon-'.$icon.'"></i> '.$name.'
						<span class="pull-right">'.$jml_data.'</span></h3>
					</div>
					<div class="panel-body" style="background-color: Ghostwhite;height: 200px"><b>Data Terakhir yang di Tambah</b><br/>
					<table class ="table table-responsive">
						<thead>
						<tr>
						<td align="center">Data ID</td>
						<td align="center">Title</td>
						</tr>
						</thead>
							<tbody>
							<tr>';
		$query1 = $mysqli->prepare("SELECT * FROM $table ORDER BY $idtable DESC LIMIT 1");
		$query1->execute();
		$result = $query1->get_result();
		while($data = $result->fetch_array()){
			echo '<td align="center">'.substr($data[0],0,40).'</td>';
			echo '<td align="center">'.$data[1].'</td>';
		}
		$query1->close();
		echo '</tr>
				</tbody>
					</table>
					</div>
				</div>
				</a></div>';
	}
	
function buat_imagepicker($label, $nama, $nilai, $lebar='4'){
		?>
	<script type="text/javascript">
	$(function(){
		$('#modal- <?php echo $nama; ?>').on('hidden.bs.modal', function (e){
			var url = $('#<?php echo $nama; ?>').val();
			if(url != "") $('.tampil-<?php echo $nama; ?>').html('<img src="'+url+'" width="150" style="margin-bottom: 10px">');
		})
	});
	</script>
<?php 
	echo '<div class="form-group imagepicker">
			<label for="'.$nama.'" class="col-sm-2 control-label">'.$label.'</label>
				<div class="col-sm-'.$lebar.'">
					<div class="tampil-'.$nama.'">';
	if($nilai != "") echo'<img src="'.$nilai.'" width="150" style="margin-bottom :10px">';
			  echo '</div>
				<div class="input-group">
			        <input type="text" class="form-control input-'.$nama.'" id="'.$nama.'"  name="'.$nama.'"  value="'.$nilai.'">
	
		<a data-toggle="modal" data-target="#modal-'.$nama.'" class="input-group-addon btn btn-primary pilih-'.$nama.'">...</a>
			     </div>
			     </div>

			<div class="modal fade" id="modal-'.$nama.'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true"> &times;</span></button>
				<h4 class="modal-title" id="myModalLabel"> <b>File Manager</b></h4>
				</div>
				<div class="modal-body">
			<iframe src="../plugin/filemanager/dialog.php?type=1&field_id='.$nama.'&relative_url=0" width="100%" height="400" style="border: 0"></iframe>
			</div>
		</div>
	</div>
	</div>
	</div>';

}

function buat_videopicker($label, $nama, $nilai, $lebar='4'){
		?>
	<script type="text/javascript">
	$(function(){
		$('#modal- <?php echo $nama; ?>').on('hidden.bs.modal', function (e){
			var url = $('#<?php echo $nama; ?>').val();
			if(url != "") $('.tampil-<?php echo $nama; ?>').html('<video width="100%" height="auto"><source src="'+url+'" type="video/mp4" /></video>');
		})
	});
	</script>
<?php 
	echo '<div class="form-group imagepicker">
			<label for="'.$nama.'" class="col-sm-2 control-label">'.$label.'</label>
				<div class="col-sm-'.$lebar.'">
					<div class="tampil-'.$nama.'">';
	if($nilai != "") {
        $youtubes = str_replace('http://www.youtube.com/watch?v=', '', $nilai);
        $youtubes = str_replace('https://www.youtube.com/watch?v=', '', $youtubes);
        echo '<embed width="320" height="240" src="https://www.youtube.com/v/' . $youtubes . '" type="application/x-shockwave-flash"></embed>';
    }
			  echo '</div>
				<div class="input-group">
			        <input type="text" class="form-control input-'.$nama.'" id="'.$nama.'"  name="'.$nama.'"  value="'.$nilai.'">
	
		<a data-toggle="modal" data-target="#modal-'.$nama.'" class="input-group-addon btn btn-primary  pilih-'.$nama.'">...</a>
			     </div>
			     </div>

			<div class="modal fade" id="modal-'.$nama.'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true"> &times;</span></button>
				<h4 class="modal-title" id="myModalLabel"> <b>File Manager</b></h4>
				</div>
				<div class="modal-body">
			<iframe src="../plugin/filemanager/dialog.php?type=3&field_id='.$nama.'&relative_url=0" width="100%" height="400" style="border: 0"></iframe>
			</div>
		</div>
	</div>
	</div>
	</div>';

}

function buat_xmlpicker($label, $nama, $nilai, $lebar='4'){
    ?>
    <script type="text/javascript">
        $(function(){
            $('#modal- <?php echo $nama; ?>').on('hidden.bs.modal', function (e){
                var url = $('#<?php echo $nama; ?>').val();
                if(url != "") $('.tampil-<?php echo $nama; ?>').html('<video width="100%" height="auto"><source src="'+url+'" type="video/mp4" /></video>');
            })
        });
    </script>
    <?php
    echo '
<div class="form-group imagepicker">
	<div class="input-group">
	    <a data-toggle="modal" data-target="#modal-'.$nama.'" class="input-group-addon btn btn-primary  pilih-'.$nama.'">KLIK</a>
		<input disabled type="text" class="form-control input-'.$nama.'" id="'.$nama.'"  name="'.$nama.'"  value="'.$nilai.'" placeholder="Manage File XML">
    </div>

    <div class="modal fade" id="modal-'.$nama.'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    	<div class="modal-dialog modal-lg" role="document">
    		<div class="modal-content">
    			<div class="modal-header">
    				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
    					<span aria-hidden="true"> &times;</span>
    				</button>
					<h4 class="modal-title" id="myModalLabel"> 
						<b>
						File Manager
						</b>
					</h4>
				</div>
				<div class="modal-body">
					<iframe src="../plugin/filemanager/dialog.php?type=0&field_id='.$nama.'&relative_url=0" width="100%" height="400" style="border: 0">
					</iframe>
				</div>
			</div>
		</div>
	</div>
</div>';

}
?>