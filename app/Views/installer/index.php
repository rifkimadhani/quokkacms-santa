<?php 
?>
<style>
body::-webkit-scrollbar 
{
    width: 0.2em;
}
#connectdevices,#makedir,#changepermission,#pushapk,#injectconfig,#installapk,#runapk,#disconnect
{
  display:none
}
.select2-container--default .select2-results__option[aria-disabled=true] {
    display: none;
}
</style>

<div class="box" style="top:0px;">
  <div class="progress">
    <div class="progress-bar progress-bar-striped progress-bar-animated progress-bar-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
  </div>
    <div class="box-body">
        <form id="newForm" action="<?= $baseUrl?>/installapp" method="post">
            <div class="form-group">
                <label class="col-form-label"><b>IP Adress</b></label>
                <input type="text" name="ip_address" value="<?php if(isset($ip_address))echo $ip_address; ?>" required minlength="0" maxlength="15" placeholder="Masukkan IP Address Disini" autocomplete="off" class="form-control">
            </div>
            <div class="form-group">
              <label class='col-form-label'><b>Latest APK</b></label>
              <select class='form-control' id='latest_apk' name='latest_apk' required>
                  <?php
                  if(count($latestgroupapk)> 0 )
                  {
                      foreach ($latestgroupapk as $key => $valueoption) 
                      {
                        echo "<option value='{$valueoption['id']}'>{$valueoption['app_id']} version {$valueoption['version_name']} ({$valueoption['version_code']})</option>";
                      }
                  }
                  ?>
              </select>
            </div>
            <div class="form-group">
              <label class='col-form-label'><b>Nama STB</b></label>
              <select class='form-control' id='stb_name' name='stb_id' required>
                <option value='' disabled selected >Pilih STB Devices</option>
                  <?php
                  if(count($stbdevices)> 0 )
                  {
                      foreach ($stbdevices as $key => $valueoption) 
                      {
                        $selected = '';
                        echo "<option value='{$valueoption['id']}' {$selected} data-src='{$valueoption['data']}'>{$valueoption['value']}</option>";
                      }
                  }
                  ?>
              </select>
            </div>

            <!-- <div class="form-group">
                <label class="col-form-label"><b>Nama STB</b></label>
                <input type="text" name="stb_name" value="<?php if(isset($stb_name))echo $stb_name; ?>"  minlength="0" maxlength="100" placeholder="Masukkan Nama STB Disini" autocomplete="off" class="form-control" required="required">
            </div>            -->
            <div class="modal-footer">
            <button type="submit" class="btn btn-primary btn-full btn-block">INSTALL APP</button>
          </div>
        </form>
    </div>
</div>
<div class="box" style="top:0px;">
    <div class="box-body">
      <div class="row">
        <div class="col-xs-12">
            <?= view('installer/stepwizard', ['order_status'=>'new']); ?>
<!--            --><?php //$this->load->view('stb/devices/stepwizard',['order_status'=>'new']);?>
        </div>
      </div>
    </div>
    <div class="box-body">
        <pre id="disconnect"></pre>
        <pre id="runapk"></pre>
        <pre id="installapk"></pre>
        <pre id="injectconfig"></pre>
        <pre id="pushapk"></pre>
        <pre id="changepermission"></pre>
        <pre id="makedir"></pre>
        <pre id="connectdevices"></pre>
    </div>
</div>

<script>
  $('document').ready(function()
  {
     var baseurl = "<?=$baseUrl?>";
     globalTableList = $('#datalist').DataTable({});
      var stboptions = {containerCssClass : "show-hide",placeholder: "Pilih STB Disini",allowClear: true,tags:true,"language": {"noResults": function(){return "No STB Vacant Found.Please Type STB Name To Add New STB";}},escapeMarkup: function (markup) {return markup;}}
      $('#newForm input[name=ip_address]').inputmask({ alias: "ip"});
      jQuery('#newForm #stb_name').select2(stboptions)
      .on('select2:unselecting', function() 
      {
          $(this).data('unselecting', true);
      })
      .on('select2:opening', function(e) 
      {
        if ($(this).data('unselecting')) 
        {
          $(this).removeData('unselecting');
          e.preventDefault();
        }
      });

      $("#newForm").submit(function(e) {
          e.preventDefault(); // avoid to execute the actual submit of the form.
          jQuery('#overlay-loader-indicator').show();

          const form = $(this);
          const urlinstall = baseurl + '/ajax_install';

          $(".box-body").children('pre').hide();
          $('.stepwizard-step').children('a').removeClass("btn-success btn-danger").addClass("btn-primary");
          $('.progress-bar').css({width: 0 + '%'});

          $.ajax({
              type: 'POST',
              url: urlinstall, // Replace with your server endpoint
              data: form.serialize(),
              success: function (response) {
                  updateStatus(JSON.parse(response))
              },
              error: function (error) {
                  // Handle error here
                  console.log('Error:', error);
              }
          });
      });
  });

  function updateStatus(data) {
      console.log('updateStatus');
      console.log(data);
  }
</script>

