<div id="iconPicker" class="modal fade">
	<div class="modal-dialog" style="width:100%;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Icon Picker</h4>
			</div>
			<div class="modal-body" style='height: 61vh;overflow-y:auto;'>
				<div>
					<ul class="icon-picker-list">
						<li>
							<a data-class="{{item}} {{activeState}}" data-index="{{index}}">
								<span class="{{item}}"></span>
								<span class="name-class">{{item}}</span>
							</a>
						</li>
					</ul>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" id="change-icon" class="btn btn-success">
					<span class="fa fa-check-circle-o"></span>
					Use Selected Icon
				</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
			</div>
		</div>
	</div>
</div>