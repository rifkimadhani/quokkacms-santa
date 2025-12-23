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
              <label class='col-form-label'><b>Device Type</b></label>
              <select class='form-control' id='device_type' name='device_type' required>
                  <option value='android_stb' selected>Android STB (Rooted)</option>
                  <option value='philips_tv'>Philips TV (Non-Rooted)</option>
              </select>
            </div>
            <div class="form-group">
                <label class="col-form-label"><b>Device IP adress</b></label>
                <input type="text" name="ip_address" value="<?php if(isset($ip_address))echo $ip_address; ?>" required minlength="0" maxlength="15" placeholder="eg. 1.2.3.4" autocomplete="off" class="form-control">
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
              <label class='col-form-label'><b>Nama device</b></label>
              <select class='form-control' id='stb_name' name='stb_id' required>
                <option value='' disabled selected >Pilih device</option>
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

            <div class="modal-footer">
            <button type="submit" class="btn btn-primary btn-full btn-block">INSTALL APP</button>
          </div>
        </form>
    </div>
</div>
<div class="box">
    <div class="box-body">
      <div id="div_connect">
      </div>
      <div id="div_mkdir">
      </div>
      <div id="div_chmod">
      </div>
      <div id="div_pushapk">
      </div>
      <div id="div_pushconfig">
      </div>
      <div id="div_install">
      </div>
      <div id="div_runapp">
      </div>
      <div id="div_disconnect">
      </div>
    </div>
</div>

<script>
  $('document').ready(function()
  {
     var baseurl = "<?=$baseUrl?>";
     globalTableList = $('#datalist').DataTable({});
      var stboptions = {containerCssClass : "show-hide",placeholder: "Pilih device",allowClear: true,tags:true,"language": {"noResults": function(){return "No STB Vacant Found.Please Type STB Name To Add New STB";}},escapeMarkup: function (markup) {return markup;}}
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

          clearDiv();

          alert("Will begin installing app to device");

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
//      console.log('updateStatus');
//      console.log(data);

      // Helper function to safely get step data
      function getStep(key, altKey) {
          if (data[key]) return data[key];
          if (altKey && data[altKey]) return data[altKey];
          return null;
      }

      // Clear all divs first
      clearDiv();

      // Common steps (both device types)
      if (getStep('connect')) updateDiv('connect', $('#div_connect'), getStep('connect'));
      
      // Android STB specific steps
      if (getStep('root')) updateDiv('root', $('#div_mkdir'), getStep('root'));
      if (getStep('mkdir') && getStep('chmod')) {
          updateDiv('mkdir', $('#div_mkdir'), getStep('mkdir'));
          updateDiv('chmod', $('#div_chmod'), getStep('chmod'));
      }
      if (getStep('push_apk')) updateDiv('push apk', $('#div_pushapk'), getStep('push_apk'));
      
      // Install step (both device types)
      if (getStep('install')) updateDiv('install', $('#div_install'), getStep('install'));
      
      // Config push (different keys for each device type)
      var configStep = getStep('push_config', 'copy_config');
      if (configStep) updateDiv('push config', $('#div_pushconfig'), configStep);
      
      // Philips TV specific steps
      if (getStep('runapp_first')) updateDiv('run app (init)', $('#div_runapp'), getStep('runapp_first'));
      if (getStep('disable_bloatware')) updateDiv('disable bloatware', $('#div_mkdir'), getStep('disable_bloatware'));
      if (getStep('accessibility')) updateDiv('accessibility', $('#div_chmod'), getStep('accessibility'));
      if (getStep('set_home')) updateDiv('set home activity', $('#div_pushapk'), getStep('set_home'));
      
      // Run app (final step for both)
      if (getStep('runapp')) updateDiv('run application', $('#div_runapp'), getStep('runapp'));
      
      // Disconnect (both device types)
      if (getStep('disconnect')) updateDiv('disconnect', $('#div_disconnect'), getStep('disconnect'));

      // Hide loader
      jQuery('#overlay-loader-indicator').hide();
  }

  function updateDiv(title, div, step) {
      if (!step) return; // Skip if no data
      
      const text = step.retString || '';
      var color = 'blue'; //default color blue

      html = "<h5 style='margin-bottom: 0;'><p style='margin-bottom: 0;'>" + title + "</p></h5>";
      html += "<p style='margin-bottom: 0;'>$ " + step.cmd + "</p>";

      //set color berdasarkan response text
      if (text.toLowerCase().indexOf('fail')>=0){
          color = 'red';
      }
      if (text.toLowerCase().indexOf('error')>=0){
          color = 'red';
      }
      if (text.toLowerCase().indexOf('not found')>=0){
          color = 'red';
      }

      html += "<p style='margin-bottom: 0; color: "+color+";'>" +  text + "</p><br/>";

      div.html(html);
  }

  /**
   * hapus semua div
   */
  function clearDiv() {
      $('#div_connect').html('');
      $('#div_mkdir').html('');
      $('#div_chmod').html('');
      $('#div_pushapk').html('');
      $('#div_pushconfig').html('');
      $('#div_install').html('');
      $('#div_runapp').html('');
      $('#div_disconnect').html('');
  }
</script>

