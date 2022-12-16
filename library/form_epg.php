<?php
function form_epg($start,$end, $program, $sinopsis,$id,$channelId, $date,$link){
	echo '<div class="col-md-4">';
			echo '<div class="panel panel-primary" style="position: fixed;bottom : 30px;" >
							   <div class="panel-heading" >
								  <h3 class="panel-title">Data</h3>
							   </div>
							   <div class="panel-body">
					<form class="form-horizontal" method="POST" action="'.$link.'&show=form&id='.$channelId.'&date='.$date.'">
					<input type="hidden" name="ide" value="'.$id.'">
					  <div class="form-group">
						<label for="inputEmail3" class="col-sm-2 control-label">Start</label>
						<div class="col-sm-10">
						  <input type="text" class="form-control" id="start" name="start" value="'.$start.'" required>
						</div>
					  </div>
					  <div class="form-group">
						<label for="inputPassword3" class="col-sm-2 control-label">End</label>
						<div class="col-sm-10">
						  <input type="text" class="form-control" id="end" name="end" value="'.$end.'">
						</div>
					  </div>
					  <div class="form-group">
						<label for="inputPassword3" class="col-sm-2 control-label">Program</label>
						<div class="col-sm-10">
						  <input type="text" class="form-control" id="program" name="program" value="'.$program.'">
						</div>
					  </div>
					  <div class="form-group">
						<label for="inputPassword3" class="col-sm-2 control-label">Sinopsis</label>
						<div class="col-sm-10">
						  <textarea  class="form-control" name="sinopsis" id="sinopsis" rows="8"> '.$sinopsis.' </textarea>
						</div>
					  </div>
					  <input class="btn btn-default pull-right" id="btnSimpan" type="submit" name="btnSimpan" value="Simpan">
					 </form>
							   </div>
							</div>';
			echo '</div>';
}