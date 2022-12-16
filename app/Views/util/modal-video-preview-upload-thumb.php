<div class="modal fade" id="videomodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" style="max-width:900px;max-height:600px;">
      <div class="modal-content"   style="max-height:600px;">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
          <div class="modal-body">
                <div class="row">
                    <div class="col-xl-7">
                        <video id="video" style="width:100%" controls></video>
                    </div>
                    <div class="col-xl-5">
                        <h5 style="font-size:12px;">Press Pause Button To Capture Video Thumbnail</h5>
                        <form id="upload-thumbnail-form" method="post" enctype="multipart/form-data"></form>
                    </div>
                </div>
          </div>
      </div>
  </div>
</div>